<?php
header('Content-Type: application/json');
include_once '../connect_db.php';

$job_type = isset($_GET['job_type']) ? mysqli_real_escape_string($connection, $_GET['job_type']) : '';
$result   = [];

if ($job_type) {
    $sql = "SELECT DISTINCT c.category2
            FROM product_master c
            JOIN jobcard_details b ON b.product_code = c.product_code
            WHERE b.job_type = '$job_type'
              AND c.category2 IS NOT NULL AND c.category2 != ''
            ORDER BY c.category2";
    if ($qry = mysqli_query($connection, $sql))
        while ($row = mysqli_fetch_row($qry))
            $result[] = $row[0];
}

echo json_encode($result);
?>
