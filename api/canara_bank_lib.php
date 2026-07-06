<?php
/**
 * Canara Bank Payment QR – standalone library for ERP storefront.
 * No dependency on the ecommerce Python project.
 *
 * Place your RSA private key at:
 *   storefront/api/canara_private_key.pem
 * (copy the same key used by the ecommerce backend).
 */

// ─── Credentials & endpoints ────────────────────────────────────────────────
define('CB_CLIENT_ID',      'HlpU92cKxh4Aq3wwOMttGsyKddgneAl2');
define('CB_CLIENT_SECRET',  'vh8LSH58ZuVCFSNMcAnOxf9lpGth1aNg');
define('CB_SYMMETRIC_KEY',  '4d7a98982739e508985fdd5a3606bee53a27b3dde8850f7f1b65a2ecda996262');
define('CB_MERCHANT_ID',    'MIDCPRIN01');
define('CB_TERMINAL_ID',    'TRDCIN0001');
define('CB_SID',            'SIDCIN0001');
define('CB_UPI_ID',         'mrch.midcprin01.sidcin0001.trdcin0001@cnrb');
define('CB_CLIENT_IP',      '103.122.53.71');
define('CB_PRIVATE_KEY',    __DIR__ . '/canara_private_key.pem');
define('CB_TOKEN_FILE',     __DIR__ . '/canara_tokens.json');

define('CB_API_BASE',       'https://api.canara.bank.in/v1/upi');
define('CB_OAUTH_BASE',     'https://oauth.canarabank.bank.in');
define('CB_TOKEN_URL',      'https://api.canara.bank.in/v1/oauth2/token');
define('CB_REFRESH_URL',    'https://api.canara.bank.in/v1/oauth2/refresh-token');
define('CB_REDIRECT_URI',   'https://citizenprintz.in/storefront/api/auth-callback.php');
define('CB_OAUTH_SCOPE',    'collection');

