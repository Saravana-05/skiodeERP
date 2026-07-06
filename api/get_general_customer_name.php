<?php
session_start();
include_once '../connect_db.php';
$result="";

// Lookup customer name by mobile number
if (isset($_GET['mb_no'])) {
    $mb_no = mysqli_real_escape_string($connection, $_GET['mb_no']);

    // First check jobcard_master (most recent entry)
    $sql = "SELECT customer_name FROM jobcard_master
            WHERE customer_mobile_no='$mb_no' AND customer_name != ''
            ORDER BY jobcard_id DESC LIMIT 1";
    if ($query = mysqli_query($connection, $sql)) {
        if ($row = $query->fetch_assoc()) {
            $result = $row["customer_name"];
        }
    }

    // If not found in jobcard_master, check customer_master (advance payment customers)
    if ($result == "") {
        $sql2 = "SELECT customer_name FROM customer_master
                 WHERE mobile_no='$mb_no' AND customer_type='General' LIMIT 1";
        if ($query2 = mysqli_query($connection, $sql2)) {
            if ($row2 = $query2->fetch_assoc()) {
                $result = $row2["customer_name"];
            }
        }
    }
}
echo $result;

// Lookup mobile number by customer name
if (isset($_GET['cust_name']) && $_GET['cust_name'] != "") {
    $cust_name = mysqli_real_escape_string($connection, $_GET['cust_name']);

    // First check jobcard_master
    $sql = "SELECT customer_mobile_no FROM jobcard_master
            WHERE customer_type='General' AND customer_name='$cust_name'
            ORDER BY jobcard_id DESC LIMIT 1";
    $qry = mysqli_query($connection, $sql);
    if ($row = mysqli_fetch_array($qry)) {
        echo $row["customer_mobile_no"];
        exit;
    }

    // If not found, check customer_master (advance payment customers)
    $sql2 = "SELECT mobile_no FROM customer_master
             WHERE customer_type='General' AND customer_name='$cust_name' LIMIT 1";
    $qry2 = mysqli_query($connection, $sql2);
    if ($row2 = mysqli_fetch_array($qry2)) {
        echo $row2["mobile_no"];
    }
    exit;
}
?>