<?php
session_start();
include_once '../connect_db.php';
$result="";
$data = array();
if(isset($_GET['id']))
{
	$sql="SELECT * FROM customer_master where customer_code='".$_GET['id']."';"; 
 
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{
			 while($row = $query->fetch_assoc()) {
				$data[] = $row;
			  }
		}
	}
header('Content-type: application/json');
 
$result=json_encode($data);
}
echo $result;
?>