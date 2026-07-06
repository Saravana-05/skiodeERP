<?php
session_start();
include_once "connect_db.php";
?>
<style>

.dashboardContent
{
	margin-top: 20px !important;
    border: 2px solid #302f2f;
	border-radius: 4px;
	min-height:550px;
}
.dashboardContent h4
{
	margin-top: -25px; 
    background: white;
} 
div.dt-container div.dt-length select {
    width: 35%; 
}
div.dt-container div.dt-length label {
	width: 55%; 
}
.iconDivCls{
	cursor:pointer;
}


/* Design Upload Modal */
#designUploadModal .upload-drop-zone {
    border: 2px dashed #6c757d;
    border-radius: 8px;
    padding: 24px 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.25s, background 0.25s;
    background: #f8f9fa;
}
#designUploadModal .upload-drop-zone.dragover {
    border-color: #0d6efd;
    background: #e8f0fe;
}
#designUploadModal .upload-drop-zone .upload-icon {
    font-size: 2rem; color: #6c757d; display: block; margin-bottom: 6px;
}
#designUploadModal .reason-section {
    margin-top: 14px; padding: 12px;
    background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px;
}
#du_progress_wrap { display: none; margin-top: 10px; }
 
/* Multi-file preview grid */
#du_files_preview_list {
    display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px;
}
.du-file-chip {
    display: flex; align-items: center; gap: 6px;
    background: #e9ecef; border-radius: 20px;
    padding: 4px 10px; font-size: 0.78rem; max-width: 260px;
}
.du-file-chip .chip-name {
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 160px;
}
.du-file-chip .chip-remove {
    cursor: pointer; color: #dc3545; font-weight: bold;
    background: none; border: none; padding: 0; font-size: 0.9rem; line-height: 1;
}
 