define('CB_PUBLIC_CERT',
'MIIGlzCCBP+gAwIBAgIQBEu3ERYfD2xIDvzaJ8yIEjANBgkqhkiG9w0BAQsFADBg' .
'MQswCQYDVQQGEwJHQjEYMBYGA1UEChMPU2VjdGlnbyBMaW1pdGVkMTcwNQYDVQQD' .
'Ey5TZWN0aWdvIFB1YmxpYyBTZXJ2ZXIgQXV0aGVudGljYXRpb24gQ0EgRFYgUjM2' .
'MB4XDTI2MDQyNDAwMDAwMFoXDTI2MTAyMzIzNTk1OVowGzEZMBcGA1UEAxMQY2l0' .
'aXplbnByaW50ei5pbjCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBALhx9' .
'eeuHVQhZsyPca+1glmVYfDgfYEoNgc2hNEHctGR7Z91DqtzFHsLynWhzoQXFIauM' .
'UNXf48snfnKBb8B9e75QzG9JypJp/gfEu/KbcPp/07MQxztEg1peifhvkwBuGb5x' .
'2KLqRgojwYWP+nfnj3ftHhMgJhRTqKrDVJBvbOB9URsdTUSR6sB4dw5NhMDCkZzo' .
'qC3/Bk58Q7dXF7BFXR5jikp4rVzp9MacMLqCL16sxt4Ev8jePEXI+rA/7RxeFWEf' .
'aOiuaiGJm8kOV59pESK4xRUMfdaXzcsA6TrQLwylckptyPe0qTG1UhnARckG/cj8y' .
'49tSPt5x98Fycep9MCAwEAAaOCAxAwggMMMB8GA1UdIwQYMBaAFGjAEhYYDq/O9oe' .
'mMlejRlFdywcnMB0GA1UdDgQWBBSMrvgKCwE5sjlyKogKwsHq5PEcXzAOBgNVHQ8B' .
'Af8EBAMCBaAwDAYDVR0TAQH/BAIwADAdBgNVHSUEFjAUBggrBgEFBQcDAQYIKwYB' .
'BQUHAwIwSQYDVR0gBEIwQDA0BgsrBgEEAbIxAQICBzAlMCMGCCsGAQUFBwIBFhdo' .
'dHRwczovL3NlY3RpZ28uY29tL0NQUzAIBgZngQwBAgEwgYQGCCsGAQUFBwEBBHgw' .
'djBPBggrBgEFBQcwAoZDaHR0cDovL2NydC5zZWN0aWdvLmNvbS9TZWN0aWdvUHVi' .
'bGljU2VydmVyQXV0aGVudGljYXRpb25DQURWUjM2LmNydDAjBggrBgEFBQcwAYYX' .
'aHR0cDovL29jc3Auc2VjdGlnby5jb20wMQYDVR0RBCowKIIQY2l0aXplbnByaW50' .
'ei5pboIUd3d3LmNpdGl6ZW5wcmludHouaW4wggGGBgorBgEEAdZ5AgQCBIIBdgSC' .
'AXIBcAB3ANdtfRDRp/V3wsfpX9cAv/mCyTNaZeHQswFzF8DIxWl3AAABnb8SZekA' .
'AAQDAEgwRgIhAPEFL6YS2vUcRsQRMdQ8rg/Oi/lHAVKymX3oqOXBTeTEAiEAksyj' .
'I/K+zp3bRilcX3UKM5Hmceb92atpQmM6lgtLuXMAdgDIo8R/x7OtuTVrAT9qehJt' .
'4zpOQ6XGRvmXrTl1mR3PmgAAAZ2/EmZIAAAEAwBHMEUCIB1uFD5EJ2jKseZHC77F' .
'1LZqMp2R+mdut6IMSTNYb9YfAiEA6LFrdY5B9wUKkcTEqFa3vcXcq/BffBTdk4Ur' .
'iQsTHygAfQBs/lAZQ6heqRa8UtEz5NzJHvFBHH0lhCDRc4CeGBjrOgAAAZ2/EmWP' .
'AAgAAAUACIXmwgQDAEYwRAIgSh3Y4M7y0W8H0Vm/QxSSQsOlhJ32wmKCQB4hcHC6' .
'LjUCIA7/sXbG1k2R+nFa1gUJeX/gjAZ3WW1zn0hTdWYlvR+7MA0GCSqGSIb3DQEB' .
'CwUAA4IBgQAWnF+nbXO7tq9MfRZiFdRZCOnZU+r8lXQCt5G5qyYsKHERkn0yHIDI' .
's/80ZYsrDdHYWeGLKa27SOjF8ss/EqCrvpXo3jrwvNfw99MKz5M7grlW0D081zQX' .
'gkSMfNFlVNVOWSvc0Q32ycScljakrLr74yLIyFJb56ibcnt/3a/dwTlmZ8nyzgg70' .
'xgMnibONqmBOWiXT/TMXFkA8HNax4WMX6OyMvQB/CSfiF5E/gmc9kTfP6m1LnU55' .
'l7/7X16j7rC5RrZcED5cpoq6lwwqiCmoKCECpGUfR5B0jsL+buQiJj28vGr7YXif' .
'D/0IDY7qbjuZUsCJtXx0252v4RZJgotEy8QsEQp8flxh5O8cfNpPx0w4ByZZd/Y1' .
'NEEypvYwgR7J2mnWGXRwlLIQ1p4Vs86UePW1s05b7B6dSIWscMc0WQFx0MXmEBG3' .
'l+V1O5Ns40N7z9ZqTijAeApmLi85ToFhdJGvUs7VkuidzENXJttFRmqfWtTCrcno' .
'ARzn2PoiBo='
);

// ─── File-based logger (avoids IIS FastCGI STDERR mixing into response) ─────
// Tries api/ folder first; if IIS denies write access falls back to sys temp dir.
function cb_log($msg) {
    $line = date('Y-m-d H:i:s') . ' ' . $msg . "\n";

    // Primary: same folder as this file
    $primary = __DIR__ . '/canara_debug.log';
    if (@file_put_contents($primary, $line, FILE_APPEND | LOCK_EX) !== false) {
        return;
    }

    // Fallback: system temp directory (always writable by IIS app pool)
    $fallback = sys_get_temp_dir() . '/canara_debug.log';
    @file_put_contents($fallback, $line, FILE_APPEND | LOCK_EX);
}

