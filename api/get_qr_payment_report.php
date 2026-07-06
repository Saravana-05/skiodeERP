<?php
mysqli_report(MYSQLI_REPORT_OFF);
session_start();
include_once __DIR__ . '/../connect_db.php';
include_once __DIR__ . '/canara_bank_lib.php';

if (!isset($connection) || $connection === false) {
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

cb_ensure_table($connection);

if (!isset($_POST['from'])) { echo json_encode([]); exit; }

$from   = date("Y-m-d", strtotime($_POST['from']));
$to     = date("Y-m-d", strtotime($_POST['to']));
$type   = isset($_POST['type'])   ? $_POST['type']   : 'all';
$status = isset($_POST['status']) ? $_POST['status'] : 'all';
$user   = isset($_POST['user'])   ? trim($_POST['user']) : 'all';

$type_esc   = mysqli_real_escape_string($connection, $type);
$status_esc = mysqli_real_escape_string($connection, $status);
$user_esc   = mysqli_real_escape_string($connection, $user);

// ═══════════════════════════════════════════════════════════════════════════════
// STEP 1: Refresh transactions from bank via /api/bank/qr-statement (bulk)
// ═══════════════════════════════════════════════════════════════════════════════
// Calls the bank statement API for the date range, then cross-references with
// local canara_transactions using ext_transaction_id to update payment status.

$stmt_from_wider = date('Y-m-d', strtotime($from . ' -7 days'));

// Get access token for FastAPI qr-statement endpoint
$stmt_access_token = '';
try { $stmt_access_token = cb_get_valid_token(); } catch (Exception $e) {
    cb_log("[QRReport] Could not get access token: " . $e->getMessage());
}

$stmt_payload = json_encode([
    'startDate'    => $stmt_from_wider . ' 00:00:00',
    'endDate'      => $to   . ' 23:59:59',
    'pageSize'     => '500',
    'pageNo'       => '0',
    'access_token' => $stmt_access_token,
]);
$ch = curl_init('https://api.citizenprintz.in/api/bank/qr-statement');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $stmt_payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
]);
$stmt_resp = curl_exec($ch);
$stmt_http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// ── DEBUG: collect info to return in JSON response ──────────────────────────
$_DEBUG = [];
$_DEBUG['php_timezone'] = date_default_timezone_get();
$_DEBUG['php_now'] = date('Y-m-d H:i:s');
$_DEBUG['bank_api_http_code'] = $stmt_http;
$_DEBUG['bank_api_raw_response'] = $stmt_resp ? json_decode($stmt_resp, true) : $stmt_resp;

