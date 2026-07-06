<?php
/**
 * api/get_sq_list.php — UPDATED
 * Actions cell converted to three-dot dropdown menu (same pattern as get_jobcard_list.php).
 */

session_start();
include_once "../connect_db.php";
//header('Content-Type: application/json');
$result="";
$sess_user_type = $_SESSION['user_type'] ?? '';
$sess_user_name = $_SESSION['user_name'] ?? '';
$sql="SELECT * FROM sales_quotation_master where is_this_converted_into_invoice<>1";
if($sess_user_type == "OPERATOR")
{
	$dt2=date("Y-m-d");
	$dt1=date('Y-m-d', strtotime($dt2. ' - 2 days'));
	$sql.=" and created_by='".mysqli_real_escape_string($connection, $sess_user_name)."' and quotation_dt_tm between '".$dt1." 00:00:00' and '".$dt2." 23:59:59'";
}
$dt_range=$_POST['dt'];
$status_txt=$_POST["status_txt"];
if($status_txt!="all")
	$sql="SELECT * FROM sales_quotation_master where is_this_converted_into_invoice=".$status_txt;
else
	$sql="SELECT * FROM sales_quotation_master where 1";
/*if($dt_range!="All")
{
$dt = date("Y-m-d", strtotime($_POST['dt']));
$sql.=" and quotation_dt_tm >='".$dt."'";  
}*/
if(strpos($dt_range,"~")===False)
{
	$dt = date("Y-m-d", strtotime($_POST['dt']));
	$sql.=" and quotation_dt_tm >='".$dt."'";  
}
else
{
	$dt_ary=explode("~",$_POST["dt"]);
	$sql.=" and quotation_dt_tm between '".date("Y-m-d", strtotime($dt_ary[0]))." 00:00:00' and '".date("Y-m-d", strtotime($dt_ary[1]))." 23:59:59'" ;  
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
$stmt = $connection->query($sql);
$data=[];
$result="";

// ── CSS only (script lives in dashboard.php — reuses .jc-action-wrap / .jc-dot-btn / .jc-drop-menu) ──────────────
$result .= '
<style>
.jc-action-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.jc-dot-btn {
    background: #334155;
    border: none;
    border-radius: 8px;
    color: #fff;
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1.4rem;
    font-weight: 900;
    transition: background 0.15s;
    line-height: 1;
    padding: 0;
    user-select: none;
}
.jc-dot-btn:hover {
    background: #4f46e5;
}
.jc-drop-menu {
    display: none;
    position: fixed;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 8px 32px rgba(15,23,42,0.22);
    z-index: 99999;
    min-width: 180px;
    overflow: hidden;
}
.jc-drop-menu.open {
    display: block;
}
.jc-drop-menu a,
.jc-drop-menu button {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 9px 16px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #1e293b;
    background: none;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    text-decoration: none;
    text-align: left;
    transition: background 0.12s;
    box-sizing: border-box;
    font-family: inherit;
    line-height: 1.3;
}
.jc-drop-menu a:last-child,
.jc-drop-menu button:last-child { border-bottom: none; }
.jc-drop-menu a:hover,
.jc-drop-menu button:hover { background: #f1f5f9; }
.jc-drop-menu .dm-print  { color: #4f46e5; }
.jc-drop-menu .dm-edit   { color: #d97706; }
.jc-drop-menu .dm-prog   { color: #f59e0b; }
.jc-drop-menu .dm-done   { color: #059669; }
.jc-drop-menu .dm-check  { color: #0891b2; }
.jc-drop-menu .dm-markpd { color: #16a34a; }
.jc-drop-menu .dm-void   { color: #dc2626; }
.jc-drop-menu .dm-voided { color: #94a3b8; font-style:italic; }
.jc-drop-menu .dm-disabled {
    color: #94a3b8 !important;
    cursor: not-allowed !important;
    pointer-events: none;
    background: #f8fafc !important;
}
#sqDiv,
#sqDiv .dataTables_wrapper,
.cp-table-section {
    overflow: visible !important;
}
</style>
';

$result.= '<table class="table table-hover" id="sqTable" style="width:100%;">';
$result.= '<thead style="background:#1e293b;">';
$th = 'style="color:#fff;font-weight:800;text-transform:uppercase;font-size:0.78rem;letter-spacing:0.5px;"';
$result.= '<tr style="background:#1e293b;">
    <th '.$th.'>S.NO</th>
    <th '.$th.'>SQ NO</th>
    <th '.$th.' style="color:#fff;font-weight:800;text-transform:uppercase;font-size:0.78rem;letter-spacing:0.5px;white-space:nowrap;">DATE</th>
    <th '.$th.'>JC NOS</th>
    <th '.$th.'>CUSTOMER</th>
    <th '.$th.'>APP.VALUE</th>
    <th '.$th.'>DISC.</th>
    <th '.$th.'>BALANCE</th>
    <th '.$th.'>USER</th>
    <th '.$th.'>PAYMODE</th>
    <th '.$th.'>ACTIONS</th>
</tr>';
$result.= '<tr style="background:#334155;">
    <th style="padding:4px;"></th>
    <th style="padding:4px;"><input type="text" placeholder="Search SQ..." style="padding:4px 8px;border:1px solid #cbd5e1;border-radius:4px;font-size:0.8rem;" /></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
</tr>';
$result.= '</thead><tbody>';
$sq_sno = 0;

 while ($row = $stmt->fetch_assoc()) {
		$td_str="<td>";
		$highlightCSS="";
		$det_sql="select * from jobcard_master where jobcard_no in (".$row["jobcard_nos"].");";
		//echo $det_sql."<br>";
		$customer_type="";
		$customer_code="";
		if($det_qry=mysqli_query($connection,$det_sql))
		{
			if($det_row=mysqli_fetch_array($det_qry))
			{
				$customer_type=$det_row["customer_type"];
				$customer_code=$det_row["customer_code"];
			}
		}
		if($customer_type=="Credit"){ $td_str='<td class="highlight">';	$highlightCSS=' class="highlight"'; }
		$valid_customer=1;
		if($customer_code_txt!="all")
		{
			if($customer_code_txt!=$customer_type && $customer_code_txt!=$customer_code)
			{
				$valid_customer=0;
				//log_this($customer_code_txt . " vs " .$customer_type." != ".($customer_code_txt!=$customer_type));
				//log_this($customer_code_txt . " vs " .$customer_code." != ".($customer_code_txt!=$customer_code));
			}
		}
		
		 $Paymode="";
		 if($row["balance_cash"]>0)
		 {
			 if($Paymode!="") $Paymode.=",";
			 $Paymode="Cash"; 
		 }
		 if($row["balance_gpay"]>0)
		 {
			 if($Paymode!="") $Paymode.=",";
			 $Paymode="Online"; 
		 }
		 if($customer_code!="" && $row["balance_received_total"]==0) 
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
		if($valid_customer)
		{
		$sq_sno++;
		$result.= '<tr data-id="'.$row["quotation_no"].'"> ';
		$result.= '<td style="text-align:center;font-weight:600;font-size:0.82rem;">'.$sq_sno.'</td>';
		$sql="SELECT * FROM  jobcard_master where jobcard_no in (".$row["jobcard_nos"].");"; 
		
		$customer_details="";
		$act_customer_name=""; 
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
				 while($jcrows=mysqli_fetch_array($query)) 
				 {
					if($jcrows["customer_name"] != "" )  $customer_name=$jcrows["customer_name"]; 
					$act_customer_name=$customer_name;
						if($jcrows["customer_type"]=="WalkIn") $customer_name="Walk In";
						if($jcrows["customer_type"]=="General"){ $customer_name.=" General ".$jcrows["customer_mobile_no"]; $act_customer_name="General";}
					
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
				
		if($row["is_this_converted_into_invoice"])
		{
		$result.= '<td style="background-color:#01f9c0;">'.$row["quotation_no"].' Closed</td>';			
		}
		else
		{
		$result.= '<td '.$highlightCSS.' >';
		if($customer_type!="WalkIn")
			$result.= '<input type="checkbox" class="form-check-input select_sqs" value="'.$row["quotation_no"].'" jc_nos="'.$row["jobcard_nos"].'" customer="'.$act_customer_name.'" /> ';
		$result.= $row["quotation_no"].'</td>';
		}
		$result.= $td_str.date("d-m-Y",strtotime($row["quotation_dt_tm"])).'</td>';
		$result.= $td_str.$row["jobcard_nos"].'</td>';
		
		
		//$result.= '<td style="font-size:11px;">'.$customer_details.'</td>';
		$result.= $td_str.'<span class="bold-customer">'.$customer_name.'</span></td>';
		$discount=$row["discount"];
		$result.= '<td  '.$highlightCSS.' style="font-size:11px;"><span class="bold-amount">'.$row["approximate_amount"].($discount>0?("-".$discount."=".($row["approximate_amount"]-$discount)):"") .'</span></td>';
		
		$result.= $td_str.$row["discount"].'</td>';
		$result.= $td_str.$row["balance_received_total"].'</td>';
		$result.= $td_str.$row["created_by"].'</td>';
		//$customer_type=$row["customer_type"];
		$highlightCSS="";
		if($customer_type=="Credit") $highlightCSS=' class="highlight"';	
		$result.= '     <td'.$highlightCSS.'>'.$Paymode.'</td>';
		
		// ── Actions cell ──
		$sqno = intval($row["quotation_no"]);
		$jc_nos_attr = addslashes($row["jobcard_nos"]);
		$is_admin_sq = ($sess_user_type == 'ADMIN' || $sess_user_type == 'SUPERADMIN');

		$result.= '<td '.$highlightCSS.' style="text-align:center;vertical-align:middle;">';

		if ($is_admin_sq) {
			// ── ADMIN: 3-dot dropdown ──
			$result.= '<div class="jc-action-wrap">';
			$result.= '<button class="jc-dot-btn" type="button" title="Actions" data-sqno="'.$sqno.'">&#8942;</button>';
			$result.= '<div class="jc-drop-menu">';

			$result.= '<a class="dm-print" target="_blank" href="bill_receipt.php?sq='.$sqno.'">'
			        . '<span class="material-icons-round" style="font-size:15px;">print</span> Print</a>';

			$result.= '<button class="dm-prog" onclick="showJobCard(\''.$jc_nos_attr.'\','.$sqno.')">'
			        . '<span class="material-icons-round" style="font-size:15px;">visibility</span> Show</button>';

			if(!$row["is_this_converted_into_invoice"])
			{
				$result.= '<button class="dm-edit" onclick="mini_edit('.$sqno.')">'
				        . '<span class="material-icons-round" style="font-size:15px;">edit</span> Edit</button>';
			}

			$result.= '</div></div>';
		} else {
			// ── Non-admin: inline buttons ──
			$result.= '<a class="btn btn-sm btn-primary m-1" target="_blank" href="bill_receipt.php?sq='.$sqno.'">Print</a>';
			$result.= '<a class="btn btn-sm btn-warning m-1" onclick="showJobCard(\''.$jc_nos_attr.'\','.$sqno.')">Show</a>';
		}

		$result.= '</td>'; 
		$result.= '</tr>'; 
		}
	
}
$result.="</tbody></table>";
echo $result;
?>