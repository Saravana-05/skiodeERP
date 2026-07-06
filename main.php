<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once "connect_db.php";
?>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/sidebars.css">
<link rel="stylesheet" type="text/css" href="css/dashboard-modern.css">
<style>
.strikeClass
{
	text-decoration: line-through !important;
    text-decoration-color: #fff !important;
}
.highlight
{
	background-color:pink !important;
}
</style>
<div class="cp-layout-wrapper">
<!-- ── Sidebar ──────────────────────────────────────────────────────────── -->
<aside class="cp-sidebar bg-darkblue" id="cpSidebar">
	<div class="offcanvas-md offcanvas-end bg-darkblue" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
		<div class="offcanvas-header">
			<h5 class="offcanvas-title" id="sidebarMenuLabel">Citizen Prints</h5>
			<button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
		</div>
		<div class="offcanvas-body d-md-flex flex-column p-0 overflow-y-auto">
			<!-- Sidebar brand -->
			<div class="cp-sidebar-brand">
				<img loading="lazy" src="img/cp_logo_64px.png" alt="CP" />
				<span>Citizen Prints</span>
			</div>
			<ul class="sidebar-nav">
                    <!--<li class="sidebar-header">
                        Tools & Components
                    </li>-->
					<?php if($_SESSION['user_type'] != "ACCOUNTANT") { ?>
                    <li class="sidebar-item">
                        <a href="javascript:void(0)" class="sidebar-link" onclick="fnSideMenu(1)" >
                            <span class="material-icons-round sidebar-icon">dashboard</span> Dashboard
                        </a>
                    </li>
					<?php } ?>
					<?php
						if($_SESSION['user_type'] == "ADMIN" || $_SESSION['user_type'] == "SUPERADMIN")
						{
							?>
                    <li class="sidebar-item">
                        <a href="javascript:void(0)" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#master"
                            aria-expanded="false" aria-controls="master">
                             <span class="material-icons-round sidebar-icon">settings_applications</span>
                            Master(s)
                        </a>
                        <ul id="master" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a class="sidebar-link" onclick="fnSideMenuSSR('USER_MASTER')" >USER MASTER</a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link" onclick="fnSideMenuSSR('MACHINE_MASTER')" >MACHINE MASTER</a>
                            </li>
							 <li class="sidebar-item">
                                <a class="sidebar-link" onclick="fnSideMenuSSR('PRODUCT_MASTER')" >PRODUCT MASTER</a>
                            </li>
							 <li class="sidebar-item">
                                <a class="sidebar-link" onclick="fnSideMenuSSR('GENERAL_CUSTOMER')" >GENERAL CUSTOMER</a>
                            </li>
						</ul>
					</li>
                    <li class="sidebar-item">
                        <a href="javascript:void(0)" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#accounts"
                            aria-expanded="false" aria-controls="accounts">
                             <span class="material-icons-round sidebar-icon">account_balance_wallet</span>
                            Accounts
                        </a>
                        <ul id="accounts" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a class="sidebar-link" onclick="fnSideMenu(2)" >RECEIPT VOUCHER</a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link" onclick="fnSideMenu(3)" >PAYMENT VOUCHER</a>
                            </li> 
                            <li class="sidebar-item">
                                <a class="sidebar-link" onclick="fnSideMenu(12)" >IMPORT EXCEL</a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link" onclick="fnSideMenuSSR('SUMMARY_RPT')" >SUMMARY REPORT</a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link" onclick="fnSideMenuSSR('CLEAR_DATA')" >CLEAR DATA</a>
                            </li>
							<?php
						}
						else if($_SESSION['user_type'] == "ACCOUNTANT")
						{
							?>
                    <li class="sidebar-item">
                        <a href="javascript:void(0)" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#accounts"
                            aria-expanded="false" aria-controls="accounts">
                             <span class="material-icons-round sidebar-icon">account_balance_wallet</span>
                            Accounts
                        </a>
                        <ul id="accounts" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a class="sidebar-link" onclick="fnSideMenu(2)" >RECEIPT VOUCHER</a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link" onclick="fnSideMenu(3)" >PAYMENT VOUCHER</a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link" onclick="fnSideMenuSSR('SUMMARY_RPT')" >SUMMARY REPORT</a>
                            </li>
                        </ul>
                    </li>
							<?php
						}
							?>
					<?php if($_SESSION['user_type'] != "ACCOUNTANT") { ?>
							<li class="sidebar-item">
                                <a class="sidebar-link" onclick="fnSideMenu(17)" >WASTAGE</a>
                            </li>
					<?php } ?>
