<?php
/**
 * api/get_jc_downloads_list.php  (v2)
 * ─────────────────────────────────────────────────────────────
 * Returns ALL individual files across all JC upload records.
 * Parses files_json so each file in a multi-upload becomes
 * its own row — enabling per-file download.
 *
 * POST params (all optional):
 *   search_txt   — matches JC no, customer name, mobile
 *   from_date    — dd-mm-yyyy
 *   to_date      — dd-mm-yyyy
 *   user_filter  — username | all  (admin/superadmin only)
 *
 * Returns JSON:
 * {
 *   "remaining_downloads": 12,
 *   "used_today": 3,
 *   "limit": 15,
 *   "total_jcs": N,
 *   "total_files": N,
 *   "records": [
 *     {
 *       "upload_id":      1,
 *       "jobcard_no":     1042,
 *       "customer_name":  "Ravi",
 *       "customer_mobile":"9876543210",
 *       "uploaded_by":    "admin",
 *       "uploaded_at":    "2026-03-30 10:00:00",
 *       "upload_reason":  "",
 *       "download_count": 3,
 *       "files": [
 *         {
 *           "file_name":     "jc_1042_..._logo.png",
 *           "file_path":     "uploads/jc_designs/jc_1042_..._logo.png",
 *           "original_name": "logo.png",
 *           "file_type":     "png",
 *           "file_size_kb":  120.5,
 *           "uploaded_at":   "2026-03-30 10:00:00"
 *         }, ...
 *       ]
 *     }, ...
 *   ]
 * }
 */

session_start();
include_once "../connect_db.php";

header('Content-Type: application/json');

if (empty($_SESSION['user_name'])) {
    echo json_encode(['error' => 'Not authenticated.']);
    exit;
}

$user_name = $_SESSION['user_name'];
$user_type = $_SESSION['user_type'] ?? 'OPERATOR';
$today     = date('Y-m-d');
$esc_user  = $connection->real_escape_string($user_name);

// ── Remaining downloads today ─────────────────────────────────
$used_res   = $connection->query("
    SELECT COUNT(*) AS cnt FROM jc_download_logs
    WHERE downloaded_by = '$esc_user'
    AND DATE(downloaded_at) = '$today'
");
$used_today = 0;
if ($used_res) $used_today = (int)$used_res->fetch_assoc()['cnt'];
$remaining  = max(0, 15 - $used_today);

// ── WHERE clauses ─────────────────────────────────────────────
$where = ["1"];

$search = trim($_POST['search_txt'] ?? '');
if ($search !== '' && $search !== 'all') {
    $esc_s   = $connection->real_escape_string($search);
    $where[] = "(u.customer_name LIKE '%$esc_s%'
                 OR u.customer_mobile LIKE '%$esc_s%'
                 OR u.jobcard_no LIKE '%$esc_s%'
                 OR u.uploaded_by LIKE '%$esc_s%')";
}

$from_date = $_POST['from_date'] ?? '';
$to_date   = $_POST['to_date']   ?? '';
if ($from_date !== '' && $from_date !== 'all') {
    $fd = DateTime::createFromFormat('d-m-Y', $from_date)->format('Y-m-d');
    $where[] = "DATE(u.uploaded_at) >= '$fd'";
}
if ($to_date !== '' && $to_date !== 'all') {
    $td = DateTime::createFromFormat('d-m-Y', $to_date)->format('Y-m-d');
    $where[] = "DATE(u.uploaded_at) <= '$td'";
}

// Operators only see their own
if ($user_type === 'OPERATOR') {
    $where[] = "u.uploaded_by = '$esc_user'";
} else {
    $user_filter = $_POST['user_filter'] ?? 'all';
    if ($user_filter !== 'all' && $user_filter !== '') {
        $esc_uf  = $connection->real_escape_string($user_filter);
        $where[] = "u.uploaded_by = '$esc_uf'";
    }
}

$where_sql = implode(' AND ', $where);

// ── Main query ────────────────────────────────────────────────
$sql = "
    SELECT
        u.id            AS upload_id,
        u.jobcard_no,
        u.customer_name,
        u.customer_mobile,
        u.files_json,
        u.file_name     AS legacy_file_name,
        u.file_path     AS legacy_file_path,
        u.file_type     AS legacy_file_type,
        u.file_size_kb  AS legacy_file_size_kb,
        u.upload_reason,
        u.uploaded_by,
        u.uploaded_at,
        COALESCE(dl.download_count, 0)  AS download_count,
        COALESCE(dl.last_downloaded, '') AS last_downloaded
    FROM jc_design_uploads u
    LEFT JOIN (
        SELECT upload_id, COUNT(*) AS download_count,
               MAX(downloaded_at)  AS last_downloaded
        FROM jc_download_logs GROUP BY upload_id
    ) dl ON dl.upload_id = u.id
    WHERE $where_sql
    ORDER BY u.uploaded_at DESC
    LIMIT 500
";

$res     = $connection->query($sql);
$records = [];
$total_files = 0;

if ($res) {
    while ($row = $res->fetch_assoc()) {
        // Parse files_json into an array of file objects
        $files = [];
        if (!empty($row['files_json'])) {
            $parsed = json_decode($row['files_json'], true);
            if (is_array($parsed)) $files = $parsed;
        }
        // Fallback: use legacy single-file columns
        if (empty($files) && !empty($row['legacy_file_name'])) {
            $files[] = [
                'file_name'     => $row['legacy_file_name'],
                'file_path'     => $row['legacy_file_path'],
                'original_name' => $row['legacy_file_name'],
                'file_type'     => $row['legacy_file_type'],
                'file_size_kb'  => $row['legacy_file_size_kb'],
                'uploaded_at'   => $row['uploaded_at'],
                'uploaded_by'   => $row['uploaded_by'],
            ];
        }

        $total_files += count($files);

        $records[] = [
            'upload_id'       => (int)$row['upload_id'],
            'jobcard_no'      => (int)$row['jobcard_no'],
            'customer_name'   => $row['customer_name'],
            'customer_mobile' => $row['customer_mobile'],
            'upload_reason'   => $row['upload_reason'],
            'uploaded_by'     => $row['uploaded_by'],
            'uploaded_at'     => $row['uploaded_at'],
            'download_count'  => (int)$row['download_count'],
            'last_downloaded' => $row['last_downloaded'],
            'files'           => $files,
        ];
    }
}

echo json_encode([
    'remaining_downloads' => $remaining,
    'used_today'          => $used_today,
    'limit'               => 15,
    'total_jcs'           => count($records),
    'total_files'         => $total_files,
    'records'             => $records,
]);
