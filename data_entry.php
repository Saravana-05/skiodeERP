<?php
session_start();
include_once "connect_db.php";
?>

<style>
.dataentryContent
{
	margin-top: 10px !important;
    border: 0px solid #ccc;
	border-radius: 4px;
	min-height:550px; 
	
}
.dataentryContent h5
{
	padding-left:5px;
} 
.dataentryContent .form-control,.dataentryContent .form-select
{
	border:1px solid #3a3a3a;
} 
div.dt-container div.dt-length select {
    width: 35%; 
}
div.dt-container div.dt-length label {
	width: 55%; 
}
.dataentryContent .iconImgCls {
    width: 50px;
} 
.dataentryContent .iconDivCls {
	text-align: left;
	padding: 10px;
}
 
/* Customize DataTables export buttons */
.dt-buttons {
	display: flex;
	justify-content: flex-end;
	margin-bottom: 10px;
	gap: 10px;
}

.buttons-excel {
	background-color: #28a745 !important;
	color: white !important;
	border: none !important;
	border-radius: 6px;
	margin-left: 10px !important;
	
}

.buttons-pdf {
	background-color: #dc3545 !important;
	color: white !important;
	border: none !important;
	border-radius: 6px;
}

.buttons-excel:hover {
	background-color: #218838 !important;
}

.buttons-pdf:hover {
	background-color: #c82333 !important;
}

.dt-button {
	font-weight: 600;
	font-size: 14px;
	padding: 8px 14px !important;
	height: 30px;
	line-height: 1em !important;
}
#resultDetailTable td,#resultDetailTable th{
	padding:3px;
}
.dataentryContent button
{
	margin-top: -5px;
}
.dataentryContent .col-form-label
{
	margin-left: 0px;
	font-weight:bold;
}
#reportResultDiv
{
	border:1px solid #cfcfcf;background:#fff;min-height:200px;font-size:0.8rem;
}
.dataentryContent>.headerDiv
{
	background-color:#3a3a3a;border-radius:0px;color:#fff;margin-bottom:10px;
}
.card-header
{
	padding:0px;
}
.ui-datepicker-trigger
{
	border:0px;
	background: #fff;
} 
.datepicker
{
	width:75%;
	float:left;
} 
#MaterialMasterDiv th,#MaterialMasterDiv td
{
	font-size: 0.8rem;
    padding: 0.25rem;
}
</style>
<div class="bg_aliceblue p-1 m-1 pt-1 dataentryContent" >
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center p-1 headerDiv">
		<h5 class="mb-0 ">Master Data</h5> 			
	</div>
	<div class="m-1 p-1">
		<button type="button" class="btn btn-sm btn-warning" onclick="fnMaterialMaster()">Material Master</button> 
		<button type="button" class="btn btn-sm btn-secondary" onclick="fnMachineMaster()">Machine Master</button> 
		<button type="button" class="btn btn-sm btn-danger" onclick="fnProductMaster()">Product Master</button> 
		<button type="button" class="btn btn-sm btn-success" onclick="fnBOM()">BOM</button>
		<button type="button" class="btn btn-sm btn-info" onclick="fnSlabs()">Slabs</button> 
		<button type="button" class="btn btn-sm btn-dark" onclick="fnCuttingSlabs()">Cutting Slabs</button> 
	</div>	
    <div class="m-1 p-1" id="MaterialMasterDiv">

	</div>
    <div class="m-1 p-1" id="MachineMasterDiv">

	</div>
    <div class="m-1 p-1" id="ProductMasterDiv">

	</div>
    <div class="m-1 p-1" id="BomDiv">

	</div>
    <div class="m-1 p-1" id="SlabsDiv">

	</div>
    <div class="m-1 p-1" id="CuttingSlabsDiv">

	</div>
</div>
<!--Material Popup-->
<div class="modal fade modal-lg" id="materialPopup" tabindex="-1" aria-labelledby="materialModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="materialForm" class="modal-content">
      <div class="modal-header bg_blue">
        <h5 class="modal-title" id="materialModalLabel">Add Material</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body"> 
		<div class="row mt-1">
			<label class="control-label col-3">Materail Code</label>
			<div class="col-9">
				<input type="text"  id="material_code" class="form-control" >
			</div> 
        </div>	
		<div class="row mt-1">
			<label class="control-label col-3">Materail Name</label>
			<div class="col-9">
				<input type="text"  id="material_name" class="form-control" >
			</div> 
        </div>	
		<div class="row mt-1">
			<label class="control-label col-3">Stock in Hand</label>
			<div class="col-9">
				<input type="text"  id="stock_in_hand" class="form-control numericOnly" >
			</div> 
        </div>	
		<div class="row mt-1">
			<label class="control-label col-3">Stock As On Date</label>
			<div class="col-9">
				<input type="text" id="stock_as_on_dt" class="form-control datepicker" >
			</div> 
        </div>		
		<div class="row mt-1">
			<label class="control-label col-3">Purchase Price</label>
			<div class="col-9">
				<input type="text" id="purchase_price" class="form-control numericOnly" >
			</div> 
        </div>	
		<div class="row mt-1">
			<label class="control-label col-3">Selling Price</label>
			<div class="col-9">
				<input type="text" id="selling_price" class="form-control numericOnly" >
			</div> 
        </div> 
		<div class="row mt-1">
			<label class="control-label col-3">Loose Sales Allowed</label>
			<div class="col-9">
				<input type="text" id="loose_sales_allowed" class="form-control numericOnly" >
			</div> 
        </div>  
		<div class="row mt-1">
			<label class="control-label col-3">Open Slab Rates</label>
			<div class="col-9">
				<input type="text" id="open_slab_rates" class="form-control numericOnly" >
			</div> 
        </div>  
		<div class="row mt-1">
			<label class="control-label col-3">GST %</label>
			<div class="col-3">
				<input type="text" id="gst_percentage" class="form-control numericOnly" >
			</div> 
			<label class="control-label col-3">HSN Code</label>
			<div class="col-3">
				<input type="text" id="hsn_code" class="form-control numericOnly" >
			</div> 
        </div>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success" onclick="fnSaveMaterial()" >Save</button>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!--Edit Material Price Popup-->
