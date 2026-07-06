<?php
session_start();
include_once "connect_db.php";
include_once "page_guard.php";
?>
<style>
/* ── Page ── */
.tb-page { background: #f1f5f9; min-height: 100vh; padding: 20px 22px; }

.tb-main-hdr {
	display: flex; align-items: center; gap: 12px;
}
.tb-main-hdr-icon {
	width: 40px; height: 40px; background: #eef2ff; border-radius: 10px;
	display: flex; align-items: center; justify-content: center;
}
.tb-main-hdr-icon .material-icons-round { font-size: 22px; color: #4f46e5; }
.tb-main-hdr h2 { font-size: 1.4rem; font-weight: 800; color: #1e293b; margin: 0; }

/* ── Outstanding card ── */
.tb-outstanding-card {
	display: none; background: transparent; border-radius: 12px; padding: 6px 0;
	align-items: center; gap: 12px;
}
.tb-outstanding-icon {
	width: 48px; height: 48px; border-radius: 12px;
	display: flex; align-items: center; justify-content: center;
}
.tb-outstanding-icon.cr { background: #dcfce7; }
.tb-outstanding-icon.dr { background: #fee2e2; }
.tb-outstanding-icon .material-icons-round { font-size: 26px; }
.tb-outstanding-icon.cr .material-icons-round { color: #16a34a; }
.tb-outstanding-icon.dr .material-icons-round { color: #dc2626; }
.tb-outstanding-info { display: flex; flex-direction: column; }
.tb-outstanding-label {
	font-size: 0.72rem; font-weight: 600; color: #64748b;
	text-transform: uppercase; letter-spacing: 0.5px;
}
.tb-outstanding-amount {
	font-size: 1.5rem; font-weight: 800; line-height: 1.2;
}
.tb-outstanding-amount.cr { color: #16a34a; }
.tb-outstanding-amount.dr { color: #dc2626; }
.tb-outstanding-type {
	font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; margin-top: 1px;
}

/* ── Filter bar (top) ── */
.tb-filter-bar {
	background: #fff; border-radius: 10px; padding: 10px 14px; margin-bottom: 18px;
	box-shadow: 0 1px 4px rgba(0,0,0,.06); border: 1px solid #e2e8f0;
	display: flex; align-items: flex-end; gap: 8px; flex-wrap: nowrap;
}
.tb-fg { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.tb-fg label {
	font-size: 0.7rem; font-weight: 700; color: #334155;
	text-transform: uppercase; letter-spacing: 0.3px; white-space: nowrap;
}
.tb-fg select {
	padding: 6px 6px; border: 1.5px solid #e2e8f0; border-radius: 6px;
	font-size: 0.82rem; font-weight: 500; color: #1e293b; background: #fff; outline: none;
	min-width: 130px; box-sizing: border-box;
}
.tb-fg select:focus { border-color: #3b82f6; }
.tb-btn-search {
	padding: 7px 12px; border: none; border-radius: 6px; font-size: 0.82rem;
	font-weight: 700; cursor: pointer; background: #3b82f6; color: #fff;
	display: flex; align-items: center; gap: 4px; white-space: nowrap;
}
.tb-btn-search:hover { background: #2563eb; }
.tb-btn-search .material-icons-round { font-size: 16px; }

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

/* ── Type badges ── */
.type-badge {
	display: inline-block; padding: 3px 10px; border-radius: 12px;
	font-size: 0.75rem; font-weight: 700; letter-spacing: 0.3px; white-space: nowrap;
}
.type-customer { background: #dbeafe; color: #2563eb; }
.type-vendor   { background: #fce7f3; color: #db2777; }
.type-default  { background: #f1f5f9; color: #475569; }

#printContentDiv { display: none; }
@media print { .tb-filter-bar { display: none; } }
</style>

<div class="tb-page">
	<div class="tb-main-hdr" style="margin-bottom:18px;">
		<div class="tb-main-hdr-icon"><span class="material-icons-round">account_balance</span></div>
		<h2>Customer Credit Balance</h2>
	</div>

	<div class="tb-filter-bar">
		<div class="tb-fg">
			<label>Type</label>
			<select id="actype_txt">
				<option value="ALL">All</option>
				<option value="CUSTOMER">Customer</option>
				<option value="VENDOR">Vendor</option>
			</select>
		</div>
		<button type="button" class="tb-btn-search" onclick="fnSearch()">
			<span class="material-icons-round">refresh</span> Refresh
		</button>
		<div style="flex:1;"></div>
		<div class="tb-outstanding-card" id="outstandingCard">
			<div class="tb-outstanding-icon" id="outstandingIcon">
				<span class="material-icons-round">account_balance_wallet</span>
			</div>
			<div class="tb-outstanding-info">
				<span class="tb-outstanding-label">Total Outstanding</span>
				<span class="tb-outstanding-amount" id="outstandingAmount"></span>
				<span class="tb-outstanding-type" id="outstandingType"></span>
			</div>
		</div>
	</div>

	<div style="display:none;">
		<input type="text" class="datepicker" id="fromDtTxt" value="<?php echo date("d-m-Y");?>" />
		<input type="text" class="datepicker" id="toDtTxt" value="<?php echo date("d-m-Y");?>" />
	</div>

	<div id="reportResultDiv"></div>
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
	fnSearch();
});

function printTrialBalanceDiv(divId) {
	var fromDt=$('#fromDtTxt').val();
	var toDt=$('#toDtTxt').val();
	var header='';
	header+='<style>*{font-size:12px;}th {border-bottom:1px solid #000;"}td {border-bottom:1px solid #eee;"}</style><div style="text-align:center;">';
	header+='<div style="text-align:center;font-size:1.2rem;font-weight:bold;">PRINTZY</div>';
	header+='<div style="text-align:center;font-size:1.2rem;font-weight:bold;">VADAPALANI, CHENNAI - 600026, TAMILNADU</div>';
	header+='<div style="text-align:center;font-size:1.2rem;font-weight:bold;">TRIAL BALANCE REPORT</div>';
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

function extractOutstanding() {
	var $h4 = $('#reportResultDiv h4');
	if ($h4.length) {
		var text = $h4.text();
		var match = text.match(/Outstanding\s+([\d,]+\.\d+)\s*(Cr|Dr)/i);
		if (match) {
			var amount = match[1];
			var type = match[2];
			var isCr = type.toLowerCase() === 'cr';
			var cls = isCr ? 'cr' : 'dr';
			$('#outstandingIcon').removeClass('cr dr').addClass(cls);
			$('#outstandingAmount').removeClass('cr dr').addClass(cls).text('₹ ' + amount);
			$('#outstandingType').text(isCr ? 'CREDIT' : 'DEBIT').css('color', isCr ? '#16a34a' : '#dc2626');
			$('#outstandingCard').css('display', 'flex');
		}
		$h4.hide();
	}
}

function applyTypeBadges() {
	var colors = { 'customer': 'type-customer', 'vendor': 'type-vendor' };
	$('#resultDetailTable tbody tr').each(function() {
		$(this).find('td').each(function() {
			var txt = $.trim($(this).text()).toLowerCase();
			if (colors[txt]) {
				$(this).html('<span class="type-badge '+colors[txt]+'">'+$.trim($(this).text())+'</span>');
			}
		});
	});
}

function fnSearch() {
	var from = $('#fromDtTxt').val();
	var to = $('#toDtTxt').val();
	var actype_txt = $('#actype_txt').val();

	$('#reportResultDiv').html('<div style="text-align:center;padding:60px;color:#94a3b8;"><div class="spinner-border text-primary" role="status"></div><br><br>Loading report data...</div>');

	$.ajax({
		type: "POST",
		url: "api/get_trial_balance_report.php?from="+from+"&to="+to+"&actype_txt="+actype_txt,
		success: function (response) {
			if(response != "") {
				$('#reportResultDiv').html(response);
				extractOutstanding();
				applyTypeBadges();
				$('#resultDetailTable').DataTable({
					"lengthMenu": [[-1, 5, 10, 25, 50], ["All", 5, 10, 25, 50]],
					language: { emptyTable: 'No data available in table' },
					dom: 'lBfrtip',
					buttons: [
						{ extend:'excelHtml5', text:'Export to Excel', title:'Trial Balance Report', className:'btn buttons-excel' },
						{ extend:'pdfHtml5', text:'Export to PDF', className:'btn buttons-pdf', orientation:'portrait', pageSize:'A4', title:'Trial Balance Report' }
					]
				});
			} else {
				toastr.error("Failed to get details!!!");
			}
		}
	});
}
</script>