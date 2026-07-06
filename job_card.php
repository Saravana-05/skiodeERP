<?php
session_start();
include("connect_db.php");
include_once "page_guard.php";
?>
<script>
var SESSION_USER_TYPE = "<?php echo $_SESSION['user_type']; ?>";
</script>
<link rel="stylesheet" media="all" type="text/css" href="css/jquery-ui-timepicker-addon.css">
<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>

<style>
#add_new_plus_btn:hover{
	font-weight:bolder;
	border:1px dotted grey;
}

.cp_textbox
{
	//width:100px;
	border:1px solid black;
	font-weight:bolder;
}
.autocomplete-items { 
  /*position the autocomplete items to be the same width as the container:*/
  height:200px;
  overflow-y:scroll;
  background-color:white;
}
.autocomplete-items div {
  padding: 1px;
  cursor: pointer;
  background-color: #fff; 
  border-bottom: 1px solid #d4d4d4; 
}

/*when hovering an item:*/
.autocomplete-items div:hover {
  background-color: #e9e9e9; 
}
/*when navigating through the items using the arrow keys:*/
.autocomplete-active {
  background-color: DodgerBlue !important; 
  color: #ffffff; 
}
@keyframes mymove {
  50% {-webkit-box-shadow: 0px 20px 0px -10px #FFFFFF, 0px -20px 0px -10px #FFFFFF, 20px 0px 0px -10px #FFFFFF, -20px 0px 0px -10px #FFFFFF, 0px 0px 0px 10px #FF0000, 3px 1px 22px 18px rgba(0,0,0,0); 
box-shadow: 0px 20px 0px -10px #FFFFFF, 0px -20px 0px -10px #FFFFFF, 20px 0px 0px -10px #FFFFFF, -20px 0px 0px -10px #FFFFFF, 0px 0px 0px 10px #FF0000, 3px 1px 22px 18px rgba(0,0,0,0);}
}
.highlighted-focus {
        //border: 2px solid yellow;
        box-shadow: 2px 2px 2px 2px rgba(255, 255, 255, 1);
        //background-color: yellow !important; /* Example highlighting */
		color:red !important;
        font-weight: bold;
	    animation: mymove 5s infinite;
		
    }
#jobdetailsTable td
{
	padding:0;
}
.jobCardDiv
	{
		margin-top: 5px !important;
		border: 0px solid #ffd505;
		border-radius: 4px;
	}
	
	.jobCardDiv h4 {
		margin-top: -23px;
		color: #ffd505;
		background-color: #004b77;
		width: max-content;
		font-size: 1rem;
	}
	.jobCardIconDivCls .iconDivCls
	{
		cursor: pointer;
		border-color: #fff;
		background-color: transparent;
		border: 0px;
		margin-bottom: 0px;
	}
	.jobCardIconDivCls .iconDivCls p {
		height: 30px;
		margin-bottom: 0px;
		font-size: 0.7rem;
	}
	.jobCardDiv .main_tab_btns
	{
		border-radius: 5px !IMPORTANT;
		/*margin-left: 0.5rem;*/
		margin-right: 0.5rem;
		text-transform: uppercase;
		color:#fff;
		/*font-weight:bold;*/
		padding-left:0.3rem;
		padding-right:0.3rem;
	}
	.jobCardDiv .main_tab_btns_current
	{
		text-decoration: underline;
		font-weight: bold;
	}
	#category1_option_div
	{
		background-color: rgb(240, 248, 255);
		border: 1px solid #9d9191;
		border-radius:5px;
		
	}
	#jobCardItemsDiv
	{
		height:50%;
		//font-size:12px;
		overflow-y:auto;
	}
	#footerDiv
	{
		position: absolute;
		bottom: 0;
		left: 0;
		background-color:#f8d7da;
		border: 1px solid #9d9191;
		border-radius:5px;
		padding: 0.5rem !important;
		margin:0px;
	}
#paymentDetailsDiv
{
	background-color:#fdff9f;
	border: 1px solid #9d9191;
	border-radius:5px;
	margin: 0rem !important;
	padding: 0px !important;
}

.container {
    background: #fff;
    display: inline-block;
    padding: 2px 4px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    border-radius: 10px;
}

#qrcode{ 
margin:auto;
}

.name-slab {
  
    
}
 #autocomplete-list {
            border: 1px solid #ccc;
            border-top: none;
            position: absolute;
            z-index: 1000;
           width: 30%;
            background: white;
			color:#000000;
        }

        .autocomplete-item {
            padding: 8px;
            cursor: pointer;
        }

        .autocomplete-item:hover {
            background-color: #000000;
			color: white;
        }
        #search-box {
            width: inherit; 
        }
.modal {
	z-index:160 !important;
}
.modal-backdrop{
	z-index:140 !important;
}		
</style>
<script>
	var sNo=1;
	var allTableData = new Array();
	var totalValue=0;
	var jc_keypress_flag=0;
	var block_editing=0;
</script>

<div class=" p-3 m-1 pt-0 jobCardDiv" style="height:99%;background-color:aliceblue;position:relative;">
	
	<div class="row p-2 pt-3 bg_blue" style="border-radius:3px;" id ="jobCardCustomerDiv"> 
		<div class="col-md-12 border-yellow" style="border-right-width: 1px;">
			<div class="row p-1">
				<div class="col-md-3 p-2">
					<h4 class="mb-0" id="JC_SQ_Title">Job Card</h4> 	
					<div class=" row mt-1">
						 <div class="col-md-12">
							<div class="form-check">
							  <input class="form-check-input" type="radio" name="customer_type_txt" id="walkCustomerOpt" checked value="WalkIn">
							  <label class="form-check-label" for="walkCustomerOpt">
								Walk In Customer
							  </label>
							</div>
						 </div> 
					</div>					
					<div class=" row mt-1">
						 <div class="col-md-12">
							<div class="form-check">
							  <input class="form-check-input" type="radio" name="customer_type_txt" id="generalCustomerOpt" value="General">
							  <label class="form-check-label" for="generalCustomerOpt">
								General Customer
							  </label>
							</div>
						 </div> 
					</div>

					<div class=" row mt-1">
						 <div class="col-md-12">
							<div class="form-check">
							  <input class="form-check-input" type="radio" name="customer_type_txt" id="creditCustomerOpt" value="Credit" autocomplete="on">
							  <label class="form-check-label" for="creditCustomerOpt">
								Credit Customer
							  </label>
							</div>
						 </div> 
					</div>
					<div class=" row mt-1">
						 <div class="col-md-12">
						 <span id="theOperation" style="display:none;"></span>
						 </div> 
					</div>
				</div>
				<div class="col-md-7 border-yellow p-1" >
					<div style="display:none;" id="credit_customer_div">
					<div class=" row mt-1">
						 <label for="search-box" class="col-md-2 col-form-label">Select</label>
						 <div class="col-md-9"> 
							<input type="text" autocomplete="off" name="credit_customer_name_txt" class="form-control" id="search-box" placeholder="Start typing..." />
							<input type="hidden" id="search_cust_id" value="" />
							<div id="autocomplete-list"></div>
						 </div> 
						 <div class="col-md-1"><button class="btn btn-sm btn-success" type="button" onclick="fn_new_credit_customer()">New </button> </div>
					</div> 					
					</div>
					<div  style="display:none;" id="customer_div" >
					<div class=" row mt-1">
					<input type="hidden" id="credit_customer_code_txt" name="credit_customer_code_txt"/>
						 <label for="customer_name_txt" class="col-md-2 col-form-label">Name</label>
						 <div class="col-md-10">
							<input type="text" autocomplete="off"   class="form-control" placeholder="Name"
							   id="customer_name_txt" name="customer_name_txt" maxlength="75"/>
						 </div> 
					</div>
					<div class="row mt-1">
						 <label for="customer_addr1_txt" class="col-md-2 col-form-label">Address</label>
						 <div class="col-md-3">
							<input type="text" autocomplete="off"  class="form-control"  placeholder="Addr Line 1" 
							   id="customer_addr1_txt" name="customer_addr1_txt"  maxlength="75"  />
						 </div>
						 <div class="col-md-2">	   
							<input type="text" autocomplete="off" class="form-control"  placeholder="Addr Line 2" 
							   id="customer_addr2_txt" name="customer_addr2_txt"  maxlength="75"  />
						 </div>
						 <div class="col-md-2">	   
							<input type="text" autocomplete="off"   class="form-control"   placeholder="City" 
							   id="customer_city_txt" name="customer_city_txt" maxlength="75"   />
						 </div> 
						  <div class="col-md-3">	   
							<select class="form-select" id="customer_state_txt">
							  <?php
								$state_sql="select * from state_master order by state_name;";
								if($state_qry=mysqli_query($connection,$state_sql))
								{
									while($state_row=mysqli_fetch_array($state_qry))
									{
										echo '<option value="'.$state_row["state_code"].'" '.($state_row["state_code"]==33?"selected":"").'>'.$state_row["state_name"].'</option>';
									}
								}
							  ?>
							  </select>
						 </div> 
					</div>
					
					<div class="row mt-1">
						 <label for="customer_name_txt" class="col-md-2 col-form-label">GST </label>
						 <div class="col-md-4">
							<input type="text" autocomplete="off"  class="form-control"  placeholder="GST No"  
							   id="customer_gst_no_txt" name="customer_gst_no_txt"  maxlength="15"  />
						 </div>	
						 <label for="customer_mobile_no_txt" class="col-md-2 col-form-label">Phone</label>
						 <div class="col-md-4">
							<div class="input-group mb-3">
								<input type="text" autocomplete="off"  class="form-control"  placeholder="Mobile No" id="customer_mobile_no_txt" name="customer_mobile_no_txt"  maxlength="15"  />
								<div class="input-group-append">
									<button class="btn btn-success" type="button" id="button-addon2" onclick="showHistory()">History</button>
								</div>
							</div>   
						 </div> 
					</div>
				</div>
				</div>
				<div class="col-md-2 pull-right" style="position: relative;">
						Date : <span id="jobcard_date_txt" style="font-size:0.75vw;font-weight:bold;"><?php echo date("d-m-Y"); ?></span><br>
						Job Card No : <span id="jobcard_no_txt" style="font-weight:bold;"></span>
						<input type="hidden" value="<?php echo date("Y-m-d");?>" id="jobcard_date_txt" />
						<div id="sq_no_container" style="display:none;"> SQ No :<span id="sq_no_txt" style="font-weight:bold;"></span>&nbsp;<span id="sq_dt_txt"></span><br><span style="display:none;" id="si_no_containter">SI No :<span id="si_no_txt" style="font-weight:bold;"></span></span></div>
						<button type="button" class="btn btn-warning btn-sm" id="nextBtn" onclick="fnNext()" style="position:absolute;bottom:0;right:0;">Next >></button>
				</div>
			</div>
		</div>
	</div>
	<div class="row p-2" id="jobCardItemsDiv" style="position:relative;">
		<div id="jobCardItems_table_div">
			<a href="javascript:void(0)" style="position:absolute;right:0px;top:1px;" id="add_new_plus_btn" onclick="fnShowAddPopup()"><img src="img/add_new_plus.png" alt="add"  /><br>[Ins]</a>
		<table class="table table-bordered table-hover table-striped" id="jobdetailsTable">
			<thead >
				<th scope="col">#</th>
				<th scope="col">Machine</th>
				<th scope="col" width="40%">Item Description</th>
				<th scope="col">Tax</th>
				<th scope="col">1st Copy Nos</th>
				<th scope="col">Addl Copy Nos</th>
				<th scope="col">FB</th>
				<th scope="col">Qty</th>
				<th scope="col">Value</th>
				<th scope="col">Opr</th>
			</thead>
			<tbody>
			<?php
				for($i=0;$i<9;$i++)
				{
					?>
				<tr>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php
				}
				?>
			</tbody>
		</table>
		<!-- saravanan changes for discount start -->
		<!-- Item-wise Discount Modal -->
<div id="itemDiscountModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-dialog-centered" style="max-width:70%;width:60%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Item-wise Discounts</h5>
        <span style="font-size:0.85rem;color:#888;margin-left:10px;">
          JC No: <strong id="idm_jc_no"></strong>
        </span>
      </div>
      <div class="modal-body" style="padding:10px;">
        <table class="table table-bordered table-sm" id="itemDiscountTable" style="font-size:0.85rem;">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Item</th>
              <th style="text-align:right;">Qty</th>
              <th style="text-align:right;">Value (₹)</th>
              <th style="text-align:right;width:130px;">Discount (₹)</th>
              <th style="text-align:right;">Net Value (₹)</th>
            </tr>
          </thead>
          <tbody id="itemDiscountTbody">
          </tbody>
          <tfoot>
            <tr style="font-weight:bold;background:#fdff9f;">
              <td colspan="3" style="text-align:right;">Total</td>
              <td style="text-align:right;" id="idm_total_value">0.00</td>
              <td style="text-align:right;" id="idm_total_discount">0.00</td>
              <td style="text-align:right;" id="idm_total_net">0.00</td>
            </tr>
          </tfoot>
        </table>
        <div id="idm_error_msg" style="color:red;font-size:0.85rem;display:none;"></div>
      </div>
      <div class="modal-footer" style="background:#e7e7e7;">
        <button class="btn btn-success" onclick="fnSaveItemDiscounts()">
          Save Discounts
        </button>
        <button class="btn btn-light" onclick="$('#itemDiscountModal').modal('hide')">
          Close
        </button>
      </div>
    </div>
  </div>
