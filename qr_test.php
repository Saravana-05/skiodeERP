<?php
/**
 * qr_test.php  —  TEMPORARY diagnostic, DELETE after use.
 * No login required. Drop on server, open in browser with ?jcno=13424
 */
mysqli_report(MYSQLI_REPORT_OFF);
include_once __DIR__ . '/connect_db.php';   // gets $connection

$jcno = intval($_GET['jcno'] ?? 0);

// ── helper ────────────────────────────────────────────────────────────────────
function ask_bank($ext_id, $source) {
    $ch = curl_init('https://api.citizenprintz.in/api/bank/qr-status-extid');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode(['extTransactionId' => $ext_id, 'source' => $source]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    ]);
    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    return ['http' => $http, 'curl_err' => $err, 'raw' => $resp, 'parsed' => json_decode($resp, true)];
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>QR Test</title>
<style>
body{font-family:monospace;background:#1e1e2e;color:#cdd6f4;padding:20px;}
input,button{padding:6px 12px;font-size:1rem;margin:4px;}
pre{background:#313244;padding:12px;border-radius:6px;white-space:pre-wrap;word-break:break-all;max-height:300px;overflow-y:auto;}
h3{color:#cba6f7;}
.ok{color:#a6e3a1;font-weight:bold;}
.bad{color:#f38ba8;font-weight:bold;}
.warn{color:#f9e2af;font-weight:bold;}
table{border-collapse:collapse;width:100%;}
td,th{border:1px solid #45475a;padding:6px 10px;font-size:0.85rem;}
th{background:#313244;color:#cba6f7;}
</style>
</head>
<body>

<h2>🔍 QR Bank Test</h2>
<form method="GET">
  Job Card No: <input type="number" name="jcno" value="<?= $jcno ?: '' ?>" placeholder="e.g. 13424" />
  <button type="submit">Check</button>
</form>

<?php if ($jcno > 0): ?>

<?php
// ── 1. All canara_transactions for this JC ───────────────────────────────────
$rows = [];
$r = mysqli_query($connection,
    "SELECT * FROM canara_transactions WHERE jobcard_no='$jcno' ORDER BY created_at DESC LIMIT 10");
while ($r && $row = mysqli_fetch_assoc($r)) $rows[] = $row;

// ── 2. pending_qr_closures ───────────────────────────────────────────────────
$pqc = null;
$rp = mysqli_query($connection,
    "SELECT * FROM pending_qr_closures WHERE jobcard_no=$jcno LIMIT 1");
if ($rp) $pqc = mysqli_fetch_assoc($rp);
?>

<h3>DB: canara_transactions (<?= count($rows) ?> rows)</h3>
<?php if (empty($rows)): ?>
  <p class="bad">No canara_transactions rows for JC <?= $jcno ?></p>
<?php else: ?>
<table>
<tr><th>#</th><th>ext_transaction_id</th><th>status</th><th>amount</th><th>created_at</th><th>rrn</th><th>txn_id</th><th>customer_vpa</th></tr>
<?php foreach ($rows as $i => $ct): ?>
<tr>
  <td><?= $i+1 ?></td>
  <td style="font-size:0.75rem"><?= htmlspecialchars($ct['ext_transaction_id']) ?></td>
  <td class="<?= $ct['status']==='SUCCESS'?'ok':($ct['status']==='PENDING'?'warn':'bad') ?>"><?= $ct['status'] ?></td>
  <td>₹<?= number_format($ct['amount'],2) ?></td>
  <td><?= $ct['created_at'] ?></td>
  <td><?= htmlspecialchars($ct['rrn'] ?? '—') ?></td>
  <td><?= htmlspecialchars($ct['txn_id'] ?? '—') ?></td>
  <td><?= htmlspecialchars($ct['customer_vpa'] ?? '—') ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

<h3>DB: pending_qr_closures</h3>
<?php if (!$pqc): ?>
  <p class="bad">No pending_qr_closures row for JC <?= $jcno ?></p>
<?php else: ?>
  <pre><?= htmlspecialchars(json_encode($pqc, JSON_PRETTY_PRINT)) ?></pre>
<?php endif; ?>

<?php if (!empty($rows)): ?>
<h3>Live Bank API — checking ALL transactions (including EXPIRED)</h3>
<p style="color:#f9e2af;font-size:0.85rem;">⚠️ EXPIRED rows ARE checked — customer may have paid on a QR our system expired but bank still accepted.</p>
<?php foreach ($rows as $ct):
    if ($ct['status'] === 'SUCCESS') { ?>
      <p><b><?= htmlspecialchars($ct['ext_transaction_id']) ?></b> — already <span class="ok">SUCCESS in DB</span>, skipping bank call.</p>
    <?php continue; }
    $ext = $ct['ext_transaction_id'];
    foreach (['ECOMMERCE','ERP'] as $src):
        $res = ask_bank($ext, $src);
        $p   = $res['parsed'] ?? [];
        $raw_s = $p['status'] ?? $p['txnStatus'] ?? $p['payment_status'] ?? $p['paymentStatus'] ?? '?';
?>
<h4 style="color:#89b4fa;margin:12px 0 4px;">ext_id: <?= htmlspecialchars($ext) ?> | source=<?= $src ?> | HTTP <?= $res['http'] ?></h4>
<?php if ($res['curl_err']): ?><p class="bad">CURL ERROR: <?= htmlspecialchars($res['curl_err']) ?></p><?php endif; ?>
<p>
  Status field value in response: <b class="<?= strtoupper($raw_s)==='SUCCESS'||strtoupper($raw_s)==='CREDIT'?'ok':(strtoupper($raw_s)==='PENDING'?'warn':'bad') ?>">
    "<?= htmlspecialchars($raw_s) ?>"
  </b>
  &nbsp;|&nbsp;
  customer_vpa: <b><?= htmlspecialchars($p['customer_vpa'] ?? $p['payerVpa'] ?? $p['payerVPA'] ?? $p['customerVpa'] ?? '—') ?></b>
  &nbsp;|&nbsp;
  txn_id: <b><?= htmlspecialchars($p['txn_id'] ?? $p['txnId'] ?? $p['transactionId'] ?? '—') ?></b>
  &nbsp;|&nbsp;
  rrn: <b><?= htmlspecialchars($p['rrn'] ?? $p['bankRrn'] ?? '—') ?></b>
</p>
<pre><?= htmlspecialchars($res['raw'] ?: '(empty)') ?></pre>
<?php endforeach; endforeach; ?>
<?php endif; ?>

<?php endif; ?>
</body>
</html>
