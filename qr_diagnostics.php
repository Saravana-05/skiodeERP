<?php
/**
 * qr_diagnostics.php
 * Admin diagnostic page: look up any QR transaction by JC number or ext_id
 * and see exactly what the DB has, what FastAPI returns, and what the log shows.
 */
session_start();
include_once "connect_db.php";
include_once "page_guard.php";
include_once "api/canara_bank_lib.php";
cb_ensure_table($connection);
fn_ensure_pending_qr_table($connection);

$jcno   = intval($_GET['jcno']   ?? 0);
$ext_id = trim($_GET['ext_id']   ?? '');
$action = trim($_GET['action']   ?? '');

// ── Run live bank check if requested ─────────────────────────────────────────
$live_results = [];
if ($action === 'check' && ($jcno > 0 || $ext_id)) {
    foreach (['ECOMMERCE', 'ERP'] as $src) {
        $payload = json_encode([
            'extTransactionId' => $ext_id,
            'source'           => $src,
        ]);
        $ch = curl_init('https://api.citizenprintz.in/api/bank/qr-status-extid');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);
        $resp     = curl_exec($ch);
        $http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $cerr     = curl_error($ch);
        curl_close($ch);
        $live_results[$src] = [
            'http'     => $http,
            'curl_err' => $cerr,
            'raw'      => $resp,
            'parsed'   => json_decode($resp, true),
        ];
    }
}

// ── Load DB data ──────────────────────────────────────────────────────────────
$ct_rows  = [];
$pqc_rows = [];
$jm_row   = null;

if ($jcno > 0) {
    $r = mysqli_query($connection,
        "SELECT * FROM canara_transactions WHERE jobcard_no='$jcno' ORDER BY created_at DESC");
    while ($row = mysqli_fetch_assoc($r)) $ct_rows[] = $row;

    $r2 = mysqli_query($connection,
        "SELECT * FROM pending_qr_closures WHERE jobcard_no=$jcno");
    while ($row = mysqli_fetch_assoc($r2)) $pqc_rows[] = $row;

    $r3 = mysqli_query($connection,
        "SELECT jobcard_no, advance_gpay, advance_cash, is_adv_in_gpay, job_card_closed FROM jobcard_master WHERE jobcard_no=$jcno LIMIT 1");
    if ($r3) $jm_row = mysqli_fetch_assoc($r3);

    // If no ext_id provided, use the most recent one from canara_transactions
    if (!$ext_id && !empty($ct_rows)) {
        $ext_id = $ct_rows[0]['ext_transaction_id'];
    }
}

