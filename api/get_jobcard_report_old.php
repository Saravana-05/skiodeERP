<?php
session_start();
include_once '../connect_db.php';
$result=""; 
if(isset($_GET['from']))
{
	$from = date("Y-m-d", strtotime($_GET['from']));
	$to = date("Y-m-d", strtotime($_GET['to']));
	$status =  $_GET['status'] ;
	if($status == "All")
		$sql="SELECT * FROM jobcard_master where jobcard_date >='".$from."' and jobcard_date<='".$to."'";  
	else if($status == "Open")
		$sql="SELECT * FROM jobcard_master where jobcard_date >='".$from."' and jobcard_date<='".$to."' and job_card_closed=1";  
	else if($status == "Close")
		$sql="SELECT * FROM jobcard_master where jobcard_date >='".$from."' and jobcard_date<='".$to."' and job_card_closed=0";  
	
	if($query=mysqli_query($connection,$sql))
	{
		$result='<table class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
			$result=$result.'<thead class="table-dark">';
			$result=$result.'<tr> ';
			$result=$result.'<th>JC No</th>';
			$result=$result.'<th>JC Date</th>'; 
			$result=$result.'<th>Customer Details</th>';
			//$result=$result.'<th>JC Details</th>';
			$result=$result.'<th>Amount Details</th>';
			$result=$result.'<th>JC Status</th>';
			$result=$result.'</tr>';
			$result=$result.'</thead>';
			$result=$result.'<tbody>';
			$i=1;
		if(mysqli_num_rows($query)>0)
		{ 
			 while($row=mysqli_fetch_array($query)) {
					$result=$result.'<tr>'; 
					$result=$result.'<td>'.$row["jobcard_no"].'</td>';
					$result=$result.'<td>'.date("d-m-Y", strtotime($row["jobcard_date"])).'</td>';
					$result=$result.'<td>';
					$result=$result.'<b>Customer Type :</b> '.$row["customer_type"]."<br />";
					$result=$result.'<b>Customer Details :</b> '.$row["customer_name"]."<br />";
					$result=$result.$row["customer_addr1"]." ".$row["customer_addr2"]."<br />";
					$result=$result.$row["customer_city"]." ".$row["customer_state"]."<br />";
					$result=$result.$row["customer_gst_no"]." ".$row["customer_mobile_no"];
					$result=$result.'</td>';
					
					$result=$result.'<td>';
					$result=$result.'<b>App. Amount :</b> '.$row["approximate_amount"]."<br />";
					$result=$result.'<b>Advance Paid :</b> CASH ('.$row["advance_cash"].'), GPAY ('.$row["advance_gpay"].')'."<br />";
					$result=$result.'<b>Balance Paid :</b> CASH ('.$row["balance_cash"].'), GPAY ('.$row["balance_gpay"].')'."<br />";
					$total_paid = $row["advance_cash"] + $row["advance_gpay"] + $row["balance_cash"] + $row["balance_gpay"];
					$result=$result.'<b>Total Paid :</b> '.$total_paid."<br />";
					$remaining_amount_to_pay = $row["approximate_amount"] - $total_paid;
					$result=$result.'<b>Remaining to Pay :</b> '.$remaining_amount_to_pay;
					$result=$result.'</td>';
					
					
					if($row["job_card_closed"] ==0)
					{
						$result=$result.'<td>OPEN</td>'; 
					}
					else
					{
						$result=$result.'<td>CLOSED</td>'; 
					}
					$result=$result.'</tr>';
					$i++;
			  }
			
		}
		 
		$result=$result.'</tbody>';
		$result=$result.'</table>';
	} 
}
echo $result;
?>