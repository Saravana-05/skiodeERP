<?php
/**
 * Canara Bank OAuth – Start Page
 * Visit this page ONCE to get the access token saved.
 * After that, tokens are auto-refreshed — no need to visit again unless refresh token expires.
 *
 * URL: https://citizenprintz.in/storefront/api/canara_oauth_start.php
 */
include_once 'canara_bank_lib.php';

// Build Canara Bank authorization URL
$auth_url = CB_OAUTH_BASE . '/v1/oauth2/authorize?' . http_build_query([
    'client_id'     => CB_CLIENT_ID,
    'redirect_uri'  => CB_REDIRECT_URI,
    'response_type' => 'code',
    'scope'         => CB_OAUTH_SCOPE,
    'state'         => bin2hex(random_bytes(8)),
]);

// Check if token already exists
$tokens       = cb_load_tokens();
$token_exists = !empty($tokens['access_token']);
$token_expiry = $token_exists ? date('d-M-Y H:i:s', $tokens['expires_at']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Canara Bank OAuth – Printzy ERP</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; padding: 40px 20px; }
        .box { background: #fff; border-radius: 8px; padding: 32px 36px; max-width: 520px; width: 100%; box-shadow: 0 2px 12px rgba(0,0,0,0.1); }
        h2  { margin: 0 0 6px; color: #1a1a1a; }
        p   { color: #555; font-size: 14px; }
        .status-ok  { background: #e6f9ed; border: 1px solid #2ecc71; border-radius: 6px; padding: 14px 16px; margin: 20px 0; }
        .status-no  { background: #fff3e0; border: 1px solid #f39c12; border-radius: 6px; padding: 14px 16px; margin: 20px 0; }
        .status-ok span { color: #27ae60; font-weight: bold; }
        .status-no span { color: #e67e22; font-weight: bold; }
        .btn { display: inline-block; background: #1a6fc4; color: #fff; padding: 12px 28px; border-radius: 6px; text-decoration: none; font-size: 15px; font-weight: bold; margin-top: 16px; }
        .btn:hover { background: #155a9e; }
        ol li { margin: 6px 0; font-size: 14px; color: #444; }
        .url  { font-size: 11px; background: #f0f0f0; padding: 8px 10px; border-radius: 4px; word-break: break-all; margin-top: 16px; color: #333; }
    </style>
</head>
<body>
<div class="box">
    <h2>🔐 Canara Bank OAuth Setup</h2>
    <p>This is a <strong>one-time setup</strong>. After completing this, the ERP can generate UPI QR codes automatically.</p>

    <?php if ($token_exists): ?>
    <div class="status-ok">
        <span>✓ Token already saved</span><br>
        <small style="color:#555;">Expires: <?= $token_expiry ?> IST</small>
    </div>
    <p>Token is active. You only need to redo this if you see "No access token" errors.</p>
    <?php else: ?>
    <div class="status-no">
        <span>⚠ No token saved yet</span><br>
        <small style="color:#777;">Complete the steps below to activate QR payments.</small>
    </div>
    <?php endif; ?>

    <ol>
        <li>Click the button below</li>
        <li>You will be taken to <strong>Canara Bank's login page</strong></li>
        <li>Enter your <strong>OAuth username &amp; password</strong> (provided by the bank)</li>
        <li>Bank will redirect back here automatically</li>
        <li>Token gets saved — QR payments are now active</li>
    </ol>

    <a href="<?= htmlspecialchars($auth_url) ?>" class="btn">🚀 Login with Canara Bank</a>

    <div class="url"><strong>Callback URL registered with bank:</strong><br><?= CB_REDIRECT_URI ?></div>
</div>
</body>
</html>