// ─── Parse bank date/time — handles Indian formats strtotime() can't ────────
function cb_parse_bank_date($raw) {
    if (!$raw || !is_string($raw)) return '';
    $raw = trim($raw);
    if ($raw === '') return '';

    // Try strtotime first (handles ISO 8601, US formats, epoch)
    $ts = strtotime($raw);
    if ($ts && $ts > 946684800) { // after 2000-01-01
        return date('Y-m-d H:i:s', $ts);
    }

    // DD-MM-YYYY HH:MM:SS or DD/MM/YYYY HH:MM:SS (Indian bank format)
    if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})\s+(\d{1,2}):(\d{2}):(\d{2})/', $raw, $m)) {
        $dt = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $m[3], $m[2], $m[1], $m[4], $m[5], $m[6]);
        if (strtotime($dt)) return $dt;
    }

    // DD-MM-YYYY or DD/MM/YYYY (date only)
    if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $raw, $m)) {
        $dt = sprintf('%04d-%02d-%02d 00:00:00', $m[3], $m[2], $m[1]);
        if (strtotime($dt)) return $dt;
    }

    // Epoch milliseconds (13+ digits)
    if (preg_match('/^\d{13,}$/', $raw)) {
        return date('Y-m-d H:i:s', intval($raw) / 1000);
    }

    // Epoch seconds (10 digits)
    if (preg_match('/^\d{10}$/', $raw)) {
        return date('Y-m-d H:i:s', intval($raw));
    }

    cb_log("[cb_parse_bank_date] Could not parse: '$raw'");
    return '';
}

// ─── Extract bank timestamp from API response data ─────────────────────────
function cb_extract_bank_paid_at($data) {
    $bank_date_raw = $data['txnDate'] ?? $data['txnDateTime'] ?? $data['transactionDate']
                  ?? $data['paymentDate'] ?? $data['completedAt'] ?? $data['createdAt']
                  ?? $data['dateTime'] ?? $data['date'] ?? $data['paidAt']
                  ?? $data['txnTime'] ?? $data['transaction_date'] ?? '';
    if ($bank_date_raw) {
        cb_log("[cb_extract_bank_paid_at] Raw bank date field: '$bank_date_raw'");
    }
    return cb_parse_bank_date($bank_date_raw);
}

// ─── Base64url helpers ───────────────────────────────────────────────────────
function b64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
function b64url_decode($data) {
    $pad = strlen($data) % 4;
    if ($pad) $data .= str_repeat('=', 4 - $pad);
    return base64_decode(strtr($data, '-_', '+/'));
}

// ─── JWE  A256KW + A128CBC-HS256 ────────────────────────────────────────────
function cb_jwe_encrypt($plaintext) {
    $key = hex2bin(CB_SYMMETRIC_KEY);           // 32-byte key for AES-256-WRAP

    $cek = random_bytes(32);                    // 32-byte CEK for A128CBC-HS256
    $iv  = random_bytes(16);

    // Protected header
    $header    = json_encode(['alg' => 'A256KW', 'enc' => 'A128CBC-HS256'], JSON_UNESCAPED_SLASHES);
    $protected = b64url_encode($header);

    // Wrap CEK — AES Key Wrap (RFC 3394) requires the default IV passed explicitly
    $wrap_iv  = "\xA6\xA6\xA6\xA6\xA6\xA6\xA6\xA6";
    $enc_key  = openssl_encrypt($cek, 'aes-256-wrap', $key, OPENSSL_RAW_DATA, $wrap_iv);
    if ($enc_key === false) throw new Exception('JWE key wrap failed: ' . openssl_error_string());

    // A128CBC-HS256: MAC_KEY = first 16, ENC_KEY = last 16
    $mac_key    = substr($cek, 0, 16);
    $enc_k      = substr($cek, 16, 16);

    $ciphertext = openssl_encrypt($plaintext, 'AES-128-CBC', $enc_k, OPENSSL_RAW_DATA, $iv);
    if ($ciphertext === false) throw new Exception('JWE encrypt failed');

    // Authentication tag (RFC 7516 §5.2.2.1)
    $al         = pack('J', strlen($protected) * 8);
    $mac_input  = $protected . $iv . $ciphertext . $al;
    $tag        = substr(hash_hmac('sha256', $mac_input, $mac_key, true), 0, 16);

    return implode('.', [
        $protected,
        b64url_encode($enc_key),
        b64url_encode($iv),
        b64url_encode($ciphertext),
        b64url_encode($tag),
    ]);
}

