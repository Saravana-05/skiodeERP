<?php
session_start();
include_once '../connect_db.php';
$result=""; 
if(isset($_GET['job_card_no']))
{
	$sql="SELECT A.*,B.material_name FROM job_card_wise_material_usage as A join material_master as B on A.material_code=B.material_code where jobcard_no=".$_GET['job_card_no']; 
 
	if($query=mysqli_query($connection,$sql))
	{
		$result='<table class="table table-bordered table-hover table-condensed table-striped ">';
			$result=$result.'<thead>';
			$result=$result.'<tr> ';
			$result=$result.'<th>SNo</th>';
			$result=$result.'<th>Material Name</th>'; 
			$result=$result.'<th>Usage</th>';
			$result=$result.'</tr>';
			$result=$result.'</thead>';
			$result=$result.'<tbody>';
			$i=1;
		if(mysqli_num_rows($query)>0)
		{
			
			 while($row=mysqli_fetch_array($query)){
					$result=$result.'<tr>';
					$result=$result.'<td>'.$i.'</td>';
					$result=$result.'<td>'.$row["material_name"].'</td>';
					$result=$result.'<td>'.$row["usage_count"].'</td>'; 
					$result=$result.'</tr>';
			  }			
		}
		else
		{
			$result=$result.'<tr><td colspan="3"><center>No Data</center></td></tr>';
		}
		$result=$result.'</tbody>';
		$result=$result.'</table>';
	} 
}
echo $result;
?>