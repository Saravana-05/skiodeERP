<?php
$result=""; 
error_reporting(0); 
include_once '../connect_db.php'; 
include 'SimpleXLSX.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] == 0) {
        $uploadDir = 'uploads/';
        $filename = basename($_FILES['excel_file']['name']);
        $targetFile = $uploadDir . $filename;

        // Make sure the uploads directory exists
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the file to the uploads folder
        if (move_uploaded_file($_FILES['excel_file']['tmp_name'], $targetFile)) {
            echo "File uploaded successfully: " . $filename;
 

			$inputFileType = 'Xlsx';
			$inputFileName = $targetFile;
	
			$xlsx = SimpleXLSX::parse($inputFileName);

			$noOfSheets = count($xlsx->sheetNames());
			echo $noOfSheets."<hr>";
			for($K=0;$K<$noOfSheets;$K++)
			{ 
				//if($K != 4) continue;
				$sheetName = $xlsx->sheetName($K);
				echo "<h4>$sheetName</h4>";
				 
				if($sheetName == "Material Master")
				{
					$sql="truncate material_master";
					mysqli_query($connection,$sql);
					for($i=1;$i<count($xlsx->rows($K));$i++)
					{
						$rowData = $xlsx->rows($K)[$i]; 
						if($rowData[0] != "")
						{
							$isExists=0;
							$sql="Select count(material_code) as mat_code from material_master where material_code='".$rowData[0]."'"; 
							if($query=mysqli_query($connection,$sql))
							{
								if(mysqli_num_rows($query)>0)
								{ 
									if($row=mysqli_fetch_array($query)) 
									{	
										if($row["mat_code"] == 1)
											$isExists=1;
									}
								}
							}
							if($isExists == 0)
							{
								$rowData[0]=str_replace(" ","",$rowData[0]);
								$rowData[0]=str_replace(".","",$rowData[0]);
								$sql="INSERT INTO `material_master`(`material_code`, `material_name`, `stock_in_hand`, `stock_as_on_dt`, `purchase_price`, `selling_price`, `loose_sales_allowed`,`open_slab_rates`, gst_percentage,hsn_code) VALUES ('".$rowData[0]."','".$rowData[1]."','".$rowData[2]."','".$rowData[3]."','".$rowData[4]."','".$rowData[5]."','".$rowData[6]."','".$rowData[7]."','".$rowData[8]."','".$rowData[9]."')";
								//print_r($sql);
								//echo '<br/>';
								mysqli_query($connection,$sql);
							}
							else
							{
								echo "Duplicate @ ".$sheetName. " :: ".$rowData[0]."<br>";
							}
						}
					}
					echo 'Completed '.$sheetName; 				 
				}
				else if($sheetName == "Machine Master")
				{
					$sql="truncate machine_master";
					mysqli_query($connection,$sql);
				 
					for($i=1;$i<count($xlsx->rows($K));$i++)
					{
						$rowData = $xlsx->rows($K)[$i];
						if($rowData[0] != "")
						{
							$isExists=0;
							$sql="Select count(machine_code) as mat_code from machine_master where machine_code='".$rowData[0]."'"; 
							if($query=mysqli_query($connection,$sql))
							{
								if(mysqli_num_rows($query)>0)
								{ 
									if($row=mysqli_fetch_array($query)) 
									{	
										if($row["mat_code"] == 1)
											$isExists=1;
									}
								}
							}
							if($isExists == 0)
							{
								$rowData[0]=str_replace(" ","",$rowData[0]);
								$rowData[0]=str_replace(".","",$rowData[0]);
								
								$sql="INSERT INTO `machine_master`(`machine_code`, `machine_name`, `machine_desc`, `counter_reading`, `counter_on_dt`) VALUES ('".$rowData[0]."','".$rowData[1]."','".$rowData[2]."','".$rowData[3]."','".$rowData[4]."')";
								//print_r($sql);
								//echo '<br/>';
								mysqli_query($connection,$sql);
							}
							else
							{
								echo "Duplicate @ ".$sheetName. " :: ".$rowData[0]."<br>";
							}
						}
					}
					echo 'Completed '.$sheetName; 
				}
				else if($sheetName == "Product Master")
				{
					$sql="truncate product_master";
					mysqli_query($connection,$sql);
					for($i=1;$i<count($xlsx->rows($K));$i++)
					{
						$rowData = $xlsx->rows($K)[$i];
						if($rowData[0] != "")
						{
							$isExists=0;
							$sql="Select count(product_code) as mat_code from product_master where product_code='".$rowData[0]."'"; 
							if($query=mysqli_query($connection,$sql))
							{
								if(mysqli_num_rows($query)>0)
								{ 
									if($row=mysqli_fetch_array($query)) 
									{	
										if($row["mat_code"] == 1)
											$isExists=1;
									}
								}
							}
							if($isExists == 0)
							{
								$rowData[0]=str_replace(" ","",$rowData[0]);
								$rowData[6]=str_replace(" ","",$rowData[6]);
								$rowData[7]=str_replace(" ","",$rowData[7]);
								$rowData[0]=str_replace(".","",$rowData[0]);
								
								$sql="INSERT INTO `product_master`(`product_code`, `product_name`, `selling_price`, `1st_copy_addl_copy_applicable`, `is_front_and_back_option_available`, `addl_copy_rate`, `category1`, `category2`, `open_slab_rates`,gst_percentage,hsn_code,is_manual_rate_allowed,is_material_based_rate_calculation,	no_counter_sales,billing_qty_round_to,moq) VALUES ('".$rowData[0]."','".$rowData[1]."','".$rowData[2]."','".$rowData[3]."','".$rowData[4]."','".$rowData[5]."','".$rowData[6]."','".$rowData[7]."','".$rowData[8]."','".$rowData[9]."','".$rowData[10]."','".$rowData[11]."','".$rowData[12]."','".$rowData[13]."','".$rowData[14]."','".$rowData[15]."')";
								 //print_r($sql);
								//echo '<br/>';
								mysqli_query($connection,$sql);
							}
							else
							{
								echo "Duplicate @ ".$sheetName. " :: ".$rowData[0]."<br>";
							}
						}
					}
					echo 'Completed '.$sheetName; 
				}
				else if($sheetName == "BOM")
				{
					//product_material_details
					$sql="truncate product_material_details";
					mysqli_query($connection,$sql);
					for($i=1;$i<count($xlsx->rows($K));$i++)
					{
						$rowData = $xlsx->rows($K)[$i];
						if($rowData[0] != "")
						{
							$isExists=0;
							$sql="Select count(product_code) as mat_code from product_material_details where product_code='".$rowData[0]."' and material_code='".$rowData[4]."'"; 
							if($query=mysqli_query($connection,$sql))
							{
								if(mysqli_num_rows($query)>0)
								{ 
									if($row=mysqli_fetch_array($query)) 
									{	
										if($row["mat_code"] == 1)
											$isExists=1;
									}
								}
							}
							if($isExists == 0)
							{
								$rowData[0]=str_replace(" ","",$rowData[0]);
								$rowData[2]=str_replace(" ","",$rowData[2]);
								$rowData[3]=str_replace(" ","",$rowData[3]);
								$rowData[4]=str_replace(" ","",$rowData[4]);
								$rowData[0]=str_replace(".","",$rowData[0]);
								if(strlen($rowData[7])==0) $rowData[7]='null';
								else $rowData[7]="'".$rowData[7]."'";
								$sql="INSERT INTO `product_material_details`( `product_code`, `is_alternatives_available`, `alternative_caption`, `alternative_key`, `material_code`, `multiplication_factor`, `round_up`,`spl_selling_price`) VALUES ('".$rowData[0]."','".$rowData[1]."','".$rowData[2]."','".$rowData[3]."','".$rowData[4]."','".$rowData[5]."','".$rowData[6]."',".$rowData[7].")";
								//print_r($sql);
								//echo '<br/>';
								mysqli_query($connection,$sql);
							}
							else
							{
								echo "Duplicate @ ".$sheetName. " :: ".$rowData[0]."<br>";
							}
						}
					}
					echo 'Completed '.$sheetName; 
				}
				else if($sheetName == "Product Machine Details")
				{
					$sql="truncate product_machine_detail";
					mysqli_query($connection,$sql);
					for($i=1;$i<count($xlsx->rows($K));$i++)
					{
						$rowData = $xlsx->rows($K)[$i];
						if($rowData[0] != "")
						{
/* 							$isExists=0;
							$sql="Select count(product_code) as mat_code from product_machine_detail where product_code='".$rowData[0]."' and machine_code='".$rowData[1]."'"; 
							if($query=mysqli_query($connection,$sql))
							{
								if(mysqli_num_rows($query)>0)
								{ 
									if($row=mysqli_fetch_array($query)) 
									{	
										if($row["mat_code"] == 1)
											$isExists=1;
									}
							 	}
							}*/
							$isExists=0;
							if($isExists == 0)
							{
								$rowData[0]=str_replace(" ","",$rowData[0]);
								$rowData[0]=str_replace(".","",$rowData[0]);
								$rowData[1]=str_replace(" ","",$rowData[1]);
								$rowData[1]=str_replace(".","",$rowData[1]);
/* 								if(strlen($rowData[2])==0) $rowData[2]="null";
									else  */
								$rowData[2]="'".$rowData[2]."'";
								$sql="INSERT INTO `product_machine_detail`(`product_code`, `machine_code`, `selling_price`) VALUES ('".$rowData[0]."','".$rowData[1]."',".$rowData[2].")";
								
								if(strlen($rowData[1])>0)
								{
/* 									print_r($sql);
									echo '<br/>'; */
									mysqli_query($connection,$sql);
								}
							}
							else
							{
								echo "Duplicate @ ".$sheetName. " :: ".$rowData[0]."<br>";
							}
						}
					}
					echo 'Completed '.$sheetName; 
				}
				else if($sheetName == "Material Alternatives")
				{
					$sql="truncate product_material_details_alternatives";
					mysqli_query($connection,$sql);
					for($i=1;$i<count($xlsx->rows($K));$i++)
					{
						$rowData = $xlsx->rows($K)[$i];
						if($rowData[0] != "")
						{
							$isExists=0;
							$sql="Select count(alternative_key) as mat_code from product_material_details_alternatives where alternative_key='".$rowData[0]."' and material_code='".$rowData[1]."'"; 
							if($query=mysqli_query($connection,$sql))
							{
								if(mysqli_num_rows($query)>0)
								{ 
									if($row=mysqli_fetch_array($query)) 
									{	
										if($row["mat_code"] == 1)
											$isExists=1;
									}
								}
							}
							
							if($isExists == 0)
							{
								$rowData[0]=str_replace(" ","",$rowData[0]);
								$rowData[0]=str_replace(".","",$rowData[0]);
								$rowData[1]=str_replace(" ","",$rowData[1]);
								$rowData[1]=str_replace(".","",$rowData[1]);
								
								
								$sql="INSERT INTO `product_material_details_alternatives`( `alternative_key`, `material_code`, `multiplication_factor`) VALUES ('".$rowData[0]."','".$rowData[1]."','".$rowData[2]."')";
								//print_r($sql);
								//echo '<br/>';
								mysqli_query($connection,$sql);
							}
							else
							{
								echo "Duplicate @ ".$sheetName. " :: ".$rowData[0]." >> ".$rowData[1]."<br>";
							}
						}
					}
					echo 'Completed '.$sheetName; 
				}
				else if($sheetName == "slab_rates")
				{
					$sql="truncate product_slab_rate_details";
					mysqli_query($connection,$sql);
					for($i=1;$i<count($xlsx->rows($K));$i++)
					{
						$rowData = $xlsx->rows($K)[$i];
						if($rowData[0] != "")
						{
/* 							$isExists=0;
							$sql="Select count(product_code) as mat_code from product_slab_rate_details where product_code='".$rowData[0]."'"; 
							if($query=mysqli_query($connection,$sql))
							{
								if(mysqli_num_rows($query)>0)
								{ 
									if($row=mysqli_fetch_array($query)) 
									{	
										if($row["mat_code"] == 1)
											$isExists=1;
									}
								}
							} */
							$isExists=0;
							if($isExists == 0)
							{
								$rowData[0]=str_replace(" ","",$rowData[0]);
								$rowData[0]=str_replace(".","",$rowData[0]);
								
								$sql="INSERT INTO `product_slab_rate_details`(`product_code`, `above_qty`, `selling_price`, `addl_copy_rate`) VALUES ('".$rowData[0]."','".$rowData[1]."','".$rowData[2]."','".$rowData[3]."')";
								//print_r($sql);
								//echo '<br/>';
								mysqli_query($connection,$sql);
							}
							else
							{
								echo "Duplicate @ ".$sheetName. " :: ".$rowData[0]."<br>";
							}
						}
					}
					echo 'Completed '.$sheetName; 
				}
 
			} 
        } else {
            echo "Error uploading the file.";
        }
    } else {
        echo "No file uploaded or there was an upload error.";
    }
} 
/* $sql = "update product_master set product_name=REPLACE(product_name,'.','')";
mysqli_query($connection,$sql); */
$sql = "update product_master set product_name=REPLACE(product_name,'/-','')";
mysqli_query($connection,$sql);
$sql = "update product_master set product_name=REPLACE(product_name,'&','')";
mysqli_query($connection,$sql);
$sql = "update product_master set product_name=TRIM(product_name)";
mysqli_query($connection,$sql);

