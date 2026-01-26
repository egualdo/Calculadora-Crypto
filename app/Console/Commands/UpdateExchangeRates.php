<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BinanceService;
use App\Services\DolarApiService;
use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class UpdateExchangeRates extends Command
{
    protected $signature = 'rates:update';
    protected $description = 'Update exchange rates from Binance and DolarAPI';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(BinanceService $binance, DolarApiService $dolarApi)
    {
        $this->info('Updating exchange rates...');

        // Binance P2P Rates
        $this->updateBinanceRates($binance);

        // DolarAPI Rates
        $this->updateDollarRates($dolarApi);

        // Clear cache
        Cache::forget('latest_exchange_rates');

        $this->info('Exchange rates updated successfully!' . Carbon::now());
    }

    private function updateBinanceRates(BinanceService $binance)
    {
        $buyRates = $binance->getP2PRates('buy');
        $sellRates = $binance->getP2PRates('sell');

        if ($buyRates) {
            ExchangeRate::create([
                'type' => 'p2p_buy',
                'currency_pair' => 'USDT/VES',
                'buy_price' => $buyRates['avg_price'],
                'average_price' => $buyRates['weighted_avg'],
                'metadata' => $buyRates,
                'last_updated' => now()
            ]);
        }

        if ($sellRates) {
            ExchangeRate::create([
                'type' => 'p2p_sell',
                'currency_pair' => 'USDT/VES',
                'sell_price' => $sellRates['avg_price'],
                'average_price' => $sellRates['weighted_avg'],
                'metadata' => $sellRates,
                'last_updated' => now()
            ]);
        }
    }

    private function updateDollarRates(DolarApiService $dolarApi)
    {
        $rates = $dolarApi->getDollarRates();

        if ($rates) {
            foreach ($rates as $type => $rate) {
                $currencyPair = 'USD/VES';
                if ($type === 'euro') {
                    $currencyPair = 'EUR/VES';
                }

                ExchangeRate::create([
                    'type' => $type,
                    'currency_pair' => $currencyPair,
                    'buy_price' => $rate['buy'],
                    'sell_price' => $rate['sell'],
                    'average_price' => $rate['average'],
                    'metadata' => $rate,
                    'last_updated' => now()
                ]);
            }
        }
    }
}