</div>
<!-- saravanan changes for discount end -->
		</div>
		<div id="jobCardItems_total_div" style="position:absolute;bottom:0;width:100%; text-align:right;padding-right:25px;display:none;">
			Total : <input type="text" autocomplete="off"   id="jobCardItemTotal" disabled style="border-radius:3px;border:1px solid grey; background-color:white;text-align:right;font-weight:bold;"  size="8"/>&nbsp;
		</div>
	</div>
	<div class="row p-2" id="jobCardSummary">
		<div id="footerDiv" class="row m-0 p-0" style="font-size:12px;font-weight:bold;">
			<div class="col-12" id="paymentDetailsDiv" >
				<div class="row p-0 m-0">
					<div class="col-2 p-2 " style="border-right:1px solid #9d9191;">
						<table border="0">
						<tr><td>Remarks</td><td>
						<input type="text" autocomplete="off" id="remarks_txt" name="remarks_txt" class="cp_textbox" maxlength="300"/>
						
						</td></tr>
						<tr><td>Delivery</td><td>
						<input type="text" autocomplete="off" id="delivery_txt" name="delivery_txt" class="cp_textbox" maxlength="300" value="Immediate" style="display:none;" />
						<div id="delivery_option_div">
							<label class="radioItem">
							  <input type="radio" name="delivery_option" value="Immediate" class="radioButton" checked onclick="fnDeliveryOption(1)" />
							  Immediate
							</label>
							<label class="radioItem">
							  <input type="radio" name="delivery_option" value="Date Time" class="radioButton" onclick="fnDeliveryOption(2)"/>
							  Date Time
							</label>
							
							<input type="text" id="delivery_dt_tm_txt" name="delivery_dt_tm_txt" class="dateTimepicker" onchange="set_delivery_txt()" style="display:none;">
						</div>
						</td></tr>
						</table>
					</div>
					<div class="col-4 p-2" id="advance_div" style="border-right:1px solid #9d9191;">
						<table border="0" width="100%">
						<tr><td>Sub.Total :</td><td>
								<input type="text" autocomplete="off" class="cp_textbox numericOnly" id="approximate_amount_txt" name="approximate_amount_txt" style="text-align:right;"/>
							</td>
						<td>Cash :</td><td>
								<input type="text" autocomplete="off" class="cp_textbox numericOnly" id="advance_cash_txt" name="advance_cash_txt" style="text-align:right;" />
							</td>
						</tr>
						<tr><td>Advance : </td><td>
								<input type="text" autocomplete="off" class="cp_textbox numericOnly" id="advance_amount_txt" name="advance_amount_txt" style="text-align:right;"/><span id="advance_warning" style="color:red;"></span>
							</td>
							<td>Online :</td><td>
								<input type="text" autocomplete="off" class="cp_textbox numericOnly" id="advance_gpay_txt" name="advance_gpay_txt" style="text-align:right;" />
								<button type="button" id="generateQrAdvBtn" onclick="generate_qrcode('JC')" style="margin-top:3px;width:100%;font-size:11px;padding:2px 4px;background:#0d6efd;color:#fff;border:none;border-radius:3px;cursor:pointer;display:none;">&#9654; Generate QR</button>
							</td>
						</tr>
						</table>
						
					</div>
			<div class="col-4">
				<div id="balance_div" style="display:none;">
				<div style="position:relative;float:left; width:20%;">
					<center><b>GST Details</b></center>
					<br>
					<input type="checkbox" id="gst_extra_chk" name="gst_extra_chk" onclick="calculate_gst()" disabled> add GST </input>
					<hr style="margin:0;padding:0;">
					
					<center><span style="font-weight:bolder;" id="gst_charges_span">0</span></span>
				</div>				
				<table border="0" width="70%">

					<tr><td>Discount</td>
						<td><input type="text" autocomplete="off"  class="cp_textbox numericOnly" id="discount_txt" name="discount_txt" style="text-align:right;" readonly title="Discount is set via ITEM DISCOUNTS button." />
							<span id="max_value_discountable" style="display:none;"></span>
						</td>
						<td><label style="font-size:0.75rem;font-weight:bold;">Cash</label></td>
						<td><input type="text" autocomplete="off" class="cp_textbox numericOnly" id="balance_cash_txt" name="balance_cash_txt" value="" style="text-align:right;"/></td>
					</tr>
					<tr>
						<td>Balance</td>
						<td><input type="text" autocomplete="off" class="cp_textbox" id="balance_amount_txt" name="balance_amount_txt" disabled style="text-align:right;"/><center> = <span id="balance_should_be"></span><span id="gst_xtra"></span></center>
						</td>
						<td><label style="font-size:0.75rem;font-weight:bold;">Online / UPI</label></td>
						<td>
							<input type="text" autocomplete="off" class="cp_textbox numericOnly" id="balance_gpay_txt" name="balance_gpay_txt" value="" style="text-align:right;"/>
							<button type="button" id="generateQrBtn" onclick="fnGenerateQrManual()" style="margin-top:3px;width:100%;font-size:11px;padding:2px 4px;background:#0d6efd;color:#fff;border:none;border-radius:3px;cursor:pointer;display:none;">&#9654; Generate QR</button>
						</td>
					</tr>
				</table>

				</div>
			</div>

							
					<div class="col-1" id="qrCodeSection" style="text-align:center;border-left:1px solid #9d9191;">
					<label class="form-check-label">QR CODE</label>
						<div class="container" style="width:auto;overflow:hidden;font-size:10px;text-align:center;font-weight:bold;">
							<div id="qrcode" onclick="copyCanvasToClipboard()"></div>
							<div id="username-slab" class="name-slab"> </div>
						</div>
					</div>
					<div class="col-1 p-2" style="text-align:center;" >
						<label class="form-check-label">PAYMENT SCREEN</label>						 
						<img loading="lazy" class="iconImgCls" src="img/whatsapp.png" alt="Whatsapp" style="width:35%;">
					</div>
					
				</div>
			</div>
			
			<div class="row">
				<div class="col-12 mt-2" style="text-align:center;">
					
					<button class="btn btn-sm btn-darkpurple" id="saveBtn" name="saveBtn" value="SAVE" style="border: 2px solid #fff;" onclick="saveJobCard()">SAVE </button>
					<!-- saravanan changes for discount start -->
					<button class="btn btn-sm btn-warning" id="itemDiscountBtn" 
        name="itemDiscountBtn" 
        style="border:2px solid #fff;display:none;" 
        onclick="fnOpenItemDiscountModal()">
  ITEM DISCOUNTS
</button>
<!-- saravanan changes for discount end -->
					<button disabled  class="btn btn-sm btn-darkpurple" id="printBtn" name="printBtn" value="PRINT" style="border: 2px solid #fff;" onclick="printJobCard()">PRINT JC</button>
					<button class="btn btn-sm btn-darkpurple" id="newBtn" name="newBtn" value="NEW" style="border: 2px solid #fff;" onclick="newJobCard()">NEW</button>

					
					<button class="btn btn-sm btn-darkpurple" id="goBtn" onclick="Convert_and_Close_JCs()" style="display:none;">Close JC >> SQ </button>
					<button class="btn btn-sm btn-warning text-dark" id="parkQrBtn" onclick="fnParkJCForQRPayment()" style="display:none;" title="Hold this Job Card — it will automatically convert to Sales Quote once the customer pays via QR">⏳ Hold for QR Payment</button>
					<button disabled  class="btn btn-sm btn-darkpurple" id="printSQBtn" name="printSQBtn" value="PRINT" style="border: 2px solid #fff;display:none;" onclick="printSQ()">PRINT SQ</button>
					<button type="button" class="btn btn-sm btn-darkpurple" id="closeBtn" name="closeBtn" value="CLOSE" style="border: 2px solid #fff;display:none;" onclick="closeJobCard()">CLOSE</button>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- ══ Online → Cash Payment Switch Panel (shown only for QR-pending JCs in Edit mode) ══ -->
<div id="qrToCashPanel" style="display:none;margin:10px 8px 0 8px;">
  <div style="background:#fff3cd;border:2px solid #ffc107;border-radius:10px;padding:14px 18px;">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
      <span style="font-size:1.4rem;">⏳</span>
      <strong style="font-size:0.95rem;color:#856404;">This Job Card has a Pending QR / Online Payment</strong>
    </div>
    <p style="font-size:0.82rem;color:#555;margin-bottom:12px;">
      The customer has not yet completed the UPI/QR payment. If the customer paid <strong>cash</strong> instead, select the option below and enter a reason.
    </p>
    <div style="display:flex;flex-direction:column;gap:8px;">
      <label style="font-size:0.85rem;cursor:pointer;">
        <input type="radio" name="qr_switch_action" value="keep" checked style="margin-right:6px;">
        Keep as <strong>Online / QR</strong> payment (still waiting for bank confirmation)
      </label>
      <label style="font-size:0.85rem;cursor:pointer;">
        <input type="radio" name="qr_switch_action" value="cash" style="margin-right:6px;">
        Switch to <strong>Cash</strong> payment (customer paid cash instead)
      </label>
    </div>
    <div id="qrCashReasonDiv" style="display:none;margin-top:12px;">
      <label style="font-size:0.82rem;font-weight:600;color:#333;display:block;margin-bottom:7px;">
        Select Reason <span style="color:red;">*</span>
      </label>
      <!-- Quick-select preset reason chips -->
      <div id="qrReasonChips" style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:10px;">
        <button type="button" class="qr-reason-chip" data-reason="Customer's GPay / UPI app is not working">
          📵 GPay / UPI not working
        </button>
        <button type="button" class="qr-reason-chip" data-reason="Bank server issue — UPI transaction failed">
          🏦 Bank server issue
        </button>
        <button type="button" class="qr-reason-chip" data-reason="QR code not scanning / QR expired">
          📷 QR not scanning / expired
        </button>
        <button type="button" class="qr-reason-chip" data-reason="Network issue on customer's phone">
          📶 Network issue
        </button>
        <button type="button" class="qr-reason-chip" data-reason="Customer changed mind — prefers to pay cash">
          💵 Customer prefers cash
        </button>
        <button type="button" class="qr-reason-chip" data-reason="">
          ✏️ Something else…
        </button>
      </div>
      <style>
        .qr-reason-chip {
          background:#fff;border:1.5px solid #ffc107;border-radius:20px;
          padding:5px 13px;font-size:0.78rem;font-weight:600;color:#856404;
          cursor:pointer;transition:all .15s;white-space:nowrap;
        }
        .qr-reason-chip:hover  { background:#fff3cd; }
        .qr-reason-chip.active { background:#ffc107;color:#000;border-color:#e6ac00; }
      </style>
      <textarea id="qrCashReasonTxt" rows="2"
        placeholder="Click a reason above, or type your own reason here…"
        style="width:100%;border:1.5px solid #ffc107;border-radius:6px;padding:6px 10px;
               font-size:0.83rem;resize:vertical;background:#fffdf0;"></textarea>
      <button type="button" onclick="fnConfirmQrToCash()"
        style="margin-top:10px;background:#198754;color:#fff;border:none;border-radius:7px;
               padding:8px 22px;font-size:0.85rem;font-weight:600;cursor:pointer;">
        ✔ Confirm — Switch to Cash
      </button>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="newItemModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 70%; width: 50%;height:50%;">

    <!-- Modal content-->
    <div class="modal-content" style="min-height: 40%;overflow: hidden;">
       
	  <div class="modal-header ">
        <h5 class="modal-title">New Item</h5>
      </div>
      <div class="modal-body">
		<div class="container-fluid">
			<div class="btn-group" role="group" aria-label="main_tabs">
				<button class="main_tab_btns btn btn-primary" page="printing" value="PRINTING">Printing</button>
				<button class="main_tab_btns btn btn-primary" page="services" value="SERVICES">Services</button>
				<button class="main_tab_btns btn btn-primary" page='materials' value="MATERIALS">Materials</button>
				<button class="main_tab_btns btn btn-primary" page='idcards' value="IDCARDS">ID Cards</button>
				<button class="main_tab_btns btn btn-primary" page='products' value="PRODUCTS">Products</button>
				
			</div>
			<input type="hidden" name="sales_typeTxt" id="sales_typeTxt" value="" />
			<div class="container-fluid" id="main_tab_content_div">
			
			</div>
			 
		</div>
        
      </div>
      <div class="modal-footer" style="background-color: #e7e7e7;min-height:50px;position:relative;text-align:right;">
		
		<button class="btn btn-darkpurple" id="addToTableBtn" name="addToTableBtn"  style="border:2px solid #fff;" onclick="fnAddToTable()">Add >></button>
		
		<span onclick='closeNewItemsModal()' class="btn btn-light" style="float:right;right:100px;">Close [Esc]</span>
		<span onclick='reset_newItemModal_ui()' class="btn btn-light" style="float:right;right:100px;">Reset [F9]</span>
        
      </div>
    </div>

  </div>
</div>
<!-- Modal -->
<div id="mobileNoHistoryModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 70%; width: 50%;height:50%;">

    <!-- Modal content-->
    <div class="modal-content" style="min-height: 40%;overflow: hidden;">
       
	  <div class="modal-header ">
        <h5 class="modal-title">History</h5>
      </div>
      <div class="modal-body">
		<div class="container-fluid" id="history_content">
			
			 
		</div>
        
      </div>

    </div>

  </div>
</div>
	<?php include_once "new_credit_customer_ui.php"; ?>



<style>
 
.contentDiv
{
	border:1px solid #000;
	border-radius:10px;
	margin:10px;
	padding:10px;
	width:500px;
	width: 97% !important;
	background-color:#d4edfc;
}
.contentDiv h1 {
    font-size: 38px;
    color: #000;
    margin: 0;
    font-weight: bold;
    letter-spacing: 2px;
    text-align: center;
}
.contentDiv h2 {
    font-size: 16px;
    color: green;
    margin-top: 10px;
    font-weight: bold;
    display: inline-block;
    position: relative;
   margin-left:37%;
    z-index: 2;
	letter-spacing: 1px;
}
  .contentDiv h2::before{
      content: "";
    position: absolute;
    top: 50%;
    width: 80px;
    height: 2px;
    background-color: red;
    left: -82px;
	}
    .contentDiv h2::after {
          content: "";
    position: absolute;
    top: 50%;
    width: 75px;
    height: 2px;
    background-color: red;
    left: 135px;
}
.contentDiv h3{
    font-size: 14px;
    color: red;
    margin-top: 10px;
    font-weight: bold;
    display: inline-block;
    position: relative; 
	letter-spacing: 1px;
	text-align:center;
	width:100%;
}
.contentDiv h4 {
    font-size: 14px;
    color: #000; 
    margin-top: 10px;
    font-weight: bold;
    display: inline-block;
    position: relative;  
	text-align:center;
	width:100%;
} 
.qrcodeDiv
{
	padding:10px;
	text-align:center;
}
#qrcodeNew{
	padding:1px;
	width:202px;
	height:202px;
	margin:auto;
}
#qrcodeNew img, #qrcode img {
	width:auto;
	height:auto;
	display:block;
	margin:auto;
}
@keyframes qr-spin {
	to { transform: rotate(360deg); }
}
</style>

<div id="qrcodeModal" style="    position: fixed;
    width: 50%;
    z-index: 101;
    top: 200px;
    left: 30%;
    background: #fff;
    border: 3px solid grey;
display:none;">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
	     <h4 class="modal-title">QR Code</h4>
        <button type="button" class="close" data-dismiss="modal" style="position:absolute;right:10px;" onclick="qrPopupClose()">&times;</button>
     
      </div>
      <div class="modal-body">
		<div class="contentDiv" id="contentQrcodeDiv" style="display:block;">
			<h1>PRINTZY</h1>
			<h2>DIGITAL PRESS</h2><br>
			<h3>SCAN TO PAY</h3>
			<div class="row">
			<div class="col-4">
			JOB CARD NO : <span id="qr_jc_no"></span>
			</div>
			<div class="col-4 qrcodeDiv" style="text-align:center;" >
			<div id="qrcodeNew" ></div>
			</div>
			<div class="col-4">
			JOB CARD DATE : <span id="qr_jc_date"></span>
			</div>
			</div>
			<div class="row">
			<div class="col-12" style="text-align:center;" id="qr_jc_user_name">

			</div>
			</div>
			<h4 ><span id="qr_jc_amount"></span></h4>

			<!-- Canara Bank payment status -->
			<div id="qr_payment_status" style="margin-top:10px;text-align:center;">
				<div id="qr_status_waiting" style="display:none;color:#555;font-size:13px;">
					<div style="display:inline-block;width:18px;height:18px;border:3px solid #ccc;border-top-color:#D73D32;border-radius:50%;animation:qr-spin 0.8s linear infinite;vertical-align:middle;margin-right:6px;"></div>
					Waiting for payment...
				</div>
				<div id="qr_status_success" style="display:none;background:#e6f9ed;border:2px solid #27ae60;border-radius:8px;padding:12px 16px;color:#1a7a3a;font-size:16px;font-weight:bold;">
					&#10003; Payment Received!<br>
					<span style="font-size:12px;font-weight:normal;color:#27ae60;">UPI payment successful</span>
				</div>
				<div id="qr_status_failed" style="display:none;background:#fff0f0;border:2px solid #e74c3c;border-radius:8px;padding:10px 14px;color:#c0392b;font-size:13px;font-weight:bold;">
					&#10007; Payment Failed / Timed out<br>
					<button onclick="retryQrPayment()" style="margin-top:6px;padding:4px 14px;background:#e74c3c;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:12px;">&#8635; Try Again</button>
				</div>
			</div>
		</div>
		<img id="preview" alt="" >
      </div>
      
    </div>

  </div>
</div>


 <button id="click2copy" style="display:none;">Copy 2 Clipboard</button>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
