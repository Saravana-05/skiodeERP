<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once "connect_db.php";
include_once "page_guard.php";
?>
<style>

.dashboardContent {
    margin-top: 0 !important;
    border: none;
    border-radius: 16px;
    min-height: auto;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    padding: 1.25rem;
}
.dashboardContent h4 {
    display: none;
}
div.dt-container div.dt-length select { width: 35%; }
div.dt-container div.dt-length label { width: 55%; }

/* ── Dashboard top wrapper (menus+summary left, low stock right) */
.dash-top-row {
    display: flex;
    gap: 20px;
    align-items: flex-start;
    padding: 0.5rem 0 0;
}
.dash-left-col {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-width: 0;
    justify-content: flex-start;
}

/* ── Tab Buttons ─────────────────────────────────────────── */
.jc-tab-bar {
    background: #1e293b;
    border-radius: 12px 12px 0 0;
    padding: 10px 12px 0 !important;
    margin-top: 10px;
    gap: 6px !important;
}
.jc-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    border: none;
    border-radius: 8px 8px 0 0;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    background: #334155;
    color: #cbd5e1;
    transition: all 0.2s;
    position: relative;
    letter-spacing: 0.3px;
}
.jc-tab-btn:hover {
    background: #475569;
    color: #f1f5f9;
}
.jc-tab-btn.active, .jc-tab-btn.show {
    color: #ffffff;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.3px;
}
#jc-tab.active, #jc-tab.show {
    background: #2563eb;
    box-shadow: 0 -2px 8px rgba(37,99,235,0.3);
}
#sq-tab.active, #sq-tab.show {
    background: #16a34a;
    box-shadow: 0 -2px 8px rgba(22,163,74,0.3);
}
#si-tab.active, #si-tab.show {
    background: #ea580c;
    box-shadow: 0 -2px 8px rgba(234,88,12,0.3);
}
#jcdl-tab.active, #jcdl-tab.show {
    background: #4f46e5;
    box-shadow: 0 -2px 8px rgba(79,70,229,0.3);
}
.jc-tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f59e0b;
    color: #1e293b;
    font-size: 0.65rem;
    font-weight: 800;
    min-width: 18px;
    height: 18px;
    border-radius: 10px;
    padding: 0 5px;
    margin-left: 2px;
}

/* ── Fixed/Sticky Table Headers ──────────────────────────── */



/* First header row (column names) — sticky at top */
#jobcardDiv table thead tr:nth-child(1) th,
#sqDiv table thead tr:nth-child(1) th,
#siDiv table thead tr:nth-child(1) th {
    position: sticky !important;
    top: 0 !important;
    z-index: 10 !important;
    background: #1e293b !important;
}

/* Second row (search inputs) — NOT sticky, scrolls away */
#jobcardDiv table thead tr:nth-child(2) th {
    position: relative !important;
    top: auto !important;
    z-index: 1 !important;
    background: #334155 !important;
}

/* ── Action icon cards ─────────────────────────────────── */
.allIconDivCls {
    display: flex;
    flex-wrap: nowrap;
    gap: 14px;
    padding: 0;
    flex-shrink: 0;
}
.allIconDivCls .iconDivCls {
    cursor: pointer;
    flex: 1 1 0;
    min-width: 0;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px 10px 12px;
    margin: 0;
    text-align: center;
    transition: all 0.25s ease;
    background: #fff;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    align-self: flex-start;
}
.allIconDivCls .iconDivCls:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.10);
    border-color: #cbd5e1;
}
/* Icon circle — colored pastel circle */
.allIconDivCls .iconDivCls .icon-circle {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
}
.allIconDivCls .iconDivCls .icon-circle .action-card-icon {
    font-size: 1.4rem !important;
    line-height: 1;
    margin: 0;
    width: auto;
    height: auto;
    overflow: hidden;
    display: inline-block;
}
/* ── Three-dot action dropdown ── */
.jc-action-wrap {
    position: relative;
    display: inline-block;
}
.jc-dot-btn {
    background: #334155;
    border: none;
    border-radius: 8px;
    color: #fff;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1.2rem;
    font-weight: 700;
    letter-spacing: 1px;
    transition: background 0.15s;
    flex-shrink: 0;
}
.jc-dot-btn:hover {
    background: #4f46e5;
}
.jc-drop-menu {
    display: none;
    position: fixed;        /* ← changed from absolute to fixed */
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 8px 32px rgba(15,23,42,0.18);
    z-index: 99999;         /* ← bumped up */
    min-width: 170px;
    overflow: hidden;
}
.jc-drop-menu.open {
    display: block;
}
.jc-drop-menu a,
.jc-drop-menu button {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 9px 16px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #1e293b;
    background: none;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    text-decoration: none;
    text-align: left;
    transition: background 0.12s;
    box-sizing: border-box;
    font-family: inherit;
}
.jc-drop-menu a:last-child,
.jc-drop-menu button:last-child {
    border-bottom: none;
}
.jc-drop-menu a:hover,
.jc-drop-menu button:hover {
    background: #f1f5f9;
}
.jc-drop-menu .dm-print   { color: #4f46e5; }
.jc-drop-menu .dm-edit    { color: #d97706; }
.jc-drop-menu .dm-prog    { color: #f59e0b; }
.jc-drop-menu .dm-done    { color: #059669; }
.jc-drop-menu .dm-check   { color: #0891b2; }
.jc-drop-menu .dm-markpd  { color: #16a34a; }
.jc-drop-menu .dm-void    { color: #dc2626; }
.jc-drop-menu .dm-disabled {
    color: #94a3b8 !important;
    cursor: not-allowed !important;
    pointer-events: none;
}
/* Each icon circle gets a unique colored background */
.icon-circle.ic-indigo  { background: #eef2ff; }
.icon-circle.ic-amber   { background: #fef3c7; }
.icon-circle.ic-emerald  { background: #d1fae5; }
.icon-circle.ic-violet   { background: #ede9fe; }
.icon-circle.ic-blue     { background: #dbeafe; }
.icon-circle.ic-rose     { background: #fce7f3; }
.icon-circle.ic-teal     { background: #ccfbf1; }
/* Icon colors */
.icon-circle.ic-indigo .action-card-icon  { color: #4f46e5 !important; }
.icon-circle.ic-amber .action-card-icon   { color: #d97706 !important; }
.icon-circle.ic-emerald .action-card-icon  { color: #059669 !important; }
.icon-circle.ic-violet .action-card-icon   { color: #7c3aed !important; }
.icon-circle.ic-blue .action-card-icon     { color: #2563eb !important; }
.icon-circle.ic-rose .action-card-icon     { color: #db2777 !important; }
.icon-circle.ic-teal .action-card-icon     { color: #0d9488 !important; }

.allIconDivCls .iconDivCls .card-title-text {
    font-size: 0.8rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 2px 0;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.allIconDivCls .iconDivCls .card-sub-text {
    font-size: 0.68rem;
    font-weight: 400;
    color: #94a3b8;
    margin: 0;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ── Low Stock card (right side, spans both rows) ───── */
 .low-stock-box {
    flex: 0 0 240px;
    max-height: 280px;
    overflow-y: auto;
    background: linear-gradient(160deg, #fff1f1 0%, #ffe4e4 60%, #ffd6d6 100%);
    color: #1e293b;
    border: 1.5px solid #fca5a5;
    border-left: 4px solid #dc2626;
    border-radius: 14px;
    box-shadow: 0 4px 18px rgba(220,38,38,0.15), 0 1px 4px rgba(220,38,38,0.10);
    font-size: 0.75rem;
    padding: 0;
    margin-left: auto;
}
.low-stock-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 14px 8px;
    border-bottom: 1px solid #fca5a5;
    position: sticky;
    top: 0;
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    border-radius: 14px 14px 0 0;
    z-index: 1;
}
.low-stock-header .ls-warn-icon {
    width: 24px;
    height: 24px;
    background: rgba(255,255,255,0.25);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
    color: #ffffff;
}
.low-stock-header .ls-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
    flex: 1;
}
.low-stock-header .ls-view-all {
    font-size: 0.7rem;
    font-weight: 700;
    color: #dc2626;
    cursor: pointer;
    white-space: nowrap;
    padding: 2px 8px;
    border-radius: 6px;
    background: #ffffff;
    transition: background 0.2s;
}
.low-stock-header .ls-view-all:hover {
    background: #fef2f2;
}
.low-stock-table {
    width: 100%;
    border-collapse: collapse;
}

.low-stock-table tbody tr:hover { background: #fef2f2; }

/* Add this new rule */
.low-stock-table tbody td:first-child {
    font-weight: 600;
    color: #1e293b;
}
.low-stock-table thead th {
    padding: 6px 14px;
    font-size: 0.62rem;
    font-weight: 600;
    text-align: left;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #94a3b8;
    border: none;
    background: #fafbfc;
}
.low-stock-table thead th:last-child { text-align: right; }
.low-stock-table tbody td {
    padding: 5px 14px;
    font-size: 0.73rem;
    border: none;
    border-bottom: 1px solid #f5f5f7;
    color: #334155;
    line-height: 1.35;
}
/* Alternating row colors */
.low-stock-table tbody tr:nth-child(odd) td {
    background: #fff5f5;
}
.low-stock-table tbody tr:nth-child(even) td {
    background: #fff;
}
.low-stock-table tbody td {
    padding: 5px 14px;
    font-size: 0.73rem;
    border: none;
    border-bottom: 1px solid #fecaca;
    color: #334155;
    line-height: 1.35;
    font-weight: 500;
}
.low-stock-table tbody td:first-child {
    font-weight: 600;
    color: #1e293b;
    border-left: 3px solid transparent;
}
.low-stock-table tbody tr:nth-child(odd) td:first-child {
    border-left: 3px solid #dc2626;
}
.low-stock-table tbody tr:nth-child(even) td:first-child {
    border-left: 3px solid #f87171;
}
.low-stock-table tbody tr:last-child td { border-bottom: none; }
.low-stock-table tbody tr:hover td {
    background: #fee2e2 !important;
    cursor: pointer;
}
.low-stock-table .stock-val {
    text-align: right;
}
.stock-badge {
    display: inline-block;
    min-width: 34px;
    padding: 2px 10px;
    border-radius: 12px;
    font-size: 0.65rem;
    font-weight: 700;
    text-align: center;
    color: #fff;
    background: #dc2626;
    box-shadow: 0 1px 4px rgba(220,38,38,0.25);
}
.stock-badge.warn {
    color: #fff;
    background: #d97706;
    box-shadow: 0 1px 4px rgba(217,119,6,0.25);
}
.low-stock-box::-webkit-scrollbar { width: 3px; }
.low-stock-box::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 3px; }

/* ── Page Header ───────────────────────────────────────── */
.cp-page-header { display: none; }

/* ── Modern Filter Bar ────────────────────────────────── */
.cp-filter-bar-modern {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
	justify-content: space-between;
    gap: 12px;
    padding: 16px 18px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    margin: 14px 0 0;
}
#jc_div .cp-filter-bar-modern {
    background: #eff6ff; border-color: #bfdbfe;
}
#sq_div .cp-filter-bar-modern {
    background: #f0fdf4; border-color: #bbf7d0;
}
#si_div .cp-filter-bar-modern {
    background: #fff7ed; border-color: #fed7aa;
}
.cp-filter-field {
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
}
.cp-filter-field label {
    font-size: 0.7rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
}
.cp-filter-field input[type="text"],
.cp-filter-field select {
    padding: 7px 10px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    font-size: 1rem;
    background: #fff;
    color: #1e293b;
    min-width: 120px;
    transition: border-color 0.2s;
}
.cp-filter-field input[type="text"]:focus,
.cp-filter-field select:focus {
    outline: none;
}
#jc_div .cp-filter-field input[type="text"]:focus,
#jc_div .cp-filter-field select:focus {
    border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}
#sq_div .cp-filter-field input[type="text"]:focus,
#sq_div .cp-filter-field select:focus {
    border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.1);
}
#si_div .cp-filter-field input[type="text"]:focus,
#si_div .cp-filter-field select:focus {
    border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
}
.cp-filter-actions {
    display: flex;
    gap: 10px;
    align-items: flex-end;
    margin-left: auto;     /* ← ADD THIS */
    padding-bottom: 1px;
    flex-shrink: 0;        /* ← ADD THIS — prevents wrapping/squishing */
}
.cp-btn-search {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 7px 18px;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
#jc_div .cp-btn-search { background: #2563eb; }
#jc_div .cp-btn-search:hover { background: #1d4ed8; }
#sq_div .cp-btn-search { background: #16a34a; }
#sq_div .cp-btn-search:hover { background: #15803d; }
#si_div .cp-btn-search { background: #ea580c; }
#si_div .cp-btn-search:hover { background: #c2410c; }
.cp-btn-search:hover { background: #4338ca; }
.cp-btn-reset {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 7px 14px;
    background: #fff;
    color: #475569;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}
.cp-btn-reset:hover { background: #f1f5f9; border-color: #cbd5e1; }

/* ── Summary Cards (inside dash-left-col) ─────────────── */
.cp-summary-cards {
    display: flex;
    gap: 12px;
    margin: 0;
    flex-wrap: nowrap;
}
.cp-summary-card {
    flex: 1 1 0;
    min-width: 0;
    display: flex;
    align-items: center;
    gap: 12px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px 14px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.2s;
}
.cp-summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}
.cp-summary-icon {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.cp-summary-icon .material-icons-round { font-size: 1.25rem; }
.cp-summary-value {
    font-size: 1.35rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
}
.cp-summary-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #334155;
    white-space: nowrap;
}
.cp-summary-sub {
    font-size: 0.65rem;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Keep table header visible after overflow fix */
#jobcardDiv, #sqDiv, #siDiv {
    overflow: visible !important;
}

/* ── Table Section Wrapper ─────────────────────────────── */
.cp-table-section {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    margin-top: 14px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    overflow: visible !important;
}

/* ── Modern Bulk Actions (toolbar above table) ─────────── */
.cp-bulk-actions-modern {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 10px 16px;
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
}
.cp-bulk-left {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.cp-bulk-right {
    display: flex;
    gap: 8px;
    align-items: center;
}
.cp-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    border: 1px solid transparent;
    transition: all 0.2s;
    background: #f8fafc;
    color: #475569;
}
.cp-action-btn:hover { background: #f1f5f9; }
.cp-action-btn .material-icons-round { font-size: 14px !important; }
.cp-action-select { color: #047857; border-color: #d1fae5; }
.cp-action-select:hover { background: #ecfdf5; }
.cp-action-unselect { color: #dc2626; border-color: #fecaca; }
.cp-action-unselect:hover { background: #fef2f2; }
.cp-action-close { color: #4f46e5; border-color: #e0e7ff; }
.cp-action-close:hover { background: #eef2ff; }
.cp-action-refresh { color: #0d9488; border-color: #ccfbf1; }
.cp-action-refresh:hover { background: #f0fdfa; }

.cp-search-box {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 6px 12px;
    min-width: 200px;
}
.cp-search-box input {
    border: none;
    outline: none;
    font-size: 0.82rem;
    color: #1e293b;
    background: transparent;
    width: 100%;
}
.cp-search-box:focus-within {
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.08);
}

/* ── Legacy filter bar (SQ/SI tabs) — modernized ──────── */
.cp-filter-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 10px;
    padding: 14px 16px;
    border-radius: 12px;
    font-size: 0.82rem;
    font-weight: 500;
    color: #1e293b;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0;
}
.cp-filter-bar label {
    white-space: nowrap;
    margin: 0;
    font-size: 0.7rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.cp-filter-bar input[type="text"],
.cp-filter-bar select {
    padding: 7px 10px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    font-size: 0.82rem;
    background: #fff;
    color: #1e293b;
}
.cp-filter-bar select { min-width: 110px; }
.cp-filter-bar .btn {
    white-space: nowrap;
    font-size: 0.82rem;
    padding: 7px 18px;
    background: #4f46e5;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-weight: 600;
}
.cp-filter-bar .btn:hover { background: #4338ca; }

/* ── Bulk action toolbar (SQ/SI tabs) ─────────────────── */
.cp-bulk-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 10px 0;
    margin-top: 0;
}
.cp-bulk-actions .btn {
    font-size: 0.78rem;
    padding: 6px 14px;
    border-radius: 6px;
    font-weight: 600;
    border: 1px solid transparent;
}
.cp-bulk-actions .btn-success { background: #ecfdf5; color: #047857; border-color: #d1fae5; }
.cp-bulk-actions .btn-success:hover { background: #d1fae5; }
.cp-bulk-actions .btn-danger { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
.cp-bulk-actions .btn-danger:hover { background: #fee2e2; }
.cp-bulk-actions .btn-info { background: #eef2ff; color: #4f46e5; border-color: #e0e7ff; }
.cp-bulk-actions .btn-info:hover { background: #e0e7ff; }
.cp-bulk-actions .btn-warning { background: #f0fdfa; color: #0d9488; border-color: #ccfbf1; }
.cp-bulk-actions .btn-warning:hover { background: #ccfbf1; }

/* ── Tab Section Headings (SQ/SI) ─────────────────────── */
.tab-pane h5 {
    display: none !important;
}


#jobcardDiv table, #sqDiv table, #siDiv table {
    margin-bottom: 0;
    border-collapse: collapse;
    width: 100% !important;
     
}
#jobcardDiv table thead th,
#sqDiv table thead th,
#siDiv table thead th {
    background: #1e293b !important;
    color: #e2e8f0 !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    padding: 10px 12px !important;
    border: none !important;
    border-bottom: 2px solid #6366f1 !important;
    position: sticky;
    top: 0;
    z-index: 2;
    text-align: center !important;
}
#jobcardDiv table thead tr:nth-child(2) th {
    background: #fff !important;
    padding: 6px 12px !important;
    border-bottom: 1px solid #f1f5f9 !important;
}
#jobcardDiv table thead tr:nth-child(2) th input {
    padding: 4px 8px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.78rem;
    width: 80% !important;
    background: #f8fafc;
}

#jobcardDiv table thead th,
#sqDiv table thead th,
#siDiv table thead th {
    white-space: nowrap !important;
}

/* DataTables sort arrows — keep inline with header text */
#jobcardDiv table thead th.dt-orderable-asc,
#jobcardDiv table thead th.dt-orderable-desc,
#sqDiv table thead th.dt-orderable-asc,
#sqDiv table thead th.dt-orderable-desc,
#siDiv table thead th.dt-orderable-asc,
#siDiv table thead th.dt-orderable-desc {
    padding-right: 20px !important;
}

/* DT6 sort icon container */
#jobcardDiv table thead th .dt-column-order,
#sqDiv table thead th .dt-column-order,
#siDiv table thead th .dt-column-order {
    position: absolute !important;
    right: 6px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    display: inline-flex !important;
    flex-direction: column !important;
    gap: 0 !important;
    line-height: 1 !important;
}

/* Make th position:relative so absolute child works */
#jobcardDiv table thead th,
#sqDiv table thead th,
#siDiv table thead th {
    position: relative !important;
}
#jobcardDiv table tbody td,
#sqDiv table tbody td,
#siDiv table tbody td {
    padding: 8px 10px !important;
    font-size: 0.78rem !important;
    color: #000000 !important;
    border-bottom: 1px solid #f1f5f9 !important;
    border-left: none !important;
    border-right: none !important;
    vertical-align: middle;
    text-align: center !important;
}
#jobcardDiv table tbody td span,
#sqDiv table tbody td span,
#siDiv table tbody td span {
    font-size: 0.78rem !important;
}
#jobcardDiv table tbody td span.material-icons-round,
#sqDiv table tbody td span.material-icons-round,
#siDiv table tbody td span.material-icons-round {
    font-size: 15px !important;
}
/* Prevent DATE column from wrapping */
#jobcardDiv table tbody td:nth-child(3),
#jobcardDiv table thead th:nth-child(3) {
    white-space: nowrap !important;
    min-width: 90px !important;
}
#jobcardDiv table thead tr:nth-child(2) th {
    background: #334155 !important;   /* slightly lighter than main header */
    padding: 6px 12px !important;
    border-bottom: 1px solid #475569 !important;
    text-align: center !important;
}
#jobcardDiv table tbody tr:hover,
#sqDiv table tbody tr:hover,
#siDiv table tbody tr:hover {
    background: #f8fafc !important;
}
#jobcardDiv table tbody tr:nth-child(even),
#sqDiv table tbody tr:nth-child(even),
#siDiv table tbody tr:nth-child(even) {
    background: #fafbfd;
}
/* ── Job Card row status colors ── */
#jobcardDiv table tbody tr[data-status="completed"] {
    background: #d1fae5 !important;
}
#jobcardDiv table tbody tr[data-status="completed"] td {
    color: #000000 !important;
}
#jobcardDiv table tbody tr[data-status="in-progress"] {
    background: #fef3c7 !important;
}
#jobcardDiv table tbody tr[data-status="in-progress"] td {
    color: #000000 !important;
}
#jobcardDiv table tbody tr[data-status="void"] {
    background: #fee2e2 !important;
}
#jobcardDiv table tbody tr[data-status="void"] td {
    color: #991b1b !important;
    text-decoration: line-through;
}
#jobcardDiv table tbody tr[data-status="completed"]:hover {
    background: #a7f3d0 !important;
}
#jobcardDiv table tbody tr[data-status="in-progress"]:hover {
    background: #fde68a !important;
}
#jobcardDiv table tbody tr[data-status="void"]:hover {
    background: #fecaca !important;
}

#jobcardDiv table,
#sqDiv table,
#siDiv table {
    margin-bottom: 0;
    border-collapse: collapse;
    width: 100% !important;
}

/* ── Header row 1 (column names) ── */
#jobcardDiv table thead th,
#sqDiv table thead th,
#siDiv table thead th {
    color: #e2e8f0 !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    padding: 10px 12px !important;
    border: none !important;
    white-space: nowrap !important;
    text-align: center !important;
    position: relative !important;
}
#jobcardDiv table thead th {
    background: #2563eb !important;
    border-bottom: 2px solid #1d4ed8 !important;
}
#sqDiv table thead th {
    background: #16a34a !important;
    border-bottom: 2px solid #15803d !important;
}
#siDiv table thead th {
    background: #ea580c !important;
    border-bottom: 2px solid #c2410c !important;
}

/* ── Header row 2 (inline search inputs) ── */
#jobcardDiv table thead tr:nth-child(2) th,
#sqDiv table thead tr:nth-child(2) th,
#siDiv table thead tr:nth-child(2) th {
    padding: 6px 12px !important;
    text-align: center !important;
    position: relative !important;
    top: auto !important;
    z-index: 1 !important;
}
#jobcardDiv table thead tr:nth-child(2) th {
    background: #1d4ed8 !important;
    border-bottom: 1px solid #1e40af !important;
}
#sqDiv table thead tr:nth-child(2) th {
    background: #15803d !important;
    border-bottom: 1px solid #166534 !important;
}
#siDiv table thead tr:nth-child(2) th {
    background: #c2410c !important;
    border-bottom: 1px solid #9a3412 !important;
}
#jobcardDiv table thead tr:nth-child(2) th input,
#sqDiv table thead tr:nth-child(2) th input,
#siDiv table thead tr:nth-child(2) th input {
    padding: 4px 8px;
    border: 1px solid #475569;
    border-radius: 6px;
    font-size: 0.78rem;
    width: 80% !important;
    background: #f8fafc;
    color: #1e293b;
}

