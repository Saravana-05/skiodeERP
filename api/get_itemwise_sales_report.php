<?php
session_start();
include_once '../connect_db.php';

if (!isset($_POST['from'])) { echo json_encode([]); exit; }

$from         = date("Y-m-d", strtotime($_POST['from']));
$to           = date("Y-m-d", strtotime($_POST['to']));
$user_txt     = $_POST["user_name_txt"];
$job_type_txt = $_POST["job_type_txt"];
$cat2_txt     = isset($_POST["cat2_txt"]) ? $_POST["cat2_txt"] : "all";
$show_summary = intval($_POST['show_summary_chk']);

$user_esc = mysqli_real_escape_string($connection, $user_txt);
$type_esc = mysqli_real_escape_string($connection, $job_type_txt);
$cat2_esc = mysqli_real_escape_string($connection, $cat2_txt);

// ═══════════════════════════════════════════════════════════
// Itemwise Sales (material consumption)
// category2 from product_master via jobcard_details
// ═══════════════════════════════════════════════════════════
// Subquery: category per JC+material, covering both default AND alternative materials.
// Default: product_material_details.material_code
// Alternative: materials in product_material_details_alternatives linked via alternative_key
$mat_cat_subquery = "(SELECT jd.jobcard_no, all_mats.mat_code AS material_code, MIN(pm.category2) AS category2
                      FROM jobcard_details jd
                      JOIN (
                          SELECT product_code, material_code AS mat_code
                          FROM product_material_details
                          UNION
                          SELECT pmd.product_code, pmda.material_code AS mat_code
                          FROM product_material_details pmd
                          JOIN product_material_details_alternatives pmda
                               ON pmda.alternative_key = pmd.alternative_key
                          WHERE pmd.alternative_key IS NOT NULL
                      ) all_mats ON all_mats.product_code = jd.product_code
                      JOIN product_master pm ON pm.product_code = jd.product_code
                      GROUP BY jd.jobcard_no, all_mats.mat_code)";

// Filter by job_type / category2 via EXISTS — avoids row multiplication
$exists_clause = "";
$ex = "";
if ($job_type_txt !== "all") $ex .= " AND jd2.job_type = '$type_esc'";
if ($cat2_txt     !== "all") $ex .= " AND pm2.category2 = '$cat2_esc'";
if ($ex !== "") {
    $exists_clause = " AND EXISTS (
        SELECT 1 FROM jobcard_details jd2
        JOIN (
            SELECT product_code, material_code AS mat_code FROM product_material_details
            UNION
            SELECT pmd_x.product_code, pmda_x.material_code AS mat_code
            FROM product_material_details pmd_x
            JOIN product_material_details_alternatives pmda_x ON pmda_x.alternative_key = pmd_x.alternative_key
            WHERE pmd_x.alternative_key IS NOT NULL
        ) all_mats2 ON all_mats2.product_code = jd2.product_code AND all_mats2.mat_code = a.material_code
        JOIN product_master pm2 ON pm2.product_code = jd2.product_code
        WHERE jd2.jobcard_no = a.jobcard_no $ex)";
}

if ($show_summary) {
    $sql = "SELECT b.material_name,
                   GROUP_CONCAT(DISTINCT COALESCE(mc.category2,'—') ORDER BY mc.category2 SEPARATOR ', ') as category2,
                   SUM(a.usage_count) as total_qty,
                   b.selling_price as rate,
                   SUM(a.usage_count * COALESCE(b.selling_price,0)) as total_amount
            FROM job_card_wise_material_usage a
            JOIN material_master b  ON a.material_code = b.material_code
            JOIN jobcard_master  jm ON a.jobcard_no    = jm.jobcard_no
            LEFT JOIN $mat_cat_subquery mc ON mc.jobcard_no = a.jobcard_no AND mc.material_code = a.material_code
            WHERE a.jobcard_date BETWEEN '$from' AND '$to'";
    if ($user_txt !== "all") $sql .= " AND jm.created_by = '$user_esc'";
    $sql .= $exists_clause;
    $sql .= " GROUP BY b.material_code, b.material_name, b.selling_price ORDER BY b.material_name";
} else {
    $sql = "SELECT a.jobcard_no, b.material_name,
                   COALESCE(mc.category2,'—') as category2,
                   a.usage_count as total_qty,
                   b.selling_price as rate,
                   (a.usage_count * COALESCE(b.selling_price,0)) as total_amount,
                   jm.created_by
            FROM job_card_wise_material_usage a
            JOIN material_master b  ON a.material_code = b.material_code
            JOIN jobcard_master  jm ON a.jobcard_no    = jm.jobcard_no
            LEFT JOIN $mat_cat_subquery mc ON mc.jobcard_no = a.jobcard_no AND mc.material_code = a.material_code
            WHERE a.jobcard_date BETWEEN '$from' AND '$to'";
    if ($user_txt !== "all") $sql .= " AND jm.created_by = '$user_esc'";
    $sql .= $exists_clause;
    $sql .= " ORDER BY a.jobcard_no, b.material_name";
}

