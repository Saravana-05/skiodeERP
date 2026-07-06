<?php
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
	$sql="SELECT * FROM material_master";  
	//$sql="select material_code,material_name,(COALESCE(stock_in_hand,0)-COALESCE(usage_sum,0)-COALESCE(spill_sum,0)+COALESCE(purchase_sum,0)) as current_stock from (SELECT a.material_code,a.material_name,a.stock_in_hand,a.stock_as_on_dt,sum(b.usage_count) as usage_sum,sum(c.spillage_count) as spill_sum,sum(d.qty) as purchase_sum FROM `material_master` a left join job_card_wise_material_usage b on a.material_code=b.material_code left join job_card_wise_material_spillage c on a.material_code=c.material_code left join purchase_details d on a.material_code=d.material_code group by a.material_code) sql_a ORDER BY `sql_a`.`usage_sum` DESC;"; 
	
	$sql="select material_code,material_name,stock_in_hand from material_master;";
	if($query=mysqli_query($connection,$sql))
	{
		$result='<table class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
			$result=$result.'<thead class="table-dark">';
			$result=$result.'<tr> ';
			$result=$result.'<th>SNo</th>'; 
			$result=$result.'<th>Material Code</th>';
			$result=$result.'<th>Material Name</th>';
			$result=$result.'<th>Stock In Hand</th>';
			$result=$result.'<th>Low Stock Warning</th>';
			$result=$result.'</tr>';
			$result=$result.'</thead>';
			$result=$result.'<tbody>';
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
					if($current_stock!=0)
					{
					$result=$result.'<tr>';
					$result=$result.'<td>'.$i.'</td>'; 
					$result=$result.'<td>'.$row["material_code"].'</td>';
					$result=$result.'<td>'.$row["material_name"].'</td>';	
					$result=$result.'<td>'.($current_stock==0?"":$current_stock).'</td>'; 
					$result=$result.'<td'.($current_stock<100?' style="background-color:red;"> Order Now':'>').'</td>'; 
					$result=$result.'</tr>';
					$i++;
					}
					
					
			  }
			
		}
		 
		$result=$result.'</tbody>';
		$result=$result.'</table>';
	} 
 
echo $result;
?>