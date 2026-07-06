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
    size: A5;
    
    margin: 0;
}

html {
    /* off-white, so body edge is visible in browser */
    background: #fff;
}

body {
    /* A5 dimensions */
    //height: 210mm;
    width: 148.5mm;
    margin: 5px;
	font-size:0.75rem;
}
.printDiv
{
	margin:2px;
	padding:1px; 
	border : 2px solid #000;
	border-radius:4px;
}
 
 
@media print {
  .noPrint{
	display:none;
  }

    body {transform: scale(1);}
    table {page-break-inside: avoid;}
  @page {
    size: A5;
  }
}
#printHeader
{
	border : 1px solid #000;
	border-radius:5px;
	margin:8px;
	//padding:5px;
}
#printBody
{
	
}
#printFooter
{
	
}
p
{
	margin:2px;
}
.logo_width
{
	width:80%;
}
#quotationDiv
{
	position: absolute;
    
    border: 1px solid #000;
    border-radius: 20px;
    padding: 2px 12px;
    background: white;
    width: auto;
    left:43%;
    margin-top:-12px;
    text-align: center;
	font-weight:BOLD;
}
.customerDetailsDiv
{
	font-weight:bold;
}

#quotationDetailsDiv .table
{
	border:1px solid #000;
	width:95%;
	margin-left:auto;
	margin-right:auto;
	height:200px;
}
#quotationDetailsDiv th,#quotationDetailsDiv td
{
	padding:1px;
	font-size:0.75rem;
	border-bottom:0px;
	border-right:1px solid #000;
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
	border-bottom:1px solid #000;
}

#qrcode {
    margin: 2px auto;
}
</style>
<body>
<?php
$printType="";
$job_card_no="";
$sq_no="";
if(isset($_GET['jc']))
{
	$printType="Job Card";
	$job_card_no=$_GET['jc'];
}
else if(isset($_GET['sq']))
{
	$printType="Quotation";
	$sq_no=$_GET['sq'];
	$sql="SELECT * FROM sales_quotation_master where  quotation_no=".$sq_no;
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{ 
			 if($row=mysqli_fetch_array($query)) 
			 {
				 $job_card_no=$row['jobcard_nos'];
			 }
		}
	}
}