<div class="modal fade" id="editMaterialPricePopup" tabindex="-1" aria-labelledby="editMaterialPriceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editMaterialPriceForm" class="modal-content">
      <div class="modal-header bg_blue">
        <h5 class="modal-title" id="editMaterialPriceModalLabel">Edit Material Price</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
		<div class="row mt-1">
			<label class="control-label col-4 col-form-label">Material Code</label>
			<div class="col-8">
				<input type="text" id="edit_material_code" class="form-control" readonly>
			</div>
        </div>
		<div class="row mt-1">
			<label class="control-label col-4 col-form-label">Purchase Price</label>
			<div class="col-8">
				<input type="text" id="edit_purchase_price" class="form-control numericOnly">
			</div>
        </div>
		<div class="row mt-1">
			<label class="control-label col-4 col-form-label">Selling Price</label>
			<div class="col-8">
				<input type="text" id="edit_selling_price" class="form-control numericOnly">
			</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success" onclick="fnUpdateMaterialPrice()">Update</button>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!--Machine Popup-->
<div class="modal fade modal-lg" id="machinePopup" tabindex="-1" aria-labelledby="machineModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="machineForm" class="modal-content">
      <div class="modal-header bg_blue">
        <h5 class="modal-title" id="machineModalLabel">Add Machine</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body"> 
		<div class="row mt-1">
			<label class="control-label col-3">Machine Code</label>
			<div class="col-9">
				<input type="text"  id="machine_code" class="form-control" >
			</div> 
        </div>	
		<div class="row mt-1">
			<label class="control-label col-3">Machine Name</label>
			<div class="col-9">
				<input type="text"  id="machine_name" class="form-control" >
			</div> 
        </div>	
		<div class="row mt-1">
			<label class="control-label col-3">Description</label>
			<div class="col-9">
				<input type="text"  id="machine_desc" class="form-control " >
			</div> 
        </div>	
		<div class="row mt-1">
			<label class="control-label col-3">Counter Reading</label>
			<div class="col-9">
				<input type="text" id="counter_reading" class="form-control numericOnly" >
			</div> 
        </div>
		<div class="row mt-1">
			<label class="control-label col-3">Counter As On Date</label>
			<div class="col-9">
				<input type="text" id="counter_on_dt" class="form-control datepicker" >
			</div> 
        </div>	 
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success" onclick="fnSaveMachine()" >Save</button>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>
</div>

<!--Product Popup-->
<div class="modal fade modal-lg" id="productPopup" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="productForm" class="modal-content">
      <div class="modal-header bg_blue">
        <h5 class="modal-title" id="productModalLabel">Add Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body"> 
		<div class="row mt-1">
			<label class="control-label col-3">Product Code</label>
			<div class="col-9">
				<input type="text"  id="product_code" class="form-control" >
			</div> 
        </div>	
		<div class="row mt-1">
			<label class="control-label col-3">Product Name</label>
			<div class="col-9">
				<input type="text"  id="product_name" class="form-control" >
			</div> 
        </div>	 
		<div class="row mt-1">
			<label class="control-label col-3">Selling Price</label>
			<div class="col-9">
				<input type="text" id="p_selling_price" class="form-control numericOnly" >
			</div> 
        </div> 
		 
		<div class="row mt-1">
			<label class="control-label col-3">Addtional Copy Rate</label>
			<div class="col-9">
				<input type="text" id="addl_copy_rate" class="form-control numericOnly" >
			</div> 
        </div> 	 
		<div class="row mt-1">
			<label class="control-label col-3">Category 1</label>
			<div class="col-3">
				<select class="form-select" id="category1" >
					<option value="Select">Select</option> 
					<option value="PRINTING">PRINTING</option> 
					<option value="SERVICES">SERVICES</option> 
					<option value="PRODUCTS">PRODUCTS</option> 
					<option value="MATERIALS">MATERIALS</option> 
					<option value="IDCARDS">IDCARDS</option> 
				</select>
			</div> 
			<label class="control-label col-3">Category 2</label>
			<div class="col-3">
				<select class="form-select" id="category2" >
					<option value="Select">Select</option> 
					<option value="LAMINATIONS">LAMINATIONS</option>  
					<option value="COVERS">COVERS</option>  
					<option value="CCOVERS">CCOVERS</option>  
					<option value="PAPERS">PAPERS</option>  
					<option value="WRISTBANDS">WRISTBANDS</option>  
					<option value="STICKERS">STICKERS</option>  
					<option value="MATERIALS">MATERIALS</option>  
					<option value="INHOUSE">INHOUSE</option>  
					<option value="ROPES">ROPES</option>  
					<option value="OUTSIDE">OUTSIDE</option>  
					<option value="BOARDS">BOARDS</option>  
					<option value="CMEDIA">CMEDIA</option>  
					<option value="ADDL">ADDL</option>  
					<option value="BADGES_FOAMBOARDS">BADGES_FOAMBOARDS</option>  
					<option value="FUSINGCARDS">FUSINGCARDS</option>  
					<option value="LAMINATEDIDCARD">LAMINATEDIDCARD</option>  
					<option value="KEYCHAINS">KEYCHAINS</option>  
					<option value="BINDING">BINDING</option>  
				</select>
			</div> 
        </div> 
		<div class="row mt-1">
			<label class="control-label col-3">GST %</label>
			<div class="col-3">
				<input type="text" id="gst_percentage" class="form-control numericOnly" >
			</div>  
			<label class="control-label col-3">HSN Code</label>
			<div class="col-3">
				<input type="text" id="hsn_code" class="form-control" >
			</div> 
        </div> 
		<div class="row mt-1">
			<div class="form-check col-6">
			  <input class="form-check-input" type="checkbox" value="" id="open_slab_rates">
			  <label class="form-check-label" for="open_slab_rates">
				Open slab rates
			  </label>
			</div>
			<div class="form-check col-6">
			  <input class="form-check-input" type="checkbox" value="" id="no_counter_sales">
			  <label class="form-check-label" for="no_counter_sales">
				No counter sales
			  </label>
			</div>
        </div> 
		<div class="row mt-1">
			<div class="form-check col-6">
			  <input class="form-check-input" type="checkbox" value="" id="1st_copy_addl_copy_applicable">
			  <label class="form-check-label" for="1st_copy_addl_copy_applicable">
				1st copy addl copy applicable
			  </label>
			</div>
			<div class="form-check col-6">
			  <input class="form-check-input" type="checkbox" value="" id="is_front_and_back_option_available">
			  <label class="form-check-label" for="is_front_and_back_option_available">
				Front and Back option available
			  </label>
			</div>
			<div class="form-check col-6">
			  <input class="form-check-input" type="checkbox" value="" id="is_material_based_rate_calculation">
			  <label class="form-check-label" for="is_material_based_rate_calculation">
				Material based rate calculation
			  </label>
			</div>
			<div class="form-check col-6">
			  <input class="form-check-input" type="checkbox" value="" id="is_manual_rate_allowed">
			  <label class="form-check-label" for="is_manual_rate_allowed">
				Manual rate allowed
			  </label>
			</div>
		</div>
		
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success" onclick="fnSaveProduct()" >Save</button>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>
</div>


