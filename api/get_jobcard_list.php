<?php
/**
 * api/get_jobcard_list.php  — UPDATED
 * Actions cell converted to three-dot dropdown menu.
 */

session_start();
include_once "../connect_db.php";

$result=""; 

$dt_range     = $_POST['dt'];
$status_txt   = $_POST["status_txt"];

if ($status_txt == "void")
    $sql = "SELECT * FROM jobcard_master where void_job_card=1";
elseif ($status_txt != "all")
    $sql = "SELECT * FROM jobcard_master where job_card_closed=" . $status_txt . " and (void_job_card=0 or void_job_card is null)";
else
    $sql = "SELECT * FROM jobcard_master where 1";

if (strpos($dt_range, "~") === False)
{
    $dt    = date("Y-m-d", strtotime($_POST['dt']));
    $sql  .= " and jobcard_date >='" . $dt . "'";
}
else
{
    $dt_ary = explode("~", $_POST["dt"]);
    $sql   .= " and jobcard_date between '"
            . date("Y-m-d", strtotime($dt_ary[0])) . "' and '"
            . date("Y-m-d", strtotime($dt_ary[1])) . "' ";
}

$customer_code_txt = $_POST["customer_code_txt"];
if ($customer_code_txt != "all")
{
    if ($customer_code_txt == "walkin")
        $sql .= " and customer_type='WalkIn'";
    elseif ($customer_code_txt == "general")
        $sql .= " and customer_type='General'";
    else
        $sql .= " and customer_code='" . $customer_code_txt . "'";
}

$user_name_txt = $_POST["user_name_txt"];
$sess_user_type = $_SESSION['user_type'] ?? '';
$sess_user_name = $_SESSION['user_name'] ?? '';
if ($sess_user_type != "ADMIN" && $sess_user_type != "SUPERADMIN") {
    $sql .= " and created_by='" . mysqli_real_escape_string($connection, $sess_user_name) . "'";
} else {
    if ($user_name_txt != "all")
        $sql .= " and created_by='" . $_POST["user_name_txt"] . "'";
}

$pay_mode_txt = $_POST["pay_mode_txt"];

if ($_POST['dt'] == "ALL_OPEN_JC")
    $sql = "SELECT * FROM jobcard_master where job_card_closed=0 and created_by='" . $_POST["user_name_txt"] . "';";

$sql = rtrim(trim($sql), ';');
$sql = "SELECT jm_sub.*, IF(pqc.id IS NOT NULL, 1, 0) AS is_qr_pending,
               COALESCE(pqc.ext_transaction_id, '') AS pending_ext_txn_id
        FROM ($sql) jm_sub
        LEFT JOIN pending_qr_closures pqc
            ON pqc.status = 'PENDING'
           AND (
               pqc.jobcard_no = jm_sub.jobcard_no
               OR FIND_IN_SET(jm_sub.jobcard_no, COALESCE(pqc.jobcard_nos, ''))
           )";

$stmt   = $connection->query($sql);
$result = "";

// ── CSS only (no script here — script lives in dashboard.php) ──────────────
$result .= '
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
#jobcardDiv,
#jobcardDiv .dataTables_wrapper,
.cp-table-section {
    overflow: visible !important;
}
</style>
';

