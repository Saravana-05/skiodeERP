<?php
session_start();
include_once "../connect_db.php";

$from = date("Y-m-d", strtotime($_GET['dt1']));
$to = date("Y-m-d", strtotime($_GET['dt2']));
$result="<table>"; 

$sql="select y.machine_code,x.cnt from (SELECT a.machine_code,sum(a.sheet_count) as cnt FROM jobcard_details a inner join ( select jobcard_id,jobcard_date from jobcard_master where jobcard_date between '".$from."' and '".$to."') b on a.jobcard_id=b.jobcard_id group by a.machine_code) x RIGHT join machine_master y on x.machine_code=y.machine_code;";
$cash=0.0;
$sb=0.0;
$ca=0.0;
$discount=0.0;
$cash+=get_sum("SELECT sum(advance_cash) as sum_cash FROM `jobcard_master` where jobcard_date between '".$from."' and '".$to."';");
//$sb+=get_sum("SELECT sum(advance_gpay) as sum_sb FROM `jobcard_master` where jobcard_date between '".$from."' and '".$to."' and advance_gpay<2000;");
//$ca+=get_sum("SELECT sum(advance_gpay) as sum_ca FROM `jobcard_master` where jobcard_date between '".$from."' and '".$to."' and advance_gpay>=2000;");
$sb+=get_sum("SELECT sum(advance_gpay) as sum_sb FROM `jobcard_master` where jobcard_date between '".$from."' and '".$to."' and advance_gpay>0;");
//$ca+=get_sum("SELECT sum(advance_gpay) as sum_ca FROM `jobcard_master` where jobcard_date between '".$from."' and '".$to."' and advance_gpay<0;");

$cash+=get_sum("SELECT sum(balance_cash) as sum_cash FROM `sales_quotation_master` WHERE quotation_dt_tm between '".$from." 00:00:00' and '".$to." 23:59:59';");
//$sb+=get_sum("SELECT sum(balance_gpay) as sum_sb  FROM `sales_quotation_master` WHERE quotation_dt_tm between '".$from." 00:00:00' and '".$to." 23:59:59' and balance_gpay<2000;");
//$ca+=get_sum("SELECT sum(balance_gpay) as sum_ca  FROM `sales_quotation_master` WHERE quotation_dt_tm between '".$from." 00:00:00' and '".$to." 23:59:59' and balance_gpay>=2000;");
$sb+=get_sum("SELECT sum(balance_gpay) as sum_sb  FROM `sales_quotation_master` WHERE quotation_dt_tm between '".$from." 00:00:00' and '".$to." 23:59:59' and balance_gpay>0;");
//$ca+=get_sum("SELECT sum(balance_gpay) as sum_ca  FROM `sales_quotation_master` WHERE quotation_dt_tm between '".$from." 00:00:00' and '".$to." 23:59:59' and balance_gpay>=2000;");
$discount+=get_sum("SELECT sum(discount) as sum_discount  FROM `sales_quotation_master` WHERE quotation_dt_tm between '".$from." 00:00:00' and '".$to." 23:59:59';");
//echo $sql;

	$result.="<tr><th>CASH</th><td>:</td><td>".$cash."</td></tr>";
	$result.="<tr><th>SAVINGS A/C</th><td>:</td><td>".$sb."</td></tr>";
	$result.="<tr><th>CURRENT A/C</th><td>:</td><td>".$ca."</td></tr>";
	$result.="<tr><th>DISCOUNT</th><td>:</td><td>".$discount."</td></tr>";

$result.="</table>"; 
echo $result;
function get_sum($sql)
{
	global $connection;
	$stmt = $connection->query($sql);
	$row = $stmt->fetch_array();
	 
	return $row[0];
}

?>