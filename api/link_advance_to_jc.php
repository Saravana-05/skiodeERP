<?php
// SARAVANA - START (Advance Payment Feature - new API file)
session_start();
include_once "../connect_db.php";
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in.']);
    exit;
}

$advance_id  = (int)($_POST['advance_id'] ?? 0);
$jobcard_no  = (int)($_POST['jobcard_no'] ?? 0);

if ($advance_id <= 0 || $jobcard_no <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid advance_id or jobcard_no.']);
    exit;
}

// Prevent double-linking
$check = mysqli_query($connection, "SELECT is_linked FROM advance_payment_master WHERE advance_id=$advance_id");
if ($row = mysqli_fetch_assoc($check)) {
    if ($row['is_linked'] == 1) {
        echo json_encode(['status' => 'error', 'message' => 'Already linked to a job card.']);
        exit;
    }
}

$sql = "UPDATE advance_payment_master SET is_linked=1, linked_jobcard_no=$jobcard_no WHERE advance_id=$advance_id";
if (mysqli_query($connection, $sql)) {
    // Update journal entry link_key to include the JC number
    mysqli_query($connection,
        "UPDATE journal_details SET link_key='ADV_{$advance_id}_JC_{$jobcard_no}', description=CONCAT(description,' -> JC:$jobcard_no')
         WHERE link_key='ADV_{$advance_id}'"
    );
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($connection)]);
}
// SARAVANA - END (Advance Payment Feature - new API file)
?>
