<?php
session_start();
include_once '../connect_db.php';
function esc($val)
{
    global $connection;
    return mysqli_real_escape_string($connection, trim($val));
}
$result="Failed";
$job_details_array= json_decode($_POST['allTableData'], true);

$dt = date("Y-m-d H:i:s");
$just_date = date("Y-m-d");
$customer_type_txt=$_POST["customer_type_txt"];
$credit_customer_code_txt=trim($_POST["credit_customer_code_txt"] ?? "");
$valid=1;
if($customer_type_txt=="Credit" && $credit_customer_code_txt==="") $valid=0;

// General customer MUST have a name — enforced at backend regardless of frontend
if($customer_type_txt=="General" && trim($_POST["customer_name_txt"] ?? "")==="") {
    echo "Failed : General customer name is required. Please enter the customer name.";
    exit;
}

if($valid)
{
	$theOperation_txt = $_POST["theOperation_txt"];
	$prev_jobcard_no_txt = (int)$_POST["jobcard_no_txt"];
	$customer_type_txt = $_POST["customer_type_txt"];
	$is_adv_in_cash_txt = false;
	if(isset($_POST["is_adv_in_cash_txt"])) $is_adv_in_cash_txt = true;
	$advance_cash_txt = (float)$_POST["advance_cash_txt"];
	$is_adv_in_gpay_txt = false;
	if(isset($_POST["is_adv_in_gpay_txt"])) $is_adv_in_gpay_txt = true;
	$advance_gpay_txt = (float)$_POST["advance_gpay_txt"];
	$remarks_txt = esc($_POST["remarks_txt"]);
	$delivery_txt = esc($_POST["delivery_txt"]);
	$actual_user = $_SESSION['user_name'];

	// Customer detail fields — always assign, safe default to empty string
	$customer_name_txt       = esc($_POST["customer_name_txt"] ?? "");
	$customer_addr1_txt      = esc($_POST["customer_addr1_txt"] ?? "");
	$customer_addr2_txt      = esc($_POST["customer_addr2_txt"] ?? "");
	$customer_city_txt       = esc($_POST["customer_city_txt"] ?? "");
	$customer_state_code_txt = esc($_POST["customer_state_code_txt"] ?? "");
	$customer_state_txt      = esc($_POST["customer_state_txt"] ?? "");
	$customer_gst_no_txt     = esc($_POST["customer_gst_no_txt"] ?? "");
	$customer_mobile_no_txt  = esc($_POST["customer_mobile_no_txt"] ?? "");

	if($prev_jobcard_no_txt != 0)
	{
			$just_date=date("Y-m-d",strtotime($_POST["jobcard_date_txt"]));
			$dt=$just_date;
			if($prev_jobcard_no_txt > 0)
			{
			$sql="select * from jobcard_master where jobcard_no=".$prev_jobcard_no_txt;
			if($qry=mysqli_query($connection,$sql))
			{
				if($row=mysqli_fetch_array($qry))
				{
					$dt = date("Y-m-d H:i:s",strtotime($row["created_dt_tm"]));
					$actual_user=$row["created_by"];
					$actual_job_card_closed=$row['job_card_closed'];
					$actual_job_closed_dt_tm=$row['job_closed_dt_tm'];
					$actual_job_card_quotation_no=$row['job_card_quotation_no'];
					$actual_job_card_invoice_no=$row['job_card_invoice_no'];
					$actual_job_card_closed_on=$row['job_card_closed_on'];
					$actual_jobcard_closed_by=$row['jobcard_closed_by'];
					$actual_void_job_card=$row['void_job_card'];
					$actual_balance_gpay=$row['balance_gpay'];
					$actual_balance_cash=$row['balance_cash'];
					$actual_balance_received_total=$row['balance_received_total'];
				}
			}
			}
			$sql="delete from jobcard_master where jobcard_no=".$prev_jobcard_no_txt.";";
			executed_sql($sql);
			$sql="delete from jobcard_details where jobcard_no=".$prev_jobcard_no_txt.";";
			executed_sql($sql);
			$sql="delete from job_card_wise_material_usage where jobcard_no=".$prev_jobcard_no_txt.";";
			executed_sql($sql);
			$sql="delete from job_card_wise_machine_usage where jobcard_no=".$prev_jobcard_no_txt.";";
			executed_sql($sql);
			$sql="delete from journal_details where link_key='JC_".$prev_jobcard_no_txt."';";
			executed_sql($sql);

	$sql = "INSERT INTO jobcard_master(jobcard_no,jobcard_date, customer_code, customer_type, customer_name, customer_addr1, customer_addr2, customer_city,customer_state_code,customer_state, customer_gst_no, customer_mobile_no, approximate_amount, is_adv_in_gpay, advance_gpay, is_adv_in_cash, advance_cash, advance_amount, created_dt_tm, created_by,remarks,delivery) VALUES (".$prev_jobcard_no_txt.",";
	}
	else
	{
		$sql = "INSERT INTO jobcard_master(jobcard_date, customer_code, customer_type, customer_name, customer_addr1, customer_addr2, customer_city,customer_state_code,customer_state, customer_gst_no, customer_mobile_no, approximate_amount, is_adv_in_gpay, advance_gpay, is_adv_in_cash, advance_cash, advance_amount, created_dt_tm, created_by,remarks,delivery) VALUES (";
	}
	$sql.="'".$just_date."',";


	if($customer_type_txt=="WalkIn")
	{
		$sql.="NULL,";
		$sql.="'".$customer_type_txt."',";
		$sql.="'','','','',NULL,'','','',";
	}
	elseif($customer_type_txt=="General")
	{
		$sql.="NULL,";
		$sql.="'".$customer_type_txt."',";
		$sql.="'".$customer_name_txt."',";
		$sql.="'".$customer_addr1_txt."',";
		$sql.="'".$customer_addr2_txt."',";
		$sql.="'".$customer_city_txt."',";
		$sql.="'".$customer_state_code_txt."',";
		$sql.="'".$customer_state_txt."',";
		$sql.="'".$customer_gst_no_txt."',";
		$sql.="'".$customer_mobile_no_txt."',";
	}
	elseif($customer_type_txt=="Credit")
	{
		$sql.="'".$credit_customer_code_txt."',";
		$sql.="'".$customer_type_txt."',";
		$sql.="'".$customer_name_txt."',";
		$sql.="'".$customer_addr1_txt."',";
		$sql.="'".$customer_addr2_txt."',";
		$sql.="'".$customer_city_txt."',";
		$sql.="'".$customer_state_code_txt."',";
		$sql.="'".$customer_state_txt."',";
		$sql.="'".$customer_gst_no_txt."',";
		$sql.="'".$customer_mobile_no_txt."',";
	}
	$sql.=(float)$_POST["approximate_amount_txt"].",";
	$sql.=($is_adv_in_gpay_txt ? 1 : 0).",";
	$advance_gpay_txt=(float)$_POST["advance_gpay_txt"];
	if($advance_gpay_txt=="")
	{
		$sql.="NULL,";
	}
	else
	{
		$sql.=(float)$advance_gpay_txt.",";
	}
	$sql.=($is_adv_in_cash_txt ? 1 : 0).",";
	$advance_cash_txt=(float)$_POST["advance_cash_txt"];
	if($advance_cash_txt=="")
	{
		$sql.="NULL,";
	}
	else
	{
		$sql.=(float)$advance_cash_txt.",";
	}
	$advance_amount_txt=$_POST["advance_amount_txt"];
	if($advance_amount_txt=="")
	{
		$sql.="NULL,";
	}
	else
	{
		$sql.=(float)$advance_amount_txt.",";
	}
	$sql.="'".$dt."','".$actual_user."','".$remarks_txt."','".$delivery_txt."');";

	$jobcard_id_txt =0;
	if(mysqli_query($connection,$sql))
	{
		$jobcard_id_txt = mysqli_insert_id($connection);
		$jobcard_no_txt=$prev_jobcard_no_txt;
		if($prev_jobcard_no_txt==0)
		{
			$sql="SELECT max(jobcard_no) as max_jobcard_no FROM jobcard_master;";
			$jobcard_no_txt=1;
			if($query=mysqli_query($connection,$sql))
			{
				if(mysqli_num_rows($query)>0)
				{
					if($row=mysqli_fetch_array($query))
					{
						if(!is_null($row["max_jobcard_no"]))
						{
							$jobcard_no_txt=$row["max_jobcard_no"]+1;
						}
					}
				}
			}

			$sql="update jobcard_master set jobcard_no =".$jobcard_no_txt;
			$sql.=" where jobcard_id=".$jobcard_id_txt.";";
			mysqli_query($connection,$sql);
		}
		else
		{
			$sql="update jobcard_master set ";
			if(!is_null($actual_job_card_closed)) $sql.="job_card_closed=".$actual_job_card_closed;
			if(!is_null($actual_jobcard_closed_by)) $sql.=",jobcard_closed_by='".$actual_jobcard_closed_by."'";
			if(!is_null($actual_job_closed_dt_tm)) $sql.=",job_closed_dt_tm='".$actual_job_closed_dt_tm."'";
			if(!is_null($actual_job_card_quotation_no)) $sql.=",job_card_quotation_no='".$actual_job_card_quotation_no."'";
			if(!is_null($actual_job_card_invoice_no)) $sql.=",job_card_invoice_no='".$actual_job_card_invoice_no."'";
			if(!is_null($actual_job_card_closed_on)) $sql.=",job_card_closed_on='".$actual_job_card_closed_on."'";
			if(!is_null($actual_void_job_card)) $sql.=",void_job_card='".$actual_void_job_card."'";
			if(!is_null($actual_balance_gpay)) $sql.=",balance_gpay='".$actual_balance_gpay."'";
			if(!is_null($actual_balance_cash)) $sql.=",balance_cash='".$actual_balance_cash."'";
			if(!is_null($actual_balance_received_total)) $sql.=",balance_received_total='".$actual_balance_received_total."'";
			$sql.=" where jobcard_no=".$prev_jobcard_no_txt.";";
			log_this($sql);
			mysqli_query($connection,$sql);
		}
		//inserting into jobcard_details table starts here

		for($i=0;$i<count($job_details_array);$i++)
		{
			$product_code_txt=$job_details_array[$i]['product_code_txt'];
			$sql = "INSERT INTO jobcard_details(jobcard_id,jobcard_no, job_type, job_detail_xml, product_code, machine_code, machine_detail, job_in_detail, 1st_copy_nos, addl_copy_nos,piece_rate_1,piece_rate_2, is_front_and_back, ups_count, sheet_count, total_qty, value_amount,value_discountable,piece_rate_discounted_1,piece_rate_discounted_2) VALUES (";
			$total_qty_txt=(int)$job_details_array[$i]['total_qty_txt'];
			if($job_details_array[$i]['is_front_and_back_txt']==1) $total_qty_txt/=2;
			$sql.="'".$jobcard_id_txt ."','".$jobcard_no_txt ."','".$job_details_array[$i]['job_type']."','".serialize($job_details_array[$i])."','".$job_details_array[$i]['product_code_txt']."','".$job_details_array[$i]['machine_code_txt']."','','','".$job_details_array[$i]['first_copy_nos_txt']."','".$job_details_array[$i]['addl_copy_nos_txt']."','".$job_details_array[$i]['first_copy_rate_txt']."','".$job_details_array[$i]['addl_copy_rate_txt']."','".$job_details_array[$i]['is_front_and_back_txt']."','".$job_details_array[$i]['noOfUps']."','".$total_qty_txt."','".$job_details_array[$i]['total_qty_txt']."','".$job_details_array[$i]['value']."','".$job_details_array[$i]['value_discountable']."','".$job_details_array[$i]['first_copy_rate_discounted_txt']."','".$job_details_array[$i]['addl_copy_rate_discounted_txt']."')";

			mysqli_query($connection,$sql);

			$j_d_array=explode("~", $job_details_array[$i]['prod_mat_qty']);
			$machine_usage_count=(int)$job_details_array[$i]['total_qty_txt'];
			if(strlen($job_details_array[$i]['prod_mat_qty'])>0)
			foreach ($j_d_array as $jd_ary)
			{
			$sql="INSERT INTO `job_card_wise_material_usage`( `jobcard_no`, `material_code`, `machine_code`, `jobcard_date`, `usage_count`) VALUES (";

			$total_qty_txt=(int)$job_details_array[$i]['total_qty_txt'];
			if(!($job_details_array[$i]['first_copy_rate_txt'] >0 && $job_details_array[$i]['addl_copy_rate_txt']>0))
			{
				if($job_details_array[$i]['first_copy_rate_txt'] >0)
				{
					$total_qty_txt = $job_details_array[$i]['value']/$job_details_array[$i]['first_copy_rate_txt'];
				}
			}
			if($job_details_array[$i]['is_front_and_back_txt']==1) $total_qty_txt/=2;

			$material_id=$jd_ary;
			$pos=strpos($material_id,",");
			if($pos)
			{
				$multi_factor =(float) substr($material_id,$pos+1);
				$material_id = substr($material_id,0,$pos);
				$total_qty_txt *= $multi_factor;
				$total_qty_txt = ceil($total_qty_txt);
				if($multi_factor!=1)
				{
					$machine_usage_count=$total_qty_txt;
					if(strpos($product_code_txt,"DOUBLE")!==False)
					{
						$machine_usage_count=$total_qty_txt*2;
					}
				}
			}

			$sql.="'".$jobcard_no_txt ."','".$material_id."','".$job_details_array[$i]['machine_code_txt']."','".$just_date."','".$total_qty_txt."');";
			if($material_id!="")
			{
				mysqli_query($connection,$sql);
			}
			}

			if($job_details_array[$i]['machine_code_txt']!="")
			{
			$sql="INSERT INTO `job_card_wise_machine_usage`( `jobcard_no`, `machine_code`,material_code, `jobcard_date`, `usage_count`) VALUES (";
			$sql.="'".$jobcard_no_txt ."','".$job_details_array[$i]['machine_code_txt']."','".$job_details_array[$i]['product_code_txt']."','".$just_date."','".$machine_usage_count."');";
			mysqli_query($connection,$sql);
			}
		}

		//inserting into jobcard_details table ends here
		echo "Success :".$jobcard_no_txt;

		// SARAVANA - START (Advance Payment Feature - link advance to JC after save)
		$linked_advance_id = isset($_POST['linked_advance_id']) ? (int)$_POST['linked_advance_id'] : 0;
		if ($linked_advance_id > 0) {
			mysqli_query($connection,
				"UPDATE advance_payment_master SET is_linked=1, linked_jobcard_no=$jobcard_no_txt
				 WHERE advance_id=$linked_advance_id AND is_linked=0"
			);
			mysqli_query($connection,
				"UPDATE journal_details SET link_key='ADV_{$linked_advance_id}_JC_{$jobcard_no_txt}',
				 description=CONCAT(description,' -> JC:$jobcard_no_txt')
				 WHERE link_key='ADV_{$linked_advance_id}'"
			);
		}
		// SARAVANA - END
	}
	else
	{
		echo "Failed : Something Went Wrong...";
	}

	//Posting to Journal Starts Here
	//Only Walkin and General Customers will get Posted here
	//Credit Customers will get posted only while closing a JobCard (since Discount to be considered)
	if($customer_type_txt=="WalkIn" || $customer_type_txt=="General")
	{
		$advance_amount_txt= $advance_cash_txt+$advance_gpay_txt;
		if($advance_amount_txt>0)
		{
			if($advance_cash_txt>0)
				post_journal_single_entry($dt,'CASH','Advance Cash for JC:'.$jobcard_no_txt,$advance_cash_txt,'JC_'.$jobcard_no_txt);
			if($advance_gpay_txt>0)
			{
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
	//Posting to Journal Ends Here
}
else
{
	echo $result;
}
?>