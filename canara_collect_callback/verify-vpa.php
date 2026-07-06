<?php
require_once 'functions.php';

$config = getConfig();
$result = null;
$error = null;

if (!hasTokens()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $requestData = [
            'source' => $config['merchantId'],
            'channel' => 'api',
            'extTransactionId' => 'VPA' . time() . rand(1000, 9999),
            'upiId' => $_POST['upiId'],
            'terminalId' => $_POST['terminalId'],
            'sid' => $_POST['sid'],
            'checksum' => ''
        ];
        
        // Generate checksum
        $checksumString = implode('', [
            $requestData['source'],
            $requestData['channel'],
            $requestData['extTransactionId'],
            $requestData['upiId'],
            $requestData['terminalId'],
            $requestData['sid']
        ]);
        $requestData['checksum'] = hash('sha256', $checksumString);
        
        // Call API
        $result = callCanaraAPI('verify-vpa', $requestData);
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify VPA - Canara Bank</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ Verify VPA</h1>
            <p>Verify Virtual Payment Address</p>
        </div>

        <div class="card">
            <h2>VPA Verification</h2>
            
            <form method="POST">
                <div class="form-group">
                    <label>UPI ID *</label>
                    <input type="text" name="upiId" required placeholder="customer@cnrf" 
                           value="<?= $_POST['upiId'] ?? 'youtube.youtube001.youtube111.youtube121@cnrf' ?>">
                    <small>Enter the UPI ID to verify</small>
                </div>
                
                <div class="form-group">
                    <label>Terminal ID *</label>
                    <input type="text" name="terminalId" required 
                           value="<? = $_POST['terminalId'] ?? $config['terminalId'] ?>">
                </div>
                
                <div class="form-group">
                    <label>Sub-Merchant ID *</label>
                    <input type="text" name="sid" required 
                           value="<?= $_POST['sid'] ?? $config['subMerchantId'] ?>">
                </div>
                
                <div class="actions">
                    <button type="submit" class="btn btn-primary">✓ Verify VPA</button>
                    <a href="index.php" class="btn btn-secondary">← Back</a>
                </div>
            </form>
        </div>

        <?php if ($error): ?>
        <div class="card">
            <div class="alert alert-error">
                <strong>Error:</strong><br>
                <?= htmlspecialchars($error) ?>
            </div>
        </div>
        <? php endif; ?>

        <?php if ($result && isset($result['Response']['body']['encryptData'])): ?>
        <?php $data = $result['Response']['body']['encryptData']; ?>
        
        <?php if ($data['status'] === 'SUCCESS' && isset($data['data'][0])): ?>
        <div class="card">
            <h2>✓ VPA Verified Successfully!</h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <label>Customer Name</label>
                    <value><?= htmlspecialchars($data['data'][0]['customerName']) ?></value>
                </div>
                <div class="info-item">
                    <label>MCC Code</label>
                    <value><?= $data['data'][0]['mcc'] ?></value>
                </div>
                <div class="info-item">
                    <label>Response Code</label>
                    <value><?= $data['data'][0]['respCode'] ?></value>
                </div>
                <div class="info-item">
                    <label>Response Message</label>
                    <value><?= $data['data'][0]['respMessge'] ?></value>
                </div>
            </div>
            
            <div class="actions">
                <a href="? new=1" class="btn btn-primary">Verify Another VPA</a>
                <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
        <?php else: ?>
        <div class="card">
            <div class="alert alert-error">
                <strong>Verification Failed</strong><br>
                Status: <?= htmlspecialchars($data['status'] ?? 'Unknown') ?>
            </div>
            <pre><? = htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) ?></pre>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>