/* ── Sticky first header row ── */
#jobcardDiv table thead tr:nth-child(1) th,
#sqDiv table thead tr:nth-child(1) th,
#siDiv table thead tr:nth-child(1) th {
    position: sticky !important;
    top: 0 !important;
    z-index: 10 !important;
}

/* ── DT6 sort icon ── */
#jobcardDiv table thead th .dt-column-order,
#sqDiv table thead th .dt-column-order,
#siDiv table thead th .dt-column-order {
    position: absolute !important;
    right: 6px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    display: inline-flex !important;
    flex-direction: column !important;
    gap: 0 !important;
    line-height: 1 !important;
}
#jobcardDiv table thead th.dt-orderable-asc,
#jobcardDiv table thead th.dt-orderable-desc,
#sqDiv table thead th.dt-orderable-asc,
#sqDiv table thead th.dt-orderable-desc,
#siDiv table thead th.dt-orderable-asc,
#siDiv table thead th.dt-orderable-desc {
    padding-right: 20px !important;
}

/* ── Body cells ── */
#jobcardDiv table tbody td,
#sqDiv table tbody td,
#siDiv table tbody td {
    padding: 10px 12px !important;
    font-size: 0.84rem !important;
    font-weight: 500;
    color: #334155;
    border-bottom: 1px solid #f1f5f9 !important;
    border-left: none !important;
    border-right: none !important;
    vertical-align: middle !important;
    text-align: center !important;
}

/* ── Row hover & stripes ── */
#jobcardDiv table tbody tr:hover td,
#sqDiv table tbody tr:hover td,
#siDiv table tbody tr:hover td {
    background: #f1f5f9 !important;
}
#jobcardDiv table tbody tr:nth-child(even) td,
#sqDiv table tbody tr:nth-child(even) td,
#siDiv table tbody tr:nth-child(even) td {
    background: #fafbfd;
}

/* ── No-wrap date column (col 3) across all tabs ── */
#jobcardDiv table tbody td:nth-child(3),
#jobcardDiv table thead th:nth-child(3),
#sqDiv table tbody td:nth-child(3),
#sqDiv table thead th:nth-child(3),
#siDiv table tbody td:nth-child(3),
#siDiv table thead th:nth-child(3) {
    white-space: nowrap !important;
    min-width: 90px !important;
}

/* ── Filter bar inputs — same size all tabs ── */
.cp-filter-field input[type="text"],
.cp-filter-field select {
    padding: 7px 10px !important;
    border-radius: 8px !important;
    border: 1px solid #e2e8f0 !important;
    font-size: 0.82rem !important;
    background: #fff;
    color: #1e293b;
    min-width: 120px;
    transition: border-color 0.2s;
}
.cp-filter-field input[type="text"]:focus,
.cp-filter-field select:focus {
    outline: none;
}
#jc_div .cp-filter-field input[type="text"]:focus,
#jc_div .cp-filter-field select:focus {
    border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}
#sq_div .cp-filter-field input[type="text"]:focus,
#sq_div .cp-filter-field select:focus {
    border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.1);
}
#si_div .cp-filter-field input[type="text"]:focus,
#si_div .cp-filter-field select:focus {
    border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
}

/* ── Action buttons in table ── */
#jobcardDiv .btn-sm,
#sqDiv .btn-sm,
#siDiv .btn-sm {
    font-size: 0.72rem !important;
    padding: 4px 10px !important;
    border-radius: 6px;
    font-weight: 600;
    border: none;
    letter-spacing: 0.2px;
}


/* ── Highlighted credit customer cells ── */
td.highlight { background: #fef3c7 !important; }

/* ── Closed JC cell override ── */
td[style*="background-color:#01f9c0"] {
    background: #d1fae5 !important;
    color: #000000 !important;
    font-weight: 600;
    font-size: 0.82rem;
}

/* Action buttons in table */
#jobcardDiv .btn-sm, #sqDiv .btn-sm, #siDiv .btn-sm {
    font-size: 0.72rem !important;
    padding: 4px 10px !important;
    border-radius: 6px;
    font-weight: 600;
    border: none;
    letter-spacing: 0.2px;
}
#jobcardDiv .btn-primary { background: #4f46e5 !important; color: #fff !important; }
#jobcardDiv .btn-primary:hover { background: #4338ca !important; }
#jobcardDiv .btn-warning { background: #f59e0b !important; color: #fff !important; }
#jobcardDiv .btn-warning:hover { background: #d97706 !important; }
#jobcardDiv .btn-success { background: #059669 !important; color: #fff !important; }
#jobcardDiv .btn-info { background: #06b6d4 !important; color: #fff !important; }

