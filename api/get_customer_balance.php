<?php
session_start();
include_once '../connect_db.php';
$result="";
$sql="select sum(amount) as bal from journal_details where achead='".$_POST["achead_txt"]."';";
//file_put_contents("a_get_cus_bal.txt",$sql);
if($qry=mysqli_query($connection,$sql))		
{ 
	if($row=mysqli_fetch_array($qry))
	{
		if($row["bal"] == 0.00) $result=" Nil ";
		else
		{
			$result= abs($row["bal"]) . ($row["bal"]>0?" Cr":" Dr");
		}
		
		
	}
}
echo $result;
?>