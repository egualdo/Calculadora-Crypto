<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class DolarApiService
{
    private $baseUrl = 'https://ve.dolarapi.com/v1/dolares';

    public function getDollarRates()
    {
        try {
            $response = Http::withoutVerifying()->timeout(10)
                ->retry(3, 1000)
                ->get($this->baseUrl);

            if (!$response->successful()) {
                throw new Exception("DolarAPI error: " . $response->status());
            }

            $rates = $response->json();

            return $this->processDollarRates($rates);
        } catch (Exception $e) {
            Log::error('Error fetching dollar rates: ' . $e->getMessage());
            return null;
        }
    }

    private function processDollarRates($rates)
    {
        $processed = [];
        foreach ($rates as $rate) {
            $key = $this->getRateKey($rate['nombre']);
            if ($key) {
                $processed[$key] = [
                    'buy' => floatval($rate['promedio']),
                    'sell' => floatval($rate['promedio']),
                    'average' => floatval($rate['promedio']),
                    'name' => $rate['nombre'],
                    'last_updated' => $rate['fechaActualizacion']
                ];
            }
        }

        return $processed;
    }

    private function getRateKey($casa)
    {

        $mapping = [
            'oficial' => 'official',
            // 'blue' => 'blue',
            'bolsa' => 'stock',
            'contado con liqui' => 'ccl',
            'euro' => 'euro'
        ];

        return $mapping[strtolower($casa)] ?? null;
    }
}
