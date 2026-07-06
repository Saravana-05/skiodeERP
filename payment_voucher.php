<?php
session_start();
include_once "connect_db.php";
include_once "page_guard.php";
?>
<style>
.reportContent
{
	margin-top: 10px !important;
    border: 0px solid #ccc;
	border-radius: 4px;
	min-height:550px; 
	
}
.reportContent h5
{
	padding-left:5px;
} 
.reportContent .form-control,.reportContent .form-select
{
	border:1px solid #3a3a3a;
} 
div.dt-container div.dt-length select {
    width: 35%; 
}
div.dt-container div.dt-length label {
	width: 55%; 
}
.reportContent .iconImgCls {
    width: 50px;
} 
.reportContent .iconDivCls {
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
	line-height: 1.1em;
}
#resultDetailTable {
	width: 100% !important;
	table-layout: fixed;
	border-collapse: collapse;
}
#resultDetailTable th {
	padding: 8px 10px;
	text-align: center;
	font-size: 0.82rem;
	font-weight: 700;
	white-space: nowrap;
}
#resultDetailTable td {
	padding: 6px 10px;
	text-align: center;
	vertical-align: middle;
	font-size: 0.82rem;
	word-wrap: break-word;
	overflow-wrap: break-word;
}
#resultDetailTable th:nth-child(1) { width: 10%; }
#resultDetailTable th:nth-child(2) { width: 12%; }
#resultDetailTable th:nth-child(3) { width: 18%; }
#resultDetailTable th:nth-child(4) { width: 8%; }
#resultDetailTable th:nth-child(5) { width: 7%; }
#resultDetailTable th:nth-child(6) { width: 10%; }
#resultDetailTable th:nth-child(7) { width: 25%; }
#resultDetailTable th:nth-child(8) { width: 10%; }
#resultDetailTable td:nth-child(3) {
	text-align: left;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	max-width: 0;
}
#resultDetailTable td:nth-child(7) {
	text-align: left;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	max-width: 0;
}
.reportContent button
{
	margin-top: -5px;
}
.reportContent .col-form-label
{
	margin-left: 0px;
	font-weight:bold;
}
#reportResultDiv
{
	border:1px solid #cfcfcf;background:#fff;min-height:200px;font-size:0.8rem;
}
.reportContent>.headerDiv
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
</style>
<div class="bg_aliceblue p-1 m-1 pt-1 reportContent" >
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center p-1 headerDiv">
		<h5 class="mb-0 ">Payment Voucher</h5> 			
	</div>
	<?php
	$sql="SELECT max(voucher_no) as max_voucher_no FROM payment_voucher;";
	$voucher_no=1;
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{
			if($row=mysqli_fetch_array($query))
			{
				if(!is_null($row["max_voucher_no"]))
				{
					$voucher_no=$row["max_voucher_no"]+1;
				}
			}
		}
	}
	?>
	<div class="card mb-3" style="border:0px;" >
		<div class="card-header  text-light " style="border: 1px solid #0fa9c5;background: #0fa9c5 !important;padding-left: 10px;">Payment Voucher Entry</div>
		<div class="card-body p-2"  style="border: 1px solid #ccc;min-height: max-content;">
			<div class="row">
				<label for="staticEmail" class="col-md-2 col-form-label numericOnly">Voucher Number</label>
				<div class="col-md-2">
					<input type="text" autocomplete="off" class="form-control"   name="voucher_no_txt" id="voucher_no_txt" value="<?php echo $voucher_no;?>"  disabled  />
				</div>
				<label for="staticEmail" class="col-md-2 col-form-label">Voucher Date</label>
				<div class="col-md-2">
					<input type="text" autocomplete="off" class="form-control datepicker"   name="voucher_date_txt" id="voucher_date_txt" value="<?php echo date("d-m-Y");?>"    />
				</div>
				<label for="staticEmail" class="col-md-2 col-form-label  ">Ref No</label>
				<div class="col-md-2">
					<input type="text" autocomplete="off" class="form-control"   name="ref_no_txt" id="ref_no_txt" value=""    />
				</div>
			</div>
			<div class="row mt-1">
				<label for="staticEmail" class="col-md-2 col-form-label">Acc.Head</label>
				<div class="col-md-2">
					<select class="form-select" id="account_head_txt" >
					<option value="Select">Select</option>
					<?php
					if($row=mysqli_query($connection,"select *  from achead_master where actype in ('CASH','BANK')"))		
					{ 
						while($data=mysqli_fetch_array($row))
						{
							echo '<option value="'.$data["achead"].'">'.$data["acname"].'</option>';           
						}
					}
					?> 
					</select>
				</div>
			
				<label for="staticEmail" class="col-md-2 col-form-label">Vendor</label>
				<div class="col-md-2">
					<select class="form-select" id="customer_txt" onchange="customer_txt_change()">
					<option value="Select">Select</option>
					<?php
					if($row=mysqli_query($connection,"select *  from customer_master where customer_type like '%VENDOR%'"))		
					{ 
						while($data=mysqli_fetch_array($row))
						{
							echo '<option value="'.$data["customer_code"].'">'.$data["customer_name"].'</option>';           
						}
					}
					?> 
					</select>
				</div>
				<label for="staticEmail" class="col-md-2 col-form-label">Amount</label>
				<div class="col-md-2">
					<input type="text" autocomplete="off" class="form-control numericOnly"   name="amount_txt" id="amount_txt" value=""    />
				</div>
			</div>
			<div class="row mt-1">
				<label for="staticEmail" class="col-md-2 col-form-label">Comments</label>
				<div class="col-md-6">
					<input type="text" autocomplete="off" class="form-control"   name="description_txt" id="description_txt" value=""    />
				</div>
				<div class="col-md-2">	 
					<button type="button" class="btn btn-sm btn-warning" onclick="fnAdd()" >Add</button> 
				</div>
				<div class="col-md-2" style="text-align:right;">	 
					OutStanding : <span id="balance_txt" style="font-weight:bold;"></span> 
				</div>
			</div>  
		</div>
	</div>
	<div class="card mb-3" style="border:0px;" >
		<div class="card-header  text-light " style="border: 1px solid #0fa9c5;background: #0fa9c5 !important;padding-left: 10px;">Payment Voucher Report</div>
		<div class="card-body p-2"  style="border: 1px solid #ccc; min-height: max-content;">
			<div class="row">
				 <label for="date1Txt" class="col-md-2 col-form-label">From Date</label>
				 <div class="col-md-2">
					<input type="text" autocomplete="off" class="form-control datepicker"  id="fromDtTxt" name="fromDtTxt"  placeholder="dd-MMM-yyyy" value="<?php echo date("d-m-Y");?>" />
				 </div>
				 <label for="date1Txt" class="col-md-2 col-form-label">To Date</label>
				 <div class="col-md-2">
					<input type="text" autocomplete="off" class="form-control datepicker"  id="toDtTxt" name="toDtTxt"  placeholder="dd-MMM-yyyy" value="<?php echo date("d-m-Y");?>" />
				 </div>
				 
				 <div class="col-md-1">
					<button type="button" class="btn btn-sm btn-darkpurple" onclick="fnSearch()">Search</button> 
				 </div>
					
			</div>
			 
			<div id="reportResultDiv" class="p-2 mt-1" style="border:0px solid #cfcfcf;background:#e1e1e1;min-height:200px;">
				 
			</div>
		</div>
	</div>
