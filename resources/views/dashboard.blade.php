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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <!-- Promedio del Dólar (Destacado) -->
            @if ($averageDollarRate)
                <div
                    class="bg-gradient-to-r from-green-400 to-green-600 text-white rounded-lg shadow-lg p-6 order-first lg:order-none">
                    <h3 class="font-bold text-lg mb-2">
                        💰 Promedio Dólar
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
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-semibold text-lg mb-2">
                        {{ $rate->type === 'p2p_buy'
                            ? 'USDT/VES Compra'
                            : ($rate->type === 'p2p_sell'
                                ? 'USDT/VES Venta'
                                : strtoupper($rate->type) . ' USD/VES') }}
                    </h3>
                    <p class="text-2xl font-bold text-blue-600">
                        {{ number_format($rate->average_price, 2) }} VES
                    </p>
                    <p class="text-sm text-gray-600">
                        Actualizado: {{ $rate->last_updated->diffForHumans() }}
                    </p>
                </div>
            @endforeach
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
                        <option value="blue" {{ ($formData['rate_type'] ?? '') == 'blue' ? 'selected' : '' }}>Dólar
                            Blue</option>
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
        </div>

        <!-- Historial Gráfico -->
        <div class="bg-white rounded-lg shadow p-6">
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
        </div>
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
