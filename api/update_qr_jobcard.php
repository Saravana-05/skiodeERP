<?php
/**
 * After saving a job card, link an existing canara_transaction to the new JC number.
 * Called when QR was generated before the JC was saved (jobcard_no was null at QR time).
 * POST { ext_transaction_id, jobcard_no }
 */
mysqli_report(MYSQLI_REPORT_OFF);
header('Content-Type: application/json');
session_start();
if (empty($_SESSION['logged_in'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
include_once __DIR__ . '/../connect_db.php';

$ext_id     = isset($_POST['ext_transaction_id']) ? trim($_POST['ext_transaction_id']) : '';
$jobcard_no = isset($_POST['jobcard_no'])          ? trim($_POST['jobcard_no'])          : '';

if (!$ext_id || !$jobcard_no) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$safe_ext = mysqli_real_escape_string($connection, $ext_id);
$safe_jc  = mysqli_real_escape_string($connection, $jobcard_no);

mysqli_query($connection,
    "UPDATE canara_transactions
        SET jobcard_no     = '$safe_jc',
            payment_source = 'JC',
            updated_at     = NOW()
      WHERE ext_transaction_id = '$safe_ext'
        AND (jobcard_no IS NULL OR jobcard_no = '')"
);

echo json_encode(['success' => true, 'affected' => mysqli_affected_rows($connection)]);
?>
