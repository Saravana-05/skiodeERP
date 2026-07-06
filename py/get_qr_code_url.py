import base64
import requests
from jose import jwe
from jose.constants import ALGORITHMS
from cryptography.hazmat.primitives import hashes
from cryptography.hazmat.primitives import serialization
from cryptography.hazmat.primitives.asymmetric import padding
import time
import base64
import json


def decrypt(input_data, shared_symmetric_key):
    # print("String to decrypt:", input_data)
    decrypted = jwe.decrypt(input_data, shared_symmetric_key
    )
    return decrypted.decode('utf-8')


def encrypt(input_text, shared_symmetric_key):
    encrypted = jwe.encrypt(
        input_text,
        key=shared_symmetric_key,
        encryption=ALGORITHMS.A128CBC_HS256,
        algorithm=ALGORITHMS.A256KW
    )
    return encrypted
    
    
def sign(input_data):
    real_pk = b'-----BEGIN PRIVATE KEY-----\n...........................\n-----END PRIVATE KEY-----'
    private_key = serialization.load_pem_private_key(
        real_pk,
        password=None,
        backend=None
    )

    signature = private_key.sign(
        input_data,
        padding.PKCS1v15(),
        hashes.SHA256()
    )

    return base64.b64encode(signature).decode('utf-8')

client_id = "client_id"
client_secret = "client_secret"
public_key = "MIIE+jCCA+KgAwIBAgISBH14NM+Q4GN+t+JuMyp5auADMA0GCSqGSIb3DQEB........" #Remove whitespaces and -----BEGIN CERTIFICATE-----, -----END CERTIFICATE-----
auth = "Basic U1lFREFQSUFVVEg6MmE4OGE5MWE5MmE2NGEx"

encrypt_data = {
"amount": "5.00",
"extTransactionId": "NPSTPAY2255122021SHIV",
"channel": "api",
"remark": "QR SIT testing",
"source": "citizenprintz",
"terminalId": "CP1",
"type": "D",
"param3": "param3",
"Param2": "param2",
"param1": "param1",
"sid": "CP12345678",
"upiId": "250242045001739@cnrb",
"requestTime": "2025-12-03 18:26:00",
"reciept": "https://citizenprintz.in",
"checksum": ""    
    }

payload = {
    "Request": {
        "body": {
            "srcAccountDetails": {
                "currency": "INR",
                "branchCode": "1228"
                },
            "destAccountDetails": {
                "currency": "INR"
                },
            "txnCurrency": "INR",
            "ifscCode": "SBIN0009995",
            "narration": "nrtv",
            "valueDate": "28-04-2023",
            "encryptData": encrypt_data
        }
    }
}

payload_json = json.dumps(payload, separators=(',', ':')).encode('utf-8')

signature = sign(payload_json)

headers = {
    'x-client-id': client_id,
    'x-client-secret': client_secret,
    'x-client-certificate': public_key,
    'x-api-interaction-id': '1',
    'x-timestamp': str(time.time()),
    'x-signature': signature,
    'Content-Type': 'application/json'
}

print("headers", headers)
    