<!--BOM Popup-->
<div class="modal fade modal-lg" id="bomPopup" tabindex="-1" aria-labelledby="bomModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="bomForm" class="modal-content">
      <div class="modal-header bg_blue">
        <h5 class="modal-title" id="bomModalLabel">Add BOM</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body"> 
		<div class="row mt-1">
			<label class="control-label col-3">Product Code</label>
			<div class="col-9">
				<select class="form-select" id="bom_product_code" >
					<option value="Select">Select</option>
					<?php
					if($row=mysqli_query($connection,"select *  from product_master"))		
					{ 
						while($data=mysqli_fetch_array($row))
						{
							echo '<option value="'.$data["product_code"].'">'.$data["product_name"].'</option>';           
						}
					}
					?>  
				</select>
			</div> 
        </div>	
		<div class="row mt-1">
			<label class="control-label col-3">Material Code</label>
			<div class="col-9">
				<select class="form-select" id="bom_material_code" >
					<option value="Select">Select</option>
					<?php
					if($row=mysqli_query($connection,"select *  from material_master"))		
					{ 
						while($data=mysqli_fetch_array($row))
						{
							echo '<option value="'.$data["material_code"].'">'.$data["material_name"].'</option>';           
						}
					}
					?>  
				</select>
			</div> 
        </div>	
		<div class="row mt-1">
			<label class="control-label col-3">Alternative Option</label>
			<div class="col-9">
				<input type="text"  id="alternative_caption" class="form-control" >
			</div> 
        </div>	
		<div class="row mt-1">
			<label class="control-label col-3">Alternative Key</label>
			<div class="col-9">
				<input type="text"  id="alternative_key" class="form-control" >
			</div> 
        </div>	 
		<div class="row mt-1">
			<label class="control-label col-3">Selling Price</label>
			<div class="col-9">
				<input type="text" id="p_selling_price" class="form-control numericOnly" >
			</div> 
        </div> 	 
		<div class="row mt-1">
			<label class="control-label col-3">Multiplication Factor</label>
			<div class="col-9">
				<input type="text" id="multiplication_factor" class="form-control numericOnly" >
			</div> 
        </div>  
		<div class="row mt-1">
			<label class="control-label col-3">Round Up</label>
			<div class="col-3">
				<input type="text" id="round_up" class="form-control numericOnly" >
			</div>  
			<label class="control-label col-3">Special Selling Price</label>
			<div class="col-3">
				<input type="text" id="spl_selling_price" class="form-control numericOnly" >
			</div> 
        </div> 
		<div class="row mt-1">
			<div class="form-check col-6">
			  <input class="form-check-input" type="checkbox" value="" id="is_alternatives_available">
			  <label class="form-check-label" for="is_alternatives_available">
				Is Alternatives Available?
			  </label>
			</div>
			<div class="form-check col-6">
			  <input class="form-check-input" type="checkbox" value="" id="is_this_a_product">
			  <label class="form-check-label" for="is_this_a_product">
				Is this a Product?
			  </label>
			</div>
        </div>     
		
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success" onclick="fnSaveBOM()" >Save</button>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>
</div>

