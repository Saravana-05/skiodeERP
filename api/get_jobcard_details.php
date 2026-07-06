<?php
session_start();
include_once '../connect_db.php';
$result="";
$data = array();
if(isset($_REQUEST['job_card_no']))
{
	if($_REQUEST['type'] == 'jc')
	{
		$sql="SELECT * FROM jobcard_master where jobcard_no=".$_REQUEST['job_card_no']; 	 
		if($query=mysqli_query($connection,$sql))
		{
			if(mysqli_num_rows($query)>0)
			{
				 while($row = $query->fetch_assoc()) {
					$data[] = $row;
				  }
			}
		}													
	}
	else if($_REQUEST['type'] == 'mat')
	{
		$sql="SELECT * FROM job_card_wise_material_usage where jobcard_no=".$_REQUEST['job_card_no']; 	 
		if($query=mysqli_query($connection,$sql))
		{
			if(mysqli_num_rows($query)>0)
			{
				 while($row = $query->fetch_assoc()) {
					$data[] = $row;
				  }
			}
			else
			{
				$sql="SELECT * FROM job_card_wise_machine_usage where jobcard_no=".$_REQUEST['job_card_no']; 	 
						if($query=mysqli_query($connection,$sql))
						{
							if(mysqli_num_rows($query)>0)
							{
								 while($row = $query->fetch_assoc()) {
									$data[] = $row;
								  }
							}
						}				
			}
		}													
	}
	else if($_REQUEST['type'] == 'mac')
	{
		$sql="SELECT * FROM job_card_wise_machine_usage where jobcard_no=".$_REQUEST['job_card_no']; 	 
		if($query=mysqli_query($connection,$sql))
		{
			if(mysqli_num_rows($query)>0)
			{
				 while($row = $query->fetch_assoc()) {
					$data[] = $row;
				  }
			}
		}													
	}
header('Content-type: application/json');
 
$result=json_encode($data);
}
echo $result;
?>