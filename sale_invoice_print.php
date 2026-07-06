<?php
session_start(); 
include_once 'connect_db.php';
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
<link href="bs/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
		<script src="bs/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
		<script src="js/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<style>
@page {
    /* dimensions for the whole page */
    size: A4;
    
    margin: 0;
}

html {
    /* off-white, so body edge is visible in browser */
    background: #fff;
}

body {
    /* A5 dimensions */
    width: 210mm;
    //height: 297mm;
    margin: 5px;
	
}
.printDiv
{
	margin:10px;
	padding:0px; 
	border : 2px solid #000;
	border-radius:4px;
}
 
 
@media print {
  .noPrint{
	display:none;
  }

    body {width:auto;height:auto;transform: scale(1);}
    table {page-break-inside: avoid;}
  @page {
    size: A4;
  }
}
#printHeader
{ 
	margin:0px;
	padding:0px;
	padding-bottom:0px;
}
#printBody
{
	margin: 15px;
    border: 1px solid #000;
    border-radius: 25px;
	margin-top:2px;
	margin-bottom:10px;
}
#printFooter
{
	margin-bottom: 10px;
}
p
{
	margin:2px;
}
.logo_width
{
	width:55%;
}
#quotationDiv
{
	position: absolute;
    /* margin-top: -10px; */
    border: 1px solid #000;
    border-radius: 20px;
    padding: 2px 12px;
    background: white;
    width: 100px;
    top: 166px;
    left: 353px;
    text-align: center;
}
.customerDetailsDiv
{
	font-weight:bold;
}

#quotationDetailsDiv .table 
{
	border-bottom:1px solid #000;
	width:100%;
	margin-left:auto;
	margin-right:auto;
	height:450px;
	border-left:0px;
	border-right:0px;
}
#quotationDetailsDiv th,#quotationDetailsDiv td,#quotationDetailsDiv tfoot td
{
	padding:3px;
	
	border-bottom:0px;
	border-right:2px solid #000;
	
}
 #quotationDetailsDiv tfoot td
{
	border-top:0px solid #000; 
	border-bottom:2px solid #000; 
	
}
.customerDetailsDiv .table 
{
	border-bottom:0px solid #000;
	width:97%;
	margin:0px;
	margin-left:auto;
	margin-right:auto; 
	border-left:0px;
	border-right:0px;
}
.customerDetailsDiv td{
	padding:3px;
	background:transparent;
	border-bottom:1px solid #000;
	border-right:1px solid #000;
	font-weight:bold;
}
.customerDetailsDiv tr > td:last-of-type  {
   border-right:0px solid #000;
}
#quotationDetailsDiv tr > th:last-of-type,#quotationDetailsDiv tr > td:last-of-type  {
   border-right:0px solid #000;
}
#quotationDetailsDiv th
{
	text-align:center;
	border-bottom:1px solid #000;
}
#quotationDetailsDiv td
{
	text-align:left;
	font-weight:500;
}
tbody
{
	border-bottom:0px solid #000;
}

