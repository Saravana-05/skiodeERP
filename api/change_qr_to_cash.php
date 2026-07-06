<?php
/**
 * change_qr_to_cash.php
 * Switches a QR-pending job card's advance payment from Online/GPay to Cash.
 * - Marks pending_qr_closures as CASH_CONVERTED and stores the reason.
 * - Stores the reason in canara_transactions.payment_change_reason.
 * - Updates jobcard_master: advance_gpay → 0, advance_cash += old advance_gpay.
 * - Fixes journal: removes old SB/GPay entry, posts new CASH entry.
 */
session_start();
include_once '../connect_db.php';
include_once 'canara_bank_lib.php';

cb_ensure_table($connection);           // ensure payment_change_reason column exists
fn_ensure_pending_qr_table($connection); // ensure cancel_reason column exists

header('Content-Type: application/json');

$jobcard_no = intval($_POST['jobcard_no'] ?? 0);
$reason     = trim($_POST['reason']      ?? '');
$user       = $_SESSION['user_name']     ?? 'system';

if (!$jobcard_no || !$reason) {
    echo json_encode(['status' => 'error', 'message' => 'Job card number and reason are required.']);
    exit;
}

$now        = date('Y-m-d H:i:s');
$safe_reason = mysqli_real_escape_string($connection, $reason);
$safe_user   = mysqli_real_escape_string($connection, $user);

// ── 1. Get the PENDING QR closure record ─────────────────────────────────────
$pqc_qry = mysqli_query($connection,
    "SELECT * FROM pending_qr_closures
      WHERE jobcard_no = $jobcard_no AND status = 'PENDING' LIMIT 1");
if (!$pqc_qry || mysqli_num_rows($pqc_qry) === 0) {
    echo json_encode(['status' => 'error', 'message' => 'No pending QR payment found for this Job Card.']);
    exit;
}
$pqc = mysqli_fetch_assoc($pqc_qry);
$ext_txn_id = $pqc['ext_transaction_id'] ?? '';

// ── 2. Mark pending_qr_closures as CASH_CONVERTED ────────────────────────────
mysqli_query($connection,
    "UPDATE pending_qr_closures
        SET status        = 'CASH_CONVERTED',
            cancel_reason = '$safe_reason',
            updated_at    = '$now'
      WHERE jobcard_no = $jobcard_no AND status = 'PENDING'");

// ── 3. Store reason in canara_transactions (keep status as PENDING — bank side) ─
if ($ext_txn_id) {
    $safe_ext = mysqli_real_escape_string($connection, $ext_txn_id);
    mysqli_query($connection,
        "UPDATE canara_transactions
            SET payment_change_reason = '$safe_reason',
                updated_at            = '$now'
          WHERE ext_transaction_id = '$safe_ext'");
}

// ── 4. Get current advance amounts from jobcard_master ────────────────────────
$jm_qry = mysqli_query($connection,
    "SELECT advance_gpay, advance_cash FROM jobcard_master
      WHERE jobcard_no = $jobcard_no LIMIT 1");
if (!$jm_qry || mysqli_num_rows($jm_qry) === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Job card not found.']);
    exit;
}
$jm       = mysqli_fetch_assoc($jm_qry);
$old_gpay = floatval($jm['advance_gpay']);
$old_cash = floatval($jm['advance_cash']);
$new_cash = round($old_cash + $old_gpay, 2);

// ── 5. Update jobcard_master: gpay → cash ────────────────────────────────────
mysqli_query($connection,
    "UPDATE jobcard_master
        SET advance_gpay   = 0,
            is_adv_in_gpay = 0,
            advance_cash   = $new_cash,
            is_adv_in_cash = 1
      WHERE jobcard_no = $jobcard_no");

// ── 6. Fix journal entries ───────────────────────────────────────────────────
// Delete old Online/SB/CA journal entries posted for this JC's advance
$link_key = "JC_$jobcard_no";
mysqli_query($connection,
    "DELETE FROM journal_details
      WHERE link_key  = '$link_key'
        AND (ac_head  = 'SB' OR ac_head = 'CA')
        AND description LIKE 'Advance%'");

// Post new CASH journal entry (only if there was a gpay amount to switch)
if ($old_gpay > 0) {
    $desc = "Advance Cash (switched from Online - $safe_reason) for JC:$jobcard_no by $safe_user";
    post_journal_single_entry($now, 'CASH', $desc, $old_gpay, $link_key);
}

echo json_encode(['status' => 'success', 'message' => 'Payment mode switched to Cash successfully.']);
?>
