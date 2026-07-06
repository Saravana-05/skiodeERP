<?php
session_start();
include_once '../connect_db.php';

header('Content-type: application/json');

if (!isset($_POST['jobcard_no']) || !isset($_POST['items'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

// Support comma-separated JC numbers (multi-JC close scenario)
$jc_parts   = array_filter(array_map('intval', explode(',', $_POST['jobcard_no'])));
$safe_jcnos = !empty($jc_parts) ? implode(',', $jc_parts) : '0';
$items = json_decode($_POST['items'], true);

if (!$items || !is_array($items)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid items data']);
    exit;
}

$total_discount = 0;

// Start transaction
mysqli_begin_transaction($connection);

try {
    foreach ($items as $item) {
        $detail_id     = intval($item['detail_id']);
        $item_discount = floatval($item['item_discount']);
        $item_value    = floatval($item['item_value']);

        // Safety check
        if ($item_discount < 0 || $item_discount > $item_value) {
            throw new Exception("Discount for item ID $detail_id is invalid (must be between 0 and $item_value)");
        }

        $sql = "UPDATE jobcard_details
                SET item_discount = $item_discount
                WHERE jobcard_details_id = $detail_id AND jobcard_no IN ($safe_jcnos)";

        if (!mysqli_query($connection, $sql)) {
            throw new Exception("DB update failed: " . mysqli_error($connection));
        }

        $total_discount += $item_discount;
    }

    mysqli_commit($connection);

    echo json_encode([
        'status'         => 'success',
        'total_discount' => round($total_discount, 2),
        'message'        => 'Item discounts saved successfully'
    ]);

} catch (Exception $e) {
    mysqli_rollback($connection);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>