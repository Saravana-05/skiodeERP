<?php
/**
 * qr_auto_recovery.php
 *
 * Automatic recovery of missed QR payments by checking the bank statement API.
 * Called periodically from the dashboard (every 10 minutes) or manually.
 *
 * Unlike process_pending_qr_closures.php (which only checks transactions linked
 * to pending_qr_closures), this checks ALL non-SUCCESS transactions from the
 * last 3 days against the bank statement — catching payments that arrived via
 * webhook failure, polling timeout, or any other gap.
 *
 * Also triggers auto-conversion for any pending_qr_closures that now have SUCCESS.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../connect_db.php';
include_once __DIR__ . '/canara_bank_lib.php';

header('Content-Type: application/json');

if (!isset($connection) || $connection === false) {
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

cb_ensure_table($connection);

$recovered = 0;
$converted = [];
$errors = [];

// Check last 3 days of transactions
$from_date = date('Y-m-d', strtotime('-3 days'));
$to_date   = date('Y-m-d');

// Get bank access token
$access_token = '';
try { $access_token = cb_get_valid_token(); } catch (Exception $e) {
    $errors[] = 'Token error: ' . $e->getMessage();
}

if ($access_token) {
    // Fetch bank statement for the date range
    $payload = json_encode([
        'startDate'    => $from_date . ' 00:00:00',
        'endDate'      => $to_date   . ' 23:59:59',
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
    $curl_err = curl_error($ch);
    curl_close($ch);

    if ($curl_err) {
        $errors[] = 'Bank API error: ' . $curl_err;
    } elseif ($http !== 200) {
        $errors[] = 'Bank API HTTP ' . $http;
    } elseif ($resp) {
        $json = json_decode($resp, true);
        $bank_txns = [];

        if (isset($json['data']['data']) && is_array($json['data']['data'])) {
            $bank_txns = $json['data']['data'];
        } elseif (isset($json['data']['txnList']) && is_array($json['data']['txnList'])) {
            $bank_txns = $json['data']['txnList'];
        } elseif (isset($json['data']) && is_array($json['data']) && isset($json['data'][0])) {
            $bank_txns = $json['data'];
        }

        // Index bank transactions by ext_transaction_id
        $bank_by_ext = [];
        foreach ($bank_txns as $btxn) {
            $ext = $btxn['extTransactionId'] ?? $btxn['ext_transaction_id'] ?? $btxn['merchantTxnId'] ?? '';
            if ($ext) $bank_by_ext[$ext] = $btxn;
        }

        // Find all non-SUCCESS transactions in our DB for this period
        $safe_from = mysqli_real_escape_string($connection, $from_date);
        $safe_to   = mysqli_real_escape_string($connection, $to_date);

        $pending_sql = "SELECT id, ext_transaction_id, jobcard_no, amount, status
                        FROM canara_transactions
                        WHERE DATE(created_at) BETWEEN '$safe_from' AND '$safe_to'
                          AND status NOT IN ('SUCCESS')
                          AND ext_transaction_id IS NOT NULL
                          AND ext_transaction_id != ''";

        $pending_res = mysqli_query($connection, $pending_sql);
        if ($pending_res) {
            $now_u = date('Y-m-d H:i:s');
            while ($rt = mysqli_fetch_assoc($pending_res)) {
                $ext_id = $rt['ext_transaction_id'];
                if (!isset($bank_by_ext[$ext_id])) continue;

                $btxn = $bank_by_ext[$ext_id];
                $raw  = $btxn['txnStatus'] ?? $btxn['status'] ?? $btxn['paymentStatus'] ?? '';
                $s    = strtoupper(trim((string)$raw));

                if (!in_array($s, ['SUCCESS','PAID','CREDIT','SUCCESSFUL','APPROVED','COMPLETE','S'], true))
                    continue;

                $safe_id  = mysqli_real_escape_string($connection, $ext_id);
                $rrn      = mysqli_real_escape_string($connection, (string)($btxn['rrn'] ?? $btxn['bankRrn'] ?? $btxn['bankRRN'] ?? $btxn['utr'] ?? ''));
                $txn_id   = mysqli_real_escape_string($connection, (string)($btxn['txnId'] ?? $btxn['txn_id'] ?? $btxn['bankTxnId'] ?? ''));
                $cust_vpa = mysqli_real_escape_string($connection, (string)($btxn['payerVpa'] ?? $btxn['customer_vpa'] ?? ''));

                $update_sql = "UPDATE canara_transactions
                    SET status = 'SUCCESS', paid_at = COALESCE(paid_at, '$now_u'), updated_at = '$now_u'"
                    . ($rrn      ? ", rrn          = '$rrn'"      : '')
                    . ($txn_id   ? ", txn_id       = '$txn_id'"   : '')
                    . ($cust_vpa ? ", customer_vpa = '$cust_vpa'" : '')
                    . " WHERE ext_transaction_id = '$safe_id' AND status != 'SUCCESS'";

                if (mysqli_query($connection, $update_sql) && mysqli_affected_rows($connection) > 0) {
                    $recovered++;
                    log_this("qr_auto_recovery: RECOVERED ext_id=$ext_id JC={$rt['jobcard_no']} amount={$rt['amount']} old_status={$rt['status']} rrn=$rrn");
                }
            }
        }
    }
}

// Auto-convert any pending_qr_closures that now have SUCCESS transactions
if ($recovered > 0) {
    fn_ensure_pending_qr_table($connection);

    $conv_sql = "SELECT pqc.*
                 FROM pending_qr_closures pqc
                 INNER JOIN canara_transactions ct
                     ON ct.jobcard_no = pqc.jobcard_no
                    AND ct.status     = 'SUCCESS'
                    AND (
                        pqc.ext_transaction_id IS NULL
                        OR pqc.ext_transaction_id = ''
                        OR ct.ext_transaction_id = pqc.ext_transaction_id
                    )
                 WHERE pqc.status = 'PENDING'";

    $conv_res = mysqli_query($connection, $conv_sql);
    if ($conv_res) {
        while ($row = mysqli_fetch_assoc($conv_res)) {
            $params = [
                'jcnos'             => !empty($row['jobcard_nos']) ? $row['jobcard_nos'] : strval($row['jobcard_no']),
                'approximate_amount'=> $row['approximate_amount'],
                'balance_cash'      => $row['balance_cash'],
                'balance_gpay'      => $row['balance_gpay'],
                'balance_amount'    => $row['balance_amount'],
                'discount'          => $row['discount_amount'],
                'is_bal_in_cash'    => $row['is_bal_in_cash'],
                'is_bal_in_gpay'    => $row['is_bal_in_gpay'],
                'is_gst_extra'      => $row['is_gst_extra'],
                'gst_tax_amount'    => $row['gst_tax_amount'],
            ];
            $user = $row['created_by'] ?: 'QR_AUTO';
            $sq   = fn_convert_jc_to_sq($connection, $params, $user);
            $now_c = date('Y-m-d H:i:s');

            if ($sq !== 'Failed' && $sq !== 'Job Card already Closed') {
                mysqli_query($connection,
                    "UPDATE pending_qr_closures
                        SET status='CONVERTED', converted_sq_no=$sq,
                            converted_at='$now_c', updated_at='$now_c'
                      WHERE jobcard_no=" . intval($row['jobcard_no']));
                $converted[] = ['jc' => $row['jobcard_no'], 'sq' => $sq];
                log_this("qr_auto_recovery: Auto-converted JC {$row['jobcard_no']} → SQ $sq");
            } elseif ($sq === 'Job Card already Closed') {
                mysqli_query($connection,
                    "UPDATE pending_qr_closures
                        SET status='CONVERTED', updated_at='$now_c'
                      WHERE jobcard_no=" . intval($row['jobcard_no']));
            }
        }
    }
}

$last_run = date('Y-m-d H:i:s');
echo json_encode([
    'recovered'      => $recovered,
    'converted'      => $converted,
    'bank_txn_count' => count($bank_by_ext ?? []),
    'date_range'     => $from_date . ' to ' . $to_date,
    'last_run'       => $last_run,
    'errors'         => $errors
]);
