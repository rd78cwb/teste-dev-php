<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrasilApiService
{
    protected $baseUrl = 'https://brasilapi.com.br/api';

    public function getCnpjData(string $cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj); // Sanitize CNPJ

        if (strlen($cnpj) !== 14) {
            return null;
        }

        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/cnpj/v1/{$cnpj}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('BrasilAPI CNPJ lookup failed: ' . $response->status(), [
                'cnpj' => $cnpj,
                'response_body' => $response->body()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Exception during BrasilAPI CNPJ lookup: ' . $e->getMessage(), ['cnpj' => $cnpj]);
            return null;
        }
    }
}