</div>

<!-- View Payment Voucher Modal -->
<div class="modal fade" id="paymentViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border:none;border-radius:12px;overflow:hidden;">
      <div class="modal-header" style="background:#0fa9c5;color:#fff;padding:12px 18px;">
        <h6 class="modal-title mb-0" style="font-weight:700;"><span class="material-icons-round" style="font-size:20px;vertical-align:-4px;margin-right:6px;">payments</span>Payment Voucher Details</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="padding:20px 24px;" id="paymentViewBody"></div>
      <div class="modal-footer" style="padding:10px 18px;border-top:1px solid #e2e8f0;">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
	function customer_txt_change()
	{
	$.ajax({
		type: "POST",
		url: "api/get_customer_balance.php", 
		data: {achead_txt:$("#customer_txt").val()},
		success: function (response) {
			$("#balance_txt").text(response);
			
	}
	});
	}
$(document).ready(function() {
	SetUpBasics();
	$('.datepicker').removeClass('hasDatepicker').datepicker({
		<?php
			if($_SESSION["user_type"]=="OPERATOR")
			{ 
		?>
			minDate: -2,
		<?php
			}
		?>
		dateFormat: 'dd-mm-yy' ,
		showOn: "button",
		buttonImage: "img/calender.png", 
		buttonText: "Select date" 
	}); 

});
function printPaymentVoucherDiv(divId) {  
	var fromDt=$('#fromDtTxt').val();
	var toDt=$('#toDtTxt').val();
	var header='';
	
	header=header+'<style>*{font-size:12px;}th {border-bottom:1px solid #000;"}td {border-bottom:1px solid #eee;"}</style><div style="text-align:center;">';
	header=header+'<div style="text-align:center;font-size:1.2rem;font-weight:bold;">CITIZEN PRINTS</div>';
	header=header+'<div style="text-align:center;font-size:1.2rem;font-weight:bold;">VADAPALANI, CHENNAI - 600026, TAMILNADU</div>';
	header=header+'<div style="text-align:center;font-size:1.2rem;font-weight:bold;">PAYMENT VOUCHER REPORTS</div>';
	header=header+'<div style="text-align:center;font-size:1rem;font-weight:bold;">For Period : '+fromDt+' - '+toDt+'</div>';
	header=header+'</div><hr style="border: none;border-bottom: 5px solid black;">';
	var body='<div style="font-size:0.6vw;font-weight:normal;">'+$("#"+divId).html()+'</div>';
	var footer='';
	footer=footer+'<br><br><table id="summary_table" style="width:50%; border-collapse: collapse;border: 1px solid grey;"><tr style="border: 1px solid black;">';
	footer=footer+'<td style="text-align: center;"><h5>SUMMARY</h5></td>';	
	 
	footer=footer+'</tr><tr style="border: 1px solid black;"><td>';	
	footer=footer+$("#card_1").html();	
	footer=footer+'</td>';
	 
	footer=footer+'</tr></table>';
	var data='<div id="printContentDiv">'+header+body+footer+'</div>';	
$('#printContentDiv').html(	header+body+footer);
	printDiv(data);
	$('#printContentDiv').html(	"");
}
function fnSearch()
{
	var from = $('#fromDtTxt').val();
	var to = $('#toDtTxt').val();
	var account_head = "All";
	if (!from || !to) {
            toastr.error("Please select both dates.");
            return;
        }

	if (new Date(from) > new Date(to)) {
		toastr.error("From Date cannot be after To Date.");
		return;
	}  
	 
	$.ajax({
		type: "POST",
		url: "api/get_payment_voucher_report.php", 
		data: {type : "get",from : from,to:to,ah:account_head},
		success: function (response) {
			if(response != "")
			{  
		 
				$('#reportResultDiv').html('');
				$('#reportResultDiv').html(response); 
				/*$('#resultDetailTable').DataTable({  
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
					],
					 footerCallback: function (row, data, start, end, display) {
							let api = this.api();
					 
							// Remove the formatting to get integer data for summation
							let intVal = function (i) {
								return typeof i === 'string'
									? i.replace(/[\$,]/g, '') * 1
									: typeof i === 'number'
									? i
									: 0;
							};
					 
							// Total over all pages
							total = api
								.column(5)
								.data()
								.reduce((a, b) => intVal(a) + intVal(b), 0);
					 
							// Total over this page
							pageTotal = api
								.column(5, { page: 'current' })
								.data()
								.reduce((a, b) => intVal(a) + intVal(b), 0);
					 
							// Update footer
							api.column(5).footer().innerHTML =
								'Rs.' + pageTotal + '/-';
						}
			
				});
				*/
			}
			else
			{
				toastr.error("Failed to get details!!!");
			}
			
		} 
	});	
} 