<?php
						if($_SESSION['user_type'] == "ADMIN" || $_SESSION['user_type'] == "SUPERADMIN" || $_SESSION['user_type'] == "ACCOUNTANT")
						{
							if($_SESSION['user_type'] != "ACCOUNTANT") {
							?>
                        </ul>
                    </li>
							<?php } ?>
<!--					<li class="sidebar-item">
                        <a href="javascript:void(0)" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#sales"
                            aria-expanded="false" aria-controls="sales">
                             <img loading="lazy" class="sidemenuImgCls" src="img/sales.png" alt="Dashboard" />
                            Sales
                        </a>
                        <ul id="sales" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link">JOB CARD</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link">JOB CARD TRANSACTION</a>
                            </li> 
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link">SALES QUOTE</a>
                            </li> 
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link">SALES INVOICE</a>
                            </li> 
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link strikeClass">WASTAGE ENTRY</a>
                            </li> 
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link strikeClass">DAILY CLOSING ENTRY</a>
                            </li> 
                        </ul>
                    </li>-->
<!--					<li class="sidebar-item">
                        <a href="javascript:void(0)" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#customers"
                            aria-expanded="false" aria-controls="customers">
                             <img loading="lazy" class="sidemenuImgCls" src="img/customers.png" alt="Dashboard" />
                            Customers
                        </a>
                        <ul id="customers" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link collapsed strikeClass" data-bs-toggle="collapse"
                                    data-bs-target="#cust_info" aria-expanded="false" aria-controls="cust_info">
                                    CUSTOMERS INFORMATION
                                </a>
                                <ul id="cust_info" class="sidebar-dropdown list-unstyled collapse">
                                    <li class="sidebar-item">
                                        <a href="javascript:void(0)" class="sidebar-link strikeClass">CUSTOMERS LEDGER</a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a href="javascript:void(0)" class="sidebar-link strikeClass">CUSTOMER'S CREDIT BALANCE</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link strikeClass">SALES QUOTE REPORT</a>
                            </li> 
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link strikeClass">SALES INVOICE REPORT</a>
                            </li>  
                        </ul>
                    </li> -->
					<li class="sidebar-item">
                        <a href="javascript:void(0)" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#purchases"
                            aria-expanded="false" aria-controls="purchases">
                             <span class="material-icons-round sidebar-icon">shopping_cart</span>
                            Purchases
                        </a>
                        <ul id="purchases" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
<!--                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link collapsed strikeClass" data-bs-toggle="collapse"
                                    data-bs-target="#supplier_info" aria-expanded="false" aria-controls="supplier_info">
                                    SUPPLIERS INFORMATION
                                </a>
                                <ul id="supplier_info" class="sidebar-dropdown list-unstyled collapse">
                                    <li class="sidebar-item">
                                        <a href="javascript:void(0)" class="sidebar-link strikeClass">SUPPLIERS LEDGER</a>
                                    </li> 
                                </ul>
                            </li>-->
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link" onclick="fnSideMenu(13)">PURCHASE INVOICE</a>
                            </li> 
<!--                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link strikeClass">PURCHASE BALANCE REPORT</a>
                            </li> -->
                        </ul>
                    </li> 
					<li class="sidebar-item">
                        <a href="javascript:void(0)" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#inventory"
                            aria-expanded="false" aria-controls="inventory">
                             <span class="material-icons-round sidebar-icon">inventory_2</span>
                            Inventory
                        </a>
                        <ul id="inventory" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
<!--                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link collapsed strikeClass" data-bs-toggle="collapse"
                                    data-bs-target="#stock_info" aria-expanded="false" aria-controls="stock_info">
                                    STOCK INFORMATION
                                </a>
                                <ul id="stock_info" class="sidebar-dropdown list-unstyled collapse">
                                    <li class="sidebar-item">
                                        <a href="javascript:void(0)" class="sidebar-link strikeClass">STOCK ITEM</a>
                                    </li> 
                                    <li class="sidebar-item">
                                        <a href="javascript:void(0)" class="sidebar-link strikeClass">STOCK GROUP</a>
                                    </li> 
                                </ul>
                            </li>-->
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link" onclick="fnSideMenu(14)">STOCK SUMMARY</a>
                            </li> 
<!--                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link strikeClass">PHYSICAL STOCK ADJUSTMENTS </a>
                            </li>  
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link strikeClass">ITEMWISE SALES REPORT</a>
                            </li>  -->
                        </ul>
                    </li> 
<!--					<li class="sidebar-item">
                        <a href="javascript:void(0)" class="sidebar-link collapsed strikeClass" data-bs-toggle="collapse" data-bs-target="#expenses"
                            aria-expanded="false" aria-controls="expenses">
                             <img loading="lazy" class="sidemenuImgCls" src="img/expenses.png" alt="Dashboard" />
                            Expenses
                        </a>
                        <ul id="expenses" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar"> 
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link strikeClass">INDIRECT EXPENSES</a>
                            </li> 
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link strikeClass">LIST</a>
                            </li>   
                        </ul>
                    </li> -->
					<?php 
						}
					?>
					<li class="sidebar-item">
                        <a href="javascript:void(0)" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#reports"
                            aria-expanded="false" aria-controls="reports">
                             <span class="material-icons-round sidebar-icon">assessment</span>
                            Reports
                        </a>
                        <ul id="reports" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
							<li class="sidebar-item">
                                <a href="javascript:void(0)" onclick="fnSideMenu(4)" class="sidebar-link">JOB CARD REPORT</a>
                            </li>
							
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#sale_trans_report" aria-expanded="false" aria-controls="sale_trans_report">
                                   SALES TRANSACTION REPORT
                                </a>
                                <ul id="sale_trans_report" class="sidebar-dropdown list-unstyled collapse">
                                    <li class="sidebar-item">
                                        <a href="javascript:void(0)" onclick="fnSideMenu(5)" class="sidebar-link">SALES QUOTE REPORT</a>
                                    </li> 
                                    <li class="sidebar-item">
                                        <a href="javascript:void(0)" onclick="fnSideMenu(6)" class="sidebar-link">SALES INVOICE REPORT</a>
                                    </li> 
                                </ul>
                            </li>
							
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#customers_report" aria-expanded="false" aria-controls="customers_report">
                                   CUSTOMERS REPORT
                                </a>
                                <ul id="customers_report" class="sidebar-dropdown list-unstyled collapse">
                                    <li class="sidebar-item">
                                        <a href="javascript:void(0)" onclick="fnSideMenu(8)" class="sidebar-link">CUSTOMERS LEDGER REPORT</a>
                                    </li> 
                                    <li class="sidebar-item">
                                        <a href="javascript:void(0)" onclick="fnSideMenu(9)" class="sidebar-link">CUSTOMER CREDIT BALANCE</a>
                                    </li> 
                                </ul>
                            </li>
					<?php 
						if($_SESSION['user_type'] == "ADMIN" || $_SESSION['user_type'] == "SUPERADMIN")
						{
							?>							
<!--                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link collapsed strikeClass" data-bs-toggle="collapse"
                                    data-bs-target="#suppliers_report" aria-expanded="false" aria-controls="suppliers_report">
                                   SUPPLIERS REPORT
                                </a>
                                <ul id="suppliers_report" class="sidebar-dropdown list-unstyled collapse">
                                    <li class="sidebar-item">
                                        <a href="javascript:void(0)" class="sidebar-link strikeClass">PURCHASE TRANSACTION REPORT</a>
                                    </li> 
                                    <li class="sidebar-item">
                                        <a href="javascript:void(0)" class="sidebar-link strikeClass">PURCHASE BALANCE REPORT</a>
                                    </li> 
                                </ul>
                            </li>  
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link strikeClass">SALES TAX REPORT</a>
                            </li> 
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link strikeClass">PURCHASE TAX REPORT</a>
                            </li>-->  
                            <li class="sidebar-item">
                                <a href="javascript:void(0)"  onclick="fnSideMenu(16)" class="sidebar-link">JOURNAL REPORT</a>
                            </li> 							
						<?php 
							}
						?>								
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" onclick="fnSideMenuSSR('WASTAGE_RPT')"  class="sidebar-link">WASTAGE REPORT</a>
                            </li>   
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" onclick="fnSideMenu(11)" class="sidebar-link">MACHINE REPORT</a>
                            </li>  
                            <li class="sidebar-item">
                                <a href="javascript:void(0)"  onclick="fnSideMenu(10)" class="sidebar-link">ITEMWISE SALES REPORT</a>
                            </li> 
                            <li class="sidebar-item">
                                <a href="javascript:void(0)"  onclick="fnSideMenu(15)" class="sidebar-link">EOD REPORT</a>
                            </li>
 
						
							 <li class="sidebar-item">
                                <a href="javascript:void(0)"  onclick="fnSideMenu(18)" class="sidebar-link">PRODUCT WISE SALES REPORT</a>
                            </li> 
							<?php if($_SESSION['user_type'] != "ACCOUNTANT") { ?>
							<li class="sidebar-item">
                                <a href="javascript:void(0)"  onclick="fnSideMenu(19)" class="sidebar-link">HISTORY REPORT</a>
                            </li>
							<?php } ?>
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" onclick="fnSideMenu(22)" class="sidebar-link">📱 QR PAYMENT REPORT</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" onclick="fnSideMenu(23)" class="sidebar-link">🔍 QR DEBUG COMPARE</a>
                            </li>
                        </ul>
                    </li> 
					<?php 
						if($_SESSION['user_type'] == "ADMIN" || $_SESSION['user_type'] == "SUPERADMIN")
						{
					?>					 
<!--					<li class="sidebar-item">
                        <a href="javascript:void(0)" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#admin"
                            aria-expanded="false" aria-controls="admin">
                             <img loading="lazy" class="sidemenuImgCls" src="img/admin.png" alt="Dashboard" />
                            Admin
                        </a>
                        <ul id="admin" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link collapsed strikeClass" data-bs-toggle="collapse"
                                    data-bs-target="#employee_details" aria-expanded="false" aria-controls="employee_details">
                                    EMPLOYEES DETAILS
                                </a>
                                <ul id="employee_details" class="sidebar-dropdown list-unstyled collapse">
                                    <li class="sidebar-item">
                                        <a href="javascript:void(0)" class="sidebar-link collapsed strikeClass" data-bs-toggle="collapse"
                                    data-bs-target="#user_maintanence" aria-expanded="false" aria-controls="user_maintanence">
                                    USER MAINTANENCE
										</a>
										<ul id="user_maintanence" class="sidebar-dropdown list-unstyled collapse">
											<li class="sidebar-item">
												<a href="javascript:void(0)" class="sidebar-link strikeClass">USERS MANGEMENT</a>
											</li> 
											<li class="sidebar-item">
												<a href="javascript:void(0)" class="sidebar-link strikeClass">USER LOG INFO</a>
											</li>  
											<li class="sidebar-item">
												<a href="javascript:void(0)" class="sidebar-link strikeClass">ACTIVE USERS</a>
											</li> 
										</ul>
                                    </li> 
                                </ul>
                            </li> 
							
							<li class="sidebar-item">
								<a href="javascript:void(0)" class="sidebar-link strikeClass">COPIER INFORMATION</a>
							</li>  
							<li class="sidebar-item">
								<a href="javascript:void(0)" class="sidebar-link strikeClass">LEDGERS</a>
							</li> 
							<li class="sidebar-item">
								<a href="javascript:void(0)" class="sidebar-link strikeClass">GST CLAUSES</a>
							</li> 
							<li class="sidebar-item">
								<a href="javascript:void(0)" class="sidebar-link strikeClass">OVERALL DAY REPORT</a>
							</li> 
                        </ul>
                    </li> 
					<li class="sidebar-item">
                        <a href="javascript:void(0)" class="sidebar-link strikeClass">
                            <img loading="lazy" class="sidemenuImgCls" src="img/settings.png" alt="Dashboard" /> User Settings
                        </a>
                    </li>-->
				
						<?php
						}
						else if($_SESSION['user_type'] == "OPERATOR")
						{
						?>

						<?php
						}
						?>
						<?php // SARAVANA - START (Advance Payment Feature - sidebar menu) ?>
						<li class="sidebar-item">
                            <a href="javascript:void(0)" class="sidebar-link" onclick="fnSideMenu(21)">
                                    <span class="material-icons-round sidebar-icon">payments</span> Advance Payment
                            </a>
                        </li>

						<?php // SARAVANA - END ?>

						<li class="sidebar-item">
							<a href="https://citizenprintz.in/backoffice_erp/dashboard.php" target="_blank" class="sidebar-link">
								<span class="material-icons-round sidebar-icon">open_in_new</span> Back Office ERP
							</a>
						</li>
						<li class="sidebar-item">
							<a  class="sidebar-link" onclick="logout()">
								<span class="material-icons-round sidebar-icon">logout</span> Exit
							</a>
						</li>
					</ul>
				</div>
			</div>
