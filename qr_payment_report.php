<?php
session_start();
include_once "connect_db.php";
include_once "page_guard.php";
?>
<style>
.qrr-content { margin-top:20px !important; border:0px solid #ccc; border-radius:4px; min-height:550px; }
.qrr-content>.headerDiv { background-color:#3a3a3a; border-radius:5px; color:#fff; }
#qrrResultDiv { border:1px solid #cfcfcf; background:#fff; min-height:200px; font-size:0.8rem; }
#qrrTable td, #qrrTable th { padding:3px; font-size:0.8rem; }
.badge-success  { background:#28a745; color:#fff; padding:2px 8px; border-radius:4px; font-size:0.75rem; }
.badge-jc       { background:#0d6efd; color:#fff; padding:2px 7px; border-radius:4px; font-size:0.75rem; }
.badge-sq       { background:#198754; color:#fff; padding:2px 7px; border-radius:4px; font-size:0.75rem; }
.badge-adv      { background:#6f42c1; color:#fff; padding:2px 7px; border-radius:4px; font-size:0.75rem; }
div.dt-container div.dt-length select { width:35%; }
div.dt-container div.dt-length label  { width:55%; }
.dt-buttons { display:flex; justify-content:flex-end; margin-bottom:10px; gap:10px; }
.buttons-excel { background-color:#28a745 !important; color:white !important; border:none !important; border-radius:6px; }
.buttons-pdf   { background-color:#dc3545 !important; color:white !important; border:none !important; border-radius:6px; }
.buttons-excel:hover { background-color:#218838 !important; }
.buttons-pdf:hover   { background-color:#c82333 !important; }
.dt-button { font-weight:600; font-size:14px; padding:8px 14px !important; height:30px; line-height:1.1em; }
</style>

<!-- ── QR Zoom Modal ──────────────────────────────────────────────────── -->
<div class="modal fade" id="qrZoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:320px;">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header py-2" style="background:#1a1a2e;color:#fff;border-radius:12px 12px 0 0;">
                <h6 class="modal-title mb-0">📱 Scan QR to Pay</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div id="qrZoomContainer" style="display:inline-block;"></div>
                <p id="qrZoomStatus" class="mt-2 mb-0" style="font-size:0.8rem;"></p>
            </div>
        </div>
    </div>
</div>

<div class="bg_aliceblue p-3 m-1 pt-0 qrr-content">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center p-2 mb-3 headerDiv">
        <h4 class="mb-0">📱 QR Payment Report</h4>
    </div>

    <form>
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <label class="col-form-label">From :</label>
            </div>
            <div class="col-auto">
                <input type="text" autocomplete="off" class="datepicker form-control form-control-sm"
                       id="qrrFromDt" placeholder="dd-MM-yyyy"
                       value="<?php echo date('d-m-Y'); ?>" style="width:110px;"/>
            </div>
            <div class="col-auto">
                <label class="col-form-label">To :</label>
            </div>
            <div class="col-auto">
                <input type="text" autocomplete="off" class="datepicker form-control form-control-sm"
                       id="qrrToDt" placeholder="dd-MM-yyyy"
                       value="<?php echo date('d-m-Y'); ?>" style="width:110px;"/>
            </div>
            <div class="col-auto">
                <label class="col-form-label">Type</label>
            </div>
            <div class="col-auto">
                <select id="qrrType" class="form-select form-select-sm">
                    <option value="all">All</option>
                    <option value="jobcard">JC-ADV (Job Card Advance)</option>
                    <option value="sq">Sales Quote</option>
                    <option value="advance">Advance Payment</option>
                </select>
            </div>
            <div class="col-auto">
                <label class="col-form-label">User</label>
            </div>
            <div class="col-auto">
                <select id="qrrUser" class="form-select form-select-sm">
                    <option value="all">All</option>
                    <?php
                    $u_sql = "SELECT DISTINCT created_by FROM jobcard_master
                              WHERE created_by IS NOT NULL AND created_by != ''
                              ORDER BY created_by ASC";
                    if ($u_qry = mysqli_query($connection, $u_sql)) {
                        while ($u_row = mysqli_fetch_assoc($u_qry)) {
                            $u = htmlspecialchars($u_row['created_by']);
                            echo "<option value=\"$u\">$u</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-sm btn-darkpurple" onclick="fnQrrSearch()">Search</button>
            </div>
        </div>
    </form>

    <div id="qrrResultDiv" class="p-2 mt-3"></div>
</div>

<script>
$(document).ready(function() {
    SetUpBasics();
    $('.datepicker').removeClass('hasDatepicker').datepicker({ dateFormat: 'dd-mm-yy' });
    // Auto-load report on page open with default filters (All type, All status, today's date)
    fnQrrSearch();
});

function fnQrrSearch() {
    var from   = $('#qrrFromDt').val();
    var to     = $('#qrrToDt').val();
    var type   = $('#qrrType').val();
    var user   = $('#qrrUser').val();

    if (!from || !to) { toastr.error("Please select both dates."); return; }

    $('#qrrResultDiv').html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin"></i> Loading QR payment report…</div>');

    if ($.fn.DataTable.isDataTable('#qrrTable')) $('#qrrTable').DataTable().destroy();

    fnQrrLoad(from, to, type, user);
}

function fnQrrLoad(from, to, type, user) {
    $.ajax({
        type: "POST",
        url:  "api/get_qr_payment_report.php",
        dataType: "json",
        data: { from: from, to: to, type: type, user: user },
        success: function(res) {
            if (!res || !res.table_html) { toastr.error("Failed to load report."); return; }

            var dtBtns = [
                {
                    extend: 'excelHtml5',
                    text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">table_view</span> Excel',
                    className: 'btn buttons-excel',
                    title: 'QR Payment Report',
                    exportOptions: {
                        columns: ':not(:last-child)',  // exclude action/hidden cols if any
                        format: {
                            body: function(data, row, col, node) {
                                // strip HTML badges to plain text for Excel
                                return $('<div>').html(data).text();
                            }
                        }
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">picture_as_pdf</span> PDF',
                    className: 'btn buttons-pdf',
                    title: 'QR Payment Report',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        format: {
                            body: function(data, row, col, node) {
                                return $('<div>').html(data).text();
                            }
                        }
                    }
                },
                {
                    extend: 'print',
                    text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">print</span> Print',
                    className: 'btn btn-secondary',
                    title: 'QR Payment Report',
                    customize: function(win) {
                        $(win.document.body).css('font-size','11px');
                        $(win.document.body).find('table').css('border-collapse','collapse');
                    }
                }
            ];

            var html  = '<div class="row">';
            html     += '<div class="col-10" id="qrrPrintDiv">';
            html     += res.table_html;
            html     += '</div>';

            // Summary panel
            html += '<div class="col-2 text-bg-light" style="border-left:1px inset #ccc;" id="qrrSummaryDiv">';
            html += res.summary_html;
            html += '<hr style="margin:8px 0;">';
            html += '<center>';
            html += '<button type="button" class="btn btn-primary btn-sm" onclick="fnQrrPrint()">PRINT</button>';
            html += '<br><br>';
            html += '<button type="button" class="btn btn-secondary btn-sm" onclick="fnSideMenu(1)">Close</button>';
            html += '</center>';
            html += '</div>';
            html += '</div>';

            $('#qrrResultDiv').html(html);

            $('#qrrTable').DataTable({
                lengthMenu: [[-1, 25, 50, 100],["All", 25, 50, 100]],
                pageLength: -1,
                language: { emptyTable: 'No transactions found' },
                dom: 'lBfrtip',
                order: [[1, 'desc']],
                buttons: dtBtns,
                drawCallback: function() {
                    fnRenderQRThumbs();
                }
            });

            // Initial render for first page
            fnRenderQRThumbs();
        },
        error: function() { toastr.error("Network error. Please try again."); }
    });
}

// ── Render all .qr-thumb divs on the current DataTable page ──────────────
function fnRenderQRThumbs() {
    if (typeof QRCode === 'undefined') return;
    $('.qr-thumb').each(function() {
        // Only render once — skip if already has a canvas or img child
        if ($(this).find('canvas,img').length > 0) return;
        var qrStr = $(this).attr('data-qr');
        if (!qrStr) return;
        new QRCode(this, {
            text: qrStr,
            width:  60,
            height: 60,
            correctLevel: QRCode.CorrectLevel.M
        });
    });
}

// ── Zoom modal — click on any QR thumbnail ────────────────────────────────
$(document).on('click', '.qr-thumb', function() {
    var qrStr  = $(this).attr('data-qr');
    var isPending = $(this).css('opacity') === '1' ||
                    $(this).attr('title').indexOf('Scan to Pay') !== -1;
    if (!qrStr) return;

    // Clear and re-render at large size
    var $container = $('#qrZoomContainer');
    $container.empty();
    new QRCode($container[0], {
        text: qrStr,
        width:  260,
        height: 260,
        correctLevel: QRCode.CorrectLevel.M
    });

    var statusText = isPending
        ? '<span style="color:#28a745;font-weight:bold;">⏳ Payment Pending — Scan this QR to pay now</span>'
        : '<span style="color:#888;">This QR is already processed</span>';
    $('#qrZoomStatus').html(statusText);

    var modal = new bootstrap.Modal(document.getElementById('qrZoomModal'));
    modal.show();
});


function fnQrrPrint() {
    var from = $('#qrrFromDt').val();
    var to   = $('#qrrToDt').val();
    var header  = '<style>*{font-size:11px;}th{border-bottom:1px solid #000;}td{border-bottom:1px solid #eee;}</style>';
    header += '<div style="text-align:center;font-size:1.2rem;font-weight:bold;">CITIZEN PRINTS</div>';
    header += '<div style="text-align:center;font-size:1rem;font-weight:bold;">VADAPALANI, CHENNAI - 600026</div>';
    header += '<div style="text-align:center;font-size:1.1rem;font-weight:bold;">QR PAYMENT REPORT</div>';
    header += '<div style="text-align:center;font-size:0.9rem;">Period : ' + from + ' to ' + to + '</div>';
    header += '<hr style="border:none;border-bottom:4px solid black;">';
    var body   = '<div style="font-size:0.6vw;">' + $('#qrrPrintDiv').html() + '</div>';
    var footer = '<br><table style="width:50%;border-collapse:collapse;border:1px solid grey;">';
    footer    += '<tr><td><h5>SUMMARY</h5></td></tr>';
    footer    += '<tr><td>' + $('#qrrSummaryDiv').html() + '</td></tr></table>';
    printDiv('<div>' + header + body + footer + '</div>');
}
</script>
