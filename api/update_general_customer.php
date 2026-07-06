<?php
session_start();
include_once "../connect_db.php";
header('Content-Type: application/json');

$mobile_key      = mysqli_real_escape_string($connection, trim($_POST['mobile_key']));
$customer_name   = mysqli_real_escape_string($connection, trim($_POST['customer_name']));
$customer_mobile = mysqli_real_escape_string($connection, trim($_POST['customer_mobile']));
$customer_addr1  = mysqli_real_escape_string($connection, trim($_POST['customer_addr1']));
$customer_addr2  = mysqli_real_escape_string($connection, trim($_POST['customer_addr2']));
$customer_city   = mysqli_real_escape_string($connection, trim($_POST['customer_city']));
$customer_gst_no = mysqli_real_escape_string($connection, trim($_POST['customer_gst_no']));

if ($mobile_key === '') {
    echo json_encode(["status" => "error", "message" => "Invalid customer key."]);
    exit;
}

$sql = "UPDATE jobcard_master SET
            customer_name        = '$customer_name',
            customer_mobile_no   = '$customer_mobile',
            customer_addr1       = '$customer_addr1',
            customer_addr2       = '$customer_addr2',
            customer_city        = '$customer_city',
            customer_gst_no      = '$customer_gst_no'
        WHERE customer_type = 'General'
          AND customer_mobile_no = '$mobile_key'";

if (mysqli_query($connection, $sql)) {
    $updated = mysqli_affected_rows($connection);
    echo json_encode(["status" => "success", "updated" => $updated]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($connection)]);
}
?>
