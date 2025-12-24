<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        // Ambil konfigurasi dari .env
        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
        // SSL / CA diatur di php.ini (curl.cainfo & openssl.cafile)
    }

    public function createTransaction(array $params)
    {
        return Snap::createTransaction($params);
    }
}