$(document).ready(function() {
	 
	$('.dateTimepicker').datetimepicker({
    dateFormat: 'dd-mm-yy', // Example date format
    timeFormat: 'hh:mm'  // Example time format
}); 
});
// saravanan changes for discount start
// ── Item-wise discount modal ───────────────────────────────────────────
function fnOpenItemDiscountModal() {
    var jcNo = $("#jobcard_no_txt").text();
    if (!jcNo) {
        toastr.error("Save the job card first.");
        return;
    }

    $("#idm_jc_no").text(jcNo);
    $("#idm_error_msg").hide().text("");
    $("#itemDiscountTbody").html(
        "<tr><td colspan='6' style='text-align:center;'>Loading…</td></tr>"
    );
    $('#itemDiscountModal').modal('show');

    $.ajax({
        type: "GET",
        url: "api/get_item_discounts.php?jobcard_no=" + jcNo,
        success: function(response) {
            var rows = "";
            var totalValue    = 0;
            var totalDiscount = 0;

            if (!response || response.length === 0) {
                rows = "<tr><td colspan='6' style='text-align:center;'>No items found.</td></tr>";
            } else {
                $.each(response, function(i, item) {
                    // ── use correct column names ──
                    var val      = parseFloat(item.value_amount) || 0;
                    var disc     = parseFloat(item.item_discount) || 0;
                    var net      = val - disc;
                    // product label: use job_in_detail, fall back to product_code
                    var label    = item.job_in_detail || item.product_code || "Item " + (i+1);
                    var rowId    = item.jobcard_details_id;
                    totalValue    += val;
                    totalDiscount += disc;

                    rows += "<tr>";
                    rows += "<td>" + (i + 1) + "</td>";
                    rows += "<td>" + label + "</td>";
                    rows += "<td style='text-align:right;'>" + item.total_qty + "</td>";
                    rows += "<td style='text-align:right;'>" + val.toFixed(2) + "</td>";
                    rows += "<td style='text-align:right;padding:2px 4px;'>"
                          + "<input type='number' min='0' max='" + val.toFixed(2) + "' "
                          + "step='0.01' class='form-control form-control-sm idm-disc-input' "
                          + "style='text-align:right;width:110px;display:inline-block;' "
                          + "data-id='" + rowId + "' "
                          + "data-max='" + val.toFixed(2) + "' "
                          + "value='" + disc.toFixed(2) + "' "
                          + "oninput='fnUpdateItemRow(this)'>"
                          + "</td>";
                    rows += "<td style='text-align:right;' id='idm_net_" + rowId + "'>"
                          + net.toFixed(2) + "</td>";
                    rows += "</tr>";
                });
            }

            $("#itemDiscountTbody").html(rows);
            $("#idm_total_value").text(totalValue.toFixed(2));
            fnRecalcDiscountTotals();
        },
        error: function() {
            toastr.error("Failed to load item details.");
        }
    });
}

function fnUpdateItemRow(inputEl) {
    var disc = parseFloat($(inputEl).val()) || 0;
    var max  = parseFloat($(inputEl).attr("data-max")) || 0;
    var id   = $(inputEl).attr("data-id");
    var net  = max - disc;

    if (disc < 0) disc = 0;
    if (disc > max) {
        $(inputEl).val(max.toFixed(2));
        disc = max;
        net  = 0;
        toastr.warning("Discount cannot exceed item value of ₹" + max.toFixed(2));
    }

    // 16% per-item limit for non-admin users
    if (SESSION_USER_TYPE !== "ADMIN" && SESSION_USER_TYPE !== "SUPERADMIN") {
        var maxAllowed = max * 0.16;
        if (disc > maxAllowed) {
            $(inputEl).val(maxAllowed.toFixed(2));
            disc = maxAllowed;
            net  = max - disc;
            toastr.warning("Discount cannot exceed 16% of item value (Max: ₹" + maxAllowed.toFixed(2) + "). Contact Admin for higher discount.");
        }
    }

    $("#idm_net_" + id).text(net.toFixed(2));
    fnRecalcDiscountTotals();
}

function fnRecalcDiscountTotals() {
    var totalDiscount = 0;
    var totalNet      = 0;
    var totalValue    = parseFloat($("#idm_total_value").text()) || 0;

    $(".idm-disc-input").each(function() {
        var disc = parseFloat($(this).val()) || 0;
        var max  = parseFloat($(this).attr("data-max")) || 0;
        totalDiscount += disc;
        totalNet      += (max - disc);
    });

    $("#idm_total_discount").text(totalDiscount.toFixed(2));
    $("#idm_total_net").text(totalNet.toFixed(2));
}

function fnSaveItemDiscounts() {
    var jcNo  = $("#jobcard_no_txt").text();
    var items = [];
    var valid = true;

    $(".idm-disc-input").each(function() {
        var disc = parseFloat($(this).val()) || 0;
        var max  = parseFloat($(this).attr("data-max")) || 0;
        var id   = $(this).attr("data-id");

        if (disc < 0 || disc > max) {
            toastr.error("Discount for row " + id + " is out of range.");
            valid = false;
            return false; // break
        }

        // 16% per-item limit for non-admin users
        if (SESSION_USER_TYPE !== "ADMIN" && SESSION_USER_TYPE !== "SUPERADMIN") {
            var maxAllowed = max * 0.16;
            if (disc > maxAllowed) {
                toastr.error("Item discount cannot exceed 16% of item value (Max: ₹" + maxAllowed.toFixed(2) + "). Please contact Admin.");
                valid = false;
                return false; // break
            }
        }

        items.push({
            detail_id:     id,
            item_discount: disc.toFixed(2),
            item_value:    max.toFixed(2)
        });
    });

    if (!valid) return;

    $.ajax({
        type: "POST",
        url: "api/save_item_discounts.php",
        data: {
            jobcard_no: jcNo,
            items: JSON.stringify(items)
        },
        success: function(response) {
            if (response.status === "success") {
                toastr.success("Item discounts saved!");

                // ── Reflect total discount back into the main form ──────
                var totalDisc = parseFloat(response.total_discount) || 0;
                $("#discount_txt").val(totalDisc.toFixed(2));
                calc_bal_total();   // recalculates balance_amount_txt

                if (totalDisc > 0) {
					$("#discount_txt").prop("readonly", true)
						.attr("title", "Discount is set item-wise. Open 'ITEM DISCOUNTS' to change.")
						.css("background-color", "#d4edda");
				} else {
					$("#discount_txt").prop("readonly", true)
						.attr("title", "Discount is set via ITEM DISCOUNTS button.")
						.css("background-color", "");
				}
				// ────────────────────────────────────────────────────────────

				$('#itemDiscountModal').modal('hide');
			} else {
				toastr.error(response.message || "Save failed.");
			}
        },
        error: function() {
            toastr.error("Network error. Please try again.");
        }
    });
}
// ── End item-wise discount ─────────────────────────────────────────────
// saravanan changes for discount end
function fnDeliveryOption(pType)
{
	if(pType==1)
	{
		$('#delivery_dt_tm_txt').hide();
		$('#delivery_txt').val('Immediate');
	}
	else
	{
		$('#delivery_txt').val('');
		 $('.dateTimepicker').datetimepicker('setDate', new Date());
		$('#delivery_dt_tm_txt').show();
	}
}
function Convert_and_Close_JCs(pBypassBalanceCheck)
{
	var sqno="",sino="";
	jcnos=$("#jobcard_no_txt").text();

	
	approximate_amount_txt=parseFloat($("#approximate_amount_txt").val());
	if (isNaN(approximate_amount_txt)) approximate_amount_txt=0.0;
	advance_amount_txt=parseFloat($("#advance_amount_txt").val());
	if (isNaN(advance_amount_txt)) advance_amount_txt=0.0;
	balance_amount_txt=parseFloat($("#balance_amount_txt").val());
	if (isNaN(balance_amount_txt)) balance_amount_txt=0.0;
	discount_txt=parseFloat($("#discount_txt").val());
	if (isNaN(discount_txt)) discount_txt=0.0;
	// Zero-discount confirm: skip for WalkIn customers (they rarely get discounts) and
	// skip when pBypassBalanceCheck=true (user already confirmed "Convert to SQ?" — a
	// second dialog is confusing and blocks the close).
	if(discount_txt==0 && !pBypassBalanceCheck)
	{
		var _custTypeForDiscChk = $('input[name="customer_type_txt"]:checked').val();
		if (_custTypeForDiscChk !== 'WalkIn') {
			var userChoice = confirm("Are you sure about ZERO Discount ?");
			if(!userChoice)
			{
				return false;
			}
		}
	}
	// 16% per-item discount limit enforced at item level; no overall check needed here
	gst_tax_amount=parseFloat($("#gst_charges_span").text());
	if (isNaN(gst_tax_amount)) gst_tax_amount=0.0;
	approximate_amount_txt=approximate_amount_txt+gst_tax_amount;
	if(!$("#creditCustomerOpt").prop("checked"))
	{
		if((approximate_amount_txt-discount_txt) - (balance_amount_txt+advance_amount_txt)>0)
		{
			alert("Cannot have balance in this case");
			return false;
		}
		/*else
		{
			//alert("Balance Settled. Happy...");
		}*/
	}
	
	var _balCash  = parseFloat($("#balance_cash_txt").val()) || 0;
	var _balGpay  = parseFloat($("#balance_gpay_txt").val()) || 0;
	var _skipJournal = 0;

	// When walk-in paid full amount as advance, use advance amounts as payment mode for SQ record.
	// Journal was already posted by save_jobcard, so set skip_journal to avoid double-entry.
	if (pBypassBalanceCheck) {
		_balCash = parseFloat($("#advance_cash_txt").val()) || 0;
		_balGpay = parseFloat($("#advance_gpay_txt").val()) || 0;
		_skipJournal = 1;
	}

	// Credit customers pay on account — skip all cash/QR payment checks
	// pBypassBalanceCheck=true when walk-in paid full amount as advance (balance is correctly 0)
	if (!$("#creditCustomerOpt").prop("checked")) {
		if (!pBypassBalanceCheck && _balCash <= 0 && _balGpay <= 0) { toastr.error("Enter Cash or GPay balance amount."); return false; }

		// If GPay amount entered but QR not yet confirmed — check server before deciding
		if (!pBypassBalanceCheck && _balGpay > 0 && _canaraPaymentStatus !== 'SUCCESS') {

			// QR was never generated — block outright. User must generate QR first.
			if (_canaraCurrentTxnId === null) {
				toastr.error("Please generate the QR code first and complete the payment before closing.");
				return false;
			}

			var _jcNo = $.trim($("#jobcard_no_txt").text());
			var _serverPaid = false;
			if (_jcNo) {
				$.ajax({
					url: 'api/canara_check_jc_paid.php',
					async: false,
					data: { jc: _jcNo },
					dataType: 'json',
					success: function(r) {
						if (r && r.paid === true) { _serverPaid = true; }
					}
				});
			}
			if (_serverPaid) {
				// Payment confirmed in DB — sync local status and proceed silently
				_canaraPaymentStatus = 'SUCCESS';
			} else {
				// QR was generated but bank has not confirmed payment yet.
				// OK  → convert to SQ now (online payment will be marked as pending).
				// Cancel → go back; user can click "⏳ Hold for QR Payment" explicitly.
				if (!confirm(
					"⚠️ QR Payment not yet confirmed by the bank.\n\n" +
					"Click OK  →  Convert to SQ now (online payment will be marked as pending)\n" +
					"Click Cancel  →  Go back (use the \"⏳ Hold for QR Payment\" button to wait for auto-confirmation)"
				)) {
					return false;   // just dismiss — nothing is parked or converted
				}
			}
		}
	}

	var is_bal_in_cash_txt = (_balCash > 0) ? 1 : 0;
	var is_gst_extra = ($('#gst_extra_chk').is(':checked'))?1:0;
	var is_bal_in_gpay_txt = (_balGpay > 0) ? 1 : 0;
	// Use _balCash/_balGpay directly — these already hold the correct values:
	// either the balance fields (normal flow) or the advance amounts (pBypassBalanceCheck / walk-in full-advance flow)
	var balance_cash_txt = _balCash;
	var balance_gpay_txt = _balGpay;
	var balance_amount_txt = $("#balance_amount_txt").val();
	if (isNaN(balance_amount_txt)) balance_amount_txt=0.0;
	var discount_txt=parseFloat($("#discount_txt").val());	
	if (isNaN(discount_txt)) discount_txt=0.0;
	$.ajax({
	type: "POST",
	url: "api/close_jobcard_into_salesquote.php",
	data:{
		"jcnos":$("#jobcard_no_txt").text(),
		  "approximate_amount_txt":approximate_amount_txt,
		  "is_bal_in_cash_txt":is_bal_in_cash_txt,
		  "is_bal_in_gpay_txt":is_bal_in_gpay_txt,
		  "balance_cash_txt":balance_cash_txt,
		  "balance_gpay_txt":balance_gpay_txt,
		  "discount_txt":discount_txt,
		  "balance_amount_txt":balance_amount_txt,
		  "is_gst_extra_txt":is_gst_extra,
		  "gst_tax_amount_txt":gst_tax_amount,
		  "skip_journal_txt":_skipJournal
	},
	success: function (response) {
		if(response!="Failed")
		{
			sqno=response;
			$('#sq_no_txt').text(sqno);
			$("#JC_SQ_Title").text("Sales Quotation");
			$("#JC_SQ_Title").attr("style","background-color:#3c2201");
			$("#jobCardCustomerDiv").attr("style","background-color:#3c2201");
			$("#sq_no_container").show();
			$("#itemDiscountBtn").hide();
			$("#qrCodeSection").hide();
			$("#generateQrBtn").hide();
			_canaraStopPolling();
			alert("Converted Into SQ : "+sqno );
			if(is_gst_extra)
			{
				$.ajax({
					type: "GET",
					url: "api/save_sale_invoice.php?sq_no="+sqno+"&jc_nos="+$("#jobcard_no_txt").text(),
					async:false,		
					success: function (response) {
						if(response == "Failed")
						{
							toastr.error("Failed to convert to Invoice!!!");
							return;
						}
						else
						{
							sino=response;
							$('#si_no_txt').text(sino);
							set_si_ui();
							toastr.success("Successfully converted to Invoice. "+response);
						}
					}
				});				
			}
			loadJobCardList();
			loadSQList();
			loadSIList();
			
			//close_globalView();
			
			$('#discount_txt').prop('disabled','true');
			$('#balance_cash_txt').prop('disabled','true');
			$('#balance_gpay_txt').prop('disabled','true');
			$('#goBtn').prop('disabled','true');
			$('#parkQrBtn').hide();
			$('#printSQBtn').show();
			$('#printSQBtn').removeAttr('disabled');
			$("#gst_extra_chk").prop('disabled','true');
			$('#closeBtn').show().prop('disabled', false);
			
		}
		else
		{
			alert("Something went wrong. Pls. try Again");
			return false;			
		}
	}
	});
	

	
}
function setKBNavFor_main_tab_btns()
{
	const main_tab_btns = document.querySelectorAll('.main_tab_btns');

main_tab_btns.forEach(radio => {
  radio.addEventListener('keydown', (event) => {
	
    let currentIndex = Array.from(main_tab_btns).indexOf(radio);
	var focusedElement = $(document.activeElement);
    let nextIndex;	
    if (event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'Enter' ) {
	  event.stopPropagation();
      event.preventDefault(); // Prevent default Up/Down arrow behavior
      if (event.key === 'ArrowLeft') {		  
        nextIndex = (currentIndex - 1 + main_tab_btns.length) % main_tab_btns.length;		
	  main_tab_btns[nextIndex].focus();
      } else if (event.key === 'ArrowRight'){ 		
        nextIndex = (currentIndex + 1) % main_tab_btns.length;		
	  main_tab_btns[nextIndex].focus();
      } else if (event.key === 'Enter'){ 
		
		jc_keypress_flag=1;
		event.preventDefault();
		focusedElement.trigger('click');
      }      
    }
  });
});	
}

