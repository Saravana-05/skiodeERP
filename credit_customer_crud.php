<?php
session_start(); 
include_once "connect_db.php";
include_once "page_guard.php";
?>
<style>
div.dt-container div.dt-length select {
    width: 35%; 
}
div.dt-container div.dt-length label {
	width: 55%; 
}
#customerForm label{
	font-weight:bold;
}
#customerTable td,#customerTable th{
	padding:3px;
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
	line-height: 1em !important;
}
</style>
<div class="mt-2" style="display:none;">
	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)" onclick="fnSideMenu(1)">Dashboard</a></li>
		<li class="breadcrumb-item active" aria-current="page">Customer Master</li>
	  </ol>
	</nav>
	</div> 
<div class="container ">
  <h5 class="mb-1 mt-1" >Customer Master</h5>
	 
	<div class="row">
		<label class="col-2">Customer Type</label>
	  <div class="col-4">
			<select id="customer_code_txt" name="customer_code_txt">
				<option value="all">All</option>
				<option value="CREDITCUSTOMER">CREDIT CUSTOMER</option>
				<option value="VENDOR">VENDOR</option> 
			</select>	
	  </div>
	  <div class="col-1">
			<button type="button" class="btn btn-sm btn-darkpurple" onclick="fnSearch()">Search</button>	
	  </div>
	  <div class="col-2">
			  <button class="btn btn-sm btn-primary " id="addCustomerBtn"  >Add Customer</button>
	  </div>
	 
  </div>
	<div id="table_holder" style="padding:0px;height:83vh !important;width:100%;overflow:auto;font-size:12px;">
	  <div class="table-responsive" id="customerDiv">
		
	  </div>
    </div>
</div>

<!-- Bootstrap Modal -->

	<?php include_once "new_credit_customer_ui.php"; ?>

 
<script >

