<?php
require_once 'functions.php';

$error = null;
$success = false;

try {
    $newTokens = refreshAccessToken();
    $success = true;
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refresh Token - Canara Bank</title>
    <link rel="stylesheet" href="styles.css">
    <? php if ($success): ?>
    <meta http-equiv="refresh" content="2;url=index.php">
    <? php endif; ?>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔄 Refresh Access Token</h1>
        </div>

        <div class="card">
            <? php if ($success): ?>
                <div class="alert alert-success">
                    <strong>✓ Token Refreshed Successfully!</strong><br>
                    Redirecting to dashboard in 2 seconds...
                </div>
                
                <div class="info-grid">
                    <div class="info-item">
                        <label>New Access Token</label>
                        <value><?= substr($newTokens['access_token'], 0, 40) ?>...</value>
                    </div>
                    <div class="info-item">
                        <label>New Refresh Token</label>
                        <value><?= substr($newTokens['refresh_token'], 0, 40) ?>...</value>
                    </div>
                </div>
                
            <?php else: ?>
                <div class="alert alert-error">
                    <strong>✗ Token Refresh Failed</strong><br>
                    <? = htmlspecialchars($error) ?><br><br>
                    You need to re-authenticate. 
                </div>
                
                <div class="actions">
                    <a href="oauth-start.php" class="btn btn-primary">Re-authenticate</a>
                    <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>