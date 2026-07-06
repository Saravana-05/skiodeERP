<?php
session_start();
include_once "../connect_db.php";
//header('Content-Type: application/json');
$result=""; 
$sql="SELECT * FROM sales_invoice_master where paid_out=0";
if($_SESSION['user_type'] == "OPERATOR")
{
	$dt2=date("Y-m-d");
	$dt1=date('Y-m-d', strtotime($dt2. ' - 2 days'));
	$sql.=" and created_by='".$_SESSION['user_name']."' and invoice_dt_tm between '".$dt1." 00:00:00' and '".$dt2." 23:59:59'";
}
$dt_range=$_POST['dt'];

$sql="SELECT * FROM sales_invoice_master where 1";
/*if($dt_range!="All")
{
$dt = date("Y-m-d", strtotime($_POST['dt']));
$sql.=" and invoice_dt_tm >='".$dt."'";  
}*/
if(strpos($dt_range,"~")===False)
{
	$dt = date("Y-m-d", strtotime($_POST['dt']));
	$sql.=" and invoice_dt_tm >='".$dt."'";  
}
else
{
	$dt_ary=explode("~",$_POST["dt"]);
	$sql.=" and invoice_dt_tm between '".date("Y-m-d", strtotime($dt_ary[0]))." 00:00:00' and '".date("Y-m-d", strtotime($dt_ary[1]))." 23:59:59' " ;  
}

