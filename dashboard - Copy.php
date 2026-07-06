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
.accordion-button::after {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-plus' viewBox='0 0 16 16'%3E%3Cpath d='M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z'/%3E%3C/svg%3E");
  transition: all 0.5s;
}
.accordion-button:not(.collapsed)::after {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-dash' viewBox='0 0 16 16'%3E%3Cpath d='M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z'/%3E%3C/svg%3E");
}
.accordion-button:not(.collapsed) {
    color: var(--bs-accordion-active-color);
    background-color: var(--bs-accordion-active-bg);
    box-shadow: inset 0 calc(-1 * var(--bs-accordion-border-width)) 0 var(--bs-accordion-border-color);
}
.accordion,.accordion
Specificity: (0,2,0)
 {
    --bs-accordion-btn-bg: #ffc0d3;
    --bs-accordion-active-bg: #ff5c8d;
    --bs-accordion-active-color: #fff;
    --bs-accordion-btn-focus-box-shadow: none;
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
		<div class="col-md-1 iconDivCls" onclick="fnModule(5)">
			<p>RECEIPT <br>VOUCHER</p>
			<img loading="lazy" class="iconImgCls" src="img/receipt_voucher.png" alt="RECEIPT VOUCHER" />
		</div>
		<div class="col-md-1 iconDivCls" onclick="fnModule(6)">
			<p>PAYMENT <br>VOUCHER</p>
			<img loading="lazy" class="iconImgCls" src="img/payment_voucher.png" alt="PAYMENT VOUCHER" />
		</div>
<!--		<div class="col-md-1 iconDivCls" onclick="fnModule(7)">
			<p>BILL <br>RECEIPT</p>
			<img loading="lazy" class="iconImgCls" src="img/bill_receipt.png" alt="BILL RECEIPT" />
		</div>
		<div class="col-md-1 iconDivCls" onclick="fnModule(8)">
			<p>BILL <br>PAYABLE</p>
			<img loading="lazy" class="iconImgCls" src="img/bill_payable.png" alt="BILL PAYABLE" />
		</div>-->
		<div class="col-md-1 iconDivCls" onclick="fnModule(9)">
			<p>USER<br>MASTER</p>
			<img loading="lazy" class="iconImgCls" src="img/user_master.png" alt="ADD NEW CUSTOMER" />
		</div>
		<?php 
		}
		?>
		<div class="col-md-1 iconDivCls" onclick="fnModule(10)">
			<p>EOD<br>PROCESS</p>
			<img loading="lazy" class="iconImgCls" src="img/eod_process.png" alt="ADD NEW CUSTOMER" />
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
<!--		<div class="col-md-3">
			<div class="card text-bg-info mb-3" >
			  <div class="card-header"><h6 class="card-title">MACHINE READING</h6></div>
			  <div class="card-body">
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
		</div>-->
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
	