</aside>
<!-- ── Main Column (header + content) ───────────────────────────────────── -->
<div class="cp-main-column">
	<!-- Top header bar -->
	<header class="cp-topbar">
		<button class="cp-topbar-toggle d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-label="Toggle navigation">
			<span class="material-icons-round">menu</span>
		</button>
		<h1 class="cp-topbar-title">Dashboard</h1>
		<div class="cp-topbar-right">
			<div class="cp-user-badge">
				<span class="material-icons-round">account_circle</span>
				<span class="cp-user-name"><?php echo $_SESSION['user_display_name']; ?></span>
				<span class="cp-user-role"><?php echo $_SESSION['user_type']; ?></span>
			</div>
		</div>
	</header>
	<main id="rightContentDiv" class="cp-content">
	</main>
</div>
<div id="globalView" style="position:fixed;z-index:150;top:0;left:0;right:0;bottom:0;background:rgba(15,23,42,0.6);backdrop-filter:blur(2px);display:none;padding:12px;">
	<div style="position:relative;width:100%;height:100%;background:#fff;border-radius:14px;box-shadow:0 20px 60px rgba(0,0,0,0.25);overflow:hidden;">
		<div onclick="close_globalView()" style="position:absolute;right:12px;top:10px;z-index:151;cursor:pointer;"><i class="fa-solid fa-circle-xmark fa-2x" style="color:#ef4444;"></i></div>
		<div id="globalViewContentDiv" style="width:100%;height:100%;overflow:auto;padding:8px;"></div>
	</div>
