<?php
include '../connect_db.php';
header('Content-Type: application/json');
$action = $_GET['action'] ?? '';

if ($action == 'read') {
 
    $stmt = $connection->query("SELECT * FROM customer_master");
     while ($row = $stmt->fetch_assoc()) {
        
		$data[] = array_map('strval', $row);
    }
    echo json_encode($data);
}
elseif ($action == 'search') {
	if($_GET['ct'] == 'all')
		 $stmt = $connection->query("SELECT * FROM customer_master");
	else 
		$stmt = $connection->query("SELECT * FROM customer_master where customer_type='".$_GET['ct']."'");
		
     while ($row = $stmt->fetch_assoc()) {
        
		$data[] = array_map('strval', $row);
    }
    echo json_encode($data);
}
elseif ($action == 'create') {
    $data = json_decode(file_get_contents('php://input'), true);
	$error=false;
	$sql="SELECT achead from achead_master where achead='".$data['code']."'";	 
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{ 
			//echo 'Customer Code Already Exists';
			echo json_encode(['status'=>'Customer(account head) Code Already Exists']);
			return;
		} 
	}	
	$sql="SELECT * from customer_master where customer_code='".$data['code']."'";	 
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{ 
			//echo 'Customer Code Already Exists';
			echo json_encode(['status'=>'Customer Code Already Exists']);
			return;
		} 
	}	
	$sql="SELECT * from customer_master where customer_name='".$data['name']."'";	 
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{ 
			//echo 'Customer Name Already Exists';
			echo json_encode(['status'=>'Customer Name Already Exists']);
			return;
		} 
	}	
	$sql="SELECT * from customer_master where gst_no='".$data['gst']."'";	 
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{ 
			//echo 'GST Number Already Exists';
			echo json_encode(['status'=>'GST Number Already Exists']);
			return;
		} 
	}	
//	$sql= "insert into achead_master(`achead`, `acname`, `opening_balance`, `actype`) VALUES ('".$data['code']."','".$data['name'].",0,'".$data['customertype']."');";
	
    $sql="INSERT INTO customer_master (customer_code,customer_name,customer_type,customer_addr1,customer_addr2,customer_city,customer_state_code,customer_state,gst_no,mobile_no,customer_email_id) VALUES('".$data['code']."','".$data['name']."','".$data['customertype']."','".$data['address1']."','".$data['address2']."','".$data['city']."','".$data['state']."','".$data['stateName']."','".$data['gst']."','".$data['phone']."','".$data['email']."')";
			 
	mysqli_query($connection,$sql);//sundry
	$sql="INSERT INTO `achead_master`(`achead`, `acname`, `actype`,opening_balance) VALUES ('".$data['code']."','".$data['name']."','sundry',".$data['opening_balance'].")";
	mysqli_query($connection,$sql);
	$sql="INSERT INTO journal_details( dt, achead, description, amount, link_key) VALUES ('2025-12-08','".$data['code']."','Opening Balance',".$data['opening_balance'].",'OPBAL_".$data['code']."');";
	mysqli_query($connection,$sql);
	
	echo json_encode(['status'=>'Success']);
			//echo "Success";
	 
}
elseif ($action == 'update') {
	 $data = json_decode(file_get_contents('php://input'), true);
  
     $sql="UPDATE customer_master SET customer_name='".$data['name']."', customer_type='".$data['customertype']."', customer_addr1='".$data['address1']."', customer_addr2='".$data['address2']."', customer_city='".$data['city']."', customer_state_code='".$data['state']."', customer_state='".$data['stateName']."', gst_no='".$data['gst']."', mobile_no='".$data['phone']."', customer_email_id='".$data['email']."' WHERE customer_code='".$data['code']."'" ; 
	 mysqli_query($connection,$sql);
	 $sql="update achead_master set opening_balance=".$data['opening_balance']." where achead='".$data['code']."'";
	 mysqli_query($connection,$sql);
	 $sql="update journal_details set amount=".$data['opening_balance']." where link_key='OPBAL_".$data['code']."';";
	 mysqli_query($connection,$sql);
   echo json_encode(['status'=>'Success']);
}
elseif ($action == 'delete') { 
	$sql="DELETE FROM  customer_master where customer_code='".$_GET['code']."'";
	mysqli_query($connection,$sql);
	$sql="DELETE FROM  achead_master where achead='".$_GET['code']."'";
	mysqli_query($connection,$sql);
	$sql="DELETE FROM  journal_details where link_key='OPBAL_".$_GET['code']."'";
	mysqli_query($connection,$sql);     
    echo json_encode(['status'=>'ok']);
}
elseif ($action == 'editrow') {
	$data=[];
	$id = $_GET['code'];
	$stmt = $connection->query("SELECT * FROM customer_master where customer_code='".$id."'");
     if ($row = $stmt->fetch_assoc()) {
        
		$data[] = array_map('strval', $row);
    }
	$stmt = $connection->query("SELECT * FROM achead_master where achead='".$id."'");
	if ($row = $stmt->fetch_assoc()) {
		$opening_balance=0.00;
		if(!is_null($row["opening_balance"]))
			$opening_balance=$row["opening_balance"];
		$data[0]["opening_balance"] = $opening_balance;
	}
    echo json_encode($data);
	
}
?>