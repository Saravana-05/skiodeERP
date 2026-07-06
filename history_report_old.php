<?php
session_start();
include_once "connect_db.php";
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
		<h4 class="mb-0 ">History Report</h4> 			
	</div>
	<!-- saravanan changes start -->
	<div class="row g-2 align-items-center">
    <label for="customer_mobile_no_txt" class="col-md-1 col-form-label">Mobile</label>
    <div class="col-md-2">
        <input type="text" autocomplete="off" class="form-control" placeholder="Mobile No"
            id="customer_mobile_no_txt" name="customer_mobile_no_txt" maxlength="15" />
    </div>

    <!-- NEW: Name label + input -->
    <label for="customer_name_search_txt" class="col-md-1 col-form-label">Name</label>
    <div class="col-md-2">
        <input type="text" autocomplete="off" class="form-control" placeholder="Customer Name"
            id="customer_name_search_txt" name="customer_name_search_txt" maxlength="100" />
    </div>

    <div class="col-md-2">
        <button type="button" class="btn btn-sm btn-darkpurple" onclick="fnSearch()">History</button>
    </div>
</div>
<!-- saravanan changes end -->
 
	<div id="reportResultDiv" class="p-3 mt-3">
		<h5 style="text-align:center;margin:1rem;padding:1rem;">Result Area</h5> 
	</div>
</div>
<script>
$(document).ready(function() {
	SetUpBasics();
	 var availableMobileNos = [
	<?php
		$mob_sql="select distinct customer_mobile_no from jobcard_master where customer_type='General';";
		if($mob_qry=mysqli_query($connection,$mob_sql))
		{
			$ans="";
			while($mob_row=mysqli_fetch_array($mob_qry))
			{
				$customer_mobile_no=$mob_row["customer_mobile_no"];
				if(strlen($customer_mobile_no)>0)
				{
					if($ans!="") $ans.=",";
					$ans.= '"'.$customer_mobile_no.'"';
				}
			}
			echo $ans;
		}
	?>

    ];
	$( "#customer_mobile_no_txt" ).autocomplete({
      source: availableMobileNos
    });	
	// saravanan changes start 
	var availableCustomerNames = [
<?php
    $name_sql = "SELECT DISTINCT customer_name FROM jobcard_master WHERE customer_type='General' AND customer_name IS NOT NULL AND customer_name != '';";
    if ($name_qry = mysqli_query($connection, $name_sql)) {
        $ans = "";
        while ($name_row = mysqli_fetch_array($name_qry)) {
            $customer_name = addslashes($name_row["customer_name"]);
            if (strlen($customer_name) > 0) {
                if ($ans != "") $ans .= ",";
                $ans .= '"' . $customer_name . '"';
            }
        }
        echo $ans;
    }
?>
];
$("#customer_name_search_txt").autocomplete({
    source: availableCustomerNames
});
// saravanan changes end

$( "#customer_mobile_no_txt" ).on('keyup',function(){
	mobile_no=$( "#customer_mobile_no_txt" ).val();
	if(mobile_no.length>9)
	{
	$.ajax({
	type: "GET",
	url: "api/get_general_customer_name.php?mb_no="+mobile_no,

	success: function (response) {
		$("#customer_name_txt").val(response);
	}
	});
	}
	else
	{
		$("#customer_name_txt").val("");
	}
});
});
// function fnSearch()
// {
// 	customer_mobile_no_txt=$("#customer_mobile_no_txt").val();
// 	if(customer_mobile_no_txt.length>0)
// 	{
// 		$.ajax({
// 			url: 'api/get_mobile_no_history.php',
// 			method: 'POST',
// 			data:{ "customer_mobile_no_txt":customer_mobile_no_txt},
// 			async:false,
// 			success: function (response) {
// 				if(response != "")
// 			{  
		 
// 				$('#reportResultDiv').html('');
// 				$('#reportResultDiv').html(response); 
// 				$('#resultDetailTable').DataTable({  
// 					"lengthMenu": [[ 10, 25, 50, -1], [ 10, 25, 50, "All"]] ,
// 					 language: {
// 						emptyTable: 'No data available in table'
// 					},
// 					  dom: 'lBfrtip',
// 					buttons: [
// 						  {
// 							extend: 'excelHtml5',
// 							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">table_view</span> Export to Excel',
// 							title: 'History Report',
// 							className: 'btn buttons-excel'
// 						},
// 						{
// 							extend: 'pdfHtml5',
// 							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">picture_as_pdf</span> Export to PDF',
// 							className: 'btn buttons-pdf',
// 							orientation: 'portrait',
// 							pageSize: 'A4',
// 							title: 'History Report'
// 						},
// 						{
// 							extend: 'print',
// 							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">print</span> Print Table',
// 							className: 'btn btn-secondary',
// 							customize: function (win) {
// 								$(win.document.body).css('font-size', '14px');

// 								// Style the table header
// 								$(win.document.body).find('table')
// 									.addClass('compact')
// 									.css('border-collapse', 'collapse')
// 									.find('thead th')
// 									.css({
// 										'background-color': '#343a40',   // Dark gray header
// 										'color': 'white',
// 										'padding': '8px',
// 										'text-align': 'center'
// 									});

// 								// Optional: Center table
// 								$(win.document.body).find('table').css('margin', '0 auto');
// 							}
// 						}
// 					]
			
// 				});
// 			}
// 			else
// 			{
// 				toastr.error("Failed to get details!!!");
// 			}
				
// 			}
// 			});			
// 	} 
// } 
// Saravanan changes start
function fnSearch()
{
    var customer_mobile_no_txt = $("#customer_mobile_no_txt").val().trim();
    var customer_name_txt = $("#customer_name_search_txt").val().trim();

    if(customer_mobile_no_txt.length === 0 && customer_name_txt.length === 0)
    {
        toastr.warning("Please enter a Mobile No or Customer Name to search.");
        return;
    }

    $.ajax({
        url: 'api/get_mobile_no_history.php',
        method: 'POST',
        data: { 
            "customer_mobile_no_txt": customer_mobile_no_txt,
            "customer_name_txt": customer_name_txt
        },
        async: false,
        success: function (response) {
            if(response != "")
            {  
                $('#reportResultDiv').html('');
                $('#reportResultDiv').html(response); 
                $('#resultDetailTable').DataTable({  
                    "lengthMenu": [[ 10, 25, 50, -1], [ 10, 25, 50, "All"]],
                    language: {
                        emptyTable: 'No data available in table'
                    },
                    dom: 'lBfrtip',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">table_view</span> Export to Excel',
                            title: 'History Report',
                            className: 'btn buttons-excel'
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">picture_as_pdf</span> Export to PDF',
                            className: 'btn buttons-pdf',
                            orientation: 'portrait',
                            pageSize: 'A4',
                            title: 'History Report'
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
                toastr.error("No records found!!!");
            }
        }
    });			
}
// Saravanan changes end
</script>