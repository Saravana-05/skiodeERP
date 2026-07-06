<?php
session_start();
include_once '../connect_db.php';
$result="0";
if(isset($_POST['jcnos']))
{
	$result=calc_gst_jc($_POST['jcnos'],(float)$_POST["discount_txt"]);
	echo $result;
}


?>