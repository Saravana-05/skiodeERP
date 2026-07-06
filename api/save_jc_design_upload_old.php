<?php
/**
 * API: save_jc_design_upload.php
 * Handles design file upload or no-file reason for a Job Card.
 * Stores files in /uploads/jc_designs/ and records metadata in jc_design_uploads table.
 *
 * POST params:
 *   jobcard_no       - Job Card number (required)
 *   customer_name    - Customer name (required)
 *   customer_mobile  - Customer mobile number (required)
 *   upload_reason    - Reason text when no file is uploaded (required if no file)
 *   design_file      - $_FILES['design_file'] (optional, allowed: pdf, psd, jpeg, jpg, png)
 *
 * Returns JSON: { "status": "success"|"error", "message": "...", "file_path": "..." }
 */

session_start();
include_once "../connect_db.php";

header('Content-Type: application/json');

// ── helpers ──────────────────────────────────────────────────────────────────
function json_response($status, $message, $extra = []) {
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $extra));
    exit;
}

// ── input validation ──────────────────────────────────────────────────────────
$jobcard_no      = isset($_POST['jobcard_no'])      ? intval($_POST['jobcard_no'])           : 0;
$customer_name   = isset($_POST['customer_name'])   ? trim($_POST['customer_name'])           : '';
$customer_mobile = isset($_POST['customer_mobile']) ? trim($_POST['customer_mobile'])         : '';
$upload_reason   = isset($_POST['upload_reason'])   ? trim($_POST['upload_reason'])           : '';

if ($jobcard_no <= 0) {
    json_response('error', 'Invalid Job Card number.');
}

// ── ensure the table exists ───────────────────────────────────────────────────
$create_table_sql = "
CREATE TABLE IF NOT EXISTS `jc_design_uploads` (
    `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `jobcard_no`      INT(11) NOT NULL,
    `customer_name`   VARCHAR(255) NOT NULL DEFAULT '',
    `customer_mobile` VARCHAR(20)  NOT NULL DEFAULT '',
    `file_name`       VARCHAR(255) NOT NULL DEFAULT '',
    `file_path`       VARCHAR(500) NOT NULL DEFAULT '',
    `file_size_kb`    DECIMAL(10,2) NOT NULL DEFAULT 0,
    `file_type`       VARCHAR(50)   NOT NULL DEFAULT '',
    `upload_reason`   TEXT,
    `uploaded_by`     VARCHAR(100)  NOT NULL DEFAULT '',
    `uploaded_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_jobcard_no` (`jobcard_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
$connection->query($create_table_sql);

// ── check if an upload record already exists for this JC ─────────────────────
$check_sql  = "SELECT id FROM jc_design_uploads WHERE jobcard_no = $jobcard_no LIMIT 1";
$check_res  = $connection->query($check_sql);
$already_exists = ($check_res && $check_res->num_rows > 0);

$file_name    = '';
$file_path_db = '';
$file_size_kb = 0;
$file_type    = '';

// ── handle file upload ────────────────────────────────────────────────────────
if (isset($_FILES['design_file']) && $_FILES['design_file']['error'] === UPLOAD_ERR_OK) {

    $allowed_ext  = ['pdf', 'psd', 'jpeg', 'jpg', 'png'];
    $max_size_mb  = 20; // maximum raw size accepted before compression
    $tmp_name     = $_FILES['design_file']['tmp_name'];
    $orig_name    = basename($_FILES['design_file']['name']);
    $file_size    = $_FILES['design_file']['size'];
    $ext          = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));

    // Validate extension
    if (!in_array($ext, $allowed_ext)) {
        json_response('error', 'Invalid file type. Allowed: PDF, PSD, JPEG, JPG, PNG.');
    }

    // Validate raw size (20 MB hard cap)
    if ($file_size > $max_size_mb * 1024 * 1024) {
        json_response('error', "File too large. Maximum allowed size is {$max_size_mb} MB.");
    }

    // Prepare upload directory
    $upload_dir = dirname(__DIR__) . '/uploads/jc_designs/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Build a unique file name: jc_{jobcard_no}_{timestamp}_{sanitised_orig}
    $safe_orig   = preg_replace('/[^a-zA-Z0-9._-]/', '_', $orig_name);
    $unique_name = 'jc_' . $jobcard_no . '_' . time() . '_' . $safe_orig;
    $dest_path   = $upload_dir . $unique_name;

    // ── image compression (JPEG / PNG only) ──────────────────────────────────
    $compressed = false;
    if (in_array($ext, ['jpeg', 'jpg', 'png'])) {
        $compressed = compress_image($tmp_name, $dest_path, $ext, 85);
    }

    // For PDF / PSD, or if compression failed, move as-is
    if (!$compressed) {
        if (!move_uploaded_file($tmp_name, $dest_path)) {
            json_response('error', 'Failed to save the uploaded file. Check server permissions.');
        }
    }

    $file_name    = $unique_name;
    $file_path_db = 'uploads/jc_designs/' . $unique_name;
    $file_size_kb = round(filesize($dest_path) / 1024, 2);
    $file_type    = $ext;
    $upload_reason = ''; // file was provided, so no reason needed

} elseif ($_FILES['design_file']['error'] !== UPLOAD_ERR_NO_FILE) {
    // A file was attempted but failed mid-upload
    $upload_errors = [
        UPLOAD_ERR_INI_SIZE   => 'File exceeds server upload limit (upload_max_filesize).',
        UPLOAD_ERR_FORM_SIZE  => 'File exceeds form MAX_FILE_SIZE.',
        UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder on server.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION  => 'A PHP extension blocked the upload.',
    ];
    $err_code = $_FILES['design_file']['error'];
    $err_msg  = $upload_errors[$err_code] ?? "Unknown upload error (code $err_code).";
    json_response('error', $err_msg);
} else {
    // No file uploaded – a reason MUST be provided
    if ($upload_reason === '') {
        json_response('error', 'Please either upload a design file or provide a reason for not uploading.');
    }
}

