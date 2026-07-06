<?php
// SARAVANA - START (Advance Payment Feature)
session_start();
include_once "connect_db.php";
include_once "page_guard.php";

// ── Build autocomplete data for customer name + mobile ────────────────────────
// Combines jobcard_master (past JC customers) + customer_master (saved General customers)
$adv_ac_names   = [];
$adv_ac_mobiles = [];

$ac_sql = "SELECT customer_name, customer_mobile_no AS mobile
             FROM jobcard_master
            WHERE customer_type = 'General' AND customer_name != '' AND customer_mobile_no != ''
           UNION
           SELECT customer_name, mobile_no AS mobile
             FROM customer_master
            WHERE customer_type = 'General' AND customer_name != '' AND mobile_no != ''
           ORDER BY customer_name";
if ($ac_qry = mysqli_query($connection, $ac_sql)) {
    while ($ac_row = mysqli_fetch_assoc($ac_qry)) {
        $adv_ac_names[]   = addslashes($ac_row['customer_name']);
        $adv_ac_mobiles[] = addslashes($ac_row['mobile']);
    }
}
$adv_ac_names   = array_values(array_unique($adv_ac_names));
$adv_ac_mobiles = array_values(array_unique($adv_ac_mobiles));
// SARAVANA - END
?>
<style>
.adv-card        { border:1px solid #ccc; border-radius:8px; padding:18px; background:#fff; }
.adv-label       { font-weight:600; font-size:0.85rem; }
.adv-section-hdr { background:#3b68cf; color:#fff; padding:7px 12px; border-radius:5px; margin-bottom:12px; font-weight:600; }
#adv_qr_wrap     { text-align:center; margin-top:10px; }
#adv_qr_div      { display:inline-block; }
.badge-pending   { background:#ffc107; color:#000; padding:3px 8px; border-radius:4px; font-size:0.78rem; }
.badge-linked    { background:#28a745; color:#fff; padding:3px 8px; border-radius:4px; font-size:0.78rem; }
.badge-qr-pending { background:#ff9800; color:#fff; padding:2px 7px; border-radius:4px; font-size:0.75rem; font-weight:600; }
.badge-qr-paid    { background:#198754; color:#fff; padding:2px 7px; border-radius:4px; font-size:0.75rem; font-weight:600; }
.badge-qr-na      { background:#6c757d; color:#fff; padding:2px 7px; border-radius:4px; font-size:0.75rem; }
#advTable th, #advTable td { font-size:0.8rem; vertical-align:middle; }
@keyframes adv-qr-spin {
    0%   { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>


<div class="container-fluid mt-2">

  <!-- ── Entry Form ───────────────────────────────────────────────────── -->
  <div class="row mb-3">
    <div class="col-md-5">
      <div class="adv-card">
        <div class="adv-section-hdr">💰 Record Advance Payment</div>
        <div class="row g-2">
          <div class="col-6">
            <label class="adv-label">Customer Name <span class="text-danger">*</span></label>
            <input type="text" id="adv_customer_name" class="form-control form-control-sm" placeholder="Enter name" autocomplete="off"/>
          </div>
          <div class="col-6">
            <label class="adv-label">Mobile No <span class="text-danger">*</span></label>
            <input type="text" id="adv_customer_mobile" class="form-control form-control-sm numericOnly" placeholder="10-digit mobile" maxlength="10" autocomplete="off"/>
          </div>
          <div class="col-6">
            <label class="adv-label">Pay Mode <span class="text-danger">*</span></label>
            <select id="adv_pay_mode" class="form-select form-select-sm" onchange="fnAdvPayModeChange()">
              <option value="Cash">Cash</option>
              <option value="GPay">GPay / UPI</option>
            </select>
          </div>
          <div class="col-6">
            <label class="adv-label">Amount (₹) <span class="text-danger">*</span></label>
            <input type="number" id="adv_amount" class="form-control form-control-sm" placeholder="0.00" min="1" step="0.01"
                   oninput="fnAdvAmountChange()"/>
          </div>
          <div class="col-12">
            <label class="adv-label">Remarks</label>
            <input type="text" id="adv_remarks" class="form-control form-control-sm" placeholder="Optional note"/>
          </div>
        </div>

        <!-- Generate QR button (shown when GPay selected + amount entered) -->
        <div id="adv_gen_qr_btn_wrap" style="display:none; margin-top:10px;">
          <button type="button" onclick="fnAdvGenerateQR()" style="width:100%;padding:6px;background:#0d6efd;color:#fff;border:none;border-radius:4px;font-size:13px;font-weight:600;cursor:pointer;">&#9654; Generate QR Code</button>
        </div>

        <!-- QR Code section (GPay only) -->
        <div id="adv_qr_section" style="display:none; margin-top:12px;">
          <hr/>
          <div class="adv-section-hdr" style="background:#198754;">📱 Scan &amp; Pay (GPay / UPI)</div>
          <div id="adv_qr_wrap">
            <div id="adv_qr_div" style="text-align:center; min-height:130px;"></div>
            <div id="adv_qr_upi_name" style="font-weight:bold; margin-top:6px;"></div>
            <div id="adv_qr_amount_label" style="font-size:1.1rem; color:#198754; font-weight:bold;"></div>
            <!-- Payment status indicators -->
            <div id="adv_qr_payment_status" style="margin-top:10px; text-align:center;">
              <div id="adv_qr_status_waiting" style="display:none; color:#555; font-size:13px;">
                <div style="display:inline-block;width:18px;height:18px;border:3px solid #ccc;border-top-color:#D73D32;border-radius:50%;animation:adv-qr-spin 0.8s linear infinite;vertical-align:middle;margin-right:6px;"></div>
                Waiting for payment...
              </div>
              <div id="adv_qr_status_success" style="display:none;background:#e6f9ed;border:2px solid #27ae60;border-radius:8px;padding:12px 16px;color:#1a7a3a;font-size:15px;font-weight:bold;">
                &#10003; Payment Received!<br>
                <span style="font-size:12px;font-weight:normal;color:#27ae60;">UPI payment successful</span>
              </div>
              <div id="adv_qr_status_failed" style="display:none;background:#fff0f0;border:2px solid #e74c3c;border-radius:8px;padding:10px 14px;color:#c0392b;font-size:13px;font-weight:bold;">
                &#10007; Payment Failed / Timed out<br>
                <button onclick="fnAdvRetryQR()" style="margin-top:6px;padding:4px 14px;background:#e74c3c;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:12px;">&#8635; Try Again</button>
              </div>
            </div>
            <!-- Save-as-pending note (shown after QR is generated, before payment) -->
            <div id="adv_save_pending_note" style="display:none;margin-top:8px;background:#fff3cd;border:1px solid #ffc107;border-radius:5px;padding:8px;font-size:12px;color:#856404;text-align:center;">
              ⏳ Payment not confirmed yet — you can save as <strong>Awaiting Payment</strong>.<br>
              Status will auto-update once the customer pays.
            </div>
          </div>
        </div>

        <div class="mt-3 d-flex gap-2">
          <button class="btn btn-success btn-sm" onclick="fnSaveAdvancePayment()">
            <span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">save</span> Save Payment
          </button>
          <button class="btn btn-secondary btn-sm" onclick="fnAdvReset()">
            <span class="material-icons-round" style="font-size:16px;vertical-align:-3px;">refresh</span> Clear
          </button>
        </div>
        <div id="adv_msg" class="mt-2"></div>
      </div>
    </div>

    <!-- ── Info panel ──────────────────────────────────────────────────── -->
    <div class="col-md-7">
      <div class="adv-card" style="background:#f0f7ff;">
        <div class="adv-section-hdr" style="background:#6c757d;">ℹ️ How it works</div>
        <ul style="font-size:0.85rem; line-height:1.9;">
          <li>Record the customer's advance payment <strong>before</strong> a Job Card is created.</li>
          <li>For <strong>GPay</strong> payments — a QR code is generated; scan to collect payment.</li>
          <li>You can save even if the QR payment is not yet confirmed — it will be stored as <strong>⏳ Awaiting Payment</strong>.</li>
          <li>Once the customer pays, the status automatically changes to <strong>✅ Paid</strong>.</li>
          <li>Only <strong>paid</strong> advance records (Cash or confirmed GPay) allow creating a Job Card.</li>
          <li>When opening a Job Card from here, <strong>only General customer type</strong> is allowed.</li>
          <li>After the Job Card is saved, the advance is automatically marked <strong>Linked</strong>.</li>
        </ul>
      </div>
    </div>
  </div>

  <!-- ── Pending Advances List ──────────────────────────────────────────── -->
  <div class="adv-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="adv-section-hdr mb-0">📋 Advance Payments List</div>
      <div class="d-flex gap-2 align-items-center">
        <span id="adv_auto_refresh_badge" style="display:none;"></span>
        <select id="adv_filter_sel" class="form-select form-select-sm" style="width:150px;" onchange="fnLoadAdvList()">
          <option value="pending">⏳ Pending Only</option>
          <option value="linked">✅ Linked Only</option>
          <option value="all">📋 All Records</option>
        </select>
        <button class="btn btn-sm btn-outline-primary" onclick="fnLoadAdvList()">🔄 Refresh</button>
      </div>
    </div>
    <div style="overflow-x:auto;">
      <table class="table table-bordered table-hover table-sm" id="advTable">
        <thead class="table-dark">
          <tr>
            <th>SNo.</th>
            <th>Date</th>
            <th>Customer Name</th>
            <th>Mobile</th>
            <th>Pay Mode</th>
            <th>Pay Status</th>
            <th>Amount (₹)</th>
            <th>QR Code</th>
            <th>Remarks</th>
            <th>Recorded By</th>
            <th>Status</th>
            <th>Linked JC</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="advTableBody">
          <tr><td colspan="13" class="text-center text-muted">Loading…</td></tr>
        </tbody>
      </table>
    </div>
  </div>

</div><!-- /container -->

<script>
// ── Canara Bank QR integration for Advance Payment ───────────────────────────
var _advCanaraCurrentTxnId  = null;
var _advCanaraPollingTimer  = null;
var _advCanaraRetryTimer    = null;
var _advCanaraPaymentStatus = null; // null | 'SUCCESS' | 'FAILED'
var _advCanaraFailedCount   = 0;   // consecutive FAILED responses before showing error UI
var _advQrDebounceTimer     = null;
var _advCurrentQrString     = null; // qr_string from bank response (stored for pending save)
var _advListPollTimer       = null; // auto-refresh timer for pending list entries
var _advPendingExtIds       = [];   // ext_transaction_ids currently PENDING in the list
var _advRowQrMap            = {};   // advance_id → qr_string (populated when list loads)

function _advCanaraStopPolling() {
    if (_advCanaraPollingTimer) { clearInterval(_advCanaraPollingTimer); _advCanaraPollingTimer = null; }
    if (_advCanaraRetryTimer)   { clearTimeout(_advCanaraRetryTimer);   _advCanaraRetryTimer   = null; }
}

function _advCanaraStartPolling(ext_id) {
    _advCanaraStopPolling();
    _advCanaraFailedCount = 0; // reset on each fresh poll session
    _advCanaraPollingTimer = setInterval(function() {
        $.getJSON('api/canara_qr_status.php', { ext_id: ext_id }, function(res) {
            if (res.status === 'SUCCESS') {
                _advCanaraStopPolling();
                _advCanaraFailedCount   = 0;
                _advCanaraPaymentStatus = 'SUCCESS';
                $("#adv_qr_status_waiting").hide();
                $("#adv_qr_status_failed").hide();
                $("#adv_qr_status_success").show();
                $("#adv_save_pending_note").hide();
                $("#adv_qr_div").html('<div style="background:#27ae60;color:#fff;font-size:13px;font-weight:bold;border-radius:6px;padding:12px 8px;text-align:center;line-height:1.8;">&#10003;<br>PAID</div>');
                toastr.success("Payment received via UPI!");
            } else if (res.status === 'FAILED') {
                // Bank sometimes returns FAILED as an intermediate state before finalising.
                // Only show the error UI after 5 consecutive FAILED responses.
                _advCanaraFailedCount++;
                if (_advCanaraFailedCount >= 5 && _advCanaraPaymentStatus !== 'FAILED') {
                    _advCanaraPaymentStatus = 'FAILED';
                    $("#adv_qr_status_waiting").hide();
                    $("#adv_qr_status_success").hide();
                    $("#adv_qr_status_failed").show();
                    toastr.error("UPI payment failed. If you have paid, please wait. Otherwise click Try Again.");
                }
                // Keep polling — a successful retry on same QR should still be detected
            }
        });
    }, 3000);

    // Auto-stop after 5 minutes — show pending note so user knows they can save anyway
    setTimeout(function() {
        if (_advCanaraPollingTimer) {
            _advCanaraStopPolling();
            if ($("#adv_qr_status_success").is(":hidden")) {
                $("#adv_qr_status_waiting").hide();
                $("#adv_qr_status_failed").show();
                $("#adv_save_pending_note").show();
            }
        }
    }, 300000);
}

function fnAdvRetryQR() {
    fnAdvGenerateQR();
}

function fnAdvGenerateQR() {
    var amt = parseFloat($("#adv_amount").val()) || 0;

    // Clear previous state
    _advCanaraStopPolling();
    _advCanaraCurrentTxnId  = null;
    _advCanaraPaymentStatus = null;
    _advCurrentQrString     = null;
    $("#adv_qr_div").html("");
    $("#adv_qr_status_waiting").hide();
    $("#adv_qr_status_success").hide();
    $("#adv_qr_status_failed").hide();
    $("#adv_save_pending_note").hide();

    if (amt <= 0) {
        $("#adv_qr_section").hide();
        return;
    }

    $("#adv_qr_section").show();
    $("#adv_qr_amount_label").text("₹ " + amt.toFixed(2) + " /-");
    $("#adv_qr_div").html('<div style="color:#888;font-size:12px;padding:20px;">Generating QR…</div>');
    $("#adv_qr_status_waiting").show();

    $.ajax({
        type:     "POST",
        url:      "api/canara_qr_generate.php",
        dataType: "json",
        timeout:  150000,
        data:     { amount: amt, jobcard_no: null, customer_name: $.trim($("#adv_customer_name").val()) },
        success: function(res) {
            if (!res.success) {
                if (res.already_paid) {
                    _advCanaraPaymentStatus = 'SUCCESS';
                    $("#adv_qr_div").html('<div style="background:#27ae60;color:#fff;font-size:13px;font-weight:bold;border-radius:6px;padding:12px 8px;text-align:center;line-height:1.8;">&#10003;<br>PAID</div>');
                    $("#adv_qr_status_waiting").hide();
                    $("#adv_qr_status_failed").hide();
                    $("#adv_qr_status_success").show();
                    toastr.info(res.error);
                } else {
                    $("#adv_qr_div").html('<div style="color:red;font-size:12px;">QR generation failed.</div>');
                    $("#adv_qr_status_waiting").hide();
                }
                return;
            }
            _advCanaraCurrentTxnId = res.transaction_id;
            _advCurrentQrString    = res.qr_string;
            $("#adv_qr_div").html("");
            try {
                new QRCode(document.getElementById("adv_qr_div"), {
                    text:         res.qr_string,
                    width:        160,
                    height:       160,
                    colorDark:    "#000000",
                    colorLight:   "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            } catch(e) {
                $("#adv_qr_div").html('<div style="color:red;font-size:12px;">QR render error.</div>');
                $("#adv_qr_status_waiting").hide();
                return;
            }
            // Show the "you can save as pending" note
            $("#adv_save_pending_note").show();
            _advCanaraStartPolling(res.transaction_id);
        },
        error: function() {
            $("#adv_qr_div").html('<div style="color:red;font-size:12px;">Network error generating QR.</div>');
            $("#adv_qr_status_waiting").hide();
        }
    });
}

function fnAdvPayModeChange() {
    var mode = $("#adv_pay_mode").val();
    if (mode === "GPay") {
        var amt = parseFloat($("#adv_amount").val()) || 0;
        $("#adv_gen_qr_btn_wrap").toggle(amt > 0);
    } else {
        $("#adv_gen_qr_btn_wrap").hide();
        _advCanaraStopPolling();
        _advCurrentQrString = null;
        $("#adv_qr_section").hide();
        $("#adv_save_pending_note").hide();
    }
}

function fnAdvAmountChange() {
    if ($("#adv_pay_mode").val() !== "GPay") return;
    var amt = parseFloat($("#adv_amount").val()) || 0;
    if (amt > 0) {
        $("#adv_gen_qr_btn_wrap").show();
    } else {
        $("#adv_gen_qr_btn_wrap").hide();
        _advCanaraStopPolling();
        _advCurrentQrString = null;
        $("#adv_qr_section").hide();
        $("#adv_save_pending_note").hide();
    }
}

function fnSaveAdvancePayment() {
    var name   = $.trim($("#adv_customer_name").val());
    var mobile = $.trim($("#adv_customer_mobile").val());
    var mode   = $("#adv_pay_mode").val();
    var amt    = parseFloat($("#adv_amount").val()) || 0;
    var rmk    = $.trim($("#adv_remarks").val());

    if (!name)                        { toastr.warning("Enter customer name.");             return; }
    if (!mobile || mobile.length < 10){ toastr.warning("Enter valid 10-digit mobile.");     return; }
    if (amt <= 0)                     { toastr.warning("Enter a valid amount.");             return; }

    // Determine QR payment status
    var qrPayStatus = 'NA';
    var extTxnId    = '';
    var qrString    = '';

    if (mode === 'GPay') {
        // Must have generated a QR first
        if (!_advCanaraCurrentTxnId) {
            toastr.warning("Please generate the QR code before saving a GPay payment.");
            return;
        }
        qrPayStatus = (_advCanaraPaymentStatus === 'SUCCESS') ? 'PAID' : 'PENDING';
        extTxnId    = _advCanaraCurrentTxnId || '';
        qrString    = _advCurrentQrString    || '';
    }

    $.ajax({
        type:     "POST",
        url:      "api/save_advance_payment.php",
        dataType: "json",
        data: {
            customer_name:      name,
            customer_mobile:    mobile,
            pay_mode:           mode,
            amount:             amt,
            remarks:            rmk,
            qr_payment_status:  qrPayStatus,
            ext_transaction_id: extTxnId,
            qr_string:          qrString
        },
        success: function(res) {
            if (res.status === "success") {
                if (qrPayStatus === 'PENDING') {
                    toastr.info("⏳ Saved as Awaiting Payment. List will auto-refresh when the customer pays.");
                } else {
                    toastr.success("Advance payment saved! ID: " + res.advance_id);
                }
                fnAdvReset();
                fnLoadAdvList();
            } else {
                toastr.error(res.message || "Save failed.");
            }
        },
        error: function() { toastr.error("Network error."); }
    });
}

function fnAdvReset() {
    _advCanaraStopPolling();
    _advCanaraCurrentTxnId  = null;
    _advCanaraPaymentStatus = null;
    _advCurrentQrString     = null;
    clearTimeout(_advQrDebounceTimer);
    $("#adv_customer_name").val("");
    $("#adv_customer_mobile").val("");
    $("#adv_pay_mode").val("Cash");
    $("#adv_amount").val("");
    $("#adv_remarks").val("");
    $("#adv_qr_div").html("");
    $("#adv_qr_section").hide();
    $("#adv_gen_qr_btn_wrap").hide();
    $("#adv_qr_status_waiting").hide();
    $("#adv_qr_status_success").hide();
    $("#adv_qr_status_failed").hide();
    $("#adv_save_pending_note").hide();
    $("#adv_msg").html("");
}

function fnAdvStartListPoll() {
    if (_advPendingExtIds.length === 0) return;
    $("#adv_auto_refresh_badge").html('<span style="color:#ff9800;font-size:0.75rem;">&#9203; Auto-checking ' + _advPendingExtIds.length + ' pending…</span>').show();
    _advListPollTimer = setInterval(function() {
        if (_advPendingExtIds.length === 0) { fnAdvStopListPoll(); return; }
        var extId = _advPendingExtIds[0];
        $.getJSON('api/canara_qr_status.php', { ext_id: extId }, function(res) {
            if (res.status === 'SUCCESS') {
                _advPendingExtIds = _advPendingExtIds.filter(function(id) { return id !== extId; });
                toastr.success("Payment confirmed for a pending advance!");
                fnLoadAdvList();
            }
        });
    }, 10000);
}

function fnAdvStopListPoll() {
    if (_advListPollTimer) { clearInterval(_advListPollTimer); _advListPollTimer = null; }
    _advPendingExtIds = [];
    $("#adv_auto_refresh_badge").hide();
}

function fnLoadAdvList() {
    var filter = $("#adv_filter_sel").val();
    $("#advTableBody").html('<tr><td colspan="13" class="text-center">Loading…</td></tr>');
    fnAdvStopListPoll(); // stop previous poll — will restart below if new pending entries found

    $.ajax({
        type: "GET",
        url:  "api/get_advance_payments.php?filter=" + filter,
        success: function(rows) {
            if (!rows || rows.length === 0) {
                $("#advTableBody").html('<tr><td colspan="13" class="text-center text-muted">No records found.</td></tr>');
                return;
            }

            var pendingExtIds = [];
            _advRowQrMap = {}; // reset map for this load
            var html = "";

            $.each(rows, function(i, r) {
                // Store qr_string indexed by advance_id so fnCreateJCFromAdvance can access it safely
                _advRowQrMap[r.advance_id] = r.qr_string || '';
                var linkedBadge = r.is_linked == 1
                    ? '<span class="badge-linked">&#10003; Linked</span>'
                    : '<span class="badge-pending">&#8987; Pending</span>';
                var linkedJC = r.linked_jobcard_no
                    ? '<a href="javascript:void(0)" onclick="showJobCard(' + r.linked_jobcard_no + ')">JC-' + r.linked_jobcard_no + '</a>'
                    : '—';

                // Pay Status badge
                var qrStatus = r.qr_payment_status || 'NA';
                var payStatusHtml;
                if (r.pay_mode === 'GPay') {
                    if (qrStatus === 'PAID') {
                        payStatusHtml = '<span class="badge-qr-paid">&#10003; Paid</span>';
                    } else if (qrStatus === 'PENDING') {
                        payStatusHtml = '<span class="badge-qr-pending">&#9203; Awaiting</span>';
                        if (r.ext_transaction_id) pendingExtIds.push(r.ext_transaction_id);
                    } else {
                        payStatusHtml = '<span class="badge-qr-na">—</span>';
                    }
                } else {
                    // Cash is always immediately confirmed
                    payStatusHtml = '<span class="badge-qr-paid">&#10003; Cash Paid</span>';
                }

                // QR cell — render small QR for GPay entries
                var qrCellHtml = '—';
                if (r.pay_mode === 'GPay' && r.qr_string) {
                    var qrDivId = 'adv_list_qr_' + r.advance_id;
                    qrCellHtml = '<div id="' + qrDivId + '" style="width:64px;height:64px;display:inline-block;"></div>';
                }

                // Action button — Create JC only for confirmed payments (Cash=NA or GPay=PAID)
                var canCreateJC = (r.is_linked == 0) && (qrStatus === 'NA' || qrStatus === 'PAID');
                var actionHtml;
                if (r.is_linked == 0) {
                    var deleteBtn = '<button class="btn btn-xs btn-danger btn-sm" style="font-size:0.75rem;" onclick="fnDeleteAdvance(' + r.advance_id + ')">&#128465;</button>';
                    if (canCreateJC) {
                        actionHtml = '<button class="btn btn-xs btn-primary btn-sm" style="font-size:0.75rem;" onclick="fnCreateJCFromAdvance(' + r.advance_id + ',\'' + escJs(r.customer_name) + '\',\'' + r.customer_mobile + '\',\'' + r.pay_mode + '\',' + r.amount + ')">&#129534; Create JC</button>'
                                   + '&nbsp;' + deleteBtn;
                    } else {
                        actionHtml = '<span style="color:#ff9800;font-size:0.72rem;font-weight:600;display:block;">&#9203; Awaiting<br>Payment</span>'
                                   + deleteBtn;
                    }
                } else {
                    actionHtml = '—';
                }

                var rowStyle = qrStatus === 'PENDING' ? ' style="background:#fff9e6;"' : '';
                html += '<tr' + rowStyle + '>';
                html += '<td>' + (i + 1) + '</td>';
                html += '<td>' + r.advance_date + '</td>';
                html += '<td>' + escHtml(r.customer_name) + '</td>';
                html += '<td>' + r.customer_mobile + '</td>';
                html += '<td>' + r.pay_mode + '</td>';
                html += '<td>' + payStatusHtml + '</td>';
                html += '<td style="text-align:right;">&#8377;&nbsp;' + parseFloat(r.amount).toFixed(2) + '</td>';
                html += '<td style="text-align:center;padding:4px;">' + qrCellHtml + '</td>';
                html += '<td>' + escHtml(r.remarks || '') + '</td>';
                html += '<td>' + r.created_by + '</td>';
                html += '<td>' + linkedBadge + '</td>';
                html += '<td>' + linkedJC + '</td>';
                html += '<td>' + actionHtml + '</td>';
                html += '</tr>';
            });
            $("#advTableBody").html(html);

            // Render small QR codes for GPay entries
            $.each(rows, function(i, r) {
                if (r.pay_mode === 'GPay' && r.qr_string) {
                    var el = document.getElementById('adv_list_qr_' + r.advance_id);
                    if (el && typeof QRCode !== 'undefined') {
                        try {
                            new QRCode(el, {
                                text:         r.qr_string,
                                width:        64,
                                height:       64,
                                colorDark:    "#000000",
                                colorLight:   "#ffffff",
                                correctLevel: QRCode.CorrectLevel.L
                            });
                        } catch(e) { $(el).text('—'); }
                    }
                }
            });

            // Auto-poll if any entries are still awaiting GPay confirmation
            if (pendingExtIds.length > 0) {
                _advPendingExtIds = pendingExtIds;
                fnAdvStartListPoll();
            }
        },
        error: function() {
            $("#advTableBody").html('<tr><td colspan="13" class="text-danger text-center">Failed to load.</td></tr>');
        }
    });
}

function fnCreateJCFromAdvance(advanceId, customerName, mobile, payMode, amount) {
    var storedQrString = _advRowQrMap[advanceId] || '';

    window._advanceContext = {
        advance_id:    advanceId,
        customer_name: customerName,
        mobile:        mobile,
        pay_mode:      payMode,
        amount:        amount
    };

    $("#rightContentDiv").empty();
    $("#rightContentDiv").load("job_card.php", function() {
        // ── Lock customer type to General only ────────────────────────────────
        $("#walkInCustomerOpt").prop("disabled", true);
        $("#creditCustomerOpt").prop("disabled", true);

        // Select General Customer and show the section
        $("#generalCustomerOpt").prop("checked", true).focus();
        $("#credit_customer_div").hide();
        $("#customer_div").show();
        $("#customer_name_txt").prop("disabled", false);
        $("#customer_mobile_no_txt").prop("disabled", false);
        try { $("#customer_name_txt").autocomplete("enable"); } catch(e) {}

        setTimeout(function() {
            $("#customer_name_txt").val(customerName);
            $("#customer_mobile_no_txt").val(mobile);

            if (payMode === "GPay") {
                $("#advance_gpay_txt").val(parseFloat(amount).toFixed(2)).trigger("change");

                // ── Payment already confirmed via advance — hide QR, show PAID only ──
                _canaraPaymentStatus = 'SUCCESS'; // skip re-validation on save
                $("#qrCodeSection").hide();        // no need to show QR again
                $("#generateQrBtn").hide();        // no need to generate new QR

                // Show green PAID status only
                $("#qr_status_waiting").hide();
                $("#qr_status_failed").hide();
                $("#qr_status_success").show();

            } else {
                // Cash advance — just pre-fill the amount
                $("#advance_cash_txt").val(parseFloat(amount).toFixed(2)).trigger("change");
            }

            toastr.info("Advance of ₹" + parseFloat(amount).toFixed(2) + " pre-filled. Fill remaining details and Save.");
        }, 200);
    });
}

function fnDeleteAdvance(advanceId) {
    if (!confirm("Delete this advance payment record? This cannot be undone.")) return;
    $.ajax({
        type:     "POST",
        url:      "api/delete_advance_payment.php",
        dataType: "json",
        data:     { advance_id: advanceId },
        success: function(res) {
            if (res.status === "success") {
                toastr.success("Record deleted.");
                fnLoadAdvList();
            } else {
                toastr.error(res.message || "Delete failed.");
            }
        },
        error: function() { toastr.error("Network error."); }
    });
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
function escJs(s) {
    return String(s).replace(/\\/g,'\\\\').replace(/'/g,"\\'");
}

// ── Customer autocomplete data (PHP → JS) ─────────────────────────────────────
var _advCustNames   = [<?php echo implode(',', array_map(fn($n) => '"'.$n.'"', $adv_ac_names));   ?>];
var _advCustMobiles = [<?php echo implode(',', array_map(fn($m) => '"'.$m.'"', $adv_ac_mobiles)); ?>];

$(document).ready(function() {
    fnLoadAdvList();

    // ── Name field autocomplete ────────────────────────────────────────────────
    // Shows existing General customers; free-type is still allowed for new ones
    if ($.fn.autocomplete) {
        $("#adv_customer_name").autocomplete({
            source:    _advCustNames,
            minLength: 1,
            select: function(event, ui) {
                // Auto-fill mobile when a known name is picked
                $.ajax({
                    type: "GET",
                    url:  "api/get_general_customer_name.php?cust_name=" + encodeURIComponent(ui.item.value),
                    async: false,
                    success: function(resp) {
                        if (resp.trim() !== "") {
                            $("#adv_customer_mobile").val(resp.trim());
                        }
                    }
                });
            }
        });

        // ── Mobile field autocomplete ──────────────────────────────────────────
        $("#adv_customer_mobile").autocomplete({
            source:    _advCustMobiles,
            minLength: 3,
            select: function(event, ui) {
                // Auto-fill name when a known mobile is picked
                $.ajax({
                    type: "GET",
                    url:  "api/get_general_customer_name.php?mb_no=" + encodeURIComponent(ui.item.value),
                    async: false,
                    success: function(resp) {
                        if (resp.trim() !== "") {
                            $("#adv_customer_name").val(resp.trim());
                        }
                    }
                });
            }
        });
    }
});
</script>
<?php // SARAVANA - END (Advance Payment Feature - entire file) ?>
