<?php
mysqli_report(MYSQLI_REPORT_OFF); // must be before any include — PHP 8.1 throws by default
/**
 * ERP QR Status — calls the unified FastAPI endpoint.
 *
 * ROOT CAUSE FIX (2026-05):
 *   The QR is generated with source=ECOMMERCE, so FastAPI stores the transaction
 *   in its own PostgreSQL and its webhook updates that record immediately.
 *   When we previously used source=ERP, FastAPI called the Canara Bank API
 *   *live* each time, which has a settlement lag (payment in account but API
 *   still says PENDING for several minutes).
 *   We now try source=ECOMMERCE first so FastAPI reads its own already-updated
 *   DB, then fall back to source=ERP if the response format differs.
 *
 * GET  ?ext_id=EXT...
 * Returns JSON { status: "PENDING"|"SUCCESS"|"FAILED", converted_sq: null|int }
 */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
header('Content-Type: application/json');

include_once __DIR__ . '/../connect_db.php';
include_once __DIR__ . '/canara_bank_lib.php';
if (isset($connection) && $connection) cb_ensure_table($connection);

$ext_id = isset($_GET['ext_id']) ? trim($_GET['ext_id']) : '';

if (!$ext_id) {
    echo json_encode(['status' => 'NOT_FOUND']);
    exit;
}

// ── Fast-path: if DB already shows SUCCESS, no need to call the bank ─────────
$safe_id = mysqli_real_escape_string($connection, $ext_id);
$db_row  = mysqli_fetch_assoc(
    mysqli_query($connection,
        "SELECT status FROM canara_transactions WHERE ext_transaction_id = '$safe_id' LIMIT 1")
);
if ($db_row && $db_row['status'] === 'SUCCESS') {
    cb_log("[QRStatus] ext_id=$ext_id — DB already SUCCESS, skipping bank call");
    echo json_encode(['status' => 'SUCCESS', 'converted_sq' => null]);
    exit;
}

// ── Helper: call FastAPI and extract status from response ────────────────────
function fn_call_fastapi_status($ext_id, $source) {
    $api_url = 'https://api.citizenprintz.in/api/bank/qr-status-extid';
    $payload = json_encode(['extTransactionId' => $ext_id, 'source' => $source]);

    $ch = curl_init($api_url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    ]);
    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_err  = curl_error($ch);
    curl_close($ch);

    cb_log("[QRStatus] source=$source ext_id=$ext_id → HTTP $http_code | $response");

    if ($curl_err || $http_code !== 200) {
        cb_log("[QRStatus] source=$source CURL_ERR=$curl_err HTTP=$http_code");
        return null; // signal network error
    }

    $data = json_decode($response, true);
    if (!is_array($data)) return null;

    // ── Handle nested ECOMMERCE response format ───────────────────────────────
    // ECOMMERCE wraps the bank response: {success:true, data:{status:"SUCCESS",
    //   data:[{respCode:"00"|"E05", txnStatus:"...", rrn:"...", payerVpa:"..."}]}}
    // The outer data.status = "SUCCESS" means the API call worked — NOT the payment.
    // The actual payment result is in data.data[0].respCode:
    //   "00"  = payment found → look at data.data[0].txnStatus for payment status
    //   "E05" = No Data Found → payment not linked to this ext_id (customer paid by UPI ID directly)
    $inner = null;
    if (isset($data['data']['data'][0]) && is_array($data['data']['data'][0])) {
        $inner    = $data['data']['data'][0];
        $resp_code = $inner['respCode'] ?? '';
        if ($resp_code === '00') {
            // Payment found — extract status and merge fields into $data for unified extraction
            $raw_status = $inner['txnStatus'] ?? $inner['status'] ?? 'SUCCESS';
            // Merge inner fields up so field-extraction below can find them
            $data = array_merge($data, $inner);
        } else {
            // E05 = No Data Found, or other error — not this ext_id
            cb_log("[QRStatus] source=$source ECOMMERCE respCode=$resp_code ({$inner['respMessge']})" .
                   " — payment not linked to this ext_id, falling through to ERP");
            return null; // signal: try ERP
        }
    } else {
        // ── Flat response format (ERP / webhook-updated ECOMMERCE) ────────────
        $raw_status = $data['status']          // most common flat key
                   ?? $data['txnStatus']       // Canara Bank raw UPI field
                   ?? $data['payment_status']  // some FastAPI wrappers
                   ?? $data['paymentStatus']   // camelCase variant
                   ?? $data['txn_status']      // snake_case variant
                   ?? null;

        if ($raw_status === null) {
            cb_log("[QRStatus] source=$source — no recognisable status field. response=$response");
            return null;
        }
    }

    // ── Normalise status value ────────────────────────────────────────────────
    // Canara Bank UPI uses 'CREDIT' for a successful merchant-side receipt.
    $s = strtoupper(trim((string)$raw_status));
    if (in_array($s, ['SUCCESS', 'PAID', 'CREDIT', 'SUCCESSFUL', 'APPROVED', 'COMPLETE', 'S'], true))
        $s = 'SUCCESS';
    elseif (in_array($s, ['FAILURE', 'FAILED', 'FAIL', 'DECLINED', 'REJECTED', 'F'], true))
        $s = 'FAILED';
    elseif (in_array($s, ['PENDING', 'INITIATED', 'IN_PROGRESS', 'PROCESSING', 'P'], true))
        $s = 'PENDING';

    cb_log("[QRStatus] source=$source raw_status='$raw_status' → normalised='$s'");

    return ['status' => $s, 'data' => $data];
}

