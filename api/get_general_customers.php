<?php
session_start();
include_once "../connect_db.php";
header('Content-Type: application/json');

// For each mobile number, pick the most recent job card that has a non-empty customer name.
// The old plain GROUP BY picked an arbitrary row (could be one with an empty name).
$sql = "SELECT jm.customer_name,
               jm.customer_mobile_no,
               jm.customer_addr1,
               jm.customer_addr2,
               jm.customer_city,
               jm.customer_state,
               jm.customer_gst_no
        FROM jobcard_master jm
        INNER JOIN (
            SELECT customer_mobile_no, MAX(jobcard_id) AS max_id
            FROM jobcard_master
            WHERE customer_type    = 'General'
              AND customer_mobile_no != ''
              AND TRIM(customer_name)  != ''
            GROUP BY customer_mobile_no
        ) latest ON jm.jobcard_id = latest.max_id
        ORDER BY jm.customer_name ASC";

$rows = [];
if ($qry = mysqli_query($connection, $sql)) {
    while ($row = mysqli_fetch_assoc($qry)) {
        $rows[] = $row;
    }
}
echo json_encode($rows);
?>
