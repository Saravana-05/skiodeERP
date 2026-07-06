<?php
session_start();
include_once "connect_db.php";
include_once "page_guard.php";
?>
<style>
/* ── Layout: full width, no sidebar ── */
.sqr-page {
	display: block; min-height: 100vh; background: #f1f5f9;
}
.sqr-main {
	min-width: 0; padding: 16px 18px;
}
.sqr-sidebar {
	display: none;
}

/* ── Summary Drawer ── */
.sqr-summary-overlay {
	display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
	background: rgba(15,23,42,0.4); z-index: 9998;
}
.sqr-summary-overlay.open { display: block; }
.sqr-summary-drawer {
	position: fixed; top: 0; right: -360px; width: 340px; height: 100vh;
	background: #fff; box-shadow: -4px 0 24px rgba(15,23,42,0.15);
	z-index: 9999; transition: right 0.3s ease; overflow-y: auto;
}
.sqr-summary-drawer.open { right: 0; }
.sqr-drawer-header {
	background: #1e293b; color: #fff; padding: 14px 18px;
	display: flex; align-items: center; justify-content: space-between;
	position: sticky; top: 0; z-index: 1;
}
.sqr-drawer-header h6 { margin: 0; font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 8px; }
.sqr-drawer-close {
	background: none; border: none; color: #94a3b8; font-size: 1.3rem;
	cursor: pointer; padding: 4px; line-height: 1; transition: color 0.2s;
}
.sqr-drawer-close:hover { color: #fff; }
.sqr-drawer-body { padding: 16px; }
.sqr-drawer-body .col-12 { padding: 0 !important; margin-bottom: 12px; }
.sqr-drawer-body table { width: 100%; font-size: 0.82rem; border-collapse: collapse; }
.sqr-drawer-body table td { padding: 3px 5px; }
.sqr-drawer-body h5 { font-size: 0.85rem !important; font-weight: 700 !important; margin-bottom: 4px !important; }
.sqr-summary-btn {
	display: inline-flex; align-items: center; gap: 5px; padding: 7px 14px;
	background: #1e293b; color: #fff; border: none; border-radius: 8px;
	font-size: 0.82rem; font-weight: 700; cursor: pointer; transition: all 0.2s;
	white-space: nowrap;
}
.sqr-summary-btn:hover { background: #334155; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(30,41,59,0.3); }
.sqr-summary-btn .material-icons-round { font-size: 18px; }

/* ── Main header ── */
.sqr-main-hdr {
	display: flex; align-items: center; gap: 12px; margin-bottom: 14px;
}
.sqr-main-hdr-icon {
	width: 40px; height: 40px; background: #eef2ff; border-radius: 10px;
	display: flex; align-items: center; justify-content: center;
}
.sqr-main-hdr-icon .material-icons-round { font-size: 22px; color: #4f46e5; }
.sqr-main-hdr h2 { font-size: 1.4rem; font-weight: 800; color: #1e293b; margin: 0; }

/* ── Filter bar (top) ── */
.sqr-filter-bar {
	background: #fff; border-radius: 10px; padding: 10px 14px; margin-bottom: 12px;
	box-shadow: 0 1px 4px rgba(0,0,0,.06); border: 1px solid #e2e8f0;
	display: flex; align-items: flex-end; gap: 8px; flex-wrap: nowrap;
}
.sqr-fg { display: flex; flex-direction: column; gap: 2px; flex: 1; min-width: 0; }
.sqr-fg label {
	font-size: 0.7rem; font-weight: 700; color: #334155;
	text-transform: uppercase; letter-spacing: 0.3px; white-space: nowrap;
}
.sqr-fg input, .sqr-fg select {
	padding: 6px 6px; border: 1.5px solid #e2e8f0; border-radius: 6px;
	font-size: 0.82rem; font-weight: 500; color: #1e293b; background: #fff; outline: none;
	width: 100%; box-sizing: border-box;
}
.sqr-fg input:focus, .sqr-fg select:focus { border-color: #3b82f6; }
.sqr-btn-search {
	padding: 7px 12px; border: none; border-radius: 6px; font-size: 0.82rem;
	font-weight: 700; cursor: pointer; background: #3b82f6; color: #fff;
	display: flex; align-items: center; gap: 4px; white-space: nowrap;
}
.sqr-btn-search:hover { background: #2563eb; }
.sqr-btn-reset {
	padding: 7px 10px; border: 1.5px solid #e2e8f0; border-radius: 6px;
	font-size: 0.82rem; font-weight: 700; cursor: pointer; background: #fff; color: #64748b;
	display: flex; align-items: center; gap: 4px; white-space: nowrap;
}
.sqr-btn-reset:hover { background: #f8fafc; }
.sqr-btn-search .material-icons-round,
.sqr-btn-reset .material-icons-round { font-size: 16px; }

/* ── Sidebar header ── */
.sqr-sb-hdr {
	background: #2c3e6b; color: #fff; padding: 14px 16px;
	font-size: 1rem; font-weight: 700;
	display: flex; align-items: center; gap: 8px;
}
.sqr-sb-hdr .material-icons-round { font-size: 19px; }

/* ── Sidebar summary panels ── */
#sidebarPanels .row { margin: 0 !important; }
#sidebarPanels .col-12 { padding: 0 !important; width: 100% !important; max-width: 100% !important; }
#sidebarPanels .bg-info { border-radius: 0 !important; }
#sidebarPanels table { width: 100% !important; font-size: 0.88rem; }
#sidebarPanels table td { padding: 4px 6px !important; word-break: break-word; font-weight: 600; color: #1e293b; }
#sidebarPanels table td div { word-break: break-all; min-width: 0; }
#sidebarPanels .btn { font-size: 0.85rem; font-weight: 700; padding: 6px 12px !important; }
#sidebarPanels h5 { font-size: 0.95rem !important; font-weight: 700 !important; }

/* ── DataTables ── */
.dataTables_wrapper { font-size: 0.9rem; font-weight: 500; }
div.dt-container div.dt-length select { width: 50px; }
.dt-buttons { display: inline-flex; gap: 6px; }
.buttons-excel {
	background-color: #22c55e !important; color: #fff !important;
	border: none !important; border-radius: 6px; font-weight: 600;
	padding: 6px 14px !important; font-size: 0.78rem !important;
}
.buttons-excel:hover { background-color: #16a34a !important; }
.dt-button { font-weight: 600; font-size: 0.78rem; padding: 6px 14px !important; height: auto; line-height: 1.3; }

/* ── Table ── */
#resultDetailTable { width: 100% !important; border-collapse: collapse; font-size: 0.88rem; }
#resultDetailTable thead th {
	background: #2c3e6b !important; color: #fff !important; font-weight: 700;
	font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.3px;
	padding: 10px 8px !important; border: none !important;
	border-right: 1px solid rgba(255,255,255,0.1) !important;
	white-space: nowrap; text-align: center;
}
#resultDetailTable thead th:last-child { border-right: none !important; }
#resultDetailTable tbody td {
	padding: 8px 7px !important; border-bottom: 1px solid #f1f5f9 !important;
	border-right: none !important; border-left: none !important;
	vertical-align: middle; text-align: center; font-size: 0.88rem; font-weight: 500; color: #1e293b;
}
#resultDetailTable tbody tr:hover { background: #f0f4ff; }
#resultDetailTable tbody tr:nth-child(even) { background: #fafbfc; }
.highlight { background-color: #fef9c3 !important; }

/* ── Table full width ── */
#reportResultDiv { overflow-x: hidden; }
#reportResultDiv > .row > .col-10 { width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important; padding: 0 !important; }
#reportResultDiv > .row > .col-2 { display: none !important; }
#reportResultDiv > .row { flex-wrap: wrap; margin: 0 !important; }
#resultDetailTable { width: 100% !important; font-size: 0.82rem; }
#resultDetailTable thead th { padding: 8px 6px !important; font-size: 0.75rem; }
#resultDetailTable tbody td { padding: 6px 5px !important; font-size: 0.82rem; }

/* ── Type badges ── */
.type-badge {
	display: inline-block; padding: 3px 10px; border-radius: 12px;
	font-size: 0.75rem; font-weight: 700; letter-spacing: 0.3px; white-space: nowrap;
}
.type-credit   { background: #fee2e2; color: #dc2626; }
.type-walkin   { background: #dbeafe; color: #2563eb; }
.type-general  { background: #d1fae5; color: #059669; }
.type-cash     { background: #fef9c3; color: #ca8a04; }
.type-online   { background: #e0e7ff; color: #4f46e5; }
.type-default  { background: #f1f5f9; color: #475569; }

#printContentDiv { display: none; }
@media print { .sqr-sidebar { display: none; } .sqr-filter-bar { display: none; } .sqr-main { padding: 0; } }
</style>

<div class="sqr-page">
	<!-- ══ MAIN (left) ══ -->
	<div class="sqr-main">
		<div class="sqr-main-hdr">
			<div class="sqr-main-hdr-icon"><span class="material-icons-round">receipt_long</span></div>
			<h2>Sale Quotation Report</h2>
		</div>

		<!-- ── Filter Bar (top) ── -->
		<div class="sqr-filter-bar">
			<div class="sqr-fg">
				<label>From Date</label>
				<input type="text" autocomplete="off" class="datepicker" id="fromDtTxt" value="<?php echo date("d-m-Y");?>" />
			</div>
			<div class="sqr-fg">
				<label>To Date</label>
				<input type="text" autocomplete="off" class="datepicker" id="toDtTxt" value="<?php echo date("d-m-Y");?>" />
			</div>
			<div class="sqr-fg">
				<label>Customer</label>
				<select id="customer_code_txt">
					<option value="all">All</option>
					<option value="walkin">WalkIn</option>
					<option value="general">General</option>
					<?php
						$sql="select customer_code,customer_name from customer_master order by customer_name;";
						if($qry=mysqli_query($connection,$sql))
							while($row=mysqli_fetch_array($qry))
								echo '<option value="'.$row["customer_code"].'">'.$row["customer_name"].'</option>';
					?>
				</select>
			</div>
			<div class="sqr-fg">
				<label>User</label>
				<select id="user_name_txt">
					<?php
						if($_SESSION["user_type"]!="OPERATOR") {
							echo '<option value="all">All</option>';
							$sql="select * from user_master;";
							if($qry=mysqli_query($connection,$sql))
								while($row=mysqli_fetch_array($qry))
									echo '<option value="'.$row["user_name"].'">'.$row["user_display_name"].'</option>';
						} else {
							echo '<option value="'.$_SESSION['user_name'].'">'.$_SESSION['user_display_name'].'</option>';
						}
					?>
				</select>
			</div>
			<div class="sqr-fg">
				<label>Pay Mode</label>
				<select id="pay_mode_txt">
					<option value="all">All</option>
					<option value="cash">Cash</option>
					<option value="online">Online</option>
					<option value="credit">Credit</option>
				</select>
			</div>
			<div class="sqr-fg">
				<label>Product Type</label>
				<select id="product_type_txt">
					<option value="all">All</option>
					<option value="PRINTING">Printing</option>
					<option value="SERVICES">Services</option>
					<option value="MATERIALS">Materials</option>
					<option value="IDCARDS">ID Cards</option>
					<option value="PRODUCTS">Products</option>
				</select>
			</div>
			<button type="button" class="sqr-btn-search" onclick="fnSearch()">
				<span class="material-icons-round">search</span> Search
			</button>
			<button type="button" class="sqr-btn-reset" onclick="fnReset()">
				<span class="material-icons-round">restart_alt</span> Reset
			</button>
			<button type="button" class="sqr-summary-btn" id="sqrSummaryBtn" onclick="toggleSQRDrawer()" style="display:none;">
				<span class="material-icons-round">assessment</span> Summary
			</button>
		</div>

		<div id="reportResultDiv"></div>
	</div>
</div>

<!-- Summary Drawer -->
<div class="sqr-summary-overlay" id="sqrOverlay" onclick="toggleSQRDrawer()"></div>
<div class="sqr-summary-drawer" id="sqrDrawer">
	<div class="sqr-drawer-header">
		<h6><span class="material-icons-round" style="font-size:20px;">assessment</span> Summary</h6>
		<button class="sqr-drawer-close" onclick="toggleSQRDrawer()"><span class="material-icons-round">close</span></button>
	</div>
	<div class="sqr-drawer-body" id="sidebarPanels"></div>
</div>
<div id="printContentDiv"></div>

<script>
$(document).ready(function() {
	SetUpBasics();
	$('.datepicker').removeClass('hasDatepicker').datepicker({
		<?php if($_SESSION["user_type"]=="OPERATOR") { ?>
		minDate: -2,
		<?php } else if($_SESSION["user_type"]=="ACCOUNTANT") { ?>
		minDate: -7,
		<?php } ?>
		dateFormat: 'dd-mm-yy'
	});
});

function fnReset() {
	$('#fromDtTxt').val('<?php echo date("d-m-Y");?>');
	$('#toDtTxt').val('<?php echo date("d-m-Y");?>');
	$('#customer_code_txt').val('all');
	$('#user_name_txt').val('all');
	$('#pay_mode_txt').val('all');
	$('#product_type_txt').val('all');
	$('#reportResultDiv').html('');
	$('#sidebarPanels').html('');
	$('#sqrSummaryBtn').hide();
	closeSQRDrawer();
}

function toggleSQRDrawer() {
	$('#sqrDrawer').toggleClass('open');
	$('#sqrOverlay').toggleClass('open');
}
function closeSQRDrawer() {
	$('#sqrDrawer').removeClass('open');
	$('#sqrOverlay').removeClass('open');
}

function printSaleQuoteDiv(divId) {
	var fromDt=$('#fromDtTxt').val();
	var toDt=$('#toDtTxt').val();
	var header='';
	header+='<style>*{font-size:12px;}th {border-bottom:1px solid #000;"}td {border-bottom:1px solid #eee;"}</style><div style="text-align:center;">';
	header+='<div style="text-align:center;font-size:1.2rem;font-weight:bold;">PRINTZY</div>';
	header+='<div style="text-align:center;font-size:1.2rem;font-weight:bold;">VADAPALANI, CHENNAI - 600026, TAMILNADU</div>';
	header+='<div style="text-align:center;font-size:1.2rem;font-weight:bold;">SALES QUOTE REPORTS</div>';
	header+='<div style="text-align:center;font-size:1rem;font-weight:bold;">For Period : '+fromDt+' - '+toDt+'</div>';
	header+='</div><hr style="border: none;border-bottom: 5px solid black;">';
	var body='<div style="font-size:0.6vw;font-weight:normal;">'+$("#printable_div").html()+'</div>';
	var footer='';
	footer+='<br><br><table id="summary_table" style="width:50%; border-collapse: collapse;border: 1px solid grey;"><tr style="border: 1px solid black;">';
	footer+='<td style="text-align: center;"><h5>JOB CARD ADV. AMT</h5></td>';
	footer+='<td style="text-align: center;"><h5>SALES</h5></td>';
	footer+='<td style="text-align: center;"><h5>TOTAL COLLECTION</h5></td>';
	footer+='</tr><tr style="border: 1px solid black;"><td>';
	footer+=$("#card_1").html();
	footer+='</td><td>';
	footer+=$("#card_2").html();
	footer+='</td><td>';
	footer+=$("#card_3").html();
	footer+='</td></tr></table>';
	var data='<div id="printContentDiv">'+header+body+footer+'</div>';
	$('#printContentDiv').html(header+body+footer);
	printDiv(data);
	$('#printContentDiv').html("");
}

function applyTypeBadges() {
	var colors = {
		'credit': 'type-credit', 'walkin': 'type-walkin', 'walk-in': 'type-walkin',
		'general': 'type-general', 'cash': 'type-cash', 'online': 'type-online'
	};
	$('#resultDetailTable tbody tr').each(function() {
		// Type column (index 5)
		var $typeTd = $(this).find('td').eq(5);
		if ($typeTd.length) {
			var txt = $.trim($typeTd.text());
			var cls = colors[txt.toLowerCase()] || 'type-default';
			$typeTd.html('<span class="type-badge '+cls+'">'+txt+'</span>');
		}
		// Pay Mode column (last)
		var $payTd = $(this).find('td:last');
		if ($payTd.length) {
			var raw = $.trim($payTd.text());
			var parts = raw.split(/[,\s]+/);
			var html = '';
			$.each(parts, function(i, p) {
				p = $.trim(p);
				if (p) {
					var cls = colors[p.toLowerCase()] || 'type-default';
					html += '<span class="type-badge '+cls+'">'+p+'</span> ';
				}
			});
			if (html) $payTd.html(html);
		}
	});
}

function fnSearch() {
	var from = $('#fromDtTxt').val();
	var to = $('#toDtTxt').val();
	var customer_code_txt = $('#customer_code_txt').val();
	var user_name_txt = $('#user_name_txt').val();
	var pay_mode_txt = $('#pay_mode_txt').val();
	var product_type_txt = $('#product_type_txt').val();

	if (!from || !to) { toastr.error("Please select both dates."); return; }
	if (new Date(from) > new Date(to)) { toastr.error("From Date cannot be after To Date."); return; }

	$('#reportResultDiv').html('<div style="text-align:center;padding:60px;color:#94a3b8;"><div class="spinner-border text-primary" role="status"></div><br><br>Loading report data...</div>');
	$('#sidebarPanels').html('');

	$.ajax({
		type: "POST",
		url: "api/get_sale_quotation_report.php",
		data:{
			"from":from, "to":to,
			"customer_code_txt":customer_code_txt,
			"user_name_txt":user_name_txt,
			"pay_mode_txt":pay_mode_txt,
			"product_type_txt":product_type_txt
		},
		success: function (response) {
			if(response != "") {
				var $temp = $('<div>').html(response);
				var $sb = $temp.find('.col-2');
				if ($sb.length) {
					$('#sidebarPanels').html($sb.html());
					$('#sqrSummaryBtn').show();
				}
				$sb.remove();
				$('#reportResultDiv').html($temp.html());
				applyTypeBadges();
				$('#resultDetailTable').DataTable({
					"lengthMenu": [[-1, 10, 25, 50], ["All", 10, 25, 50]],
					language: { emptyTable: 'No data available in table' },
					dom: 'lBfrtip',
					buttons: [{ extend:'excelHtml5', text:'Export to Excel', title:'Sales Quote report', className:'btn buttons-excel' }]
				});
			} else {
				toastr.error("Failed to get details!!!");
			}
		}
	});
}
</script>