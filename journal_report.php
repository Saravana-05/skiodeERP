<?php
session_start();
include_once "connect_db.php";
include_once "page_guard.php";
?>
<style>
.reportContent
{
	margin-top: 20px !important;
    border: 0px solid #ccc;border-radius: 4px;
	min-height:550px; 
	
}
.reportContent h4
{
	/*margin-top: -25px; 
    background: white;*/
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
	text-align: right;
    line-height: 8px;
	font-weight: bold;
}
#reportResultDiv
{
	border:1px solid #cfcfcf;background:#fff;min-height:200px;font-size:0.8rem;
}
.reportContent>.headerDiv
{
	background-color:#3a3a3a;border-radius:5px;color:#fff;
}
</style>
<div class="bg_aliceblue p-3 m-1 pt-0 reportContent">
 
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center p-2 mb-3 headerDiv" >
		<h4 class="mb-0 ">Journal Report</h4> 			
	</div>
	<div class="row">
		 <label for="date1Txt" class="col-md-2 col-form-label">From Date</label>
		 <div class="col-md-2">
			<input type="text" autocomplete="off" class="form-control datepicker"  id="fromDtTxt" name="fromDtTxt"  placeholder="dd-MMM-yyyy" value="<?php echo date("d-m-Y");?>" />
		 </div>
		 <label for="date1Txt" class="col-md-1 col-form-label">To Date</label>
		 <div class="col-md-2">
			<input type="text" autocomplete="off" class="form-control datepicker"  id="toDtTxt" name="toDtTxt"  placeholder="dd-MMM-yyyy" value="<?php echo date("d-m-Y");?>" />
		 </div>
		 <div class="col-md-2">
			<button type="button" class="btn btn-sm btn-darkpurple" onclick="fnSearch()" >Search</button> 
		 </div> 
	</div>
 
	<div id="reportResultDiv" class="p-3 mt-3">
		<h5 style="text-align:center;margin:1rem;padding:1rem;">Result Area</h5> 
	</div>
</div>
<script>
$(document).ready(function() {
	SetUpBasics();
	$('.datepicker').removeClass('hasDatepicker').datepicker({
		dateFormat: 'dd-mm-yy'  
	}); 
});
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
		url: "api/get_journal_report.php?from="+from+"&to="+to, 
		success: function (response) {
			if(response != "")
			{  
		 
				$('#reportResultDiv').html('');
				$('#reportResultDiv').html(response); 
				$('#resultDetailTable').DataTable({  
					"lengthMenu": [[ 10, 25, 50, -1], [ 10, 25, 50, "All"]] ,
					 language: {
						emptyTable: 'No data available in table'
					},
					  dom: 'lBfrtip',
					buttons: [
						  {
							extend: 'excelHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">table_view</span> Export to Excel',
							title: 'Journal Report',
							className: 'btn buttons-excel'
						},
						{
							extend: 'pdfHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">picture_as_pdf</span> Export to PDF',
							className: 'btn buttons-pdf',
							orientation: 'portrait',
							pageSize: 'A4',
							title: 'Journal Report'
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
</script>