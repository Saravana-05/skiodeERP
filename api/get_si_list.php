<?php
/**
 * api/get_si_list.php — UPDATED
 * Actions cell converted to three-dot dropdown menu (same pattern as get_jobcard_list.php / get_sq_list.php).
 */

session_start();
include_once "../connect_db.php";

$dt_range = $_POST['dt'] ?? '';
$customer_code_txt = $_POST["customer_code_txt"] ?? 'all';
$user_name_txt     = $_POST["user_name_txt"]     ?? 'all';
$pay_mode_txt      = $_POST["pay_mode_txt"]      ?? 'all';

/* ── Build base SQL ─────────────────────────────────────────── */
$sql = "SELECT * FROM sales_invoice_master WHERE 1";

$sess_user_type = $_SESSION['user_type'] ?? '';
$sess_user_name = $_SESSION['user_name'] ?? '';
if ($sess_user_type == "OPERATOR") {
    $dt2 = date("Y-m-d");
    $dt1 = date('Y-m-d', strtotime($dt2 . ' - 2 days'));
    $sql .= " AND created_by='" . $connection->real_escape_string($sess_user_name) . "'"
          . " AND invoice_dt_tm BETWEEN '" . $dt1 . " 00:00:00' AND '" . $dt2 . " 23:59:59'";
} elseif (strpos($dt_range, "~") === false) {
    /* Single date — datepicker gives DD-MM-YYYY */
    $d = DateTime::createFromFormat('d-m-Y', trim($dt_range));
    if ($d) {
        $sql .= " AND DATE(invoice_dt_tm) = '" . $d->format('Y-m-d') . "'";
    }
} else {
    /* Date range DD-MM-YYYY~DD-MM-YYYY */
    $dt_ary = explode("~", $dt_range);
    $d_from = DateTime::createFromFormat('d-m-Y', trim($dt_ary[0]));
    $d_to   = DateTime::createFromFormat('d-m-Y', trim($dt_ary[1]));
    if ($d_from && $d_to) {
        $from_dt = $d_from->format('Y-m-d');
        $to_dt   = $d_to->format('Y-m-d');
        $sql .= " AND invoice_dt_tm BETWEEN '" . $from_dt . " 00:00:00' AND '" . $to_dt . " 23:59:59'";
    }
}

if ($user_name_txt != "all") {
    $sql .= " AND created_by='" . $connection->real_escape_string($user_name_txt) . "'";
}

$stmt = $connection->query($sql);