/* Checkbox styling in table */
.select_jcs, .select_sqs {
    width: 15px;
    height: 15px;
    accent-color: #4f46e5;
    vertical-align: middle;
    margin-right: 6px;
    cursor: pointer;
}

/* DataTable pagination override */
.dataTables_wrapper .dataTables_paginate {
    padding: 10px 16px !important;
    text-align: right;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 6px !important;
    font-size: 0.8rem !important;
    padding: 4px 10px !important;
    margin: 0 2px;
    border: 1px solid #e2e8f0 !important;
    background: #fff !important;
    color: #475569 !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #4f46e5 !important;
    color: #fff !important;
    border-color: #4f46e5 !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #eef2ff !important;
    color: #4f46e5 !important;
    border-color: #c7d2fe !important;
}
.dataTables_wrapper .dataTables_info {
    padding: 10px 16px !important;
    font-size: 0.8rem;
    color: #64748b;
}

/* Highlight class for credit customers */
td.highlight {
    background: #fef3c7 !important;
}

/* Closed JC badge */
td[style*="background-color:#01f9c0"] {
    background: #d1fae5 !important;
    color: #000000 !important;
    font-weight: 600;
    font-size: 0.82rem;
}

/* ── Design Upload Modal ──────────────────────────────── */
#designUploadModal .upload-drop-zone {
    border: 2px dashed #6c757d;
    border-radius: 8px;
    padding: 24px 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.25s, background 0.25s;
    background: #f8f9fa;
}
#designUploadModal .upload-drop-zone.dragover {
    border-color: #0d6efd;
    background: #e8f0fe;
}
#designUploadModal .upload-drop-zone .upload-icon {
    font-size: 2rem; color: #6c757d; display: block; margin-bottom: 6px;
}
#designUploadModal .reason-section {
    margin-top: 14px; padding: 12px;
    background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px;
}
#du_progress_wrap { display: none; margin-top: 10px; }

/* Multi-file preview grid */
#du_files_preview_list {
    display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px;
}
.du-file-chip {
    display: flex; align-items: center; gap: 6px;
    background: #e9ecef; border-radius: 20px;
    padding: 4px 10px; font-size: 0.78rem; max-width: 260px;
}
.du-file-chip .chip-name {
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 160px;
}
.du-file-chip .chip-remove {
    cursor: pointer; color: #dc3545; font-weight: bold;
    background: none; border: none; padding: 0; font-size: 0.9rem; line-height: 1;
}

