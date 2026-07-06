<?php
/**
 * api/get_download_log.php
 * ─────────────────────────────────────────────────────────────
 * Returns full download history (admin/superadmin only).
 * Operators get only their own history.
 *
 * POST params:
 *   from_date  — dd-mm-yyyy
 *   to_date    — dd-mm-yyyy
 *   user_filter — username | all
 *
 * Returns JSON array of log rows.
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
$esc_user  = $connection->real_escape_string($user_name);

$where = ["1"];

// Date range
$from_date = $_POST['from_date'] ?? '';
$to_date   = $_POST['to_date']   ?? '';
if ($from_date !== '' && $from_date !== 'all') {
    $fd      = DateTime::createFromFormat('d-m-Y', $from_date)->format('Y-m-d');
    $where[] = "DATE(l.downloaded_at) >= '$fd'";
}
if ($to_date !== '' && $to_date !== 'all') {
    $td      = DateTime::createFromFormat('d-m-Y', $to_date)->format('Y-m-d');
    $where[] = "DATE(l.downloaded_at) <= '$td'";
}

// User restriction
if ($user_type === 'OPERATOR') {
    $where[] = "l.downloaded_by = '$esc_user'";
} else {
    $user_filter = $_POST['user_filter'] ?? 'all';
    if ($user_filter !== 'all' && $user_filter !== '') {
        $esc_uf  = $connection->real_escape_string($user_filter);
        $where[] = "l.downloaded_by = '$esc_uf'";
    }
}

$where_sql = implode(' AND ', $where);

$sql = "
    SELECT
        l.*,
        u.customer_name,
        u.customer_mobile,
        u.file_type,
        u.file_size_kb
    FROM jc_download_logs l
    LEFT JOIN jc_design_uploads u ON u.id = l.upload_id
    WHERE $where_sql
    ORDER BY l.downloaded_at DESC
    LIMIT 500
";

$res  = $connection->query($sql);
$rows = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
}

echo json_encode(['logs' => $rows, 'total' => count($rows)]);
