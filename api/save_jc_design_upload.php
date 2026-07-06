<?php
/**
 * API: save_jc_design_upload.php  (v3 — CDR/AI/EPS fix + hardened ext detection)
 * ─────────────────────────────────────────────────────────────
 * BUG FIXED: Reason-only submissions no longer throw "Unknown upload error"
 * BUG FIXED: CDR, AI, EPS, RAR, 7Z files now correctly allowed
 * BUG FIXED: Filenames with multiple dots (e.g. "73 nos. (1).cdr") now handled
 * NEW: Supports multiple file uploads stored as JSON array (files_json column)
 * NEW: New files are APPENDED to existing files (not replaced)
 *
 * POST params:
 *   jobcard_no       - Job Card number (required)
 *   customer_name    - Customer name
 *   customer_mobile  - Customer mobile
 *   upload_reason    - Reason text (required only when NO files sent)
 *   design_files[]   - Multiple file inputs (allowed: pdf, psd, jpeg, jpg, png,
 *                      zip, rar, 7z, cdr, cdt, ai, eps — max 20MB each)
 *
 * Returns JSON: { "status": "success"|"error", "message": "...", "files": [...] }
 */

session_start();
include_once "../connect_db.php";

header('Content-Type: application/json');

// ── Helper ────────────────────────────────────────────────────
function json_response($status, $message, $extra = []) {
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $extra));
    exit;
}

// ── Bulletproof extension extractor ──────────────────────────
// Works correctly for filenames like:
//   "73 nos. (1).cdr"  →  cdr
//   "report.v2.pdf"    →  pdf
//   "design.ai"        →  ai
function get_file_ext($filename) {
    // Split on ALL dots, take the very last segment
    $parts = explode('.', $filename);
    if (count($parts) < 2) return ''; // no extension at all
    $ext = end($parts);
    $ext = strtolower(trim($ext));
    $ext = preg_replace('/[^a-z0-9]/', '', $ext); // strip brackets, spaces, etc.
    return $ext;
}

// ── Input ─────────────────────────────────────────────────────
$jobcard_no      = isset($_POST['jobcard_no'])      ? intval($_POST['jobcard_no'])    : 0;
$customer_name   = isset($_POST['customer_name'])   ? trim($_POST['customer_name'])   : '';
$customer_mobile = isset($_POST['customer_mobile']) ? trim($_POST['customer_mobile']) : '';
$upload_reason   = isset($_POST['upload_reason'])   ? trim($_POST['upload_reason'])   : '';

if ($jobcard_no <= 0) {
    json_response('error', 'Invalid Job Card number.');
}

