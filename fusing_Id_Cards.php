 <?php
include_once 'connect_db.php';

?>
<style>
.stockDiv
{
	background-color:#edc889;
	border: 1px solid #9d9191;
		border-radius:5px;
}
.stockDiv .col-form-label
{
	text-align: center;
}
.machineDiv
{
	border: 1px solid #ffd505;
	border-radius:5px;
}
.machineDiv label
{
	font-weight:bold; 
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
</style>


<div class="row m-0 p-0">
	<div class="col-md-12 p-2 stockDiv">
  		<div class=" row">
			 <label for="Stock Name" class="col-md-2 col-form-label">Stock Name</label>
			 <div class="col-md-4" style="align-content: center;">
				<select class="form-select" id="stockOpt">
				  <option value="0">-Select-</option>
				  <?php
				  if($row=mysqli_query($connection,"select *  from fusing_card_stock_master   where  is_active=1"))		
					{ 
						while($data=mysqli_fetch_array($row))
						{
							echo '<option value="'.$data["fusing_card_stock_id"].'">'.$data["fusing_card_stock_name"].'</option>';           
						}
					}
				  ?>
				  
				</select> 
			</div> 	  
			<label for="Stock Name" class="col-md-2 col-form-label">Piece</label>
			<div class="col-md-4" style="align-content: center;">
				<input type="text" autocomplete="off" class="form-control numericOnly" id="pieceTxt" name="pieceTxt"  disabled /> 
			</div> 		 
		</div> 
		<div class=" row mt-1">
			<label for="Stock Name" class="col-md-2 col-form-label">Qty In Nos</label>
			<div class="col-md-4" style="align-content: center;">
				<input type="text" autocomplete="off" class="form-control numericOnly" id="qtyInNosTxt" name="qtyInNosTxt"   /> 
			</div> 	 
			<label for="Stock Name" class="col-md-2 col-form-label">Machine</label>
			<div class="col-md-4" style="align-content: center;">
				<select class="form-select" id="machineOpt">
				  <option value="0">-Select-</option>
				  <?php
				  if($row=mysqli_query($connection,"select *  from machine_master   where  is_active=1 and sales_subtype_name='customer_covers'"))		
					{ 
						while($data=mysqli_fetch_array($row))
						{
							echo '<option value="'.$data["machine_id"].'">'.$data["machine_name"].'</option>';           
						}
					}
				  ?>
				</select>  
			</div> 	 
		</div> 
		<div class=" row mt-1">
			<label for="Stock Name" class="col-md-2 col-form-label">Billing Qty</label>
			<div class="col-md-4" style="align-content: center;">
				<input type="text" autocomplete="off" class="form-control numericOnly" id="billingQtyTxt" name="billingQtyTxt"   /> 
			</div> 			
			<label for="Stock Name" class="col-md-2 col-form-label">Stock Taken</label>
			<div class="col-md-4" style="align-content: center;">
				<input type="text" autocomplete="off" class="form-control numericOnly" id="stockTakenTxt" name="stockTakenTxt" disabled  />  
			</div>
		</div>
		<div class=" row mt-1">
			<label for="Stock Name" class="col-md-2 col-form-label">Rate / Card</label>
			<div class="col-md-4" style="align-content: center;">
				<input type="text" autocomplete="off" class="form-control numericOnly" id="rateCardTxt" name="rateCardTxt" disabled  /> 
			</div>	
			<label for="Stock Name" class="col-md-2 col-form-label">Stock Usage</label>
			<div class="col-md-4" style="align-content: center;">
				<input type="text" autocomplete="off" class="form-control numericOnly" id="stockUsageTxt" name="stockUsageTxt"    />  
			</div>
		</div>
		<div class=" row mt-1">
			<label for="Stock Name" class="col-md-2 col-form-label">Amount</label>
			<div class="col-md-4" style="align-content: center;">
				<input type="text" autocomplete="off" class="form-control numericOnly" id="amountTxt" name="amountTxt"    /> 
			</div>	
			<label for="Stock Name" class="col-md-2 col-form-label">Impression</label>
			<div class="col-md-4" style="align-content: center;">
				<input type="text" autocomplete="off" class="form-control numericOnly" id="noOfImpressionTxt" name="noOfImpressionTxt" disabled  />  
			</div>
		</div>
	</div> 
</div>
<div class="gridDiv  m-0 p-0" style="display:none;">
	<table class="table table-bordered" id="detailsTable">
		<thead>
			<tr class="table-danger">
			  <th>SI.No</th>
			  <th>Machine</th>
			  <th>Stock Name</th>
			  <th>Ist Copy Nos</th>
			  <th>Add. Copy Nos</th>
			  <th>No. Of Ups</th>
			  <th>No. Of Sheets</th>
			  <th>Total Qty.</th>
			  <th>Value</th>
			  <th>F/B</th>
			  <th>Sheet Count</th>
			</tr>
		</thead>
		<tbody >
		 
		</tbody>
		<tfoot>
			<tr  >
			  
			  <td colspan="7" style="text-align:right;">Total</td>
			  <td></td> 
			   <td colspan="3" ></td> 
			</tr>
		</tfoot>
	</table>	
</div>
<div id="footerDiv" class="row m-0 p-0">
	<div class="col-7" id="paymentDetailsDiv">
		<div class="row p-0 m-0">
			<div class="col-2 p-2 " style="border-right:1px solid #9d9191;">
				<label for="App Amount" class="col-form-label">App. Amount</label>
				<input type="text" autocomplete="off" class="form-control" id="appAmountTxt" name="appAmountTxt" />
			</div>
			<div class="col-6 p-2">
				<label for="App Amount" class="col-form-label">Advance Amount</label>
				<div class=" row m-0 p-0">
					<div class="form-check col-md-4" style="padding-left: 1rem !important;">
					  <input class="form-check-input" type="radio" name="advance_amount_typeOpt" id="cashTypeOpt" value="cash">
					  <label class="form-check-label" for="cashOpt">
						Cash
					  </label>
					</div>
					<div class="form-check col-md-4" style="padding-left: 1rem !important;">
					  <input class="form-check-input" type="radio" name="advance_amount_typeOpt" id="gpayTypeOpt" value="gpay">
					  <label class="form-check-label" for="gpayTypeOpt">
						Gpay
					  </label>
					</div>
					<div class="form-check col-md-4" style="padding-left: 1rem !important;">
					  <input class="form-check-input" type="radio" name="advance_amount_typeOpt" id="advTypeOpt" value="advtotal">
					  <label class="form-check-label" for="advTypeOpt">
						Adv. Total
					  </label>
					</div>					
				</div> 
				<div class=" row  m-0 p-0">
					<div class="col-md-4" style="padding-left: 1.2rem !important;">
						<input type="text" autocomplete="off" class="form-control" id="cashTxt" name="cashTxt" />
					</div>
					<div class="col-md-4" style="padding-left: 1.2rem !important;">
						<input type="text" autocomplete="off" class="form-control" id="gpayTxt" name="gpayTxt" />
					</div>
					<div class="col-md-4" style="padding-left: 1.2rem !important;">
						<input type="text" autocomplete="off" class="form-control" id="advTxt" name="advTxt" />
					</div>
				</div>
			</div>
			<div class="col-2" style="text-align:center;border-left:1px solid #9d9191;">
			<label for="App Amount" class="col-form-label">QR CODE</label>
				<img loading="lazy" class="iconImgCls" src="img/qrcode.png" alt="qrcode" style="width:75%;">
			</div>
			<div class="col-2 p-2" style="text-align:center;" >
				
				<label for="App Amount" class="col-form-label">PAYMENT SCREEN</label>
				<img loading="lazy" class="iconImgCls" src="img/whatsapp.png" alt="Whatsapp" style="width:35%;">
			</div>
			
		</div>
	</div>
	<div class="col-5">
		<input type="button" class="btn btn-sm btn-darkpurple" id="saveBtn" name="saveBtn" value="SAVE" style="border: 2px solid #fff;" onclick="saveJobCard()"/>
		<input type="button" class="btn btn-sm btn-darkpurple" id="printBtn" name="printBtn" value="PRINT" style="border: 2px solid #fff;" onclick="printJobCard()"/>
		<input type="button" class="btn btn-sm btn-darkpurple" id="alterBtn" name="alterBtn" value="ALTER" style="border: 2px solid #fff;" onclick="alterJobCard()"/>
		<input type="button" class="btn btn-sm btn-darkpurple" id="newBtn" name="newBtn" value="NEW" style="border: 2px solid #fff;" onclick="newJobCard()"/>
		<input type="button" class="btn btn-sm btn-darkpurple" id="voidBtn" name="voidBtn" value="VOID" style="border: 2px solid #fff;"  onclick="voidJobCard()"/>
		<input type="button" class="btn btn-sm btn-darkpurple" id="closeBtn" name="closeBtn" value="CLOSE" style="border: 2px solid #fff;" onclick="closeJobCard()"/>
	</div>
</div>
<script src="js/job_card_type2.js" crossorigin="anonymous"></script>
<script>

</script>