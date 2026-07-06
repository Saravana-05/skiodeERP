<?php
/**
 * API: delete_jc_design_file.php
 * ─────────────────────────────────────────────────────────────
 * Removes a single file from a JC's files_json array and
 * deletes the physical file from disk.
 *
 * POST params:
 *   jobcard_no  - Job Card number
 *   file_name   - The unique stored file_name to delete
 *
 * Returns JSON: { "status": "success"|"error", "message": "...", "files": [...remaining...] }
 */

session_start();
include_once "../connect_db.php";

header('Content-Type: application/json');

function json_resp($status, $message, $extra = []) {
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $extra));
    exit;
}

if (empty($_SESSION['user_name'])) {
    json_resp('error', 'Not authenticated.');
}

$jobcard_no = isset($_POST['jobcard_no']) ? intval($_POST['jobcard_no']) : 0;
$file_name  = isset($_POST['file_name'])  ? trim($_POST['file_name'])   : '';

if ($jobcard_no <= 0 || $file_name === '') {
    json_resp('error', 'Missing jobcard_no or file_name.');
}

// Fetch existing record
$esc_jc  = intval($jobcard_no);
$res     = $connection->query("SELECT id, files_json, uploaded_by FROM jc_design_uploads WHERE jobcard_no = $esc_jc LIMIT 1");

if (!$res || $res->num_rows === 0) {
    json_resp('error', 'No upload record found for this Job Card.');
}

$row          = $res->fetch_assoc();
$record_id    = (int)$row['id'];
$files        = json_decode($row['files_json'] ?? '[]', true) ?: [];
$user_type    = $_SESSION['user_type'] ?? 'OPERATOR';
$current_user = $_SESSION['user_name'];

// Only ADMIN / SUPERADMIN or the uploader can delete
// (remove this check if all users should be able to delete)
// Uncomment to restrict:
// if ($user_type === 'OPERATOR' && $row['uploaded_by'] !== $current_user) {
//     json_resp('error', 'You can only delete files you uploaded.');
// }

// Find and remove the file from the JSON array
$found      = false;
$remaining  = [];
$file_path  = '';

foreach ($files as $f) {
    if ($f['file_name'] === $file_name) {
        $found      = true;
        $file_path  = $f['file_path'] ?? '';
    } else {
        $remaining[] = $f;
    }
}

if (!$found) {
    json_resp('error', 'File not found in this Job Card record.');
}

// Delete physical file from disk
if ($file_path !== '') {
    $full_path = dirname(__DIR__) . '/' . $file_path;
    if (file_exists($full_path)) {
        @unlink($full_path);
    }
}

// Update DB — save remaining files
$new_json    = json_encode($remaining, JSON_UNESCAPED_UNICODE);
$esc_json    = $connection->real_escape_string($new_json);

// Also update legacy single-file columns to the first remaining file (or empty)
$first       = $remaining[0] ?? null;
$esc_fn      = $connection->real_escape_string($first['file_name']   ?? '');
$esc_fp      = $connection->real_escape_string($first['file_path']   ?? '');
$esc_ft      = $connection->real_escape_string($first['file_type']   ?? '');
$esc_fsize   = $first['file_size_kb'] ?? 0;

$sql = "
    UPDATE jc_design_uploads SET
        files_json   = '$esc_json',
        file_name    = '$esc_fn',
        file_path    = '$esc_fp',
        file_size_kb = $esc_fsize,
        file_type    = '$esc_ft',
        uploaded_at  = NOW()
    WHERE id = $record_id
    LIMIT 1
";

if ($connection->query($sql)) {
    json_resp('success', "File deleted successfully.", ['files' => $remaining]);
} else {
    json_resp('error', 'Database error: ' . $connection->error);
}
