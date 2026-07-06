<?php
/**
 * API: rename_jc_design_file.php
 * ─────────────────────────────────────────────────────────────
 * Updates the original_name (display name) of a file stored
 * inside the files_json array in jc_design_uploads.
 *
 * POST params:
 *   jobcard_no       - Job Card number
 *   file_name        - The unique stored file_name to rename
 *   new_display_name - New display name to set
 *
 * Returns JSON: { "status": "success"|"error", "message": "...", "files": [...] }
 */

session_start();
include_once "../connect_db.php";

header('Content-Type: application/json');

function json_resp_r($status, $message, $extra = []) {
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $extra));
    exit;
}

if (empty($_SESSION['user_name'])) {
    json_resp_r('error', 'Not authenticated.');
}

$jobcard_no      = isset($_POST['jobcard_no'])       ? intval($_POST['jobcard_no'])           : 0;
$file_name       = isset($_POST['file_name'])         ? trim($_POST['file_name'])              : '';
$new_display_name = isset($_POST['new_display_name']) ? trim($_POST['new_display_name'])       : '';

if ($jobcard_no <= 0 || $file_name === '' || $new_display_name === '') {
    json_resp_r('error', 'Missing required fields.');
}

// Fetch existing record
$esc_jc = intval($jobcard_no);
$res    = $connection->query("SELECT id, files_json FROM jc_design_uploads WHERE jobcard_no = $esc_jc LIMIT 1");

if (!$res || $res->num_rows === 0) {
    json_resp_r('error', 'No upload record found for this Job Card.');
}

$row    = $res->fetch_assoc();
$rec_id = (int)$row['id'];
$files  = json_decode($row['files_json'] ?? '[]', true) ?: [];

// Find the file and update its original_name
$found = false;
foreach ($files as &$f) {
    if ($f['file_name'] === $file_name) {
        $f['original_name'] = $new_display_name;
        $found = true;
        break;
    }
}
unset($f);

if (!$found) {
    json_resp_r('error', 'File not found in this Job Card record.');
}

// Save updated files_json back to DB
$new_json = json_encode($files, JSON_UNESCAPED_UNICODE);
$esc_json = $connection->real_escape_string($new_json);

$sql = "UPDATE jc_design_uploads SET files_json = '$esc_json' WHERE id = $rec_id LIMIT 1";

if ($connection->query($sql)) {
    json_resp_r('success', 'File renamed successfully.', ['files' => $files]);
} else {
    json_resp_r('error', 'Database error: ' . $connection->error);
}
?>