</div>
</div>

<!-- ── Navigation guard: warn before leaving unsaved Job Card ──────────────── -->
<div class="modal fade" id="jcDirtyModal" tabindex="-1" aria-labelledby="jcDirtyModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-warning">
      <div class="modal-header" style="background:#ffc107;">
        <h5 class="modal-title" id="jcDirtyModalLabel" style="font-weight:bold;">
          ⚠️ Unsaved Job Card
        </h5>
      </div>
      <div class="modal-body">
        <p style="margin-bottom:0;">The Job Card has <strong>unsaved changes</strong>.<br>
        Please <strong>SAVE</strong> the record before navigating away,<br>
        or click <em>Leave Without Saving</em> to discard all changes.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">✏️ Stay &amp; Save</button>
        <button type="button" class="btn btn-danger" id="jcDirtyLeaveBtn">🚪 Leave Without Saving</button>
      </div>
    </div>
  </div>
</div>

<script>
// ── Navigation guard helpers ──────────────────────────────────────────────────
window._jcDirty      = false; // set true by job_card.php when form is touched
var _pendingNavFn    = null;  // callback to run after user confirms "leave"

function _fnCheckDirtyNav(navFn) {
    // Only intercept when job card screen is active and has unsaved changes
    if (window._jcDirty && $('#saveBtn').length > 0 && !$('#saveBtn').prop('disabled')) {
        _pendingNavFn = navFn;
        var modal = new bootstrap.Modal(document.getElementById('jcDirtyModal'));
        modal.show();
        return false; // navigation blocked — wait for user choice
    }
    return true; // proceed
}

