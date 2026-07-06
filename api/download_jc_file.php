<?php
/**
 * api/download_jc_file.php  (v2)
 * ─────────────────────────────────────────────────────────────
 * Streams a specific design file to the browser.
 * Supports multi-file JC records via the `file` GET param.
 *
 * GET params:
 *   id    — jc_design_uploads.id  (required)
 *   file  — specific file_name within files_json (optional;
 *           if omitted, uses the legacy single file_path)
 *
 * Rules:
 *   • Session required
 *   • 15 downloads per user per calendar day
 *   • Logs every download to jc_download_logs
 *   • Browser filename: CustomerName_JC{no}_{date}.{ext}
 */

session_start();
include_once "../connect_db.php";

if (empty($_SESSION['user_name'])) {
    http_response_code(401);
    die('Not authenticated.');
}

$user_name    = $_SESSION['user_name'];
$record_id    = isset($_GET['id'])   ? intval($_GET['id'])           : 0;
$wanted_file  = isset($_GET['file']) ? basename(trim($_GET['file']))  : '';

if ($record_id <= 0) {
    http_response_code(400); die('Invalid record ID.');
}

// ── Auto-create log table ─────────────────────────────────────
$connection->query("
CREATE TABLE IF NOT EXISTS `jc_download_logs` (
    `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `upload_id`     INT(11) UNSIGNED NOT NULL,
    `jobcard_no`    INT(11)          NOT NULL,
    `downloaded_by` VARCHAR(100)     NOT NULL DEFAULT '',
    `downloaded_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ip_address`    VARCHAR(45)      NOT NULL DEFAULT '',
    `file_name`     VARCHAR(255)     NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    KEY `idx_user_date` (`downloaded_by`, `downloaded_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// ── Check daily limit ─────────────────────────────────────────
$today    = date('Y-m-d');
$esc_user = $connection->real_escape_string($user_name);
$lim_res  = $connection->query("
    SELECT COUNT(*) AS cnt FROM jc_download_logs
    WHERE downloaded_by='$esc_user' AND DATE(downloaded_at)='$today'
");
$used_today = (int)$lim_res->fetch_assoc()['cnt'];
if ($used_today >= 15) {
    http_response_code(429);
    die(json_encode([
        'error'   => 'Daily download limit reached.',
        'message' => "You have used all 15 downloads today ($today). Resets at midnight.",
    ]));
}

// ── Fetch upload record ───────────────────────────────────────
$esc_id  = intval($record_id);
$rec_res = $connection->query("SELECT * FROM jc_design_uploads WHERE id=$esc_id LIMIT 1");
if (!$rec_res || $rec_res->num_rows === 0) {
    http_response_code(404); die('Record not found.');
}
$rec = $rec_res->fetch_assoc();

// ── Resolve which file to serve ───────────────────────────────
$file_path_rel = '';  // relative from app root
$file_name_log = '';  // stored filename on disk
$original_name = '';  // for building download filename

if ($wanted_file !== '') {
    // Look for the specific file inside files_json
    $files = json_decode($rec['files_json'] ?? '[]', true) ?: [];
    foreach ($files as $f) {
        if (basename($f['file_name']) === $wanted_file) {
            $file_path_rel = $f['file_path'];
            $file_name_log = $f['file_name'];
            $original_name = $f['original_name'] ?? $f['file_name'];
            break;
        }
    }
    if ($file_path_rel === '') {
        http_response_code(404); die('Requested file not found in this record.');
    }
} else {
    // Fall back to legacy single-file columns
    if (empty($rec['file_path'])) {
        http_response_code(400); die('No file attached to this record.');
    }
    $file_path_rel = $rec['file_path'];
    $file_name_log = $rec['file_name'];
    $original_name = $rec['file_name'];
}

// ── Physical path ─────────────────────────────────────────────
$app_root  = dirname(__DIR__);
$full_path = $app_root . '/' . $file_path_rel;

if (!file_exists($full_path) || !is_readable($full_path)) {
    http_response_code(404); die('File not found on server.');
}

// ── Build download filename ───────────────────────────────────
$ext         = strtolower(pathinfo($file_name_log, PATHINFO_EXTENSION));
$safe_cust   = preg_replace('/[^a-zA-Z0-9_\-]/', '_', trim($rec['customer_name']));
$safe_cust   = $safe_cust ?: 'Customer';
$jc_no       = $rec['jobcard_no'];
$upload_date = date('d-m-Y', strtotime($rec['uploaded_at']));
$dl_name     = "{$safe_cust}_JC{$jc_no}_{$upload_date}.{$ext}";

// ── Log download ──────────────────────────────────────────────
$ip      = $connection->real_escape_string($_SERVER['REMOTE_ADDR'] ?? '');
$esc_fn  = $connection->real_escape_string($dl_name);
$connection->query("
    INSERT INTO jc_download_logs (upload_id, jobcard_no, downloaded_by, downloaded_at, ip_address, file_name)
    VALUES ($esc_id, $jc_no, '$esc_user', NOW(), '$ip', '$esc_fn')
");

// ── Stream to browser ─────────────────────────────────────────
$mime_map = [
    'pdf'  => 'application/pdf',
    'psd'  => 'application/octet-stream',
    'jpeg' => 'image/jpeg',
    'jpg'  => 'image/jpeg',
    'png'  => 'image/png',
];
$mime = $mime_map[$ext] ?? 'application/octet-stream';

header('Content-Type: '         . $mime);
header('Content-Disposition: attachment; filename="' . $dl_name . '"');
header('Content-Length: '       . filesize($full_path));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

ob_clean(); flush();
readfile($full_path);
exit;
