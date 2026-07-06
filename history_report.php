<?php
session_start();
include_once "connect_db.php";
include_once "page_guard.php";
?>
<style>
.reportContent {
    margin-top: 20px !important;
    border: 0px solid #ccc; border-radius: 4px;
    min-height: 550px;
}
.reportContent h4 {}
div.dt-container div.dt-length select { width: 35%; }
div.dt-container div.dt-length label { width: 55%; }
.reportContent .iconImgCls { width: 50px; }
.reportContent .iconDivCls { text-align: left; padding: 10px; }
.dt-buttons { display: flex; justify-content: flex-end; margin-bottom: 10px; gap: 10px; }
.buttons-excel { background-color: #28a745 !important; color: white !important; border: none !important; border-radius: 6px; margin-left: 10px !important; }
.buttons-pdf   { background-color: #dc3545 !important; color: white !important; border: none !important; border-radius: 6px; }
.buttons-excel:hover { background-color: #218838 !important; }
.buttons-pdf:hover   { background-color: #c82333 !important; }
.dt-button { font-weight: 600; font-size: 14px; padding: 8px 14px !important; height: 30px; line-height: 1.1em; }
#resultDetailTable td, #resultDetailTable th { padding: 3px; }
.reportContent button { margin-top: -5px; }
.reportContent .col-form-label { text-align: right; line-height: 8px; font-weight: bold; }
#reportResultDiv { border: 1px solid #cfcfcf; background: #fff; min-height: 200px; font-size: 0.8rem; }
.reportContent > .headerDiv { background-color: #3a3a3a; border-radius: 5px; color: #fff; }

/* ── File viewer modal ── */
#jcFileModal {
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,0.75); overflow: auto;
}
#jcFileModal .modal-box {
    background: #fff; max-width: 900px; margin: 40px auto;
    border-radius: 10px; overflow: hidden;
}
#jcFileModal .modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-bottom: 1px solid #e0e0e0; background: #f8f9fa;
}
#jcFileModal .modal-header span { font-weight: 600; font-size: 15px; }
#jcFileModal .modal-header .meta { font-size: 12px; color: #666; }
#jcFileModal .modal-header button {
    border: none; background: none; font-size: 24px; cursor: pointer;
    line-height: 1; margin-top: -2px; color: #333;
}
#jcFileGrid { display: flex; flex-wrap: wrap; gap: 14px; padding: 20px; min-height: 100px; }
.jc-file-card {
    width: 170px; border: 1px solid #ddd; border-radius: 8px;
    overflow: hidden; display: flex; flex-direction: column; background: #fafafa;
}
.jc-file-card .thumb {
    height: 130px; display: flex; align-items: center; justify-content: center;
    background: #f0f0f0; overflow: hidden; cursor: pointer;
}
.jc-file-card .thumb img { max-width: 100%; max-height: 130px; object-fit: contain; }
.jc-file-card .thumb .doc-icon { font-size: 46px; }
.jc-file-card .fname {
    padding: 6px 8px 4px; font-size: 11px; color: #555;
    word-break: break-all; flex: 1;
}
.jc-file-card .dl-btn {
    display: block; text-align: center; padding: 7px;
    background: #0d6efd; color: #fff; text-decoration: none;
    font-size: 12px; font-weight: 500;
}
.jc-file-card .dl-btn:hover { background: #0b5ed7; color: #fff; }
.jc-file-card .dl-btn.disabled {
    background: #ccc; color: #777; pointer-events: none; cursor: not-allowed;
}
</style>

<div class="bg_aliceblue p-3 m-1 pt-0 reportContent">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center p-2 mb-3 headerDiv">
        <h4 class="mb-0">History Report</h4>
    </div>

    <div class="row g-2 align-items-center mb-2">
    <label for="customer_name_search_txt" class="col-md-1 col-form-label">Name</label>
    <div class="col-md-2">
        <input type="text" autocomplete="off" class="form-control" placeholder="General/Credit Name"
            id="customer_name_search_txt" name="customer_name_search_txt" maxlength="100" />
    </div>
    <label for="customer_mobile_no_txt" class="col-md-1 col-form-label">Mobile</label>
    <div class="col-md-2">
        <input type="text" autocomplete="off" class="form-control" placeholder="Mobile No"
            id="customer_mobile_no_txt" name="customer_mobile_no_txt" maxlength="15" />
    </div>
    <label for="filter_user_txt" class="col-md-1 col-form-label">User</label>
    <div class="col-md-2">
        <select class="form-control" id="filter_user_txt">
            <option value="">-- All Users --</option>
            <?php
            $usr_sql = "SELECT DISTINCT created_by FROM jobcard_master WHERE created_by IS NOT NULL AND created_by != '' ORDER BY created_by ASC";
            if ($usr_qry = mysqli_query($connection, $usr_sql)) {
                while ($usr_row = mysqli_fetch_array($usr_qry)) {
                    $u = htmlspecialchars($usr_row['created_by']);
                    echo "<option value=\"$u\">$u</option>";
                }
            }
            ?>
        </select>
    </div>
</div>

<div class="row g-2 align-items-center mb-2">
    <label for="filter_from_date" class="col-md-1 col-form-label">From Date</label>
    <div class="col-md-2">
        <input type="date" class="form-control" id="filter_from_date" />
    </div>
    <label for="filter_to_date" class="col-md-1 col-form-label">To Date</label>
    <div class="col-md-2">
        <input type="date" class="form-control" id="filter_to_date" />
    </div>
    <div class="col-md-2">
        <button type="button" class="btn btn-sm btn-darkpurple" onclick="fnSearch()">History</button>
        <button type="button" class="btn btn-sm btn-secondary ms-1" onclick="fnClearFilters()">Clear</button>
    </div>
</div>

    <div id="reportResultDiv" class="p-3 mt-3">
        <h5 style="text-align:center;margin:1rem;padding:1rem;">Result Area</h5>
    </div>
</div>

<!-- ── File viewer modal (shared, rendered once) ── -->
<div id="jcFileModal">
    <div class="modal-box">
        <div class="modal-header">
            <span id="jcModalTitle"></span>
            <div style="display:flex;align-items:center;gap:14px;">
                <span id="jcDlRemaining" class="meta"></span>
                <button onclick="closeFileModal()">&times;</button>
            </div>
        </div>
        <div id="jcFileGrid"></div>
    </div>
</div>

<script>
<?php
// ── Pass session user info to JS securely ──────────────────────
$sess_user_type = strtoupper($_SESSION['user_type'] ?? 'OPERATOR');
$is_admin_php   = ($sess_user_type === 'ADMIN' || $sess_user_type === 'SUPERADMIN') ? 'true' : 'false';
?>
var _jcIsAdmin   = <?= $is_admin_php ?>;
var _jcImageExts = ["jpg","jpeg","png","gif","webp","bmp","svg"];
var _jcRemaining = 0; // updated when modal opens

$(document).ready(function () {
    SetUpBasics();

    // ── Autocomplete: mobile nos — ALL customer types ──────────
    var availableMobileNos = [
        <?php
        // Removed customer_type='General' — fetch all types
        $mob_sql = "SELECT DISTINCT customer_mobile_no FROM jobcard_master
                    WHERE customer_mobile_no IS NOT NULL AND customer_mobile_no != ''";
        if ($mob_qry = mysqli_query($connection, $mob_sql)) {
            $parts = [];
            while ($mob_row = mysqli_fetch_array($mob_qry)) {
                $v = $mob_row['customer_mobile_no'];
                if (strlen($v) > 0) $parts[] = '"' . addslashes($v) . '"';
            }
            echo implode(',', $parts);
        }
        ?>
    ];
    $("#customer_mobile_no_txt").autocomplete({ source: availableMobileNos });

    // ── Autocomplete: customer names — ALL customer types ──────
    var availableCustomerNames = [
        <?php
        // Removed customer_type='General' — fetch all types
        $name_sql = "SELECT DISTINCT customer_name FROM jobcard_master
                     WHERE customer_name IS NOT NULL AND customer_name != ''";
        if ($name_qry = mysqli_query($connection, $name_sql)) {
            $parts = [];
            while ($name_row = mysqli_fetch_array($name_qry)) {
                $v = $name_row['customer_name'];
                if (strlen($v) > 0) $parts[] = '"' . addslashes($v) . '"';
            }
            echo implode(',', $parts);
        }
        ?>
    ];
    $("#customer_name_search_txt").autocomplete({ source: availableCustomerNames });

    // ── Auto-fill name when mobile is typed ────────────────────
    $("#customer_mobile_no_txt").on('keyup', function () {
        var mobile_no = $(this).val();
        if (mobile_no.length > 9) {
            $.ajax({
                type: "GET",
                url: "api/get_general_customer_name.php?mb_no=" + mobile_no,
                success: function (response) {
                    $("#customer_name_search_txt").val(response);
                }
            });
        } else {
            $("#customer_name_search_txt").val("");
        }
    });

    // ── Close modal on backdrop click ──────────────────────────
    $("#jcFileModal").on('click', function (e) {
        if (e.target === this) closeFileModal();
    });
});

// ── Search ─────────────────────────────────────────────────────
function fnSearch() {
    var mobile   = $("#customer_mobile_no_txt").val().trim();
    var name     = $("#customer_name_search_txt").val().trim();
    var user     = $("#filter_user_txt").val().trim();
    var fromDate = $("#filter_from_date").val().trim();
    var toDate   = $("#filter_to_date").val().trim();

    if (mobile.length === 0 && name.length === 0 && user.length === 0 && fromDate.length === 0 && toDate.length === 0) {
        toastr.warning("Please enter at least one filter to search.");
        return;
    }

    $.ajax({
        url: 'api/get_mobile_no_history.php',
        method: 'POST',
        data: {
            customer_mobile_no_txt: mobile,
            customer_name_txt: name,
            filter_user: user,
            filter_from_date: fromDate,
            filter_to_date: toDate
        },
        async: false,
        success: function (response) {
            if (response !== "") {
                $('#reportResultDiv').html(response);
                // DataTable init stays exactly the same as before...
                $('#resultDetailTable').DataTable({  
                    "lengthMenu": [[ -1, 10, 25, 50, 100 ], ["All", 10, 25, 50, 100 ]],
                    language: {
                        emptyTable: 'No data available in table'
                    },
                    dom: 'lBfrtip',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">table_view</span> Export to Excel',
                            title: 'History Report',
                            className: 'btn buttons-excel'
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">picture_as_pdf</span> Export to PDF',
                            className: 'btn buttons-pdf',
                            orientation: 'portrait',
                            pageSize: 'A4',
                            title: 'History Report'
                        },
                        {
                            extend: 'print',
                            text: '<span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">print</span> Print Table',
                            className: 'btn btn-secondary',
                            customize: function (win) {
                                $(win.document.body).css('font-size', '14px');
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('border-collapse', 'collapse')
                                    .find('thead th')
                                    .css({
                                        'background-color': '#343a40',
                                        'color': 'white',
                                        'padding': '8px',
                                        'text-align': 'center'
                                    });
                                $(win.document.body).find('table').css('margin', '0 auto');
                            }
                        }
                    ]
                });
            } else {
                toastr.error("No records found!!!");
            }
        }
    });
}