// Called by "Leave Without Saving" button — run pending nav after modal closes
$(document).on('click', '#jcDirtyLeaveBtn', function() {
    var fn = _pendingNavFn;
    window._jcDirty = false;
    _pendingNavFn   = null;
    var inst = bootstrap.Modal.getInstance(document.getElementById('jcDirtyModal'));
    if (inst) inst.hide();
    if (fn) setTimeout(fn, 150); // run after modal animation completes
});

function editJobCard(job_card_nos,sino="")
{
	$("#globalViewContentDiv").load("job_card.php",function(){
	$("#jobcard_no_txt").text(job_card_nos);
	$("#theOperation").text("FOR_EDIT");
	
	load_job_cards_from_db("FOR_EDIT");
	
	$("#globalView").show();
	});	
}
function showJobCard(job_card_nos,sqno="",sino="")
{
	$("#globalViewContentDiv").load("job_card.php",function(){
	$("#jobcard_no_txt").text(job_card_nos);
	load_job_cards_from_db("JUST_SHOW");
	if(sqno!="")
	{
		$("#sq_no_txt").text(sqno);
		load_sq_details_from_db();
	}
	if(sino!="")
	{
		$("#si_no_txt").text(sino);
		set_si_ui();
	}
	
	$("#globalView").show();
	});	
}
$(document).ready(function() {
	SetUpBasics();
	// Restore the last visited page on refresh; default to Dashboard (1)
	var lastPage = parseInt(localStorage.getItem('cp_last_page')) || 1;
	<?php if($_SESSION['user_type'] == "ACCOUNTANT") { ?>
	if(lastPage == 1) lastPage = 2;
	<?php } ?>
	fnSideMenu(lastPage);
	$('.sidebar-link').on('click',function(evt){
		$(".sidebar-link").removeClass("active");
		$(this).addClass("active");
	});
});
function logout()
{
	$.ajax({
			type: "POST",
			url: "logout.php",
			success: function (response) {
				if(response=="Failed")
				{
					 alert(response);
				} 
				else
				{
					
					localStorage.removeItem('cp_last_page'); // reset page on logout
					//$("#window_div").load("index.php");
					location.reload();
				}
			},
			failure: function (response) {
				//alert(response.d);
			}
		});				
}

