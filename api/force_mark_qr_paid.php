<?php
/**
 * force_mark_qr_paid.php
 *
 * Admin-level manual override: marks a QR/UPI transaction as SUCCESS
 * and auto-converts the parked Job Card to a Sales Quote.
 *
 * Used when the bank account confirms payment received but the bank API
 * still returns PENDING (settlement lag / API inconsistency).
 *
 * POST { ext_transaction_id, jobcard_no }
 * Returns JSON { status, converted_sq, message }
 */
mysqli_report(MYSQLI_REPORT_OFF);
session_start();
header('Content-Type: application/json');

include_once __DIR__ . '/../connect_db.php';
include_once __DIR__ . '/canara_bank_lib.php';
fn_ensure_pending_qr_table($connection);
cb_ensure_table($connection);

$ext_id = trim($_POST['ext_transaction_id'] ?? '');
$jcno   = intval($_POST['jobcard_no']       ?? 0);
$user   = $_SESSION['user_name']            ?? 'system';

if (!$ext_id && !$jcno) {
    echo json_encode(['status' => 'error', 'message' => 'ext_transaction_id or jobcard_no required.']);
    exit;
}

$now = date('Y-m-d H:i:s');

// ── 1. If no ext_id provided, find it from canara_transactions ───────────────
if (!$ext_id && $jcno > 0) {
    $r = mysqli_query($connection,
        "SELECT ext_transaction_id FROM canara_transactions
          WHERE jobcard_no = '$jcno'
          ORDER BY created_at DESC LIMIT 1");
    if ($r && $row = mysqli_fetch_assoc($r)) {
        $ext_id = $row['ext_transaction_id'];
    }
}

$safe_ext = $ext_id ? mysqli_real_escape_string($connection, $ext_id) : null;
$safe_user = mysqli_real_escape_string($connection, $user);

// ── 2. Mark canara_transactions as SUCCESS ───────────────────────────────────
if ($safe_ext) {
    mysqli_query($connection,
        "UPDATE canara_transactions
            SET status     = 'SUCCESS',
                paid_at    = COALESCE(paid_at, '$now'),
                updated_at = '$now',
                txn_id     = COALESCE(NULLIF(txn_id,''), 'MANUAL_OVERRIDE_$safe_user')
          WHERE ext_transaction_id = '$safe_ext'
            AND status != 'SUCCESS'"
    );
    log_this("force_mark_qr_paid: ext_id=$ext_id JC=$jcno marked SUCCESS by $user");
}

// ── 3. Find the pending_qr_closures record for this JC ───────────────────────
if (!$jcno && $safe_ext) {
    $r2 = mysqli_query($connection,
        "SELECT jobcard_no FROM canara_transactions
          WHERE ext_transaction_id = '$safe_ext' LIMIT 1");
    if ($r2 && $row2 = mysqli_fetch_assoc($r2)) {
        $jcno = intval($row2['jobcard_no']);
    }
}

if (!$jcno) {
    echo json_encode(['status' => 'error',
                      'message' => 'Could not determine Job Card number from transaction.']);
    exit;
}

$converted_sq = null;
$pr = mysqli_query($connection,
    "SELECT * FROM pending_qr_closures WHERE jobcard_no=$jcno AND status='PENDING' LIMIT 1");

if ($pr && $prow = mysqli_fetch_assoc($pr)) {
    // ── 4. Auto-convert JC → SQ ──────────────────────────────────────────────
    $params = [
        'jcnos'             => $prow['jobcard_no'],
        'approximate_amount'=> $prow['approximate_amount'],
        'balance_cash'      => $prow['balance_cash'],
        'balance_gpay'      => $prow['balance_gpay'],
        'balance_amount'    => $prow['balance_amount'],
        'discount'          => $prow['discount_amount'],
        'is_bal_in_cash'    => $prow['is_bal_in_cash'],
        'is_bal_in_gpay'    => $prow['is_bal_in_gpay'],
        'is_gst_extra'      => $prow['is_gst_extra'],
        'gst_tax_amount'    => $prow['gst_tax_amount'],
    ];
    $sq = fn_convert_jc_to_sq($connection, $params, $user);

    if ($sq !== 'Failed' && $sq !== 'Job Card already Closed') {
        mysqli_query($connection,
            "UPDATE pending_qr_closures
                SET status='CONVERTED', converted_sq_no=$sq,
                    converted_at='$now', updated_at='$now'
              WHERE jobcard_no=$jcno");
        $converted_sq = $sq;
        log_this("force_mark_qr_paid: JC $jcno → SQ $sq by $user");
        echo json_encode(['status' => 'success', 'converted_sq' => $converted_sq,
                          'message' => "Payment confirmed. JC $jcno converted to SQ $sq."]);
    } elseif ($sq === 'Job Card already Closed') {
        mysqli_query($connection,
            "UPDATE pending_qr_closures
                SET status='CONVERTED', updated_at='$now'
              WHERE jobcard_no=$jcno");
        echo json_encode(['status' => 'success', 'converted_sq' => null,
                          'message' => "Payment confirmed. Job Card $jcno was already closed."]);
    } else {
        echo json_encode(['status' => 'error',
                          'message' => "Payment marked SUCCESS but SQ conversion failed. Please close JC $jcno manually."]);
    }
} else {
    // No pending_qr_closures record — payment confirmed but JC was never parked.
    // Still return success so the QR report updates.
    log_this("force_mark_qr_paid: ext_id=$ext_id JC=$jcno — no pending_qr_closures found, only updated canara_transactions");
    echo json_encode(['status' => 'success', 'converted_sq' => null,
                      'message' => "Payment marked as received. No pending Job Card found to auto-convert — please close the JC manually if needed."]);
}
?>
