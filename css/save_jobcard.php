<?php
session_start();
include_once '../connect_db.php';
$result="Failed";
$job_details_array= json_decode($_POST['allTableData'], true); 
/* print_r($array);
echo '----';
echo $array[0]['machine_code_txt']; */

$dt = date("Y-m-d H:i:s");
$just_date = date("Y-m-d");

$customer_type_txt = $_POST["customer_type_txt"];
$is_adv_in_cash_txt = $_POST["is_adv_in_cash_txt"];
$is_adv_in_cash_txt = false;
if(isset($_POST["is_adv_in_cash_txt"])) $is_adv_in_cash_txt = true;
$advance_cash_txt = (float)$_POST["advance_cash_txt"];
$is_adv_in_gpay_txt = false;
if(isset($_POST["is_adv_in_gpay_txt"])) $is_adv_in_gpay_txt = true;
$advance_gpay_txt = (float)$_POST["advance_gpay_txt"];


$sql = "INSERT INTO jobcard_master(jobcard_date, customer_code, customer_type, customer_name, customer_addr1, customer_addr2, customer_city,customer_state_code,customer_state, customer_gst_no, customer_mobile_no, approximate_amount, is_adv_in_gpay, advance_gpay, is_adv_in_cash, advance_cash, advance_amount, created_dt_tm, created_by) VALUES (";
$sql.="'".$just_date."',";
$customer_type_txt=$_POST["customer_type_txt"];

