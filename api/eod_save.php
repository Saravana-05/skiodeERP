<?php
session_start();
include_once '../connect_db.php';
$result="Failed";
$mac_details_array= json_decode($_POST['allTableData'], true);
$dt = date("Y-m-d");
$sql="INSERT INTO `machine_eod_counter_details`( `machine_code`, `dt`, `machine_counter_reading`, `software_counter_reading`, `difference_in_reading`, `machine_usage_count`, `machine_spillage_count`) VALUES ";
$val_sql="";
for($i=0;$i<count($mac_details_array);$i++)
{

	if($val_sql!="") $val_sql.=",";
	$val_sql.="('".$mac_details_array[$i]['machine_code']."','".$dt."','".$mac_details_array[$i]['machine_counter_reading']."','".$mac_details_array[$i]['software_counter_reading']."','".$mac_details_array[$i]['difference_in_reading']."','0','0')";
	
}
$sql.=$val_sql.";";
if(mysqli_query($connection,$sql))
{
	if(mysqli_affected_rows($connection)>0)
	{
		$result="Success";
	}
}
echo $result;
?>