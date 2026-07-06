<?php 
require_once 'Jose/src/Component/Encryption/Algorithm/KeyEncryption/AESKW.php';
 
require_once 'Jose/src/Component/Encryption/Algorithm/KeyEncryption/A256KW.php';
 require_once 'Jose/src/Component/Encryption/Algorithm/ContentEncryption/AESCBCHS.php'; 
 
require_once 'Jose/src/Component/Encryption/Algorithm/ContentEncryption/A256CBCHS512.php'; // Example of another class you may need

require_once 'Jose/src/Component/Encryption/JWEBuilder.php';
require_once 'Jose/src/Component/Encryption/JWEDecrypter.php';

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Encryption\Algorithm\KeyEncryption\A256KW;
use Jose\Component\Encryption\Algorithm\ContentEncryption\A256CBCHS512;
use Jose\Component\Encryption\Algorithm\ContentEncryption\A128CBCHS256;
use Jose\Component\Encryption\Compression\CompressionMethodManager;
use Jose\Component\Encryption\Compression\Deflate;
use Jose\Component\Encryption\JWEBuilder;
use Jose\Component\Encryption\JWEDecrypter;
use Jose\Component\Encryption\Serializer\JWESerializerManager;
use Jose\Component\Encryption\Serializer\CompactSerializer;
use Jose\Component\KeyManagement\JWKFactory;


// The key encryption algorithm manager with the A256KW algorithm.
$keyEncryptionAlgorithmManager = new AlgorithmManager([
    new A256KW(),
]);

// The content encryption algorithm manager with the A256CBC-HS256 algorithm.
$contentEncryptionAlgorithmManager = new AlgorithmManager([
    new A128CBCHS256(),
]);

// The compression method manager with the DEF (Deflate) method.
$compressionMethodManager = new CompressionMethodManager([
    new Deflate(),
]);

// We instantiate our JWE Builder.
$jweBuilder = new JWEBuilder(
    $keyEncryptionAlgorithmManager,
    $contentEncryptionAlgorithmManager,
    $compressionMethodManager
);

use Jose\Component\Core\JWK;

  /** COMMOM CODE (Creates JWK Key from shared string) */

/**
 * Utility function that converts a given shared key into JWK Key. 
 * @return JWK
 */
function createKey()
{
    $sharedKey = hex2bin('2457bf1dc01659e2cd0af14dced615376aaab76f40c0e89d641421f807112b84');

    $jwk = JWKFactory::createFromSecret(
        $sharedKey,
        // The shared secret
        [
            'alg' => 'A256KW',
            'use' => 'enc'
        ]
    );

    return $jwk;
}



// The payload we want to encrypt. It MUST be a string.
$payload = '{
                "Authorization": "Basic U1lFREFQSUFVVEg6MmE4OGE5MWE5MmE2NGEx",
                "txnPassword": "2a88a91a92a64a1a1a3",
                "srcAcctNumber": "2774201000198",
                "destAcctNumber": "9833111000032",
                "customerID": "13961989",
                "txnAmount": "1",
                "benefName": "test"
            }';

/** ENCRYPTION  CODE */

/**
 * Encrypts a given payload and returns the encrypted token
 * @param mixed $payload Input to be encrypted
 * @return string Encrypted token
 */
function encrypt($payload) : string
{
    $jwe = $GLOBALS['jweBuilder']
        ->create() // We want to create a new JWE
        ->withPayload($payload) // We set the payload
        ->withSharedProtectedHeader([
            'alg' => 'A256KW',
            // Key Encryption Algorithm
            'enc' => 'A128CBC-HS256',
            // Content Encryption Algorithm
            'zip' => 'DEF' // We enable the compression (irrelevant as the payload is small, just for the example).
        ])
        ->addRecipient(createKey()) // We add a recipient (a shared key or public key).
        ->build(); // We build it


    $serializer = new CompactSerializer(); // The serializer

    $token = $serializer->serialize($jwe, 0); // We serialize the recipient at index 0 (we only have one recipient).

    print("Encrypted string: \n");

    print($token);
    
    return $token;
}



