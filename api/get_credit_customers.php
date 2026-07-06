<?php
session_start();
include_once '../connect_db.php';
$result="";
$sql="select * from customer_master;";
$customer_code ="";
$customer_name="";
if($row=mysqli_query($connection,$sql))		
{ 
	while($data=mysqli_fetch_array($row))
	{
		$customer_code =$customer_code .$data["customer_code"].'^';
		$customer_name=$customer_name.$data["customer_name"].'^';
				 
	}
}
$customer_code = trim($customer_code , "^");
$customer_name= trim($customer_name, "^");
$result=$customer_code."~".$customer_name;
echo $result;
?>