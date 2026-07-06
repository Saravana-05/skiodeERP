<?php
session_start();

// Database credentials
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "cp_ssr_db";

// Create a MySQLi connection
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Example: Fetch user with ID = 1
$customer_id = 1;

$sql = "SELECT customer_name FROM customer_master WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($row = $result->fetch_assoc()) {
    $_SESSION['customer_name'] = $row['customer_name'];

    echo json_encode([
        'success' => true,
        'customer_name' => $_SESSION['customer_name']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'User not found'
    ]);
}

$stmt->close();
$conn->close();
?>
