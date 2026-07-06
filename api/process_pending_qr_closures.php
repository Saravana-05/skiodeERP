<?php
/**
 * process_pending_qr_closures.php
 * Called on dashboard load to catch any parked JCs whose QR payment
 * arrived while no one was polling (user off the page, browser closed, etc.)
 *
 * STEP 1 — Actively call the bank API for every PENDING canara_transaction
 *           that is linked to a parked JC. This fills the gap when the bank
 *           webhook missed the payment or the browser polling timed out.
 *
 * STEP 2 — Find pending_qr_closures rows that now have a matching
 *           canara_transactions row with status=SUCCESS and auto-convert them.
 *
 * Returns JSON { converted: [ {jc, sq}, ... ] }
 */
mysqli_report(MYSQLI_REPORT_OFF);
include_once __DIR__ . '/../connect_db.php';
include_once __DIR__ . '/canara_bank_lib.php';
fn_ensure_pending_qr_table($connection);

header('Content-Type: application/json');

// ── STEP 1: Refresh PENDING transactions by calling the bank API ──────────────
// Only check transactions linked to still-pending JC closures to keep it fast.
// Include EXPIRED rows — our system expires a QR after our own timeout, but
// the bank's QR may still be valid for longer. The customer can pay on what
// we call "expired" and the bank processes it. Never skip EXPIRED when checking.
$pending_sql = "SELECT ct.ext_transaction_id, ct.jobcard_no
                FROM canara_transactions ct
                INNER JOIN pending_qr_closures pqc
                    ON pqc.jobcard_no = ct.jobcard_no
                   AND pqc.status    = 'PENDING'
                WHERE ct.status != 'SUCCESS'
                  AND ct.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";