<!--Slabs Popup-->
<div class="modal fade modal-lg" id="slabsPopup" tabindex="-1" aria-labelledby="slabsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="slabsForm" class="modal-content">
      <div class="modal-header bg_blue">
        <h5 class="modal-title" id="slabsModalLabel">Add Slabs</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body"> 
		<div class="row mt-1">
			<label class="control-label col-3">Product Code</label>
			<div class="col-9">
				<select class="form-select" id="slabs_product_code" >
					<option value="Select">Select</option>
					<?php
					if($row=mysqli_query($connection,"select *  from product_master"))		
					{ 
						while($data=mysqli_fetch_array($row))
						{
							echo '<option value="'.$data["product_code"].'">'.$data["product_name"].'</option>';           
						}
					}
					?>  
				</select>
			</div> 
        </div>	 
		<div class="row mt-1">
			<label class="control-label col-3">Above Qty</label>
			<div class="col-9">
				<input type="text"  id="slabs_above_qty" class="form-control numericOnly" >
			</div> 
        </div>	 
		<div class="row mt-1">
			<label class="control-label col-3">Selling Price</label>
			<div class="col-9">
				<input type="text"  id="slabs_selling_price" class="form-control numericOnly" >
			</div> 
        </div>	 
		<div class="row mt-1">
			<label class="control-label col-3">Addl Copy Rate</label>
			<div class="col-9">
				<input type="text"  id="slabs_addl_copy_rate" class="form-control numericOnly" >
			</div> 
        </div>    
		
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success" onclick="fnSaveSlabs()" >Save</button>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>
</div>

<!--Cutting Slabs Popup-->
<div class="modal fade modal-lg" id="cuttingSlabsPopup" tabindex="-1" aria-labelledby="cuttingSlabsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="cuttingSlabsForm" class="modal-content">
      <div class="modal-header bg_blue">
        <h5 class="modal-title" id="cuttingSlabsModalLabel">Add Cutting Slabs</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body"> 
		<div class="row mt-1">
			<label class="control-label col-3">Product Code</label>
			<div class="col-9">
				<select class="form-select" id="c_slabs_product_code" >
					<option value="Select">Select</option>
					<?php
					if($row=mysqli_query($connection,"select *  from product_master"))		
					{ 
						while($data=mysqli_fetch_array($row))
						{
							echo '<option value="'.$data["product_code"].'">'.$data["product_name"].'</option>';           
						}
					}
					?>  
				</select>
			</div> 
        </div>	 
		<div class="row mt-1">
			<label class="control-label col-3">No. of Ups</label>
			<div class="col-9">
				<input type="text"  id="no_of_ups" class="form-control numericOnly" >
			</div> 
        </div>	 
		<div class="row mt-1">
			<label class="control-label col-3">SVC Charge</label>
			<div class="col-9">
				<input type="text"  id="svc_charge" class="form-control numericOnly" >
			</div> 
        </div>	 
		<div class="row mt-1">
			<label class="control-label col-3">Per Sheet Rate</label>
			<div class="col-9">
				<input type="text"  id="per_sheet_rate" class="form-control numericOnly" >
			</div> 
        </div>	   
		
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success" onclick="fnSaveCuttingSlabs()" >Save</button>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>
</div>
<script>
$(document).ready(function() {
	SetUpBasics();
	$('.datepicker').removeClass('hasDatepicker').datepicker({
		dateFormat: 'dd-mm-yy' ,
		showOn: "button",
		buttonImage: "img/calender.png", 
		buttonText: "Select date"
	}); 

	$('#MaterialMasterDiv').hide();
	$('#MachineMasterDiv').hide();
	$('#ProductMasterDiv').hide();
	$('#BomDiv').hide();
	$('#SlabsDiv').hide();
	$('#CuttingSlabsDiv').hide();
});

function fnMaterialMaster()
{
	$('#MaterialMasterDiv').show();
	$('#MachineMasterDiv').hide();
	$('#ProductMasterDiv').hide();
	$('#BomDiv').hide();
	$('#SlabsDiv').hide();
	$('#CuttingSlabsDiv').hide();
	loadMaterialMaster();
}
function fnMachineMaster()
{
	$('#MaterialMasterDiv').hide();
	$('#MachineMasterDiv').show();
	$('#ProductMasterDiv').hide();
	$('#BomDiv').hide();
	$('#SlabsDiv').hide();
	$('#CuttingSlabsDiv').hide();
	loadMachineMaster();
}
function fnProductMaster()
{
	$('#MaterialMasterDiv').hide();
	$('#MachineMasterDiv').hide();
	$('#ProductMasterDiv').show();
	$('#BomDiv').hide();
	$('#SlabsDiv').hide();
	$('#CuttingSlabsDiv').hide();
	loadProductMaster();
}
function fnBOM()
{
	$('#MaterialMasterDiv').hide();
	$('#MachineMasterDiv').hide();
	$('#ProductMasterDiv').hide();
	$('#BomDiv').show();
	$('#SlabsDiv').hide();
	$('#CuttingSlabsDiv').hide();
	loadBOMMaster();
}
function fnSlabs()
{
	$('#MaterialMasterDiv').hide();
	$('#MachineMasterDiv').hide();
	$('#ProductMasterDiv').hide();
	$('#BomDiv').hide();
	$('#SlabsDiv').show();
	$('#CuttingSlabsDiv').hide();
	loadSlabsMaster();
}
function fnCuttingSlabs()
{
	$('#MaterialMasterDiv').hide();
	$('#MachineMasterDiv').hide();
	$('#ProductMasterDiv').hide();
	$('#BomDiv').hide();
	$('#SlabsDiv').hide();
	$('#CuttingSlabsDiv').show();
	loadCuttingSlabsMaster();
}