/* Existing files table */
.du-existing-files-table { width: 100%; font-size: 0.78rem; border-collapse: collapse; margin-top: 8px; }
.du-existing-files-table th { background: #343a40; color: #fff; padding: 6px 8px; text-align: left; }
.du-existing-files-table td { padding: 5px 8px; border-bottom: 1px solid #dee2e6; vertical-align: middle; }
.du-existing-files-table tr:last-child td { border-bottom: none; }
.du-file-thumb { width: 36px; height: 36px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6; }
.du-file-icon-box {
    width: 36px; height: 36px; border-radius: 4px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; border: 1px solid #dee2e6; background: #f8f9fa;
}

</style>

<div class="bg_aliceblue p-3 m-1 pt-0 dashboardContent">
	<div class="dash-top-row">
		<div class="dash-left-col">
			<div class="allIconDivCls">
				<div class="iconDivCls" onclick="fnModule(1)">
					<div class="icon-circle ic-indigo"><span class="material-icons-round action-card-icon">description</span></div>
					<div class="card-title-text">Job Card</div>
					<div class="card-sub-text">Create / View</div>
				</div>
			<?php
				if($_SESSION['user_type'] == "ADMIN" || $_SESSION['user_type'] == "SUPERADMIN")
				{
			?>
				<div class="iconDivCls" onclick="fnModule(14)">
					<div class="icon-circle ic-amber"><span class="material-icons-round action-card-icon">dataset</span></div>
					<div class="card-title-text">Master Data</div>
					<div class="card-sub-text">Manage Data</div>
				</div>
				<div class="iconDivCls" onclick="fnModule(4)">
					<div class="icon-circle ic-emerald"><span class="material-icons-round action-card-icon">person_add</span></div>
					<div class="card-title-text">Customer Master</div>
					<div class="card-sub-text">Manage Customers</div>
				</div>
				<div class="iconDivCls" onclick="fnModule(9)">
					<div class="icon-circle ic-violet"><span class="material-icons-round action-card-icon">manage_accounts</span></div>
					<div class="card-title-text">User Master</div>
					<div class="card-sub-text">Manage Users</div>
				</div>
			<?php
				}
			?>
				<div class="iconDivCls" onclick="fnModule(5)">
					<div class="icon-circle ic-blue"><span class="material-icons-round action-card-icon">receipt_long</span></div>
					<div class="card-title-text">Receipt Voucher</div>
					<div class="card-sub-text">Add / View</div>
				</div>
				<?php if($_SESSION['user_type'] == "ADMIN" || $_SESSION['user_type'] == "SUPERADMIN"): ?>
				<div class="iconDivCls" onclick="fnModule(6)">
					<div class="icon-circle ic-rose"><span class="material-icons-round action-card-icon">payments</span></div>
					<div class="card-title-text">Payment Voucher</div>
					<div class="card-sub-text">Add / View</div>
				</div>
				<?php endif; ?>
				<div class="iconDivCls" onclick="fnModule(10)">
					<div class="icon-circle ic-teal"><span class="material-icons-round action-card-icon">summarize</span></div>
					<div class="card-title-text">EOD Process</div>
					<div class="card-sub-text">End of Day</div>
				</div>
			</div>
			<?php if($_SESSION['user_type'] == "ADMIN" || $_SESSION['user_type'] == "SUPERADMIN"): ?>
<div class="cp-summary-cards" id="jcSummaryCards">
    <div class="cp-summary-card">
        <div class="cp-summary-icon" style="background:#eef2ff;color:#4f46e5;"><span class="material-icons-round">folder_open</span></div>
        <div class="cp-summary-info">
            <div class="cp-summary-value" id="jc_total_count">--</div>
            <div class="cp-summary-label">Total Job Cards</div>
            <div class="cp-summary-sub">This month</div>
        </div>
    </div>
    <div class="cp-summary-card">
        <div class="cp-summary-icon" style="background:#dbeafe;color:#2563eb;"><span class="material-icons-round">pending</span></div>
        <div class="cp-summary-info">
            <div class="cp-summary-value" id="jc_progress_count">--</div>
            <div class="cp-summary-label">In Progress</div>
            <div class="cp-summary-sub">This month</div>
        </div>
    </div>
    <div class="cp-summary-card">
        <div class="cp-summary-icon" style="background:#d1fae5;color:#059669;"><span class="material-icons-round">check_circle</span></div>
        <div class="cp-summary-info">
            <div class="cp-summary-value" id="jc_completed_count">--</div>
            <div class="cp-summary-label">Completed</div>
            <div class="cp-summary-sub">This month</div>
        </div>
    </div>
    <div class="cp-summary-card">
        <div class="cp-summary-icon" style="background:#fef2f2;color:#dc2626;"><span class="material-icons-round">block</span></div>
        <div class="cp-summary-info">
            <div class="cp-summary-value" id="jc_void_count">--</div>
            <div class="cp-summary-label">Void</div>
            <div class="cp-summary-sub">This month</div>
        </div>
    </div>
</div>
<?php endif; ?>
		</div>
		<?php if($_SESSION['user_type'] == "ADMIN" || $_SESSION['user_type'] == "SUPERADMIN"): ?>
		<div class="low-stock-box">
			<div class="low-stock-header">
				<div class="ls-warn-icon">&#9888;</div>
				<div class="ls-title">Low Stock Items</div>
				<div class="ls-view-all" onclick="fnSideMenu(14)">View All</div>
			</div>
			<?php include "api/get_low_stock.php";?>
		</div>
		<?php endif; ?>
	</div>
	<?php 
		if($_SESSION['user_type'] == "SUPERADMIN")
		{
	?>
	<div class="row mt-2"> 
		 <label for="date1Txt" class="col-md-1 col-form-label">Date</label>
		 <div class="col-md-2">
			<input type="text" autocomplete="off" 
			   class="form-control datepicker" 
			   id="date1Txt" name="date1Txt" 
			   placeholder="dd-MM-yyyy" value="<?php echo date("d-m-Y");?>"/>
		 </div> 
		<label for="date2Txt" class="col-md-1 col-form-label">Date</label>
		 <div class="col-md-2">
			<input type="text" autocomplete="off" 
			   class="form-control datepicker" 
			   id="date2Txt" name="date2Txt" 
			   placeholder="dd-MM-yyyy" value="<?php echo date("d-m-Y");?>"/>
		 </div> 
		 <div class="col-md-2">
			<button type="button" id="superadmin_show_btn" class="btn btn-info">Show</button>
		 </div>
		 
	</div>
	<div class="row filterCls mb-3">

	</div>
	<div class="row cardCls mt-3">
		<div class="col-md-3">
			<div class="card text-bg-primary mb-3" >
			  <div class="card-header"><h6 class="card-title"><span class="material-icons-round" style="font-size:16px;vertical-align:text-bottom;margin-right:6px;">work</span>JOB TRANSACTION</h6></div>
			  <div class="card-body">
				 <table>
					<tr>
						<th>JOB CARD</th>
						<td> : </td>
						<td><span id="jc_cnt"></span></td>
					</tr>
					<tr>
						<th>QUOTATION</th>
						<td> : </td>
						<td><span id="sq_cnt"></span></td>
					</tr>
					<tr>
						<th>INVOICE</th>
						<td> : </td>
						<td><span id="si_cnt"></span></td>
					</tr>
					<tr>
						<th>BALANCE</th>
						<td> : </td>
						<td><span id="balance_txt"></span></td>
					</tr>
				 </table>
			  </div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-bg-purple mb-3" >
			  <div class="card-header"><h6 class="card-title"><span class="material-icons-round" style="font-size:16px;vertical-align:text-bottom;margin-right:6px;">category</span>CATEGORY WISE TRANS.</h6></div>
				  <div class="card-body">
					<table>
						<tr>
							<th>PRINTING</th>
							<td>   </td>
							<td><span id="printing_cnt"></span></td>
						</tr>
						<tr>
							<th>SERVICES</th>
							<td>   </td>
							<td><span id="services_cnt"></span></td>
						</tr>
						<tr>
							<th>MATERIALS</th>
							<td>   </td>
							<td><span id="materials_cnt"></span></td>
						</tr>
						<tr>
							<th>ID CARDS</th>
							<td>   </td>
							<td><span id="id_card_cnt"></span></td>
						</tr>
						<tr>
							<th>PRODUCTS</th>
							<td>   </td>
							<td><span id="products_cnt"></span></td>
						</tr>
					 </table>  
			  </div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-bg-danger mb-3" >
			  <div class="card-header"><h6 class="card-title"><span class="material-icons-round" style="font-size:16px;vertical-align:text-bottom;margin-right:6px;">precision_manufacturing</span>MACHINE COUNT</h6></div>
			  <div class="card-body" id="machine_count_div">
				<table>
					<tr>
						<th>XEROX IRIDESSE</th>
						<td> : </td>
						<td> </td>
					</tr>
					<tr>
						<th>XEROX 3100</th>
						<td> : </td>
						<td> </td>
					</tr>
					<tr>
						<th>K M 4070</th>
						<td> : </td>
						<td> </td>
					</tr>
					<tr>
						<th>CANNON 6575</th>
						<td> : </td>
						<td> </td>
					</tr>									
				 </table>  
			  </div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-bg-warning mb-3" >
			  <div class="card-header"><h6 class="card-title"><span class="material-icons-round" style="font-size:16px;vertical-align:text-bottom;margin-right:6px;">trending_up</span>TOTAL SALES</h6></div>
			  <div class="card-body" id="total_sales_div">
				<table>
					<tr>
						<th>CASH</th>
						<td> : </td>
						<td> </td>
					</tr>
					<tr>
						<th>SAVINGS A/C</th>
						<td> : </td>
						<td> </td>
					</tr>
					<tr>
						<th>CURRENT A/C</th>
						<td> : </td>
						<td> </td>
					</tr>
					<tr>
						<th>DISCOUNT</th>
						<td> : </td>
						<td> </td>
					</tr>									
				 </table>  
			  </div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-bg-orange mb-3" >
			  <div class="card-header"><h6 class="card-title"><span class="material-icons-round" style="font-size:16px;vertical-align:text-bottom;margin-right:6px;">groups</span>CUSTOMER CATEGORY</h6></div>
			  <div class="card-body">
				<table>
					<tr>
						<th>GENERAL CUSTOMER</th>
						<td> : </td>
						<td><span id="general_cnt"></span> </td>
					</tr>
					<tr>
						<th>WALK IN CUSTOMER</th>
						<td> : </td>
						<td><span id="walkin_cnt"></span> </td>
					</tr>
					<tr>
						<th>CREDIT CUSTOMER</th>
						<td> : </td>
						<td><span id="credit_cnt"></span> </td>
					</tr>
					<tr>
						<th> </th>
						<td>  </td>
						<td> </td>
					</tr>									
				 </table>   
			  </div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-bg-success mb-3" >
			  <div class="card-header"><h6 class="card-title"><span class="material-icons-round" style="font-size:16px;vertical-align:text-bottom;margin-right:6px;">person</span>ACTIVE USERS</h6></div>
			  <div class="card-body" id="active_users_div">

			  </div>
			</div>
		</div> 
	</div> 

	<?php
		}
		else if($_SESSION['user_type'] != "SUPERADMIN")
		{
	?>
	<div class="jc-tab-bar" role="tablist" style="display:flex;gap:8px;padding:10px 0 0;flex-wrap:wrap;">
    <button class="jc-tab-btn active" id="jc-tab" data-bs-toggle="tab" data-bs-target="#jc_div" type="button" role="tab" aria-controls="jc_div" aria-selected="true">
        <span class="material-icons-round" style="font-size:15px;vertical-align:-3px;">description</span>
        <?php
            $sql="SELECT count(jobcard_no) as cnt FROM `jobcard_master` where jobcard_date='".date("Y-m-d")."' and job_card_closed=0;";
            if($qry=mysqli_query($connection,$sql)) {
                if($row=mysqli_fetch_array($qry)) {
                    $cnt=$row["cnt"];
                    if($cnt > 0) echo '<span class="jc-tab-badge">'.$cnt.'</span>';
                }
            }
        ?> Job Cards
    </button>
    <button class="jc-tab-btn" id="sq-tab" data-bs-toggle="tab" data-bs-target="#sq_div" type="button" role="tab" aria-controls="sq_div" aria-selected="false">
        <span class="material-icons-round" style="font-size:15px;vertical-align:-3px;">request_quote</span>
        Sales Quotes
    </button>
    <button class="jc-tab-btn" id="si-tab" data-bs-toggle="tab" data-bs-target="#si_div" type="button" role="tab" aria-controls="si_div" aria-selected="false">
        <span class="material-icons-round" style="font-size:15px;vertical-align:-3px;">receipt</span>
        Sales Invoices
    </button>
    <button class="jc-tab-btn" id="jcdl-tab" type="button" onclick="window.location='jc_downloads.php'">
        <span class="material-icons-round" style="font-size:15px;vertical-align:-3px;">download</span>
        JC Downloads
    </button>
</div>
	<div class="tab-content" style="width:100%;overflow:visible;">

    <!-- ── JC TAB ─────────────────────────────────────────── -->
    <div id="jc_div" class="tab-pane fade show active" role="tabpanel" aria-labelledby="jc-tab">
        <div style="position:relative;">
            <?php
            $today = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $before_yesterday = date('Y-m-d', strtotime('-2 days'));
            $week = date('Y-m-d', strtotime('-7 days'));
            ?>
            <div class="cp-filter-bar-modern">
                <?php if($_SESSION['user_type']!="OPERATOR") { ?>
                <div class="cp-filter-field">
                    <label>From Date</label>
                    <input type="text" class="datepicker" id="jc_from_dt_txt" style="max-width:100px;" value="<?php echo date("d-m-Y");?>"/>
                </div>
                <div class="cp-filter-field">
                    <label>To Date</label>
                    <input type="text" class="datepicker" id="jc_to_dt_txt" style="max-width:100px;" value="<?php echo date("d-m-Y");?>"/>
                </div>
                <?php } else { ?>
                <div class="cp-filter-field">
                    <label>Date</label>
                    <select id="jc_date_txt">
                        <option value="<?php echo $today;?>" selected>Today</option>
                        <option value="<?php echo $yesterday;?>">Yesterday</option>
                        <option value="<?php echo $before_yesterday;?>">Before Yesterday</option>
                        <option value="<?php echo $week;?>">Week</option>
                        <option value="ALL_OPEN_JC">All Open</option>
                    </select>
                </div>
                <?php } ?>
                <div class="cp-filter-field">
                    <label>Customer</label>
                    <select id="customer_code_txt" style="max-width:400px;">
                        <option value="all">All</option>
                        <option value="WalkIn">WalkIn</option>
                        <option value="General">General</option>
                        <?php
                        $sql="select customer_code,customer_name from customer_master order by customer_name;";
                        if($qry=mysqli_query($connection,$sql))
                            while($row=mysqli_fetch_array($qry))
                                echo '<option value="'.$row["customer_code"].'">'.$row["customer_name"].'</option>';
                        ?>
                    </select>
                </div>
                <div class="cp-filter-field">
                    <label>User</label>
                    <select id="user_name_txt" style="max-width:100px;">
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
                <div class="cp-filter-field">
                    <label>Pay Mode</label>
                    <select id="pay_mode_txt">
                        <option value="all">All</option>
                        <option value="cash">Cash</option>
                        <option value="online">Online</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>
                <?php if($_SESSION['user_type']!="OPERATOR") { ?>
                <div class="cp-filter-field">
                    <label>Status</label>
                    <select id="status_txt">
                        <option value="all">All</option>
                        <option value="0" selected>Open</option>
                        <option value="1">Closed</option>
                        <option value="void">Void</option>
                    </select>
                </div>
                <?php } ?>
                <div class="cp-filter-actions">
                    <button type="button" class="cp-btn-search" onclick="loadJobCardList()">
                        <span class="material-icons-round" style="font-size:16px;vertical-align:middle;margin-right:4px;">search</span> Search
                    </button>
                    <button type="button" class="cp-btn-reset" onclick="resetJCFilters()">
                        <span class="material-icons-round" style="font-size:16px;vertical-align:middle;margin-right:4px;">refresh</span> Reset
                    </button>
                </div>
            </div>

            <div class="cp-table-section">
                <div class="cp-bulk-actions-modern">
                    <div class="cp-bulk-left">
                        <button class="cp-action-btn cp-action-select" onclick="select_all_jcs()"><span class="material-icons-round" style="font-size:15px;">check_circle</span> Select All</button>
                        <button class="cp-action-btn cp-action-unselect" onclick="unselect_all_jcs()"><span class="material-icons-round" style="font-size:15px;">remove_circle_outline</span> Unselect All</button>
                        <button class="cp-action-btn cp-action-close" onclick="close_selected_jcs()"><span class="material-icons-round" style="font-size:15px;">lock</span> Close Selected</button>
                        <button class="cp-action-btn cp-action-refresh" onclick="loadJobCardList()"><span class="material-icons-round" style="font-size:15px;">sync</span> Refresh</button>
                    </div>
                    <div class="cp-bulk-right">
                        <div class="cp-search-box">
                            <span class="material-icons-round" style="font-size:18px;color:#94a3b8;">search</span>
                            <input type="text" id="jcQuickSearch" placeholder="Search job cards..." oninput="fnJCQuickSearch(this.value)"/>
                        </div>
                    </div>
                </div>
                <div id="jobcardDiv" style="font-size:0.84rem;"></div>
            </div>
        </div>
    </div><!-- /#jc_div -->

    <!-- ── SQ TAB ─────────────────────────────────────────── -->
    <div id="sq_div" class="tab-pane fade" role="tabpanel" aria-labelledby="sq-tab">
        <div style="position:relative;">
            <?php
            $today = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $before_yesterday = date('Y-m-d', strtotime('-2 days'));
            $week = date('Y-m-d', strtotime('-7 days'));
            ?>
            <div class="cp-filter-bar-modern">
                <?php if($_SESSION['user_type']!="OPERATOR") { ?>
                <div class="cp-filter-field">
                    <label>From Date</label>
                    <input type="text" class="datepicker" id="sq_from_dt_txt" style="max-width:100px;" value="<?php echo date("d-m-Y");?>"/>
                </div>
                <div class="cp-filter-field">
                    <label>To Date</label>
                    <input type="text" class="datepicker" id="sq_to_dt_txt" style="max-width:100px;" value="<?php echo date("d-m-Y");?>"/>
                </div>
                <?php } else { ?>
                <div class="cp-filter-field">
                    <label>Date</label>
                    <select id="sq_date_txt">
                        <option value="<?php echo $today;?>" selected>Today</option>
                        <option value="<?php echo $yesterday;?>">Yesterday</option>
                        <option value="<?php echo $before_yesterday;?>">Before Yesterday</option>
                        <option value="<?php echo $week;?>">Week</option>
                    </select>
                </div>
                <?php } ?>
                <div class="cp-filter-field">
                    <label>Customer</label>
                    <select id="sq_customer_code_txt" style="max-width:400px;">
                        <option value="all">All</option>
                        <option value="WalkIn">WalkIn</option>
                        <option value="General">General</option>
                        <?php
                        $sql="select customer_code,customer_name from customer_master order by customer_name;";
                        if($qry=mysqli_query($connection,$sql))
                            while($row=mysqli_fetch_array($qry))
                                echo '<option value="'.$row["customer_code"].'">'.$row["customer_name"].'</option>';
                        ?>
                    </select>
                </div>
                <div class="cp-filter-field">
                    <label>User</label>
                    <select id="sq_user_name_txt" style="max-width:140px;">
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
                <div class="cp-filter-field">
                    <label>Pay Mode</label>
                    <select id="sq_pay_mode_txt">
                        <option value="all">All</option>
                        <option value="cash">Cash</option>
                        <option value="online">Online</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>
                <?php if($_SESSION['user_type']!="OPERATOR") { ?>
                <div class="cp-filter-field">
                    <label>Status</label>
                    <select id="sq_status_txt">
                        <option value="all">All</option>
                        <option value="0" selected>Open</option>
                        <option value="1">Closed</option>
                    </select>
                </div>
                <?php } ?>
                <div class="cp-filter-actions">
                    <button type="button" class="cp-btn-search" onclick="loadSQList()">
                        <span class="material-icons-round" style="font-size:16px;vertical-align:middle;margin-right:4px;">search</span> Search
                    </button>
                    <button type="button" class="cp-btn-reset" onclick="resetSQFilters()">
                        <span class="material-icons-round" style="font-size:16px;vertical-align:middle;margin-right:4px;">refresh</span> Reset
                    </button>
                </div>
            </div>

            <div class="cp-table-section">
                <div class="cp-bulk-actions-modern">
                    <div class="cp-bulk-left">
                        <button class="cp-action-btn cp-action-select" onclick="select_all_sqs()"><span class="material-icons-round" style="font-size:15px;">check_circle</span> Select All</button>
                        <button class="cp-action-btn cp-action-unselect" onclick="unselect_all_sqs()"><span class="material-icons-round" style="font-size:15px;">remove_circle_outline</span> Unselect All</button>
                        <button class="cp-action-btn cp-action-close" onclick="sales_invoice_sqs()"><span class="material-icons-round" style="font-size:15px;">receipt</span> Sales Invoice</button>
                        <button class="cp-action-btn cp-action-refresh" onclick="loadSQList()"><span class="material-icons-round" style="font-size:15px;">sync</span> Refresh</button>
                    </div>
                    <div class="cp-bulk-right">
                        <div class="cp-search-box">
                            <span class="material-icons-round" style="font-size:18px;color:#94a3b8;">search</span>
                            <input type="text" id="sqQuickSearch" placeholder="Search quotes..." oninput="fnSQQuickSearch(this.value)"/>
                        </div>
                    </div>
                </div>
                <div id="sqDiv" style="font-size:0.84rem;"></div>
            </div>
        </div>
    </div><!-- /#sq_div -->

    <!-- ── SI TAB ─────────────────────────────────────────── -->
    <div id="si_div" class="tab-pane fade" role="tabpanel" aria-labelledby="si-tab">
        <div style="position:relative;">
            <?php
            $today = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $before_yesterday = date('Y-m-d', strtotime('-2 days'));
            $week = date('Y-m-d', strtotime('-7 days'));
            ?>
            <div class="cp-filter-bar-modern">
                <?php if($_SESSION['user_type']!="OPERATOR") { ?>
                <div class="cp-filter-field">
                    <label>From Date</label>
                    <input type="text" class="datepicker" id="si_from_dt_txt" style="max-width:100px;" value="<?php echo date("d-m-Y");?>"/>
                </div>
                <div class="cp-filter-field">
                    <label>To Date</label>
                    <input type="text" class="datepicker" id="si_to_dt_txt" style="max-width:100px;" value="<?php echo date("d-m-Y");?>"/>
                </div>
                <?php } else { ?>
                <div class="cp-filter-field">
                    <label>Date</label>
                    <select id="si_date_txt">
                        <option value="<?php echo $today;?>" selected>Today</option>
                        <option value="<?php echo $yesterday;?>">Yesterday</option>
                        <option value="<?php echo $before_yesterday;?>">Before Yesterday</option>
                        <option value="<?php echo $week;?>">Week</option>
                    </select>
                </div>
                <?php } ?>
                <div class="cp-filter-field">
                    <label>Customer</label>
                    <select id="si_customer_code_txt" style="max-width:400px;">
                        <option value="all">All</option>
                        <option value="WalkIn">WalkIn</option>
                        <option value="General">General</option>
                        <?php
                        $sql="select customer_code,customer_name from customer_master order by customer_name;";
                        if($qry=mysqli_query($connection,$sql))
                            while($row=mysqli_fetch_array($qry))
                                echo '<option value="'.$row["customer_code"].'">'.$row["customer_name"].'</option>';
                        ?>
                    </select>
                </div>
                <div class="cp-filter-field">
                    <label>User</label>
                    <select id="si_user_name_txt" style="max-width:140px;">
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
                <div class="cp-filter-field">
                    <label>Pay Mode</label>
                    <select id="si_pay_mode_txt">
                        <option value="all">All</option>
                        <option value="cash">Cash</option>
                        <option value="online">Online</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>
                <div class="cp-filter-actions">
                    <button type="button" class="cp-btn-search" onclick="loadSIList()">
                        <span class="material-icons-round" style="font-size:16px;vertical-align:middle;margin-right:4px;">search</span> Search
                    </button>
                    <button class="cp-action-btn cp-action-refresh" onclick="loadSIList()">
                            <span class="material-icons-round" style="font-size:15px;">sync</span> Refresh
                        </button>
                    <button type="button" class="cp-btn-reset" onclick="resetSIFilters()">
                        <span class="material-icons-round" style="font-size:16px;vertical-align:middle;margin-right:4px;">refresh</span> Reset
                    </button>
                </div>
            </div>

            <div class="cp-table-section">
                <div class="cp-bulk-actions-modern">
                    <div class="cp-bulk-left">
                        <!-- <button class="cp-action-btn cp-action-refresh" onclick="loadSIList()">
                            <span class="material-icons-round" style="font-size:15px;">sync</span> Refresh
                        </button> -->
                    </div>
                    <div class="cp-bulk-right">
                        <div class="cp-search-box">
                            <span class="material-icons-round" style="font-size:18px;color:#94a3b8;">search</span>
                            <input type="text" id="siQuickSearch" placeholder="Search invoices..." oninput="fnSIQuickSearch(this.value)"/>
                        </div>
                    </div>
                </div>
                <div id="siDiv" style="font-size:0.84rem;"></div>
            </div>
        </div>
    </div><!-- /#si_div -->

</div><!-- /tab-content -->

	<?php
		}
	?>

</div>

<!-- ══════════════════════════════════════════════════════════════════════════
     MODAL 1 (NEW): Design File Upload — shown BEFORE Job Card Details modal
     ══════════════════════════════════════════════════════════════════════════ -->
<div class="modal fade modal-lg" id="designUploadModal" tabindex="-1"
     aria-labelledby="designUploadModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
 
      <div class="modal-header bg-warning text-dark" style="padding:0.5rem 1rem;">
        <h5 class="modal-title" id="designUploadModalLabel">
          <span class="material-icons-round me-2" style="font-size:20px;vertical-align:-4px;">cloud_upload</span>
          Design Files — JC #<span id="du_jc_no_display"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
 
      <div class="modal-body">
 
        <!-- Customer info strip -->
        <div class="d-flex gap-3 mb-3 p-2 bg-light rounded border" style="font-size:0.85rem;">
          <span><strong>Customer:</strong> <span id="du_customer_name_display"></span></span>
          <span><strong>Mobile:</strong> <span id="du_customer_mobile_display"></span></span>
        </div>
 
        <!-- Hidden fields -->
        <input type="hidden" id="du_jc_no"           value="" />
        <input type="hidden" id="du_customer_name"   value="" />
        <input type="hidden" id="du_customer_mobile" value="" />
 
        <!-- ── EXISTING FILES (shown when files already uploaded) ── -->
        <div id="du_existing_files_section" style="display:none;" class="mb-3">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <strong style="font-size:0.85rem;">
              <i class="fas fa-folder-open text-success me-1"></i>
              Already Uploaded Files
            </strong>
            <span class="badge bg-success" id="du_existing_count">0</span>
          </div>
          <table class="du-existing-files-table" id="du_existing_files_table">
            <thead>
              <tr>
                <th style="width:44px;"></th>
                <th>File Name</th>
                <th>Type</th>
                <th>Size</th>
                <th>Uploaded</th>
                <th>By</th>
                <th style="width:70px;">Action</th>
              </tr>
            </thead>
            <tbody id="du_existing_files_tbody"></tbody>
          </table>
          <hr class="mt-3"/>
        </div>
 
        <!-- ── NEW FILE UPLOAD ZONE ── -->
        <div class="upload-drop-zone" id="du_drop_zone"
             onclick="document.getElementById('du_file_input').click()">
          <span class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></span>
          <div><strong>Click to browse</strong> or drag &amp; drop files here</div>
          <div class="text-muted mt-1" style="font-size:0.75rem;">
            <strong>Multiple files allowed</strong> &nbsp;•&nbsp;
            PDF, PSD, JPEG, JPG, PNG, ZIP, RAR, CDR, AI, EPS &nbsp;•&nbsp; Max 20 MB each
            <span class="text-danger fw-bold">Files ≥ 20 MB must be zipped</span>
            &nbsp;•&nbsp; Files &lt; 20 MB: ZIP optional
          </div>
        </div>
 
        <!-- Hidden multiple file input -->
        <input type="file" id="du_file_input"
               accept=".pdf,.psd,.jpeg,.jpg,.png,.zip,.rar,.7z,.cdr,.cdt,.ai,.eps"
               multiple style="display:none;"
               onchange="du_onFilesSelected(this)" />
 
        <!-- New-files preview chips -->
        <div id="du_files_preview_list"></div>
 
        <!-- Upload progress -->
        <div id="du_progress_wrap">
          <div class="progress mt-2" style="height:16px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                 id="du_progress_bar" role="progressbar" style="width:0%">0%</div>
          </div>
          <div class="text-muted mt-1" style="font-size:0.75rem;" id="du_progress_label">Uploading…</div>
        </div>
 
        <!-- OR divider -->
        <div class="d-flex align-items-center my-3">
          <hr class="flex-grow-1"><span class="mx-2 text-muted fw-bold" style="font-size:0.8rem;">OR — No file?</span><hr class="flex-grow-1">
        </div>
 
        <!-- Reason section -->
        <div class="reason-section">
          <label for="du_no_file_reason" class="form-label fw-bold mb-1" style="font-size:0.82rem;">
            <i class="fas fa-comment-alt me-1 text-warning"></i>
            Reason for not uploading a design file
          </label>
          <select id="du_no_file_reason" class="form-select" style="font-size:0.82rem;">
            <option value="">-- Select Reason --</option>
            <option value="Not Important">Not Important</option>
            <option value="Already File Existing">Already File Existing</option>
            <option value="Later I Upload">Later I Upload</option>
          </select>
          <div class="form-text text-muted" style="font-size:0.72rem;">
            Fill this ONLY when no file is uploaded above.
          </div>
        </div>
 
      </div><!-- /modal-body -->
 
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="du_proceed_btn"
                onclick="du_submitAndProceed()">
          <span class="material-icons-round me-1" style="font-size:16px;vertical-align:-3px;">check_circle</span> Save &amp; Proceed to Job Card Details
        </button>
      </div>
 
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════════════════
     MODAL 2 (EXISTING): Job Card Details — shown AFTER design upload
     ══════════════════════════════════════════════════════════════════════════ -->
<div class="modal fade modal-xl" id="jobcardCompletedModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-success  text-white" style="padding: 0.5rem 1rem;">
				<h5 class="modal-title" id="exampleModalLabel">Job Card Details</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class=" row  m-0 p-0">
					<input type="hidden" id="jc_id" value="" />
					<input type="hidden" id="jc_dt" value="" />
					<div id="materialUsageSpilageDetailDiv" class="col-12 p-1"  >				 
					</div>	
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" onclick="fnMarkCompleteJobCard()" id="completedJCBtn">Completed</button>
			</div>
		</div>
	</div>
</div>

<!-- SQ Mini Edit Modal (unchanged) -->
<div class="modal fade" id="SQ_Mini_Edit_modal" tabindex="-1" aria-labelledby="SQ_Mini_Edit_modalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-success  text-white" style="padding: 0.5rem 1rem;">
				<h5 class="modal-title" id="SQ_Mini_Edit_modalLabel">SQ Mini Edit</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
			<table>
        <tr><td>Quote No : </td><td><input type="text" id="SQ_Mini_Editquotation_no_txt" disabled/></td></tr>
		<tr><td>Discount : </td><td><input type="text" class="numericOnly" id="SQ_Mini_Editdiscount_txt" style="text-align:right;"/></td></tr>
		<tr><td>Balance Cash : </td><td><input type="text" class="numericOnly" id="SQ_Mini_Editbalance_cash_txt" style="text-align:right;"/></td></tr>
		<tr><td>Balance Online : </td><td><input type="text" class="numericOnly" id="SQ_Mini_Editbalance_gpay_txt" style="text-align:right;"/></td></tr>
		</table>
		
      </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" onclick="fnSQ_Mini_Edit_Update()" id="SQ_Mini_Edit_Btn">Submit</button>
			</div>
    </div>

  </div>
</div>	  

<script>
var DU_IS_ADMIN = <?php echo ($_SESSION['user_type']=='ADMIN'||$_SESSION['user_type']=='SUPERADMIN') ? 'true' : 'false'; ?>;

/* ═══════════════════════════════════════════════════════════════
   DESIGN UPLOAD MODAL — v2 (multi-file, edit/delete, bug fixed)
   ═══════════════════════════════════════════════════════════════ */
 
/* ═══════════════════════════════════════════════════════════════
   DESIGN UPLOAD — ZIP-AWARE VERSION
   Rules:
     • Files < 20 MB  → accepted as-is (ZIP optional)
     • Files ≥ 20 MB  → MUST be zipped (client-side via JSZip) before upload
     • ZIP files      → always accepted regardless of size
     • No file?       → must provide a reason to proceed
   ═══════════════════════════════════════════════════════════════ */




var DU_LIMIT_MB   = 20;
var DU_LIMIT_BYTES = DU_LIMIT_MB * 1024 * 1024;

/* ── Entry point ──────────────────────────────────────────────
   Called from the "In Progress" button in get_jobcard_list.php */
function fnDesignUploadPopUp(pJobCardNo, pCustomerName, pCustomerMobile) {
    du_pendingFiles = [];
    $('#du_jc_no').val(pJobCardNo);
    $('#du_customer_name').val(pCustomerName);
    $('#du_customer_mobile').val(pCustomerMobile);
    $('#du_jc_no_display').text(pJobCardNo);
    $('#du_customer_name_display').text(pCustomerName || '—');
    $('#du_customer_mobile_display').text(pCustomerMobile || '—');
    $('#du_no_file_reason').val('').prop('disabled', false);
    $('#du_file_input').val('').prop('disabled', false);
    $('#du_drop_zone').show();
    $('#du_files_preview_list').html('');
    $('#du_progress_wrap').hide();
    du_resetProgressBar();
    du_setDropZoneStyle(false);
    $('#du_existing_files_section').hide();
    $('#du_existing_files_tbody').html('');

    // Check for existing uploads
    $.ajax({
        type: 'GET',
        url: 'api/get_jc_design_upload.php',
        data: { jobcard_no: pJobCardNo },
        async: false,
        success: function(resp) {
            try {
                var r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                if (r.status === 'found') {
                    var files = [];
                    if (r.data.files_json) {
                        try { files = JSON.parse(r.data.files_json); } catch(e) {}
                    }
                    if (!files.length && r.data.file_name) {
                        files = [{
                            file_name: r.data.file_name,
                            file_path: r.data.file_path,
                            file_type: r.data.file_type,
                            file_size_kb: r.data.file_size_kb,
                            original_name: r.data.file_name,
                            uploaded_at: r.data.uploaded_at,
                            uploaded_by: r.data.uploaded_by
                        }];
                    }
                    if (r.data.upload_reason) {
                        $('#du_no_file_reason').val(r.data.upload_reason);
                        // Non-admin: reason already saved → lock upload zone
                        if (!DU_IS_ADMIN) {
                            $('#du_drop_zone').hide();
                            $('#du_file_input').prop('disabled', true);
                            $('#du_no_file_reason').prop('disabled', true);
                        }
                    }
                    if (files.length > 0) {
                        du_renderExistingFiles(files, pJobCardNo);
                        // Non-admin: files already uploaded → lock upload zone
                        if (!DU_IS_ADMIN) {
                            $('#du_drop_zone').hide();
                            $('#du_file_input').prop('disabled', true);
                        }
                    }
                }
            } catch(e) {}
        }
    });

    $('#designUploadModal').modal('show');
}

/* ── Render existing uploaded files table ─────────────────────*/
function du_renderExistingFiles(files, jcNo) {
    $('#du_existing_count').text(files.length);
    var rows = '';
    files.forEach(function(f) {
        var ext = (f.file_type || '').toLowerCase();
        var isImage = ['jpeg','jpg','png'].includes(ext);
        var thumb = isImage
            ? '<img src="' + f.file_path + '" class="du-file-thumb" onerror="this.style.display=\'none\'">'
            : '<div class="du-file-icon-box">' + du_fileIcon(ext) + '</div>';
        var dispName  = f.original_name || f.file_name || '—';
        var sizeStr   = f.file_size_kb > 1024
                      ? (f.file_size_kb/1024).toFixed(2)+' MB'
                      : f.file_size_kb+' KB';
        var uploadedAt = (f.uploaded_at || '').substring(0,10);
        var uploadedBy = f.uploaded_by || '—';
        rows += '<tr id="du_row_' + du_safeId(f.file_name) + '">'
             + '<td>' + thumb + '</td>'
             + '<td title="' + du_esc(f.file_name) + '">' + du_esc(dispName) + '</td>'
             + '<td><span class="badge bg-secondary">' + ext.toUpperCase() + '</span></td>'
             + '<td>' + sizeStr + '</td>'
             + '<td>' + uploadedAt + '</td>'
             + '<td>' + du_esc(uploadedBy) + '</td>'
             + '<td style="white-space:nowrap;">'
             + '<button class="btn btn-warning btn-sm py-0 px-1 me-1" style="font-size:0.72rem;"'
             + ' onclick="du_editFileName(' + jcNo + ',\'' + du_esc(f.file_name) + '\',\'' + du_esc(dispName) + '\')">'
             + '<span class="material-icons-round" style="font-size:14px;">edit</span></button>'
             + '<button class="btn btn-danger btn-sm py-0 px-1" style="font-size:0.72rem;"'
             + ' onclick="du_deleteFile(' + jcNo + ',\'' + du_esc(f.file_name) + '\')">'
             + '<span class="material-icons-round" style="font-size:14px;">delete</span></button>'
             + '</td>'
             + '</tr>';
    });
    $('#du_existing_files_tbody').html(rows);
    $('#du_existing_files_section').show();
}

/* ── Edit (rename) an existing file display name ─────────────*/
function du_editFileName(jcNo, fileName, currentName) {
    var newName = prompt('Rename file:', currentName);
    if (newName === null) return;       // cancelled
    newName = newName.trim();
    if (newName === '') { toastr.warning('Name cannot be empty.'); return; }
    $.ajax({
        type: 'POST',
        url:  'api/rename_jc_design_file.php',
        data: { jobcard_no: jcNo, file_name: fileName, new_display_name: newName },
        success: function(resp) {
            try {
                var r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                if (r.status === 'success') {
                    toastr.success('File renamed.');
                    du_renderExistingFiles(r.files, jcNo);
                } else {
                    toastr.error(r.message || 'Rename failed.');
                }
            } catch(e) { toastr.error('Unexpected response.'); }
        },
        error: function() { toastr.error('Network error. Please try again.'); }
    });
}

/* ── Delete an existing file ──────────────────────────────────*/
function du_deleteFile(jcNo, fileName) {
    if (!confirm('Delete this file from JC #' + jcNo + '? This cannot be undone.')) return;
    $.ajax({
        type: 'POST',
        url:  'api/delete_jc_design_file.php',
        data: { jobcard_no: jcNo, file_name: fileName },
        success: function(resp) {
            try {
                var r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                if (r.status === 'success') {
                    toastr.success('File deleted.');
                    if (r.files && r.files.length > 0) {
                        du_renderExistingFiles(r.files, jcNo);
                    } else {
                        $('#du_existing_files_section').hide();
                    }
                } else {
                    toastr.error(r.message || 'Delete failed.');
                }
            } catch(e) { toastr.error('Unexpected response.'); }
        },
        error: function() { toastr.error('Network error. Please try again.'); }
    });
}

/* ── File selection (multiple) ────────────────────────────────*/
// ✅ NEW — simpler, no auto-zip, supports all types
function du_onFilesSelected(input) {
    if (!input.files || input.files.length === 0) return;
    var allowed = ['pdf','psd','jpeg','jpg','png','zip','rar','7z','cdr','cdt','ai','eps'];
    for (var i = 0; i < input.files.length; i++) {
        var file = input.files[i];
        var parts = file.name.split('.');
        var ext   = parts[parts.length - 1].toLowerCase();

        // ★ ADD THESE TWO LINES
        console.log('FILE:', file.name, '| EXT:', ext, '| ALLOWED:', allowed.includes(ext));

        if (!allowed.includes(ext)) {
            toastr.error('"' + file.name + '" — invalid type. Allowed: PDF, PSD, JPEG, JPG, PNG, ZIP, RAR, 7Z, CDR, CDT, AI, EPS.');
            continue;
        }
        if (file.size > 20 * 1024 * 1024) {
            toastr.error('"' + file.name + '" exceeds 20 MB limit.');
            continue;
        }
        var already = du_pendingFiles.some(function(f){ return f.name === file.name; });
        console.log('ALREADY EXISTS:', already, '| PENDING COUNT:', du_pendingFiles.length);
        if (!already) du_pendingFiles.push(file);
    }
    du_renderPendingChips();
    input.value = '';
}

function du_renderPendingChips() {
    var html = '';
    du_pendingFiles.forEach(function(f, idx) {   // f is now the File directly
        var sizeStr = f.size > 1024*1024
                    ? (f.size/1024/1024).toFixed(1)+' MB'
                    : (f.size/1024).toFixed(0)+' KB';
        html += '<div class="du-file-chip">'
             + du_fileIcon(f.name.split('.').pop().toLowerCase())
             + '<span class="chip-name" title="'+du_esc(f.name)+'">'+du_esc(f.name)+'</span>'
             + '<span style="color:#6c757d;font-size:0.7rem;">('+sizeStr+')</span>'
             + '<button class="chip-remove" onclick="du_removePending('+idx+')" title="Remove">&#x2715;</button>'
             + '</div>';
    });
    $('#du_files_preview_list').html(html);
}

function du_removePending(idx) {
    du_pendingFiles.splice(idx, 1);
    du_renderPendingChips();
}

/* ── Drag and drop ────────────────────────────────────────────*/
$(document).on('dragover', '#du_drop_zone', function(e) {
    e.preventDefault(); du_setDropZoneStyle(true);
});
$(document).on('dragleave', '#du_drop_zone', function(e) {
    du_setDropZoneStyle(false);
});
$(document).on('drop', '#du_drop_zone', function(e) {
    e.preventDefault(); du_setDropZoneStyle(false);
    var files = e.originalEvent.dataTransfer.files;
    if (!files || !files.length) return;
    var dt = new DataTransfer();
    for (var i = 0; i < files.length; i++) dt.items.add(files[i]);
    var inp = document.getElementById('du_file_input');
    inp.files = dt.files;
    du_onFilesSelected(inp);
});

function du_setDropZoneStyle(active) {
    active ? $('#du_drop_zone').addClass('dragover') : $('#du_drop_zone').removeClass('dragover');
}
function du_resetProgressBar() {
    $('#du_progress_bar').css('width','0%').text('0%').removeClass('bg-success').addClass('bg-warning');
    $('#du_progress_label').text('Preparing…');
}

/* ── Zip a single File object, return Promise<File> ──────────*/
function du_zipFile(file) {
    return new Promise(function(resolve, reject) {
        var zip = new JSZip();
        zip.file(file.name, file);
        var zipName = file.name.replace(/\.[^/.]+$/, '') + '.zip';
        zip.generateAsync({ type: 'blob', compression: 'DEFLATE', compressionOptions: { level: 6 } },
            function updateCallback(meta) {
                var pct = Math.round(meta.percent);
                $('#du_progress_label').text('Zipping "' + file.name + '"… ' + pct + '%');
                $('#du_progress_bar').css('width', (pct * 0.5) + '%').text(pct + '%'); // zip = first 50%
            }
        ).then(function(blob) {
            var zippedFile = new File([blob], zipName, { type: 'application/zip' });
            resolve(zippedFile);
        }).catch(reject);
    });
}

/* ── Submit ───────────────────────────────────────────────────*/
function du_submitAndProceed() {
    var jcNo           = $('#du_jc_no').val();
    var customerName   = $('#du_customer_name').val();
    var customerMobile = $('#du_customer_mobile').val();
    var reason         = $('#du_no_file_reason').val().trim();
    var hasFiles       = du_pendingFiles.length > 0;

    console.log('SUBMIT: jcNo=' + jcNo + ' | pending=' + du_pendingFiles.length);

    if (!hasFiles && reason === '') {
        toastr.warning('Please add at least one design file, or provide a reason for not uploading.');
        $('#du_no_file_reason').focus();
        return;
    }

    var fd = new FormData();
    fd.append('jobcard_no',      jcNo);
    fd.append('customer_name',   customerName);
    fd.append('customer_mobile', customerMobile);
    fd.append('upload_reason',   reason);

    // ★ du_pendingFiles contains plain File objects — append directly
    du_pendingFiles.forEach(function(f) {
        console.log('Appending to FormData:', f.name, f.size);
        fd.append('design_files[]', f);
    });

    $('#du_proceed_btn').prop('disabled', true)
        .html('<i class="fas fa-spinner fa-spin me-1"></i> Saving…');
    $('#du_progress_wrap').show();
    du_resetProgressBar();

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'api/save_jc_design_upload.php', true);

    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            var pct = Math.round((e.loaded / e.total) * 100);
            $('#du_progress_bar').css('width', pct+'%').text(pct+'%');
            $('#du_progress_label').text('Uploading… ' + pct + '%');
        }
    };

    xhr.onload = function() {
        $('#du_proceed_btn').prop('disabled', false)
            .html('<span class="material-icons-round me-1" style="font-size:16px;vertical-align:-3px;">check_circle</span> Save &amp; Proceed to Job Card Details');
        console.log('SERVER RESPONSE:', xhr.responseText);
        try {
            var resp = JSON.parse(xhr.responseText);
            if (resp.status === 'success') {
                $('#du_progress_bar').css('width','100%').text('100%')
                    .removeClass('bg-warning').addClass('bg-success');
                $('#du_progress_label').text('Saved! Opening Job Card Details…');
                toastr.success(resp.message);
                setTimeout(function() {
                    $('#designUploadModal').modal('hide');
                    fnCompletePopUp(parseInt(jcNo));
                }, 600);
            } else {
                $('#du_progress_wrap').hide();
                toastr.error(resp.message || 'Save failed. Please try again.');
            }
        } catch(e) {
            $('#du_progress_wrap').hide();
            toastr.error('Unexpected server response. Please try again.');
        }
    };

    xhr.onerror = function() {
        $('#du_proceed_btn').prop('disabled', false)
            .html('<span class="material-icons-round me-1" style="font-size:16px;vertical-align:-3px;">check_circle</span> Save &amp; Proceed to Job Card Details');
        $('#du_progress_wrap').hide();
        toastr.error('Network error during upload. Please try again.');
    };

    xhr.send(fd);
}

