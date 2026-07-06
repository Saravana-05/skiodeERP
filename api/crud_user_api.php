<?php
include '../connect_db.php';
header('Content-Type: application/json');
$action = $_GET['action'] ?? '';

if ($action == 'read') {
 
    $stmt = $connection->query("SELECT * FROM user_master");
     while ($row = $stmt->fetch_assoc()) {
        
		$data[] = array_map('strval', $row);
    }
    echo json_encode($data);
}
elseif ($action == 'create') {
    $data = json_decode(file_get_contents('php://input'), true);
	$error=false;
	$sql="SELECT * from user_master where user_name='".$data['username']."'";	 
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{ 
			echo json_encode(['status'=>'User Name Already Exists']);
			return;
		} 
	}	 
	 
    $sql="INSERT INTO user_master (user_name,pass_word,user_type,user_display_name) VALUES('".$data['username']."','".$data['password']."','".$data['usertype']."','".$data['displayname']."')";
			   mysqli_query($connection,$sql);
	echo json_encode(['status'=>'Success']);
			//echo "Success";
	 
}
elseif ($action == 'update') {
	 $data = json_decode(file_get_contents('php://input'), true);
  
     $sql="UPDATE user_master SET  pass_word='".$data['password']."', user_type='".$data['usertype']."', user_display_name='".$data['displayname']."' WHERE user_name='".$data['username']."'" ; 
	 mysqli_query($connection,$sql);
   echo json_encode(['status'=>'Success']);
}
elseif ($action == 'delete') { 
	$sql="DELETE FROM  user_master where user_name='".$_GET['code']."'";
	mysqli_query($connection,$sql); 
     
    echo json_encode(['status'=>'ok']);
}
elseif ($action == 'editrow') {
	$id = $_GET['code'];
	$stmt = $connection->query("SELECT * FROM user_master where user_name='".$id."'");
     while ($row = $stmt->fetch_assoc()) {
        
		$data[] = array_map('strval', $row);
    }
    echo json_encode($data);
	
}
?>