//Material Master
function loadMaterialMaster()
{
	$.ajax({
		type: "POST",
		url: "api/data_entry_details.php", 
		data: {action : "get",type:"material"},
		success: function (response) {
			if(response != "")
			{  
		 
				$('#MaterialMasterDiv').html('');
				$('#MaterialMasterDiv').html(response); 
				$('#MaterialMasterTable').DataTable({  
					"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]] ,
					 language: {
						emptyTable: 'No data available in table'
					},
					  dom: 'lBfrtip',
					buttons: [
						  {
							extend: 'excelHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">table_view</span> Export to Excel',
							title: 'Receipt Voucher Report',
							className: 'btn buttons-excel'
						},
						{
							extend: 'pdfHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">picture_as_pdf</span> Export to PDF',
							className: 'btn buttons-pdf',
							orientation: 'portrait',
							pageSize: 'A4',
							title: 'Receipt Voucher Report'
						},
						{
							extend: 'print',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">print</span> Print Table',
							className: 'btn btn-secondary',
							customize: function (win) {
								$(win.document.body).css('font-size', '14px');

								// Style the table header
								$(win.document.body).find('table')
									.addClass('compact')
									.css('border-collapse', 'collapse')
									.find('thead th')
									.css({
										'background-color': '#343a40',   // Dark gray header
										'color': 'white',
										'padding': '8px',
										'text-align': 'center'
									});

								// Optional: Center table
								$(win.document.body).find('table').css('margin', '0 auto');
							}
						}
					] 
			
				});
			}
			else
			{
				toastr.error("Failed to get details!!!");
			}
			
		} 
	});	
}

function fnAddMaterialPopup()
{
	$('#materialForm')[0].reset();
	$('#materialPopup').modal('show');
}

function fnMaterialDelete(pId)
{
	if (!confirm('Delete this Material?')) return;
    $.ajax({
		type: "POST",
		url: "api/data_entry_details.php", 
		data: {action : "del",type:"material",code:pId},
		success: function (response) {
			if(response == 'Failed')
			{
				toastr.error("Failed to delete!!!");
			}
			else{
				toastr.success("Deleted Successfully!!!");
				loadMaterialMaster();
			}
		}
    });
}	

function fnSaveMaterial()
{
	var material_code = $('#material_code').val();
	var material_name = $('#material_name').val();
	var stock_in_hand = $('#stock_in_hand').val();
	var stock_as_on_dt = $('#stock_as_on_dt').val();
	var purchase_price = $('#purchase_price').val(); 
	var selling_price = $('#selling_price').val();
	var loose_sales_allowed = $('#loose_sales_allowed').val();
	var open_slab_rates = $('#open_slab_rates').val(); 
	var gst_percentage = $("#gst_percentage").val();
	var hsn_code = $("#hsn_code").val(); 
	if (material_code == "") {
		toastr.error("Enter Material Code");
		return;
	}
	if (material_name == "") {
		toastr.error("Enter Material Name");
		return;
	}
	$.ajax({
		type: "POST",
		url: "api/data_entry_details.php",
		data:{action : "add",type:"material","material_code":material_code,
			  "material_name":material_name,
			  "stock_in_hand":stock_in_hand,
			  "stock_as_on_dt":stock_as_on_dt,
			  "purchase_price":purchase_price,
			  "selling_price":selling_price,
			  "loose_sales_allowed":loose_sales_allowed,
			  "open_slab_rates":open_slab_rates,
			  "gst_percentage":gst_percentage,
			  "hsn_code":hsn_code},
			success: function (response) {
			if(response == "success")
			{
				toastr.success("Successfully Saved!!!");				
				loadMaterialMaster();
				$('#materialPopup').modal('hide'); 
			}
			else  
			{
				toastr.error(response);
			}
			
		} 
	});	
}

function fnMaterialEditPrice(pCode, pPurchasePrice, pSellingPrice)
{
	$('#edit_material_code').val(pCode);
	$('#edit_purchase_price').val(pPurchasePrice);
	$('#edit_selling_price').val(pSellingPrice);
	$('#editMaterialPricePopup').modal('show');
}

function fnUpdateMaterialPrice()
{
	var material_code = $('#edit_material_code').val();
	var purchase_price = $('#edit_purchase_price').val();
	var selling_price = $('#edit_selling_price').val();
	if (purchase_price === "") {
		toastr.error("Enter Purchase Price");
		return;
	}
	if (selling_price === "") {
		toastr.error("Enter Selling Price");
		return;
	}
	$.ajax({
		type: "POST",
		url: "api/data_entry_details.php",
		data: {action: "update", type: "material", material_code: material_code, purchase_price: purchase_price, selling_price: selling_price},
		success: function(response) {
			if(response == "success") {
				toastr.success("Price Updated Successfully!!!");
				$('#editMaterialPricePopup').modal('hide');
				loadMaterialMaster();
			} else {
				toastr.error("Failed to update price!!!");
			}
		}
	});
}

//Machine Master
function loadMachineMaster()
{
	$.ajax({
		type: "POST",
		url: "api/data_entry_details.php", 
		data: {action : "get",type:"machine"},
		success: function (response) {
			if(response != "")
			{  
		 
				$('#MachineMasterDiv').html('');
				$('#MachineMasterDiv').html(response); 
				$('#MachineMasterTable').DataTable({  
					"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]] ,
					 language: {
						emptyTable: 'No data available in table'
					},
					  dom: 'lBfrtip',
					buttons: [
						  {
							extend: 'excelHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">table_view</span> Export to Excel',
							title: 'Machine Master Report',
							className: 'btn buttons-excel'
						},
						{
							extend: 'pdfHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">picture_as_pdf</span> Export to PDF',
							className: 'btn buttons-pdf',
							orientation: 'portrait',
							pageSize: 'A4',
							title: 'Product Master Report'
						},
						{
							extend: 'print',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">print</span> Print Table',
							className: 'btn btn-secondary',
							customize: function (win) {
								$(win.document.body).css('font-size', '14px');

								// Style the table header
								$(win.document.body).find('table')
									.addClass('compact')
									.css('border-collapse', 'collapse')
									.find('thead th')
									.css({
										'background-color': '#343a40',   // Dark gray header
										'color': 'white',
										'padding': '8px',
										'text-align': 'center'
									});

								// Optional: Center table
								$(win.document.body).find('table').css('margin', '0 auto');
							}
						}
					] 
			
				});
			}
			else
			{
				toastr.error("Failed to get details!!!");
			}
			
		} 
	});	
}