function fnClearFilters() {
    $("#customer_mobile_no_txt").val("");
    $("#customer_name_search_txt").val("");
    $("#filter_user_txt").val("");
    $("#filter_from_date").val("");
    $("#filter_to_date").val("");
    $("#reportResultDiv").html('<h5 style="text-align:center;margin:1rem;padding:1rem;">Result Area</h5>');
}
// ── File viewer ────────────────────────────────────────────────
function openFileViewer(jcn, files, remaining, isAdmin) {
    _jcRemaining = remaining;
    document.getElementById('jcModalTitle').textContent = 'Files — Job Card #' + jcn;
    updateDlBadge(isAdmin);

    var grid = document.getElementById('jcFileGrid');
    grid.innerHTML = '';

    files.forEach(function (f) {
        var ext    = (f.file_type || '').toLowerCase().replace('.', '');
        var isImg  = _jcImageExts.indexOf(ext) >= 0;
        var card   = document.createElement('div');
        card.className = 'jc-file-card';

        // Thumbnail or doc icon
        var thumbHtml = '';
        if (isImg) {
            var imgUrl = '/' + f.file_path.replace(/^\/+/, '');
            thumbHtml  = '<div class="thumb" onclick="window.open(\'' + imgUrl + '\',\'_blank\')" title="Click to view full size">'
                       + '<img src="' + imgUrl + '" alt="preview" '
                       + '     onerror="this.parentNode.innerHTML=\'<span style=&quot;font-size:11px;color:#999;&quot;>Preview unavailable</span>\'">'
                       + '</div>';
        } else {
            var icons  = { pdf: '📄', psd: '🎨', ai: '🎨', zip: '🗜️' };
            var icon   = icons[ext] || '📁';
            thumbHtml  = '<div class="thumb"><span class="doc-icon">' + icon + '</span></div>';
        }

        // File name label
        var fname     = f.file_name || f.original_name || 'File';
        var shortName = fname.length > 26 ? fname.substring(0, 24) + '…' : fname;

        // Download button — limit checked client-side (server enforces too)
        var dlUrl    = 'api/download_jc_file.php?id=' + f.upload_id + '&file=' + encodeURIComponent(f.file_name);
        var canDl    = isAdmin || _jcRemaining > 0;
        var dlBtnHtml = canDl
            ? '<a class="dl-btn" href="' + dlUrl + '" target="_blank" '
              + '   onclick="onDownloadClick(' + isAdmin + ')">&#11015; Download</a>'
            : '<span class="dl-btn disabled">&#128683; Limit reached</span>';

        card.innerHTML = thumbHtml
            + '<div class="fname" title="' + fname + '">' + shortName + '</div>'
            + dlBtnHtml;

        grid.appendChild(card);
    });

    document.getElementById('jcFileModal').style.display = 'block';
}

function onDownloadClick(isAdmin) {
    if (!isAdmin && _jcRemaining > 0) {
        _jcRemaining--;
        updateDlBadge(isAdmin);
        // Grey out remaining buttons if limit now 0
        if (_jcRemaining === 0) {
            document.querySelectorAll('#jcFileGrid .dl-btn:not(.disabled)').forEach(function (btn) {
                btn.outerHTML = '<span class="dl-btn disabled">&#128683; Limit reached</span>';
            });
        }
    }
}

function updateDlBadge(isAdmin) {
    var txt = isAdmin ? '&#9989; Unlimited downloads' : (_jcRemaining + ' downloads left today');
    document.getElementById('jcDlRemaining').innerHTML = txt;
}

function closeFileModal() {
    document.getElementById('jcFileModal').style.display = 'none';
}
</script>