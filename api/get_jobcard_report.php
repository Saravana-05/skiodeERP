<?php
session_start();
include_once '../connect_db.php';
$result=""; 
if(isset($_POST['from']))
{
	$from = date("Y-m-d", strtotime($_POST['from']));
	$to = date("Y-m-d", strtotime($_POST['to']));
	$current_date=date("Y-m-d");

/*	$status =  $_POST['status'] ;
 	if($status == "All") */
		$sql="SELECT * FROM jobcard_master where jobcard_date >='".$from."' and jobcard_date<='".$to."'";  
/* 	else if($status == "Open")
		$sql="SELECT * FROM jobcard_master where jobcard_date >='".$from."' and jobcard_date<='".$to."' and job_card_closed=1";  
	else if($status == "Close")
		$sql="SELECT * FROM jobcard_master where jobcard_date >='".$from."' and jobcard_date<='".$to."' and job_card_closed=0";  */ 
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
	$sql.=" order by jobcard_no";
	//$result.=$sql."<br>";
	$pay_mode_txt=$_POST["pay_mode_txt"];
	$result.='<div class="row"><div class="col-10"  id="printable_div">';
	$total_cash=0.0;
	$total_credit=0.0;
	$total_void=0.0;
	$total_online=0.0;
	$total_discount=0.0;
	$total_tax=0.0;
	if($query=mysqli_query($connection,$sql))
	{
		$result.='<table class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
			$result=$result.'<thead class="table-dark">';
			$result=$result.'<tr> ';
			$result=$result.'<th>S.No</th>';
			$result=$result.'<th>JC Date</th>'; 
			$result=$result.'<th>JC No</th>';
			$result=$result.'<th>Customer</th>';
			$result=$result.'<th>User</th>';
			$result=$result.'<th>Time</th>';
			//$result=$result.'<th>JC Details</th>';
			$result=$result.'<th>App.Amount</th>';
			$result=$result.'<th>Adv.Amount</th>';
			$result=$result.'<th>Paymode</th>';
			$result=$result.'<th>Actions</th>';
			$result=$result.'</tr>';
			$result=$result.'</thead>';
			$result=$result.'<tbody>';
			$i=1;
		if(mysqli_num_rows($query)>0)
		{ 
			 while($row=mysqli_fetch_array($query)) {
					$td_str="<td>";
					$highlightCSS="";
					$customer_type=$row["customer_type"];
					if($customer_type=="Credit"){ $td_str='<td class="highlight">';	$highlightCSS=' class="highlight"'; }
					$pay_mode="";
					if($row["customer_code"]!="") $pay_mode="credit";
					
					if($row["advance_gpay"]>0) {
						$pay_mode=(strlen($pay_mode)>0?",":"")."online";
					}
					if($row["advance_cash"]>0) {
						$pay_mode=(strlen($pay_mode)>0?",":"")."cash";
					}	
					if($row["void_job_card"]) $pay_mode=(strlen($pay_mode)>0?",":"")."void";
					if($pay_mode_txt=="all" || strpos($pay_mode,$pay_mode_txt)!==false)
					{

						$result=$result.'<tr>'; 					
						$result=$result.$td_str.$i.'</td>';
						$result=$result.$td_str.date("d-m-Y", strtotime($row["jobcard_date"])).'</td>';
						$result=$result.$td_str.'<a  target="_blank" href="jc_print_out.php?jc='.$row["jobcard_no"].'">'.$row["jobcard_no"].'</a></td>';
						
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
						else
						{

							if(!$row["void_job_card"])								
								$total_credit+=$row["approximate_amount"];
						}
						$result=$result.$td_str.$customer_name.'</td>';					
						$result=$result.$td_str.$row["created_by"].'</td>';
						$result=$result.$td_str.date("h:i:s", strtotime($row["created_dt_tm"])).'</td>';
						$result=$result.'<td align="right"'.$highlightCSS.'>'.$row["approximate_amount"].'</td>';
						$advance_amount=$row["advance_amount"];
						if($advance_amount==0)$advance_amount="";
						$result=$result.$td_str.$advance_amount.'</td>';
						$pay_mode="";
						if(!$row["void_job_card"])
						if($row["advance_gpay"]>0) {
							$pay_mode="Online";
							$total_online+=$row["advance_gpay"];
						}
						if(!$row["void_job_card"])
						if($row["advance_cash"]>0) {
							$pay_mode.=(strlen($pay_mode)>0?",":"")."Cash";
							$total_cash+=$row["advance_cash"];
						}	
						if($row["void_job_card"])  $pay_mode=(strlen($pay_mode)>0?",":"")."void";
						$result=$result.$td_str.$pay_mode.'</td>';

						$result=$result.$td_str.'<a  onclick="showJobCard('.$row["jobcard_no"].','."''".')"><i class="fas fa-desktop"></i></a></td>';
						$result=$result.'</tr>';
						$i++;
					}
/* 					else
					{
						$result.="<tr><td>".$pay_mode_txt." Vs ".$pay_mode."</td></tr>";
					} */
			 }
			
		}
		 
		$result=$result.'</tbody>';
		$result=$result.'</table>';
	} 
	$result.='</div>';
	$result.='<div class="col-2 text-bg-light" style="border-left:1px inset #ccc;" >';
	$result.='<div class="text-center bg-success text-white"><h5 class="mb-0">SUMMARY</h5></div>';
	$result.='<div  id="card_1">';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">'."Cash : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($total_cash,2)."</div></td></tr>";
	$result.='<tr><td align="right">'."Credit : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($total_credit,2)."</div></td></tr>";
	$result.='<tr><td align="right">'."Online : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($total_online,2)."</div></td></tr>";
	$result.='</table>';
	$result.='</div>';
			$result.='<div class="col-12"  style="border:0px solid #ccc; padding:5px;">';
	$result.='<br> <center><button type="button" class="btn btn-primary btn-sm" onclick="printJobCardDiv('."'printable_div'".')">PRINT</button><br><br>';
	$result.='<button type="button" class="btn btn-secondary btn-sm" onclick="fnSideMenu(1)">Close</button></center>';
	$result.='</div>';
	$result.='</div>';

	$result.='</div>';
}
echo $result;
?>