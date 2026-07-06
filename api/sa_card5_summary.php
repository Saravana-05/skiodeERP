<?php
session_start();
include_once "../connect_db.php";

$from = date("Y-m-d", strtotime($_GET['dt1']));
$to = date("Y-m-d", strtotime($_GET['dt2']));
$result=""; 

$sql="SELECT count(jobcard_no) as cnt FROM jobcard_master where jobcard_date between '".$from."' and '".$to."' and customer_type='General'";
//echo $sql;
$stmt = $connection->query($sql);
 if ($row = $stmt->fetch_assoc()) {
	$result.=$row["cnt"].",";
}
$sql="SELECT count(jobcard_no) as cnt FROM jobcard_master where jobcard_date between '".$from."' and '".$to."' and customer_type='WalkIn'";
//echo $sql;
$stmt = $connection->query($sql);
 if ($row = $stmt->fetch_assoc()) {
	$result.=$row["cnt"].",";
}
$sql="SELECT count(jobcard_no) as cnt FROM jobcard_master where jobcard_date between '".$from."' and '".$to."' and customer_type='Credit'";
//echo $sql;
$stmt = $connection->query($sql);
 if ($row = $stmt->fetch_assoc()) {
	$result.=$row["cnt"].",";
}

echo $result;
?>