<?php
include '../connect_db.php';
header('Content-Type: application/json');
$action = $_GET['action'] ?? '';

if ($action == 'read') {
 
    $stmt = $connection->query("SELECT machine_code, machine_name, COALESCE(machine_desc, '') AS machine_desc,counter_reading,COALESCE(counter_on_dt, '') AS counter_on_dt FROM machine_master");
     while ($row = $stmt->fetch_assoc()) {
        
		$data[] = array_map('strval', $row);
    }
    echo json_encode($data);
}
elseif ($action == 'create') {
    $data = json_decode(file_get_contents('php://input'), true);
	$error=false;
	$sql="SELECT * from machine_master where machine_code='".$data['machine_code']."'";	 
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{ 
			echo json_encode(['status'=>'Machine code Already Exists']);
			return;
		} 
	}	 
	 
    $sql="INSERT INTO machine_master (machine_code,machine_name,machine_desc,counter_reading,counter_on_dt) VALUES('".$data['machine_code']."','".$data['machine_name']."','".$data['machine_desc']."','".$data['counter_reading']."','".date("Y-m-d", strtotime($data['counter_on_dt']))."')";
	mysqli_query($connection,$sql);
	echo json_encode(['status'=>'Success']);
			//echo "Success";
	 
}
elseif ($action == 'update') {
	$data = json_decode(file_get_contents('php://input'), true);

	$sql="UPDATE machine_master SET  machine_name='".$data['machine_name']."', machine_desc='".$data['machine_desc']."', counter_reading='".$data['counter_reading']."', counter_on_dt='".date("Y-m-d", strtotime($data['counter_on_dt']))."' WHERE machine_code='".$data['machine_code']."'" ; 
	mysqli_query($connection,$sql);
	echo json_encode(['status'=>'Success']);
}
elseif ($action == 'delete') { 
	$sql="DELETE FROM  machine_master where machine_code='".$_GET['machine_code']."'";
	mysqli_query($connection,$sql); 
     
    echo json_encode(['status'=>'ok']);
}
elseif ($action == 'editrow') {
	$id = $_GET['code'];
	$stmt = $connection->query("SELECT * FROM machine_master where machine_code='".$id."'");
     while ($row = $stmt->fetch_assoc()) {
        
		$data[] = array_map('strval', $row);
    }
    echo json_encode($data);
	
}
?>