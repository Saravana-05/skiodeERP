<?php
session_start();
include_once '../connect_db.php';
$result=""; 
if(isset($_GET['from']))
{
	$from = date("Y-m-d", strtotime($_GET['from']));
	$to = date("Y-m-d", strtotime($_GET['to']));
	$sql="SELECT * FROM journal_details where dt >='".$from."' and dt<='".$to."'";  
	 
	if($query=mysqli_query($connection,$sql))
	{
		$result='<table class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
			$result=$result.'<thead class="table-dark">';
			$result=$result.'<tr> ';
			$result=$result.'<th>SNo</th>';
			$result=$result.'<th>DATE</th>'; 
			$result=$result.'<th>ACHEAD</th>';
			$result=$result.'<th>AMOUNT</th>';
			$result=$result.'<th>DESCRIPTION</th>';
			$result=$result.'</tr>';
			$result=$result.'</thead>';
			$result=$result.'<tbody>';
			$i=1;
		if(mysqli_num_rows($query)>0)
		{ 
			 while($row=mysqli_fetch_array($query)) {
					$result=$result.'<tr>';
					$result=$result.'<td>'.$i.'</td>';
					$result=$result.'<td>'.date("d-m-Y", strtotime($row["dt"])).'</td>';
					$result=$result.'<td>'.$row["achead"].'</td>';
					if($row["amount"] >0)
					{
						$result=$result.'<td>'.$row["amount"].' Cr</td>';  
					}
					else
					{
						$result=$result.'<td>'.abs($row["amount"]).' Dr</td>';  
					}
					
					$result=$result.'<td>'.$row["description"].'</td>'; 
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