/* ── Process files (zip large ones), then upload ─────────────*/
function du_processAndUpload(jcNo, customerName, customerMobile, reason) {

    // Build promise chain: zip files that need it, pass others through
    var zipPromises = du_pendingFiles.map(function(entry) {
        if (entry.needsZip) {
            return du_zipFile(entry.file); // returns Promise<File>
        } else {
            return Promise.resolve(entry.file);
        }
    });

    Promise.all(zipPromises).then(function(readyFiles) {

        var fd = new FormData();
        fd.append('jobcard_no',      jcNo);
        fd.append('customer_name',   customerName);
        fd.append('customer_mobile', customerMobile);
        fd.append('upload_reason',   reason);
        readyFiles.forEach(function(f) {
            fd.append('design_files[]', f);
        });

        $('#du_progress_label').text('Uploading…');
        $('#du_progress_bar').css('width','50%').text('50%'); // upload = second 50%

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'api/save_jc_design_upload.php', true);

        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                // Map 0-100% upload progress to 50-100% on bar
                var pct = 50 + Math.round((e.loaded / e.total) * 50);
                $('#du_progress_bar').css('width', pct+'%').text(pct+'%');
                $('#du_progress_label').text('Uploading… ' + pct + '%');
            }
        };

        xhr.onload = function() {
            $('#du_proceed_btn').prop('disabled', false)
                .html('<span class="material-icons-round me-1" style="font-size:16px;vertical-align:-3px;">check_circle</span> Save &amp; Proceed to Job Card Details');
            try {
                var resp = JSON.parse(xhr.responseText);
                if (resp.status === 'success') {
                    $('#du_progress_bar').css('width','100%').text('100%')
                        .removeClass('bg-warning').addClass('bg-success');
                    $('#du_progress_label').text('Saved! Opening Job Card Details…');
                    toastr.success(resp.message);
                    setTimeout(function() {
                        $('#designUploadModal').modal('hide');
                        fnCompletePopUp(parseInt(jcNo));
                    }, 600);
                } else {
                    $('#du_progress_wrap').hide();
                    toastr.error(resp.message || 'Save failed. Please try again.');
                }
            } catch(e) {
                $('#du_progress_wrap').hide();
                toastr.error('Unexpected server response. Please try again.');
            }
        };

        xhr.onerror = function() {
            $('#du_proceed_btn').prop('disabled', false)
                .html('<span class="material-icons-round me-1" style="font-size:16px;vertical-align:-3px;">check_circle</span> Save &amp; Proceed to Job Card Details');
            $('#du_progress_wrap').hide();
            toastr.error('Network error during upload. Please try again.');
        };

        xhr.send(fd);

    }).catch(function(err) {
        $('#du_proceed_btn').prop('disabled', false)
            .html('<span class="material-icons-round me-1" style="font-size:16px;vertical-align:-3px;">check_circle</span> Save &amp; Proceed to Job Card Details');
        $('#du_progress_wrap').hide();
        toastr.error('Failed to zip file: ' + (err && err.message ? err.message : 'Unknown error'));
    });
}

