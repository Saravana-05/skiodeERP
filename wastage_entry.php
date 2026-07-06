<?php
session_start();
include_once "connect_db.php";
include_once "page_guard.php";
?> 
<link rel="stylesheet" media="all" type="text/css" href="css/jquery-ui-timepicker-addon.css">
<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
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
#resultDetailTable td,#resultDetailTable th{
	padding:3px;
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
		<h5 class="mb-0 ">Wastage Entry</h5> 			
	</div>
	<?php
	/*$sql="SELECT max(voucher_no) as max_voucher_no FROM receipt_voucher;";
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
	}*/
	?>
	<div class="card mb-3" style="border:0px;" >
		<div class="card-header  text-light " style="border: 1px solid #0fa9c5;background: #0fa9c5 !important;padding-left: 10px;">Wastage Entry</div>
		<div class="card-body p-1"  style="border: 1px solid #ccc;min-height: max-content;">
			<div class="row"> 
				<label for="staticEmail" class="col-md-2 col-form-label">Wastage Date</label>
				<div class="col-md-2">
					<input type="text" autocomplete="off" class="form-control dateTimepicker"   name="wastage_date_txt" id="wastage_date_txt" value="<?php echo date("d-m-Y");?>"    />
				</div>
				<label for="staticEmail" class="col-md-2 col-form-label  ">Ref No</label>
				<div class="col-md-2">
					<input type="text" autocomplete="off" class="form-control numericOnly"   name="ref_no_txt" id="ref_no_txt" value=""    />
				</div>
				<label for="staticEmail" class="col-md-2 col-form-label">Machine</label>
				<div class="col-md-2">
					<select class="form-select" id="machine_id_txt" >
					<option value="Select">Select</option>
					<?php
					if($row=mysqli_query($connection,"select *  from machine_master"))		
					{ 
						while($data=mysqli_fetch_array($row))
						{
							echo '<option value="'.$data["machine_code"].'">'.$data["machine_name"].'</option>';           
						}
					}
					?> 
					</select>
				</div>
			</div>
			<div class="row mt-1">
				<label for="staticEmail" class="col-md-2 col-form-label">Material</label>
				<div class="col-md-2">
					<select class="form-select" id="material_code_txt" >
					<option value="Select" purchase_price="0">Select</option>
					<?php
					if($row=mysqli_query($connection,"select *  from material_master"))		
					{ 
						while($data=mysqli_fetch_array($row))
						{
							echo '<option value="'.$data["material_code"].'" purchase_price="'.$data["purchase_price"].'">'.$data["material_name"].'</option>';           
						}
					}
					?> 
					</select>
					<span id="material_value_txt">0</span>
				</div>
			
				<label for="staticEmail" class="col-md-2 col-form-label">Qty</label>
				<div class="col-md-2">
					<input type="text" autocomplete="off" class="form-control numericOnly"   name="qty_txt" id="qty_txt" value=""    />
				</div>
				<label for="staticEmail" class="col-md-2 col-form-label">Value</label>
				<div class="col-md-2">
					<input type="text" autocomplete="off" disabled class="form-control numericOnly"   name="value_txt" id="value_txt" value=""    />
				</div>
			</div>
			<div class="row mt-1">
				<label for="staticEmail" class="col-md-2 col-form-label">Created by</label>
				<div class="col-md-2">
					 
					<select class="form-select" id="created_by_txt" >
					
					<?php
					if($row=mysqli_query($connection,"select *  from user_master"))		
					{ 
						while($data=mysqli_fetch_array($row))
						{
							echo '<option value="'.$data["user_name"].'">'.$data["user_display_name"].'</option>';           
						}
					}
					?> 
					</select>
				</div>
				<label for="staticEmail" class="col-md-2 col-form-label">Remarks</label>
				<div class="col-md-4">
					<input type="text" autocomplete="off" class="form-control"   name="remarks_txt" id="remarks_txt" value=""    />
				</div>
				<div class="col-md-2">	 
					<button type="button" class="btn btn-sm btn-warning mt-1" onclick="fnAdd()" >Add</button> 
				</div>
			</div>  
		</div>
	</div>
	<div class="card mb-3" style="border:0px;" >
		<div class="card-header  text-light " style="border: 1px solid #0fa9c5;background: #0fa9c5 !important;padding-left: 10px;">Wastage Report</div>
		<div class="card-body p-2"  style="border: 1px solid #ccc; min-height: max-content;">
			<div class="row">
				 <label for="date1Txt" class="col-md-2 col-form-label">From Date</label>
				 <div class="col-md-2">
					<input type="text" autocomplete="off" class="form-control datepicker"  id="fromDtTxt" name="fromDtTxt"  placeholder="dd-MMM-yyyy" value="<?php echo date("d-m-Y");?>" />
				 </div>
				 <label for="date1Txt" class="col-md-1 col-form-label">To Date</label>
				 <div class="col-md-2">
					<input type="text" autocomplete="off" class="form-control datepicker"  id="toDtTxt" name="toDtTxt"  placeholder="dd-MMM-yyyy" value="<?php echo date("d-m-Y");?>" />
				 </div>
				<!-- <label for="date1Txt" class="col-md-2 col-form-label">Account Head</label>
				 <div class="col-md-2">
					<select class="form-select" id="account_head" >
						<option value="All">All</option>
						<?php
						if($row=mysqli_query($connection,"select *  from achead_master"))		
						{ 
							while($data=mysqli_fetch_array($row))
							{
								echo '<option value="'.$data["achead"].'">'.$data["acname"].'</option>';           
							}
						}
						?> 
					</select>
				 </div>-->
				 <div class="col-md-1">
					<button type="button" class="btn btn-sm btn-darkpurple" onclick="fnSearch()">Search</button> 
				 </div>
					
			</div>
			 
			<div id="reportResultDiv" class="p-2 mt-1" style="border:0px solid #cfcfcf;background:#e1e1e1;min-height:200px;">
				 
			</div>
		</div>
	</div>