#qrcode {
    margin: 2px auto;
	width:100px;
}
.h3_title
{
	text-align:center;
	padding-bottom:0px;
	margin-bottom:1px;
}
.header_content
{
	
    font-weight: 600;
}
.header_content1
{
	
    font-weight: bold;
}
.footerTable
{
	border:0px solid #000;
	width:100%;
	margin:0px;
	margin-left:auto;
	margin-right:auto; 
	border-left:0px;
	border-right:0px;
}
.footerTable tr
{
	    height: 20px;
}
.gstTable td
{
	border:1px dotted #666;
}
.no_border_bottom
{
	border-bottom:0px !important;
}
.header {
      position: relative;
      background: linear-gradient(to right, #bcbdbd, #fbfbfb);
      text-align: center;
      padding: 20px 20px 20px;
      overflow: hidden;
    }

    .header h1 {
      font-size: 38px;
      color: #6a1b9a;
      margin: 0;
      font-weight: bold;
      letter-spacing: 2px;
	  text-align:left;
    }

    .header h2 {
      font-size: 16px;
      color: #6a1b9a;
      margin-top: 10px;
      font-weight: bold;
      display: inline-block;
      position: relative;
	  float:left;
	  margin-left:100px;
	      z-index: 2;
    }

    .header h2::before,
    .header h2::after {
      content: "";
      position: absolute;
      top: 50%;
      width: 60px;
      height: 2px;
      background-color: red;
    }

    .header h2::before {
      left: -70px;
    }

    .header h2::after {
      right: -70px;
    }

    .wave {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      line-height: 0;
    }

    .wave svg {
      display: block;
      width: 100%;
      height: 130px;
    }

    .wave path {
      fill: #fff;
    }
	.invoice_header
	{
		border:1px solid #000;
		border-radius:15px;
		padding:5px;
		text-align:center;
		font-weight:bold;
		margin:5px;
	}
	.invoice_header1
	{
		border:1px solid #000;
		border-radius:15px;
		padding:5px;
		text-align:left;
		font-weight:bold;
		margin: 5px;
		font-family:sans-serif;
		padding-left: 25px;
	}
	.middleDiv
	{
		width: 300px;
		border: 1px solid #000;
		border-radius: 30px;
		position: absolute;
		left: 14%;
		padding: 6px;
		font-weight: 800;
		text-align: center;
		z-index: 6;
		top: 228px;
		background: #fff;
		letter-spacing: 1px;
		font-size: 1.0rem;
		font-family: sans-serif;	
	}
	.account_detailsDiv
	{
		    padding: 10px;
		padding-left: 1rem;
		border: 1px solid #000;
		border-radius: 0px 55px 0px 40px;
		margin-left: 2rem;
		font-family: system-ui;
    font-size: 1rem;
    font-weight: 500;
	}
</style>
<body>
<?php
$printType="";
$job_card_no="";
$si_no="";
$sq_no="";
$net_total=0;
	$customer_Type = ""; 
	$customer_details = ""; 
	$jobcard_date = ""; 
	$invoice_dt = ""; 
	$advance_gpay = 0; 
	$advance_cash = 0; 
	$balance_gpay = 0; 
	$balance_cash = 0; 
	$discount = 0; 
	$balance_amount = 0; 
	$sub_total = 0; 
	$advance_details = ""; 
	$balance_details = ""; 
	$hidden_details = "";
	$total_tax=0;
	$round_off=0;
	$final_amount=0;
if(isset($_GET['si']))
{
	$printType="Sale Invoice";
	$si_no=$_GET['si'];
	$sql="SELECT * FROM sales_invoice_master where  invoice_no =".$si_no;
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{ 
			 if($row=mysqli_fetch_array($query)) 
			 {
				 $sq_no=$row['sale_quotation_nos'];
				 $invoice_dt = date("d-m-Y",strtotime($row["invoice_dt_tm"]));
				 $sub_total=$row["sub_total"];
				 $total_tax=$row["total_tax"];
				 $round_off=$row["round_off"];
				 $net_total=$row["net_total"];
				 $inclusive_gst=$row["inclusive_gst"];
			 }
		}
	}
	$sql="SELECT * FROM sales_quotation_master where  quotation_no in (".$sq_no.")"; 
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{ 
			 while($row=mysqli_fetch_array($query)) 
			 {
				 $job_card_no=$job_card_no.$row['jobcard_nos'].',';
			 }
		}
	}
}
$job_card_no = trim($job_card_no, ',');

