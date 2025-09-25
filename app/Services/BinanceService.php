<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class BinanceService
{
    private $baseUrl = 'https://p2p.binance.com/bapi/c2c/v2/friendly/c2c/adv/search';

    public function getP2PRates($type = 'buy', $rows = 10)
    {
        try {
            $requestData = [
                'proMerchantAds' => false,
                'page' => 1,
                'rows' => $rows,
                'payTypes' => [], // Todos los métodos de pago
                'publisherType' => null,
                'fiat' => 'VES',
                'tradeType' => strtoupper($type),
                'asset' => 'USDT',
                'countries' => ['VE'],
                'transAmount' => ''
            ];

            $response = Http::timeout(15)
                ->retry(3, 1000)
                ->post($this->baseUrl, $requestData);

            if (!$response->successful()) {
                throw new Exception("Binance API error: " . $response->status());
            }

            $data = $response->json();

            return $this->processP2PData($data['data'] ?? []);
        } catch (Exception $e) {
            Log::error('Error fetching Binance P2P rates: ' . $e->getMessage());
            return null;
        }
    }

    private function processP2PData($data)
    {
        if (empty($data)) {
            return null;
        }

        $prices = [];
        $totalAvailable = 0;

        foreach ($data as $offer) {
            $price = floatval($offer['adv']['price']);
            $available = floatval($offer['adv']['tradableQuantity']);

            $prices[] = $price;
            $totalAvailable += $available;
        }

        // Precio promedio ponderado por disponibilidad
        $weightedAvg = $this->calculateWeightedAverage($data);

        return [
            'min_price' => min($prices),
            'max_price' => max($prices),
            'avg_price' => array_sum($prices) / count($prices),
            'weighted_avg' => $weightedAvg,
            'total_offers' => count($data),
            'total_available' => $totalAvailable,
            'offers' => array_slice($data, 0, 5) // Primeras 5 ofertas
        ];
    }

    private function calculateWeightedAverage($offers)
    {
        $totalValue = 0;
        $totalWeight = 0;

        foreach ($offers as $offer) {
            $price = floatval($offer['adv']['price']);
            $available = floatval($offer['adv']['tradableQuantity']);

            $totalValue += $price * $available;
            $totalWeight += $available;
        }

        return $totalWeight > 0 ? $totalValue / $totalWeight : 0;
    }
}
