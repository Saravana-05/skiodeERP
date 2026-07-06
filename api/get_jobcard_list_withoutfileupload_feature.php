<?php
session_start();
include_once "../connect_db.php";
//header('Content-Type: application/json');
$result=""; 
/*
$sql="SELECT * FROM jobcard_master where job_card_closed=0";
if($_SESSION['user_type'] == "OPERATOR")
{
	$sql.=" and created_by='".$_SESSION['user_name']."'";
}*/
$dt_range=$_POST['dt'];
$status_txt=$_POST["status_txt"];
if($status_txt!="all")
	$sql="SELECT * FROM jobcard_master where job_card_closed=".$status_txt;
else
	$sql="SELECT * FROM jobcard_master where 1";
if(strpos($dt_range,"~")===False)
{
	$dt = date("Y-m-d", strtotime($_POST['dt']));
	$sql.=" and jobcard_date >='".$dt."'";  
}
else
{
	$dt_ary=explode("~",$_POST["dt"]);
	$sql.=" and jobcard_date between '".date("Y-m-d", strtotime($dt_ary[0]))."' and '".date("Y-m-d", strtotime($dt_ary[1]))."' " ;  
}

$customer_code_txt=$_POST["customer_code_txt"];
if($customer_code_txt!="all")
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
}
$user_name_txt=$_POST["user_name_txt"];
if($user_name_txt!="all")
{
	$sql.=" and created_by='".$_POST["user_name_txt"]."'";
} 
$pay_mode_txt=$_POST["pay_mode_txt"];
if($_POST['dt']=="ALL_OPEN_JC")
{
	$sql="SELECT * FROM jobcard_master where job_card_closed=0 and created_by='".$_POST["user_name_txt"]."';";
}


