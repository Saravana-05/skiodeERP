<?php
session_start();
include_once __DIR__ . '/../connect_db.php';
include_once __DIR__ . '/canara_bank_lib.php';
header('Content-Type: application/json');

if (!isset($connection) || $connection === false) {
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

cb_ensure_table($connection);

$action = $_POST['action'] ?? '';
$date   = $_POST['date']   ?? date('Y-m-d');
$date   = date('Y-m-d', strtotime($date));

if ($action === 'fetch') {
    $wider_from = date('Y-m-d', strtotime($date . ' -1 day'));
    $wider_to   = date('Y-m-d', strtotime($date . ' +1 day'));

    // First refresh from bank statement API
    $access_token = '';
    try { $access_token = cb_get_valid_token(); } catch (Exception $e) {}

    if ($access_token) {
        $payload = json_encode([
            'startDate'    => $wider_from . ' 00:00:00',
            'endDate'      => $wider_to   . ' 23:59:59',
            'pageSize'     => '500',
            'pageNo'       => '0',
            'access_token' => $access_token,
        ]);
        $ch = curl_init('https://api.citizenprintz.in/api/bank/qr-statement');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);
        $resp = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http === 200 && $resp) {
            $json = json_decode($resp, true);
            $bank_txns = [];
            if (isset($json['data']['data']) && is_array($json['data']['data'])) {
                $bank_txns = $json['data']['data'];
            } elseif (isset($json['data']['txnList']) && is_array($json['data']['txnList'])) {
                $bank_txns = $json['data']['txnList'];
            } elseif (isset($json['data']) && is_array($json['data']) && isset($json['data'][0])) {
                $bank_txns = $json['data'];
            }

            $bank_by_ext = [];
            foreach ($bank_txns as $btxn) {
                $ext = $btxn['extTransactionId'] ?? $btxn['ext_transaction_id'] ?? $btxn['merchantTxnId'] ?? '';
                if ($ext) $bank_by_ext[$ext] = $btxn;
            }

            $refresh_sql = "SELECT ext_transaction_id, id
                            FROM canara_transactions
                            WHERE DATE(created_at) BETWEEN '$wider_from' AND '$wider_to'
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
                    else continue;

                    $safe_id  = mysqli_real_escape_string($connection, $ext_id);
                    $rrn      = mysqli_real_escape_string($connection, (string)($btxn['rrn'] ?? $btxn['bankRrn'] ?? $btxn['bankRRN'] ?? $btxn['utr'] ?? ''));
                    $txn_id   = mysqli_real_escape_string($connection, (string)($btxn['txnId'] ?? $btxn['txn_id'] ?? $btxn['bankTxnId'] ?? ''));
                    $cust_vpa = mysqli_real_escape_string($connection, (string)($btxn['payerVpa'] ?? $btxn['customer_vpa'] ?? ''));

                    $update_sql = "UPDATE canara_transactions
                        SET status = 'SUCCESS', paid_at = '$now_u', updated_at = '$now_u'"
                        . ($rrn      ? ", rrn          = '$rrn'"      : '')
                        . ($txn_id   ? ", txn_id       = '$txn_id'"   : '')
                        . ($cust_vpa ? ", customer_vpa = '$cust_vpa'" : '')
                        . " WHERE ext_transaction_id = '$safe_id' AND status != 'SUCCESS'";
                    mysqli_query($connection, $update_sql);
                }
            }
        }
    }

    // Fetch all transactions for the date (SUCCESS only for comparison)
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
                ct.updated_at,
                ct.customer_name,
                DATE_FORMAT(COALESCE(ct.paid_at, ct.created_at), '%H:%i:%s') AS paid_time,
                DATE_FORMAT(COALESCE(ct.paid_at, ct.created_at), '%d-%m-%Y %h:%i %p') AS paid_at_formatted
            FROM canara_transactions ct
            WHERE ct.status = 'SUCCESS'
              AND (DATE(ct.paid_at) = '$date' OR (ct.paid_at IS NULL AND DATE(ct.created_at) = '$date'))
            ORDER BY COALESCE(ct.paid_at, ct.created_at) ASC";

    $transactions = [];
    if ($q = mysqli_query($connection, $sql)) {
        while ($r = mysqli_fetch_assoc($q)) {
            $transactions[] = $r;
        }
    }

    // Also fetch ALL statuses for broader debug view
    $sql_all = "SELECT
                    ct.id,
                    ct.ext_transaction_id,
                    ct.jobcard_no,
                    ct.amount,
                    ct.status,
                    ct.rrn,
                    ct.customer_name,
                    ct.created_at,
                    ct.paid_at,
                    DATE_FORMAT(ct.created_at, '%H:%i:%s') AS created_time
                FROM canara_transactions ct
                WHERE DATE(ct.created_at) = '$date'
                   OR DATE(ct.paid_at) = '$date'
                ORDER BY ct.created_at ASC";

    $all_txns = [];
    if ($q2 = mysqli_query($connection, $sql_all)) {
        while ($r2 = mysqli_fetch_assoc($q2)) {
            $all_txns[] = $r2;
        }
    }

    echo json_encode([
        'transactions'   => $transactions,
        'all_statuses'   => $all_txns,
        'date'           => $date,
        'success_count'  => count($transactions),
        'total_count'    => count($all_txns),
    ]);
    exit;
}

echo json_encode(['error' => 'Unknown action']);
