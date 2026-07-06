<?php
session_start();
include_once "../connect_db.php";

$from = date("Y-m-d", strtotime($_GET['dt1']));
$to = date("Y-m-d", strtotime($_GET['dt2']));
$result=""; 
$jc_cnt=0;$sq_cnt=0;$si_cnt=0;
$sql="SELECT count(jobcard_no) as cnt FROM jobcard_master where jobcard_date between '".$from."' and '".$to."'";
//echo $sql;
$stmt = $connection->query($sql);
 if ($row = $stmt->fetch_assoc()) {
	 $jc_cnt=$row["cnt"];
	$result.=$row["cnt"].",";
}
$sql="SELECT count(quotation_no) as cnt FROM sales_quotation_master where quotation_dt_tm between '".$from." 00:00:00' and '".$to." 23:59:59'";
$stmt = $connection->query($sql);
 if ($row = $stmt->fetch_assoc()) {
	 $sq_cnt=$row["cnt"];
	$result.=$row["cnt"].",";
}

$sql="SELECT count(invoice_no) as cnt FROM sales_invoice_master where invoice_dt_tm between '".$from." 00:00:00' and '".$to." 23:59:59'";
$stmt = $connection->query($sql);
 if ($row = $stmt->fetch_assoc()) {
	 $si_cnt=$row["cnt"];
	$result.=$row["cnt"].",";
}
$result.=($jc_cnt-$sq_cnt).",";
/* $sql="SELECT sum(approximate_amount-advance_amount) as balance FROM jobcard_master where jobcard_date between '".$from."' and '".$to."'  and job_card_closed=0 and void_job_card=0";
$stmt = $connection->query($sql);
 if ($row = $stmt->fetch_assoc()) {
	$result.=$row["balance"].",";
} */
echo $result;
?>