/* Existing files table */
.du-existing-files-table { width: 100%; font-size: 0.78rem; border-collapse: collapse; margin-top: 8px; }
.du-existing-files-table th { background: #343a40; color: #fff; padding: 6px 8px; text-align: left; }
.du-existing-files-table td { padding: 5px 8px; border-bottom: 1px solid #dee2e6; vertical-align: middle; }
.du-existing-files-table tr:last-child td { border-bottom: none; }
.du-file-thumb { width: 36px; height: 36px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6; }
.du-file-icon-box {
    width: 36px; height: 36px; border-radius: 4px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; border: 1px solid #dee2e6; background: #f8f9fa;
}

</style>

<div class="bg_aliceblue p-3 m-1 pt-0 dashboardContent">
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-2 mb-1">
		<h4 class="mb-0">Dashboard</h4> 			
	</div>
	<div class="row allIconDivCls">

		<div class="col-md-1 iconDivCls" onclick="fnModule(1)">
			<p>JOB <br>CARD</p>
			<img loading="lazy" class="iconImgCls" src="img/job_card.png" alt="JOB CARD" />
		</div>
		
	<?php 
		if($_SESSION['user_type'] == "ADMIN" || $_SESSION['user_type'] == "SUPERADMIN")
		{
			?>		
		<div class="col-md-1 iconDivCls" onclick="fnModule(14)">
			<p>MASTER <br>DATA</p>
			<img loading="lazy" class="iconImgCls" src="img/sale_invoice.png" alt="Data Entry" />
		</div>
		<div class="col-md-1 iconDivCls" onclick="fnModule(4)">
			<p>CUSTOMER<br>MASTER</p>
			<img loading="lazy" class="iconImgCls" src="img/add_new_customer.png" alt="ADD NEW CUSTOMER" />
		</div>

		<div class="col-md-1 iconDivCls" onclick="fnModule(9)">
			<p>USER<br>MASTER</p>
			<img loading="lazy" class="iconImgCls" src="img/user_master.png" alt="ADD NEW CUSTOMER" />
		</div>
		<?php 
		}
		?>
		<div class="col-md-1 iconDivCls" onclick="fnModule(5)">
			<p>RECEIPT <br>VOUCHER</p>
			<img loading="lazy" class="iconImgCls" src="img/receipt_voucher.png" alt="RECEIPT VOUCHER" />
		</div>
		<div class="col-md-1 iconDivCls" onclick="fnModule(6)">
			<p>PAYMENT <br>VOUCHER</p>
			<img loading="lazy" class="iconImgCls" src="img/payment_voucher.png" alt="PAYMENT VOUCHER" />
		</div>		
		<div class="col-md-1 iconDivCls" onclick="fnModule(10)">
			<p>EOD<br>PROCESS</p>
			<img loading="lazy" class="iconImgCls" src="img/eod_process.png" alt="ADD NEW CUSTOMER" />
		</div>
		<div style="float: right;
    width: 25%;
    height: 160px;
    overflow: auto;
    background: #f12727;
    color: white;
    border: 2px black dotted;
    border-radius: 10px 5px;
    position: absolute;
    right: 28px;">
			<center><b>Low Stock Items</b></center>
			<?php include "api/get_low_stock.php";?>
		</div>
	</div>
	<?php 
		if($_SESSION['user_type'] == "SUPERADMIN")
		{
	?>
	<div class="row mt-2"> 
		 <label for="date1Txt" class="col-md-1 col-form-label">Date</label>
		 <div class="col-md-2">
			<input type="text" autocomplete="off" 
			   class="form-control datepicker" 
			   id="date1Txt" name="date1Txt" 
			   placeholder="dd-MM-yyyy" value="<?php echo date("d-m-Y");?>"/>
		 </div> 
		<label for="date2Txt" class="col-md-1 col-form-label">Date</label>
		 <div class="col-md-2">
			<input type="text" autocomplete="off" 
			   class="form-control datepicker" 
			   id="date2Txt" name="date2Txt" 
			   placeholder="dd-MM-yyyy" value="<?php echo date("d-m-Y");?>"/>
		 </div> 
		 <div class="col-md-2">
			<button type="button" id="superadmin_show_btn" class="btn btn-info">Show</button>
		 </div>
		 
	</div>
	<div class="row filterCls mb-3">

	</div>
	<div class="row cardCls mt-3">
		<div class="col-md-3">
			<div class="card text-bg-primary mb-3" >
			  <div class="card-header"><h6 class="card-title">JOB TRANSACTION</h6></div>
			  <div class="card-body">
				 <table>
					<tr>
						<th>JOB CARD</th>
						<td> : </td>
						<td><span id="jc_cnt"></span></td>
					</tr>
					<tr>
						<th>QUOTATION</th>
						<td> : </td>
						<td><span id="sq_cnt"></span></td>
					</tr>
					<tr>
						<th>INVOICE</th>
						<td> : </td>
						<td><span id="si_cnt"></span></td>
					</tr>
					<tr>
						<th>BALANCE</th>
						<td> : </td>
						<td><span id="balance_txt"></span></td>
					</tr>
				 </table>
			  </div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-bg-purple mb-3" >
			  <div class="card-header"><h6 class="card-title">CATEGORY WISE TRANS.</h6></div>
				  <div class="card-body">
					<table>
						<tr>
							<th>PRINTING</th>
							<td>   </td>
							<td><span id="printing_cnt"></span></td>
						</tr>
						<tr>
							<th>SERVICES</th>
							<td>   </td>
							<td><span id="services_cnt"></span></td>
						</tr>
						<tr>
							<th>MATERIALS</th>
							<td>   </td>
							<td><span id="materials_cnt"></span></td>
						</tr>
						<tr>
							<th>ID CARDS</th>
							<td>   </td>
							<td><span id="id_card_cnt"></span></td>
						</tr>
						<tr>
							<th>PRODUCTS</th>
							<td>   </td>
							<td><span id="products_cnt"></span></td>
						</tr>
					 </table>  
			  </div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-bg-danger mb-3" >
			  <div class="card-header"><h6 class="card-title">MACHINE COUNT</h6></div>
			  <div class="card-body" id="machine_count_div">
				<table>
					<tr>
						<th>XEROX IRIDESSE</th>
						<td> : </td>
						<td> </td>
					</tr>
					<tr>
						<th>XEROX 3100</th>
						<td> : </td>
						<td> </td>
					</tr>
					<tr>
						<th>K M 4070</th>
						<td> : </td>
						<td> </td>
					</tr>
					<tr>
						<th>CANNON 6575</th>
						<td> : </td>
						<td> </td>
					</tr>									
				 </table>  
			  </div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-bg-warning mb-3" >
			  <div class="card-header"><h6 class="card-title">TOTAL SALES</h6></div>
			  <div class="card-body" id="total_sales_div">
				<table>
					<tr>
						<th>CASH</th>
						<td> : </td>
						<td> </td>
					</tr>
					<tr>
						<th>SAVINGS A/C</th>
						<td> : </td>
						<td> </td>
					</tr>
					<tr>
						<th>CURRENT A/C</th>
						<td> : </td>
						<td> </td>
					</tr>
					<tr>
						<th>DISCOUNT</th>
						<td> : </td>
						<td> </td>
					</tr>									
				 </table>  
			  </div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-bg-orange mb-3" >
			  <div class="card-header"><h6 class="card-title">CUSTOMER CATEGORY</h6></div>
			  <div class="card-body">
				<table>
					<tr>
						<th>GENERAL CUSTOMER</th>
						<td> : </td>
						<td><span id="general_cnt"></span> </td>
					</tr>
					<tr>
						<th>WALK IN CUSTOMER</th>
						<td> : </td>
						<td><span id="walkin_cnt"></span> </td>
					</tr>
					<tr>
						<th>CREDIT CUSTOMER</th>
						<td> : </td>
						<td><span id="credit_cnt"></span> </td>
					</tr>
					<tr>
						<th> </th>
						<td>  </td>
						<td> </td>
					</tr>									
				 </table>   
			  </div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-bg-success mb-3" >
			  <div class="card-header"><h6 class="card-title">ACTIVE USERS</h6></div>
			  <div class="card-body" id="active_users_div">

			  </div>
			</div>
		</div> 
	</div> 

	<?php 
		}
		else if($_SESSION['user_type'] != "SUPERADMIN")
		{
	?>
	<style>
	.nav-tabs .nav-link
	{
		border:1px dotted #caced2;
		border-right:0;
		border-top-right-radius: 2vw;
	}
	.nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active
	{
		background-color:#04f628;
		font-weight:bolder;
		text-decoration:underline;
	}
	</style>

	<ul class="nav nav-tabs" role="tablist" style="font-size:0.75vw;">
		<li  class="nav-item" role="presentation">
			<button class="nav-link active" id="jc-tab" data-bs-toggle="tab" data-bs-target="#jc_div" type="button" role="tab" aria-controls="jc_div" aria-selected="true"><?php
				$sql="SELECT count(jobcard_no) as cnt  FROM `jobcard_master` where jobcard_date='". date("Y-m-d")."' and job_card_closed=0;";
				if($qry=mysqli_query($connection,$sql))
				{
					if($row=mysqli_fetch_array($qry))
					{
						$cnt=$row["cnt"];
						if($cnt==0)$cnt="";
						echo $cnt;
					}
				}
			?> Job Cards</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link  " id="sq-tab" data-bs-toggle="tab" data-bs-target="#sq_div" type="button" role="tab" aria-controls="sq_div" aria-selected="false">Sales Quotes</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link  " id="si-tab" data-bs-toggle="tab" data-bs-target="#si_div" type="button" role="tab" aria-controls="si_div" aria-selected="false">Sales Invoices</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="jcdl-tab" type="button" onclick="window.location='jc_downloads.php'">
				📁 JC Downloads
			</button>
		</li>
    </ul>
	<div class="tab-content" style="width:100%;height:70%;">
		<div id="jc_div" class="tab-pane fade show active" role="tabpanel" aria-labelledby="jc-tab">
			<div style="position:relative;">
			<h5 class="bg_blue p-1" style="text-align:center;font-size:1vw;">OPEN JOB CARDS</h5>
				
			<?php
			$today = date('Y-m-d');
			$yesterday = date('Y-m-d', strtotime('-1 day', strtotime($today)));
			$before_yesterday = date('Y-m-d', strtotime('-2 day', strtotime($today)));
			$week = date('Y-m-d', strtotime('-7 day', strtotime($today)));
			?>
			
				<table width="100%" style="background-color:teal;color:black;font-weight:bold;font-size:0.75vw;">
				<tr>
				<?php
				if($_SESSION['user_type']!="OPERATOR")
				{
				?>
				<td> From</td><td> <input type="text" size="10" class="datepicker" id="jc_from_dt_txt" value="<?php echo date("d-m-Y");?>"/></td>
				<td> To</td><td> <input type="text" size="10" class="datepicker" id="jc_to_dt_txt" value="<?php echo date("d-m-Y");?>"/></td>
				<?php
				}
				else{				
				?>
				<td>Spl. Filters => </td>
				<td>Date</td>
				<td><select id="jc_date_txt" name="jc_date_txt">
						<option value="<?php echo $today;?>" selected>Today</option>
						<option value="<?php echo $yesterday;?>">Yesterday</option> 
						<option value="<?php echo $before_yesterday;?>">Before Yesterday</option> 
						<option value="<?php echo $week;?>">Week</option> 
						<option value="ALL_OPEN_JC">All Open</option> 
					</select>
				</td>
				<?php
				}
				?>
				<td>Customer</td>
				<td>
					<select id="customer_code_txt" name="customer_code_txt" style="width:200px;">
						<option value="all">All</option>
						<option value="WalkIn">WalkIn</option>
						<option value="General">General</option>
						<?php
							$sql="select customer_code,customer_name from customer_master order by customer_name;";
							if($qry=mysqli_query($connection,$sql))
							{
								while($row=mysqli_fetch_array($qry))
								{
									echo '<option value="'.$row["customer_code"].'">'.$row["customer_name"].'</option>';
								}
							}
						?>
					</select>									
				</td>
				<td>User</td>	
				<td>
					<select id="user_name_txt" name="user_name_txt">						
						<?php
							if($_SESSION["user_type"]!="OPERATOR")
							{
							echo '<option value="all">All</option>';
							$sql="select * from user_master;";
							if($qry=mysqli_query($connection,$sql))
							{
								while($row=mysqli_fetch_array($qry))
								{
									echo '<option value="'.$row["user_name"].'">'.$row["user_display_name"].'</option>';
								}
							}
							}
							else
							{
								echo '<option value="'.$_SESSION['user_name'].'">'.$_SESSION['user_display_name'].'</option>';	
							}
							
						?>
					</select>
				</td>
				<td>Pay Mode</td>
				<td>
					<select id="pay_mode_txt" name="pay_mode_txt">
						<option value="all">All</option>
						<option value="cash">Cash</option>
						<option value="online">Online</option>		
						<option value="credit">Credit</option>	
					</select>
				</td>	
				<?php
				if($_SESSION['user_type']!="OPERATOR")
				{
					$hi=1;
				?>
				<td>Status</td>
				<td>
					<select id="status_txt" name="status_txt">
						<option value="all">All</option>
						<option value="0" selected>Open</option>
						<option value="1">Closed</option>
					</select>
				<?php
				}
				?>				
				<td>
				<button type="button" class="btn btn-sm btn-darkpurple" onclick="loadJobCardList()">Search</button> 
				</td>
				</tr>
				</table>	 
						
				
				<div style="position:absolute;left:250px; top:73px;width:65%;z-index:100;">
					<button class="btn btn-sm btn-success" style="font-size: 0.75vw;" onclick="select_all_jcs()">Select All JC</button>
					<button class="btn btn-sm btn-danger" style="font-size: 0.75vw;" onclick="unselect_all_jcs()">Unselect All</button>
					<button class="btn btn-sm btn-info" style="font-size: 0.75vw;" onclick="close_selected_jcs()">Close Selected</button>
					<input type="button" style="font-size: 0.75vw;" class="btn btn-sm btn-warning" name="searchBtn" id="searchBtn" value="Refresh" onclick="loadJobCardList()" style=" "/>
				</div>	
				<div id="jobcardDiv" style="position:relative;margin-top:5px;font-size:0.75vw;">
					
				</div>
			</div>
		</div>
		<div id="sq_div" class="tab-pane fade" role="tabpanel" aria-labelledby="sq-tab">
			<div style="position:relative;">
			<h5 class=" p-1" style="text-align:center;font-size:1vw;">OPEN SALES QUOTES</h5>
			<?php
			$today = date('Y-m-d');
			$yesterday = date('Y-m-d', strtotime('-1 day', strtotime($today)));
			$before_yesterday = date('Y-m-d', strtotime('-2 day', strtotime($today)));
			$week = date('Y-m-d', strtotime('-7 day', strtotime($today)));
			?>
			
				<table width="100%" style="background-color:#ffabff;color:black;font-weight:bold;font-size:0.75vw;">
				<tr>
				<?php
				if($_SESSION['user_type']!="OPERATOR")
				{
				?>
				<td> From</td><td> <input type="text" size="10" class="datepicker" id="sq_from_dt_txt" value="<?php echo date("d-m-Y");?>"/></td>
				<td> To</td><td> <input type="text" size="10" class="datepicker" id="sq_to_dt_txt" value="<?php echo date("d-m-Y");?>"/></td>
				<?php
				}
				else{				
				?>
				<td>Spl. Filters => </td>
				<td>Date</td>
				<td><select id="sq_date_txt" name="sq_date_txt">
						<option value="<?php echo $today;?>" selected>Today</option>
						<option value="<?php echo $yesterday;?>">Yesterday</option> 
						<option value="<?php echo $before_yesterday;?>">Before Yesterday</option> 
						<option value="<?php echo $week;?>">Week</option> 
					</select>
				</td>
				<?php
				}
				?>				
				<td>Customer</td>
				<td>
					<select id="sq_customer_code_txt" name="sq_customer_code_txt" style="width:200px;">
						<option value="all">All</option>
						<option value="WalkIn">WalkIn</option>
						<option value="General">General</option>
						<?php
							$sql="select customer_code,customer_name from customer_master order by customer_name;";
							if($qry=mysqli_query($connection,$sql))
							{
								while($row=mysqli_fetch_array($qry))
								{
									echo '<option value="'.$row["customer_code"].'">'.$row["customer_name"].'</option>';
								}
							}
						?>
					</select>									
				</td>
				<td>User</td>	
				<td>
					<select id="sq_user_name_txt" name="sq_user_name_txt">						
						<?php
							if($_SESSION["user_type"]!="OPERATOR")
							{
							echo '<option value="all">All</option>';
							$sql="select * from user_master;";
							if($qry=mysqli_query($connection,$sql))
							{
								while($row=mysqli_fetch_array($qry))
								{
									echo '<option value="'.$row["user_name"].'">'.$row["user_display_name"].'</option>';
								}
							}
							}
							else
							{
								echo '<option value="'.$_SESSION['user_name'].'">'.$_SESSION['user_display_name'].'</option>';	
							}
							
						?>
					</select>
				</td>
				<td>Pay Mode</td>
				<td>
					<select id="sq_pay_mode_txt" name="sq_pay_mode_txt">
						<option value="all">All</option>
						<option value="cash">Cash</option>
						<option value="online">Online</option>		
						<option value="credit">Credit</option>	
					</select>
				</td>	
				<?php
				if($_SESSION['user_type']!="OPERATOR")
				{
					$hi=1;
				?>
				<td>Status</td>
				<td>
					<select id="sq_status_txt" name="sq_status_txt">
						<option value="all">All</option>
						<option value="0" selected>Open</option>
						<option value="1">Closed</option>
					</select>
				</td>
				<?php
				}
				?>				
				<td>
				<button type="button" class="btn btn-sm btn-darkpurple" onclick="loadSQList()">Search</button> 
				</td>
				</tr>
				</table>	 
						
			<div style="position:absolute;left:200px; top:73px;width:65%;z-index:100;">
				<button class="btn btn-sm btn-success" style="font-size: 0.75vw;" onclick="select_all_sqs()">Select All SQ</button>
				<button class="btn btn-sm btn-danger" style="font-size: 0.75vw;" onclick="unselect_all_sqs()">Unselect All</button>
				<button class="btn btn-sm btn-info" style="font-size: 0.75vw;" onclick="sales_invoice_sqs()">Sales Invoice</button>
				<input type="button" style="font-size: 0.75vw;" class="btn btn-sm btn-warning" name="searchSQBtn" id="searchSQBtn" value="Refresh" onclick="loadSQList()" style=""/>
			</div>		
			
				<div id="sqDiv" style="position:relative;margin-top:5px;font-size:0.75vw;">
					
				</div>
			</div>		
		</div>
		<div id="si_div" class="tab-pane fade" role="tabpanel" aria-labelledby="si-tab">
			<div style="position:relative;">
			<h5 class=" p-1" style="text-align:center;font-size:1vw;">OPEN SALES INVOICES</h5>
<?php
			$today = date('Y-m-d');
			$yesterday = date('Y-m-d', strtotime('-1 day', strtotime($today)));
			$before_yesterday = date('Y-m-d', strtotime('-2 day', strtotime($today)));
			$week = date('Y-m-d', strtotime('-7 day', strtotime($today)));
			?>
			
				<table width="100%" style="background-color:cyan;color:black;font-weight:bold;font-size:0.75vw;">
				<tr>
<?php
				if($_SESSION['user_type']!="OPERATOR")
				{
				?>
				<td> From</td><td> <input type="text" size="10" class="datepicker" id="si_from_dt_txt" value="<?php echo date("d-m-Y");?>"/></td>
				<td> To</td><td> <input type="text" size="10" class="datepicker" id="si_to_dt_txt" value="<?php echo date("d-m-Y");?>"/></td>
				<?php
				}
				else{				
				?>
				<td>Spl. Filters => </td>
				<td>Date</td>
				<td><select id="si_date_txt" name="si_date_txt">

						<option value="<?php echo $today;?>" selected>Today</option>
						<option value="<?php echo $yesterday;?>">Yesterday</option> 
						<option value="<?php echo $before_yesterday;?>">Before Yesterday</option> 
						<option value="<?php echo $week;?>">Week</option> 
					</select>
				</td>
				<?php
				}
				?>					
				<td>Customer</td>
				<td>
					<select id="si_customer_code_txt" style="width:200px;" name="si_customer_code_txt">
						<option value="all">All</option>
						<option value="WalkIn">WalkIn</option>
						<option value="General">General</option>
						<?php
							$sql="select customer_code,customer_name from customer_master order by customer_name;";
							if($qry=mysqli_query($connection,$sql))
							{
								while($row=mysqli_fetch_array($qry))
								{
									echo '<option value="'.$row["customer_code"].'">'.$row["customer_name"].'</option>';
								}
							}
						?>
					</select>									
				</td>
				<td>User</td>	
				<td>
					<select id="si_user_name_txt" name="si_user_name_txt">						
						<?php
							if($_SESSION["user_type"]!="OPERATOR")
							{
							echo '<option value="all">All</option>';
							$sql="select * from user_master;";
							if($qry=mysqli_query($connection,$sql))
							{
								while($row=mysqli_fetch_array($qry))
								{
									echo '<option value="'.$row["user_name"].'">'.$row["user_display_name"].'</option>';
								}
							}
							}
							else
							{
								echo '<option value="'.$_SESSION['user_name'].'">'.$_SESSION['user_display_name'].'</option>';	
							}
							
						?>
					</select>
				</td>
				<td>Pay Mode</td>
				<td>
					<select id="si_pay_mode_txt" name="si_pay_mode_txt">
						<option value="all">All</option>
						<option value="cash">Cash</option>
						<option value="online">Online</option>		
						<option value="credit">Credit</option>	
					</select>
				</td>	
				
				<td>
				<button type="button" class="btn btn-sm btn-darkpurple" onclick="loadSIList()">Search</button> 
				</td>
				</tr>
				</table>	 
						
			<div style="position:absolute;left:200px; top:73px;width:65%;z-index:100;">
				<input type="button" style="font-size: 0.75vw;" class="btn btn-sm btn-warning" name="searchSIBtn" id="searchSIBtn" value="Refresh" onclick="loadSIList()" style=""/>
			</div>		
			
				<div id="siDiv" style="position:relative;margin-top:5px;font-size:0.75vw;">
					
				</div>
			</div>		
		</div>
	</div>	

	<?php
		}
	?>

</div>

<!-- ══════════════════════════════════════════════════════════════════════════
     MODAL 1 (NEW): Design File Upload — shown BEFORE Job Card Details modal
     ══════════════════════════════════════════════════════════════════════════ -->
<div class="modal fade modal-lg" id="designUploadModal" tabindex="-1"
     aria-labelledby="designUploadModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
 
      <div class="modal-header bg-warning text-dark" style="padding:0.5rem 1rem;">
        <h5 class="modal-title" id="designUploadModalLabel">
          <i class="fas fa-upload me-2"></i>
          Design Files — JC #<span id="du_jc_no_display"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
 
      <div class="modal-body">
 
        <!-- Customer info strip -->
        <div class="d-flex gap-3 mb-3 p-2 bg-light rounded border" style="font-size:0.85rem;">
          <span><strong>Customer:</strong> <span id="du_customer_name_display"></span></span>
          <span><strong>Mobile:</strong> <span id="du_customer_mobile_display"></span></span>
        </div>
 
        <!-- Hidden fields -->
        <input type="hidden" id="du_jc_no"           value="" />
        <input type="hidden" id="du_customer_name"   value="" />
        <input type="hidden" id="du_customer_mobile" value="" />
 
        <!-- ── EXISTING FILES (shown when files already uploaded) ── -->
        <div id="du_existing_files_section" style="display:none;" class="mb-3">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <strong style="font-size:0.85rem;">
              <i class="fas fa-folder-open text-success me-1"></i>
              Already Uploaded Files
            </strong>
            <span class="badge bg-success" id="du_existing_count">0</span>
          </div>
          <table class="du-existing-files-table" id="du_existing_files_table">
            <thead>
              <tr>
                <th style="width:44px;"></th>
                <th>File Name</th>
                <th>Type</th>
                <th>Size</th>
                <th>Uploaded</th>
                <th>By</th>
                <th style="width:70px;">Action</th>
              </tr>
            </thead>
            <tbody id="du_existing_files_tbody"></tbody>
          </table>
          <hr class="mt-3"/>
        </div>
 
        <!-- ── NEW FILE UPLOAD ZONE ── -->
        <div class="upload-drop-zone" id="du_drop_zone"
             onclick="document.getElementById('du_file_input').click()">
          <span class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></span>
          <div><strong>Click to browse</strong> or drag &amp; drop files here</div>
          <div class="text-muted mt-1" style="font-size:0.75rem;">
            <strong>Multiple files allowed</strong> &nbsp;•&nbsp;
            PDF, PSD, JPEG, JPG, PNG &nbsp;•&nbsp; Max 20 MB each
          </div>
        </div>
 
        <!-- Hidden multiple file input -->
        <input type="file" id="du_file_input"
               accept=".pdf,.psd,.jpeg,.jpg,.png"
               multiple style="display:none;"
               onchange="du_onFilesSelected(this)" />
 
        <!-- New-files preview chips -->
        <div id="du_files_preview_list"></div>
 
        <!-- Upload progress -->
        <div id="du_progress_wrap">
          <div class="progress mt-2" style="height:16px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                 id="du_progress_bar" role="progressbar" style="width:0%">0%</div>
          </div>
          <div class="text-muted mt-1" style="font-size:0.75rem;" id="du_progress_label">Uploading…</div>
        </div>
 
        <!-- OR divider -->
        <div class="d-flex align-items-center my-3">
          <hr class="flex-grow-1"><span class="mx-2 text-muted fw-bold" style="font-size:0.8rem;">OR — No file?</span><hr class="flex-grow-1">
        </div>
 
        <!-- Reason section -->
        <div class="reason-section">
          <label for="du_no_file_reason" class="form-label fw-bold mb-1" style="font-size:0.82rem;">
            <i class="fas fa-comment-alt me-1 text-warning"></i>
            Reason for not uploading a design file
          </label>
          <textarea id="du_no_file_reason" class="form-control" rows="2"
                    placeholder="e.g. Customer will send file later / No design needed / Hard copy provided…"
                    style="font-size:0.82rem;"></textarea>
          <div class="form-text text-muted" style="font-size:0.72rem;">
            Fill this ONLY when no file is uploaded above.
          </div>
        </div>
 
      </div><!-- /modal-body -->
 
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="du_proceed_btn"
                onclick="du_submitAndProceed()">
          <i class="fas fa-check me-1"></i> Save &amp; Proceed to Job Card Details
        </button>
      </div>
 
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════════════════
     MODAL 2 (EXISTING): Job Card Details — shown AFTER design upload
     ══════════════════════════════════════════════════════════════════════════ -->
<div class="modal fade modal-xl" id="jobcardCompletedModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-success  text-white" style="padding: 0.5rem 1rem;">
				<h5 class="modal-title" id="exampleModalLabel">Job Card Details</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class=" row  m-0 p-0">
					<input type="hidden" id="jc_id" value="" />
					<input type="hidden" id="jc_dt" value="" />
					<div id="materialUsageSpilageDetailDiv" class="col-12 p-1"  >				 
					</div>	
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" onclick="fnMarkCompleteJobCard()" id="completedJCBtn">Completed</button>
			</div>
		</div>
	</div>
</div>

<!-- SQ Mini Edit Modal (unchanged) -->
<div class="modal fade" id="SQ_Mini_Edit_modal" tabindex="-1" aria-labelledby="SQ_Mini_Edit_modalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-success  text-white" style="padding: 0.5rem 1rem;">
				<h5 class="modal-title" id="SQ_Mini_Edit_modalLabel">SQ Mini Edit</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
			<table>
        <tr><td>Quote No : </td><td><input type="text" id="SQ_Mini_Editquotation_no_txt" disabled/></td></tr>
		<tr><td>Discount : </td><td><input type="text" class="numericOnly" id="SQ_Mini_Editdiscount_txt" style="text-align:right;"/></td></tr>
		<tr><td>Balance Cash : </td><td><input type="text" class="numericOnly" id="SQ_Mini_Editbalance_cash_txt" style="text-align:right;"/></td></tr>
		<tr><td>Balance Online : </td><td><input type="text" class="numericOnly" id="SQ_Mini_Editbalance_gpay_txt" style="text-align:right;"/></td></tr>
		</table>
		
      </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" onclick="fnSQ_Mini_Edit_Update()" id="SQ_Mini_Edit_Btn">Submit</button>
			</div>
    </div>

  </div>
</div>	  
	  
<script>
/* ═══════════════════════════════════════════════════════════════
   DESIGN UPLOAD MODAL — v2 (multi-file, edit/delete, bug fixed)
   ═══════════════════════════════════════════════════════════════ */
 
var du_pendingFiles = []; // File objects staged by user (not yet uploaded)
 
/* ── Entry point ──────────────────────────────────────────────
   Called from the "In Progress" button in get_jobcard_list.php */
function fnDesignUploadPopUp(pJobCardNo, pCustomerName, pCustomerMobile) {
    du_pendingFiles = [];
    $('#du_jc_no').val(pJobCardNo);
    $('#du_customer_name').val(pCustomerName);
    $('#du_customer_mobile').val(pCustomerMobile);
    $('#du_jc_no_display').text(pJobCardNo);
    $('#du_customer_name_display').text(pCustomerName || '—');
    $('#du_customer_mobile_display').text(pCustomerMobile || '—');
    $('#du_no_file_reason').val('');
    $('#du_file_input').val('');
    $('#du_files_preview_list').html('');
    $('#du_progress_wrap').hide();
    du_resetProgressBar();
    du_setDropZoneStyle(false);
    $('#du_existing_files_section').hide();
    $('#du_existing_files_tbody').html('');
 
    // Check for existing uploads
    $.ajax({
        type: 'GET',
        url: 'api/get_jc_design_upload.php',
        data: { jobcard_no: pJobCardNo },
        async: false,
        success: function(resp) {
            try {
                var r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                if (r.status === 'found') {
                    var files = [];
                    if (r.data.files_json) {
                        try { files = JSON.parse(r.data.files_json); } catch(e) {}
                    }
                    // Fallback: legacy single file
                    if (!files.length && r.data.file_name) {
                        files = [{
                            file_name: r.data.file_name,
                            file_path: r.data.file_path,
                            file_type: r.data.file_type,
                            file_size_kb: r.data.file_size_kb,
                            original_name: r.data.file_name,
                            uploaded_at: r.data.uploaded_at,
                            uploaded_by: r.data.uploaded_by
                        }];
                    }
                    // Also show reason if present
                    if (r.data.upload_reason) {
                        $('#du_no_file_reason').val(r.data.upload_reason);
                    }
                    if (files.length > 0) {
                        du_renderExistingFiles(files, pJobCardNo);
                    }
                }
            } catch(e) {}
        }
    });
 
    $('#designUploadModal').modal('show');
}
 
/* ── Render existing uploaded files table ─────────────────────*/
function du_renderExistingFiles(files, jcNo) {
    $('#du_existing_count').text(files.length);
    var rows = '';
    files.forEach(function(f) {
        var ext = (f.file_type || '').toLowerCase();
        var isImage = ['jpeg','jpg','png'].includes(ext);
        var thumb = isImage
            ? '<img src="' + f.file_path + '" class="du-file-thumb" onerror="this.style.display=\'none\'">'
            : '<div class="du-file-icon-box">' + du_fileIcon(ext) + '</div>';
 
        var dispName = f.original_name || f.file_name || '—';
        var sizeStr  = f.file_size_kb > 1024
                     ? (f.file_size_kb/1024).toFixed(2)+' MB'
                     : f.file_size_kb+' KB';
        var uploadedAt = (f.uploaded_at || '').substring(0,10);
        var uploadedBy = f.uploaded_by || '—';
 
        rows += '<tr id="du_row_' + du_safeId(f.file_name) + '">'
             + '<td>' + thumb + '</td>'
             + '<td title="' + du_esc(f.file_name) + '">' + du_esc(dispName) + '</td>'
             + '<td><span class="badge bg-secondary">' + ext.toUpperCase() + '</span></td>'
             + '<td>' + sizeStr + '</td>'
             + '<td>' + uploadedAt + '</td>'
             + '<td>' + du_esc(uploadedBy) + '</td>'
             + '<td>'
             + '<button class="btn btn-danger btn-sm py-0 px-1" style="font-size:0.72rem;"'
             + ' onclick="du_deleteFile(' + jcNo + ',\'' + du_esc(f.file_name) + '\')">'
             + '<i class="fas fa-trash-alt"></i></button>'
             + '</td>'
             + '</tr>';
    });
    $('#du_existing_files_tbody').html(rows);
    $('#du_existing_files_section').show();
}
 
/* ── Delete an existing file ──────────────────────────────────*/
function du_deleteFile(jcNo, fileName) {
    if (!confirm('Delete this file from JC #' + jcNo + '? This cannot be undone.')) return;
    $.ajax({
        type: 'POST',
        url:  'api/delete_jc_design_file.php',
        data: { jobcard_no: jcNo, file_name: fileName },
        success: function(resp) {
            try {
                var r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                if (r.status === 'success') {
                    toastr.success('File deleted.');
                    if (r.files && r.files.length > 0) {
                        du_renderExistingFiles(r.files, jcNo);
                    } else {
                        $('#du_existing_files_section').hide();
                    }
                } else {
                    toastr.error(r.message || 'Delete failed.');
                }
            } catch(e) { toastr.error('Unexpected response.'); }
        },
        error: function() { toastr.error('Network error. Please try again.'); }
    });
}
 
/* ── File selection (multiple) ────────────────────────────────*/
function du_onFilesSelected(input) {
    if (!input.files || input.files.length === 0) return;
    var allowed = ['pdf','psd','jpeg','jpg','png'];
    for (var i = 0; i < input.files.length; i++) {
        var file = input.files[i];
        var ext  = file.name.split('.').pop().toLowerCase();
        if (!allowed.includes(ext)) {
            toastr.error('"' + file.name + '" — invalid type. Allowed: PDF, PSD, JPEG, JPG, PNG.');
            continue;
        }
        if (file.size > 20 * 1024 * 1024) {
            toastr.error('"' + file.name + '" exceeds 20 MB limit.');
            continue;
        }
        // Avoid duplicates by name
        var already = du_pendingFiles.some(function(f){ return f.name === file.name; });
        if (!already) du_pendingFiles.push(file);
    }
    du_renderPendingChips();
    // Clear the input so same file can be re-added if needed
    input.value = '';
}
 
function du_renderPendingChips() {
    var html = '';
    du_pendingFiles.forEach(function(f, idx) {
        var sizeStr = f.size > 1024*1024
                    ? (f.size/1024/1024).toFixed(1)+' MB'
                    : (f.size/1024).toFixed(0)+' KB';
        html += '<div class="du-file-chip">'
             + du_fileIcon(f.name.split('.').pop().toLowerCase())
             + '<span class="chip-name" title="'+du_esc(f.name)+'">'+du_esc(f.name)+'</span>'
             + '<span style="color:#6c757d;font-size:0.7rem;">('+sizeStr+')</span>'
             + '<button class="chip-remove" onclick="du_removePending('+idx+')" title="Remove">&#x2715;</button>'
             + '</div>';
    });
    $('#du_files_preview_list').html(html);
}
 
function du_removePending(idx) {
    du_pendingFiles.splice(idx, 1);
    du_renderPendingChips();
}
 
/* ── Drag and drop ────────────────────────────────────────────*/
$(document).on('dragover', '#du_drop_zone', function(e) {
    e.preventDefault(); du_setDropZoneStyle(true);
});
$(document).on('dragleave', '#du_drop_zone', function(e) {
    du_setDropZoneStyle(false);
});
$(document).on('drop', '#du_drop_zone', function(e) {
    e.preventDefault(); du_setDropZoneStyle(false);
    var files = e.originalEvent.dataTransfer.files;
    if (!files || !files.length) return;
    var dt = new DataTransfer();
    for (var i = 0; i < files.length; i++) dt.items.add(files[i]);
    var inp = document.getElementById('du_file_input');
    inp.files = dt.files;
    du_onFilesSelected(inp);
});
 
function du_setDropZoneStyle(active) {
    active ? $('#du_drop_zone').addClass('dragover') : $('#du_drop_zone').removeClass('dragover');
}
function du_resetProgressBar() {
    $('#du_progress_bar').css('width','0%').text('0%').removeClass('bg-success').addClass('bg-warning');
    $('#du_progress_label').text('Uploading…');
}
 
/* ── Submit ───────────────────────────────────────────────────*/
function du_submitAndProceed() {
    var jcNo           = $('#du_jc_no').val();
    var customerName   = $('#du_customer_name').val();
    var customerMobile = $('#du_customer_mobile').val();
    var reason         = $('#du_no_file_reason').val().trim();
    var hasFiles       = du_pendingFiles.length > 0;
 
    // Must have either new files OR a reason
    if (!hasFiles && reason === '') {
        toastr.warning('Please add at least one design file, or provide a reason for not uploading.');
        $('#du_no_file_reason').focus();
        return;
    }
 
    var fd = new FormData();
    fd.append('jobcard_no',      jcNo);
    fd.append('customer_name',   customerName);
    fd.append('customer_mobile', customerMobile);
    fd.append('upload_reason',   reason);
    du_pendingFiles.forEach(function(f) {
        fd.append('design_files[]', f);   // note: design_files[] matches PHP $_FILES['design_files']
    });
 
    $('#du_proceed_btn').prop('disabled', true)
        .html('<i class="fas fa-spinner fa-spin me-1"></i> Saving…');
    $('#du_progress_wrap').show();
    du_resetProgressBar();
 
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'api/save_jc_design_upload.php', true);
 
    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            var pct = Math.round((e.loaded / e.total) * 100);
            $('#du_progress_bar').css('width', pct+'%').text(pct+'%');
            $('#du_progress_label').text('Uploading… ' + pct + '%');
        }
    };
 
    xhr.onload = function() {
        $('#du_proceed_btn').prop('disabled', false)
            .html('<i class="fas fa-check me-1"></i> Save &amp; Proceed to Job Card Details');
        try {
            var resp = JSON.parse(xhr.responseText);
            if (resp.status === 'success') {
                $('#du_progress_bar').css('width','100%').text('100%')
                    .removeClass('bg-warning').addClass('bg-success');
                $('#du_progress_label').text('Saved! Opening Job Card Details…');
                toastr.success(resp.message);
                setTimeout(function() {
                    $('#designUploadModal').modal('hide');
                    fnCompletePopUp(parseInt(jcNo));
                }, 600);
            } else {
                $('#du_progress_wrap').hide();
                toastr.error(resp.message || 'Save failed. Please try again.');
            }
        } catch(e) {
            $('#du_progress_wrap').hide();
            toastr.error('Unexpected server response. Please try again.');
        }
    };
 
    xhr.onerror = function() {
        $('#du_proceed_btn').prop('disabled', false)
            .html('<i class="fas fa-check me-1"></i> Save &amp; Proceed to Job Card Details');
        $('#du_progress_wrap').hide();
        toastr.error('Network error during upload. Please try again.');
    };
 
    xhr.send(fd);
}
 
