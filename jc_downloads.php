<?php
session_start();
include_once "connect_db.php";

if (empty($_SESSION['user_name'])) { header('Location: login.php'); exit; }

$user_type    = $_SESSION['user_type']        ?? 'OPERATOR';
$user_name    = $_SESSION['user_name']         ?? '';
$display_name = $_SESSION['user_display_name'] ?? $user_name;
$is_admin     = in_array($user_type, ['ADMIN','SUPERADMIN']);

$all_users = [];
if ($is_admin) {
    $ur = $connection->query("SELECT user_name, user_display_name FROM user_master ORDER BY user_display_name");
    if ($ur) while ($uw = $ur->fetch_assoc()) $all_users[] = $uw;
}

// base64-embed the logo so it works from any path
$logo_b64 = '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';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Citizen Prints — JC Design Files</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>

<style>
/* ═══════════════════════════════════════
   DESIGN TOKENS
═══════════════════════════════════════ */
:root {
    --primary:       #6366f1;
    --primary-dark:  #4f46e5;
    --primary-light: #eef2ff;
    --accent:        #f43f5e;
    --accent-dark:   #e11d48;
    --accent-light:  #fff1f3;
    --bg:            #f1f5f9;
    --card:          #ffffff;
    --border:        #e2e8f0;
    --border-m:      #cbd5e1;
    --text:          #0f172a;
    --text-s:        #475569;
    --text-m:        #94a3b8;
    --navy:          #0f172a;
    --navy2:         #1e293b;
    --navy3:         #334155;

    /* Semantic */
    --emerald:       #10b981;
    --emerald-bg:    #d1fae5;
    --amber:         #f59e0b;
    --amber-bg:      #fef3c7;
    --amber-dark:    #92400e;
    --rose:          #f43f5e;
    --rose-bg:       #fff1f3;
    --purple:        #8b5cf6;
    --purple-bg:     #ede9fe;
    --green:         #22c55e;
    --green-bg:      #dcfce7;
    --green-dark:    #15803d;

    /* Layout */
    --radius-xs:  4px;
    --radius-sm:  8px;
    --radius:     12px;
    --radius-lg:  16px;
    --font:       'Inter', 'Nunito', sans-serif;
    --topbar-h:   60px;

    --shadow-sm:  0 1px 3px rgba(15,23,42,0.07), 0 1px 2px rgba(15,23,42,0.05);
    --shadow:     0 4px 16px rgba(15,23,42,0.10), 0 1px 4px rgba(15,23,42,0.06);
    --shadow-lg:  0 10px 40px rgba(15,23,42,0.16), 0 2px 8px rgba(15,23,42,0.08);
    --shadow-ind: 0 4px 18px rgba(99,102,241,0.32);
    --shadow-acc: 0 4px 18px rgba(244,63,94,0.30);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body {
    font-family: var(--font);
    background: var(--bg);
    color: var(--text);
    font-size: 14px;
    line-height: 1.55;
    min-height: 100vh;
    -webkit-font-smoothing: antialiased;
}

/* ═══════════════════════════════════════
   TOP BAR
═══════════════════════════════════════ */
.topbar {
    position: sticky; top: 0; z-index: 400;
    height: var(--topbar-h);
    background: var(--navy);
    border-bottom: 2px solid var(--primary);
    display: flex; align-items: center;
    justify-content: space-between;
    padding: 0 24px;
    gap: 16px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.40);
}

.tb-left  { display: flex; align-items: center; gap: 14px; }
.tb-right { display: flex; align-items: center; gap: 10px; }

/* Logo */
.tb-logo {
    width: 40px; height: 40px; border-radius: 50%;
    border: 2px solid rgba(99,102,241,0.50);
    overflow: hidden; flex-shrink: 0;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.18);
}
.tb-logo img { width: 100%; height: 100%; object-fit: cover; display: block; }

/* Brand */
.tb-brand { display: flex; flex-direction: column; line-height: 1.2; }
.tb-brand .b1 {
    font-size: 0.98rem; font-weight: 800;
    color: #ffffff;
    letter-spacing: 0.4px;
    text-transform: uppercase;
}
.tb-brand .b2 {
    font-size: 0.63rem; font-weight: 700;
    color: var(--primary);
    letter-spacing: 1.8px;
    text-transform: uppercase;
}

.tb-sep { width: 1px; height: 28px; background: rgba(255,255,255,0.12); }

/* Page title */
.tb-page {
    font-size: 0.8rem; font-weight: 600;
    color: rgba(255,255,255,0.65);
    letter-spacing: 0.2px;
    display: flex; align-items: center; gap: 7px;
}
.tb-page i { color: var(--primary); font-size: 0.82rem; }

/* User badge */
.tb-user {
    display: flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 22px;
    padding: 4px 12px 4px 5px;
}
.tb-avatar {
    width: 26px; height: 26px; border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    display: flex; align-items: center; justify-content: center;
    font-size: 0.68rem; font-weight: 800; color: #fff;
    flex-shrink: 0;
}
.tb-user .u-name {
    font-size: 0.77rem; font-weight: 700;
    color: #ffffff;
}
.tb-user .u-role {
    font-size: 0.60rem; font-weight: 600;
    color: rgba(255,255,255,0.45);
    text-transform: uppercase; letter-spacing: 0.5px;
}

/* Back button */
.btn-back {
    display: inline-flex; align-items: center; gap: 7px;
    height: 34px; padding: 0 15px;
    background: rgba(255,255,255,0.08);
    border: 1.5px solid rgba(255,255,255,0.18);
    border-radius: var(--radius-sm);
    color: #ffffff;
    font-size: 0.78rem; font-weight: 700;
    font-family: var(--font);
    text-decoration: none; letter-spacing: 0.2px;
    transition: all 0.18s; white-space: nowrap;
}
.btn-back:hover {
    background: var(--primary);
    border-color: var(--primary);
    color: #fff;
    box-shadow: var(--shadow-ind);
    transform: translateX(-2px);
}
.btn-back i { font-size: 0.70rem; }

