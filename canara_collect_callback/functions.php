<?php

/**
 * Helper Functions
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
function getConfig() {
    return require 'config.php';
}

// Get base URL
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    return $protocol . '://' . $host . $path;
}

// Save tokens
function saveTokens($tokens) {
    $config = getConfig();
    $tokens['created_at'] = time();
    file_put_contents($config['tokenFile'], json_encode($tokens, JSON_PRETTY_PRINT));
    @chmod($config['tokenFile'], 0600);
}

// Load tokens
function loadTokens() {
    $config = getConfig();
    if (file_exists($config['tokenFile'])) {
        return json_decode(file_get_contents($config['tokenFile']), true);
    }
    return null;
}

// Check if tokens exist
function hasTokens() {
    $tokens = loadTokens();
    return ! empty($tokens['access_token']);
}

// Check if token is expired (24 hours in UAT, 1 day in production)
function isTokenExpired() {
    $tokens = loadTokens();
    if (!$tokens) return true;
    
    $tokenAge = time() - ($tokens['created_at'] ??  0);
    return $tokenAge > 86400; // 24 hours
}

// Get valid access token (auto-refresh if needed)
function getValidAccessToken() {
    if (isTokenExpired()) {
        refreshAccessToken();
    }
    
    $tokens = loadTokens();
    return $tokens['access_token'] ?? null;
}

// Refresh access token
function refreshAccessToken() {
    $config = getConfig();
    $tokens = loadTokens();
    
    if (empty($tokens['refresh_token'])) {
        throw new Exception('No refresh token available');
    }
    
    $baseUrl = $config['apiUrl'][$config['environment']];
    $url = $baseUrl . '/v1/oauth2/refresh-token';
    
    $postData = http_build_query([
        'grant_type' => 'refresh_token',
        'refresh_token' => $tokens['refresh_token']
    ]);
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_USERPWD => $config['oauth']['clientId'] .  ':' . $config['oauth']['clientSecret'],
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $newTokens = json_decode($response, true);
        saveTokens($newTokens);
        return $newTokens;
    } else {
        throw new Exception("Token refresh failed:  $response");
    }
}

// Encryption class
class CanarabankEncryption {
    private $encryptionKey;
    private $hmacKey;
    
    public function __construct($masterKey) {
        if (strlen($masterKey) !== 32) {
            throw new Exception('Master key must be 32 bytes');
        }
        $this->hmacKey = substr($masterKey, 0, 16);
        $this->encryptionKey = substr($masterKey, 16, 16);
    }
    
    public function encrypt($plaintext) {
        $iv = openssl_random_pseudo_bytes(16);
        $ciphertext = openssl_encrypt($plaintext, 'AES-128-CBC', $this->encryptionKey, OPENSSL_RAW_DATA, $iv);
        if ($ciphertext === false) throw new Exception('Encryption failed');
        $tag = $this->calculateHMAC($iv, $ciphertext);
        return base64_encode($iv . $tag .  $ciphertext);
    }
    
    public function decrypt($encryptedData) {
        $combined = base64_decode($encryptedData);
        if (strlen($combined) < 32) throw new Exception('Invalid data');
        
        $iv = substr($combined, 0, 16);
        $tag = substr($combined, 16, 16);
        $ciphertext = substr($combined, 32);
        
        if (! hash_equals($this->calculateHMAC($iv, $ciphertext), $tag)) {
            throw new Exception('Authentication failed');
        }
        
        $plaintext = openssl_decrypt($ciphertext, 'AES-128-CBC', $this->encryptionKey, OPENSSL_RAW_DATA, $iv);
        if ($plaintext === false) throw new Exception('Decryption failed');
        return $plaintext;
    }
    
    private function calculateHMAC($iv, $ciphertext) {
        $hmac = hash_hmac('sha256', $iv . $ciphertext, $this->hmacKey, true);
        return substr($hmac, 0, 16);
    }
}

// Make API call
function callCanaraAPI($endpoint, $requestData) {
    $config = getConfig();
    $encryption = new CanarabankEncryption($config['masterKey']);
    
    // Encrypt request
    $jsonData = json_encode($requestData);
    $encryptedData = $encryption->encrypt($jsonData);
    
    $requestBody = [
        'Request' => [
            'body' => [
                'encryptData' => $encryptedData
            ]
        ]
    ];
    
    // Get access token
    $accessToken = getValidAccessToken();
    
    // Make API call
    $baseUrl = $config['apiUrl'][$config['environment']];
    $url = $baseUrl . '/v1/upi/' . $endpoint;
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestBody),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
            'X-Merchant-Id: ' . $config['merchantId']
        ],
        CURLOPT_TIMEOUT => 30
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        throw new Exception("API Error: $error");
    }
    
    if ($httpCode !== 200) {
        throw new Exception("HTTP $httpCode: $response");
    }
    
    $responseData = json_decode($response, true);
    
    // Decrypt response if encrypted
    if (isset($responseData['Response']['body']['encryptData']) && 
        is_string($responseData['Response']['body']['encryptData'])) {
        try {
            $decrypted = $encryption->decrypt($responseData['Response']['body']['encryptData']);
            $responseData['Response']['body']['encryptData'] = json_decode($decrypted, true);
        } catch (Exception $e) {
            // Response might not be encrypted
        }
    }
    
    return $responseData;
}