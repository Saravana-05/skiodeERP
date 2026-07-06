<?php
/**
 * Canara Bank environment diagnostic — upload & run once, then delete.
 * Visit: https://citizenprintz.in/storefront/api/canara_test.php
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');

echo '<!DOCTYPE html><html><head><title>Canara Diagnostic</title>
<style>body{font-family:monospace;padding:20px} .ok{color:green} .fail{color:red} .warn{color:orange} table{border-collapse:collapse;margin:10px 0} td,th{border:1px solid #ccc;padding:6px 10px}</style></head><body>';
echo '<h2>Canara Bank PHP Environment Check</h2>';

// 1. PHP version
$v = PHP_VERSION;
$ok = version_compare($v, '7.2.0', '>=');
echo "<p>PHP Version: <b>$v</b> " . ($ok ? '<span class="ok">✓</span>' : '<span class="fail">✗ Need 7.2+</span>') . "</p>";

// 2. OpenSSL extension
$ssl = extension_loaded('openssl');
echo "<p>OpenSSL extension: " . ($ssl ? '<span class="ok">✓ loaded</span>' : '<span class="fail">✗ MISSING</span>') . "</p>";
if ($ssl) echo "<p>OpenSSL version: " . OPENSSL_VERSION_TEXT . "</p>";

// 3. cURL extension
$curl = extension_loaded('curl');
echo "<p>cURL extension: " . ($curl ? '<span class="ok">✓ loaded</span>' : '<span class="fail">✗ MISSING</span>') . "</p>";

// 4. AES-256-WRAP cipher
if ($ssl) {
    $ciphers = openssl_get_cipher_methods();
    $has_wrap = in_array('aes-256-wrap', array_map('strtolower', $ciphers));
    echo "<p>AES-256-WRAP cipher: " . ($has_wrap ? '<span class="ok">✓ available</span>' : '<span class="fail">✗ MISSING — JWE will fail</span>') . "</p>";
}

// 5. pack('J') 64-bit support
$pack_ok = false;
try {
    $test = pack('J', 12345);
    $pack_ok = strlen($test) === 8;
} catch (Throwable $e) {}
echo "<p>pack('J') 64-bit: " . ($pack_ok ? '<span class="ok">✓ works</span>' : '<span class="fail">✗ FAILED — need 64-bit PHP</span>') . "</p>";

// 6. File write test
$token_file = __DIR__ . '/canara_tokens.json';
$write_ok = @file_put_contents($token_file . '.testwrite', 'test') !== false;
if ($write_ok) @unlink($token_file . '.testwrite');
echo "<p>Directory writable (__DIR__): " . ($write_ok ? '<span class="ok">✓</span>' : '<span class="fail">✗ Cannot write files here</span>') . "</p>";

// 7. Existing token file
if (file_exists($token_file)) {
    $tokens = json_decode(file_get_contents($token_file), true);
    $expired = isset($tokens['expires_at']) && time() >= $tokens['expires_at'];
    $exp_str = isset($tokens['expires_at']) ? date('d-M-Y H:i:s', $tokens['expires_at']) : 'unknown';
    echo "<p>Token file: <span class='ok'>EXISTS</span> — expires $exp_str IST " . ($expired ? '<span class="fail">(EXPIRED)</span>' : '<span class="ok">(VALID)</span>') . "</p>";
} else {
    echo "<p>Token file: <span class='warn'>not found</span> (need to complete OAuth)</p>";
}

// 8. Private key
$key_file = __DIR__ . '/canara_private_key.pem';
$key_exists = file_exists($key_file);
echo "<p>Private key (canara_private_key.pem): " . ($key_exists ? '<span class="ok">✓ found</span>' : '<span class="fail">✗ NOT FOUND</span>') . "</p>";
if ($key_exists) {
    $pem = file_get_contents($key_file);
    $key = openssl_pkey_get_private($pem);
    echo "<p>Private key loadable: " . ($key ? '<span class="ok">✓ yes</span>' : '<span class="fail">✗ invalid PEM</span>') . "</p>";
}

// 9. canara_bank_lib.php include test
echo "<hr><h3>Include Test</h3>";
try {
    include_once 'canara_bank_lib.php';
    echo '<p><span class="ok">✓ canara_bank_lib.php included successfully</span></p>';

    // Quick JWE test
    $jwe = cb_jwe_encrypt('{"test":"hello"}');
    $dec = cb_jwe_decrypt($jwe);
    $jwe_ok = $dec === '{"test":"hello"}';
    echo "<p>JWE encrypt/decrypt round-trip: " . ($jwe_ok ? '<span class="ok">✓ passed</span>' : '<span class="fail">✗ FAILED</span>') . "</p>";

} catch (Throwable $e) {
    echo '<p><span class="fail">✗ Include/JWE error: ' . htmlspecialchars($e->getMessage()) . '</span></p>';
}

// 10. cURL to Canara OAuth (just TCP connect, no real request)
if ($curl) {
    echo "<hr><h3>Network Connectivity</h3>";
    $ch = curl_init('https://oauth.canarabank.bank.in/v1/oauth2/token');
    curl_setopt_array($ch, [
        CURLOPT_NOBODY         => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    echo "<p>Connect to oauth.canarabank.bank.in: " . ($err ? '<span class="fail">✗ ' . htmlspecialchars($err) . '</span>' : "<span class='ok'>✓ HTTP $code</span>") . "</p>";
}

echo '<hr><p style="font-size:12px;color:#999;">Delete this file after review.</p></body></html>';
