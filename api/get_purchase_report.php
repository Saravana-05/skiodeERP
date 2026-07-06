<?php
session_start();
include_once '../connect_db.php';
$result=""; 
if(isset($_GET['from']))
{
	$from = date("Y-m-d", strtotime($_GET['from']));
	$to = date("Y-m-d", strtotime($_GET['to']));
	$sql="SELECT * FROM sales_quotation_master where quotation_dt_tm between '".$from." 00:00:00' and '".$to." 23:59:59;'";  
	 
	if($query=mysqli_query($connection,$sql))
	{
		$result='<table class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
			$result=$result.'<thead class="table-dark">';
			$result=$result.'<tr> ';
			$result=$result.'<th>SNo</th>';
			$result=$result.'<th>SQ No</th>'; 
			$result=$result.'<th>DATE</th>';			
			$result=$result.'<th>Amount</th>';
			$result=$result.'<th>Desc.</th>';
			$result=$result.'</tr>';
			$result=$result.'</thead>';
			$result=$result.'<tbody>';
			$i=1;
		if(mysqli_num_rows($query)>0)
		{ 
			 while($row=mysqli_fetch_array($query)) {
					$result=$result.'<tr>';
					$result=$result.'<td>'.$i.'</td>';
					$result=$result.'<td>'.$row["quotation_no"].'</td>';
					$result=$result.'<td>'.date("d-m-Y", strtotime($row["quotation_dt_tm"])).'</td>';
					$result=$result.'<td>'.$row["approximate_amount"]-$row["discount"].'</td>'; 
					$result=$result.'<td>'.$row["created_by"].'</td>'; 
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