function fnAddMachinePopup()
{
	$('#machineForm')[0].reset();
	$('#machinePopup').modal('show');
}

function fnSaveMachine()
{
	var machine_code  = $('#machine_code').val();
	var machine_name = $('#machine_name').val();
	var machine_desc = $('#machine_desc').val();
	var counter_reading = $('#counter_reading').val();
	var counter_on_dt = $('#counter_on_dt').val();   
	if (machine_code  == "") {
		toastr.error("Enter Machine Code");
		return;
	}
	if (machine_name == "") {
		toastr.error("Enter Machine Name");
		return;
	}
	$.ajax({
		type: "POST",
		url: "api/data_entry_details.php",
		data:{action : "add",type:"machine","machine_code":machine_code ,
			  "machine_name":machine_name,
			  "machine_desc":machine_desc,
			  "counter_reading":counter_reading,
			  "counter_on_dt":counter_on_dt },
			success: function (response) {
			if(response == "success")
			{
				toastr.success("Successfully Saved!!!");				
				loadMachineMaster();
				$('#machinePopup').modal('hide'); 
			}
			else  
			{
				toastr.error(response);
			}
			
		} 
	});	
}

function fnMachineDelete(pId)
{
	if (!confirm('Delete this Machine?')) return;
    $.ajax({
		type: "POST",
		url: "api/data_entry_details.php", 
		data: {action : "del",type:"machine",code:pId},
		success: function (response) {
			if(response == 'Failed')
			{
				toastr.error("Failed to delete!!!");
			}
			else{
				toastr.success("Deleted Successfully!!!");
				loadMachineMaster();
			}
		}
    });
}	

//Product Master
function loadProductMaster()
{
	$.ajax({
		type: "POST",
		url: "api/data_entry_details.php", 
		data: {action : "get",type:"product"},
		success: function (response) {
			if(response != "")
			{  
				$('#ProductMasterDiv').html('');
				$('#ProductMasterDiv').html(response); 
				$('#ProductMasterTable').DataTable({  
					"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]] ,
					 language: {
						emptyTable: 'No data available in table'
					},
					  dom: 'lBfrtip',
					buttons: [
						  {
							extend: 'excelHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">table_view</span> Export to Excel',
							title: 'Product Master Report',
							className: 'btn buttons-excel'
						},
						{
							extend: 'pdfHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">picture_as_pdf</span> Export to PDF',
							className: 'btn buttons-pdf',
							orientation: 'portrait',
							pageSize: 'A4',
							title: 'Product Master Report'
						},
						{
							extend: 'print',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">print</span> Print Table',
							className: 'btn btn-secondary',
							customize: function (win) {
								$(win.document.body).css('font-size', '14px');

								// Style the table header
								$(win.document.body).find('table')
									.addClass('compact')
									.css('border-collapse', 'collapse')
									.find('thead th')
									.css({
										'background-color': '#343a40',   // Dark gray header
										'color': 'white',
										'padding': '8px',
										'text-align': 'center'
									});

								// Optional: Center table
								$(win.document.body).find('table').css('margin', '0 auto');
							}
						}
					] 
			
				});
			}
			else
			{
				toastr.error("Failed to get details!!!");
			}
			
		} 
	});	
}

function fnAddProductPopup()
{
	$('#productForm')[0].reset();
	$('#productPopup').modal('show');
}

function fnSaveProduct()
{
	var product_code  = $('#product_code').val();
	var product_name = $('#product_name').val();
	var selling_price = $('#p_selling_price').val();
	var addl_copy_rate = $('#addl_copy_rate').val();
	var gst_percentage = $('#gst_percentage').val();   
	var hsn_code = $('#hsn_code').val(); 
	var category1 = $('#category1 :selected').val(); 
	var category2 = $('#category2 :selected').val(); 
	var copy_addl_copy_applicable=0;
	var is_front_and_back_option_available=0;
	var is_material_based_rate_calculation=0;
	var is_manual_rate_allowed=0;
	var open_slab_rates=0;
	var no_counter_sales=0;
	if($('#1st_copy_addl_copy_applicable').prop('checked'))
		copy_addl_copy_applicable=1;
	if($('#is_front_and_back_option_available').prop('checked'))
		is_front_and_back_option_available=1;
	if($('#is_material_based_rate_calculation').prop('checked'))
		is_material_based_rate_calculation=1;
	if($('#is_manual_rate_allowed').prop('checked'))
		is_manual_rate_allowed=1;
	if($('#open_slab_rates').prop('checked'))
		open_slab_rates=1;
	if($('#no_counter_sales').prop('checked'))
		no_counter_sales=1;
	if (product_code  == "") {
		toastr.error("Enter Product Code");
		return;
	}
	if (product_name == "") {
		toastr.error("Enter Product Name");
		return;
	} 
	$.ajax({
		type: "POST",
		url: "api/data_entry_details.php",
		data:{action : "add",type:"product","product_code":product_code,
			  "product_name":product_name,
			  "selling_price":selling_price,
			  "1st_copy_addl_copy_applicable":copy_addl_copy_applicable,
			  "is_front_and_back_option_available":is_front_and_back_option_available,
			  "is_material_based_rate_calculation":is_material_based_rate_calculation,
			  "is_manual_rate_allowed":is_manual_rate_allowed,
			  "addl_copy_rate":addl_copy_rate,
			  "category1":category1,
			  "category2":category2,
			  "open_slab_rates":open_slab_rates,
			  "no_counter_sales":no_counter_sales,
			  "gst_percentage":gst_percentage,
			  "hsn_code":hsn_code 
			},
			success: function (response) {
			if(response == "success")
			{
				toastr.success("Successfully Saved!!!");				
				loadProductMaster();
				$('#productPopup').modal('hide'); 
			}
			else  
			{
				toastr.error(response);
			}
			
		} 
	});	
}

