<?php
/**
 * One-time fix: Mark advance_id=15 (DAFF HERBALS, ₹500 GPay) as fully PAID.
 * Updates both advance_payment_master and canara_transactions.
 * DELETE this file after running it.
 */
session_start();
include_once "connect_db.php";

$advance_id     = 15;
$ext_id         = 'EXT20260604120024fc1393';
$now            = date('Y-m-d H:i:s');
$errors         = [];
$results        = [];

// 1. Update advance_payment_master → qr_payment_status = PAID
$sql1 = "UPDATE advance_payment_master
         SET qr_payment_status = 'PAID'
         WHERE advance_id = $advance_id";
if (mysqli_query($connection, $sql1)) {
    $results[] = "✅ advance_payment_master: advance_id=$advance_id set to PAID (rows affected: " . mysqli_affected_rows($connection) . ")";
} else {
    $errors[] = "❌ advance_payment_master update failed: " . mysqli_error($connection);
}

// 2. Update canara_transactions → status = SUCCESS
$safe_ext = mysqli_real_escape_string($connection, $ext_id);
$sql2 = "UPDATE canara_transactions
         SET status = 'SUCCESS', updated_at = '$now'
         WHERE ext_transaction_id = '$safe_ext'";
if (mysqli_query($connection, $sql2)) {
    $results[] = "✅ canara_transactions: ext_id=$ext_id set to SUCCESS (rows affected: " . mysqli_affected_rows($connection) . ")";
} else {
    $errors[] = "❌ canara_transactions update failed: " . mysqli_error($connection);
}

// 3. Show current state of the record for confirmation
$check = mysqli_query($connection,
    "SELECT advance_id, customer_name, amount, pay_mode, qr_payment_status, ext_transaction_id
     FROM advance_payment_master WHERE advance_id = $advance_id");
$row = $check ? mysqli_fetch_assoc($check) : null;

?>
<!DOCTYPE html>
<html>
<head><title>Fix Advance #15</title>
<style>body{font-family:Arial;padding:30px;} .ok{color:green;} .err{color:red;} table{border-collapse:collapse;margin-top:15px;} td,th{border:1px solid #ccc;padding:8px 12px;}</style>
</head>
<body>
<h2>Fix Advance Payment #15 — DAFF HERBALS ₹500 GPay</h2>

<?php foreach($results as $r) echo "<p class='ok'>$r</p>"; ?>
<?php foreach($errors as $e) echo "<p class='err'>$e</p>"; ?>

<?php if($row): ?>
<h3>Current Record State:</h3>
<table>
  <tr><th>Field</th><th>Value</th></tr>
  <?php foreach($row as $k=>$v): ?>
  <tr><td><?= htmlspecialchars($k) ?></td><td><?= htmlspecialchars($v ?? 'NULL') ?></td></tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<br><br>
<p style="background:#fff3cd;padding:10px;border:1px solid #ffc107;">
  ⚠️ <strong>Please delete this file after confirming the fix:</strong><br>
  <code>C:\xampp\htdocs\storefront_uptodate_code_march20th_2026\storefront\fix_advance_15.php</code>
</p>
</body>
</html>