$customer_code_txt=$_POST["customer_code_txt"];
/* if($customer_code_txt!="all")
{
	if($customer_code_txt=="walkin")
	{
		$sql.=" and customer_type='WalkIn'";
	}elseif($customer_code_txt=="general")
	{
		$sql.=" and customer_type='General'";
	}else
	{
		$sql.=" and customer_code='".$customer_code_txt."'";
	}
} */
$user_name_txt=$_POST["user_name_txt"];
if($user_name_txt!="all")
{
	$sql.=" and created_by='".$_POST["user_name_txt"]."'";
} 
$pay_mode_txt=$_POST["pay_mode_txt"];
//echo $sql;
log_this($sql);
$stmt = $connection->query($sql);
$data=[];
$result="";
$result.= '<table class="table table-bordered table-hover" id="siTable" style="width:99%;">';
$result.= '  <thead class="table-dark">';
$result.= ' <tr> <th>Invoice No</th><th>Invoice Date</th><th>SQ Nos</th><th>Customer</th><th>Amount</th><th>Paymode</th><th>User</th><th>Actions </th></tr>';
$result.= ' <tr> <th><input type="text" placeholder="Invoice No" style="width:70px;font-size:0.75vw;" /></th>
<th></th>
<th></th>
<th></th> 
<th></th> 
<th></th>
<th></th>
<th>  </th></tr>'; 
$result.= '</thead><tbody>';

 while ($row = $stmt->fetch_assoc()) {
		$jcnos="";
		$sq_det_sql="select * from sales_quotation_master where quotation_no in (".$row["sale_quotation_nos"].");";
		log_this($sq_det_sql);
		$Paymode="";
		if($sq_det_qry=mysqli_query($connection,$sq_det_sql))
		{
			while($sq_det_row=mysqli_fetch_array($sq_det_qry))
			{
				if($jcnos!="")$jcnos.=",";
				$jcnos.=$sq_det_row["jobcard_nos"];
		 $Paymode="";
		 if($sq_det_row["balance_cash"]>0)
		 {
			 if($Paymode!="") $Paymode.=",";
			 $Paymode="Cash"; 
		 }
		 if($sq_det_row["balance_gpay"]>0)
		 {
			 if($Paymode!="") $Paymode.=",";
			 $Paymode="Online"; 
		 }
		 if($sq_det_row["balance_received_total"]==0) 
		 {
			 if($Paymode!="") $Paymode.=",";
			 $Paymode="Credit";
		 }
		
		if($pay_mode_txt!="all")
		{
			if(strpos(strtoupper($Paymode),strtoupper($pay_mode_txt))===False)
			{
				$valid_customer=0;			
			}
		}				
			}
		}
		//echo $det_sql."<br>";
	
		
		$sql="SELECT * FROM  jobcard_master where jobcard_no in (".$jcnos.");"; 
		
		$customer_details="";
		$customer_type="";
		$customer_code="";
		$customer_name=""; 
		$customer_addr1="";
		$customer_addr2="";
		$customer_city="";
		$customer_mobile_no="";
		$customer_gst_no="";
		if($query=mysqli_query($connection,$sql))
		{
			if(mysqli_num_rows($query)>0)
			{ 
				 if($jcrows=mysqli_fetch_array($query)) 
				 {
					if($jcrows["customer_type"] != "" )$customer_code=$jcrows["customer_type"]; 
					if($jcrows["customer_code"] != "" )$customer_code=$jcrows["customer_code"]; 
					if($jcrows["customer_name"] != "" )$customer_name=$jcrows["customer_name"]; 
					if($jcrows["customer_addr1"] != "" )$customer_addr1=$jcrows["customer_addr1"]; 
					if($jcrows["customer_addr2"] != "" )$customer_addr2=$jcrows["customer_addr2"]; 
					if($jcrows["customer_city"] != "" )$customer_city=$jcrows["customer_city"]; 
					if($jcrows["customer_mobile_no"] != "" )$customer_mobile_no=$jcrows["customer_mobile_no"];  
					if($jcrows["customer_gst_no"] != "" )$customer_gst_no=$jcrows["customer_gst_no"];  
				 }
			}
		}
		
		
		
		$customer_details.=(strlen($customer_details)>0?"<br>":"").$customer_name;
		$customer_details.=(strlen($customer_details)>0?"<br>":"").$customer_addr1;
		$customer_details.=(strlen($customer_details)>0?"<br>":"").$customer_addr2;
		$customer_details.=(strlen($customer_details)>0?"<br>":"").$customer_city;
		if(strlen($customer_gst_no)>0)
			$customer_details.=(strlen($customer_details)>0?"<br>":"")."GST : ".$customer_gst_no;
		if(strlen($customer_mobile_no)>0)
			$customer_details.=(strlen($customer_details)>0?"<br>":"")."Mobile : ".$customer_mobile_no;
		
		//$result.= '     <td style="font-size:11px;">'.$customer_details.'</td>';
		$valid_customer=1;
		if($customer_code_txt!="all")
		{
			if($customer_code_txt!=$customer_type && $customer_code_txt!=$customer_code)
			{
				$valid_customer=0;
			}
		}
		
		
		if($valid_customer)
		{		
		
		$result.= ' <tr data-id="'.$row["invoice_no"].'"> ';
		$result.= '     <td><input type="checkbox" class="form-check-input select_sqs" value="'.$row["invoice_no"].'" /> '.$row["invoice_no"].'</td>';
		$result.= '     <td>'.date("d-m-Y",strtotime($row["invoice_dt_tm"])).'</td>';
		$result.= '     <td>'.$row["sale_quotation_nos"].'</td>';		
		$result.= '<td >'.$customer_name.'</td>';
		$result.= '     <td>'.$row["net_total"].'</td>';
		
		
	
		$result.= '<td>'.$Paymode.'</td>';
		$result.= '<td>'.$row["created_by"].'</td>';
		$result.= '<td style="padding:0;">';
		$result.= '<a class="btn btn-sm btn-primary m-1" target="_blank" href="sale_invoice_print.php?si='.$row["invoice_no"].'">Print</a>';
		$result.= '<a class="btn btn-sm btn-success m-1" onclick="showJobCard('."'".$jcnos."'".','."'".$row["sale_quotation_nos"]."'".','.$row["invoice_no"].')">Show</a>';	 
		$result.= '</td>'; 
		$result.= '   </tr>'; 
		}
	
}
$result.="</tbody></table>";
echo $result;
?>