function fnProductDelete(pId)
{
	if (!confirm('Delete this Product?')) return;
    $.ajax({
		type: "POST",
		url: "api/data_entry_details.php", 
		data: {action : "del",type:"product",code:pId},
		success: function (response) {
			if(response == 'Failed')
			{
				toastr.error("Failed to delete!!!");
			}
			else{
				toastr.success("Deleted Successfully!!!");
				loadProductMaster();
			}
		}
    });
}	

//BOM 
function loadBOMMaster()
{
	$.ajax({
		type: "POST",
		url: "api/data_entry_details.php", 
		data: {action : "get",type:"bom"},
		success: function (response) {
			if(response != "")
			{  
				$('#BomDiv').html('');
				$('#BomDiv').html(response); 
				$('#BOMMasterTable').DataTable({  
					"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]] ,
					 language: {
						emptyTable: 'No data available in table'
					},
					  dom: 'lBfrtip',
					buttons: [
						  {
							extend: 'excelHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">table_view</span> Export to Excel',
							title: 'BOM',
							className: 'btn buttons-excel'
						},
						{
							extend: 'pdfHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">picture_as_pdf</span> Export to PDF',
							className: 'btn buttons-pdf',
							orientation: 'portrait',
							pageSize: 'A4',
							title: 'BOM'
						},
						{
							extend: 'print',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">print</span> Print Table',
							className: 'btn btn-secondary',
							customize: function (win) {
								$(win.document.body).css('font-size', '14px');

								// Style the table header
								$(win.document.body).find('table')
									.addClass('compact')
									.css('border-collapse', 'collapse')
									.find('thead th')
									.css({
										'background-color': '#343a40',   // Dark gray header
										'color': 'white',
										'padding': '8px',
										'text-align': 'center'
									});

								// Optional: Center table
								$(win.document.body).find('table').css('margin', '0 auto');
							}
						}
					] 
			
				});
			}
			else
			{
				toastr.error("Failed to get details!!!");
			}
			
		} 
	});	
}

function fnAddBOMPopup()
{
	$('#bomForm')[0].reset();
	$('#bomPopup').modal('show');
}

function fnSaveBOM()
{
	var product_code  = $('#bom_product_code :selected').val();
	var alternative_caption = $('#alternative_caption').val();
	var alternative_key = $('#alternative_key').val();
	var material_code = $('#bom_material_code :selected').val();
	var multiplication_factor = $('#multiplication_factor').val();   
	var round_up = $('#round_up').val(); 
	var spl_selling_price = $('#spl_selling_price').val(); 
	var is_alternatives_available=0;
	var is_this_a_product=0; 
	if($('#is_alternatives_available').prop('checked'))
		is_alternatives_available=1;
	if($('#is_this_a_product').prop('checked'))
		is_this_a_product=1; 
	if (product_code  == "") {
		toastr.error("Select Product Code");
		return;
	}
	if (material_code  == "") {
		toastr.error("Select Material Code");
		return;
	}
	 
	$.ajax({
		type: "POST",
		url: "api/data_entry_details.php",
		data:{action : "add",type:"bom","product_code":product_code,
			  "is_alternatives_available":is_alternatives_available,
			  "alternative_caption":alternative_caption,
			  "alternative_key":alternative_key,
			  "material_code":material_code,
			  "is_this_a_product":is_this_a_product,
			  "multiplication_factor":multiplication_factor,
			  "round_up":round_up,
			  "spl_selling_price":spl_selling_price 
			},
			success: function (response) {
			if(response == "success")
			{
				toastr.success("Successfully Saved!!!");				
				loadBOMMaster();
				$('#bomPopup').modal('hide'); 
			}
			else  
			{
				toastr.error(response);
			}
			
		} 
	});	
}

function fnBOMDelete(pId)
{
	if (!confirm('Delete this BOM?')) return;
    $.ajax({
		type: "POST",
		url: "api/data_entry_details.php", 
		data: {action : "del",type:"bom",code:pId},
		success: function (response) {
			if(response == 'Failed')
			{
				toastr.error("Failed to delete!!!");
			}
			else{
				toastr.success("Deleted Successfully!!!");
				loadBOMMaster();
			}
		}
    });
}	

