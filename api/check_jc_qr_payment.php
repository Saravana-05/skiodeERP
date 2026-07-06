<?php
/**
 * check_jc_qr_payment.php
 *
 * Called when staff clicks "🔄 Check Payment" for a QR-pending Job Card.
 *
 * KEY DESIGN DECISION:
 *   A Job Card can have multiple QR transactions (customer scans fail, staff
 *   regenerates QR). The payment may land on ANY of those transactions — not
 *   necessarily the most-recent one. This endpoint therefore scans ALL
 *   PENDING/FAILED canara_transactions for the JC (within 7 days) and checks
 *   each one against the bank. The first SUCCESS found wins.
 *
 * GET jcno=NNNN
 *   → scans all PENDING/FAILED canara_transactions for that JC
 *   → checks each against FastAPI (source=ECOMMERCE first, ERP fallback)
 *   → if any shows SUCCESS: updates DB, auto-converts JC → SQ
 *   → returns { status, converted_sq }
 */
mysqli_report(MYSQLI_REPORT_OFF);
header('Content-Type: application/json');
session_start();

include_once __DIR__ . '/../connect_db.php';
include_once __DIR__ . '/canara_bank_lib.php';
fn_ensure_pending_qr_table($connection);
cb_ensure_table($connection);

$jcno = intval($_GET['jcno'] ?? 0);
if ($jcno <= 0) {
    echo json_encode(['status' => 'NOT_FOUND', 'message' => 'Invalid job card number.']);
    exit;
}

// ── Helper: call FastAPI and normalise the status field ──────────────────────
function cjqp_call_bank($ext_id, $source) {
    $payload = json_encode(['extTransactionId' => $ext_id, 'source' => $source]);
    $ch = curl_init('https://api.citizenprintz.in/api/bank/qr-status-extid');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    ]);
    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $cerr = curl_error($ch);
    curl_close($ch);

    if ($cerr || $http !== 200) return null;
    $d = json_decode($resp, true);
    if (!is_array($d)) return null;

    // ── Handle nested ECOMMERCE format: {success,data:{data:[{respCode,txnStatus,...}]}} ──
    if (isset($d['data']['data'][0]) && is_array($d['data']['data'][0])) {
        $inner = $d['data']['data'][0];
        $rc    = $inner['respCode'] ?? '';
        if ($rc === '00') {
            // Found — merge inner fields up and use txnStatus as status
            $raw = $inner['txnStatus'] ?? $inner['status'] ?? 'SUCCESS';
            $d   = array_merge($d, $inner); // so field-extraction finds rrn/payerVpa/txnId
        } else {
            // E05 = No Data Found — payment not linked to this ext_id
            return null;
        }
    } else {
        // Flat response (ERP / webhook-updated path)
        $raw = $d['status'] ?? $d['txnStatus'] ?? $d['payment_status']
            ?? $d['paymentStatus'] ?? $d['txn_status'] ?? null;
        if ($raw === null) return null;
    }

    $s = strtoupper(trim((string)$raw));
    // 'CREDIT' = merchant received money (Canara Bank UPI merchant-side status)
    if (in_array($s, ['SUCCESS','PAID','CREDIT','SUCCESSFUL','APPROVED','COMPLETE','S'], true))
        $s = 'SUCCESS';
    elseif (in_array($s, ['FAILURE','FAILED','FAIL','DECLINED','REJECTED','F'], true))
        $s = 'FAILED';
    elseif (in_array($s, ['PENDING','INITIATED','IN_PROGRESS','PROCESSING','P'], true))
        $s = 'PENDING';

    if (!$s) return null;
    return ['status' => $s, 'data' => $d];
}

// ── 1. Gather ALL PENDING/FAILED canara_transactions for this JC ─────────────
//    (A customer may have scanned QR #1, paid it, then staff regenerated QR #2
//     which is now the newest row — checking only the latest row would miss the
//     payment on QR #1.)
$safe_jc = intval($jcno);