// ── Auto-create / migrate table ───────────────────────────────
$connection->query("
CREATE TABLE IF NOT EXISTS `jc_design_uploads` (
    `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `jobcard_no`      INT(11)          NOT NULL,
    `customer_name`   VARCHAR(255)     NOT NULL DEFAULT '',
    `customer_mobile` VARCHAR(20)      NOT NULL DEFAULT '',
    `files_json`      LONGTEXT                              COMMENT 'JSON array of all uploaded file objects',
    `file_name`       VARCHAR(255)     NOT NULL DEFAULT ''  COMMENT 'Legacy first-file name',
    `file_path`       VARCHAR(500)     NOT NULL DEFAULT '',
    `file_size_kb`    DECIMAL(10,2)    NOT NULL DEFAULT 0,
    `file_type`       VARCHAR(50)      NOT NULL DEFAULT '',
    `upload_reason`   TEXT,
    `uploaded_by`     VARCHAR(100)     NOT NULL DEFAULT '',
    `uploaded_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_jobcard_no` (`jobcard_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Add files_json column if upgrading from v1
$col_check = $connection->query("SHOW COLUMNS FROM jc_design_uploads LIKE 'files_json'");
if ($col_check && $col_check->num_rows === 0) {
    $connection->query("ALTER TABLE jc_design_uploads ADD COLUMN `files_json` LONGTEXT AFTER `customer_mobile`");
}

// ── Check existing record ─────────────────────────────────────
$check_res      = $connection->query("SELECT id, files_json FROM jc_design_uploads WHERE jobcard_no = $jobcard_no LIMIT 1");
$already_exists = ($check_res && $check_res->num_rows > 0);
$existing_id    = 0;
$existing_files = [];
if ($already_exists) {
    $ex_row         = $check_res->fetch_assoc();
    $existing_id    = (int)$ex_row['id'];
    $existing_files = json_decode($ex_row['files_json'] ?? '[]', true) ?: [];
}

// ── Detect whether any real files were sent ───────────────────
$has_files = false;
$raw_files = null;

if (isset($_FILES['design_files']) && is_array($_FILES['design_files']['name'])) {
    $raw_files = $_FILES['design_files'];
    foreach ($raw_files['error'] as $err) {
        if ($err === UPLOAD_ERR_OK) {
            $has_files = true;
            break;
        }
    }
}

// If no files, reason is mandatory
if (!$has_files) {
    if ($upload_reason === '') {
        json_response('error', 'Please upload at least one design file, or provide a reason for not uploading.');
    }
    // Reason given — skip file processing, go straight to DB save
}

// ── Allowed types ─────────────────────────────────────────────
$allowed_ext = ['pdf', 'psd', 'jpeg', 'jpg', 'png', 'zip', 'rar', '7z', 'cdr', 'cdt', 'ai', 'eps'];
$max_size_mb = 20;
$upload_dir  = dirname(__DIR__) . '/uploads/jc_designs/';

if ($has_files) {
    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        @mkdir($upload_dir, 0755, true);
    }
    // Verify the directory is actually writable before attempting any move/write.
    // This catches IIS permission issues early and returns a clean JSON error
    // instead of letting PHP warnings corrupt the response and cause a 500.
    if (!is_writable($upload_dir)) {
        json_response('error',
            'Upload folder is not writable on the server. '
            . 'Please grant the IIS app pool user (IIS_IUSRS) Modify permission on: '
            . realpath($upload_dir) ?: $upload_dir
        );
    }
}

$new_files  = [];
$first_file = null;

if ($has_files) {
    $count = count($raw_files['name']);
    for ($i = 0; $i < $count; $i++) {
        $err = $raw_files['error'][$i];

        // Skip genuinely empty file slots
        if ($err === UPLOAD_ERR_NO_FILE) continue;

        // Real upload errors
        if ($err !== UPLOAD_ERR_OK) {
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE   => 'File #' . ($i+1) . ' exceeds the server upload_max_filesize limit.',
                UPLOAD_ERR_FORM_SIZE  => 'File #' . ($i+1) . ' exceeds the form MAX_FILE_SIZE.',
                UPLOAD_ERR_PARTIAL    => 'File #' . ($i+1) . ' was only partially uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Server is missing a temporary upload folder.',
                UPLOAD_ERR_CANT_WRITE => 'Server failed to write file #' . ($i+1) . ' to disk.',
                UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the upload of file #' . ($i+1) . '.',
            ];
            json_response('error', $upload_errors[$err] ?? 'Upload failed for file #' . ($i+1) . '.');
        }

        $tmp_name  = $raw_files['tmp_name'][$i];
        $orig_name = basename($raw_files['name'][$i]);
        $file_size = $raw_files['size'][$i];

        // ★ Use bulletproof extension extractor
        $ext = get_file_ext($orig_name);

        // Validate extension — error message is always auto-generated from $allowed_ext
        if (!$ext || !in_array($ext, $allowed_ext)) {
            $allowed_display = strtoupper(implode(', ', $allowed_ext));
            json_response('error', "\"$orig_name\" — invalid file type (.$ext). Allowed: $allowed_display.");
        }

        // Validate size
        if ($file_size > $max_size_mb * 1024 * 1024) {
            json_response('error', "\"$orig_name\" exceeds the {$max_size_mb} MB size limit.");
        }

        // Unique safe filename
        $safe_orig   = preg_replace('/[^a-zA-Z0-9._-]/', '_', $orig_name);
        $unique_name = 'jc_' . $jobcard_no . '_' . time() . '_' . $i . '_' . $safe_orig;
        $dest_path   = $upload_dir . $unique_name;

        // Compress images under 1.5 MB; move larger files as-is to avoid memory exhaustion
        $saved = false;
        $compress_size_limit = 1.5 * 1024 * 1024; // 1.5 MB
        if (in_array($ext, ['jpeg', 'jpg', 'png']) && $file_size <= $compress_size_limit) {
            $saved = compress_image($tmp_name, $dest_path, $ext, 85);
        }
        if (!$saved) {
            // @ suppresses PHP warnings so they don't corrupt the JSON response
            if (!@move_uploaded_file($tmp_name, $dest_path)) {
                json_response('error', "Could not save \"$orig_name\". Check server folder permissions on: $upload_dir");
            }
        }

        $file_obj = [
            'file_name'     => $unique_name,
            'file_path'     => 'uploads/jc_designs/' . $unique_name,
            'original_name' => $orig_name,
            'file_type'     => $ext,
            'file_size_kb'  => round(filesize($dest_path) / 1024, 2),
            'uploaded_by'   => $_SESSION['user_name'] ?? 'unknown',
            'uploaded_at'   => date('Y-m-d H:i:s'),
        ];

        $new_files[] = $file_obj;
        if ($first_file === null) $first_file = $file_obj;
    }
}