// ── Strategy: try ECOMMERCE first (FastAPI reads its own DB — reliable),
//    fall back to ERP if no usable status returned ───────────────────────────
$result = fn_call_fastapi_status($ext_id, 'ECOMMERCE');

if ($result === null) {
    // ECOMMERCE call failed (network) — try ERP source as fallback
    cb_log("[QRStatus] ECOMMERCE call failed, trying ERP source");
    $result = fn_call_fastapi_status($ext_id, 'ERP');
} elseif ($result['status'] === 'PENDING') {
    // ECOMMERCE says PENDING — also ask ERP source in case it has fresher data
    cb_log("[QRStatus] ECOMMERCE returned PENDING, also checking ERP source");
    $erp_result = fn_call_fastapi_status($ext_id, 'ERP');
    if ($erp_result && $erp_result['status'] === 'SUCCESS') {
        cb_log("[QRStatus] ERP source returned SUCCESS — overriding ECOMMERCE PENDING");
        $result = $erp_result;
    }
}

if ($result === null) {
    // Both sources failed — return current DB status
    cb_log("[QRStatus] Both sources failed, returning DB status: " . ($db_row['status'] ?? 'PENDING'));
    echo json_encode(['status' => $db_row['status'] ?? 'PENDING', 'converted_sq' => null]);
    exit;
}

$status = $result['status'];
$data   = $result['data'];
cb_log("[QRStatus] Final status=$status for ext_id=$ext_id");

// ── Write back to MySQL so the DB stays in sync ───────────────────────────────
$converted_sq = null;

