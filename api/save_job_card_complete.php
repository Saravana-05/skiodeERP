<?php
session_start();
include_once '../connect_db.php';
$result="Failed";
if(isset($_POST['jobcard_no']))
{
	
	$jobcard_no = $_POST['jobcard_no'];
	$dt = date("Y-m-d H:i:s");
	$sql="update jobcard_master set job_work_completed=1, 	job_work_completed_on_dttm='".$dt."' , job_work_completion_marked_by	='".$_SESSION['user_name']."' where jobcard_no=".$jobcard_no;	 
	
 	mysqli_query($connection,$sql);
	
	$materialArr = json_decode($_POST['material_spillage_details'], true);
	for($m=0;$m<count($materialArr);$m++)
	{
		if($materialArr[$m]["spillage_count"] >0)
		{
			$materialArr[$m]["jobcard_date"]=date("Y-m-d H:i:s");
			$spillage_count=$materialArr[$m]["spillage_count"];
			if($materialArr[$m]["front_back"]==1) $spillage_count=ceil($spillage_count/2);
			$sql="INSERT INTO `job_card_wise_material_spillage`(`jobcard_no`,  `jobcard_date`, `material_code`,`machine_code`,`spillage_count`) VALUES ('".$materialArr[$m]["jobcard_no"]."','".$materialArr[$m]["jobcard_date"]."','".$materialArr[$m]["material_code"]."','".$materialArr[$m]["machine_code"]."','".$spillage_count."')"; 
			
			mysqli_query($connection,$sql);
			
			$sql="INSERT INTO `job_card_wise_machine_spillage`(`jobcard_no`,  `jobcard_date`, `machine_code`,`spillage_count`) VALUES ('".$materialArr[$m]["jobcard_no"]."','".$materialArr[$m]["jobcard_date"]."','".$materialArr[$m]["machine_code"]."','".$materialArr[$m]["spillage_count"]."')"; 
			
			mysqli_query($connection,$sql);
		}
	}
	/*	
	$machineArr = json_decode($_POST['machine_spillage_details'], true);
	for($m=0;$m<count($machineArr);$m++)
	{
		if($machineArr[$m]["spillage_count"] >0)
		{
			$sql="INSERT INTO `job_card_wise_machine_spillage`(`jobcard_no`,  `jobcard_date`, `machine_code`,`spillage_count`) VALUES ('".$machineArr[$m]["jobcard_no"]."','".$machineArr[$m]["jobcard_date"]."','".$machineArr[$m]["machine_code"]."','".$machineArr[$m]["spillage_count"]."')"; 
			
			mysqli_query($connection,$sql); 
		}
	}*/
	$result="Success";
}
echo $result;
?>