$result .= '<table class="table table-hover" id="jobcardTable">';
$result .= '<thead style="background:#1e293b;">';
$result .= '<tr style="background:#1e293b;">
    <th style="color:#fff;font-weight:800;text-transform:uppercase;font-size:0.78rem;letter-spacing:0.5px;">S.NO</th>
    <th style="color:#fff;font-weight:800;text-transform:uppercase;font-size:0.78rem;letter-spacing:0.5px;">JC NO</th>
    <th style="color:#fff;font-weight:800;text-transform:uppercase;font-size:0.78rem;letter-spacing:0.5px;white-space:nowrap;">DATE</th>
    <th style="color:#fff;font-weight:800;text-transform:uppercase;font-size:0.78rem;letter-spacing:0.5px;">CUSTOMER</th>
    <th style="color:#fff;font-weight:800;text-transform:uppercase;font-size:0.78rem;letter-spacing:0.5px;">USER</th>
    <th style="color:#fff;font-weight:800;text-transform:uppercase;font-size:0.78rem;letter-spacing:0.5px;">TIME</th>
    <th style="color:#fff;font-weight:800;text-transform:uppercase;font-size:0.78rem;letter-spacing:0.5px;">VALUE</th>
    <th style="color:#fff;font-weight:800;text-transform:uppercase;font-size:0.78rem;letter-spacing:0.5px;">ADVANCE</th>
    <th style="color:#fff;font-weight:800;text-transform:uppercase;font-size:0.78rem;letter-spacing:0.5px;">PAY MODE</th>
    <th style="color:#fff;font-weight:800;text-transform:uppercase;font-size:0.78rem;letter-spacing:0.5px;">TYPE</th>
    <th style="color:#fff;font-weight:800;text-transform:uppercase;font-size:0.78rem;letter-spacing:0.5px;">ACTIONS</th>
</tr>';
$result .= '<tr style="background:#334155;">
    <th style="padding:4px;"></th>
    <th style="padding:4px;"><input type="text" placeholder="Search JC..." style="padding:4px 8px;border:1px solid #cbd5e1;border-radius:4px;font-size:0.8rem;" /></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
    <th style="padding:4px;"></th>
