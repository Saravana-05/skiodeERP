<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Canara Bank OAuth callback.
 * Approved URL: https://citizenprintz.in/storefront/api/auth-callback.php
 */
include_once 'canara_bank_lib.php';

$code  = $_GET['code']  ?? '';
$state = $_GET['state'] ?? '';
$error = $_GET['error'] ?? '';

cb_log("[CanaraOAuth] code=$code state=$state error=$error");

if ($error) {
    http_response_code(400);
    die("Bank returned error: " . htmlspecialchars($error));
}
if (!$code) {
    http_response_code(400);
    die("Missing authorization code.");
}

try {
    $post_data = http_build_query([
        'grant_type'   => 'authorization_code',
        'code'         => $code,
        'redirect_uri' => CB_REDIRECT_URI,
        'scope'        => CB_OAUTH_SCOPE,
    ]);

    $ch = curl_init(CB_TOKEN_URL);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $post_data,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER     => [
            'Authorization: ' . cb_basic_auth(),
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
            'User-Agent: CitizenPrintz-ERP/1.0',
        ],
    ]);
    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_err  = curl_error($ch);
    curl_close($ch);

    cb_log("[CanaraOAuth] token response HTTP $http_code: $response");

    if ($curl_err) throw new Exception("cURL error: $curl_err");
    if ($http_code !== 200) throw new Exception("Token exchange failed HTTP $http_code: $response");

    $data = json_decode($response, true);
    if (empty($data['access_token'])) throw new Exception("No access_token in response: $response");

    cb_save_tokens($data);

    echo '<!DOCTYPE html><html><head><title>Auth Success</title></head><body>';
    echo '<h2 style="color:green;font-family:sans-serif;">&#10003; Canara Bank OAuth completed successfully.</h2>';
    echo '<p style="font-family:sans-serif;">Token saved. You can close this tab.</p>';
    echo '<p style="font-family:sans-serif;font-size:13px;color:#555;">access_token received: ' . substr($data['access_token'], 0, 20) . '...</p>';
    echo '</body></html>';

} catch (Exception $e) {
    cb_log("[CanaraOAuth] ERROR: " . $e->getMessage());
    echo '<h3 style="color:red;font-family:sans-serif;">OAuth Error</h3>';
    echo '<pre style="background:#f8f8f8;padding:15px;font-size:13px;">' . htmlspecialchars($e->getMessage()) . '</pre>';
}
