<?php
include '../connect_db.php';
 
if(isset($_POST['action']))
{
	$action = $_POST['action'];
}
if ($action == 'read') {
	 
    $sql =  "SELECT * FROM product_master" ;
    if($query=mysqli_query($connection,$sql))
	{
		$result='<table class="table table-bordered table-hover table-condensed table-striped " id="resultProductTable">';
		$result=$result.'<thead class="table-dark">';
		$result=$result.'<tr> ';
		$result=$result.'<th>Code</th>';
		$result=$result.'<th>Name</th>'; 
		$result=$result.'<th>Category 1</th>'; 
		$result=$result.'<th>Category 2</th>'; 
		$result=$result.'<th>Selling Price</th>';
		$result=$result.'<th>Rate Details</th>';
		$result=$result.'<th>Available Option</th>'; 
		$result=$result.'<th>HSN Code</th>';
		$result=$result.'<th>UOM</th>';
		$result=$result.'<th>Action</th>';
		$result=$result.'</tr>';
		$result=$result.'</thead>';
		$result=$result.'<tbody>';
		$i=1; 
		if(mysqli_num_rows($query)>0)
		{ 
			 while($row=mysqli_fetch_array($query)) {
				$result=$result.'<tr>';
				//$result=$result.'<td>'.$i.'</td>';
				$result=$result.'<td data-id="'.$row["product_code"].'">'.$row["product_code"].'</td>';
				$result=$result.'<td>'.$row["product_name"].'</td>';
				$result=$result.'<td>'.$row["category1"].'</td>';
				$result=$result.'<td>'.$row["category2"].'</td>';
				$result=$result.'<td>'.$row["selling_price"].'</td>';
				$result=$result.'<td> PO No : '.$row["our_po_no"].'<br /> Date : '.date("d-m-Y", strtotime($row["Product_date"])).'</td>';
				$result=$result.'<td> Vendor : '.$row["vendor_code"].'<br /> Invoice No : '. $row["vendor_invoice_no"]  .'<br /> Date : '.date("d-m-Y", strtotime($row["vendor_invoice_date"])).'</td>';
				$result=$result.'<td> Value : '.$row["discount_value"].'<br /> After Discount : '. $row["total_after_discount_value"].'</td>';
				$result=$result.'<td>'; 
				
				if($row["1st_copy_addl_copy_applicable"]==0)
					$result=$result.'1st copy addl. copy  applicable : False';
				else
					$result=$result.'1st copy addl. copy  applicable : True';
				
				if($row["is_front_and_back_option_available"]==0)
					$result=$result.'<br /> Front and back option available : False';
				else
					$result=$result.'<br /> Front and back option available : True';
				
				if($row["is_material_based_rate_calculation"]==0)
					$result=$result.'<br /> Material based rate calculation : False';
				else
					$result=$result.'<br /> Material based rate calculation : True';
				 
				if($row["is_manual_rate_allowed"]==0)
					$result=$result.'<br /> Manual Rate Allowed : False';
				else
					$result=$result.'<br /> Manual Rate Allowed : True';
				 
				$result=$result.'</td>';
			  
				$result=$result.'<td>'.$row["total_after_discount_value"].'</td>'; 
				$result=$result.'<td><button class="btn btn-sm btn-danger deleteBtn mt-1" onclick="fnProductDelete('.$row["product_code"].')" >Delete</button></td>';
				$result=$result.'</tr>';
				$i++;
			  }
			
		} 
		$result=$result.'</tbody>'; 
		$result=$result.'</table>';
	} 
    echo $result;
}
elseif ($action == 'add') {
    $dt=date("Y-m-d",strtotime($_POST["Product_date"]));
$sql = "INSERT INTO `product_master`(`Product_voucher_no`, `vendor_code`, `vendor_invoice_no`, `Product_date`, `vendor_invoice_date`, `our_po_no`, `discount_value`, `total_after_discount_value`, `igst_value`, `cgst_value`, `sgst_value`, `net_total_value` ) VALUES ('".$_POST["Product_voucher_no"]."','".$_POST["vendor_code"]."','".$_POST["vendor_invoice_no"]."','".$dt."','".date("Y-m-d",strtotime($_POST["vendor_invoice_date"]))."','".$_POST["our_po_no"]."','".$_POST["discount_value"]."','".$_POST["total_after_discount_value"]."','".$_POST["igst_value"]."','".$_POST["cgst_value"]."','".$_POST["sgst_value"]."','".$_POST["net_total_value"]."')";
	if(mysqli_query($connection,$sql))
	{
		$Product_details_array= json_decode($_POST['allTableData'], true); 
		for($i=0;$i<count($Product_details_array);$i++)
		{
			$sql = "INSERT INTO `Product_details`(`Product_voucher_no`, `material_code`, `Product_price`, `qty`, `gst_percentage`, `item_discount`) VALUES ('".$_POST["Product_voucher_no"]."','".$Product_details_array[$i]['material_code']."','".$Product_details_array[$i]['Product_price']."','".$Product_details_array[$i]['qty']."','".$Product_details_array[$i]['gst']."','".$Product_details_array[$i]['discount']."')"; 
			mysqli_query($connection,$sql);
		}
//Posting to Journal Starts Here

	$total_after_discount_value_txt=(float) $_POST["total_after_discount_value"];
	
/* 		$sql="INSERT INTO `journal_details`( `dt`, `achead`, `description`, `amount`, `link_key`) VALUES ";
		$val_sql="";
		
		$val_sql.="('".$dt."','Product','Product Voucher No:".$_POST["Product_voucher_no"]."',".($total_after_discount_value_txt*-1).",'PU_".$_POST["Product_voucher_no"]."')";
		if($val_sql!="") $val_sql.=",";
		$val_sql.="('".$dt."','".$_POST["vendor_code"]."','Product Voucher No:".$_POST["Product_voucher_no"]."',".$total_after_discount_value_txt.",'PU_".$_POST["Product_voucher_no"]."');";
		
		$sql.=$val_sql;
		
		mysqli_query($connection,$sql); */
		post_journal_double_entry($dt,$_POST["vendor_code"],"Product","Product Voucher No:".$_POST["Product_voucher_no"],$total_after_discount_value_txt,"PU_".$_POST["Product_voucher_no"]);
	

//Posting to Journal Ends Here		
		echo "Success";
	}		
	else
		echo "Failed";
} 
elseif ($action == 'delete') { 
	$Product_voucher_no = $_POST['pId'];
	$sql="DELETE FROM  product_master where Product_voucher_no='".$Product_voucher_no ."'";
	mysqli_query($connection,$sql); 
     $sql="DELETE FROM  Product_details where Product_voucher_no='".$Product_voucher_no ."'";
	mysqli_query($connection,$sql);
     $sql="DELETE FROM  journal_details where link_key='PU_".$Product_voucher_no ."'";
	mysqli_query($connection,$sql); 
    echo "Success";
} 
elseif ($action =='getNextPVNo')
{
	$sql="SELECT max(Product_voucher_no) as max_voucher_no FROM product_master;";
	$Product_voucher_no=1;
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{
			if($row=mysqli_fetch_array($query))
			{
				if(!is_null($row["max_voucher_no"]))
				{
					$Product_voucher_no=$row["max_voucher_no"]+1;
					echo $Product_voucher_no;
				}
			}
		}
	}
}
?>