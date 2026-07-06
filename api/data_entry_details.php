<?php
session_start();
include("../connect_db.php");
$result="Failed"; 
$dt=date('Y-m-d');
if(isset($_POST['action']))
{
	$action = $_POST['action'];
	$type = $_POST['type'];
	if($type == 'material')
	{
		if($action == 'get')
		{
			$sql="SELECT * FROM  material_master ";
			if($query=mysqli_query($connection,$sql))
			{
				
				$result='<div class="m-2" style="width:98%;border:0px solid #ccc;">';
				$result.='<div class="row  mb-2" style="background:#532c7e;">';
				$result.='<div class="col-10"><h5 class="text-white mb-0" style="padding-top:6px;">Material Master</h5></div>';
				$result.='<div class="col-2"><button class="btn btn-sm btn-info m-1" id="addPurchaseBtn" onclick="fnAddMaterialPopup()" >Add Material</button></div>';
				$result.='</div>'; 
				$result.='<table class="table table-bordered table-hover table-condensed table-striped " id="MaterialMasterTable"></div>';
				
				$result=$result.'<thead class="table-dark">';
				$result=$result.'<tr> ';
				$result=$result.'<th>Sno</th>';
				$result=$result.'<th>Material Code</th>';
				$result=$result.'<th>Material Name</th>'; 
				$result=$result.'<th>Stock Details</th>'; 
				$result=$result.'<th>Purchase Price</th>'; 
				$result=$result.'<th>Selling Price</th>';
				$result=$result.'<th>Other Details</th>'; 
				$result=$result.'<th>GST Details</th>'; 
				$result=$result.'<th>Status</th>'; 
				$result=$result.'<th>Action</th>'; 
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
						$result=$result.'<td>'.$row["material_code"].'</td>'; 
						$result=$result.'<td>'.$row["material_name"].'</td>'; 
						$temp_dt="";
						if ($row["stock_as_on_dt"] !== '0000-00-00') {
							$temp_dt=date("d-m-Y", strtotime($row["stock_as_on_dt"]));
						}  
						$result=$result.'<td>Stock In Hand : '.$row["stock_in_hand"].'<br />Stock Date : '.$temp_dt.'</td>';
						$result=$result.'<td>'.$row["purchase_price"].'</td>'; 
						$result=$result.'<td>'.$row["selling_price"].'</td>'; 
						$result=$result.'<td>Loose Sales Allowed : '.$row["loose_sales_allowed"].'<br />Slab Rates : '.$row["open_slab_rates"].'</td>'; 
						$result=$result.'<td>GST % : '.$row["gst_percentage"].'<br />HSN Code : '.$row["hsn_code"].'</td>'; 
						if($row['is_active'])
							$result=$result.'<td>Active</td>'; 
						else
							$result=$result.'<td>InActive</td>'; 
						 
						$result=$result.'<td>';
						$result=$result.'<button class="btn btn-sm btn-warning mt-1 me-1" onclick="fnMaterialEditPrice('."'".$row["material_code"]."','".$row["purchase_price"]."','".$row["selling_price"]."'".')" ><span class="material-icons-round" style="font-size:14px;">edit</span> </button>';
						$result=$result.'<button class="btn btn-sm btn-danger deleteBtn mt-1" onclick="fnMaterialDelete('."'".$row["material_code"]."'".')" ><span class="material-icons-round" style="font-size:14px;">delete</span> </button>';
						$result=$result.'</td>'; 
						$result=$result.'</tr>';
						$i++;
					}			
				}
				$result=$result.'</tbody>'; 
				$result=$result.'</table>';
				$result=$result.'</div>';
			}
		}
		else if($action == 'add')
		{		
			$sql="SELECT material_code from material_master where material_code='".$_POST['material_code']."'";	 
			if($query=mysqli_query($connection,$sql))
			{
				if(mysqli_num_rows($query)>0)
				{  
					echo "Material Code Already Exists";
					return;
				} 
			}	
			$sql="SELECT material_name from material_master where material_name='".$_POST['material_name']."'";	 
			if($query=mysqli_query($connection,$sql))
			{
				if(mysqli_num_rows($query)>0)
				{  
					echo "Material Name Already Exists";
					return;
				} 
			}	
			$sql="INSERT INTO `material_master`(`material_code`, `material_name`, `stock_in_hand`, `stock_as_on_dt`, `purchase_price`, `selling_price`, `loose_sales_allowed`, `open_slab_rates`, `gst_percentage`, `hsn_code`, `is_active`) VALUES ('".$_POST["material_code"]."','".$_POST["material_name"]."','".$_POST["stock_in_hand"]."','".date("Y-m-d", strtotime($_POST["stock_as_on_dt"]))."','".$_POST["purchase_price"]."','".$_POST["selling_price"]."','".$_POST["loose_sales_allowed"]."','".$_POST["open_slab_rates"]."','".$_POST["gst_percentage"]."','".$_POST["hsn_code"]."','1')"; 			 
			mysqli_query($connection,$sql);
			$result = "success";
		}
		else if($action == 'del')
		{
			$sql="DELETE FROM  material_master where material_code='".$_POST['code']."'";
			mysqli_query($connection,$sql);
			$result = "success";
		}
		else if($action == 'update')
		{
			$sql="UPDATE material_master SET purchase_price='".$_POST['purchase_price']."', selling_price='".$_POST['selling_price']."' WHERE material_code='".$_POST['material_code']."'";
			if(mysqli_query($connection,$sql))
				$result = "success";
		}
	}
	else if($type == 'machine')
	{
		if($action == 'get')
		{
			$sql="SELECT * FROM  machine_master ";
			if($query=mysqli_query($connection,$sql))
			{
				$result='<div class="m-2" style="width:98%;border:0px solid #ccc;">';
				$result.='<div class="row  mb-2" style="background:#532c7e;">';
				$result.='<div class="col-10"><h5 class="text-white mb-0" style="padding-top:6px;">Machine Master</h5></div>';
				$result.='<div class="col-2"><button class="btn btn-sm btn-info m-1" id="addPurchaseBtn" onclick="fnAddMachinePopup()" >Add Machine</button></div>';
				$result.='</div>'; 
				$result.='<table class="table table-bordered table-hover table-condensed table-striped " id="MachineMasterTable">';
				$result=$result.'<thead class="table-dark">';
				$result=$result.'<tr> ';
				$result=$result.'<th>Sno</th>';
				$result=$result.'<th>Machine Code</th>';
				$result=$result.'<th>Machine Name</th>'; 
				$result=$result.'<th>Description</th>'; 
				$result=$result.'<th>Counter Reading</th>'; 
				$result=$result.'<th>Counter Dt</th>'; 
				$result=$result.'<th>Action</th>'; 
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
						$result=$result.'<td>'.$row["machine_code"].'</td>'; 
						$result=$result.'<td>'.$row["machine_name"].'</td>'; 
						$result=$result.'<td>'.$row["machine_desc"].'</td>'; 
						$result=$result.'<td>'.$row["counter_reading"].'</td>'; 
						$temp_dt="";
						if ($row["counter_on_dt"] !== '0000-00-00') {
							$temp_dt=date("d-m-Y", strtotime($row["counter_on_dt"]));
						}  
						$result=$result.'<td>'. $temp_dt .'</td>';  
						$result=$result.'<td><button class="btn btn-sm btn-danger deleteBtn mt-1" onclick="fnMachineDelete('."'".$row["machine_code"]."'".')" >Delete</button></td>';  
						$result=$result.'</tr>';
						$i++;
					}			
				}
				$result=$result.'</tbody>'; 
				$result=$result.'</table>';
			}
		}
		else if($action == 'add')
		{
			$sql="SELECT machine_code from machine_master where machine_code='".$_POST['machine_code']."'";	 
			if($query=mysqli_query($connection,$sql))
			{
				if(mysqli_num_rows($query)>0)
				{  
					echo "Machine Code Already Exists";
					return;
				} 
			}
			$sql="SELECT machine_name from machine_master where machine_name='".$_POST['machine_name']."'";	 
			if($query=mysqli_query($connection,$sql))
			{
				if(mysqli_num_rows($query)>0)
				{  
					echo "Machine Name Already Exists";
					return;
				} 
			}	 
			$sql="INSERT INTO `machine_master`(`machine_code`, `machine_name`, `machine_desc`, `counter_reading`, `counter_on_dt`) VALUES ('".$_POST["machine_code"]."','".$_POST["machine_name"]."','".$_POST["machine_desc"]."','".$_POST["counter_reading"]."','".date("Y-m-d", strtotime($_POST["counter_on_dt"]))."')"; 			 
			mysqli_query($connection,$sql);
			$result = "success";
		}
		else if($action == 'del')
		{
			$sql="DELETE FROM  machine_master where machine_code='".$_POST['code']."'";
			mysqli_query($connection,$sql); 
			$result = "success";
		}
	}
	else if($type == 'product')
	{
		if($action == 'get')
		{
			$sql="SELECT * FROM  product_master ";
			if($query=mysqli_query($connection,$sql))
			{
				$result='<div class="m-2" style="width:98%;border:0px solid #ccc;">';
				$result.='<div class="row  mb-2" style="background:#532c7e;">';
				$result.='<div class="col-10"><h5 class="text-white mb-0" style="padding-top:6px;">Product Master</h5></div>';
				$result.='<div class="col-2"><button class="btn btn-sm btn-info m-1" id="addPurchaseBtn" onclick="fnAddProductPopup()" >Add Product</button></div>';
				$result.='</div>'; 
				$result.='<table class="table table-bordered table-hover table-condensed table-striped " id="ProductMasterTable">';
				$result=$result.'<thead class="table-dark">';
				$result=$result.'<tr> ';
				$result=$result.'<th>Sno</th>';
				$result=$result.'<th>Product Details</th>'; 
				$result=$result.'<th>Selling Price</th>'; 
				$result=$result.'<th>Applicable Details</th>'; 
				$result=$result.'<th>Category Details</th>'; 
				$result=$result.'<th>Slab Details</th>'; 
				$result=$result.'<th>Other Details</th>'; 
				$result=$result.'<th>Action</th>'; 
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
						$result=$result.'<td>'.$row["product_code"].'<br />'; 
						$result=$result.$row["product_name"].'</td>'; 
						$result=$result.'<td>'.$row["selling_price"].'</td>'; 
						$result=$result.'<td>';
						$copy_addl_copy_applicable="No";
						if($row["1st_copy_addl_copy_applicable"] ==1)
							$copy_addl_copy_applicable="Yes";
						$is_front_and_back_option_available="No";
						if($row["is_front_and_back_option_available"] ==1)
							$is_front_and_back_option_available="Yes";
						$is_material_based_rate_calculation="No";
						if($row["is_material_based_rate_calculation"] ==1)
							$is_material_based_rate_calculation="Yes";
						$is_manual_rate_allowed='No';
						if($row["is_manual_rate_allowed"] ==1)
							$is_manual_rate_allowed="Yes";
						$open_slab_rates='No';
						if($row["open_slab_rates"] ==1)
							$open_slab_rates="Yes";
						$no_counter_sales='No';
						if($row["no_counter_sales"] ==1)
							$no_counter_sales="Yes";
						$result=$result.'1st copy addl copy applicable : '.$copy_addl_copy_applicable.'<br />';
						$result=$result.'Front and back option available : '.$is_front_and_back_option_available.'<br />';
						$result=$result.'Material based rate calculation : '.$is_material_based_rate_calculation.'<br />';
						$result=$result.'Manual rate allowed : '.$is_manual_rate_allowed;
						$result=$result.'</td>'; 
						$result=$result.'<td>Category 1 : '.$row["category1"].'<br />Category 2 : '.$row["category2"].'</td>'; 
						$result=$result.'<td>Open Slab Rates : '.$open_slab_rates.'<br />No Counter Sales : '.$no_counter_sales.'</td>'; 
						$result=$result.'<td>GST % : '.$row["gst_percentage"].'<br />HSN Code : '.$row["hsn_code"].'</td>'; 
						$result=$result.'<td><button class="btn btn-sm btn-danger deleteBtn mt-1" onclick="fnProductDelete('."'".$row["product_code"]."'".')" >Delete</button></td>';  
						$result=$result.'</tr>';
						$i++;
					}			
				}
				$result=$result.'</tbody>'; 
				$result=$result.'</table>';
			}
		}
		else if($action == 'add')
		{
			$sql="SELECT product_code from product_master where product_code='".$_POST['product_code']."'";	 
			if($query=mysqli_query($connection,$sql))
			{
				if(mysqli_num_rows($query)>0)
				{  
					echo "Product Code Already Exists";
					return;
				} 
			}
			$sql="SELECT product_name from product_master where product_name='".$_POST['product_name']."'";	 
			if($query=mysqli_query($connection,$sql))
			{
				if(mysqli_num_rows($query)>0)
				{  
					echo "Product Name Already Exists";
					return;
				} 
			}	 
			$sql="INSERT INTO `product_master`(`product_code`, `product_name`, `selling_price`, `1st_copy_addl_copy_applicable`, `is_front_and_back_option_available`, `is_material_based_rate_calculation`, `is_manual_rate_allowed`, `addl_copy_rate`, `category1`, `category2`, `open_slab_rates`, `no_counter_sales`, `gst_percentage`, `hsn_code`) VALUES ('".$_POST["product_code"]."','".$_POST["product_name"]."','".$_POST["selling_price"]."','".$_POST["1st_copy_addl_copy_applicable"]."','".$_POST["is_front_and_back_option_available"]."','".$_POST["is_material_based_rate_calculation"]."','".$_POST["is_manual_rate_allowed"]."','".$_POST["addl_copy_rate"]."','".$_POST["category1"]."','".$_POST["category2"]."','".$_POST["open_slab_rates"]."','".$_POST["no_counter_sales"]."','".$_POST["gst_percentage"]."','".$_POST["hsn_code"]."')";   
			mysqli_query($connection,$sql);
			$result = "success";
		}
		else if($action == 'del')
		{
			$sql="DELETE FROM  product_master where product_code='".$_POST['product_code']."'";
			mysqli_query($connection,$sql); 
			$result = "success";
		}
	}
	else if($type == 'bom')
	{
		if($action == 'get')
		{
			$sql="SELECT a.*,b.product_name,c.material_name FROM  product_material_details a 
			join product_master b on a.product_code=b.product_code 
			join material_master c on a.material_code = c.material_code";
			if($query=mysqli_query($connection,$sql))
			{
				$result='<div class="m-2" style="width:98%;border:0px solid #ccc;">';
				$result.='<div class="row  mb-2" style="background:#532c7e;">';
				$result.='<div class="col-10"><h5 class="text-white mb-0" style="padding-top:6px;">BOM</h5></div>';
				$result.='<div class="col-2"><button class="btn btn-sm btn-info m-1" id="addPurchaseBtn" onclick="fnAddBOMPopup()" >Add BOM</button></div>';
				$result.='</div>'; 
				$result.='<table class="table table-bordered table-hover table-condensed table-striped " id="BOMMasterTable">';
				$result=$result.'<thead class="table-dark">';
				$result=$result.'<tr> ';
				$result=$result.'<th>Sno</th>';
				$result=$result.'<th>Product Name</th>';
				$result=$result.'<th>Material Name</th>';
				$result=$result.'<th>Alternative Details</th>'; 
				$result=$result.'<th>Is Product</th>'; 
				$result=$result.'<th>Other Details</th>';  
				$result=$result.'<th>Action</th>'; 
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
						$result=$result.'<td>Code : '.$row["product_code"].' <br/>'.'Name : '.$row["product_name"].'</td>'; 
						$result=$result.'<td>Code : '.$row["material_code"].' <br/>'.'Name : '.$row["material_name"].'</td>'; 
						$is_alternatives_available = "No";
						if($row["is_alternatives_available"])
							$is_alternatives_available="Yes";
						$is_this_a_product = "No";
						if($row["is_this_a_product"])
							$is_this_a_product="Yes";
						
						$result=$result.'<td>Alternative Available : '.$is_alternatives_available.' <br/>'.'Alternative Caption : '.$row["alternative_caption"].' <br/>'.'Alternative Key : '.$row["alternative_key"].'</td>'; 
						$result=$result.'<td>'.$is_this_a_product.'</td>';
						$result=$result.'<td>'.'Multiplication Factor : '.$row["multiplication_factor"].' <br/>'.'Round Up : '.$row["round_up"].' <br/>'.'Spl Selling Price : '.$row["spl_selling_price"].'</td>'; 
						  
						$result=$result.'<td><button class="btn btn-sm btn-danger deleteBtn mt-1" onclick="fnBOMDelete('."'".$row["product_material_id"]."'".')" >Delete</button></td>';  
						$result=$result.'</tr>';
						$i++;
					}			
				}
				$result=$result.'</tbody>'; 
				$result=$result.'</table>';
			}
		}
		else if($action == 'add')
		{
			$sql="INSERT INTO `product_material_details`( `product_code`, `is_alternatives_available`, `alternative_caption`, `alternative_key`, `material_code`, `is_this_a_product`, `multiplication_factor`, `round_up`, `spl_selling_price`) VALUES ('".$_POST["product_code"]."','".$_POST["is_alternatives_available"]."','".$_POST["alternative_caption"]."','".$_POST["alternative_key"]."','".$_POST["material_code"]."','".$_POST["is_this_a_product"]."','".$_POST["multiplication_factor"]."','".$_POST["round_up"]."','".$_POST["spl_selling_price"]."')";   
			mysqli_query($connection,$sql);
			$result = "success";
		}
		else if($action == 'del')
		{
			$sql="DELETE FROM  product_material_details where product_material_id ='".$_POST['product_material_id']."'";
			mysqli_query($connection,$sql); 
			$result = "success";
		}
	}
	else if($type == 'slabs')
	{
		if($action == 'get')
		{
			$sql="SELECT a.*,b.product_name FROM  product_slab_rate_details a 
			join product_master b on a.product_code=b.product_code";
			if($query=mysqli_query($connection,$sql))
			{
				$result='<div class="m-2" style="width:98%;border:0px solid #ccc;">';
				$result.='<div class="row  mb-2" style="background:#532c7e;">';
				$result.='<div class="col-10"><h5 class="text-white mb-0" style="padding-top:6px;">Slabs</h5></div>';
				$result.='<div class="col-2"><button class="btn btn-sm btn-info m-1" id="addPurchaseBtn" onclick="fnAddSlabsPopup()" >Add Slabs</button></div>';
				$result.='</div>'; 
				$result.='<table class="table table-bordered table-hover table-condensed table-striped " id="SlabsMasterTable">';
				$result=$result.'<thead class="table-dark">';
				$result=$result.'<tr> ';
				$result=$result.'<th>Sno</th>';
				$result=$result.'<th>Product Name</th>';
				$result=$result.'<th>Above Qty</th>';
				$result=$result.'<th>Selling Price</th>'; 
				$result=$result.'<th>Addl. Copy Rate</th>';  
				$result=$result.'<th>Action</th>'; 
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
						$result=$result.'<td>Code : '.$row["product_code"].' <br/>'.'Name : '.$row["product_name"].'</td>';   
						$result=$result.'<td>'.$row["above_qty"].'</td>';   
						$result=$result.'<td>'.$row["selling_price"].'</td>';   
						$result=$result.'<td>'.$row["addl_copy_rate"].'</td>';   
						  
						$result=$result.'<td><button class="btn btn-sm btn-danger deleteBtn mt-1" onclick="fnSlabsDelete('."'".$row["id"]."'".')" >Delete</button></td>';  
						$result=$result.'</tr>';
						$i++;
					}			
				}
				$result=$result.'</tbody>'; 
				$result=$result.'</table>';
			}
		}
		else if($action == 'add')
		{
			$sql="INSERT INTO `product_slab_rate_details`(`product_code`, `above_qty`, `selling_price`, `addl_copy_rate`) VALUES ('".$_POST["product_code"]."','".$_POST["above_qty"]."','".$_POST["selling_price"]."','".$_POST["addl_copy_rate"]."')";   
			mysqli_query($connection,$sql);
			$result = "success";
		}
		else if($action == 'del')
		{
			$sql="DELETE FROM  product_slab_rate_details where id ='".$_POST['id']."'";
			mysqli_query($connection,$sql); 
			$result = "success";
		}
	}
	else if($type == 'cuttingslabs')
	{
		if($action == 'get')
		{
			$sql="SELECT a.*,b.product_name FROM  product_cutting_slab_rate_details a 
			join product_master b on a.product_code=b.product_code";
			if($query=mysqli_query($connection,$sql))
			{
				$result='<div class="m-2" style="width:98%;border:0px solid #ccc;">';
				$result.='<div class="row  mb-2" style="background:#532c7e;">';
				$result.='<div class="col-10"><h5 class="text-white mb-0" style="padding-top:6px;">Cutting Slabs</h5></div>';
				$result.='<div class="col-2"><button class="btn btn-sm btn-info m-1" id="addPurchaseBtn" onclick="fnAddCuttingSlabsPopup()" >Add Slabs</button></div>';
				$result.='</div>'; 
				$result.='<table class="table table-bordered table-hover table-condensed table-striped " id="CuttingSlabsMasterTable">';
				$result=$result.'<thead class="table-dark">';
				$result=$result.'<tr> ';
				$result=$result.'<th>Sno</th>';
				$result=$result.'<th>Product Name</th>';
				$result=$result.'<th>No. of Ups</th>';
				$result=$result.'<th>SVC Charge</th>'; 
				$result=$result.'<th>Per Sheet Rate</th>';  
				$result=$result.'<th>Action</th>'; 
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
						$result=$result.'<td>Code : '.$row["product_code"].' <br/>'.'Name : '.$row["product_name"].'</td>';   
						$result=$result.'<td>'.$row["no_of_ups"].'</td>';   
						$result=$result.'<td>'.$row["svc_charge"].'</td>';   
						$result=$result.'<td>'.$row["per_sheet_rate"].'</td>';   
						  
						$result=$result.'<td><button class="btn btn-sm btn-danger deleteBtn mt-1" onclick="fnCuttingSlabsDelete('."'".$row["id"]."'".')" >Delete</button></td>';  
						$result=$result.'</tr>';
						$i++;
					}			
				}
				$result=$result.'</tbody>'; 
				$result=$result.'</table>';
			}
		}
		else if($action == 'add')
		{
			$sql="INSERT INTO `product_cutting_slab_rate_details`(`product_code`, `no_of_ups`, `svc_charge`, `per_sheet_rate`) VALUES ('".$_POST["product_code"]."','".$_POST["no_of_ups"]."','".$_POST["svc_charge"]."','".$_POST["per_sheet_rate"]."')";   
			mysqli_query($connection,$sql);
			$result = "success";
		}
		else if($action == 'del')
		{
			$sql="DELETE FROM  product_cutting_slab_rate_details where id ='".$_POST['id']."'";
			mysqli_query($connection,$sql); 
			$result = "success";
		}
	}
}	
echo $result;
?>