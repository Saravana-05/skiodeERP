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
		$total_amount=0;
		$sql="SELECT * FROM  wastage_master where 1=1";  
		//echo  "ACH".$account_head."-----" ;
		if($from != "")
		{
			$sql=$sql." and  wastage_date between '".$from." 00:00:00' and  '".$to." 23:59:59' ";
		}
		
		$result='<div class="row"><div class="col-10"  id="printable_div">'; 
		if($query=mysqli_query($connection,$sql))
		{
			$result.='<table class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
				$result.='<thead class="table-dark">';
				$result.='<tr> ';
				$result.='<th>SNo</th>';
				$result.='<th>Date</th>';
				$result.='<th>Ref no</th>'; 
				$result.='<th>Machine</th>'; 
				$result.='<th>Stock Name</th>'; 
				$result.='<th>Qty</th>';
				$result.='<th>Value</th>'; 
				$result.='<th>Remarks</th>'; 
				$result.='<th>Creatd by</th>'; 
				$result.='<th>Time</th>'; 
				$result.='<th class="noPrint">Actions</th>'; 
 
				$result.='</tr>';
				$result.='</thead>';
				$result.='<tbody>';
				$i=1;
				$total_qty=0;
				$total_value=0;
			if(mysqli_num_rows($query)>0)
			{ 
				 while($row=mysqli_fetch_array($query)) 
				 {
					$result.='<tr>';
					$result.='<td>'.$i.'</td>';
					$result.='<td>'.date("d-m-Y", strtotime($row["wastage_date"])).'</td>';
					$result.='<td>'.$row["ref_no"].'</td>'; 
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
					$result.='<td>'.$machine_name.'</td>'; 
					$result.='<td>'.$stock_name.'</td>'; 
					$result.='<td>'.$row["qty"].'</td>'; 
					$result.='<td>'.$row["value"].'</td>'; 
					$result.='<td>'.$row["remarks"].'</td>'; 
					$result.='<td>'.$row["created_by"].'</td>'; 
					$result.='<td>'.date("h:i:s", strtotime($row["wastage_date"])).'</td>'; 
					$result.='<td><button class="btn btn-sm btn-danger deleteBtn mt-1 noPrint" onclick="fnWastageDelete('.$row["wastage_id"].')" >Delete</button></td>'; 
					$total_qty=$total_qty+$row["qty"];
					$total_amount=$total_amount+$row["value"];
					/*if($row["amount"] >0)
					{
						$result.='<td>'.$row["amount"].' Cr</td>'; 
					}
					else
					{
						$result.='<td>'.abs($row["amount"]).' Dr</td>'; 
					} */
					$result.='</tr>';
					
					$i++;
				}			
			}
/* 			$result.='</tbody>';
			$result.='<tfoot>';
			$result.='<tr>';
			$result.='<th colspan="5" style="text-align:right">Total:</th>';
			$result.='<th></th>';
			$result.='<th></th>';
			$result.='<th></th>';
			$result.='<th></th>';
			$result.='<th></th>';
			$result.='<th></th>';
			$result.='</tr>';
			$result.='</tfoot>'; */
			
		} 
		$sql="select * from job_card_wise_material_spillage where jobcard_date between '".$from."' and '".$to."' ";
		$sql="SELECT a.*,b.created_by,b.job_work_completed_on_dttm,b.job_work_completion_marked_by FROM `job_card_wise_material_spillage` a inner join jobcard_master b on a.jobcard_no=b.jobcard_no where a.jobcard_date between '".$from."' and '".$to."' ";
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
					$result.='<td>'.$row["spillage_count"].'</td>';
					
					$result.='<td>'.$row["spillage_count"]*$purchase_price.'</td>';
					$result.='<td>JC Spillage</td>';
					$result.='<td>'.$row["job_work_completion_marked_by"].'</td>';
					$result.='<td>'.date("h:i:s", strtotime($row["job_work_completed_on_dttm"])).'</td>';
					$result.='<td></td></tr>';
				}
			}
		}
		$result.='</tbody></table>';
				$result.='</div>';
	$result.='<div class="col-2 text-bg-light" style="border-left:1px inset #ccc;" >';
	$result.='<div class="text-center bg-success text-white"><h5 class="mb-0">SUMMARY</h5></div>';
	$result.='<div  id="card_1">';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">'."Qty : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($total_qty,2)."</div></td></tr>"; 
	$result.='<tr><td align="right" width="50%">'."Value : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($total_amount,2)."</div></td></tr>"; 
	$result.='</table>';
	$result.='</div>';
			$result.='<div class="col-12"  style="border:0px solid #ccc; padding:5px;">';
	$result.='<br> <center><button type="button" class="btn btn-primary btn-sm noPrint" onclick="printLedgerDiv('."'printable_div'".')">PRINT</button><br><br>';
	$result.='<button type="button" class="btn btn-secondary btn-sm" onclick="fnSideMenu(1)">Close</button></center>';
	$result.='</div>';
	$result.='</div>';
		
	}
	else if($_POST['type'] == "add")
	{
		$dt=date('Y-m-d');
		$sql="INSERT INTO `wastage_master`( `ref_no`, `wastage_date`, `machine_code`, `material_code`, `qty`, `value`, `remarks`, `created_by` ) VALUES ( '".$_POST["ref_no"]."','".date("Y-m-d H-i-s")."','".$_POST["machine_code"]."','".$_POST["material_code"]."','".$_POST["qty"]."','".$_POST["value"]."','".$_POST["remarks"]."','".$_POST["created_by"]."')";
		 
		mysqli_query($connection,$sql);
		/*$sql="INSERT INTO `journal_details`( `dt`, `achead`, `description`, `amount`, `link_key`) VALUES ";
		$val_sql="";
		$val_sql.="('".date("Y-m-d", strtotime($_POST["wastage_date"]))."','".$_POST["account_head"]."','".$_POST["amount"]."','RCPTV_".$_POST["wastage_id"]."');";
		if($val_sql!="") $val_sql.=",";
		$val_sql.="('".date("Y-m-d", strtotime($_POST["wastage_date"]))."','".$_POST["customer_code"]."','-".$_POST["amount"]."','RCPTV_".$_POST["wastage_id"]."');";
		$sql.=$val_sql;
		mysqli_query($connection,$sql);*/
		$result = "success";
	}
	else if($_POST['type'] == "del")
	{
		$sql="DELETE FROM  wastage_master where wastage_id='".$_POST['w_id']."'";
		mysqli_query($connection,$sql); 
		$result = "success";
	}
}
echo $result;
?>