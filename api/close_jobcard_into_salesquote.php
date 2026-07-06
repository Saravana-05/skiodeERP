<?php
mysqli_report(MYSQLI_REPORT_OFF);
session_start();
include_once __DIR__ . '/../connect_db.php';

function customError($errno, $errstr) {
  if (error_reporting() === 0) return false; // respect @ suppressor
  $err_str = "<b>Error:</b> [$errno] $errstr<br>";
  echo $err_str;
  log_this($err_str);
  echo "Ending Script";
  die();
}
set_error_handler("customError");

$params = [
    'jcnos'             => $_POST['jcnos'],
    'approximate_amount'=> (float)($_POST['approximate_amount_txt'] ?? 0),
    'balance_cash'      => (float)($_POST['balance_cash_txt']       ?? 0),
    'balance_gpay'      => (float)($_POST['balance_gpay_txt']       ?? 0),
    'balance_amount'    => (float)($_POST['balance_amount_txt']     ?? 0),
    'discount'          => (float)($_POST['discount_txt']           ?? 0),
    'is_bal_in_cash'    => (int)  ($_POST['is_bal_in_cash_txt']     ?? 0),
    'is_bal_in_gpay'    => (int)  ($_POST['is_bal_in_gpay_txt']     ?? 0),
    'is_gst_extra'      => (int)  ($_POST['is_gst_extra_txt']       ?? 0),
    'gst_tax_amount'    => (float)($_POST['gst_tax_amount_txt']     ?? 0),
    'skip_journal'      => (int)  ($_POST['skip_journal_txt']       ?? 0),
];

// Always use the original JC creator — not whoever is closing it (admin may be closing on behalf of operator)
$jcnos_safe = implode(',', array_filter(array_map('intval', explode(',', $_POST['jcnos']))));
$orig_user   = $_SESSION['user_name'] ?? 'system'; // fallback only
if (!empty($jcnos_safe)) {
    $oc_qry = mysqli_query($connection,
        "SELECT created_by FROM jobcard_master WHERE jobcard_no IN ($jcnos_safe) LIMIT 1");
    if ($oc_qry && $oc_row = mysqli_fetch_assoc($oc_qry)) {
        if (!empty($oc_row['created_by'])) $orig_user = $oc_row['created_by'];
    }
}
$result = fn_convert_jc_to_sq($connection, $params, $orig_user);

echo $result;
?>
