<?php
session_start();
include_once '../connect_db.php';
$result="Invalid Attempt";
$sql="";

if(isset($_SESSION['user_type']))
{
	
	if($_SESSION['user_type']=="ADMIN" || $_SESSION['user_type']==SUPERADMIN)
	{
		$invoice_no_txt=0;
		if(isset($_GET["invoice_no_txt"])) 
		{
			$invoice_no_txt=$_GET["invoice_no_txt"];
			$sql="select * from sales_invoice_master where invoice_no=".$invoice_no_txt;
			$sq_no="";
			if($qry=mysqli_query($connection,$sql))
			{
				if($row=mysqli_fetch_array($qry))
				{
					$sq_no=$row["sale_quotation_nos"];
				}
			}
			
			
			$sql="START TRANSACTION;".PHP_EOL;
			$sql.="update sales_quotation_master set is_this_converted_into_invoice=0,invoice_no=NULL,invoice_converted_by=NULL where quotation_no in (".$sq_no.");".PHP_EOL;
			$sql.="delete FROM journal_details where link_key='SI_".$invoice_no_txt."';".PHP_EOL;
			$sql.="delete from sales_invoice_master where invoice_no=".$invoice_no_txt.";".PHP_EOL;
			$sql.="delete from sales_invoice_gst_details where invoice_no=".$invoice_no_txt.";".PHP_EOL;
			$sql.="COMMIT;".PHP_EOL;	
			mysqli_multi_query($connection,$sql);
			
			$result="Done";
		}
	}
}

/* echo "<pre>";
echo $sql; */
echo $result;
?>