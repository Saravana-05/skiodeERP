<?php
session_start();
include_once '../connect_db.php';
$result="Failed";
if(isset($_POST["SQ_Mini_Editquotation_no_txt"]))
{
	$SQ_Mini_Editquotation_no_txt=$_POST["SQ_Mini_Editquotation_no_txt"];
	$SQ_Mini_Editdiscount_txt=$_POST["SQ_Mini_Editdiscount_txt"];
	$SQ_Mini_Editbalance_cash_txt=$_POST["SQ_Mini_Editbalance_cash_txt"];
	$SQ_Mini_Editbalance_gpay_txt=$_POST["SQ_Mini_Editbalance_gpay_txt"];
	post_sq($SQ_Mini_Editquotation_no_txt,(float)$SQ_Mini_Editdiscount_txt,(float)$SQ_Mini_Editbalance_cash_txt,(float)$SQ_Mini_Editbalance_gpay_txt);
	$result="Success";
}
echo $result;
function post_sq($pSq_No,$pdiscount,$pbalance_cash,$pbalance_gpay)
{
	global $connection;
	
	$max_sqno=$pSq_No;
	$jcnos="";
	$gst_tax_amount=0;
	$sql="select * from sales_quotation_master where quotation_no=".$max_sqno;
	if($qry=mysqli_query($connection,$sql))
	{
		if($row=mysqli_fetch_array($qry))
		{	
			if($row["is_gst_extra"])
			{
				$gst_tax_amount=calc_gst_jc($row["jobcard_nos"],$pdiscount);
			}
		}
	}
	$balance_received_total=$pbalance_cash+$pbalance_gpay;
	$sql = "update sales_quotation_master set discount = '".$pdiscount."', balance_cash='".$pbalance_cash."',balance_gpay='".$pbalance_gpay."',balance_received_total='".$balance_received_total."' ";
	if($gst_tax_amount>0)
		$sql.= ",gst_tax_amount='".$gst_tax_amount."'";
	$sql.=" where quotation_no=".$max_sqno;
	
	mysqli_query($connection,$sql);
	
	
	
	$sql="select * from sales_quotation_master where quotation_no=".$max_sqno;
	
	if($qry=mysqli_query($connection,$sql))
	{
		if($row=mysqli_fetch_array($qry))
		{
			$dt=date('Y-m-d',strtotime($row["quotation_dt_tm"]));
			$balance_cash_txt=$row["balance_cash"];
			$balance_gpay_txt=$row["balance_gpay"];
			$jcnos=$row["jobcard_nos"];
			$approximate_amount=$row["approximate_amount"];
			$discount_txt=$row["discount"];
			$gst_txt=$row["gst_tax_amount"];
		}
	}
	$balance_amount_txt = $balance_cash_txt+$balance_gpay_txt;
	delete_journal_entries('SQ_'.$max_sqno);
	if($balance_amount_txt>0)
	{
	$balance_amount_txt = $balance_cash_txt+$balance_gpay_txt;
	if($balance_amount_txt>0)
	{
		
		if($balance_cash_txt>0)
			post_journal_single_entry($dt,'CASH','Balance Cash for SQ:'.$max_sqno." JC: ".$jcnos,$balance_cash_txt,'SQ_'.$max_sqno);
		if($balance_gpay_txt>0)
		{
			//if($balance_gpay_txt>1999)
			if($balance_gpay_txt<0)
			{
				post_journal_single_entry($dt,'CA','Balance CA GPAY for SQ:'.$max_sqno." JC: ".$jcnos,$balance_gpay_txt,'SQ_'.$max_sqno);
			}
			else
			{
				post_journal_single_entry($dt,'SB','Balance SB GPAY for SQ:'.$max_sqno." JC: ".$jcnos,$balance_gpay_txt,'SQ_'.$max_sqno);
			}
		}
	}		
	}
	else
	{
		//surely this is credit customer
		//fetch Credit Customer Code
		$jcnos=$jcnos;
		$sql = "select customer_code from jobcard_master where jobcard_no in (".$jcnos.") limit 1;";
		$customer_code="";
		if($qry = mysqli_query($connection,$sql))
		{
			if($row=mysqli_fetch_array($qry))
			{
				$customer_code=$row["customer_code"];
			}
		}
		if($customer_code!="")
		{
			
			$approximate_amount-=$discount_txt;
			$approximate_amount+=$gst_txt;
			if($approximate_amount>0)
			{
				post_journal_double_entry($dt,"SALES",$customer_code,"Quote :".$max_sqno,$approximate_amount,"SQ_".$max_sqno);
			}
		}
	}	
}
?>