function newJobCard()
{
	window._jcDirty = false;
	$("#rightContentDiv").empty();
	$("#rightContentDiv").load("job_card.php");
}

// ── Online → Cash switch: show/hide reason box on radio change ──────────────
$(document).on('change', 'input[name="qr_switch_action"]', function() {
	if ($(this).val() === 'cash') {
		$('#qrCashReasonDiv').slideDown(180);
		// Reset chips and textarea
		$('.qr-reason-chip').removeClass('active');
		$('#qrCashReasonTxt').val('').focus();
	} else {
		$('#qrCashReasonDiv').slideUp(180);
	}
});

// ── Preset reason chip click ─────────────────────────────────────────────────
$(document).on('click', '.qr-reason-chip', function() {
	$('.qr-reason-chip').removeClass('active');
	$(this).addClass('active');
	var reason = $(this).data('reason');
	if (reason) {
		$('#qrCashReasonTxt').val(reason).focus();
	} else {
		// "Something else" chip — clear and let user type
		$('#qrCashReasonTxt').val('').focus();
	}
});

// ── Confirm: switch QR/Online payment to Cash ────────────────────────────────
function fnConfirmQrToCash() {
	var reason = $('#qrCashReasonTxt').val().trim();
	if (!reason) {
		toastr.error('Please enter a reason before confirming.');
		$('#qrCashReasonTxt').focus();
		return;
	}
	var jcno = $('#jobcard_no_txt').text().trim();
	if (!jcno) { toastr.error('Job Card number not found.'); return; }

	if (!confirm('Switch payment for JC ' + jcno + ' from Online/QR to Cash?\n\nReason: ' + reason)) return;

	$.ajax({
		type: 'POST',
		url:  'api/change_qr_to_cash.php',
		data: { jobcard_no: jcno, reason: reason },
		dataType: 'json',
		success: function(r) {
			if (r && r.status === 'success') {
				toastr.success('Payment mode switched to Cash. Reason saved to QR report.');
				setTimeout(function() { closeJobCard(); }, 1500);
			} else {
				toastr.error(r.message || 'Failed to switch payment mode.');
			}
		},
		error: function() { toastr.error('Network error. Please try again.'); }
	});
}

function closeJobCard()
{
	window._jcDirty = false;
	if($("#globalView").css('display') !== 'none' )
	{
		$("#globalViewContentDiv").html("");
		$("#globalView").hide();
		
	}
	else
	{
	$("#rightContentDiv").empty();
	$("#rightContentDiv").load("dashboard.php");				
	}
}

function closeNewItemsModal()
{

	$('#newItemModal').modal('hide');
	//$("#approximate_amount_txt").focus();

}

function disableDiv(divId) {
  var div = document.getElementById(divId);
  div.disabled = true;
  var elements = div.getElementsByTagName('*');
  for (var i = 0; i < elements.length; i++) {
	  if(elements[i].name !="closeBtn" && elements[i].name !="newBtn")
			elements[i].disabled = true;
    //elements[i].onclick = function() { return false; };
  }
}

function enableDiv(divId) {
  var div = document.getElementById(divId);
  div.disabled = false;
  var elements = div.getElementsByTagName('*');
  for (var i = 0; i < elements.length; i++) {
	 if(elements[i].name !="printBtn" && elements[i].name !="approximate_amount_txt" && elements[i].name !="advance_amount_txt")
		elements[i].disabled = false;
	
		$('#advance_cash_txt').prop('disabled',false);
			$('#advance_gpay_txt').prop('disabled',false);
		if($('#creditCustomerOpt').is(':checked'))
		{
			$('#advance_cash_txt').prop('disabled',true);
			$('#advance_gpay_txt').prop('disabled',true);
		}			
    //elements[i].onclick = function() { return true; };&& elements[i].name !="advance_cash_txt" && elements[i].name !="advance_gpay_txt" 
  }
}
			
$('#credit_customer_name_txt').on('change', function(evt) { $("#credit_customer_code_txt").val(this.value); });
$('.main_tab_btns').on('focus', function(evt) {
	
	$(".main_tab_btns").removeClass("bg-orange");
	$(this).addClass("bg-orange");
	$("#sales_typeTxt").val($('.main_tab_btns').val());
	//$("#main_tab_content_div").load($(this).attr("page")+".php");
	//$(".main_tab_btns").hide();
	$("#main_tab_content_div").load($(this).attr("page")+".php?category1="+$(this).attr("value"));
	//$("#main_tab_content_div").load("page_category_1.php?category1="+$(this).attr("value"));
});
$('.main_tab_btns').on('click', function(evt) {
	evt.stopPropagation();
	evt.preventDefault();
	$(".main_tab_btns").removeClass("bg-orange");
	$(this).addClass("bg-orange");
	$("#sales_typeTxt").val($('.main_tab_btns').val());
	//$("#main_tab_content_div").load($(this).attr("page")+".php");
	$(".main_tab_btns").hide();
	$("#main_tab_content_div").load($(this).attr("page")+".php?category1="+$(this).attr("value"), function(){
	const radioButtons = document.querySelectorAll('input[type="radio"][name="media_stock_type_txt"]');
	
	radioButtons[0].focus();
	$(radioButtons[0]).prop("checked", true);
	$(radioButtons[0]).trigger('click');
	$(".media_stock_type_txt").on("keyup", function(e) {
				if(jc_keypress_flag)
				{					
					jc_keypress_flag=0;
					return;					
				}
				if( e.target !== this ) {						
					   return;
				}
			e.stopPropagation();
			if ( e.which == 13 ) {				
				e.preventDefault();
				$("#search_product_txt").focus();
				$(".media_stock_type_txt").prop('disabled',true);
			}
			});	
	});
	//$("#main_tab_content_div").load("page_category_1.php?category1="+$(this).attr("value"));
});
 

$(document).ready(function() {
	SetUpBasics();

	// ── Navigation guard: mark JC dirty as soon as user touches the form ──────
	window._jcDirty = false;
	$('#customer_name_txt, #customer_mobile_no_txt, #remarks_txt').on('input', function() {
		window._jcDirty = true;
	});
	$('input[name="customer_type_txt"]').on('change', function() {
		window._jcDirty = true;
	});
	$('#cash_txt, #advance_txt, #advance_gpay_txt').on('input', function() {
		window._jcDirty = true;
	});
	// ─────────────────────────────────────────────────────────────────────────

	$(document).keydown(function(e) {
		if(e.keyCode === 45)//insert key
		{
			e.stopPropagation();
			fnShowAddPopup();
		}
		if(e.keyCode === 120)//F9 Key
		{
			e.stopPropagation();
			reset_newItemModal_ui();
		}
	});	
    var availableMobileNos = [
	<?php
		$mob_sql="SELECT DISTINCT customer_mobile_no FROM jobcard_master WHERE customer_type='General' AND customer_mobile_no != ''
		          UNION
		          SELECT DISTINCT mobile_no FROM customer_master WHERE customer_type='General' AND mobile_no != ''
		          ORDER BY customer_mobile_no;";
		if($mob_qry=mysqli_query($connection,$mob_sql))
		{
			$ans="";
			while($mob_row=mysqli_fetch_array($mob_qry))
			{
				$customer_mobile_no=$mob_row["customer_mobile_no"];
				if(strlen($customer_mobile_no)>0)
				{
					if($ans!="") $ans.=",";
					$ans.= '"'.$customer_mobile_no.'"';
				}
			}
			echo $ans;
		}
	?>

    ];
    
    var availableCustomerNames = [
  <?php
    $name_sql = "SELECT DISTINCT customer_name FROM jobcard_master WHERE customer_type='General' AND customer_name != ''
                 UNION
                 SELECT DISTINCT customer_name FROM customer_master WHERE customer_type='General' AND customer_name != ''
                 ORDER BY customer_name;";
    if ($name_qry = mysqli_query($connection, $name_sql)) {
        $name_ans = "";
        while ($name_row = mysqli_fetch_array($name_qry)) {
            $customer_name = addslashes($name_row["customer_name"]);
            if (strlen($customer_name) > 0) {
                if ($name_ans != "") $name_ans .= ",";
                $name_ans .= '"' . $customer_name . '"';
            }
        }
        echo $name_ans;
    }
  ?>
  ];
$( "#customer_mobile_no_txt" ).autocomplete({
      source: availableMobileNos,
	  select: function( event, ui ) {
		  lookout_for_general_customer(ui.item.value);
	  }
    });	
    
    $("#customer_name_txt").autocomplete({
    source: availableCustomerNames,
    minLength: 1,
    select: function(event, ui) {
        // Auto-fill mobile number based on selected name
        $.ajax({
            type: "GET",
            url: "api/get_general_customer_name.php?cust_name=" + encodeURIComponent(ui.item.value),
            async: false,
            success: function(response) {
                if (response.trim() != "") {
                    $("#customer_mobile_no_txt").val(response.trim());
                }
            }
        });
    }
});

// Enable autocomplete only when General Customer is selected
$("#customer_name_txt").autocomplete("disable");

$('#generalCustomerOpt').on('click', function() {
    $("#customer_name_txt").autocomplete("enable");
});
$('#walkCustomerOpt').on('click', function() {
    $("#customer_name_txt").autocomplete("disable");
});
$('#creditCustomerOpt').on('click', function() {
    $("#customer_name_txt").autocomplete("disable");
});
function lookout_for_general_customer(pmobile_no="")
{
	mobile_no=$( "#customer_mobile_no_txt" ).val();
	if(pmobile_no!="")mobile_no=pmobile_no;
	if(mobile_no.length>5)
	{
	$.ajax({
	type: "GET",
	url: "api/get_general_customer_name.php?mb_no="+mobile_no,
	async:false,
	success: function (response) { 
		if(response.trim()!="")
		{
			if($("#customer_name_txt").val()=="")
				$("#customer_name_txt").val(response);
		}
	}
	});
	}		
}
$( "#customer_mobile_no_txt" ).on('change',function(){
	lookout_for_general_customer();
});
    function copyCanvasToClipboardold(elementId) {
		
        const element = $("#"+elementId+">canvas");
		element.toBlob(function(blob) { 
		const item = new ClipboardItem({ "image/png": blob });
		navigator.clipboard.write([item]); 
		
		});

    }

    // Attach the function to a button click (e.g., a button with class 'copy-btn')
  /* $('#click2copy').click(function() {
        copyCanvasToClipboard('contentQrcodeDiv'); // Replace 'myElement' with your target element's ID
    });	  */
	
	  $("#click2copy").on("click", async function () {
	  $("#contentQrcodeDiv").show();
	  
	  const element = document.getElementById("contentQrcodeDiv");

      // Save current styles
      const originalDisplay = element.style.display;

      // Temporarily show it off-screen
      element.style.display = "block";
      element.style.position = "absolute";
      //element.style.left = "-9999px"; // move out of view

      // Capture with html2canvas
      const canvas = await html2canvas(element);
		
      // Restore original display
      element.style.display = originalDisplay;
      element.style.position = "";
      element.style.left = "";
	  $("#contentQrcodeDiv").hide();
      // Show preview
		$("#preview").attr("src", canvas.toDataURL("image/png"));
		 
  $("#preview").show(); 
      // Copy to clipboard
     /* canvas.toBlob(async (blob) => {
        try {
          await navigator.clipboard.write([
            new ClipboardItem({ "image/png": blob })
          ]);
          //alert("✅ Hidden div copied to clipboard!");
        } catch (err) {
          console.error("Failed to copy: ", err);
         // alert("❌ Copy failed: " + err.message);
        }
      }, "image/png");*/
	  
      });
//const radioButtons = document.querySelectorAll('input[type="radio"][name="customer_type_txt"]');
const radioButtons = document.querySelectorAll('input');

/*radioButtons.forEach(radio => {
  radio.addEventListener('keyup', (event) => {
    let currentIndex = Array.from(radioButtons).indexOf(radio);
	var focusedElement = $(document.activeElement);
    let nextIndex;	
	event.stopPropagation();
    if (event.key === 'ArrowUp' || event.key === 'ArrowDown' || event.key == 'Enter') {
      event.preventDefault(); // Prevent default Up/Down arrow behavior
      if (event.key === 'ArrowUp') {		  
        nextIndex = (currentIndex - 1 + radioButtons.length) % radioButtons.length;		
	  radioButtons[nextIndex].focus();
	  focusedElement = $(document.activeElement);
	  focusedElement.trigger('click');		
      } else if (event.key === 'ArrowDown'){ // ArrowDown		
        nextIndex = (currentIndex + 1) % radioButtons.length;		
	  radioButtons[nextIndex].focus();
	  focusedElement = $(document.activeElement);
	  focusedElement.trigger('click');		
		
      }else if (event.key === 'Enter'){
		  
		  focusedId=focusedElement.attr("id");
		  if(focusedId=="walkCustomerOpt") $("#nextBtn").focus();
		  if(focusedId=="generalCustomerOpt") $("#customer_mobile_no_txt").focus();
		  if(focusedId=="creditCustomerOpt") $("#search-box").focus();			  
	  }      
    }
  });
});	*/
radioButtons.forEach(radio => {
  radio.addEventListener('keyup', (event) => {
    let currentIndex = Array.from(radioButtons).indexOf(radio);
	var focusedElement = $(document.activeElement);
    let nextIndex;	
	event.stopPropagation();
    if (event.key == 'Enter') {
      event.preventDefault(); // Prevent default Up/Down arrow behavior
      if (event.key === 'ArrowUp') {		  
        nextIndex = (currentIndex - 1 + radioButtons.length) % radioButtons.length;		
	  radioButtons[nextIndex].focus();
	  focusedElement = $(document.activeElement);
	  focusedElement.trigger('click');		
      } else if (event.key === 'ArrowDown'){ // ArrowDown		
        nextIndex = (currentIndex + 1) % radioButtons.length;		
	  radioButtons[nextIndex].focus();
	  focusedElement = $(document.activeElement);
	  focusedElement.trigger('click');		
		
      }else if (event.key === 'Enter'){
		  
		  focusedId=focusedElement.attr("id");
		  if(focusedId=="walkCustomerOpt") $("#nextBtn").focus();
		  if(focusedId=="generalCustomerOpt") $("#customer_mobile_no_txt").focus();
		  if(focusedId=="creditCustomerOpt") $("#search-box").focus();			  
	  }      
    }
  });
});
	
        // Apply highlighting when an element gains focus
        $('input, select, textarea, button').focus(function() {
            $(this).addClass('highlighted-focus');
			//$(this).effect("highlight", {color: "#ffffcc"}, 1000); // Highlight with yellow, duration 1 second
        });

         // Remove highlighting when an element loses focus
        $('input, select, textarea, button').blur(function() {
            $(this).removeClass('highlighted-focus');
        }); 	
	$("#newItemModal").on("shown.bs.modal", function () {
		//$( ".btn-group" ).find('.main_tab_btns:eq(0)').focus();
		
		$(".main_tab_btns").show();
		setKBNavFor_main_tab_btns();
		$('button:visible:enabled:first', this).focus();
	})	

	$("#newItemModal").on("hidden.bs.modal", function () {

		if(totalValue>0)
		{
			$("#approximate_amount_txt").val(totalValue.toFixed(2));
			enableDiv("jobCardSummary");

			// Auto-switch WalkIn → General if total >= ₹1000
			if(totalValue >= 1000 && $('#walkCustomerOpt').is(':checked')) {
				$('#generalCustomerOpt').prop('checked', true).trigger('click');
				toastr.warning("Amount ₹" + totalValue.toFixed(2) + " ≥ ₹1000. Automatically switched to General Customer. Please enter Name & Mobile No.");
				setTimeout(function() { $("#customer_name_txt").focus(); }, 150);
				return;
			}

			$("#remarks_txt").focus();

		}
	});
	

	
	/*$("#is_adv_in_cash_txt").on('change',function(evt){
			$("#advance_cash_txt").prop("disabled",!this.checked);
	});
	$("#is_adv_in_gpay_txt").on('change',function(evt){
			$("#advance_gpay_txt").prop("disabled",!this.checked);
	});*/
	disableDiv("jobCardSummary");
	$('#category1_option_div').hide();
	$('#functionTypesBtn').hide();
	$('#stock_based_ui_div').hide();

	/* $('input').on('keyup', function(evt) {
		evt.preventDefault();
		evt.stopPropagation();
		
		if (evt.which === 38)//Up Arrow
		{ 
		var focusables = $('input'); // Get all visible focusable elements
		var current = focusables.index(this); // Get the index of the current element
		
		var next = focusables.eq(current - 1).length ? focusables.eq(current - 1) : focusables.eq(0); // Get the next or loop to the first
		next.focus(); // Set focus to the next element
		//evt.preventDefault();		
		}
		else if (evt.which === 40)//Down Arrow 
		{ 
		var focusables = $('input'); // Get all visible focusable elements
		var current = focusables.index(this); // Get the index of the current element
		
		var next = focusables.eq(current + 1).length ? focusables.eq(current + 1) : focusables.eq(0); // Get the next or loop to the first
		next.focus(); // Set focus to the next element
		
		}
	}); */
	$('#walkCustomerOpt').on('click', function(evt) {
			evt.stopPropagation();
			$("#credit_customer_div").hide();
			$("#customer_name_txt").prop("disabled", false);
			$("#customer_addr1_txt").prop("disabled", false);
			$("#customer_addr2_txt").prop("disabled", false);
			$("#customer_city_txt").prop("disabled", false);
			$("#customer_state_txt").prop("disabled", false);
			$("#customer_email_txt").prop("disabled", false);
			$("#customer_gst_no_txt").prop("disabled", false);
			$("#customer_mobile_no_txt").prop("disabled", false);
			$("#customer_name_txt").val("");
			$("#customer_addr1_txt").val("");
			$("#customer_addr2_txt").val("");
			$("#customer_city_txt").val("");
			$("#customer_email_txt").val("");
			$("#customer_gst_no_txt").val("");
			$("#customer_mobile_no_txt").val("");			
			$("#customer_div").hide();
	});
	$('#generalCustomerOpt').on('click', function(evt) {
			
			$("#credit_customer_div").hide();
			$("#customer_div").show();
			$("#customer_name_txt").prop("disabled", false);
			$("#customer_addr1_txt").prop("disabled", true);
			$("#customer_addr2_txt").prop("disabled", true);
			$("#customer_city_txt").prop("disabled", true);
			$("#customer_state_txt").prop("disabled", true);
			$("#customer_email_txt").prop("disabled", true);
			$("#customer_gst_no_txt").prop("disabled", true);
			$("#customer_mobile_no_txt").prop("disabled", false);
			$("#credit_customer_code_txt").val("");
			$("#customer_name_txt").val("");
			$("#customer_addr1_txt").val("");
			$("#customer_addr2_txt").val("");
			$("#customer_city_txt").val("");
			$("#customer_email_txt").val("");
			$("#customer_gst_no_txt").val("");
			$("#customer_mobile_no_txt").val("");
			//$("#customer_name_txt").focus();
	});
	$('#creditCustomerOpt').on('click', function(evt) {
			
			$("#customer_div").show();
			$("#credit_customer_div").show();
			//$("#search-box").focus();
			$('#search-box').val("");
			$("#customer_name_txt").prop("disabled", true);
			$("#customer_addr1_txt").prop("disabled", true);
			$("#customer_addr2_txt").prop("disabled", true);
			$("#customer_city_txt").prop("disabled", true);
			$("#customer_state_txt").prop("disabled", true);
			$("#customer_email_txt").prop("disabled", true);
			$("#customer_gst_no_txt").prop("disabled", true);
			$("#customer_mobile_no_txt").prop("disabled", true);
			$("#credit_customer_code_txt").val("");
			$("#customer_name_txt").val("");
			$("#customer_addr1_txt").val("");
			$("#customer_addr2_txt").val("");
			$("#customer_city_txt").val("");
			$("#customer_email_txt").val("");
			$("#customer_gst_no_txt").val("");
			$("#customer_mobile_no_txt").val("");
						
	});
	$('.main_tab_btns123').on('click', function(evt) {
				
				$(".main_tab_btns").removeClass("btnSuccess");
				$(".main_tab_btns").removeClass("main_tab_btns_current");
				$(this).addClass("btnSuccess"); 
				$(this).addClass("main_tab_btns_current"); 
				$("#sales_typeTxt").val($(this).text());
				$("#titleName").text($(this).text());
				$("#main_tab_content_div").load($(this).attr("page")+".php", function() {
				  $('#category1_option_div').show();
				$('#functionTypesBtn').show();
				$('#stock_based_ui_div').hide();
				});
			});
	$('.datepicker').removeClass('hasDatepicker').datepicker({
				format: 'dd-mm-yyyy HH:MM' 
			}); 
	$("#walkCustomerOpt").focus();			
}); 
function fnShowAddPopup()
{
	if(!$("#printBtn").prop('disabled'))
	{
		toastr.error("once saved couldnot be altered");
		return;
	}
	var isDisabled = $('#nextBtn').prop('disabled');
	if(isDisabled)
	{
	$('#main_tab_content_div').html("");
	$('#newItemModal').modal('show');
	//$( ".btn-group" ).find('.main_tab_btns:eq(0)').trigger( "click");
	}
	//disableDiv("jobCardCustomerDiv");
	
	/* $('#category1_option_div').show();
	$('#functionTypesBtn').hide();
	$('#stock_based_ui_div').hide(); */
}
function fnNext()
{
	var customer_type_txt = $('input[name="customer_type_txt"]:checked').val();
	if(customer_type_txt!="WalkIn")
	{
		var customer_name_txt = $('#customer_name_txt').val();
		var customer_mobile_no_txt = $('#customer_mobile_no_txt').val();
		if(customer_type_txt=="General")
		{
			if(customer_name_txt.trim()=="")
			{
				alert("General Customer Name is required. Please enter the customer name before proceeding.");
				$("#customer_name_txt").focus();
				return;
			}
			if(customer_mobile_no_txt=="")
			{
				alert("General Customer should have Mobile No");
				$("#customer_mobile_no_txt").focus();
				return;
			}
		}
		var credit_customer_code_txt = $('#credit_customer_code_txt').val();
		if(customer_type_txt=="Credit")
		{
			if(customer_name_txt=="" || credit_customer_code_txt=="")
			{
				alert("Credit Customer should be selected");
				return;
			}
		}
	}
/* 	$('#main_tab_content_div').html("");
	$('#newItemModal').modal('show');
	$( ".btn-group" ).find('.main_tab_btns:eq(0)').trigger( "click");
	//$( ".btn-group" ).find('.main_tab_btns:eq(0)').focus();
 */	
	disableDiv("jobCardCustomerDiv");
	fnShowAddPopup();
	/* $('#category1_option_div').show();
	$('#functionTypesBtn').hide();
	$('#stock_based_ui_div').hide(); */
}
function reset_newItemModal_ui()
{
	$('#main_tab_content_div').html("");
	$(".main_tab_btns").show();
	$(".main_tab_btns:first-child").focus();
}