function fnAdd()
{
	
	var voucher_no_txt = $('#voucher_no_txt').val();
	var voucher_date_txt = $('#voucher_date_txt').val();
	var ref_no_txt = $('#ref_no_txt').val();
	var account_head_txt = $('#account_head_txt :selected').val();
	var customer_code_txt = $('#customer_txt :selected').val();
	var customer_name_txt = $('#customer_txt :selected').text();
	var description_txt = $('#description_txt').val();
	var amount_txt = $('#amount_txt').val();
	if (voucher_no_txt == "") {
		toastr.error("Enter Voucher Number");
		return;
	}
	if (voucher_date_txt == "") {
		toastr.error("Enter Voucher Date");
		return;
	}
	/*if (ref_no_txt == "") {
		toastr.error("Enter Ref. No");
		return;
	}*/
	if (account_head_txt == "Select") {
		toastr.error("Select Account Head");
		return;
	} 
	if (customer_txt == "Select") {
		toastr.error("Select Vendor");
		return;
	} 
	if (amount_txt == "") {
		toastr.error("Enter Amount");
		return;
	} 
	 
	$.ajax({
		type: "POST",
		url: "api/get_payment_voucher_report.php", 
		data: {type : "add",voucher_no : voucher_no_txt,voucher_date:voucher_date_txt,
		ref_no:ref_no_txt,account_head:account_head_txt,customer_code:customer_code_txt,
		customer_name:customer_name_txt,description:description_txt,amount:amount_txt
		},
		success: function (response) {
			if(response == "success")
			{
				toastr.success("Saved Successfully!!!");
				$('#voucher_no_txt').val('');
				$('#voucher_date_txt').val('');
				$('#ref_no_txt').val('');
				$('#account_head_txt').val('Select'); 
				$('#customer_txt').val('Select');
				$('#description_txt').val('');
				$('#amount_txt').val('');
				//fnSearch();
				fnSideMenu(3);
			}
			else
			{
				toastr.error("Failed to save!!!");
			}
		}
	});
}