$(document).ready(function () {
	SetUpBasics();
  loadCustomers();


  $('#addCustomerBtn').click(function () {
	
    $('#customerForm')[0].reset();
    $('#actionId').val('create');
    $('#customerModalLabel').text('Add Customer');
	$('#cust-code').attr("disabled",false);
	$('#cust-name').attr("disabled",false);
    $('#customerModal').modal('show');
  });

  $('#customerForm123').submit(function (e) {
    e.preventDefault();
    //const id = $('#cust-code').val();
    var customerType = "";
	if($('#credit_cust').prop("checked"))
	{
		 customerType = $('#credit_cust:checked').val();
	}
	if($('#vendor_cust').prop("checked"))
	{
		 customerType = customerType + ":" +$('#vendor_cust:checked').val();
	}
	 
	if(customerType == "")
	{
		toastr.error("Select customer type");
		return;
	}
	
    const payload = {
      code:$('#cust-code').val(),
      name: $('#cust-name').val(),
      customertype: customerType,
      address1: $('#cust-addr1').val(),
      address2: $('#cust-addr2').val(),
      city: $('#cust-city').val(),
      state: $('#cust-state :selected').val(),
      stateName: $('#cust-state :selected').text(),
      gst: $('#cust-gst').val(),
      phone: $('#cust-phone').val(),
      email: $('#cust-email').val()
    };
    var action = $('#actionId').val(); //id ? 'update' : 'create';
    $.ajax({
      url: `api/crud_customer_api.php?action=${action}`,
      method: 'POST',
      data: JSON.stringify(payload),
      contentType: 'application/json',
      success: function (response) { 
		   if (response.status == "Success") {
			   toastr.success("Successfully updated.");
				$('#customerModal').modal('hide');				 
				loadCustomers();
			}
			else
			{  
				toastr.error(response.status);
				return;				
			}        
      } 
    });
  });

  $('#customerDiv').on('click', '.editBtn', function () {
    const row = $(this).closest('tr');
	 
    var code = row.data('id');//$('#cust-code').val(row.data('id'));
    /*$('#cust-code').val(row.data('id'));
    $('#cust-name').val(row.find('td:eq(1)').text());
    $('#cust-addr').val(row.find('td:eq(2)').text());
    $('#cust-city').val(row.find('td:eq(3)').text());
    $('#cust-state').val(row.find('td:eq(4)').text());
    $('#cust-gst').val(row.find('td:eq(5)').text());
    $('#cust-phone').val(row.find('td:eq(6)').text());*/
	 
	$('#actionId').val('update');
	$.get('api/crud_customer_api.php?action=editrow&code='+code, function (data) {
      let rows = ''; 
	  for(var i=0;i<data.length;i++)
	  {
			$('#cust-code').val(data[i].customer_code);
			var cust_type = data[i].customer_type;
			if(cust_type.includes("CREDITCUSTOMER")) $('#credit_cust').attr("checked",true); else $('#credit_cust').attr("checked",false);
			if(cust_type.includes("VENDOR")) $('#vendor_cust').attr("checked",true); else $('#vendor_cust').attr("checked",false);
/*			var tmp=cust_type.split(':');
			if(tmp.length==1)
			{
				if(tmp[0]=="CREDITCUSTOMER")
				{
					 $('#credit_cust').attr("checked",true);
				}
				else if(tmp[0]=="VENDOR")
				{
					 $('#vendor_cust').attr("checked",true);
				}
			}
			else if(tmp.length==2)
			{
				if(tmp[0]=="CREDITCUSTOMER")
				{
					 $('#credit_cust').attr("checked",true);
					 if(tmp[1]=="VENDOR")
						{
						 $('#vendor_cust').attr("checked",true);
						}
				}
				else if(tmp[0]=="VENDOR")
				{
					 $('#vendor_cust').attr("checked",true);
					  if(tmp[1]=="CREDITCUSTOMER")
						{
						 $('#credit_cust').attr("checked",true);
						}
				}
			}*/
			$('#cust-code').val(data[i].customer_code);
			$('#cust-name').val(data[i].customer_name);
			$('#cust-addr1').val(data[i].customer_addr1);
			$('#cust-addr2').val(data[i].customer_addr2);
			$('#cust-city').val(data[i].customer_city);
			$('#cust-state').val(data[i].customer_state_code);
			$('#cust-gst').val(data[i].gst_no);
			$('#cust-phone').val(data[i].mobile_no);
			$('#cust-email').val(data[i].customer_email_id);
			$('#cust-opening_balance').val(Math.abs(data[i].opening_balance));
			
			if(data[i].opening_balance<0) $("#debit_ob").prop("checked", true); else $("#credit_ob").prop("checked", true);
			
			$('#cust-code').attr("disabled",true);
			$('#cust-name').attr("disabled",true);
	  }
	});
    $('#customerModalLabel').text('Edit Customer');
    $('#customerModal').modal('show');
  });

  $('#customerDiv').on('click', '.deleteBtn', function () {
    if (!confirm('Delete this customer?')) return;
    const id = $(this).closest('tr').data('id');
    $.get(`api/crud_customer_api.php?action=delete&code=${id}`, function () {
      loadCustomers();
    });
  });
});

  function loadCustomers() {
    $.get('api/crud_customer_api.php?action=read', function (data) {
      let rows = '';
	   $('#customerDiv').html('');
		rows = '<table class="table table-bordered table-hover" id="customerTable">';
		rows += '  <thead class="table-dark">';
		rows += ' <tr> <th>Code</th><th>Name</th><th>Type</th><th>Address</th><th>Actions</th></tr>';
		rows += '</thead><tbody>';
			
		  
		
		
	  for(var i=0;i<data.length;i++)
	  {

		  
		rows += ' <tr data-id="'+data[i].customer_code+'">';  
        rows += '     <td>'+ data[i].customer_code +'</td>'; 
        rows += '     <td>'+data[i].customer_name +'</td>'; 
		//console.log(data[i].customer_type); 
			rows += '     <td>'+data[i].customer_type.replace('R:', 'R<br>').replace(':','') +'</td>'; 
	 
        rows += '     <td style="font-size:11px;">'+data[i].customer_addr1+ '<br>'+data[i].customer_addr2 +data[i].customer_city + '<br>'+data[i].customer_state +'<br><b>GST:</b>'+data[i].gst_no+'<b>  Mobile : </b>'+data[i].mobile_no+'</td>'; 
        rows += '     <td>'; 
        rows += '       <button class="btn btn-sm btn-warning editBtn mt-1">Edit</button>'; 
        rows += '       <button class="btn btn-sm btn-danger deleteBtn mt-1">Delete</button>'; 
        rows += '     </td>'; 
        rows += '   </tr>';  
	  }
      rows += ' </tbody></table>';
      $('#customerDiv').html(rows);
	 // new DataTable('#customerTable');
		//var table = $('#customerTable').DataTable({  "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]] });
		$('#customerTable').DataTable({  
					"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]] ,
					 language: {
						emptyTable: 'No data available in table'
					},
					  dom: 'lBfrtip',
					buttons: [
						  {
							extend: 'excelHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">table_view</span> Export to Excel',
							title: 'Customer List',
							className: 'btn buttons-excel'
						},
						{
							extend: 'pdfHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">picture_as_pdf</span> Export to PDF',
							className: 'btn buttons-pdf',
							orientation: 'portrait',
							pageSize: 'A4',
							title: 'Customer List'
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
    });
  }

function fnSearch(){
	var customer_code_txt = $('#customer_code_txt :selected').val();
	$.get('api/crud_customer_api.php?action=search&ct='+ customer_code_txt, function (data) {
      let rows = '';
	   $('#customerDiv').html('');
		rows = '<table class="table table-bordered table-hover" id="customerTable">';
		rows += '  <thead class="table-dark">';
		rows += ' <tr> <th>Code</th><th>Name</th><th>Type</th><th>Address</th><th>Actions</th></tr>';
		rows += '</thead><tbody>';
			
		  
		
		
	  for(var i=0;i<data.length;i++)
	  {

		  
		rows += ' <tr data-id="'+data[i].customer_code+'">';  
        rows += '     <td>'+ data[i].customer_code +'</td>'; 
        rows += '     <td>'+data[i].customer_name +'</td>'; 
		//console.log(data[i].customer_type);
		rows += '     <td>'+data[i].customer_type.replace('R:', 'R<br>').replace(':','') +'</td>'; 
        rows += '     <td style="font-size:11px;">'+data[i].customer_addr1+ '<br>'+data[i].customer_addr2 +data[i].customer_city + '<br>'+data[i].customer_state +'<br><b>GST:</b>'+data[i].gst_no+'<b>  Mobile : </b>'+data[i].mobile_no+'</td>'; 
        rows += '     <td>'; 
        rows += '       <button class="btn btn-sm btn-warning editBtn mt-1">Edit</button>'; 
        rows += '       <button class="btn btn-sm btn-danger deleteBtn mt-1">Delete</button>'; 
        rows += '     </td>'; 
        rows += '   </tr>';  
	  }
      rows += ' </tbody></table>';
      $('#customerDiv').html(rows);
	 // new DataTable('#customerTable');
		//var table = $('#customerTable').DataTable({  "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]] });
		$('#customerTable').DataTable({  
					"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]] ,
					 language: {
						emptyTable: 'No data available in table'
					},
					  dom: 'lBfrtip',
					buttons: [
						  {
							extend: 'excelHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">table_view</span> Export to Excel',
							title: 'Customer List',
							className: 'btn buttons-excel'
						},
						{
							extend: 'pdfHtml5',
							text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">picture_as_pdf</span> Export to PDF',
							className: 'btn buttons-pdf',
							orientation: 'portrait',
							pageSize: 'A4',
							title: 'Customer List'
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
    }); 
}

</script>
 
