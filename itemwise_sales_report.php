<?php
session_start();
include_once "connect_db.php";
include_once "page_guard.php";
?>
<style>
.reportContent { margin-top:20px !important; border:0px solid #ccc; border-radius:4px; min-height:550px; }
div.dt-container div.dt-length select { width:35%; }
div.dt-container div.dt-length label  { width:55%; }
.dt-buttons { display:flex; justify-content:flex-end; margin-bottom:10px; gap:10px; }
.buttons-excel { background-color:#28a745 !important; color:white !important; border:none !important; border-radius:6px; margin-left:10px !important; }
.buttons-pdf   { background-color:#dc3545 !important; color:white !important; border:none !important; border-radius:6px; }
.buttons-excel:hover { background-color:#218838 !important; }
.buttons-pdf:hover   { background-color:#c82333 !important; }
.dt-button { font-weight:600; font-size:14px; padding:8px 14px !important; height:30px; line-height:1.1em; }
#iwDetailTable td, #iwDetailTable th { padding:3px; }
.reportContent button { margin-top:-5px; }
.reportContent .col-form-label { margin-left:10px; }
#reportResultDiv { border:1px solid #cfcfcf; background:#fff; min-height:200px; font-size:0.8rem; }
.reportContent>.headerDiv { background-color:#3a3a3a; border-radius:5px; color:#fff; }
</style>

<div class="bg_aliceblue p-3 m-1 pt-0 reportContent">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center p-2 mb-3 headerDiv">
        <h4 class="mb-0">Itemwise Sales Report</h4>
    </div>
    <form>
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <label class="col-form-label">From :</label>
            </div>
            <div class="col-auto">
                <input type="text" autocomplete="off" class="datepicker form-control form-control-sm"
                       id="fromDtTxt" name="fromDtTxt" placeholder="dd-MM-yyyy"
                       value="<?php echo date('d-m-Y'); ?>" style="width:110px;"/>
            </div>
            <div class="col-auto">
                <label class="col-form-label">To :</label>
            </div>
            <div class="col-auto">
                <input type="text" autocomplete="off" class="datepicker form-control form-control-sm"
                       id="toDtTxt" name="toDtTxt" placeholder="dd-MM-yyyy"
                       value="<?php echo date('d-m-Y'); ?>" style="width:110px;"/>
            </div>
            <div class="col-auto">
                <label class="col-form-label">User</label>
            </div>
            <div class="col-auto">
                <select id="user_name_txt" name="user_name_txt" class="form-select form-select-sm">
                <?php
                    if ($_SESSION["user_type"] !== "OPERATOR") {
                        echo '<option value="all">All</option>';
                        $sql = "SELECT * FROM user_master";
                        if ($qry = mysqli_query($connection, $sql))
                            while ($row = mysqli_fetch_array($qry))
                                echo '<option value="'.$row["user_name"].'">'.$row["user_display_name"].'</option>';
                    } else {
                        echo '<option value="'.$_SESSION['user_name'].'">'.$_SESSION['user_display_name'].'</option>';
                    }
                ?>
                </select>
            </div>
            <div class="col-auto">
                <label class="col-form-label">Type</label>
            </div>
            <div class="col-auto">
                <select id="job_type_txt" name="job_type_txt" class="form-select form-select-sm" onchange="fnLoadCat2()">
                    <option value="all">All</option>
                    <option value="PRINTING">PRINTING</option>
                    <option value="SERVICES">SERVICES</option>
                    <option value="MATERIALS">MATERIALS</option>
                    <option value="IDCARDS">IDCARDS</option>
                    <option value="PRODUCTS">PRODUCTS</option>
                </select>
            </div>
            <div class="col-auto">
                <label class="col-form-label">Category</label>
            </div>
            <div class="col-auto">
                <select id="cat2_txt" name="cat2_txt" class="form-select form-select-sm">
                    <option value="all">All</option>
                    <?php
                        $cat2_sql = "SELECT DISTINCT category2 FROM product_master WHERE category2 IS NOT NULL AND category2 != '' ORDER BY category2";
                        if ($cat2_qry = mysqli_query($connection, $cat2_sql))
                            while ($cat2_row = mysqli_fetch_array($cat2_qry))
                                echo '<option value="'.htmlspecialchars($cat2_row['category2']).'">'.htmlspecialchars($cat2_row['category2']).'</option>';
                    ?>
                </select>
            </div>
            <div class="col-auto">
                <input type="checkbox" id="show_summary_chk" name="show_summary_chk"/>
                <label class="col-form-label" for="show_summary_chk">Summarize</label>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-sm btn-darkpurple" onclick="fnSearch()">Search</button>
            </div>
        </div>
    </form>

    <div id="reportResultDiv" class="p-2 mt-3"></div>
</div>

<script>
$(document).ready(function() {
    SetUpBasics();
    $('.datepicker').removeClass('hasDatepicker').datepicker({
        <?php if ($_SESSION["user_type"] === "OPERATOR"): ?>minDate: -2,<?php endif; ?>
        dateFormat: 'dd-mm-yy'
    });
});

function fnLoadCat2() {
    var type = $('#job_type_txt').val();
    var sel  = $('#cat2_txt');
    sel.html('<option value="all">All</option>');
    if (type === 'all') return;
    $.getJSON('api/get_cat2_by_type.php', { job_type: type }, function(data) {
        $.each(data, function(i, v) {
            sel.append('<option value="'+v+'">'+v+'</option>');
        });
    });
}

function fnSearch() {
    var from             = $('#fromDtTxt').val();
    var to               = $('#toDtTxt').val();
    var user_name_txt    = $('#user_name_txt').val();
    var job_type_txt     = $('#job_type_txt').val();
    var cat2_txt         = $('#cat2_txt').val();
    var show_summary_chk = $('#show_summary_chk').is(':checked') ? 1 : 0;

    if (!from || !to) { toastr.error("Please select both dates."); return; }

    $('#reportResultDiv').html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin"></i> Loading…</div>');

    // Destroy existing DataTable if search run again
    if ($.fn.DataTable.isDataTable('#iwDetailTable')) $('#iwDetailTable').DataTable().destroy();

    $.ajax({
        type: "POST",
        url:  "api/get_itemwise_sales_report.php",
        dataType: "json",
        data: {
            from:             from,
            to:               to,
            user_name_txt:    user_name_txt,
            job_type_txt:     job_type_txt,
            cat2_txt:         cat2_txt,
            show_summary_chk: show_summary_chk
        },
        success: function(res) {
            if (!res || !res.iw_table) { toastr.error("Failed to get report."); return; }

            var dtBtns = [
                {
                    extend: 'excelHtml5',
                    text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">table_view</span> Excel',
                    className: 'btn buttons-excel',
                    exportOptions: { rows: { selected: false } }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">picture_as_pdf</span> PDF',
                    className: 'btn buttons-pdf',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: 'print',
                    text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">print</span> Print',
                    className: 'btn btn-secondary',
                    customize: function(win) {
                        $(win.document.body).css('font-size','12px');
                        $(win.document.body).find('table').css('border-collapse','collapse');
                    }
                }
            ];

            var html  = '<div class="row">';
            html     += '<div class="col-10" id="printable_div">';
            html += res.iw_table;

            html += '</div>';

            // Summary panel
            html += '<div class="col-2 text-bg-light" style="border-left:1px inset #ccc;" id="card_1">';
            html += res.summary_html;
            html += '<hr style="margin:8px 0;">';
            html += '<center>';
            html += '<button type="button" class="btn btn-primary btn-sm" onclick="fnPrintReport()">PRINT</button>';
            html += '<br><br>';
            html += '<button type="button" class="btn btn-secondary btn-sm" onclick="fnSideMenu(1)">Close</button>';
            html += '</center>';
            html += '</div>';

            html += '</div>';

            $('#reportResultDiv').html(html);

            // Init DataTable — Itemwise
            $('#iwDetailTable').DataTable({
                lengthMenu: [[25, 50, 100, -1],[25, 50, 100, "All"]],
                language: { emptyTable: 'No data' },
                dom: 'lBfrtip',
                orderCellsTop: true,
                buttons: dtBtns.map(function(b) {
                    return Object.assign({}, b, { title: 'Itemwise Sales' });
                })
            });
        },
        error: function() { toastr.error("Network error. Please try again."); }
    });
}

function fnPrintReport() {
    var from = $('#fromDtTxt').val();
    var to   = $('#toDtTxt').val();
    var header = '<style>*{font-size:12px;}th{border-bottom:1px solid #000;}td{border-bottom:1px solid #eee;}</style>';
    header += '<div style="text-align:center;font-size:1.2rem;font-weight:bold;">PRINTZY</div>';
    header += '<div style="text-align:center;font-size:1.2rem;font-weight:bold;">VADAPALANI, CHENNAI - 600026, TAMILNADU</div>';
    header += '<div style="text-align:center;font-size:1.2rem;font-weight:bold;">ITEMWISE SALES REPORT</div>';
    header += '<div style="text-align:center;font-size:1rem;font-weight:bold;">For Period : '+from+' - '+to+'</div>';
    header += '<hr style="border:none;border-bottom:5px solid black;">';
    var body   = '<div style="font-size:0.6vw;">'+$('#printable_div').html()+'</div>';
    var footer = '<br><table style="width:50%;border-collapse:collapse;border:1px solid grey;">';
    footer    += '<tr><td style="text-align:center;"><h5>SUMMARY</h5></td></tr>';
    footer    += '<tr><td>'+$('#card_1').html()+'</td></tr></table>';
    var data   = '<div>'+header+body+footer+'</div>';
    $('#printContentDiv').html(data);
    printDiv(data);
    $('#printContentDiv').html('');
}
</script>