/* ── Utility ──────────────────────────────────────────────────*/
function du_fileIcon(ext) {
    var icons = { pdf:'📄', psd:'🎨', jpg:'🖼️', jpeg:'🖼️', png:'🖼️', zip:'🗜️' };
    return icons[ext] || '📁';
}
function du_esc(str) {
    return String(str||'')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function du_safeId(str) {
    return String(str||'').replace(/[^a-zA-Z0-9]/g,'_');
}


/* ═══════════════════════════════════════════════════════════════════════════
   EXISTING FUNCTIONS (unchanged except fnCompletePopUp — kept as-is)
   ═══════════════════════════════════════════════════════════════════════════ */

function fnSQ_Mini_Edit_Update()
{
	var SQ_Mini_Editquotation_no_txt=$("#SQ_Mini_Editquotation_no_txt").val();
	var SQ_Mini_Editdiscount_txt=$("#SQ_Mini_Editdiscount_txt").val();
	var SQ_Mini_Editbalance_cash_txt=$("#SQ_Mini_Editbalance_cash_txt").val();
	var SQ_Mini_Editbalance_gpay_txt=$("#SQ_Mini_Editbalance_gpay_txt").val();
	
	$.ajax({
		type: "POST",
		url: "api/save_sq_mini_edit_data.php",
		data:{
			"SQ_Mini_Editquotation_no_txt":SQ_Mini_Editquotation_no_txt, 
			"SQ_Mini_Editdiscount_txt":SQ_Mini_Editdiscount_txt,
			"SQ_Mini_Editbalance_cash_txt":SQ_Mini_Editbalance_cash_txt,
			"SQ_Mini_Editbalance_gpay_txt":SQ_Mini_Editbalance_gpay_txt
		},
			async:false,		
			success: function (response) {
				if(response !== "Failed")
				{
					alert("Updated Successfully.  Refresh List (search) to see the updation");
					$('#SQ_Mini_Edit_modal').modal('hide');
				}
				else
				{
					alert("Cannot Do Updation. Try Again." );
				}
			}
	});
	
}
function mini_edit(p_quotation_no_txt)
{
	$.ajax({
			type: "GET",
			url: "api/get_sq_mini_edit_data.php?quotation_no_txt="+p_quotation_no_txt,
			async:false,		
			success: function (response) {
				if(response !== "Failed")
				{
					ans=response.split(",");
					$("#SQ_Mini_Editquotation_no_txt").val(ans[0]);
					$("#SQ_Mini_Editdiscount_txt").val(ans[1]);
					$("#SQ_Mini_Editbalance_cash_txt").val(ans[2]);
					$("#SQ_Mini_Editbalance_gpay_txt").val(ans[3]);
				}

			}
		});	
	$('#SQ_Mini_Edit_modal').modal('show');
}
function close_globalView()
{
	$("#globalView").hide();
}
function select_all_jcs()
{
	$(".select_jcs").prop('checked', true);
}
function unselect_all_jcs()
{
	$(".select_jcs").prop('checked', false);
}
function select_all_sqs()
{
	$(".select_sqs").prop('checked', true);
}
function unselect_all_sqs()
{
	$(".select_sqs").prop('checked', false);
}


function sales_invoice_sqs()
{
	var sq_nos=""; 
	var jc_nos=""; 
	var customer="";
	var err_flag=0;
	$('.select_sqs').each(function() {
		if ($(this).is(':checked')) {
			if(sq_nos!="") sq_nos=sq_nos+",";
			if(jc_nos!="") jc_nos=jc_nos+",";
			sq_nos=sq_nos+$(this).val();
			jc_nos=jc_nos+$(this).attr("jc_nos");
			if(customer=="")
			{
				customer=$(this).attr("customer");
			}
			if(customer!=$(this).attr("customer"))
			{
				err_flag=1;
				return;
			}
		}
	}); 
	if(err_flag==1)
	{
		toastr.error("Can't Combine Diff. Customer(s)");
		return;		
	}
	if(sq_nos.length==0) 
	{
		toastr.error("No Sale Quotation Selected");
		return;
	}
	
	if(confirm("Are you sure you want to convert to Sales Invoice?")){
        $.ajax({
			type: "GET",
			url: "api/save_sale_invoice.php?sq_no="+sq_nos+"&jc_nos="+jc_nos,
			async:false,		
			success: function (response) {
				if(response == "Failed")
				{
					toastr.error("Failed to convert to Invoice!!!");
					return;
				}
				else
				{
					toastr.success("Successfully converted to Invoice. "+response);
					loadSQList();
				}
			}
		});
    }
    else{
        return false;
    }
	 
}
function close_selected_jcs()
{
	var job_card_nos = "";
	var job_card_type = "";
	var credit_customer_name = "";
	var invalid_operation = false;

	$('.select_jcs').each(function() {
		if ($(this).is(':checked')) {
			if (job_card_nos != "") job_card_nos = job_card_nos + ",";
			job_card_nos = job_card_nos + $(this).val();

			var job_completed = $(this).attr("job_completed");
			if (job_completed == 0) {
				alert("Incomplete job cards cannot be closed. Please mark the job as completed first.");
				invalid_operation = true;
				return false; // break .each()
			}

			var this_type = $(this).attr("customer_type");
			var this_name = $(this).attr("customer_name");

			if (job_card_type === "") {
				// First selected JC — store its type and name
				job_card_type = this_type;
				credit_customer_name = this_name;
			} else {
				// Subsequent JCs — must be the same customer type
				if (job_card_type !== this_type) {
					alert("Cannot mix job cards of different customer types (" + job_card_type + " and " + this_type + ").\nPlease select only " + job_card_type + " job cards.");
					invalid_operation = true;
					return false;
				}
				// For Credit customers: all JCs must belong to the same credit account
				if (job_card_type === "Credit" && credit_customer_name !== this_name) {
					alert("Cannot mix job cards from different credit customers.\nAll selected job cards must belong to the same credit customer.");
					invalid_operation = true;
					return false;
				}
				// WalkIn and General: allowed to combine regardless of individual names
			}
		}
	});

	if (invalid_operation) return;
	if (job_card_nos.length === 0) {
		alert("No Job Card Selected");
		return;
	}

	$("#globalViewContentDiv").load("job_card.php", function() {
		$("#jobcard_no_txt").text(job_card_nos);
		load_job_cards_from_db("JC_2_SQ");
		$("#globalView").show();
	});
}

$(document).ready(function() {
	// Wrap UI inits in try-catch so a CDN failure never blocks data loading
	try { SetUpBasics(); } catch(e) { console.warn('SetUpBasics error:', e); }
	try {
		$('.datepicker').removeClass('hasDatepicker').datepicker({ dateFormat: 'dd-mm-yy' });
	} catch(e) { console.warn('Datepicker init error:', e); }

	<?php
		 if($_SESSION['user_type'] != "SUPERADMIN")
		{
	?>

	// In $(document).ready() — replace the shown.bs.tab listeners with this:
loadJobCardList();
loadSQList();

// SI loads after a brief delay — tab is hidden but DataTable still initializes
setTimeout(function() {
    loadSIList();
}, 300);
	

	// Check for any on-hold JCs whose QR payment arrived while user was away
	function fnCheckPendingQRClosures() {
		$.getJSON('api/process_pending_qr_closures.php', function(res) {
			if (res.converted && res.converted.length > 0) {
				res.converted.forEach(function(item) {
					toastr.success('✅ JC ' + item.jc + ' converted to SQ ' + item.sq + ' — QR payment received!', '', {timeOut: 8000});
				});
				loadJobCardList();
				loadSQList();
			}
		});
	}
	fnCheckPendingQRClosures(); // run once on page load

	// Repeat every 30 seconds while dashboard is open — catches payments that
	// arrive after page load (customer pays while staff is processing other JCs)
	setInterval(function() {
		// Only poll if there are any "Awaiting Payment" rows visible in the JC list
		if ($('.select_jcs').length > 0 && $('[title="On hold — will auto-convert to SQ when QR payment is received"]').length > 0) {
			fnCheckPendingQRClosures();
		}
	}, 30000);

	// ── Auto-recovery: check bank statement for missed payments ──────────
	// Runs on page load and every 10 minutes. Checks last 3 days of
	// transactions against bank statement API and recovers any missed payments.
	function fnQRAutoRecovery() {
		$.getJSON('api/qr_auto_recovery.php', function(res) {
			if (res.recovered > 0) {
				toastr.success('Auto-recovered ' + res.recovered + ' missed QR payment(s) from bank statement!', 'Payment Recovery', {timeOut: 10000});
				loadJobCardList();
				loadSQList();
			}
			if (res.converted && res.converted.length > 0) {
				res.converted.forEach(function(item) {
					toastr.success('JC ' + item.jc + ' auto-converted to SQ ' + item.sq + ' (recovered payment)', '', {timeOut: 8000});
				});
			}
		});
	}
	fnQRAutoRecovery();
	window._qrAutoRecoveryInterval = setInterval(fnQRAutoRecovery, 600000);

	<?php
		}
		else
		{
	?>

	$("#superadmin_show_btn").on("click",function(){
	
		var from = $('#date1Txt').val();
		var to = $('#date2Txt').val()		
        $.ajax({
			type: "GET",
			url: "api/sa_card1_summary.php?dt1="+from+"&dt2="+to,
			async:false,		
			success: function (response) {
				if(response !== "Failed")
				{
					ans=response.split(",");
					$("#jc_cnt").text(ans[0]);
					$("#sq_cnt").text(ans[1]);
					$("#si_cnt").text(ans[2]);
					$("#balance_txt").text(ans[3]);
				}

			}
		});	
        $.ajax({
			type: "GET",
			url: "api/sa_card2_summary.php?dt1="+from+"&dt2="+to,
			async:false,		
			success: function (response) {
				if(response !== "Failed")
				{
					ans=response.split(",");
					$("#printing_cnt").text(ans[0]);
					$("#services_cnt").text(ans[1]);
					$("#materials_cnt").text(ans[2]);
					$("#id_card_cnt").text(ans[3]);
					$("#products_cnt").text(ans[4]);
				}

			}
		});	
        $.ajax({
			type: "GET",
			url: "api/sa_card3_summary.php?dt1="+from+"&dt2="+to,
			async:false,		
			success: function (response) {
				if(response !== "Failed")
				{
					$("#machine_count_div").html(response);	
				}

			}
		});	
        $.ajax({
			type: "GET",
			url: "api/sa_card4_summary.php?dt1="+from+"&dt2="+to,
			async:false,		
			success: function (response) {
				if(response !== "Failed")
				{
					$("#total_sales_div").html(response);	
				}

			}
		});	
        $.ajax({
			type: "GET",
			url: "api/sa_card5_summary.php?dt1="+from+"&dt2="+to,
			async:false,		
			success: function (response) {
				if(response !== "Failed")
				{
					ans=response.split(",");
					$("#general_cnt").text(ans[0]);
					$("#walkin_cnt").text(ans[1]);
					$("#credit_cnt").text(ans[2]);
				}

			}
		});	
        $.ajax({
			type: "GET",
			url: "api/sa_card6_summary.php?dt1="+from+"&dt2="+to,
			async:false,		
			success: function (response) {
				if(response !== "Failed")
				{
					$("#active_users_div").html(response);	
				}

			}
		});	
	});
	<?php
		}
	?>	
});
function loadJobCardList()
{
    var dt="";
    if($("#jc_date_txt").length)
        dt = $('#jc_date_txt').val(); 
    else
        dt = $('#jc_from_dt_txt').val()+ "~"+$('#jc_to_dt_txt').val(); 
    var customer_code_txt = $('#customer_code_txt').val();
    var user_name_txt = $('#user_name_txt').val();
    var pay_mode_txt = $('#pay_mode_txt').val();
    status_txt="0";
    if($("#status_txt").length)
    {
        status_txt=$("#status_txt").val();
    }

    $('#jobcardDiv').html('');
    $.ajax({
        type: "POST",
        url: "api/get_jobcard_list.php", 
        data:{
            "dt":dt, 
            "customer_code_txt":customer_code_txt,
            "user_name_txt":user_name_txt,
            "pay_mode_txt":pay_mode_txt,
            "status_txt":status_txt
        },
        success: function (response) {
            if(response != "")
            {  
                $("#jobcardDiv").html(response);
                var table = $('#jobcardTable').DataTable({
                    destroy: true,
                    autoWidth: false,
                    dom: 'rtip',
                    lengthMenu: [[-1, 10, 25, 50], ["All", 10, 25, 50]],
                    language: {
                        emptyTable: '<div style="text-align:center;padding:30px 0;color:#94a3b8;"><span class="material-icons-round" style="font-size:3rem;display:block;margin-bottom:8px;color:#cbd5e1;">inventory_2</span>No data available in table<br><span style="font-size:0.8rem;">Try adjusting your filters or <a href="javascript:void(0)" onclick="fnModule(1)" style="color:#4f46e5;">create a new job card</a>.</span></div>',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries'
                    }
                });

                var jcCount = table.rows().count();
                var $badge = $('#jc-tab .jc-tab-badge');
                if (jcCount > 0) {
                    if ($badge.length) {
                        $badge.text(jcCount);
                    } else {
                        $('#jc-tab').append('<span class="jc-tab-badge">' + jcCount + '</span>');
                    }
                } else {
                    $badge.remove();
                }

                // Color rows by status
                table.rows().every(function() {
                    var $row = $(this.node());
                    var jcCell = $row.find('td:eq(1)').text().toLowerCase();
                    if (jcCell.indexOf('closed') > -1) {
                        $row.css({'background-color': '#d1fae5', 'border-left': '4px solid #16a34a'});
                        $row.find('td').css('color', '#000000');
                    } else if ($row.find('.select_jcs').length) {
                        $row.css({'background-color': '#fef3c7', 'border-left': '4px solid #f59e0b'});
                        $row.find('td').css('color', '#000000');
                    }
                });

                $('#jobcardTable thead tr:eq(1) th').each(function(i) {
                    $('input', this).on('keyup change', function() {
                        if (table.column(i).search() !== this.value) {
                            table.column(i).search(this.value).draw();
                        }
                    });
                });
            }
            else
            {
                toastr.error("Failed to get details!!!");
            }
        },
        error: function(xhr, status, error){
            console.error(error);
        }
    });

    <?php if($_SESSION['user_type'] == "ADMIN" || $_SESSION['user_type'] == "SUPERADMIN"): ?>
    loadJCSummaryCards();
    <?php endif; ?>
}

function loadJCSummaryCards() {
    var now = new Date();
    var mm  = String(now.getMonth() + 1).padStart(2, '0');
    var yyyy = now.getFullYear();
    $.ajax({
        type: "GET",
        url: "api/get_jc_summary.php",
        data: { month: mm, year: yyyy },
        dataType: "json",
        success: function(data) {
            if (data) {
                $('#jc_total_count').text(data.total || 0);
                $('#jc_progress_count').text(data.in_progress || 0);
                $('#jc_completed_count').text(data.completed || 0);
                $('#jc_void_count').text(data.void || 0);
            }
        }
    });
}

function fnJCQuickSearch(val) {
	var table = $('#jobcardTable').DataTable();
	if (table) {
		table.search(val).draw();
	}
}

function resetJCFilters() {
	if ($('#jc_from_dt_txt').length) {
		var today = '<?php echo date("d-m-Y"); ?>';
		$('#jc_from_dt_txt').val(today);
		$('#jc_to_dt_txt').val(today);
	}
	$('#customer_code_txt').val('all');
	$('#user_name_txt').val('all');
	$('#pay_mode_txt').val('all');
	if ($('#status_txt').length) $('#status_txt').val('0');
	$('#jcQuickSearch').val('');
	loadJobCardList();
}

function fnSQQuickSearch(val) {
    var table = $('#sqTable').DataTable();
    if (table) table.search(val).draw();
}

function resetSQFilters() {
    if ($('#sq_from_dt_txt').length) {
        var today = '<?php echo date("d-m-Y"); ?>';
        $('#sq_from_dt_txt').val(today);
        $('#sq_to_dt_txt').val(today);
    }
    $('#sq_customer_code_txt').val('all');
    $('#sq_user_name_txt').val('all');
    $('#sq_pay_mode_txt').val('all');
    if ($('#sq_status_txt').length) $('#sq_status_txt').val('0');
    $('#sqQuickSearch').val('');
    loadSQList();
}

function fnSIQuickSearch(val) {
    var table = $('#siTable').DataTable();
    if (table) table.search(val).draw();
}

function resetSIFilters() {
    if ($('#si_from_dt_txt').length) {
        var today = '<?php echo date("d-m-Y"); ?>';
        $('#si_from_dt_txt').val(today);
        $('#si_to_dt_txt').val(today);
    }
    $('#si_customer_code_txt').val('all');
    $('#si_user_name_txt').val('all');
    $('#si_pay_mode_txt').val('all');
    $('#siQuickSearch').val('');
    loadSIList();
}

function loadSQList()
{
	var dt="";
	if($("#sq_date_txt").length)
		dt = $('#sq_date_txt').val(); 
	else
		dt = $('#sq_from_dt_txt').val()+ "~"+$('#sq_to_dt_txt').val();
	var customer_code_txt = $('#sq_customer_code_txt').val();
	var user_name_txt = $('#sq_user_name_txt').val();
	var pay_mode_txt = $('#sq_pay_mode_txt').val();
	status_txt="0";
	if($("#sq_status_txt").length)
	{
		status_txt=$("#sq_status_txt").val();
	}

	$('#sqDiv').html('');
	$.ajax({url: "api/get_sq_list.php",
	method: 'POST', 
	data:{
			"dt":dt, 
			"customer_code_txt":customer_code_txt,
			"user_name_txt":user_name_txt,
			"pay_mode_txt":pay_mode_txt,
			"status_txt":status_txt
		},
	success: function(result){
		$('#sqQuickSearch').val('');
		$("#sqDiv").html(result);
		var table = $('#sqTable').DataTable({
		destroy: true,
		autoWidth: false,
        dom: 'rtip',
		"lengthMenu": [[-1, 10, 25, 50], ["All", 10, 25, 50]],
		language: {
            emptyTable: '<div style="text-align:center;padding:30px 0;color:#94a3b8;"><span class="material-icons-round" style="font-size:3rem;display:block;margin-bottom:8px;color:#cbd5e1;">request_quote</span>No quotes found.<br><span style="font-size:0.8rem;">Try adjusting your filters.</span></div>',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries'
        },
        
		});
		 $('#sqTable thead tr:eq(1) th').each(function(i) {
			$('input', this).on('keyup change', function() {
			  if (table.column(i).search() !== this.value) {
				table.column(i).search(this.value).draw();
			  }
			});
		  });
	},error: function(xhr, status, error){
		console.error(error);
	}});

}
function loadSIList() {

    console.log('========== loadSIList START ==========');

    var dt = "";
    if($("#si_date_txt").length)
        dt = $('#si_date_txt').val();
    else
        dt = $('#si_from_dt_txt').val() + "~" + $('#si_to_dt_txt').val();

    var customer_code_txt = $('#si_customer_code_txt').val();
    var user_name_txt     = $('#si_user_name_txt').val();
    var pay_mode_txt      = $('#si_pay_mode_txt').val();

    console.log('Filters:', {
        dt,
        customer_code_txt,
        user_name_txt,
        pay_mode_txt
    });

    console.log('siDiv exists:', $('#siDiv').length);
    console.log('siDiv visible:', $('#siDiv').is(':visible'));

    $('#siDiv').html('');

    $.ajax({
        url: "api/get_si_list.php",
        method: 'POST',
        data: {
            "dt": dt,
            "customer_code_txt": customer_code_txt,
            "user_name_txt": user_name_txt,
            "pay_mode_txt": pay_mode_txt
        },

        beforeSend: function () {
            console.log('AJAX Request Started');
        },

        success: function(result) {

            console.log('AJAX SUCCESS');
            console.log('Response Length:', result.length);
            console.log('Response Preview:', result.substring(0,500));

            $("#siDiv").html(result);

            console.log('HTML inserted into siDiv');
            console.log('siTable found:', $('#siTable').length);
            console.log('siDiv html length:', $('#siDiv').html().length);

            try {

                console.log('Initializing DataTable...');

                var table = $('#siTable').DataTable({
                    destroy: true,
                    autoWidth: false,
                    dom: 'rtip',
                    lengthMenu: [[-1,10,25,50],["All",10,25,50]],
                    language: {
                        emptyTable: '<div style="text-align:center;padding:30px 0;color:#94a3b8;"><span class="material-icons-round" style="font-size:3rem;display:block;margin-bottom:8px;color:#cbd5e1;">receipt</span>No invoices found.<br><span style="font-size:0.8rem;">Try adjusting your filters.</span></div>',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries'
                    },
                    
                });

                console.log('DataTable Initialized Successfully');

                console.log('Rows Found:',
                    table.rows().count()
                );

            } catch(ex) {

                console.error('DATATABLE ERROR');
                console.error(ex);

            }

            console.log('siDiv Visible:', $('#siDiv').is(':visible'));
            console.log('siTable Visible:', $('#siTable').is(':visible'));

            console.log('========== loadSIList END ==========');

        },

        error: function(xhr,status,error) {

            console.error('AJAX ERROR');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);

        }
    });
}
// ── Manual QR payment status check ──────────────────────────────────────────
function fnCheckQRPayment(jcno, extId) {
	var $btn = $('#qrCheckBtn_' + jcno);
	$btn.prop('disabled', true).html('⏳ Checking…');

	// Always use the JC-number endpoint. It scans ALL outstanding transactions
	// for this JC (not just the most recent one), so it correctly handles the
	// case where a customer paid on QR #1 but staff later regenerated QR #2.
	// (The stored extId from pending_qr_closures might point to an old/expired QR.)
	var url = 'api/check_jc_qr_payment.php?jcno=' + jcno;

	$.getJSON(url, function(res) {
		if (res.status === 'SUCCESS') {
			if (res.converted_sq) {
				toastr.success('✅ Payment received! JC ' + jcno + ' auto-converted to SQ ' + res.converted_sq + '.', '', {timeOut: 8000});
			} else {
				// Payment confirmed but no pending_qr_closures record (JC was not properly parked).
				toastr.warning('⚠️ Payment received for JC ' + jcno + ', but the Job Card was not on hold. Please open the JC and close it manually to SQ.', '', {timeOut: 10000});
			}
			setTimeout(function() { loadJobCardList(); loadSQList(); }, 1200);
		} else if (res.status === 'FAILED') {
			toastr.error('❌ Payment FAILED at bank for JC ' + jcno + '. Customer must retry.');
			$btn.prop('disabled', false).html('🔄 Check Payment');
		} else if (res.status === 'NOT_FOUND') {
			toastr.warning(res.message || ('No QR transaction found for JC ' + jcno + '. Please regenerate the QR.'));
			$btn.prop('disabled', false).html('🔄 Check Payment');
		} else {
			toastr.info('⏳ Payment not yet received by bank for JC ' + jcno + '. Ask the customer to complete payment.');
			$btn.prop('disabled', false).html('🔄 Check Payment');
		}
	}).fail(function() {
		toastr.error('Network error while checking payment status. Please try again.');
		$btn.prop('disabled', false).html('🔄 Check Payment');
	});
}

