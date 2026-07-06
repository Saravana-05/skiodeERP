<?php
include_once 'connect_db.php';
include_once "page_guard.php";
?>
<style>
.mainDivContent
{
	margin-top: 20px !important;
    border: 2px solid #302f2f;
	border-radius: 4px;
}
.mainDivContent h4
{
	margin-top: -20px; 
    background: white;
	font-size:1.2rem;
}
.jobCardListDiv td
{
	padding:0;
}
div.dt-container div.dt-length select {
    width: 35%; 
}
div.dt-container div.dt-length label {
	width: 55%; 
}
#jobcardDetailTable td,#jobcardDetailTable th{
	padding:3px;
}
#jobcardDetailTable th{
	text-align:center;
}
</style>
<div class="mt-2">
	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)" onclick="fnSideMenu(1)">Dashboard</a></li>
		<li class="breadcrumb-item active" aria-current="page">Job Card Closing</li>
	  </ol>
	</nav>
	</div>
<div class="bg_aliceblue p-3 m-1 pt-0 mainDivContent">
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-2 mb-1">
		<h4 class="mb-0">Job Card Closing</h4> 			
	</div>
	
	<div class="jobCardListDiv">
		 
			
	</div>
	
</div>

<div class="modal fade modal-xl" id="jobcardCloseModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success  text-white">
        <h5 class="modal-title" id="exampleModalLabel">Job Card Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
			<input type="hidden" class="form-control"   name="jobcard_no_txt" id="jobcard_no_txt" value="" />
			<input type="hidden" class="form-control"   name="jobcard_date_txt" id="jobcard_date_txt" value="" />
			<input type="hidden" class="form-control"   name="cust_type_txt" id="cust_type_txt" value="" />
			<div class="row">
				<label for="staticEmail" class="col-md-3 col-form-label">App. Amount</label>
				 
				<label for="staticEmail" class="col-md-3 col-form-label">Adv. Gpay</label>
				 
				<label for="staticEmail" class="col-md-3 col-form-label">Adv. Cash</label>
				 	 
				<label for="staticEmail" class="col-md-3 col-form-label">Remaining Balance</label> 			
			</div>
			<div class="row">
				<div class="col-md-3">
					<input type="text" autocomplete="off" class="form-control" disabled name="app_amount_txt" id="app_amount_txt" value="" />
				</div>  
				<div class="col-md-3">
					<input type="text" autocomplete="off" class="form-control" disabled name="advance_gpay_txt" id="advance_gpay_txt" value="" />
				</div> 
				<div class="col-md-3">
					<input type="text" autocomplete="off" class="form-control" disabled name="advance_cash_txt" id="advance_cash_txt" value="" />
				</div> 
				<div class="col-md-3">
					<input type="text" autocomplete="off" class="form-control" disabled name="remaining_balance_txt" id="remaining_balance_txt" value="" />
				</div> 			 
				 			
			</div>
			
			<!-- Payment Mode Row -->
			<div class="row m-0 p-0 mt-2 wgDivCls" id="paymentModeRow">
				<div class="col-md-3">
					<label class="col-form-label fw-bold">Payment Mode</label>
					<select id="bal_pay_mode" class="form-select" onchange="fnBalPayModeChange()">
						<option value="">-- Select --</option>
						<option value="Cash">Cash</option>
						<option value="Online">Online / UPI</option>
					</select>
				</div>
				<div class="col-md-3">
					<label class="col-form-label">Discount</label>
					<input type="text" autocomplete="off" class="form-control" id="discount_txt" name="discount_txt" style="text-align:right;" onkeyup="fnDiscountChanged()" onchange="fnDiscountChanged()"/>
					<label class="col-form-label">Balance to Pay</label>
					<input type="text" autocomplete="off" class="form-control" id="balance_received_total_txt" name="balance_received_total_txt" style="text-align:right;" disabled/>
					<!-- Online amount input — shown only when Online/UPI is selected -->
					<div id="online_amt_section" style="display:none;margin-top:6px;">
						<label class="col-form-label">Online Amount</label>
						<input type="text" autocomplete="off" class="form-control numericOnly"
						       id="balance_gpay_txt" name="balance_gpay_txt"
						       style="text-align:right;" placeholder="Enter amount"
						       oninput="fnOnlineAmtChanged()" onchange="fnOnlineAmtChanged()"/>
						<button type="button" class="btn btn-sm btn-primary w-100 mt-1"
						        id="generateCloseQrBtn" onclick="fnGenerateCloseQRManual()"
						        style="display:none;">&#9654; Generate QR</button>
					</div>
				</div>
				<div class="col-md-6" id="qr_close_panel" style="display:none;text-align:center;border:1px solid #ddd;border-radius:6px;padding:8px;">
					<div id="qr_close_code" style="min-height:60px;"></div>
					<div id="qr_close_waiting" style="display:none;color:#555;font-size:12px;">
						<div style="display:inline-block;width:14px;height:14px;border:2px solid #ccc;border-top-color:#D73D32;border-radius:50%;animation:qr-spin 0.8s linear infinite;vertical-align:middle;margin-right:4px;"></div>
						Waiting for UPI payment...
					</div>
					<div id="qr_close_success" style="display:none;background:#e6f9ed;border:2px solid #27ae60;border-radius:6px;padding:8px;color:#1a7a3a;font-weight:bold;">&#10003; Payment Received!</div>
					<div id="qr_close_failed" style="display:none;color:#c0392b;">
						&#10007; Payment Failed
						<button class="btn btn-sm btn-danger ms-2" onclick="fnRetryCloseQR()">&#8635; Retry</button>
					</div>
				</div>
			</div>
			<!-- Hidden field for cash (online amount is now a visible input above) -->
			<input type="hidden" id="balance_cash_txt" name="balance_cash_txt" value="0"/>
			<div class=" row  m-0 p-0">
				<div class="form-check col-md-3 p-0" id="quotationDiv">
					  &nbsp;<input class="form-check-input" type="radio" name="convert_job_card_type" id="convert_to_quotation_txt" value="quotation" style="margin-left: 2px;margin-top: 6px;" />
					  <label class="col-form-label" for="convert_to_quotation_txt">
						Convert to Quotation
					  </label>
				</div>
				<div class="form-check col-md-3 p-0 wgDivCls" id="invoiceDiv">
				  <input class="form-check-input" type="radio" name="convert_job_card_type" id="convert_to_invoice_txt" value="invoice" style="margin-left: 2px;margin-top: 6px;margin-right:5px;" />
				  <label class="col-form-label" for="convert_to_invoice_txt">
					Convert to Invoice
				  </label>
				</div>	
			</div>
			 
			<div class=" row  m-0 p-0">
				<div id="materialUsageSpilageDetailDiv" class="col-6 p-1"  >				 
				</div>	
				<div id="machineUsageSpilageDetailDiv" class="col-6 p-1"  >				 
				</div>	
			</div>
			
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <!-- Shown only when Online/UPI payment mode is selected -->
        <button type="button" class="btn btn-warning text-dark" id="holdForQrBtn"
                style="display:none;"
                onclick="fnParkCloseJCForQR()"
                title="Hold this Job Card — it will auto-convert to SQ once the QR payment is received">
          ⏳ Hold for QR Payment
        </button>
        <button type="button" class="btn btn-danger" onclick="fnCloseJobCard()">Close Job Card</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-lg" id="viewMachineModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info  text-white">
        <h5 class="modal-title" id="exampleModalLabel">View Machine Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
			<div id="machineUsageDetailDiv">				 
			</div>			
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-lg" id="viewMaterialModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning  text-white">
        <h5 class="modal-title" id="exampleModalLabel">View Material Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
			<div id="materialUsageDetailDiv">				 
			</div>			
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
	SetUpBasics();
	fnLoadJCTable();
});
function fnLoadJCTable()
{
	$.ajax({
		type: "POST",
		url: "api/load_jobcard_details.php", 
		success: function (response) {
			if(response != "")
			{  
		 
				 $('.jobCardListDiv').html('');
				  $('.jobCardListDiv').html(response); 
				 var table = $('#jobcardDetailTable').DataTable({  "lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]] });
			}
			else
			{
				toastr.error("Failed to get details!!!");
			}
			
		} 
	});	
}
function fnCloseJCPopUp(pJobCardNo,pCustType)
{
	// Reset payment section
	$('#bal_pay_mode').val('');
	$('#discount_txt').val('');
	$('#balance_received_total_txt').val('');
	$('#balance_cash_txt').val('0');
	$('#balance_gpay_txt').val('0');
	$('#qr_close_panel').hide();
	$('#qr_close_code').html('');
	$('#qr_close_waiting').hide();
	$('#qr_close_success').hide();
	$('#qr_close_failed').hide();
	_closeStopPolling();
	_closePaymentStatus = null;

	$('#holdForQrBtn').hide();
	_closeCurrentExtId = null;
	$('#jobcardCloseModal').modal('show');
	$.ajax({
		type: "POST",
		url: "api/get_jobcard_details.php?type=jc&job_card_no="+pJobCardNo,
		success: function (response) {
			if(response != "")
			{
				var json = response[0];
				
				$('#jobcard_no_txt').val(pJobCardNo);
				$('#app_amount_txt').val(json["approximate_amount"]);
				$('#advance_gpay_txt').val(json["advance_gpay"]);
				$('#advance_cash_txt').val(json["advance_cash"]);
				$('#jobcard_date_txt').val(json["jobcard_date"]);
				$('#cust_type_txt').val(json["customer_type"]);
				 
				var  remaining_balance_txt = parseFloat(json["approximate_amount"])-(parseFloat(json["advance_gpay"]) + parseFloat(json["advance_cash"]));
				$('#remaining_balance_txt').val(remaining_balance_txt);
				
				if(pCustType == 'Credit')
				{
					$('.wgDivCls').hide();
				}
				else
				{
					$('.wgDivCls').show();
				}
			}
			else
			{
				toastr.error("Failed to get details!!!");
			}
			
		} 
	});	
	$.ajax({
		type: "POST",
		url: "api/get_jobcard_details.php?type=mat&job_card_no="+pJobCardNo,
		async:false,		
		success: function (data) {
			if(data != "")
			{
				$('#materialUsageSpilageDetailDiv').html('');
				rows = '<center><h6 class="bg-warning p-1">Material Details</h6></center><table class="table table-bordered table-hover" id="materialUsageSpilageTable">';
				rows += '  <thead class="table-dark">';
				rows += ' <tr><th>SNo</th><th>Material Name</th><th>Usage</th> <th>Spilage</th></tr>';
				rows += '</thead><tbody>'; 
				for(var i=0;i<data.length;i++)
				{ 
					rows += ' <tr>';  
					rows += '     <td>'+ (i+1) +'</td>'; 
					rows += '     <td>'+data[i].material_code +'</td>'; 
					rows += '     <td>'+data[i].usage_count+'</td>'; 
					rows += '     <td>'; 
					rows += '       <input type="text" autocomplete="off" name="material_spilage_'+i+'" id="material_spilage_'+i+'" class="form-control" />';  
					rows += '     </td>'; 
					rows += '   </tr>';  
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
				rows += ' <tr><th>SNo</th><th>Machine Name</th><th>Usage</th> <th>Spilage</th></tr>';
				rows += '</thead><tbody>'; 
				for(var i=0;i<data.length;i++)
				{ 
					rows += ' <tr>';  
					rows += '     <td>'+ (i+1) +'</td>'; 
					rows += '     <td>'+data[i].machine_code +'</td>'; 
					rows += '     <td>'+data[i].usage_count+'</td>'; 
					rows += '     <td>'; 
					rows += '       <input type="text" autocomplete="off" name="machine_spilage_'+i+'" id="material_spilage_'+i+'" class="form-control" />';  
					rows += '     </td>'; 
					rows += '   </tr>';  
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
}

function fnMachineUsagePopUp(pJobCardNo)
{
	$('#viewMachineModal').modal('show');
	$.ajax({
		type: "POST",
		url: "api/get_machine_usage_details.php?job_card_no="+pJobCardNo, 
		success: function (response) {
			if(response != "")
			{ 
				$('#machineUsageDetailDiv').html('');
				$('#machineUsageDetailDiv').html(response); 
			}
			else
			{
				toastr.error("Failed to get details!!!");
			}
			
		} 
	});	
}

function fnMaterialUsagePopUp(pJobCardNo)
{
	$('#viewMaterialModal').modal('show');
	$.ajax({
		type: "POST",
		url: "api/get_material_usage_details.php?job_card_no="+pJobCardNo, 
		success: function (response) {
			if(response != "")
			{ 
				$('#materialUsageDetailDiv').html('');
				$('#materialUsageDetailDiv').html(response); 
			}
			else
			{
				toastr.error("Failed to get details!!!");
			}
			
		} 
	});	
}

function fnCloseJobCard()
{
	var close_job_card_type="";
	if($('input[name="convert_job_card_type"]').prop('checked'))
	{
		close_job_card_type=$("input[name='convert_job_card_type']:checked").val();
	}
	var jobcard_no    = $('#jobcard_no_txt').val();
	var jobcard_date  = $('#jobcard_date_txt').val();
	var cust_type     = $('#cust_type_txt').val();
	var remaining_balance = parseFloat($('#remaining_balance_txt').val()) || 0;
	var discount      = parseFloat($('#discount_txt').val()) || 0;
	var balance_gpay  = parseFloat($('#balance_gpay_txt').val()) || 0;
	var balance_cash  = parseFloat($('#balance_cash_txt').val()) || 0;
	var balance_received_total = balance_gpay + balance_cash;
	var total_amount  = remaining_balance - balance_gpay - balance_cash - discount;
	var pay_mode      = $('#bal_pay_mode').val();
	var is_bal_in_cash = (pay_mode === 'Cash') ? 1 : 0;
	var is_bal_in_gpay = (pay_mode === 'Online') ? 1 : 0;

	if(cust_type != "Credit")
	{
		if(!pay_mode) { toastr.error("Select a payment mode."); return; }
		if(pay_mode === 'Online' && _closePaymentStatus !== 'SUCCESS') {
			toastr.error("UPI payment not confirmed yet. Please complete the payment.");
			return;
		}
		if(total_amount > 0.01)
		{
			toastr.error("Pay balance amount completely");
			return;
		}
		// Full payment is now confirmed. For WalkIn customers auto-select
		// "Convert to Quotation" — it is the only valid option for them and
		// requiring a manual tick after payment is already verified is pointless.
		if(cust_type === 'WalkIn')
		{
			$('#convert_to_quotation_txt').prop('checked', true);
		}
		if(!$('#convert_to_quotation_txt').prop('checked'))
		{
			toastr.error("Select convert type!!!");
			return;
		}
	}
	else
	{
		if(!$('#convert_to_quotation_txt').prop('checked') && !($('#convert_to_invoice_txt').prop('checked')))
		{
			toastr.error("Select convert type!!!");
			return;
		}
	}
	
	var materialUsageSpilageArr =[];
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
	});
	 
	$.ajax({
		type: "POST",
		url: "api/save_job_card_close_details.php", 
		data:{"jobcard_no":jobcard_no,
			  "cust_type" : cust_type,
			  "close_job_card_type":close_job_card_type,
			  "is_bal_in_cash":is_bal_in_cash,
			  "is_bal_in_gpay":is_bal_in_gpay,
			  "balance_cash":balance_cash,
			  "balance_gpay":balance_gpay,
			  "discount":discount,
			  "balance_received_total":balance_received_total,
			  "material_spillage_details": JSON.stringify(materialUsageSpilageArr),
			  "machine_spillage_details": JSON.stringify(machineUsageSpilageArr)
		},
		success: function (response) {
			if(response != "Failed")
			{ 
				toastr.success('Successfully closed Job Card' );
				$('#jobcardCloseModal').modal('hide');
				fnLoadJCTable();
			}
			else
			{
				toastr.error("Failed to close job card!!!");
			}
			
		} 
	});
}

// ── Payment mode & QR for job card closing ──────────────────────────────────
var _closePaymentStatus = null;
var _closePollingTimer  = null;
var _closeCurrentExtId  = null;   // ext_transaction_id of the active balance QR

function _closeStopPolling() {
	if (_closePollingTimer) { clearInterval(_closePollingTimer); _closePollingTimer = null; }
}

function fnCalcAmtToPay() {
	var remaining = parseFloat($('#remaining_balance_txt').val()) || 0;
	var discount  = parseFloat($('#discount_txt').val()) || 0;
	return Math.max(0, remaining - discount);
}

function fnDiscountChanged() {
	if ($('#bal_pay_mode').val()) fnBalPayModeChange();
}

function fnBalPayModeChange() {
	var mode     = $('#bal_pay_mode').val();
	var amtToPay = fnCalcAmtToPay();
	$('#balance_received_total_txt').val(amtToPay.toFixed(2));

	if (mode === 'Cash') {
		$('#balance_cash_txt').val(amtToPay.toFixed(2));
		$('#balance_gpay_txt').val('0');
		$('#qr_close_panel').hide();
		$('#holdForQrBtn').hide();
		_closeStopPolling();
		_closePaymentStatus = null;
		_closeCurrentExtId = null;
	} else if (mode === 'Online') {
		$('#balance_cash_txt').val('0');
		$('#balance_gpay_txt').val(amtToPay.toFixed(2));
		$('#qr_close_panel').show();
		$('#holdForQrBtn').hide();   // shown only after QR is successfully generated
		fnGenerateCloseQR(amtToPay);
	} else {
		$('#balance_cash_txt').val('0');
		$('#balance_gpay_txt').val('0');
		$('#qr_close_panel').hide();
		$('#holdForQrBtn').hide();
		_closeStopPolling();
		_closePaymentStatus = null;
		_closeCurrentExtId = null;
	}
}

function fnRetryCloseQR() { fnGenerateCloseQR(fnCalcAmtToPay()); }

function fnGenerateCloseQR(amount) {
	if (amount <= 0) { toastr.warning("No balance to pay."); return; }
	_closeStopPolling();
	_closePaymentStatus = null;
	$('#holdForQrBtn').hide();   // hide until QR is ready
	$('#qr_close_code').html('<div style="font-size:10px;color:#888;padding:6px;">Loading...</div>');
	$('#qr_close_waiting').hide();
	$('#qr_close_success').hide();
	$('#qr_close_failed').hide();

	$.ajax({
		url: 'api/canara_qr_generate.php',
		type: 'POST',
		data: { amount: amount.toFixed(2), jobcard_no: $('#jobcard_no_txt').val() },
		dataType: 'json',
		success: function(res) {
			if (!res.success) {
				if (res.already_paid) {
					_closePaymentStatus = 'SUCCESS';
					$('#qr_close_code').html('<div style="background:#27ae60;color:#fff;font-size:11px;font-weight:bold;border-radius:6px;padding:8px;text-align:center;">&#10003;<br>PAID</div>');
					$('#qr_close_success').show();
					toastr.info(res.error);
				} else {
					$('#qr_close_code').html('');
					toastr.error("QR Error: " + (res.error || "Unknown"));
				}
				return;
			}
			_closeCurrentExtId = res.transaction_id;   // save for "Hold for QR Payment"
			$('#qr_close_code').html('');
			new QRCode(document.getElementById('qr_close_code'), {
				text: res.qr_string, width: 130, height: 130,
				colorDark: "#000000", colorLight: "#ffffff",
				correctLevel: QRCode.CorrectLevel.M
			});
			$('#holdForQrBtn').show();   // QR is ready — allow parking
			$('#qr_close_waiting').show();

			_closePollingTimer = setInterval(function() {
				$.getJSON('api/canara_qr_status.php', { ext_id: res.transaction_id }, function(sr) {
					if (sr.status === 'SUCCESS') {
						_closeStopPolling();
						_closePaymentStatus = 'SUCCESS';
						$('#qr_close_waiting').hide();
						$('#qr_close_failed').hide();
						$('#qr_close_success').show();
						$('#qr_close_code').html('<div style="background:#27ae60;color:#fff;font-size:11px;font-weight:bold;border-radius:6px;padding:8px;text-align:center;">&#10003;<br>PAID</div>');
						toastr.success("UPI payment received!");
					} else if (sr.status === 'FAILED') {
						_closeStopPolling();
						_closePaymentStatus = 'FAILED';
						$('#qr_close_waiting').hide();
						$('#qr_close_success').hide();
						$('#qr_close_failed').show();
					}
				});
			}, 3000);

			setTimeout(function() {
				if (_closePollingTimer) {
					_closeStopPolling();
					if ($('#qr_close_success').is(':hidden')) {
						$('#qr_close_waiting').hide(); $('#qr_close_failed').show();
					}
				}
			}, 300000);
		},
		error: function() {
			$('#qr_close_code').html('');
			toastr.error("Failed to generate QR code. Check network.");
		}
	});
}

$('#jobcardCloseModal').on('hidden.bs.modal', function() {
	_closeStopPolling();
	_closePaymentStatus = null;
	_closeCurrentExtId  = null;
	$('#holdForQrBtn').hide();
});

// ── Park this JC so it auto-converts when QR payment is received ─────────────
function fnParkCloseJCForQR() {
	var jcNo    = $('#jobcard_no_txt').val();
	var approx  = parseFloat($('#app_amount_txt').val())             || 0;
	var balGpay = parseFloat($('#balance_received_total_txt').val()) || 0;
	var disc    = parseFloat($('#discount_txt').val())               || 0;

	if (!jcNo) { toastr.error("No Job Card loaded."); return; }
	if (balGpay <= 0) { toastr.error("Online payment amount is zero."); return; }

	$('#holdForQrBtn').prop('disabled', true).text('⏳ Parking…');

	$.ajax({
		type    : 'POST',
		url     : 'api/save_pending_qr_closure.php',
		dataType: 'json',
		data: {
			jcnos:                  jcNo,
			approximate_amount_txt: approx,
			balance_gpay_txt:       balGpay,
			balance_cash_txt:       0,
			balance_amount_txt:     balGpay,
			discount_txt:           disc,
			is_bal_in_cash_txt:     0,
			is_bal_in_gpay_txt:     1,
			is_gst_extra_txt:       0,
			gst_tax_amount_txt:     0,
			ext_transaction_id:     _closeCurrentExtId || ''
		},
		success: function(res) {
			if (res.success) {
				toastr.success('Job Card ' + jcNo + ' placed on hold — will auto-convert to SQ when payment is received.');
				$('#jobcardCloseModal').modal('hide');
				fnLoadJCTable();   // refresh the job card closing list
			} else {
				toastr.error('Failed to park Job Card: ' + (res.error || 'Unknown error'));
				$('#holdForQrBtn').prop('disabled', false).text('⏳ Hold for QR Payment');
			}
		},
		error: function() {
			toastr.error('Network error. Please try again.');
			$('#holdForQrBtn').prop('disabled', false).text('⏳ Hold for QR Payment');
		}
	});
}
</script>