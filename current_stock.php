<?php
session_start();
include_once "connect_db.php";
include_once "page_guard.php";
?>
<style>
/* ── Reset & Base ── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.sih-wrap {
  font-family: 'Inter', 'Segoe UI', sans-serif;
  background: #f1f5f9;
  min-height: 100vh;
  padding: 24px;
  color: #1e293b;
}

/* ── Header Card ── */
.sih-header-card {
  background: linear-gradient(135deg, #e2e8f0 0%, #eef2ff 50%, #e0f2fe 100%);
  border-radius: 20px;
  padding: 24px 28px;
  display: flex;
  align-items: center;
  gap: 32px;
  flex-wrap: wrap;
  margin-bottom: 24px;
  border: 1px solid rgba(44, 62, 107, 0.12);
  box-shadow: 0 4px 24px rgba(30, 41, 59, 0.07);
}

.sih-header-icon {
  width: 56px; height: 56px;
  background: linear-gradient(135deg, #2c3e6b, #3b5998);
  border-radius: 16px;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 16px rgba(44, 62, 107, 0.35);
  flex-shrink: 0;
}
.sih-header-icon .material-icons-round { color: #fff; font-size: 26px; }

.sih-header-text { flex: 1; min-width: 160px; }
.sih-header-text h2 {
  font-size: 1.55rem; font-weight: 700; color: #1e293b; line-height: 1.2;
}
.sih-header-text p {
  font-size: 0.82rem; color: #6b7280; margin-top: 3px;
}

/* ── Stat Tiles ── */
.sih-stats {
  display: flex; gap: 12px; flex-wrap: wrap; margin-left: auto;
}

.sih-stat {
  background: #fff;
  border-radius: 14px;
  padding: 14px 20px;
  min-width: 120px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.06);
  border: 1px solid rgba(0,0,0,0.05);
  display: flex; align-items: center; gap: 12px;
}
.sih-stat-icon {
  width: 36px; height: 36px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.sih-stat-icon.purple { background: #e0e7ff; }
.sih-stat-icon.purple .material-icons-round { color: #4f46e5; font-size: 18px; }
.sih-stat-icon.blue   { background: #dbeafe; }
.sih-stat-icon.blue   .material-icons-round { color: #2563eb; font-size: 18px; }
.sih-stat-icon.amber  { background: #fef3c7; }
.sih-stat-icon.amber  .material-icons-round { color: #d97706; font-size: 18px; }
.sih-stat-icon.green  { background: #d1fae5; }
.sih-stat-icon.green  .material-icons-round { color: #059669; font-size: 18px; }

.sih-stat-info { line-height: 1.2; }
.sih-stat-val {
  font-size: 1.25rem; font-weight: 700; color: #1e293b;
}
.sih-stat-lbl {
  font-size: 0.71rem; color: #9ca3af; font-weight: 500; white-space: nowrap;
}

/* ── Table Card ── */
.sih-table-card {
  background: #fff;
  border-radius: 10px;
  padding: 22px 24px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
  border: 1px solid #e2e8f0;
}

/* ── Toolbar ── */
.sih-toolbar {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 22px;
}

.sih-length-wrap {
  display: flex; align-items: center; gap: 8px;
  font-size: 0.82rem; color: #6b7280;
}
.sih-length-wrap select {
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  padding: 5px 10px;
  font-size: 0.82rem;
  color: #374151;
  background: #fafafa;
  cursor: pointer;
  outline: none;
  transition: border-color .2s;
}
.sih-length-wrap select:focus { border-color: #3b82f6; }

.sih-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 16px;
  border-radius: 10px;
  border: none;
  font-size: 0.82rem; font-weight: 600;
  cursor: pointer;
  transition: opacity .2s, transform .1s;
}
.sih-btn:hover { opacity: .87; transform: translateY(-1px); }
.sih-btn:active { transform: translateY(0); }
.sih-btn .material-icons-round { font-size: 15px; }

.sih-btn-excel {
  background: #22c55e;
  color: #fff;
  box-shadow: 0 3px 10px rgba(34,197,94,.3);
}
.sih-btn-pdf {
  background: linear-gradient(135deg, #f87171, #fb923c);
  color: #fff;
  box-shadow: 0 3px 10px rgba(248,113,113,.3);
}
.sih-btn-print {
  background: #fff;
  color: #374151;
  border: 1.5px solid #e5e7eb;
  box-shadow: 0 2px 6px rgba(0,0,0,.06);
}

.sih-search-wrap {
  margin-left: auto;
  position: relative;
}
.sih-search-wrap .material-icons-round {
  position: absolute; left: 11px; top: 50%;
  transform: translateY(-50%);
  color: #9ca3af; font-size: 18px; pointer-events: none;
}
.sih-search-wrap input {
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  padding: 8px 14px 8px 36px;
  font-size: 0.82rem;
  color: #374151;
  width: 240px;
  outline: none;
  transition: border-color .2s, box-shadow .2s;
  background: #fafafa;
}
.sih-search-wrap input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59,130,246,.15);
}

/* ── Table ── */
#sih-result-area { min-height: 160px; }

table#resultDetailTable {
  width: 100% !important;
  border-collapse: collapse;
  font-size: 0.95rem;
}

table#resultDetailTable thead tr {
  background: #2c3e6b;
}
table#resultDetailTable thead th {
  padding: 10px 8px !important;
  font-size: 0.82rem;
  font-weight: 700;
  color: #ffffff;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  border: none !important;
  border-right: 1px solid rgba(255,255,255,0.1) !important;
  white-space: nowrap;
}
table#resultDetailTable thead th .sih-th-inner {
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
table#resultDetailTable thead th .sih-th-icon {
  font-size: 15px;
  color: #ffffff;
  flex-shrink: 0;
}
table#resultDetailTable thead th:first-child { border-radius: 0; }
table#resultDetailTable thead th:last-child  { border-radius: 0; border-right: none !important; }

table#resultDetailTable tbody tr {
  border-bottom: 1px solid #f1f5f9;
  transition: background .15s;
}
table#resultDetailTable tbody tr:hover { background: #f0f4ff; }
table#resultDetailTable tbody tr:nth-child(even) { background: #fafbfc; }
table#resultDetailTable tbody td {
  padding: 8px 7px !important;
  font-size: 0.82rem;
  font-weight: 500;
  border: none !important;
  border-bottom: 1px solid #f1f5f9 !important;
  vertical-align: middle;
  color: #1e293b;
}

/* Row number badge */
.sih-row-num {
  display: inline-flex; align-items: center; justify-content: center;
  width: 26px; height: 26px;
  border-radius: 7px;
  font-size: 0.82rem;
  font-weight: 700;
}

/* Stock badge */
.sih-stock-badge {
  display: inline-block;
  padding: 3px 12px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.82rem;
}
.sih-stock-ok   { background: #e0e7ff; color: #4f46e5; }
.sih-stock-high { background: #d1fae5; color: #059669; }
.sih-stock-low  { background: #fee2e2; color: #dc2626; }
.sih-stock-mid  { background: #e0f2fe; color: #0369a1; }

/* Low stock warning badge */
.sih-warn-badge {
  display: inline-flex; align-items: center; gap: 5px;
  background: #fff0f0;
  color: #dc2626;
  border: 1px solid #fecaca;
  border-radius: 20px;
  padding: 3px 12px;
  font-size: 0.76rem;
  font-weight: 600;
}
.sih-warn-badge .material-icons-round { font-size: 12px; }
.sih-dash { color: #d1d5db; }

/* ── Pagination ── */
.sih-pagination-wrap {
  display: flex; align-items: center; justify-content: space-between;
  margin-top: 20px; flex-wrap: wrap; gap: 12px;
  font-size: 0.8rem; color: #9ca3af;
}
.sih-pages {
  display: flex; align-items: center; gap: 6px;
}
.sih-page-btn {
  width: 32px; height: 32px;
  border-radius: 50%;
  border: 1.5px solid #e5e7eb;
  background: #fff;
  color: #374151;
  font-size: 0.8rem; font-weight: 600;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: all .2s;
}
.sih-page-btn:hover { border-color: #3b82f6; color: #2563eb; }
.sih-page-btn.active {
  background: #2c3e6b;
  color: #fff; border-color: transparent;
  box-shadow: 0 3px 10px rgba(44,62,107,.35);
}
.sih-page-btn.arrow { color: #9ca3af; }
.sih-page-btn.arrow:hover { color: #2563eb; border-color: #3b82f6; }

/* DataTables overrides — hide default UI, we render our own */
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate,
.dt-buttons { display: none !important; }

/* Loading state */
.sih-loading {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center; gap: 14px; padding: 60px 0; color: #9ca3af;
}
.sih-spinner {
  width: 40px; height: 40px;
  border: 3px solid #e5e7eb;
  border-top-color: #2c3e6b;
  border-radius: 50%;
  animation: sih-spin .7s linear infinite;
}
@keyframes sih-spin { to { transform: rotate(360deg); } }

@media (max-width: 700px) {
  .sih-stats { gap: 8px; }
  .sih-stat { min-width: 100px; padding: 10px 12px; }
  .sih-search-wrap input { width: 180px; }
}
</style>

<div class="sih-wrap">

  <!-- ── Header ── -->
  <div class="sih-header-card">
    <div class="sih-header-icon">
      <span class="material-icons-round">inventory_2</span>
    </div>
    <div class="sih-header-text">
      <h2>Stock In Hand</h2>
      <p>Real-time overview of available stock across all materials</p>
    </div>
    <div class="sih-stats">
      <div class="sih-stat">
        <div class="sih-stat-icon purple">
          <span class="material-icons-round">category</span>
        </div>
        <div class="sih-stat-info">
          <div class="sih-stat-val" id="stat-total-materials">—</div>
          <div class="sih-stat-lbl">Total Materials</div>
        </div>
      </div>
      <div class="sih-stat">
        <div class="sih-stat-icon blue">
          <span class="material-icons-round">layers</span>
        </div>
        <div class="sih-stat-info">
          <div class="sih-stat-val" id="stat-total-stock">—</div>
          <div class="sih-stat-lbl">Total Stock</div>
        </div>
      </div>
      <div class="sih-stat">
        <div class="sih-stat-icon amber">
          <span class="material-icons-round">warning_amber</span>
        </div>
        <div class="sih-stat-info">
          <div class="sih-stat-val" id="stat-low-stock">—</div>
          <div class="sih-stat-lbl">Low Stock Items</div>
        </div>
      </div>
      <div class="sih-stat">
        <div class="sih-stat-icon green">
          <span class="material-icons-round">trending_up</span>
        </div>
        <div class="sih-stat-info">
          <div class="sih-stat-val" id="stat-availability">—</div>
          <div class="sih-stat-lbl">Stock Availability</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Table Card ── -->
  <div class="sih-table-card">

    <!-- Toolbar -->
    <div class="sih-toolbar">
      <div class="sih-length-wrap">
        <span>Show</span>
        <select id="sih-page-size">
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
          <option value="150">150</option>
          <option value="-1" selected>All</option>
        </select>
        <span>entries</span>
      </div>
      <button class="sih-btn sih-btn-excel" id="sih-export-excel">
        <span class="material-icons-round">table_view</span> Export Excel
      </button>
      <button class="sih-btn sih-btn-pdf" id="sih-export-pdf">
        <span class="material-icons-round">picture_as_pdf</span> Export PDF
      </button>
      <button class="sih-btn sih-btn-print" id="sih-print">
        <span class="material-icons-round">print</span> Print Table
      </button>
      <div class="sih-search-wrap">
        <span class="material-icons-round">search</span>
        <input type="text" id="sih-search" placeholder="Search material...">
      </div>
    </div>

    <!-- Result / Table -->
    <div id="sih-result-area">
      <div class="sih-loading">
        <div class="sih-spinner"></div>
        <span>Loading stock data…</span>
      </div>
    </div>

    <!-- Pagination -->
    <div class="sih-pagination-wrap" id="sih-pagination-wrap" style="display:none">
      <span id="sih-info-text"></span>
      <div class="sih-pages" id="sih-pages"></div>
    </div>

  </div><!-- /.sih-table-card -->
</div><!-- /.sih-wrap -->

<script>
$(document).ready(function () {
  SetUpBasics();
  fnSearch();

  // Page size change
  $('#sih-page-size').on('change', function () {
    if (sihDT) {
      sihDT.page.len(parseInt(this.value)).draw();
      renderPagination();
    }
  });

  // Search
  $('#sih-search').on('input', function () {
    if (sihDT) { sihDT.search(this.value).draw(); renderPagination(); }
  });

  // Export Excel
  $('#sih-export-excel').on('click', function () {
    if (sihDT) sihDT.button('.buttons-excel').trigger();
  });

  // Export PDF
  $('#sih-export-pdf').on('click', function () {
    if (sihDT) sihDT.button('.buttons-pdf').trigger();
  });

  // Print
  $('#sih-print').on('click', function () {
    if (sihDT) sihDT.button('.buttons-print').trigger();
  });
});

var sihDT = null;

function fnSearch() {
  $.ajax({
    type: "POST",
    url: "api/get_current_stock.php",
    success: function (response) {
      if (response !== "") {
        $('#sih-result-area').html(response);

        // Add our styled wrapper around the raw table returned by API
        styleRawTable();

        sihDT = $('#resultDetailTable').DataTable({
          lengthMenu: [[25, 50, 100, 150, -1], [25, 50, 100, 150, "All"]],
          pageLength: -1,
          language: { emptyTable: 'No data available in table' },
          dom: 'lBfrtip',
          buttons: [
            {
              extend: 'excelHtml5',
              text: 'Export to Excel',
              title: 'Current Stock',
              className: 'buttons-excel'
            },
            {
              extend: 'pdfHtml5',
              text: 'Export to PDF',
              className: 'buttons-pdf',
              orientation: 'portrait',
              pageSize: 'A4',
              title: 'Current Stock'
            },
            {
              extend: 'print',
              text: 'Print Table',
              className: 'buttons-print',
              customize: function (win) {
                $(win.document.body).css('font-size', '14px');
                $(win.document.body).find('table')
                  .css('border-collapse', 'collapse')
                  .find('thead th')
                  .css({ 'background-color': '#2c3e6b', 'color': 'white', 'padding': '8px' });
              }
            }
          ],
          drawCallback: function () {
            styleTableRows();
            renderPagination();
          }
        });

        computeStats(sihDT);

      } else {
        $('#sih-result-area').html('<p style="text-align:center;padding:40px;color:#9ca3af;">No data found.</p>');
        if (typeof toastr !== 'undefined') toastr.error("Failed to get details!!!");
      }
    }
  });
}

// Style the raw <table> returned from the API to apply our classes
function styleRawTable() {
  var $tbl = $('#resultDetailTable');
  if (!$tbl.length) return;

  // Hide Material Code column (col 2) in header and all rows
  $tbl.find('thead th:nth-child(2), tbody td:nth-child(2)').hide();

  // Column definitions: [icon, label] — only for visible columns
  var cols = [
    ['tag',           '#'],
    null, // Material Code — hidden
    ['label',         'Material Name'],
    ['inventory_2',   'Stock In Hand'],
    ['warning_amber', 'Low Stock Warning']
  ];

  $tbl.find('thead th').each(function (i) {
    if (cols[i]) {
      $(this).html(
        '<span class="sih-th-inner">' +
          '<span class="material-icons-round sih-th-icon">' + cols[i][0] + '</span>' +
          cols[i][1] +
        '</span>'
      );
    }
  });
}

// Add row badges and stock colour-coding after each draw
function styleTableRows() {
  var $tbody = $('#resultDetailTable tbody');

  // Keep Material Code column hidden after every redraw
  $tbody.find('td:nth-child(2)').hide();

  $tbody.find('tr').each(function (idx) {
    var $tr = $(this);

    // Col 1 — Row number badge
    var $numCell = $tr.find('td:nth-child(1)');
    var numText = $.trim($numCell.text());
    if (numText !== '' && !isNaN(numText)) {
      $numCell.html('<span class="sih-row-num">' + numText + '</span>');
    }

    // Col 4 — Stock In Hand badge
    var $stockCell = $tr.find('td:nth-child(4)');
    var stockText = $.trim($stockCell.text());
    var qty = parseFloat(stockText.replace(/,/g, ''));
    if (!isNaN(qty)) {
      var cls = qty >= 1000 ? 'sih-stock-high' : (qty < 100 ? 'sih-stock-low' : (qty < 500 ? 'sih-stock-mid' : 'sih-stock-ok'));
      $stockCell.html('<span class="sih-stock-badge ' + cls + '">' + stockText + '</span>');
    }

    // Col 5 — Low Stock Warning
    // PHP sets inline style="background-color:red;" when stock < 100
    var $warnCell = $tr.find('td:nth-child(5)');
    var isLow = $warnCell.attr('style') && $warnCell.attr('style').indexOf('red') !== -1;
    // Store as data attr BEFORE removing style so computeStats can still read it
    $tr.attr('data-low', isLow ? '1' : '0');
    $warnCell.removeAttr('style');
    if (isLow) {
      $warnCell.html('<span class="sih-warn-badge"><span class="material-icons-round">error</span> Order Now</span>');
    } else {
      $warnCell.html('<span class="sih-dash">—</span>');
    }
  });
}

// Compute header stats using DataTables API — reads ALL rows across all pages
function computeStats(dt) {
  var totalMaterials = 0, totalStock = 0, lowCount = 0;

  dt.rows().every(function () {
    var rowNode = this.node();
    totalMaterials++;

    // Col 4 — read raw stock text (before badge wrapping, so get innerText)
    var stockText = $('td:nth-child(4)', rowNode).text().replace(/,/g, '').trim();
    var qty = parseFloat(stockText) || 0;
    totalStock += qty;

    // Use qty directly for low-stock detection — < 100 matches PHP threshold
    if (qty > 0 && qty < 100) lowCount++;
  });

  var availability = totalMaterials > 0
    ? Math.round(((totalMaterials - lowCount) / totalMaterials) * 100)
    : 0;

  $('#stat-total-materials').text(totalMaterials.toLocaleString());
  $('#stat-total-stock').text(totalStock.toLocaleString());
  $('#stat-low-stock').text(lowCount);
  $('#stat-availability').text(availability + '%');
}

// Custom pagination renderer
function renderPagination() {
  if (!sihDT) return;

  var info      = sihDT.page.info();
  var current   = info.page;       // 0-indexed
  var totalPages = info.pages;
  var start     = info.start + 1;
  var end       = info.end;
  var total     = info.recordsDisplay;

  $('#sih-info-text').text('Showing ' + start + ' to ' + end + ' of ' + total + ' entries');

  var $pages = $('#sih-pages').empty();

  // Prev arrow
  var $prev = $('<button class="sih-page-btn arrow"><span class="material-icons-round" style="font-size:14px">chevron_left</span></button>');
  $prev.prop('disabled', current === 0).on('click', function () {
    sihDT.page('previous').draw('page'); renderPagination();
  });
  $pages.append($prev);

  // Page buttons (show up to 5)
  var start_p = Math.max(0, current - 2);
  var end_p   = Math.min(totalPages - 1, start_p + 4);
  for (var i = start_p; i <= end_p; i++) {
    (function (pg) {
      var $btn = $('<button class="sih-page-btn' + (pg === current ? ' active' : '') + '">' + (pg + 1) + '</button>');
      $btn.on('click', function () { sihDT.page(pg).draw('page'); renderPagination(); });
      $pages.append($btn);
    })(i);
  }

  // Next arrow
  var $next = $('<button class="sih-page-btn arrow"><span class="material-icons-round" style="font-size:14px">chevron_right</span></button>');
  $next.prop('disabled', current >= totalPages - 1).on('click', function () {
    sihDT.page('next').draw('page'); renderPagination();
  });
  $pages.append($next);

  $('#sih-pagination-wrap').show();
}
</script>