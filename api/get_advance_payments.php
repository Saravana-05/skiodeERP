<?php
// SARAVANA - START (Advance Payment Feature - new API file)
session_start();
include_once "../connect_db.php";
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in'])) {
    echo json_encode([]);
    exit;
}

// Safety: return empty array if table doesn't exist yet
$tbl_chk = mysqli_query($connection, "SHOW TABLES LIKE 'advance_payment_master'");
if (!$tbl_chk || mysqli_num_rows($tbl_chk) === 0) {
    echo json_encode([]);
    exit;
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'pending'; // pending | linked | all

if ($filter === 'pending') {
    $where = "WHERE is_linked = 0";
} elseif ($filter === 'linked') {
    $where = "WHERE is_linked = 1";
} else {
    $where = "";
}

$sql = "SELECT * FROM advance_payment_master $where ORDER BY created_dt_tm DESC";
$result = mysqli_query($connection, $sql);

$rows = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
}
echo json_encode($rows);
// SARAVANA - END (Advance Payment Feature - new API file)
?>
