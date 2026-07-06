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
#machineForm label{
	font-weight:bold;
}
#machineTable td,#machineTable th{
	padding:3px;
}
</style>
<div class="mt-2" style="display:none;">
	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)" onclick="fnSideMenu(1)">Dashboard</a></li>
		<li class="breadcrumb-item active" aria-current="page">Machine Master</li>
	  </ol>
	</nav>
	</div> 
<div class="container ">
  <h5 class="mb-1 mt-1" style="width:75%;float:left;">Machine Master</h5>

  <button class="btn btn-sm btn-primary mt-1" id="addMachineBtn" style="float:right;">Add Machine</button>
	<div id="table_holder" style="padding:0px;height:83vh !important;width:100%;overflow:auto;font-size:12px;">
	  <div class="table-responsive" id="machineDiv">
		
	  </div>
    </div>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="machineModal" tabindex="-1" aria-labelledby="machineModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="machineForm" class="modal-content">
      <div class="modal-header bg_blue">
        <h5 class="modal-title" id="machineModalLabel">Add Machine</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="actionId" value=""> 
		<div class="mb-3">
          <label>Machine Code</label>
          <input type="text" autocomplete="off" id="machine_code" class="form-control" required>
        </div>
		<div class="mb-3">
          <label>Machine Name</label>
          <input type="text" autocomplete="off" id="machine_name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Desc.</label>
          <input type="text" autocomplete="off" id="machine_desc" class="form-control" required>
        </div>
		<div class="mb-3">
			<label>Counter Reading</label> 
			<input type="text" autocomplete="off" id="counter_reading" class="form-control" >
        </div>
		<div class="mb-3">
          <label>Reading On Date </label>
          <input type="text" autocomplete="off" id="counter_on_dt" class="form-control datepicker" required>
        </div> 
        
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-sm btn-success">Save</button>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>
 
<script >

$(document).ready(function () {
	SetUpBasics();
  loadMachines();
$('.datepicker').datepicker();

  $('#addMachineBtn').click(function () {
	
    $('#machineForm')[0].reset();
    $('#actionId').val('create');
    $('#machineModalLabel').text('Add Machine');
	$('#machine_code').attr("disabled",false); 
    $('#machineModal').modal('show');
  });

  $('#machineForm').submit(function (e) {
    e.preventDefault();
    //const id = $('#cust-code').val();
   
	 
	if($('#machine_type :selected').val() == "Select")
	{
		toastr.error("Select Machine Type");
		return;
	}
	
    const payload = {
      machine_code:$('#machine_code').val(),
      machine_name:$('#machine_name').val(),
      machine_desc: $('#machine_desc').val(), 
      counter_reading: $('#counter_reading').val(),
      counter_on_dt: $('#counter_on_dt').val() 
    };
    var action = $('#actionId').val(); //id ? 'update' : 'create';
    $.ajax({
      url: `api/crud_machine_api.php?action=${action}`,
      method: 'POST',
      data: JSON.stringify(payload),
      contentType: 'application/json',
      success: function (response) { 
		   if (response.status == "Success") {
			   toastr.success("Successfully updated.");
				$('#machineModal').modal('hide');
				 
				loadMachines();
			}
			else
			{  
				toastr.error(response.status);
				return;
				
			}
        
      } 
    });
  });

  $('#machineDiv').on('click', '.editBtn', function () {
    const row = $(this).closest('tr');
	 
    var code = row.data('id'); 
	 
	$('#actionId').val('update');
	$.get('api/crud_machine_api.php?action=editrow&code='+code, function (data) {
      let rows = ''; 
	  for(var i=0;i<data.length;i++)
	  { 
			$('#machine_code').val(data[i].machine_code);
			$('#machine_name').val(data[i].machine_name);
			$('#machine_desc').val(data[i].machine_desc);
			$('#counter_reading').val(data[i].counter_reading);
			$('#counter_on_dt').val(data[i].counter_on_dt); 
			 
			$('#machine_code').attr("disabled",true);
	  }
	});
    $('#machineModalLabel').text('Edit Machine');
    $('#machineModal').modal('show');
  });

  $('#machineDiv').on('click', '.deleteBtn', function () {
    if (!confirm('Delete this customer?')) return;
    const id = $(this).closest('tr').data('id');
    $.get(`api/crud_machine_api.php?action=delete&machine_code=${id}`, function () {
      loadMachines();
    });
  });
});

  function loadMachines() {
    $.get('api/crud_machine_api.php?action=read', function (data) {
      let rows = '';
	   $('#machineDiv').html('');
		rows = '<table class="table table-bordered table-hover" id="machineTable">';
		rows += '  <thead class="table-dark">';
		rows += ' <tr> <th>Machine Code</th><th>Name</th><th>Desc.</th><th>Counter</th><th>On Date</th><th>Actions</th></tr>';
		rows += '</thead><tbody>';
			
		  
		
		
	  for(var i=0;i<data.length;i++)
	  { 
		rows += ' <tr data-id="'+data[i].machine_code+'">';  
        rows += '     <td>'+ data[i].machine_code +'</td>'; 
        rows += '     <td>'+ data[i].machine_name +'</td>'; 
        rows += '     <td>'+data[i].machine_desc +'</td>'; 
        rows += '     <td>'+data[i].counter_reading +'</td>'; 
        rows += '     <td>'+data[i].counter_on_dt +'</td>';  
        rows += '     <td>'; 
        rows += '       <button class="btn btn-sm btn-warning editBtn mt-1">Edit</button>'; 
        rows += '       <button class="btn btn-sm btn-danger deleteBtn mt-1">Delete</button>'; 
        rows += '     </td>'; 
        rows += '   </tr>';  
	  }
      rows += ' </tbody></table>';
      $('#machineDiv').html(rows);
	 // new DataTable('#machineTable');
		var table = $('#machineTable').DataTable({  "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]] });
    });
  }
</script>
 
