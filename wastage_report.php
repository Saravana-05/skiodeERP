<?php
session_start();
include_once "connect_db.php";
include_once "page_guard.php";
?> 
<div class="card-header  text-light " style="border: 1px solid #0fa9c5;background: #0fa9c5 !important;padding-left: 10px;">Wastage Report</div>
		<div class="card-body p-2"  style="border: 1px solid #ccc; min-height: max-content;">
			<div>
				<label for="date1Txt">From : </label>
				<input type="text" autocomplete="off" class="datepicker"  id="fromDtTxt" name="fromDtTxt"  placeholder="dd-MM-yyyy" value="<?php echo date("d-m-Y");?>" />
				<label for="date1Txt">To : </label>
				<input type="text" autocomplete="off" class="datepicker"  id="toDtTxt" name="toDtTxt"  placeholder="dd-MM-yyyy" value="<?php echo date("d-m-Y");?>" />
				User :
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
				Machine :
				<select id="machine_code_txt" name="machine_code_txt">
					<?php
						echo '<option value="all">All</option>';
						$sql="select * from machine_master;";
						if($qry=mysqli_query($connection,$sql))
						{
							while($row=mysqli_fetch_array($qry))
							{
								echo '<option value="'.$row["machine_code"].'">'.$row["machine_name"].'</option>';
							}
						}
						
					?>

				</select>				 
				 
					<button type="button" class="btn btn-sm btn-darkpurple" onclick="fnSearch()">Search</button> 
				 
					
			</div>
			 
			<div id="reportResultDiv" class="p-2 mt-1" style="border:0px solid #cfcfcf;background:#e1e1e1;min-height:200px;">
				 
			</div>
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
function printWastageReportDiv(divId) {  
	var fromDt=$('#fromDtTxt').val();
	var toDt=$('#toDtTxt').val();
	var header='';
	
	header=header+'<style>*{font-size:12px;}th {border-bottom:1px solid #000;"}td {border-bottom:1px solid #eee;"}@media print {  .noPrint{	display:none;	}}</style><div style="text-align:center;">';
	header=header+'<div style="text-align:center;font-size:1.2rem;font-weight:bold;">PRINTZY</div>';
	header=header+'<div style="text-align:center;font-size:1.2rem;font-weight:bold;">VADAPALANI, CHENNAI - 600026, TAMILNADU</div>';
	header=header+'<div style="text-align:center;font-size:1.2rem;font-weight:bold;">WASTAGE REPORTS</div>';
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
	var machine_code_txt = $('#machine_code_txt').val();
	 
	if (!from || !to) {
            toastr.error("Please select both dates.");
            return;
        }

	if (new Date(from) > new Date(to)) {
		toastr.error("From Date cannot be after To Date.");
		return;
	}  
	$('#reportResultDiv').html('Loading...');
	$.ajax({
		type: "POST",
		url: "api/load_wastage_report.php", 
		data: {from : from,to:to,user_name_txt:user_name_txt,machine_code_txt:machine_code_txt },
		success: function (response) {

				$('#reportResultDiv').html(response); 

		} 
	});	
} 

</script>		