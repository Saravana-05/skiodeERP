<?php
session_start();
include_once "connect_db.php";
include_once "page_guard.php";
?>
<style>
/* ── Layout: main left, sidebar right ── */
.lr-page {
	display: flex; min-height: 100vh; background: #f1f5f9;
}
.lr-main {
	flex: 1; min-width: 0; padding: 20px 22px;
	overflow-y: auto; overflow-x: auto;
}
.lr-sidebar {
	width: 260px; min-width: 260px;
	background: #fff; border-left: 1px solid #e2e8f0;
	overflow-y: auto; overflow-x: hidden;
	height: 100vh; position: sticky; top: 0;
}

/* ── Main header ── */
.lr-main-hdr {
	display: flex; align-items: center; gap: 12px; margin-bottom: 18px;
}
.lr-main-hdr-icon {
	width: 40px; height: 40px; background: #eef2ff; border-radius: 10px;
	display: flex; align-items: center; justify-content: center;
}
.lr-main-hdr-icon .material-icons-round { font-size: 22px; color: #4f46e5; }
.lr-main-hdr h2 { font-size: 1.4rem; font-weight: 800; color: #1e293b; margin: 0; }

/* ── Filter bar (top) ── */
.lr-filter-bar {
	background: #fff; border-radius: 10px; padding: 10px 14px; margin-bottom: 18px;
	box-shadow: 0 1px 4px rgba(0,0,0,.06); border: 1px solid #e2e8f0;
	display: flex; align-items: flex-end; gap: 8px; flex-wrap: nowrap;
}
.lr-fg { display: flex; flex-direction: column; gap: 2px; flex: 1; min-width: 0; }
.lr-fg label {
	font-size: 0.7rem; font-weight: 700; color: #334155;
	text-transform: uppercase; letter-spacing: 0.3px; white-space: nowrap;
}
.lr-fg input, .lr-fg select {
	padding: 6px 6px; border: 1.5px solid #e2e8f0; border-radius: 6px;
	font-size: 0.82rem; font-weight: 500; color: #1e293b; background: #fff; outline: none;
	width: 100%; box-sizing: border-box;
}
.lr-fg input:focus, .lr-fg select:focus { border-color: #3b82f6; }
.lr-btn-search {
	padding: 7px 12px; border: none; border-radius: 6px; font-size: 0.82rem;
	font-weight: 700; cursor: pointer; background: #3b82f6; color: #fff;
	display: flex; align-items: center; gap: 4px; white-space: nowrap;
}
.lr-btn-search:hover { background: #2563eb; }
.lr-btn-search .material-icons-round { font-size: 16px; }

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
.buttons-pdf {
	background-color: #ef4444 !important; color: #fff !important;
	border: none !important; border-radius: 6px; font-weight: 600;
	padding: 6px 14px !important; font-size: 0.78rem !important;
}
.buttons-pdf:hover { background-color: #dc2626 !important; }
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

/* ── Table horizontal scroll ── */
#reportResultDiv { overflow-x: auto; }
#reportResultDiv > .row > .col-10 { width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important; }
#reportResultDiv > .row > .col-2 { display: none !important; }
#reportResultDiv > .row { flex-wrap: wrap; }

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
@media print { .lr-sidebar { display: none; } .lr-filter-bar { display: none; } .lr-main { padding: 0; } }
</style>

<div class="lr-page">
	<div class="lr-main">
		<div class="lr-main-hdr">
			<div class="lr-main-hdr-icon"><span class="material-icons-round">menu_book</span></div>
			<h2>Customers Ledger Report</h2>
		</div>

		<div class="lr-filter-bar">
			<div class="lr-fg">
				<label>From Date</label>
				<input type="text" autocomplete="off" class="datepicker" id="fromDtTxt" value="<?php echo date("d-m-Y");?>" />
			</div>
			<div class="lr-fg">
				<label>To Date</label>
				<input type="text" autocomplete="off" class="datepicker" id="toDtTxt" value="<?php echo date("d-m-Y");?>" />
			</div>
			<div class="lr-fg">
				<label>Type</label>
				<select id="actype_txt" onchange="reload_acheads()">
					<option value="ALL">All</option>
					<option value="CUSTOMER">Customer</option>
					<option value="VENDOR">Vendor</option>
				</select>
			</div>
			<div class="lr-fg" style="flex:2;">
				<label>Account Head</label>
				<select id="achead"></select>
			</div>
			<button type="button" class="lr-btn-search" onclick="fnSearch()">
				<span class="material-icons-round">search</span> Search
			</button>
		</div>

		<div id="reportResultDiv"></div>
	</div>

	<div class="lr-sidebar">
		<div id="sidebarPanels"></div>
	</div>
</div>
<div id="printContentDiv"></div>

<script>
$(document).ready(function() {
	SetUpBasics();
	$('.datepicker').removeClass('hasDatepicker').datepicker({
		<?php if($_SESSION["user_type"]=="OPERATOR") { ?>
		minDate: -2,
		<?php } ?>
		dateFormat: 'dd-mm-yy'
	});
	reload_acheads();
});

function reload_acheads() {
	var actype_txt = $("#actype_txt").val();
	$.ajax({
		type: "POST",
		url: "api/get_acheads.php?actype_txt="+actype_txt,
		success: function (response) {
			if(response != "") {
				$("#achead").empty();
				var ans = response.split("^");
				for(var i=0; i<ans.length; i++) {
					var st = ans[i];
					if(st.length>0) {
						var stary = st.split("~");
						$("#achead").append('<option value="'+stary[0]+'">'+stary[1]+'</option>');
					}
				}
			} else {
				toastr.error("Failed to get details!!!");
			}
		}
	});
}

function printLedgerDiv(divId) {
	var fromDt=$('#fromDtTxt').val();
	var toDt=$('#toDtTxt').val();
	var header='';
	header+='<style>*{font-size:12px;}th {border-bottom:1px solid #000;"}td {border-bottom:1px solid #eee;"}</style><div style="text-align:center;">';
	header+='<div style="text-align:center;font-size:1.2rem;font-weight:bold;">CITIZEN PRINTS</div>';
	header+='<div style="text-align:center;font-size:1.2rem;font-weight:bold;">VADAPALANI, CHENNAI - 600026, TAMILNADU</div>';
	header+='<div style="text-align:center;font-size:1.2rem;font-weight:bold;">LEDGER REPORTS</div>';
	header+='<div style="text-align:center;font-size:1rem;font-weight:bold;">For Period : '+fromDt+' - '+toDt+'</div>';
	header+='</div><hr style="border: none;border-bottom: 5px solid black;">';
	var body='<div style="font-size:0.6vw;font-weight:normal;">'+$("#"+divId).html()+'</div>';
	var footer='';
	footer+='<br><br><table id="summary_table" style="width:50%; border-collapse: collapse;border: 1px solid grey;"><tr style="border: 1px solid black;">';
	footer+='<td style="text-align: center;"><h5>SUMMARY</h5></td>';
	footer+='</tr><tr style="border: 1px solid black;"><td>';
	footer+=$("#card_1").html();
	footer+='</td>';
	footer+='</tr></table>';
	var data='<div id="printContentDiv">'+header+body+footer+'</div>';
	$('#printContentDiv').html(header+body+footer);
	printDiv(data);
	$('#printContentDiv').html("");
}

function fnSearch() {
	var from = $('#fromDtTxt').val();
	var to = $('#toDtTxt').val();
	var achead = $('#achead :selected').val();

	if (!from || !to) { toastr.error("Please select both dates."); return; }
	if (new Date(from) > new Date(to)) { toastr.error("From Date cannot be after To Date."); return; }
	if (achead == "Select") { toastr.error("Select Account Head."); return; }

	$('#reportResultDiv').html('<div style="text-align:center;padding:60px;color:#94a3b8;"><div class="spinner-border text-primary" role="status"></div><br><br>Loading report data...</div>');
	$('#sidebarPanels').html('');

	$.ajax({
		type: "POST",
		url: "api/get_ledger_report.php?from="+from+"&to="+to+"&achead="+achead,
		success: function (response) {
			if(response != "") {
				$('#reportResultDiv').html(response);
				var $sb = $('#reportResultDiv > .row > .col-2');
				if ($sb.length) $('#sidebarPanels').html($sb.html());
			} else {
				toastr.error("Failed to get details!!!");
			}
		}
	});
}
</script>