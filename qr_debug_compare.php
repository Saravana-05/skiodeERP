<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once "connect_db.php";
include_once "page_guard.php";
?>
<style>
.debug-container { max-width: 100%; }
.debug-header { display: flex; align-items: center; gap: 14px; margin-bottom: 20px; }
.debug-header-icon { width: 48px; height: 48px; border-radius: 14px; background: #fef2f2; display: flex; align-items: center; justify-content: center; }
.debug-header-icon .material-icons-round { font-size: 1.5rem; color: #dc2626; }
.debug-title { font-size: 1.3rem; font-weight: 800; color: #0f172a; }
.debug-subtitle { font-size: 0.82rem; color: #64748b; }

.dbg-card-section { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
.dbg-section-title { font-size: 0.9rem; font-weight: 700; color: #1e293b; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
.dbg-section-title .material-icons-round { font-size: 1.1rem; }

.dbg-input-row { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 14px; }
.dbg-input-group { display: flex; flex-direction: column; gap: 3px; }
.dbg-input-group label { font-size: 0.7rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.3px; }
.dbg-input-group input, .dbg-input-group select {
    padding: 7px 10px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.82rem; background: #fff; color: #1e293b;
}
.dbg-input-group input:focus, .dbg-input-group select:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 3px rgba(99,102,241,0.08); }

textarea.dbg-sms-input {
    width: 100%; min-height: 160px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;
    font-size: 0.8rem; font-family: 'Courier New', monospace; line-height: 1.6; resize: vertical; color: #334155;
}
textarea.dbg-sms-input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 3px rgba(99,102,241,0.08); }
.dbg-sms-hint { font-size: 0.72rem; color: #94a3b8; margin-top: 6px; }

.dbg-btn-compare {
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 22px;
    background: #dc2626; color: #fff; border: none; border-radius: 8px;
    font-size: 0.85rem; font-weight: 700; cursor: pointer; transition: background 0.2s;
}
.dbg-btn-compare:hover { background: #b91c1c; }
.dbg-btn-fetch {
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px;
    background: #4f46e5; color: #fff; border: none; border-radius: 8px;
    font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: background 0.2s;
}
.dbg-btn-fetch:hover { background: #4338ca; }

/* Tab toggle for input mode */
.dbg-mode-tabs { display: flex; gap: 0; margin-bottom: 16px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; width: fit-content; }
.dbg-mode-tab {
    padding: 8px 20px; font-size: 0.82rem; font-weight: 600; cursor: pointer; border: none;
    background: #f8fafc; color: #64748b; transition: all 0.2s;
}
.dbg-mode-tab.active { background: #4f46e5; color: #fff; }
.dbg-mode-tab:hover:not(.active) { background: #e2e8f0; }

/* Manual entry table */
.dbg-manual-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
.dbg-manual-table thead th {
    background: #f8fafc; color: #475569; font-size: 0.72rem; font-weight: 700;
    text-transform: uppercase; padding: 8px 10px; border-bottom: 2px solid #e2e8f0; text-align: left;
}
.dbg-manual-table tbody td { padding: 4px 4px; }
.dbg-manual-table tbody input {
    width: 100%; padding: 7px 8px; border: 1px solid #e2e8f0; border-radius: 6px;
    font-size: 0.82rem; color: #1e293b; background: #fff;
}
.dbg-manual-table tbody input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 2px rgba(99,102,241,0.1); }
.dbg-manual-table tbody input.dbg-amt-input { width: 100px; text-align: right; font-weight: 600; }
.dbg-manual-table tbody input.dbg-time-input { width: 100px; }
.dbg-manual-table tbody input.dbg-ref-input { width: 180px; font-family: monospace; }
.dbg-manual-table tbody input.dbg-name-input { width: 160px; }
.dbg-row-num { font-size: 0.75rem; color: #94a3b8; font-weight: 600; text-align: center; width: 30px; }
.dbg-btn-del-row {
    background: none; border: none; color: #e11d48; cursor: pointer; font-size: 1rem;
    padding: 2px 6px; border-radius: 4px; opacity: 0.5; transition: opacity 0.2s;
}
.dbg-btn-del-row:hover { opacity: 1; background: #fef2f2; }
.dbg-btn-add-row {
    display: inline-flex; align-items: center; gap: 4px; margin-top: 8px; padding: 5px 14px;
    background: #f0fdf4; color: #059669; border: 1px dashed #86efac; border-radius: 6px;
    font-size: 0.78rem; font-weight: 600; cursor: pointer;
}
.dbg-btn-add-row:hover { background: #dcfce7; }
.dbg-entry-count { font-size: 0.75rem; color: #64748b; margin-left: 12px; }

/* Results */
.dbg-results-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 16px; }
@media(max-width:900px) { .dbg-results-grid { grid-template-columns: 1fr; } }

.dbg-result-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
.dbg-result-card-header { padding: 12px 16px; font-size: 0.82rem; font-weight: 700; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 8px; }
.dbg-result-card-header .dbg-count { margin-left: auto; background: #f1f5f9; padding: 2px 10px; border-radius: 12px; font-size: 0.75rem; color: #475569; }

.dbg-cmp-table { width: 100%; border-collapse: collapse; }
.dbg-cmp-table thead th {
    background: #f8fafc; color: #475569; font-size: 0.7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.3px; padding: 8px 10px; border-bottom: 2px solid #e2e8f0; text-align: left;
}
.dbg-cmp-table tbody td { padding: 8px 10px; font-size: 0.8rem; color: #334155; border-bottom: 1px solid #f1f5f9; }
.dbg-cmp-table tbody tr:hover { background: #f8fafc; }

.dbg-status-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: 700; }
.dbg-badge-match { background: #d1fae5; color: #065f46; }
.dbg-badge-missing { background: #fee2e2; color: #991b1b; }
.dbg-badge-extra { background: #fef3c7; color: #92400e; }
.dbg-badge-time-diff { background: #dbeafe; color: #1e40af; }

/* Summary stats */
.dbg-stats-row { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
.dbg-stat-card { flex: 1; min-width: 130px; background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; text-align: center; }
.dbg-stat-value { font-size: 1.4rem; font-weight: 800; color: #0f172a; }
.dbg-stat-label { font-size: 0.68rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.3px; margin-top: 2px; }
.dbg-stat-card.danger { border-color: #fecaca; }
.dbg-stat-card.danger .dbg-stat-value { color: #dc2626; }
.dbg-stat-card.success { border-color: #bbf7d0; }
.dbg-stat-card.success .dbg-stat-value { color: #059669; }
.dbg-stat-card.warning { border-color: #fde68a; }
.dbg-stat-card.warning .dbg-stat-value { color: #d97706; }
.dbg-stat-card.info { border-color: #bfdbfe; }
.dbg-stat-card.info .dbg-stat-value { color: #2563eb; }

.dbg-full-width { grid-column: 1 / -1; }
.dbg-hidden { display: none; }
.dbg-loader { display: inline-block; width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top: 2px solid #fff; border-radius: 50%; animation: dbg-spin 0.6s linear infinite; }
@keyframes dbg-spin { to { transform: rotate(360deg); } }

.dbg-amount { font-weight: 700; font-variant-numeric: tabular-nums; }
.dbg-amount.credit { color: #059669; }
.dbg-time-cell { font-variant-numeric: tabular-nums; white-space: nowrap; }
</style>

<div class="debug-container p-3">
    <div class="debug-header">
        <div class="debug-header-icon"><span class="material-icons-round">bug_report</span></div>
        <div>
            <div class="debug-title">QR Payment Debug — Compare & Find Mismatches</div>
            <div class="debug-subtitle">Compare GPay/Bank payments with system QR records to find missing or mismatched payments</div>
        </div>
    </div>

    <!-- Step 1: Date & Fetch -->
    <div class="dbg-card-section">
        <div class="dbg-section-title"><span class="material-icons-round">calendar_today</span> Step 1: Select Date & Fetch System Records</div>
        <div class="dbg-input-row">
            <div class="dbg-input-group">
                <label>Date</label>
                <input type="date" id="debug_date" value="<?php echo date('Y-m-d', strtotime('-1 day')); ?>"/>
            </div>
            <button class="dbg-btn-fetch" id="btnFetchSys" onclick="dbgFetchSystemData(this)"><span class="material-icons-round" style="font-size:16px;">download</span> Fetch System Records</button>
        </div>
    </div>

    <!-- Step 2: Input Mode -->
    <div class="dbg-card-section">
        <div class="dbg-section-title"><span class="material-icons-round">payments</span> Step 2: Enter Payment Details</div>

        <!-- Mode toggle -->
        <div class="dbg-mode-tabs">
            <button class="dbg-mode-tab active" onclick="dbgSwitchMode('manual')"><span class="material-icons-round" style="font-size:14px;vertical-align:-2px;">edit_note</span> Manual Entry (GPay)</button>
            <button class="dbg-mode-tab" onclick="dbgSwitchMode('sms')"><span class="material-icons-round" style="font-size:14px;vertical-align:-2px;">sms</span> Paste SMS</button>
        </div>

        <!-- Manual Entry Mode -->
        <div id="dbg_mode_manual">
            <div style="font-size:0.78rem;color:#64748b;margin-bottom:10px;">Enter payment details from GPay screenshots — Amount & Time are required, UPI Txn ID is optional but helps match accurately.</div>
            <div style="overflow-x:auto;">
            <table class="dbg-manual-table" id="dbg_manual_table">
                <thead>
                    <tr>
                        <th style="width:30px;">#</th>
                        <th>Amount (₹)</th>
                        <th>Time (HH:MM)</th>
                        <th>UPI Txn ID</th>
                        <th>Customer / Note</th>
                        <th style="width:30px;"></th>
                    </tr>
                </thead>
                <tbody id="dbg_manual_tbody">
                </tbody>
            </table>
            </div>
            <button class="dbg-btn-add-row" onclick="dbgAddManualRow()"><span class="material-icons-round" style="font-size:14px;">add</span> Add Row</button>
            <span class="dbg-entry-count" id="dbg_entry_count">0 entries</span>
        </div>

        <!-- SMS Mode (hidden by default) -->
        <div id="dbg_mode_sms" class="dbg-hidden">
            <textarea class="dbg-sms-input" id="sms_input" placeholder="Paste all bank SMS messages here. Each SMS on a new line or separated by blank lines.

Example formats supported:
Rs.500.00 credited to A/c XX1234 on 18-06-26 at 14:30:25 by UPI-RefNo 412345678901
INR 1,250.00 credited to your A/c on 18/06/2026 14:30 UPI Ref: 412345678901
Credited INR 750 to A/c XXXX1234 on 18-Jun-2026 02:30PM Ref 412345678901"></textarea>
            <div class="dbg-sms-hint">Supports Canara Bank, SBI, HDFC, ICICI and most Indian bank SMS formats. Paste credit SMS only.</div>
        </div>

        <div style="margin-top: 14px;">
            <button class="dbg-btn-compare" onclick="dbgRunComparison()"><span class="material-icons-round" style="font-size:16px;">compare_arrows</span> Compare & Find Mismatches</button>
        </div>
    </div>

    <!-- Results -->
    <div id="dbg_results_area" class="dbg-hidden">
        <div class="dbg-stats-row" id="dbg_stats_row"></div>
        <div class="dbg-results-grid">
            <div class="dbg-result-card">
                <div class="dbg-result-card-header" style="background:#fef2f2;color:#dc2626;">
                    <span class="material-icons-round" style="font-size:16px;">error</span>
                    Missing in System (You paid, System doesn't have)
                    <span class="dbg-count" id="dbg_missing_count">0</span>
                </div>
                <div id="dbg_missing_body" style="overflow-x:auto;"></div>
            </div>
            <div class="dbg-result-card">
                <div class="dbg-result-card-header" style="background:#eff6ff;color:#2563eb;">
                    <span class="material-icons-round" style="font-size:16px;">schedule</span>
                    Time Mismatches (&gt; 5 min difference)
                    <span class="dbg-count" id="dbg_time_count">0</span>
                </div>
                <div id="dbg_time_body" style="overflow-x:auto;"></div>
            </div>
            <div class="dbg-result-card dbg-full-width">
                <div class="dbg-result-card-header" style="background:#f0fdf4;color:#059669;">
                    <span class="material-icons-round" style="font-size:16px;">check_circle</span>
                    Matched Payments
                    <span class="dbg-count" id="dbg_matched_count">0</span>
                </div>
                <div id="dbg_matched_body" style="overflow-x:auto; max-height: 400px; overflow-y: auto;"></div>
            </div>
            <div class="dbg-result-card dbg-full-width">
                <div class="dbg-result-card-header" style="background:#fffbeb;color:#d97706;">
                    <span class="material-icons-round" style="font-size:16px;">help</span>
                    Extra in System (System has, your list doesn't)
                    <span class="dbg-count" id="dbg_extra_count">0</span>
                </div>
                <div id="dbg_extra_body" style="overflow-x:auto;"></div>
            </div>
        </div>
    </div>
</div>

<script>
var dbgSystemData = [];
var dbgCurrentMode = 'manual';
var dbgManualRowId = 0;

$(function() {
    for (var i = 0; i < 5; i++) dbgAddManualRow();
});

function dbgSwitchMode(mode) {
    dbgCurrentMode = mode;
    $('.dbg-mode-tab').removeClass('active');
    if (mode === 'manual') {
        $('.dbg-mode-tab').eq(0).addClass('active');
        $('#dbg_mode_manual').show();
        $('#dbg_mode_sms').hide();
    } else {
        $('.dbg-mode-tab').eq(1).addClass('active');
        $('#dbg_mode_manual').hide();
        $('#dbg_mode_sms').show();
    }
}

function dbgAddManualRow() {
    dbgManualRowId++;
    var n = dbgManualRowId;
    var html = '<tr id="dbg_mrow_' + n + '">'
        + '<td class="dbg-row-num">' + n + '</td>'
        + '<td><input type="text" class="dbg-amt-input" placeholder="183" data-field="amount" /></td>'
        + '<td><input type="text" class="dbg-time-input" placeholder="14:27" data-field="time" /></td>'
        + '<td><input type="text" class="dbg-ref-input" placeholder="653432085968" data-field="ref" /></td>'
        + '<td><input type="text" class="dbg-name-input" placeholder="Nithyakumar" data-field="name" /></td>'
        + '<td><button class="dbg-btn-del-row" onclick="dbgDelManualRow(' + n + ')" title="Remove">&times;</button></td>'
        + '</tr>';
    $('#dbg_manual_tbody').append(html);
    dbgUpdateEntryCount();
    $('#dbg_mrow_' + n + ' .dbg-amt-input').focus();
}

function dbgDelManualRow(n) {
    $('#dbg_mrow_' + n).remove();
    dbgUpdateEntryCount();
}

function dbgUpdateEntryCount() {
    var filled = 0;
    $('#dbg_manual_tbody tr').each(function() {
        var amt = $(this).find('[data-field="amount"]').val();
        if (amt && parseFloat(amt) > 0) filled++;
    });
    $('#dbg_entry_count').text(filled + ' entries with amount');
}

function dbgGetManualEntries() {
    var entries = [];
    $('#dbg_manual_tbody tr').each(function() {
        var amt = $(this).find('[data-field="amount"]').val().replace(/[₹,\s]/g, '');
        var time = $(this).find('[data-field="time"]').val().trim();
        var ref = $(this).find('[data-field="ref"]').val().trim();
        var name = $(this).find('[data-field="name"]').val().trim();

        if (!amt || parseFloat(amt) <= 0) return;

        var timeStr = null;
        if (time) {
            var tp = time.match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?\s*(AM|PM|am|pm)?$/);
            if (tp) {
                var h = parseInt(tp[1]), m = tp[2], s = tp[3] || '00';
                var ap = (tp[4] || '').toUpperCase();
                if (ap === 'PM' && h < 12) h += 12;
                if (ap === 'AM' && h === 12) h = 0;
                timeStr = ('0'+h).slice(-2) + ':' + m + ':' + s;
            }
        }

        entries.push({
            raw: '₹' + amt + (time ? ' at ' + time : '') + (name ? ' — ' + name : ''),
            amount: parseFloat(amt),
            time: timeStr,
            date: null,
            ref: ref || null
        });
    });
    return entries;
}

function dbgParseSMS(rawText) {
    var lines = rawText.split(/\n/).map(function(l){ return l.trim(); }).filter(function(l){ return l.length > 10; });
    var parsed = [];
    var mergedMessages = [];
    var currentMsg = '';
    for (var i = 0; i < lines.length; i++) {
        var line = lines[i];
        if (/(?:credited|received|Rs\.?\s*[\d,]+|INR\s*[\d,]+)/i.test(line) && currentMsg) {
            mergedMessages.push(currentMsg);
            currentMsg = line;
        } else if (!currentMsg) {
            currentMsg = line;
        } else {
            currentMsg += ' ' + line;
        }
    }
    if (currentMsg) mergedMessages.push(currentMsg);

    for (var j = 0; j < mergedMessages.length; j++) {
        var msg = mergedMessages[j];
        var entry = { raw: msg, amount: null, time: null, date: null, ref: null };
        var amtMatch = msg.match(/(?:Rs\.?\s*|INR\s*)([\d,]+(?:\.\d{1,2})?)/i);
        if (amtMatch) entry.amount = parseFloat(amtMatch[1].replace(/,/g, ''));

        var datePatterns = [
            /(\d{2})[\/\-](\d{2})[\/\-](\d{4})/,
            /(\d{2})[\/\-](\d{2})[\/\-](\d{2})(?!\d)/,
            /(\d{2})[\/\-]([A-Za-z]{3})[\/\-](\d{4})/,
            /(\d{2})[\/\-]([A-Za-z]{3})[\/\-](\d{2})(?!\d)/
        ];
        for (var dp = 0; dp < datePatterns.length; dp++) {
            var dm = msg.match(datePatterns[dp]);
            if (dm) {
                var day = dm[1], month = dm[2], year = dm[3];
                if (year.length === 2) year = '20' + year;
                var months = {jan:'01',feb:'02',mar:'03',apr:'04',may:'05',jun:'06',jul:'07',aug:'08',sep:'09',oct:'10',nov:'11',dec:'12'};
                if (isNaN(month)) month = months[month.toLowerCase()] || month;
                entry.date = year + '-' + ('0'+month).slice(-2) + '-' + ('0'+day).slice(-2);
                break;
            }
        }
        var timeMatch = msg.match(/(\d{1,2}):(\d{2})(?::(\d{2}))?\s*(AM|PM)?/i);
        if (timeMatch) {
            var h = parseInt(timeMatch[1]), m = timeMatch[2], s = timeMatch[3] || '00';
            var ampm = (timeMatch[4] || '').toUpperCase();
            if (ampm === 'PM' && h < 12) h += 12;
            if (ampm === 'AM' && h === 12) h = 0;
            entry.time = ('0'+h).slice(-2) + ':' + m + ':' + s;
        }
        var refMatch = msg.match(/(?:Ref\.?\s*(?:No\.?)?\s*:?\s*|UTR\s*:?\s*|UPI[- ]?RefNo\s*:?\s*)(\d{10,20})/i);
        if (refMatch) entry.ref = refMatch[1];
        if (entry.amount !== null) parsed.push(entry);
    }
    return parsed;
}

function dbgFetchSystemData(btn) {
    var dt = $('#debug_date').val();
    if (!dt) { toastr.warning('Select a date'); return; }
    $(btn).prop('disabled', true).html('<span class="dbg-loader"></span> Fetching...');
    $.ajax({
        url: 'api/qr_debug_compare_api.php',
        type: 'POST',
        data: { action: 'fetch', date: dt },
        dataType: 'json',
        success: function(data) {
            dbgSystemData = data.transactions || [];
            $(btn).prop('disabled', false)
                .html('<span class="material-icons-round" style="font-size:16px;">check_circle</span> Loaded ' + dbgSystemData.length + ' records')
                .css('background', '#059669');
            toastr.success('Loaded ' + dbgSystemData.length + ' SUCCESS records (' + data.total_count + ' total for ' + data.date + ')');
            setTimeout(function() {
                $(btn).html('<span class="material-icons-round" style="font-size:16px;">download</span> Fetch System Records').css('background', '');
            }, 3000);
        },
        error: function(xhr) {
            $(btn).prop('disabled', false).html('<span class="material-icons-round" style="font-size:16px;">download</span> Fetch System Records');
            toastr.error('Failed to fetch: ' + xhr.statusText);
        }
    });
}

function dbgRunComparison() {
    var bankEntries;
    if (dbgCurrentMode === 'manual') {
        bankEntries = dbgGetManualEntries();
        if (bankEntries.length === 0) { toastr.warning('Enter at least one payment (amount is required)'); return; }
    } else {
        var smsText = $('#sms_input').val().trim();
        if (!smsText) { toastr.warning('Paste bank SMS messages first'); return; }
        bankEntries = dbgParseSMS(smsText);
        if (bankEntries.length === 0) { toastr.error('Could not parse any SMS messages. Check the format.'); return; }
    }

    if (dbgSystemData.length === 0) {
        toastr.warning('Fetch system records first (Step 1)');
        return;
    }

    var matched = [];
    var missing = [];
    var timeMismatch = [];
    var usedSystemIds = {};

    for (var bi = 0; bi < bankEntries.length; bi++) {
        var bank = bankEntries[bi];
        var bestMatch = null;
        var bestScore = 0;
        var timeDiffMin = null;

        for (var si = 0; si < dbgSystemData.length; si++) {
            var sys = dbgSystemData[si];
            if (usedSystemIds[sys.id]) continue;
            var sysAmt = parseFloat(sys.amount);
            var score = 0;

            if (Math.abs(sysAmt - bank.amount) < 0.50) {
                score += 10;
            } else {
                continue;
            }

            if (bank.ref && sys.rrn && bank.ref === sys.rrn) {
                score += 50;
            }

            if (bank.time && sys.paid_time) {
                var bankMin = dbgTimeToMinutes(bank.time);
                var sysMin = dbgTimeToMinutes(sys.paid_time);
                var diff = Math.abs(bankMin - sysMin);
                if (diff < 5) score += 20;
                else if (diff < 30) score += 10;
                else if (diff < 120) score += 3;
            }

            if (score > bestScore) {
                bestScore = score;
                bestMatch = sys;
                if (bank.time && sys.paid_time) {
                    timeDiffMin = Math.abs(dbgTimeToMinutes(bank.time) - dbgTimeToMinutes(sys.paid_time));
                }
            }
        }

        if (bestMatch && bestScore >= 10) {
            usedSystemIds[bestMatch.id] = true;
            var entry = { bank: bank, system: bestMatch, timeDiff: timeDiffMin };
            if (timeDiffMin !== null && timeDiffMin > 5) {
                timeMismatch.push(entry);
            }
            matched.push(entry);
        } else {
            missing.push(bank);
        }
    }

    var extra = dbgSystemData.filter(function(s) { return !usedSystemIds[s.id]; });

    var bankTotal = 0, sysTotal = 0, missingTotal = 0;
    bankEntries.forEach(function(b) { bankTotal += b.amount; });
    dbgSystemData.forEach(function(r) { sysTotal += parseFloat(r.amount); });
    missing.forEach(function(b) { missingTotal += b.amount; });

    var srcLabel = dbgCurrentMode === 'manual' ? 'GPay Entries' : 'Bank SMS';

    // Stats
    $('#dbg_stats_row').html(
        '<div class="dbg-stat-card info"><div class="dbg-stat-value">' + bankEntries.length + '</div><div class="dbg-stat-label">' + srcLabel + '</div></div>' +
        '<div class="dbg-stat-card"><div class="dbg-stat-value">' + dbgSystemData.length + '</div><div class="dbg-stat-label">System Records</div></div>' +
        '<div class="dbg-stat-card success"><div class="dbg-stat-value">' + matched.length + '</div><div class="dbg-stat-label">Matched</div></div>' +
        '<div class="dbg-stat-card danger"><div class="dbg-stat-value">' + missing.length + '</div><div class="dbg-stat-label">Missing in System</div></div>' +
        '<div class="dbg-stat-card warning"><div class="dbg-stat-value">' + extra.length + '</div><div class="dbg-stat-label">Extra in System</div></div>' +
        '<div class="dbg-stat-card ' + (timeMismatch.length > 0 ? 'info' : '') + '"><div class="dbg-stat-value">' + timeMismatch.length + '</div><div class="dbg-stat-label">Time Mismatches</div></div>' +
        '<div class="dbg-stat-card danger"><div class="dbg-stat-value">₹' + missingTotal.toFixed(2) + '</div><div class="dbg-stat-label">Missing Amount</div></div>' +
        '<div class="dbg-stat-card"><div class="dbg-stat-value">₹' + (bankTotal - sysTotal).toFixed(2) + '</div><div class="dbg-stat-label">Difference</div></div>'
    );

    // Missing
    $('#dbg_missing_count').text(missing.length);
    if (missing.length > 0) {
        var html = '<table class="dbg-cmp-table"><thead><tr><th>#</th><th>Amount</th><th>Time</th><th>UPI Txn ID</th><th>Details</th></tr></thead><tbody>';
        missing.forEach(function(b, i) {
            html += '<tr><td>' + (i+1) + '</td><td class="dbg-amount credit">₹' + b.amount.toFixed(2) + '</td><td class="dbg-time-cell">' + (b.time || '—') + '</td><td style="font-family:monospace;font-size:0.75rem;">' + (b.ref || '—') + '</td><td style="font-size:0.72rem;color:#64748b;max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="' + dbgEsc(b.raw) + '">' + dbgEsc(b.raw) + '</td></tr>';
        });
        html += '</tbody></table>';
        $('#dbg_missing_body').html(html);
    } else {
        $('#dbg_missing_body').html('<div style="padding:16px;text-align:center;color:#059669;font-size:0.85rem;">No missing payments found!</div>');
    }

    // Time mismatches
    $('#dbg_time_count').text(timeMismatch.length);
    if (timeMismatch.length > 0) {
        var html = '<table class="dbg-cmp-table"><thead><tr><th>#</th><th>Amount</th><th>Your Time</th><th>System Time</th><th>Diff (min)</th><th>JC / Ext ID</th></tr></thead><tbody>';
        timeMismatch.forEach(function(e, i) {
            html += '<tr><td>' + (i+1) + '</td><td class="dbg-amount credit">₹' + e.bank.amount.toFixed(2) + '</td><td class="dbg-time-cell">' + (e.bank.time || '—') + '</td><td class="dbg-time-cell">' + (e.system.paid_time || '—') + '</td><td><span class="dbg-status-badge dbg-badge-time-diff">' + e.timeDiff + ' min</span></td><td>' + (e.system.jobcard_no || '—') + ' / ' + (e.system.ext_transaction_id||'').substring(0,20) + '</td></tr>';
        });
        html += '</tbody></table>';
        $('#dbg_time_body').html(html);
    } else {
        $('#dbg_time_body').html('<div style="padding:16px;text-align:center;color:#2563eb;font-size:0.85rem;">All times match within 5 minutes</div>');
    }

    // Matched
    $('#dbg_matched_count').text(matched.length);
    if (matched.length > 0) {
        var html = '<table class="dbg-cmp-table"><thead><tr><th>#</th><th>Amount</th><th>Your Time</th><th>System Time</th><th>Your Ref</th><th>System RRN</th><th>JC No</th><th>Customer</th><th>Status</th></tr></thead><tbody>';
        matched.forEach(function(e, i) {
            var refOk = (e.bank.ref && e.system.rrn && e.bank.ref === e.system.rrn);
            html += '<tr><td>' + (i+1) + '</td><td class="dbg-amount credit">₹' + e.bank.amount.toFixed(2) + '</td><td class="dbg-time-cell">' + (e.bank.time || '—') + '</td><td class="dbg-time-cell">' + (e.system.paid_time || '—') + '</td><td style="font-family:monospace;font-size:0.75rem;">' + (e.bank.ref || '—') + '</td><td>' + (e.system.rrn || '—') + (refOk ? ' <span class="dbg-status-badge dbg-badge-match">Match</span>' : '') + '</td><td>' + (e.system.jobcard_no || '—') + '</td><td>' + dbgEsc(e.system.customer_name || '—') + '</td><td><span class="dbg-status-badge dbg-badge-match">' + e.system.status + '</span></td></tr>';
        });
        html += '</tbody></table>';
        $('#dbg_matched_body').html(html);
    } else {
        $('#dbg_matched_body').html('<div style="padding:16px;text-align:center;color:#64748b;font-size:0.85rem;">No matches found</div>');
    }

    // Extra in system
    $('#dbg_extra_count').text(extra.length);
    if (extra.length > 0) {
        var html = '<table class="dbg-cmp-table"><thead><tr><th>#</th><th>Amount</th><th>Paid At</th><th>JC No</th><th>Customer</th><th>RRN</th><th>Status</th><th>Ext ID</th></tr></thead><tbody>';
        extra.forEach(function(s, i) {
            html += '<tr><td>' + (i+1) + '</td><td class="dbg-amount credit">₹' + parseFloat(s.amount).toFixed(2) + '</td><td class="dbg-time-cell">' + (s.paid_at || s.created_at || '—') + '</td><td>' + (s.jobcard_no || '—') + '</td><td>' + dbgEsc(s.customer_name || '—') + '</td><td>' + (s.rrn || '—') + '</td><td><span class="dbg-status-badge dbg-badge-extra">' + s.status + '</span></td><td style="font-size:0.72rem;">' + (s.ext_transaction_id||'').substring(0,25) + '</td></tr>';
        });
        html += '</tbody></table>';
        $('#dbg_extra_body').html(html);
    } else {
        $('#dbg_extra_body').html('<div style="padding:16px;text-align:center;color:#d97706;font-size:0.85rem;">No extra records</div>');
    }

    $('#dbg_results_area').removeClass('dbg-hidden');
    $('html, body').animate({ scrollTop: $('#dbg_results_area').offset().top - 80 }, 400);
    toastr.info('Comparison complete: ' + matched.length + ' matched, ' + missing.length + ' missing, ' + timeMismatch.length + ' time mismatches');
}

function dbgTimeToMinutes(timeStr) {
    if (!timeStr) return 0;
    var parts = timeStr.split(':');
    return parseInt(parts[0]) * 60 + parseInt(parts[1]);
}

function dbgEsc(s) {
    return $('<div>').text(s).html();
}
</script>
