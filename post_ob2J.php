<?php
session_start(); 
include_once "connect_db.php";
$sql="select * from achead_master where opening_balance<>0";
$qry=mysqli_query($connection,$sql);

$sql="";
while($row=mysqli_fetch_array($qry))
{
	if($sql!="") $sql.=",".PHP_EOL;
	$sql.="('2025-12-08','".$row["achead"]."','Opening Balance',".$row["opening_balance"].",'OPBAL_".$row["achead"]."')";
}
$sql="INSERT INTO journal_details( dt, achead, description, amount, link_key) VALUES ".$sql;
file_put_contents("op_sql.txt",$sql);
echo "<pre>".$sql;
?>