function cb_jwe_decrypt($token) {
    $key   = hex2bin(CB_SYMMETRIC_KEY);
    $parts = explode('.', $token);
    if (count($parts) !== 5) throw new Exception('Invalid JWE compact token');

    [$protected, $enc_key_b64, $iv_b64, $ciphertext_b64, $tag_b64] = $parts;

    $enc_key    = b64url_decode($enc_key_b64);
    $iv         = b64url_decode($iv_b64);
    $ciphertext = b64url_decode($ciphertext_b64);
    $tag        = b64url_decode($tag_b64);

    $wrap_iv = "\xA6\xA6\xA6\xA6\xA6\xA6\xA6\xA6";
    $cek = openssl_decrypt($enc_key, 'aes-256-wrap', $key, OPENSSL_RAW_DATA, $wrap_iv);
    if ($cek === false) throw new Exception('JWE key unwrap failed');

    $mac_key = substr($cek, 0, 16);
    $enc_k   = substr($cek, 16, 16);

    $al         = pack('J', strlen($protected) * 8);
    $mac_input  = $protected . $iv . $ciphertext . $al;
    $expected   = substr(hash_hmac('sha256', $mac_input, $mac_key, true), 0, 16);

    if (!hash_equals($expected, $tag)) throw new Exception('JWE auth tag mismatch');

    $plaintext = openssl_decrypt($ciphertext, 'AES-128-CBC', $enc_k, OPENSSL_RAW_DATA, $iv);
    if ($plaintext === false) throw new Exception('JWE decryption failed');

    return $plaintext;
}

// ─── RSA signature (PKCS1v15 + SHA-256) ─────────────────────────────────────
function cb_sign($payload_str) {
    if (!file_exists(CB_PRIVATE_KEY)) {
        throw new Exception('Private key not found at ' . CB_PRIVATE_KEY);
    }
    $pem         = file_get_contents(CB_PRIVATE_KEY);
    $private_key = openssl_pkey_get_private($pem);
    if (!$private_key) throw new Exception('Cannot load private key');
    openssl_sign($payload_str, $signature, $private_key, OPENSSL_ALGO_SHA256);
    return base64_encode($signature);
}

// ─── Build request headers ───────────────────────────────────────────────────
function cb_headers($access_token, $signature) {
    return [
        'Authorization: Bearer '    . $access_token,
        'x-client-id: '            . CB_CLIENT_ID,
        'x-client-secret: '        . CB_CLIENT_SECRET,
        'x-client-certificate: '   . CB_PUBLIC_CERT,
        'x-api-interaction-id: '   . sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0,0xffff), mt_rand(0,0xffff), mt_rand(0,0xffff),
            mt_rand(0,0x0fff)|0x4000, mt_rand(0,0x3fff)|0x8000,
            mt_rand(0,0xffff), mt_rand(0,0xffff), mt_rand(0,0xffff)),
        'x-timestamp: '            . gmdate('Y-m-d\TH:i:s\Z'),
        'x-signature: '            . $signature,
        'x-forwarded-for: '        . CB_CLIENT_IP,
        'Content-Type: application/json',
        'Accept: application/json',
    ];
}

