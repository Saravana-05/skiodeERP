<?php
//session_start();
include_once '../connect_db.php';
function save_sales_invoice_again($psq_no,$pjc_nos,$psi_no)
{
	global $connection;
$result="Failed";
if(isset($psq_no))
{
	$sq_no = $psq_no; 
	$sq_no =rtrim($sq_no, ','); 
	$jc_no =$pjc_nos; 
	$jc_no =rtrim($jc_no, ','); 
	$dt = date("Y-m-d H:i:s");
	$sql="SELECT max(invoice_no) as max_invoice_no FROM sales_invoice_master;";
	$invoice_no=1;

		$invoice_no=$psi_no;

	$sql="SELECT sum(approximate_amount) as sum_app_amt FROM jobcard_master where jobcard_no in (".$jc_no.");";
	
	$approximate_amount = 0; 
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{
			if($row=mysqli_fetch_array($query))
			{
				if(!is_null($row["sum_app_amt"]))
				{
					$approximate_amount = $row["sum_app_amt"]; 
				}

			}
		}
	}
	$sql="SELECT sum(discount) as sum_discount_amt,sum(gst_tax_amount) as sum_gst_tax_amount FROM sales_quotation_master where quotation_no in (".$sq_no.");";
	
	$discount = 0; 
	$gst_tax_amount_collected = 0;
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{
			if($row=mysqli_fetch_array($query))
			{
				if(!is_null($row["sum_discount_amt"]))
				{  
					$discount = $row["sum_discount_amt"]; 
					$gst_tax_amount_collected = $row["sum_gst_tax_amount"];
					
				} 
			}
		}
	}
	//echo $sq_no;
	//GST Calculations Starts Here
	$is_this_igst_invoice=0;
	$total_tax=0;
	$net_total=0;
	$sub_total=0;
	$inclusive_gst=1;
	if($gst_tax_amount_collected > 0 )
	{
		$inclusive_gst=0;
	}
	//Get the first jobcard data or gst no or state
	$customer_code="";
	$customer_Type="";
	$sql = "select customer_code,customer_type,customer_gst_no,customer_state_code,customer_state from jobcard_master where jobcard_no in (".$jc_no.");";
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{
			if($row=mysqli_fetch_array($query))
			{					  
//compare states and find out whether it belongs to igst or cgst,sgst category
				$customer_code = $row["customer_code"];
				$customer_Type = $row["customer_type"];
				$customer_state_code = $row["customer_state_code"];
				$customer_state = $row["customer_state"];
				$customer_gst_no = $row["customer_gst_no"];
				if($customer_state_code=="") $customer_state_code=33;
				if($customer_state_code!=33)// hard coded for TN should be improved later
				{
					$is_this_igst_invoice=1;
				}
				if($customer_Type=='Credit') $inclusive_gst=0;
			}
		}
	}	

	//get sum of value,qty of materials used in the jcs grouped by hsnno(or %)
	$sql="SELECT a.jobcard_no,a.product_code,sum(a.total_qty) as sum_total_qty,sum(a.value_amount) as sum_value_amount,b.product_name,b.hsn_code,b.gst_percentage FROM `jobcard_details` a inner join product_master b on a.product_code=b.product_code group by b.gst_percentage having a.jobcard_no in (".$jc_no.");";
	$sql="SELECT a.product_code,sum(a.total_qty) as sum_total_qty,sum(a.value_amount) as sum_value_amount,b.product_name,b.gst_percentage FROM ( select * from `jobcard_details` where jobcard_no in (".$jc_no.")) a inner join product_master b on a.product_code=b.product_code group by b.gst_percentage order by gst_percentage;";
	
	if($query=mysqli_query($connection,$sql))
	{
		$diff_gst_percentage_count=mysqli_num_rows($query);
		$per_gst_percentage_discount=0.00;
		
		if($discount>0)
		{
			$per_gst_percentage_discount=$discount/$diff_gst_percentage_count;
		}
		if($diff_gst_percentage_count>0)
		{
			while($row=mysqli_fetch_array($query))
			{	
				$hsn_codes="";
				$gst_percentage=$row["gst_percentage"];
				$hsn_sql="SELECT DISTINCT b.hsn_code,b.gst_percentage FROM ( select * from `jobcard_details` where jobcard_no in (".$jc_no.")) a inner join product_master b on a.product_code=b.product_code where gst_percentage=".$gst_percentage.";";
				
				if($hsn_query=mysqli_query($connection,$hsn_sql))
				{
					while($hsn_row=mysqli_fetch_array($hsn_query))
					{
						if($hsn_codes!="") $hsn_codes.=",";
						$hsn_codes .=$hsn_row["hsn_code"];						
					}
				}
				$before_gst_value=$row["sum_value_amount"];
				
				$sub_total+=$before_gst_value;
				
				$before_gst_value-=$per_gst_percentage_discount;
				
				$igst=0;
				$cgst=0;
				$sgst=0;				
				if($inclusive_gst==1)
				{
					$igst=round($before_gst_value-($before_gst_value/((1.0+($gst_percentage/100)))),2);
					$total_tax+=$igst;
					$before_gst_value-=$igst;
					if(!$is_this_igst_invoice)
					{
						$cgst=round($igst/2,2);
						$sgst=round($igst/2,2);						
						$igst=0;
					}					
				}
				else
				{
					$igst=round($before_gst_value*($gst_percentage/100),2);
					$total_tax+=$igst;
					if(!$is_this_igst_invoice)
					{
						$cgst=round($igst/2,2);
						$sgst=round($igst/2,2);	
						$total_tax=$cgst+$sgst;
						$igst=0;
					}
				}	

					$ins_gst_sql="update sales_invoice_gst_details set hsn_codes='".$hsn_codes."',before_gst_value=".$before_gst_value.",cgst=".$cgst.",sgst=".$sgst.",igst=".$igst." where invoice_no=".$invoice_no." and gst_percentage=".$gst_percentage;
				echo $ins_gst_sql."<br>";
				mysqli_query($connection,$ins_gst_sql);
				$net_total+=$before_gst_value+$igst+$cgst+$sgst;
			}
		}
	}
	$round_off = round(round($net_total,0)-round($net_total,2),2);
	$net_total=round(round($net_total+$round_off,2),0);
	//divide discount (if any) by no of rows and apply it for all
	//calculate the gsts and push it in sales_invoice_gst_details
	//GST Calculations Ends here

		$sql="update sales_invoice_master set approximate_amount=".$approximate_amount.",discount=".$discount.",inclusive_gst=".$inclusive_gst.",total_tax=".$total_tax.",round_off=".$round_off.",net_total=".$net_total." where  invoice_no=".$invoice_no;
		echo $sql."<br>";
	
	mysqli_query($connection,$sql);
	
	
	
	/* $sq_no_arr=explode(',',$sq_no);	 
	for($i=0;$i<count($sq_no_arr);$i++)
	{ */
		// Always preserve the original SQ creator — not whoever is converting (admin may be re-converting)
		$sq_nos_safe2 = implode(',', array_filter(array_map('intval', explode(',', $sq_no))));
		$orig_user2 = 'system';
		if (!empty($sq_nos_safe2)) {
			$oc2 = mysqli_query($connection,
				"SELECT created_by FROM sales_quotation_master WHERE quotation_no IN ($sq_nos_safe2) LIMIT 1");
			if ($oc2 && $or2 = mysqli_fetch_assoc($oc2)) {
				if (!empty($or2['created_by'])) $orig_user2 = mysqli_real_escape_string($connection, $or2['created_by']);
			}
		}
		//$sql = "update sales_quotation_master set is_this_converted_into_invoice=1,invoice_no=".$invoice_no.",invoice_converted_by='".$_SESSION['user_name']."' where quotation_no=".$sq_no_arr[$i];
		$sql = "update sales_quotation_master set is_this_converted_into_invoice=1,invoice_no=".$invoice_no.",invoice_converted_by='".$orig_user2."' where quotation_no in (".$sq_no.");";
		mysqli_query($connection,$sql);
	/* } */
	$result=$invoice_no;

			$sql="delete from journal_details where link_key='SI_".$invoice_no."'";
			mysqli_query($connection,$sql);

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
}
?>