$sql = "update product_master set product_code=TRIM(product_code)";
mysqli_query($connection,$sql);
$sql = "update product_master set product_code=REPLACE(product_code,'.','')";
mysqli_query($connection,$sql); 
$sql = "update product_master set product_code=REPLACE(product_code,'/-','')";
mysqli_query($connection,$sql);

$sql = "update material_master set material_code=REPLACE(material_code,'/-','')";
mysqli_query($connection,$sql);
$sql = "update material_master set material_code=REPLACE(material_code,'.','')";
mysqli_query($connection,$sql);
$sql = "update material_master set material_code=TRIM(material_code)";
mysqli_query($connection,$sql);

$sql = "update machine_master set machine_code=TRIM(machine_code)";
mysqli_query($connection,$sql);
$sql = "update machine_master set machine_name=TRIM(machine_name)";
mysqli_query($connection,$sql);

$sql = "update product_material_details set product_code=REPLACE(product_code,'/-','')";
mysqli_query($connection,$sql);
$sql = "update product_material_details set product_code=REPLACE(product_code,'.','')";
mysqli_query($connection,$sql);
$sql = "update product_material_details set product_code=TRIM(product_code)";
mysqli_query($connection,$sql);

$sql = "update product_material_details set material_code=REPLACE(material_code,'/-','')";
mysqli_query($connection,$sql);
$sql = "update product_material_details set material_code=REPLACE(material_code,'.','')";
mysqli_query($connection,$sql);
$sql = "update product_material_details set material_code=TRIM(material_code)";
mysqli_query($connection,$sql);