//Slabs 
function loadSlabsMaster()
{
	$.ajax({
		type: "POST",
		url: "api/data_entry_details.php", 
		data: {action : "get",type:"slabs"},
		success: function (response) {
			if(response != "")
			{  
				$('#SlabsDiv').html('');
				$('#SlabsDiv').html(response); 
				$('#SlabsMasterTable').DataTable({  
					"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]] ,
					 language: {
						emptyTable: 'No data available in table'
					},
					  dom: 'lBfrtip',
					buttons: [
						  {
							extend: 'excelHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">table_view</span> Export to Excel',
							title: 'Slabs',
							className: 'btn buttons-excel'
						},
						{
							extend: 'pdfHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">picture_as_pdf</span> Export to PDF',
							className: 'btn buttons-pdf',
							orientation: 'portrait',
							pageSize: 'A4',
							title: 'Slabs'
						},
						{
							extend: 'print',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">print</span> Print Table',
							className: 'btn btn-secondary',
							customize: function (win) {
								$(win.document.body).css('font-size', '14px');

								// Style the table header
								$(win.document.body).find('table')
									.addClass('compact')
									.css('border-collapse', 'collapse')
									.find('thead th')
									.css({
										'background-color': '#343a40',   // Dark gray header
										'color': 'white',
										'padding': '8px',
										'text-align': 'center'
									});

								// Optional: Center table
								$(win.document.body).find('table').css('margin', '0 auto');
							}
						}
					] 
			
				});
			}
			else
			{
				toastr.error("Failed to get details!!!");
			}
			
		} 
	});	
}

function fnAddSlabsPopup()
{
	$('#slabsForm')[0].reset();
	$('#slabsPopup').modal('show');
}

function fnSaveSlabs()
{
	var product_code  = $('#slabs_product_code :selected').val();
	var above_qty = $('#slabs_above_qty').val();
	var selling_price = $('#slabs_selling_price').val();
	var addl_copy_rate = $('#slabs_addl_copy_rate').val(); 
	if (product_code  == "") {
		toastr.error("Select Product Code");
		return;
	} 
	 
	$.ajax({
		type: "POST",
		url: "api/data_entry_details.php",
		data:{action : "add",type:"slabs","product_code":product_code,
			  "above_qty":above_qty,
			  "selling_price":selling_price,
			  "addl_copy_rate":addl_copy_rate 
			},
			success: function (response) {
			if(response == "success")
			{
				toastr.success("Successfully Saved!!!");				
				loadSlabsMaster();
				$('#slabsPopup').modal('hide'); 
			}
			else  
			{
				toastr.error(response);
			}
			
		} 
	});	
}

function fnSlabsDelete(pId)
{
	if (!confirm('Delete this Slabs?')) return;
    $.ajax({
		type: "POST",
		url: "api/data_entry_details.php", 
		data: {action : "del",type:"slabs",id:pId},
		success: function (response) {
			if(response == 'Failed')
			{
				toastr.error("Failed to delete!!!");
			}
			else{
				toastr.success("Deleted Successfully!!!");
				loadSlabsMaster();
			}
		}
    });
}

//Cutting Slabs 
function loadCuttingSlabsMaster()
{
	$.ajax({
		type: "POST",
		url: "api/data_entry_details.php", 
		data: {action : "get",type:"cuttingslabs"},
		success: function (response) {
			if(response != "")
			{  
				$('#CuttingSlabsDiv').html('');
				$('#CuttingSlabsDiv').html(response); 
				$('#CuttingSlabsMasterTable').DataTable({  
					"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]] ,
					 language: {
						emptyTable: 'No data available in table'
					},
					  dom: 'lBfrtip',
					buttons: [
						  {
							extend: 'excelHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">table_view</span> Export to Excel',
							title: 'Cutting Slabs',
							className: 'btn buttons-excel'
						},
						{
							extend: 'pdfHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">picture_as_pdf</span> Export to PDF',
							className: 'btn buttons-pdf',
							orientation: 'portrait',
							pageSize: 'A4',
							title: 'Cutting Slabs'
						},
						{
							extend: 'print',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">print</span> Print Table',
							className: 'btn btn-secondary',
							customize: function (win) {
								$(win.document.body).css('font-size', '14px');

								// Style the table header
								$(win.document.body).find('table')
									.addClass('compact')
									.css('border-collapse', 'collapse')
									.find('thead th')
									.css({
										'background-color': '#343a40',   // Dark gray header
										'color': 'white',
										'padding': '8px',
										'text-align': 'center'
									});

								// Optional: Center table
								$(win.document.body).find('table').css('margin', '0 auto');
							}
						}
					] 
			
				});
			}
			else
			{
				toastr.error("Failed to get details!!!");
			}
			
		} 
	});	
}

function fnAddCuttingSlabsPopup()
{
	$('#cuttingSlabsForm')[0].reset();
	$('#cuttingSlabsPopup').modal('show');
}

function fnSaveCuttingSlabs()
{
	var product_code  = $('#c_slabs_product_code :selected').val();
	var no_of_ups = $('#no_of_ups').val();
	var svc_charge = $('#svc_charge').val();
	var per_sheet_rate = $('#per_sheet_rate').val(); 
	if (product_code  == "") {
		toastr.error("Select Product Code");
		return;
	} 
	 
	$.ajax({
		type: "POST",
		url: "api/data_entry_details.php",
		data:{action : "add",type:"cuttingslabs","product_code":product_code,
			  "no_of_ups":no_of_ups,
			  "svc_charge":svc_charge,
			  "per_sheet_rate":per_sheet_rate 
			},
			success: function (response) {
			if(response == "success")
			{
				toastr.success("Successfully Saved!!!");				
				loadCuttingSlabsMaster();
				$('#cuttingSlabsPopup').modal('hide'); 
			}
			else  
			{
				toastr.error(response);
			}
			
		} 
	});	
}

function fnSlabsDelete(pId)
{
	if (!confirm('Delete this Slabs?')) return;
    $.ajax({
		type: "POST",
		url: "api/data_entry_details.php", 
		data: {action : "del",type:"cuttingslabs",id:pId},
		success: function (response) {
			if(response == 'Failed')
			{
				toastr.error("Failed to delete!!!");
			}
			else{
				toastr.success("Deleted Successfully!!!");
				loadSlabsMaster();
			}
		}
    });
}
</script>