if ($status === 'SUCCESS') {
    // DEBUG: capture all keys from bank response
    $_QR_DEBUG = ['bank_response_all_keys' => $data, 'php_now' => date('Y-m-d H:i:s'), 'php_timezone' => date_default_timezone_get()];

    // ── Extract RRN — bank uses 'rrn', 'bankRrn', 'bankRRN' ──────────────────
    $rrn_raw  = $data['rrn']        ?? $data['bankRrn']     ?? $data['bankRRN']
             ?? $data['bank_rrn']   ?? $data['utr']         ?? '';
    // ── Extract transaction ID — bank uses 'txnId', 'txn_id', 'transactionId' ─
    $tid_raw  = $data['txn_id']         ?? $data['txnId']        ?? $data['transactionId']
             ?? $data['transaction_id'] ?? $data['bankTxnId']    ?? $data['bank_txn_id'] ?? '';
    // ── Extract payer VPA — bank uses 'customer_vpa', 'payerVpa', 'payerVPA' ──
    $vpa_raw  = $data['customer_vpa']  ?? $data['payerVpa']    ?? $data['payerVPA']
             ?? $data['payer_vpa']     ?? $data['customerVpa'] ?? $data['customerVPA'] ?? '';

    $rrn      = $rrn_raw  ? mysqli_real_escape_string($connection, (string)$rrn_raw)  : '';
    $txn_id   = $tid_raw  ? mysqli_real_escape_string($connection, (string)$tid_raw)  : '';
    $cust_vpa = $vpa_raw  ? mysqli_real_escape_string($connection, (string)$vpa_raw)  : '';

    $now_ist = date('Y-m-d H:i:s');
    $safe_paid = mysqli_real_escape_string($connection, $now_ist);

    cb_log("[QRStatus] SUCCESS fields — rrn='$rrn' txn_id='$txn_id' vpa='$cust_vpa' setting paid_at='$now_ist' (IST)");

    // Always overwrite paid_at with current IST time — the webhook stores UTC which
    // causes a 5:30 hour mismatch. The poller runs within seconds of actual payment.
    mysqli_query($connection,
        "UPDATE canara_transactions
            SET status     = 'SUCCESS',
                paid_at    = '$safe_paid',
                updated_at = '$now_ist'"
        . ($rrn      ? ", rrn          = '$rrn'"      : '')
        . ($txn_id   ? ", txn_id       = '$txn_id'"   : '')
        . ($cust_vpa ? ", customer_vpa = '$cust_vpa'" : '')
        . " WHERE ext_transaction_id = '$safe_id' AND status != 'SUCCESS'"
    );
    cb_log("[QRStatus] Updated canara_transactions to SUCCESS for ext_id=$ext_id");

    // ── Auto-convert parked JC → SQ if pending closure exists ────────────────
    if (function_exists('fn_ensure_pending_qr_table') && function_exists('fn_convert_jc_to_sq')) {
        $jc_res = mysqli_query($connection,
            "SELECT jobcard_no FROM canara_transactions WHERE ext_transaction_id='$safe_id' LIMIT 1");
        $jc_row = $jc_res ? mysqli_fetch_assoc($jc_res) : null;
        if ($jc_row && !empty($jc_row['jobcard_no'])) {
            $jc_no_conv = intval($jc_row['jobcard_no']);
            fn_ensure_pending_qr_table($connection);
            $pr = mysqli_query($connection,
                "SELECT * FROM pending_qr_closures WHERE jobcard_no=$jc_no_conv AND status='PENDING' LIMIT 1");
            if ($pr && $prow = mysqli_fetch_assoc($pr)) {
                $p = [
                    'jcnos'             => !empty($prow['jobcard_nos']) ? $prow['jobcard_nos'] : strval($prow['jobcard_no']),
                    'approximate_amount'=> $prow['approximate_amount'],
                    'balance_cash'      => $prow['balance_cash'],
                    'balance_gpay'      => $prow['balance_gpay'],
                    'balance_amount'    => $prow['balance_amount'],
                    'discount'          => $prow['discount_amount'],
                    'is_bal_in_cash'    => $prow['is_bal_in_cash'],
                    'is_bal_in_gpay'    => $prow['is_bal_in_gpay'],
                    'is_gst_extra'      => $prow['is_gst_extra'],
                    'gst_tax_amount'    => $prow['gst_tax_amount'],
                ];
                $sq = fn_convert_jc_to_sq($connection, $p, $prow['created_by'] ?: 'QR_AUTO');
                if ($sq !== 'Failed' && $sq !== 'Job Card already Closed') {
                    $now_c = date('Y-m-d H:i:s');
                    mysqli_query($connection,
                        "UPDATE pending_qr_closures
                            SET status='CONVERTED', converted_sq_no=$sq,
                                converted_at='$now_c', updated_at='$now_c'
                          WHERE jobcard_no=$jc_no_conv");
                    $converted_sq = $sq;
                    cb_log("[QRStatus] Auto-converted JC $jc_no_conv → SQ $sq");
                    log_this("canara_qr_status: Auto-converted JC $jc_no_conv → SQ $sq");
                }
            } else {
                cb_log("[QRStatus] No PENDING pending_qr_closures for JC $jc_no_conv");
            }
        }
    }

    // ── Auto-update advance payment if saved as PENDING ───────────────────────
    $adv_tbl_chk = mysqli_query($connection, "SHOW TABLES LIKE 'advance_payment_master'");
    if ($adv_tbl_chk && mysqli_num_rows($adv_tbl_chk) > 0) {
        $adv_col_chk = mysqli_query($connection,
            "SHOW COLUMNS FROM advance_payment_master LIKE 'ext_transaction_id'");
        if ($adv_col_chk && mysqli_num_rows($adv_col_chk) > 0) {
            $adv_res = mysqli_query($connection,
                "SELECT * FROM advance_payment_master
                  WHERE ext_transaction_id = '$safe_id' AND qr_payment_status = 'PENDING' LIMIT 1");
            if ($adv_res && $adv_row = mysqli_fetch_assoc($adv_res)) {
                $adv_id_u   = intval($adv_row['advance_id']);
                $adv_date_j = $adv_row['advance_date'];
                $adv_name_j = $adv_row['customer_name'];
                $adv_mob_j  = $adv_row['customer_mobile'];
                $adv_amt_j  = floatval($adv_row['amount']);
                mysqli_query($connection,
                    "UPDATE advance_payment_master SET qr_payment_status = 'PAID'
                      WHERE advance_id = $adv_id_u");
                if (function_exists('post_journal_single_entry')) {
                    post_journal_single_entry(
                        $adv_date_j, 'SB',
                        'Advance GPay (no JC) - ' . $adv_name_j . ' / ' . $adv_mob_j,
                        $adv_amt_j, 'ADV_' . $adv_id_u
                    );
                }
            }
        }
    }

} elseif ($status === 'FAILED') {
    mysqli_query($connection,
        "UPDATE canara_transactions
            SET status = 'FAILED', updated_at = NOW()
          WHERE ext_transaction_id = '$safe_id' AND status = 'PENDING'"
    );
    cb_log("[QRStatus] Updated canara_transactions to FAILED for ext_id=$ext_id");
}

$_out = ['status' => $status, 'converted_sq' => $converted_sq];
if (isset($_QR_DEBUG)) $_out['_DEBUG'] = $_QR_DEBUG;
echo json_encode($_out);
?>