$sql = "update product_machine_detail set machine_code=REPLACE(machine_code,'/-','')";
mysqli_query($connection,$sql);
$sql = "update product_machine_detail set machine_code=REPLACE(machine_code,'.','')";
mysqli_query($connection,$sql);
$sql = "update product_machine_detail set machine_code=TRIM(machine_code)";
mysqli_query($connection,$sql);

$sql = "update product_machine_detail set product_code=REPLACE(product_code,'/-','')";
mysqli_query($connection,$sql);
$sql = "update product_machine_detail set product_code=REPLACE(product_code,'.','')";
mysqli_query($connection,$sql);
$sql = "update product_machine_detail set product_code=TRIM(product_code)";
mysqli_query($connection,$sql);

$sql = "update product_material_details_alternatives set material_code=REPLACE(material_code,'/-','')";
mysqli_query($connection,$sql);
$sql = "update product_material_details_alternatives set material_code=REPLACE(material_code,'.','')";
mysqli_query($connection,$sql);
$sql = "update product_material_details_alternatives set material_code=TRIM(material_code)";
mysqli_query($connection,$sql);

$sql = "update product_slab_rate_details set product_code=REPLACE(product_code,'/-','')";
mysqli_query($connection,$sql);
$sql = "update product_slab_rate_details set product_code=REPLACE(product_code,'.','')";
mysqli_query($connection,$sql);
$sql = "update product_slab_rate_details set product_code=TRIM(product_code)";
mysqli_query($connection,$sql);

echo "<hr>Done";
?>