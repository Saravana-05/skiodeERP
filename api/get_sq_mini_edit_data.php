<?php
session_start();
include_once '../connect_db.php';
$result="Failed";
if(isset($_REQUEST['quotation_no_txt']))
{
	$quotation_no_txt=$_REQUEST['quotation_no_txt'];
	$sql="select * from sales_quotation_master where quotation_no=".$quotation_no_txt.";";
	if($qry=mysqli_query($connection,$sql))
	{
		if($row=mysqli_fetch_array($qry))
		{
			$result=$row["quotation_no"].",".$row["discount"].",".$row["balance_cash"].",".$row["balance_gpay"];
		}
	}
}
echo $result;