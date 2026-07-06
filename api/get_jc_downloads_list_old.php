<?php
/**
 * api/get_jc_downloads_list.php
 * ─────────────────────────────────────────────────────────────
 * Returns design upload records for the download page.
 * Supports search by JC no, customer name, mobile, date range,
 * file type, and uploaded-by user.
 *
 * Also returns the current user's remaining downloads for today.
 *
 * POST params (all optional — omit or send "all" to skip filter):
 *   search_txt        — free text (matches JC no, customer name, mobile)
 *   from_date         — dd-mm-yyyy
 *   to_date           — dd-mm-yyyy
 *   file_type_filter  — pdf | psd | jpeg | jpg | png | all
 *   user_filter       — username | all   (admin/superadmin only)
 *   has_file_filter   — yes | no | all
 *
 * Returns JSON:
 * {
 *   "remaining_downloads": 12,
 *   "used_today": 3,
 *   "limit": 15,
 *   "records": [ { ...row, "download_count": N } ]
 * }
 */

session_start();
include_once "../connect_db.php";



header('Content-Type: application/json');

if (empty($_SESSION['user_name'])) {
    echo json_encode(['error' => 'Not authenticated.']);
    exit;
}

$user_name  = $_SESSION['user_name'];
$user_type  = $_SESSION['user_type'] ?? 'OPERATOR';
$today      = date('Y-m-d');

// ── Remaining downloads today ─────────────────────────────────
$esc_user  = $connection->real_escape_string($user_name);
$used_res  = $connection->query("
    SELECT COUNT(*) AS cnt FROM jc_download_logs
    WHERE downloaded_by = '$esc_user'
    AND DATE(downloaded_at) = '$today'
");
$used_today = 0;
if ($used_res) {
    $used_today = (int)$used_res->fetch_assoc()['cnt'];
}
$remaining = max(0, 15 - $used_today);

// ── Build WHERE clauses ───────────────────────────────────────
$where = ["1"];

// Free text search
$search = trim($_POST['search_txt'] ?? '');
if ($search !== '' && $search !== 'all') {
    $esc_s = $connection->real_escape_string($search);
    $where[] = "(u.customer_name LIKE '%$esc_s%'
                 OR u.customer_mobile LIKE '%$esc_s%'
                 OR u.jobcard_no LIKE '%$esc_s%'
                 OR u.uploaded_by LIKE '%$esc_s%')";
}

// Date range
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

// File type filter
$file_type = $_POST['file_type_filter'] ?? 'all';
if ($file_type !== 'all' && $file_type !== '') {
    $esc_ft  = $connection->real_escape_string($file_type);
    $where[] = "u.file_type = '$esc_ft'";
}

// Has file filter
$has_file = $_POST['has_file_filter'] ?? 'all';
if ($has_file === 'yes') {
    $where[] = "u.file_name != ''";
} elseif ($has_file === 'no') {
    $where[] = "u.file_name = ''";
}

// User filter — operators only see their own uploads
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
        u.*,
        COALESCE(dl.download_count, 0) AS download_count,
        COALESCE(dl.last_downloaded,  '') AS last_downloaded
    FROM jc_design_uploads u
    LEFT JOIN (
        SELECT upload_id,
               COUNT(*)    AS download_count,
               MAX(downloaded_at) AS last_downloaded
        FROM jc_download_logs
        GROUP BY upload_id
    ) dl ON dl.upload_id = u.id
    WHERE $where_sql
    ORDER BY u.uploaded_at DESC
    LIMIT 500
";

$res     = $connection->query($sql);
// TEMP DEBUG — remove after fix
if (!$res) {
    echo json_encode(['error' => $connection->error, 'sql' => $sql]);
    exit;
}
$records = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $records[] = $row;
    }
}

echo json_encode([
    'remaining_downloads' => $remaining,
    'used_today'          => $used_today,
    'limit'               => 15,
    'total_records'       => count($records),
    'records'             => $records,
]);