// ── sanitise strings for DB ───────────────────────────────────────────────────
$uploaded_by     = $_SESSION['user_name'] ?? 'unknown';
$esc_jc_no       = intval($jobcard_no);
$esc_cust_name   = $connection->real_escape_string($customer_name);
$esc_cust_mobile = $connection->real_escape_string($customer_mobile);
$esc_file_name   = $connection->real_escape_string($file_name);
$esc_file_path   = $connection->real_escape_string($file_path_db);
$esc_file_type   = $connection->real_escape_string($file_type);
$esc_reason      = $connection->real_escape_string($upload_reason);
$esc_user        = $connection->real_escape_string($uploaded_by);

// ── insert or update record ───────────────────────────────────────────────────
if ($already_exists) {
    $sql = "
        UPDATE jc_design_uploads SET
            customer_name   = '$esc_cust_name',
            customer_mobile = '$esc_cust_mobile',
            file_name       = '$esc_file_name',
            file_path       = '$esc_file_path',
            file_size_kb    = $file_size_kb,
            file_type       = '$esc_file_type',
            upload_reason   = '$esc_reason',
            uploaded_by     = '$esc_user',
            uploaded_at     = NOW()
        WHERE jobcard_no = $esc_jc_no
        LIMIT 1
    ";
} else {
    $sql = "
        INSERT INTO jc_design_uploads
            (jobcard_no, customer_name, customer_mobile, file_name, file_path,
             file_size_kb, file_type, upload_reason, uploaded_by, uploaded_at)
        VALUES
            ($esc_jc_no, '$esc_cust_name', '$esc_cust_mobile', '$esc_file_name',
             '$esc_file_path', $file_size_kb, '$esc_file_type',
             '$esc_reason', '$esc_user', NOW())
    ";
}

if ($connection->query($sql)) {
    $msg = ($file_name !== '')
        ? "Design file uploaded successfully (compressed to {$file_size_kb} KB)."
        : "Reason recorded successfully.";
    json_response('success', $msg, ['file_path' => $file_path_db]);
} else {
    json_response('error', 'Database error: ' . $connection->error);
}

// ── image compression helper ──────────────────────────────────────────────────
/**
 * Compress a JPEG or PNG image and save to $dest.
 * Returns true on success, false on failure.
 */
function compress_image($src, $dest, $ext, $quality = 85) {
    if (!function_exists('imagecreatefromjpeg')) {
        return false; // GD not available
    }
    try {
        if (in_array($ext, ['jpeg', 'jpg'])) {
            $img = imagecreatefromjpeg($src);
            if (!$img) return false;
            $result = imagejpeg($img, $dest, $quality);
        } elseif ($ext === 'png') {
            $img = imagecreatefrompng($src);
            if (!$img) return false;
            // PNG quality: 0 (no compression) – 9 (max). Map JPEG quality to PNG level.
            $pngQuality = (int) round((100 - $quality) / 11);
            imagealphablending($img, true);
            imagesavealpha($img, true);
            $result = imagepng($img, $dest, $pngQuality);
        } else {
            return false;
        }
        imagedestroy($img);
        return $result;
    } catch (Exception $e) {
        return false;
    }
}
