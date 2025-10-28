<?php

return [
    'tmn_code'      => env('VNP_TMN_CODE'),
    'hash_secret'   => env('VNP_HASH_SECRET'),
    'payment_url'   => env('VNP_URL'),
    'return_url'    => env('VNP_RETURN_URL'),
    'ipn_url'       => env('VNP_IPN_URL'),
    'version'       => env('VNP_VERSION', '2.1.0'),
    'command'       => env('VNP_COMMAND', 'pay'),
    'currency_code' => env('VNP_CURRENCY', 'VND'),
];
