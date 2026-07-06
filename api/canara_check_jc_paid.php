<?php
mysqli_report(MYSQLI_REPORT_OFF);
include_once __DIR__ . '/../connect_db.php';

header('Content-Type: application/json');

$jc = isset($_GET['jc']) ? mysqli_real_escape_string($connection, trim($_GET['jc'])) : '';
$paid = false;

if ($jc) {
    $r = mysqli_query($connection,
        "SELECT id FROM canara_transactions WHERE jobcard_no='$jc' AND status='SUCCESS' LIMIT 1");
    if ($r && mysqli_num_rows($r) > 0) {
        $paid = true;
    }
}

echo json_encode(['paid' => $paid]);
?>