</div>
<script>
function calc_value_txt()
{
	
	material_value=parseFloat($('#material_value_txt').text());
	qty_txt=parseFloat($('#qty_txt').val());
	if(isNaN(material_value)) material_value=0.0;
	if(isNaN(qty_txt)) qty_txt=0.0;
	net_value=qty_txt*material_value;
	net_value=net_value.toFixed(2);
	$('#value_txt').val(net_value);
	
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
	$('.dateTimepicker').datetimepicker({
    dateFormat: 'dd-mm-yy', // Example date format
    timeFormat: 'hh:mm'  // Example time format
}); 
$('#qty_txt').on("input",function() { 
	calc_value_txt();
}); 

$('#material_code_txt').change(function() { 
	
    var selectedValue = $('option:selected', this).attr('purchase_price');
	$('#material_value_txt').text(selectedValue); 
	//console.log("material_code_changed");
	calc_value_txt();
    // Add your desired actions here
  });
});

function printWastageEntryDiv(divId) {  
	var fromDt=$('#fromDtTxt').val();
	var toDt=$('#toDtTxt').val();
	var header='';
	
	header=header+'<style>*{font-size:12px;}th {border-bottom:1px solid #000;"}td {border-bottom:1px solid #eee;"}</style><div style="text-align:center;">';
	header=header+'<div style="text-align:center;font-size:1.2rem;font-weight:bold;">PRINTZY</div>';
	header=header+'<div style="text-align:center;font-size:1.2rem;font-weight:bold;">VADAPALANI, CHENNAI - 600026, TAMILNADU</div>';
	header=header+'<div style="text-align:center;font-size:1.2rem;font-weight:bold;">WASTAGE ENTRY REPORTS</div>';
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
		url: "api/get_wastage_report.php", 
		data: {type : "get",from : from,to:to },
		success: function (response) {
			if(response != "")
			{  
		 
				$('#reportResultDiv').html('');
				$('#reportResultDiv').html(response); 
				/*$('#resultDetailTable').DataTable({  
					"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]] ,
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
							title: 'Wastage Report'
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
								  pageTotal ; //+ '/-';
						}
			
				});*/
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
	var wastage_date = $('#wastage_date_txt').val();
	var ref_no = $('#ref_no_txt').val();
	var machine_code = $('#machine_id_txt :selected').val();
	var material_code = $('#material_code_txt :selected').val(); 
	var qty = $('#qty_txt').val();
	var remarks = $('#remarks_txt').val();
	var value = $('#value_txt').val();
	var created_by = $('#created_by_txt :selected').val();
	 
	if (wastage_date == "") {
		toastr.error("Enter Wastage Date");
		return;
	}

	/*if (machine_code == "Select") {
		toastr.error("Select Machine");
		return;
	} */
	if (material_code == "Select" && machine_code=="Select") {
		toastr.error("Select Material or Machine");
		return;
	} 
	if (qty == "") {
		toastr.error("Enter Qty");
		return;
	} 
/*	if (value == "") {
		toastr.error("Enter Value");
		return;
	} */
	if (created_by == "") {
		toastr.error("Enter Username");
		return;
	} 
	 
	$.ajax({
		type: "POST",
		url: "api/get_wastage_report.php", 
		data: {type : "add",wastage_date:wastage_date,
		ref_no:ref_no,machine_code:machine_code,qty:qty,created_by:created_by,
		material_code:material_code,remarks:remarks,value:value
		},
		success: function (response) {
			if(response == "success")
			{
				toastr.success("Saved Successfully!!!");
				$('#wastage_date_txt').val('');
				$('#qty_txt').val('');
				$('#ref_no_txt').val('');
				$('#machine_id_txt').val('Select'); 
				$('#material_code_txt').val('Select');
				$('#remarks_txt').val('');
				$('#value_txt').val('');
				$('#created_by').val('');
				//fnSearch();
				fnSideMenu(17);
			}
			else
			{
				toastr.error("Failed to save!!!");
			}
		}
	});
}

function fnWastageDelete(pId)
{
	if (!confirm('Delete this Receipt Voucher?')) return;
    $.ajax({
		type: "POST",
		url: "api/get_wastage_report.php", 
		data: {type : "del",w_id : pId},
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