<?php
session_start();
include_once '../connect_db.php';
$result="Failed";
if(isset($_POST['jobcard_no']))
{
	$jobcard_no = $_POST['jobcard_no'];
	$quotation_no=0;
	$invoice_no=0;
	$dt = date("Y-m-d H:i:s");
	if($_POST['close_job_card_type'] == 'quotation')
	{
		$sql = "INSERT INTO `quotation_master`(`jobcard_no`, `quotation_dt_tm`) VALUES (".$jobcard_no.",'".$dt."')";
 		mysqli_query($connection,$sql);
		$quotation_no = mysqli_insert_id($connection);
		
	}
	else if($_POST['close_job_card_type'] == 'invoice')
	{		 
		$sql = "INSERT INTO `invoice_master`(`jobcard_no`, `invoice_dt_tm`) VALUES (".$jobcard_no.",'".$dt."')";
	 	mysqli_query($connection,$sql);
		$invoice_no = mysqli_insert_id($connection);
	}
	$balance_received_total=0;
	if($_POST['balance_received_total'] != "")
		$balance_received_total=$_POST['balance_received_total'];
	
	$sql="update jobcard_master set job_card_closed=1, 	job_card_quotation_no=".$quotation_no." , job_card_invoice_no=".$invoice_no." , job_card_closed_on='".$dt."', is_bal_in_gpay=".$_POST["is_bal_in_gpay"].",balance_gpay=".$_POST['balance_gpay'].",is_bal_in_cash=".$_POST['is_bal_in_cash'].",balance_cash=".$_POST['balance_cash'].",balance_received_total=".$balance_received_total.",discount_amount=".$_POST['discount']." where jobcard_no=".$jobcard_no;	 
 	mysqli_query($connection,$sql);
	
	$materialArr = json_decode($_POST['material_spillage_details'], true);
	for($m=0;$m<count($materialArr);$m++)
	{
		$sql="INSERT INTO `job_card_wise_material_spillage`(`jobcard_no`, `material_code`, `jobcard_date`, `spillage_count`) VALUES ('".$materialArr[$m]["jobcard_no"]."','".$materialArr[$m]["jobcard_date"]."','".$materialArr[$m]["material_code"]."','".$materialArr[$m]["spillage_count"]."')"; 
		 mysqli_query($connection,$sql);
	}
		
	$machineArr = json_decode($_POST['machine_spillage_details'], true);
	for($m=0;$m<count($machineArr);$m++)
	{
		$sql="INSERT INTO `job_card_wise_machine_spillage`(`jobcard_no`, `machine_code`, `jobcard_date`, `spillage_count`) VALUES ('".$materialArr[$m]["jobcard_no"]."','".$materialArr[$m]["jobcard_date"]."','".$materialArr[$m]["machine_code"]."','".$materialArr[$m]["spillage_count"]."')"; 
		 mysqli_query($connection,$sql); 
	}
	if($_POST["cust_type"] != 'Credit')
	{
		$description ="Balance amount for JC : ".$jobcard_no;
/* 		if($_POST["is_bal_in_gpay"])
		{
			$sql="INSERT INTO `journal_details`(`dt`, `achead`, `description`, `amount`) VALUES ('".$dt."','GPAY','".$description."','".$_POST['balance_gpay']."')"; 
			mysqli_query($connection,$sql); 
		}
		if($_POST["is_bal_in_cash"])
		{
			$sql="INSERT INTO `journal_details`(`dt`, `achead`, `description`, `amount`) VALUES ('".$dt."','CASH','".$description."','".$_POST['balance_cash']."')"; 
			mysqli_query($connection,$sql); 
		}
		 */
		
	}
	$result="Success";
}
echo $result;

// Save per-item discounts into jobcard_details
if(isset($_POST['item_discount_details'])) {
    $item_discount_details = json_decode($_POST['item_discount_details'], true);

    foreach($item_discount_details as $item) {
        $detail_id    = intval($item['jobcard_details_id']);
        $item_discount = floatval($item['item_discount']);
        $net_value     = floatval($item['net_value']);

        $sql = "UPDATE jobcard_details 
                SET item_discount = $item_discount 
                WHERE jobcard_details_id = $detail_id";
        mysqli_query($connection, $sql);
    }
}

// Optionally save total item discount to jobcard_master
// First add column if not exists:
// ALTER TABLE jobcard_master ADD COLUMN item_discount_total DECIMAL(10,2) DEFAULT 0;
$total_item_discount = floatval($_POST['total_item_discount'] ?? 0);
$jc_no = intval($_POST['jobcard_no']);
$sql2 = "UPDATE jobcard_master 
         SET item_discount_total = $total_item_discount 
         WHERE jobcard_no = $jc_no";
mysqli_query($connection, $sql2);
?>