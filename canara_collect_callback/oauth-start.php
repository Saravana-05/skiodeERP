<?php
session_start();
require_once 'functions.php';

$config = getConfig();
$oauth = $config['oauth'];
$baseUrl = $config['apiUrl'][$config['environment']];

// Generate state for CSRF protection
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

// Build authorization URL
$authUrl = $baseUrl . '/v1/oauth2/authorize?' .  http_build_query([
    'client_id' => $oauth['clientId'],
    'redirect_uri' => $oauth['redirectUri'],
    'response_type' => 'code',
    'state' => $state,
    'scope' => $oauth['scope']
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start OAuth Flow - Canara Bank</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 OAuth Authorization</h1>
            <p>Step 1: Get Authorization Code</p>
        </div>

        <div class="card">
            <h2>Instructions</h2>
            
            <div class="alert alert-info">
                <strong>What happens next:</strong><br>
                1. You'll be redirected to Canara Bank's login page<br>
                2. Login with your OAuth username and password<br>
                3. After successful login, you'll be redirected back<br>
                4. Your access token will be automatically generated
            </div>

            <div class="info-grid" style="margin:  20px 0;">
                <div class="info-item">
                    <label>Client ID</label>
                    <value><?php echo substr($oauth['clientId'], 0, 30); ?>...</value>
                </div>
                <div class="info-item">
                    <label>Redirect URI</label>
                    <value><?php echo $oauth['redirectUri']; ?></value>
                </div>
                <div class="info-item">
                    <label>Scope</label>
                    <value><?php echo $oauth['scope']; ?></value>
                </div>
                <div class="info-item">
                    <label>State</label>
                    <value><?php echo substr($state, 0, 20); ?>...</value>
                </div>
            </div>

            <div class="actions">
                <a href="<?php echo htmlspecialchars($authUrl); ?>" class="btn btn-primary">
                    🚀 Continue to Canara Bank Login
                </a>
                <a href="index.php" class="btn btn-secondary">← Back to Dashboard</a>
            </div>

            <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px;">
                <strong>Authorization URL:</strong>
                <div style="margin-top: 10px; font-size: 12px; word-break: break-all; font-family: monospace;">
                    <?php echo htmlspecialchars($authUrl); ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>