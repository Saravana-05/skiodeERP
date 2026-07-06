<?php
session_start();
require_once 'functions.php';

$config = getConfig();
$error = null;
$success = false;

// Check for authorization code
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $state = $_GET['state'] ?? '';
    $scope = $_GET['scope'] ?? '';
    
    try {
        // Exchange code for tokens
        $baseUrl = $config['apiUrl'][$config['environment']];
        $tokenUrl = $baseUrl .  '/v1/oauth2/token';
        
        $postData = http_build_query([
            'grant_type' => 'authorization_code',
            'redirect_uri' => $config['oauth']['redirectUri'],
            'code' => $code,
            'scope' => $config['oauth']['scope']
        ]);
        
        $ch = curl_init($tokenUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type:  application/x-www-form-urlencoded'],
            CURLOPT_USERPWD => $config['oauth']['clientId'] . ':' . $config['oauth']['clientSecret'],
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $tokens = json_decode($response, true);
            saveTokens($tokens);
            $success = true;
        } else {
            $error = "Token exchange failed (HTTP $httpCode): " . $response;
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    
} elseif (isset($_GET['error'])) {
    $error = $_GET['error_description'] ?? $_GET['error'];
} else {
    $error = "No authorization code received";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OAuth Callback - Canara Bank</title>
    <link rel="stylesheet" href="styles.css">
    <? php if ($success): ?>
    <meta http-equiv="refresh" content="3;url=index.php">
    <?php endif; ?>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 OAuth Callback</h1>
            <p>Processing authorization...</p>
        </div>

        <div class="card">
            <? php if ($success): ?>
                <div class="alert alert-success">
                    <strong>✓ Success!</strong><br>
                    Access token obtained successfully! <br>
                    Redirecting to dashboard in 3 seconds...
                </div>
                
                <? php
                $tokens = loadTokens();
                ?>
                
                <div class="info-grid">
                    <div class="info-item">
                        <label>Access Token</label>
                        <value><?php echo substr($tokens['access_token'], 0, 40); ?>...</value>
                    </div>
                    <div class="info-item">
                        <label>Token Type</label>
                        <value><?php echo $tokens['token_type'] ?? 'Bearer'; ?></value>
                    </div>
                    <div class="info-item">
                        <label>Expires In</label>
                        <value><?php echo $tokens['expires_in'] ?? 86400; ?> seconds</value>
                    </div>
                    <div class="info-item">
                        <label>Scope</label>
                        <value><?php echo $tokens['scope'] ?? $config['oauth']['scope']; ?></value>
                    </div>
                </div>
                
                <div class="actions">
                    <a href="index.php" class="btn btn-success">Go to Dashboard</a>
                    <a href="generate-qr.php" class="btn btn-primary">Generate QR Code</a>
                </div>
                
            <? php else: ?>
                <div class="alert alert-error">
                    <strong>✗ Error</strong><br>
                    <? php echo htmlspecialchars($error); ?>
                </div>
                
                <div class="actions">
                    <a href="oauth-start.php" class="btn btn-primary">Try Again</a>
                    <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>