function delete_allTableData(pIndex)
{
	if(!$("#printBtn").prop('disabled'))
	{
		toastr.error("once saved couldnot be altered");
		return;
	}
	allTableData.splice(pIndex,1)
	render_jobdetailsTable_from_allTableData();
}
function render_jobdetailsTable_from_allTableData()
{
$("#jobdetailsTable tbody").empty();
	sNo=0;
	totalValue=0;
	for(var i=0;i<allTableData.length;i++)
	{
		sNo++;
		markup = "<tr>";
		gst_perc = allTableData[i]['gst_percentage_txt'];
		if(gst_perc % 1 == 0)
			gst_perc=parseInt(gst_perc,10);
			
		allTableData[i]['sNo']=sNo;
		markup = markup + "<td>" + allTableData[i]['sNo'] + "</td>";   
		markup = markup + "<td>" + allTableData[i]['machine_code_txt'] + "</td>";   
		//markup = markup + "<td>" + allTableData[i]['product_code_txt'] + "</td>";   
		markup = markup + "<td>" + allTableData[i]['product_name_txt'] + "</td>";   
		markup = markup + "<td>" + gst_perc  + "%</td>";
		markup = markup + "<td>" + allTableData[i]['first_copy_nos_txt'] + "</td>";   
		markup = markup + "<td>" + allTableData[i]['addl_copy_nos_txt'] + "</td>";   
		markup = markup + "<td>" + (allTableData[i]['is_front_and_back_txt']==1?"Yes":"No" )+ "</td>";   
		//markup = markup + "<td>" + allTableData[i]['prod_mat_qty'].replaceAll("~","<br>") + "</td>";   
		markup = markup + "<td>" + allTableData[i]['total_qty_txt'] + "</td>";   
		markup = markup + "<td>" + allTableData[i]['value'] + "</td>";  
		totalValue = totalValue + parseFloat(allTableData[i]['value']);
		markup = markup + "<td>";
		if(!block_editing)
		{
		markup = markup + "<a href='javascript:void(0)' onclick='delete_allTableData("+i+")'><img src='img/del.png' width='16' height='16' alt='Delete' /></a> ";
		}
		markup = markup + "</td>";
		markup = markup + "</tr>";
		$("#jobdetailsTable tbody").append(markup);
	}

	for(var i=allTableData.length;i<9;i++)
	{
		markup = "<tr>";
		markup = markup + "<td>&nbsp;</td>";   
		markup = markup + "<td></td>";   
		markup = markup + "<td></td>";   
		markup = markup + "<td></td>";   
		markup = markup + "<td></td>";   
		markup = markup + "<td></td>";   
		markup = markup + "<td></td>";   
		markup = markup + "<td></td>";   
		markup = markup + "<td></td>";   
		markup = markup + "<td></td>";
		markup = markup + "</tr>";
		$("#jobdetailsTable tbody").append(markup);
	}
	$("#approximate_amount_txt").val(totalValue.toFixed(2));	
}
function fnAddToTable()
{
	window._jcDirty = true; // user added an item row — form is now dirty
	var markup = "";
/* 	if($("#prod_mat_qty_div").length==0)
	{
		toastr.error('Nothing to Add...');
		return false;
	} */
	var machine_code_txt = "";
	if ($('#machine_code_txt').length) {
		machine_code_txt = $('#machine_code_txt :selected').val();
		if(machine_code_txt=="")
		{

		toastr.error('Select Machine Name !!!');
		return false;

		}
	}
	var product_code_txt = ""; 
	if ($('#product_code_txt').length) {
		product_code_txt = $('#product_code_txt').val(); 
	}
	var gst_percentage_txt="";
	if($('#gst_percentage_txt').length)
	{
		gst_percentage_txt=$('#gst_percentage_txt').val();
	}
	var product_name_txt = ""; 
	if ($('#search_product_txt').length) {
		product_name_txt = $('#search_product_txt').val(); 
	}
	var no_of_ups_txt=0;
	if ($('#no_of_ups_txt').length) {
		no_of_ups_txt=$('#no_of_ups_txt').val();
	}
	var first_copy_nos_txt = $('#1st_copy_nos_txt').val();
	
	var addl_copy_nos_txt = "";
	if ($('#addl_copy_nos_txt').length) {
		addl_copy_nos_txt = $('#addl_copy_nos_txt').val();
	}
	
	var first_copy_rate_txt = $('#1st_copy_rate_txt').val();	
	var first_copy_rate_discounted_txt = $('#1st_copy_rate_discounted_txt').val();	
	var addl_copy_rate_txt = "";
	var addl_copy_rate_discounted_txt = "";
	if ($('#addl_copy_rate_txt').length) {
		addl_copy_rate_txt = $('#addl_copy_rate_txt').val();
		addl_copy_rate_discounted_txt = $('#addl_copy_rate_discounted_txt').val();
	}
	
	var is_front_and_back_txt = 0;
	if ($('#is_front_and_back_txt').length) {
		if($('#is_front_and_back_txt').is(':checked')) is_front_and_back_txt=1;
	}
	var sheets = $('#sheetsTxt').val();

	var sheetCount ="";
	var total_qty_txt = $('#total_qty_txt').val();
	var value = $("#value_txt").val();
	if(isNaN(value)) value=0;
	var value_discountable = $("#value_discountable_txt").val();
	if(isNaN(value_discountable)) value_discountable=0;
	var prod_mat_qty_txt="";
	if($("#prod_mat_qty_div").length)
	{
		prod_mat_qty_txt=$("#prod_mat_qty_div").text();
	}
	/* else
	{
		toastr.error("Invalid Entry");
		return
	} */
	if(product_code_txt == null)
	{
		toastr.error('Select Stock Name !!!');
		return false;
	}

	if(parseFloat(value)<=0)
	{
		toastr.error('Zero Value Entries Not Allowed !!!');
		return false;
	}
	if(first_copy_nos_txt == "" || first_copy_nos_txt == 0)
	{
		toastr.error('Quantity is empty!!!');
		return false;
	}
	job_type_txt=$("#get_category_txt").val();
	hypen_pos= job_type_txt.indexOf("-");
	if(hypen_pos>-1)
	{
		job_type_txt= job_type_txt.slice(0,hypen_pos);
	}
	var tempArray= {}
	tempArray['sNo']=sNo;
	tempArray['machine_code_txt']=machine_code_txt;
	tempArray['product_code_txt']=product_code_txt;
	tempArray['gst_percentage_txt']=gst_percentage_txt;
	tempArray['job_type']=job_type_txt;
	tempArray['product_name_txt']=product_name_txt;
	tempArray['first_copy_nos_txt']=first_copy_nos_txt;
	tempArray['addl_copy_nos_txt']=addl_copy_nos_txt;
	tempArray['first_copy_rate_txt']=first_copy_rate_txt;
	tempArray['addl_copy_rate_txt']=addl_copy_rate_txt;
	tempArray['first_copy_rate_discounted_txt']=first_copy_rate_discounted_txt;
	tempArray['addl_copy_rate_discounted_txt']=addl_copy_rate_discounted_txt;
	
	tempArray['is_front_and_back_txt']=is_front_and_back_txt;
	tempArray['noOfUps']=no_of_ups_txt;
	tempArray['total_qty_txt']=total_qty_txt;
	tempArray['prod_mat_qty']=prod_mat_qty_txt;
	tempArray['value']=value;
	tempArray['value_discountable']=value_discountable;
	
	var arrCnt = 0;
	if(allTableData.length>=1)
		arrCnt=allTableData.length;
	allTableData[arrCnt]=tempArray;
	//allTableData.push(tempArray);
	render_jobdetailsTable_from_allTableData();
	
	sNo++;
	reset_newItemModal_ui();
	toastr.success("Added");
	
}

