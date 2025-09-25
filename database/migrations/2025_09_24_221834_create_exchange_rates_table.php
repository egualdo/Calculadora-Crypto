<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['p2p_buy', 'p2p_sell', 'official', 'blue', 'euro']);
            $table->string('currency_pair'); // USDT/VES, USD/VES, etc.
            $table->decimal('buy_price', 15, 4)->nullable();
            $table->decimal('sell_price', 15, 4)->nullable();
            $table->decimal('average_price', 15, 4);
            $table->json('metadata')->nullable(); // Datos adicionales
            $table->timestamp('last_updated');
            $table->timestamps();

            $table->index(['type', 'currency_pair']);
            $table->index('last_updated');
        });
    }

    public function down()
    {
        Schema::dropIfExists('exchange_rates');
    }
};