// ── Force-mark a QR transaction as paid (bank API lag workaround) ─────────────
function fnForceMarkQRPaid(jcno, extId, amount) {
	var msg = '✅ CONFIRM MANUAL PAYMENT\n\n'
	        + 'Job Card : ' + jcno + '\n'
	        + 'Amount   : ₹' + amount + '\n\n'
	        + 'Use this ONLY when the payment is confirmed in your bank account\n'
	        + 'but the system still shows PENDING.\n\n'
	        + 'This will mark the payment as received and convert the\n'
	        + 'Job Card to a Sales Quote immediately.\n\n'
	        + 'Proceed?';
	if (!confirm(msg)) return;

	var $btn = $('#qrMarkBtn_' + jcno);
	var $chk = $('#qrCheckBtn_' + jcno);
	$btn.prop('disabled', true).html('⏳ Processing…');
	$chk.prop('disabled', true);

	$.ajax({
		type    : 'POST',
		url     : 'api/force_mark_qr_paid.php',
		dataType: 'json',
		data    : { ext_transaction_id: extId, jobcard_no: jcno },
		success : function(res) {
			if (res.status === 'success') {
				if (res.converted_sq) {
					toastr.success('✅ Payment confirmed! JC ' + jcno + ' → SQ ' + res.converted_sq + '.', '', {timeOut: 8000});
				} else {
					toastr.warning(res.message || 'Payment marked. Please close the JC manually if needed.', '', {timeOut: 8000});
				}
				setTimeout(function() { loadJobCardList(); loadSQList(); }, 1200);
			} else {
				toastr.error(res.message || 'Failed to mark payment. Please try again.');
				$btn.prop('disabled', false).html('✅ Mark Paid');
				$chk.prop('disabled', false);
			}
		},
		error: function() {
			toastr.error('Network error. Please try again.');
			$btn.prop('disabled', false).html('✅ Mark Paid');
			$chk.prop('disabled', false);
		}
	});
}

