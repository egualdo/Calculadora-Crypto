<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\ExchangeRate;
use Carbon\Carbon;

class BcvScraperService
{
    private $baseUrl = 'https://www.bcv.org.ve/';

    /**
     * Try to scrape BCV homepage and extract USD and EUR exchange rates (VES per unit)
     * Returns array with keys 'official' and 'euro' when found, each containing
     * ['buy','sell','average','name','last_updated','source']
     */
    public function getRates()
    {
        try {
            $response = Http::withoutVerifying()->timeout(10)->get($this->baseUrl);
            if (!$response->successful()) {
                throw new Exception('BCV fetch failed: ' . $response->status());
            }

            $html = $response->body();

            // Attempt DOM parsing: locate element with id/class 'recuadrotsmc',
            // then find the first descendant with classes 'col-sm-6 col-xs-6 centrado'
            // and extract the text inside the <strong> tag (expected EUR value).
            $rates = [];

            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            // Ensure proper encoding
            $loaded = $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            libxml_clear_errors();

            $eurVal = null;
            $usdVal = null;

            if ($loaded) {
                $xpath = new \DOMXPath($dom);

                // find nodes matching id or class 'recuadrotsmc'
                $recuadroNodes = $xpath->query("//*[contains(@id,'recuadrotsmc') or contains(@class,'recuadrotsmc')]");

                // helper to extract the <strong> numeric value from a given root node
                $extractFromRoot = function ($root) use ($xpath) {
                    $colQuery = ".//*[contains(concat(' ', normalize-space(@class), ' '), ' col-sm-6 ') and contains(concat(' ', normalize-space(@class), ' '), ' col-xs-6 ') and contains(concat(' ', normalize-space(@class), ' '), ' centrado ')]";
                    $colNodes = $xpath->query($colQuery, $root);
                    if ($colNodes->length > 0) {
                        $firstCol = $colNodes->item(0);
                        $strong = $xpath->query('.//strong', $firstCol);
                        if ($strong->length > 0) {
                            $raw = trim($strong->item(0)->textContent);
                            $normalized = str_replace(["\xc2\xa0", ' ', '\u00A0'], ['', '', ''], $raw);
                            if (strpos($normalized, '.') !== false && strpos($normalized, ',') !== false) {
                                $normalized = str_replace('.', '', $normalized);
                                $normalized = str_replace(',', '.', $normalized);
                            } else {
                                $normalized = str_replace(',', '.', $normalized);
                            }
                            $normalized = preg_replace('/[^0-9.\-]/', '', $normalized);
                            if ($normalized !== '') {
                                return floatval($normalized);
                            }
                        }
                    }
                    return null;
                };

                // EUR: use the first recuadro node
                if ($recuadroNodes->length > 0) {
                    $eurVal = $extractFromRoot($recuadroNodes->item(0));
                }

                // USD: the user indicated the USD recuadrotsmc is the fifth occurrence
                if ($recuadroNodes->length >= 5) {
                    $usdVal = $extractFromRoot($recuadroNodes->item(4));
                }
            }
            // dd($eurVal);
            // Fallback: regex-based search if DOM extraction failed
            if ($eurVal === null) {
                // helper to find numeric value near a keyword
                $findValue = function ($keyword) use ($html) {
                    $pattern = '/' . preg_quote($keyword, '/') . '[^\d\-]{0,80}([0-9]{1,3}(?:[.,][0-9]{3})*(?:[.,][0-9]+)?)/iu';
                    if (preg_match($pattern, $html, $m)) {
                        $num = $m[1];
                        // normalize number: remove thousands separator and unify decimal point
                        $normalized = str_replace(['.', '\\u00A0', '\\s'], ['', '', ''], $num);
                        $normalized = str_replace(',', '.', $normalized);
                        $value = floatval($normalized);
                        return $value;
                    }
                    // fallback: search for patterns like 1 EUR = 12.345,67
                    $pattern2 = '/1\s*(?:' . preg_quote($keyword, '/') . '|[A-Z]{3})[^\d]{0,20}([0-9]{1,3}(?:[.,][0-9]{3})*(?:[.,][0-9]+)?)/iu';
                    if (preg_match($pattern2, $html, $m2)) {
                        $num = $m2[1];
                        $normalized = str_replace(['.', '\\u00A0', '\\s'], ['', '', ''], $num);
                        $normalized = str_replace(',', '.', $normalized);
                        return floatval($normalized);
                    }
                    return null;
                };

                $eurKeywords = ['Euro', 'EUR'];
                foreach ($eurKeywords as $k) {
                    $v = $findValue($k);
                    if ($v !== null && $v > 0) {
                        $eurVal = $v;
                        break;
                    }
                }
            }

            if ($usdVal !== null) {
                $rates['official'] = [
                    'buy' => $usdVal,
                    'sell' => $usdVal,
                    'average' => $usdVal,
                    'name' => 'BCV Dólar Oficial',
                    'last_updated' => now()->toIsoString(),
                    'source' => 'bcv.org.ve'
                ];
            }

            if ($eurVal !== null) {
                $rates['euro'] = [
                    'buy' => $eurVal,
                    'sell' => $eurVal,
                    'average' => $eurVal,
                    'name' => 'BCV Euro',
                    'last_updated' => now()->toIsoString(),
                    'source' => 'bcv.org.ve'
                ];
            }

            return $rates;
        } catch (Exception $e) {
            Log::error('BCV scraping error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update database with today's BCV rates.
     * If there are existing records for the same `type` for today, delete them and insert the new ones.
     * Reuses the same storage shape used by the UpdateExchangeRates command.
     *
     * @return bool True on success, false on failure or when no rates found
     */
    public function updateDatabaseRates()
    {
        $rates = $this->getRates();
        if (!$rates || !is_array($rates)) {
            return false;
        }

        $startOfDay = Carbon::today();
        $endOfDay = Carbon::tomorrow();

        foreach ($rates as $key => $r) {
            $type = $key;

            $currencyPair = 'USD/VES';
            if ($type === 'euro') {
                $currencyPair = 'EUR/VES';
            } elseif ($type !== 'official') {
                $currencyPair = strtoupper($type) . '/VES';
            }

            // Delete any existing records for this type created today
            ExchangeRate::where('type', $type)
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->delete();

            // Insert new record
            ExchangeRate::create([
                'type' => $type,
                'currency_pair' => $currencyPair,
                'buy_price' => $r['buy'] ?? null,
                'sell_price' => $r['sell'] ?? null,
                'average_price' => $r['average'] ?? null,
                'metadata' => $r,
                'last_updated' => now()
            ]);
        }

        return true;
    }
}