$sql="SELECT count(jobcard_no) as count_jobcard_no FROM jobcard_details where jobcard_no in (".$job_card_no.")";
log_this($sql);
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
	 
	if($cnt_jc_no >10)
		$loopCnt= floor($cnt_jc_no/10)+1;
	
	$sql="SELECT * FROM jobcard_master where jobcard_no in (".$job_card_no.")";
	$created_by= "";
	$customer_type = ""; 
	$customer_details = ""; 
	$jobcard_date = ""; 
	$advance_gpay = 0; 
	$advance_cash = 0; 
	$advance_total = 0;
	$balance_gpay = 0; 
	$balance_cash = 0; 
	$discount = 0; 
	$balance_amount = 0; 
	$advance_details = ""; 
	$balance_details = ""; 
	$hidden_details = ""; 
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{
			while($row=mysqli_fetch_array($query))
			{
				$created_by= $row["created_by"];
				$customer_type = $row["customer_type"];
				if($customer_type == "Walkin")
				{
					$customer_details = "Walkin"; 
				}
				else if($customer_type == "General")
				{
					$customer_details = "General<br/>".$row["customer_name"]. "<br />".$row["customer_mobile_no"]; 
				}
				else if($customer_type == "Credit")
				{
					$customer_details = $row["customer_name"]. "<br />";
					$customer_details .= $row["customer_addr1"]." ".$row["customer_addr2"]. "<br />";
					$customer_details .= $row["customer_city"]." ".$row["customer_state"]. "<br />";
					$customer_details .= $row["customer_mobile_no"]; 
				}
				
				$jobcard_date = $row["jobcard_date"];
				if($row['is_adv_in_gpay'] == 1)
				{
					$advance_gpay = $advance_gpay + $row['advance_gpay'];
				}
				if($row['is_adv_in_cash'] == 1)
				{
					$advance_cash = $advance_cash + $row['advance_cash'];
				}
				$advance_total=$advance_gpay+$advance_cash;
				
			}
		}
	}
	$sql="select * from sales_quotation_master where quotation_no=".$sq_no;
	$prev_balance=0.0;
	if($query=mysqli_query($connection,$sql))
	{
		if($row=mysqli_fetch_array($query))
		{
				$jobcard_date = $row["quotation_dt_tm"];
				$balance_gpay = $balance_gpay + $row['balance_gpay']; 
			 
				$balance_cash = $balance_cash + $row['balance_cash']; 
				$prev_balance=$row["prev_balance"];
			 
			$discount = $row['discount'];
			$balance_amount = $row['balance_received_total'];
		}
	}
	$advance_details = "GPAY : ".$advance_gpay;
	$hidden_details = '<input type="hidden" name="customer_type_txt" id="customer_type_txt" value="'.$customer_type.'" /><input type="hidden" name="balance_gpay_txt" id="balance_gpay_txt" value="'.$balance_gpay.'" />';
	$advance_details = $advance_details." CASH : ".$advance_cash;
	$balance_details = "GPAY : ".$balance_gpay;
	$balance_details = $balance_details." CASH : ".$balance_cash;

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
		$rowVal = 10; 
		if($i>0)
			$rowVal = (10*$i) + 1;;
		$sql="SELECT * FROM jobcard_details where jobcard_no in (".$job_card_no.") LIMIT ".$rowVal."		OFFSET ".(10*$i);
		
		if($query=mysqli_query($connection,$sql))
		{
			if(mysqli_num_rows($query)>0)
			{
				$sNo=1;
				
			?>

<div class="printDiv">	 
	<div class="row" id="printHeader">
		<div class="col-6 " style="border-right: 1px solid #000;">
		<div style="text-align:center">
			<img loading="lazy" class="logo_width mt-3 mb-3" src="img/printzy_logo.png" alt="Printzy" style="">
		</div>
			<p><i class="fa-solid fa-location-dot" ></i> No.4,100 feet road, Vadapalani,</p>
			<p>Chennai - 600026</p>
		</div>
		<div class="col-6">
			<div class="row">
				<div class="col-1"></div>
				<div class="col-1 pt-2">
					<i class="fa-solid fa-phone" ></i>
				</div>
				<div class="col-7">
					<p>+91 90030 88363</p>
					<p>+91 89396 68686</p>
					<p>+91 70940 06001</p>
				</div>
				<div class="col-2">
					<img src="img/sq_img1.png" />
				</div>
				<div class="col-1"></div>
				<div class="col-1  pt-2">
					<i class="fa-solid fa-envelope" ></i>
				</div>
				<div class="col-7">
					<p>citizenprints@gmail.com</p>
					<p>cityzenprints@gmail.com</p>
					<p>citizenprintpro@gmail.com</p>
				</div>
				<div class="col-2"></div>
				<div class="col-1"></div>
				<div class="col-1  pt-2">
					<i class="fa-solid fa-globe" ></i> 
				</div>
				<div class="col-8">
					<p>www.citizenprintz.com</p> 
				</div>
			</div> 
			
		</div>
		<div class=""><img src="img/sq_img2.png" style="position: absolute;    top: 70px; left: 276px;"/></div>
		<div style="position:relative;">		
		<div id="quotationDiv"><?php echo $printType; ?></div>
		</div>
		
	</div>
	 
	<div id="printBody">
		<?php echo $hidden_details;?>
		<div class="row customerDetailsDiv">
			<div class="col-6 "  style="padding-left:1.3rem;">
				<p>To : </p> 
				<p style="padding-left:15px;"><?php echo $customer_details;?></p>
				  
			</div>
			<div class="col-6 " style="text-align:right;padding-right:1.3rem;">
				<div class="row">
			
				<?php 
					if($printType == 'Job Card')
					{
						echo '<div class="col-6" style="text-align: right;"> JC NO : </div><div class="col-6" style="text-align: left;"><span id="snoTxt">JC-'.$job_card_no.'</span></div>';
					}
					else if($printType == 'Quotation')
					{
						echo '<div class="col-6" style="text-align: right;"> SQ NO : </div><div class="col-6" style="text-align: left;"><span id="snoTxt">SQ-'.$sq_no.'</span></div>'; 
					}
				?>
				
				<div class="col-6" style="text-align: right;"> DATE : </div><div class="col-6" style="text-align: left;"><span id="dateTxt"><?php echo date("d-m-Y", strtotime($jobcard_date));?></span> </div>
				<div class="col-6" style="text-align: right;"> MODE OF PAY : </div>
				</div>
			</div>
		</div>
		<div id="quotationDetailsDiv" class="mt-2">
			<table class="table ">
				<thead>
					<tr>
					  <th width="10%">S.No</th>
					  <th width="60%">PARTICULARS</th>
					  <th width="15%">QTY</th>
					  <th width="15%">AMOUNT</th> 
					</tr>
				</thead>
				<tbody >
				<?php
				while($row=mysqli_fetch_array($query))
				{
					echo '<tr>';
					echo '<td style="text-align:center;">'.$sNo.'</td>';
					
					$product_code = $row['product_code'];
					$prod_sql="select * from product_master where product_code='".$product_code."';";
					$product_name="";
					if($prod_qry=mysqli_query($connection,$prod_sql))
					{
						if($prod_row=mysqli_fetch_array($prod_qry))
						{
							$product_name= $prod_row["product_name"];
						}
					}
					
					echo '<td>'.$product_name.'</td>';
					echo '<td style="text-align:center;">'.$row['total_qty'].'</td>';
					echo '<td style="text-align:right;">'.$row['value_amount'].'</td> ';
					echo '</tr>';
					$sNo++;
				}
				while(++$sNo<15)
				{
					echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><tr>';
				}

?>					
				</tbody>
				<tfoot>
					<tr>					  
					  <td colspan="3" style="text-align:right;">AMOUNT : </td>
					  <td style="text-align:right;"><?php echo number_format($total_amount, 2);?></td>
					</tr>
					<?php if ($discount>0) { ?>
					<tr>					  
					  <td colspan="3" style="text-align:right;">DISCOUNT : </td>
					  <td style="text-align:right;"><?php echo number_format($discount, 2);?></td>
					</tr>
					<?php } 
					if($advance_total+$balance_amount>0)
					{
					?>
					<tr>					  
					  <td colspan="3" style="text-align:right;">ADV PAID (<?php echo  $advance_details;?>) : </td>
					  <td style="text-align:right;"><?php echo number_format($advance_total, 2);?> </td>
					</tr>
					<tr>					  
					  <td colspan="3" style="text-align:right;">BAL PAID (<?php echo  $balance_details;?>) : </td>
					  <td style="text-align:right;"><?php echo number_format($balance_amount, 2);?></td>
					</tr> 
					<?php }
					// PREV. BALANCE only applies to Credit customers.
					// Walk-in / General customers never carry a running balance —
					// showing it on their bill is wrong even if the DB has a stale value.
					$effective_prev = ($customer_type === 'Credit') ? floatval($prev_balance) : 0;
					if($effective_prev != 0)
					{
					?>
					<tr>
					  <td colspan="3" style="text-align:right;">PREV. BALANCE : </td>
					  <td style="text-align:right;"><?php echo number_format($effective_prev, 2);?> </td>
					</tr>
					<?php
					}
					?>
					<tr>
					  <td colspan="3" style="text-align:right;">NET TOTAL : </td>
					  <td style="text-align:right;"><span id="balance_txt"><?php echo number_format($effective_prev + $total_amount - $discount, 2);?> </span></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<div id="printFooter">
		<div class="row">
			<div class="col-5" style="padding-left:1rem;">
				<p><b> Remarks :</b> </p>
			</div>
			<div class="col-3">
				 
				<div class="container" style="width:auto;overflow:hidden;font-size:10px;text-align:center;font-weight:bold;">
					<div id="qrcode"></div>
					<div id="username-slab" class="name-slab"> </div>
				</div>
			</div>
			<div class="col-4">
			
				<p><b>For PRINTZY</b></p>				<br />
				<br />
			</div>
		</div>
		<div  style="padding-left:0.4rem;">Username : <b><?php echo $created_by; ?></b></div>
		
	</div>
</div> 
<?php
			}
		}
	}

