<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora Crypto - VES/USDT/USD/EUR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg: #f3f4f6;
            --card: #ffffff;
            --muted: #4b5563;
            --primary: #0d6efd;
            --success: #16a34a;
            --warning: #b45309;
            --accent: #6d28d9;
        }

        body {
            background-color: var(--bg);
        }

        .dark {
            --bg: #0b1220;
            --card: #0f1724;
            --muted: #9aa6b2;
            --primary: #60a5fa;
            --success: #34d399;
            --warning: #f59e0b;
            --accent: #c084fc;
            color: #e6eef8;
        }

        /* Override common utility classes when in dark mode */
        .dark [class*="bg-white"] {
            background-color: var(--card) !important;
            color: inherit !important
        }

        .dark [class*="bg-gray-50"] {
            background-color: #07101a !important
        }

        .dark [class*="bg-blue-50"],
        .dark [class*="bg-blue-100"] {
            background-color: rgba(14, 54, 92, 0.18) !important
        }

        .dark [class*="bg-green-50"],
        .dark [class*="bg-green-100"] {
            background-color: rgba(6, 78, 59, 0.12) !important
        }

        .dark [class*="bg-yellow-50"],
        .dark [class*="bg-yellow-100"] {
            background-color: rgba(113, 63, 18, 0.08) !important
        }

        .dark [class*="bg-purple-50"],
        .dark [class*="bg-purple-100"] {
            background-color: rgba(76, 29, 149, 0.08) !important
        }

        .dark .text-gray-600,
        .dark [class*="text-gray"] {
            color: var(--muted) !important
        }

        .dark .text-blue-600,
        .dark [class*="text-blue"] {
            color: var(--primary) !important
        }

        .dark .text-green-600,
        .dark [class*="text-green"] {
            color: var(--success) !important
        }

        .dark .text-yellow-600,
        .dark [class*="text-yellow"] {
            color: var(--warning) !important
        }

        .dark .text-purple-600,
        .dark [class*="text-purple"] {
            color: var(--accent) !important
        }

        .dark .border,
        .dark [class*="border-"] {
            border-color: rgba(255, 255, 255, 0.06) !important
        }

        .dark .border-blue-200,
        .dark [class*="border-blue"] {
            border-color: rgba(96, 165, 250, 0.12) !important
        }

        .dark .shadow,
        .dark .shadow-lg {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.5) !important
        }

        .dark input,
        .dark select,
        .dark textarea {
            background-color: #07101a !important;
            color: var(--muted) !important;
            border-color: rgba(255, 255, 255, 0.04) !important
        }

        .dark .bg-gradient-to-r {
            background-image: none !important
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold">Calculadora Crypto</h1>
            <div class="flex items-center gap-2">
                <button id="theme-toggle" aria-label="Alternar modo oscuro"
                    class="px-3 py-2 rounded bg-gray-200 hover:bg-gray-300 transition">🌙</button>
            </div>
        </div>

        <!-- Tarjetas de Precios -->
        <div class="flex gap-4 mb-8 overflow-x-auto items-stretch whitespace-normal">
            <!-- Promedio del Dólar (Destacado) -->
            @if ($averageDollarRate)
                <div
                    class="flex-none w-64 bg-gradient-to-r from-green-400 to-green-600 text-white rounded-lg shadow-lg p-4 order-first lg:order-none">
                    <h3 class="font-bold text-lg mb-2">
                        💰 Promedio del Dólar
                    </h3>
                    <p class="text-2xl font-bold">
                        {{ number_format($averageDollarRate['average_price'], 2) }} VES
                    </p>
                    <p class="text-sm opacity-90 mt-2">
                        Oficial: {{ number_format($averageDollarRate['official_rate'], 2) }} |
                        P2P: {{ number_format($averageDollarRate['p2p_sell_rate'], 2) }}
                    </p>
                    <p class="text-xs opacity-75 mt-1">
                        Actualizado: {{ $averageDollarRate['last_updated']->diffForHumans() }}
                    </p>
                </div>
            @endif

            @foreach ($rates as $rate)
                @if ($rate->type != 'blue')
                    <div class="flex-none w-56 bg-white rounded-lg shadow p-4">
                        <h3 class="font-semibold text-lg mb-2">
                            @if ($rate->type === 'p2p_buy')
                                USDT/VES Compra
                            @elseif ($rate->type === 'p2p_sell')
                                USDT/VES Venta
                            @else
                                {{ $rate->currency_pair ?? 'USD/VES' }} {{ ucfirst($rate->type) }}
                            @endif
                        </h3>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ number_format($rate->average_price, 2) }} VES
                        </p>
                        <p class="text-sm text-gray-600">
                            Actualizado: {{ $rate->last_updated->diffForHumans() }}
                        </p>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Comparación Dólar P2P vs Oficial -->
        @if ($averageDollarRate && isset($averageDollarRate['comparison']))
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h2 class="text-2xl font-bold mb-6 text-center">📊 Comparación Dólar P2P vs Oficial</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Dólar Oficial -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <h3 class="font-bold text-lg text-blue-800 mb-2">🏛️ Dólar Oficial</h3>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ number_format($averageDollarRate['comparison']['ves_per_usd_official'], 2) }} VES
                        </p>
                        <p class="text-sm text-blue-600 mt-1">por 1 USD</p>
                    </div>

                    <!-- Dólar P2P -->
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                        <h3 class="font-bold text-lg text-green-800 mb-2">🔄 Dólar P2P Binance</h3>
                        <p class="text-2xl font-bold text-green-600">
                            {{ number_format($averageDollarRate['comparison']['ves_per_usd_p2p'], 2) }} VES
                        </p>
                        <p class="text-sm text-green-600 mt-1">por 1 USD</p>
                    </div>

                    <!-- Diferencia Absoluta -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                        <h3 class="font-bold text-lg text-yellow-800 mb-2">📈 Diferencia</h3>
                        <p class="text-2xl font-bold text-yellow-600">
                            {{ number_format($averageDollarRate['comparison']['absolute_difference'], 2) }} VES
                        </p>
                        <p class="text-sm text-yellow-600 mt-1">
                            ({{ number_format($averageDollarRate['comparison']['percentage_difference'], 1) }}%)
                        </p>
                    </div>

                    <!-- Mejor Opción -->
                    <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded">
                        <h3 class="font-bold text-lg text-purple-800 mb-2">💡 Recomendación</h3>
                        <div class="space-y-2">
                            <p class="text-sm">
                                <span class="font-semibold">Comprar:</span>
                                <span
                                    class="text-purple-600">{{ $averageDollarRate['comparison']['better_for_buying'] }}</span>
                            </p>
                            <p class="text-sm">
                                <span class="font-semibold">Vender:</span>
                                <span
                                    class="text-purple-600">{{ $averageDollarRate['comparison']['better_for_selling'] }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Análisis de Ahorro -->
                {{-- <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-bold text-lg mb-4 text-center">💰 Análisis de Ahorro</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if ($averageDollarRate['comparison']['savings_official_vs_p2p'] > 0)
                            <div class="text-center p-3 bg-red-100 rounded">
                                <p class="text-sm text-gray-600">Si compras con dólar oficial en lugar de P2P:</p>
                                <p class="text-lg font-bold text-red-600">
                                    Ahorras
                                    {{ number_format($averageDollarRate['comparison']['savings_official_vs_p2p'], 2) }}
                                    VES por USD
                                </p>
                            </div>
                        @else
                            <div class="text-center p-3 bg-red-100 rounded">
                                <p class="text-sm text-gray-600">Si compras con dólar oficial en lugar de P2P:</p>
                                <p class="text-lg font-bold text-red-600">
                                    Pagas
                                    {{ number_format(abs($averageDollarRate['comparison']['savings_official_vs_p2p']), 2) }}
                                    VES más por USD
                                </p>
                            </div>
                        @endif

                        @if ($averageDollarRate['comparison']['savings_p2p_vs_official'] > 0)
                            <div class="text-center p-3 bg-green-100 rounded">
                                <p class="text-sm text-gray-600">Si compras con P2P en lugar de dólar oficial:</p>
                                <p class="text-lg font-bold text-green-600">
                                    Ahorras
                                    {{ number_format($averageDollarRate['comparison']['savings_p2p_vs_official'], 2) }}
                                    VES por USD
                                </p>
                            </div>
                        @else
                            <div class="text-center p-3 bg-green-100 rounded">
                                <p class="text-sm text-gray-600">Si compras con P2P en lugar de dólar oficial:</p>
                                <p class="text-lg font-bold text-green-600">
                                    Pagas
                                    {{ number_format(abs($averageDollarRate['comparison']['savings_p2p_vs_official']), 2) }}
                                    VES más por USD
                                </p>
                            </div>
                        @endif
                    </div>
                </div> --}}

                <!-- Tabla de Equivalencias en Dólares -->
                {{-- @if (isset($averageDollarRate['comparison']['dollar_equivalences']))
                    <div class="bg-white rounded-lg p-4 border-2 border-blue-200 mt-6">
                        <h3 class="font-bold text-xl mb-4 text-center">💵 Equivalencias en Dólares</h3>
                        <p class="text-sm text-gray-600 text-center mb-4">
                            Cuántos dólares obtienes con diferentes montos en VES usando cada cotización
                        </p>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-2 text-left font-semibold">Monto VES</th>
                                        <th class="px-4 py-2 text-center font-semibold text-blue-600">🏛️ Dólar Oficial
                                        </th>
                                        <th class="px-4 py-2 text-center font-semibold text-green-600">🔄 Dólar P2P</th>
                                        <th class="px-4 py-2 text-center font-semibold text-yellow-600">📊 Diferencia
                                        </th>
                                        <th class="px-4 py-2 text-center font-semibold text-purple-600">💡 Mejor Opción
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($averageDollarRate['comparison']['dollar_equivalences'] as $equivalence)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-2 font-medium">
                                                {{ number_format($equivalence['ves_amount'], 0) }} VES
                                            </td>
                                            <td class="px-4 py-2 text-center text-blue-600 font-semibold">
                                                ${{ number_format($equivalence['usd_official'], 2) }}
                                            </td>
                                            <td class="px-4 py-2 text-center text-green-600 font-semibold">
                                                ${{ number_format($equivalence['usd_p2p'], 2) }}
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <span class="text-yellow-600 font-semibold">
                                                    ${{ number_format($equivalence['difference'], 2) }}
                                                </span>
                                                <br>
                                                <span class="text-xs text-gray-500">
                                                    ({{ number_format($equivalence['percentage_difference'], 1) }}%)
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                @if ($equivalence['better_option'] === 'Oficial')
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        🏛️ Oficial
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        🔄 P2P
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-800 text-center">
                                <span class="font-semibold">💡 Consejo:</span>
                                La opción marcada te da más dólares por la misma cantidad de VES
                            </p>
                        </div>
                    </div>
                @endif --}}
            </div>
        @endif

        <!-- Calculadora de Equivalencias en Dólares -->
        {{-- <div class="bg-white rounded-lg shadow p-6 mb-8"> --}}
        {{-- <h2 class="text-2xl font-bold mb-4 text-center">💵 Calculadora de Equivalencias en Dólares</h2>
            <p class="text-sm text-gray-600 text-center mb-6">
                Ingresa cualquier monto en VES para ver cuántos dólares obtienes con cada cotización
            </p> --}}

        {{-- <form action="{{ route('calculate.equivalence') }}" method="POST" class="max-w-md mx-auto">
                @csrf
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium mb-2">Monto en VES</label>
                        <input type="number" step="0.01" name="ves_amount"
                            value="{{ $formData['ves_amount'] ?? '' }}" placeholder="Ej: 1000000"
                            class="w-full p-3 border rounded-lg text-lg font-semibold" required>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" id="calculate-equivalence-btn"
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-green-500 text-white rounded-lg hover:from-blue-600 hover:to-green-600 transition-all duration-200 font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="btn-text">Calcular</span>
                            <span id="btn-loading" class="hidden">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Calculando...
                            </span>
                        </button>
                    </div>
                </div>
            </form> --}}

        {{-- @if (isset($equivalenceResult))
                <div id="equivalence-results"
                    class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-green-50 rounded-lg border-2 border-blue-200">
                    <h3 class="font-bold text-xl text-center mb-6">📊 Resultado de la Equivalencia</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Dólar Oficial -->
                        <div class="bg-blue-100 p-6 rounded-lg text-center">
                            <h4 class="font-bold text-lg text-blue-800 mb-3">🏛️ Dólar Oficial</h4>
                            <p class="text-3xl font-bold text-blue-600 mb-2">
                                ${{ number_format($equivalenceResult['usd_official'], 2) }}
                            </p>
                            <p class="text-sm text-blue-600">
                                Tasa: {{ number_format($equivalenceResult['official_rate'], 2) }} VES
                            </p>
                            <p class="text-xs text-gray-600 mt-1">
                                {{ number_format($equivalenceResult['ves_amount'], 0) }} VES ÷
                                {{ number_format($equivalenceResult['official_rate'], 2) }}
                            </p>
                        </div>

                        <!-- Dólar P2P -->
                        <div class="bg-green-100 p-6 rounded-lg text-center">
                            <h4 class="font-bold text-lg text-green-800 mb-3">🔄 Dólar P2P Binance</h4>
                            <p class="text-3xl font-bold text-green-600 mb-2">
                                ${{ number_format($equivalenceResult['usd_p2p'], 2) }}
                            </p>
                            <p class="text-sm text-green-600">
                                Tasa: {{ number_format($equivalenceResult['p2p_rate'], 2) }} VES
                            </p>
                            <p class="text-xs text-gray-600 mt-1">
                                {{ number_format($equivalenceResult['ves_amount'], 0) }} VES ÷
                                {{ number_format($equivalenceResult['p2p_rate'], 2) }}
                            </p>
                        </div>
                    </div>

                    <!-- Análisis de la Diferencia -->
                    <div class="bg-yellow-100 p-6 rounded-lg text-center">
                        <h4 class="font-bold text-lg text-yellow-800 mb-4">📈 Análisis de la Diferencia</h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div class="bg-white p-4 rounded-lg">
                                <p class="text-sm text-gray-600 mb-1">Diferencia Absoluta</p>
                                <p class="text-2xl font-bold text-yellow-600">
                                    ${{ number_format($equivalenceResult['difference'], 2) }}
                                </p>
                            </div>
                            <div class="bg-white p-4 rounded-lg">
                                <p class="text-sm text-gray-600 mb-1">Diferencia Porcentual</p>
                                <p class="text-2xl font-bold text-yellow-600">
                                    {{ number_format($equivalenceResult['percentage_difference'], 1) }}%
                                </p>
                            </div>
                            <div class="bg-white p-4 rounded-lg">
                                <p class="text-sm text-gray-600 mb-1">Mejor Opción</p>
                                <p class="text-2xl font-bold text-purple-600">
                                    {{ $equivalenceResult['better_option'] }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg">
                            <p class="text-lg font-semibold text-gray-800">
                                💰 Ahorro potencial:
                                <span
                                    class="text-green-600">${{ number_format($equivalenceResult['savings'], 2) }}</span>
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                Al elegir la mejor opción obtienes
                                {{ number_format($equivalenceResult['savings'], 2) }} dólares adicionales
                            </p>
                        </div>
                    </div>
                </div>
            @endif --}}
        {{-- </div> --}}

        <!-- Calculadora -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4">Calculadora</h2>
            <form action="{{ route('calculate') }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-5 gap-4 hidden">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Monto</label>
                    <input type="number" step="0.01" name="amount" value="{{ $formData['amount'] ?? 1 }}"
                        class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">De</label>
                    <select name="from_currency" class="w-full p-2 border rounded">
                        <option value="VES" {{ ($formData['from_currency'] ?? '') == 'VES' ? 'selected' : '' }}>VES
                        </option>
                        <option value="USDT" {{ ($formData['from_currency'] ?? '') == 'USDT' ? 'selected' : '' }}>
                            USDT
                        </option>
                        <option value="USD" {{ ($formData['from_currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD
                        </option>
                        <option value="EUR" {{ ($formData['from_currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">A</label>
                    <select name="to_currency" class="w-full p-2 border rounded">
                        <option value="VES" {{ ($formData['to_currency'] ?? '') == 'VES' ? 'selected' : '' }}>VES
                        </option>
                        <option value="USDT" {{ ($formData['to_currency'] ?? '') == 'USDT' ? 'selected' : '' }}>USDT
                        </option>
                        <option value="USD" {{ ($formData['to_currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD
                        </option>
                        <option value="EUR" {{ ($formData['to_currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tasa</label>
                    <select name="rate_type" class="w-full p-2 border rounded">
                        <option value="p2p_buy" {{ ($formData['rate_type'] ?? '') == 'p2p_buy' ? 'selected' : '' }}>
                            USDT Compra P2P</option>
                        <option value="p2p_sell" {{ ($formData['rate_type'] ?? '') == 'p2p_sell' ? 'selected' : '' }}>
                            USDT Venta P2P</option>
                        <option value="official" {{ ($formData['rate_type'] ?? '') == 'official' ? 'selected' : '' }}>
                            Dólar Oficial</option>
                        <option value="euro" {{ ($formData['rate_type'] ?? '') == 'euro' ? 'selected' : '' }}>
                            Euro</option>
                        <option value="comparison"
                            {{ ($formData['rate_type'] ?? '') == 'comparison' ? 'selected' : '' }}>
                            🔄 Comparar P2P vs Oficial</option>

                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">
                        Calcular
                    </button>
                </div>
            </form>

            <!-- Resultados en vivo: comparaciones simultáneas -->
            <div id="live-comparisons" class="mt-6 p-4 bg-gray-50 rounded-lg border">
                <h3 class="font-semibold mb-3">Resultados en tiempo real</h3>

                <!-- Inputs sincronizados por moneda -->
                <div id="multi-inputs" class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">VES</label>
                        <input id="input-VES" data-currency="VES" type="number" step="0.01"
                            class="w-full p-2 border rounded" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">USDT</label>
                        <input id="input-USDT" data-currency="USDT" type="number" step="0.01"
                            class="w-full p-2 border rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">USD BCV</label>
                        <input id="input-USD" data-currency="USD" type="number" step="0.01"
                            class="w-full p-2 border rounded" />
                    </div>



                    <div>
                        <label class="block text-sm font-medium mb-1">EUR BCV</label>
                        <input id="input-EUR" data-currency="EUR" type="number" step="0.01"
                            class="w-full p-2 border rounded" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="live-results-grid">
                    <!-- JS will populate cards here -->
                </div>
            </div>

            @if (isset($calculation))
                <div class="mt-6 p-4 bg-green-50 rounded-lg">
                    <h3 class="font-semibold text-lg">Resultado:</h3>
                    <p class="text-2xl font-bold text-green-600">
                        {{ number_format($formData['amount'], 2) }} {{ $formData['from_currency'] }}
                        =
                        {{ number_format($calculation, 2) }} {{ $formData['to_currency'] }}
                    </p>
                    <p class="text-sm text-gray-600">
                        Tasa: {{ number_format($rates[$formData['rate_type']]->average_price, 2) }} VES
                    </p>
                </div>
            @endif

            @if (isset($comparisonResult))
                <div class="mt-6 p-6 bg-gradient-to-r from-blue-50 to-green-50 rounded-lg border-2 border-blue-200">
                    <h3 class="font-semibold text-xl text-center mb-4">🔄 Comparación P2P vs Oficial</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <!-- Dólar Oficial -->
                        <div class="bg-blue-100 p-4 rounded-lg">
                            <h4 class="font-bold text-lg text-blue-800 mb-2">🏛️ Dólar Oficial</h4>
                            <p class="text-2xl font-bold text-blue-600">
                                {{ number_format($comparisonResult['amount'], 2) }}
                                {{ $comparisonResult['from_currency'] }}
                                = {{ number_format($comparisonResult['official']['result'], 2) }}
                                {{ $comparisonResult['to_currency'] }}
                            </p>
                            <p class="text-sm text-blue-600 mt-1">
                                Tasa: {{ number_format($comparisonResult['official']['rate'], 2) }} VES
                            </p>
                        </div>

                        <!-- Dólar P2P -->
                        <div class="bg-green-100 p-4 rounded-lg">
                            <h4 class="font-bold text-lg text-green-800 mb-2">🔄 Dólar P2P</h4>
                            <p class="text-2xl font-bold text-green-600">
                                {{ number_format($comparisonResult['amount'], 2) }}
                                {{ $comparisonResult['from_currency'] }}
                                = {{ number_format($comparisonResult['p2p']['result'], 2) }}
                                {{ $comparisonResult['to_currency'] }}
                            </p>
                            <p class="text-sm text-green-600 mt-1">
                                Tasa: {{ number_format($comparisonResult['p2p']['rate'], 2) }} VES
                            </p>
                        </div>
                    </div>

                    <!-- Análisis de la diferencia -->
                    <div class="bg-yellow-100 p-4 rounded-lg text-center">
                        <h4 class="font-bold text-lg text-yellow-800 mb-2">📊 Análisis de la Diferencia</h4>
                        <p class="text-lg">
                            <span class="font-semibold">Diferencia:</span>
                            {{ number_format($comparisonResult['difference'], 2) }}
                            {{ $comparisonResult['to_currency'] }}
                            <span class="text-sm text-gray-600">
                                ({{ number_format($comparisonResult['percentage_difference'], 1) }}%)
                            </span>
                        </p>
                        <p class="text-lg mt-2">
                            <span class="font-semibold">Mejor opción:</span>
                            <span class="text-purple-600 font-bold">{{ $comparisonResult['better_option'] }}</span>
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            Ahorro potencial: {{ number_format($comparisonResult['savings'], 2) }}
                            {{ $comparisonResult['to_currency'] }}
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Historial Gráfico -->
        {{-- <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Evolución Histórica del Dólar (Últimos 2 Meses)</h2>
                <div class="flex space-x-2">
                    <button id="toggleChart"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                        <span id="chartToggleText">Ver Solo Promedio</span>
                    </button>
                    <button id="refreshChart"
                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                        🔄 Actualizar
                    </button>
                </div>
            </div>
            <div class="relative">
                <canvas id="ratesChart" height="400"></canvas>
                <div id="chartLoading"
                    class="absolute inset-0 items-center justify-center bg-white bg-opacity-75 hidden"
                    style="display: none;">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                        <p class="text-gray-600">Cargando datos históricos...</p>
                    </div>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-600 font-medium">Dólar Oficial</p>
                    <p class="text-xs text-gray-500">Tasa oficial del BCV</p>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <p class="text-sm text-green-600 font-medium">USDT P2P Venta</p>
                    <p class="text-xs text-gray-500">Binance P2P - Venta</p>
                </div>
                <div class="text-center p-3 bg-purple-50 rounded-lg">
                    <p class="text-sm text-purple-600 font-medium">Promedio Dólar</p>
                    <p class="text-xs text-gray-500">Promedio entre oficial y P2P</p>
                </div>
            </div>
        </div> --}}
    </div>
    @php
        $ratesEnd = $rates
            ->mapWithKeys(function ($rate, $key) {
                return [
                    $key => [
                        'average_price' => $rate->average_price,
                        'currency_pair' => $rate->currency_pair ?? null,
                        'type' => $rate->type,
                    ],
                ];
            })
            ->toArray();
    @endphp
    <script>
        // Embed server rates for client-side calculations
        const ratesData = {!! json_encode($ratesEnd) !!};

        // Preferred rate type to convert a currency to VES when needed
        const preferredRateForCurrency = {
            'USD': 'official',
            'USDT': 'p2p_sell',
            'EUR': 'euro'
        };

        function formatNumber(n) {
            return Number(n).toLocaleString('es-VE', {
                maximumFractionDigits: 2
            });
        }

        function getVesFromInput(amount, fromCurrency) {
            if (!amount || isNaN(amount)) return 0;
            if (fromCurrency === 'VES') return Number(amount);
            const preferred = preferredRateForCurrency[fromCurrency];
            let rate = ratesData[preferred];
            if (!rate) {
                // fallback: find any rate whose currency_pair starts with the currency
                rate = Object.values(ratesData).find(r => r.currency_pair && r.currency_pair.startsWith(fromCurrency));
            }
            if (!rate) return 0;
            return Number(amount) * Number(rate.average_price);
        }

        function updateLiveComparisons() {
            const amountEl = document.querySelector('input[name="amount"]');
            const fromEl = document.querySelector('select[name="from_currency"]');
            const amount = parseFloat(amountEl.value || 0);
            const fromCurrency = fromEl.value || 'VES';
            const vesAmount = getVesFromInput(amount, fromCurrency);

            const container = document.getElementById('live-results-grid');
            container.innerHTML = '';

            Object.keys(ratesData).forEach(key => {
                const r = ratesData[key];
                const pair = r.currency_pair || (r.type === 'euro' ? 'EUR/VES' : 'USD/VES');
                const foreignCurrency = pair.split('/')[0];

                // foreign amount = vesAmount / rate
                const foreignAmount = r.average_price ? (vesAmount / Number(r.average_price)) : 0;

                const card = document.createElement('div');
                card.className = 'bg-white rounded-lg shadow p-4';
                card.innerHTML = `
                    <h4 class="font-semibold text-lg mb-1">${pair} — ${r.type}</h4>
                    <p class="text-2xl font-bold text-blue-600">${formatNumber(foreignAmount)} ${foreignCurrency}</p>
                    <p class="text-sm text-gray-600">${formatNumber(vesAmount)} VES</p>
                `;

                container.appendChild(card);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // existing DOMContentLoaded code runs earlier; ensure listeners for live calc
            const amountEl = document.querySelector('input[name="amount"]');
            const fromEl = document.querySelector('select[name="from_currency"]');

            if (amountEl) amountEl.addEventListener('input', updateLiveComparisons);
            if (fromEl) fromEl.addEventListener('change', updateLiveComparisons);

            // initial render
            updateLiveComparisons();
            // --- Multi-input synchronization ---
            const currencyInputs = {
                'VES': document.getElementById('input-VES'),
                'USD': document.getElementById('input-USD'),
                'USDT': document.getElementById('input-USDT'),
                'EUR': document.getElementById('input-EUR')
            };

            let isSyncing = false;

            function getRateForCurrency(currency) {
                // VES is the base currency: 1 VES per VES
                if (currency === 'VES') return 1;
                const preferred = preferredRateForCurrency[currency];
                let rate = ratesData[preferred];
                if (!rate) {
                    rate = Object.values(ratesData).find(r => r.currency_pair && r.currency_pair.startsWith(
                        currency));
                }
                return rate ? Number(rate.average_price) : null;
            }

            function decimalsFor(currency) {
                return 2;
            }

            function syncFrom(currency, rawValue) {
                if (isSyncing) return;
                isSyncing = true;

                const value = parseFloat(rawValue);
                if (isNaN(value)) {
                    Object.values(currencyInputs).forEach(i => i.value = '');
                    isSyncing = false;
                    return;
                }

                // compute ves amount
                let ves = 0;
                if (currency === 'VES') {
                    ves = value;
                } else {
                    const rate = getRateForCurrency(currency);
                    if (!rate) {
                        ves = 0;
                    } else {
                        ves = value * rate; // amount * (VES per unit)
                    }
                }

                // update all inputs
                Object.keys(currencyInputs).forEach(cur => {
                    if (cur === currency) return; // skip source
                    const input = currencyInputs[cur];
                    const rate = getRateForCurrency(cur);
                    if (!rate || rate === 0) {
                        input.value = '';
                        return;
                    }
                    const converted = ves / rate; // VES to target
                    input.value = Number(converted).toFixed(decimalsFor(cur));
                });

                // Keep the master amount field in sync with VES
                const mainAmount = document.querySelector('input[name="amount"]');
                const mainFrom = document.querySelector('select[name="from_currency"]');
                if (mainAmount && mainFrom) {
                    mainFrom.value = 'VES';
                    mainAmount.value = ves.toFixed(2);
                    // update live comparison cards after syncing main amount
                    if (typeof updateLiveComparisons === 'function') updateLiveComparisons();
                }

                isSyncing = false;
            }

            // attach listeners
            Object.keys(currencyInputs).forEach(cur => {
                const input = currencyInputs[cur];
                if (!input) return;
                input.addEventListener('input', (e) => {
                    syncFrom(cur, e.target.value);
                });
                input.addEventListener('focus', (e) => {
                    // clear other inputs placeholder if desired
                });
            });

            // initial fill: take main amount and from to populate inputs
            (function initMultiInputs() {
                const mainAmountVal = parseFloat((document.querySelector('input[name="amount"]') || {
                    value: 0
                }).value || 0);
                const mainFromVal = (document.querySelector('select[name="from_currency"]') || {
                    value: 'VES'
                }).value;
                // set source input with main values
                const sourceInput = currencyInputs[mainFromVal];
                if (sourceInput) {
                    sourceInput.value = mainAmountVal;
                    syncFrom(mainFromVal, mainAmountVal);
                } else {
                    // default fill VES
                    currencyInputs['VES'].value = mainAmountVal;
                    syncFrom('VES', mainAmountVal);
                }
            })();
        });
        let chart = null;
        let allDatasets = [];
        let showOnlyAverage = false;

        // Configuración del gráfico
        const chartConfig = {
            type: 'line',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Evolución de Cotizaciones del Dólar',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toLocaleString('es-VE') +
                                    ' VES';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Fecha',
                            font: {
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            maxTicksLimit: 10,
                            callback: function(value, index, ticks) {
                                const date = new Date(this.getLabelForValue(value));
                                return date.toLocaleDateString('es-VE', {
                                    month: 'short',
                                    day: 'numeric'
                                });
                            }
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Precio (VES)',
                            font: {
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('es-VE') + ' VES';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                elements: {
                    point: {
                        hoverRadius: 8,
                        radius: 4
                    }
                }
            }
        };

        // Función para cargar datos del gráfico
        async function loadChartData() {
            const loadingElement = document.getElementById('chartLoading');
            if (!loadingElement) return; // chart UI not present
            loadingElement.style.display = 'flex';

            try {
                const response = await fetch('/api/historical-rates');
                const data = await response.json();

                if (data.labels && data.datasets) {
                    allDatasets = data.datasets;
                    updateChart(data);
                } else {
                    showNoDataMessage();
                }
            } catch (error) {
                console.error('Error cargando datos:', error);
                showErrorMessage();
            } finally {
                loadingElement.style.display = 'none';
            }
        }

        // Función para actualizar el gráfico
        function updateChart(data) {
            const canvasEl = document.getElementById('ratesChart');
            if (!canvasEl) return;
            const ctx = canvasEl.getContext('2d');

            if (chart) {
                chart.destroy();
            }

            chart = new Chart(ctx, {
                ...chartConfig,
                data: data
            });
        }

        // Función para alternar entre mostrar todas las líneas o solo el promedio
        function toggleChartView() {
            if (!chart || !allDatasets.length) return;

            showOnlyAverage = !showOnlyAverage;
            const toggleText = document.getElementById('chartToggleText');

            let datasetsToShow;
            if (showOnlyAverage) {
                datasetsToShow = allDatasets.filter(dataset => dataset.label === 'Promedio Dólar');
                if (toggleText) toggleText.textContent = 'Ver Todas las Líneas';
            } else {
                datasetsToShow = allDatasets;
                if (toggleText) toggleText.textContent = 'Ver Solo Promedio';
            }

            chart.data.datasets = datasetsToShow;
            chart.update('active');
        }

        // Función para mostrar mensaje cuando no hay datos
        function showNoDataMessage() {
            const canvas = document.getElementById('ratesChart');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.font = '16px Arial';
            ctx.fillStyle = '#666';
            ctx.textAlign = 'center';
            ctx.fillText('No hay datos históricos disponibles', canvas.width / 2, canvas.height / 2);
        }

        // Función para mostrar mensaje de error
        function showErrorMessage() {
            const canvas = document.getElementById('ratesChart');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.font = '16px Arial';
            ctx.fillStyle = '#dc2626';
            ctx.textAlign = 'center';
            ctx.fillText('Error al cargar los datos del gráfico', canvas.width / 2, canvas.height / 2);
        }

        // Event listeners (guarded)
        const toggleChartBtn = document.getElementById('toggleChart');
        if (toggleChartBtn) toggleChartBtn.addEventListener('click', toggleChartView);
        const refreshChartBtn = document.getElementById('refreshChart');
        if (refreshChartBtn) refreshChartBtn.addEventListener('click', loadChartData);

        // Cargar datos cuando la página esté lista
        document.addEventListener('DOMContentLoaded', function() {
            loadChartData();

            // Actualizar automáticamente cada 5 minutos
            setInterval(loadChartData, 5 * 60 * 1000);

            // Scroll automático a resultados de equivalencia si existen
            const equivalenceResults = document.getElementById('equivalence-results');
            if (equivalenceResults) {
                setTimeout(() => {
                    equivalenceResults.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            }

            // Manejar el botón de calcular equivalencias
            const calculateBtn = document.getElementById('calculate-equivalence-btn');
            const btnText = document.getElementById('btn-text');
            const btnLoading = document.getElementById('btn-loading');

            if (calculateBtn) {
                calculateBtn.addEventListener('click', function() {
                    // Mostrar estado de carga
                    this.disabled = true;
                    if (btnText) btnText.classList.add('hidden');
                    if (btnLoading) btnLoading.classList.remove('hidden');
                });
            }
        });

        // Manejar redimensionamiento de ventana
        window.addEventListener('resize', function() {
            if (chart) {
                chart.resize();
            }
        });
    </script>
    <script>
        (function() {
            const toggle = document.getElementById('theme-toggle');
            const stored = localStorage.getItem('theme');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = stored ? stored === 'dark' : prefersDark;

            function apply(dark) {
                if (dark) document.documentElement.classList.add('dark');
                else document.documentElement.classList.remove('dark');
                if (toggle) toggle.textContent = dark ? '☀️' : '🌙';
                localStorage.setItem('theme', dark ? 'dark' : 'light');
            }

            if (toggle) {
                toggle.addEventListener('click', function() {
                    apply(!document.documentElement.classList.contains('dark'));
                });
            }

            apply(isDark);
        })();
    </script>
</body>

</html>