function saveJobCard()
{
	
	var customer_type_txt = $('input[name="customer_type_txt"]:checked').val();
	var customer_name_txt = $('#customer_name_txt').val();
	var credit_customer_code_txt = $('#credit_customer_code_txt').val();
	var customer_addr1_txt = $('#customer_addr1_txt').val();
	var customer_addr2_txt = $('#customer_addr2_txt').val();
	var customer_city_txt = $('#customer_city_txt').val();
	var customer_state_txt = $('#customer_state_txt :selected').text();
	var customer_state_code_txt = $('#customer_state_txt :selected').val();
	var customer_gst_no_txt = $('#customer_gst_no_txt').val();
	var customer_mobile_no_txt = $('#customer_mobile_no_txt').val();
	var approximate_amount_txt = $('#approximate_amount_txt').val(); 
	var is_adv_in_cash_txt = ($("#advance_cash_txt").val()>0)?1:0; // ($('#is_adv_in_cash_txt').is(':checked'))?1:0;
	var is_adv_in_gpay_txt = ($("#advance_gpay_txt").val()>0)?1:0; // ($('#is_adv_in_gpay_txt').is(':checked'))?1:0;
	var advance_cash_txt = $("#advance_cash_txt").val();
	var advance_gpay_txt = $("#advance_gpay_txt").val();
	var advance_amount_txt = $("#advance_amount_txt").val();  
	var remarks_txt = $("#remarks_txt").val();  
	var delivery_txt = $("#delivery_txt").val();  
	var theOperation_txt = $("#theOperation").text();  
	var jobcard_no_txt = $("#jobcard_no_txt").text(); 
	var jobcard_date_txt=$("#jobcard_date_txt").text();

	if(customer_type_txt=="Credit")
	{
		credit_customer_code_txt=credit_customer_code_txt.trim();
		if(credit_customer_code_txt.length==0)
		{
			alert("Reselect Credit Customer");
			return;
		}
	}
	if(customer_type_txt=="General")
	{
		if(customer_name_txt.trim()=="")
		{
			alert("General Customer should have a Name");
			$("#customer_name_txt").focus();
			return;
		}
		if(customer_mobile_no_txt.trim()=="")
		{
			alert("General Customer should have a Mobile No");
			$("#customer_mobile_no_txt").focus();
			return;
		}
	}
	if(parseFloat(approximate_amount_txt)<=0)
	{
		alert("Nothing to Save");
		return;
	}

	// Block WalkIn save if amount >= ₹1000
	if(customer_type_txt=="WalkIn" && parseFloat(approximate_amount_txt) >= 1000)
	{
		toastr.error("WalkIn customers cannot have orders ≥ ₹1000. Please switch to General Customer and enter Name & Mobile No.");
		$('#generalCustomerOpt').prop('checked', true).trigger('click');
		setTimeout(function() { $("#customer_name_txt").focus(); }, 150);
		return;
	}
	else
	{
		// Block save only if a QR was generated but payment not yet confirmed
		var _advOnlineAmt = parseFloat($("#advance_gpay_txt").val()) || 0;
		if (_advOnlineAmt > 0 && _canaraCurrentTxnId !== null && _canaraPaymentStatus !== 'SUCCESS') {
			alert("UPI payment of ₹" + _advOnlineAmt.toFixed(2) + " is not confirmed yet. Please complete the payment before saving.");
			return;
		}

		advance_amount_txt=parseFloat(advance_amount_txt).toFixed(2);
		
		if (isNaN(advance_amount_txt)) advance_amount_txt=0;
		// Skip 75% check when advance was pre-collected via the Advance Payment menu
		// (the total was unknown at the time of collection, so restriction cannot apply)
		if (!window._advanceContext) {
			max_advance = parseFloat((parseFloat(approximate_amount_txt) * 0.75).toFixed(2));
			if (isNaN(max_advance)) max_advance = 0;
			if (advance_amount_txt > max_advance && customer_type_txt === "General")
			{
				toastr.error("Advance amount should not exceed 75% of the total (max: " + max_advance + ")");
				return;
			}
		}
		
	}
	$("#saveBtn").prop('disabled', true);
	$.ajax({
		type: "POST",
		url: "api/save_jobcard.php",
		data:{
			  "theOperation_txt":theOperation_txt,
			  "jobcard_no_txt":jobcard_no_txt,
			  "jobcard_date_txt":jobcard_date_txt,
			  "customer_type_txt":customer_type_txt,
			  "customer_name_txt":customer_name_txt,
			  "credit_customer_code_txt":credit_customer_code_txt,
			  "customer_addr1_txt":customer_addr1_txt,
			  "customer_addr2_txt":customer_addr2_txt,
			  "customer_city_txt":customer_city_txt,
			  "customer_state_code_txt":customer_state_code_txt,
			  "customer_state_txt":customer_state_txt,
			  "customer_gst_no_txt":customer_gst_no_txt,
			  "customer_mobile_no_txt":customer_mobile_no_txt,
			  "approximate_amount_txt":approximate_amount_txt,
			  "is_adv_in_cash_txt":is_adv_in_cash_txt,
			  "is_adv_in_gpay_txt":is_adv_in_gpay_txt,
			  "advance_cash_txt":advance_cash_txt,
			  "advance_gpay_txt":advance_gpay_txt,
			  "advance_amount_txt":advance_amount_txt,
			  "remarks_txt":remarks_txt,
			  "delivery_txt":delivery_txt,
			  "allTableData":JSON.stringify(allTableData),
				  "linked_advance_id":(window._advanceContext ? window._advanceContext.advance_id : 0)},
		success: function (response) {
			if(response.indexOf("Success")>-1)
			{
				$("#printBtn").removeAttr('disabled');
				jobcard_no_txt=response.substr(response.indexOf(":")+1);
				toastr.success('Job Card No '+jobcard_no_txt);
				$("#jobcard_no_txt").text(jobcard_no_txt);
				_jcSaved = true;
				window._jcDirty = false; // saved — no longer dirty
				$("#closeBtn").show().prop('disabled', false);
				// Link QR transaction to this JC if QR was generated before save
				if (_canaraCurrentTxnId) {
					$.post('api/update_qr_jobcard.php', {
						ext_transaction_id: _canaraCurrentTxnId,
						jobcard_no: jobcard_no_txt
					});
				}
				// Clear advance context so future saves don't re-link
				var _advanceWasGpay = (window._advanceContext && window._advanceContext.pay_mode === 'GPay');
				if (window._advanceContext) window._advanceContext = null;
				reset_newItemModal_ui();
				$("#saveBtn").prop('disabled', true);
				//$("#is_adv_in_cash_txt").prop('disabled', true);
				//$("#is_adv_in_gpay_txt").prop('disabled', true);
				$("#add_new_plus_btn").prop('disabled', true);
				$("#advance_cash_txt").prop('disabled', true);
				$("#advance_gpay_txt").prop('disabled', true);
				$("#remarks_txt").prop('disabled', true);
				$("#delivery_txt").show();
				$("#delivery_txt").prop('disabled', true);
				$("#delivery_option_div").hide();
				$("#printBtn").focus();
				if(parseFloat(advance_amount_txt) == parseFloat(approximate_amount_txt) && customer_type_txt=="WalkIn")
				{
					if($("#sq_no_txt").text()=="")
					{
					var userChoice = confirm("Are you sure you want to convert this into Sales Quote Directly?");
					if(userChoice)
					{
						Convert_and_Close_JCs(true);
					}
					}
				}
			}
			else
			{
				toastr.error(response);
				$("#saveBtn").removeAttr('disabled');
			}
			
		} 
	});	
}
function load_job_cards_from_db(pVerb)
{
	if(pVerb!="FOR_EDIT")
		block_editing=1;
	else
		block_editing=0;
	$('#delivery_option_div').hide();
	$('#delivery_txt').show();
	
	$.ajax({
		type: "POST",
		url: "api/load_job_cards_from_db.php",
		data:{jcnos:$("#jobcard_no_txt").text()},
		success: function (response) {
			
			res_ary=response.split("~>");
			jc_data=JSON.parse(res_ary[0]);
			allTableData=JSON.parse(res_ary[1]);

			$("#jobcard_date_txt").text(jc_data["jobcard_date_txt"]);
			$("#sq_no_txt").text(jc_data["job_card_quotation_no"]);
			$("#advance_gpay_txt").val(jc_data["advance_gpay_txt"]);

			// Restore PAID status only in edit mode — not in job closing/SQ screen
			if (jc_data["advance_gpay_paid"] && pVerb === "FOR_EDIT") {
				_canaraPaymentStatus = 'SUCCESS';
				_jcSaved = true;
				$("#qrcode").html('<div style="background:#27ae60;color:#fff;font-size:11px;font-weight:bold;border-radius:6px;padding:8px 4px;text-align:center;line-height:1.6;">&#10003;<br>PAID</div>');
				$("#qr_status_waiting").hide();
				$("#qr_status_failed").hide();
				$("#qr_status_success").show();
				$("#generateQrAdvBtn").hide();
				$("#qrCodeSection").show();
			} else if (pVerb === "JC_2_SQ") {
				// Reset payment state so balance QR can be generated fresh
				_canaraPaymentStatus = null;
				_canaraCurrentTxnId  = null;
				$("#qrcode").html("");
				$("#qr_status_waiting").hide();
				$("#qr_status_success").hide();
				$("#qr_status_failed").hide();
			} else if (pVerb === "FOR_EDIT") {
				// Advance not yet paid — reset stale QR state from any previously viewed JC
				_canaraPaymentStatus = null;
				_canaraCurrentTxnId  = null;
				_canaraStopPolling();
				$("#qrcode").html("");
				$("#qr_status_waiting").hide();
				$("#qr_status_success").hide();
				$("#qr_status_failed").hide();
				if ((parseFloat(jc_data["advance_gpay_txt"]) || 0) > 0) {
					$("#generateQrAdvBtn").show().prop('disabled', false).removeAttr('disabled');
					$("#qrCodeSection").show();
				}
			}
			$("#advance_cash_txt").val(jc_data["advance_cash_txt"]);
			$("#advance_amount_txt").val(jc_data["advance_amount_txt"]);
			$("#max_value_discountable").text(jc_data["max_value_discountable"]);
			$("#approximate_amount_txt").val(jc_data["approximate_amount_txt"]);
			$("#remarks_txt").val(jc_data["remarks_txt"]);
			$("#delivery_txt").val(jc_data["delivery_txt"]);

			if(jc_data["customer_type_txt"]=="WalkIn") {
				$("#walkCustomerOpt").prop("checked", true);
				$("#customer_div").hide();
			} else if(jc_data["customer_type_txt"]=="General"){
				$("#generalCustomerOpt").prop("checked", true);
				$("#gst_extra_chk").removeAttr('disabled');
				$("#customer_div").show();
				$("#customer_name_txt").val(jc_data["customer_name_txt"]);
				$("#customer_addr1_txt").val(jc_data["customer_addr1_txt"]);
				$("#customer_addr2_txt").val(jc_data["customer_addr2_txt"]);
				$("#customer_city_txt").val(jc_data["customer_city_txt"]);
				$("#customer_state_txt").val(jc_data["customer_state_code_txt"]);
				$("#customer_gst_no_txt").val(jc_data["customer_gst_no_txt"]);
				$("#customer_mobile_no_txt").val(jc_data["customer_mobile_no_txt"]);
			} else if(jc_data["customer_type_txt"]=="Credit") {
				$("#creditCustomerOpt").prop("checked", true);
				$("#customer_div").show();
				$("#credit_customer_code_txt").val(jc_data["customer_code_txt"]);
				$("#customer_name_txt").val(jc_data["customer_name_txt"]);
				$("#customer_addr1_txt").val(jc_data["customer_addr1_txt"]);
				$("#customer_addr2_txt").val(jc_data["customer_addr2_txt"]);
				$("#customer_city_txt").val(jc_data["customer_city_txt"]);
				$("#customer_state_txt").val(jc_data["customer_state_code_txt"]);
				$("#customer_gst_no_txt").val(jc_data["customer_gst_no_txt"]);
				$("#customer_mobile_no_txt").val(jc_data["customer_mobile_no_txt"]);
			}

			render_jobdetailsTable_from_allTableData();
			disableDiv("jobCardCustomerDiv");

			// Enable discount and balance fields first
			if(jc_data["customer_type_txt"]!="Credit")
			{
				$("#balance_cash_txt").prop("disabled", false);
				$("#balance_gpay_txt").prop("disabled", false);
				$("#generateQrBtn").prop("disabled", false);
			}
			$("#goBtn").prop("disabled", false);

			$("#printBtn").hide();
			$("#saveBtn").hide();
			$("#newBtn").hide();
			$("#closeBtn").show();
			$("#goBtn").show();
			$("#parkQrBtn").hide(); // shown only when GPay amount > 0 (see balance_gpay_txt handler)
			$("#add_new_plus_btn").hide();
			$("#add_new_plus_btn").prop('disabled', true);
			$("#itemDiscountBtn").hide(); // hide by default, show selectively below

			// ── Apply item discount AFTER all other field settings ──────────
			// This must come AFTER disabled/readonly resets so it is not wiped
			var savedItemDiscount = parseFloat(jc_data["total_item_discount"]) || 0;
			if (savedItemDiscount > 0) {
				$("#discount_txt").val(savedItemDiscount.toFixed(2));
				$("#discount_txt").prop("disabled", false);
				$("#discount_txt").prop("readonly", true);
				$("#discount_txt").css("background-color", "#d4edda")
					.attr("title", "Discount is set item-wise. Use ITEM DISCOUNTS button to change.");
			} else {
				$("#discount_txt").val("");
				$("#discount_txt").prop("disabled", false);
				$("#discount_txt").prop("readonly", true);
				$("#discount_txt").css("background-color", "")
					.attr("title", "Discount is set via ITEM DISCOUNTS button.");
			}
			// ────────────────────────────────────────────────────────────────

			calc_bal_should_be();
			$('#balance_cash_txt').val('');
			$('#balance_gpay_txt').val('');
			$('#generateQrBtn').hide();
			$("#balance_div").show();

			if(pVerb=="JC_2_SQ")
			{
				$("#itemDiscountBtn").show().prop('disabled', false);
				$("#qrCodeSection").show();
				$("#balance_cash_txt").focus();
			}

			if(pVerb=="JUST_SHOW")
			{
				$("#discount_txt").prop('disabled', true);
				$("#discount_txt").prop("readonly", false); // disabled takes over in JUST_SHOW
				$("#balance_cash_txt").prop('disabled', true);
				$("#balance_gpay_txt").prop('disabled', true);
				$("#gst_extra_chk").prop('disabled', true);
				$("#goBtn").hide();
				$("#parkQrBtn").hide().prop('disabled', false).text('⏳ Hold for QR Payment');
				$("#closeBtn").hide();
				$("#itemDiscountBtn").hide();
				$("#qrCodeSection").hide();
				$("#balance_div").show();

				if(jc_data["gst_extra_chk"])
				{
					$("#gst_extra_chk").prop('disabled', true);
					if(jc_data["gst_extra_chk"]=="1")
					{
						$("#gst_extra_chk").prop('checked', true);
						$("#gst_charges_span").text(jc_data["gst_tax_amount"]);	
						response=jc_data["gst_tax_amount"];		
						balance = parseFloat($("#balance_should_be").text());
						gst_extra = parseFloat(response);
						final_balance = balance + gst_extra;
						final_balance = final_balance.toFixed(2);
						$("#gst_xtra").text(" + " + response + " = " + final_balance);					
					}
				}
			}
			
			if(pVerb=="FOR_EDIT")
			{
				// Check if this JC has a pending QR payment — if yes, show
				// only the Online→Cash switch panel; block all other editing
				if(jc_data["is_qr_pending"])
				{
					// Hide all normal edit controls
					$("#saveBtn").hide();
					$("#add_new_plus_btn").hide().prop('disabled', true);
					$("#balance_div").hide();
					$("#goBtn").hide();
					$("#parkQrBtn").hide();
					// Show the switch panel
					$("input[name='qr_switch_action'][value='keep']").prop('checked', true);
					$("#qrCashReasonDiv").hide();
					$("#qrCashReasonTxt").val('');
					$("#qrToCashPanel").show();
				}
				else
				{
					// Normal edit flow — no pending QR
					$("#add_new_plus_btn").show();
					$("#saveBtn").show();
					$("#balance_div").hide();
					$("#goBtn").hide();
					$("#parkQrBtn").hide().prop('disabled', false).text('⏳ Hold for QR Payment');
					$("#saveBtn").removeAttr('disabled');
					$("#advance_gpay_txt").removeAttr('disabled');
					$("#advance_cash_txt").removeAttr('disabled');
					if(jc_data["customer_type_txt"]=="Credit") {
						$("#credit_customer_div").show();
						$("#search-box").removeAttr('disabled');
					}
				}
			}
		}
	});
}
function load_sq_details_from_db()
{
	$("#sq_no_container").show();
	$("#JC_SQ_Title").text("Sales Quotation");
	$("#JC_SQ_Title").attr("style","background-color:#3c2201");
	$("#jobCardCustomerDiv").attr("style","background-color:#3c2201");
	$("#itemDiscountBtn").hide();
	$("#qrCodeSection").hide();
	$.ajax({
		type: "POST",
		url: "api/load_sq_from_db.php",
		data:{sqno:$("#sq_no_txt").text()},
		success: function (response) {
			
			ary=response.split(",");
			
			if(ary.length>0)
			{
			$("#discount_txt").val(ary[0]);
			$("#balance_gpay_txt").val(ary[1]);
			$("#balance_cash_txt").val(ary[2]);
			$("#balance_amount_txt").val(ary[3]);
			if(ary.length>4)
				$("#sq_dt_txt").text(ary[4]);
			}
		}
	});			
}
function set_si_ui()
{
	$("#si_no_containter").show();
	$("#JC_SQ_Title").text("Sales Invoice");
	$("#JC_SQ_Title").attr("style","background-color:#3b68cf");
	$("#jobCardCustomerDiv").attr("style","background-color:#3b68cf");	
}
function calc_bal_should_be()
{

	discount_txt= $("#discount_txt").val();
	approximate_amount_txt= $("#approximate_amount_txt").val();
	advance_amount_txt= $("#advance_amount_txt").val();
	/* console.log(advance_amount_txt); */
	
	if(discount_txt=="") discount_txt=0;
	if(approximate_amount_txt=="") approximate_amount_txt=0;	
	if(advance_amount_txt=="") advance_amount_txt=0;	

	
	if (isNaN(approximate_amount_txt)) approximate_amount_txt=0;
	if (isNaN(advance_amount_txt)) advance_amount_txt=0;
	if (isNaN(discount_txt)) discount_txt=0;
	/* console.log(advance_amount_txt); */
	approximate_amount_txt=parseFloat(approximate_amount_txt);
	advance_amount_txt=parseFloat(advance_amount_txt);
	discount_txt=parseFloat(discount_txt);
	/* console.log(approximate_amount_txt);
	console.log(discount_txt);
	console.log(advance_amount_txt); */
	//balance_should_be=approximate_amount_txt-advance_amount_txt-discount_txt;
	/* console.log(balance_should_be); */
	bal_cash= $("#balance_cash_txt").val();
	bal_gpay= $("#balance_gpay_txt").val();

	if(bal_cash=="") bal_cash=0;
	if(bal_gpay=="") bal_gpay=0;
	bal_cash=parseFloat(bal_cash);
	bal_gpay=parseFloat(bal_gpay);

	if (isNaN(bal_gpay)) bal_gpay=0;
	if (isNaN(bal_cash)) bal_cash=0;
	bal_total =bal_gpay+bal_cash;
	bal_total=bal_total.toFixed(2);	
	
	balance_should_be=approximate_amount_txt-advance_amount_txt-discount_txt-bal_total;
	$("#balance_should_be").text(balance_should_be.toFixed(2));
}
function calc_bal_total()
{
	bal_cash= $("#balance_cash_txt").val();
	bal_gpay= $("#balance_gpay_txt").val();

	if(bal_cash=="") bal_cash=0;
	if(bal_gpay=="") bal_gpay=0;
	bal_cash=parseFloat(bal_cash);
	bal_gpay=parseFloat(bal_gpay);

	if (isNaN(bal_gpay)) bal_gpay=0;
	if (isNaN(bal_cash)) bal_cash=0;
	bal_total =bal_gpay+bal_cash;
	bal_total=bal_total.toFixed(2);
	if (isNaN(bal_total)) bal_total=0;	
	$("#balance_amount_txt").val(bal_total);
	calc_bal_should_be();
}
function calc_adv_total()
{
	adv_cash= $("#advance_cash_txt").val();
	adv_gpay= $("#advance_gpay_txt").val();
	if(adv_cash=="") adv_cash=0;
	if(adv_gpay=="") adv_gpay=0;
	adv_cash=parseFloat(adv_cash);
	adv_gpay=parseFloat(adv_gpay);
	if (isNaN(adv_gpay)) adv_gpay=0;
	if (isNaN(adv_cash)) adv_cash=0;
	adv_total =adv_gpay+adv_cash;
	adv_total=adv_total.toFixed(2);
	if (isNaN(adv_total)) adv_total=0;
	$("#advance_amount_txt").val(adv_total);
	// Show 75% warning only when advance is entered directly (not pre-filled from Advance Payment menu)
	if (!window._advanceContext) {
		var max_advance_75 = parseFloat((parseFloat($('#approximate_amount_txt').val()) * 0.75).toFixed(2));
		if (isNaN(max_advance_75)) max_advance_75 = 0;
		var customer_type_txt = $('input[name="customer_type_txt"]:checked').val();
		if (parseFloat(adv_total) > max_advance_75 && customer_type_txt === "General") {
			$("#advance_warning").text("Should not exceed 75% of total (max: " + max_advance_75 + ")");
		} else {
			$("#advance_warning").text("");
		}
	} else {
		$("#advance_warning").text("");
	}

}
$("#advance_cash_txt").on("keyup", function(e) {
	e.stopPropagation();
calc_adv_total();
});
$("#advance_cash_txt").on("change", function(e) {
e.stopPropagation();
calc_adv_total();
});
$("#advance_gpay_txt").on("keyup", function(e) {
	e.stopPropagation();
	calc_adv_total();
	clearTimeout(_qrDebounceTimer);
	var amt = parseFloat($(this).val()) || 0;
	if (amt > 0 && _canaraPaymentStatus !== 'SUCCESS') {
		$("#generateQrAdvBtn").show().prop('disabled', false).removeAttr('disabled');
	} else {
		$("#generateQrAdvBtn").hide();
		if (amt <= 0) { $("#qrcode").html(""); _canaraStopPolling(); $("#closeBtn").prop("disabled", false); }
	}
});
$("#advance_gpay_txt").on("change", function(e) {
	e.stopPropagation();
	calc_adv_total();
	var amt = parseFloat($(this).val()) || 0;
	if (amt > 0 && _canaraPaymentStatus !== 'SUCCESS') { $("#generateQrAdvBtn").show().prop('disabled', false).removeAttr('disabled'); }
	else { $("#generateQrAdvBtn").hide(); if (amt <= 0) { $("#qrcode").html(""); _canaraStopPolling(); $("#closeBtn").prop("disabled", false); } }
});
$("#discount_txt").on("keyup", function(e) {
	calc_bal_total();
});
$("#discount_txt").on("change", function(e) {
	e.stopPropagation();
	calc_bal_total();
});

