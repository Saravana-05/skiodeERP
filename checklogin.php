<?php
session_start(); 
include_once "connect_db.php";
function validate($data){
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
if (isset($_POST['user_name']) && isset($_POST['pass_word'])) {
    $username = validate($_POST['user_name']);
    $password = validate($_POST['pass_word']);


    $sql = "SELECT * FROM user_master WHERE user_name='$username' AND pass_word='$password'";
	$result = mysqli_query($connection, $sql);
     if (mysqli_num_rows($result)> 0) {
			$row = mysqli_fetch_assoc($result);
            
            	$_SESSION['logged_in'] = 1;
            	$_SESSION['user_name'] = $row['user_name'];
            	$_SESSION['user_display_name'] = $row['user_display_name'];
            	$_SESSION['user_type'] = $row['user_type'];
				echo "Success";
		}
        else{
			echo "Failed";
		}
}
else{
   echo "Failed";
}
?>