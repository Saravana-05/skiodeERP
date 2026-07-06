<?php
/**
 * debug_qr_auto_convert.php
 * Diagnostic + manual trigger for pending QR → SQ auto-conversion.
 * REMOVE or PASSWORD-PROTECT this file after debugging is done.
 *
 * Usage:
 *   Check status : GET  ?jc=12711
 *   Force convert: GET  ?jc=12711&force=1
 */
mysqli_report(MYSQLI_REPORT_OFF);
session_start();
include_once __DIR__ . '/../connect_db.php';
fn_ensure_pending_qr_table($connection);

header('Content-Type: application/json');

$jc    = intval($_GET['jc']   ?? 0);
$force = intval($_GET['force'] ?? 0);

if (!$jc) {
    echo json_encode(['error' => 'Pass ?jc=<jobcard_no>']);
    exit;
}

$safe_jc = intval($jc);

// ── 1. canara_transactions row ───────────────────────────────────────────────
$txn = null;
$r = mysqli_query($connection,
    "SELECT ext_transaction_id, status, amount, paid_at
     FROM canara_transactions WHERE jobcard_no='$safe_jc' ORDER BY created_at DESC LIMIT 1");
if ($r && $row = mysqli_fetch_assoc($r)) $txn = $row;

// ── 2. pending_qr_closures row ───────────────────────────────────────────────
$pend = null;
$r2 = mysqli_query($connection,
    "SELECT * FROM pending_qr_closures WHERE jobcard_no=$safe_jc ORDER BY created_at DESC LIMIT 1");
if ($r2 && $row2 = mysqli_fetch_assoc($r2)) $pend = $row2;

$out = [
    'jobcard_no'         => $safe_jc,
    'canara_transaction' => $txn,
    'pending_closure'    => $pend,
    'conversion_result'  => null,
];

// ── 3. Force convert if requested ────────────────────────────────────────────
if ($force && $pend && $pend['status'] === 'PENDING') {
    $params = [
        'jcnos'             => $pend['jobcard_no'],
        'approximate_amount'=> $pend['approximate_amount'],
        'balance_cash'      => $pend['balance_cash'],
        'balance_gpay'      => $pend['balance_gpay'],
        'balance_amount'    => $pend['balance_amount'],
        'discount'          => $pend['discount_amount'],
        'is_bal_in_cash'    => $pend['is_bal_in_cash'],
        'is_bal_in_gpay'    => $pend['is_bal_in_gpay'],
        'is_gst_extra'      => $pend['is_gst_extra'],
        'gst_tax_amount'    => $pend['gst_tax_amount'],
    ];
    $user      = $pend['created_by'] ?: 'DEBUG';
    $sq_result = fn_convert_jc_to_sq($connection, $params, $user);
    $now       = date('Y-m-d H:i:s');

    if ($sq_result !== 'Failed' && $sq_result !== 'Job Card already Closed') {
        mysqli_query($connection,
            "UPDATE pending_qr_closures
                SET status='CONVERTED', converted_sq_no=$sq_result, converted_at='$now', updated_at='$now'
              WHERE jobcard_no=$safe_jc");
    }
    $out['conversion_result'] = $sq_result;
} elseif ($force && $pend && $pend['status'] !== 'PENDING') {
    $out['conversion_result'] = 'Already ' . $pend['status'];
} elseif ($force && !$pend) {
    $out['conversion_result'] = 'No pending_qr_closures record found for this JC';
}

echo json_encode($out, JSON_PRETTY_PRINT);
?>
