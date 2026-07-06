<?php
/**
 * save_pending_qr_closure.php
 * Parks a Job Card for auto-conversion to Sales Quote when the Canara QR
 * payment is confirmed by the bank callback.
 *
 * POST params (same names as close_jobcard_into_salesquote.php):
 *   jcnos, approximate_amount_txt, balance_gpay_txt, balance_cash_txt,
 *   balance_amount_txt, discount_txt, is_bal_in_cash_txt, is_bal_in_gpay_txt,
 *   is_gst_extra_txt, gst_tax_amount_txt, ext_transaction_id (optional)
 *
 * Returns JSON { success: true } or { success: false, error: "..." }
 */
mysqli_report(MYSQLI_REPORT_OFF);
session_start();
include_once __DIR__ . '/../connect_db.php';
fn_ensure_pending_qr_table($connection);

header('Content-Type: application/json');

// Parse comma-separated JC numbers (multi-JC close support).
// jobcard_no  = first JC integer — used for the UNIQUE KEY / JOIN with canara_transactions.
// jobcard_nos = full CSV string  — passed to fn_convert_jc_to_sq to close ALL selected JCs.
$jcnos_raw     = trim($_POST['jcnos'] ?? '');
$jcnos_parts   = array_filter(array_map('intval', explode(',', $jcnos_raw)));
$jobcard_no    = !empty($jcnos_parts) ? reset($jcnos_parts) : 0;
$jcnos_str     = implode(',', $jcnos_parts);
$safe_jcnos    = mysqli_real_escape_string($connection, $jcnos_str);

$approx_amount  = floatval($_POST['approximate_amount_txt'] ?? 0);
$balance_gpay   = floatval($_POST['balance_gpay_txt']       ?? 0);
$balance_cash   = floatval($_POST['balance_cash_txt']       ?? 0);
$balance_amount = floatval($_POST['balance_amount_txt']     ?? 0);
$discount       = floatval($_POST['discount_txt']           ?? 0);
$is_bal_in_cash = intval($_POST['is_bal_in_cash_txt']       ?? 0);
$is_bal_in_gpay = intval($_POST['is_bal_in_gpay_txt']       ?? 1);
$is_gst_extra   = intval($_POST['is_gst_extra_txt']         ?? 0);
$gst_tax_amount = floatval($_POST['gst_tax_amount_txt']     ?? 0);
$ext_id_raw     = trim($_POST['ext_transaction_id']         ?? '');
$created_by     = mysqli_real_escape_string($connection, $_SESSION['user_name'] ?? 'system');
$now            = date('Y-m-d H:i:s');

if ($jobcard_no <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid job card number']);
    exit;
}

$ext_val = $ext_id_raw
    ? "'" . mysqli_real_escape_string($connection, $ext_id_raw) . "'"
    : 'NULL';

$sql = "INSERT INTO pending_qr_closures
            (jobcard_no, jobcard_nos, ext_transaction_id, approximate_amount, balance_gpay,
             balance_cash, balance_amount, discount_amount, is_bal_in_cash,
             is_bal_in_gpay, is_gst_extra, gst_tax_amount, created_by,
             created_at, updated_at, status)
        VALUES
            ($jobcard_no, '$safe_jcnos', $ext_val, $approx_amount, $balance_gpay,
             $balance_cash, $balance_amount, $discount, $is_bal_in_cash,
             $is_bal_in_gpay, $is_gst_extra, $gst_tax_amount, '$created_by',
             '$now', '$now', 'PENDING')
        ON DUPLICATE KEY UPDATE
            jobcard_nos        = VALUES(jobcard_nos),
            ext_transaction_id = VALUES(ext_transaction_id),
            approximate_amount = VALUES(approximate_amount),
            balance_gpay       = VALUES(balance_gpay),
            balance_cash       = VALUES(balance_cash),
            balance_amount     = VALUES(balance_amount),
            discount_amount    = VALUES(discount_amount),
            is_bal_in_cash     = VALUES(is_bal_in_cash),
            is_bal_in_gpay     = VALUES(is_bal_in_gpay),
            is_gst_extra       = VALUES(is_gst_extra),
            gst_tax_amount     = VALUES(gst_tax_amount),
            created_by         = VALUES(created_by),
            updated_at         = '$now',
            status             = IF(status = 'CONVERTED', 'CONVERTED', 'PENDING')";

if (mysqli_query($connection, $sql)) {
    log_this("Parked JC $jobcard_no for auto QR conversion. ext_id=$ext_id_raw");
    echo json_encode(['success' => true]);
} else {
    $err = mysqli_error($connection);
    log_this("save_pending_qr_closure error: $err");
    echo json_encode(['success' => false, 'error' => $err]);
}
?>
