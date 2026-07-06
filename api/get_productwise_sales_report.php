<?php
session_start();
include_once '../connect_db.php';
$result=""; 
if(isset($_POST['from']))
{
	$from = date("Y-m-d", strtotime($_POST['from']));
	$to = date("Y-m-d", strtotime($_POST['to']));
	$user_name_txt = $_POST["user_name_txt"];
	$job_type_txt  = $_POST["job_type_txt"];	
	$show_summary_chk = intval($_POST['show_summary_chk']);

	if($show_summary_chk)
	{
		// SUMMARY MODE — 7 columns (no Username)
		$sql="SELECT c.product_code, c.product_name, b.job_type,
		             sum(b.total_qty) as total_qty, 
		             sum(b.value_amount) as selling_price,
		             sum(COALESCE(b.item_discount,0)) as total_item_discount,
		             sum(b.value_amount - COALESCE(b.item_discount,0)) as net_amount
		      FROM jobcard_master a 
		      JOIN jobcard_details b ON a.jobcard_id=b.jobcard_id 
		      JOIN product_master c ON b.product_code=c.product_code 
		      WHERE a.jobcard_date BETWEEN '".$from."' AND '".$to."' 
		        AND a.void_job_card=0";
		if($user_name_txt!="all")
			$sql.=" AND a.created_by='".mysqli_real_escape_string($connection,$user_name_txt)."'";
		if($job_type_txt!="all")
			$sql.=" AND b.job_type='".mysqli_real_escape_string($connection,$job_type_txt)."'";
		$sql.=" GROUP BY c.product_code, c.product_name, b.job_type;";
	}
	else
	{
		// DETAIL MODE — 8 columns (with Username)
		$sql="SELECT a.jobcard_no, c.product_name, b.job_type, 
		             b.total_qty, 
		             b.value_amount as selling_price,
		             COALESCE(b.item_discount,0) as item_discount,
		             (b.value_amount - COALESCE(b.item_discount,0)) as net_amount,
		             a.created_by
		      FROM jobcard_master a 
		      JOIN jobcard_details b ON a.jobcard_id=b.jobcard_id 
		      JOIN product_master c ON b.product_code=c.product_code 
		      WHERE a.jobcard_date BETWEEN '".$from."' AND '".$to."' 
		        AND a.void_job_card=0";
		if($user_name_txt!="all")
			$sql.=" AND a.created_by='".mysqli_real_escape_string($connection,$user_name_txt)."'";
		if($job_type_txt!="all")
			$sql.=" AND b.job_type='".mysqli_real_escape_string($connection,$job_type_txt)."'";
	}

	$grand_gross    = 0;
	$grand_discount = 0;
	$grand_net      = 0;
	$rows_html      = '';
	$i = 1;

	if($query = mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{ 
			while($row = mysqli_fetch_assoc($query))
			{
				$gross    = floatval($row["selling_price"]);
				$discount = floatval($show_summary_chk ? $row["total_item_discount"] : $row["item_discount"]);
				$net      = floatval($row["net_amount"]);
				$has_disc = $discount > 0;

				$grand_gross    += $gross;
				$grand_discount += $discount;
				$grand_net      += $net;

				$rows_html .= '<tr>';
				$rows_html .= '<td>'.$i.'</td>'; 
				$rows_html .= '<td>'.htmlspecialchars($row["product_name"]).'</td>'; 
				$rows_html .= '<td>'.htmlspecialchars($row["job_type"]).'</td>';
				$rows_html .= '<td style="text-align:right;">'.$row["total_qty"].'</td>';
				$rows_html .= '<td style="text-align:right;">'.ind_money($gross,2).'</td>';
				$rows_html .= '<td style="text-align:right;'.($has_disc?'color:red;font-weight:bold;':'').'">'.ind_money($discount,2).'</td>';
				$rows_html .= '<td style="text-align:right;font-weight:bold;">'.ind_money($net,2).'</td>';
				if(!$show_summary_chk)
					$rows_html .= '<td>'.htmlspecialchars($row["created_by"]).'</td>';
				$rows_html .= '</tr>';
				$i++;
			}
		}
	}

	$result .= '<div class="row"><div class="col-10" id="printable_div">';
	$result .= '<table class="table table-bordered table-hover table-condensed table-striped" id="resultDetailTable">';
	
	// ── THEAD ─────────────────────────────────────────────────────────────
	$result .= '<thead class="table-dark"><tr>';
	$result .= '<th>S.No</th>';
	$result .= '<th>Stock Name</th>'; 
	$result .= '<th>Stock Group</th>';
	$result .= '<th style="text-align:right;">Total Qty</th>';
	$result .= '<th style="text-align:right;">Gross (₹)</th>';
	$result .= '<th style="text-align:right;">Discount (₹)</th>';
	$result .= '<th style="text-align:right;">Net (₹)</th>';
	if(!$show_summary_chk) $result .= '<th>Username</th>';
	$result .= '</tr></thead>';
	// ─────────────────────────────────────────────────────────────────────

	// ── TFOOT — totals go here, NOT in tbody ──────────────────────────────
	// Summary=7 cols: colspan(4)+gross+disc+net = 7
	// Detail =8 cols: colspan(4)+gross+disc+net+empty = 8
	$result .= '<tfoot>';
	$result .= '<tr style="background:#fdff9f;font-weight:bold;">';
	$result .= '<td colspan="4" style="text-align:right;">TOTAL</td>';
	$result .= '<td style="text-align:right;">'.ind_money($grand_gross,2).'</td>';
	$result .= '<td style="text-align:right;color:red;">'.ind_money($grand_discount,2).'</td>';
	$result .= '<td style="text-align:right;color:green;">'.ind_money($grand_net,2).'</td>';
	if(!$show_summary_chk) $result .= '<td></td>';
	$result .= '</tr>';
	$result .= '</tfoot>';
	// ─────────────────────────────────────────────────────────────────────

	// ── TBODY — only data rows, no totals ────────────────────────────────
	$result .= '<tbody>';
	$result .= $rows_html;
	$result .= '</tbody>';
	// ─────────────────────────────────────────────────────────────────────

	$result .= '</table>';
	$result .= '</div>';

	// ── Right summary panel ───────────────────────────────────────────────
	$result .= '<div class="col-2 text-bg-light" style="border-left:1px inset #ccc;">';
	$result .= '<div class="text-center bg-success text-white"><h5 class="mb-0">SUMMARY</h5></div>';
	$result .= '<table width="100%" style="font-weight:bold;">';
	
	$summary_sql = "SELECT b.job_type,
	                       sum(b.value_amount) as amt,
	                       sum(COALESCE(b.item_discount,0)) as disc,
	                       sum(b.value_amount - COALESCE(b.item_discount,0)) as net_amt,
	                       count(b.value_amount) as cnt 
	                FROM jobcard_master a 
	                JOIN jobcard_details b ON a.jobcard_id=b.jobcard_id  
	                WHERE a.jobcard_date BETWEEN '".$from."' AND '".$to."' 
	                  AND a.void_job_card=0";
	if($user_name_txt!="all")
		$summary_sql .= " AND a.created_by='".mysqli_real_escape_string($connection,$user_name_txt)."'";
	if($job_type_txt!="all")
		$summary_sql .= " AND b.job_type='".mysqli_real_escape_string($connection,$job_type_txt)."'";
	$summary_sql .= " GROUP BY b.job_type";
	
	$total_gross = 0;
	$total_disc  = 0;
	$total_net   = 0;

	if($summary_query = mysqli_query($connection,$summary_sql))
	{
		if(mysqli_num_rows($summary_query)>0)
		{ 
			while($summary_row = mysqli_fetch_assoc($summary_query)) 
			{
				$amt  = floatval($summary_row['amt']);
				$disc = floatval($summary_row['disc']);
				$net  = floatval($summary_row['net_amt']);

				$result .= '<tr>
				              <td align="right" width="45%">'.htmlspecialchars($summary_row['job_type']).'</td>
				              <td>('.$summary_row['cnt'].')</td>
				              <td><div style="border:1px solid black;text-align:right;padding:2px;">'.ind_money($amt,2).'</div></td>
				            </tr>';

				if($disc > 0)
				{
					$result .= '<tr>
					              <td align="right" style="font-size:0.75rem;color:red;">└ Disc</td>
					              <td></td>
					              <td><div style="border:1px solid red;color:red;text-align:right;padding:2px;font-size:0.75rem;">-'.ind_money($disc,2).'</div></td>
					            </tr>
					            <tr>
					              <td align="right" style="font-size:0.75rem;color:green;">└ Net</td>
					              <td></td>
					              <td><div style="border:1px solid green;color:green;text-align:right;padding:2px;font-size:0.75rem;">'.ind_money($net,2).'</div></td>
					            </tr>';
				}

				$total_gross += $amt;
				$total_disc  += $disc;
				$total_net   += $net;
			}
		}

		$result .= '<tr><td colspan="3"><hr style="margin:3px 0;"></td></tr>';
		$result .= '<tr>
		              <td align="right">Gross :</td><td>&nbsp;</td>
		              <td><div style="border:1px solid black;text-align:right;padding:2px;">'.ind_money($total_gross,2).'</div></td>
		            </tr>';
		if($total_disc > 0)
		{
			$result .= '<tr>
			              <td align="right" style="color:red;">Discount :</td><td>&nbsp;</td>
			              <td><div style="border:1px solid red;color:red;background:#fff5f5;text-align:right;padding:2px;">-'.ind_money($total_disc,2).'</div></td>
			            </tr>';
		}
		$result .= '<tr>
		              <td align="right" style="color:green;"><strong>Net Total :</strong></td><td>&nbsp;</td>
		              <td><div style="border:2px solid green;color:green;font-weight:bold;text-align:right;padding:2px;">'.ind_money($total_net,2).'</div></td>
		            </tr>';
	}

	$result .= '</table>';
	$result .= '<br><br><center>';
	$result .= '<button type="button" class="btn btn-primary btn-sm" onclick="printProductwiseDiv(\'printable_div\')">PRINT</button>';
	$result .= '<br><br>';
	$result .= '<button type="button" class="btn btn-secondary btn-sm" onclick="fnSideMenu(1)">Close</button>';
	$result .= '</center>';
	$result .= '</div>';
	$result .= '</div>';
}
echo $result;
?>