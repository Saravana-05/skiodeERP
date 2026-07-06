<?php
session_start();
include_once '../connect_db.php';
include_once 'save_sales_invoice_again.php';
//JC
/* $sql="select * from jobcard_master";
if($qry=mysqli_query($connection,$sql))
{
	while($row=mysqli_fetch_array($qry))
	{
		post_jc($row["jobcard_no"]);
	}
}
//SQ
$sql="select * from sales_quotation_master";
if($qry=mysqli_query($connection,$sql))
{
	while($row=mysqli_fetch_array($qry))
	{
		post_sq($row["quotation_no"]);
	}
}  */
//SI
$sql="select * from sales_invoice_master where invoice_no in (71,75,77,78,79,80)";
if($qry=mysqli_query($connection,$sql))
{
	while($row=mysqli_fetch_array($qry))
	{
		post_si($row["invoice_no"]);
	}
}
echo "Done";
function post_si($pSi_No)
{
	echo $pSi_No."<br>";
	global $connection;
	$invoice_no=$pSi_No;
	delete_journal_entries('SI_'.$invoice_no);
	
	$igst=0;
	$cgst=0;
	$sgst=0;
	$sub_total=0;
	$round_off=0;
	$sq_no=0;
	$net_total=0;
	$customer_code="";
	$dt="";
	$discount=0;

	$sql="select * from sales_invoice_master where invoice_no=".$invoice_no;
	if($qry=mysqli_query($connection,$sql))
	{
		if($row=mysqli_fetch_array($qry))
		{
			
			$sub_total=$row["net_total"]-$row["round_off"]+$row["discount"]-$row["total_tax"];
			$round_off=$row["round_off"];
			$discount=$row["discount"];
			$sq_no=$row["sale_quotation_nos"];
			$net_total=$row["net_total"];
			$dt=date('Y-m-d',strtotime($row["invoice_dt_tm"]));
			if(!empty($sq_no))
			{
				$jcnos="";
			$sql="select * from sales_quotation_master where quotation_no in (".$sq_no.");";
			if($qry=mysqli_query($connection,$sql))
			{
				while($row=mysqli_fetch_array($qry))
				{
					if($jcnos!="") $jcnos.=",";
					$jcnos.=$row["jobcard_nos"];
					if(empty($customer_code))
					{
					$jcsql="select * from jobcard_master where jobcard_no in (".$jcnos.");";
					if($jcqry=mysqli_query($connection,$jcsql))
					{
						while($jcrow=mysqli_fetch_array($jcqry))
						{
							$customer_code=$jcrow["customer_code"];
							
						}
					}	
					}					
				}
				save_sales_invoice_again($sq_no,$jcnos,$invoice_no);
			}
			}
			else
			{
				$customer_code=$row["customer_code"];
			}
		}
	}
	$sql="select * from sales_invoice_master where invoice_no=".$invoice_no;
	if($qry=mysqli_query($connection,$sql))
	{
		if($row=mysqli_fetch_array($qry))
		{
			
			$sub_total=$row["net_total"]-$row["round_off"]+$row["discount"]-$row["total_tax"];
			$round_off=$row["round_off"];
			$discount=$row["discount"];
			$sq_no=$row["sale_quotation_nos"];
			$net_total=$row["net_total"];
		}
	}		
	$sql="select * from sales_invoice_gst_details where invoice_no=".$invoice_no;
	if($qry=mysqli_query($connection,$sql))
	{
		if($row=mysqli_fetch_array($qry))
		{
			$igst=$row["igst"];
			$cgst=$row["cgst"];
			$sgst=$row["sgst"];
			
		}
	}
	
/*	$sql="INSERT INTO `journal_details`( `dt`, `achead`, `description`, `amount`, `link_key`) VALUES ";
	
	$val_sql="";
	if($sub_total>0)
		$val_sql.="('".$dt."','SALES','Sales Invoice :".$invoice_no." Quote : ".$sq_no."',".$sub_total+$round_off.",'SI_".$invoice_no."')";
	if($igst>0)
	{
		if($val_sql!="") $val_sql.=",";
		$val_sql.="('".$dt."','IGST','IGST for SI:".$invoice_no."',".$igst.",'SI_".$invoice_no."')";
	}
	if($cgst>0)
	{
		if($val_sql!="") $val_sql.=",";
		$val_sql.="('".$dt."','CGST','CGST for SI:".$invoice_no."',".$cgst.",'SI_".$invoice_no."')";
	}
	if($sgst>0)
	{
		if($val_sql!="") $val_sql.=",";
		$val_sql.="('".$dt."','SGST','SGST for SI:".$invoice_no."',".$sgst.",'SI_".$invoice_no."')";
	}
	if($sub_total>0 && $customer_code!="")
	{
		if($val_sql!="") $val_sql.=",";
		$val_sql.="('".$dt."','".$customer_code."','Sales Invoice :".$invoice_no." Quote : ".$sq_no."',-".$net_total.",'SI_".$invoice_no."')";
	}
	$sql.=$val_sql.";";*/
		$sql="INSERT INTO `journal_details`( `dt`, `achead`, `description`, `amount`, `link_key`,contra_achead) VALUES ";
		$val_sql="";
		if($sub_total>0)
			$val_sql.="('".$dt."','SALES','Sales Invoice :".$invoice_no." Quote : ".$sq_no."',".$sub_total-$discount+$round_off.",'SI_".$invoice_no."','".$customer_code."')";
		if($igst>0)
		{
			if($val_sql!="") $val_sql.=",";
			$val_sql.="('".$dt."','IGST','IGST for SI:".$invoice_no."',".$igst.",'SI_".$invoice_no."','".$customer_code."')";
		}
		if($cgst>0)
		{
			if($val_sql!="") $val_sql.=",";
			$val_sql.="('".$dt."','CGST','CGST for SI:".$invoice_no."',".$cgst.",'SI_".$invoice_no."','".$customer_code."')";
		}
		if($sgst>0)
		{
			if($val_sql!="") $val_sql.=",";
			$val_sql.="('".$dt."','SGST','SGST for SI:".$invoice_no."',".$sgst.",'SI_".$invoice_no."','".$customer_code."')";
		}
		if($sub_total>0 && $customer_code!="")
		{
			if($val_sql!="") $val_sql.=",";
			$val_sql.="('".$dt."','".$customer_code."','Sales Invoice :".$invoice_no." Quote : ".$sq_no."',-".$net_total.",'SI_".$invoice_no."','SALES')";
		}
		$sql.=$val_sql.";";	
	mysqli_query($connection,$sql);	
	//remove sq journal entries
	$sq_no_ary=explode(",",$sq_no);
	$sql="delete from journal_details where link_key in (";
	foreach($sq_no_ary as $sq)
	{
		$sql.="'SQ_".$sq."',";
	}
	$sql = rtrim($sql, ','); 
	$sql.=");";
	
	mysqli_query($connection,$sql);	
}
function post_jc($pJc_No)
{
	global $connection;
	delete_journal_entries('JC_'.$pJc_No);
	$customer_type_txt="";
	$jobcard_no_txt=$pJc_No;
	$sql="select * from jobcard_master where jobcard_no=".$pJc_No;
	if($qry=mysqli_query($connection,$sql))
	{
		if($row=mysqli_fetch_array($qry))
		{
			$customer_type_txt=$row["customer_type"];
			$advance_cash_txt=$row["advance_cash"];
			$advance_gpay_txt=$row["advance_gpay"];
			$dt=date('Y-m-d',strtotime($row["jobcard_date"]));
			
		}
	}
	if($customer_type_txt=="WalkIn" || $customer_type_txt=="General")
	{
		$advance_amount_txt= $advance_cash_txt+$advance_gpay_txt;
		if($advance_amount_txt>0)
		{
			if($advance_cash_txt>0)
				post_journal_single_entry($dt,'CASH','Advance Cash for JC:'.$jobcard_no_txt,$advance_cash_txt,'JC_'.$jobcard_no_txt);
			if($advance_gpay_txt>0)
			{
				//if($advance_gpay_txt>1999)
				if($advance_gpay_txt<0)
				{
					post_journal_single_entry($dt,'CA','Advance CA GPAY for JC:'.$jobcard_no_txt,$advance_gpay_txt,'JC_'.$jobcard_no_txt);
				}
				else
				{
					post_journal_single_entry($dt,'SB','Advance SB GPAY for JC:'.$jobcard_no_txt,$advance_gpay_txt,'JC_'.$jobcard_no_txt);
				}
			}
		}
	}		
}
function post_sq($pSq_No)
{
	global $connection;
	$max_sqno=$pSq_No;
	$jcnos="";
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
	if($balance_amount_txt>0)
	{
	$balance_amount_txt = $balance_cash_txt+$balance_gpay_txt;
	if($balance_amount_txt>0)
	{
		delete_journal_entries('SQ_'.$max_sqno);
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