// ─── Generic Canara Bank API call ────────────────────────────────────────────
function cb_api_call($endpoint_path, $request_data) {
    $access_token = cb_get_valid_token();

    // Sign the plain payload
    $plain_payload  = json_encode(
        ['Request' => ['body' => ['encryptData' => $request_data]]],
        JSON_UNESCAPED_SLASHES
    );
    $signature = cb_sign($plain_payload);

    // Encrypt only the request_data
    $encrypted      = cb_jwe_encrypt(json_encode($request_data, JSON_UNESCAPED_SLASHES));
    $final_payload  = json_encode(
        ['Request' => ['body' => ['encryptData' => $encrypted]]],
        JSON_UNESCAPED_SLASHES
    );

    $url = CB_API_BASE . '/' . ltrim($endpoint_path, '/');

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $final_payload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => cb_headers($access_token, $signature),
    ]);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_err  = curl_error($ch);
    curl_close($ch);

    cb_log("[CanaraBank] $endpoint_path → HTTP $http_code | $response");

    if ($curl_err) throw new Exception("cURL error: $curl_err");
    if ($http_code !== 200) throw new Exception("API HTTP $http_code: $response");

    $resp_json = json_decode($response, true);

    // Decrypt response
    $encrypted_resp = $resp_json['Response']['body']['encryptData'] ?? null;
    if (!$encrypted_resp) throw new Exception("Missing encryptData in response");

    $decrypted = cb_jwe_decrypt($encrypted_resp);
    return json_decode($decrypted, true);
}

// ─── OAuth token management ──────────────────────────────────────────────────
function cb_save_tokens($data) {
    $tokens = [
        'access_token'      => $data['access_token']  ?? null,
        'refresh_token'     => $data['refresh_token'] ?? null,
        'expires_at'        => time() + intval($data['expires_in'] ?? 3600),
        'refresh_expires_at'=> time() + intval(
            $data['refresh_token_expires_in'] ?? $data['refresh_expires_in'] ?? 86400
        ),
        'created_at'        => time(),
    ];
    file_put_contents(CB_TOKEN_FILE, json_encode($tokens, JSON_PRETTY_PRINT));
    @chmod(CB_TOKEN_FILE, 0600);
}

function cb_load_tokens() {
    if (!file_exists(CB_TOKEN_FILE)) return null;
    return json_decode(file_get_contents(CB_TOKEN_FILE), true);
}

function cb_basic_auth() {
    return 'Basic ' . base64_encode(CB_CLIENT_ID . ':' . CB_CLIENT_SECRET);
}

function cb_refresh_token() {
    $tokens = cb_load_tokens();
    if (empty($tokens['refresh_token'])) throw new Exception('No refresh token stored');

    $ch = curl_init(CB_REFRESH_URL);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query([
            'grant_type'    => 'refresh_token',
            'refresh_token' => $tokens['refresh_token'],
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => [
            'Authorization: ' . cb_basic_auth(),
            'Content-Type: application/x-www-form-urlencoded',
        ],
    ]);
    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) throw new Exception("Token refresh failed HTTP $http_code: $response");
    $data = json_decode($response, true);
    cb_save_tokens($data);
    return $data['access_token'];
}

function cb_get_valid_token() {
    $tokens = cb_load_tokens();
    if (!$tokens || empty($tokens['access_token'])) {
        throw new Exception('No Canara Bank access token. Complete OAuth first.');
    }
    // Refresh 2 min before expiry
    if (time() >= ($tokens['expires_at'] - 120)) {
        return cb_refresh_token();
    }
    return $tokens['access_token'];
}

// ─── Generate unique ext_transaction_id ─────────────────────────────────────
function cb_unique_ext_id($connection) {
    do {
        $ext_id = 'EXT' . date('YmdHis') . sprintf('%06d', random_int(0, 999999));
        $row = mysqli_fetch_assoc(mysqli_query($connection,
            "SELECT id FROM canara_transactions WHERE ext_transaction_id='$ext_id'"));
    } while ($row);
    return $ext_id;
}