// DB paid_at values for today's transactions
$db_debug_q = mysqli_query($connection,
    "SELECT ext_transaction_id, status, paid_at, created_at, updated_at
     FROM canara_transactions WHERE DATE(created_at) = '$from' OR DATE(paid_at) = '$from'
     ORDER BY created_at DESC LIMIT 30");
$_DEBUG['db_rows'] = [];
if ($db_debug_q) {
    while ($dd = mysqli_fetch_assoc($db_debug_q)) {
        $_DEBUG['db_rows'][] = $dd;
    }
}

if ($stmt_http === 200 && $stmt_resp) {
    $stmt_json = json_decode($stmt_resp, true);
    $bank_txns = [];

    // Extract transaction list from response — handle nested structures
    if (isset($stmt_json['data']['data']) && is_array($stmt_json['data']['data'])) {
        $bank_txns = $stmt_json['data']['data'];
    } elseif (isset($stmt_json['data']['txnList']) && is_array($stmt_json['data']['txnList'])) {
        $bank_txns = $stmt_json['data']['txnList'];
    } elseif (isset($stmt_json['data']) && is_array($stmt_json['data']) && isset($stmt_json['data'][0])) {
        $bank_txns = $stmt_json['data'];
    }

    // DEBUG: capture first 3 bank transactions with ALL their keys
    $_DEBUG['bank_txns_count'] = count($bank_txns);
    $_DEBUG['bank_txns_sample'] = array_slice($bank_txns, 0, 3);

    // Index bank transactions by extTransactionId for fast lookup
    $bank_by_ext = [];
    foreach ($bank_txns as $btxn) {
        $ext = $btxn['extTransactionId'] ?? $btxn['ext_transaction_id'] ?? $btxn['merchantTxnId'] ?? '';
        if ($ext) $bank_by_ext[$ext] = $btxn;
    }

    // Fetch all non-SUCCESS local transactions in date range
    $refresh_sql = "SELECT ext_transaction_id, id
                    FROM canara_transactions
                    WHERE DATE(created_at) BETWEEN DATE_SUB('$from', INTERVAL 7 DAY) AND '$to'
                      AND status NOT IN ('SUCCESS')
                      AND ext_transaction_id IS NOT NULL
                      AND ext_transaction_id != ''";
    $refresh_res = mysqli_query($connection, $refresh_sql);
    if ($refresh_res) {
        $now_u = date('Y-m-d H:i:s');
        while ($rt = mysqli_fetch_assoc($refresh_res)) {
            $ext_id = $rt['ext_transaction_id'];
            if (!isset($bank_by_ext[$ext_id])) continue;

            $btxn = $bank_by_ext[$ext_id];
            $raw  = $btxn['txnStatus'] ?? $btxn['status'] ?? $btxn['paymentStatus'] ?? '';
            $s    = strtoupper(trim((string)$raw));

            if (in_array($s, ['SUCCESS','PAID','CREDIT','SUCCESSFUL','APPROVED','COMPLETE','S'], true))
                $s = 'SUCCESS';
            elseif (in_array($s, ['FAILURE','FAILED','FAIL','DECLINED','REJECTED','F'], true))
                $s = 'FAILED';
            elseif (in_array($s, ['PENDING','INITIATED','IN_PROGRESS','PROCESSING','P'], true))
                $s = 'PENDING';

            if ($s !== 'SUCCESS') continue;

            $safe_id  = mysqli_real_escape_string($connection, $ext_id);
            $rrn      = mysqli_real_escape_string($connection, (string)($btxn['rrn'] ?? $btxn['bankRrn'] ?? $btxn['bankRRN'] ?? $btxn['utr'] ?? ''));
            $txn_id   = mysqli_real_escape_string($connection, (string)($btxn['txnId'] ?? $btxn['txn_id'] ?? $btxn['bankTxnId'] ?? ''));
            $cust_vpa = mysqli_real_escape_string($connection, (string)($btxn['payerVpa'] ?? $btxn['customer_vpa'] ?? ''));

            $safe_paid = mysqli_real_escape_string($connection, $now_u);

            $update_sql = "UPDATE canara_transactions
                SET status     = 'SUCCESS',
                    paid_at    = '$safe_paid',
                    updated_at = '$now_u'"
                . ($rrn      ? ", rrn          = '$rrn'"      : '')
                . ($txn_id   ? ", txn_id       = '$txn_id'"   : '')
                . ($cust_vpa ? ", customer_vpa = '$cust_vpa'" : '')
                . " WHERE ext_transaction_id = '$safe_id' AND status != 'SUCCESS'";
            mysqli_query($connection, $update_sql);

            // Also update advance_payment_master if applicable
            $adv_col_chk = mysqli_query($connection, "SHOW COLUMNS FROM advance_payment_master LIKE 'ext_transaction_id'");
            if ($adv_col_chk && mysqli_num_rows($adv_col_chk) > 0) {
                mysqli_query($connection,
                    "UPDATE advance_payment_master SET qr_payment_status = 'PAID'
                     WHERE ext_transaction_id = '$safe_id' AND qr_payment_status = 'PENDING'");
            }
        }
    }

    // ── Second pass: fix paid_at that was stored in UTC by FastAPI webhook ──────
    // The webhook uses datetime.utcnow() but PHP uses Asia/Kolkata (IST = UTC+5:30).
    // Detect: if paid_at is ~5:30 hours behind created_at, it's UTC → add 5:30 hours.
    $fix_sql = "SELECT id, paid_at, created_at FROM canara_transactions
                WHERE (DATE(paid_at) BETWEEN '$from' AND '$to' OR DATE(created_at) BETWEEN '$from' AND '$to')
                  AND status = 'SUCCESS'
                  AND paid_at IS NOT NULL
                  AND TIMESTAMPDIFF(MINUTE, paid_at, created_at) BETWEEN 320 AND 340";
    $fix_res = mysqli_query($connection, $fix_sql);
    if ($fix_res) {
        while ($fr = mysqli_fetch_assoc($fix_res)) {
            $corrected = date('Y-m-d H:i:s', strtotime($fr['paid_at'] . ' +5 hours +30 minutes'));
            $safe_corr = mysqli_real_escape_string($connection, $corrected);
            mysqli_query($connection,
                "UPDATE canara_transactions SET paid_at = '$safe_corr' WHERE id = " . intval($fr['id']));
        }
    }
}

// ═══════════════════════════════════════════════════════════════════════════════
// STEP 2: Build the report from the now-refreshed local DB
// ═══════════════════════════════════════════════════════════════════════════════

$where = "WHERE (
              (ct.paid_at IS NOT NULL AND DATE(ct.paid_at) BETWEEN '$from' AND '$to')
              OR (ct.paid_at IS NULL AND DATE(ct.created_at) BETWEEN '$from' AND '$to')
          )
          AND ct.status = 'SUCCESS'";

if ($type === 'jobcard') {
    $where .= " AND ct.jobcard_no IS NOT NULL AND ct.jobcard_no != '' AND (ct.payment_source IS NULL OR ct.payment_source = 'JC')";
} elseif ($type === 'sq') {
    $where .= " AND ct.payment_source = 'SQ'";
} elseif ($type === 'advance') {
    $where .= " AND (ct.jobcard_no IS NULL OR ct.jobcard_no = '')";
}

if ($user !== 'all' && $user !== '') {
    $where .= " AND jm.created_by = '$user_esc'";
}

$sql = "SELECT
            ct.id,
            ct.ext_transaction_id,
            ct.jobcard_no,
            ct.payment_source,
            ct.amount,
            ct.status,
            ct.rrn,
            ct.txn_id,
            ct.customer_vpa,
            ct.created_at,
            ct.paid_at,
            ct.qr_string,
            ct.payment_change_reason,
            CASE
                WHEN ct.payment_source = 'SQ' THEN 'Sales Quote'
                WHEN ct.payment_source = 'JC' THEN 'JC Advance'
                WHEN (ct.jobcard_no IS NOT NULL AND ct.jobcard_no != '') THEN 'JC Advance'
                ELSE 'Advance Payment'
            END AS pay_type,
            CASE
                WHEN ct.customer_name IS NOT NULL AND ct.customer_name != '' THEN ct.customer_name
                WHEN jm.customer_name IS NOT NULL AND jm.customer_name != '' THEN jm.customer_name
                WHEN cm.customer_name IS NOT NULL AND cm.customer_name != '' THEN cm.customer_name
                WHEN jm.customer_type IS NOT NULL AND jm.customer_type != '' THEN jm.customer_type
                ELSE '—'
            END AS customer_name,
            COALESCE(NULLIF(jm.created_by, ''), '—') AS jc_created_by,
            pqc.converted_sq_no AS pqc_sq_no
        FROM canara_transactions ct
        LEFT JOIN jobcard_master jm ON jm.jobcard_no = ct.jobcard_no
        LEFT JOIN customer_master cm ON cm.customer_code = jm.customer_code
        LEFT JOIN pending_qr_closures pqc ON pqc.jobcard_no = ct.jobcard_no
            AND pqc.status IN ('CONVERTED','PENDING')
        $where
        GROUP BY ct.id
        ORDER BY COALESCE(ct.paid_at, ct.created_at) DESC";

$rows = [];
$total_amt = 0;

if ($q = mysqli_query($connection, $sql)) {
    while ($r = mysqli_fetch_assoc($q)) {
        $sq_num = '';
        if (!empty($r['pqc_sq_no'])) {
            $sq_num = $r['pqc_sq_no'];
        } elseif (!empty($r['jobcard_no'])) {
            $jcno_s = intval($r['jobcard_no']);
            $sq_lk  = mysqli_query($connection,
                "SELECT quotation_no FROM sales_quotation_master
                 WHERE FIND_IN_SET($jcno_s, REPLACE(jobcard_nos,' ','')) > 0
                 LIMIT 1");
            if ($sq_lk && $sq_r = mysqli_fetch_assoc($sq_lk)) {
                $sq_num = $sq_r['quotation_no'];
            }
        }
        $r['sq_no_resolved'] = $sq_num;
        $rows[] = $r;
        $total_amt += floatval($r['amount']);
    }
}

// ── Build table HTML ──────────────────────────────────────────────────────────
$table  = '<table class="table table-bordered table-hover table-condensed table-striped" id="qrrTable">';
$table .= '<thead class="table-dark"><tr>';
$table .= '<th style="text-align:center;">#</th>';
$table .= '<th>Paid At</th>';
$table .= '<th>Type</th>';
$table .= '<th>JC No</th>';
$table .= '<th>SQ No</th>';
$table .= '<th>Customer Name</th>';
$table .= '<th>User</th>';
$table .= '<th style="text-align:right;">Amount (₹)</th>';
$table .= '<th style="text-align:center;">QR</th>';
$table .= '</tr></thead>';

$table .= '<tfoot><tr style="background:#fdff9f;font-weight:bold;">';
$table .= '<td colspan="7" style="text-align:right;">TOTAL RECEIVED</td>';
$table .= '<td style="text-align:right;color:#198754;font-weight:bold;">+ '.ind_money($total_amt, 2).'</td>';
$table .= '<td></td>';
$table .= '</tr></tfoot>';

$tbody = '';
$sno = count($rows);
foreach ($rows as $r) {
    $amt      = floatval($r['amount']);
    $pay_type = $r['pay_type'];

    if ($pay_type === 'Sales Quote') {
        $t_badge = '<span class="badge-sq">SQ Balance</span>';
    } elseif ($pay_type === 'JC Advance') {
        $t_badge = '<span class="badge-jc">JC Advance</span>';
    } else {
        $t_badge = '<span class="badge-adv">Adv Payment</span>';
    }

    $jc_no     = !empty($r['jobcard_no']) ? htmlspecialchars($r['jobcard_no']) : '—';
    $sq_no     = !empty($r['sq_no_resolved']) ? htmlspecialchars($r['sq_no_resolved']) : '—';
    $cust_name = htmlspecialchars($r['customer_name']  ?? '—');
    $jc_user   = htmlspecialchars($r['jc_created_by']  ?? '—');
    $paid_at = !empty($r['paid_at']) ? date('d-m-Y h:i A', strtotime($r['paid_at'])) : date('d-m-Y h:i A', strtotime($r['created_at']));

    // QR cell
    $qr_str = $r['qr_string'] ?? '';
    if ($qr_str) {
        $qr_attr = htmlspecialchars($qr_str, ENT_QUOTES, 'UTF-8');
        $qr_cell = '<td style="text-align:center;vertical-align:middle;">'
                 . '<div class="qr-thumb" data-qr="'.$qr_attr.'" '
                 . 'style="width:60px;height:60px;cursor:pointer;display:inline-block;'
                 . 'opacity:0.35;border:1px dashed #999;border-radius:4px;padding:2px;" '
                 . 'title="Click to zoom (Paid)"></div>'
                 . '</td>';
    } else {
        $qr_cell = '<td style="text-align:center;color:#bbb;">—</td>';
    }

    $tbody .= '<tr>';
    $tbody .= '<td style="text-align:center;">'.$sno--.'</td>';
    $tbody .= '<td>'.$paid_at.'</td>';
    $tbody .= '<td>'.$t_badge.'</td>';
    $tbody .= '<td>'.$jc_no.'</td>';
    $tbody .= '<td>'.$sq_no.'</td>';
    $tbody .= '<td>'.$cust_name.'</td>';
    $tbody .= '<td>'.$jc_user.'</td>';
    $tbody .= '<td style="text-align:right;font-weight:bold;color:#198754;">+ '.ind_money($amt, 2).'</td>';
    $tbody .= $qr_cell;
    $tbody .= '</tr>';
}

$table .= '<tbody>'.$tbody.'</tbody></table>';

// ── Summary panel ─────────────────────────────────────────────────────────────
$summary  = '<div class="text-center bg-success text-white"><h6 class="mb-0 py-1">PAYMENT SUMMARY</h6></div>';
$summary .= '<table width="100%" style="font-size:0.8rem;">';
$summary .= '<tr><td align="right">Total Txns&nbsp;:</td><td><div style="border:1px solid black;text-align:right;padding:2px;">'.count($rows).'</div></td></tr>';
$summary .= '<tr><td colspan="2"><hr style="margin:4px 0;"></td></tr>';
$summary .= '<tr><td align="right" style="color:green;font-weight:bold;">Total Received&nbsp;:</td><td><div style="border:2px solid green;color:green;font-weight:bold;text-align:right;padding:2px;">'.ind_money($total_amt, 2).'</div></td></tr>';
$summary .= '</table>';

echo json_encode([
    'table_html'   => $table,
    'summary_html' => $summary,
    '_DEBUG'        => $_DEBUG,
]);
?>
