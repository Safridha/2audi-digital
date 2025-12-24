<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class RegionController extends Controller
{
    private function base(): string
    {
        return rtrim(config('rajaongkir.base_url'), '/');
    }

    private function headers(): array
    {
        return [
            'Accept' => 'application/json',
            'key'    => config('rajaongkir.key'),
        ];
    }

    public function provinces()
    {
        $res = Http::withOptions(['verify' => false])
            ->withHeaders($this->headers())
            ->get($this->base() . '/destination/province');

        if (! $res->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil provinsi (HTTP ' . $res->status() . ')',
                'data'    => [],
            ], 200);
        }

        return response()->json([
            'success' => true,
            'data'    => $res->json()['data'] ?? [],
        ]);
    }

    public function cities($provinceId)
    {
        $res = Http::withOptions(['verify' => false])
            ->withHeaders($this->headers())
            ->get($this->base() . '/destination/city/' . $provinceId);

        if (! $res->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kota (HTTP ' . $res->status() . ')',
                'data'    => [],
            ], 200);
        }

        return response()->json([
            'success' => true,
            'data'    => $res->json()['data'] ?? [],
        ]);
    }

    public function districts($cityId) 
    {
        $res = Http::withOptions(['verify' => false])
            ->withHeaders($this->headers())
            ->get($this->base() . '/destination/district/' . $cityId);

        if (! $res->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kecamatan (HTTP ' . $res->status() . ')',
                'data'    => [],
            ], 200);
        }

        return response()->json([
            'success' => true,
            'data'    => $res->json()['data'] ?? [],
        ]);
    }
}
