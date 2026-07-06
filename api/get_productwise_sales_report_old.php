<?php
session_start();
include_once '../connect_db.php';
$result=""; 
if(isset($_POST['from']))
{
	$from = date("Y-m-d", strtotime($_POST['from']));
	$to = date("Y-m-d", strtotime($_POST['to']));
	$user_name_txt=$_POST["user_name_txt"];
	$job_type_txt=$_POST["job_type_txt"];	
	$show_summary_chk=$_POST['show_summary_chk'];
	if($show_summary_chk)
	{
		$sql="SELECT c.product_code,c.product_name,b.job_type,sum(b.total_qty) as total_qty, sum(b.value_amount) as selling_price, a.created_by FROM jobcard_master a join jobcard_details b on a.jobcard_id=b.jobcard_id join product_master c on b.product_code = c.product_code where a.jobcard_date between '".$from."' and '".$to."' and a.void_job_card=0";
		
		if($user_name_txt!="all")
		{
			$sql.=" and a.created_by='".$_POST["user_name_txt"]."'";
		}
		if($job_type_txt!="all")
		{
			$sql.=" and b.job_type='".$_POST["job_type_txt"]."'";
		}		
		$sql.=" group by c.product_code;";
		//log_this($sql);
	}
	else
	{
		$sql="SELECT a.jobcard_no,a.job_card_quotation_no,c.product_name,b.job_type,b.total_qty, b.value_amount as selling_price, a.created_by
		FROM jobcard_master a join jobcard_details b on a.jobcard_id=b.jobcard_id 
		join product_master c on b.product_code = c.product_code 
		where a.jobcard_date between '".$from."' and '".$to."' and a.void_job_card=0";   
		if($user_name_txt!="all")
		{
			$sql.=" and a.created_by='".$_POST["user_name_txt"]."'";
		}
		if($job_type_txt!="all")
		{
			$sql.=" and b.job_type='".$_POST["job_type_txt"]."'";
		}
	}
	//log_this($sql);
	
 
	$result.='<div class="row"><div class="col-10" id="printable_div">';
 
	if($query=mysqli_query($connection,$sql))
	{
		$result.='<table class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
			$result.='<thead class="table-dark">';
			$result.='<tr> ';
			$result.='<th>S.No</th>';
			$result.='<th>Stock Name</th>'; 
			$result.='<th>Stock Group</th>';
			$result.='<th>Total Qty</th>';
			$result.='<th>Cost Value</th>';
			if(!$show_summary_chk) $result.='<th>Discount</th>';
			$result.='<th>Username</th>';
			$result.='</tr>';
			$result.='</thead>';
			$result.='<tbody>';
			$i=1;
		if(mysqli_num_rows($query)>0)
		{ 
			 while($row=mysqli_fetch_array($query)) {
					 
						$result.='<tr>';
						$result.='<td>'.$i.'</td>'; 
						$result.='<td>'.$row["product_name"].'</td>'; 
						$result.='<td>'.$row["job_type"].'</td>';
						$result.='<td>'.$row["total_qty"].'</td>';
						$result.='<td>'.$row["selling_price"].'</td>';
						if(!$show_summary_chk)
						{
							$sqno=$row["job_card_quotation_no"];
							$discount="";
							if(!empty($sqno))
							{
							$sq_sql="select * from sales_quotation_master where quotation_no =".$sqno.";";
							
							if($sq_qry=mysqli_query($connection,$sq_sql))
							{
								if($sq_row=mysqli_fetch_array($sq_qry))
								{
									$discount=$sq_row["discount"];
									if($discount==0)$discount="";
								}
							}
							}
							$result.='<td>'.$discount.'</td>';
						}
						$result.='<td>'.$row["created_by"].'</td>'; 
						  
						$result.='</tr>';
						
						$i++;
					 
			  }
			
		}
		 
		$result.='</tbody>';
		$result.='</table>';
	} 
	$result.='</div>';
	$result.='<div class="col-2 text-bg-light" style="border-left:1px inset #ccc;"  >';
	$result.='<div class="text-center bg-success text-white"><h5 class="mb-0"> SUMMARY</h5></div>';
	$result.='<table width="100%" style="font-weight:bold;">';
	
	$summary_sql="SELECT b.job_type,sum(b.value_amount) as amt,count(b.value_amount) as cnt FROM jobcard_master a join jobcard_details b on a.jobcard_id=b.jobcard_id  where a.jobcard_date between '".$from."' and '".$to."' ";
	if($user_name_txt!="all")
	{
		$summary_sql.=" and a.created_by='".$_POST["user_name_txt"]."'";
	}
	if($job_type_txt!="all")
	{
		$summary_sql.=" and b.job_type='".$_POST["job_type_txt"]."'";
	}	
	$summary_sql.="   group by b.job_type ";
	
	$total = 0;
	if($summary_query=mysqli_query($connection,$summary_sql))
	{
		if(mysqli_num_rows($summary_query)>0)
		{ 
			while($summary_row=mysqli_fetch_array($summary_query)) 
			{
				 $result.='<tr><td align="right" width="50%">'.$summary_row['job_type'].'</td><td>('.$summary_row['cnt'].')</td><td><div style="border:1px solid black;color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($summary_row['amt'],2)."</div></td></tr>";
				 $summary_row_amt=$summary_row['amt'];
				 $total += $summary_row_amt;
			}
			
		}
		$result.='<tr><td align="right" style="color:green;">'."Total : ".'</td><td>&nbsp;</td><td><div style="border:1px solid black;color:green;background-color:white;text-align:right;padding:2px;">'.ind_money($total)."</div></td></tr>";
	}
	/*$result.='<tr><td align="right" width="50%">'."Cash : ".'</td><td><div style="border:1px solid black;color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($total_cash,2)."</div></td></tr>";
	$result.='<tr><td align="right">'."Credit : ".'</td><td><div style="border:1px solid black;color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($total_credit,2)."</div></td></tr>";
	$result.='<tr><td align="right">'."Online : ".'</td><td><div style="border:1px solid black;color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($total_online,2)."</div></td></tr>";
	*/
	$result.='</table>';
	$result.='<br><br><br><br><center>';
	$result.='<br> <center><button type="button" class="btn btn-primary btn-sm" onclick="printProductwiseDiv('."'printable_div'".')">PRINT</button><br><br>';
	$result.='<button type="button" class="btn btn-secondary btn-sm" onclick="fnSideMenu(1)">Close</button></center>';
	
	$result.='</center>';
	$result.='</div>';
	$result.='</div>';	
}
echo $result;
?>