/* ── CSS only (script lives in dashboard.php — reuses .jc-action-wrap / .jc-dot-btn / .jc-drop-menu) ──────────────*/
$result  = '
<style>
.jc-action-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.jc-dot-btn {
    background: #334155;
    border: none;
    border-radius: 8px;
    color: #fff;
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1.4rem;
    font-weight: 900;
    transition: background 0.15s;
    line-height: 1;
    padding: 0;
    user-select: none;
}
.jc-dot-btn:hover {
    background: #4f46e5;
}
.jc-drop-menu {
    display: none;
    position: fixed;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 8px 32px rgba(15,23,42,0.22);
    z-index: 99999;
    min-width: 180px;
    overflow: hidden;
}
.jc-drop-menu.open {
    display: block;
}
.jc-drop-menu a,
.jc-drop-menu button {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 9px 16px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #1e293b;
    background: none;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    text-decoration: none;
    text-align: left;
    transition: background 0.12s;
    box-sizing: border-box;
    font-family: inherit;
    line-height: 1.3;
}
.jc-drop-menu a:last-child,
.jc-drop-menu button:last-child { border-bottom: none; }
.jc-drop-menu a:hover,
.jc-drop-menu button:hover { background: #f1f5f9; }
.jc-drop-menu .dm-print  { color: #4f46e5; }
.jc-drop-menu .dm-edit   { color: #d97706; }
.jc-drop-menu .dm-prog   { color: #f59e0b; }
.jc-drop-menu .dm-done   { color: #059669; }
.jc-drop-menu .dm-check  { color: #0891b2; }
.jc-drop-menu .dm-markpd { color: #16a34a; }
.jc-drop-menu .dm-void   { color: #dc2626; }
.jc-drop-menu .dm-voided { color: #94a3b8; font-style:italic; }
.jc-drop-menu .dm-disabled {
    color: #94a3b8 !important;
    cursor: not-allowed !important;
    pointer-events: none;
    background: #f8fafc !important;
}
#siDiv,
#siDiv .dataTables_wrapper,
.cp-table-section {
    overflow: visible !important;
}
</style>
';

/* ── Table header ───────────────────────────────────────────── */
$result .= '<table class="table table-hover" id="siTable" style="width:100%;">';
$result .= '<thead style="background:#1e293b;">';
$th = 'style="color:#fff;font-weight:800;text-transform:uppercase;font-size:0.78rem;letter-spacing:0.5px;"';
$result .= '<tr style="background:#1e293b;">
    <th '.$th.'>S.NO</th>
    <th '.$th.'>INV NO</th>
    <th '.$th.' style="color:#fff;font-weight:800;text-transform:uppercase;font-size:0.78rem;letter-spacing:0.5px;white-space:nowrap;">DATE</th>
    <th '.$th.'>SQ NOS</th>
    <th '.$th.'>CUSTOMER</th>
    <th '.$th.'>AMOUNT</th>
    <th '.$th.'>PAYMODE</th>
    <th '.$th.'>USER</th>
    <th '.$th.'>ACTIONS</th>
</tr>';
$result .= '<tr style="background:#334155;">
    <th style="padding:4px;"></th>
    <th style="padding:4px;"><input type="text" placeholder="Search Inv..." style="padding:4px 8px;border:1px solid #cbd5e1;border-radius:4px;font-size:0.8rem;" /></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
</tr>';
$result .= '</thead><tbody>';
$si_sno = 0;

while ($row = $stmt->fetch_assoc()) {

    /* ── Reset per-row variables ── */
    $jcnos            = "";
    $sq_nos           = $row["sale_quotation_nos"];
    $si_customer_code = $row["customer_code"];
    $Paymode          = "";
    $customer_type    = "";
    $customer_code    = "";
    $customer_name    = "";
    $customer_addr1   = "";
    $customer_addr2   = "";
    $customer_city    = "";
    $customer_mobile_no = "";
    $customer_gst_no  = "";
    $customer_details = "";
    $valid_customer   = 1; // must initialise BEFORE any filtering below

    /* ── Resolve SQ → JC chain ── */
    if (!empty($sq_nos)) {
        $sq_det_sql = "SELECT * FROM sales_quotation_master WHERE quotation_no IN (" . $sq_nos . ");";
        $pay_cash   = 0;
        $pay_online = 0;
        $pay_credit = 0;

        if ($sq_det_qry = mysqli_query($connection, $sq_det_sql)) {
            while ($sq_det_row = mysqli_fetch_array($sq_det_qry)) {
                if ($jcnos != "") $jcnos .= ",";
                $jcnos .= $sq_det_row["jobcard_nos"];

                // Accumulate across ALL SQ rows — don't reset each iteration
                if ($sq_det_row["balance_cash"]            > 0) $pay_cash   = 1;
                if ($sq_det_row["balance_gpay"]            > 0) $pay_online = 1;
                if ($sq_det_row["balance_received_total"] == 0) $pay_credit = 1;
            }
        }

        $modes = [];
        if ($pay_cash)   $modes[] = "Cash";
        if ($pay_online) $modes[] = "Online";
        if ($pay_credit) $modes[] = "Credit";
        $Paymode = implode(", ", $modes);

        // Pay-mode filter
        if ($pay_mode_txt != "all") {
            if (stripos($Paymode, $pay_mode_txt) === false) {
                $valid_customer = 0;
            }
        }
    }

    /* ── Resolve JC → customer details ── */
    if (!empty($jcnos)) {
        $jc_sql = "SELECT * FROM jobcard_master WHERE jobcard_no IN (" . $jcnos . ") LIMIT 1;";
        if ($jc_qry = mysqli_query($connection, $jc_sql)) {
            if ($jcrows = mysqli_fetch_array($jc_qry)) {
                $customer_type      = $jcrows["customer_type"]      ?? '';
                $customer_code      = $jcrows["customer_code"]      ?? '';
                $customer_name      = $jcrows["customer_name"]      ?? '';
                $customer_addr1     = $jcrows["customer_addr1"]     ?? '';
                $customer_addr2     = $jcrows["customer_addr2"]     ?? '';
                $customer_city      = $jcrows["customer_city"]      ?? '';
                $customer_mobile_no = $jcrows["customer_mobile_no"] ?? '';
                $customer_gst_no    = $jcrows["customer_gst_no"]    ?? '';
            }
        }

        $parts = array_filter([
            $customer_name,
            $customer_addr1,
            $customer_addr2,
            $customer_city,
            $customer_gst_no    ? "GST: "    . $customer_gst_no    : '',
            $customer_mobile_no ? "Mobile: " . $customer_mobile_no : '',
        ]);
        $customer_details = implode("<br>", $parts);

    } elseif (!empty($si_customer_code)) {
        // Credit-only invoice (no JC chain)
        $customer_type = "Credit";
        $Paymode       = "Credit";
        $cus_sql = "SELECT customer_name FROM customer_master WHERE customer_code='"
                 . $connection->real_escape_string($si_customer_code) . "';";
        if ($cus_qry = mysqli_query($connection, $cus_sql)) {
            if ($cus_row = mysqli_fetch_array($cus_qry)) {
                $customer_name = $cus_row["customer_name"];
            }
        }

        // Pay-mode filter for credit-only invoices
        if ($pay_mode_txt != "all" && stripos($Paymode, $pay_mode_txt) === false) {
            $valid_customer = 0;
        }
    }

    /* ── Customer filter ── */
    if ($customer_code_txt != "all") {
        $match = (strcasecmp($customer_code_txt, $customer_type) === 0)
               || (strcasecmp($customer_code_txt, $customer_code) === 0);
        if (!$match) {
            $valid_customer = 0;
        }
    }

    /* ── Render row ── */
    if ($valid_customer) {
        $si_sno++;
        $sino = (int)$row["invoice_no"];

        $result .= '<tr data-id="' . $row["invoice_no"] . '">';
        $result .= '<td style="text-align:center;font-weight:600;font-size:0.82rem;">' . $si_sno . '</td>';
        $result .= '<td><input type="checkbox" class="form-check-input select_sqs" value="' . $row["invoice_no"] . '" /> ' . $row["invoice_no"] . '</td>';
        $result .= '<td>' . date("d-m-Y", strtotime($row["invoice_dt_tm"])) . '</td>';
        $result .= '<td>' . htmlspecialchars($row["sale_quotation_nos"]) . '</td>';
        $result .= '<td><span class="bold-customer">' . htmlspecialchars($customer_name) . '</span></td>';
        $result .= '<td><span class="bold-amount">' . htmlspecialchars($row["net_total"]) . '</span></td>';
        $result .= '<td>' . htmlspecialchars($Paymode) . '</td>';
        $result .= '<td>' . htmlspecialchars($row["created_by"]) . '</td>';

        // ── Actions cell ──
        $is_admin_si = ($sess_user_type == 'ADMIN' || $sess_user_type == 'SUPERADMIN');
        $result .= '<td style="text-align:center;vertical-align:middle;">';
        if (empty($si_customer_code)) {
            if ($is_admin_si) {
                // ── ADMIN: 3-dot dropdown ──
                $result .= '<div class="jc-action-wrap">';
                $result .= '<button class="jc-dot-btn" type="button" title="Actions" data-sino="' . $sino . '">&#8942;</button>';
                $result .= '<div class="jc-drop-menu">';

                $result .= '<a class="dm-print" target="_blank" href="sale_invoice_print.php?si=' . $sino . '">'
                         . '<span class="material-icons-round" style="font-size:15px;">print</span> Print</a>';

                $result .= '<button class="dm-prog" onclick="showJobCard(\'' . addslashes($jcnos) . '\',\'' . addslashes($row["sale_quotation_nos"]) . '\',' . $sino . ')">'
                         . '<span class="material-icons-round" style="font-size:15px;">visibility</span> Show</button>';

                $result .= '</div></div>';
            } else {
                // ── Non-admin: inline buttons ──
                $result .= '<a class="btn btn-sm btn-primary m-1" target="_blank" href="sale_invoice_print.php?si=' . $sino . '">Print</a>';
                $result .= '<a class="btn btn-sm btn-warning m-1" onclick="showJobCard(\'' . addslashes($jcnos) . '\',\'' . addslashes($row["sale_quotation_nos"]) . '\',' . $sino . ')">Show</a>';
            }
        }
        $result .= '</td>';

        $result .= '</tr>';
    }
}

$result .= '</tbody></table>';
echo $result;
?>