<?php
session_start();
include_once "../connect_db.php";

$from = date("Y-m-d", strtotime($_GET['dt1']));
$to = date("Y-m-d", strtotime($_GET['dt2']));
$result="<table>"; 

$sql="select y.machine_code,x.cnt from (SELECT a.machine_code,sum(a.sheet_count) as cnt FROM jobcard_details a inner join ( select jobcard_id,jobcard_date from jobcard_master where jobcard_date between '".$from."' and '".$to."') b on a.jobcard_id=b.jobcard_id group by a.machine_code) x RIGHT join machine_master y on x.machine_code=y.machine_code;";
//echo $sql;
$stmt = $connection->query($sql);
 while($row = $stmt->fetch_assoc()) {
	$md_sql="select * from machine_eod_counter_details where machine_code='".$row["machine_code"]."' and dt <'".$from."'  order by dt desc limit 1";
	$prev_dt="";
	$prev_dt_cntr=0;
	if($md_qry=mysqli_query($connection,$md_sql))
	{
		if($md_row=mysqli_fetch_array($md_qry))
		{
			$prev_dt=date("d-m-Y", strtotime($md_row["dt"]));
			$prev_dt_cntr=$md_row["machine_counter_reading"];			
		}
	}
	$result.="<tr><th>".$row["machine_code"]."</th><td>:</td><td title='Closing As on ".$prev_dt."'>".$prev_dt_cntr." + </td><td>".$row["cnt"]."</td><td> = ".($prev_dt_cntr+$row["cnt"])."</td></tr>";
}
$result.="</table>"; 
echo $result;
?>