function fnPaymentView(btn) {
	var $tr = $(btn).closest('tr');
	var cells = $tr.find('td');
	var date = $(cells[0]).text();
	var accHead = $(cells[1]).text();
	var vendor = $(cells[2]).text();
	var refNo = $(cells[3]).text();
	var vhNo = $(cells[4]).text();
	var amount = $(cells[5]).text();
	var comments = $(cells[6]).text();
	var html = '<table class="table table-borderless mb-0" style="font-size:0.9rem;">';
	html += '<tr><td style="width:35%;font-weight:700;color:#64748b;">Voucher No</td><td style="font-weight:600;color:#1e293b;">' + vhNo + '</td></tr>';
	html += '<tr><td style="font-weight:700;color:#64748b;">Date</td><td style="font-weight:600;color:#1e293b;">' + date + '</td></tr>';
	html += '<tr><td style="font-weight:700;color:#64748b;">Account Head</td><td style="font-weight:600;color:#1e293b;">' + accHead + '</td></tr>';
	html += '<tr><td style="font-weight:700;color:#64748b;">Vendor Name</td><td style="font-weight:600;color:#1e293b;">' + vendor + '</td></tr>';
	html += '<tr><td style="font-weight:700;color:#64748b;">Ref No</td><td style="font-weight:600;color:#1e293b;">' + (refNo || '-') + '</td></tr>';
	html += '<tr><td style="font-weight:700;color:#64748b;">Amount</td><td style="font-weight:700;color:#dc2626;font-size:1.1rem;">₹ ' + amount + '</td></tr>';
	html += '<tr><td style="font-weight:700;color:#64748b;">Comments</td><td style="font-weight:500;color:#1e293b;">' + (comments || '-') + '</td></tr>';
	html += '</table>';
	$('#paymentViewBody').html(html);
	new bootstrap.Modal(document.getElementById('paymentViewModal')).show();
}

function fnPaymentDelete(pId)
{
	if (!confirm('Delete this Payment Voucher?')) return;
    $.ajax({
		type: "POST",
		url: "api/get_payment_voucher_report.php", 
		data: {type : "del",v_no : pId},
		success: function (response) {
			if(response == 'Failed')
			{
				toastr.error("Failed to delete!!!");
			}
			else{
				toastr.success("Deleted Successfully!!!");
				fnSearch();
			}
		}
    });
}
</script>