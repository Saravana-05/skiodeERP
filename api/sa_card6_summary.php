<?php
session_start();
include_once "../connect_db.php";

$from = date("Y-m-d", strtotime($_GET['dt1']));
$to = date("Y-m-d", strtotime($_GET['dt2']));
$result="<table>"; 

$sql="select y.machine_code,x.cnt from (SELECT a.machine_code,sum(a.sheet_count) as cnt FROM jobcard_details a inner join ( select jobcard_id,jobcard_date from jobcard_master where jobcard_date between '".$from."' and '".$to."') b on a.jobcard_id=b.jobcard_id group by a.machine_code) x RIGHT join machine_master y on x.machine_code=y.machine_code;";
$sql="select DISTINCT created_by from jobcard_master where jobcard_date between '".$from."' and '".$to."';";
$stmt = $connection->query($sql);
$i=0;
 while($row = $stmt->fetch_assoc()) {
	$i++; 
	$result.="<tr><th>".$i."</th><td>:</td><td>".$row["created_by"]."</td></tr>";
}
$result.="</table>"; 
echo $result;
?>