/* ═══════════════════════════════════════
   QUOTA BAR
═══════════════════════════════════════ */
.quota-bar {
    background: var(--navy2);
    border-bottom: 1px solid rgba(99,102,241,0.20);
    padding: 8px 24px;
    display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
}
.q-label {
    font-size: 0.67rem; font-weight: 700;
    color: rgba(255,255,255,0.45);
    text-transform: uppercase; letter-spacing: 1px;
    white-space: nowrap;
}
.q-count-text {
    font-size: 0.74rem; font-weight: 600;
    color: rgba(255,255,255,0.70);
    white-space: nowrap;
    display: flex; align-items: center; gap: 4px;
}
.q-count-text strong {
    font-weight: 800;
    color: #ffffff;
}
.q-numbers {
    display: flex; align-items: baseline; gap: 3px;
}
.q-used  { font-size: 1.05rem; font-weight: 800; color: #fff; font-family: var(--font); }
.q-slash { font-size: 0.72rem; color: rgba(255,255,255,0.30); }
.q-max   { font-size: 0.74rem; color: rgba(255,255,255,0.40); }
.q-track {
    flex: 1; min-width: 100px; max-width: 200px;
    height: 6px; background: rgba(255,255,255,0.10);
    border-radius: 3px; overflow: hidden;
}
.q-fill {
    height: 100%; border-radius: 3px;
    background: linear-gradient(90deg, var(--primary), var(--accent));
    transition: width 0.6s cubic-bezier(0.34,1.56,0.64,1), background 0.4s;
}
.q-fill.warn   { background: linear-gradient(90deg, var(--amber), #d97706); }
.q-fill.danger { background: linear-gradient(90deg, var(--accent), var(--accent-dark)); }
.q-note {
    font-size: 0.71rem; font-weight: 600;
    color: rgba(255,255,255,0.55);
    white-space: nowrap;
}
.q-reset {
    font-size: 0.61rem; font-weight: 600;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.10);
    border-radius: 20px; padding: 2px 9px;
    color: rgba(255,255,255,0.35);
    white-space: nowrap; letter-spacing: 0.3px;
}

/* ═══════════════════════════════════════
   SEARCH CARD / PANEL
═══════════════════════════════════════ */
.search-card {
    background: var(--card);
    border-bottom: 1px solid var(--border);
    padding: 16px 24px;
    box-shadow: var(--shadow-sm);
}
.search-inner {
    display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;
}
.sb-group { display: flex; flex-direction: column; gap: 5px; }
.sb-group label {
    font-size: 0.60rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 1px;
    color: var(--text-m);
    padding-left: 1px;
}
.sb-group input,
.sb-group select {
    height: 38px; padding: 0 12px;
    background: var(--bg);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 0.83rem; font-family: var(--font);
    color: var(--text); font-weight: 500;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
}
.sb-group input:focus,
.sb-group select:focus {
    border-color: var(--primary);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
}
.sb-group input::placeholder { color: var(--text-m); font-weight: 400; }
.sb-search { flex: 1; min-width: 200px; position: relative; }
.sb-search input { width: 100%; padding-left: 36px; }
.sb-si {
    position: absolute; left: 11px; top: 50%;
    transform: translateY(-50%);
    color: var(--text-m); font-size: 0.78rem; pointer-events: none;
}

.sb-actions {
    display: flex; gap: 8px; align-items: center;
    padding-top: 18px; /* aligns with input bottom when labels present */
}

.btn-go {
    height: 38px; padding: 0 22px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: #fff; border: none;
    border-radius: var(--radius-sm);
    font-size: 0.83rem; font-weight: 700;
    font-family: var(--font); cursor: pointer;
    display: flex; align-items: center; gap: 7px;
    transition: opacity 0.15s, transform 0.12s, box-shadow 0.15s;
    letter-spacing: 0.2px; white-space: nowrap;
    box-shadow: var(--shadow-ind);
}
.btn-go:hover  { opacity: 0.92; transform: translateY(-1px); box-shadow: 0 6px 22px rgba(99,102,241,0.42); }
.btn-go:active { transform: translateY(0); opacity: 1; }

.btn-clr {
    width: 38px; height: 38px;
    background: var(--bg); border: 1.5px solid var(--border-m);
    border-radius: 50%;
    color: var(--text-m); cursor: pointer;
    font-size: 0.82rem; font-family: var(--font);
    display: flex; align-items: center; justify-content: center;
    transition: all 0.15s; flex-shrink: 0;
}
.btn-clr:hover {
    background: var(--rose-bg);
    border-color: var(--accent);
    color: var(--accent);
    transform: rotate(-30deg);
}

/* ═══════════════════════════════════════
   RESULTS TOOLBAR
═══════════════════════════════════════ */
.results-bar {
    padding: 14px 24px;
    display: flex; align-items: center;
    justify-content: space-between;
    gap: 12px; flex-wrap: wrap;
}
.rb-stats { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }

.stat-pill {
    display: inline-flex; align-items: center; gap: 7px;
    border-radius: 20px; padding: 5px 14px;
    font-size: 0.74rem; font-weight: 600;
    box-shadow: var(--shadow-sm);
}
.stat-pill .n {
    font-weight: 800; font-size: 0.88rem;
}
.stat-pill.pill-ind {
    background: var(--primary);
    color: #fff;
}
.stat-pill.pill-ind .n { color: #fff; }
.stat-pill.pill-rose {
    background: var(--accent);
    color: #fff;
}
.stat-pill.pill-rose .n { color: #fff; }

.hint-tag {
    font-size: 0.67rem; color: var(--text-m); font-weight: 500;
    display: flex; align-items: center; gap: 5px;
}
.hint-tag code {
    font-family: ui-monospace, 'JetBrains Mono', monospace;
    font-size: 0.64rem;
    background: var(--primary-light);
    color: var(--primary-dark);
    border-radius: 4px; padding: 1px 6px; font-weight: 700;
}

.view-toggle { display: flex; gap: 4px; }
.vt {
    width: 34px; height: 34px;
    border: 1.5px solid var(--border);
    background: var(--card);
    border-radius: var(--radius-sm);
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    color: var(--text-m); font-size: 0.78rem;
    transition: all 0.15s; box-shadow: var(--shadow-sm);
}
.vt:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
.vt.on    { background: var(--primary); border-color: var(--primary); color: #fff; box-shadow: var(--shadow-ind); }

/* ═══════════════════════════════════════
   STICKY CONTROLS
═══════════════════════════════════════ */
.sticky-controls {
    position: sticky;
    top: var(--topbar-h);
    z-index: 300;
}

/* ═══════════════════════════════════════
   CONTENT
═══════════════════════════════════════ */
.content { padding: 4px 24px 64px; }

/* ═══════════════════════════════════════
   JC GROUP CARD
═══════════════════════════════════════ */
.jc-group {
    background: var(--card);
    border: 1.5px solid var(--border);
    border-left: 4px solid var(--primary);
    border-radius: 14px;
    margin-bottom: 18px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    transition: box-shadow 0.22s, transform 0.18s;
}
.jc-group:hover {
    box-shadow: var(--shadow);
    transform: translateY(-2px);
}

/* Group header */
.jc-hdr {
    padding: 13px 18px;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    display: flex; align-items: center; flex-wrap: wrap; gap: 12px;
}
.jc-num {
    font-weight: 800; font-size: 0.78rem;
    background: var(--primary);
    color: #fff; border-radius: var(--radius-sm);
    padding: 4px 12px; letter-spacing: 0.4px;
    box-shadow: var(--shadow-ind); flex-shrink: 0;
    font-family: ui-monospace, monospace;
}
.jc-cust-block .jc-cname {
    font-weight: 800; font-size: 0.9rem;
    color: #ffffff; letter-spacing: 0.1px;
}
.jc-cust-block .jc-cphone {
    font-size: 0.70rem; font-weight: 500;
    color: rgba(255,255,255,0.50);
    margin-top: 2px;
}
.jc-hdr-right {
    margin-left: auto; display: flex; align-items: center;
    gap: 10px; flex-wrap: wrap;
}
.jc-file-pill {
    font-size: 0.67rem; font-weight: 700;
    background: rgba(99,102,241,0.20);
    border: 1px solid rgba(99,102,241,0.35);
    color: #a5b4fc;
    border-radius: 20px; padding: 3px 11px;
    letter-spacing: 0.3px;
}
.jc-meta-block {
    display: flex; flex-direction: column; align-items: flex-end; gap: 2px;
}
.jc-meta-block span {
    font-size: 0.66rem; font-weight: 500;
    color: rgba(255,255,255,0.40);
    letter-spacing: 0.2px;
}
.jc-meta-block .dl-stat {
    color: #fb7185; font-weight: 700; font-size: 0.67rem;
}

/* ═══════════════════════════════════════
   FILE GRID
═══════════════════════════════════════ */
.file-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 16px;
    padding: 16px 18px 20px;
    background: #f4f6fb;
    border-top: 1px solid var(--border);
}

/* File card */
.fc {
    background: var(--card);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    overflow: hidden; display: flex; flex-direction: column;
    transition: border-color 0.20s, transform 0.18s, box-shadow 0.20s;
    box-shadow: 0 2px 8px rgba(15,23,42,0.07);
}
.fc:hover {
    border-color: var(--primary);
    transform: translateY(-4px);
    box-shadow: 0 10px 34px rgba(99,102,241,0.16);
}

/* Preview panel — coloured per file type */
.fc-pre {
    height: 96px;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    overflow: hidden; position: relative; flex-shrink: 0;
    gap: 4px;
}
/* Default bg */
.fc-pre { background: linear-gradient(135deg,#f1f5f9 0%,#e2e8f0 100%); }
/* Per-type gradients */
.fc-pre.pre-pdf  { background: linear-gradient(135deg,#fef2f2 0%,#fecaca 100%); }
.fc-pre.pre-psd  { background: linear-gradient(135deg,#f5f3ff 0%,#ddd6fe 100%); }
.fc-pre.pre-ai,
.fc-pre.pre-eps  { background: linear-gradient(135deg,#fff7ed 0%,#fed7aa 100%); }
.fc-pre.pre-cdr,
.fc-pre.pre-cdt  { background: linear-gradient(135deg,#eff6ff 0%,#bfdbfe 100%); }
.fc-pre.pre-jpg,
.fc-pre.pre-jpeg { background: linear-gradient(135deg,#fefce8 0%,#fde68a 100%); }
.fc-pre.pre-png  { background: linear-gradient(135deg,#f0fdf4 0%,#bbf7d0 100%); }
.fc-pre.pre-zip,
.fc-pre.pre-rar,
.fc-pre.pre-7z   { background: linear-gradient(135deg,#f8fafc 0%,#cbd5e1 100%); }

.fc-pre img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform 0.30s;
    position: absolute; top: 0; left: 0;
}
.fc:hover .fc-pre img { transform: scale(1.07); }

/* File-type icon (non-image) */
.fc-ico-wrap {
    display: flex; flex-direction: column; align-items: center; gap: 3px;
}
.fc-ico-emoji { font-size: 2.2rem; line-height: 1; user-select: none; }
.fc-ico-ext {
    font-size: 0.60rem; font-weight: 900;
    letter-spacing: 1px; text-transform: uppercase;
    opacity: 0.55; font-family: ui-monospace, monospace;
}

/* Type badge (top-right corner) */
.fc-type {
    position: absolute; top: 7px; right: 7px;
    font-size: 0.56rem; font-weight: 800;
    letter-spacing: 0.06em; text-transform: uppercase;
    border-radius: var(--radius-xs); padding: 2px 6px;
    backdrop-filter: blur(4px);
}
.tp-pdf  { background: rgba(254,226,226,0.90); color: #b91c1c; }
.tp-psd  { background: rgba(237,233,254,0.90); color: var(--purple); }
.tp-ai,
.tp-eps  { background: rgba(255,237,213,0.90); color: #92400e; }
.tp-cdr,
.tp-cdt  { background: rgba(219,234,254,0.90); color: #1d4ed8; }
.tp-png  { background: rgba(220,252,231,0.90); color: var(--green-dark); }
.tp-jpg,
.tp-jpeg { background: rgba(254,249,195,0.90); color: #854d0e; }
.tp-zip,
.tp-rar,
.tp-7z   { background: rgba(226,232,240,0.90); color: #475569; }

/* Body */
.fc-body { padding: 11px 13px 8px; flex: 1; }
.fc-name {
    font-size: 0.80rem; font-weight: 700; color: var(--text);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    letter-spacing: 0.1px;
}
.fc-orig {
    font-size: 0.65rem; font-weight: 500; color: var(--text-m);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-top: 2px;
}
.fc-row {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: 8px; gap: 6px;
}
.fc-size {
    font-family: ui-monospace, monospace;
    font-size: 0.64rem; font-weight: 700;
    color: var(--text-s);
    background: var(--bg); border-radius: 4px;
    padding: 2px 6px;
}
.fc-date { font-size: 0.63rem; font-weight: 500; color: var(--text-m); }

/* Footer — two rows: Download full-width, then Rename+Delete side-by-side */
.fc-foot {
    display: flex; flex-direction: column; gap: 6px;
    padding: 10px 12px 12px;
    border-top: 1px solid var(--border);
    background: #f8faff;
}
.fc-act-row {
    display: flex; gap: 6px;
}
.fc-act-row .btn-action { flex: 1; }

/* Download button */
.btn-dl {
    width: 100%; height: 32px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: #fff; border: none;
    border-radius: var(--radius-sm);
    font-size: 0.75rem; font-weight: 700;
    font-family: var(--font); cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    letter-spacing: 0.2px;
    transition: opacity 0.15s, transform 0.12s, box-shadow 0.15s;
    box-shadow: 0 2px 8px rgba(99,102,241,0.30);
}
.btn-dl:hover  { opacity: 0.90; transform: translateY(-1px); box-shadow: 0 5px 16px rgba(99,102,241,0.44); }
.btn-dl:active { transform: translateY(0); opacity: 1; }
.btn-dl.spinning  { opacity: 0.55; cursor: not-allowed; pointer-events: none; }
.btn-dl.limit-hit {
    background: #e2e8f0; color: var(--text-m);
    cursor: not-allowed; pointer-events: none;
    box-shadow: none;
}

/* No-file / reason row */
.reason-row {
    margin: 0 18px 18px;
    padding: 13px 15px;
    background: var(--amber-bg);
    border: 1.5px solid #fde68a;
    border-left: 4px solid var(--amber);
    border-radius: 10px;
    font-size: 0.79rem; font-weight: 500; color: var(--amber-dark);
    display: flex; gap: 10px; align-items: flex-start; flex-wrap: wrap;
}

/* Action buttons (Rename / Delete / Upload) */
.btn-action {
    height: 30px; padding: 0 10px;
    border: none; border-radius: var(--radius-sm);
    font-size: 0.71rem; font-weight: 700;
    font-family: var(--font); cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center; gap: 5px;
    transition: all 0.15s; white-space: nowrap;
    letter-spacing: 0.15px;
}
.btn-action:hover  { transform: translateY(-1px); filter: brightness(1.10); }
.btn-action:active { transform: translateY(0); filter: none; }
.btn-upload { background: var(--emerald); color: #fff; box-shadow: 0 2px 7px rgba(16,185,129,0.30); }
.btn-rename { background: #f59e0b; color: #fff; box-shadow: 0 2px 7px rgba(245,158,11,0.28); }
.btn-del    { background: var(--accent); color: #fff; box-shadow: 0 2px 7px rgba(244,63,94,0.26); }

/* ═══════════════════════════════════════
   TABLE VIEW
═══════════════════════════════════════ */
.tbl-outer {
    overflow-x: auto;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
}

table.ftbl {
    width: 100%; border-collapse: separate; border-spacing: 0;
    font-size: 0.82rem; background: var(--card);
}
table.ftbl thead th {
    background: var(--navy);
    color: rgba(255,255,255,0.70);
    padding: 12px 16px;
    font-size: 0.64rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.9px;
    border-bottom: 2px solid var(--primary);
    white-space: nowrap;
    position: sticky; top: 0;
}
table.ftbl thead th:first-child { border-radius: var(--radius-lg) 0 0 0; }
table.ftbl thead th:last-child  { border-radius: 0 var(--radius-lg) 0 0; }

table.ftbl tbody tr { transition: background 0.12s; }
table.ftbl tbody tr:hover { background: #eef2ff; }
table.ftbl td {
    padding: 10px 16px; border-bottom: 1px solid var(--border);
    vertical-align: middle;
}
table.ftbl tbody tr:last-child td { border-bottom: none; }

.tbl-jc {
    font-weight: 800; font-size: 0.76rem;
    font-family: ui-monospace, monospace;
    background: var(--primary);
    color: #fff;
    border-radius: var(--radius-sm); padding: 3px 10px;
    display: inline-block; letter-spacing: 0.3px;
    box-shadow: 0 2px 6px rgba(99,102,241,0.28);
}
.tbl-cname  { font-weight: 700; font-size: 0.82rem; color: var(--text); }
.tbl-phone  { font-size: 0.70rem; font-weight: 500; color: var(--text-m); margin-top: 2px; }
.tbl-upby   { font-size: 0.67rem; font-weight: 500; color: var(--text-m); margin-top: 3px; }
.tbl-dlstat { font-size: 0.64rem; font-weight: 700; color: var(--accent); margin-top: 2px; }

.tbl-thumb {
    width: 40px; height: 40px; border-radius: var(--radius-sm);
    object-fit: cover; border: 1.5px solid var(--border); display: block;
}
.tbl-ico {
    width: 40px; height: 40px; border-radius: var(--radius-sm);
    background: var(--bg); border: 1.5px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
}
.tbl-fname { max-width: 170px; }
.tbl-fname .fn { font-weight: 700; font-size: 0.80rem; color: var(--text); display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.tbl-fname .fo { font-size: 0.64rem; font-weight: 500; color: var(--text-m); display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.tbl-sz { font-family: ui-monospace, monospace; font-size: 0.72rem; font-weight: 600; color: var(--text-s); white-space: nowrap; }
.tbl-dt { font-size: 0.72rem; font-weight: 500; color: var(--text-m); white-space: nowrap; }

.btn-tdl {
    height: 30px; padding: 0 13px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: #fff; border: none;
    border-radius: var(--radius-sm);
    font-size: 0.72rem; font-weight: 700;
    font-family: var(--font); cursor: pointer;
    display: inline-flex; align-items: center; gap: 5px;
    transition: all 0.15s; letter-spacing: 0.2px; white-space: nowrap;
    box-shadow: 0 2px 7px rgba(99,102,241,0.28);
}
.btn-tdl:hover  { opacity: 0.90; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(99,102,241,0.40); }
.btn-tdl.limit-hit  { background: var(--border); color: var(--text-m); cursor: not-allowed; pointer-events: none; box-shadow: none; }
.btn-tdl.spinning   { opacity: 0.60; cursor: not-allowed; pointer-events: none; }

/* table action cell */
.tbl-action-cell {
    display: flex; gap: 5px; align-items: center; flex-wrap: wrap;
    min-width: 160px; white-space: nowrap;
}

/* even-group row shading */
.tbl-even td { background: #f8faff; }

/* ═══════════════════════════════════════
   EMPTY / LOADING
═══════════════════════════════════════ */
.state-box {
    text-align: center; padding: 72px 24px;
    color: var(--text-m);
}
.state-box .s-ico {
    font-size: 3.4rem; opacity: 0.15;
    display: block; margin-bottom: 20px;
}
.state-box .s-title {
    font-size: 1rem; font-weight: 800;
    color: var(--text-s); margin-bottom: 8px;
}
.state-box .s-sub {
    font-size: 0.83rem; font-weight: 500;
    color: var(--text-m); max-width: 280px; margin: 0 auto; line-height: 1.65;
}

.spin-wrap { text-align: center; padding: 60px 24px; }
.spin-ring {
    display: inline-block; width: 36px; height: 36px;
    border: 3px solid var(--border);
    border-top-color: var(--primary);
    border-radius: 50%; animation: spin 0.65s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.spin-wrap p { margin-top: 14px; font-size: 0.84rem; font-weight: 600; color: var(--text-m); }

/* ═══════════════════════════════════════
   TOAST
═══════════════════════════════════════ */
.toast-pop {
    position: fixed; bottom: 24px; right: 24px; z-index: 9999;
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    color: #fff;
    border-radius: var(--radius);
    padding: 14px 20px 14px 16px;
    font-size: 0.83rem; font-weight: 700;
    box-shadow: 0 8px 36px rgba(99,102,241,0.45);
    display: none; max-width: 310px;
    border-left: 4px solid rgba(255,255,255,0.35);
    animation: slideUp 0.28s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}

/* ═══════════════════════════════════════
   JQ-UI OVERRIDE
═══════════════════════════════════════ */
.ui-datepicker { font-family: var(--font) !important; font-size: 0.82rem !important; border-radius: var(--radius) !important; }
.ui-state-highlight,
.ui-widget-content .ui-state-highlight {
    background: var(--primary) !important;
    color: #fff !important;
    border-color: var(--primary-dark) !important;
}

/* ═══════════════════════════════════════
   SCROLLBAR
═══════════════════════════════════════ */
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: var(--bg); }
::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 4px; opacity: 0.5; }
::-webkit-scrollbar-thumb:hover { background: var(--primary-dark); }

/* ═══════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════ */
@media (max-width: 640px) {
    .topbar, .quota-bar, .search-card, .results-bar, .content {
        padding-left: 14px; padding-right: 14px;
    }
    .search-inner {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
    .sb-search { grid-column: 1 / -1; }
    .sb-actions { grid-column: 1 / -1; justify-content: flex-end; }
    .file-grid {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 10px; padding: 12px;
    }
    .tb-brand .b2, .tb-sep, .tb-page { display: none; }
}
</style>
</head>
<body>

<!-- ═══════════════════════════════════════════
     TOP BAR
═══════════════════════════════════════════ -->
<header class="topbar">
    <div class="tb-left">
        <div class="tb-logo">
            <img src="data:image/jpeg;base64,<?= $logo_b64 ?>" alt="Citizen Prints"/>
        </div>
        <div class="tb-brand">
            <span class="b1">Citizen Prints</span>
            <span class="b2">JC Design Files</span>
        </div>
        <div class="tb-sep"></div>
        <div class="tb-page">
            <i class="fas fa-folder-open"></i>
            Design File Download Centre
        </div>
    </div>
    <div class="tb-right">
        <div class="tb-user">
            <div class="tb-avatar"><?= strtoupper(substr($display_name,0,1)) ?></div>
            <div>
                <div class="u-name"><?= htmlspecialchars($display_name) ?></div>
                <div class="u-role"><?= htmlspecialchars($user_type) ?></div>
            </div>
        </div>
        <a href="index.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>
</header>

<!-- ═══════════════════════════════════════════
     STICKY CONTROLS
═══════════════════════════════════════════ -->
<div class="sticky-controls">

    <!-- Quota bar -->
    <div class="quota-bar">
        <span class="q-label"><i class="fas fa-download me-1"></i>Downloads Today</span>
        <div class="q-numbers">
            <span class="q-used" id="qUsed">—</span>
            <span class="q-slash">/</span>
            <span class="q-max">15</span>
        </div>
        <div class="q-track"><div class="q-fill" id="qFill" style="width:0%"></div></div>
        <span class="q-count-text"><strong id="qUsed2">—</strong> / 15 downloads used today</span>
        <span class="q-note" id="qNote">Loading…</span>
        <span class="q-reset"><i class="fas fa-clock me-1"></i>Resets at midnight</span>
    </div>

    <!-- Search card -->
    <div class="search-card">
        <div class="search-inner">
            <div class="sb-group sb-search">
                <label>Search</label>
                <i class="fas fa-magnifying-glass sb-si"></i>
                <input type="text" id="searchTxt"
                       placeholder="JC no, customer name or mobile…"
                       onkeydown="if(event.key==='Enter') doSearch()"/>
            </div>
            <div class="sb-group">
                <label>From Date</label>
                <input type="text" id="fromDate" class="datepicker" placeholder="dd-mm-yyyy" style="width:128px;"/>
            </div>
            <div class="sb-group">
                <label>To Date</label>
                <input type="text" id="toDate" class="datepicker" placeholder="dd-mm-yyyy" style="width:128px;"/>
            </div>
            <?php if ($is_admin): ?>
            <div class="sb-group">
                <label>Uploaded By</label>
                <select id="userFilter" style="width:148px;">
                    <option value="all">All Users</option>
                    <?php foreach ($all_users as $u): ?>
                    <option value="<?= htmlspecialchars($u['user_name']) ?>"><?= htmlspecialchars($u['user_display_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="sb-actions">
                <button class="btn-go" onclick="doSearch()">
                    <i class="fas fa-magnifying-glass"></i> Search
                </button>
                <button class="btn-clr" onclick="clearSearch()" title="Reset filters">
                    <i class="fas fa-rotate-left"></i>
                </button>
            </div>
        </div>
    </div>

</div><!-- /sticky-controls -->

<!-- ═══════════════════════════════════════════
     RESULTS TOOLBAR
═══════════════════════════════════════════ -->
<div class="results-bar">
    <div class="rb-stats">
        <div class="stat-pill pill-ind">
            <i class="fas fa-folder" style="font-size:0.68rem;opacity:0.85;"></i>
            <span class="n" id="statJCs">0</span> Job Cards
        </div>
        <div class="stat-pill pill-rose">
            <i class="fas fa-file" style="font-size:0.68rem;opacity:0.85;"></i>
            <span class="n" id="statFiles">0</span> Files
        </div>
        <div class="hint-tag">
            <i class="fas fa-info-circle" style="font-size:0.68rem;"></i>
            Saved as <code>CustomerName_JC{no}_{date}.ext</code>
        </div>
    </div>
    <div class="view-toggle">
        <button class="vt on" id="btnGrid"  onclick="setView('grid')"  title="Grid view"><i class="fas fa-grip"></i></button>
        <button class="vt"    id="btnTable" onclick="setView('table')" title="List view"><i class="fas fa-list"></i></button>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════════════ -->
<div class="content" id="mainContent">
    <div class="state-box">
        <span class="s-ico"><i class="fas fa-folder-open"></i></span>
        <div class="s-title">No files loaded yet</div>
        <div class="s-sub">Select a date range and click <strong>Search</strong> to load all JC design files.</div>
    </div>
</div>

<!-- Hidden file input for upload from this page -->
<input type="file" id="jcd_file_input" multiple style="display:none;"
       accept=".pdf,.psd,.jpeg,.jpg,.png,.zip,.rar,.7z,.cdr,.cdt,.ai,.eps"
       onchange="jcd_doUpload(this)" />

<!-- Toast -->
<div class="toast-pop" id="dlToast">
    <i class="fas fa-ban me-2"></i><strong>Daily limit reached!</strong><br>
    <span style="font-size:0.76rem;opacity:0.9;font-weight:500;">You've used all 15 downloads today. Resets at midnight.</span>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
var qRem   = 15, qUsed = 0;
var view   = 'grid';
var last   = null;
var isAdmin = <?php echo $is_admin ? 'true' : 'false'; ?>;

$(function() {
    $('.datepicker').datepicker({ dateFormat:'dd-mm-yy' });
    var t = $.datepicker.formatDate('dd-mm-yy', new Date());
    $('#fromDate,#toDate').val(t);
    doSearch();
});

function setView(m) {
    view = m;
    $('#btnGrid').toggleClass('on', m==='grid');
    $('#btnTable').toggleClass('on', m==='table');
    if (last) renderAll(last);
}

/* ── Search ── */
function doSearch() {
    $('#mainContent').html('<div class="spin-wrap"><div class="spin-ring"></div><p>Loading files…</p></div>');
    $.ajax({
        type:'POST', url:'api/get_jc_downloads_list.php',
        data:{
            search_txt: $('#searchTxt').val(),
            from_date:  $('#fromDate').val(),
            to_date:    $('#toDate').val(),
            user_filter: $('#userFilter').length ? $('#userFilter').val() : 'all'
        },
        success:function(raw){
            try {
                var r = typeof raw==='string' ? JSON.parse(raw) : raw;
                last = r;
                setQuota(r.used_today, r.remaining_downloads, r.limit);
                $('#statJCs').text(r.total_jcs||0);
                $('#statFiles').text(r.total_files||0);
                renderAll(r);
            } catch(e) {
                $('#mainContent').html('<div class="state-box"><span class="s-ico">⚠️</span><div class="s-title">Parse error</div><div class="s-sub">Could not read server response.</div></div>');
            }
        },
        error:function(){
            $('#mainContent').html('<div class="state-box"><span class="s-ico">⚠️</span><div class="s-title">Server error</div><div class="s-sub">Please try again.</div></div>');
        }
    });
}

function clearSearch() {
    $('#searchTxt').val('');
    var t = $.datepicker.formatDate('dd-mm-yy', new Date());
    $('#fromDate,#toDate').val(t);
    if ($('#userFilter').length) $('#userFilter').val('all');
    doSearch();
}

/* ── Quota ── */
function setQuota(used, rem, limit) {
    qUsed = used; qRem = rem;
    var pct = limit>0 ? Math.round((used/limit)*100) : 0;
    $('#qUsed,#qUsed2').text(used);
    $('#qFill').css('width', pct+'%').removeClass('warn danger');
    if (pct>=100) $('#qFill').addClass('danger');
    else if (pct>=70) $('#qFill').addClass('warn');
    $('#qNote').text(rem>0 ? rem+' remaining today' : '⛔ Limit reached — resets midnight');
}

/* ── Render ── */
function renderAll(r) {
    if (!r.records||r.records.length===0) {
        $('#mainContent').html('<div class="state-box"><span class="s-ico"><i class="fas fa-folder-open"></i></span><div class="s-title">No files found</div><div class="s-sub">Try a different search or date range.</div></div>');
        return;
    }
    $('#mainContent').html(view==='grid' ? buildGrid(r.records) : buildTable(r.records));
}

/* ── GRID ── */
function buildGrid(records) {
    var h='';
    records.forEach(function(jc){
        var dt   = (jc.uploaded_at||'').substring(0,10);
        var fcnt = jc.files.length;
        h += '<div class="jc-group">';
        /* header */
        h += '<div class="jc-hdr">'
           + '<span class="jc-num">JC&nbsp;'+jc.jobcard_no+'</span>'
           + '<div class="jc-cust-block">'
           + '<div class="jc-cname">'+x(jc.customer_name||'—')+'</div>';
        if (jc.customer_mobile) h += '<div class="jc-cphone"><i class="fas fa-phone" style="font-size:0.58rem;margin-right:3px;"></i>'+x(jc.customer_mobile)+'</div>';
        h += '</div>'
           + '<div class="jc-hdr-right">';
        if (fcnt>0) h += '<span class="jc-file-pill"><i class="fas fa-file me-1"></i>'+fcnt+' '+(fcnt===1?'file':'files')+'</span>';
        h += '<div class="jc-meta-block">'
           + '<span>'+dt+'</span>'
           + '<span>by '+x(jc.uploaded_by)+'</span>';
        if (jc.download_count>0) h += '<span class="dl-stat">'+jc.download_count+' downloads</span>';
        h += '</div></div></div>';

        /* files */
        if (fcnt>0) {
            h += '<div class="file-grid">';
            jc.files.forEach(function(f){ h += buildFC(f,jc); });
            h += '</div>';
        }
        /* reason */
        if (fcnt===0) {
    var ua = 'data-jcno="'+jc.jobcard_no+'" data-cname="'+x(jc.customer_name||'')+'" data-mobile="'+x(jc.customer_mobile||'')+'"';
    h += '<div class="reason-row"><i class="fas fa-comment-dots" style="margin-top:2px;flex-shrink:0;"></i><div>'
       + (jc.upload_reason ? '<strong>No file uploaded.</strong> Reason: '+x(jc.upload_reason)
                           : '<span style="color:var(--text-m);">No file or reason recorded.</span>')
       + '</div>'
       + (isAdmin
           ? '<button class="btn-action btn-upload js-upload" style="flex-shrink:0;" '+ua+'>'
             + '<i class="fas fa-upload"></i> Upload File</button>'
           : '')
       + '</div>';
}
        h += '</div>';
    });
    return h;
}

function buildFC(f, jc) {
    var ext  = (f.file_type||'').toLowerCase();
    var img  = ['jpg','jpeg','png'].includes(ext);
    var disp = f.original_name||f.file_name||'—';
    var sz   = fmtSz(f.file_size_kb);

    var pre;
    if (img) {
        pre = '<div class="fc-pre pre-'+ext+'">'
            + '<img src="'+x(f.file_path)+'" loading="lazy" alt="'+x(disp)+'" onerror="this.parentNode.className+=\' pre-img-err\';this.style.display=\'none\';">'
            + '<span class="fc-type tp-'+ext+'">'+ext.toUpperCase()+'</span>'
            + '</div>';
    } else {
        pre = '<div class="fc-pre pre-'+ext+'">'
            + '<div class="fc-ico-wrap">'
            + '<span class="fc-ico-emoji">'+fmoji(ext)+'</span>'
            + '<span class="fc-ico-ext">'+ext.toUpperCase()+'</span>'
            + '</div>'
            + '<span class="fc-type tp-'+ext+'">'+ext.toUpperCase()+'</span>'
            + '</div>';
    }

    // Use data attributes instead of inline JS strings
    var da = 'data-uid="'+jc.upload_id+'" data-fname="'+x(f.file_name)+'" data-fpath="'+x(f.file_path)+'" data-ftype="'+ext+'" data-disp="'+x(disp)+'"';
    var ra = 'data-jcno="'+jc.jobcard_no+'" data-fname="'+x(f.file_name)+'" data-disp="'+x(disp)+'"';
    var da2= 'data-jcno="'+jc.jobcard_no+'" data-fname="'+x(f.file_name)+'"';

    return '<div class="fc">'
         + pre
         + '<div class="fc-body">'
         + '<div class="fc-name" title="'+x(disp)+'">'+x(disp)+'</div>'
         + '<div class="fc-orig" title="'+x(f.file_name)+'">'+x(f.file_name)+'</div>'
         + '<div class="fc-row">'
         + '<span class="fc-size">'+sz+'</span>'
         + '<span class="fc-date">'+((f.uploaded_at||'').substring(0,10))+'</span>'
         + '</div>'
         + '</div>'
         + '<div class="fc-foot">'
         + '<button class="btn-prev js-prev" '+da+'><i class="fas fa-eye"></i> Preview &amp; Download</button>'
         + '<div class="fc-act-row">'
         + '<button class="btn-action btn-rename js-rename" '+ra+'><i class="fas fa-pen"></i> Rename</button>'
         + '<button class="btn-action btn-del js-del" '+da2+'><i class="fas fa-trash-alt"></i> Delete</button>'
         + '</div>'
         + '</div></div>';
}

/* ── TABLE ── */
function buildTable(records) {
    var rows='';
    records.forEach(function(jc,gi){
        var ecls = gi%2===0?'':'tbl-even';
        if (jc.files.length===0) {
            rows+='<tr class="'+ecls+'">'
                +'<td><span class="tbl-jc">JC '+jc.jobcard_no+'</span>'
                +'<div class="tbl-cname" style="margin-top:5px;">'+x(jc.customer_name||'—')+'</div>'
                +'<div class="tbl-phone">'+x(jc.customer_mobile||'')+'</div></td>'
                +'<td colspan="5" style="color:var(--text-m);font-size:0.75rem;font-weight:600;font-style:italic;">'
                +(jc.upload_reason?'💬 '+x(jc.upload_reason):'No file uploaded')+'</td>'
                +'<td><div class="tbl-action-cell">'
                +(isAdmin
    ? '<button class="btn-action btn-upload js-upload" data-jcno="'+jc.jobcard_no+'" data-cname="'+x(jc.customer_name||'')+'" data-mobile="'+x(jc.customer_mobile||'')+'">'
      + '<i class="fas fa-upload"></i> Upload File</button>'
    : '<span style="font-size:0.72rem;color:var(--text-m);">No file</span>')
                +'</div></td></tr>';
            return;
        }
        jc.files.forEach(function(f,fi){
            var ext  = (f.file_type||'').toLowerCase();
            var img  = ['jpg','jpeg','png'].includes(ext);
            var disp = f.original_name||f.file_name||'—';
            var lim  = qRem<=0;
            var thb  = img
                ? '<img src="'+x(f.file_path)+'" class="tbl-thumb" loading="lazy" onerror="this.style.display=\'none\'">'
                : '<div class="tbl-ico">'+fmoji(ext)+'</div>';
            rows+='<tr class="'+ecls+'">';
            if (fi===0) {
                rows+='<td rowspan="'+jc.files.length+'" style="vertical-align:middle;border-right:2px solid var(--border);min-width:140px;">'
                    +'<span class="tbl-jc">JC '+jc.jobcard_no+'</span>'
                    +'<div class="tbl-cname" style="margin-top:5px;">'+x(jc.customer_name||'—')+'</div>'
                    +'<div class="tbl-phone">'+x(jc.customer_mobile||'')+'</div>'
                    +'<div class="tbl-upby">'+x(jc.uploaded_by)+' &middot; '+((jc.uploaded_at||'').substring(0,10))+'</div>'
                    +(jc.download_count>0?'<div class="tbl-dlstat">'+jc.download_count+' downloads</div>':'')
                    +'</td>';
            }
            var tPrevCall = 'trigPreview('+jc.upload_id+',\''+x(f.file_name)+'\',\''+x(f.file_path)+'\',\''+ext+'\',\''+x(disp)+'\')';
            var tda = 'data-uid="'+jc.upload_id+'" data-fname="'+x(f.file_name)+'" data-fpath="'+x(f.file_path)+'" data-ftype="'+ext+'" data-disp="'+x(disp)+'"';
var tra = 'data-jcno="'+jc.jobcard_no+'" data-fname="'+x(f.file_name)+'" data-disp="'+x(disp)+'"';
var tda2= 'data-jcno="'+jc.jobcard_no+'" data-fname="'+x(f.file_name)+'"';
            rows+='<td style="width:52px;">'+thb+'</td>'
                +'<td class="tbl-fname"><span class="fn" title="'+x(f.file_name)+'">'+x(disp)+'</span>'
                +'<span class="fo">'+x(f.file_name)+'</span></td>'
                +'<td><span class="fc-type tp-'+ext+'">'+ext.toUpperCase()+'</span></td>'
                +'<td class="tbl-sz">'+fmtSz(f.file_size_kb)+'</td>'
                +'<td class="tbl-dt">'+((f.uploaded_at||'').substring(0,10))+'</td>'
                +'<td><div class="tbl-action-cell">'
                +'<button class="btn-tprev js-prev" '+tda+'><i class="fas fa-eye"></i> Preview</button>'
+'<button class="btn-action btn-rename js-rename" '+tra+'><i class="fas fa-pen"></i> Rename</button>'
+'<button class="btn-action btn-del js-del" '+tda2+'><i class="fas fa-trash-alt"></i> Delete</button>'
                +'</div></td></tr>';
        });
    });
    return '<div class="tbl-outer"><table class="ftbl">'
          +'<thead><tr>'
          +'<th>Job Card</th><th></th><th>File Name</th>'
          +'<th>Type</th><th>Size</th><th>Date</th><th>Action</th>'
          +'</tr></thead><tbody>'+rows+'</tbody></table></div>';
}

/* ── Download ── */
function trigDL(uid, fname, btn) {
    if (qRem<=0) { showToast(); return; }
    var $b=$(btn);
    $b.addClass('spinning').html('<i class="fas fa-spinner fa-spin"></i> …');
    var a=document.createElement('a');
    a.href='api/download_jc_file.php?id='+uid+'&file='+encodeURIComponent(fname);
    a.style.display='none'; document.body.appendChild(a); a.click(); document.body.removeChild(a);
    setTimeout(function(){
        $.post('api/get_jc_downloads_list.php',{from_date:'all',to_date:'all',search_txt:''},function(raw){
            try {
                var r=typeof raw==='string'?JSON.parse(raw):raw;
                setQuota(r.used_today,r.remaining_downloads,r.limit);
                if (r.remaining_downloads>0) {
                    $b.removeClass('spinning').html('<i class="fas fa-download"></i> Download');
                } else {
                    $b.removeClass('spinning').addClass('limit-hit').html('<i class="fas fa-ban"></i> Limit');
                    showToast();
                    $('.btn-dl,.btn-tdl').addClass('limit-hit').html('<i class="fas fa-ban"></i> Limit');
                }
            } catch(e){ $b.removeClass('spinning').html('<i class="fas fa-download"></i> Download'); }
        });
    },1800);
}

/* ── Upload file from JC Downloads page ── */
var jcd_upload_jcno = 0, jcd_upload_name = '', jcd_upload_mobile = '';
function jcd_uploadFile(jcNo, custName, custMobile) {
    jcd_upload_jcno   = jcNo;
    jcd_upload_name   = custName;
    jcd_upload_mobile = custMobile;
    $('#jcd_file_input').val('').click();
}
function jcd_doUpload(input) {
    if (!input.files || input.files.length === 0) return;
    var fd = new FormData();
    fd.append('jobcard_no',      jcd_upload_jcno);
    fd.append('customer_name',   jcd_upload_name);
    fd.append('customer_mobile', jcd_upload_mobile);
    fd.append('upload_reason',   '');
    for (var i = 0; i < input.files.length; i++) {
        fd.append('design_files[]', input.files[i]);
    }
    alert('Uploading ' + input.files.length + ' file(s)… please wait.');
    $.ajax({
        type: 'POST',
        url:  'api/save_jc_design_upload.php',
        data: fd,
        processData: false,
        contentType: false,
        success: function(raw) {
            try {
                var r = typeof raw === 'string' ? JSON.parse(raw) : raw;
                if (r.status === 'success') {
                    alert('File(s) uploaded successfully!');
                    doSearch();
                } else {
                    alert('Upload failed: ' + (r.message || 'Unknown error'));
                }
            } catch(e) { alert('Unexpected response.'); }
        },
        error: function() { alert('Network error during upload. Please try again.'); }
    });
}

/* ── Admin: Rename file display name ── */
function jcd_rename(jcNo, fileName, currentName) {
    var newName = prompt('Rename file:', currentName);
    if (newName === null) return;
    newName = newName.trim();
    if (newName === '') { alert('Name cannot be empty.'); return; }
    $.post('api/rename_jc_design_file.php',
        { jobcard_no: jcNo, file_name: fileName, new_display_name: newName },
        function(raw) {
            try {
                var r = typeof raw === 'string' ? JSON.parse(raw) : raw;
                if (r.status === 'success') {
                    alert('File renamed successfully.');
                    doSearch();
                } else {
                    alert('Error: ' + (r.message || 'Rename failed.'));
                }
            } catch(e) { alert('Unexpected error.'); }
        }
    );
}

/* ── Admin: Delete file ── */
function jcd_delete(jcNo, fileName) {
    if (!confirm('Delete this file from JC #' + jcNo + '? This cannot be undone.')) return;
    $.post('api/delete_jc_design_file.php',
        { jobcard_no: jcNo, file_name: fileName },
        function(raw) {
            try {
                var r = typeof raw === 'string' ? JSON.parse(raw) : raw;
                if (r.status === 'success') {
                    alert('File deleted.');
                    doSearch();
                } else {
                    alert('Error: ' + (r.message || 'Delete failed.'));
                }
            } catch(e) { alert('Unexpected error.'); }
        }
    );
}

/* ── Helpers ── */
function showToast(){ $('#dlToast').fadeIn(250); setTimeout(function(){ $('#dlToast').fadeOut(380); },4500); }
function fmtSz(kb){ if(!kb) return '—'; return kb>1024?(kb/1024).toFixed(2)+' MB':kb+' KB'; }
function fmoji(e){ return {pdf:'📄',psd:'🎨',jpg:'🖼️',jpeg:'🖼️',png:'🖼️'}[e]||'📁'; }
function x(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

/* ── File Preview ── */
var _prevUID=0, _prevFname='', _prevBtn=null;

function trigPreview(uid, fname, fpath, ftype, disp) {
    _prevUID   = uid;
    _prevFname = fname;
    _prevBtn   = null;

    // Set header info
    $('#fprevTitle').text(disp || fname);
    $('#fprevType').text(ftype.toUpperCase());
    $('#fprevBody').html('');
    $('#fprevDlBtn').off('click').on('click', function(){
        trigDL(uid, fname, this);
    });

    var ext = (ftype||'').toLowerCase();
    var imgs = ['jpg','jpeg','png'];
    var body = '';

    if (imgs.includes(ext)) {
        // Image preview — click to zoom full-screen
        body = '<div class="fprev-img-wrap" id="fprevImgWrap">'
             + '<img id="fprevImg" src="'+fpath+'" alt="'+disp+'" '
             + 'onerror="$(\'#fprevImgWrap\').html(\'<div class=\\\'fprev-no\\\'><span>⚠️</span><p>Could not load image.</p></div>\')">'
             + '</div>'
             + '<p class="fprev-hint"><i class="fas fa-search-plus"></i> Click image to zoom</p>';
    } else if (ext === 'pdf') {
        // PDF preview via iframe
        body = '<div class="fprev-pdf-wrap">'
             + '<iframe src="'+fpath+'#toolbar=0&navpanes=0" frameborder="0" allowfullscreen></iframe>'
             + '</div>';
    } else {
        // Non-previewable file types
        var icons = {psd:'🎨',ai:'🎨',eps:'🎨',cdr:'🎨',cdt:'🎨',zip:'🗜️',rar:'🗜️','7z':'🗜️'};
        var ico   = icons[ext] || '📁';
        body = '<div class="fprev-no">'
             + '<span>'+ico+'</span>'
             + '<p><strong>'+ext.toUpperCase()+'</strong> files cannot be previewed in the browser.</p>'
             + '<p style="color:var(--text-m);font-size:0.82rem;">Click the Download button below to save the file.</p>'
             + '</div>';
    }

    $('#fprevBody').html(body);

    // Image zoom click handler
    if (imgs.includes(ext)) {
        $('#fprevBody').off('click','#fprevImg').on('click','#fprevImg', function(){
            $('#fprevZoomImg').attr('src', fpath);
            fnShowModal('fprevZoomModal');
        });
    }

    fnShowModal('fprevModal');
}

/* Safe modal opener — works with Bootstrap 5 JS bundle */
function fnShowModal(id) {
    var el = document.getElementById(id);
    if (!el) return;
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(el).show();
    } else {
        // Fallback: plain CSS show if BS JS somehow missing
        $(el).addClass('show').css({ display: 'block', 'padding-right': '15px' });
        $('body').addClass('modal-open');
        if (!$('.modal-backdrop').length) {
            $('<div class="modal-backdrop fade show"></div>').appendTo('body');
        }
        $(el).find('[data-bs-dismiss="modal"]').off('click.fbk').on('click.fbk', function(){
            $(el).removeClass('show').css('display','none');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        });
    }
}
</script>

<!-- ════════════════════════════════════════════
     FILE PREVIEW MODAL
════════════════════════════════════════════ -->
<style>
/* Preview modal */
#fprevModal .modal-dialog { max-width:860px; }
#fprevModal .modal-content { border-radius:14px; overflow:hidden; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.35); }
#fprevModal .modal-header  { background:linear-gradient(135deg,#1e293b,#334155); color:#fff; padding:14px 20px; border:none; }
#fprevModal .modal-body    { padding:0; background:#0f172a; }
#fprevModal .modal-footer  { background:#1e293b; border-top:1px solid #334155; padding:10px 16px; }
.fprev-title-row { display:flex; align-items:center; gap:10px; }
.fprev-ext-badge { background:#6366f1; color:#fff; font-size:0.65rem; font-weight:700;
                   padding:2px 8px; border-radius:20px; letter-spacing:.6px; }
#fprevTitle { font-size:0.95rem; font-weight:600; white-space:nowrap; overflow:hidden;
              text-overflow:ellipsis; max-width:600px; }

/* Image preview */
.fprev-img-wrap { display:flex; justify-content:center; align-items:center;
                  min-height:420px; max-height:72vh; overflow:auto; background:#0f172a; padding:16px; }
.fprev-img-wrap img { max-width:100%; max-height:68vh; border-radius:8px;
                      cursor:zoom-in; box-shadow:0 4px 24px rgba(0,0,0,0.5);
                      transition:transform .15s ease; }
.fprev-img-wrap img:hover { transform:scale(1.01); }
.fprev-hint { text-align:center; color:#64748b; font-size:0.72rem; padding:6px 0 10px;
              margin:0; background:#0f172a; }

/* PDF preview */
.fprev-pdf-wrap { height:75vh; background:#525659; }
.fprev-pdf-wrap iframe { width:100%; height:100%; display:block; border:none; }

/* Non-previewable */
.fprev-no { display:flex; flex-direction:column; align-items:center; justify-content:center;
            min-height:340px; color:#94a3b8; gap:12px; }
.fprev-no span { font-size:4rem; line-height:1; }
.fprev-no p { margin:0; font-size:0.92rem; text-align:center; }

/* Download button in modal */
.btn-fprev-dl { background:linear-gradient(135deg,#6366f1,#4f46e5); color:#fff;
                border:none; border-radius:8px; padding:9px 22px; font-size:0.85rem;
                font-weight:600; cursor:pointer; display:flex; align-items:center; gap:7px;
                transition:opacity .15s; }
.btn-fprev-dl:hover { opacity:.88; }
.btn-fprev-dl i { font-size:0.9rem; }

/* Zoom modal (full-screen image) */
#fprevZoomModal .modal-dialog { max-width:96vw; margin:16px auto; }
#fprevZoomModal .modal-content { background:#000; border:none; border-radius:10px; }
#fprevZoomModal .modal-body { padding:8px; display:flex; justify-content:center; align-items:center; }
#fprevZoomImg { max-width:100%; max-height:92vh; border-radius:6px; }

/* Preview button style */
.btn-prev {
    display:inline-flex; align-items:center; gap:6px;
    background:linear-gradient(135deg,#0ea5e9,#0284c7);
    color:#fff; border:none; border-radius:8px;
    padding:7px 16px; font-size:0.8rem; font-weight:600;
    cursor:pointer; transition:opacity .15s;
}
.btn-prev:hover { opacity:.88; }
.btn-tprev {
    display:inline-flex; align-items:center; gap:5px;
    background:linear-gradient(135deg,#0ea5e9,#0284c7);
    color:#fff; border:none; border-radius:6px;
    padding:4px 10px; font-size:0.75rem; font-weight:600;
    cursor:pointer; transition:opacity .15s; white-space:nowrap;
}
.btn-tprev:hover { opacity:.88; }
</style>

<!-- Preview Modal -->
<div class="modal fade" id="fprevModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fprev-title-row">
          <span class="fprev-ext-badge" id="fprevType">FILE</span>
          <span id="fprevTitle">Preview</span>
        </div>
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="fprevBody"></div>
      </div>
      <div class="modal-footer justify-content-between">
        <span style="color:#94a3b8;font-size:0.78rem;"><i class="fas fa-info-circle me-1"></i>Preview only — click Download to save</span>
        <button type="button" class="btn-fprev-dl" id="fprevDlBtn">
          <i class="fas fa-download"></i> Download File
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Zoom Modal (full-screen image) -->
<div class="modal fade" id="fprevZoomModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body">
        <img id="fprevZoomImg" src="" alt="Zoom View"/>
      </div>
    </div>
  </div>
</div>

</body>
</html>