$pending_res = mysqli_query($connection, $pending_sql);
if ($pending_res) {
    while ($pt = mysqli_fetch_assoc($pending_res)) {
        $ext_id = $pt['ext_transaction_id'];
        if (!$ext_id) continue;

        // Call FastAPI — use source=ECOMMERCE (FastAPI reads its own DB, already
        // updated by the bank webhook) rather than source=ERP (which calls the
        // Canara Bank API live and has a settlement lag of several minutes).
        $api_url = 'https://api.citizenprintz.in/api/bank/qr-status-extid';
        $payload = json_encode(['extTransactionId' => $ext_id, 'source' => 'ECOMMERCE']);

        $ch = curl_init($api_url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);
        $resp      = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_error($ch);
        curl_close($ch);

        if ($curl_err || $http_code !== 200) {
            log_this("process_pending_qr_closures: Bank API error for $ext_id — $curl_err HTTP $http_code");
            continue;
        }

        $bank_data = json_decode($resp, true);
        if (!is_array($bank_data)) { continue; }

        $raw_status = null; // will be set below

        // ── Handle nested ECOMMERCE format: {success,data:{data:[{respCode,...}]}} ──
        if (isset($bank_data['data']['data'][0]) && is_array($bank_data['data']['data'][0])) {
            $inner = $bank_data['data']['data'][0];
            $rc    = $inner['respCode'] ?? '';
            if ($rc === '00') {
                $raw_status = $inner['txnStatus'] ?? $inner['status'] ?? 'SUCCESS';
                $bank_data  = array_merge($bank_data, $inner); // merge for field extraction
            } else {
                // E05 = No Data Found for this ext_id — ECOMMERCE doesn't have it.
                // Fall through to ERP source below (don't skip — payment may be on ERP).
                log_this("process_pending_qr_closures: ext_id=$ext_id ECOMMERCE respCode=$rc — trying ERP fallback");
                $bank_data = []; // clear so ERP result is used
            }
        } else {
            // Flat response (ERP / webhook path)
            $raw_status = $bank_data['status']       ?? $bank_data['txnStatus']
                       ?? $bank_data['payment_status']?? $bank_data['paymentStatus']
                       ?? $bank_data['txn_status']   ?? null;
        }

        // ── ERP fallback: try if ECOMMERCE gave no usable status ─────────────
        if ($raw_status === null) {
            $erp_payload = json_encode(['extTransactionId' => $ext_id, 'source' => 'ERP']);
            $erp_ch = curl_init($api_url);
            curl_setopt_array($erp_ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $erp_payload,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            ]);
            $erp_resp      = curl_exec($erp_ch);
            $erp_http_code = curl_getinfo($erp_ch, CURLINFO_HTTP_CODE);
            $erp_curl_err  = curl_error($erp_ch);
            curl_close($erp_ch);

            if (!$erp_curl_err && $erp_http_code === 200) {
                $erp_data = json_decode($erp_resp, true);
                if (is_array($erp_data)) {
                    if (isset($erp_data['data']['data'][0]) && is_array($erp_data['data']['data'][0])) {
                        $erp_inner = $erp_data['data']['data'][0];
                        if (($erp_inner['respCode'] ?? '') === '00') {
                            $raw_status = $erp_inner['txnStatus'] ?? $erp_inner['status'] ?? 'SUCCESS';
                            $bank_data  = array_merge($erp_data, $erp_inner);
                        }
                    } else {
                        $raw_status = $erp_data['status']        ?? $erp_data['txnStatus']
                                   ?? $erp_data['payment_status'] ?? $erp_data['paymentStatus']
                                   ?? $erp_data['txn_status']    ?? null;
                        if ($raw_status !== null) $bank_data = $erp_data;
                    }
                }
            }
            log_this("process_pending_qr_closures: ext_id=$ext_id ERP fallback HTTP=$erp_http_code raw_status=" . ($raw_status ?? 'null'));
        }

        if ($raw_status === null) {
            log_this("process_pending_qr_closures: ext_id=$ext_id — no status from ECOMMERCE or ERP, skipping");
            continue;
        }

        $bank_status = strtoupper(trim((string)$raw_status));
        // Canara Bank uses 'CREDIT' for successful merchant-side receipt
        if (in_array($bank_status, ['PAID','CREDIT','SUCCESSFUL','APPROVED','COMPLETE','S'], true))
            $bank_status = 'SUCCESS';
        if (in_array($bank_status, ['FAILURE','FAIL','DECLINED','REJECTED','F'], true))
            $bank_status = 'FAILED';

        log_this("process_pending_qr_closures: ext_id=$ext_id JC={$pt['jobcard_no']} FastAPI→$bank_status raw=$resp");

        if ($bank_status === 'SUCCESS') {
            $safe_id  = mysqli_real_escape_string($connection, $ext_id);
            $now_u    = date('Y-m-d H:i:s');

            // Extract RRN — bank uses 'rrn', 'bankRrn', 'bankRRN', 'utr'
            $rrn_raw  = $bank_data['rrn']       ?? $bank_data['bankRrn']  ?? $bank_data['bankRRN']
                     ?? $bank_data['bank_rrn']   ?? $bank_data['utr']      ?? '';
            // Extract txn_id — bank uses 'txnId', 'txn_id', 'transactionId', 'bankTxnId'
            $tid_raw  = $bank_data['txn_id']         ?? $bank_data['txnId']     ?? $bank_data['transactionId']
                     ?? $bank_data['transaction_id'] ?? $bank_data['bankTxnId'] ?? '';
            // Extract VPA — bank uses 'customer_vpa', 'payerVpa', 'payerVPA', 'customerVpa'
            $vpa_raw  = $bank_data['customer_vpa'] ?? $bank_data['payerVpa']   ?? $bank_data['payerVPA']
                     ?? $bank_data['payer_vpa']    ?? $bank_data['customerVpa'] ?? $bank_data['customerVPA'] ?? '';

            $rrn      = $rrn_raw ? mysqli_real_escape_string($connection, (string)$rrn_raw) : '';
            $txn_id   = $tid_raw ? mysqli_real_escape_string($connection, (string)$tid_raw) : '';
            $cust_vpa = $vpa_raw ? mysqli_real_escape_string($connection, (string)$vpa_raw) : '';

            mysqli_query($connection,
                "UPDATE canara_transactions
                    SET status     = 'SUCCESS',
                        paid_at    = COALESCE(paid_at, '$now_u'),
                        updated_at = '$now_u'"
                . ($rrn      ? ", rrn          = '$rrn'"      : '')
                . ($txn_id   ? ", txn_id       = '$txn_id'"   : '')
                . ($cust_vpa ? ", customer_vpa = '$cust_vpa'" : '')
                . " WHERE ext_transaction_id = '$safe_id' AND status != 'SUCCESS'"
            );
            log_this("process_pending_qr_closures: SUCCESS ext_id=$ext_id JC={$pt['jobcard_no']} rrn=$rrn vpa=$cust_vpa");
        } elseif ($bank_status === 'FAILED') {
            $safe_id = mysqli_real_escape_string($connection, $ext_id);
            $now_u   = date('Y-m-d H:i:s');
            mysqli_query($connection,
                "UPDATE canara_transactions
                    SET status = 'FAILED', updated_at = '$now_u'
                  WHERE ext_transaction_id = '$safe_id' AND status = 'PENDING'"
            );
        }
    }
}

