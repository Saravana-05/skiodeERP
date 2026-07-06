<?php
/**
 * get_item_discount_report.php
 * Returns item-level discount rows from jobcard_details joined with jobcard_master.
 * Supports filters: from_date, to_date, customer, item_name, job_type.
 * Only rows where item_discount > 0 are returned.
 */
session_start();
include_once '../connect_db.php';

$from_date = isset($_POST['from_date']) ? trim($_POST['from_date']) : date('Y-m-d');
$to_date   = isset($_POST['to_date'])   ? trim($_POST['to_date'])   : date('Y-m-d');
$customer  = isset($_POST['customer'])  ? trim($_POST['customer'])  : '';
$item_name = isset($_POST['item_name']) ? trim($_POST['item_name']) : '';
$job_type  = isset($_POST['job_type'])  ? trim($_POST['job_type'])  : '';

$where_parts = [];

if ($from_date)
    $where_parts[] = "DATE(jm.created_dt_tm) >= '" . mysqli_real_escape_string($connection, $from_date) . "'";
if ($to_date)
    $where_parts[] = "DATE(jm.created_dt_tm) <= '" . mysqli_real_escape_string($connection, $to_date) . "'";
if ($customer)
    $where_parts[] = "(jm.customer_name LIKE '%" . mysqli_real_escape_string($connection, $customer) . "%'
                    OR jm.customer_mobile_no LIKE '%" . mysqli_real_escape_string($connection, $customer) . "%')";
if ($job_type)
    $where_parts[] = "jd.job_type = '" . mysqli_real_escape_string($connection, $job_type) . "'";
if ($item_name)
    $where_parts[] = "(jd.job_in_detail LIKE '%" . mysqli_real_escape_string($connection, $item_name) . "%'
                    OR jd.product_code  LIKE '%" . mysqli_real_escape_string($connection, $item_name) . "%')";

// Always restrict to rows that have a discount
$where_parts[] = "COALESCE(jd.item_discount, 0) > 0";

$where_sql = "WHERE " . implode(" AND ", $where_parts);

$sql = "SELECT
            jm.jobcard_no,
            DATE_FORMAT(jm.created_dt_tm, '%d-%m-%Y') AS jc_date,
            jm.customer_name,
            jm.customer_mobile_no                       AS customer_mobile,
            jm.customer_type,
            UPPER(COALESCE(NULLIF(jd.job_type,''), 'OTHER')) AS job_type,
            COALESCE(NULLIF(jd.job_in_detail,''), jd.product_code) AS product_name,
            jd.machine_code,
            jd.total_qty,
            COALESCE(jd.value_amount, 0)                AS item_value,
            COALESCE(jd.item_discount, 0)               AS item_discount
        FROM jobcard_master jm
        INNER JOIN jobcard_details jd ON jd.jobcard_no = jm.jobcard_no
        $where_sql
        ORDER BY jm.jobcard_no DESC, jd.jobcard_details_id ASC";

$results = [];
$qry = mysqli_query($connection, $sql);
if ($qry) {
    while ($row = mysqli_fetch_assoc($qry)) {
        $results[] = $row;
    }
} else {
    // Return error info for debugging
    echo json_encode(['error' => mysqli_error($connection)]);
    exit;
}

echo json_encode($results);
?>
