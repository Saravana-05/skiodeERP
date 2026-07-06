<?php
$result="";  
include_once '../connect_db.php';

require __DIR__ . '/vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

            // Read the Excel file using PhpSpreadsheet
            $spreadsheet = IOFactory::load($targetFile);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();
			

			$spreadsheet = new Spreadsheet();

			$inputFileType = 'Xlsx';
			$inputFileName = $targetFile;

			/**  Create a new Reader of the type defined in $inputFileType  **/
			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
			/**  Advise the Reader that we only want to load cell data  **/
			$reader->setReadDataOnly(true);

			$worksheetData = $reader->listWorksheetInfo($inputFileName);

			foreach ($worksheetData as $worksheet) {

				$sheetName = $worksheet['worksheetName'];

				echo "<h4>$sheetName</h4>";
				/**  Load $inputFileName to a Spreadsheet Object  **/
				$reader->setLoadSheetsOnly($sheetName);
				$spreadsheet = $reader->load($inputFileName);

				$worksheet = $spreadsheet->getActiveSheet();
				//print_r($worksheet->toArray());
				$sheetArray = $worksheet->toArray();
				if($sheetName == "Material Master")
				{
					$sql="Delete from material_master";
					mysqli_query($connection,$sql);
					for($i=1;$i<count($sheetArray);$i++)
					{
						$rowData=$sheetArray[$i];
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
								$sql="INSERT INTO `material_master`(`material_code`, `material_name`, `stock_in_hand`, `stock_as_on_dt`, `purchase_price`, `selling_price`, `loose_sales_allowed`,`open_slab_rates`, `is_active`) VALUES ('".$rowData[0]."','".$rowData[1]."','".$rowData[2]."','".$rowData[3]."','".$rowData[4]."','".$rowData[5]."','".$rowData[6]."','".$rowData[7]."',1)";
								//print_r($sql);
								//echo '<br/>';
								mysqli_query($connection,$sql);
							}
						}
					}
					echo 'Completed '.$sheetName; 
				}
				else if($sheetName == "Machine Master")
				{
					$sql="Delete from machine_master";
					mysqli_query($connection,$sql);
					for($i=1;$i<count($sheetArray);$i++)
					{
						$rowData=$sheetArray[$i];
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
								$sql="INSERT INTO `machine_master`(`machine_code`, `machine_name`, `machine_desc`, `counter_reading`, `counter_on_dt`) VALUES ('".$rowData[0]."','".$rowData[1]."','".$rowData[2]."','".$rowData[3]."','".$rowData[4]."')";
								//print_r($sql);
								//echo '<br/>';
								mysqli_query($connection,$sql);
							}
						}
					}
					echo 'Completed '.$sheetName; 
				}
				else if($sheetName == "Product Master")
				{
					$sql="Delete from product_master";
					mysqli_query($connection,$sql);
					for($i=1;$i<count($sheetArray);$i++)
					{
						$rowData=$sheetArray[$i];
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
								$sql="INSERT INTO `product_master`(`product_code`, `product_name`, `selling_price`, `1st_copy_addl_copy_applicable`, `is_front_and_back_option_available`, `addl_copy_rate`, `category1`, `category2`, `open_slab_rates`) VALUES ('".$rowData[0]."','".$rowData[1]."','".$rowData[2]."','".$rowData[3]."','".$rowData[4]."','".$rowData[5]."','".$rowData[6]."','".$rowData[7]."','".$rowData[8]."')";
								 //print_r($sql);
								//echo '<br/>';
								mysqli_query($connection,$sql);
							}
						}
					}
					echo 'Completed '.$sheetName; 
				}
				else if($sheetName == "BOM")
				{
					//product_material_details
					$sql="Delete from product_material_details";
					mysqli_query($connection,$sql);
					for($i=1;$i<count($sheetArray);$i++)
					{
						$rowData=$sheetArray[$i];
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
								$sql="INSERT INTO `product_material_details`( `product_code`, `is_alternatives_available`, `alternative_caption`, `alternative_key`, `material_code`, `multiplication_factor`, `round_up`) VALUES ('".$rowData[0]."','".$rowData[1]."','".$rowData[2]."','".$rowData[3]."','".$rowData[4]."','".$rowData[5]."','".$rowData[6]."')";
								//print_r($sql);
								//echo '<br/>';
								mysqli_query($connection,$sql);
							}
						}
					}
					echo 'Completed '.$sheetName; 
				}
				else if($sheetName == "Product Machine Details")
				{
					$sql="Delete from product_machine_detail";
					mysqli_query($connection,$sql);
					for($i=1;$i<count($sheetArray);$i++)
					{
						$rowData=$sheetArray[$i];
						if($rowData[0] != "")
						{
							$isExists=0;
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
							}
							if($isExists == 0)
							{
								$sql="INSERT INTO `product_machine_detail`(`product_code`, `machine_code`, `selling_price`) VALUES ('".$rowData[0]."','".$rowData[1]."','".$rowData[2]."')";
								//print_r($sql);
								//echo '<br/>';
								mysqli_query($connection,$sql);
							}
						}
					}
					echo 'Completed '.$sheetName; 
				}
				else if($sheetName == "Material Alternatives")
				{
					$sql="Delete from product_material_details_alternatives";
					mysqli_query($connection,$sql);
					for($i=1;$i<count($sheetArray);$i++)
					{
						$rowData=$sheetArray[$i];
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
								$sql="INSERT INTO `product_material_details_alternatives`( `alternative_key`, `material_code`, `multiplication_factor`) VALUES ('".$rowData[0]."','".$rowData[1]."','".$rowData[2]."')";
								//print_r($sql);
								//echo '<br/>';
								mysqli_query($connection,$sql);
							}
						}
					}
					echo 'Completed '.$sheetName; 
				}
				else if($sheetName == "slab_rates")
				{
					$sql="Delete from product_slab_rate_details";
					mysqli_query($connection,$sql);
					for($i=1;$i<count($sheetArray);$i++)
					{
						$rowData=$sheetArray[$i];
						if($rowData[0] != "")
						{
							$isExists=0;
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
							}
							if($isExists == 0)
							{
								$sql="INSERT INTO `product_slab_rate_details`(`product_code`, `above_qty`, `selling_price`, `addl_copy_rate`) VALUES ('".$rowData[0]."','".$rowData[1]."','".$rowData[2]."','".$rowData[3]."')";
								//print_r($sql);
								//echo '<br/>';
								mysqli_query($connection,$sql);
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
$sql = "update product_master set product_name=REPLACE(product_name,'.','')";
mysqli_query($connection,$sql);
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

echo $result;
?>