// ─── DB helpers ─────────────────────────────────────────────────────────────
function cb_ensure_table($connection) {
    // Suppress PHP 8.1+ mysqli exceptions for DDL statements
    mysqli_report(MYSQLI_REPORT_OFF);

    mysqli_query($connection, "
        CREATE TABLE IF NOT EXISTS `canara_transactions` (
            `id`                   INT AUTO_INCREMENT PRIMARY KEY,
            `ext_transaction_id`   VARCHAR(50)  NOT NULL,
            `jobcard_no`           VARCHAR(50)  DEFAULT NULL,
            `amount`               DECIMAL(10,2) NOT NULL,
            `qr_string`            TEXT,
            `status`               VARCHAR(20)  DEFAULT 'PENDING',
            `customer_name`        VARCHAR(100) DEFAULT NULL,
            `rrn`                  VARCHAR(50)  DEFAULT NULL,
            `txn_id`               VARCHAR(100) DEFAULT NULL,
            `customer_vpa`         VARCHAR(100) DEFAULT NULL,
            `created_at`           DATETIME     NOT NULL,
            `updated_at`           DATETIME     NOT NULL,
            `paid_at`              DATETIME     DEFAULT NULL,
            UNIQUE KEY `uq_ext` (`ext_transaction_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // Add customer_name column if table existed before this column was added
    $col_check = mysqli_query($connection,
        "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
          WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME   = 'canara_transactions'
            AND COLUMN_NAME  = 'customer_name' LIMIT 1");
    if ($col_check && mysqli_num_rows($col_check) === 0) {
        mysqli_query($connection,
            "ALTER TABLE `canara_transactions`
             ADD COLUMN `customer_name` VARCHAR(100) DEFAULT NULL AFTER `status`");
    }

    // Add payment_source column ('JC', 'SQ', or NULL for advance)
    $src_check = mysqli_query($connection,
        "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
          WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME   = 'canara_transactions'
            AND COLUMN_NAME  = 'payment_source' LIMIT 1");
    if ($src_check && mysqli_num_rows($src_check) === 0) {
        mysqli_query($connection,
            "ALTER TABLE `canara_transactions`
             ADD COLUMN `payment_source` VARCHAR(10) DEFAULT NULL AFTER `jobcard_no`");
    }

    // Add payment_change_reason column — stores reason when staff switches online→cash
    $pcr_check = mysqli_query($connection,
        "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
          WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME   = 'canara_transactions'
            AND COLUMN_NAME  = 'payment_change_reason' LIMIT 1");
    if ($pcr_check && mysqli_num_rows($pcr_check) === 0) {
        mysqli_query($connection,
            "ALTER TABLE `canara_transactions`
             ADD COLUMN `payment_change_reason` VARCHAR(500) DEFAULT NULL");
    }
}

function cb_insert_transaction($connection, $ext_id, $amount, $jobcard_no = null) {
    $now  = date('Y-m-d H:i:s');
    $amt  = floatval($amount);
    $jc   = $jobcard_no ? "'" . mysqli_real_escape_string($connection, $jobcard_no) . "'" : 'NULL';
    $safe = mysqli_real_escape_string($connection, $ext_id);
    mysqli_query($connection,
        "INSERT INTO canara_transactions
            (ext_transaction_id, jobcard_no, amount, status, created_at, updated_at)
         VALUES ('$safe', $jc, $amt, 'PENDING', '$now', '$now')"
    );
}

function cb_update_transaction($connection, $ext_id, $fields) {
    $parts = [];
    foreach ($fields as $col => $val) {
        $safe_col = preg_replace('/[^a-z_]/', '', $col);
        $safe_val = mysqli_real_escape_string($connection, $val ?? '');
        $parts[]  = "`$safe_col`='$safe_val'";
    }
    $parts[] = "updated_at='" . date('Y-m-d H:i:s') . "'";
    $set     = implode(', ', $parts);
    $safe_id = mysqli_real_escape_string($connection, $ext_id);
    mysqli_query($connection,
        "UPDATE canara_transactions SET $set WHERE ext_transaction_id='$safe_id'"
    );
}

function cb_get_transaction($connection, $ext_id) {
    $safe = mysqli_real_escape_string($connection, $ext_id);
    $res  = mysqli_query($connection,
        "SELECT * FROM canara_transactions WHERE ext_transaction_id='$safe' LIMIT 1");
    return $res ? mysqli_fetch_assoc($res) : null;
}
