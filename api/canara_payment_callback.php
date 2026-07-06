<?php
/**
 * Canara Bank / NPCI UPI payment callback.
 * Approved URL: https://citizenprintz.in/storefront/api/canara_payment_callback.php
 *
 * NPCI POSTs encrypted JSON when a UPI transaction completes.
 * Decrypts the payload, updates local DB, returns "200_OK" (required by NPCI).
 */
include_once '../connect_db.php';
include_once 'canara_bank_lib.php';

cb_ensure_table($connection);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

$raw = file_get_contents('php://input');
cb_log('[CanaraCallback] raw: ' . $raw);

if (!$raw) {
    http_response_code(200);
    echo '200_OK';
    exit;
}

try {
    $body = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) throw new Exception('Invalid JSON');

    $encrypted_data = $body['Request']['body']['encryptData'] ?? null;
    if (!$encrypted_data) throw new Exception('Missing encryptData');

    $decrypted = json_decode(cb_jwe_decrypt($encrypted_data), true);
    cb_log('[CanaraCallback] decrypted: ' . json_encode($decrypted));

    $ext_id       = $decrypted['extTransactionId'] ?? '';
    $status       = $decrypted['status']           ?? '';
    $rrn          = $decrypted['rrn']              ?? null;
    $txn_id       = $decrypted['txnId']            ?? null;
    $customer_vpa = $decrypted['customer_vpa']     ?? null;

    if (!$ext_id || !$status) throw new Exception('Missing required fields');

    // Idempotency: skip if already SUCCESS
    $existing = cb_get_transaction($connection, $ext_id);
    if ($existing && $existing['status'] === 'SUCCESS') {
        cb_log('[CanaraCallback] already SUCCESS, skipping');
        echo '200_OK';
        exit;
    }

    if ($status === 'SUCCESS') {
        $fields = [
            'status'       => 'SUCCESS',
            'rrn'          => $rrn,
            'txn_id'       => $txn_id,
            'customer_vpa' => $customer_vpa,
            'paid_at'      => date('Y-m-d H:i:s'),
        ];
    } elseif (in_array($status, ['FAILURE', 'FAILED'], true)) {
        $fields = [
            'status'       => 'FAILED',
            'txn_id'       => $txn_id,
            'customer_vpa' => $customer_vpa,
        ];
    } else {
        $fields = ['status' => 'PENDING'];
    }

    cb_update_transaction($connection, $ext_id, $fields);

    // ── Auto-convert parked Job Card to Sales Quote on successful payment ────
    if ($status === 'SUCCESS') {
        try {
            if (!function_exists('fn_ensure_pending_qr_table') || !function_exists('fn_convert_jc_to_sq')) {
                cb_log('[CanaraCallback] fn_convert_jc_to_sq not found — connect_db.php not deployed');
            } else {
                $jc_no = null;
                if ($existing && !empty($existing['jobcard_no'])) {
                    $jc_no = $existing['jobcard_no'];
                } else {
                    $refetched = cb_get_transaction($connection, $ext_id);
                    if ($refetched && !empty($refetched['jobcard_no'])) $jc_no = $refetched['jobcard_no'];
                }
                cb_log("[CanaraCallback] jobcard_no for auto-convert: " . ($jc_no ?? 'NULL'));

                if ($jc_no) {
                    fn_ensure_pending_qr_table($connection);
                    $safe_jc  = mysqli_real_escape_string($connection, $jc_no);
                    $pend_res = mysqli_query($connection,
                        "SELECT * FROM pending_qr_closures WHERE jobcard_no='$safe_jc' AND status='PENDING' LIMIT 1");
                    cb_log("[CanaraCallback] pending_qr_closures query rows: " . ($pend_res ? mysqli_num_rows($pend_res) : 'query_failed'));

                    if ($pend_res && $pend_row = mysqli_fetch_assoc($pend_res)) {
                        $params = [
                            'jcnos'             => !empty($pend_row['jobcard_nos']) ? $pend_row['jobcard_nos'] : strval($pend_row['jobcard_no']),
                            'approximate_amount'=> $pend_row['approximate_amount'],
                            'balance_cash'      => $pend_row['balance_cash'],
                            'balance_gpay'      => $pend_row['balance_gpay'],
                            'balance_amount'    => $pend_row['balance_amount'],
                            'discount'          => $pend_row['discount_amount'],
                            'is_bal_in_cash'    => $pend_row['is_bal_in_cash'],
                            'is_bal_in_gpay'    => $pend_row['is_bal_in_gpay'],
                            'is_gst_extra'      => $pend_row['is_gst_extra'],
                            'gst_tax_amount'    => $pend_row['gst_tax_amount'],
                        ];
                        $user      = $pend_row['created_by'] ?: 'QR_AUTO';
                        $sq_result = fn_convert_jc_to_sq($connection, $params, $user);
                        $now_ts    = date('Y-m-d H:i:s');
                        cb_log("[CanaraCallback] fn_convert_jc_to_sq result: $sq_result");

                        if ($sq_result !== 'Failed' && $sq_result !== 'Job Card already Closed') {
                            mysqli_query($connection,
                                "UPDATE pending_qr_closures
                                    SET status='CONVERTED', converted_sq_no=$sq_result,
                                        converted_at='$now_ts', updated_at='$now_ts'
                                  WHERE jobcard_no='$safe_jc'");
                            cb_log("[CanaraCallback] Auto-converted JC $jc_no → SQ $sq_result");
                        } else {
                            cb_log("[CanaraCallback] Auto-conversion failed for JC $jc_no: $sq_result");
                        }
                    } else {
                        cb_log("[CanaraCallback] No PENDING closure found for JC $jc_no — may not have been parked");
                    }
                }
            }
        } catch (\Throwable $autoErr) {
            cb_log('[CanaraCallback] Auto-convert error: ' . $autoErr->getMessage());
        }
    }

} catch (\Throwable $e) {
    cb_log('[CanaraCallback] ERROR: ' . $e->getMessage());
    // Still return 200_OK to NPCI to avoid retries
}

http_response_code(200);
echo '200_OK';
