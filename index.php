<?php
session_start();
include_once "connect_db.php";
session_write_close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Printzy</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
	<style>
	.material-icons-round {
	    font-family: 'Material Icons Round' !important;
	    font-weight: normal;
	    font-style: normal;
	    font-size: 24px;
	    line-height: 1;
	    letter-spacing: normal;
	    text-transform: none;
	    display: inline-block;
	    white-space: nowrap;
	    word-wrap: normal;
	    direction: ltr;
	    -webkit-font-feature-settings: 'liga';
	    font-feature-settings: 'liga';
	    -webkit-font-smoothing: antialiased;
	    text-rendering: optimizeLegibility;
	    -moz-osx-font-smoothing: grayscale;
	}
	</style>
	<link rel="stylesheet" href="css/toastr.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="js/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
	
	<!--Boostrap-->	
		
	<script src="bs/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>	
	<link href="bs/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
	<!--<script src="js/bootstrap-datepicker.min.js"></script>
	<link href="css/bootstrap-datepicker.min.css" rel="stylesheet">	-->
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
	<link rel="stylesheet" href="css/toastr.min.css">
	<script src="js/toastr.min.js"></script>	
  
<!-- DataTables core -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
 
<!-- DataTables Buttons (MUST come after DataTables) -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css"> 
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

<!-- Optional export dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<!-- Font Awesome for icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
		 
		
	<link href="css/mystyle.css" rel="stylesheet" crossorigin="anonymous">
<script>
function printDiv(data) {
	//div_selector='"#'+divId+'"';
	//data=$("#"+divId).html();
	//data='<div style="font-size:0.6vw;font-weight:normal;">'+data+'</div>';
	w=window.open();
	w.document.write(data);
	w.print();
	w.close();
}
function SetUpBasics()
{
	$(document).off("keyup");
	$(document).on("keyup",function(e)
	{
		if(e.which==113)//F2 key
		{
			if($("#jc-tab").length)//confirm we are @ dashboard
			{
			fnModule(1);
			}
		}
	});
		$(".numericOnly").off("keypress");
		$(".numericOnly").on("keypress",function (e) {  
		if (e.which != 8 && e.which != 46 && e.which != 0 && (e.which < 48 || e.which > 57)) {
			//display error message
			//$("#errmsg").html("Digits Only").show().fadeOut("slow");
			e.preventDefault(); 
				   return false;
		}
	});
	$(".datepicker").removeClass('hasDatepicker').datepicker();
}	
</script>
</head>
<style>
h3 { font-weight: bold; }

.login-page-bg {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
    padding: 2rem 1rem;
}

.login-page-bg > h1 { margin-bottom: 0.5rem; }
.login-page-bg > h1 img { width: 72px; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3)); }

.login-page-bg .card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    overflow: hidden;
    max-width: 420px;
}

.login-page-bg .card-body { padding: 2rem 2rem 1.5rem; }

.login-page-bg .form-group { margin-bottom: 1.25rem; }
.login-page-bg .form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
    font-size: 0.85rem;
    color: #334155;
}
.login-page-bg .form-group input.form-control {
    padding: 0.65rem 0.85rem;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    font-size: 0.92rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.login-page-bg .form-group input.form-control:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
    outline: none;
}

.login-page-bg .btn-success {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border: none;
    border-radius: 10px;
    padding: 0.6rem 2.5rem;
    font-weight: 700;
    font-size: 0.9rem;
    letter-spacing: 0.3px;
    transition: transform 0.15s, box-shadow 0.15s;
}
.login-page-bg .btn-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(34,197,94,0.35);
}

.error {
    width: 95%;
    border-radius: 8px;
    text-align: center;
    font-size: 0.85rem;
    font-weight: 600;
    padding: 6px 0;
}

.ui-datepicker { z-index: 9999 !important; }
</style>
<body>
<div id="window_div" class="container-fluid">
<?php
if(!isset($_SESSION['logged_in']))
{
?>
    <div class="login-page-bg">
        <h1 class="text-center mb-3">
            <img loading="lazy" src="img/printzy_logo.png" alt="Printzy">
        </h1>
        <div class="card" style="width:100%;max-width:420px;">
            <div class="card-body">
                <h3 class="text-success text-center mb-3">Login Gateway</h3>
                <form action="login.php" method="POST">
                    <p class="error" id="errMsg"></p>
                    <div class="form-group">
                        <label for="user_name">User Name</label>
                        <input type="text" autocomplete="off" name="user_name" class="form-control" id="user_name" placeholder="Enter your username" required autofocus />
                    </div>
                    <div class="form-group">
                        <label for="pass_word">Password</label>
                        <div class="position-relative">
                            <input type="password" autocomplete="off" name="pass_word" class="form-control" id="pass_word" placeholder="Enter your password" required />
                            <i class="fa fa-eye" id="togglePassword"
                               style="cursor:pointer;position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:1rem;"></i>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
	
<script>
$(document).ready(function () {
	SetUpBasics();
const togglePassword = document.querySelector("#togglePassword");
const password = document.querySelector("#pass_word");

togglePassword.addEventListener("click", function () {
   
  // toggle the type attribute
  const type = password.getAttribute("type") === "password" ? "text" : "password";
  password.setAttribute("type", type);
  // toggle the eye icon
  this.classList.toggle('fa-eye');
  this.classList.toggle('fa-eye-slash');
});	
  $("form").submit(function (event) {
	  event.preventDefault();
	  if($("#user_name").val()=="")
	  {
		  $("#user_name").focus();
		  return;
	  }
	  if($("#pass_word").val()=="")
	  {
		  $("#pass_word").focus();
		  return;
	  }
	  fnLogin();
  });
});
  
function fnLogin()
{ 
    var uname = $('#user_name').val();
    var pword = $('#pass_word').val();
    $.ajax({
        type: "POST",
        url: "checklogin.php",
        data:{user_name:uname,pass_word:pword},
        success: function (response) {
            if(response=="Failed")
            {
                 $('#errMsg').text("Invalid Credentials.");
                 $('#errMsg').css('color','red');
            } 
            else
            {
				$('#errMsg').text("");
				window.history.replaceState({}, '');
                               
                $("#window_div").load("main.php");
                 
            }
        },
        failure: function (response) {
            //alert(response.d);
        }
    });	
    return false;
 
}
</script>
<?php
}
else
{
	?>
<script>	
 $("#window_div").load("main.php");	
 
</script> 
	<?php
}
?>
</div>
</body>

</html>
