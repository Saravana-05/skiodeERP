<?php
session_start();
require_once 'functions.php';

$config = getConfig();
$tokens = loadTokens();
$hasTokens = hasTokens();
$isExpired = isTokenExpired();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canara Bank UPI Integration</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏦 Canara Bank UPI Integration</h1>
            <p>OAuth 2.0 + A128CBC-HS256 Encryption</p>
        </div>

        <!-- OAuth Status Card -->
        <div class="card">
            <h2>OAuth Status</h2>
            
            <?php if ($hasTokens): ?>
                <?php if ($isExpired): ?>
                    <span class="status-badge warning">⚠️ Token Expired</span>
                <?php else: ?>
                    <span class="status-badge success">✓ Authenticated</span>
                <?php endif; ?>
                
                <div class="info-grid">
                    <div class="info-item">
                        <label>Access Token</label>
                        <value><?php echo substr($tokens['access_token'], 0, 30); ?>...</value>
                    </div>
                    <div class="info-item">
                        <label>Token Age</label>
                        <value><?php echo gmdate("H:i:s", time() - $tokens['created_at']); ?></value>
                    </div>
                    <div class="info-item">
                        <label>Expires In</label>
                        <value><?php echo max(0, 86400 - (time() - $tokens['created_at'])); ?> seconds</value>
                    </div>
                    <div class="info-item">
                        <label>Environment</label>
                        <value><?php echo strtoupper($config['environment']); ?></value>
                    </div>
                </div>
                
                <div class="actions">
                    <?php if ($isExpired): ?>
                        <a href="refresh-token.php" class="btn btn-warning">🔄 Refresh Token</a>
                    <?php endif; ?>
                    <a href="oauth-start.php" class="btn btn-primary">🔑 Re-authenticate</a>
                </div>
                
                <div class="token-info">
                    <strong>Token Details:</strong>
                    <pre><?php echo json_encode($tokens, JSON_PRETTY_PRINT); ?></pre>
                </div>
                
            <?php else: ?>
                <span class="status-badge error">✗ Not Authenticated</span>
                
                <div class="alert alert-info">
                    <strong>Getting Started: </strong><br>
                    1. Make sure you've updated <code>config.php</code> with your OAuth credentials<br>
                    2. Click the button below to start the OAuth flow<br>
                    3. Login with your OAuth username and password<br>
                    4. You'll be redirected back here with an access token
                </div>
                
                <a href="oauth-start.php" class="btn btn-primary">🚀 Start OAuth Flow</a>
            <?php endif; ?>
        </div>

        <!-- Configuration Card -->
        <div class="card">
            <h2>Configuration</h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <label>Merchant ID</label>
                    <value><?php echo $config['merchantId']; ?></value>
                </div>
                <div class="info-item">
                    <label>Terminal ID</label>
                    <value><?php echo $config['terminalId']; ?></value>
                </div>
                <div class="info-item">
                    <label>Sub-Merchant ID</label>
                    <value><?php echo $config['subMerchantId']; ?></value>
                </div>
                <div class="info-item">
                    <label>OAuth Client ID</label>
                    <value><?php echo substr($config['oauth']['clientId'], 0, 20); ?>...</value>
                </div>
                <div class="info-item">
                    <label>OAuth Scope</label>
                    <value><?php echo $config['oauth']['scope']; ?></value>
                </div>
                <div class="info-item">
                    <label>Redirect URI</label>
                    <value><?php echo $config['oauth']['redirectUri']; ?></value>
                </div>
            </div>
        </div>

        <!-- Actions Card -->
        <?php if ($hasTokens && !$isExpired): ?>
        <div class="card">
            <h2>API Operations</h2>
            
            <div class="actions">
                <a href="generate-qr.php" class="btn btn-success">📱 Generate QR Code</a>
                <a href="verify-vpa.php" class="btn btn-primary">✓ Verify VPA</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Help Card -->
        <div class="card">
            <h2>Need Help?</h2>
            
            <div class="alert alert-info">
                <strong>Configuration Required:</strong><br>
                Edit <code>config.php</code> and update:  <br>
                • <strong>OAuth Client ID</strong> - From App Creation<br>
                • <strong>OAuth Client Secret</strong> - From App Creation<br>
                • <strong>Redirect URI</strong> - Must match your callback URL<br>
                • <strong>Scope</strong> - Provided by Canara Bank (e.g., 'van')<br>
                • <strong>Master Key</strong> - 32-byte encryption key
            </div>
            
            <p style="margin-top: 15px;">
                <strong>Contact:</strong> apibanking@canarabank.com
            </p>
        </div>
    </div>
</body>
</html>