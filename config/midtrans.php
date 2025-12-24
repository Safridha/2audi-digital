<?php

return [

    'merchant_id'    => env('MIDTRANS_MERCHANT_ID'),
    'client_key'     => env('MIDTRANS_CLIENT_KEY'),
    'server_key'     => env('MIDTRANS_SERVER_KEY'),

    // false = sandbox, true = production
    'is_production'  => env('MIDTRANS_IS_PRODUCTION', false),

    // sanitize input sebelum dikirim ke Midtrans
    'is_sanitized'   => env('MIDTRANS_SANITIZE', true),

    // 3DS untuk kartu kredit
    'is_3ds'         => env('MIDTRANS_3DS', true),
];
