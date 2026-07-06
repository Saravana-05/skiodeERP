<?php
session_start();
include_once '../connect_db.php';
$result="";
$sql="SELECT * FROM jobcard_master where job_card_closed=0"; 
if($query=mysqli_query($connection,$sql))
{
	if(mysqli_num_rows($query)>0)
	{
		$i=1;
		$result='<table class="table table-bordered table-hover  table-striped " id="jobcardDetailTable">
			<thead class="table-dark">
				<tr>					 
					<th>JC No</th>
					<th>JC Date</th>
					<th>Customer Type</th>
					<th>Amount Details</th> 
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';
		while($row=mysqli_fetch_array($query))
		{ 
			$result= $result.'<tr>';
			//echo '<td>'.$i.'</td>';
			$result= $result.'<td>'.$row["jobcard_no"].'</td>';
			$result= $result.'<td>'.date("d-m-Y", strtotime($row["jobcard_date"])).'</td>';
			$result= $result.'<td>';
			$result= $result.$row["customer_type"];
			//$result= $result.'Customer Addr : '.$row["customer_name"].'<br />';
			//$result= $result.$row["customer_addr1"].','.$row["customer_addr2"].'<br />';
			//$result= $result.$row["customer_addr1"].','.$row["customer_addr2"].'<br />'.$row["customer_city"].','.$row["customer_state"].',<br />'.$row["customer_gst_no"].'<br />'.$row["customer_mobile_no"].',<br />';
			$result= $result.'</td>';
			$result= $result.'<td>App. Amt : '.$row["approximate_amount"].'<br />';
			$BalanceAmt = $row["approximate_amount"] - ($row["advance_gpay"] + $row["advance_cash"]);
			$result= $result.'Balance Amt : '.$BalanceAmt;
			$result= $result.'</td>'; 
			$result= $result.'<td>';
			$result= $result.'<input type="button" class="btn btn-info btn-sm m-1" name="view_machine" value="View Machine" onclick="fnMachineUsagePopUp('.$row["jobcard_no"].')" />';
			$result= $result.'<input type="button" class="btn btn-warning btn-sm m-1" name="view_machine" value="View Material" onclick="fnMaterialUsagePopUp('.$row["jobcard_no"].')" />';
			$result= $result.'<input type="button" class="btn btn-success btn-sm m-1" name="view_machine" value="Close Job Card" onclick="fnCloseJCPopUp('.$row["jobcard_no"].','."'".$row["customer_type"]."'".')" />';
			$result= $result.'</td>';
			$result= $result.'</tr>';
			$i++;
		}
		$result= $result.'</tbody>';
		$result= $result.'</table>';
	}
}
echo $result;
?>