$stmt = $connection->query($sql);
$data=[];
$result="";
$result.= '<table class="table table-bordered table-hover" id="jobcardTable">';
$result.= '  <thead class="table-dark">';
$result.= ' <tr> <th>JC No</th><th>JC Date</th><th>Customer Name</th><th>User Name</th><th>Time</th><th>App.Value</th><th>Advance</th><th>Paymode</th><th>Type</th><th>Actions </th></tr>';  //<th>Details</th>
$result.= ' <tr> <th><input type="text" placeholder="JC No" style="width:70px;font-size:0.75vw;" /></th>
<th></th> 
<th></th> 
<th></th>
<th>  </th>
<th></th>
<th></th>
<th></th>
<th></th>
<th>  </th></tr>'; 
$result.= '</thead><tbody>';

 while ($row = $stmt->fetch_assoc()) {
		$td_str="<td>";
		$highlightCSS="";
		$det_sql="select * from jobcard_details where jobcard_no=".$row["jobcard_no"].";";
		//echo $det_sql."<br>";
		 $customer_type=$row["customer_type"];
		 if($customer_type=="Credit"){ $td_str='<td class="highlight">';	$highlightCSS=' class="highlight"'; }
		 $Paymode="";
		 if($row["advance_cash"]>0)
		 {
			 if($Paymode!="") $Paymode.=",";
			 $Paymode="Cash"; 
		 }
		 if($row["advance_gpay"]>0)
		 {
			 if($Paymode!="") $Paymode.=",";
			 $Paymode="Online"; 
		 }
		 if($row["customer_code"]!="") 
		 {
			 if($Paymode!="") $Paymode.=",";
			 $Paymode="Credit";
		 }
		$valid_customer=1;
		if($pay_mode_txt!="all")
		{
			if(strpos(strtoupper($Paymode),strtoupper($pay_mode_txt))===False)
			{
				$valid_customer=0;			
			}
		}
		if($valid_customer)
		{		 
		$result.= ' <tr data-id="'.$row["jobcard_no"].'"> ';
		if($row["job_card_closed"]==0)
			$result.= '     <td '.$highlightCSS.'><input type="checkbox" class="form-check-input select_jcs" value="'.$row["jobcard_no"].'" customer_type="'.$row["customer_type"].'" customer_name="'.$row["customer_name"].'" job_completed="'.$row["job_work_completed"].'"/> '.$row["jobcard_no"].'</td>';
		else
			$result.= '     <td '.$highlightCSS.'style="background-color:#01f9c0;">'.$row["jobcard_no"].' Closed</td>';
		$result.= $td_str.date("d-m-Y",strtotime($row["jobcard_date"])).'</td>';
		$customer_name=$row["customer_name"];
		if($row["customer_type"]!="Credit")
		{
			if($customer_name=="")
				$customer_name=$row["customer_type"];
			else
				$customer_name=$row["customer_type"] ."-".$customer_name;
			$customer_mobile_no=$row["customer_mobile_no"];
			if($customer_mobile_no!="")
				$customer_name.=" - ".$customer_mobile_no;
		}
		
			$result.= '     <td '.$highlightCSS.'>'.$customer_name.'</td>';
			$result.= $td_str.$row["created_by"].'</td>';
			$result.= $td_str.date("h:i:s",strtotime($row["created_dt_tm"])).'</td>';
		$customer_details="";
		$customer_details.=(strlen($customer_details)>0?"<br>":"").$row["customer_name"];
		$customer_details.=(strlen($customer_details)>0?"<br>":"").$row["customer_addr1"];
		$customer_details.=(strlen($customer_details)>0?"<br>":"").$row["customer_addr2"];
		$customer_details.=(strlen($customer_details)>0?"<br>":"").$row["customer_city"];
		if(strlen($row["customer_gst_no"])>0)
			$customer_details.=(strlen($customer_details)>0?"<br>":"")."GST : ".$row["customer_gst_no"];
		if(strlen($row["customer_mobile_no"])>0)
			$customer_details.=(strlen($customer_details)>0?"<br>":"")."Mobile : ".$row["customer_mobile_no"];
		
		
		//$result.= '     <td style="font-size:11px;">'.$customer_details.'</td>';
		
		$jc_details="";
		if($det_qry=mysqli_query($connection,$det_sql))
		{
			$i=0;
			
			while($det_row=mysqli_fetch_array($det_qry))
			{
				$job_detail_xml_ary=unserialize($det_row["job_detail_xml"]);
				$prod_name=$job_detail_xml_ary["product_code_txt"];
				$prod_sql="select product_name from product_master where product_code='".$job_detail_xml_ary["product_code_txt"]."';";
				if($prod_qry=$connection->query($prod_sql))
				{
					if($prod_row=$prod_qry->fetch_assoc())
						$prod_name=$prod_row["product_name"];
				}
				$jc_details.=$prod_name." ( Qty : ".$job_detail_xml_ary["total_qty_txt"].") = " .$job_detail_xml_ary["value"].'<hr style="margin:0;">';
				//$row["job_detail_xmls".$i]=$det_row["job_detail_xml"];
				//print_r($det_row);
			}
			
		}
		//$result.= '     <td style="font-size:11px;">'.$jc_details.'</td>';

		$result.= $td_str.$row["approximate_amount"].'</td>';
		$result.= $td_str.$row["advance_amount"].'</td>';
		$result.= $td_str.$Paymode.'</td>';
		
		$highlightCSS="";
		if($customer_type=="Credit") $highlightCSS=' class="highlight"';
		$result.= '     <td '.$highlightCSS.'>'.$row["customer_type"].'</td>';
		
					$result.= '<td '.$highlightCSS.' style="padding:0;">';
					$result.= '<a class="btn btn-sm btn-primary m-1" target="_blank" href="jc_print_out.php?jc='.$row["jobcard_no"].'">Print</a>';
					
					if($row["job_work_completed"] || $row["job_card_closed"])
					{
						 $result.= '<button type="button" class="btn btn-success btn-sm" disabled >
								<span class="material-icons-round" style="font-size:14px;vertical-align:-2px;">check_circle</span> Completed
								</button>&nbsp;';
						if($_SESSION["user_type"]=="ADMIN")
						{
						$result.= '<a class="btn btn-sm btn-warning m-1" value="'.$row["jobcard_no"].'" onclick="editJobCard('.$row["jobcard_no"].')">Edit</a>';
						}
					}
					else
					{
						
						if(!$row['void_job_card'])
						{
							if(date("Y-m-d",strtotime($row["jobcard_date"]))==date("Y-m-d") || $_SESSION["user_type"]=="ADMIN")
							{
							$result.= '<a class="btn btn-sm btn-warning m-1" value="'.$row["jobcard_no"].'" onclick="editJobCard('.$row["jobcard_no"].')">Edit</a>';
							}
							 $result.= '<button type="button" class="btn btn-warning btn-sm" title="Click upon completion..."  onclick="fnCompletePopUp('.$row["jobcard_no"].')">
									<span class="material-icons-round" style="font-size:14px;vertical-align:-2px;">pending</span> In Progress
									</button>&nbsp;';	
						}								
					}
					if(!$row["job_card_closed"])
					{
					if(!$row["job_work_completed"])
						$result.='<button type="button" class="btn btn-info btn-sm" onclick="fnMarkVoid('.$row["jobcard_no"].')">Void</button>';
					elseif( ($_SESSION["user_type"]=="ADMIN" || $_SESSION["user_type"]=="SUPERADMIN"))
						$result.='<button type="button" class="btn btn-info btn-sm" onclick="fnMarkVoid('.$row["jobcard_no"].')">Void</button>';
					}
					if($row['void_job_card'])
						$result.="Void";
					
					$result.= '</td>'; 
					$result.= '   </tr>'; 
		}
	
}
$result.="</tbody></table>";
echo $result;
?>