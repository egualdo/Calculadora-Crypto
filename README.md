# Calculadora Crypto - VES/USDT/USD

Una calculadora de divisas moderna desarrollada en Laravel que permite realizar conversiones entre Bolívares Venezolanos (VES), USDT y USD, utilizando cotizaciones en tiempo real del dólar oficial y Binance P2P.

## 🚀 Características

-   **Cotizaciones en Tiempo Real**: Integración con APIs del dólar oficial y Binance P2P
-   **Calculadora Inteligente**: Conversiones automáticas entre VES, USDT y USD
-   **Gráfico Histórico**: Visualización de la evolución de las cotizaciones en los últimos 2 meses
-   **Promedio del Dólar**: Cálculo automático del promedio entre dólar oficial y P2P venta
-   **Interfaz Responsive**: Diseño moderno que se adapta a todos los dispositivos
-   **Actualización Automática**: Los datos se actualizan cada 5 minutos automáticamente

## 📊 Funcionalidades

### Cotizaciones Disponibles

-   **Dólar Oficial**: Tasa oficial del Banco Central de Venezuela
-   **USDT P2P Compra**: Precios de compra en Binance P2P
-   **USDT P2P Venta**: Precios de venta en Binance P2P
-   **Dólar Blue**: Tasa del mercado paralelo
-   **Promedio Dólar**: Promedio entre dólar oficial y USDT P2P venta

### Gráfico Histórico

-   Visualización de los últimos 2 meses de datos
-   Tres líneas de datos: Dólar Oficial, USDT P2P Venta y Promedio
-   Interactividad: Alternar entre vista completa y solo promedio
-   Actualización manual y automática

## 🛠️ Tecnologías Utilizadas

-   **Backend**: Laravel 11
-   **Frontend**: Blade Templates, Tailwind CSS, Chart.js
-   **Base de Datos**: SQLite
-   **APIs Externas**:
    -   [DolarAPI](https://ve.dolarapi.com/) para cotizaciones oficiales
    -   [Binance P2P API](https://p2p.binance.com/) para tasas P2P

## 📦 Instalación

### Requisitos

-   PHP 8.1 o superior
-   Composer
-   Node.js (opcional, para assets)

### Pasos de Instalación

1. **Clonar el repositorio**

```bash
git clone https://github.com/egualdo/Calculadora-Crypto.git
cd Calculadora-Crypto
```

2. **Instalar dependencias**

```bash
composer install
```

3. **Configurar variables de entorno**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar base de datos**

```bash
php artisan migrate
```

5. **Actualizar cotizaciones iniciales**

```bash
php artisan rates:update
```

6. **Iniciar el servidor**

```bash
php artisan serve
```

## 🔄 Comandos Disponibles

### Actualizar Cotizaciones

```bash
php artisan rates:update
```

Este comando:

-   Obtiene las cotizaciones del dólar oficial desde DolarAPI
-   Obtiene las tasas P2P de Binance (compra y venta)
-   Guarda los datos en la base de datos
-   Limpia la caché de cotizaciones

### Programar Actualizaciones Automáticas

Para mantener las cotizaciones actualizadas automáticamente, puedes configurar un cron job:

```bash
# Agregar al crontab para ejecutar cada 30 minutos
*/30 * * * * cd /path/to/project && php artisan rates:update
```

## 📁 Estructura del Proyecto

```
crypto-calculator/
├── app/
│   ├── Console/Commands/
│   │   └── UpdateExchangeRates.php    # Comando para actualizar cotizaciones
│   ├── Http/Controllers/
│   │   └── ExchangeController.php     # Controlador principal
│   ├── Models/
│   │   └── ExchangeRate.php           # Modelo de cotizaciones
│   └── Services/
│       ├── BinanceService.php         # Servicio para Binance P2P
│       └── DolarApiService.php        # Servicio para DolarAPI
├── database/
│   └── migrations/
│       └── create_exchange_rates_table.php
├── resources/
│   └── views/
│       └── dashboard.blade.php        # Vista principal
└── routes/
    └── web.php                        # Rutas de la aplicación
```

## 🌐 APIs y Endpoints

### Endpoints Disponibles

-   `GET /` - Dashboard principal con calculadora y gráfico
-   `POST /calculate` - Realizar conversiones de moneda
-   `GET /api/rates` - API para obtener cotizaciones actuales
-   `GET /api/historical-rates` - API para datos históricos del gráfico

### APIs Externas Utilizadas

1. **DolarAPI**: https://ve.dolarapi.com/v1/dolares

    - Proporciona cotizaciones oficiales del dólar venezolano

2. **Binance P2P**: https://p2p.binance.com/bapi/c2c/v2/friendly/c2c/adv/search
    - Proporciona tasas P2P de USDT/VES

## 🎨 Características del Frontend

-   **Diseño Responsive**: Adaptable a móviles, tablets y desktop
-   **Gráficos Interactivos**: Implementados con Chart.js
-   **Tarjetas de Cotizaciones**: Visualización clara de precios actuales
-   **Calculadora Intuitiva**: Formulario fácil de usar para conversiones
-   **Indicadores de Estado**: Loading, errores y actualizaciones en tiempo real

## 🔧 Configuración

### Variables de Entorno

Las siguientes variables pueden ser configuradas en el archivo `.env`:

```env
APP_NAME="Calculadora Crypto"
APP_URL=http://localhost:8000

# Base de datos (SQLite por defecto)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Cache (opcional)
CACHE_DRIVER=file
```

## 📈 Monitoreo y Mantenimiento

### Logs

Los logs se almacenan en `storage/logs/laravel.log` y incluyen:

-   Errores de conexión con APIs externas
-   Fallos en la actualización de cotizaciones
-   Errores de procesamiento de datos

### Caché

Las cotizaciones se almacenan en caché por 10 minutos para optimizar el rendimiento.

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👨‍💻 Autor

**Eduardo Gualdo**

-   GitHub: [@egualdo](https://github.com/egualdo)

## 🙏 Agradecimientos

-   [DolarAPI](https://ve.dolarapi.com/) por proporcionar las cotizaciones oficiales
-   [Binance](https://www.binance.com/) por la API P2P
-   [Laravel](https://laravel.com/) por el framework
-   [Chart.js](https://www.chartjs.org/) por las librerías de gráficos
-   [Tailwind CSS](https://tailwindcss.com/) por el framework de CSS