// ── Read last 60 lines of canara_debug.log ────────────────────────────────────
// cb_log() writes to api/ if writable, otherwise falls back to sys_get_temp_dir()
$log_lines = [];
$log_file  = __DIR__ . '/api/canara_debug.log';
if (!file_exists($log_file)) {
    $log_file = sys_get_temp_dir() . '/canara_debug.log'; // IIS fallback
}
if (file_exists($log_file)) {
    $all = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    // Filter to JC or ext_id if known
    $filter = $jcno ? "JC $jcno" : ($ext_id ? substr($ext_id, 0, 12) : '');
    if ($filter) {
        $log_lines = array_filter($all, fn($l) => strpos($l, $filter) !== false || strpos($l, 'QRStatus') !== false);
        $log_lines = array_slice(array_values($log_lines), -60);
    } else {
        $log_lines = array_slice($all, -60);
    }
}
?>
<style>
.diag-box { background:#1e1e2e; color:#cdd6f4; font-family:monospace; font-size:0.75rem; padding:10px; border-radius:6px; max-height:280px; overflow-y:auto; white-space:pre-wrap; word-break:break-all; }
.diag-section { margin-bottom:18px; }
.diag-section h6 { background:#313244; color:#cba6f7; padding:5px 10px; border-radius:4px; margin-bottom:6px; font-size:0.8rem; }
.badge-ok  { background:#28a745; color:#fff; border-radius:4px; padding:1px 8px; font-size:0.75rem; }
.badge-bad { background:#dc3545; color:#fff; border-radius:4px; padding:1px 8px; font-size:0.75rem; }
.badge-warn{ background:#ffc107; color:#000; border-radius:4px; padding:1px 8px; font-size:0.75rem; }
table.dtbl { width:100%; font-size:0.75rem; border-collapse:collapse; }
table.dtbl th { background:#313244; color:#cba6f7; padding:3px 6px; }
table.dtbl td { padding:3px 6px; border-bottom:1px solid #333; }
</style>

<div class="bg_aliceblue p-3 m-1 pt-0">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center p-2 mb-3"
         style="background:#1e1e2e;border-radius:5px;color:#cdd6f4;">
        <h5 class="mb-0">🔍 QR Payment Diagnostics</h5>
    </div>

    <form method="GET" class="row g-2 align-items-center mb-3">
        <div class="col-auto">
            <label class="col-form-label">Job Card No :</label>
        </div>
        <div class="col-auto">
            <input type="number" name="jcno" class="form-control form-control-sm"
                   value="<?= $jcno ?: '' ?>" placeholder="e.g. 123" style="width:120px;" />
        </div>
        <div class="col-auto">
            <label class="col-form-label">Ext Txn ID :</label>
        </div>
        <div class="col-auto">
            <input type="text" name="ext_id" class="form-control form-control-sm"
                   value="<?= htmlspecialchars($ext_id) ?>" placeholder="EXT..." style="width:240px;" />
        </div>
        <div class="col-auto">
            <button type="submit" name="action" value="view" class="btn btn-sm btn-secondary">View DB</button>
        </div>
        <div class="col-auto">
            <button type="submit" name="action" value="check" class="btn btn-sm btn-primary">
                🔄 Check Bank Now (ECOMMERCE + ERP)
            </button>
        </div>
    </form>

    <?php if ($jcno > 0 || $ext_id): ?>

    <div class="row">
        <div class="col-8">

            <?php if ($jm_row): ?>
            <div class="diag-section">
                <h6>📋 jobcard_master (JC <?= $jcno ?>)</h6>
                <div class="diag-box"><?= json_encode($jm_row, JSON_PRETTY_PRINT) ?></div>
            </div>
            <?php endif; ?>

            <div class="diag-section">
                <h6>💳 canara_transactions for JC <?= $jcno ?: $ext_id ?></h6>
                <?php if (empty($ct_rows)): ?>
                    <div class="diag-box">No records found.</div>
                <?php else: ?>
                <table class="dtbl">
                    <tr><th>ext_transaction_id</th><th>status</th><th>amount</th><th>created_at</th><th>paid_at</th><th>rrn</th></tr>
                    <?php foreach ($ct_rows as $ct): ?>
                    <tr>
                        <td style="font-size:0.65rem;"><?= htmlspecialchars($ct['ext_transaction_id']) ?></td>
                        <td>
                            <?php if ($ct['status'] === 'SUCCESS'): ?>
                                <span class="badge-ok">SUCCESS</span>
                            <?php elseif ($ct['status'] === 'PENDING'): ?>
                                <span class="badge-warn">PENDING</span>
                            <?php else: ?>
                                <span class="badge-bad"><?= htmlspecialchars($ct['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>₹<?= number_format($ct['amount'], 2) ?></td>
                        <td><?= $ct['created_at'] ?></td>
                        <td><?= $ct['paid_at'] ?: '—' ?></td>
                        <td><?= htmlspecialchars($ct['rrn'] ?? '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php endif; ?>
            </div>

            <div class="diag-section">
                <h6>⏳ pending_qr_closures for JC <?= $jcno ?></h6>
                <?php if (empty($pqc_rows)): ?>
                    <div class="diag-box">No records found — JC was not parked for QR payment.</div>
                <?php else: ?>
                <div class="diag-box"><?= json_encode($pqc_rows, JSON_PRETTY_PRINT) ?></div>
                <?php endif; ?>
            </div>

            <?php if (!empty($live_results)): ?>
            <div class="diag-section">
                <h6>🌐 Live Bank API responses</h6>
                <?php foreach ($live_results as $src => $lr): ?>
                <p style="color:#cba6f7;margin:4px 0;font-size:0.75rem;"><strong>source=<?= $src ?></strong>
                    — HTTP <?= $lr['http'] ?>
                    <?php if ($lr['curl_err']): ?><span class="badge-bad">CURL ERR: <?= htmlspecialchars($lr['curl_err']) ?></span><?php endif; ?>
                    <?php
                        // Read status from all possible field names
                        $p = $lr['parsed'] ?? [];
                        $raw_s = $p['status'] ?? $p['txnStatus'] ?? $p['payment_status']
                              ?? $p['paymentStatus'] ?? $p['txn_status'] ?? '?';
                        $s = strtoupper(trim((string)$raw_s));
                        // Normalise — Canara Bank uses CREDIT for merchant-side success
                        $is_success = in_array($s, ['SUCCESS','PAID','CREDIT','SUCCESSFUL','APPROVED','COMPLETE','S']);
                        $is_failed  = in_array($s, ['FAILURE','FAILED','FAIL','DECLINED','REJECTED','F']);
                        if ($is_success) echo '<span class="badge-ok">SUCCESS (' . htmlspecialchars($s) . ')</span>';
                        elseif ($s === 'PENDING' || $s === 'INITIATED') echo '<span class="badge-warn">PENDING</span>';
                        elseif ($is_failed) echo '<span class="badge-bad">FAILED (' . htmlspecialchars($s) . ')</span>';
                        else echo '<span class="badge-warn">? ' . htmlspecialchars($s) . '</span>';
                    ?>
                </p>
                <div class="diag-box"><?= htmlspecialchars($lr['raw'] ?: '(empty response)') ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div>

        <div class="col-4">
            <div class="diag-section">
                <h6>📝 canara_debug.log (last 60 related entries)</h6>
                <p style="font-size:0.68rem;color:#888;margin:0 0 4px;">
                    Log path: <code><?= htmlspecialchars($log_file) ?></code>
                    <?= file_exists($log_file)
                        ? ' <span class="badge-ok">exists</span>'
                        : ' <span class="badge-bad">not found yet</span>' ?>
                </p>
                <div class="diag-box" style="max-height:600px;">
                    <?php if (empty($log_lines)): ?>
                        <?= file_exists($log_file)
                            ? 'No log entries matching JC ' . $jcno . ' / ' . htmlspecialchars($ext_id)
                            : 'Log file does not exist yet — will be created on first QR status check after this fix is deployed.' ?>
                    <?php else: ?>
                        <?= htmlspecialchars(implode("\n", $log_lines)) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>

    <div class="mt-3" style="font-size:0.75rem;color:#6c757d;">
        <strong>How to use:</strong> Enter the Job Card number and click "Check Bank Now" to see both what the DB has
        AND what FastAPI returns for <code>source=ECOMMERCE</code> and <code>source=ERP</code> side by side.
        The difference between these two tells you whether the source mismatch is the issue.
        The log shows every status check ever made for this JC.
    </div>
</div>
