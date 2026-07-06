<?php include_once "page_guard.php"; ?>
<div class="container-fluid">
	<div class="card mt-3">
	<h5 class="card-header">Import Excel</h5>
	<div class="card-body">
	<h5 class="card-title">  </h5>
	 <form action="api/import_excel_data.php" method="post" enctype="multipart/form-data" id="import_excel_frm">
        <input type="file" name="excel_file" accept=".xls,.xlsx" required>
        <br><br>
        <input type="submit" name="submit" value="Upload" class="btn btn-primary" >
    </form>
	 
	</div>
	</div>
	<div id="result_div">
	</div>
</div> 
<script>
$('#import_excel_frm').submit(function(event) {
        event.preventDefault(); // Prevent default form submission
        // ... AJAX code here ...
   $("#result_div").html("Processing...");
   var form = $('#import_excel_frm')[0];
   var formData = new FormData(form);
    $.ajax({
        type: 'POST', // or 'GET' depending on your server-side handling
        url: 'api/import_excel_data.php', // The URL to send the data
		enctype: 'multipart/form-data',
        processData: false,  // Important!
        contentType: false,
        cache: false,
        data: formData, // The serialized form data
        success: function(response) {
            // Handle successful response from the server
            $("#result_div").html(response);
            // Update UI elements, display success messages, etc.
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Handle errors during the AJAX request
            $("#result_div").html(' Error:'+ textStatus+errorThrown);
            // Display error messages to the user
        }
    });
	});
</script>