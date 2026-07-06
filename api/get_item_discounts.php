<?php
session_start();
include_once '../connect_db.php';

header('Content-type: application/json');

if (!isset($_GET['jobcard_no'])) {
    echo json_encode([]);
    exit;
}

// Support comma-separated JC numbers (multi-JC close scenario)
$jc_parts = array_filter(array_map('intval', explode(',', $_GET['jobcard_no'])));
if (empty($jc_parts)) {
    echo json_encode([]);
    exit;
}
$safe_jcnos = implode(',', $jc_parts);

$sql = "SELECT jobcard_details_id,
               jobcard_no,
               product_code,
               job_in_detail,
               total_qty,
               value_amount,
               value_discountable,
               COALESCE(item_discount, 0) AS item_discount
        FROM jobcard_details
        WHERE jobcard_no IN ($safe_jcnos)
        ORDER BY jobcard_no ASC, jobcard_details_id ASC";

$data = [];
if ($qry = mysqli_query($connection, $sql)) {
    while ($row = mysqli_fetch_assoc($qry)) {
        $data[] = $row;
    }
} else {
    echo json_encode(['error' => mysqli_error($connection)]);
    exit;
}

echo json_encode($data);
?>