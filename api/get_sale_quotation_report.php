<?php
session_start();
include_once '../connect_db.php';
$result=""; 
if(isset($_POST['from']))
{
	$from = date("Y-m-d", strtotime($_POST['from']));
	$to = date("Y-m-d", strtotime($_POST['to']));
	$sql="SELECT * FROM sales_quotation_master where quotation_dt_tm between '".$from." 00:00:00' and '".$to." 23:59:59'";  
	$customer_code_txt=$_POST["customer_code_txt"];

	$user_name_txt=$_POST["user_name_txt"];
	if($user_name_txt!="all")
	{
		$sql.=" and created_by='".$_POST["user_name_txt"]."'";
	}
	

	$pay_mode_txt    = $_POST["pay_mode_txt"];
	$product_type_txt = isset($_POST["product_type_txt"]) ? trim($_POST["product_type_txt"]) : 'all';

	// Per-type accumulators (for summary sidebar)
	$overall_type_disc  = ['PRINTING'=>0,'SERVICES'=>0,'MATERIALS'=>0,'IDCARDS'=>0,'PRODUCTS'=>0];
	$overall_type_value = ['PRINTING'=>0,'SERVICES'=>0,'MATERIALS'=>0,'IDCARDS'=>0,'PRODUCTS'=>0];
	$overall_type_count = ['PRINTING'=>0,'SERVICES'=>0,'MATERIALS'=>0,'IDCARDS'=>0,'PRODUCTS'=>0];

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
	$Sales_Credit=0;$Sales_Void=0;$Sales_DistAmt=0;
	$sq_approx_total=0; // SQ gross total (for SUMMARY panel Gross row)
	$sq_adv_total=0;    // advance total across all SQs (display only in table)
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
			$result.='<th>Date</th>'; 
			$result.='<th>SQ No</th>';
			$result.='<th>JC No</th>';
			$result.='<th>Customer</th>';
			$result.='<th>Type</th>';
			$result.='<th>Inv. No</th>';
			$result.='<th>User</th>';
			$result.='<th>Time</th>';
			//$result.='<th>JC Details</th>';
			$result.='<th>App.Amt</th>';
			//$result.='<th>Adv.Rcvd</th>';
			$result.='<th>Disc.</th>';
			$result.='<th>Net.Amt</th>';
			$result.='<th>Adv</th>';
			$result.='<th>Amt Payable</th>';
			$result.='<th>Cash</th>';
			$result.='<th>Online</th>';
			$result.='<th>Paymode</th>';
			$result.='</tr>';
			$result.='</thead>';
			$result.='<tbody>';
			$i=1;
		if(mysqli_num_rows($query)>0)
		{ 
			 while($row=mysqli_fetch_array($query)) {
					$td_str="<td>";
					$highlightCSS="";
					
					
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
					if($customer_type=="Credit"){ $td_str='<td class="highlight">';	$highlightCSS=' class="highlight"'; }
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
							if($SQ_GPay<0)
							{
								$Sales_CA+=$SQ_GPay;
							}
							else
							{
								$Sales_SB+=$SQ_GPay;
							}
					}
					if($row["balance_cash"]>0) {
						$pay_mode.=(strlen($pay_mode)>0?",":"")."cash";
						$Sales_Cash+=$row["balance_cash"];
					}

					$JC_Nos=$row["jobcard_nos"];
					$jc_sql="select * from jobcard_master where jobcard_no in (".$row["jobcard_nos"].");";
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
							}
							$customer_type = $jc_row["customer_type"];
							$customer_name = $jc_row["customer_name"];
							$customer_code = $jc_row["customer_code"];
							$customer_mobile_no = $jc_row["customer_mobile_no"];
							$first_time_passed=1;
							}
						}

						if($jit_cash>0)
							$advance_details.="Cash : ".$jit_cash;
						if($jit_sb > 0)
							$advance_details.= " SB : " .$jit_sb;
						if($jit_ca > 0)
							$advance_details.= " CA : " .$jit_ca;
					}

						// ── Product-type breakdown for this SQ ──────────────────────
						$row_type_disc  = ['PRINTING'=>0,'SERVICES'=>0,'MATERIALS'=>0,'IDCARDS'=>0,'PRODUCTS'=>0];
						$row_type_value = ['PRINTING'=>0,'SERVICES'=>0,'MATERIALS'=>0,'IDCARDS'=>0,'PRODUCTS'=>0];
						$row_type_count = ['PRINTING'=>0,'SERVICES'=>0,'MATERIALS'=>0,'IDCARDS'=>0,'PRODUCTS'=>0];
						$safe_jcnos = preg_replace('/[^0-9,]/', '', $row['jobcard_nos']);
						if ($safe_jcnos) {
							$td_sql = "SELECT UPPER(COALESCE(NULLIF(job_type,''),'OTHER')) AS jt,
							                  SUM(COALESCE(item_discount,0)) AS disc_sum,
							                  SUM(COALESCE(value_amount,0)) AS value_sum,
							                  COUNT(*) AS item_count
							           FROM jobcard_details
							           WHERE jobcard_no IN ($safe_jcnos)
							           GROUP BY job_type";
							if ($td_qry = mysqli_query($connection, $td_sql)) {
								while ($td_row = mysqli_fetch_assoc($td_qry)) {
									$jt = $td_row['jt'];
									if (isset($row_type_disc[$jt])) {
										$row_type_disc[$jt]  += floatval($td_row['disc_sum']);
										$row_type_value[$jt] += floatval($td_row['value_sum']);
										$row_type_count[$jt] += intval($td_row['item_count']);
									}
								}
							}
						}

						// Apply product type filter — show SQs that have items of the selected type
						$ptype_upper = strtoupper($product_type_txt);
						if ($ptype_upper !== 'ALL') {
							if (!isset($row_type_value[$ptype_upper]) || $row_type_value[$ptype_upper] <= 0) {
								continue; // skip this SQ row
							}
						}

						// Accumulate into overall sidebar totals (only the filtered type when a filter is active)
						foreach ($row_type_disc as $jt => $d) {
							if ($ptype_upper !== 'ALL' && $jt !== $ptype_upper) continue;
							$overall_type_disc[$jt]  += $d;
							$overall_type_value[$jt] += $row_type_value[$jt];
							$overall_type_count[$jt] += $row_type_count[$jt];
						}
						// ─────────────────────────────────────────────────────────────

						$result.='<tr>';
						$result.='<td '.$highlightCSS.' style="text-align:center;">'.$i.'</td>';
						$result.='<td '.$highlightCSS.' style="white-space: nowrap;">'.date("d-m-Y", strtotime($row["quotation_dt_tm"])).'</td>';
						$jcnos=$row["jobcard_nos"];
						$result.='<td '.$highlightCSS.' style="text-align:center;"><a href="javascript:void()" onclick="showJobCard('."'".$jcnos."'".','.$row["quotation_no"].')">'.$row["quotation_no"].'</a>';
						$result.='&nbsp;<a class="m-1 noPrint" target="_blank" href="bill_receipt.php?sq='.$row["quotation_no"].'"><span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">print</span></a></td>';
						$result.='<td '.$highlightCSS.' style="text-align:center;">'.$row["jobcard_nos"].'</td>';
						$Quotation_Nos=$Quotation_Nos.$row["quotation_no"].',';
						$result.=$td_str;
						if($customer_type=="Credit")
						{
							$total_credit+=($row["approximate_amount"]-$row["discount"]);
							$Sales_Credit+=$row["approximate_amount"]-$row["discount"];
						}
						// Show customer name; fall back to type label if name is empty
						$display_name = ($customer_name != '') ? $customer_name : $customer_type;
						$result.= htmlspecialchars($display_name);
						if($customer_mobile_no != '') $result.= '<br/><small style="color:#666;">'.$customer_mobile_no.'</small>';
						$result.='</td>';
						// Type column (separate)
						$result.='<td '.$highlightCSS.'>'.htmlspecialchars($customer_type).'</td>';
						$Gst_Inv_No='';
						$inv_sql="select invoice_no from sales_invoice_master where sale_quotation_nos='".$row["quotation_no"]."' or sale_quotation_nos like '".$row["quotation_no"].",%' or sale_quotation_nos like ',".$row["quotation_no"]."%'";
						
						if($inv_qry=mysqli_query($connection,$inv_sql))
						{
							if($inv_row=mysqli_fetch_array($inv_qry))
							{
								$Gst_Inv_No=$inv_row["invoice_no"];
							}
						}
						$result.=$td_str.$Gst_Inv_No.'</td>'; 
						$result.=$td_str.$row["created_by"].'</td>'; 
						$result.=$td_str.date("h:i:s", strtotime($row["quotation_dt_tm"])).'</td>';
						$result.='<td '.$highlightCSS.' style="text-align:right;">'.$row["approximate_amount"].'</td>'; 
						

						// advance_txt = amount paid as advance = net_payable - balance_collected
						// This correctly handles all cases:
						//   partial advance: advance = net - balance_collected (correct)
						//   walk-in full advance saved as balance_cash: balance_collected = net → advance = 0 (no double deduction)
						//   credit customer: credit_amt = net, balance_collected = 0, credit = net → advance = 0
						$net_payable      = floatval($row["approximate_amount"]) - floatval($row["discount"]);
						$bal_collected    = floatval($row["balance_cash"]) + floatval($row["balance_gpay"]);
						$credit_amt       = ($customer_type == 'Credit') ? $net_payable : 0;
						$advance_txt      = max(0, $net_payable - $bal_collected - $credit_amt);
						$balance_pending_txt = $row["approximate_amount"] - $row["discount"] + $row["gst_tax_amount"];
						
						
						//$result.=$td_str.$advance_txt.'</td>'; 
						$result.='<td '.$highlightCSS.' style="text-align:right;">'.ind_money($row["discount"],2).'</td>';
						$result.='<td '.$highlightCSS.' style="text-align:right;">'.ind_money($balance_pending_txt,2).'</td>'; 
						$result.='<td '.$highlightCSS.' style="text-align:right;">'.ind_money($advance_txt,2).'</td>'; 
						$result.='<td '.$highlightCSS.' style="text-align:right;">'.ind_money($balance_pending_txt-$advance_txt,2).'</td>'; 
						$Sales_DistAmt    += $row["discount"];
						$sq_approx_total  += floatval($row["approximate_amount"]);
						$sq_adv_total     += floatval($advance_txt); // display only — not added to totals
						$balance_payment_details="";
						$balance_cash=0;
						$balance_online=0;
						if($row["balance_cash"]>0) { $balance_payment_details="Cash : ".ind_money($row["balance_cash"],2); $balance_cash=$row["balance_cash"]; }
						if($row["balance_gpay"]>0)
						{
							//if($row["balance_gpay"]>1999)
								$balance_online=$row["balance_gpay"];
							if($row["balance_gpay"]<0)
								$balance_payment_details.=" CA : ".$row["balance_gpay"];
							else
								$balance_payment_details.=" SB : ".$row["balance_gpay"];
						}
						//$result.='<td  >'.$balance_payment_details.'</td>'; 
						//$result.=$td_str.$row["balance_received_total"].'</td>'; 
						$result.='<td '.$highlightCSS.' style="text-align:right;">'.ind_money($balance_cash,2).'</td>'; 
						$result.='<td '.$highlightCSS.' style="text-align:right;">'.ind_money($balance_online,2).'</td>'; 
						//$result.='<td  style="text-align:right;">'.$balance_payment_details.'</td>'; 
						$result.=$td_str.$pay_mode.'</td>'; 
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
	// ── SUMMARY panel (product-type breakdown) ───────────────────────────────
	$type_labels_s = ['PRINTING'=>'Printing','SERVICES'=>'Services','MATERIALS'=>'Materials','IDCARDS'=>'ID Cards','PRODUCTS'=>'Products'];
	$BOX = 'border:1px solid #ccc;text-align:right;padding:2px 5px;font-weight:bold;font-size:12px;background:#fff;';

	$result.='<div class="col-12" style="padding:5px;">';
	$result.='<div style="background:#2d6a4f;color:#fff;border-radius:4px 4px 0 0;text-align:center;padding:5px;">'
	        .'<h5 class="mb-0" style="font-size:13px;font-weight:bold;letter-spacing:1px;">SUMMARY</h5></div>';
	$result.='<div style="border:1px solid #ccc;border-top:0;border-radius:0 0 4px 4px;padding:6px;">';

	$grand_gross_s = 0; $grand_disc_s = 0;
	foreach ($overall_type_value as $jt => $tv) {
		if ($tv <= 0 && $overall_type_disc[$jt] <= 0) continue;
		$td_s = $overall_type_disc[$jt];
		$tn_s = $tv - $td_s;
		$cnt  = $overall_type_count[$jt];
		$lbl  = $type_labels_s[$jt];
		$grand_gross_s += $tv;
		$grand_disc_s  += $td_s;

		$result.='<table width="100%" style="font-size:12px;margin-bottom:2px;border-collapse:collapse;">';
		$result.='<tr>'
		        .'<td style="font-weight:bold;padding:2px 0;">'.$lbl.'('.$cnt.')</td>'
		        .'<td style="width:45%;"><div style="'.$BOX.'">'.ind_money($tv,2).'</div></td>'
		        .'</tr>';
		$result.='<tr>'
		        .'<td style="padding:1px 0 1px 10px;color:#c0392b;font-size:11px;">&#x2514; Disc</td>'
		        .'<td><div style="'.$BOX.'color:#c0392b;">-'.ind_money($td_s,2).'</div></td>'
		        .'</tr>';
		$result.='<tr>'
		        .'<td style="padding:1px 0 3px 10px;color:#198754;font-size:11px;">&#x2514; Net</td>'
		        .'<td><div style="'.$BOX.'color:#198754;">'.ind_money($tn_s,2).'</div></td>'
		        .'</tr>';
		$result.='</table>';
		$result.='<hr style="margin:3px 0;border-color:#ddd;">';
	}

	// Gross = sum of SQ approximate_amount, Discount = total discounts
	// Net Total = Cash + SB + Credit (= Total BV) so it always matches TOTAL COLLECTION
	$summary_gross_display = $sq_approx_total;
	$summary_disc_display  = $Sales_DistAmt;
	$Sales_TotAmt          = $Sales_Cash + $Sales_CA + $Sales_SB + $Sales_Credit;
	$summary_net_display   = $Sales_TotAmt;

	$result.='<table width="100%" style="font-size:12px;margin-top:4px;border-collapse:collapse;">';
	$result.='<tr>'
	        .'<td style="font-weight:bold;padding:2px 0;">Gross :</td>'
	        .'<td style="width:45%;"><div style="'.$BOX.'">'.ind_money($summary_gross_display,2).'</div></td>'
	        .'</tr>';
	$result.='<tr>'
	        .'<td style="font-weight:bold;color:#c0392b;padding:2px 0;">Discount :</td>'
	        .'<td><div style="'.$BOX.'color:#c0392b;background:#fff2f2;">-'.ind_money($summary_disc_display,2).'</div></td>'
	        .'</tr>';
