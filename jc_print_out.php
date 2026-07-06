<?php
session_start(); 
include_once 'connect_db.php';
?>

<link href="bs/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
		<script src="bs/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
		<script src="js/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<style>


html {
    /* off-white, so body edge is visible in brower */
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
	padding:3px; 
	border : 2px dotted #888;
	border-radius:4px;
	overflow:hidden;
}
 
 
@media print {
  .noPrint{
	display:none;
  }

    body {transform: scale(1);}
    table {page-break-inside: avoid;}
  @page {
    size: A5;
	margin: 0;	
  }
.printDiv
{
	//margin:1%;
	padding:3px; 
	border : 2px dotted #888;
	border-radius:4px;
	//overflow:hidden;
	//height:100%;
	//page-break-after: always; /* Ensure a new page after each border */ 
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
	border:1px solid red;
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
    /* margin-top: -10px; */
    border: 1px solid #000;
    border-radius: 20px;
    padding: 2px 12px;
    background: white;
    width: 100px;
    top: 153px;
    left: 230px;
    text-align: center;
}
.customerDetailsDiv
{
	font-weight:bold;
}

#quotationDetailsDiv .table
{
	border:0;
	width:95%;
	margin-left:auto;
	margin-right:auto;
	height:200px;
}
#quotationDetailsDiv th,#quotationDetailsDiv td
{
	border:0;	
	padding:1px;
	font-size:0.75rem;
	border-bottom:0px;
	
}
#quotationDetailsDiv th
{
	border:0;	
	text-align:center;
	
}
#quotationDetailsDiv td
{
	text-align:left;
	font-weight:500;
}

#qrcode {
    margin: 2px auto;
}
</style>
<body>
<?php
$printType="";
$job_card_no="";

