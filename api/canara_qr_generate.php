<?php
mysqli_report(MYSQLI_REPORT_OFF); // must be before any include — PHP 8.1 throws by default
/**
 * ERP QR Generation — PHP owns MySQL, FastAPI is pure bank proxy.
 * 1) Call FastAPI /qr-generate (ECOMMERCE) — FastAPI assigns ext_id + calls bank
 * 2) PHP takes FastAPI's transaction_id (bank-registered ext_id) as the MySQL key
 * 3) PHP inserts canara_transactions using FastAPI's transaction_id
 * 4) Return FastAPI's transaction_id to frontend for status polling
 * POST { amount, jobcard_no, customer_name }  →  JSON { success, transaction_id, qr_string }
 */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
header('Content-Type: application/json');
session_start();

include_once __DIR__ . '/../connect_db.php';
include_once __DIR__ . '/canara_bank_lib.php';
if (isset($connection) && $connection) cb_ensure_table($connection);

$amount         = isset($_POST['amount'])         ? floatval($_POST['amount'])                                    : 0;
$jobcard_no     = isset($_POST['jobcard_no'])     ? trim($_POST['jobcard_no'])                                    : null;
$customer_name  = isset($_POST['customer_name'])  ? mysqli_real_escape_string($connection, trim($_POST['customer_name'])) : null;
$payment_source = isset($_POST['payment_source']) ? strtoupper(trim($_POST['payment_source']))                  : null;
if ($jobcard_no === '' || $jobcard_no === 'null') $jobcard_no = null;
if ($customer_name === '') $customer_name = null;
if (!in_array($payment_source, ['JC', 'SQ'])) $payment_source = null;

// Define safe_jc once here so it is always available for EXPIRE query below
$safe_jc = $jobcard_no ? mysqli_real_escape_string($connection, $jobcard_no) : null;

if ($amount <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid amount']);
    exit;
}

// ── Block duplicate QR only for the same payment source (JC advance vs SQ balance) ──
if ($safe_jc && $payment_source) {
    $safe_src = mysqli_real_escape_string($connection, $payment_source);
    $dup_res  = mysqli_query($connection,
        "SELECT ext_transaction_id, amount FROM canara_transactions
          WHERE jobcard_no = '$safe_jc' AND payment_source = '$safe_src' AND status = 'SUCCESS' LIMIT 1");
    if ($dup_res && mysqli_num_rows($dup_res) > 0) {
        $dup = mysqli_fetch_assoc($dup_res);
        echo json_encode([
            'success'      => false,
            'already_paid' => true,
            'error'        => 'Job Card ' . $jobcard_no . ' ' . $payment_source . ' payment already completed (₹' . number_format($dup['amount'], 2) . ').',
        ]);
        exit;
    }
}

// ── Call FastAPI first — get the bank-registered transaction_id ───────────────
// source=ECOMMERCE so FastAPI handles its own PostgreSQL insert
// No ref_id — avoids PostgreSQL FK constraint error with jobcard numbers
$api_url = 'https://api.citizenprintz.in/api/bank/qr-generate'
         . '?amount=' . urlencode(number_format($amount, 2, '.', ''))
         . '&source=ECOMMERCE';

$ch = curl_init($api_url);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => '',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 120,  // 120 s — gives FastAPI full time to call the bank
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
]);

$response  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_err  = curl_error($ch);
curl_close($ch);

if ($curl_err || $http_code !== 200) {
    echo json_encode(['success' => false, 'error' => 'FastAPI unreachable: ' . ($curl_err ?: "HTTP $http_code"), '_debug' => $response]);
    exit;
}

$data = json_decode($response, true);

if (!$data || empty($data['qr_string']) || empty($data['transaction_id'])) {
    echo json_encode(['success' => false, 'error' => $data['detail'] ?? 'QR generation failed', '_debug' => $response]);
    exit;
}

// ── Use FastAPI's transaction_id — this is what the bank registered ───────────
$bank_ext_id  = $data['transaction_id'];
$safe_ext     = mysqli_real_escape_string($connection, $bank_ext_id);
$safe_qr      = mysqli_real_escape_string($connection, $data['qr_string']);
$safe_amt     = floatval($amount);
$safe_jc_val  = $jobcard_no
    ? "'" . mysqli_real_escape_string($connection, $jobcard_no) . "'"
    : "NULL";
$now = date('Y-m-d H:i:s');

// ── Expire previous PENDING attempts for same job card + payment source ──────
if ($safe_jc) {
    $src_clause = $payment_source
        ? "AND payment_source = '" . mysqli_real_escape_string($connection, $payment_source) . "'"
        : "AND payment_source IS NULL";
    mysqli_query($connection,
        "UPDATE canara_transactions
            SET status = 'EXPIRED', updated_at = '$now'
          WHERE jobcard_no = '$safe_jc' $src_clause AND status = 'PENDING'"
    );
}

// ── Insert into canara_transactions with the bank's ext_id ────────────────────
$safe_cust_val = $customer_name ? "'$customer_name'" : "NULL";
$safe_src_val  = $payment_source ? "'" . mysqli_real_escape_string($connection, $payment_source) . "'" : "NULL";
mysqli_query($connection,
    "INSERT INTO canara_transactions
         (ext_transaction_id, jobcard_no, payment_source, amount, status, qr_string, customer_name, created_at, updated_at)
     VALUES ('$safe_ext', $safe_jc_val, $safe_src_val, $safe_amt, 'PENDING', '$safe_qr', $safe_cust_val, '$now', '$now')
     ON DUPLICATE KEY UPDATE
         qr_string      = VALUES(qr_string),
         customer_name  = VALUES(customer_name),
         payment_source = VALUES(payment_source),
         updated_at     = VALUES(updated_at)"
);

// Return FastAPI's transaction_id — frontend uses this for status polling
echo json_encode([
    'success'        => true,
    'transaction_id' => $bank_ext_id,
    'qr_string'      => $data['qr_string'],
]);
?>
