<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShippingController extends Controller
{
    public function cekOngkir(Request $request)
    {
        $data = $request->validate([
            'destination' => 'required',            
            'weight'      => 'required|integer|min:1', 
            'courier'     => 'required|string',       
        ]);

        $courier = strtolower($data['courier']);

        $baseUrl = rtrim(config('rajaongkir.base_url'), '/'); 
        $apiKey  = config('rajaongkir.key');
        $origin  = config('rajaongkir.origin');

        if (!$baseUrl || !$apiKey || !$origin) {
            return response()->json([
                'success' => false,
                'message' => 'Config RajaOngkir belum lengkap. Cek RAJAONGKIR_KEY / BASE_URL / ORIGIN di .env',
            ], 200);
        }

        try {
            
            $response = Http::asForm()
                ->withHeaders([
                    'Accept' => 'application/json',
                    'key'    => $apiKey,
                ])
                ->post($baseUrl . '/calculate/domestic-cost', [
                    'origin'      => (int) $origin,
                    'destination' => (int) $data['destination'],
                    'weight'      => (int) $data['weight'],
                    'courier'     => $courier,
                    'price'       => 'lowest', 
                ]);

            $raw = $response->json();

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'API RajaOngkir error HTTP ' . $response->status(),
                    'raw'     => $raw,
                ], 200);
            }

            $metaStatus = $raw['meta']['status'] ?? null;
            $items = $raw['data'] ?? [];

            if ($metaStatus !== 'success' || empty($items)) {
                return response()->json([
                    'success' => false,
                    'message' => $raw['meta']['message'] ?? 'Tarif ongkir tidak ditemukan.',
                    'raw'     => $raw,
                ], 200);
            }

            $costs = [];
            foreach ($items as $item) {
                $costs[] = [
                    'service'     => $item['service'] ?? 'SERVICE',
                    'description' => $item['description'] ?? '',
                    'cost'        => [[
                        'value' => (int) ($item['cost'] ?? 0),
                        'etd'   => (string) ($item['etd'] ?? ''),
                        'note'  => '',
                    ]],
                ];
            }

            return response()->json([
                'success' => true,
                'costs'   => $costs,
                'raw'     => null,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
            ], 200);
        }
    }
}