// Fast-path: check if the SPECIFIC balance QR (stored in pending_qr_closures.ext_transaction_id)
// is already SUCCESS in our DB.  Matching ANY SUCCESS for the JC is wrong — the advance
// payment is a separate SUCCESS transaction on the same JC and must NOT trigger conversion.
$pqc_ext_row = mysqli_fetch_assoc(mysqli_query($connection,
    "SELECT ext_transaction_id FROM pending_qr_closures
      WHERE jobcard_no=$safe_jc AND status='PENDING' LIMIT 1"));
$pqc_ext_id  = $pqc_ext_row['ext_transaction_id'] ?? '';

if ($pqc_ext_id) {
    // We know the exact ext_id being waited for — only that SUCCESS counts
    $safe_pqc_ext  = mysqli_real_escape_string($connection, $pqc_ext_id);
    $success_check = mysqli_query($connection,
        "SELECT id FROM canara_transactions
          WHERE jobcard_no='$safe_jc' AND status='SUCCESS'
            AND ext_transaction_id='$safe_pqc_ext' LIMIT 1");
} else {
    // Legacy row with no ext_transaction_id — fall back to any SUCCESS (old behaviour)
    $success_check = mysqli_query($connection,
        "SELECT id FROM canara_transactions WHERE jobcard_no='$safe_jc' AND status='SUCCESS' LIMIT 1");
}

if ($success_check && mysqli_num_rows($success_check) > 0) {
    // Payment already confirmed in local DB — just run auto-conversion if needed
    $converted_sq = null;
    $pr = mysqli_query($connection,
        "SELECT * FROM pending_qr_closures WHERE jobcard_no=$safe_jc AND status='PENDING' LIMIT 1");
    if ($pr && $prow = mysqli_fetch_assoc($pr)) {
        $now_c = date('Y-m-d H:i:s');
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
            mysqli_query($connection,
                "UPDATE pending_qr_closures
                    SET status='CONVERTED', converted_sq_no=$sq,
                        converted_at='$now_c', updated_at='$now_c'
                  WHERE jobcard_no=$safe_jc");
            $converted_sq = $sq;
        } elseif ($sq === 'Job Card already Closed') {
            mysqli_query($connection,
                "UPDATE pending_qr_closures SET status='CONVERTED', updated_at='$now_c'
                  WHERE jobcard_no=$safe_jc");
        }
    }
    log_this("check_jc_qr_payment: JC $jcno already SUCCESS in DB — converted_sq=$converted_sq");
    echo json_encode(['status' => 'SUCCESS', 'converted_sq' => $converted_sq]);
    exit;
}

// Collect ALL non-SUCCESS transactions for this JC — including EXPIRED ones.
// Our system marks a QR as EXPIRED after our own timeout, but the bank's QR
// may still be valid longer. A customer can pay on what we call "expired" and
// the bank processes it fine. We must check ALL of them so we don't miss it.
$tr = mysqli_query($connection,
    "SELECT ext_transaction_id, status FROM canara_transactions
      WHERE jobcard_no = '$safe_jc'
        AND status != 'SUCCESS'
        AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
      ORDER BY created_at DESC");

$pending_ext_ids = [];
while ($tr && $row = mysqli_fetch_assoc($tr)) {
    if ($row['ext_transaction_id']) $pending_ext_ids[] = $row['ext_transaction_id'];
}

if (empty($pending_ext_ids)) {
    echo json_encode(['status' => 'NOT_FOUND',
                      'message' => 'No QR transaction found for this Job Card. Please generate a QR first.']);
    exit;
}

// ── 2. Poll bank for each outstanding transaction ─────────────────────────────
$now          = date('Y-m-d H:i:s');
$bank_status  = 'PENDING';
$found_data   = [];
$found_ext_id = '';
$converted_sq = null;

foreach ($pending_ext_ids as $ext_id) {
    // Try ECOMMERCE first (FastAPI reads its own DB — updated by bank webhook)
    $result = cjqp_call_bank($ext_id, 'ECOMMERCE');
    if ($result === null) {
        $result = cjqp_call_bank($ext_id, 'ERP'); // fallback
    } elseif ($result['status'] === 'PENDING') {
        // ECOMMERCE says PENDING — also try ERP in case of fresher state
        $erp = cjqp_call_bank($ext_id, 'ERP');
        if ($erp && $erp['status'] === 'SUCCESS') $result = $erp;
    }

    if ($result === null) continue;

    log_this("check_jc_qr_payment: JC $jcno ext_id=$ext_id → " . $result['status']);

    if ($result['status'] === 'SUCCESS') {
        $bank_status  = 'SUCCESS';
        $found_data   = $result['data'];
        $found_ext_id = $ext_id;
        break; // found the paid transaction — stop checking others
    } elseif ($result['status'] === 'FAILED') {
        // Mark this specific transaction failed in DB; keep scanning others
        $safe_ext = mysqli_real_escape_string($connection, $ext_id);
        mysqli_query($connection,
            "UPDATE canara_transactions SET status='FAILED', updated_at='$now'
              WHERE ext_transaction_id='$safe_ext' AND status='PENDING'");
    }
}

if ($bank_status === 'SUCCESS') {
    $safe_ext = mysqli_real_escape_string($connection, $found_ext_id);

    // Extract RRN — try all known field names
    $rrn_raw  = $found_data['rrn']       ?? $found_data['bankRrn']  ?? $found_data['bankRRN']
             ?? $found_data['bank_rrn']   ?? $found_data['utr']      ?? '';
    // Extract txn_id — bank uses 'txnId', 'txn_id', 'transactionId', 'bankTxnId'
    $tid_raw  = $found_data['txn_id']         ?? $found_data['txnId']     ?? $found_data['transactionId']
             ?? $found_data['transaction_id'] ?? $found_data['bankTxnId'] ?? '';
    // Extract payer VPA — bank uses 'customer_vpa', 'payerVpa', 'payerVPA', 'customerVpa'
    $vpa_raw  = $found_data['customer_vpa'] ?? $found_data['payerVpa']    ?? $found_data['payerVPA']
             ?? $found_data['payer_vpa']    ?? $found_data['customerVpa'] ?? $found_data['customerVPA'] ?? '';

    $rrn      = $rrn_raw ? mysqli_real_escape_string($connection, (string)$rrn_raw) : '';
    $txn_id   = $tid_raw ? mysqli_real_escape_string($connection, (string)$tid_raw) : '';
    $cust_vpa = $vpa_raw ? mysqli_real_escape_string($connection, (string)$vpa_raw) : '';

    $safe_paid = mysqli_real_escape_string($connection, $now);

    log_this("check_jc_qr_payment: JC $jcno SUCCESS fields — rrn=$rrn txn_id=$txn_id vpa=$cust_vpa paid_at=$now (IST)");

    // Always overwrite paid_at with current IST time — webhook stores UTC causing 5:30hr mismatch
    mysqli_query($connection,
        "UPDATE canara_transactions
            SET status='SUCCESS', paid_at='$safe_paid', updated_at='$now'"
        . ($rrn      ? ", rrn='$rrn'"              : '')
        . ($txn_id   ? ", txn_id='$txn_id'"         : '')
        . ($cust_vpa ? ", customer_vpa='$cust_vpa'" : '')
        . " WHERE ext_transaction_id='$safe_ext' AND status!='SUCCESS'"
    );

    // Auto-convert parked JC → SQ
    $pr = mysqli_query($connection,
        "SELECT * FROM pending_qr_closures WHERE jobcard_no=$safe_jc AND status='PENDING' LIMIT 1");
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
            mysqli_query($connection,
                "UPDATE pending_qr_closures
                    SET status='CONVERTED', converted_sq_no=$sq,
                        converted_at='$now', updated_at='$now'
                  WHERE jobcard_no=$safe_jc");
            $converted_sq = $sq;
        } elseif ($sq === 'Job Card already Closed') {
            mysqli_query($connection,
                "UPDATE pending_qr_closures SET status='CONVERTED', updated_at='$now'
                  WHERE jobcard_no=$safe_jc");
        }
    }
    log_this("check_jc_qr_payment: JC $jcno SUCCESS (ext=$found_ext_id) — converted_sq=$converted_sq");
}

echo json_encode(['status' => $bank_status, 'converted_sq' => $converted_sq]);
?>