if($customer_type_txt=="WalkIn")
{
	$sql.="NULL,";
	$sql.="'".$customer_type_txt."',";
	$sql.="'','','','','','','','',";
}
elseif($customer_type_txt=="General")
{
	$sql.="NULL,";
	$sql.="'".$customer_type_txt."',";
	$sql.="'".$_POST["customer_name_txt"]."',";
	$sql.="'".$_POST["customer_addr1_txt"]."',";
	$sql.="'".$_POST["customer_addr2_txt"]."',";
	$sql.="'".$_POST["customer_city_txt"]."',";
	$sql.="'".$_POST["customer_state_code_txt"]."',";
	$sql.="'".$_POST["customer_state_txt"]."',";
	$sql.="'".$_POST["customer_gst_no_txt"]."',";
	$sql.="'".$_POST["customer_mobile_no_txt"]."',";
}
elseif($customer_type_txt=="Credit")
{
	$sql.="'".$_POST["credit_customer_code_txt"]."',";
	$sql.="'".$customer_type_txt."',";
	$sql.="'".$_POST["customer_name_txt"]."',";
	$sql.="'".$_POST["customer_addr1_txt"]."',";
	$sql.="'".$_POST["customer_addr2_txt"]."',";
	$sql.="'".$_POST["customer_city_txt"]."',";
	$sql.="'".$_POST["customer_state_code_txt"]."',";
	$sql.="'".$_POST["customer_state_txt"]."',";
	$sql.="'".$_POST["customer_gst_no_txt"]."',";
	$sql.="'".$_POST["customer_mobile_no_txt"]."',";	
}
$sql.=$_POST["approximate_amount_txt"].",";
$sql.=$_POST["is_adv_in_gpay_txt"].",";
$advance_gpay_txt=(float)$_POST["advance_gpay_txt"];
if($advance_gpay_txt=="")
{
	$sql.="NULL,";
}
else
{
	$sql.=(float)$advance_gpay_txt.",";
}
$sql.=$_POST["is_adv_in_cash_txt"].",";
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
$sql.="'".$dt."','".$_SESSION['user_name']."');";
file_put_contents("A_SQL.txt",$sql);
if(mysqli_query($connection,$sql))
{
	$jobcard_id_txt = mysqli_insert_id($connection);
	//echo $jobcard_id_txt."<br>";
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
	//echo $jobcard_no_txt;
	$sql="update jobcard_master set jobcard_no =".$jobcard_no_txt." where jobcard_id=".$jobcard_id_txt.";";
	//echo $sql;
	mysqli_query($connection,$sql);
	//inserting into jobcard_details table starts here
	
	for($i=0;$i<count($job_details_array);$i++)
	{
		$sql = "INSERT INTO jobcard_details(jobcard_id, job_type, job_detail_xml, product_code, machine_code, machine_detail, job_in_detail, 1st_copy_nos, addl_copy_nos,piece_rate_1,piece_rate_2, is_front_and_back, ups_count, sheet_count, total_qty, value_amount,value_discountable,piece_rate_discounted_1,piece_rate_discounted_2) VALUES (";
		$total_qty_txt=(int)$job_details_array[$i]['total_qty_txt'];
		if($job_details_array[$i]['is_front_and_back_txt']==1) $total_qty_txt/=2;
		$sql.="'".$jobcard_no_txt."','','".serialize($job_details_array[$i])."','".$job_details_array[$i]['product_code_txt']."','".$job_details_array[$i]['machine_code_txt']."','','','".$job_details_array[$i]['first_copy_nos_txt']."','".$job_details_array[$i]['addl_copy_nos_txt']."','".$job_details_array[$i]['first_copy_rate_txt']."','".$job_details_array[$i]['addl_copy_rate_txt']."','".$job_details_array[$i]['is_front_and_back_txt']."','".$job_details_array[$i]['noOfUps']."','".$total_qty_txt."','".$job_details_array[$i]['total_qty_txt']."','".$job_details_array[$i]['value']."','".$job_details_array[$i]['value_discountable']."','".$job_details_array[$i]['first_copy_rate_discounted_txt']."','".$job_details_array[$i]['addl_copy_rate_discounted_txt']."')";
		file_put_contents("jc_det.sql",$sql);
		mysqli_query($connection,$sql);
		
		if($job_details_array[$i]['machine_code_txt']!="")
		{
		$sql="INSERT INTO `job_card_wise_machine_usage`(`job_card_id`, `machine_code`, material_code,`jobcard_date`, `usage_count`) VALUES (";
		$sql.="'".$jobcard_no_txt."','".$job_details_array[$i]['machine_code_txt']."','".$job_details_array[$i]['product_code_txt']."','".$just_date."','".$job_details_array[$i]['total_qty_txt']."');";
		mysqli_query($connection,$sql); 
		}
		
		$j_d_array=explode("~", $job_details_array[$i]['prod_mat_qty']);
		if(strlen($job_details_array[$i]['prod_mat_qty'])>0)
		foreach ($j_d_array as $jd_ary)
		{		
		$sql="INSERT INTO `job_card_wise_material_usage`(`job_card_id`, `material_code`, `jobcard_date`, `usage_count`) VALUES (";
		
		$total_qty_txt=(int)$job_details_array[$i]['total_qty_txt'];	
		if($job_details_array[$i]['is_front_and_back_txt']==1) $total_qty_txt/=2;		

		$material_id=$jd_ary;
		$pos=strpos($material_id,",");
		if($pos)
		{
			$multi_factor =(float) substr($material_id,$pos+1);
			$material_id = substr($material_id,0,$pos);
			//echo $material_id ." ::: ".$multi_factor;
			$total_qty_txt *= $multi_factor;
		}
		$sql.="'".$jobcard_no_txt."','".$material_id."','".$just_date."','".$total_qty_txt."');";
		if($material_id!="")
			mysqli_query($connection,$sql);
		}		
	}
	
	//inserting into jobcard_details table ends here
	echo "Success :".$jobcard_no_txt;
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
		$sql="INSERT INTO `journal_details`( `dt`, `achead`, `description`, `amount`, `link_key`) VALUES ";
		$val_sql="";
		if($advance_cash_txt>0)
		$val_sql.="('".$dt."','CASH','Advance Cash for JC:".$jobcard_no_txt."',".$advance_cash_txt.",'JC_".$jobcard_no_txt."')";
		if($advance_gpay_txt>0)
		{
			if($val_sql!="") $val_sql.=",";
		$val_sql.="('".$dt."','GPAY','Advance GPAY for JC:".$jobcard_no_txt."',".$advance_gpay_txt.",'JC_".$jobcard_no_txt."');";
		}
		$sql.=$val_sql;
		mysqli_query($connection,$sql);
	}
}
//Posting to Journal Ends Here


?>