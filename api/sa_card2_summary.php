<?php
session_start();
include_once "../connect_db.php";

$from = date("Y-m-d", strtotime($_GET['dt1']));
$to = date("Y-m-d", strtotime($_GET['dt2']));
$result=""; 

$sql="SELECT count(jobcard_no) as cnt FROM jobcard_master where job_type='PRINTING' and jobcard_date between '".$from."' and '".$to."'";
$sql="SELECT COUNT(A.job_type) as cnt FROM jobcard_details a inner join ( select jobcard_id,jobcard_date from jobcard_master where jobcard_date between '".$from."' and '".$to."') b on a.jobcard_id=b.jobcard_id where a.job_type='PRINTING'";
//echo $sql;
$stmt = $connection->query($sql);
 if ($row = $stmt->fetch_assoc()) {
	$result.=$row["cnt"].",";
}
else
	$result.="0,";
$sql="SELECT count(jobcard_no) as cnt FROM jobcard_master where job_type='SERVICES' and jobcard_date between '".$from."' and '".$to."'";
$sql="SELECT COUNT(A.job_type) as cnt FROM jobcard_details a inner join ( select jobcard_id,jobcard_date from jobcard_master where jobcard_date between '".$from."' and '".$to."') b on a.jobcard_id=b.jobcard_id where a.job_type='SERVICES'";

$stmt = $connection->query($sql);
 if ($row = $stmt->fetch_assoc()) {
	$result.=$row["cnt"].",";
}
else
	$result.="0,";
$sql="SELECT count(jobcard_no) as cnt FROM jobcard_master where job_type='MATERIALS' and jobcard_date between '".$from."' and '".$to."'";
$sql="SELECT COUNT(A.job_type) as cnt FROM jobcard_details a inner join ( select jobcard_id,jobcard_date from jobcard_master where jobcard_date between '".$from."' and '".$to."') b on a.jobcard_id=b.jobcard_id where a.job_type='MATERIALS'";
//echo $sql;
$stmt = $connection->query($sql);
 if ($row = $stmt->fetch_assoc()) {
	$result.=$row["cnt"].",";
}
else
	$result.="0,";
$sql="SELECT count(jobcard_no) as cnt FROM jobcard_master where job_type='IDCARDS' and jobcard_date between '".$from."' and '".$to."'";
$sql="SELECT COUNT(A.job_type) as cnt FROM jobcard_details a inner join ( select jobcard_id,jobcard_date from jobcard_master where jobcard_date between '".$from."' and '".$to."') b on a.jobcard_id=b.jobcard_id where a.job_type='IDCARDS'";
//echo $sql;
$stmt = $connection->query($sql);
 if ($row = $stmt->fetch_assoc()) {
	$result.=$row["cnt"].",";
}
else
	$result.="0,";
$sql="SELECT count(jobcard_no) as cnt FROM jobcard_master where job_type='PRODUCTS' and jobcard_date between '".$from."' and '".$to."'";
$sql="SELECT COUNT(A.job_type) as cnt FROM jobcard_details a inner join ( select jobcard_id,jobcard_date from jobcard_master where jobcard_date between '".$from."' and '".$to."') b on a.jobcard_id=b.jobcard_id where a.job_type='PRODUCTS'";
//echo $sql;
$stmt = $connection->query($sql);
 if ($row = $stmt->fetch_assoc()) {
	$result.=$row["cnt"].",";
}
else
	$result.="0,";

echo $result;
?>