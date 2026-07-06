<?php
session_start();
include("../connect_db.php");
$result=""; 
$result="<style>th{text-align:center;}</style>";
if(isset($_POST['from']))
{
		$from = date("Y-m-d", strtotime($_POST['from']));
		$to = date("Y-m-d", strtotime($_POST['to'])); 
		$mac_ar=array();
		$sql="select * from machine_master";
		if($qry=mysqli_query($connection,$sql))
		{
			while($row=mysqli_fetch_array($qry))
			{
				$mac_ar[$row["machine_name"]]=0;
			}
		}
		
		$sql="SELECT * FROM  wastage_master where wastage_date between '".$from." 00:00:00' and '".$to." 23:59:59' ";  
		$user_name_txt="all";
		$machine_code_txt="all";
		
		if(isset($_POST["user_name_txt"])) $user_name_txt=$_POST["user_name_txt"];	
		if(isset($_POST["machine_code_txt"])) $machine_code_txt=$_POST["machine_code_txt"];
		
		if($user_name_txt!="all")
				$sql.=" and created_by='".$user_name_txt."' ";
		if($machine_code_txt!="all")
				$sql.=" and machine_code='".$machine_code_txt."' ";
			
		//$sql="SELECT a.jobcard_no,b.jobcard_date,b.created_by,a.material_code,a.spillage_count FROM `job_card_wise_material_spillage` a inner join jobcard_master b on a.jobcard_no=b.jobcard_no where b.jobcard_date between '".$from." 00:00:00' and '".$to." 23:59:59' ;";
		//echo  "ACH".$account_head."-----" ;
	 $result.='<div class="row"><div class="col-10" id="printable_div">';
		 $tot_cnt=0;
		 $total_amount=0;
		if($query=mysqli_query($connection,$sql))
		{
			$result.='<table class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
				$result=$result.'<thead class="table-dark">';
				$result=$result.'<tr> ';
				$result=$result.'<th>SNo</th>';
				$result=$result.'<th>Date</th>';
				$result=$result.'<th>Ref no</th>'; 
				$result=$result.'<th>Machine</th>'; 
				$result=$result.'<th>Stock Name</th>'; 
				$result=$result.'<th>Qty</th>';
				$result=$result.'<th>Value</th>'; 
				$result=$result.'<th>Remarks</th>'; 
				$result=$result.'<th>Creatd by</th>'; 
				$result=$result.'<th>Time</th>'; 
				$result=$result.'<th class="noPrint">Actions</th>'; 
 
				$result=$result.'</tr>';
				$result=$result.'</thead>';
				$result=$result.'<tbody>';
				$i=1;
			if(mysqli_num_rows($query)>0)
			{ 
				 while($row=mysqli_fetch_array($query)) 
				 {
					$result=$result.'<tr>';
					$result=$result.'<td>'.$i.'</td>';
					$result=$result.'<td>'.date("d-m-Y", strtotime($row["wastage_date"])).'</td>';
					$result=$result.'<td>'.$row["ref_no"].'</td>'; 
					$machine_name = ""; //machine_code
					$stock_name = ""; //material_code
					$sql="SELECT machine_name from machine_master where machine_code='".$row['machine_code']."'"; 
					if($query1=mysqli_query($connection,$sql))
					{
						if(mysqli_num_rows($query1)>0)
						{ 
							if($row1=mysqli_fetch_array($query1)) 
							{
								$machine_name = $row1["machine_name"]; 
							}
						} 
					}
					$sql="SELECT material_name from material_master where material_code='".$row['material_code']."'"; 
					if($query1=mysqli_query($connection,$sql))
					{
						if(mysqli_num_rows($query1)>0)
						{ 
							if($row1=mysqli_fetch_array($query1)) 
							{
								$stock_name = $row1["material_name"]; 
							}
						} 
					}	
					$result=$result.'<td>'.$machine_name.'</td>'; 
					$result=$result.'<td>'.$stock_name.'</td>'; 
					$result=$result.'<td style="text-align:right;">'.$row["qty"].'</td>'; 
					$result=$result.'<td style="text-align:right;">'.$row["value"].'</td>'; 
					$result=$result.'<td>'.$row["remarks"].'</td>'; 
					$result=$result.'<td>'.$row["created_by"].'</td>'; 
					$result=$result.'<td>'.date("h:i:s", strtotime($row["wastage_date"])).'</td>'; 
					$result=$result.'<td class="noPrint"><button class="btn btn-sm btn-danger deleteBtn mt-1 noPrint" onclick="fnWastageDelete('.$row["wastage_id"].')" >Delete</button></td>'; 
					$tot_cnt=$tot_cnt + $row["qty"];
					$total_amount=$total_amount + $row["value"];
					/*if($row["amount"] >0)
					{
						$result=$result.'<td>'.$row["amount"].' Cr</td>'; 
					}
					else
					{
						$result=$result.'<td>'.abs($row["amount"]).' Dr</td>'; 
					} */
					$result=$result.'</tr>';
					$i++;
				}			
			}
			
		$sql="select * from job_card_wise_material_spillage where jobcard_date between '".$from."' and '".$to."' ";
		$sql="SELECT a.*,b.created_by,b.job_work_completed_on_dttm,b.job_work_completion_marked_by FROM `job_card_wise_material_spillage` a inner join jobcard_master b on a.jobcard_no=b.jobcard_no where a.jobcard_date between '".$from."' and '".$to."' ";
		if($user_name_txt!="all")
				$sql.=" and b.created_by='".$user_name_txt."' ";
		if($machine_code_txt!="all")
				$sql.=" and a.machine_code='".$machine_code_txt."' ";		
		//echo $sql;
		if($query=mysqli_query($connection,$sql))
		{
			if(mysqli_num_rows($query)>0)
			{
/* 				$result='<table class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
				
				$result.='<thead class="table-dark">';
				$result.='<tr> ';
				$result.='<th>SNo</th>';
				$result.='<th>Date</th>';
				$result.='<th>JC no</th>'; 
				$result.='<th>Machine</th>'; 
				$result.='<th>Stock Name</th>'; 
				$result.='<th>Qty</th>';
				$result.='<th>Value</th>'; 
				$result.='<th>Remarks</th>'; 
				$result.='<th>Creatd by</th>'; 
				$result.='<th>Time</th>'; 
				
 
				$result.='</tr>';
				$result.='</thead>';
				$result.='<tbody>';	 */
				//$i=1;
				while($row=mysqli_fetch_array($query))
				{
					$result.='<tr>';
					$result.='<td>'.$i++.'</td>';					
					$result.='<td>'.date("d-m-Y", strtotime($row["job_work_completed_on_dttm"])).'</td>';
					$result.='<td>'.$row["jobcard_no"].'</td>';
					$sql ="select * from material_master where material_code='".$row["material_code"]."';";
					$material_name=$row["material_code"];
					$purchase_price=1.0;
					if($matQry=mysqli_query($connection,$sql))
					{
						if($matRow=mysqli_fetch_array($matQry))
						{
							$material_name=$matRow["material_name"];
							$purchase_price=$matRow["purchase_price"];
						}
					}
					$result.='<td>'.$row["machine_code"].'</td>';
					$result.='<td>'.$material_name.'</td>';
					$result.='<td style="text-align:right;">'.$row["spillage_count"].'</td>';
					$spill_value=ind_money($row["spillage_count"]*$purchase_price,2);
					$result.='<td style="text-align:right;">'.$spill_value.'</td>';
					$tot_cnt=$tot_cnt + $row["spillage_count"];
					$total_amount=$total_amount + $spill_value;					
					$result.='<td>JC Spillage</td>';
					$result.='<td>'.$row["job_work_completion_marked_by"].'</td>';
					$result.='<td>'.date("h:i:s", strtotime($row["job_work_completed_on_dttm"])).'</td>';
					$result.='<td></td></tr>';
				}
			}
		}			
			$result=$result.'</tbody>';
			$result=$result.'<tfoot>';
			$result=$result.'<tr>';
			$result=$result.'<th colspan="5" style="text-align:right">Total:</th>';
			$result=$result.'<th style="text-align:right;">'.$tot_cnt.'</th>';
			$result=$result.'<th></th>';
			$result=$result.'<th></th>';
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
	$result.='<div  id="card_1">';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">'."Qty : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($tot_cnt)."</div></td></tr>";
	$result.='<tr><td align="right">'."Amount : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($total_amount,2)."</div></td></tr>"; 
	$result.='</table>';
	$result.='</div>';
			$result.='<div class="col-12"  style="border:0px solid #ccc; padding:5px;">';
	$result.='<br> <center><button type="button" class="btn btn-primary btn-sm" onclick="printWastageReportDiv('."'printable_div'".')">PRINT</button><br><br>';
	$result.='<button type="button" class="btn btn-secondary btn-sm" onclick="fnSideMenu(1)">Close</button></center>';
	$result.='</div>';
	$result.='</div>';
}
echo $result;
?>