$("#balance_cash_txt").on("keyup change", function(e) {
	e.stopPropagation();
	var enteredCash = parseFloat($(this).val()) || 0;
	var maxAllowed  = Math.max(0,
		(parseFloat($("#approximate_amount_txt").val()) || 0)
		- (parseFloat($("#advance_amount_txt").val())    || 0)
		- (parseFloat($("#discount_txt").val())          || 0)
		- (parseFloat($("#balance_gpay_txt").val())      || 0)
	);
	if (enteredCash > maxAllowed + 0.009) {
		$(this).val(maxAllowed.toFixed(2));
		toastr.warning("Cash amount cannot exceed the remaining balance of ₹" + maxAllowed.toFixed(2));
	}
	calc_bal_total();
});

$("#balance_gpay_txt").on("keyup change", function(e) {
	e.stopPropagation();
	var enteredGpay = parseFloat($(this).val()) || 0;
	var maxAllowed  = Math.max(0,
		(parseFloat($("#approximate_amount_txt").val()) || 0)
		- (parseFloat($("#advance_amount_txt").val())    || 0)
		- (parseFloat($("#discount_txt").val())          || 0)
		- (parseFloat($("#balance_cash_txt").val())      || 0)
	);
	if (enteredGpay > maxAllowed + 0.009) {
		$(this).val(maxAllowed.toFixed(2));
		toastr.warning("GPay amount cannot exceed the remaining balance of ₹" + maxAllowed.toFixed(2));
		enteredGpay = maxAllowed;
	}
	calc_bal_total();
	// Show Generate QR / Hold buttons only for non-credit customers
	if ($("#goBtn").is(":visible") && !$("#creditCustomerOpt").prop("checked")) {
		var rawAmt = parseFloat($("#balance_gpay_txt").val()) || 0; // read actual field after any cap
		if (rawAmt > 0) {
			$("#generateQrBtn").show();
			// parkQrBtn shown only after QR is generated — not on typing
		} else {
			$("#generateQrBtn").hide();
			$("#parkQrBtn").hide();
			// Only clear QR if no active transaction is being polled (don't kill a live QR)
			if (!_canaraCurrentTxnId) {
				$("#qrcode").html("");
				_canaraStopPolling();
			}
		}
	}
});

function fnGenerateQrManual() {
	var amt        = parseFloat($("#balance_gpay_txt").val()) || 0;
	if (amt <= 0) { toastr.error("Enter a valid Online / UPI amount."); return; }
	var maxAllowed = Math.max(0,
		(parseFloat($("#approximate_amount_txt").val()) || 0)
		- (parseFloat($("#advance_amount_txt").val())    || 0)
		- (parseFloat($("#discount_txt").val())          || 0)
		- (parseFloat($("#balance_cash_txt").val())      || 0)
	);
	if (amt > maxAllowed + 0.009) {
		toastr.error("GPay amount ₹" + amt.toFixed(2) + " exceeds remaining balance ₹" + maxAllowed.toFixed(2) + ". Cannot generate QR.");
		$("#balance_gpay_txt").val(maxAllowed.toFixed(2)).focus();
		return;
	}
	generate_qrcode('SQ');
}

var _qrDebounceTimer = null;
function printJobCard()
{
	var jobCardNo=$('#jobcard_no_txt').text();
	if(jobCardNo == "")
	{
		toastr.error("Job Card No is empty.");
	}
	window.open("jc_print_out.php?jc="+jobCardNo,"_blank");
}
function printSQ()
{
	var sqno=$('#sq_no_txt').text();
	if(sqno == "")
	{
		toastr.error("SQ No is empty.");
	}
	window.open("bill_receipt.php?sq="+sqno,"_blank");
}

// ── Canara Bank QR payment integration ──────────────────────────────────────
var _canaraCurrentTxnId  = null;
var _canaraPollingTimer  = null;
var _canaraRetryTimer    = null; // setTimeout handle for auto-retry after FAILED
var _canaraPaymentStatus = null; // 'SUCCESS', 'FAILED', or null
var _canaraFailedCount   = 0;   // consecutive FAILED responses before showing error UI
var _jcSaved             = false; // true once job card has been saved — closeBtn stays enabled
var _canaraLastPType     = 'JC'; // last pType passed to generate_qrcode — used by retryQrPayment

function retryQrPayment() {
	generate_qrcode(_canaraLastPType);
}

function _canaraStopPolling() {
	if (_canaraPollingTimer) {
		clearInterval(_canaraPollingTimer);
		_canaraPollingTimer = null;
	}
	if (_canaraRetryTimer) {
		clearTimeout(_canaraRetryTimer);
		_canaraRetryTimer = null;
	}
}

function _canaraStartPolling(ext_id) {
	_canaraStopPolling();
	_canaraFailedCount = 0; // reset on each fresh poll session
	_canaraPollingTimer = setInterval(function () {
		$.ajax({
			url     : 'api/canara_qr_status.php',
			type    : 'GET',
			data    : { ext_id: ext_id },
			dataType: 'json',
			cache   : false,   // prevent browser caching stale PENDING responses
			success : function (res) {
			if (res.status === 'SUCCESS') {
				if (_canaraPaymentStatus === 'SUCCESS') return;
				_canaraStopPolling();
				_canaraFailedCount   = 0;
				_canaraPaymentStatus = 'SUCCESS';
				$("#qr_status_waiting").hide();
				$("#qr_status_failed").hide();
				$("#qr_status_success").show();
				$("#qrcode").html('<div style="background:#27ae60;color:#fff;font-size:11px;font-weight:bold;border-radius:6px;padding:8px 4px;text-align:center;line-height:1.6;">&#10003;<br>PAID</div>');
				$("#itemDiscountBtn").hide();
				$("#generateQrAdvBtn").hide();
				toastr.success("Payment received via UPI!");
				// If this JC was parked, the status call already converted it — refresh lists
				if (res.converted_sq) {
					toastr.success('JC auto-converted to SQ ' + res.converted_sq);
					$('#sq_no_txt').text(res.converted_sq);
					// Hide all action buttons — show only CLOSE
					$('#saveBtn, #itemDiscountBtn, #printBtn, #newBtn, #goBtn, #parkQrBtn, #printSQBtn, #generateQrAdvBtn, #generateQrBtn, #nextBtn, #add_new_plus_btn').hide();
					$('#closeBtn').show().prop('disabled', false);
					loadJobCardList();
					loadSQList();
				}
			} else if (res.status === 'FAILED') {
				// Bank sometimes returns FAILED as an intermediate state before finalising.
				// Only show the error UI after 5 consecutive FAILED responses to avoid
				// a false alarm when the payment is still being processed by the bank.
				_canaraFailedCount++;
				if (_canaraFailedCount >= 5 && _canaraPaymentStatus !== 'FAILED') {
					_canaraPaymentStatus = 'FAILED';
					$("#qr_status_waiting").hide();
					$("#qr_status_success").hide();
					$("#qr_status_failed").show();
					toastr.error("UPI payment failed. If you have paid, please wait. Otherwise click Try Again.");
				}
				// Do NOT stop polling — keep checking so a successful retry is detected
			}
			} // end success
		}); // end $.ajax
	}, 3000); // poll every 3 s

	// Auto-stop after 5 minutes (bank QR validity)
	setTimeout(function () {
		if (_canaraPollingTimer) {
			_canaraStopPolling();
			if ($("#qr_status_success").is(":hidden")) {
				$("#qr_status_waiting").hide();
				$("#qr_status_failed").show();
			}
		}
	}, 300000);
}

