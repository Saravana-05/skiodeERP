<?php
session_start();
include_once "connect_db.php";
include_once "page_guard.php";
?>
<style>
.reportContent>.headerDiv
{
	background-color:#3a3a3a;border-radius:0px;color:#fff;margin-bottom:10px;
}
</style>
<div class="bg_aliceblue p-1 m-1 pt-1 reportContent" >
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center p-1 headerDiv">
		<h5 class="mb-0 ">Product Master</h5> 			
	</div>
	<button class="btn btn-sm btn-primary mt-1 mb-1" id="addUserBtn" style="float:right;">Add Product</button>
	<div id="table_holder" style="padding:0px;height:83vh !important;width:100%;overflow:auto;font-size:12px;">
	  <div class="table-responsive" id="productDiv">
		
	  </div>
    </div>  
	 
</div>


<!-- Bootstrap Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="userForm" class="modal-content">
      <div class="modal-header bg_blue">
        <h5 class="modal-title" id="productModalLabel">Add User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="actionId" value=""> 
		<div class="mb-3">
          <label>User Name</label>
          <input type="text" autocomplete="off" id="user_name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="text" autocomplete="off" id="pass_word" class="form-control" required>
        </div>
		<div class="mb-3">
			<label>User Type</label> 
			<select class="form-select" id="user_type" >
				<option value="Select">-Select-</option>
				<option value="ADMIN">ADMIN</option>
				<option value="OPERATOR">OPERATOR</option>
			</select>
        </div>
		<div class="mb-3">
          <label>User Display Name</label>
          <input type="text" autocomplete="off" id="user_display_name" class="form-control" required>
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
  loadProducts();


  $('#addUserBtn').click(function () {
	
    $('#userForm')[0].reset();
    $('#actionId').val('create');
    $('#productModalLabel').text('Add User');
	$('#user_name').attr("disabled",false); 
    $('#productModal').modal('show');
  });

  $('#userForm').submit(function (e) {
    e.preventDefault();
    //const id = $('#cust-code').val();
   
	 
	if($('#user_type :selected').val() == "Select")
	{
		toastr.error("Select User Type");
		return;
	}
	
    const payload = {
      username:$('#user_name').val(),
      password: $('#pass_word').val(), 
      usertype: $('#user_type :selected').val(),
      displayname: $('#user_display_name').val() 
    };
    var action = $('#actionId').val(); //id ? 'update' : 'create';
    $.ajax({
      url: `api/crud_product_api.php?action=${action}`,
      method: 'POST',
      data: JSON.stringify(payload),
      contentType: 'application/json',
      success: function (response) { 
		   if (response.status == "Success") {
			   toastr.success("Successfully updated.");
				$('#productModal').modal('hide');
				 
				loadProducts();
			}
			else
			{  
				toastr.error(response.status);
				return;
				
			}
        
      } 
    });
  });

  $('#productDiv').on('click', '.editBtn', function () {
    const row = $(this).closest('tr');
	 
    var code = row.data('id'); 
	 
	$('#actionId').val('update');
	$.get('api/crud_product_api.php?action=editrow&code='+code, function (data) {
      let rows = ''; 
	  for(var i=0;i<data.length;i++)
	  { 
			$('#user_name').val(data[i].user_name);
			$('#pass_word').val(data[i].pass_word);
			$('#user_display_name').val(data[i].user_display_name);
			$('#user_type').val(data[i].user_type); 
			 
			$('#user_name').attr("disabled",true);
	  }
	});
    $('#productModalLabel').text('Edit User');
    $('#productModal').modal('show');
  });

  $('#productDiv').on('click', '.deleteBtn', function () {
    if (!confirm('Delete this customer?')) return;
    const id = $(this).closest('tr').data('id');
    $.get(`api/crud_product_api.php?action=delete&code=${id}`, function () {
      loadProducts();
    });
  });
});

  function loadProducts() {
    $.get('api/crud_product_api.php?action=read', function (data) {
      let rows = '';
	   $('#productDiv').html('');
		rows = '<table class="table table-bordered table-hover" id="userTable">';
		rows += '  <thead class="table-dark">';
		rows += ' <tr> <th>User Name</th><th>Password</th><th>Type</th><th>Display Name</th><th>Actions</th></tr>';
		rows += '</thead><tbody>';
			
		  
		
		
	  for(var i=0;i<data.length;i++)
	  { 
		rows += ' <tr data-id="'+data[i].user_name+'">';  
        rows += '     <td>'+ data[i].user_name +'</td>'; 
        rows += '     <td>'+data[i].pass_word +'</td>'; 
        rows += '     <td>'+data[i].user_type +'</td>'; 
        rows += '     <td>'+data[i].user_display_name +'</td>';  
        rows += '     <td>'; 
        rows += '       <button class="btn btn-sm btn-warning editBtn mt-1">Edit</button>'; 
        rows += '       <button class="btn btn-sm btn-danger deleteBtn mt-1">Delete</button>'; 
        rows += '     </td>'; 
        rows += '   </tr>';  
	  }
      rows += ' </tbody></table>';
      $('#productDiv').html(rows);
	 // new DataTable('#userTable');
		var table = $('#userTable').DataTable({  "lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]] });
    });
  }
</script>
 