/* ── Utility ──────────────────────────────────────────────────*/
function du_fileIcon(ext) {
    var icons = { pdf:'📄', psd:'🎨', jpg:'🖼️', jpeg:'🖼️', png:'🖼️' };
    return icons[ext] || '📁';
}
function du_esc(str) {
    return String(str||'')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function du_safeId(str) {
    return String(str||'').replace(/[^a-zA-Z0-9]/g,'_');
}


/* ═══════════════════════════════════════════════════════════════════════════
   EXISTING FUNCTIONS (unchanged except fnCompletePopUp — kept as-is)
   ═══════════════════════════════════════════════════════════════════════════ */

function fnSQ_Mini_Edit_Update()
{
	var SQ_Mini_Editquotation_no_txt=$("#SQ_Mini_Editquotation_no_txt").val();
	var SQ_Mini_Editdiscount_txt=$("#SQ_Mini_Editdiscount_txt").val();
	var SQ_Mini_Editbalance_cash_txt=$("#SQ_Mini_Editbalance_cash_txt").val();
	var SQ_Mini_Editbalance_gpay_txt=$("#SQ_Mini_Editbalance_gpay_txt").val();
	
	$.ajax({
		type: "POST",
		url: "api/save_sq_mini_edit_data.php",
		data:{
			"SQ_Mini_Editquotation_no_txt":SQ_Mini_Editquotation_no_txt, 
			"SQ_Mini_Editdiscount_txt":SQ_Mini_Editdiscount_txt,
			"SQ_Mini_Editbalance_cash_txt":SQ_Mini_Editbalance_cash_txt,
			"SQ_Mini_Editbalance_gpay_txt":SQ_Mini_Editbalance_gpay_txt
		},
			async:false,		
			success: function (response) {
				if(response !== "Failed")
				{
					alert("Updated Successfully.  Refresh List (search) to see the updation");
					$('#SQ_Mini_Edit_modal').modal('hide');
				}
				else
				{
					alert("Cannot Do Updation. Try Again." );
				}
			}
	});
	
}
function mini_edit(p_quotation_no_txt)
{
	$.ajax({
			type: "GET",
			url: "api/get_sq_mini_edit_data.php?quotation_no_txt="+p_quotation_no_txt,
			async:false,		
			success: function (response) {
				if(response !== "Failed")
				{
					ans=response.split(",");
					$("#SQ_Mini_Editquotation_no_txt").val(ans[0]);
					$("#SQ_Mini_Editdiscount_txt").val(ans[1]);
					$("#SQ_Mini_Editbalance_cash_txt").val(ans[2]);
					$("#SQ_Mini_Editbalance_gpay_txt").val(ans[3]);
				}

			}
		});	
	$('#SQ_Mini_Edit_modal').modal('show');
}
function close_globalView()
{
	$("#globalView").hide();
}
function select_all_jcs()
{
	$(".select_jcs").prop('checked', true);
}
function unselect_all_jcs()
{
	$(".select_jcs").prop('checked', false);
}
function select_all_sqs()
{
	$(".select_sqs").prop('checked', true);
}
function unselect_all_sqs()
{
	$(".select_sqs").prop('checked', false);
}
function sales_invoice_sqs()
{
	var sq_nos=""; 
	var jc_nos=""; 
	var customer="";
	var err_flag=0;
	$('.select_sqs').each(function() {
		if ($(this).is(':checked')) {
			if(sq_nos!="") sq_nos=sq_nos+",";
			if(jc_nos!="") jc_nos=jc_nos+",";
			sq_nos=sq_nos+$(this).val();
			jc_nos=jc_nos+$(this).attr("jc_nos");
			if(customer=="")
			{
				customer=$(this).attr("customer");
			}
			if(customer!=$(this).attr("customer"))
			{
				err_flag=1;
				return;
			}
		}
	}); 
	if(err_flag==1)
	{
		toastr.error("Can't Combine Diff. Customer(s)");
		return;		
	}
	if(sq_nos.length==0) 
	{
		toastr.error("No Sale Quotation Selected");
		return;
	}
	
	if(confirm("Are you sure you want to convert to Sales Invoice?")){
        $.ajax({
			type: "GET",
			url: "api/save_sale_invoice.php?sq_no="+sq_nos+"&jc_nos="+jc_nos,
			async:false,		
			success: function (response) {
				if(response == "Failed")
				{
					toastr.error("Failed to convert to Invoice!!!");
					return;
				}
				else
				{
					toastr.success("Successfully converted to Invoice. "+response);
					loadSQList();
				}
			}
		});
    }
    else{
        return false;
    }
	 
}
function close_selected_jcs()
{
	job_card_nos="";
	job_card_type="";
	customer_name="";
	invalid_operation=false;
	$('.select_jcs').each(function() {
		if ($(this).is(':checked')) {
			if(job_card_nos!="") job_card_nos=job_card_nos+",";
			job_card_nos=job_card_nos+$(this).val()
			job_completed=$(this).attr("job_completed");
			if(job_completed==0)
			{
				alert("incomplete job cards cannot be closed");
				invalid_operation=true;
				return false;
			}
			if(customer_name=="")
			{
				customer_name=$(this).attr("customer_name");
			}
			else
			{
				nxt_customer_name=$(this).attr("customer_name");
				if(customer_name!=nxt_customer_name)
				{
					alret("Cannot mix job cards");
					invalid_operation=true;
					return false;
				}
			}
			if(job_card_type=="")
			{
				job_card_type=$(this).attr("customer_type");
			}
			else
			{
				nxt_job_card_type=$(this).attr("customer_type");
				if(job_card_type!=nxt_job_card_type){
					alert("Cannot mix job cards");
					invalid_operation=true;
					return false;
				}
				
			}
		}
	});
	if(invalid_operation) return;
	if(job_card_nos.length==0) 
	{
		alert("No Job Card Selected");
		return;
	}
	$("#globalViewContentDiv").load("job_card.php",function(){
	$("#jobcard_no_txt").text(job_card_nos);
	load_job_cards_from_db("JC_2_SQ");
	$("#globalView").show();
	});
}

$(document).ready(function() {
	SetUpBasics();
 	$('.datepicker').removeClass('hasDatepicker').datepicker({
		dateFormat: 'dd-mm-yy'  
	});	 
	
	<?php 
		 if($_SESSION['user_type'] != "SUPERADMIN")
		{
	?>

	loadJobCardList();
	loadSQList();
	loadSIList();
	<?php
		}
		else
		{
	?>

	$("#superadmin_show_btn").on("click",function(){
	
		var from = $('#date1Txt').val();
		var to = $('#date2Txt').val()		
        $.ajax({
			type: "GET",
			url: "api/sa_card1_summary.php?dt1="+from+"&dt2="+to,
			async:false,		
			success: function (response) {
				if(response !== "Failed")
				{
					ans=response.split(",");
					$("#jc_cnt").text(ans[0]);
					$("#sq_cnt").text(ans[1]);
					$("#si_cnt").text(ans[2]);
					$("#balance_txt").text(ans[3]);
				}

			}
		});	
        $.ajax({
			type: "GET",
			url: "api/sa_card2_summary.php?dt1="+from+"&dt2="+to,
			async:false,		
			success: function (response) {
				if(response !== "Failed")
				{
					ans=response.split(",");
					$("#printing_cnt").text(ans[0]);
					$("#services_cnt").text(ans[1]);
					$("#materials_cnt").text(ans[2]);
					$("#id_card_cnt").text(ans[3]);
					$("#products_cnt").text(ans[4]);
				}

			}
		});	
        $.ajax({
			type: "GET",
			url: "api/sa_card3_summary.php?dt1="+from+"&dt2="+to,
			async:false,		
			success: function (response) {
				if(response !== "Failed")
				{
					$("#machine_count_div").html(response);	
				}

			}
		});	
        $.ajax({
			type: "GET",
			url: "api/sa_card4_summary.php?dt1="+from+"&dt2="+to,
			async:false,		
			success: function (response) {
				if(response !== "Failed")
				{
					$("#total_sales_div").html(response);	
				}

			}
		});	
        $.ajax({
			type: "GET",
			url: "api/sa_card5_summary.php?dt1="+from+"&dt2="+to,
			async:false,		
			success: function (response) {
				if(response !== "Failed")
				{
					ans=response.split(",");
					$("#general_cnt").text(ans[0]);
					$("#walkin_cnt").text(ans[1]);
					$("#credit_cnt").text(ans[2]);
				}

			}
		});	
        $.ajax({
			type: "GET",
			url: "api/sa_card6_summary.php?dt1="+from+"&dt2="+to,
			async:false,		
			success: function (response) {
				if(response !== "Failed")
				{
					$("#active_users_div").html(response);	
				}

			}
		});	
	});
	<?php
		}
	?>	
});
function loadJobCardList()
{
	var dt="";
	if($("#jc_date_txt").length)
		dt = $('#jc_date_txt').val(); 
	else
		dt = $('#jc_from_dt_txt').val()+ "~"+$('#jc_to_dt_txt').val(); 
	var customer_code_txt = $('#customer_code_txt').val();
	var user_name_txt = $('#user_name_txt').val();
	var pay_mode_txt = $('#pay_mode_txt').val();
	status_txt="0";
	if($("#status_txt").length)
	{
		status_txt=$("#status_txt").val();
	}

	if ($.fn.DataTable.isDataTable('#jobcardTable')) {
		$('#jobcardTable').DataTable().destroy();
	}
	$('#jobcardDiv').html('');
	$.ajax({
		type: "POST",
		url: "api/get_jobcard_list.php",
		data:{
			"dt":dt,
			"customer_code_txt":customer_code_txt,
			"user_name_txt":user_name_txt,
			"pay_mode_txt":pay_mode_txt,
			"status_txt":status_txt
		},
		success: function (response) {
			if(response != "")
			{
				$("#jobcardDiv").html(response);
				var table = $('#jobcardTable').DataTable({
				"lengthMenu": [[-1, 10, 25, 50], ["All", 10, 25, 50]],
				autoWidth: false,
				language: {
						emptyTable: 'No data available in table'
					}
				});

				var jcCount = table.rows().count();
				$('#jc-tab').text((jcCount > 0 ? jcCount + ' ' : '') + 'Job Cards');

				 $('#jobcardTable thead tr:eq(1) th').each(function(i) {
					$('input', this).on('keyup change', function() {
					  if (table.column(i).search() !== this.value) {
						table.column(i).search(this.value).draw();
					  }
					});
				  });
			}
			else
			{
				toastr.error("Failed to get details!!!");
			}
		},error: function(xhr, status, error){
		console.error(error);
	}});
 
	
}
function loadSQList()
{
	var dt="";
	if($("#sq_date_txt").length)
		dt = $('#sq_date_txt').val(); 
	else
		dt = $('#sq_from_dt_txt').val()+ "~"+$('#sq_to_dt_txt').val();
	var customer_code_txt = $('#sq_customer_code_txt').val();
	var user_name_txt = $('#sq_user_name_txt').val();
	var pay_mode_txt = $('#sq_pay_mode_txt').val();
	status_txt="0";
	if($("#sq_status_txt").length)
	{
		status_txt=$("#sq_status_txt").val();
	}

	if ($.fn.DataTable.isDataTable('#sqTable')) {
		$('#sqTable').DataTable().destroy();
	}
	$('#sqDiv').html('');
	$.ajax({url: "api/get_sq_list.php",
	method: 'POST',
	data:{
			"dt":dt,
			"customer_code_txt":customer_code_txt,
			"user_name_txt":user_name_txt,
			"pay_mode_txt":pay_mode_txt,
			"status_txt":status_txt
		},
	success: function(result){
		$("#sqDiv").html(result);
		var table = $('#sqTable').DataTable({
		"lengthMenu": [[-1, 10, 25, 50], ["All", 10, 25, 50]],
		autoWidth: false,
		language: {
				emptyTable: 'No data available in table'
			}
		});
		 $('#sqTable thead tr:eq(1) th').each(function(i) {
			$('input', this).on('keyup change', function() {
			  if (table.column(i).search() !== this.value) {
				table.column(i).search(this.value).draw();
			  }
			});
		  });
	},error: function(xhr, status, error){
		console.error(error);
	}});

}
function loadSIList()
{ 
	var dt="";
	if($("#si_date_txt").length)
		dt = $('#si_date_txt').val(); 
	else
		dt = $('#si_from_dt_txt').val()+ "~"+$('#si_to_dt_txt').val();	
	var customer_code_txt = $('#si_customer_code_txt').val();
	var user_name_txt = $('#si_user_name_txt').val();
	var pay_mode_txt = $('#si_pay_mode_txt').val();

	if ($.fn.DataTable.isDataTable('#siTable')) {
		$('#siTable').DataTable().destroy();
	}
	$('#siDiv').html('');
	$.ajax({url: "api/get_si_list.php",
	method: 'POST',
	data:{
			"dt":dt,
			"customer_code_txt":customer_code_txt,
			"user_name_txt":user_name_txt,
			"pay_mode_txt":pay_mode_txt
		},
	success: function(result){
		$("#siDiv").html(result);
		var table = $('#siTable').DataTable({
		"lengthMenu": [[-1, 10, 25, 50], ["All", 10, 25, 50]],
		autoWidth: false,
		language: {
				emptyTable: 'No data available in table'
			}
		});
		 $('#siTable thead tr:eq(1) th').each(function(i) {
			$('input', this).on('keyup change', function() {
			  if (table.column(i).search() !== this.value) {
				table.column(i).search(this.value).draw();
			  }
			});
		  });
	},error: function(xhr, status, error){ 
		console.error(error);
	}});

}
function fnMarkVoid(pJobCardNo)
{
	if (confirm("Sure of Marking this JC as VOID!") == true) 
	{
	$.ajax({
		type: "POST",
		url: "api/mark_job_card_void.php",
		data:{'job_card_no':pJobCardNo},
		async:false,		
	success: function (data) {
		loadJobCardList();
	}
	});		
		
	}
} 

/* ─────────────────────────────────────────────────────────────────────────────
   fnCompletePopUp — UNCHANGED. Called automatically after design upload succeeds.
   ───────────────────────────────────────────────────────────────────────────── */
function fnCompletePopUp(pJobCardNo)
{  
	$("#jc_id").val(pJobCardNo);
	$.ajax({
		type: "POST",
		url: "api/get_jobcard_details.php",
		data:{"job_card_no":pJobCardNo,"type":"mat"},
		async:false,		
		success: function (data) {
			if(data != "")
			{
				$('#materialUsageSpilageDetailDiv').html('');
				rows = '<center><h6 class="bg-warning p-1">Material Details</h6></center><table class="table table-bordered table-hover" id="materialUsageSpilageTable">';
				rows += '  <thead class="table-dark">';
				rows += ' <tr><th>SNo</th><th>Material Name</th><th>Machine Name</th><th>Usage</th><th>F & B </th><th>Spilage</th></tr>';  
				rows += '</thead><tbody>'; 
				for(var i=0;i<data.length;i++)
				{ 
					if(data[i].material_code.length>0)
					{
					rows += ' <tr>';  
					rows += '     <td>'+ (i+1) +'</td>'; 
					rows += '     <td>'+data[i].material_code +'</td>'; 
					rows += '     <td>'+data[i].machine_code +'</td>'; 
					rows += '     <td>'+data[i].usage_count+'</td>'; 
					rows += '     <td><input type="checkbox" name="front_and_back_'+i+'" id="front_and_back_'+i+'" class="form-check-input"> </td>'; 
					 rows += '     <td>'; 
					 rows += '       <input type="text" name="material_spilage_'+i+'" id="material_spilage_'+i+'" class="form-control" />';  
					 rows += '     </td>'; 
					rows += '   </tr>';  
					}
				  }
				  rows += ' </tbody></table>';
				$('#materialUsageSpilageDetailDiv').html(rows); 
				$('#materialUsageSpilageTable').DataTable({  
				"searching":false,
				"ordering":false,
				"paging":false 
				});
			}
		}
	});
	$('div#materialUsageSpilageTable_info.dt-info').hide();
	$('div#machineUsageSpilageTable_info.dt-info').hide();
	$('#jobcardCompletedModal').modal('show');
}

function fnMarkCompleteJobCard()
{
	var jobcard_no=$('#jc_id').val();
	var jobcard_date=$('#jc_dt').val();
	var materialUsageSpilageArr =[];
	$('#completedJCBtn').prop('disabled',true);
	$('#materialUsageSpilageTable tbody tr').each(function() {
		var temp={};
		temp['jobcard_no']=jobcard_no;
		temp['jobcard_date']=jobcard_date;
		temp['material_code']=$(this).find('td:eq(1)').text();
		temp['machine_code']=$(this).find('td:eq(2)').text();
		temp['front_back']=$(this).find('td:eq(4) input').is(':checked')?1:0;
		temp['spillage_count']=$(this).find('td:eq(5) input').val(); ;
		materialUsageSpilageArr.push(temp); 
	});
	 
	$.ajax({
		type: "POST",
		url: "api/save_job_card_complete.php", 
		data:{"jobcard_no":jobcard_no,  "material_spillage_details": JSON.stringify(materialUsageSpilageArr)
		},
		success: function (response) {
			if(response == "Success")
			{ 
				toastr.success('Successfully Completed Job Card' );
				$('#jobcardCompletedModal').modal('hide');
				loadJobCardList();
			}
			else
			{
				toastr.error("Failed to Complete job card!!!");
			}
			$('#completedJCBtn').prop('disabled',false);
		} 
	});	
}
</script>
