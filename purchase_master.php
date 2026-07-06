<?php
session_start();
include_once "connect_db.php";
include_once "page_guard.php";
?>
<style>
/* ── Purchase page modern styling ─────────────────────────────── */
.pm-page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
.pm-page-title { font-size: 1.25rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 10px; }
.pm-page-title .material-icons-round { font-size: 1.4rem; color: #4f46e5; }
.pm-btn-add {
	display: inline-flex; align-items: center; gap: 6px; padding: 8px 20px;
	background: #4f46e5; color: #fff; border: none; border-radius: 8px;
	font-size: 0.85rem; font-weight: 700; cursor: pointer; transition: all 0.2s;
}
.pm-btn-add:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79,70,229,0.3); }

/* Filter bar */
.pm-filter-bar {
	display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap;
	background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; margin-bottom: 16px;
}
.pm-filter-group { display: flex; flex-direction: column; gap: 3px; }
.pm-filter-group label { font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; }
.pm-filter-group input {
	padding: 6px 10px; border: 1px solid #e2e8f0; border-radius: 7px; font-size: 0.82rem; color: #1e293b; background: #fff;
}
.pm-filter-group input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 3px rgba(99,102,241,0.08); }
.pm-btn-filter {
	display: inline-flex; align-items: center; gap: 5px; padding: 7px 16px;
	background: #4f46e5; color: #fff; border: none; border-radius: 7px;
	font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: background 0.2s;
}
.pm-btn-filter:hover { background: #4338ca; }
.pm-btn-reset {
	display: inline-flex; align-items: center; gap: 5px; padding: 7px 16px;
	background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 7px;
	font-size: 0.82rem; font-weight: 600; cursor: pointer;
}
.pm-btn-reset:hover { background: #e2e8f0; }

/* Table section */
.pm-table-section {
	background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; overflow: hidden;
}
.pm-table-section table.dataTable thead th,
.pm-table-section #resultPurchaseTable thead th {
	background: #1e293b !important; color: #ffffff !important; font-size: 0.78rem !important;
	font-weight: 800 !important; text-transform: uppercase; letter-spacing: 0.4px;
	padding: 11px 12px !important; border-bottom: 2px solid #334155 !important; border-top: none !important;
}
.pm-table-section #resultPurchaseTable tbody td {
	padding: 10px 12px !important; font-size: 0.85rem; color: #1e293b; font-weight: 500;
	border-bottom: 1px solid #e2e8f0 !important; vertical-align: top;
}
.pm-table-section #resultPurchaseTable tbody tr:hover { background: #f8fafc !important; }
.pm-table-section .dataTables_wrapper .dataTables_length { margin-bottom: 10px; }
.pm-table-section .dataTables_wrapper .dataTables_length select {
	padding: 4px 8px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.82rem;
}
.pm-table-section .dataTables_wrapper .dataTables_filter input {
	padding: 6px 10px; border: 1px solid #e2e8f0; border-radius: 7px; font-size: 0.82rem;
}
.pm-table-section .dataTables_wrapper .dataTables_filter input:focus {
	border-color: #6366f1; outline: none; box-shadow: 0 0 0 3px rgba(99,102,241,0.08);
}
.pm-table-section .dataTables_wrapper .dataTables_info { font-size: 0.78rem; color: #64748b; }
.pm-table-section .dataTables_wrapper .dataTables_paginate .paginate_button {
	padding: 4px 10px !important; font-size: 0.78rem !important; border-radius: 6px !important; margin: 0 2px !important;
}
.pm-table-section .dataTables_wrapper .dataTables_paginate .paginate_button.current {
	background: #1e293b !important; color: #fff !important; border-color: #1e293b !important;
}

/* Export buttons */
.pm-table-section .dt-buttons { display: flex; gap: 8px; margin-bottom: 12px; }
.pm-table-section .dt-button {
	font-weight: 600 !important; font-size: 0.78rem !important; padding: 6px 14px !important;
	border-radius: 7px !important; border: none !important; height: auto !important; line-height: normal !important;
}
.pm-table-section .buttons-excel { background: #059669 !important; color: #fff !important; }
.pm-table-section .buttons-excel:hover { background: #047857 !important; }
.pm-table-section .buttons-pdf { background: #dc2626 !important; color: #fff !important; }
.pm-table-section .buttons-pdf:hover { background: #b91c1c !important; }
.pm-table-section .btn-secondary { background: #475569 !important; color: #fff !important; border: none !important; }
.pm-table-section .btn-secondary:hover { background: #334155 !important; }

/* Delete button in table */
.pm-table-section .btn-outline-danger {
	font-size: 0.75rem; padding: 3px 10px; border-radius: 6px; font-weight: 600;
}

/* Add purchase form styling */
#addPurchaseDiv .bg_blue { background: #4f46e5 !important; border-radius: 8px; }
#addPurchaseDiv .bg_blue h5 { color: #fff; font-size: 0.95rem; font-weight: 700; }
#addPurchaseDiv label { font-weight: 600 !important; font-size: 0.8rem; color: #475569; }
#addPurchaseDiv .form-control, #addPurchaseDiv .form-select { font-size: 0.82rem; border-radius: 7px; }

.item_tbl > th, td { border: 1px dotted grey; }
div.dt-container div.dt-length select { width: 35%; }
div.dt-container div.dt-length label { width: 55%; }
#purchaseForm label { font-weight: bold; }
.ui-datepicker-trigger { border: 0px; background: #fff; }
.datepicker { width: 75%; float: left; }
</style>

<div class="mt-2" style="display:none;">
	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)" onclick="fnSideMenu(1)">Dashboard</a></li>
		<li class="breadcrumb-item active" aria-current="page">Purchase Master</li>
	  </ol>
	</nav>
</div>
<?php
	$sql="SELECT max(purchase_voucher_no) as max_voucher_no FROM purchase_master;";
	$purchase_voucher_no=1;
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{
			if($row=mysqli_fetch_array($query))
			{
				if(!is_null($row["max_voucher_no"]))
				{
					$purchase_voucher_no=$row["max_voucher_no"]+10;
				}
			}
		}
	}
?>
<div class="p-3">
	<!-- Page Header -->
	<div class="pm-page-header">
		<div class="pm-page-title">
			<span class="material-icons-round">receipt_long</span> Purchase Invoice
		</div>
		<button class="pm-btn-add" id="addPurchaseBtn"><span class="material-icons-round" style="font-size:16px;">add</span> Add Purchase</button>
	</div>

	<!-- Add Purchase Form (hidden by default) -->
	<div id="addPurchaseDiv" class="mt-1" style="padding:0px;width:100%;font-size:12px;display:none;">
		<form id="purchaseForm" class="modal-content">
			<div class="bg_blue p-2 mt-2 mb-2">
				<h5 id="purchaseModalLabel" class="m-0">Add Purchase</h5>
			</div>
			<div class="row mt-1 px-2">
				<label class="control-label col-2">Purchase Voucher No</label>
				<div class="col-2">
					<input type="text" id="purchase_voucher_no" class="form-control" value="<?php echo $purchase_voucher_no; ?>" disabled>
				</div>
				<label class="control-label col-2">Purchase Dt</label>
				<div class="col-2">
					<input type="text" id="purchase_date" class="form-control datepicker" value="" required >
				</div>
				<label class="control-label col-1">PO No</label>
				<div class="col-3">
					<input type="text" id="our_po_no" class="form-control numericOnly" value="" >
				</div>
			</div>
			<div class="row mt-1 px-2">
				<label class="control-label col-2">Vendor Invoice No</label>
				<div class="col-2">
					<input type="text" id="vendor_invoice_no" class="form-control" value="" required >
				</div>
				<label class="control-label col-2">Vendor Invoice Dt</label>
				<div class="col-2">
					<input type="text" id="vendor_invoice_date" class="form-control datepicker" value="" required >
				</div>
				<label class="control-label col-1">Vendor</label>
				<div class="col-3">
					<select class="form-select" id="vendor_code">
						<option value="Select">Select</option>
						<?php
						if($row=mysqli_query($connection,"select *  from customer_master where customer_type like '%VENDOR%'"))
						{
							while($data=mysqli_fetch_array($row))
							{
								echo '<option value="'.$data["customer_code"].'" attr-state="'.$data["customer_state_code"].'" >'.$data["customer_name"].'</option>';
							}
						}
						?>
					</select>
				</div>
			</div>
			<div class="m-1 px-2">
				<button type="button" class="btn btn-sm btn-info" style="float:right;" onclick="fnPurchaseDetailPopup()" >Add Detail</button>
			</div>
			<div class="px-2">
			<table class="table table-bordered table-hover table-striped mt-2" id="purchasedetailsTable">
			<thead>
				<th scope="col">#</th>
				<th scope="col">Code</th>
				<th scope="col">Material</th>
				<th scope="col">Purchase Price</th>
				<th scope="col">Quantity</th>
				<th scope="col">GST %</th>
				<th scope="col">Discount %</th>
				<th scope="col">Amount</th>
				<th scope="col">Total</th>
				<th scope="col">Opr</th>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
			<tr>
			<th colspan="8" style="text-align:right">Total:</th>
			<th><input type="text" id="net_total_value" size="8" class="form-control numericOnly" value=""  > </th>
			</tr>
			</tfoot>
			</table>
			</div>
			<div class="row mt-1" style="display:none;">
				<label class="control-label col-1">IGST</label>
				<div class="col-2">
					<input type="text" id="igst_value" class="form-control numericOnly" value="">
				</div>
				<label class="control-label col-1">CGST</label>
				<div class="col-2">
					<input type="text" id="cgst_value" class="form-control numericOnly" value="">
				</div>
				<label class="control-label col-1">SGST</label>
				<div class="col-2">
					<input type="text" id="sgst_value" class="form-control numericOnly" value="">
				</div>
				<label class="control-label col-1">Total</label>
				<div class="col-2">
					<input type="text" id="total_after_discount_value" class="form-control numericOnly" value="">
				</div>
			</div>
			<div class="row mt-2 px-2">
				<div class="col-5"></div>
				<div class="col-2">
					<button type="button" class="btn btn-sm btn-success" onclick="fnSavePurchase()">Save</button>
					<button type="button" class="btn btn-sm btn-secondary" onclick="fnAddPurchaseCancel()">Cancel</button>
				</div>
			</div>
		</form>
	</div>

	<!-- Filter Bar -->
	<div class="pm-filter-bar" id="purchaseFilterBar">
		<div class="pm-filter-group">
			<label>From Date</label>
			<input type="date" id="pm_from_date" value="<?php echo date('Y-m-01'); ?>" />
		</div>
		<div class="pm-filter-group">
			<label>To Date</label>
			<input type="date" id="pm_to_date" value="<?php echo date('Y-m-d'); ?>" />
		</div>
		<button class="pm-btn-filter" onclick="loadPurchase()"><span class="material-icons-round" style="font-size:15px;">filter_list</span> Filter</button>
		<button class="pm-btn-reset" onclick="pmResetFilter()"><span class="material-icons-round" style="font-size:15px;">restart_alt</span> Show All</button>
	</div>

	<!-- Table Section -->
	<div class="pm-table-section" id="purchaseDiv">
	</div>
</div>

<!-- Add Detail Modal -->
<div class="modal fade" id="purchaseDetailModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="purchaseDetailForm" class="modal-content">
      <div class="modal-header bg_blue" style="background:#4f46e5;border-radius:8px 8px 0 0;">
        <h5 class="modal-title" id="customerModalLabel" style="color:#fff;">Add Purchase Detail</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="actionId" value="">
		<div class="mb-3">
          <label>Material</label>
          <select class="form-select" id="material_code">
				<option value="Select">Select</option>
			  <?php
				$sql="select * from material_master order by material_name;";
				if($qry=mysqli_query($connection,$sql))
				{
					while($row=mysqli_fetch_array($qry))
					{
						echo '<option value="'.$row["material_code"].'" purchase_price="'.$row["purchase_price"].'" '.'>'.$row["material_name"].'</option>';
					}
				}
			  ?>
			  </select>
        </div>
        <div class="mb-3">
          <label>Purchase Price</label>
          <input type="text" id="purchase_price" class="form-control numericOnly">
        </div>
        <div class="mb-3">
          <label>Quantity</label>
          <input type="text" id="qty" class="form-control numericOnly" required>
        </div>
        <div class="mb-3">
          <label>GST %</label>
          <input type="text" id="gst_percentage" class="form-control numericOnly">
        </div>
        <div class="mb-3">
          <label>Discount %</label>
          <input type="text" id="item_discount" class="form-control numericOnly">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success" onclick="fnSaveDetail()">Save</button>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- View Purchase Modal -->
<div class="modal fade" id="purchaseViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border:none;border-radius:12px;overflow:hidden;">
      <div class="modal-header" style="background:#1e293b;color:#fff;padding:12px 18px;">
        <h6 class="modal-title mb-0" style="font-weight:700;"><span class="material-icons-round" style="font-size:20px;vertical-align:-4px;margin-right:6px;">receipt_long</span>Purchase Invoice Details</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="padding:20px 24px;" id="purchaseViewBody"></div>
      <div class="modal-footer" style="padding:10px 18px;border-top:1px solid #e2e8f0;">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
var sNo=1;
var allTableData = new Array();
var totalValue=0;

$(document).ready(function () {
	SetUpBasics();
	$('.datepicker').removeClass('hasDatepicker').datepicker({
		<?php if($_SESSION["user_type"]=="OPERATOR") { ?>
		minDate: -2,
		<?php } else if($_SESSION["user_type"]=="ACCOUNTANT") { ?>
		minDate: -7,
		<?php } ?>
		dateFormat: 'dd-mm-yy',
		showOn: "button",
		buttonImage: "img/calender.png",
		buttonText: "Select date"
	});
	$(".numericOnly").keypress(function (e) {
		if (e.which != 46 && e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
			e.preventDefault();
			return false;
		}
	});
	$('#vendor_code').on('change', function() {
        var selectedValue = $(this).val();
        var state_code=$(this).attr('attr-state');
    });
	loadPurchase();
	$('#purchaseDiv').show();
    $('#addPurchaseDiv').hide();

  $('#addPurchaseBtn').click(function () {
	sNo=1;
	allTableData = new Array();
	totalValue=0;
    $('#purchaseForm')[0].reset();
	$.ajax({
			type: "POST",
			url: "api/crud_purchase_api.php",
			  async:false,
			data: {action : "getNextPVNo"},
			success: function (response) {
				if(response != "")
				{
					$("#purchase_voucher_no").val(response);
				}
			}
	});
    $('#actionId').val('add');
    $('#purchaseModalLabel').text('Add Purchase');
	render_jobdetailsTable_from_allTableData();
    $('#purchaseDiv').hide();
	$('#purchaseFilterBar').hide();
    $('#addPurchaseDiv').show();
  });

   $('#material_code').change(function() {
    var selectedValue = $('option:selected', this).attr('purchase_price');
	$('#purchase_price').val(selectedValue);
  });

  $('#purchaseForm').submit(function (e) {
    e.preventDefault();
	if($('#user_type :selected').val() == "Select")
	{
		toastr.error("Select User Type");
		return;
	}
    const payload = {
      username:$('#user_name').val(),
      password: $('#pass_word').val(),
      usertype: $('#user_type :selected').val(),
      displayname: $('#user_display_name').val()
    };
    var action = $('#actionId').val();
    $.ajax({
      url: `api/crud_purchase_api.php?action=${action}`,
      method: 'POST',
      data: JSON.stringify(payload),
      contentType: 'application/json',
      success: function (response) {
		   if (response.status == "Success") {
			   toastr.success("Successfully updated.");
				$('#purchaseDiv').show();
				$('#purchaseFilterBar').show();
				$('#addPurchaseDiv').hide();
				loadPurchase();
			}
			else
			{
				toastr.error(response.status);
				return;
			}
      }
    });
  });

  $('#purchaseDiv').on('click', '.editBtn', function () {
    const row = $(this).closest('tr');
    var code = row.data('id');
	$('#actionId').val('update');
	$.get('api/crud_purchase_api.php?action=editrow&code='+code, function (data) {
      let rows = '';
	  for(var i=0;i<data.length;i++)
	  {
			$('#user_name').val(data[i].user_name);
			$('#pass_word').val(data[i].pass_word);
			$('#user_display_name').val(data[i].user_display_name);
			$('#user_type').val(data[i].user_type);
			$('#user_name').attr("disabled",true);
	  }
	});
    $('#purchaseModalLabel').text('Edit User');
    $('#purchaseDiv').hide();
	$('#purchaseFilterBar').hide();
    $('#addPurchaseDiv').show();
  });

});

function pmResetFilter() {
	$('#pm_from_date').val('');
	$('#pm_to_date').val('');
	loadPurchase();
}

function fnDiscount()
{
	var total_net_amt = $('#net_total_value').val();
	var discount = $('#discount_value').val();
	var total_amt = parseFloat(total_net_amt)-parseFloat((discount/100)*total_net_amt);
	$('#total_after_discount_value').val(total_amt.toFixed(2));
}

function fnSaveDetail()
{
	var material_code=$('#material_code :selected').val();
	var material_name=$('#material_code :selected').text();
	var purchase_price=$('#purchase_price').val();
	var qty=$('#qty').val();
	var gst=$('#gst_percentage').val();
	var discount=$('#item_discount').val();
	var amount = parseFloat(purchase_price)*parseFloat(qty);
	amount = parseFloat(amount)-parseFloat((discount/100)*amount);
	var gstValue=0;
	if(!isNaN(gst))
	{
		if(parseFloat(gst)>0)
		{
		gstValue= (parseFloat(amount) * parseFloat(gst)/100);
		}
	}
	var total =  parseFloat(amount) + parseFloat(gstValue);
	total=total.toFixed(2);
	var tempArray= {}
	tempArray['sNo']=sNo;
	tempArray['material_code']=material_code;
	tempArray['material_name']=material_name;
	tempArray['purchase_price']=purchase_price;
	tempArray['qty']=qty;
	tempArray['gst']=gst;
	tempArray['discount']=discount;
	tempArray['amount']=amount;
	tempArray['total']=total;

	var arrCnt = 0;
	if(allTableData.length>=1)
		arrCnt=allTableData.length;
	allTableData[arrCnt]=tempArray;
	render_jobdetailsTable_from_allTableData();
	$('#purchaseDetailModal').modal('hide');
}

function delete_allTableData(pIndex)
{
	allTableData.splice(pIndex,1)
	render_jobdetailsTable_from_allTableData();
}

function render_jobdetailsTable_from_allTableData()
{
$("#purchasedetailsTable tbody").empty();
	sNo=0;
	totalValue=0;
	for(var i=0;i<allTableData.length;i++)
	{
		sNo++;
		markup = "<tr>";
		allTableData[i]['sNo']=sNo;
		markup = markup + "<td>" + allTableData[i]['sNo'] + "</td>";
		markup = markup + "<td>" + allTableData[i]['material_code'] + "</td>";
		markup = markup + "<td>" + allTableData[i]['material_name'] + "</td>";
		markup = markup + "<td>" + allTableData[i]['purchase_price'] + "</td>";
		markup = markup + "<td>" + allTableData[i]['qty'] + "</td>";
		markup = markup + "<td>" + allTableData[i]['gst'] + "</td>";
		markup = markup + "<td>" + allTableData[i]['discount'] + "</td>";
		markup = markup + "<td>" + allTableData[i]['amount'] + "</td>";
		markup = markup + "<td>" + allTableData[i]['total'] + "</td>";
		markup = markup + "<td>";
		markup = markup + "<a href='javascript:void(0)' onclick='delete_allTableData("+i+")'><img src='img/del.png' width='16' height='16' alt='Delete' /></a> ";
		markup = markup + "</td>";
		markup = markup + "</tr>";
		totalValue = totalValue + parseFloat(allTableData[i]['total'] );
		$("#purchasedetailsTable tbody").append(markup);
	}
	$('#net_total_value').val(totalValue.toFixed(2));
	for(var i=allTableData.length;i<11;i++)
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
		markup = markup + "</tr>";
		$("#purchasedetailsTable tbody").append(markup);
	}
	fnCalculateTotal();
}

function fnCalculateTotal()
{
	var IGST=parseFloat($('#igst_value').val());
	var CGST=parseFloat($('#cgst_value').val());
	var SGST=parseFloat($('#sgst_value').val());
	var total=parseFloat($('#net_total_value').val());
	var finalTotal = 0;
	if(IGST>0)
		finalTotal =  finalTotal + (total +   (total*IGST/100) );
	if(CGST>0)
		finalTotal =  finalTotal + (total +  (total*CGST/100) );
	if(SGST>0)
		finalTotal =  finalTotal + (total +   (total*SGST/100) );
	$('#total_after_discount_value').val(finalTotal);
}

function fnAddPurchaseCancel()
{
	$('#purchaseDiv').show();
	$('#purchaseFilterBar').show();
    $('#addPurchaseDiv').hide();
}

function fnPurchaseDetailPopup()
{
	if($('#purchase_date').val() == '')
	{
		toastr.error("Enter Purchase Date");
		return;
	}
	if($('#vendor_invoice_no').val() == '')
	{
		toastr.error("Enter Vendor Invoice No");
		return;
	}
	if($('#vendor_invoice_date').val() == '')
	{
		toastr.error("Enter Vendor Invoice Date");
		return;
	}
	if($('#vendor_code :selected').val() == 'Select')
	{
		toastr.error("Select vendor Code");
		return;
	}
	$('#purchaseDetailForm')[0].reset();
	$('#purchaseDetailModal').modal('show');
}

function fnPurchaseView(btn) {
	var $tr = $(btn).closest('tr');
	var cells = $tr.find('td');
	var pvNo = $(cells[0]).text();
	var date = $(cells[1]).text();
	var poNo = $(cells[2]).text() || '-';
	var vendor = $(cells[3]).text();
	var invoiceNo = $(cells[4]).text();
	var invoiceDate = $(cells[5]).text();
	var items = $(cells[6]).html();
	var tax = $(cells[7]).html();
	var total = $(cells[8]).text();

	var html = '<div style="display:flex;gap:20px;flex-wrap:wrap;margin-bottom:16px;">';
	html += '<div style="flex:1;min-width:200px;background:#f8fafc;border-radius:10px;padding:14px 18px;border:1px solid #e2e8f0;">';
	html += '<div style="font-size:0.7rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.4px;margin-bottom:8px;">Purchase Info</div>';
	html += '<table class="table table-borderless table-sm mb-0" style="font-size:0.85rem;">';
	html += '<tr><td style="width:40%;font-weight:700;color:#64748b;">PV No</td><td style="font-weight:700;color:#1e40af;">' + pvNo + '</td></tr>';
	html += '<tr><td style="font-weight:700;color:#64748b;">Purchase Date</td><td style="font-weight:600;color:#1e293b;">' + date + '</td></tr>';
	html += '<tr><td style="font-weight:700;color:#64748b;">PO No</td><td style="font-weight:600;color:#1e293b;">' + poNo + '</td></tr>';
	html += '</table></div>';

	html += '<div style="flex:1;min-width:200px;background:#f8fafc;border-radius:10px;padding:14px 18px;border:1px solid #e2e8f0;">';
	html += '<div style="font-size:0.7rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.4px;margin-bottom:8px;">Vendor Info</div>';
	html += '<table class="table table-borderless table-sm mb-0" style="font-size:0.85rem;">';
	html += '<tr><td style="width:40%;font-weight:700;color:#64748b;">Vendor</td><td style="font-weight:600;color:#1e293b;">' + vendor + '</td></tr>';
	html += '<tr><td style="font-weight:700;color:#64748b;">Invoice No</td><td style="font-weight:600;color:#1e293b;">' + invoiceNo + '</td></tr>';
	html += '<tr><td style="font-weight:700;color:#64748b;">Invoice Date</td><td style="font-weight:600;color:#1e293b;">' + invoiceDate + '</td></tr>';
	html += '</table></div></div>';

	html += '<div style="background:#f8fafc;border-radius:10px;padding:14px 18px;border:1px solid #e2e8f0;margin-bottom:16px;">';
	html += '<div style="font-size:0.7rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.4px;margin-bottom:8px;">Items</div>';
	html += '<div style="font-size:0.85rem;">' + items + '</div>';
	html += '</div>';

	html += '<div style="display:flex;gap:20px;flex-wrap:wrap;">';
	html += '<div style="flex:1;background:#f8fafc;border-radius:10px;padding:14px 18px;border:1px solid #e2e8f0;">';
	html += '<div style="font-size:0.7rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.4px;margin-bottom:8px;">Tax</div>';
	html += '<div style="font-size:0.85rem;">' + tax + '</div>';
	html += '</div>';
	html += '<div style="flex:1;background:#ecfdf5;border-radius:10px;padding:14px 18px;border:1px solid #a7f3d0;">';
	html += '<div style="font-size:0.7rem;font-weight:700;color:#064e3b;text-transform:uppercase;letter-spacing:0.4px;margin-bottom:8px;">Total Amount</div>';
	html += '<div style="font-size:1.3rem;font-weight:800;color:#059669;">' + total + '</div>';
	html += '</div></div>';

	$('#purchaseViewBody').html(html);
	new bootstrap.Modal(document.getElementById('purchaseViewModal')).show();
}

function fnPurchaseDelete(pId)
{
	if (!confirm('Delete this Purchase?')) return;
    $.ajax({
		type: "POST",
		url: "api/crud_purchase_api.php",
		data: {action : "delete",pId : pId},
		success: function (response) {
			if(response == 'Failed')
			{
				toastr.error("Failed to delete!!!");
			}
			else{
				toastr.success("Deleted Successfully!!!");
				loadPurchase();
			}
		}
    });
}

function fnSavePurchase()
{
	var purchase_voucher_no = $('#purchase_voucher_no').val();
	var purchase_date = $('#purchase_date').val();
	var our_po_no = $('#our_po_no').val();
	var vendor_invoice_no = $('#vendor_invoice_no').val();
	var vendor_invoice_date = $('#vendor_invoice_date').val();
	var vendor_code = $('#vendor_code :selected').val();
	var igst_value = $('#igst_value').val();
	var cgst_value = $('#cgst_value').val();
	var sgst_value = $('#sgst_value').val();
	var discount_value = $("#discount_value").val();
	var total_after_discount_value = $("#net_total_value").val();
	var net_total_value = $("#net_total_value").val();

	$.ajax({
		type: "POST",
		url: "api/crud_purchase_api.php",
		data:{action : "add","purchase_voucher_no":purchase_voucher_no,
			  "purchase_date":purchase_date,
			  "our_po_no":our_po_no,
			  "vendor_invoice_no":vendor_invoice_no,
			  "vendor_invoice_date":vendor_invoice_date,
			  "vendor_code":vendor_code,
			  "igst_value":igst_value,
			  "cgst_value":cgst_value,
			  "sgst_value":sgst_value,
			  "discount_value":discount_value,
			  "total_after_discount_value":total_after_discount_value,
			  "net_total_value":net_total_value,
			  "allTableData":JSON.stringify(allTableData)},
		success: function (response) {
			if(response == "Failed")
			{
				toastr.error(response);
			}
			else
			{
				toastr.success("Successfully Saved!!!");
				loadPurchase();
				$('#purchaseDiv').show();
				$('#purchaseFilterBar').show();
				$('#addPurchaseDiv').hide();
			}
		}
	});
}

function loadPurchase() {
	var postData = { action: 'read' };
	var fromDt = $('#pm_from_date').val();
	var toDt = $('#pm_to_date').val();
	if (fromDt) postData.from_date = fromDt;
	if (toDt) postData.to_date = toDt;

	$.ajax({
		type: "POST",
		url: "api/crud_purchase_api.php",
		data: postData,
		success: function (response) {
			if(response != "")
			{
				$('#purchaseDiv').html(response);
				$('#resultPurchaseTable').DataTable({
					"lengthMenu": [[ 25, 50, 100, -1], [ 25, 50, 100, "All"]],
					"pageLength": -1,
					"order": [],
					language: {
						emptyTable: 'No purchase records found',
						info: 'Showing _START_ to _END_ of _TOTAL_ purchases',
						lengthMenu: 'Show _MENU_ entries'
					},
					dom: 'lBfrtip',
					buttons: [
						{
							extend: 'excelHtml5',
							text: '<span class="material-icons-round" style="font-size:15px;vertical-align:-3px;">table_view</span> Excel',
							title: 'Purchase Invoice Report',
							className: 'btn buttons-excel'
						},
						{
							extend: 'pdfHtml5',
							text: '<span class="material-icons-round" style="font-size:15px;vertical-align:-3px;">picture_as_pdf</span> PDF',
							className: 'btn buttons-pdf',
							orientation: 'portrait',
							pageSize: 'A4',
							title: 'Purchase Invoice Report'
						},
						{
							extend: 'print',
							text: '<span class="material-icons-round" style="font-size:15px;vertical-align:-3px;">print</span> Print',
							className: 'btn btn-secondary',
							customize: function (win) {
								$(win.document.body).css('font-size', '13px');
								$(win.document.body).find('table')
									.addClass('compact')
									.css('border-collapse', 'collapse')
									.find('thead th')
									.css({
										'background-color': '#f8fafc',
										'color': '#475569',
										'padding': '8px',
										'text-align': 'left',
										'font-size': '11px',
										'border-bottom': '2px solid #e2e8f0'
									});
								$(win.document.body).find('table').css('margin', '0 auto');
							}
						}
					],
					footerCallback: function (row, data, start, end, display) {
						let api = this.api();
						let intVal = function (i) {
							if (typeof i === 'string') {
								var n = i.replace(/[₹,\s]/g, '');
								return isNaN(n) ? 0 : parseFloat(n);
							}
							return typeof i === 'number' ? i : 0;
						};
						var total = api.column(8).data().reduce((a, b) => intVal(a) + intVal(b), 0);
					}
				});
			}
			else
			{
				toastr.error("Failed to get details!!!");
			}
		}
	});
}
</script>
