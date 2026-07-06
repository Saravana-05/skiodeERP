<?php include_once "page_guard.php"; ?>
<style>
.invoiceDiv
	{
		margin-top: 5px !important;
		border: 0px solid #ffd505;
		border-radius: 4px;
	}
	.invoiceDiv h4 {
		margin-top: -28px;
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
	.invoiceDiv .main_tab_btns
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
	.invoiceDiv .main_tab_btns_current
	{
		text-decoration: underline;
		font-weight: bold;
	}
	#functionBtn
	{
		background-color: rgb(240, 248, 255);
		border: 1px solid #9d9191;
		border-radius:5px;
		
	}
	.invoiceDetailsDiv
	{
		border : 1px solid #9d9191;
		border-radius:5px;
		background-color:#faebff;
	}
	#detailsTable th{
		font-size:0.8rem;
	}
	.table
{ 
	border:1px solid #a7a0a0;
}
.table th {
    padding: 0.3rem !important;
    font-size: 0.8rem;
	text-align:center;
} 
.table td {
    padding: 0.2rem !important;
    font-size: 0.8rem;
}
tfoot tr
{
	--bs-table-bg: #fff;
}
#footerDiv
{ 
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
.col-form-label { 
    font-size: 0.9rem;
}
</style>
<div class=" p-3 m-1 pt-0 invoiceDiv">
	
	<div class="row p-2 pt-3 bg_blue" style="border-radius:3px"> 
		<div class="col-md-12 border-yellow" style="border-right-width: 1px;">
			<div class="row m-2">
				<div class="col-md-12 p-2">
					<h4 class="mb-0">INVOICE</h4> 		
					<div class="row  mt-2  border-yellow">
						<div class="col-md-4 p-2 pt-0 pb-0" style="border-right:1px solid #ffd505;"> 
							<div class=" row mt-1">
								 <label for="staticEmail" class="col-md-5 col-form-label">BIlL CATEGORY  : </label>
								  <label for="staticEmail" class="col-md-7 col-form-label"> GENERAL CUSTOMER</label>
							</div>
							<div class=" row mt-1">
								 <label for="staticEmail" class="col-md-3 col-form-label">NAME</label>
								 <div class="col-md-9">
									<input type="text" autocomplete="off" 
									   class="form-control" 
									   id="customerNameTxt" name="customerNameTxt"   />
								 </div> 
							</div>
							<div class="row mt-1">
								 <label for="staticEmail" class="col-md-3 col-form-label">ADDRESS</label>
								 <div class="col-md-9">
									<input type="text" autocomplete="off" 
									   class="form-control" 
									   id="addressTxt" name="addressTxt"   />
								 </div> 
							</div>
							<div class="row mt-1">
								 <label for="staticEmail" class="col-md-3 col-form-label">  </label>
								 <div class="col-md-9">
									<input type="text" autocomplete="off" 
									   class="form-control" 
									   id="cityTxt" name="cityTxt"   />
								 </div>
							</div>
							<div class="row mt-1">
								 <label for="staticEmail" class="col-md-3 col-form-label">  </label>
								 <div class="col-md-9">
									<input type="text" autocomplete="off" 
									   class="form-control" 
									   id="stateTxt" name="stateTxt"   />
								 </div> 
							</div>
							<div class="row mt-1">
								 <label for="staticEmail" class="col-md-3 col-form-label">GST NO</label>
								 <div class="col-md-9">
									<input type="text" autocomplete="off" 
									   class="form-control" 
									   id="gstTxt" name="gstTxt"   />
								 </div> 
							</div>
							<div class="row mt-1">
								 <label for="staticEmail" class="col-md-3 col-form-label">PHONE</label>
								 <div class="col-md-9">
									<input type="text" autocomplete="off" 
									   class="form-control" 
									   id="phoneTxt" name="phoneTxt"   />
								 </div> 
							</div>
						</div>
						<div class="col-md-4 p-2 pt-0 pb-0" > 
							<div class=" row mt-1">
								 <label for="staticEmail" class="col-md-5 col-form-label">BIlL TYPE  </label> 
							</div>
							<div class=" row mt-1">
								 <div class="col-md-4">
								 
								 </div>
								 <div class="col-md-4">
									QUOTE NO
								 </div>
								 <div class="col-md-4">
									QUOTE DATE
								 </div>
							</div>
							<div class=" row mt-1">
								 <div class="col-md-4">
									<div class="form-check">
									  <input class="form-check-input" type="radio" name="gstTypeOpt" id="nonGstOpt" value="NonGST">
									  <label class="form-check-label" for="nonGstOpt">
										NON GST
									  </label>
									</div>
								 </div> 
								 <div class="col-md-4">
									<input type="text" autocomplete="off"  class="form-control" id="nonGST_QuoteNoTxt" name="nonGST_QuoteNoTxt"   />
								 </div> 
								 <div class="col-md-4">
									<input type="text" autocomplete="off"  class="form-control datepicker" id="nonGST_QuoteDtTxt" name="nonGST_QuoteDtTxt"   />
								 </div> 
							</div>
							<div class=" row mt-1">
								 <div class="col-md-4">
									<div class="form-check">
									  <input class="form-check-input" type="radio" name="gstTypeOpt" id="gstOpt" value="GST">
									  <label class="form-check-label" for="gstOpt">
										 GST
									  </label>
									</div>
								 </div> 
								 <div class="col-md-4">
									<input type="text" autocomplete="off"  class="form-control" id="GST_QuoteNoTxt" name="GST_QuoteNoTxt"   />
								 </div> 
								 <div class="col-md-4">
									<input type="text" autocomplete="off"  class="form-control datepicker" id="GST_QuoteDtTxt" name="GST_QuoteDtTxt"   />
								 </div> 
							</div>
						</div>
						<div class="col-md-1 p-2 pt-0 pb-0" style="border-right:1px solid #ffd505;"> 
							<br><br> 
							<img loading="lazy" class="iconImgCls" src="img/folder.png" alt="folder" style="width:100%;">
							<label for="App Amount" class="col-form-label">PULL</label>
						</div>
						<div class="col-md-3 p-2 pt-0 pb-0"> 
							<div class=" row mt-1">
								 <label for="staticEmail" class="col-md-8 col-form-label">JOB CARD DETAILS</label> 
							</div>
							<br>
							<div class="row mt-1">
								 <label for="staticEmail" class="col-md-6 col-form-label">JOB CARD DATE</label>
								 <div class="col-md-6">
									<input type="text" autocomplete="off"  class="form-control datepicker" 
									   id="jobCardDateTxt" name="jobCardDateTxt" 
									   placeholder="dd-MMM-yyyy" />
								 </div> 
							</div>
							<div class="row mt-1">
								 <label for="staticEmail" class="col-md-6 col-form-label">JOB CARD No</label>
								 <div class="col-md-6">
									<input type="text" autocomplete="off"  class="form-control numericOnly" 
									   id="jobCardNoTxt" name="jobCardNoTxt" 
									   placeholder="" />
								 </div> 
							</div>
							<div class="row mt-1">
								 <label for="staticEmail" class="col-md-6 col-form-label">USER</label>
								 <div class="col-md-6">
									<input type="text" autocomplete="off"  class="form-control  " 
									   id="userTxt" name="userTxt" 
									   placeholder="" />
								 </div> 
							</div>
						</div>
					</div> 
				</div> 
			</div>
		</div> 
	</div>
	<div class="invoiceDetailsDiv row mt-1 p-2 ">	
		<center><h3 style="font-size: 1.4rem;"><u>INVOICE</u></h3></center>
		<div class="gridDiv  m-0 p-0">
			<table class="table table-bordered" id="detailsTable">
				<thead>
					<tr class="table-danger">
					  <th rowspan="2">SI.No</th>
					  <th rowspan="2">Machine</th>
					  <th rowspan="2">Stock Name</th>
					  <th rowspan="2">Total Qty</th>
					  <th rowspan="2">Value</th> 
					  <th rowspan="2">Disc. Allowed</th>
					  <th colspan="2">Taxable Value</th>
					  <th colspan="2">Taxable Amount</th> 
					  <th rowspan="2">Net Amount</th> 
					</tr>
					<tr class="table-danger">
					    
					  <th>12%  </th>
					  <th>18% Tax</th>
					  <th>12% Tax</th>
					  <th>18% Tax</th>
					  
					</tr>
				</thead>
				<tbody >
					<tr>
						<td>1.</td>
						<td></td>
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
					<tr class="table-danger">
						<td>2.</td>
						<td></td>
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
					<tr>
						<td>3.</td>
						<td></td>
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
				</tbody> 
			</table>	
</div>
<div id="footerDiv" class="row m-0 p-0">
	<div class="col-12" id="paymentDetailsDiv">
		<div class="row p-0 m-0">
			<div class="col-1 p-2 " style="border-right:1px solid #9d9191;">
				<center><h6 style="color:red;">ADV. PAYMENT</h6></center>
				<div class=" row mt-1 pt-1">
					 <label for="staticEmail" class="col-md-12 col-form-label">Cash</label>
					 <div class="col-md-12">
						<input type="text" autocomplete="off"  class="form-control numericOnly" 
						   id="cashTxt" name="cashTxt"  placeholder="" />
					 </div> 
				</div>
				<div class=" row mt-1">
					 <label for="staticEmail" class="col-md-12 col-form-label">Gpay</label>
					 <div class="col-md-12">
						<input type="text" autocomplete="off"  class="form-control " 
						   id="gpayTxt" name="gpayTxt"  placeholder="" />
					 </div> 
				</div>
			</div>
			<div class="col-6 p-2">
				<center><h6 style="color:red;">BILL DETAILS</h6></center> 
				<div class=" row mt-1 p-1">
					 <label for="staticEmail" class="col-md-1 p-0 col-form-label">Sub Total</label>
					 <div class="col-md-2">
						<br>
						<input type="text" autocomplete="off"  class="form-control  " 
						   id="subTotalTxt" name="subTotalTxt"  placeholder="" />
					 </div> 
					 <label for="staticEmail" class="col-md-1 p-0 col-form-label">Taxable Value </label>
					 <div class="col-md-2">
						<label for="staticEmail" class=" col-form-label">12% </label><br>
						<input type="text" autocomplete="off"  class="form-control  " 
						   id="subTotalTxt" name="subTotalTxt"  placeholder="" />
					 </div>
					<div class="col-md-2">
						<label for="staticEmail" class=" col-form-label">18% </label><br>
						<input type="text" autocomplete="off"  class="form-control  " 
						   id="subTotalTxt" name="subTotalTxt"  placeholder="" />
					 </div>					 
					 <label for="staticEmail" class="col-md-1 p-0 col-form-label">Bill Amt</label>
					 <div class="col-md-2">
						<br>
						<input type="text" autocomplete="off"  class="form-control numericOnly" 
						   id="bill_amountTxt" name="bill_amountTxt"  placeholder="" />
					 </div> 
				</div>  
				<div class=" row mt-1 p-1">
					 <label for="staticEmail" class="col-md-1 p-0 col-form-label">Discount</label>
					 <div class="col-md-2">
						<input type="text" autocomplete="off"  class="form-control  " 
						   id="discountTxt" name="discountTxt"  placeholder="" />
					 </div> 
					 <label for="staticEmail" class="col-md-1 p-0 col-form-label">Taxable Amount </label>
					 <div class="col-md-2">
						<label for="staticEmail" class=" col-form-label">12% </label><br>
						<input type="text" autocomplete="off"  class="form-control  " 
						   id="subTotalTxt" name="subTotalTxt"  placeholder="" />
					 </div>
					<div class="col-md-2">
						<label for="staticEmail" class=" col-form-label">18% </label><br>
						<input type="text" autocomplete="off"  class="form-control  " 
						   id="subTotalTxt" name="subTotalTxt"  placeholder="" />
					 </div>					 
					 <label for="staticEmail" class="col-md-1 p-0 col-form-label">Bill Amt</label>  
					 <div class="col-md-2">
						<input type="text" autocomplete="off"  class="form-control numericOnly" 
						   id="adv_amountTxt" name="adv_amountTxt"  placeholder="" />
					 </div> 
				</div>  
			</div> 
			<div class="col-4 p-2" style="background-color:#e7f5d1;border-left:1px solid #9d9191"> 
				<div class=" row mt-1 p-1" style="border-bottom:1px solid #9d9191;">
					<div class="col-md-1"></div>
					 <label for="staticEmail" class="col-md-6 p-0 col-form-label"><span style="color:red;font-size: 1rem;">AMOUNT PAYABLE : </span></label>
					 <div class="col-md-4">
						<input type="text" autocomplete="off"  class="form-control  " 
						   id="amt_payableTxt" name="amt_payableTxt"  placeholder="" />
					 </div>  
					 <div class="col-md-1"></div>
				</div>
				<div class=" row mt-1 p-1">
					 <label for="staticEmail" class="col-md-2 p-0 col-form-label">Savings A/c</label>
					 <div class="col-md-4">
						<input type="text" autocomplete="off"  class="form-control  " 
						   id="savingsTxt" name="savingsTxt"  placeholder="" />
					 </div> 
					 <label for="staticEmail" class="col-md-2 p-0 col-form-label">Cash</label>
					 <div class="col-md-4">
						<input type="text" autocomplete="off"  class="form-control numericOnly" 
						   id="cash_amountTxt" name="cash_amountTxt"  placeholder="" />
					 </div> 
				</div>  
				<div class=" row mt-1 p-1">
					 <label for="staticEmail" class="col-md-2 p-0 col-form-label">Current A/c</label>
					 <div class="col-md-4">
						<input type="text" autocomplete="off"  class="form-control  " 
						   id="current_AccountTxt" name="current_AccountTxt"  placeholder="" />
					 </div> 
					 <label for="staticEmail" class="col-md-2 p-0 col-form-label">Cash Tendered</label>
					 <div class="col-md-4">
						<input type="text" autocomplete="off"  class="form-control numericOnly" 
						   id="cash_tenderedTxt" name="cash_tenderedTxt"  placeholder="" />
					 </div> 
				</div>  
				<div class=" row mt-1 p-1"> 
					 <div class="col-md-6">
						 
					 </div> 
					 <label for="staticEmail" class="col-md-2 p-0 col-form-label">Balance Payable</label>
					 <div class="col-md-4">
						<input type="text" autocomplete="off"  class="form-control numericOnly" 
						   id="balance_payTxt" name="balance_payTxt"  placeholder="" />
					 </div> 
				</div>  
			</div>
			<div class="col-1 p-2" style="background-color:#e7f5d1;border-left:1px solid #9d9191">
				<div class="row">
					<div class="col-12 mb-2" style="text-align:center;">
						<label for="App Amount" class="col-form-label" style="margin-top: 1.3rem;">QR CODE</label><br>
						<img loading="lazy" class="iconImgCls" src="img/qrcode.png" alt="qrcode" style="width:90%;">
					</div>
					<div class="col-12 " style="text-align:center;" > 
						<label for="App Amount" class="col-form-label">PAYMENT SCREEN</label>
						<img loading="lazy" class="iconImgCls" src="img/whatsapp.png" alt="Whatsapp" style="width:90%;">
					</div>
				</div>
			</div>
			
		</div>
	</div>
	<div class="col-12 mt-3">
	<center>
		<input type="button" class="btn btn-sm btn-darkpurple" id="saveBtn" name="saveBtn" value="SAVE" style="border: 2px solid #fff;" onclick="saveQuotation()"/>
		<input type="button" class="btn btn-sm btn-darkpurple" id="printBtn" name="printBtn" value="PRINT" style="border: 2px solid #fff;" onclick="printQuotation()"/>
		<input type="button" class="btn btn-sm btn-darkpurple" id="alterBtn" name="alterBtn" value="ALTER" style="border: 2px solid #fff;" onclick="alterQuotation()"/>
		<input type="button" class="btn btn-sm btn-darkpurple" id="newBtn" name="newBtn" value="NEW" style="border: 2px solid #fff;" onclick="newQuotation()"/>
		<input type="button" class="btn btn-sm btn-darkpurple" id="voidBtn" name="voidBtn" value="VOID" style="border: 2px solid #fff;"  onclick="voidQuotation()"/>
		<input type="button" class="btn btn-sm btn-darkpurple" id="closeBtn" name="closeBtn" value="CLOSE" style="border: 2px solid #fff;" onclick="closeQuotation()"/>
		</center>
	</div>
</div>
	</div>
</div>