function fnMarkVoid(pJobCardNo)
{
	if (confirm("Sure of Marking this JC as VOID!") == true)
	{
	$.ajax({
		type: "POST",
		url: "api/mark_job_card_void.php",
		data:{'job_card_no':pJobCardNo},
		async:false,		
	success: function (data) {
		loadJobCardList();
	}
	});		
		
	}
} 

/* ─────────────────────────────────────────────────────────────────────────────
   fnCompletePopUp — UNCHANGED. Called automatically after design upload succeeds.
   ───────────────────────────────────────────────────────────────────────────── */
function fnCompletePopUp(pJobCardNo)
{  
	$("#jc_id").val(pJobCardNo);
	$.ajax({
		type: "POST",
		url: "api/get_jobcard_details.php",
		data:{"job_card_no":pJobCardNo,"type":"mat"},
		async:false,		
		success: function (data) {
			if(data != "")
			{
				$('#materialUsageSpilageDetailDiv').html('');
				rows = '<center><h6 class="bg-warning p-1">Material Details</h6></center><table class="table table-bordered table-hover" id="materialUsageSpilageTable">';
				rows += '  <thead class="table-dark">';
				rows += ' <tr><th>SNo</th><th>Material Name</th><th>Machine Name</th><th>Usage</th><th>F & B </th><th>Spilage</th></tr>';  
				rows += '</thead><tbody>'; 
				for(var i=0;i<data.length;i++)
				{ 
					if(data[i].material_code.length>0)
					{
					rows += ' <tr>';  
					rows += '     <td>'+ (i+1) +'</td>'; 
					rows += '     <td>'+data[i].material_code +'</td>'; 
					rows += '     <td>'+data[i].machine_code +'</td>'; 
					rows += '     <td>'+data[i].usage_count+'</td>'; 
					rows += '     <td><input type="checkbox" name="front_and_back_'+i+'" id="front_and_back_'+i+'" class="form-check-input"> </td>'; 
					 rows += '     <td>'; 
					 rows += '       <input type="text" name="material_spilage_'+i+'" id="material_spilage_'+i+'" class="form-control" />';  
					 rows += '     </td>'; 
					rows += '   </tr>';  
					}
				  }
				  rows += ' </tbody></table>';
				$('#materialUsageSpilageDetailDiv').html(rows); 
				$('#materialUsageSpilageTable').DataTable({  
				"searching":false,
				"ordering":false,
				"paging":false 
				});
			}
		}
	});
	$('div#materialUsageSpilageTable_info.dt-info').hide();
	$('div#machineUsageSpilageTable_info.dt-info').hide();
	$('#jobcardCompletedModal').modal('show');
}

function fnMarkCompleteJobCard()
{
	var jobcard_no=$('#jc_id').val();
	var jobcard_date=$('#jc_dt').val();
	var materialUsageSpilageArr =[];
	$('#completedJCBtn').prop('disabled',true);
	$('#materialUsageSpilageTable tbody tr').each(function() {
		var temp={};
		temp['jobcard_no']=jobcard_no;
		temp['jobcard_date']=jobcard_date;
		temp['material_code']=$(this).find('td:eq(1)').text();
		temp['machine_code']=$(this).find('td:eq(2)').text();
		temp['front_back']=$(this).find('td:eq(4) input').is(':checked')?1:0;
		temp['spillage_count']=$(this).find('td:eq(5) input').val(); ;
		materialUsageSpilageArr.push(temp); 
	});
	 
	$.ajax({
		type: "POST",
		url: "api/save_job_card_complete.php", 
		data:{"jobcard_no":jobcard_no,  "material_spillage_details": JSON.stringify(materialUsageSpilageArr)
		},
		success: function (response) {
			if(response == "Success")
			{ 
				toastr.success('Successfully Completed Job Card' );
				$('#jobcardCompletedModal').modal('hide');
				loadJobCardList();
			}
			else
			{
				toastr.error("Failed to Complete job card!!!");
			}
			$('#completedJCBtn').prop('disabled',false);
		}
	});
}


/* ── Three-dot JC action dropdown ── */
$(document).on('click', '.jc-dot-btn', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var $wrap = $(this).closest('.jc-action-wrap');
    var $menu = $wrap.find('.jc-drop-menu');
    var isOpen = $menu.hasClass('open');
    // Close all open menus
    $('.jc-drop-menu.open').removeClass('open');
    if (!isOpen) {
        $menu.addClass('open');
        // Calculate position BEFORE showing, then decide top/bottom
        var btnRect = this.getBoundingClientRect();
        var menuHeight = 220; // approximate max height of dropdown
        var spaceBelow = window.innerHeight - btnRect.bottom;
        var spaceAbove = btnRect.top;

        var topPos;
        if (spaceBelow < menuHeight && spaceAbove > menuHeight) {
            // Not enough room below — open UPWARD
            topPos = btnRect.top - menuHeight - 4;
        } else {
            // Default — open downward
            topPos = btnRect.bottom + 4;
        }

        $menu.css({
            position: 'fixed',
            top:  topPos + 'px',
            left: (btnRect.right - 180) + 'px',
            zIndex: 99999
        });
    }
});
$(document).on('click', function(e) {
    if (!$(e.target).closest('.jc-action-wrap').length) {
        $('.jc-drop-menu.open').removeClass('open');
    }
});
</script>