// ── STEP 2: Convert any parked JCs that now have a SUCCESS transaction ─────────
$converted = [];

// CRITICAL: match only the SPECIFIC balance QR transaction stored in pqc.ext_transaction_id.
// Without this, an advance QR payment (already SUCCESS for the same JC) would trigger
// immediate conversion even though the balance payment hasn't been received yet.
// If ext_transaction_id is NULL/empty (legacy rows), fall back to any SUCCESS for the JC.
$sql = "SELECT pqc.*
        FROM pending_qr_closures pqc
        INNER JOIN canara_transactions ct
            ON ct.jobcard_no = pqc.jobcard_no
           AND ct.status     = 'SUCCESS'
           AND (
               pqc.ext_transaction_id IS NULL
               OR pqc.ext_transaction_id = ''
               OR ct.ext_transaction_id = pqc.ext_transaction_id
           )
        WHERE pqc.status = 'PENDING'";

$res = mysqli_query($connection, $sql);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $params = [
            'jcnos'             => !empty($row['jobcard_nos']) ? $row['jobcard_nos'] : strval($row['jobcard_no']),
            'approximate_amount'=> $row['approximate_amount'],
            'balance_cash'      => $row['balance_cash'],
            'balance_gpay'      => $row['balance_gpay'],
            'balance_amount'    => $row['balance_amount'],
            'discount'          => $row['discount_amount'],
            'is_bal_in_cash'    => $row['is_bal_in_cash'],
            'is_bal_in_gpay'    => $row['is_bal_in_gpay'],
            'is_gst_extra'      => $row['is_gst_extra'],
            'gst_tax_amount'    => $row['gst_tax_amount'],
        ];
        $user   = $row['created_by'] ?: 'QR_AUTO';
        $sq     = fn_convert_jc_to_sq($connection, $params, $user);
        $now_c  = date('Y-m-d H:i:s');

        if ($sq !== 'Failed' && $sq !== 'Job Card already Closed') {
            mysqli_query($connection,
                "UPDATE pending_qr_closures
                    SET status='CONVERTED', converted_sq_no=$sq,
                        converted_at='$now_c', updated_at='$now_c'
                  WHERE jobcard_no=" . intval($row['jobcard_no']));
            $converted[] = ['jc' => $row['jobcard_no'], 'sq' => $sq];
            log_this("process_pending_qr_closures: Auto-converted JC {$row['jobcard_no']} → SQ $sq");
        } elseif ($sq === 'Job Card already Closed') {
            mysqli_query($connection,
                "UPDATE pending_qr_closures
                    SET status='CONVERTED', updated_at='$now_c'
                  WHERE jobcard_no=" . intval($row['jobcard_no']));
        }
    }
}

echo json_encode(['converted' => $converted]);
?>
