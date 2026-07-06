<?php
include_once "connect_db.php";
$q = isset($_GET['q']) ? $_GET['q'] : '';

if ($q !== '') {
    $sql = "SELECT customer_code,customer_name FROM customer_master WHERE customer_name LIKE '%$q%' LIMIT 10";
    $result = $connection->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='autocomplete-item' cust_id='".$row['customer_code']."'>" . htmlspecialchars($row['customer_name']) . "</div>";
        }
    } else {
        echo "<div class='autocomplete-item'>No match found</div>";
    }
}
?>