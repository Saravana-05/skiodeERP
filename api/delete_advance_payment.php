<?php
// SARAVANA - START (Advance Payment Feature - delete pending advance)
session_start();
include_once "../connect_db.php";
header('Content-Type: application/json');

// Only ADMIN allowed to delete
if (!isset($_SESSION['logged_in']) || ($_SESSION['user_type'] !== 'ADMIN' && $_SESSION['user_type'] !== 'SUPERADMIN')) {
    echo json_encode(['status' => 'error', 'message' => 'Access denied. Only Admin users can delete.']);
    exit;
}

$advance_id = (int)($_POST['advance_id'] ?? 0);

if ($advance_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid advance_id.']);
    exit;
}

// Only allow deleting records that are NOT yet linked to a Job Card
$check = mysqli_query($connection, "SELECT is_linked FROM advance_payment_master WHERE advance_id=$advance_id");
if ($row = mysqli_fetch_assoc($check)) {
    if ($row['is_linked'] == 1) {
        echo json_encode(['status' => 'error', 'message' => 'Cannot delete a record already linked to a Job Card.']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Record not found.']);
    exit;
}

// Delete journal entry posted for this advance
mysqli_query($connection, "DELETE FROM journal_details WHERE link_key='ADV_{$advance_id}'");

// Delete the advance record
if (mysqli_query($connection, "DELETE FROM advance_payment_master WHERE advance_id=$advance_id AND is_linked=0")) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($connection)]);
}
// SARAVANA - END
?>