</tr>';
$result .= '</thead><tbody>';
$sno = 0;
while ($row = $stmt->fetch_assoc())
{
    $td_str       = "<td style='text-align:center;'>";
    $highlightCSS = "";
    $customer_type = $row["customer_type"];

    if ($customer_type == "Credit") {
        $td_str       = '<td class="highlight" style="text-align:center;">';
        $highlightCSS = ' class="highlight"';
    }

    $row_style = '';
    if (!empty($row['is_qr_pending']) && $row['job_card_closed'] == 0) {
        $row_style = ' style="background-color:#fff3cd;"';
    }

    $Paymode = "";
    if ($row["advance_cash"] > 0)  { if ($Paymode != "") $Paymode .= ","; $Paymode  = "Cash"; }
    if ($row["advance_gpay"] > 0)  { if ($Paymode != "") $Paymode .= ","; $Paymode .= "Online"; }
    if ($row["customer_code"] != "") { if ($Paymode != "") $Paymode .= ","; $Paymode .= "Credit"; }

    $valid_customer = 1;
    if ($pay_mode_txt != "all") {
        if (strpos(strtoupper($Paymode), strtoupper($pay_mode_txt)) === False)
            $valid_customer = 0;
    }

    if ($valid_customer)
    {
        $customer_name = $row["customer_name"];
        if ($row["customer_type"] != "Credit") {
            if ($customer_name == "")
                $customer_name = $row["customer_type"];
            else
                $customer_name = $row["customer_type"] . "-" . $customer_name;
            $customer_mobile_no = $row["customer_mobile_no"];
            if ($customer_mobile_no != "")
                $customer_name .= " - " . $customer_mobile_no;
        }

        $raw_customer_name   = addslashes($row["customer_name"]);
        $raw_customer_mobile = addslashes($row["customer_mobile_no"]);

        $sno++;
        $jc_status = '';
        if ($row['void_job_card'] == 1) {
            $jc_status = 'void';
        } else if ($row['job_card_closed'] == 1) {
            $jc_status = 'completed';
        } else {
            $jc_status = 'in-progress';
        }
        $result .= '<tr data-id="' . $row["jobcard_no"] . '" data-status="' . $jc_status . '"' . $row_style . '>';
        $result .= '<td style="text-align:center;font-weight:600;font-size:0.82rem;">' . $sno . '</td>';

        // QR badge
        $qr_pending_badge = '';
        if (!empty($row['is_qr_pending']) && $row['job_card_closed'] == 0) {
            $qr_pending_badge = '<br><span style="display:inline-block;background:#ff9800;color:#000;'
                              . 'border-radius:4px;padding:1px 6px;font-size:0.68rem;font-weight:600;">'
                              . '⏳ Awaiting Payment</span>';
        }

        // JC No cell
        if ($row["job_card_closed"] == 0)
            $result .= '<td ' . $highlightCSS . ' style="text-align:center;">'
                     . '<input type="checkbox" class="form-check-input select_jcs"'
                     . ' value="' . $row["jobcard_no"] . '"'
                     . ' customer_type="' . $row["customer_type"] . '"'
                     . ' customer_name="' . $row["customer_name"] . '"'
                     . ' job_completed="' . $row["job_work_completed"] . '"/> '
                     . '<strong style="font-size:0.88rem;color:#1565c0;">' . $row["jobcard_no"] . '</strong>'
                     . $qr_pending_badge . '</td>';
        else
            $result .= '<td ' . $highlightCSS . ' style="background-color:#01f9c0;text-align:center;">'
                     . $row["jobcard_no"] . '<br><small>Closed</small></td>';

        $result .= '<td style="text-align:center;white-space:nowrap;">' . date("d-m-Y", strtotime($row["jobcard_date"])) . '</td>';
        $result .= '<td ' . $highlightCSS . ' style="font-weight:600;font-size:0.85rem;text-align:center;">' . $customer_name . '</td>';
        $result .= $td_str . $row["created_by"] . '</td>';
        $result .= $td_str . date("h:i:s", strtotime($row["created_dt_tm"])) . '</td>';
        $result .= $td_str . '<span style="font-weight:700;font-size:0.88rem;">' . $row["approximate_amount"] . '</span></td>';
        $result .= $td_str . '<span style="font-weight:700;font-size:0.88rem;color:#198754;">' . $row["advance_amount"] . '</span></td>';
        $result .= $td_str . $Paymode . '</td>';

        $highlightCSS2 = "";
        if ($customer_type == "Credit") $highlightCSS2 = ' class="highlight"';
        $result .= '<td ' . $highlightCSS2 . ' style="text-align:center;">' . $row["customer_type"] . '</td>';

        // ── Actions cell ────────────────────────────────────────────────────
        $jcno = intval($row["jobcard_no"]);
        $is_admin = ($sess_user_type == "ADMIN" || $sess_user_type == "SUPERADMIN");

        $result .= '<td style="text-align:center;vertical-align:middle;">';

        if ($is_admin) {
            // ── ADMIN: 3-dot dropdown ──
            $result .= '<div class="jc-action-wrap">';
            $result .= '<button class="jc-dot-btn" type="button" title="Actions" data-jcno="' . $jcno . '">&#8942;</button>';
            $result .= '<div class="jc-drop-menu">';

            $result .= '<a class="dm-print" href="jc_print_out.php?jc=' . $jcno . '" target="_blank">'
                     . '<span class="material-icons-round" style="font-size:15px;">print</span> Print</a>';

            if (!$row['void_job_card']) {
                $result .= '<button class="dm-edit" onclick="editJobCard(' . $jcno . ')">'
                         . '<span class="material-icons-round" style="font-size:15px;">edit</span> Edit</button>';
            }

            if ($row["job_work_completed"] || $row["job_card_closed"]) {
                $result .= '<button class="dm-done dm-disabled">'
                         . '<span class="material-icons-round" style="font-size:15px;">check_circle</span> Completed</button>';
            } else if (!$row['void_job_card']) {
                if ($customer_type == "WalkIn") {
                    $result .= '<button class="dm-prog" onclick="fnCompletePopUp(' . $jcno . ')">'
                             . '<span class="material-icons-round" style="font-size:15px;">pending</span> In Progress</button>';
                } else {
                    $result .= '<button class="dm-prog" onclick="fnDesignUploadPopUp('
                             . $jcno . ',\'' . $raw_customer_name . '\',\'' . $raw_customer_mobile . '\')">'
                             . '<span class="material-icons-round" style="font-size:15px;">pending</span> In Progress</button>';
                }
            }

            if (!empty($row['is_qr_pending']) && $row['job_card_closed'] == 0) {
                $pending_ext = addslashes($row['pending_ext_txn_id']);
                $gpay_amt    = number_format(floatval($row['advance_gpay']), 2, '.', '');
                $result .= '<button class="dm-check" id="qrCheckBtn_' . $jcno . '"'
                         . ' onclick="fnCheckQRPayment(' . $jcno . ', \'' . $pending_ext . '\')">'
                         . '<span class="material-icons-round" style="font-size:15px;">qr_code_scanner</span> Check Payment</button>';
                $result .= '<button class="dm-markpd" id="qrMarkBtn_' . $jcno . '"'
                         . ' onclick="fnForceMarkQRPaid(' . $jcno . ', \'' . $pending_ext . '\', \'' . $gpay_amt . '\')">'
                         . '<span class="material-icons-round" style="font-size:15px;">verified</span> Mark Paid</button>';
            }

            if ($row['void_job_card']) {
                $result .= '<button class="dm-voided dm-disabled">'
                         . '<span class="material-icons-round" style="font-size:15px;">block</span> Voided</button>';
            } elseif (!$row["job_card_closed"]) {
                $result .= '<button class="dm-void" onclick="fnMarkVoid(' . $jcno . ')">'
                         . '<span class="material-icons-round" style="font-size:15px;">block</span> Void</button>';
            }

            $result .= '</div></div>';
        } else {
            // ── Non-admin: inline buttons ──
            $result .= '<a class="btn btn-sm btn-primary m-1" target="_blank" href="jc_print_out.php?jc=' . $jcno . '">Print</a>';

            if (!$row['void_job_card']) {
                if (!$row["job_work_completed"] && !$row["job_card_closed"]) {
                    if (date("Y-m-d", strtotime($row["jobcard_date"])) == date("Y-m-d")) {
                        $result .= '<a class="btn btn-sm btn-warning m-1" onclick="editJobCard(' . $jcno . ')">Edit</a>';
                    }
                }
            }

            if ($row["job_work_completed"] || $row["job_card_closed"]) {
                $result .= '<span class="btn btn-sm btn-success m-1 disabled">Completed</span>';
            } else if (!$row['void_job_card']) {
                if ($customer_type == "WalkIn") {
                    $result .= '<button type="button" class="btn btn-warning btn-sm m-1" onclick="fnCompletePopUp(' . $jcno . ')">In Progress</button>';
                } else {
                    $result .= '<button type="button" class="btn btn-warning btn-sm m-1" onclick="fnDesignUploadPopUp('
                             . $jcno . ',\'' . $raw_customer_name . '\',\'' . $raw_customer_mobile . '\')">In Progress</button>';
                }
            }

            if (!empty($row['is_qr_pending']) && $row['job_card_closed'] == 0) {
                $pending_ext = addslashes($row['pending_ext_txn_id']);
                $gpay_amt    = number_format(floatval($row['advance_gpay']), 2, '.', '');
                $result .= '<button type="button" class="btn btn-info btn-sm m-1" id="qrCheckBtn_' . $jcno . '"'
                         . ' onclick="fnCheckQRPayment(' . $jcno . ', \'' . $pending_ext . '\')">Check Payment</button>';
                $result .= '<button type="button" class="btn btn-success btn-sm m-1" id="qrMarkBtn_' . $jcno . '"'
                         . ' onclick="fnForceMarkQRPaid(' . $jcno . ', \'' . $pending_ext . '\', \'' . $gpay_amt . '\')">Mark Paid</button>';
            }

            if ($row['void_job_card']) {
                $result .= '<span class="btn btn-sm btn-secondary m-1 disabled">Voided</span>';
            } elseif (!$row["job_card_closed"] && !$row["job_work_completed"]) {
                $result .= '<button type="button" class="btn btn-info btn-sm m-1" onclick="fnMarkVoid(' . $jcno . ')">Void</button>';
            }
        }

        $result .= '</td>';
        $result .= '</tr>';
    }
}

$result .= "</tbody></table>";
echo $result;