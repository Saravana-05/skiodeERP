<?php
session_start();
include_once "../connect_db.php";
$sqno=$_POST["sqno"];
$sql="select * from sales_quotation_master  where quotation_no in (".$sqno.");";
$result="";
if($qry=mysqli_query($connection,$sql))
{
	$discount=0.0;
	$bal_gpay=0.0;
	$bal_cash=0.0;
	$bal_rcvd=0.0;
	$quotation_dt_tm="";
	while($row=mysqli_fetch_array($qry))
	{
		$discount+=$row["discount"];
		$bal_gpay+=$row["balance_gpay"];
		$bal_cash+=$row["balance_cash"];
		$bal_rcvd+=$row["balance_received_total"];
		$quotation_dt_tm=date("d-m-Y",strtotime($row["quotation_dt_tm"]));
		//$result.=",".$row[""];
	}
	$result.=$discount;
	$result.=",".$bal_gpay;
	$result.=",".$bal_cash;
	$result.=",".$bal_rcvd;	
	$result.=",/".$quotation_dt_tm;	
}
echo $result;
?>