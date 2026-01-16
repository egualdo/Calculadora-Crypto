<?php

use App\Http\Controllers\ExchangeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ExchangeController::class, 'dashboard'])->name('dashboard');
Route::post('/calculate', [ExchangeController::class, 'calculate'])->name('calculate');
Route::post('/calculate-equivalence', [ExchangeController::class, 'calculateEquivalence'])->name('calculate.equivalence');
Route::get('/api/rates', [ExchangeController::class, 'getRatesApi'])->name('api.rates');
Route::get('/api/historical-rates', [ExchangeController::class, 'getHistoricalRatesApi'])->name('api.historical-rates');