/** DECRYPTION CODE */
/**
 * Decrypts and prints a encrypted string
 * @return void
 */
function decrypt($encrypted_token) : string
{

    $jweDecrypter = new JWEDecrypter(
        $GLOBALS['keyEncryptionAlgorithmManager'],
        $GLOBALS['contentEncryptionAlgorithmManager'],
        $GLOBALS['compressionMethodManager']
    );

    $serializerManager = new JWESerializerManager([
        new CompactSerializer(),
    ]);

    $jwe = $serializerManager->unserialize($encrypted_token);

    $success = $jweDecrypter->decryptUsingKey($jwe, createKey(), 0);

    if ($success) {
        echo "\nSuccessfully Decrypted";
    } else {
        echo "Error while decrypting";
    }

    $plaintext = $jwe->getPayload();

    echo "\nOutput: \n" . $plaintext ."\n";
    
    return $plaintext;
}

/**
 *  Creates a digital signature for the input message from a given private key
 * @param mixed $message input message to be signed
 * @return string signature encoded as base64
 */
function sign($message) : string {
    $privateKey = openssl_pkey_get_private(file_get_contents('private.pem'));

    openssl_sign($message, $signature, $privateKey, "sha256WithRSAEncryption");
    
    $encoded = base64_encode($signature);
    return $encoded;    
}

/**
 * 
 * Verifies if the given signature is indeed valid.
 * 
 * @param mixed $message Original message for which the signature is created.
 * @param mixed $messageSignature Signature itself
 * @return void
 */
function verify($message, $messageSignature): bool{

    $signature = base64_decode($messageSignature);

    $publicKey = openssl_pkey_get_public(file_get_contents("public.pem"));

    $result = openssl_verify($message, $signature, $publicKey, "sha256WithRSAEncryption");

    if ($result) {
        echo "\nSignature is valid";
        return true;
    } else {
        echo "\nSignature is not valid";
        return false;
    }
}

/** call encrypt function */
encrypt($payload);

$encrypted_token = 'eyJhbGciOiJBMjU2S1ciLCJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiemlwIjoiREVGIn0.KmMXDJ40xqMpAvmAADWJtXeCruTjGLc-bLoJAc4_XVbtjK4idzrqWw.WFwMBlqKkJT5cTuyqH8W5A.3QgodhVHJRnwvxZtDuzVjRi_t_LEq4Qj0smNuT8fAXohtz8i-pPq8fWsMHsEdlCEhe-nR3sAmGNYH5SjLurY4d7bn9b6jBVLdD0tsQKae8ZSl0dfsQO5drTEKojfWOIjoQPuYOgqawtqP6d856i0avfFIDnmWMVJ5yBpWOvAIkKp_h6hPAy0EcjfSCjbffD5ffmwELUgCYUvpnDafCyfw9qx-WM9bbXmYUHZoHyo3whHZ3kKkmfgc_qsTrA0cts7.3h8famePuEbidItX8g0VLg';

/** call decrypt function */
decrypt($encrypted_token);

print(sign('hello'));
verify('hello', 'V/PX/0oa2yvHc6c5Jfp8FwvX6GcWsv2JcALIGvEv3kr9ODd2s/VrCm9dSvHGzwYb/ONTYwDL5IK92vD3Op5ZBa6mFuCqkhPpaA/81ZDUHPScz6PUxk3mzI0eUc+XroA9dO+/2iSI80amCg+/KFeljRX2dewu2n7XepyJxkecQfGzLCiyTg81IgrVOzk7mrgQrkFZtFS3JHrxKTFQaNQmVLFNMuiCNylNXVFAolzqAKx9evk5733pnmWHgWUknKBq1bQ7iTapI2Rxcm6pnWAvxCMC5SjN372TbUDFL8D2nNbD2fQzXMtEY76SqkDm+fzmuJQbf7Bg+vyFOZ3FaR02YQ==');
?>