<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    protected $token;
    protected $baseUrl;

    public function __construct()
    {
        $this->token   = config('services.fonnte.token');
        $this->baseUrl = 'https://api.fonnte.com/send';
    }

    public function sendMessage(string $phone, string $message): bool
    {
        if (!$this->token) return false;

        $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])
            ->asForm()
            ->post($this->baseUrl, [
                'target'  => $phone,
                'message' => $message,
            ]);

        return $response->successful();
    }
}
