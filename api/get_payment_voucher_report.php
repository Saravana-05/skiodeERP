<?php
session_start();
include("../connect_db.php");
$result="Failed"; 
if(isset($_POST['type']))
{
	if($_POST['type'] == "get")
	{
		$from = date("Y-m-d", strtotime($_POST['from']));
		$to = date("Y-m-d", strtotime($_POST['to'])); 
		$account_head = $_POST['ah']; 
		$total_amount=0;
		$sql="SELECT * FROM  payment_voucher where voucher_date >= '".$from."' and   voucher_date <= '".$to."' ";  
		//echo  "ACH".$account_head."-----" ;
		if($account_head != "All")
		{
			$sql=$sql." and account_head='".$account_head."'";
		}
		$result='<div class="row"><div class="col-10"  id="printable_div">';  
		if($query=mysqli_query($connection,$sql))
		{
			$result.='<table class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
				$result=$result.'<thead class="table-dark">';
				$result=$result.'<tr> ';
				$result=$result.'<th>Date</th>';
				$result=$result.'<th>Acc. Head</th>'; 
				$result=$result.'<th>Vendor Name</th>'; 
				$result=$result.'<th>Ref No</th>'; 
				$result=$result.'<th>Vh No</th>';
				$result=$result.'<th>Amount</th>'; 
				$result=$result.'<th>Comments</th>';
				$result=$result.'<th>Actions</th>'; 
				$result=$result.'</tr>';
				$result=$result.'</thead>';
				$result=$result.'<tbody>';
				$i=1;
			if(mysqli_num_rows($query)>0)
			{ 
				 while($row=mysqli_fetch_array($query)) 
				 {
					$result=$result.'<tr>';
					//$result=$result.'<td>'.$i.'</td>';
					$result=$result.'<td>'.date("d-m-Y", strtotime($row["voucher_date"])).'</td>';
					$result=$result.'<td>'.$row["account_head"].'</td>'; 
					$result=$result.'<td>'.$row["vendor_name"].'</td>'; 
					$result=$result.'<td>'.$row["ref_no"].'</td>'; 
					$result=$result.'<td>'.$row["voucher_no"].'</td>'; 
					$result=$result.'<td>'.$row["amount"].'</td>';
					$result=$result.'<td>'.$row["description"].'</td>'; 
					$result=$result.'<td style="white-space:nowrap;"><button class="btn btn-sm btn-primary me-1 mt-1" onclick="fnPaymentView(this)" title="View"><span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">visibility</span></button><button class="btn btn-sm btn-danger deleteBtn mt-1" onclick="fnPaymentDelete('.$row["voucher_no"].')" title="Delete"><span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">delete</span></button></td>';
					/*if($row["amount"] >0)
					{
						$result=$result.'<td>'.$row["amount"].' Cr</td>'; 
					}
					else
					{
						$result=$result.'<td>'.abs($row["amount"]).' Dr</td>'; 
					} */
					$result=$result.'</tr>';
					$total_amount=$total_amount+$row["amount"];
					$i++;
				}			
			}
			$result=$result.'</tbody>';
			$result=$result.'<tfoot>';
			$result=$result.'<tr>';
			$result=$result.'<th colspan="5" style="text-align:right">Total:</th>';
			$result=$result.'<th></th>';
			$result=$result.'<th></th>';
			$result=$result.'<th></th>';
			$result=$result.'</tr>';
			$result=$result.'</tfoot>';
			$result=$result.'</table>';
		} 
			$result.='</div>';
	$result.='<div class="col-2 text-bg-light" style="border-left:1px inset #ccc;" >';
	$result.='<div class="text-center bg-success text-white"><h5 class="mb-0">SUMMARY</h5></div>';
	$sql2 = "SELECT account_head, SUM(amount) as total FROM payment_voucher WHERE voucher_date >= '".$from."' AND voucher_date <= '".$to."'";
if($account_head != "All") {
    $sql2 .= " AND account_head='".$account_head."'";
}
$sql2 .= " GROUP BY account_head ORDER BY account_head";

$result.='<div id="card_1">';
$result.='<table width="100%" style="font-size:0.8rem;border-collapse:collapse;">';

if($q2 = mysqli_query($connection, $sql2)) {
    while($r2 = mysqli_fetch_array($q2)) {
        $result.='<tr>';
        $result.='<td style="padding:3px 5px;text-align:right;width:55%;font-weight:bold;">'.$r2["account_head"].' :</td>';
        $result.='<td style="padding:3px 5px;"><div style="border:1px solid black;background:white;text-align:right;padding:2px;">'.ind_money($r2["total"],2).'</div></td>';
        $result.='</tr>';
    }
}

$result.='<tr><td colspan="2"><hr style="margin:3px 0;border-top:2px solid #333;"></td></tr>';
$result.='<tr>';
$result.='<td style="padding:3px 5px;text-align:right;width:55%;font-weight:bold;">TOTAL :</td>';
$result.='<td style="padding:3px 5px;"><div style="border:2px solid black;background:#ffffcc;text-align:right;padding:2px;font-weight:bold;">'.ind_money($total_amount,2).'</div></td>';
$result.='</tr>';

$result.='</table>';
$result.='</div>';
			$result.='<div class="col-12"  style="border:0px solid #ccc; padding:5px;">';
	$result.='<br> <center><button type="button" class="btn btn-primary btn-sm" onclick="printPaymentVoucherDiv('."'printable_div'".')">PRINT</button><br><br>';
	$result.='<button type="button" class="btn btn-secondary btn-sm" onclick="fnSideMenu(1)">Close</button></center>';
	$result.='</div>';
	$result.='</div>';
	}
	else if($_POST['type'] == "add")
	{
		$dt=date('Y-m-d');
		$sql="INSERT INTO `payment_voucher`( `voucher_no`, `voucher_date`, `ref_no`, `account_head`, `vendor_code`, `vendor_name`, `amount`, `description`, `created_on`) VALUES ( '".$_POST["voucher_no"]."','".date("Y-m-d", strtotime($_POST["voucher_date"]))."','".$_POST["ref_no"]."','".$_POST["account_head"]."','".$_POST["customer_code"]."','".$_POST["customer_name"]."','".$_POST["amount"]."','".$_POST["description"]."','".$dt."')";
		
		mysqli_query($connection,$sql);

/*		$sql="INSERT INTO `journal_details`( `dt`, `achead`, `description`, `amount`, `link_key`) VALUES ";
		$val_sql="";
		$val_sql.="('".date("Y-m-d", strtotime($_POST["voucher_date"]))."','".$_POST["account_head"]."','"."Payment Voucher :".$_POST["voucher_no"]."','".$_POST["amount"]."','RCPTV_".$_POST["voucher_no"]."')";
		if($val_sql!="") $val_sql.=",";
		$val_sql.="('".date("Y-m-d", strtotime($_POST["voucher_date"]))."','".$_POST["customer_code"]."','"."Payment Voucher :".$_POST["voucher_no"]."','".((float)$_POST["amount"]*-1)."','RCPTV_".$_POST["voucher_no"]."');";
		$sql.=$val_sql;
		
		
		mysqli_query($connection,$sql);*/
		$dt=date("Y-m-d", strtotime($_POST["voucher_date"]));
		post_journal_double_entry($dt,$_POST["account_head"],$_POST["customer_code"],"Payment Voucher :".$_POST["voucher_no"],$_POST["amount"],"PAYVC_".$_POST["voucher_no"]);		
				
		$result = "success";
	}
	else if($_POST['type'] == "del")
	{
		$sql="DELETE FROM  payment_voucher where voucher_no='".$_POST['v_no']."'";
		mysqli_query($connection,$sql); 
		$result = "success";
	}
}
echo $result;
?>