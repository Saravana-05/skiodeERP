<?php
include_once 'connect_db.php';
?>

<link href="bs/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
		<script src="bs/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
		<script src="js/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
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
    height: 210mm;
    width: 148.5mm;
    margin: 0;
	font-size:0.75rem;
}
.printDiv
{
	margin:2px;
	padding:3px; 
	border : 2px solid #000;
	border-radius:4px;
}
 
 
@media print {
  .noPrint{
	display:none;
  }
}
#printHeader
{
	border : 1px solid #000;
	border-radius:5px;
	margin:5px;
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
    /* margin-top: -10px; */
    border: 1px solid #000;
    border-radius: 20px;
    padding: 2px 12px;
    background: white;
    width: 100px;
        top: 148px;
    left: 210px;
}
.customerDetailsDiv
{
	font-weight:bold;
}
#quotationDetailsDiv 
{
height: 460px;
}	
#quotationDetailsDiv .table
{
	border:1px solid #ccc;
	width:95%;
	margin-left:auto;
	margin-right:auto;
	height:200px;
}
#quotationDetailsDiv th,#quotationDetailsDiv td
{
	padding:3px;
	font-size:0.75rem;
	border-bottom:0px;
	border-right:1px solid #ccc;
}
#quotationDetailsDiv th
{
	text-align:center;
	border-bottom:1px solid #ccc;
}
#quotationDetailsDiv td
{
	text-align:left;
	font-weight:500;
}
tbody
{
	border-bottom:1px solid #ccc;
}
</style>
<body>
<?php
$job_card_no=$_GET['jc'];
$sql="SELECT count(jobcard_no) as count_jobcard_no FROM jobcard_details where jobcard_no=".$job_card_no;
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
	
	$sql="SELECT * FROM jobcard_master where jobcard_no=".$job_card_no;
	$customer_Type = ""; 
	$jobcard_date = ""; 
	$discount_amount = 0; 
	$balance_amount = 0; 
	$advance_details = ""; 
	$balance_details = ""; 
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{
			if($row=mysqli_fetch_array($query))
			{
				$customer_Type = $row["customer_type"];
				$jobcard_date = $row["jobcard_date"];
				if($row['is_adv_in_gpay'] == 1)
				{
					$advance_details = "GPAY : ".$row['advance_gpay'];
				}
				if($row['is_adv_in_cash'] == 1)
				{
					$advance_details = $advance_details." CASH : ".$row['advance_cash'];
				}
				if($row['is_bal_in_gpay'] == 1)
				{
					$balance_details = "GPAY : ".$row['balance_gpay'];
				}
				if($row['is_bal_in_cash'] == 1)
				{
					$balance_details = $balance_details." CASH : ".$row['balance_cash'];
				}
				$discount_amount = $row['discount_amount'];
				$balance_amount = $row['balance_received_total'];
			}
		}
	}
	$sql="SELECT sum(value_amount) as total_amount FROM jobcard_details where jobcard_no=".$job_card_no; 
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
		$sql="SELECT * FROM jobcard_details where jobcard_no=".$job_card_no." LIMIT ".$rowVal."		OFFSET ".(10*$i);
		
		if($query=mysqli_query($connection,$sql))
		{
			if(mysqli_num_rows($query)>0)
			{
				$sNo=1;
			?>

<div class="printDiv">	 
	<div class="row" id="printHeader">
		<div class="col-6 " style="border-right: 1px solid #000;">
			<img loading="lazy" class="logo_width mt-3 mb-3" src="img/logo.png" alt="Citizen Prints">
			<p>No.4,100 feet road, Vadapalani,</p>
			<p>Chennai - 600026</p>
		</div>
		<div class="col-6">
			<div class="row">
				<div class="col-2">
				
				</div>
				<div class="col-10">
					<p>+91 90030 88363</p>
					<p>+91 89396 68686</p>
					<p>+91 70940 06001</p>
				</div>
				<div class="col-2">
				
				</div>
				<div class="col-10">
					<p>citizenprints@gmail.com</p>
					<p>cityzenprints@gmail.com</p>
					<p>citizenprintpro@gmail.com</p>
				</div>
				<div class="col-2">
				
				</div>
				<div class="col-10">
					<p>www.citizenprintz.com</p> 
				</div>
			</div> 
			
		</div>
		<div id="quotationDiv">QUOTATION</div>
		
	</div>
	 
	<div id="printBody">
		<div class="row customerDetailsDiv">
			<div class="col-6">
				<p>To : </p>
				<p><?php echo $customer_Type;?></p>
				<p>&nbsp;</p> 
			</div>
			<div class="col-6 " style="text-align:right;">
				<p>NO : <span id="snoTxt"><?php echo $job_card_no;?></span></p>
				<p>DATE : <span id="dateTxt"><?php echo $jobcard_date;?></span></p> 
				<p></p> 
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
					echo '<td style="text-align:right;">'.$sNo.'</td>';
					echo '<td>'.$row['product_code'].'</td>';
					echo '<td style="text-align:right;">'.$row['total_qty'].'</td>';
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
					  
					  <td colspan="3" style="text-align:right;">
						<p>AMOUNT : </p>
						<p>DISCOUNT : </p>
						<p>ADV PAID (<?php echo  $advance_details;?>) : </p>
						<p>BAL PAID (<?php echo  $balance_details;?>) : </p>
					  </td>
					  <td style="text-align:right;">
						<p><?php echo number_format($total_amount, 2);?></p>
						<p><?php echo number_format($discount_amount, 2);?></p>
						<p></p>
						<p></p>
					  </td>  
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<div id="printFooter">
		<div class="row">
			<div class="col-5">
				<p> Remarks : </p>
			</div>
			<div class="col-3">
				<img src="img/qrcode.png" alt="" style="width:72px;" />
			</div>
			<div class="col-4">
				<br />
				<p>For CITIZEN PRINTS</p>
				<br />
				<br />
			</div>
		</div>
		<div>Username : ADMIN</div>
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
<script>
$(document).ready(function() {
	SetUpBasics();
	//loadQuotationDetailsTable();
});

function loadQuotationDetailsTable()
{
	var i=1;
	for(i=1;i<11;i++)
	{
		var markup = "<tr>";
		markup = markup + "<td>" + i + " </td>";
		markup = markup + "<td>" + "AAA" + " </td>";
		markup = markup + "<td>" + "1000" + " </td>";
		markup = markup + "<td>" + "1000" + " </td>";
		markup = markup + "</tr>";
		
		$("#quotationDetailsDiv table tbody").append(markup);
	}
	
    
}
</script>