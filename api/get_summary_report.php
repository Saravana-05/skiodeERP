<?php
session_start();
include_once '../connect_db.php';
$result=""; 
if(isset($_POST['from']))
{
	$from = date("Y-m-d", strtotime($_POST['from']));
	$to = date("Y-m-d", strtotime($_POST['to']));
	$sql="SELECT * FROM sales_invoice_master where invoice_dt_tm between '".$from." 00:00:00' and '".$to." 23:59:59'";
	$result.='<div class="row"><div class="col-10 mx-auto" id="printable_div">';
	$total_cash=0.0;
	$total_credit=0.0;
	$total_online=0.0;
	$total_discount=0.0;
	$total_tax=0.0;	

	$Cr_Cash=0;$Cr_SB=0;$Cr_CA=0;$Cr_TotAmt=0;
	$JC_Cash=0;$JC_Gpay=0;$JC_TotAmt=0;$JC_SB=0;$JC_CA=0;
	$Tot_Coll_Cash=0;$Tot_Coll_CA=0;$Tot_Coll_SB=0;
	$GST_Sales_Cash=0;$GST_Sales_CA=0;$GST_Sales_SB=0;$GST_Sales_TotAmt=0;
	$Sales_Cash=0;$Sales_CA=0;$Sales_SB=0;$Sales_TotAmt=0;
	$Sales_Credit=0;$Sales_Void=0;$Sales_DiscAmt=0 ;
	$GST_Total_Tax=0;$GST_Total_Value=0;$GST_Inv_Count=0;
	
	
	$JC_Nos='';
	$Quotation_Nos='';
	$Invoice_Nos='';
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{ 
			 while($row=mysqli_fetch_array($query)) {
					$GST_Inv_Count+=1;
					$GST_Total_Value+=$row["net_total"];
					$GST_Total_Tax+=$row["total_tax"];
			  }
			
		}		 
	} 
	
	
	// ── JOB CARD ADV AMT ────────────────────────────────────────────────────────
	// Rule: JC Adv = money received TODAY as advance on a Job Card.
	//   Open JCs:   always count advance (no SQ yet, no risk of double-count).
	//   Closed JCs: count advance ONLY when it is a real partial advance —
	//               i.e. the linked SQ collected LESS than the full net payable.
	//               When walk-in pays full amount as advance and SQ balance_cash
	//               equals that full amount, advance_txt = 0, so we skip it here
	//               and it is already captured in the Sales Quote balance section.
	$summary_sql="Select * from jobcard_master where created_dt_tm BETWEEN '".$from." 00:00:00' and '".$to." 23:59:59'";
	if($summary_query=mysqli_query($connection,$summary_sql))
	{
		if(mysqli_num_rows($summary_query)>0)
		{
			while($summary_row=mysqli_fetch_array($summary_query))
			{
				if(!$summary_row["void_job_card"]){
					$adv_cash = floatval($summary_row["advance_cash"]);
					$adv_sb   = floatval($summary_row["advance_gpay"]);
					if(!$summary_row["job_card_closed"]){
						// Open JC: advance not yet absorbed into any SQ balance
						$JC_Cash += $adv_cash;
						$JC_SB   += $adv_sb;
					} else if ($adv_cash > 0 || $adv_sb > 0) {
						// Closed JC: check linked SQ to see if it is a real partial advance
						$jc_no_s = intval($summary_row["jobcard_no"]);
						$sq_link = mysqli_query($connection,
							"SELECT approximate_amount, discount, balance_cash, balance_gpay
							 FROM sales_quotation_master
							 WHERE FIND_IN_SET($jc_no_s, REPLACE(jobcard_nos,' ','')) > 0
							 LIMIT 1");
						if ($sq_link && $sq_lr = mysqli_fetch_assoc($sq_link)) {
							$net_p   = floatval($sq_lr['approximate_amount']) - floatval($sq_lr['discount']);
							$bal_col = floatval($sq_lr['balance_cash']) + floatval($sq_lr['balance_gpay']);
							$cr_amt  = ($summary_row['customer_type'] == 'Credit') ? $net_p : 0;
							$adv_txt = max(0, $net_p - $bal_col - $cr_amt);
							if ($adv_txt > 0) {
								// Real advance: add to JC Adv section
								$JC_Cash += $adv_cash;
								$JC_SB   += $adv_sb;
							}
							// If adv_txt = 0: full payment stored as balance_cash in SQ,
							// already counted in Sales Quote section below — skip here.
						}
					}
				}
				if($summary_row["void_job_card"]==1)
					$Sales_Void += $summary_row["approximate_amount"];
				else
					$Sales_TotAmt += $summary_row["approximate_amount"];
			}
			$JC_TotAmt = $JC_Cash + $JC_SB;
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
	$summary_sql="Select IFNULL( sum(amount),0) as Cr_CA from receipt_voucher where voucher_date  between '".$from." 00:00:00' and '".$to." 23:59:59' and account_head='CA' ";
	if($summary_query=mysqli_query($connection,$summary_sql))
	{
		if(mysqli_num_rows($summary_query)>0)
		{ 
			if($summary_row=mysqli_fetch_array($summary_query)) 
			{
				$Cr_CA=$summary_row["Cr_CA"]; 
			}
		}
	}
	$summary_sql="Select IFNULL( sum(amount),0) as Cr_SB from receipt_voucher where voucher_date  between '".$from." 00:00:00' and '".$to." 23:59:59' and account_head='SB' ";
	if($summary_query=mysqli_query($connection,$summary_sql))
	{
		if(mysqli_num_rows($summary_query)>0)
		{ 
			if($summary_row=mysqli_fetch_array($summary_query)) 
			{
				$Cr_SB=$summary_row["Cr_SB"]; 
			}
		}
	}
	// ── Sales Quote: balance payments collected on SQ date ──────────────────────
	// Rule: Sales Quote section = money received on delivery (balance_cash / balance_gpay).
	// Advance already counted in JC Adv section above — do NOT add it here again.
	$Sales_Cash = 0; $Sales_SB = 0; $Sales_CA = 0; $Sales_DiscAmt = 0; $Sales_Credit = 0;
	$sq_sql = "SELECT * FROM sales_quotation_master
	           WHERE quotation_dt_tm BETWEEN '".$from." 00:00:00' AND '".$to." 23:59:59'
	           ORDER BY quotation_dt_tm ASC";
	if($sq_qry = mysqli_query($connection, $sq_sql)) {
		while($row = mysqli_fetch_assoc($sq_qry)) {
			$bal_cash = floatval($row['balance_cash']);
			$bal_gpay = floatval($row['balance_gpay']);
			$disc     = floatval($row['discount']);
			$approx   = floatval($row['approximate_amount']);

			// Balance collected at delivery
			if ($bal_cash > 0) $Sales_Cash += $bal_cash;
			if ($bal_gpay > 0) $Sales_SB   += $bal_gpay;
			$Sales_DiscAmt += $disc;

			// Customer type for credit calculation
			$customer_type = '';
			$safe_jcnos = preg_replace('/[^0-9,]/', '', $row['jobcard_nos']);
			if ($safe_jcnos) {
				$jc_info_qry = mysqli_query($connection,
					"SELECT customer_type FROM jobcard_master WHERE jobcard_no IN ($safe_jcnos) LIMIT 1");
				if ($jc_info_qry && $jc_info_row = mysqli_fetch_assoc($jc_info_qry))
					$customer_type = $jc_info_row['customer_type'];
			}
			if ($customer_type == 'Credit') $Sales_Credit += $approx - $disc;
		}
	}
/* 	$summary_sql="Select sum(balance_gpay) as Sales_CA  from sales_quotation_master where quotation_dt_tm BETWEEN '".$from." 00:00:00' and '".$to." 23:59:59' and balance_gpay>1999;";  
	file_put_contents("abcd.txt",$summary_sql);
	if($summary_query=mysqli_query($connection,$summary_sql))
	{
		if(mysqli_num_rows($summary_query)>0)
		{ 
			if($summary_row=mysqli_fetch_array($summary_query)) 
			{
				$Sales_CA=$summary_row["Sales_CA"]; 
			}
		}
	} */
	
	$Cr_TotAmt=$Cr_Cash+$Cr_SB+$Cr_CA;
	$Tot_Coll_Cash=$JC_Cash + $Sales_Cash + $GST_Sales_Cash;
	$Tot_Coll_CA=$Sales_CA + $GST_Sales_CA + $JC_CA;
	$Tot_Coll_SB=$Sales_SB + $GST_Sales_SB + $JC_SB;
 
	
	
	$result.='<div class="row">';
	$result.='<div class="col-6" style="border:1px solid #ccc; padding:5px;">';
	$result.='<div class="text-center bg-success text-white"><h5 class="mb-0">RECEIPTS</h5></div>';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">Cr Cash : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Cr_Cash,2).'</div></td></tr> ';
	$result.='<tr><td align="right">Cr SB :  </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Cr_SB,2).'</div></td></tr> ';
	$result.='<tr><td align="right">Cr CA : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Cr_CA,2).'</div></td></tr>';
	$result.='<tr><td align="right">Total Amt : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Cr_TotAmt,2).'</div></td></tr>';
	$result.='</table>';
	$result.='</div>';
	$result.='<div class="col-6"  style="border:1px solid #ccc; padding:5px;">';
	$result.='<div class="text-center bg-success text-white"><h5 class="mb-0">JOB CARD ADV. AMT</h5></div>';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">Cash : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($JC_Cash,2).'</div></td></tr>';
	$result.='<tr><td align="right">SB A/c : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($JC_SB,2).'</div></td></tr>';
	$result.='<tr><td align="right">CA A/c : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($JC_CA,2).'</div></td></tr>';
	$result.='<tr><td align="right">Total Amt : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($JC_TotAmt,2).'</div></td></tr>';
	$result.='</table>';
	$result.='</div>';
	$result.='</div> ';
	$result.='<div class="row">';
	$result.='<div class="col-6" style="border:1px solid #ccc; padding:5px;">';
	$result.='<div class="text-center bg-success text-white"><h5 class="mb-0">Sales Quote</h5></div>';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">Cash : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sales_Cash,2).'</div></td></tr> ';
	$result.='<tr><td align="right">CA A/c :  </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sales_CA,2).'</div></td></tr> ';
	$result.='<tr><td align="right">SB A/c : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sales_SB,2).'</div></td></tr>';
	$result.='<tr><td align="right">Credit : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sales_Credit,2).'</div></td></tr>';
	//$result.='<tr><td align="right">Void : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.$Sales_Void.'</div></td></tr>';
	$result.='<tr><td align="right">Discount : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sales_DiscAmt,2).'</div></td></tr>';
	$Sales_TotAmt=$Sales_Cash+$Sales_CA+$Sales_SB+$Sales_Credit;
	$result.='<tr><td align="right">Total Amt : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sales_TotAmt,2).'</div></td></tr>';
	//$result.='<tr><td colspan="2" align="right">JC_Cash+JC_SB+JC_CA+<br>SQ_Cash+SQ_SB+SQ_CA+SQ_Credit<br>-Void-SQ_Disc=Total Amt</td></tr>';
	$result.='</table>';
	$result.='</div>';
	$result.='<div class="col-6"  style="border:1px solid #ccc; padding:5px;">';
	$result.='<div class="text-center bg-success text-white"><h5 class="mb-0">TOTAL COLLECTION</h5></div>';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">Cash Total: </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Tot_Coll_Cash,2).'</div></td></tr>';   
	$result.='<tr><td align="right">CA Total : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Tot_Coll_CA,2).'</div></td></tr>';
	$result.='<tr><td align="right">SB Total : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Tot_Coll_SB,2).'</div></td></tr>';
	$result.='<tr><td align="right">Credit : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($Sales_Credit,2).'</div></td></tr>';
	$result.='<tr><td align="right">Grand Total : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;"><B>'.ind_money($Tot_Coll_Cash+$Tot_Coll_CA+$Tot_Coll_SB+$Sales_Credit,2).'</B></div></td></tr>';
	$result.='</table>';
	$result.='</div>';
	$result.='</div> ';

	$result.='<div class="row">';
	$result.='<div class="col-6" style="border:1px solid #ccc; padding:5px;">';
	$result.='<div class="text-center bg-success text-white"><h5 class="mb-0">GST Sales Invoice</h5></div>';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">Invoice Count : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($GST_Inv_Count,0,false).'</div></td></tr>'; 
	$result.='<tr><td align="right">Total Value :  </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($GST_Total_Value,2).'</div></td></tr> ';
	$result.='<tr><td align="right">Total GST : </td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($GST_Total_Tax,2).'</div></td></tr>';
	
	$result.='</table>';
	$result.='</div>';
	$result.='<div class="col-6"  style="border:1px solid #ccc; padding:5px;">';

	$result.='<br><br><br><br><center>';
	$result.='<br> <center><button type="button" class="btn btn-primary btn-sm" onclick="printSummaryDiv('."'printable_div'".')">PRINT</button><br><br>';
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