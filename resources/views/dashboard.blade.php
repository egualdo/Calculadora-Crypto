<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora Crypto - VES/USDT/USD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8">Calculadora de Divisas</h1>

        <!-- Tarjetas de Precios -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Promedio del Dólar (Destacado) -->
            @if ($averageDollarRate)
                <div
                    class="bg-gradient-to-r from-green-400 to-green-600 text-white rounded-lg shadow-lg p-6 order-first lg:order-none">
                    <h3 class="font-bold text-lg mb-2">
                        💰 Promedio del Dólar USD
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
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="font-semibold text-lg mb-2">
                            {{ $rate->type === 'p2p_buy'
                                ? 'USDT/VES Compra'
                                : ($rate->type === 'p2p_sell'
                                    ? 'USDT/VES Venta'
                                    : ' USD/VES' . ' ' . 'Oficial') }}
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
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-center">💵 Calculadora de Equivalencias en Dólares</h2>
            <p class="text-sm text-gray-600 text-center mb-6">
                Ingresa cualquier monto en VES para ver cuántos dólares obtienes con cada cotización
            </p>

            <form action="{{ route('calculate.equivalence') }}" method="POST" class="max-w-md mx-auto">
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
            </form>

            @if (isset($equivalenceResult))
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
            @endif
        </div>

        <!-- Calculadora -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4">Calculadora</h2>
            <form action="{{ route('calculate') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
                        <option value="comparison"
                            {{ ($formData['rate_type'] ?? '') == 'comparison' ? 'selected' : '' }}>
                            🔄 Comparar P2P vs Oficial</option>
                        {{-- <option value="blue" {{ ($formData['rate_type'] ?? '') == 'blue' ? 'selected' : '' }}>Dólar
                            Blue</option> --}}
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">
                        Calcular
                    </button>
                </div>
            </form>

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

    <script>
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
            const ctx = document.getElementById('ratesChart').getContext('2d');

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
                toggleText.textContent = 'Ver Todas las Líneas';
            } else {
                datasetsToShow = allDatasets;
                toggleText.textContent = 'Ver Solo Promedio';
            }

            chart.data.datasets = datasetsToShow;
            chart.update('active');
        }

        // Función para mostrar mensaje cuando no hay datos
        function showNoDataMessage() {
            const canvas = document.getElementById('ratesChart');
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
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.font = '16px Arial';
            ctx.fillStyle = '#dc2626';
            ctx.textAlign = 'center';
            ctx.fillText('Error al cargar los datos del gráfico', canvas.width / 2, canvas.height / 2);
        }

        // Event listeners
        document.getElementById('toggleChart').addEventListener('click', toggleChartView);
        document.getElementById('refreshChart').addEventListener('click', loadChartData);

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
                    btnText.classList.add('hidden');
                    btnLoading.classList.remove('hidden');
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
</body>

</html>
