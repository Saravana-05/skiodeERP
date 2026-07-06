<?php
session_start();
include_once '../connect_db.php';
$result=""; 
if(isset($_POST['from']))
{
	$from = date("Y-m-d", strtotime($_POST['from']));
	$to = date("Y-m-d", strtotime($_POST['to']));
	$sql="SELECT a.*,b.* FROM job_card_wise_material_usage a 
	join material_master b  on a.material_code=b.material_code 	 
	where jobcard_date >='".$from."' and jobcard_date<='".$to."' ";  
	$user_name_txt=$_POST["user_name_txt"];
	if($user_name_txt!="all")
	{
		$sql.=" and c.created_by='".$_POST["user_name_txt"]."'";
	}
	$tot_wastage_qty=0;
	$tot_wastage_value=0;
	$tot_closing_qty=0;
	$tot_closing_value=0;
	$tot_inwards_qty=0;
	$tot_inwards_value=0;
	$tot_outwards_qty=0;
	$tot_outwards_value=0;
	$result.='<div class="row"><div class="col-10"  id="printable_div">';
	if($query=mysqli_query($connection,$sql))
	{
		$result.='<table class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
			$result=$result.'<thead class="table-dark">';
			$result=$result.'<tr> ';
			$result=$result.'<th>SNo</th>';
			$result=$result.'<th>Material Code</th>';  
			$result=$result.'<th>Material Name</th>';
			$result=$result.'<th>Opening Qty</th>';
			$result=$result.'<th>Opening Value</th>';
			$result=$result.'<th>Inwards Qty</th>';
			$result=$result.'<th>Inwards Value</th>';
			$result=$result.'<th>Outwards Qty</th>';
			$result=$result.'<th>Outwards Value</th>';
			$result=$result.'<th>Wastage Qty</th>';
			$result=$result.'<th>Wastage Value</th>';
			$result=$result.'<th>Closing Qty</th>';
			$result=$result.'<th>Closing Value</th>'; 
			$result=$result.'</tr>';
			$result=$result.'</thead>';
			$result=$result.'<tbody>';
			$i=1;
		if(mysqli_num_rows($query)>0)
		{ 
			 while($row=mysqli_fetch_array($query)) {
					$result=$result.'<tr>';
					$result=$result.'<td>'.$i.'</td>';
					$result=$result.'<td>'.$row["material_code"].'</td>'; 
					$result=$result.'<td>'.$row["material_name"].'</td>';
					$result=$result.'<td>'.$row["stock_in_hand"].'</td>'; 
					$result=$result.'<td>'.($row["stock_in_hand"] * $row["purchase_price"]).'</td>';
					$inwards_qty=0;
					$inwards_value=0;
					$summary_sql="Select sum(a.qty) as inwards_qty ,sum(a.purchase_price) as inwards_value  from purchase_details a 
					join purchase_master b on a.purchase_voucher_no = b.purchase_voucher_no 
					where purchase_date  between '".$from." 00:00:00' and '".$to." 23:59:59' and a.material_code='".$row["material_code"]."' ";
					
					if($summary_query=mysqli_query($connection,$summary_sql))
					{
						if(mysqli_num_rows($summary_query)>0)
						{ 
							if($summary_row=mysqli_fetch_array($summary_query)) 
							{ 
								$inwards_qty=$summary_row["inwards_qty"];
								$inwards_value=($summary_row["inwards_qty"] * $summary_row["inwards_value"]);
							}
						}
					} 
					$outwards_qty=$row["usage_count"];
					$outwards_value=$outwards_qty*$row["selling_price"];
					$wastage_qty=0;
					$wastage_value=0;
					$closing_qty=0;
					$closing_value=0;
					$summary_sql="Select sum(spillage_count) as wastage_qty ,sum(b.purchase_price) as wastage_value  from job_card_wise_material_spillage a 
					join material_master b on a.material_code = b.material_code  
					where jobcard_date  between '".$from." 00:00:00' and '".$to." 23:59:59' and a.material_code='".$row["material_code"]."' ";
					
					if($summary_query=mysqli_query($connection,$summary_sql))
					{
						if(mysqli_num_rows($summary_query)>0)
						{ 
							if($summary_row=mysqli_fetch_array($summary_query)) 
							{ 
								$wastage_qty=$summary_row["wastage_qty"];
								$wastage_value=($summary_row["wastage_qty"] * $summary_row["wastage_value"]);
							}
						}
					} 
					$result=$result.'<td>'.$inwards_qty.'</td>'; 
					$result=$result.'<td>'.$inwards_value.'</td>'; 
					$result=$result.'<td>'.$outwards_qty.'</td>'; 
					$result=$result.'<td>'.$outwards_value.'</td>'; 					
					$result=$result.'<td>'.$wastage_qty.'</td>'; 
					$result=$result.'<td>'.$wastage_value.'</td>'; 					
					$result=$result.'<td>'.$closing_qty.'</td>'; 
					$result=$result.'<td>'.$closing_value.'</td>';  
					$result=$result.'</tr>';
					$tot_wastage_qty=$tot_wastage_qty+ $wastage_qty;
					$tot_wastage_value=$tot_wastage_value+$wastage_value;
					$tot_closing_qty=$tot_closing_qty+$closing_qty;
					$tot_closing_value=$tot_closing_value+$closing_value;
					$tot_inwards_qty=$tot_inwards_qty+$inwards_qty;
					$tot_inwards_value=$tot_inwards_value+$inwards_value;
					$tot_outwards_qty=$tot_outwards_qty+$outwards_qty;
					$tot_outwards_value=$tot_outwards_value+$outwards_value;
					$i++;
			  }			
		}
		$result=$result.'</tbody>';
		$result=$result.'</table>';
	} 
	$result.='</div>';
	$result.='<div class="col-2 text-bg-light" style="border-left:1px inset #ccc;" >';
	$result.='<div class="text-center bg-success text-white"><h5 class="mb-0">SUMMARY</h5></div>';
	$result.='<div  id="card_1">';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">'."Total Inwards Qty : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($tot_inwards_qty,2)."</div></td></tr>";
	$result.='<tr><td align="right" width="50%">'."Total Inwards Value : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($tot_inwards_value,2)."</div></td></tr>";
	$result.='<tr><td align="right" width="50%">'."Total Outwards Qty : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($tot_outwards_qty,2)."</div></td></tr>";
	$result.='<tr><td align="right" width="50%">'."Total Outwards Value : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($tot_outwards_value,2)."</div></td></tr>";
	$result.='<tr><td align="right" width="50%">'."Total Wastage Qty : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($tot_wastage_qty,2)."</div></td></tr>";
	$result.='<tr><td align="right" width="50%">'."Total Wastage Value : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($tot_wastage_value,2)."</div></td></tr>";
	$result.='<tr><td align="right" width="50%">'."Total Closing Qty : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($tot_closing_qty,2)."</div></td></tr>";
	$result.='<tr><td align="right" width="50%">'."Total Closing Value : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($tot_closing_value,2)."</div></td></tr>";
 
	$result.='</table>';
	$result.='</div>';
			$result.='<div class="col-12"  style="border:0px solid #ccc; padding:5px;">';
	$result.='<br> <center><button type="button" class="btn btn-primary btn-sm" onclick="printMaterialUsageDiv('."'printable_div'".')">PRINT</button><br><br>';
	$result.='<button type="button" class="btn btn-secondary btn-sm" onclick="fnSideMenu(1)">Close</button></center>';
	$result.='</div>';
	$result.='</div>';

	$result.='</div>';
}
echo $result;
?>