$iw_rows         = '';
$iw_total_qty    = 0;
$iw_total_amount = 0;
$i = 1;
if ($q = mysqli_query($connection, $sql)) {
    while ($r = mysqli_fetch_assoc($q)) {
        $qty    = floatval($r['total_qty']);
        $rate   = floatval($r['rate']);
        $amount = floatval($r['total_amount']);
        $iw_total_qty    += $qty;
        $iw_total_amount += $amount;

        $iw_rows .= '<tr>';
        $iw_rows .= '<td>'.$i.'</td>';
        if (!$show_summary) $iw_rows .= '<td>'.$r['jobcard_no'].'</td>';
        $iw_rows .= '<td>'.htmlspecialchars($r['material_name']).'</td>';
        $iw_rows .= '<td>'.htmlspecialchars($r['category2']).'</td>';
        $iw_rows .= '<td style="text-align:right;">'.ind_money($qty,0,false).'</td>';
        $iw_rows .= '<td style="text-align:right;">'.ind_money($rate,2).'</td>';
        $iw_rows .= '<td style="text-align:right;font-weight:bold;">'.ind_money($amount,2).'</td>';
        if (!$show_summary) $iw_rows .= '<td>'.htmlspecialchars($r['created_by']).'</td>';
        $iw_rows .= '</tr>';
        $i++;
    }
}

// Build table HTML
// Summary cols: S.No | Material Name | Category | Qty | Rate | Amount         = 6
// Detail cols:  S.No | JC No | Material Name | Category | Qty | Rate | Amount | User = 8
$iw_table  = '<table class="table table-bordered table-hover table-condensed table-striped" id="iwDetailTable">';
$iw_table .= '<thead class="table-dark"><tr>';
$iw_table .= '<th>S.No</th>';
if (!$show_summary) $iw_table .= '<th>JC No</th>';
$iw_table .= '<th>Material Name</th><th>Sub Category</th>';
$iw_table .= '<th style="text-align:right;">Qty Used</th>';
$iw_table .= '<th style="text-align:right;">Product Price (₹)</th>';
$iw_table .= '<th style="text-align:right;">Amount (₹)</th>';
if (!$show_summary) $iw_table .= '<th>User</th>';
$iw_table .= '</tr></thead>';
$iw_table .= '<tfoot><tr style="background:#fdff9f;font-weight:bold;">';
$iw_table .= '<td colspan="'.($show_summary?3:4).'" style="text-align:right;">TOTAL</td>';
$iw_table .= '<td style="text-align:right;">'.ind_money($iw_total_qty,0,false).'</td>';
$iw_table .= '<td></td>';
$iw_table .= '<td style="text-align:right;color:green;">'.ind_money($iw_total_amount,2).'</td>';
if (!$show_summary) $iw_table .= '<td></td>';
$iw_table .= '</tr></tfoot>';
$iw_table .= '<tbody>'.$iw_rows.'</tbody>';
$iw_table .= '</table>';

// Summary panel
$summary_html  = '<div class="text-center bg-primary text-white"><h6 class="mb-0 py-1">SUMMARY</h6></div>';
$summary_html .= '<table width="100%" style="font-size:0.8rem;">';
$summary_html .= '<tr><td align="right">Total Qty&nbsp;:</td><td><div style="border:1px solid black;text-align:right;padding:2px;">'.ind_money($iw_total_qty,0,false).'</div></td></tr>';
$summary_html .= '<tr><td align="right" style="color:green;font-weight:bold;">Total Amt&nbsp;:</td><td><div style="border:2px solid green;color:green;font-weight:bold;text-align:right;padding:2px;">'.ind_money($iw_total_amount,2).'</div></td></tr>';
$summary_html .= '</table>';

echo json_encode([
    'iw_table'     => $iw_table,
    'summary_html' => $summary_html,
]);
?>
