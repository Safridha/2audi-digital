<?php

return [

    // API Key dari RajaOngkir Komerce
    'key' => env('RAJAONGKIR_KEY', ''),

    // Base URL Komerce
    'base_url' => env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),

    // Origin = district_id asal toko (kecamatan)
    'origin' => env('RAJAONGKIR_ORIGIN', null),

];