var _ssrTitles = {
	'USER_MASTER':'User Master','MACHINE_MASTER':'Machine Master',
	'PRODUCT_MASTER':'Product Master','GENERAL_CUSTOMER':'General Customer',
	'SUMMARY_RPT':'Summary Report','WASTAGE_RPT':'Wastage Report','CLEAR_DATA':'Clear Data'
};
function fnSideMenuSSR(pPageId)
{
	if (!_fnCheckDirtyNav(function() { fnSideMenuSSR(pPageId); })) return;
	_setTopbarTitle(_ssrTitles[pPageId] || pPageId);
	switch(pPageId)
	{
		case 'USER_MASTER':
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("user_master.php");
			break;
		case 'MACHINE_MASTER':
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("machine_master.php");
			break;
		case 'PRODUCT_MASTER':
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("product_master.php");
			break;
		case 'GENERAL_CUSTOMER':
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("edit_general_customer.php");
			break;
		case 'SUMMARY_RPT':
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("summary_report.php");
			break;
		case 'WASTAGE_RPT':
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("wastage_report.php");
			break;
		case 'CLEAR_DATA':
			if (confirm("Are you sure about clearing Data ?!!") == true) {
				access_code=prompt("Enter Access Code");
				$.ajax({
						type: "POST",
						url: "api/clear_data.php",
						data:{"access_code":access_code},
						success: function (response) {
							if(response=="Failed")
							{
								 alert(response);
							} 
							else
							{
								alert("Cleared Old Data");
								$("#rightContentDiv").load("dashboard.php");
								 
							}
						},
						failure: function (response) {
							//alert(response.d);
						}
					});				
			}
			
			
	}
}
var _pageTitles = {
	1:'Dashboard', 2:'Receipt Voucher', 3:'Payment Voucher', 4:'Job Card Report',
	5:'Sales Quote Report', 6:'Sales Invoice Report', 7:'Journal Report',
	8:'Customer Ledger', 9:'Credit Balance', 10:'Itemwise Sales Report',
	11:'Machine Report', 12:'Import Excel', 13:'Purchase Invoice',
	14:'Stock Summary', 15:'EOD Report', 16:'Journal Report',
	17:'Wastage Entry', 18:'Product Wise Sales', 19:'History Report',
	20:'Job Card Closing', 21:'Advance Payment', 22:'QR Payment Report', 23:'QR Debug Compare'
};
function _setTopbarTitle(t){ if($('.cp-topbar-title').length) $('.cp-topbar-title').text(t); }

