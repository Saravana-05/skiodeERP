<?php
session_start();
include_once "connect_db.php";
include_once "page_guard.php";
?>
<style>
.rptDiv { margin-top:10px; background:#f0f8ff; border-radius:5px; padding:15px; }
.rptDiv .hdr { background:#3a3a3a; color:#fff; border-radius:4px; padding:8px 14px; margin-bottom:12px; }
#discountTable th { background:#3a3a3a; color:#fff; font-size:12px; text-align:center; padding:5px 8px; }
#discountTable td { font-size:12px; padding:4px 8px; vertical-align:middle; }
#discountTable tbody tr:nth-child(even) { background:#f9f9f9; }
#discountTable tbody tr:hover { background:#eef3ff; }
.sum-row  { background:#fff3cd !important; font-weight:bold; }
.dt-buttons { display:flex; justify-content:flex-end; margin-bottom:8px; gap:8px; }
.buttons-excel { background:#28a745!important; color:#fff!important; border:none!important; border-radius:5px; }
.buttons-pdf   { background:#dc3545!important; color:#fff!important; border:none!important; border-radius:5px; }

/* Product-type badge colours */
.badge-PRINTING  { background:#0d6efd; color:#fff; }
.badge-SERVICES  { background:#6f42c1; color:#fff; }
.badge-MATERIALS { background:#fd7e14; color:#fff; }
.badge-IDCARDS   { background:#20c997; color:#fff; }
.badge-PRODUCTS  { background:#e83e8c; color:#fff; }
.badge-OTHER     { background:#6c757d; color:#fff; }
.type-badge { display:inline-block; font-size:10px; font-weight:bold;
              padding:2px 7px; border-radius:10px; letter-spacing:.3px; }

/* Breakdown table */
#breakdownTable th { background:#3a3a3a; color:#fff; font-size:12px; padding:5px 10px; }
#breakdownTable td { font-size:12px; padding:4px 10px; }
#breakdownTable tbody tr:nth-child(even) { background:#f5f5f5; }
.breakdown-total { font-weight:bold; background:#fff3cd !important; }
</style>

<div class="rptDiv">
    <div class="hdr"><h5 class="mb-0">Item Discount Report — Product-wise</h5></div>

    <!-- Filters -->
    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-2">
            <label class="form-label fw-bold mb-1">From Date</label>
            <input type="date" class="form-control form-control-sm" id="f_from_date"
                value="<?php echo date('Y-m-d'); ?>" />
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold mb-1">To Date</label>
            <input type="date" class="form-control form-control-sm" id="f_to_date"
                value="<?php echo date('Y-m-d'); ?>" />
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold mb-1">Product Type</label>
            <select class="form-select form-select-sm" id="f_job_type">
                <option value="">-- All Types --</option>
                <option value="PRINTING">Printing</option>
                <option value="SERVICES">Services</option>
                <option value="MATERIALS">Materials</option>
                <option value="IDCARDS">ID Cards</option>
                <option value="PRODUCTS">Products</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold mb-1">Customer</label>
            <input type="text" class="form-control form-control-sm" id="f_customer"
                placeholder="Name or Mobile" />
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold mb-1">Item</label>
            <input type="text" class="form-control form-control-sm" id="f_item"
                placeholder="Item / product name" />
        </div>
        <div class="col-md-2 d-flex gap-2 align-items-end">
            <button class="btn btn-sm btn-darkpurple" onclick="fnLoadDiscountReport()">
                <i class="fa fa-search"></i> Search
            </button>
            <button class="btn btn-sm btn-secondary" onclick="fnClearFilters()">
                Clear
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-2 mb-3" id="summaryCards" style="display:none;">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-2 text-center">
                    <div style="font-size:11px;color:#888;">Total Item Value</div>
                    <div style="font-size:18px;font-weight:bold;color:#0d6efd;" id="card_total_value">0.00</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-2 text-center">
                    <div style="font-size:11px;color:#888;">Total Discount Given</div>
                    <div style="font-size:18px;font-weight:bold;color:#dc3545;" id="card_total_discount">0.00</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-2 text-center">
                    <div style="font-size:11px;color:#888;">Net Amount</div>
                    <div style="font-size:18px;font-weight:bold;color:#198754;" id="card_net_amount">0.00</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-2 text-center">
                    <div style="font-size:11px;color:#888;">Overall Discount %</div>
                    <div style="font-size:18px;font-weight:bold;color:#fd7e14;" id="card_disc_pct">0%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product-type breakdown summary -->
    <div id="breakdownDiv" style="display:none;margin-bottom:16px;">
        <div style="font-weight:bold;font-size:13px;margin-bottom:6px;color:#3a3a3a;">
            Discount Breakdown by Product Type
        </div>
        <table class="table table-bordered table-sm" id="breakdownTable" style="max-width:600px;">
            <thead>
                <tr>
                    <th>Product Type</th>
                    <th style="text-align:right;">Value (₹)</th>
                    <th style="text-align:right;">Discount (₹)</th>
                    <th style="text-align:right;">Net (₹)</th>
                    <th style="text-align:right;">Disc %</th>
                </tr>
            </thead>
            <tbody id="breakdownTbody"></tbody>
        </table>
    </div>

    <!-- Result Table -->
    <div id="discountReportDiv">
        <p class="text-center text-muted mt-4">Select filters and click Search</p>
    </div>
</div>

<script>
// Label map for job_type values
var TYPE_LABELS = {
    PRINTING:  'Printing',
    SERVICES:  'Services',
    MATERIALS: 'Materials',
    IDCARDS:   'ID Cards',
    PRODUCTS:  'Products',
    OTHER:     'Other'
};

function fnLoadDiscountReport() {
    var fromDate = $("#f_from_date").val();
    var toDate   = $("#f_to_date").val();
    var customer = $("#f_customer").val().trim();
    var item     = $("#f_item").val().trim();
    var jobType  = $("#f_job_type").val();

    $.ajax({
        url: 'api/get_item_discount_report.php',
        method: 'POST',
        data: {
            from_date: fromDate,
            to_date:   toDate,
            customer:  customer,
            item_name: item,
            job_type:  jobType
        },
        success: function (res) {
            var data;
            try { data = (typeof res === 'string') ? JSON.parse(res) : res; } catch(e) { data = null; }

            if (!data || data.error) {
                toastr.error("Report error: " + (data && data.error ? data.error : "Unknown"));
                return;
            }
            if (data.length === 0) {
                toastr.warning("No discount records found for the selected filters.");
                $("#discountReportDiv").html(
                    '<p class="text-center text-muted mt-3">No discount records found.</p>'
                );
                $("#summaryCards, #breakdownDiv").hide();
                return;
            }
            renderDiscountReport(data);
        },
        error: function () { toastr.error("Failed to load report."); }
    });
}

function renderDiscountReport(data) {
    var totalVal  = 0, totalDisc = 0;
    var byType    = {};  // { job_type: { val, disc } }

    var html = '<table class="table table-bordered table-sm" id="discountTable">';
    html += '<thead><tr>'
        + '<th>#</th>'
        + '<th>JC No</th>'
        + '<th>Date</th>'
        + '<th>Customer</th>'
        + '<th>Mobile</th>'
        + '<th>Type</th>'
        + '<th>Item Description</th>'
        + '<th>Machine</th>'
        + '<th>Qty</th>'
        + '<th style="text-align:right;">Value (₹)</th>'
        + '<th style="text-align:right;">Discount (₹)</th>'
        + '<th style="text-align:right;">Net (₹)</th>'
        + '<th style="text-align:right;">Disc %</th>'
        + '</tr></thead><tbody>';

    for (var i = 0; i < data.length; i++) {
        var r       = data[i];
        var val     = parseFloat(r.item_value)    || 0;
        var disc    = parseFloat(r.item_discount) || 0;
        var net     = val - disc;
        var pct     = val > 0 ? ((disc / val) * 100).toFixed(1) : '0.0';
        var jt      = (r.job_type || 'OTHER').toUpperCase();
        var label   = TYPE_LABELS[jt] || jt;

        totalVal  += val;
        totalDisc += disc;

        if (!byType[jt]) byType[jt] = { val: 0, disc: 0 };
        byType[jt].val  += val;
        byType[jt].disc += disc;

        var discColor = disc > 0 ? 'color:#c0392b;font-weight:bold;' : '';

        html += '<tr>'
            + '<td>' + (i + 1) + '</td>'
            + '<td><b>' + escHtml(r.jobcard_no) + '</b></td>'
            + '<td>' + escHtml(r.jc_date) + '</td>'
            + '<td>' + escHtml(r.customer_name) + '</td>'
            + '<td>' + escHtml(r.customer_mobile) + '</td>'
            + '<td><span class="type-badge badge-' + jt + '">' + label + '</span></td>'
            + '<td>' + escHtml(r.product_name) + '</td>'
            + '<td>' + escHtml(r.machine_code) + '</td>'
            + '<td style="text-align:right;">' + escHtml(r.total_qty) + '</td>'
            + '<td style="text-align:right;">' + val.toFixed(2) + '</td>'
            + '<td style="text-align:right;' + discColor + '">' + disc.toFixed(2) + '</td>'
            + '<td style="text-align:right;font-weight:bold;">' + net.toFixed(2) + '</td>'
            + '<td style="text-align:right;' + discColor + '">' + pct + '%</td>'
            + '</tr>';
    }

    // Summary row
    var netTotal = totalVal - totalDisc;
    var totalPct = totalVal > 0 ? ((totalDisc / totalVal) * 100).toFixed(1) : '0.0';
    html += '<tr class="sum-row">'
        + '<td colspan="9" style="text-align:right;">TOTAL</td>'
        + '<td style="text-align:right;">₹' + totalVal.toFixed(2)  + '</td>'
        + '<td style="text-align:right;color:#dc3545;">₹' + totalDisc.toFixed(2) + '</td>'
        + '<td style="text-align:right;">₹' + netTotal.toFixed(2)  + '</td>'
        + '<td style="text-align:right;">' + totalPct + '%</td>'
        + '</tr>';

    html += '</tbody></table>';
    $("#discountReportDiv").html(html);

    if ($.fn.DataTable.isDataTable('#discountTable'))
        $('#discountTable').DataTable().destroy();

    $('#discountTable').DataTable({
        lengthMenu: [[-1, 25, 50, 100], ["All", 25, 50, 100]],
        pageLength: -1,
        order: [[1, 'desc']],
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">table_view</span> Excel',
                title: 'Item Discount Report',
                className: 'btn buttons-excel'
            },
            {
                extend: 'pdfHtml5',
                text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">picture_as_pdf</span> PDF',
                className: 'btn buttons-pdf',
                orientation: 'landscape',
                pageSize: 'A4',
                title: 'Item Discount Report'
            },
            {
                extend: 'print',
                text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">print</span> Print',
                className: 'btn btn-secondary'
            }
        ]
    });

    // ── Summary cards ─────────────────────────────────────────────────────────
    $("#card_total_value").text('₹' + totalVal.toFixed(2));
    $("#card_total_discount").text('₹' + totalDisc.toFixed(2));
    $("#card_net_amount").text('₹' + netTotal.toFixed(2));
    $("#card_disc_pct").text(totalPct + '%');
    $("#summaryCards").show();

    // ── Product-type breakdown table ──────────────────────────────────────────
    var typeOrder = ['PRINTING','SERVICES','MATERIALS','IDCARDS','PRODUCTS','OTHER'];
    var bHtml = '';
    var bTotalVal = 0, bTotalDisc = 0;

    typeOrder.forEach(function(jt) {
        if (!byType[jt]) return;
        var tv   = byType[jt].val;
        var td   = byType[jt].disc;
        var tnet = tv - td;
        var tpct = tv > 0 ? ((td / tv) * 100).toFixed(1) : '0.0';
        var lbl  = TYPE_LABELS[jt] || jt;
        bTotalVal  += tv;
        bTotalDisc += td;
        bHtml += '<tr>'
            + '<td><span class="type-badge badge-' + jt + '">' + lbl + '</span></td>'
            + '<td style="text-align:right;">₹' + tv.toFixed(2)   + '</td>'
            + '<td style="text-align:right;color:#dc3545;font-weight:bold;">₹' + td.toFixed(2) + '</td>'
            + '<td style="text-align:right;">₹' + tnet.toFixed(2)  + '</td>'
            + '<td style="text-align:right;">' + tpct + '%</td>'
            + '</tr>';
    });

    // Grand total row
    var bNetTotal = bTotalVal - bTotalDisc;
    var bPct      = bTotalVal > 0 ? ((bTotalDisc / bTotalVal) * 100).toFixed(1) : '0.0';
    bHtml += '<tr class="breakdown-total">'
        + '<td>TOTAL</td>'
        + '<td style="text-align:right;">₹' + bTotalVal.toFixed(2)  + '</td>'
        + '<td style="text-align:right;color:#dc3545;">₹' + bTotalDisc.toFixed(2) + '</td>'
        + '<td style="text-align:right;">₹' + bNetTotal.toFixed(2)  + '</td>'
        + '<td style="text-align:right;">' + bPct + '%</td>'
        + '</tr>';

    $("#breakdownTbody").html(bHtml);
    $("#breakdownDiv").show();
}

function fnClearFilters() {
    $("#f_from_date").val('<?php echo date('Y-m-d'); ?>');
    $("#f_to_date").val('<?php echo date('Y-m-d'); ?>');
    $("#f_job_type").val('');
    $("#f_customer").val('');
    $("#f_item").val('');
    $("#discountReportDiv").html('<p class="text-center text-muted mt-4">Select filters and click Search</p>');
    $("#summaryCards, #breakdownDiv").hide();
}

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
</script>
