<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],
    'apple' => [
        'client_id' => env('APPLE_CLIENT_ID'),
        'client_secret' => env('APPLE_CLIENT_SECRET'),
        'redirect' => env('APPLE_REDIRECT_URI'),
    ],

    'api' => [
        'key' => env('API_KEY'),
    ],

    'mailtrap' => [
        'api_key' => env('MAIL_PASSWORD'),
    ],
    'doku' => [
        'client_id' => env('DOKU_CLIENT_ID'),
        'secret_key' => env('DOKU_SECRET_KEY'),
        'private_key' => env('DOKU_PRIVATE_KEY'),
        'sandbox_url' => env('DOKU_SANDBOX_URL', 'https://api-sandbox.doku.com'),
        'prod_url' => env('DOKU_PROD_URL', 'https://api-sandbox.doku.com'),
        'api_url' => env('DOKU_URL', 'https://api-sandbox.doku.com'),
        // QRIS configuration
        'qris' => [
            'merchant_id' => env('DOKU_QRIS_MERCHANT_ID'),
        ],
        // Bank-specific partner service IDs (DGPC codes)
        'banks' => [
            'BTN' => [
                'dgpc' => env('DOKU_BTN_DGPC', '959626'),
                'channel' => 'VIRTUAL_ACCOUNT_BTN'
            ],
            'CIMB' => [
                'dgpc' => env('DOKU_CIMB_DGPC', '18990'),
                'channel' => 'VIRTUAL_ACCOUNT_BANK_CIMB'
            ],
            'DANAMON' => [
                'dgpc' => env('DOKU_DANAMON_DGPC', '89226'),
                'channel' => 'VIRTUAL_ACCOUNT_BANK_DANAMON'
            ],
            'BNC' => [
                'dgpc' => env('DOKU_BNC_DGPC', '903415370'),
                'channel' => 'VIRTUAL_ACCOUNT_BNC'
            ],
            'BNI' => [
                'dgpc' => env('DOKU_BNI_DGPC', '84923'),
                'channel' => 'VIRTUAL_ACCOUNT_BNI'
            ],
            'BRI' => [
                'dgpc' => env('DOKU_BRI_DGPC', '139256'),
                'channel' => 'VIRTUAL_ACCOUNT_BRI'
            ],
            'MANDIRI' => [
                'dgpc' => env('DOKU_MANDIRI_DGPC', '861880'),
                'channel' => 'VIRTUAL_ACCOUNT_BANK_MANDIRI'
            ],
            'BSI' => [
                'dgpc' => env('DOKU_BSI_DGPC', '202020'),
                'channel' => 'VIRTUAL_ACCOUNT_BSI'
            ],
            'MAYBANK' => [
                'dgpc' => env('DOKU_MAYBANK_DGPC', '786765'),
                'channel' => 'VIRTUAL_ACCOUNT_MAYBANK'
            ],
            'PERMATA' => [
                'dgpc' => env('DOKU_PERMATA_DGPC', '88566'),
                'channel' => 'VIRTUAL_ACCOUNT_BANK_PERMATA'
            ],  
        ],
        'qris' => [
            'merchant_id' => env('DOKU_QRIS_MERCHANT_ID', '15551'),
            'channel' => 'QRIS',
        ],
    ],

];