function _canaraRenderQR(qr_string) {
	// Small inline QR (120×120)
	$("#qrcode").html("");
	new QRCode(document.getElementById("qrcode"), {
		text: qr_string,
		width: 120, height: 120,
		colorDark: "#000000", colorLight: "#ffffff",
		correctLevel: QRCode.CorrectLevel.M
	});
	// Large modal QR (200×200)
	$("#qrcodeNew").html("");
	new QRCode(document.getElementById("qrcodeNew"), {
		text: qr_string,
		width: 200, height: 200,
		colorDark: "#000000", colorLight: "#ffffff",
		correctLevel: QRCode.CorrectLevel.M
	});
	$("#username-slab").text("PRINTZY");
	$("#qr_jc_user_name").text("PRINTZY");
}

function fnParkJCForQRPayment() {
	var jcNo    = $.trim($("#jobcard_no_txt").text());
	if (!jcNo) { toastr.error("No Job Card loaded."); return; }

	var approx   = parseFloat($("#approximate_amount_txt").val()) || 0;
	var gstAmt   = parseFloat($("#gst_charges_span").text()) || 0;
	approx += gstAmt;
	var discount = parseFloat($("#discount_txt").val()) || 0;
	var balGpay  = parseFloat($("#balance_gpay_txt").val()) || 0;
	var balCash  = parseFloat($("#balance_cash_txt").val()) || 0;
	var balAmt   = parseFloat($("#balance_amount_txt").val()) || 0;
	var isGstExtra   = $('#gst_extra_chk').is(':checked') ? 1 : 0;
	var isBalInCash  = (balCash > 0) ? 1 : 0;
	var isBalInGpay  = (balGpay > 0) ? 1 : 0;

	$.ajax({
		type    : 'POST',
		url     : 'api/save_pending_qr_closure.php',
		dataType: 'json',
		data: {
			jcnos:                  jcNo,
			approximate_amount_txt: approx,
			balance_gpay_txt:       balGpay,
			balance_cash_txt:       balCash,
			balance_amount_txt:     balAmt,
			discount_txt:           discount,
			is_bal_in_cash_txt:     isBalInCash,
			is_bal_in_gpay_txt:     isBalInGpay,
			is_gst_extra_txt:       isGstExtra,
			gst_tax_amount_txt:     gstAmt,
			ext_transaction_id:     _canaraCurrentTxnId || ''
		},
		success: function(res) {
			if (res.success) {
				toastr.success("Job Card placed on hold. It will automatically convert to a Sales Quote once the QR payment is received.");
				$('#goBtn').hide();
				$('#parkQrBtn').text('⏳ On Hold – Awaiting QR Payment').prop('disabled', true);
				$('#closeBtn').show().prop('disabled', false);
			} else {
				toastr.error("Failed to park Job Card: " + (res.error || "Unknown error"));
			}
		},
		error: function() { toastr.error("Network error while parking Job Card."); }
	});
}

function generate_qrcode(pType) {
	// Block re-generation if payment already confirmed
	if (_canaraPaymentStatus === 'SUCCESS') {
		console.log('[QR] Blocked: _canaraPaymentStatus is SUCCESS');
		return;
	}
	_canaraLastPType = pType; // remember for retryQrPayment()

	var amount;
	if (pType === 'JC') {
		amount = parseFloat($("#advance_gpay_txt").val()) || 0;
	} else {
		amount = parseFloat($("#balance_gpay_txt").val()) || 0;
	}

	console.log('[QR] generate_qrcode called. pType=' + pType + ' amount=' + amount + ' status=' + _canaraPaymentStatus);

	// Clear previous state (also cancels any pending retry timer)
	_canaraStopPolling();
	_canaraCurrentTxnId = null;
	_canaraPaymentStatus = null;
	$("#parkQrBtn").hide();   // hide until QR is successfully rendered
	$("#qrcode").html("");
	$("#qrcodeNew").html("");
	$("#username-slab").text("");
	$("#qr_status_waiting").hide();
	$("#qr_status_success").hide();
	$("#qr_status_failed").hide();

	if (amount <= 0) {
		console.log('[QR] Blocked: amount is 0 or less');
		return;
	}

	var jobcard_no = $('#jobcard_no_txt').text();

	// Show loading placeholder
	$("#qrcode").html('<div style="font-size:9px;text-align:center;color:#888;padding:5px;">Loading...</div>');

	// Call Canara Bank QR generation (Windows Server API)
	$.ajax({
		url     : 'api/canara_qr_generate.php',
		type    : 'POST',
		timeout : 150000,  // 150 s — bank API can be slow; must exceed PHP+FastAPI chain
		data    : { amount: amount.toFixed(2), jobcard_no: jobcard_no, payment_source: pType },
		dataType: 'json',
		success: function (res) {
			if (!res.success) {
				if (res.already_paid) {
					// JC already paid — show PAID badge and mark status as SUCCESS
					_canaraPaymentStatus = 'SUCCESS';
					_jcSaved = true; // treat as saved so closeBtn stays enabled
					$("#qrcode").html('<div style="background:#27ae60;color:#fff;font-size:11px;font-weight:bold;border-radius:6px;padding:8px 4px;text-align:center;line-height:1.6;">&#10003;<br>PAID</div>');
					$("#qr_status_waiting").hide();
					$("#qr_status_failed").hide();
					$("#qr_status_success").show();
					$("#closeBtn").prop("disabled", false).show();
					toastr.info(res.error); // "Job Card X payment already completed"
				} else {
					toastr.error("QR Error: " + (res.error || "Unknown"));
					$("#qrcode").html("");
					$("#closeBtn").prop("disabled", false);
				}
				return;
			}
			_canaraCurrentTxnId = res.transaction_id;

			_canaraRenderQR(res.qr_string);
			// Show "Hold for QR Payment" only for balance QR (pType=SQ), not advance QR
			if (pType === 'SQ') {
				$("#parkQrBtn").show().prop("disabled", false);
			}
			if (!_jcSaved) $("#closeBtn").prop("disabled", true);

			$("#qr_jc_amount").text(amount.toFixed(2) + "/-");
			$("#qr_jc_no").text(jobcard_no);
			$("#qr_jc_date").text($('#jobcard_date_txt').text());
			$("#qr_status_waiting").show();

			_canaraStartPolling(res.transaction_id);

			if (res.warning) toastr.warning(res.warning);
		},
		error: function () {
			$("#qrcode").html("");
			toastr.error("Failed to generate Canara Bank QR. Check network.");
		}
	});
}
function autocomplete(inp,code_inp, arr,pcode) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/

  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
	  
      closeAllLists();
	  
      //if (!val) { return false;}
	  
      currentFocus = -1;
	  val=val.trim();
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      a.setAttribute("style", "background-color:white;color:black;position:absolute;z-index:1001; min-width:30%;min-height:300px;");
	  
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        match_pos=arr[i].toUpperCase().indexOf(val.toUpperCase());
		
		if(val.length==0) match_pos=1000;
        if (match_pos>-1) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
		  if(val.length>0)
		  {
          b.innerHTML = arr[i].substr(0,match_pos);
          b.innerHTML += "<strong>" + arr[i].substr(match_pos, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(match_pos+val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'  code_value='" + pcode[i] + "' >";
		  }
		  else
		  {
			 b.innerHTML = arr[i];
			 b.innerHTML += "<input type='hidden' value='" + arr[i] + "'  code_value='" + pcode[i] + "' >";
		  }
          /*execute a function when someone clicks on the item value (DIV element):*/
		  
          b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
			  code_inp.value = this.getElementsByTagName("input")[0].getAttribute('code_value'); 	
				if(code_inp.value !="")
				{
					
					$.ajax({
							url: 'api/get_customer_details.php?id='+code_inp.value,
							method: 'GET', 
							success: function (response) {
								 if(response != "")
								 {
									var json = response[0];
									$("#credit_customer_code_txt").val(json["customer_code"]);
									$("#customer_name_txt").val(json["customer_name"]);
									$("#customer_addr1_txt").val(json["customer_addr1"]);
									$("#customer_addr2_txt").val(json["customer_addr2"]);
									$("#customer_city_txt").val(json["customer_city"]);
									$("#customer_state_txt").val(json["customer_state_code"]);
									$("#customer_gst_no_txt").val(json["gst_no"]);
									$("#customer_mobile_no_txt").val(json["mobile_no"]);
									$("#customer_mobile_no_txt").val(json["mobile_no"]);
			 					    $("#nextBtn").focus();
		  
								 }
							}
						});
				}			  
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
			  
              closeAllLists();
			  
          });
          a.appendChild(b);
        }
      }
  });
  inp.addEventListener("focus",function(e){
	  e.stopPropagation();
	  e.preventDefault();
	  inp.dispatchEvent(new Event('input', { bubbles: false }));

  }); 
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keyup", function(e) {
	  
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
		
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
		  
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x || x.length==0) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
	x[currentFocus].scrollIntoView();
    x[currentFocus].classList.add("autocomplete-active");
	
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/

    var x = document.getElementsByClassName("autocomplete-items");
	
	if(x.length>0){					
		for (var i = 0; i < x.length; i++) {
		  if (elmnt != x[i] && elmnt != inp) { // && $(elmnt).attr('id') !="search_product_txtautocomplete-list"
				
			x[i].parentNode.removeChild(x[i]);
		  }

		}
	}

  }
  /*execute a function when someone clicks in the document:*/
  
  //document.addEventListener("click", handleClick);

	function handleClick(e)
	{
		  e.stopPropagation();
		  //e.preventDefault();	  
		  
		  closeAllLists(e.target);	
		 
		
	}
}

$(document).ready(function () {
load_credit_customers_for_autocomplete();
/*             $('#search-box1').on('input', function (e) {
				e.stopPropagation();
                const query = $(this).val();

                if (query.length > 0) {
                    $.ajax({
                        url: 'autocomplete.php',
                        method: 'GET',
                        data: { q: query },
                        success: function (data) {
                            $('#autocomplete-list').html(data);
                        }
                    });
                } else {
                    $('#autocomplete-list').empty();
                }
            }); */
			 
$('#discount_txt').keydown(function(event) {
       if (event.key === 'Enter'){ 
        event.preventDefault();
       $('#balance_cash_txt').focus();
	  }
    });
            $(document).on('click', '.autocomplete-item1', function (e) {
				e.stopPropagation();
                $('#search-box').val($(this).text());
				$('#search_cust_id').val($(this).attr("cust_id"));
				if($(this).attr("cust_id") !="")
				{
					
					$.ajax({
							url: 'api/get_customer_details.php?id='+$(this).attr("cust_id"),
							method: 'GET', 
							success: function (response) {
								 if(response != "")
								 {
									var json = response[0];
									$("#credit_customer_code_txt").val(json["customer_code"]);
									$("#customer_name_txt").val(json["customer_name"]);
									$("#customer_addr1_txt").val(json["customer_addr1"]);
									$("#customer_addr2_txt").val(json["customer_addr2"]);
									$("#customer_city_txt").val(json["customer_city"]);
									$("#customer_state_txt").val(json["customer_state_code"]);
									$("#customer_gst_no_txt").val(json["gst_no"]);
									$("#customer_mobile_no_txt").val(json["mobile_no"]);
									$("#customer_mobile_no_txt").val(json["mobile_no"]);
								 }
							}
						});
				}
                $('#autocomplete-list').empty();
            });
        });
function fn_new_credit_customer()
{
	$('#customerForm')[0].reset();
    $('#actionId').val('create');
    $('#customerModalLabel').text('Add Customer');
	$('#gst_web').attr('src',"https://bulkpe.in/check-gst");
	//$('#gst_web').attr('src',"https://services.gst.gov.in/services/searchtp");
	$('#cust-code').attr("disabled",false);
	$('#cust-name').attr("disabled",false);
    $('#customerModal').modal('show');
}
	$("#customerModal").on("hidden.bs.modal", function () {
		//alert("Customer Modal Closed");
		load_credit_customers_for_autocomplete();
	});
function load_credit_customers_for_autocomplete()
{
$.ajax({
		url: 'api/get_credit_customers.php',
		method: 'GET',
		async:false,
		success: function (response) {
			cust_ar=response.split("~");
			stock_details_arr = cust_ar[1].split("^");
			stock_details_code_arr = cust_ar[0].split("^");
			autocomplete(document.getElementById("search-box"),document.getElementById("search_cust_id"), stock_details_arr,stock_details_code_arr);
			$("#search-box").focus();
		}
		});	
}	
function copyCanvasToClipboard() {

	
	/* const element = document.getElementById("contentQrcodeDiv");
	const canvas =  html2canvas(element);
	$("#preview").attr("src", canvas.toDataURL("image/png")); */
	
		
	$('#qrcodeModal').show();
	$("#click2copy").trigger("click");

	 
}
function qrPopupClose()
{
	$('#qrcodeModal').hide();
}
function calculate_gst()
{
	if($("#gst_extra_chk").prop("checked"))
	{
		jcnos=$("#jobcard_no_txt").text();
		discount_txt=$("#discount_txt").val();
		$.ajax({
			url: 'api/get_gst_charges.php',
			method: 'POST',
			data:{ "jcnos":jcnos,"discount_txt":discount_txt},
			async:false,
			success: function (response) {
				$("#gst_charges_span").text(response);
				balance = parseFloat( $("#balance_should_be").text());
				gst_extra =parseFloat(response);
				final_balance = balance+gst_extra;
				final_balance = final_balance.toFixed(2);
				$("#gst_xtra").text(" + " +response+ " = "+final_balance);
				
			}
			});		
	}
	else
	{
		$("#gst_charges_span").text('0');
		$("#gst_xtra").text("");
	}
	
}
function showHistory()
{
	customer_mobile_no_txt=$("#customer_mobile_no_txt").val();
	if(customer_mobile_no_txt.length>0)
	{
		$.ajax({
			url: 'api/get_mobile_no_history.php',
			method: 'POST',
			data:{ "customer_mobile_no_txt":customer_mobile_no_txt},
			async:false,
			success: function (response) {
				$("#history_content").html(response);
				$("#mobileNoHistoryModal").modal('show');
				
			}
			});			
	}
}
function set_delivery_txt()
{
	$("#delivery_txt").val($("#delivery_dt_tm_txt").val());
}
</script>
<script>
window.addEventListener("message", function(event) {
  // Security check - only accept from your React app origin
  if (event.origin !== "http://52.63.19.252") return;

  const { type, payload } = event.data;

  if (type === "FILL_CREDIT_CUSTOMER") {
    document.getElementById("creditCustomerOpt")?.click();
    setTimeout(() => {
      const set = (id, val) => {
        const el = document.getElementById(id);
        if (el) { el.value = val || ""; $(el).trigger("change"); }
      };
      const searchBox = document.getElementById("search-box");
      if (searchBox) searchBox.value = payload.customer_name || "";

      set("credit_customer_code_txt", payload.customer_code);
      set("customer_name_txt",        payload.customer_name);
      set("customer_addr1_txt",       payload.customer_addr1);
      set("customer_addr2_txt",       payload.customer_addr2);
      set("customer_city_txt",        payload.customer_city);
      set("customer_gst_no_txt",      payload.gst_no);
      set("customer_mobile_no_txt",   payload.mobile_no);

      const stateEl = document.getElementById("customer_state_txt");
      if (stateEl && payload.customer_state_code) {
        stateEl.value = payload.customer_state_code;
        $(stateEl).trigger("change");
      }
      
    }, 100);
  }

  if (type === "FILL_GENERAL_CUSTOMER") {
    document.getElementById("generalCustomerOpt")?.click();
    setTimeout(() => {
      const set = (id, val) => {
        const el = document.getElementById(id);
        if (el) { el.value = val || ""; $(el).trigger("change"); }
      };
      set("customer_name_txt",      payload.customer_name);
      set("customer_mobile_no_txt", payload.mobile_no);
      
    }, 100);
  }
});
</script>