<div class="accordion" id="accordionPanelsStayOpenExample">
  <div class="accordion-item">
    <h2 class="accordion-header" id="panelsStayOpen-headingOne">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
        Job Cards
      </button>
    </h2>
    <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
      <div class="accordion-body">
       <div id="jc_div">
			<div style="position:relative;">
			<h5 class="bg_blue p-1" style="text-align:center;">OPEN JOB CARDS</h5>
				
			<?php
			$today = date('Y-m-d'); // Current date, e.g., '2025-09-05'
			$yesterday = date('Y-m-d', strtotime('-1 day', strtotime($today)));
			$before_yesterday = date('Y-m-d', strtotime('-2 day', strtotime($today)));
			$week = date('Y-m-d', strtotime('-7 day', strtotime($today)));
			?>
			
				<table width="100%" style="background-color:teal;color:black;font-weight:bold;">
				<tr>
				<?php
				if($_SESSION['user_type']!="OPERATOR")
				{
				?>
				<td> From</td><td> <input type="text" class="datepicker" id="jc_from_dt_txt" value="<?php echo date("d-m-Y");?>"/></td>
				<td> To</td><td> <input type="text" class="datepicker" id="jc_to_dt_txt" value="<?php echo date("d-m-Y");?>"/></td>
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
					</select>
				</td>
				<?php
				}
				?>
				<td>Customer</td>
				<td>
					<select id="customer_code_txt" name="customer_code_txt">
						<option value="all">All</option>
						<option value="walkin">WalkIn</option>
						<option value="general">General</option>
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
					$hi=1;//placeholder
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
						
				
				<div style="position:absolute;left:250px; top:82px;width:65%;z-index:100;">
					<button class="btn btn-sm btn-success" onclick="select_all_jcs()">Select All JC</button>
					<button class="btn btn-sm btn-danger" onclick="unselect_all_jcs()">Unselect All</button>
					<button class="btn btn-sm btn-info" onclick="close_selected_jcs()">Close Selected</button>
					<input type="button" class="btn btn-sm btn-warning" name="searchBtn" id="searchBtn" value="Refresh" onclick="loadJobCardList()" style=" "/>
				</div>	
				<div id="jobcardDiv" style="position:relative;margin-top:5px;">
					
				</div>
			</div>
		</div>
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
        Sales Quotes
      </button>
    </h2>
    <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
      <div class="accordion-body">
       
		<div id="sq_div">
			<div style="position:relative;">
			<h5 class="bg_magenta p-1" style="text-align:center;">OPEN SALES QUOTES</h5>
			<?php
			$today = date('Y-m-d'); // Current date, e.g., '2025-09-05'
			$yesterday = date('Y-m-d', strtotime('-1 day', strtotime($today)));
			$before_yesterday = date('Y-m-d', strtotime('-2 day', strtotime($today)));
			$week = date('Y-m-d', strtotime('-7 day', strtotime($today)));
			?>
			
				<table width="100%" style="background-color:magenta;color:black;font-weight:bold;">
				<tr>
				<?php
				if($_SESSION['user_type']!="OPERATOR")
				{
				?>
				<td> From</td><td> <input type="text" class="datepicker" id="sq_from_dt_txt" value="<?php echo date("d-m-Y");?>"/></td>
				<td> To</td><td> <input type="text" class="datepicker" id="sq_to_dt_txt" value="<?php echo date("d-m-Y");?>"/></td>
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
					<select id="sq_customer_code_txt" name="sq_customer_code_txt">
						<option value="all">All</option>
						<option value="walkin">WalkIn</option>
						<option value="general">General</option>
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
					$hi=1;//placeholder
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
						
				

			
			<div style="position:absolute;left:200px; top:77px;width:65%;z-index:100;">
				<button class="btn btn-sm btn-success" onclick="select_all_sqs()">Select All SQ</button>
				<button class="btn btn-sm btn-danger" onclick="unselect_all_sqs()">Unselect All</button>
				<button class="btn btn-sm btn-info" onclick="sales_invoice_sqs()">Sales Invoice</button>
				<input type="button" class="btn btn-sm btn-warning" name="searchSQBtn" id="searchSQBtn" value="Refresh" onclick="loadSQList()" style=""/>
			</div>		
			
				<div id="sqDiv" style="position:relative;">
					
				</div>
			</div>		
		</div>
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header" id="panelsStayOpen-headingThree">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
       Sales Invoices
      </button>
    </h2>
    <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
      <div class="accordion-body">
        
		<div id="si_div" >
			<div style="position:relative;">
			<h5 class="bg_cyan p-1" style="text-align:center;">OPEN SALES INVOICES</h5>
<?php
			$today = date('Y-m-d'); // Current date, e.g., '2025-09-05'
			$yesterday = date('Y-m-d', strtotime('-1 day', strtotime($today)));
			$before_yesterday = date('Y-m-d', strtotime('-2 day', strtotime($today)));
			$week = date('Y-m-d', strtotime('-7 day', strtotime($today)));
			?>
			
				<table width="100%" style="background-color:cyan;color:black;font-weight:bold;">
				<tr>
<?php
				if($_SESSION['user_type']!="OPERATOR")
				{
				?>
				<td> From</td><td> <input type="text" class="datepicker" id="si_from_dt_txt" value="<?php echo date("d-m-Y");?>"/></td>
				<td> To</td><td> <input type="text" class="datepicker" id="si_to_dt_txt" value="<?php echo date("d-m-Y");?>"/></td>
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
					<select id="si_customer_code_txt" name="si_customer_code_txt">
						<option value="all">All</option>
						<option value="walkin">WalkIn</option>
						<option value="general">General</option>
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
						
			<div style="position:absolute;left:200px; top:76px;width:65%;z-index:100;">

				<input type="button" class="btn btn-sm btn-warning" name="searchSIBtn" id="searchSIBtn" value="Refresh" onclick="loadSIList()" style=""/>
			</div>		
			
				<div id="siDiv" style="position:relative;">
					
				</div>
			</div>		
		</div>
      </div>
    </div>
  </div>
</div>
	<!--<ul class="nav nav-tabs" role="tablist">
		<li  class="nav-item" role="presentation">
			<button class="nav-link active" id="jc-tab" data-bs-toggle="tab" data-bs-target="#jc_div" type="button" role="tab" aria-controls="jc_div" aria-selected="true">Job Cards</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link  " id="sq-tab" data-bs-toggle="tab" data-bs-target="#sq_div" type="button" role="tab" aria-controls="sq_div" aria-selected="false">Sales Quotes</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link  " id="si-tab" data-bs-toggle="tab" data-bs-target="#si_div" type="button" role="tab" aria-controls="si_div" aria-selected="false">Sales Invoices</button>
		</li>
    </ul>
	<div class="tab-content" style="width:100%;height:70%;">
		
	</div>	-->

	<?php
		}
	?>

</div>

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
					<div id="materialUsageSpilageDetailDiv" class="col-6 p-1"  >				 
					</div>	
					<div id="machineUsageSpilageDetailDiv" class="col-6 p-1"  >				 
					</div>	
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" onclick="fnMarkCompleteJobCard()">Completed</button>
			</div>
		</div>
	</div>
</div>
	  
	  
<script>
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
	$('.select_sqs').each(function() {
		if ($(this).is(':checked')) {
			if(sq_nos!="") sq_nos=sq_nos+",";
			if(jc_nos!="") jc_nos=jc_nos+",";
			sq_nos=sq_nos+$(this).val();
			jc_nos=jc_nos+$(this).attr("jc_nos");
		}
	}); 
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
	//alert(job_card_nos);
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
	//alert("about to"); 
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
	//var status = $('#jc_status').val();

	
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
				language: {
						emptyTable: 'No data available in table'
					}
				});
				
				
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
		//alert("Got Error");
		//alert(error);
		console.error(error);

		// You can also inspect the xhr object for more information

	}});
 
	
}
function loadSQList()
{
	//alert("about to");
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

	var urlText="api/get_sq_list.php";
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
		//alert("Got Data");
		$("#sqDiv").html(result);
		var table = $('#sqTable').DataTable({ 
		"lengthMenu": [[-1, 10, 25, 50], ["All", 10, 25, 50]],
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
		//alert("Got Error");
		//alert(error);
		console.error(error);

		// You can also inspect the xhr object for more information

		console.log('Status:', status);

		console.log('Response:', xhr.responseText);		
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

	var urlText="api/get_si_list.php";
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
				rows += ' <tr><th>SNo</th><th>Material Name</th><th>Usage</th></tr>'; // <th>Spilage</th>
				rows += '</thead><tbody>'; 
				for(var i=0;i<data.length;i++)
				{ 
					if(data[i].material_code.length>0)
					{
					rows += ' <tr>';  
					rows += '     <td>'+ (i+1) +'</td>'; 
					rows += '     <td>'+data[i].material_code +'</td>'; 
					rows += '     <td>'+data[i].usage_count+'</td>'; 
					//rows += '     <td>'; 
					//rows += '       <input type="text" name="material_spilage_'+i+'" id="material_spilage_'+i+'" class="form-control" />';  
					//rows += '     </td>'; 
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
	$.ajax({
		type: "POST",
		url: "api/get_jobcard_details.php?type=mac&job_card_no="+pJobCardNo, 
		async:false,
		success: function (data) {
			if(data != "")
			{
				$('#machineUsageSpilageDetailDiv').html('');
				rows = '<center><h6 class="bg-warning p-1">Machine Details</h6></center><table class="table table-bordered table-hover" id="machineUsageSpilageTable">';
				rows += '  <thead class="table-dark">';
				rows += ' <tr><th>SNo</th><th>Machine Name</th><th>Usage</th></tr>'; // <th>Spilage</th>
				rows += '</thead><tbody>'; 
				for(var i=0;i<data.length;i++)
				{ 
					if(data[i].machine_code.length>0)
					{
					rows += ' <tr>';  
					rows += '     <td>'+ (i+1) +'</td>'; 
					rows += '     <td>'+data[i].machine_code +'</td>'; 
					rows += '     <td>'+data[i].usage_count+'</td>'; 
					//rows += '     <td>'; 
					//rows += '       <input type="text" name="machine_spilage_'+i+'" id="material_spilage_'+i+'" class="form-control" />';  
					//rows += '     </td>'; 
					rows += '   </tr>';  
					}
				  }
				  rows += ' </tbody></table>';
				$('#machineUsageSpilageDetailDiv').html(rows); 
				$('#machineUsageSpilageTable').DataTable({  "searching":false,
				"ordering":false,
				"paging":false });
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
	/*var materialUsageSpilageArr =[];
	$('#materialUsageSpilageTable tbody tr').each(function() {
		var temp={};
		temp['jobcard_no']=jobcard_no;
		temp['jobcard_date']=jobcard_date;
		temp['material_code']=$(this).find('td:eq(1)').text();
		temp['spillage_count']=$(this).find('td:eq(3) input').val(); ;
		materialUsageSpilageArr.push(temp); 
	});
	var machineUsageSpilageArr =[];
	$('#machineUsageSpilageTable tbody tr').each(function() {
		var temp={};
		temp['jobcard_no']=jobcard_no;
		temp['jobcard_date']=jobcard_date;
		temp['machine_code']=$(this).find('td:eq(1)').text();
		temp['spillage_count']=$(this).find('td:eq(3) input').val(); ;
		machineUsageSpilageArr.push(temp); 
	});*/
	$.ajax({
		type: "POST",
		url: "api/save_job_card_complete.php", 
		data:{"jobcard_no":jobcard_no
		},
		//,  "material_spillage_details": JSON.stringify(materialUsageSpilageArr),
		//"machine_spillage_details": JSON.stringify(machineUsageSpilageArr)
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
			
		} 
	});	
}
</script>