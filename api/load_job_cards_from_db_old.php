<?php
session_start();
include_once "../connect_db.php";
$jcnos=$_POST["jcnos"];
$jcAry=explode(",",$jcnos);
$jd_xml=[];
$advance_gpay_txt=0.0;
$advance_cash_txt=0.0;
$approximate_amount_txt=0.0;
$max_value_discountable=0.0;
$remarks_txt="";
$jobcard_date_txt = "";
$actual_job_card_quotation_no="";
foreach($jcAry as $jcno )
{
	$mas_sql="select * from jobcard_master where jobcard_no=".$jcno.";";
	if($mas_qry=mysqli_query($connection,$mas_sql))
	{
		if($mas_row=mysqli_fetch_array($mas_qry))
		{
			$jobcard_date_txt = date("d-m-Y",strtotime($mas_row["created_dt_tm"]));
			$advance_gpay_txt+=$mas_row["advance_gpay"];
			$advance_cash_txt+=$mas_row["advance_cash"];	
			$actual_job_card_quotation_no=$mas_row['job_card_quotation_no'];			
			$approximate_amount_txt+=$mas_row["approximate_amount"];	
			if($mas_row["remarks"]!="")
			$remarks_txt=$mas_row["remarks"];		
			$delivery_txt=$mas_row["delivery"];		
		}
	}
	$det_sql="select * from jobcard_details where jobcard_no=".$jcno.";";
	if($det_qry=mysqli_query($connection,$det_sql))
		{
			while($det_row=mysqli_fetch_array($det_qry))
			{
				$max_value_discountable += $det_row["value_discountable"];	
				$sql="select * from product_master where product_code='".$det_row["product_code"]."'";
				$gst_percentage_txt="";
				if($prod_qry=mysqli_query($connection,$sql))
				{
					if($prod_row=mysqli_fetch_array($prod_qry))
					{
						$gst_percentage_txt=$prod_row["gst_percentage"];
					}
						
				}
				//$det_row["job_detail_xml"]["gst_percentage_txt"]=$gst_percentage_txt;
				$job_detail_xml_ary=unserialize($det_row["job_detail_xml"]);
				$job_detail_xml_ary["gst_percentage_txt"]=$gst_percentage_txt;
				array_push($jd_xml, $job_detail_xml_ary);
				//echo $det_row["job_detail_xml"]."~";
			}
		}
		//echo json_encode($jd_xml);
		
}
//gst_extra_chk
$gst_extra_chk=0;
$gst_tax_amount=0;
$sql="SELECT is_gst_extra,	gst_tax_amount  FROM sales_quotation_master where 	jobcard_nos in (".$jcnos.")";
 
if($query=mysqli_query($connection,$sql))
{
	if(mysqli_num_rows($query)>0)
	{
		if($row=mysqli_fetch_array($query))
		{
			if(!is_null($row["is_gst_extra"]))
			{
				$gst_extra_chk=$row["is_gst_extra"];
				$gst_tax_amount=$row["gst_tax_amount"];
			}
		}
	}
}
$jcno =$jcAry[0];
$ans=[];
$mas_sql="select * from jobcard_master where jobcard_no=".$jcno.";";
if($mas_qry=mysqli_query($connection,$mas_sql))
{
	if($mas_row=mysqli_fetch_array($mas_qry))
	{
		$ans+=["jobcard_date_txt" => $jobcard_date_txt];
		$ans+=["customer_code_txt" => $mas_row["customer_code"]];
		$ans+=["customer_type_txt" => $mas_row["customer_type"]];
		$ans+=["customer_name_txt" => $mas_row["customer_name"]];
		$ans+=["customer_addr1_txt" => $mas_row["customer_addr1"]];
		$ans+=["customer_addr2_txt" => $mas_row["customer_addr2"]];
		$ans+=["customer_city_txt" => $mas_row["customer_city"]];
		
		$ans+=["customer_state_code_txt" => $mas_row["customer_state_code"]];
		$ans+=["customer_state_txt" => $mas_row["customer_state"]];
		$ans+=["customer_gst_no_txt" => $mas_row["customer_gst_no"]];
		$ans+=["customer_mobile_no_txt" => $mas_row["customer_mobile_no"]];
		$ans+=["approximate_amount_txt" => $approximate_amount_txt];
		$ans+=["advance_gpay_txt" => $advance_gpay_txt];
		$ans+=["advance_cash_txt" => $advance_cash_txt];
		$ans+=["advance_amount_txt" => $advance_cash_txt+$advance_gpay_txt];
		$ans+=["max_value_discountable" => $max_value_discountable];
		$ans+=["remarks_txt"=>$remarks_txt];
		$ans+=["delivery_txt"=>$delivery_txt];
		$ans+=["gst_extra_chk"=>$gst_extra_chk];
		$ans+=["gst_tax_amount"=>$gst_tax_amount];
		$ans+=["job_card_quotation_no"=>$actual_job_card_quotation_no];
		
		
	}
}
echo json_encode($ans);
echo "~>";
echo json_encode($jd_xml);
?>