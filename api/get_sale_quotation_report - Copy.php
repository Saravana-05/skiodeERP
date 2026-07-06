<?php
session_start();
include_once '../connect_db.php';
$result=""; 
if(isset($_POST['from']))
{
	file_put_contents("aaa.txt","");
	$from = date("Y-m-d", strtotime($_POST['from']));
	$to = date("Y-m-d", strtotime($_POST['to']));
	$sql="SELECT * FROM sales_quotation_master where quotation_dt_tm between '".$from." 00:00:00' and '".$to." 23:59:59'";  
	$customer_code_txt=$_POST["customer_code_txt"];

	$user_name_txt=$_POST["user_name_txt"];
	if($user_name_txt!="all")
	{
		$sql.=" and created_by='".$_POST["user_name_txt"]."'";
	}
	

	$pay_mode_txt=$_POST["pay_mode_txt"];
	$result.='<div class="row"><div class="col-10" id="printable_div">';
	$total_cash=0.0;
	$total_credit=0.0;
	$total_online=0.0;
	$total_discount=0.0;
	$total_tax=0.0;	

	$Cr_Cash=0;$Cr_SB=0;$Cr_CA=0;$Cr_TotAmt=0;
	$JC_Cash=0;$JC_Bank_SB=0;$JC_Bank_CA=0;$JC_TotAmt=0;
	$Tot_Coll_Cash=0;$Tot_Coll_CA=0;$Tot_Coll_SB=0;
	
	$Sales_Cash=0;$Sales_CA=0;$Sales_SB=0;$Sales_TotAmt=0;
	$Sales_Credit=0;$Sales_Void=0;$Sales_DistAmt=0 ;
	$GST_Sales_Cash=0;$GST_Sales_CA=0;$GST_Sales_SB=0;$GST_Sales_TotAmt=0;
	$Tot_BV=0;
	
	$JC_Nos='';
	$Quotation_Nos='';
	$Invoice_Nos='';
	if($query=mysqli_query($connection,$sql))
	{
		$result.='<table width="100%" class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
			$result.='<thead class="table-dark">';
			$result.='<tr> ';
			$result.='<th>S.No</th>';
			$result.='<th>SQ Date</th>'; 
			$result.='<th>SQ No</th>';
			$result.='<th>JC No</th>';
			$result.='<th>Customer</th>';
			$result.='<th>User</th>';
			$result.='<th>Time</th>';
			//$result.='<th>JC Details</th>';
			$result.='<th>App.Amount</th>';
			$result.='<th>Adv.Rcvd</th>';
			$result.='<th>Discount</th>';
			$result.='<th>Bal.Rcvd</th>';
			$result.='<th>Bal.Pending</th>';
			
			$result.='<th>Paymode</th>';
			$result.='</tr>';
			$result.='</thead>';
			$result.='<tbody>';
			$i=1;
		if(mysqli_num_rows($query)>0)
		{ 
			 while($row=mysqli_fetch_array($query)) {
					$pay_mode="";
					$skipthis=0;
					$jc_nos="";
					$customer_name="";
					$customer_code="";
					$customer_mobile_no="";
					$Quotation_Nos="";
					$JC_Nos=$JC_Nos.$row["jobcard_nos"].',';	
					$jc_sql="select * from jobcard_master where jobcard_no in (".$row["jobcard_nos"].");";		
					if($jc_qry=mysqli_query($connection,$jc_sql))
					{
						if($jc_row=mysqli_fetch_array($jc_qry))
						{
							if($jc_row["customer_code"]!="")
							{ 
								$pay_mode.=(strlen($pay_mode)>0?",":"")."credit";
								//$total_credit+=$row["approximate_amount"];
							}
							$customer_type = $jc_row["customer_type"];
							$customer_name = $jc_row["customer_name"];
							$customer_code = $jc_row["customer_code"];							
							$customer_mobile_no = $jc_row["customer_mobile_no"];							
						}
					}						
					if($customer_code_txt!="all")
					{
						if(strtoupper($customer_code_txt)!=strtoupper($customer_code)) 
						{
							if($customer_code_txt =="walkin"){
								if($customer_type!=="WalkIn") $skipthis=1;
							}
							else if($customer_code_txt =="general"){
								if($customer_type!=="General") $skipthis=1;
							}
							else
							{
							$skipthis=1;
							}
						}
					}
					if(!$skipthis)
					{
					if($row["balance_gpay"]>0) 
					{
						$pay_mode.=(strlen($pay_mode)>0?",":"")."online";						
					
					}
					if($row["balance_cash"]>0) {
						$pay_mode.=(strlen($pay_mode)>0?",":"")."cash";						
					}						
					if($pay_mode_txt=="all" || strpos($pay_mode,$pay_mode_txt)!==false) 
						$skipthis=0;
					else
						$skipthis=1;
					}					
					if(!$skipthis)
					{
						$pay_mode="";
					if($row["balance_gpay"]>0) 
					{
						$pay_mode.=(strlen($pay_mode)>0?",":"")."online";						
						$SQ_GPay=$row["balance_gpay"];
							//if($SQ_GPay>1999)
							if($SQ_GPay<0)
							{
								$Sales_CA+=$SQ_GPay;
							}
							else
							{
								//if(strpos("online",$pay_mode_txt)!==false)
								$Sales_SB+=$SQ_GPay;
							}						
					}
					if($row["balance_cash"]>0) {
						$pay_mode.=(strlen($pay_mode)>0?",":"")."cash";						
						$Sales_Cash+=$row["balance_cash"];
					}
					
					$JC_Nos=$JC_Nos.$row["jobcard_nos"].',';	
					$jc_sql="select * from jobcard_master where jobcard_no in (".$row["jobcard_nos"].");";
					//file_put_contents("aaa.txt",$jc_sql.PHP_EOL,FILE_APPEND);
					$advance_details="";
					$jit_cash=0.0;
					$jit_sb=0.0;
					$jit_ca=0.0;
					if(!$skipthis)
					if($jc_qry=mysqli_query($connection,$jc_sql))
					{
						$first_time_passed=0;
						while($jc_row=mysqli_fetch_array($jc_qry))
						{
							if(!$first_time_passed)
							{
							if($jc_row["customer_code"]!="")
							{ 
								$pay_mode.=(strlen($pay_mode)>0?",":"")."credit";
								//$total_credit+=$row["approximate_amount"];
							}
							$customer_type = $jc_row["customer_type"];
							$customer_name = $jc_row["customer_name"];
							$customer_code = $jc_row["customer_code"];
							$customer_mobile_no = $jc_row["customer_mobile_no"];
							$first_time_passed=1;
							}
							$JC_Cash+=$jc_row["advance_cash"];
							$jit_cash+=$jc_row["advance_cash"];
							if($jc_row["advance_cash"]>0)
							{
								if(strpos($pay_mode,"cash")===False)
								$pay_mode.=(strlen($pay_mode)>0?",":"")."cash";	
							}
							$JC_GPay=$jc_row["advance_gpay"];
							if($jc_row["advance_gpay"]>0)
							{
								if(strpos($pay_mode,"online")===False)
								$pay_mode.=(strlen($pay_mode)>0?",":"")."online";	
							}							
							//if($JC_GPay>1999)
							if($JC_GPay<0)
							{
								$JC_Bank_CA+=$JC_GPay;
								$jit_ca+=$JC_GPay;
							}
							else
							{
								$JC_Bank_SB+=$JC_GPay;
								$jit_sb+=$JC_GPay;
								
							}							
						}
						file_put_contents("aaa.txt",$jit_cash." + ".$jit_sb." + ".$jit_ca.PHP_EOL,FILE_APPEND);
						if($jit_cash>0)
							$advance_details.="Cash : ".$jit_cash;
						if($jit_sb > 0)
							$advance_details.= " SB : " .$jit_sb;
						if($jit_ca > 0)
							$advance_details.= " CA : " .$jit_ca;
						
						
					}

						$result.='<tr>';
						$result.='<td style="text-align:center;">'.$i.'</td>';
						$result.='<td style="white-space: nowrap;">'.date("d-m-Y", strtotime($row["quotation_dt_tm"])).'</td>';
						$jcnos=$row["jobcard_nos"];
						$result.='<td  style="text-align:center;"><a href="javascript:void()" onclick="showJobCard('.$jcnos.','.$row["quotation_no"].')">'.$row["quotation_no"].'</a>';
						$result.='&nbsp;<a class="m-1 noPrint" target="_blank" href="bill_receipt.php?sq='.$row["quotation_no"].'"><i class="fa-solid fa-print"></i></a></td>';
						$result.='<td style="text-align:center;">'.$row["jobcard_nos"].'</td>';
						$Quotation_Nos=$Quotation_Nos.$row["quotation_no"].',';
						$result.='<td>';
						if($customer_type!="Credit")
						{
							if($customer_type=="General")
							{
								$result.="Gen - ".$customer_mobile_no."<br />";
							}
							else
							{
							$result.=$customer_type."<br />";
							}
						}
						else
						{
							$result.=$customer_name."<br />";
							$total_credit+=($row["approximate_amount"]-$row["discount"]);
							$Sales_Credit+=$row["approximate_amount"]-$row["discount"];
							//file_put_contents("aaa.txt","SQ No ".$Quotation_Nos." = ".$Sales_Credit.PHP_EOL,FILE_APPEND);
						}
						$result.='</td>';						
						
						$result.='<td>'.$row["created_by"].'</td>'; 
						$result.='<td>'.date("h:i:s", strtotime($row["quotation_dt_tm"])).'</td>';
						$result.='<td  style="text-align:right;">'.$row["approximate_amount"].'</td>'; 
						

						$advance_txt=0.00;
						$balance_pending_txt=0.00;
						if($row["balance_received_total"]>0)
							$advance_txt=$row["approximate_amount"]-$row["balance_received_total"]-$row["discount"];
						else
							$balance_pending_txt=$row["approximate_amount"]-$row["discount"];
						
						$result.='<td  style="text-align:right;">'.$advance_details.'</td>'; 
						//$result.='<td>'.$advance_txt.'</td>'; 
						$result.='<td  style="text-align:right;">'.$row["discount"].'</td>'; 
						$Sales_DistAmt+=$row["discount"];
						$balance_payment_details="";
						if($row["balance_cash"]>0) $balance_payment_details="Cash : ".$row["balance_cash"];
						if($row["balance_gpay"]>0)
						{
							//if($row["balance_gpay"]>1999)
							if($row["balance_gpay"]<0)
								$balance_payment_details.=" CA : ".$row["balance_gpay"];
							else
								$balance_payment_details.=" SB : ".$row["balance_gpay"];
						}
						$result.='<td  >'.$balance_payment_details.'</td>'; 
						//$result.='<td>'.$row["balance_received_total"].'</td>'; 
						$result.='<td  style="text-align:right;">'.$balance_pending_txt.'</td>'; 
						$result.='<td>'.$pay_mode.'</td>'; 
						$result.='</tr>';
						
						$i++;
					}
			  }
			
		}		 
		$result=$result.'</tbody>';
		$result=$result.'</table>';
	} 
	$result.='</div>';
	$result.='<div class="col-2 text-bg-light" style="border:1px solid #ccc;border-radius:5px;" >';
	 
	$result.='<div class="row">';

	$result.='<div class="col-12"  style="border:0px solid #ccc; padding:5px;" >';
/* 	$result.='<div class="text-center bg-info text-white"><h5 class="mb-1">JOB CARD ADV. AMT</h5></div>';
	$result.='<div  id="card_1">';
	$result.='<table width="100%">';
	$result.='<tr><td align="right">Cash : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;color:green;font-weight:bold;">'.$JC_Cash.'</div></td></tr>';   
	$result.='<tr><td align="right">Online SB : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$JC_Bank_SB.'</div></td></tr>';
	$result.='<tr><td align="right">Online CA : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$JC_Bank_CA.'</div></td></tr>';
	$JC_TotAmt=$JC_Cash+$JC_Bank_SB+$JC_Bank_CA;
	$result.='<tr><td align="right">Total Amt : </td><td><div style="border:1px solid black;background-color:white;text-align:right;padding:2px;color:green;font-weight:bold;">'.$JC_TotAmt.'</div></td></tr>';
	$result.='</table>';
	$result.='</div>'; 
	$result.='</div>'; */ 
	$result.='<div class="col-12" style="border:0px solid #ccc; padding:5px;"  >';
	$result.='<div class="text-center bg-info text-white"><h5 class="mb-1">SALES</h5></div>';
	$result.='<div id="card_2">';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">Cash : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Sales_Cash.'</div></td></tr> ';
	$result.='<tr><td align="right">CA A/c :  </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Sales_CA.'</div></td></tr> ';
	$result.='<tr><td align="right">SB A/c : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Sales_SB.'</div></td></tr>';
	$result.='<tr><td align="right">Credit : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Sales_Credit.'</div></td></tr>';
	
	$result.='<tr><td align="right">Dist Amt : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Sales_DistAmt.'</div></td></tr>';
	$Sales_TotAmt=$Sales_Cash+$Sales_CA+$Sales_SB+$Sales_Credit;//-$Sales_DistAmt;
	$result.='<tr><td align="right">Total Amt : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;color:green;font-weight:bold;">'.$Sales_TotAmt.'</div></td></tr>';
	$result.='</table>';
	$result.='</div>';
	$result.='</div>';
	$result.='<div class="col-12"  style="border:0px solid #ccc; padding:5px;" >';
	$result.='<div class="text-center bg-info text-white" ><h5 class="mb-1">TOTAL COLLECTION</h5></div>';
	$result.='<div  id="card_3">';
	$result.='<table width="100%" >';
	$Tot_Coll_Cash=$JC_Cash+$Sales_Cash;
	$result.='<tr><td align="right" width="50%">Cash Total: </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;color:green;font-weight:bold;">'.$Tot_Coll_Cash.'</div></td></tr>';   
	$Tot_Coll_CA=$JC_Bank_CA+$Sales_CA;
	$result.='<tr><td align="right">CA Total : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;color:green;font-weight:bold;">'.$Tot_Coll_CA.'</div></td></tr>';
	$Tot_Coll_SB=$JC_Bank_SB+$Sales_SB;
	$result.='<tr><td align="right">SB Total : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;color:green;font-weight:bold;">'.$Tot_Coll_SB.'</div></td></tr>';
	$Tot_BV=$Tot_Coll_Cash+$Tot_Coll_CA+$Tot_Coll_SB+$Sales_Credit;
	$result.='<tr><td align="right">Total BV : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;color:green;font-weight:bold;">'.$Tot_BV.'</div></td></tr>';
	$result.='</table>';
	$result.='</div>'; 
	$result.='</div>'; 
 
	$result.='<div class="col-12"  style="border:0px solid #ccc; padding:5px;">';
	$result.='<br> <center><button type="button" class="btn btn-primary btn-sm" onclick="printSaleQuoteDiv('."'printable_div'".')">PRINT</button><br><br>';
	$result.='<button type="button" class="btn btn-secondary btn-sm" onclick="fnSideMenu(1)">Close</button></center>';
	$result.='</div>';
	$result.='</div> ';
	 
	 
	$result.='</div></div>';
	$result.='<br>';

	
	$result.='';
	$result.='</div>';
	$result.='</div>';	
}
echo $result;
?>