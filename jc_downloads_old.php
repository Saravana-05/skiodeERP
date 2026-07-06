<?php
/**
 * jc_downloads.php
 * Dedicated page for searching and downloading JC design files.
 * Place this in your app root (same level as dashboard.php).
 */
session_start();
include_once "connect_db.php";

if (empty($_SESSION['user_name'])) {
    header('Location: login.php');
    exit;
}

$user_type    = $_SESSION['user_type']         ?? 'OPERATOR';
$user_name    = $_SESSION['user_name']          ?? '';
$display_name = $_SESSION['user_display_name']  ?? $user_name;
$is_admin     = in_array($user_type, ['ADMIN', 'SUPERADMIN']);

// Fetch all users for admin filter dropdown
$all_users = [];
if ($is_admin) {
    $u_res = $connection->query("SELECT user_name, user_display_name FROM user_master ORDER BY user_display_name");
    if ($u_res) {
        while ($u_row = $u_res->fetch_assoc()) {
            $all_users[] = $u_row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>JC Design File Downloads</title>

<!-- Bootstrap 5 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<!-- jQuery UI datepicker CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"/>
<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>

<style>
/* ── Reset & Base ─────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --bg:          #0f1117;
    --bg2:         #181c27;
    --bg3:         #1e2335;
    --border:      #2a3050;
    --accent:      #f5a623;
    --accent2:     #3ecf8e;
    --accent3:     #5b8dee;
    --danger:      #e05c5c;
    --text:        #e8ecf4;
    --text-muted:  #7b8299;
    --card-shadow: 0 4px 24px rgba(0,0,0,0.4);
    --radius:      10px;
    --font:        'DM Sans', sans-serif;
    --mono:        'DM Mono', monospace;
}

body {
    font-family: var(--font);
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    font-size: 14px;
}

/* ── Header ──────────────────────────────────────────────── */
.page-header {
    background: var(--bg2);
    border-bottom: 1px solid var(--border);
    padding: 14px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 100;
}
.page-header .brand {
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 700;
    font-size: 1.1rem;
    letter-spacing: -0.3px;
}
.page-header .brand .icon-wrap {
    width: 36px; height: 36px;
    background: linear-gradient(135deg, var(--accent), #e08a00);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    color: #000;
    box-shadow: 0 2px 10px rgba(245,166,35,0.35);
}
.back-btn {
    background: var(--bg3);
    border: 1px solid var(--border);
    color: var(--text-muted);
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 0.82rem;
    text-decoration: none;
    transition: all 0.2s;
    font-family: var(--font);
}
.back-btn:hover { color: var(--text); border-color: var(--accent); }

/* ── Quota Bar ───────────────────────────────────────────── */
.quota-bar-wrap {
    background: var(--bg2);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px 20px;
    margin-bottom: 20px;
}
.quota-label {
    font-size: 0.78rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 8px;
}
.quota-numbers {
    font-family: var(--mono);
    font-size: 1.5rem;
    font-weight: 500;
}
.quota-numbers .used   { color: var(--accent); }
.quota-numbers .sep    { color: var(--border); margin: 0 6px; }
.quota-numbers .limit  { color: var(--text-muted); font-size: 1rem; }
.quota-progress {
    height: 6px;
    border-radius: 4px;
    background: var(--bg3);
    margin-top: 10px;
    overflow: hidden;
}
.quota-progress-fill {
    height: 100%;
    border-radius: 4px;
    background: linear-gradient(90deg, var(--accent2), var(--accent));
    transition: width 0.5s ease;
}
.quota-progress-fill.warning { background: linear-gradient(90deg, var(--accent), #e08a00); }
.quota-progress-fill.danger  { background: linear-gradient(90deg, var(--danger), #c03030); }
.quota-reset-note {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-top: 6px;
}

/* ── Filter Panel ────────────────────────────────────────── */
.filter-panel {
    background: var(--bg2);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    margin-bottom: 20px;
}
.filter-panel .filter-title {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.filter-panel .filter-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
}
.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: flex-end;
}
.filter-group { display: flex; flex-direction: column; gap: 5px; }
.filter-group label {
    font-size: 0.72rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.filter-group input,
.filter-group select {
    background: var(--bg3);
    border: 1px solid var(--border);
    color: var(--text);
    border-radius: 7px;
    padding: 7px 11px;
    font-size: 0.83rem;
    font-family: var(--font);
    outline: none;
    transition: border-color 0.2s;
}
.filter-group input:focus,
.filter-group select:focus { border-color: var(--accent); }
.filter-group input::placeholder { color: var(--text-muted); }
.filter-group select option { background: var(--bg3); }

.search-wrap { flex: 1; min-width: 220px; }
.search-wrap input { width: 100%; padding-left: 36px; }
.search-icon-wrap { position: relative; }
.search-icon-wrap .fa-magnifying-glass {
    position: absolute;
    left: 11px; top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.8rem;
    pointer-events: none;
}

.btn-search {
    background: linear-gradient(135deg, var(--accent), #e08a00);
    color: #000;
    border: none;
    border-radius: 7px;
    padding: 8px 20px;
    font-weight: 600;
    font-size: 0.83rem;
    cursor: pointer;
    font-family: var(--font);
    transition: opacity 0.2s, transform 0.15s;
    display: flex; align-items: center; gap: 7px;
}
.btn-search:hover { opacity: 0.9; transform: translateY(-1px); }

.btn-clear {
    background: var(--bg3);
    color: var(--text-muted);
    border: 1px solid var(--border);
    border-radius: 7px;
    padding: 8px 14px;
    font-size: 0.83rem;
    cursor: pointer;
    font-family: var(--font);
    transition: all 0.2s;
}
.btn-clear:hover { color: var(--text); border-color: var(--text-muted); }

/* ── Tabs ────────────────────────────────────────────────── */
.tab-bar {
    display: flex;
    gap: 4px;
    border-bottom: 1px solid var(--border);
    margin-bottom: 20px;
}
.tab-btn {
    background: none;
    border: none;
    color: var(--text-muted);
    padding: 10px 18px;
    font-family: var(--font);
    font-size: 0.85rem;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
    display: flex; align-items: center; gap: 7px;
}
.tab-btn:hover { color: var(--text); }
.tab-btn.active {
    color: var(--accent);
    border-bottom-color: var(--accent);
    font-weight: 600;
}
.tab-badge {
    background: var(--bg3);
    border-radius: 20px;
    padding: 1px 7px;
    font-size: 0.7rem;
    font-family: var(--mono);
    color: var(--text-muted);
}
.tab-btn.active .tab-badge {
    background: rgba(245,166,35,0.15);
    color: var(--accent);
}

/* ── Results Table ───────────────────────────────────────── */
.results-wrap {
    background: var(--bg2);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
}
.results-header {
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.results-header .result-count {
    font-size: 0.8rem;
    color: var(--text-muted);
    font-family: var(--mono);
}
.results-header .result-count span { color: var(--accent); font-weight: 600; }

table.dl-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
}
table.dl-table thead th {
    background: var(--bg3);
    padding: 10px 14px;
    text-align: left;
    font-weight: 600;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-muted);
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}
table.dl-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.15s;
}
table.dl-table tbody tr:last-child { border-bottom: none; }
table.dl-table tbody tr:hover { background: rgba(255,255,255,0.03); }
table.dl-table td {
    padding: 11px 14px;
    vertical-align: middle;
}

.jc-badge {
    font-family: var(--mono);
    font-size: 0.78rem;
    font-weight: 500;
    background: rgba(91,141,238,0.12);
    color: var(--accent3);
    border-radius: 5px;
    padding: 2px 7px;
}
.customer-name { font-weight: 500; color: var(--text); }
.customer-mobile { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }

.file-type-badge {
    font-family: var(--mono);
    font-size: 0.7rem;
    font-weight: 500;
    border-radius: 4px;
    padding: 2px 6px;
    text-transform: uppercase;
}
.ft-pdf  { background: rgba(224,92,92,0.15);  color: #e05c5c; }
.ft-png  { background: rgba(62,207,142,0.15); color: var(--accent2); }
.ft-jpg,
.ft-jpeg { background: rgba(245,166,35,0.15); color: var(--accent); }
.ft-psd  { background: rgba(91,141,238,0.15); color: var(--accent3); }

.no-file-badge {
    font-size: 0.72rem;
    color: var(--text-muted);
    background: var(--bg3);
    border-radius: 4px;
    padding: 2px 7px;
    display: inline-flex; align-items: center; gap: 5px;
}

.size-txt {
    font-family: var(--mono);
    font-size: 0.75rem;
    color: var(--text-muted);
}

.dl-count-chip {
    font-family: var(--mono);
    font-size: 0.72rem;
    color: var(--text-muted);
    background: var(--bg3);
    border-radius: 20px;
    padding: 2px 8px;
}

/* ── Download Button ─────────────────────────────────────── */
.btn-dl {
    background: linear-gradient(135deg, var(--accent2), #2aae74);
    color: #000;
    border: none;
    border-radius: 7px;
    padding: 6px 14px;
    font-weight: 600;
    font-size: 0.78rem;
    cursor: pointer;
    font-family: var(--font);
    transition: all 0.2s;
    display: inline-flex; align-items: center; gap: 6px;
    text-decoration: none;
    white-space: nowrap;
}
.btn-dl:hover { opacity: 0.85; transform: translateY(-1px); color: #000; }
.btn-dl:active { transform: translateY(0); }
.btn-dl.disabled {
    background: var(--bg3);
    color: var(--text-muted);
    cursor: not-allowed;
    transform: none;
    opacity: 0.7;
}

.reason-text {
    font-size: 0.75rem;
    color: var(--text-muted);
    font-style: italic;
    max-width: 180px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* ── Empty & Loading States ──────────────────────────────── */
.state-box {
    padding: 60px 20px;
    text-align: center;
    color: var(--text-muted);
}
.state-box .state-icon {
    font-size: 2.5rem;
    margin-bottom: 14px;
    opacity: 0.4;
}
.state-box p { font-size: 0.88rem; max-width: 280px; margin: 0 auto; }

.spinner-ring {
    display: inline-block;
    width: 28px; height: 28px;
    border: 3px solid var(--border);
    border-top-color: var(--accent);
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Log Table (History Tab) ─────────────────────────────── */
.log-user-chip {
    font-family: var(--mono);
    font-size: 0.72rem;
    background: rgba(91,141,238,0.1);
    color: var(--accent3);
    border-radius: 4px;
    padding: 2px 7px;
}
.log-time {
    font-family: var(--mono);
    font-size: 0.75rem;
    color: var(--text-muted);
}

/* ── Limit Warning Toast ─────────────────────────────────── */
.quota-toast {
    position: fixed;
    bottom: 24px; right: 24px;
    background: var(--danger);
    color: #fff;
    border-radius: 10px;
    padding: 14px 20px;
    font-size: 0.85rem;
    font-weight: 500;
    box-shadow: 0 8px 30px rgba(224,92,92,0.4);
    z-index: 9999;
    display: none;
    max-width: 320px;
    animation: slideUp 0.3s ease;
}
@keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

/* ── jQuery UI datepicker dark override ──────────────────── */
.ui-datepicker {
    background: var(--bg2) !important;
    border: 1px solid var(--border) !important;
    border-radius: var(--radius) !important;
    color: var(--text) !important;
    font-family: var(--font) !important;
}
.ui-datepicker-header { background: var(--bg3) !important; border-color: var(--border) !important; }
.ui-datepicker td a { color: var(--text) !important; }
.ui-datepicker td a:hover, .ui-datepicker td a.ui-state-highlight { background: var(--accent) !important; color: #000 !important; border-color: transparent !important; }
.ui-datepicker-prev, .ui-datepicker-next { color: var(--text-muted) !important; }

/* ── Scrollbar ───────────────────────────────────────────── */
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: var(--bg); }
::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 768px) {
    .filter-row { flex-direction: column; }
    .filter-group { width: 100%; }
    .filter-group input, .filter-group select { width: 100%; }
    table.dl-table { font-size: 0.75rem; }
    table.dl-table td, table.dl-table th { padding: 8px 8px; }
}
</style>
</head>
<body>

<!-- ── Page Header ─────────────────────────────────────────────────── -->
<div class="page-header">
    <div class="brand">
        <div class="icon-wrap"><i class="fas fa-folder-open"></i></div>
        <span>JC Design Files</span>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span style="font-size:0.78rem;color:var(--text-muted);">
            <i class="fas fa-user me-1"></i><?= htmlspecialchars($display_name) ?>
        </span>
        <a href="dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left me-1"></i> Dashboard
        </a>
    </div>
</div>

<!-- ── Main Content ────────────────────────────────────────────────── -->
<div style="max-width:1280px;margin:0 auto;padding:24px 20px;">

    <!-- Quota Bar -->
    <div class="quota-bar-wrap" id="quotaBarWrap">
        <div class="quota-label"><i class="fas fa-download me-1"></i> Your Downloads Today</div>
        <div class="d-flex align-items-center gap-16" style="gap:16px;">
            <div class="quota-numbers">
                <span class="used" id="quotaUsed">—</span>
                <span class="sep">/</span>
                <span class="limit">15</span>
            </div>
            <div style="flex:1;">
                <div class="quota-progress">
                    <div class="quota-progress-fill" id="quotaFill" style="width:0%"></div>
                </div>
                <div class="quota-reset-note" id="quotaNote">Resets at midnight &nbsp;•&nbsp; <span id="quotaRemaining">—</span> downloads remaining today</div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tab-bar">
        <button class="tab-btn active" onclick="switchTab('files', this)">
            <i class="fas fa-file-arrow-down"></i> Design Files
            <span class="tab-badge" id="filesTabBadge">0</span>
        </button>
        <button class="tab-btn" onclick="switchTab('history', this)">
            <i class="fas fa-clock-rotate-left"></i> Download History
            <span class="tab-badge" id="histTabBadge">0</span>
        </button>
    </div>

    <!-- ── TAB: Design Files ─────────────────────────────────────── -->
    <div id="tab_files">

        <!-- Filter Panel -->
        <div class="filter-panel">
            <div class="filter-title"><i class="fas fa-sliders"></i> Search & Filter</div>
            <div class="filter-row">

                <!-- Free text search -->
                <div class="filter-group search-wrap">
                    <label>Search (JC No / Customer / Mobile)</label>
                    <div class="search-icon-wrap">
                        <i class="fas fa-magnifying-glass"></i>
                        <input type="text" id="searchTxt" placeholder="e.g. 1045 or Ravi or 9876…"
                               onkeydown="if(event.key==='Enter') loadFiles()"/>
                    </div>
                </div>

                <!-- From date -->
                <div class="filter-group">
                    <label>From Date</label>
                    <input type="text" id="fromDate" class="datepicker" placeholder="dd-mm-yyyy" style="width:130px;"/>
                </div>

                <!-- To date -->
                <div class="filter-group">
                    <label>To Date</label>
                    <input type="text" id="toDate" class="datepicker" placeholder="dd-mm-yyyy" style="width:130px;"/>
                </div>

                <!-- File type -->
                <div class="filter-group">
                    <label>File Type</label>
                    <select id="fileTypeFilter" style="width:100px;">
                        <option value="all">All Types</option>
                        <option value="pdf">PDF</option>
                        <option value="psd">PSD</option>
                        <option value="jpeg">JPEG</option>
                        <option value="jpg">JPG</option>
                        <option value="png">PNG</option>
                    </select>
                </div>

                <!-- Has file -->
                <div class="filter-group">
                    <label>Has File</label>
                    <select id="hasFileFilter" style="width:110px;">
                        <option value="all">All</option>
                        <option value="yes">File Uploaded</option>
                        <option value="no">Reason Only</option>
                    </select>
                </div>

                <?php if ($is_admin): ?>
                <!-- User filter (admin/superadmin only) -->
                <div class="filter-group">
                    <label>Uploaded By</label>
                    <select id="userFilter" style="width:140px;">
                        <option value="all">All Users</option>
                        <?php foreach ($all_users as $u): ?>
                        <option value="<?= htmlspecialchars($u['user_name']) ?>">
                            <?= htmlspecialchars($u['user_display_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <!-- Buttons -->
                <div class="filter-group" style="justify-content:flex-end;">
                    <label>&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button class="btn-search" onclick="loadFiles()">
                            <i class="fas fa-magnifying-glass"></i> Search
                        </button>
                        <button class="btn-clear" onclick="clearFilters()">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Results -->
        <div class="results-wrap">
            <div class="results-header">
                <div class="result-count">Showing <span id="resultCount">0</span> records</div>
                <div style="font-size:0.75rem;color:var(--text-muted);">
                    <i class="fas fa-info-circle me-1"></i>
                    Files download as: <code style="color:var(--accent);font-size:0.72rem;">CustomerName_JC{no}_{date}.ext</code>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="dl-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>JC No</th>
                            <th>Customer</th>
                            <th>File</th>
                            <th>Size</th>
                            <th>Uploaded By</th>
                            <th>Uploaded At</th>
                            <th>Downloads</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="filesTableBody">
                        <tr>
                            <td colspan="9">
                                <div class="state-box">
                                    <div class="state-icon"><i class="fas fa-magnifying-glass"></i></div>
                                    <p>Use the filters above and click Search to find design files.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /tab_files -->

    <!-- ── TAB: Download History ─────────────────────────────────── -->
    <div id="tab_history" style="display:none;">

        <!-- History Filters -->
        <div class="filter-panel">
            <div class="filter-title"><i class="fas fa-clock-rotate-left"></i> History Filter</div>
            <div class="filter-row">
                <div class="filter-group">
                    <label>From Date</label>
                    <input type="text" id="histFromDate" class="datepicker" placeholder="dd-mm-yyyy" style="width:130px;"/>
                </div>
                <div class="filter-group">
                    <label>To Date</label>
                    <input type="text" id="histToDate" class="datepicker" placeholder="dd-mm-yyyy" style="width:130px;"/>
                </div>
                <?php if ($is_admin): ?>
                <div class="filter-group">
                    <label>User</label>
                    <select id="histUserFilter" style="width:140px;">
                        <option value="all">All Users</option>
                        <?php foreach ($all_users as $u): ?>
                        <option value="<?= htmlspecialchars($u['user_name']) ?>">
                            <?= htmlspecialchars($u['user_display_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="filter-group" style="justify-content:flex-end;">
                    <label>&nbsp;</label>
                    <button class="btn-search" onclick="loadHistory()">
                        <i class="fas fa-magnifying-glass"></i> Search
                    </button>
                </div>
            </div>
        </div>

        <div class="results-wrap">
            <div class="results-header">
                <div class="result-count">Showing <span id="histCount">0</span> log entries</div>
            </div>
            <div style="overflow-x:auto;">
                <table class="dl-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>JC No</th>
                            <th>File Downloaded As</th>
                            <th>Customer</th>
                            <th>File Type</th>
                            <th>Downloaded By</th>
                            <th>Date &amp; Time</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody id="histTableBody">
                        <tr>
                            <td colspan="8">
                                <div class="state-box">
                                    <div class="state-icon"><i class="fas fa-clock-rotate-left"></i></div>
                                    <p>Select a date range and click Search to view download history.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /tab_history -->

</div><!-- /main content -->

<!-- Quota Toast (shown when limit hit) -->
<div class="quota-toast" id="quotaToast">
    <i class="fas fa-ban me-2"></i>
    <strong>Daily limit reached!</strong><br>
    <span style="font-size:0.78rem;opacity:0.9;">You have used all 15 downloads for today. Limit resets at midnight.</span>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* ────────────────────────────────────────────────────────────
   State
   ──────────────────────────────────────────────────────────── */
var quotaRemaining = 15;
var quotaUsed      = 0;
var currentTab     = 'files';

/* ────────────────────────────────────────────────────────────
   Init
   ──────────────────────────────────────────────────────────── */
$(function() {
    // Dark-friendly datepicker
    $('.datepicker').datepicker({ dateFormat: 'dd-mm-yy' });

    // Default today in both date filters
    var today = $.datepicker.formatDate('dd-mm-yy', new Date());
    $('#fromDate, #toDate, #histFromDate, #histToDate').val(today);

    // Load files on page open
    loadFiles();
});

/* ────────────────────────────────────────────────────────────
   Tab switching
   ──────────────────────────────────────────────────────────── */
function switchTab(tab, btn) {
    currentTab = tab;
    $('.tab-btn').removeClass('active');
    $(btn).addClass('active');
    $('#tab_files').toggle(tab === 'files');
    $('#tab_history').toggle(tab === 'history');
    if (tab === 'history') loadHistory();
}

/* ────────────────────────────────────────────────────────────
   Update Quota Bar
   ──────────────────────────────────────────────────────────── */
function updateQuotaBar(used, remaining, limit) {
    quotaUsed      = used;
    quotaRemaining = remaining;

    $('#quotaUsed').text(used);
    $('#quotaRemaining').text(remaining);

    var pct  = Math.round((used / limit) * 100);
    var fill = $('#quotaFill');
    fill.css('width', pct + '%');
    fill.removeClass('warning danger');
    if (pct >= 100) fill.addClass('danger');
    else if (pct >= 70) fill.addClass('warning');

    // Re-render download buttons based on new quota
    updateAllDlButtons();
}

function updateAllDlButtons() {
    $('.btn-dl[data-id]').each(function() {
        var hasFile = $(this).data('hasfile') == 1;
        if (!hasFile) return;
        if (quotaRemaining <= 0) {
            $(this).addClass('disabled').attr('title', 'Daily download limit reached (15/day)');
        } else {
            $(this).removeClass('disabled').attr('title', 'Download file');
        }
    });
}

/* ────────────────────────────────────────────────────────────
   Load Files Tab
   ──────────────────────────────────────────────────────────── */
function loadFiles() {
    var tbody = $('#filesTableBody');
    tbody.html('<tr><td colspan="9"><div class="state-box"><div class="spinner-ring"></div><p style="margin-top:14px;">Searching…</p></div></td></tr>');

    var postData = {
        search_txt:       $('#searchTxt').val(),
        from_date:        $('#fromDate').val(),
        to_date:          $('#toDate').val(),
        file_type_filter: $('#fileTypeFilter').val(),
        has_file_filter:  $('#hasFileFilter').val(),
        user_filter:      $('#userFilter').length ? $('#userFilter').val() : 'all'
    };

    $.ajax({
        type: 'POST',
        url:  'api/get_jc_downloads_list.php',
        data: postData,
        success: function(resp) {
            try {
                var r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                updateQuotaBar(r.used_today, r.remaining_downloads, r.limit);
                $('#filesTabBadge').text(r.total_records);
                $('#resultCount').text(r.total_records);
                renderFilesTable(r.records);
            } catch(e) {
                tbody.html('<tr><td colspan="9"><div class="state-box"><div class="state-icon">⚠️</div><p>Failed to parse server response.</p></div></td></tr>');
            }
        },
        error: function() {
            tbody.html('<tr><td colspan="9"><div class="state-box"><div class="state-icon">⚠️</div><p>Server error. Please try again.</p></div></td></tr>');
        }
    });
}

function renderFilesTable(records) {
    var tbody = $('#filesTableBody');
    if (!records || records.length === 0) {
        tbody.html('<tr><td colspan="9"><div class="state-box"><div class="state-icon"><i class="fas fa-folder-open"></i></div><p>No design file records found matching your search.</p></div></td></tr>');
        return;
    }

    var html = '';
    records.forEach(function(r, i) {
        var hasFile   = r.file_name && r.file_name.length > 0;
        var ftClass   = 'ft-' + (r.file_type || 'pdf');
        var sizeStr   = parseFloat(r.file_size_kb) > 1024
                        ? (r.file_size_kb / 1024).toFixed(2) + ' MB'
                        : r.file_size_kb + ' KB';
        var dlCount   = parseInt(r.download_count) || 0;
        var dlDisabled = (!hasFile || quotaRemaining <= 0) ? 'disabled' : '';
        var dlTitle   = !hasFile ? 'No file — reason only'
                      : (quotaRemaining <= 0 ? 'Daily download limit reached' : 'Download file');

        // Format upload date nicely
        var uploadedAt = r.uploaded_at ? r.uploaded_at.substring(0,10) : '—';
        var uploadedTm = r.uploaded_at ? r.uploaded_at.substring(11,19) : '';

        html += '<tr>';
        html += '<td style="color:var(--text-muted);font-family:var(--mono);font-size:0.72rem;">' + (i+1) + '</td>';
        html += '<td><span class="jc-badge">JC ' + r.jobcard_no + '</span></td>';
        html += '<td>'
             + '<div class="customer-name">' + escHtml(r.customer_name || '—') + '</div>'
             + '<div class="customer-mobile"><i class="fas fa-phone" style="font-size:0.65rem;"></i> ' + escHtml(r.customer_mobile || '—') + '</div>'
             + '</td>';

        // File cell
        if (hasFile) {
            html += '<td>'
                 + '<span class="file-type-badge ' + ftClass + '">' + (r.file_type || '').toUpperCase() + '</span>'
                 + '<div style="font-size:0.72rem;color:var(--text-muted);margin-top:3px;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="' + escHtml(r.file_name) + '">'
                 + escHtml(r.file_name)
                 + '</div>'
                 + '</td>';
            html += '<td><span class="size-txt">' + sizeStr + '</span></td>';
        } else {
            html += '<td>'
                 + '<span class="no-file-badge"><i class="fas fa-comment-dots"></i> Reason recorded</span>'
                 + (r.upload_reason ? '<div class="reason-text mt-1" title="' + escHtml(r.upload_reason) + '">' + escHtml(r.upload_reason) + '</div>' : '')
                 + '</td>';
            html += '<td><span class="size-txt">—</span></td>';
        }

        html += '<td>'
             + '<span class="log-user-chip">' + escHtml(r.uploaded_by) + '</span>'
             + '</td>';
        html += '<td>'
             + '<div style="font-size:0.78rem;">' + uploadedAt + '</div>'
             + '<div class="log-time">' + uploadedTm + '</div>'
             + '</td>';
        html += '<td>'
             + '<span class="dl-count-chip"><i class="fas fa-download" style="font-size:0.65rem;"></i> ' + dlCount + 'x</span>'
             + (r.last_downloaded ? '<div style="font-size:0.68rem;color:var(--text-muted);margin-top:3px;">Last: ' + r.last_downloaded.substring(0,10) + '</div>' : '')
             + '</td>';

        // Action button
        html += '<td>';
        if (hasFile) {
            html += '<button class="btn-dl ' + dlDisabled + '" '
                 + 'data-id="' + r.id + '" data-hasfile="1" '
                 + 'title="' + dlTitle + '" '
                 + 'onclick="triggerDownload(' + r.id + ', this)">'
                 + '<i class="fas fa-download"></i> Download'
                 + '</button>';
        } else {
            html += '<button class="btn-dl disabled" data-hasfile="0" title="No file attached" disabled>'
                 + '<i class="fas fa-ban"></i> No File'
                 + '</button>';
        }
        html += '</td>';
        html += '</tr>';
    });

    tbody.html(html);
}

/* ────────────────────────────────────────────────────────────
   Trigger Download
   ──────────────────────────────────────────────────────────── */
function triggerDownload(id, btnEl) {
    if (quotaRemaining <= 0) {
        showQuotaToast();
        return;
    }

    var $btn = $(btnEl);
    $btn.addClass('disabled').html('<i class="fas fa-spinner fa-spin"></i> Downloading…');

    // Trigger browser download via hidden <a>
    var link = document.createElement('a');
    link.href = 'api/download_jc_file.php?id=' + id;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Give server a moment then refresh quota counts
    setTimeout(function() {
        // Re-fetch quota by reloading file list silently
        $.ajax({
            type: 'POST',
            url:  'api/get_jc_downloads_list.php',
            data: { from_date: 'all', to_date: 'all', search_txt: '' },
            success: function(resp) {
                try {
                    var r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                    updateQuotaBar(r.used_today, r.remaining_downloads, r.limit);
                    // Re-enable btn (unless limit now hit)
                    if (r.remaining_downloads > 0) {
                        $btn.removeClass('disabled').html('<i class="fas fa-download"></i> Download');
                    } else {
                        $btn.addClass('disabled').html('<i class="fas fa-ban"></i> Limit Hit');
                        showQuotaToast();
                    }
                } catch(e) {
                    $btn.removeClass('disabled').html('<i class="fas fa-download"></i> Download');
                }
            },
            error: function() {
                $btn.removeClass('disabled').html('<i class="fas fa-download"></i> Download');
            }
        });
    }, 1800);
}

/* ────────────────────────────────────────────────────────────
   Load Download History Tab
   ──────────────────────────────────────────────────────────── */
function loadHistory() {
    var tbody = $('#histTableBody');
    tbody.html('<tr><td colspan="8"><div class="state-box"><div class="spinner-ring"></div><p style="margin-top:14px;">Loading history…</p></div></td></tr>');

    $.ajax({
        type: 'POST',
        url:  'api/get_download_log.php',
        data: {
            from_date:   $('#histFromDate').val(),
            to_date:     $('#histToDate').val(),
            user_filter: $('#histUserFilter').length ? $('#histUserFilter').val() : 'all'
        },
        success: function(resp) {
            try {
                var r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                $('#histTabBadge').text(r.total);
                $('#histCount').text(r.total);
                renderHistoryTable(r.logs);
            } catch(e) {
                tbody.html('<tr><td colspan="8"><div class="state-box"><div class="state-icon">⚠️</div><p>Failed to load history.</p></div></td></tr>');
            }
        }
    });
}

function renderHistoryTable(logs) {
    var tbody = $('#histTableBody');
    if (!logs || logs.length === 0) {
        tbody.html('<tr><td colspan="8"><div class="state-box"><div class="state-icon"><i class="fas fa-clock-rotate-left"></i></div><p>No download history found for the selected period.</p></div></td></tr>');
        return;
    }

    var html = '';
    logs.forEach(function(l, i) {
        var ftClass = 'ft-' + (l.file_type || 'pdf');
        html += '<tr>';
        html += '<td style="color:var(--text-muted);font-family:var(--mono);font-size:0.72rem;">' + (i+1) + '</td>';
        html += '<td><span class="jc-badge">JC ' + l.jobcard_no + '</span></td>';
        html += '<td style="font-family:var(--mono);font-size:0.75rem;color:var(--accent);">' + escHtml(l.file_name || '—') + '</td>';
        html += '<td><div class="customer-name">' + escHtml(l.customer_name || '—') + '</div>'
             + '<div class="customer-mobile">' + escHtml(l.customer_mobile || '') + '</div></td>';
        html += '<td>' + (l.file_type ? '<span class="file-type-badge ' + ftClass + '">' + l.file_type.toUpperCase() + '</span>' : '—') + '</td>';
        html += '<td><span class="log-user-chip">' + escHtml(l.downloaded_by) + '</span></td>';
        html += '<td><span class="log-time">' + (l.downloaded_at || '—') + '</span></td>';
        html += '<td style="font-family:var(--mono);font-size:0.72rem;color:var(--text-muted);">' + escHtml(l.ip_address || '—') + '</td>';
        html += '</tr>';
    });

    tbody.html(html);
}

/* ────────────────────────────────────────────────────────────
   Helpers
   ──────────────────────────────────────────────────────────── */
function clearFilters() {
    $('#searchTxt').val('');
    var today = $.datepicker.formatDate('dd-mm-yy', new Date());
    $('#fromDate, #toDate').val(today);
    $('#fileTypeFilter').val('all');
    $('#hasFileFilter').val('all');
    if ($('#userFilter').length) $('#userFilter').val('all');
    loadFiles();
}

function showQuotaToast() {
    var $t = $('#quotaToast');
    $t.fadeIn(300);
    setTimeout(function() { $t.fadeOut(400); }, 4000);
}

function escHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;');
}
</script>
</body>
</html>
