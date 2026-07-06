<?php
session_start();
include_once "connect_db.php";
include_once "page_guard.php";
?>
<style>
.reportContent
{
	margin-top: 20px !important;
    border: 0px solid #ccc;
	border-radius: 4px;
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
	margin-left: 10px;
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
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center p-2 mb-3 headerDiv">
		<h4 class="mb-0">Productwise Sales
		Report</h4> 			
	</div>
	<form>
		<div>
			 <label for="fromDtTxt">From :</label>
				<input type="text" autocomplete="off" class="datepicker"  id="fromDtTxt" name="fromDtTxt"  placeholder="dd-MM-yyyy" value="<?php echo date("d-m-Y");?>" />
			 <label for="toDtTxt">To : </label>
				<input type="text" autocomplete="off" class="datepicker"  id="toDtTxt" name="toDtTxt"  placeholder="dd-MM-yyyy" value="<?php echo date("d-m-Y");?>" />
		 
			User
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
			Type 
			<select id="job_type_txt" name="job_type_txt">			
				<option value="all">All</option>
				<option value="PRINTING">PRINTING</option>
				<option value="SERVICES">SERVICES</option>
				<option value="MATERIALS">MATERIALS</option>
				<option value="IDCARDS">IDCARDS</option>
				<option value="PRODUCTS">PRODUCTS</option>
				
			</select>
			<input type="checkbox" name="show_summary_chk" id="show_summary_chk">Summary</input>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-sm btn-darkpurple" onclick="fnSearch()">Search</button> 
		</div>
	</form>
	
	<div id="reportResultDiv" class="p-2 mt-3" style="border:0px solid #cfcfcf;background:#fff;min-height:200px;">
		 
	</div>
</div>
<script>
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
		dateFormat: 'dd-mm-yy'  
	}); 
	console.log("Date Pkr Set");
});
function printProductwiseDiv(divId) {  
	var fromDt=$('#fromDtTxt').val();
	var toDt=$('#toDtTxt').val();
	var header='';
	
	header=header+'<style>*{font-size:12px;}th {border-bottom:1px solid #000;"}td {border-bottom:1px solid #eee;"}</style><div style="text-align:center;">';
	header=header+'<div style="text-align:center;font-size:1.2rem;font-weight:bold;">CITIZEN PRINTS</div>';
	header=header+'<div style="text-align:center;font-size:1.2rem;font-weight:bold;">VADAPALANI, CHENNAI - 600026, TAMILNADU</div>';
	header=header+'<div style="text-align:center;font-size:1.2rem;font-weight:bold;">PRODUCTWISE SALES REPORTS</div>';
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
	$('#reportResultDiv').html('');
	var from = $('#fromDtTxt').val();
	var to = $('#toDtTxt').val(); 
	var user_name_txt = $('#user_name_txt').val(); 
	var job_type_txt = $('#job_type_txt').val(); 
	var show_summary_chk = ($('#show_summary_chk').is(":checked")?1:0); 
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
		url: "api/get_productwise_sales_report.php", 
		data:{
			"from":from,
			"to":to, 
			"user_name_txt":user_name_txt,
			"job_type_txt":job_type_txt,
			"show_summary_chk":show_summary_chk
		},		
		// saravanan changes start
		success: function (response) {
    if(response != "")
    {  
        $('#reportResultDiv').html(response);

        // Destroy previous instance if search run again
        if($.fn.DataTable.isDataTable('#resultDetailTable')) {
            $('#resultDetailTable').DataTable().destroy();
        }

        $('#resultDetailTable').DataTable({  
            "lengthMenu": [[ 25,50, 100, 150, -1], [ 25,50, 100, 150, "All"]],
            language: {
                emptyTable: 'No data available in table'
            },
            dom: 'lBfrtip',
            // Tell DataTables not to use tfoot for column width detection
            "orderCellsTop": true,
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">table_view</span> Export to Excel',
                    title: 'Product-wise Sales',
                    className: 'btn buttons-excel',
                    exportOptions: { rows: { selected: false } }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">picture_as_pdf</span> Export to PDF',
                    className: 'btn buttons-pdf',
                    orientation: 'portrait',
                    pageSize: 'A4',
                    title: 'Product-wise Sales'
                },
                {
                    extend: 'print',
                    text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">print</span> Print Table',
                    className: 'btn btn-secondary',
                    customize: function (win) {
                        $(win.document.body).css('font-size', '14px');
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('border-collapse', 'collapse')
                            .find('thead th')
                            .css({
                                'background-color': '#343a40',
                                'color': 'white',
                                'padding': '8px',
                                'text-align': 'center'
                            });
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
// saravanan changes end
	});	
} 
</script>