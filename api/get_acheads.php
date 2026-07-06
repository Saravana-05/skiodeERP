<?php
session_start();
include_once '../connect_db.php';
$result="";
$actype_txt="ALL";
if(isset($_GET["actype_txt"])) $actype_txt=$_GET["actype_txt"];
$sql="select customer_code,customer_name from customer_master ";
	if($actype_txt!="ALL")
		$sql.=" where customer_type like '%".$actype_txt."%'";

$sql .=" order by customer_name";	
$customer_code ="";
$customer_name="";
if($row=mysqli_query($connection,$sql))		
{ 
	while($data=mysqli_fetch_array($row))
	{
		$customer_code = $data["customer_code"];
		$customer_name= $data["customer_name"];
		if(strlen($result)>0) $result.="^";
		$result.=$customer_code."~".$customer_name;		 
	}
}
echo $result;
?>