$result.='<tr>'
	        .'<td style="font-weight:bold;color:#198754;padding:2px 0;">Net Total :</td>'
	        .'<td><div style="border:2px solid #198754;text-align:right;padding:2px 5px;font-weight:bold;font-size:12px;background:#e8f5e9;color:#198754;">'.ind_money($summary_net_display,2).'</div></td>'
	        .'</tr>';
	$result.='</table>';
	$result.='</div>';
	$result.='</div>';
	// ─────────────────────────────────────────────────────────────────────────

	$result.='<div class="col-12" style="border:0px solid #ccc; padding:5px;"  >';
	$result.='<div class="text-center bg-info text-white"><h5 class="mb-1">SALES</h5></div>';
	$result.='<div id="card_2">';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">Cash : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sales_Cash,2).'</div></td></tr> ';
	$result.='<tr><td align="right">CA A/c :  </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sales_CA,2).'</div></td></tr> ';
	$result.='<tr><td align="right">SB A/c : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sales_SB,2).'</div></td></tr>';
	$result.='<tr><td align="right">Credit : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sales_Credit,2).'</div></td></tr>';
	$result.='<tr><td align="right">Dist Amt : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sales_DistAmt,2).'</div></td></tr>';
	$Sales_TotAmt=$Sales_Cash+$Sales_CA+$Sales_SB+$Sales_Credit;
	$result.='<tr><td align="right">Total Amt : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;color:green;font-weight:bold;">'.ind_money($Sales_TotAmt,2).'</div></td></tr>';
	$result.='</table>';
	$result.='</div>';
	$result.='</div>';
	$result.='<div class="col-12"  style="border:0px solid #ccc; padding:5px;" >';
	$result.='<div class="text-center bg-info text-white" ><h5 class="mb-1">TOTAL COLLECTION</h5></div>';
	$result.='<div  id="card_3">';
	$result.='<table width="100%" >';
	$Tot_Coll_Cash=$JC_Cash+$Sales_Cash;
	$result.='<tr><td align="right" width="50%">Cash Total: </td><td><div style="border:1px solid black;background-color:white;text-align:right;padding:2px;color:green;font-weight:bold;">'.ind_money($Tot_Coll_Cash,2).'</div></td></tr>';
	$Tot_Coll_CA=$JC_Bank_CA+$Sales_CA;
	$result.='<tr><td align="right">CA Total : </td><td><div style="border:1px solid black;background-color:white;text-align:right;padding:2px;color:green;font-weight:bold;">'.ind_money($Tot_Coll_CA,2).'</div></td></tr>';
	$Tot_Coll_SB=$JC_Bank_SB+$Sales_SB;
	$result.='<tr><td align="right">SB Total : </td><td><div style="border:1px solid black;background-color:white;text-align:right;padding:2px;color:green;font-weight:bold;">'.ind_money($Tot_Coll_SB,2).'</div></td></tr>';
	$result.='<tr><td align="right">Advance : </td><td><div style="border:1px solid black;background-color:white;text-align:right;padding:2px;color:#2d6a4f;font-weight:bold;">'.ind_money($sq_adv_total,2).'</div></td></tr>';
	$Tot_BV=$Tot_Coll_Cash+$Tot_Coll_CA+$Tot_Coll_SB+$Sales_Credit+$sq_adv_total;
$result.='<tr><td align="right">Total BV : </td><td><div style="border:2px solid green;background-color:#e8f5e9;text-align:right;padding:2px;color:green;font-weight:bold;">'.ind_money($Tot_BV,2).'</div></td></tr>';
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