// ── Fetch Canara Bank QR string for this job card ──────────────────────────
$canara_qr_string = '';
if ($job_card_no) {
    $safe_jc_qr = mysqli_real_escape_string($connection, $job_card_no);
    $tbl_chk = mysqli_query($connection, "SHOW TABLES LIKE 'canara_transactions'");
    if ($tbl_chk && mysqli_num_rows($tbl_chk) > 0) {
        $qr_res = mysqli_query($connection,
            "SELECT qr_string FROM canara_transactions
              WHERE jobcard_no IN ($safe_jc_qr)
                AND qr_string IS NOT NULL AND qr_string != ''
              ORDER BY created_at DESC LIMIT 1");
        if ($qr_res && ($qr_row = mysqli_fetch_assoc($qr_res))) {
            $canara_qr_string = $qr_row['qr_string'];
        }
    }
}

$sql="SELECT count(jobcard_no) as count_jobcard_no FROM jobcard_details where jobcard_no in (".$job_card_no.")";
	$cnt_jc_no=0;
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{
			if($row=mysqli_fetch_array($query))
			{
				if(!is_null($row["count_jobcard_no"]))
				{
					$cnt_jc_no=$row["count_jobcard_no"];
				}
			}
		}
	}
	$loopCnt=1;
	 
	if($cnt_jc_no >20)
		$loopCnt= floor($cnt_jc_no/20)+1;
	
	$sql="SELECT * FROM jobcard_master where jobcard_no in (".$job_card_no.")";
    
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{
			while($row=mysqli_fetch_array($query))
			{
				$customer_Type = $row["customer_type"];
				$customer_details = $row["customer_name"]. "<br />";
				$customer_details .= $row["customer_addr1"]." ".$row["customer_addr2"]. "<br />";
				$customer_details .= $row["customer_city"]." ".$row["customer_state"]. "<br />";
				$customer_details .= "GST No : ".$row["customer_gst_no"]. "<br />";
				$customer_details .= $row["customer_mobile_no"]; 
				$jobcard_date = $row["jobcard_date"];

				
			}

		}
	}
	$sql="select * from sales_quotation_master where quotation_no in (".$sq_no.")";
	if($query=mysqli_query($connection,$sql))
	{
		while($row=mysqli_fetch_array($query))
		{
			$discount += $row['discount'];
		}
	}
	$sql="SELECT sum(value_amount) as total_amount FROM jobcard_details where jobcard_no in (".$job_card_no.")"; 
	$total_amount=0;
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{
			if($row=mysqli_fetch_array($query))
			{
				$total_amount = $row["total_amount"]; 
			}
		}
	}
	 
	for($i=0;$i<$loopCnt;$i++)
	{
		$rowVal = 20; 
		if($i>0)
			$rowVal = (20*$i) + 1;;
		$sql="SELECT * FROM jobcard_details where jobcard_no in (".$job_card_no.") LIMIT ".$rowVal."		OFFSET ".(20*$i);
		
		if($query=mysqli_query($connection,$sql))
		{
			if(mysqli_num_rows($query)>0)
			{
				$sNo=1;
				
			?>

<div class="printDiv" style="line-height:normal;font-family:Arial;font-size:14px;">	 
	<div class="row" id="printHeader">
		<div class="col-12 p-0">
			 <div class="header">
				<h1>PRINTZY</h1>
				<h2>DIGITAL PRESS</h2>
				<img src="img/printzy_logo.png"  style="position: absolute;top:5px;right:0;"/>
				<div class="wave">
				  <svg viewBox="0 0 1000 100" preserveAspectRatio="none">
					<path d="M0,80 C360,0 1080,160 1440,80 L1440,100 L0,100 Z"></path>
				  </svg>
				</div>
			  </div>
		</div>
		 
		<div class="col-12" style="margin-top: -20px;z-index: 1;">
			<div class="invoice_header"> 
					<div class="m-2" style="font-family: sans-serif;font-size:0.9rem;letter-spacing: 1px;font-weight: inherit;"><i class="fa-solid fa-phone" style="color:red;"></i>  +91 90030 88363 <span class="p-2">|</span>  +91 89396 68686  <span class="p-2">|</span>  +91 70940 06001 </div>
					<div class="m-2" style="font-family: sans-serif;font-size:0.9rem;letter-spacing: 1px;font-weight: inherit;"><i class="fa-solid fa-envelope" style="color:red;"></i> citizenprints@gmail.com  <span class="p-2">|</span>  cityzenprints@gmail.com <span class="p-2">|</span>  citizenprintpro@gmail.com </div>
					<div class="m-2" style="font-family: sans-serif;font-size:0.9rem;letter-spacing: 1px;font-weight: inherit;"><i class="fa-solid fa-globe" style="color:red;"></i> www.citizenprintz.com</div>
					<div class="m-2" style="font-family: serif;font-size: 1.1rem;letter-spacing: 1px;"><i class="fa-solid fa-location-dot" style="color:red;"></i> No.4,100 feet Road, Vadapalani, Chennai - 600 026.</div>
			</div>  
			<div class="middleDiv">
				GST NO : 33AETPL8115Q1ZR
			</div>
			<div class="invoice_header1">
				<div class="row">
					<div class="col-7 p-0">
					<br> 
						Bill To : <br>
							<div style="margin-left:15px;">
							<?php echo $customer_details;?></div>
					</div>
					<div class="col-5 p-0">
					<br> 
						 <div class="row mt-2 mb-3">
							<div class="col-5">Invoice No : </div>
							<div class="col-6"><?php 
									echo ' <span id="snoTxt">SI-'.$si_no.'</span><br>';
								?></div>
						 </div>
						  <div class="row mt-2 mb-3">
							<div class="col-5">Invoice Date :</div>
							<div class="col-6"><?php 
									echo $invoice_dt;
								?></div>
						 </div>
						  <div class="row mt-2 mb-2">
							<div class="col-6">Delivery Note :</div>
							<div class="col-6"></div>
						 </div>
						 
					</div>
				</div>
			</div>
		</div>
		 
	</div>
	 
	<div id="printBody">
		<?php echo $hidden_details;?>
		<div class="row customerDetailsDiv" >
			<table class="table" style="line-height:normal;font-family:Arial;font-size:12px;">
				<tbody>
					 

					<tr>
					  <td width="5%" align="center" style="padding:10px;">Sl.No</td>
					  <td width="10%" align="center" style="padding:10px;">HSN/SAC</td>
					  <td width="55%" align="center" style="padding:10px;">Description</td>
					  <td width="15%" align="center" style="padding:10px;">Quantity</td>
					  <td width="15%" align="center" style="padding:10px;">Amount <?php echo $inclusive_gst?"<br>(Incl. GST)":"";?></td> 
					</tr>
				<?php
				while($row=mysqli_fetch_array($query))
				{
					echo '<tr>';
					echo '<td class="no_border_bottom" style="text-align:right;">'.$sNo.'</td>';
					
					$product_code = $row['product_code'];
					$prod_sql="select * from product_master where product_code='".$product_code."';";
					$product_name="";
					$hsn_code="";
					if($prod_qry=mysqli_query($connection,$prod_sql))
					{
						if($prod_row=mysqli_fetch_array($prod_qry))
						{
							$product_name= $prod_row["product_name"];
							$hsn_code= $prod_row["hsn_code"];
						}
					}
					echo '<td class="no_border_bottom" align="center">'.$hsn_code.'</td>';
					echo '<td class="no_border_bottom">'.$product_name.'</td>';
					
					echo '<td class="no_border_bottom" style="text-align:center;">'.$row['total_qty'].'</td>';
					echo '<td class="no_border_bottom" style="text-align:right;">'.number_format($row['value_amount'],2) .'</td> ';
					echo '</tr>';
					$sNo++;
				}
				$gst_row_cnt=1;
				$gst_sql="select count(invoice_no) as cnt from sales_invoice_gst_details where invoice_no=".$si_no;
				if($gst_qry=mysqli_query($connection,$gst_sql))
				{
					if($gst_row=mysqli_fetch_array($gst_qry))
					{
						$gst_row_cnt=$gst_row["cnt"];
					}					
				}
				$total_cnt = $sNo + $gst_row_cnt; 
				while(++$total_cnt<24)
				{
					echo '<tr><td class="no_border_bottom">&nbsp;</td><td class="no_border_bottom">&nbsp;</td><td class="no_border_bottom">&nbsp;</td><td class="no_border_bottom">&nbsp;</td><td class="no_border_bottom">&nbsp;</td><tr>';
				}

?>					
				</tbody>
				<tfoot>
					<tr style="border-top: 1px solid #000;">
						<td colspan="3" style="text-align:left;">
							Rupees : <?php echo convertRupeesToWords($net_total); ?> <br>
						</td>
						<td>SUB TOTAL : <br></td>
						<td style="text-align:right;">
							<?php echo number_format($total_amount, 2);?><br>
						</td>
					</tr> 
					<tr>
						<td colspan="3" style="text-align:right;padding:0px;border-bottom: 0px;">

							<table class="gstTable" width="100%" style="font-weight:normal;font-size:12px;border-bottom:0px solid #000;">
								<tbody>
									<tr>
										<td rowspan="2" valign="top" align="center">HSN/SAC</td>
										<td rowspan="2" valign="top" align="center">Taxable<br>Value</td>
										<td colspan="2" align="center">CGST</td>
										<td colspan="2" align="center">SGST</td>
										<td colspan="2" align="center">IGST</td>
										<td rowspan="2" valign="top" align="center">Total<br>Tax Amount</td> 
									</tr>
									<tr>
										 
										<td align="center">Rate</td>
										<td align="center">Amount</td>
										<td align="center">Rate</td>
										<td align="center">Amount</td>
										<td align="center">Rate</td>
										<td align="center">Amount</td>										 
									</tr>
							<?php
							$gst_sql="select * from sales_invoice_gst_details where invoice_no=".$si_no;
							$total_tax=0;
							if($gst_qry=mysqli_query($connection,$gst_sql))
							{
								while($gst_row=mysqli_fetch_array($gst_qry))
								{
									$cgst=number_format($gst_row["cgst"],2);
									$sgst=number_format($gst_row["sgst"],2);
									$igst=number_format($gst_row["igst"],2);
									$total_tax+=$gst_row["cgst"]+$gst_row["sgst"]+$gst_row["igst"];
									$gst_percentage=$gst_row["gst_percentage"];
									$cgst_percentage="";
									$sgst_percentage="";
									$igst_percentage=""; 
									if((float)$igst>0.0)
									{
									$cgst_percentage="";
									$sgst_percentage="";
									$igst_percentage=number_format($gst_percentage,2);
									if($igst_percentage==(int)$igst_percentage)
									{
										$igst_percentage=number_format($igst_percentage/2,0);
									}									
									$igst_percentage.="%";
									$cgst="";
									$sgst="";
									}
									else
									{
										
									$cgst_percentage=number_format($gst_percentage/2,2);
									$sgst_percentage=number_format($gst_percentage/2,2);
									if($cgst_percentage==(int)$cgst_percentage)
									{
										$cgst_percentage=number_format($gst_percentage/2,0);
									}
									if($sgst_percentage==(int)$sgst_percentage)
									{
										$sgst_percentage=number_format($gst_percentage/2,0);
									}
									$cgst_percentage.="%";
									$sgst_percentage.="%";
									$igst_percentage="";	
									$igst="";
									}
									?>							
									<tr>
										<td class="no_border_" style="border-left: 0px; padding-left: 10px;
											border-bottom: 0px;"><?php echo $gst_row["hsn_codes"]; ?></td>
										<td align="center"><?php echo number_format($gst_row["before_gst_value"],2); ?></td>
										<td align="center"><?php echo $cgst_percentage; ?></td>
										<td align="center"><?php echo $cgst; ?></td>
										<td align="center"><?php echo $sgst_percentage; ?></td>
										<td align="center"><?php echo $sgst; ?></td>
										<td align="center"><?php echo $igst_percentage; ?></td>
										<td align="center"><?php echo $igst; ?></td>
										<td align="center"><?php echo number_format($gst_row["cgst"]+$gst_row["sgst"]+$gst_row["igst"],2); ?></td>
									</tr>
							<?php
								}
							}
							?>
								</tbody>			
							</table>
						</td>
						<td style="border-bottom: 0px;">
							<?php if ($discount>0) { ?>DISCOUNT : <br><?php } ?>
							TOTAL TAX : <br>							
							ROUND OFF : <br>
							NET TOTAL(Rs) : <br>
						</td>
						<td style="text-align:right; border-bottom:0px;">
							<?php if ($discount>0) { echo number_format($discount, 2)."<br>"; } ?>
							<?php echo number_format($total_tax, 2);?><br>							
							<?php echo number_format($round_off, 2);?><br>
							<?php echo number_format($net_total, 2);?><br>
						</td>
					</tr> 
				</tfoot>
			</table>
			
		</div>
	</div>
	<div id="printFooter">
		<div class="row" >
			<div class="col-5 account_detailsDiv"  >
				 <p>Account Details :</p>
					 <p>Canara Bank</p>
					 <p>Arumbakkam Branch</p>
					 <p>Account No : 60441010001739</p>
					 <p>IFSC Code : CNRB0016044</p>

			</div> 
			<div class="col-3">
				 <br>
				<div class="container" style="width:auto;overflow:hidden;font-size:10px;text-align:center;font-weight:bold;">
					<div id="qrcode"></div>
					<div id="username-slab" class="name-slab"> </div>
				</div>
			</div>
			<div class="col-3">
				<center><b>For PRINTZY</b></center>
			</div>
		</div>
		<div  style="padding-left:0.4rem;display:none;">Username : <b><?php echo $_SESSION["user_type"]; ?></b></div>
	</div>
</div> 
<?php
			}
		}
	}