if(isset($_GET['jc']))
{
	$printType="Job Card";
	$job_card_no=$_GET['jc'];
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
	 
	if($cnt_jc_no >10)
		$loopCnt= floor($cnt_jc_no/10)+1;
	
	$sql="SELECT * FROM jobcard_master where jobcard_no in (".$job_card_no.")";
	$customer_type = ""; 
	$customer_details = ""; 
	$jobcard_date = ""; 
	$advance_gpay = 0; 
	$advance_cash = 0; 
	$balance_gpay = 0; 
	$balance_cash = 0; 
	$discount_amount = 0; 
	$balance_amount = 0; 
	$advance_details = ""; 
	$balance_details = ""; 
	$hidden_details = ""; 
	$remarks_txt = "";
	$delivery_txt = "";
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{
			while($row=mysqli_fetch_array($query))
			{
				$remarks_txt=$row["remarks"];
				$delivery_txt=$row["delivery"];
				
				$created_by= $row["created_by"];
				$customer_type = $row["customer_type"];
				if($customer_type == "WalkIn")
				{
					$customer_details = "Walk In<br><br>"; 
				}
				else if($customer_type == "General")
				{
					$customer_details = $row["customer_name"]. "<br/>Ph : ".$row["customer_mobile_no"]."<br/>"; 
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
				if($row['is_bal_in_gpay'] == 1)
				{
					$balance_gpay = $balance_gpay + $row['balance_gpay']; 
				}
				if($row['is_bal_in_cash'] == 1)
				{
					$balance_cash = $balance_cash + $row['balance_cash']; 
				}
				$discount_amount = $row['discount_amount'];
				$balance_amount = $row['balance_received_total'];
			}
			$advance_details = "Gpay : ".$advance_gpay;
			$hidden_details = '<input type="hidden" name="adv_gpay_txt" id="advance_gpay_txt" value="'.$advance_gpay.'" />';
			$advance_details = $advance_details." Cash : ".$advance_cash;
			$net_advance=$advance_cash+$advance_gpay;
		}
	}
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
	
			<center><b>PRINTZY</b></center>
			<center>JOBCARD</center>
			<div class="row border-bottom border-2">
			<div class="col-6">
				Job. No : <b>JC-<?php echo $job_card_no; ?></b>
			</div>
			<div class="col-6" style="text-align:right;">
				Date : <b><?php echo date("d-m-Y", strtotime($jobcard_date));?></b>
			</div>
			</div>
	<div id="printBody">
		<?php echo $hidden_details;?>
		<div class="row border-bottom border-dark border-2 customerDetailsDiv">
			<div class="col-12"  style="padding-left:1.3rem;">
				<p>To : </p> 
				<p style="padding-left:15px;"><?php echo $customer_details;?></p>
				  
			</div>

		</div>
		<div id="quotationDetailsDiv" class="mt-2">
			<table width="100%;">
				<thead>
					<tr style="border-bottom:1px solid #000;">
					  <th width="10%">S.No</th>
					  <th width="55%">Description</th>
					  <th width="10%">FB</th>
					  <th width="25%">Qty</th> 
					</tr>
				</thead>
				<tbody >
				<?php
				while($row=mysqli_fetch_array($query))
				{
					echo '<tr style="border-bottom:2px solid #ddd;">';
					echo '<td style="text-align:center;" valign="top">'.$sNo.'</td>';
					$product_code = $row['product_code'];
					$job_detail_xml_ary=unserialize($row["job_detail_xml"]);
					
					$prod_sql="select * from product_master where product_code='".$product_code."';";
					
					$product_name="";
					if($prod_qry=mysqli_query($connection,$prod_sql))
					{
						if($prod_row=mysqli_fetch_array($prod_qry))
						{
							$product_name= $prod_row["product_name"];
						}
					}
					$qty_content=$row['total_qty'];
							if(!($row['piece_rate_1'] >0 && $row['piece_rate_2']>0))
							{
								if($row['piece_rate_1'] >0)
								{
								$qty_content = $row['value_amount']/$row['piece_rate_1'];
								}
							}
					$machine_detail=$row['machine_code'];
					
					$subItemDetails="";
					$job_detail_xml=$job_detail_xml_ary["prod_mat_qty"];
					if(strlen($job_detail_xml)>0)
					{
					$ary= explode("~",$job_detail_xml);
					if(count($ary)>0)
					{
						$subItemDetails='<table style="margin-left:30px;border:1px solid grey;"><tr><td style="text-align:center;" colspan="2"><u>Materials</u></td></tr>';
					for($i=0;$i<count($ary);$i++)
					{
						//$subItemDetails.="<tr><td>".$ary[$i]."</td> <td> ; </td><td></td></tr>";
						$matAry=explode(",",$ary[$i]);
						$mat_sql="select * from material_master where material_code='".$matAry[0]."';";
						$material_name=$matAry[0];
						if($mat_qry=mysqli_query($connection,$mat_sql))
						{
							if($mat_row=mysqli_fetch_array($mat_qry))
							{
								$material_name=$mat_row["material_name"];
							}
						}
						$matQty=ceil($qty_content * (float)$matAry[1]);
						//if($row['is_front_and_back'] && ((strpos(strtolower($material_name), 'paper') !== false) || (strpos(strtolower($material_name), 'sheet') !== false) || (strpos(strtolower($material_name), 'board') !== false)))
						if($row['is_front_and_back'] )
						{
							$matQty=ceil((float)$matQty/2);
						}
						$subItemDetails.="<tr><td>".$material_name."</td> <td> x </td><td>".$matQty."</td></tr>";
					}
					$subItemDetails.="</table>";
					}
					}
					echo '<td>'.$product_name.($machine_detail!=""?'<br>('.str_replace("_"," ",$machine_detail).')':'').$subItemDetails.'</td>';
					$fb_status=($row['is_front_and_back']?"<b><i>Yes</i></b>":"<i>No</i>");
					if(strpos($product_name,"DOUBLE")!==False)
						$fb_status="<b><i>Yes</i></b>";
					echo '<td style="text-align:center;" valign="top">'.$fb_status.'</td>';
					$qty_content=$row['total_qty'];
					//if($row['is_front_and_back']) $qty_content=$row['total_qty']/2;
					$qty_content="<table width='100%'><tr><td width='70%'>Qty </td><td style='text-align:right;'>".$qty_content."</td></tr></table>";
					if(!is_null($row['addl_copy_nos']))
					{
						if($row['addl_copy_nos']>0)
						{
							$cnt=$row["1st_copy_nos"];
							//if($row['is_front_and_back'])$cnt=$row["1st_copy_nos"]/2;
						$qty_content="<table width='100%'><tr><td width='70%'>1st Copy</td><td style='text-align:right;'>".$cnt."</td></tr>";
							$cnt=$row["addl_copy_nos"];
							//if($row['is_front_and_back'])$cnt=$row["addl_copy_nos"]/2;						
						$qty_content.="<tr><td>Adl Copy</td><td style='text-align:right;'>".$cnt."</td></tr>";
						$qty_content.="</table>";
						}
					}
					if(!is_null($row['ups_count']))
					{
						if(strpos($product_name,"BINDING"))
						{
						$qty_content="<table width='100%'><tr><td width='70%'>Sheets</td><td style='text-align:right;'>".$row["ups_count"]."</td></tr>";
						$qty_content.="<tr><td>Qty</td><td style='text-align:right;'>".$row["total_qty"]."</td></tr>";
						$qty_content.="</table>";
						}
						else if($row['ups_count']>0)
						{
						$qty_content="<table width='100%'><tr><td width='70%'>UPs</td><td style='text-align:right;'>".$row["ups_count"]."</td></tr>";
						$qty_content.="<tr><td>Sheets</td><td style='text-align:right;'>".$row["total_qty"]."</td></tr>";
						$qty_content.="</table>";
						}
					}
					echo '<td valign="top">'.$qty_content.'</td> ';
					echo '</tr>';
					$sNo++;
				}

?>					
				</tbody>
				<tfoot>
					
					<tr>
					  <td colspan="3"><span>User : <b><?php echo $created_by; ?></b></span > <div style="float:right;">Approx Amt : </div></td>
					  <td style="text-align:right;"><?php echo number_format($total_amount, 2);?></td>
					</tr>
					<tr>					  
					  <td colspan="3" style="text-align:right;">Adv Paid (<?php echo  $advance_details;?>) : </td>
					  <td style="text-align:right;"><?php echo number_format($net_advance, 2); ?> </td>
					</tr>
					  
				</tfoot>
			</table>
			
		</div>
	</div>
	<?php
if($remarks_txt!="")
	echo "Remarks : ".$remarks_txt."<br>";
if($delivery_txt!="")
	echo "Delivery : ".$delivery_txt."<br>";
?>
		<div class="container" style="width:100%;overflow:hidden;text-align:center;font-weight:bold;">
			<div id="qrcode" style="width:100px;"></div>
			<div id="bank_ac_name" class="name-slab"> </div>
		</div>

</div> 
<?php
			}
		}
	}

?>
 
<button onclick="window.print();" class="noPrint btn btn-success">
Print Me
</button>
</body> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
function generate_qrcode() {
	var adv_gpay = parseFloat($("#advance_gpay_txt").val()) || 0;
	$("#qrcode").html("");
	$("#bank_ac_name").text("");
	if (adv_gpay <= 0) return;

	// Use the Canara Bank QR stored when QR was generated; fall back to static merchant UPI
	var qr_string = "<?php echo addslashes($canara_qr_string); ?>";
	if (!qr_string) {
		qr_string = "upi://pay?pa=mrch.midcprin01.sidcin0001.trdcin0001@cnrb"
		          + "&pn=Printzy&am=" + adv_gpay
		          + "&cu=INR&tn=JC-<?php echo $job_card_no; ?>";
	}
	new QRCode(document.getElementById("qrcode"), {
		text: qr_string,
		width: 100,
		height: 100,
		colorDark: "#000000",
		colorLight: "#ffffff",
		correctLevel: QRCode.CorrectLevel.H
	});
	$("#bank_ac_name").text("Printzy");
}
generate_qrcode();
</script>