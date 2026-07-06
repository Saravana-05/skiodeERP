<?php
session_start();
include_once '../connect_db.php';
$result=""; 
$pay_mode="";
function add_payment_mode($pay_mode_now)
{
	global $pay_mode;
	if(strpos($pay_mode,$pay_mode_now)===false)
		$pay_mode.=(strlen($pay_mode)>0?", ":"").$pay_mode_now;	
}
if(isset($_POST['from']))
{
	$from = date("Y-m-d", strtotime($_POST['from']));
	$to = date("Y-m-d", strtotime($_POST['to']));
	$sql="SELECT * FROM sales_invoice_master where invoice_dt_tm between '".$from." 00:00:00' and '".$to." 23:59:59'";
	$customer_code_txt=$_POST["customer_code_txt"];
	if($customer_code_txt==NULL) $customer_code_txt="";
	$user_name_txt=$_POST["user_name_txt"];
	if($user_name_txt!="all")
	{
		$sql.=" and created_by='".$_POST["user_name_txt"]."'";
	}	

	$pay_mode_txt=$_POST["pay_mode_txt"];
	$result.='<div class="row"><div class="col-10" id="printable_div" >';
	$total_cash=0.0;
	$total_credit=0.0;
	$total_online=0.0;
	$total_discount=0.0;
	$total_tax=0.0;	

	$Cr_Cash=0;$Cr_Bank=0;$Cr_Gpay=0;$Cr_TotAmt=0;
	$JC_Cash=0;$JC_Gpay=0;$JC_TotAmt=0;
	$Tot_Coll_Cash=0;$Tot_Coll_CA=0;$Tot_Coll_SB=0;
	$GST_Sales_Cash=0;$GST_Sales_CA=0;$GST_Sales_SB=0;$GST_Sales_TotAmt=0;
	$Sales_Cash=0;$Sales_CA=0;$Sales_SB=0;$Sales_TotAmt=0;
	$Sales_Credit=0;$Sales_Void=0;$Sales_DistAmt=0 ;
	$Sum_Cash=0;$Sum_Online=0;$Sum_Credit=0;$Sum_Discount=0;$Sum_CA=0;$Sum_SB=0;$Sum_TotAmt=0;
	$balance_received_per_invoice=0.0;
	$JC_Nos='';
	$Quotation_Nos='';
	$Invoice_Nos='';
	$result.="<style>@media print {.noPrint {    display:none;  }  .print_class{ font-size:0.7vw;}   @page{ size:a4; } }</style>";
	if($query=mysqli_query($connection,$sql))
	{
		$result=$result.'<table width="100%" class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
			$result=$result.'<thead class="table-dark">';
			$result=$result.'<tr> ';
			$result=$result.'<th>#</th>';
			 
			$result=$result.'<th>Date</th>';
			$result=$result.'<th>Inv. No</th>';
			$result=$result.'<th>Customer</th>';
			$result=$result.'<th>GST.NO</th>';
			$result=$result.'<th>User</th>';
			$result=$result.'<th>Time</th>';
			$result=$result.'<th>App. Amt</th>'; 
			//$result=$result.'<th>Adv.Rcvd</th>';
			$result=$result.'<th>Disc.</th>';
			$result=$result.'<th>Taxable. Amt</th>';
			$result=$result.'<th>Tax</th>';
			//$result=$result.'<th>Bal.Rcvd</th>';
			//$result=$result.'<th>Bal.Pending</th>'; 
			
			$result=$result.'<th>Net</th>';
			$result=$result.'<th>Paymode</th>';
			$result=$result.'</tr>';
			$result=$result.'</thead>';
			$result=$result.'<tbody>';
			$i=1;
		if(mysqli_num_rows($query)>0)
		{ 
			 while($row=mysqli_fetch_array($query)) {
					$JC_Nos="";
					$balance_received_per_invoice=0.0;
					$pay_mode="";
					$skipthis=0; 
					$customer_name="";
					$customer_GST_NO="";
					$customer_code="";
					$sq_nos=$row["sale_quotation_nos"];
					$si_customer_code=$row["customer_code"];
					if(!empty($sq_nos))
					{
						$Quotation_Nos=$Quotation_Nos.$row["sale_quotation_nos"].',';
						$sq_sql="select * from sales_quotation_master where quotation_no in (".$row["sale_quotation_nos"].");";
						if($sq_qry=mysqli_query($connection,$sq_sql))
						{
							while($sq_row=mysqli_fetch_array($sq_qry))
							{
								if($sq_row["balance_gpay"]>0) {
									add_payment_mode("online");
									
								}
								if($sq_row["balance_cash"]>0) {
									add_payment_mode("cash");

								}

								$jc_sql="select * from jobcard_master where jobcard_no in (".$sq_row["jobcard_nos"].");";
								if($jc_qry=mysqli_query($connection,$jc_sql))
								{
									if($jc_row=mysqli_fetch_array($jc_qry))
									{
										if($jc_row["customer_code"]!="")
										{ 
											add_payment_mode("credit");
										}
										$customer_type = $jc_row["customer_type"];
										$customer_name = $jc_row["customer_name"];
										$customer_code = $jc_row["customer_code"];
										$customer_GST_NO = $jc_row["customer_gst_no"];
									}
								}
							}
						}
					}//if(!empty($sq_nos))
					elseif(!empty($si_customer_code))
					{
						$customer_type="Credit";
						$customer_code=$si_customer_code;
						$Paymode="Credit";
						$cus_sql="select * from customer_master where customer_code='".$si_customer_code."';";
						if($cus_qry=mysqli_query($connection,$cus_sql))
						{
							if($cus_row=mysqli_fetch_array($cus_qry))
							{
								$customer_name=$cus_row["customer_name"];
								$customer_GST_NO = $cus_row["gst_no"];
							}
						}
					}							
					if($customer_code_txt!="all")
					{
						if($customer_code_txt!="walkin" && $customer_code_txt!="general")
						{
						if($customer_code==NULL)$customer_code="~";
						if(strtoupper($customer_code_txt)!=strtoupper($customer_code)) $skipthis=1;
						}
						else
						{
							if(strtoupper($customer_code_txt)!=strtoupper($customer_type)) $skipthis=1;							
						}
					}
					if(!$skipthis)
					{
					if($pay_mode_txt=="all" || strpos($pay_mode,$pay_mode_txt)!==false) 
						$skipthis=0;
					else
						$skipthis=1;
					}					
					if(!$skipthis)
					{		
					$pay_mode="";
					if(!empty($sq_nos))
					{
					$sq_sql="select * from sales_quotation_master where quotation_no in (".$row["sale_quotation_nos"].");";
					if($sq_qry=mysqli_query($connection,$sq_sql))
					{
						while($sq_row=mysqli_fetch_array($sq_qry))
						{
							if($sq_row["balance_gpay"]>0) {
								add_payment_mode("online");
								$total_online +=$sq_row["balance_gpay"];
							}
							if($sq_row["balance_cash"]>0) {
								add_payment_mode("cash");

								$total_cash+=$sq_row["balance_cash"];
							}
							$JC_Nos=$JC_Nos.$sq_row["jobcard_nos"].',';							
							$jc_sql="select * from jobcard_master where jobcard_no in (".$sq_row["jobcard_nos"].");";
							if($jc_qry=mysqli_query($connection,$jc_sql))
							{
								if($jc_row=mysqli_fetch_array($jc_qry))
								{
									if($jc_row["customer_code"]!="")
									{ 
										add_payment_mode("credit");
										$total_credit+=$sq_row["approximate_amount"];
										
									}
									$customer_type = $jc_row["customer_type"];
									$customer_name = $jc_row["customer_name"];
									$customer_code = $jc_row["customer_code"];
									$customer_GST_NO = $jc_row["customer_gst_no"];
								}
							}
							$balance_payment_details="";
							if($sq_row["balance_cash"]>0) {
								$balance_payment_details="Cash : ".$sq_row["balance_cash"];
								$Sum_Cash = $Sum_Cash + $sq_row["balance_cash"];
								$balance_received_per_invoice+=$sq_row["balance_cash"];
							}
							if($sq_row["balance_gpay"]>0)
							{
								//if($sq_row["balance_gpay"]>1999)
								if($sq_row["balance_gpay"]<0)
								{
									$balance_payment_details.=" CA : ".$sq_row["balance_gpay"];
									$Sum_CA = $Sum_CA + $sq_row["balance_gpay"];
									$balance_received_per_invoice+=$sq_row["balance_gpay"];
								}
								else
								{
									$balance_payment_details.=" SB : ".$sq_row["balance_gpay"];
									$Sum_SB = $Sum_SB + $sq_row["balance_gpay"];
									$balance_received_per_invoice+=$sq_row["balance_gpay"];
								}
							}
							if($sq_row["balance_received_total"]>0)
								$advance_txt=$sq_row["approximate_amount"]-$sq_row["balance_received_total"]-$sq_row["discount"];
						}
					}				
					}
					$result=$result.'<tr>';
					$result=$result.'<td align="center">'.$i.'</td>';					
					$result=$result.'<td style="white-space: nowrap;">'.date("d-m-Y", strtotime($row["invoice_dt_tm"])).'</td>';
					$JC_Nos=rtrim($JC_Nos,",");
					$result=$result.'<td align="center"><a href="javascript:void(0)" onclick="showJobCard('."'".$JC_Nos."'".','."'".$row["sale_quotation_nos"]."'".','.$row["invoice_no"].')">'.$row["invoice_no"].'</a>&nbsp;<a class="noprint" href="sale_invoice_print.php?si='.$row["invoice_no"].'" target="_blank">Print</a></td>';
					$Invoice_Nos=$Invoice_Nos.$row["invoice_no"].',';					
					if($customer_type=="Credit")
					{
						$total_credit+=($row["approximate_amount"]-$row["discount"]);
					}
					// Credit customers → show real name; all others (WalkIn/General) → "General"
					$display_name = ($customer_type == 'Credit') ? $customer_name : 'General';
					$result.='<td';
					if(strlen($display_name)>14) $result.=' class="print_class"';
					$result.='>'.htmlspecialchars($display_name).'</td>';
					$result=$result.'<td>'.$customer_GST_NO.'</td>';		
					$result=$result.'<td>'.$row["created_by"].'</td>'; 		
					$result.='<td align="center">'.date("h:i:s", strtotime($row["invoice_dt_tm"])).'</td>';	
					
					$result=$result.'<td align="right">'.ind_money($row["approximate_amount"],2).'</td>'; 
					$jc_sql="select * from jobcard_master where jobcard_no in (".$JC_Nos.");";
					
					$advance_details="";
					$jit_cash=0.0;
					$jit_sb=0.0;
					$jit_ca=0.0;
					$total_advance=0.0;
					$JC_Bank_SB=0;
					if(!empty($sq_nos))
					if($jc_qry=mysqli_query($connection,$jc_sql))
					{
						$first_time_passed=0;
						while($jc_row=mysqli_fetch_array($jc_qry))
						{
							if(!$first_time_passed)
							{
							if($jc_row["customer_code"]!="")
							{ 
								add_payment_mode("credit");
								//$total_credit+=$row["approximate_amount"];
							}
							$customer_type = $jc_row["customer_type"];
							$customer_name = $jc_row["customer_name"];
							$customer_code = $jc_row["customer_code"];
							$first_time_passed=1;
							}
							$JC_Cash+=$jc_row["advance_cash"];
							$jit_cash+=$jc_row["advance_cash"];
							if($jc_row["advance_cash"]>0)
							{
								add_payment_mode("cash");
								$Sum_Cash = $Sum_Cash + $JC_Cash;
							}
							$JC_GPay=$jc_row["advance_gpay"];
							if($jc_row["advance_gpay"]>0)
							{								
								add_payment_mode("online");	
								$Sum_Online = $Sum_Online + $JC_GPay;
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
						
						if($jit_cash>0)
							$advance_details.="Cash : ".$jit_cash;
						if($jit_sb > 0)
							$advance_details.= " SB : " .$jit_sb;
						if($jit_ca > 0)
							$advance_details.= " CA : " .$jit_ca;
						$total_advance=$jit_cash+$jit_sb+$jit_ca;
						
					}
					
					//$result.='<td class="print_class"">'.$advance_details.'</td>'; 
					$result.='<td align="right">'.ind_money($row["discount"],2).'</td>';
					$Sum_Discount = $Sum_Discount+$row["discount"];
					$taxable_value=$row["approximate_amount"]-$row["discount"];
					
					$result=$result.'<td align="right">'.ind_money($taxable_value,2).'</td>'; 
					$result=$result.'<td align="right">'.ind_money($row["total_tax"],2).'</td>'; 
					$total_tax+=$row["total_tax"];
					//$result.='<td  class="print_class" >'.$balance_payment_details.'</td>'; 
					$balance_pending_txt=$row["net_total"]-$total_advance-$balance_received_per_invoice;
					$balance_pending_txt=round($balance_pending_txt,2);
					if($customer_type=="Credit" && $balance_pending_txt > 0) {
						$Sum_Credit = $Sum_Credit + $balance_pending_txt;
					}
					//$result.='<td  style="text-align:right;">'.ind_money($balance_pending_txt,2).'</td>'; 
					
					$result=$result.'<td align="right">'.ind_money($row["net_total"],2).'</td>'; 
					$Sum_TotAmt = $Sum_TotAmt+$row["net_total"];
					$result.='<td align="center" class="print_class">'.$pay_mode.'</td>';
					$result=$result.'</tr>';
					$i++;
					}
			  }
			
		}		 
		$result=$result.'</tbody>';
		$result=$result.'</table>';
	} 
	$result.='</div>';
	$JC_Nos=rtrim($JC_Nos, ", ");
	if($JC_Nos!="")
	{
		$summary_sql="Select IFNULL(sum(advance_gpay),0) as JC_Gpay, IFNULL(sum(advance_cash),0) as JC_Cash  from jobcard_master where jobcard_no in (".$JC_Nos.")";
		 
		if($summary_query=mysqli_query($connection,$summary_sql))
		{
			if(mysqli_num_rows($summary_query)>0)
			{ 
				if($summary_row=mysqli_fetch_array($summary_query)) 
				{
					$JC_Cash=$summary_row["JC_Cash"]; 
					$JC_Gpay=$summary_row["JC_Gpay"];
					$JC_TotAmt=$JC_Cash + $JC_Gpay;
				}
			}
		}
	}
	$Quotation_Nos=rtrim($Quotation_Nos, ", ");
	$Invoice_Nos=rtrim($Invoice_Nos, ", ");
	if($Invoice_Nos!="")
	{
		$summary_sql="Select IFNULL(sum(approximate_amount),0) as GST_Sales_Cash from sales_invoice_master where invoice_no in (".$Invoice_Nos.")"; 
		
		if($summary_query=mysqli_query($connection,$summary_sql))
		{
			if(mysqli_num_rows($summary_query)>0)
			{ 
				if($summary_row=mysqli_fetch_array($summary_query)) 
				{
					$GST_Sales_Cash=$summary_row["GST_Sales_Cash"];
					$GST_Sales_CA=0;
					$GST_Sales_SB=0;
					$GST_Sales_TotAmt=$GST_Sales_Cash+$GST_Sales_CA+$GST_Sales_SB;
				}
			}
		}
	}
	if($Invoice_Nos!="")
	{
		$summary_sql="Select IFNULL(sum(approximate_amount),0) as GST_Sales_Cash from sales_invoice_master where invoice_no in (".$Invoice_Nos.")"; 
		if($summary_query=mysqli_query($connection,$summary_sql))
		{
			if(mysqli_num_rows($summary_query)>0)
			{ 
				if($summary_row=mysqli_fetch_array($summary_query)) 
				{
					$GST_Sales_Cash=$summary_row["GST_Sales_Cash"];
					$GST_Sales_CA=0;
					$GST_Sales_SB=0;
					$GST_Sales_TotAmt=$GST_Sales_Cash+$GST_Sales_CA+$GST_Sales_SB;
				}
			}
		}
	}
	$summary_sql="Select IFNULL( sum(amount),0) as Cr_Cash from receipt_voucher where voucher_date  between '".$from." 00:00:00' and '".$to." 23:59:59' and account_head='Cash' ";
	if($summary_query=mysqli_query($connection,$summary_sql))
	{
		if(mysqli_num_rows($summary_query)>0)
		{ 
			if($summary_row=mysqli_fetch_array($summary_query)) 
			{
				$Cr_Cash=$summary_row["Cr_Cash"]; 
			}
		}
	}
	$summary_sql="Select IFNULL( sum(amount),0) as Cr_Gpay from receipt_voucher where voucher_date  between '".$from." 00:00:00' and '".$to." 23:59:59' and account_head='GPay' ";
	if($summary_query=mysqli_query($connection,$summary_sql))
	{
		if(mysqli_num_rows($summary_query)>0)
		{ 
			if($summary_row=mysqli_fetch_array($summary_query)) 
			{
				$Cr_Gpay=$summary_row["Cr_Gpay"]; 
			}
		}
	}
	if($Quotation_Nos!="")
	{
		$summary_sql="Select sum(balance_cash) as Sales_Cash,sum(balance_gpay) as Sales_SB ,sum(discount) as Sales_DistAmt from sales_quotation_master where quotation_no in (".$Quotation_Nos.")";  
		if($summary_query=mysqli_query($connection,$summary_sql))
		{
			if(mysqli_num_rows($summary_query)>0)
			{ 
				if($summary_row=mysqli_fetch_array($summary_query)) 
				{
					$Sales_Cash=$summary_row["Sales_Cash"]; 
					$Sales_DistAmt=$summary_row["Sales_DistAmt"]; 
					$Sales_SB=$summary_row["Sales_SB"]; 
				}
			}
		}
	}
		$Cr_TotAmt=$Cr_Cash+$Cr_Bank+$Cr_Gpay;
	$Tot_Coll_Cash=$Cr_Cash + $JC_Cash + $Sales_Cash + $GST_Sales_Cash ;
	$Tot_Coll_CA=$Sales_CA + $GST_Sales_CA  ;
	$Tot_Coll_SB=$Sales_SB + $GST_Sales_SB  ;
	$result.='<div class="col-2 text-bg-light" style="border-left:1px inset #ccc;" >';
	 
	/*$result.='<div class="row">';
	$result.='<div class="col-6" style="border:1px solid #ccc; padding:5px;">';
	$result.='<div class="text-center bg-success text-white"><H55 class="mb-0">RECEIPTS</H55></div>';
	$result.='<div  id="card_1">';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">Cr Cash : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Cr_Cash.'</div></td></tr> ';
	$result.='<tr><td align="right">Cr Bank :  </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Cr_Bank.'</div></td></tr> ';
	$result.='<tr><td align="right">Cr Gpay : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Cr_Gpay.'</div></td></tr>';
	$result.='<tr><td align="right">Total Amt : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Cr_TotAmt.'</div></td></tr>';
	$result.='</table>';
	$result.='</div>';
	$result.='</div>';
	$result.='<div class="col-6"  style="border:1px solid #ccc; padding:5px;">';
	$result.='<div class="text-center bg-success text-white"><H55 class="mb-0">JOB CARD ADV. AMT</H55></div>';
	$result.='<div  id="card_2">';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">Cash : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$JC_Cash.'</div></td></tr>';   
	$result.='<tr><td align="right">Gpay : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$JC_Gpay.'</div></td></tr>';
	$result.='<tr><td align="right">Total Amt : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$JC_TotAmt.'</div></td></tr>';
	$result.='</table>';
	$result.='</div>';
	$result.='</div>';
	$result.='</div> ';
	$result.='<div class="row">';
	$result.='<div class="col-6" style="border:1px solid #ccc; padding:5px;">';
	$result.='<div class="text-center bg-success text-white"><H55 class="mb-0">SALES</H55></div>';
	$result.='<div  id="card_3">';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">Cash : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Sales_Cash.'</div></td></tr> ';
	$result.='<tr><td align="right">CA A/c :  </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Sales_CA.'</div></td></tr> ';
	$result.='<tr><td align="right">SB A/c : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Sales_SB.'</div></td></tr>';
	$result.='<tr><td align="right">Credit : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Sales_Credit.'</div></td></tr>';
	$result.='<tr><td align="right">Void : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Sales_Void.'</div></td></tr>';
	$result.='<tr><td align="right">Dist Amt : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Sales_DistAmt.'</div></td></tr>';
	$result.='<tr><td align="right">Total Amt : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Sales_TotAmt.'</div></td></tr>';
	$result.='</table>';
	$result.='</div>';
	$result.='</div>';
	$result.='<div class="col-6"  style="border:1px solid #ccc; padding:5px;">';
	$result.='<div class="text-center bg-success text-white"><H55 class="mb-0">TOTAL COLLECTION</H55></div>';
	$result.='<div  id="card_4">';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">Cash Total: </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Tot_Coll_Cash.'</div></td></tr>';   
	$result.='<tr><td align="right">CA Total : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Tot_Coll_CA.'</div></td></tr>';
	$result.='<tr><td align="right">SB Total : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Tot_Coll_SB.'</div></td></tr>';
	$result.='</table>';
	$result.='</div>';
	$result.='</div>';
	$result.='</div> ';*/ 
	$result.='<div class="row">';
	$result.='<div class="col-12" style="border:1px solid #ccc; padding:5px;">';
	$result.='<div  id="card_5">';
	$result.='<div class="text-center bg-success text-white"></div>';
	
	$result.='<table width="100%" style="break-inside: avoid;"><tr><th colspan="2" class="summary_title">SUMMARY</th></tr>';
	$result.='<tr><td align="right" width="50%">Cash : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sum_Cash,2).'</div></td></tr>';
	$result.='<tr><td align="right" width="50%">Credit : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sum_Credit,2).'</div></td></tr>';
	$result.='<tr><td align="right" width="50%">Discount : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sum_Discount,2).'</div></td></tr>';
	$result.='<tr><td align="right">CA A/c :  </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sum_CA,2).'</div></td></tr> ';
	$result.='<tr><td align="right">SB A/c : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sum_SB,2).'</div></td></tr>';
	$result.='<tr><td align="right">Tax : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($total_tax,2).'</div></td></tr>';
	
	$result.='<tr><td align="right">Total Amt : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sum_TotAmt,2).'</div></td></tr>';
	$result.='</table>';
	$result.='</div>';
	 
	$result.='</div>';
	$result.='<div class="col-12"  style="border:0px solid #ccc; padding:5px;">';
	$result.='<br> <center><button type="button" class="btn btn-primary btn-sm" onclick="printSaleInvoiceDiv('."'printable_div'".')">PRINT</button><br><br>';
	$result.='<button type="button" class="btn btn-secondary btn-sm" onclick="fnSideMenu(1)">Close</button></center>';
	$result.='</div>';
	 
	$result.='</div> ';
	 
	 
	$result.='</div></div>';
	
	$result.='</center>';
	$result.='</div>';
	$result.='</div>';	
}
echo $result;
?>