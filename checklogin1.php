<?php

include_once 'connect_db.php';

$result = "Failed";
if(isset($_POST["uname"]) && isset($_POST["pword"]))
{	 
	/*if($row=mysqli_query($connection,"select *  from user_master   where user_name='".$_REQUEST["uname"]."' and pass_word='".$_REQUEST["pword"]."'"))		
    { 
        if($data=mysqli_fetch_array($row))
        {
            $result="Success";             
        }
    }*/
	if($_POST["uname"]=="admin" && $_POST["pword"]=="admin")
	{
		$result="Success"; 
	}
}  
echo $result; 
?>