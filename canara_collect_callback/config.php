<?php

/**
 * Canara Bank Configuration
 */

return [
    // Encryption Key (32 bytes - CHANGE THIS!)
    'masterKey' => '12345678901234567890123456789012',
    
    // Merchant Details
    'merchantId' => 'YOUTUBE001',
    'terminalId' => 'terma1',
    'subMerchantId' => 'sida1',
    
    // OAuth Configuration (UPDATE THESE!)
    'oauth' => [
        'clientId' => 'IoxKv7ZPwy0ei6vTv97X6jssU4lJ3dDH',
        'clientSecret' => 'uGTfDcXDY1V40y2wr1DB4y0LlJVdysbZ',
        'redirectUri' => 'https://citizenprintz.in/storefront/canara_collect_callback/oauth-callback.php',  // Update if different
        'scope' => 'van'  // Get from Canara Bank
    ],
    
    // API URLs
    'apiUrl' => [
        'uat' => 'https://uat-apibanking.canarabank.in',
        'production' => 'https://apibanking.canarabank.in'
    ],
    
    // Environment:  'uat' or 'production'
    'environment' => 'uat',
    
    // Token storage file
    'tokenFile' => 'canara_tokens.json'
];