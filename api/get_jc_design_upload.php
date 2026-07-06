<?php
/**
 * API: get_jc_design_upload.php
 * Returns the design upload record for a given Job Card number.
 *
 * GET param: jobcard_no
 *
 * Returns JSON:
 *   { "status": "found",   "data": { ...row... } }
 *   { "status": "not_found" }
 *   { "status": "error",   "message": "..." }
 */

session_start();
include_once "../connect_db.php";

header('Content-Type: application/json');

$jobcard_no = isset($_GET['jobcard_no']) ? intval($_GET['jobcard_no']) : 0;

if ($jobcard_no <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Job Card number.']);
    exit;
}

// Auto-create table if it doesn't exist yet (idempotent)
$connection->query("
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
");

$sql = "SELECT * FROM jc_design_uploads WHERE jobcard_no = " . intval($jobcard_no) . " ORDER BY uploaded_at DESC LIMIT 1";
$res = $connection->query($sql);

if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    echo json_encode(['status' => 'found', 'data' => $row]);
} else {
    echo json_encode(['status' => 'not_found']);
}
