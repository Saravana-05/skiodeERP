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
		<h4 class="mb-0">Material Usage Report</h4> 			
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
		 <label for="date1Txt" class="col-md-1 col-form-label">User</label>
		 <div class="col-md-2">
			<select id="user_name_txt" name="user_name_txt"  class="form-select" >
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
		 </div>
		 <div class="col-md-1">
			<button type="button" class="btn btn-sm btn-darkpurple" onclick="fnSearch()">Search</button> 
		 </div>
			
	</div>
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
});

function printMaterialUsageDiv(divId) {  
	var fromDt=$('#fromDtTxt').val();
	var toDt=$('#toDtTxt').val();
	var header='';
	
	header=header+'<style>*{font-size:12px;}th {border-bottom:1px solid #000;"}td {border-bottom:1px solid #eee;"}</style><div style="text-align:center;">';
	header=header+'<div style="text-align:center;font-size:1.2rem;font-weight:bold;">CITIZEN PRINTS</div>';
	header=header+'<div style="text-align:center;font-size:1.2rem;font-weight:bold;">VADAPALANI, CHENNAI - 600026, TAMILNADU</div>';
	header=header+'<div style="text-align:center;font-size:1.2rem;font-weight:bold;">MATERIAL USAGE REPORTS</div>';
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
		var user_name_txt = $('#user_name_txt').val(); 
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
		url: "api/get_material_usage_report.php", 
		data:{
			"from":from,
			"to":to, 
			"user_name_txt":user_name_txt 
		},	
		success: function (response) {
			if(response != "")
			{  
				$('#reportResultDiv').html(response); 
			}
			else
			{
				toastr.error("Failed to get details!!!");
			}
			
		} 
	});	
} 
</script>