// ── Merge new files onto existing ones ───────────────────────
$all_files        = array_merge($existing_files, $new_files);
$final_files_json = json_encode($all_files, JSON_UNESCAPED_UNICODE);

// ── Sanitise for DB ───────────────────────────────────────────
$uploaded_by    = $_SESSION['user_name'] ?? 'unknown';
$esc_jc         = intval($jobcard_no);
$esc_name       = $connection->real_escape_string($customer_name);
$esc_mobile     = $connection->real_escape_string($customer_mobile);
$esc_files_json = $connection->real_escape_string($final_files_json);
$esc_reason     = $connection->real_escape_string($upload_reason);
$esc_user       = $connection->real_escape_string($uploaded_by);
$esc_fn         = $connection->real_escape_string($first_file['file_name']  ?? '');
$esc_fp         = $connection->real_escape_string($first_file['file_path']  ?? '');
$esc_ft         = $connection->real_escape_string($first_file['file_type']  ?? '');
$esc_fsize      = $first_file['file_size_kb'] ?? 0;

// ── Save to DB ────────────────────────────────────────────────
if ($already_exists) {
    $set = "
        customer_name   = '$esc_name',
        customer_mobile = '$esc_mobile',
        files_json      = '$esc_files_json',
        upload_reason   = '$esc_reason',
        uploaded_by     = '$esc_user',
        uploaded_at     = NOW()
    ";
    // Only overwrite legacy single-file columns if new files were uploaded
    if ($first_file) {
        $set .= ", file_name='$esc_fn', file_path='$esc_fp', file_size_kb=$esc_fsize, file_type='$esc_ft'";
    }
    $sql = "UPDATE jc_design_uploads SET $set WHERE jobcard_no = $esc_jc LIMIT 1";
} else {
    $sql = "
        INSERT INTO jc_design_uploads
            (jobcard_no, customer_name, customer_mobile, files_json,
             file_name, file_path, file_size_kb, file_type,
             upload_reason, uploaded_by, uploaded_at)
        VALUES
            ($esc_jc, '$esc_name', '$esc_mobile', '$esc_files_json',
             '$esc_fn', '$esc_fp', $esc_fsize, '$esc_ft',
             '$esc_reason', '$esc_user', NOW())
    ";
}

if ($connection->query($sql)) {
    $total = count($all_files);
    $msg   = $total > 0
           ? "$total file(s) saved for JC #$jobcard_no."
           : "Reason recorded successfully for JC #$jobcard_no.";
    json_response('success', $msg, ['files' => $all_files]);
} else {
    json_response('error', 'Database error: ' . $connection->error);
}

// ── Image compression ─────────────────────────────────────────
function compress_image($src, $dest, $ext, $quality = 85) {
    if (!function_exists('imagecreatefromjpeg')) return false;
    try {
        if (in_array($ext, ['jpeg', 'jpg'])) {
            $img = @imagecreatefromjpeg($src);
            if (!$img) return false;
            $ok = imagejpeg($img, $dest, $quality);
        } elseif ($ext === 'png') {
            $img = @imagecreatefrompng($src);
            if (!$img) return false;
            imagealphablending($img, true);
            imagesavealpha($img, true);
            $ok = imagepng($img, $dest, (int)round((100 - $quality) / 11));
        } else {
            return false;
        }
        imagedestroy($img);
        return $ok;
    } catch (Exception $e) {
        return false;
    }
}