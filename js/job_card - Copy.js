var sNo=1;
$(document).ready(function() {
	$(".numericOnly").keypress(function (e) {  
		if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
			//display error message
			//$("#errmsg").html("Digits Only").show().fadeOut("slow");
				   return false;
		}
	});
	$('.datepicker').datepicker({
		format: 'dd-mm-yyyy' 
	}); 
});
function clear()
{
   $('#machineOpt').val(0);
   $('#stockOpt').val(0);
   $('#fbCopyTxt').val(0);
   $('#firstCopyTxt').val('');
   $('#addlCopyTxt').val('');
   $('#sheetsTxt').val('');
}
function fnAddMore()
{
	var markup = "";
	var machineId = $('#machineOpt :selected').val();
	var machineName = $('#machineOpt :selected').text();
	var stockId = $('#stockOpt :selected').val();
	var stockName = $('#stockOpt :selected').text();
	var firstCopy = $('#firstCopyTxt').val();
	var addCopy = $('#addlCopyTxt').val();
	var fb = $('#fbCopyTxt :selected').val();
	var sheets = $('#sheetsTxt').val();
	var sheetCount ="";
	var totalQty = "";
	var value = "";
	if(stockId == "0" || stockName == null)
	{
		toastr.error('Select Stock Name !!!');
		return false;
	}
	if(machineId == "0" || machineName == null)
	{
		toastr.error('Select Machine Name !!!');
		return false;
	}
	if(sNo % 2 == 0)
	{
		markup = '<tr class="table-danger">';
	}
	else
	{
		markup = "<tr>";
	}		
	
	markup = markup + "<td>" + sNo + "</td>";  
	markup = markup + "<td>" + machineName + "<input</td>";
	markup = markup + "<td>" + stockName + "</td>";
	markup = markup + "<td>" + firstCopy + "</td>";
	markup = markup + "<td>" + addCopy + "</td>";
	markup = markup + "<td> </td>";
	markup = markup + "<td>" + sheets + "</td>";
	markup = markup + "<td>" + totalQty + " </td>";
	markup = markup + "<td>" + value + " </td>";
	markup = markup + "<td>" + fb + " </td>";
	markup = markup + "<td>" + sheetCount + " </td>";
	markup = markup + "</tr>";
    $("#detailsTable tbody").append(markup);
	sNo++;
	clear();
}
function saveJobCard()
{
	var jobCardDate = $('#jobCardDateTxt').val();
	var jobCardNo = $('#jobCardNoTxt').val();
	var customer_type = $('input[name="customerTypeOpt"]:checked').val();
	var customerName = $('#customerNameTxt').val();
	var address = $('#addressTxt').val();
	var city = $('#cityTxt').val();
	var state = $('#stateTxt').val();
	var phone = $('#phoneTxt').val(); 
	var gstNo = $('#gstTxt').val(); 
	var sales_type_id = $('#sales_typeTxt').val(); 
	var sales_subtype_name = $('#sales_subtypeTxt').val(); 
	var app_amount = $('#appAmountTxt').val(); 
	var advance_amount_type = $('input[name="advance_amount_typeOpt"]:checked').val();  
	var advance_amount = 0;   
	if(advance_amount_type == "cash")
	{
		advance_amount = $('#cashTxt').val();
	}
	else if(advance_amount_type == "gpay")
	{
		advance_amount = $('#gpayTxt').val();
	}
	else if(advance_amount_type == "advtotal")
	{
		advance_amount = $('#advTxt').val(); 
	}
	
	var allTableData = [];
	$('#detailsTable tbody tr').each(function() {
		var rowData = {};
		$(this).find('td').each(function(index) {
			// You can use a header array or a fixed structure to map data
			// For example, if you have a header row:
			// var headerText = $('table thead th').eq(index).text();
			// rowData[headerText] = $(this).text();
			
			// Or simply store as an array:
			rowData['column' + (index + 1)] = $(this).text(); 
		});
		allTableData.push(rowData);
	});
	console.log(allTableData);
	
	$.ajax({
		type: "POST",
		url: "api/save_jobcard.php",
		data:{jobCardDate:jobCardDate,jobCardNo:jobCardNo,customer_type:customer_type
			,customerName:customerName
			,address:address,city:city,state:state,phone:phone,gstNo:gstNo
			,sales_type_id:sales_type_id,sales_subtype_name:sales_subtype_name
			,app_amount:app_amount,advance_amount_type:advance_amount_type
			,advance_amount:advance_amount,allTableData:allTableData},
		success: function (response) {
			if(response == "Success")
			{
				toastr.success('Successfully Added.');
				clearPapers();
				clear();
			}
			else
			{
				toastr.error(response);
			}
			
		} 
	});	
}

function printJobCard()
{
	
}
function alterJobCard()
{
	
}
function newJobCard()
{
	
}
function voidJobCard()
{
	
}
function closeJobCard()
{
	
}

function clearPapers()
{
	$("#detailsTable tbody").empty();
   $('#machineOpt').val(0);
   $('#stockOpt').val(0);
   $('#categoryOpt').val(0);
   $('#fbCopyTxt').val(0);
   $('#firstCopyTxt').val('');
   $('#addlCopyTxt').val('');
   $('#sheetsTxt').val('');
}