?>
 
<div class="noPrint"><?php if ($customer_type=="Credit") { ?><label><input id="show_qr_code_chk" onclick="generate_qrcode()" type="checkbox"> Show Qr Code (Credit Customer Spl.)</input></label> <?php } ?>  <button onclick="window.print();" class="btn btn-success">
Print Me
</button></div>
</body> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
$(document).ready(function() {
	generate_qrcode();
});

function generate_qrcode() {
	var bal_gpay = parseFloat($("#balance_gpay_txt").val()) || 0;
	var customer_type = $("#customer_type_txt").val();

	// Credit customer special: use net total if checkbox checked and no GPay balance
	if (customer_type === "Credit" && $("#show_qr_code_chk").is(':checked') && bal_gpay === 0) {
		bal_gpay = parseFloat($("#balance_txt").text().replace(/,/g, "")) || 0;
	}

	$("#qrcode").html("");
	$("#username-slab").text("");
	if (bal_gpay <= 0) return;

	// Use the Canara Bank QR stored when QR was generated; fall back to static merchant UPI
	var qr_string = "<?php echo addslashes($canara_qr_string); ?>";
	if (!qr_string) {
		qr_string = "upi://pay?pa=mrch.midcprin01.sidcin0001.trdcin0001@cnrb"
		          + "&pn=Printzy&am=" + bal_gpay
		          + "&cu=INR&tn=SQ-<?php echo $sq_no; ?>";
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