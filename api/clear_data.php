<?php
session_start();
include_once '../connect_db.php';
$result="Failed";
if(isset($_POST["access_code"]))
{
	$access_code=$_POST["access_code"];
	if($access_code=="CPSSR@2025")
	{
		$sql="START TRANSACTION;
truncate jobcard_details;
truncate jobcard_master;
truncate job_card_wise_machine_spillage;
truncate job_card_wise_machine_usage;
truncate job_card_wise_material_spillage;
truncate job_card_wise_material_usage;
truncate journal_details;
truncate purchase_master;
truncate sales_invoice_gst_details;
truncate sales_invoice_master;
truncate sales_quotation_master;
truncate wastage_master;
truncate receipt_voucher;
truncate purchase_details;
truncate payment_voucher;
COMMIT;";
	if(mysqli_multi_query($connection,$sql))
	{
		$result="Success";
	}
/* 	$ar_sql=split(";",$sql);
		$result="Success";
		foreach ($ar_sql as $sq)
		{
			echo $sq."<br>";
			if(!mysqli_query($connection,$sq))
			{
				$result="Failed";
			}
		} */
	}
}
echo $result;
?>