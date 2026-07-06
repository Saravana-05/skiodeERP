
<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true" style="margin-top:50px;">
  <div class="modal-dialog modal-lg">
    <form id="customerForm" class="modal-content">
      <div class="modal-header bg_blue">
        <h5 class="modal-title" id="customerModalLabel">Add Customer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
		<div class="row">
			<div class="col-12 text-end">
			<a href="https://services.gst.gov.in/services/searchtp" title="Visit GST Portal" target="_blank">Check GST No</a>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
        <input type="hidden" id="actionId" value="">
		<div class="mb-3">
          <label>Code</label>
          <input type="text" autocomplete="off" id="cust-code" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Name</label>
          <input type="text" autocomplete="off" id="cust-name" class="form-control" required>
        </div>
		<div class="mb-3">
			<label>Customer Type</label> 
			<input class="form-check-input" type="checkbox" name="cust_type" id="credit_cust" value="CREDITCUSTOMER">
			<label class="form-check-label" for="credit_cust" style="font-weight:normal;">
			CREDIT CUSTOMER
			</label> 
			<input class="form-check-input" type="checkbox" name="cust_type" id="vendor_cust" value="VENDOR">
			<label class="form-check-label" for="vendor_cust" style="font-weight:normal;">
			VENDOR
			</label> 
        </div>
		<div class="mb-3">
          <label>Address</label>
          <input type="text" autocomplete="off" id="cust-addr1" class="form-control">
        </div>
		<div class="mb-3">
          
          <input type="text" autocomplete="off" id="cust-addr2" class="form-control">
        </div>
		<div class="row">
			<div class="mb-3 col-6">
			  <label>City</label>
			  <input type="text" autocomplete="off" id="cust-city" class="form-control">
			</div>
			<div class="mb-3 col-6">
			  <label>State</label>
			  
			  <select class="form-select" id="cust-state">
			  <?php
				$state_sql="select * from state_master order by state_name;";
				if($state_qry=mysqli_query($connection,$state_sql))
				{
					while($state_row=mysqli_fetch_array($state_qry))
					{
						echo '<option value="'.$state_row["state_code"].'" '.($state_row["state_code"]==33?"selected":"").'>'.$state_row["state_name"].'</option>';
					}
				}
			  ?>
			  </select>
			</div>
		</div>
		<div class="row">
			<div class="mb-3 col-6">
			  <label>Email Id</label>
			  <input type="text" autocomplete="off" id="cust-email" class="form-control" >
			</div>
			<div class="mb-3 col-6">
			  <label>Opening Balance</label>
			  <input type="text" autocomplete="off" id="cust-opening_balance" value="0.00" class="form-control">
				<div class="form-check">
					<input type="radio" class="form-check-input" id="credit_ob" name="optcrdr" value="1" checked>
					<label class="form-check-label" for="radio1">Cr</label>
				</div>
				<div class="form-check">
					<input type="radio" class="form-check-input" id="debit_ob" name="optcrdr" value="-1">
					<label class="form-check-label" for="radio2">Dr</label>
			    </div>
			</div>			 
		</div>
		<div class="row">
			<div class="mb-3 col-6">
			  <label>GST</label>
			  <input type="text" autocomplete="off" id="cust-gst" class="form-control">
			</div>
			<div class="mb-3 col-6">
			  <label>Phone</label>
			  <input type="text" autocomplete="off" id="cust-phone" class="form-control">
			</div>
		</div>
        
      </div>

	  </div>	  
      <div class="modal-footer">
        <button type="submit" class="btn btn-sm btn-success" id="customer_form_submit_btn">Save</button>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
	  </div>

    </form>
  </div>
</div>
<script >
$(document).ready(function () {
	SetUpBasics();
	
 $('#customerForm').submit(function (e) {
	
    e.preventDefault();
	e.stopPropagation();
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
	
	
	var cust_opening_balance=0.0;
	cust_opening_balance=$("#cust-opening_balance").val();
	if(isNaN(cust_opening_balance)) cust_opening_balance=0.00;
	else cust_opening_balance=parseFloat(cust_opening_balance);
	
	mf=$('input[name="optcrdr"]:checked').val();
	cust_opening_balance*=mf;
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
      email: $('#cust-email').val(),
	  opening_balance:cust_opening_balance
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
	
});
</script>