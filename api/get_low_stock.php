<?php
error_reporting(0);
session_start();
include_once '../connect_db.php';
$result="";
function get_sum($sql1)
{
	global $connection;
	$ans=0;
	if($qry=mysqli_query($connection,$sql1))
	{
		if($row=mysqli_fetch_array($qry))
		{
			$ans=$row[0];
		}
	}
	return $ans;
}

$dt=date("Y-m-d");
	$sql="select material_code,material_name,stock_in_hand from material_master;";
	if($query=mysqli_query($connection,$sql))
	{
		$result.='<table class="low-stock-table">';
			$result.='<thead><tr>';
			$result.='<th>Item name</th>';
			$result.='<th style="text-align:right;">Stock</th>';
			$result.='</tr></thead>';
			$result.='<tbody>';
			$i=1;
		if(mysqli_num_rows($query)>0)
		{
			 while($row=mysqli_fetch_array($query)) {
					$sql1="select sum(usage_count) as usage_sum from job_card_wise_material_usage where material_code='".$row["material_code"]."';";
					$usage_sum = get_sum($sql1);
					$sql1="select sum(qty) as purchase_sum from purchase_details where material_code='".$row["material_code"]."';";
					$purchase_sum = get_sum($sql1);
					$sql1="select sum(spillage_count) as spill_sum from job_card_wise_material_spillage where material_code='".$row["material_code"]."';";
					$spill_sum = get_sum($sql1);
					$sql1="select sum(qty) as wastage_sum from wastage_master where material_code='".$row["material_code"]."';";
					$wastage_sum = get_sum($sql1);
					$current_stock= $row["stock_in_hand"]-$usage_sum+$purchase_sum-$spill_sum-$wastage_sum;
					if($current_stock<250 && $current_stock!=0)
					{
					$badge_class = ($current_stock < 100) ? 'stock-badge' : 'stock-badge warn';
					$result.='<tr>';
					$result.='<td>'.$row["material_name"].'</td>';
					$result.='<td class="stock-val"><span class="'.$badge_class.'">'.$current_stock.'</span></td>';
					$result.='</tr>';
					$i++;
					}
			  }
		}
		$result.='</tbody>';
		$result.='</table>';
	}

echo $result;
?>
