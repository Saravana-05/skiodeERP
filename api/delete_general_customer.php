<?php
session_start();
include_once "../connect_db.php";
header('Content-Type: application/json');

// Only ADMIN / SUPERADMIN allowed
if (!isset($_SESSION['logged_in']) ||
    !in_array($_SESSION['user_type'] ?? '', ['ADMIN', 'SUPERADMIN'])) {
    echo json_encode(['status' => 'error', 'message' => 'Access denied. Only Admin users can delete.']);
    exit;
}

$mobile_key = mysqli_real_escape_string($connection, trim($_POST['mobile_key'] ?? ''));

if ($mobile_key === '') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid customer key.']);
    exit;
}

// Count how many job cards will be affected
$count_qry = mysqli_query($connection,
    "SELECT COUNT(*) AS cnt FROM jobcard_master
     WHERE customer_type = 'General' AND customer_mobile_no = '$mobile_key'");
$affected_count = 0;
if ($count_qry && $count_row = mysqli_fetch_assoc($count_qry)) {
    $affected_count = (int)$count_row['cnt'];
}

// Clear the customer's personal details from all their job cards.
// The job cards remain intact (amounts, items, status) — only the identity is erased.
$sql = "UPDATE jobcard_master
        SET customer_name      = '',
            customer_mobile_no = '',
            customer_addr1     = '',
            customer_addr2     = '',
            customer_city      = '',
            customer_gst_no    = ''
        WHERE customer_type = 'General'
          AND customer_mobile_no = '$mobile_key'";

if (mysqli_query($connection, $sql)) {
    echo json_encode([
        'status'   => 'success',
        'affected' => $affected_count,
        'message'  => 'Customer deleted successfully.'
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($connection)]);
}
?>
