<?php
require_once 'functions. php';

$config = getConfig();
$result = null;
$error = null;

if (! hasTokens()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $requestData = [
            'source' => $config['merchantId'],
            'channel' => 'api',
            'extTransactionId' => 'TXN' . date('YmdHis') . rand(1000, 9999),
            'upiId' => $_POST['upiId'],
            'terminalId' => $_POST['terminalId'],
            'sid' => $_POST['sid'],
            'amount' => $_POST['amount'],
            'type' => $_POST['type'] ?? 'D',
            'remark' => $_POST['remark'],
            'reciept' => $_POST['reciept'],
            'requestTime' => date('Y-m-d H:i:s'),
            'param1' => $_POST['param1'] ?? '',
            'Param2' => $_POST['param2'] ?? '',
            'param3' => $_POST['param3'] ?? '',
            'checksum' => ''
        ];
        
        // Generate checksum
        $checksumString = implode('', [
            $requestData['source'],
            $requestData['channel'],
            $requestData['extTransactionId'],
            $requestData['upiId'],
            $requestData['terminalId'],
            $requestData['sid'],
            $requestData['amount'],
            $requestData['type'],
            $requestData['requestTime']
        ]);
        $requestData['checksum'] = hash('sha256', $checksumString);
        
        // Call API
        $result = callCanaraAPI('qr-generation', $requestData);
        
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
    <title>Generate QR Code - Canara Bank</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📱 Generate QR Code</h1>
            <p>Create dynamic or static UPI QR codes</p>
        </div>

        <div class="card">
            <h2>QR Code Parameters</h2>
            
            <form method="POST">
                <div class="form-group">
                    <label>Amount (₹) *</label>
                    <input type="text" name="amount" required placeholder="100. 00" value="<? = $_POST['amount'] ?? '100.00' ?>">
                    <small>Transaction amount in rupees</small>
                </div>
                
                <div class="form-group">
                    <label>UPI ID *</label>
                    <input type="text" name="upiId" required placeholder="merchant@cnrf" 
                           value="<?= $_POST['upiId'] ?? 'youtube. youtube001. youtube111. youtube121@cnrf' ?>">
                    <small>Merchant UPI ID</small>
                </div>
                
                <div class="form-group">
                    <label>Terminal ID *</label>
                    <input type="text" name="terminalId" required 
                           value="<?= $_POST['terminalId'] ?? $config['terminalId'] ?>">
                </div>
                
                <div class="form-group">
                    <label>Sub-Merchant ID *</label>
                    <input type="text" name="sid" required 
                           value="<?= $_POST['sid'] ??  $config['subMerchantId'] ?>">
                </div>
                
                <div class="form-group">
                    <label>Remark *</label>
                    <input type="text" name="remark" required placeholder="Payment for order #123" 
                           value="<?= $_POST['remark'] ?? 'Test Payment' ?>">
                </div>
                
                <div class="form-group">
                    <label>Receipt URL *</label>
                    <input type="url" name="reciept" required placeholder="https://example.com/receipt" 
                           value="<? = $_POST['reciept'] ?? 'https://example.com/receipt' ?>">
                </div>
                
                <div class="form-group">
                    <label>QR Type *</label>
                    <select name="type" style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius:  5px;">
                        <option value="D">Dynamic QR</option>
                        <option value="S">Static QR</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Custom Parameter 1 (Optional)</label>
                    <input type="text" name="param1" placeholder="Order ID" value="<?= $_POST['param1'] ?? '' ?>">
                </div>
                
                <div class="actions">
                    <button type="submit" class="btn btn-success">📱 Generate QR Code</button>
                    <a href="index.php" class="btn btn-secondary">← Back</a>
                </div>
            </form>
        </div>

        <? php if ($error): ?>
        <div class="card">
            <div class="alert alert-error">
                <strong>Error:</strong><br>
                <?= htmlspecialchars($error) ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($result && isset($result['Response']['body']['encryptData'])): ?>
        <?php $data = $result['Response']['body']['encryptData']; ?>
        
        <? php if ($data['status'] === 'SUCCESS'): ?>
        <div class="card">
            <h2>✓ QR Code Generated Successfully!</h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <label>Transaction ID</label>
                    <value><?= $data['extTransactionId'] ?></value>
                </div>
                <div class="info-item">
                    <label>Amount</label>
                    <value>₹<? = $data['amount'] ?></value>
                </div>
                <div class="info-item">
                    <label>Type</label>
                    <value><?= $data['type'] === 'D' ? 'Dynamic' : 'Static' ?></value>
                </div>
                <div class="info-item">
                    <label>Status</label>
                    <value><?= $data['status'] ? ></value>
                </div>
            </div>
            
            <div class="qr-display">
                <h3>Scan to Pay</h3>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?= urlencode($data['qrString']) ?>" 
                     alt="QR Code">
                     
                <div class="qr-string">
                    <strong>QR String:</strong><br>
                    <?= htmlspecialchars($data['qrString']) ?>
                </div>
            </div>
            
            <div class="actions">
                <a href="? new=1" class="btn btn-primary">Generate Another QR</a>
                <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
        <?php else: ?>
        <div class="card">
            <div class="alert alert-error">
                <strong>API Error:</strong><br>
                <?= htmlspecialchars($data['errorMsg'] ?? 'Unknown error') ?>
            </div>
            <pre><?= htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) ?></pre>
        </div>
        <?php endif; ?>
        <? php endif; ?>
    </div>
</body>
</html>