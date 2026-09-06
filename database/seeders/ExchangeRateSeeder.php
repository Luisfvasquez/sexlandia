<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class ExchangeRateSeeder extends Seeder
{
    /**
     * Tasa de cambio inicial (bolívares por dólar).
     *
     * Los precios del catálogo se almacenan en USD; esta tasa permite mostrar
     * el equivalente en Bs. En producción se actualiza con `php artisan exchange:update-usd`.
     */
    public function run(): void
    {
        $rate = 540;

        ExchangeRate::where('is_active', true)->update(['is_active' => false]);

        $newRate = ExchangeRate::updateOrCreate(
            ['currency_from' => 'USD', 'currency_to' => 'BS', 'date' => now()->format('Y-m-d')],
            ['rate' => $rate, 'is_active' => true],
        );

        Cache::forever('exchange_rate', $newRate->rate);
    }
}
