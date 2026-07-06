<?php
session_start();
include_once '../connect_db.php';
$result="Failed";
if(isset($_POST['job_card_no']))
{
	
	$job_card_no = $_POST['job_card_no'];
	$dt = date("Y-m-d H:i:s");

	$sql="select * from jobcard_master where jobcard_no=".$job_card_no;
	
	if($query=mysqli_query($connection,$sql))
	{
		if($row=mysqli_fetch_array($query))
		{
			$job_card_closed=$row["job_card_closed"];
			if($job_card_closed==0)
			{
				$sql="update jobcard_master set void_job_card=1,job_card_closed=1,job_card_closed_on='".$dt."',jobcard_closed_by='".$_SESSION['user_name']."' where jobcard_no=".$job_card_no;	 
				
				mysqli_query($connection,$sql);
							
				$job_work_completed=$row["job_work_completed"];
				if($job_work_completed==0)
				{
					//reduce machine usage  & Material Usage as this work never went for production
					$sql="delete from job_card_wise_material_usage where jobcard_no=".$job_card_no;
					
					mysqli_query($connection,$sql);
					$sql="delete from job_card_wise_machine_usage where jobcard_no=".$job_card_no;
					
					mysqli_query($connection,$sql);
					
				}
				$result="Success";
			}
		}
	}
}
echo $result;
?>