<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Cache;

class ExchangeController extends Controller
{
    public function dashboard()
    {
        $rates = Cache::remember('latest_exchange_rates', 600, function () {
            return ExchangeRate::latestRates()->get()->keyBy('type');
        });

        // Calcular el promedio entre dólar oficial y Binance P2P venta
        $averageDollarRate = $this->calculateAverageDollarRate($rates);

        return view('dashboard', compact('rates', 'averageDollarRate'));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'from_currency' => 'required|in:USDT,USD,VES,EUR',
            'to_currency' => 'required|in:USDT,USD,VES,EUR',
            'rate_type' => 'required|in:p2p_buy,p2p_sell,official,euro'
        ]);

        $rates = ExchangeRate::latestRates()->get()->keyBy('type');
        $rate = $rates[$request->rate_type] ?? null;

        if (!$rate) {
            return back()->withErrors(['rate_type' => 'Tipo de tasa no disponible']);
        }

        $result = $this->convertCurrency(
            $request->amount,
            $request->from_currency,
            $request->to_currency,
            $rate->average_price
        );

        // Calcular el promedio entre dólar oficial y Binance P2P venta
        $averageDollarRate = $this->calculateAverageDollarRate($rates);

        return view('dashboard', [
            'rates' => $rates,
            'calculation' => $result,
            'formData' => $request->all(),
            'averageDollarRate' => $averageDollarRate
        ]);
    }

    private function convertCurrency($amount, $from, $to, $rate)
    {
        // Conversión base: USDT/USD a VES
        if ($from === 'VES' && $to === 'USDT') {
            return $amount / $rate; // VES to USDT
        } elseif ($from === 'USDT' && $to === 'VES') {
            return $amount * $rate; // USDT to VES
        } elseif ($from === 'VES' && $to === 'USD') {
            return $amount / $rate; // VES to USD
        } elseif ($from === 'USD' && $to === 'VES') {
            return $amount * $rate; // USD to VES
        } elseif ($from === 'VES' && $to === 'EUR') {
            return $amount / $rate; // VES to EUR (rate = VES per EUR)
        } elseif ($from === 'EUR' && $to === 'VES') {
            return $amount * $rate; // EUR to VES
        } elseif ($from === 'USDT' && $to === 'USD') {
            // Asumimos 1:1 para simplificar, podrías agregar tasa USDT/USD
            return $amount;
        } elseif ($from === 'USD' && $to === 'USDT') {
            return $amount;
        }

        return $amount;
    }

    private function calculateAverageDollarRate($rates)
    {
        $officialRate = $rates['official'] ?? null;
        $p2pSellRate = $rates['p2p_sell'] ?? null;

        if (!$officialRate || !$p2pSellRate) {
            return null;
        }

        $average = ($officialRate->average_price + $p2pSellRate->average_price) / 2;

        return [
            'average_price' => round($average, 2),
            'official_rate' => $officialRate->average_price,
            'p2p_sell_rate' => $p2pSellRate->average_price,
            'last_updated' => max($officialRate->last_updated, $p2pSellRate->last_updated)
        ];
    }

    public function getRatesApi()
    {
        $rates = ExchangeRate::latestRates()->get()->keyBy('type');
        return response()->json($rates);
    }

    public function getHistoricalRatesApi()
    {
        // Obtener datos de los últimos 2 meses
        $twoMonthsAgo = now()->subMonths(2);

        $historicalData = ExchangeRate::where('created_at', '>=', $twoMonthsAgo)
            ->whereIn('type', ['official', 'p2p_sell'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(['type', function ($item) {
                return $item->created_at->format('Y-m-d');
            }]);

        // Procesar datos para Chart.js
        $processedData = [];
        $labels = [];

        // Obtener todas las fechas únicas
        $allDates = collect();
        foreach ($historicalData as $type => $typeData) {
            foreach ($typeData as $date => $dayData) {
                $allDates->push($date);
            }
        }
        $allDates = $allDates->unique()->sort()->values();

        // Crear estructura de datos para el gráfico
        $datasets = [];
        $colors = [
            'official' => ['rgb(59, 130, 246)', 'rgba(59, 130, 246, 0.1)'], // Azul
            'p2p_sell' => ['rgb(34, 197, 94)', 'rgba(34, 197, 94, 0.1)'] // Verde
        ];

        foreach (['official', 'p2p_sell'] as $type) {
            $data = [];
            $typeData = $historicalData[$type] ?? collect();

            foreach ($allDates as $date) {
                if (isset($typeData[$date])) {
                    // Tomar el último registro del día (promedio de precios del día)
                    $dayAverage = $typeData[$date]->avg('average_price');
                    $data[] = round($dayAverage, 2);
                } else {
                    $data[] = null; // No hay datos para este día
                }
            }

            $datasets[] = [
                'label' => $type === 'official' ? 'Dólar Oficial' : 'USDT P2P Venta',
                'data' => $data,
                'borderColor' => $colors[$type][0],
                'backgroundColor' => $colors[$type][1],
                'fill' => false,
                'tension' => 0.1,
                'pointRadius' => 2,
                'pointHoverRadius' => 5
            ];
        }

        // Crear dataset para el promedio
        $averageData = [];
        foreach ($allDates as $date) {
            $officialData = $historicalData['official'][$date] ?? collect();
            $p2pData = $historicalData['p2p_sell'][$date] ?? collect();

            if ($officialData->isNotEmpty() && $p2pData->isNotEmpty()) {
                $officialAvg = $officialData->avg('average_price');
                $p2pAvg = $p2pData->avg('average_price');
                $averageData[] = round(($officialAvg + $p2pAvg) / 2, 2);
            } else {
                $averageData[] = null;
            }
        }

        $datasets[] = [
            'label' => 'Promedio Dólar',
            'data' => $averageData,
            'borderColor' => 'rgb(168, 85, 247)', // Púrpura
            'backgroundColor' => 'rgba(168, 85, 247, 0.1)',
            'fill' => false,
            'tension' => 0.1,
            'pointRadius' => 3,
            'pointHoverRadius' => 6,
            'borderWidth' => 3
        ];

        return response()->json([
            'labels' => $allDates->values()->toArray(),
            'datasets' => $datasets
        ]);
    }
}