function fnSideMenu(pPageId)
{
	if (!_fnCheckDirtyNav(function() { fnSideMenu(pPageId); })) return;
	_setTopbarTitle(_pageTitles[pPageId] || 'Dashboard');
	// Remember this page so refresh restores it
	localStorage.setItem('cp_last_page', pPageId);
	// Stop dashboard auto-refresh when navigating away
	if(pPageId !== 1 && window._jcAutoRefresh) {
		clearInterval(window._jcAutoRefresh);
		window._jcAutoRefresh = null;
	}
	if(pPageId !== 1 && window._qrAutoRecoveryInterval) {
		clearInterval(window._qrAutoRecoveryInterval);
		window._qrAutoRecoveryInterval = null;
	}
	switch(pPageId)
	{
		case 1:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("dashboard.php");
			break;
		case 2:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("receipt_voucher.php");
			break;
		case 3:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("payment_voucher.php");
			break;
		case 4:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("job_card_report.php");
			break;
		case 5:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("sale_quotation_report.php");
			break;
		case 6:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("sale_invoice_report.php");
			break;
		case 7:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("journal_report.php");
			break;
		case 8:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("ledger_report.php");
			break;
		case 9:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("trial_balance_report.php");
			break;
		case 10:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("itemwise_sales_report.php");
			break;
		case 11:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("machine_usage_report.php");
			break;
		case 12:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("import_excel.php");
			break;
		case 13:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("purchase_master.php");
			break;
		case 14:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("current_stock.php");
			break;
		case 15:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("eod_report.php");
			break;
		case 16:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("journal_report.php");
			break;
		case 17:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("wastage_entry.php");
			break;
		case 18:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("productwise_sales_report.php");
			break;
		case 19:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("history_report.php");
			break;
		
		case 20:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("job_card_closing.php");
			break;

		// SARAVANA - START (Advance Payment Feature - routing case)
		case 21:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("advance_payment.php");
			break;
		// SARAVANA - END
		case 22:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("qr_payment_report.php");
			break;
		case 23:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("qr_debug_compare.php");
			break;
	}
}
var _modTitles = {1:'Job Card',3:'Sales Invoice',4:'Customer Master',5:'Receipt Voucher',6:'Payment Voucher',8:'Bill Payable',9:'User Master',10:'EOD Process',14:'Master Data'};
function fnModule(pType)
{
	_setTopbarTitle(_modTitles[pType] || 'Dashboard');
	switch(pType)
	{
		case 1:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("job_card.php");
			break;
		case 3:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("sale_invoice.php");
			break;
		case 4:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("credit_customer_crud.php");
			break;
		case 5:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("receipt_voucher.php");
			break;
		case 6:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("payment_voucher.php");
			break;
		case 7:
			$("#rightContentDiv").empty();
			//$("#rightContentDiv").load("bill_receipt.php");
			break;
		case 8:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("bill_payable.php");
			break;
		case 9:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("user_master.php");
			break;
		case 10:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("eod_process.php");
			break;
		case 14:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("data_entry.php");
			break;			
			
	}
}
</script>