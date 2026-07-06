<?php
include_once 'connect_db.php';
$product_code_txt=$_GET["product_code_txt"];
$sql ="select * from product_master where product_code='".$_GET["product_code_txt"]."';";

$mbq=1;
$moq=1;
$selling_price="";
$addl_copy_rate="";
$selling_price_discounted="";
$addl_copy_rate_discounted="";

$open_slab_rates=false;
$is_material_based_rate_calculation=false;
$no_of_ups_txt="";
$flag="PRODUCT_SLAB";
if($query=mysqli_query($connection,$sql))
{
	if($row=mysqli_fetch_array($query))
	{
		$moq=$row['moq'];
		$mbq=$row['billing_qty_round_to'];
		$selling_price=$row["selling_price"];
		$selling_price_discounted==$row["selling_price"];
		if(!is_null($row["addl_copy_rate"]))
		{
			$addl_copy_rate= $row["addl_copy_rate"];
			$addl_copy_rate_discounted= $row["addl_copy_rate"];
		}
		if(!is_null($row["open_slab_rates"])) $open_slab_rates=$row["open_slab_rates"];
		if(!is_null($row["is_material_based_rate_calculation"])) $is_material_based_rate_calculation=$row["is_material_based_rate_calculation"];
	}//if($row=mysqli_fetch_array($query))
}
$qty = $_GET["first_copy_count"]+$_GET["addl_copy_count"];
//fetch machine based rates
if(isset($_GET["machine_code"]))
{
	$machine_code=$_GET["machine_code"];
	$mac_sql="select * from product_machine_detail where machine_code='".$machine_code."' and product_code='".$product_code_txt."';";
	
	if($mac_qry=mysqli_query($connection,$mac_sql))
	{
		if($mac_row=mysqli_fetch_array($mac_qry))
		{
			if($mac_row["selling_price"]>0)
			{
				$selling_price=$mac_row["selling_price"];
				$selling_price_discounted=$mac_row["selling_price"];
			}
		}
	}
}
if($is_material_based_rate_calculation)
{
	if(isset($_GET["prod_mat_qty_txt"]))
	{
		$prod_mat_qty_txt=$_GET["prod_mat_qty_txt"];
		$mat_ary=explode("~",$prod_mat_qty_txt);
		$inp_param="";
		$selling_price=0;
		$flag="MAT_BASED_VALUE";
		
		foreach($mat_ary as $mat)
		{
			$mat_param_ary=explode(",",$mat);
			
			//if(strlen($inp_param)>0)$inp_param.=",";
			$inp_param="'".$mat_param_ary[0]."'";
			$mf_param = $mat_param_ary[1];
			$sql="select * from material_master where material_code =".$inp_param.";";
			
			if($mat_qry=mysqli_query($connection,$sql))
			{
				if($mat_row=mysqli_fetch_array($mat_qry))
				{
					$mat_selling_price=$mat_row["selling_price"];
					$bom_sql="select * from product_material_details where product_code='".$product_code_txt."' and material_code =".$inp_param.";";
					
					if($bom_qry = mysqli_query($connection,$bom_sql))
					{
						if($bom_row=mysqli_fetch_array($bom_qry))
						{
							if(!is_null($bom_row["spl_selling_price"]))
							{
								
								$mat_selling_price=$bom_row["spl_selling_price"];
							}
						}
					}
					//$mat_mf=$mf_param;
					$mat_qty = ceil($qty*$mf_param);
					$mat_cost=$mat_qty *$mat_selling_price;
					$selling_price=$selling_price+$mat_cost;
					
				}
			}
			
		}
	}
}
else
{
	if(1)//$open_slab_rates)
	{
		if(isset($_GET["no_of_ups_txt"]))
		{
			$no_of_ups_txt=$_GET["no_of_ups_txt"];
		}
		if($no_of_ups_txt=="")
		{
			$sql ="select * from product_slab_rate_details where product_code='".$_GET["product_code_txt"]."' and above_qty<=".$qty." order by above_qty desc;";
			
			if($query=mysqli_query($connection,$sql))
			{
				if($row=mysqli_fetch_array($query))
				{
					$selling_price_discounted=$row["selling_price"];
					if($open_slab_rates)
					{
						$selling_price=$row["selling_price"];
					}
					if(!is_null($row["addl_copy_rate"]))
					{
						if($open_slab_rates)
						{
							$addl_copy_rate= $row["addl_copy_rate"];
						}
						$addl_copy_rate_discounted= $row["addl_copy_rate"];
					}
				}//if($row=mysqli_fetch_array($query))

			}
		
		}
		else
		{
			$flag="SERVICE_SLAB";
			$sql="select * from product_cutting_slab_rate_details where product_code='".$_GET["product_code_txt"]."' and no_of_ups>=".$no_of_ups_txt." order by no_of_ups;";
			
			if($query=mysqli_query($connection,$sql))
				{
					if($row=mysqli_fetch_array($query))
					{
						$selling_price=$row["svc_charge"];
						if(!is_null($row["per_sheet_rate"]))
						{
							$addl_copy_rate= $row["per_sheet_rate"];
						}
					}//if($row=mysqli_fetch_array($query))
					else
					{
						$flag="SERVICE_FLAT";
						$sql="select * from product_cutting_flat_rate_details where product_code='".$_GET["product_code_txt"]."' and no_of_ups>=".$no_of_ups_txt." and qty_upto>=".$qty." order by no_of_ups;";
						
						if($query=mysqli_query($connection,$sql))
						{
							if($row=mysqli_fetch_array($query))
							{
								$selling_price=$row["selling_price"];
							}//
						
						}
					}
				}		
		}
	}
	
}
echo $selling_price;
//if(!is_null($addl_copy_rate))
//{
	echo ",".$addl_copy_rate.",".$flag.",".$selling_price_discounted.",".$addl_copy_rate_discounted.",".$moq.",".$mbq;
//}
?>