function convertRupeesToWords($number) {
   $no = floor($number);
   $point = round($number - $no, 2) * 100;
   $hundred = null;
   $digits_1 = strlen($no);
   $i = 0;
   $str = array();
   $words = array('0' => '', '1' => 'one', '2' => 'two',
    '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
    '7' => 'seven', '8' => 'eight', '9' => 'nine',
    '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
    '13' => 'thirteen', '14' => 'fourteen',
    '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
    '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
    '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
    '60' => 'sixty', '70' => 'seventy',
    '80' => 'eighty', '90' => 'ninety');
   $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
   while ($i < $digits_1) {
     $divider = ($i == 2) ? 10 : 100;
     $number = floor($no % $divider);
     $no = floor($no / $divider);
     $i += ($divider == 10) ? 1 : 2;
     if ($number) {
        $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
        $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
        $str [] = ($number < 21) ? $words[$number] .
            " " . $digits[$counter] . $plural . " " . $hundred
            :
            $words[floor($number / 10) * 10]
            . " " . $words[$number % 10] . " "
            . $digits[$counter] . $plural . " " . $hundred;
     } else $str[] = null;
  }
  $str = array_reverse($str);
  $result = implode('', $str);
  $points = ($point) ?
    "." . $words[$point / 10] . " " . 
          $words[$point = $point % 10] : '';
  if($points!="")
  {
  $result.=" and ".$points;
  }
  $result.=" only.";
  $result=ucwords($result);
return $result;
}
?>
<button onclick="window.print();" class="noPrint btn btn-success">
Print Me
</button>
</body> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
$(document).ready(function() {
	
	generate_qrcode(); 
});
 

function generate_qrcode() {
	var bal_gpay = parseFloat("<?php echo $net_total; ?>") || 0;
	$("#qrcode").html("");
	$("#username-slab").text("");
	if (bal_gpay <= 0) return;

	// Use the Canara Bank QR stored when QR was generated; fall back to static merchant UPI
	var qr_string = "<?php echo addslashes($canara_qr_string); ?>";
	if (!qr_string) {
		qr_string = "upi://pay?pa=mrch.midcprin01.sidcin0001.trdcin0001@cnrb"
		          + "&pn=Printzy&am=" + bal_gpay
		          + "&cu=INR&tn=SI-<?php echo $si_no; ?>";
	}
	new QRCode(document.getElementById("qrcode"), {
		text: qr_string,
		width: 100,
		height: 100,
		colorDark: "#000000",
		colorLight: "#ffffff",
		correctLevel: QRCode.CorrectLevel.H
	});
	$("#username-slab").text("Printzy");
}
</script>