<?php
/**
 * api/download_jc_file.php
 * ─────────────────────────────────────────────────────────────
 * Secure file download endpoint for JC design uploads.
 *
 * GET params:
 *   id  — jc_design_uploads.id (record ID)
 *
 * Rules:
 *   • User must be logged in (session active)
 *   • Max 15 downloads per user per calendar day
 *   • File must physically exist on disk
 *   • Download is logged to jc_download_logs table
 *   • Filename sent to browser: CustomerName_JC{no}_{date}.{ext}
 */

session_start();
include_once "../connect_db.php";

// ── Auth check ────────────────────────────────────────────────
if (empty($_SESSION['user_name'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Not authenticated.']));
}

$user_name = $_SESSION['user_name'];
$record_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($record_id <= 0) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid record ID.']));
}

// ── Auto-create download log table ───────────────────────────
$connection->query("
CREATE TABLE IF NOT EXISTS `jc_download_logs` (
    `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `upload_id`     INT(11) UNSIGNED NOT NULL COMMENT 'FK to jc_design_uploads.id',
    `jobcard_no`    INT(11)          NOT NULL,
    `downloaded_by` VARCHAR(100)     NOT NULL DEFAULT '',
    `downloaded_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ip_address`    VARCHAR(45)      NOT NULL DEFAULT '',
    `file_name`     VARCHAR(255)     NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    KEY `idx_user_date` (`downloaded_by`, `downloaded_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Tracks every file download per user for rate-limiting';
");

// ── Check daily download limit (15 per user per day) ─────────
$today     = date('Y-m-d');
$limit_sql = "SELECT COUNT(*) AS cnt FROM jc_download_logs
              WHERE downloaded_by = '" . $connection->real_escape_string($user_name) . "'
              AND DATE(downloaded_at) = '$today'";
$limit_res = $connection->query($limit_sql);
$limit_row = $limit_res->fetch_assoc();
$used_today = (int)$limit_row['cnt'];

if ($used_today >= 15) {
    http_response_code(429);
    die(json_encode([
        'error'      => 'Daily download limit reached.',
        'message'    => "You have used all 15 downloads for today ($today). Limit resets at midnight.",
        'used'       => $used_today,
        'limit'      => 15
    ]));
}

// ── Fetch the upload record ───────────────────────────────────
$esc_id  = intval($record_id);
$rec_sql = "SELECT * FROM jc_design_uploads WHERE id = $esc_id LIMIT 1";
$rec_res = $connection->query($rec_sql);

if (!$rec_res || $rec_res->num_rows === 0) {
    http_response_code(404);
    die(json_encode(['error' => 'Record not found.']));
}

$rec = $rec_res->fetch_assoc();

// Must have a file (not a reason-only record)
if (empty($rec['file_name']) || empty($rec['file_path'])) {
    http_response_code(400);
    die(json_encode(['error' => 'No file is attached to this Job Card record.']));
}

// ── Resolve physical path ─────────────────────────────────────
// file_path is stored as relative from app root e.g. "uploads/jc_designs/jc_1_xyz.pdf"
$app_root  = dirname(__DIR__);                      // one level up from /api/
$full_path = $app_root . '/' . $rec['file_path'];

if (!file_exists($full_path) || !is_readable($full_path)) {
    http_response_code(404);
    die(json_encode(['error' => 'File not found on server. It may have been moved or deleted.']));
}

// ── Build a clean download filename ──────────────────────────
// Format: CustomerName_JC1234_30-03-2026.ext
$ext           = strtolower(pathinfo($rec['file_name'], PATHINFO_EXTENSION));
$safe_cust     = preg_replace('/[^a-zA-Z0-9_\-]/', '_', trim($rec['customer_name']));
$safe_cust     = $safe_cust ?: 'Customer';
$jc_no         = $rec['jobcard_no'];
$upload_date   = date('d-m-Y', strtotime($rec['uploaded_at']));
$download_name = "{$safe_cust}_JC{$jc_no}_{$upload_date}.{$ext}";

// ── Log this download ─────────────────────────────────────────
$ip       = $_SERVER['REMOTE_ADDR'] ?? '';
$esc_user = $connection->real_escape_string($user_name);
$esc_fn   = $connection->real_escape_string($download_name);
$esc_ip   = $connection->real_escape_string($ip);

$connection->query("
    INSERT INTO jc_download_logs (upload_id, jobcard_no, downloaded_by, downloaded_at, ip_address, file_name)
    VALUES ($esc_id, $jc_no, '$esc_user', NOW(), '$esc_ip', '$esc_fn')
");

// ── Stream file to browser ────────────────────────────────────
$mime_map = [
    'pdf'  => 'application/pdf',
    'psd'  => 'application/octet-stream',
    'jpeg' => 'image/jpeg',
    'jpg'  => 'image/jpeg',
    'png'  => 'image/png',
];
$mime = $mime_map[$ext] ?? 'application/octet-stream';

header('Content-Type: '         . $mime);
header('Content-Disposition: attachment; filename="' . $download_name . '"');
header('Content-Length: '       . filesize($full_path));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

ob_clean();
flush();
readfile($full_path);
exit;
