<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Cache;

/**
 * Fuente única de verdad para la tasa de cambio y las conversiones.
 *
 * Convención del sistema:
 *  - Todos los montos monetarios se ALMACENAN en dólares (USD).
 *  - La tasa representa "bolívares por dólar" (Bs/USD).
 *  - El monto en Bs de cualquier valor es: USD * tasa.
 */
class CurrencyService
{
    /**
     * Memo de la tasa por request para evitar N+1 al pintar catálogos.
     * false = aún no resuelta; null = resuelta pero no hay tasa.
     */
    private float|null|false $memo = false;

    /**
     * Tasa activa (Bs por USD) resolviendo BD -> caché. null si no hay ninguna.
     */
    public function activeRate(): ?float
    {
        if ($this->memo !== false) {
            return $this->memo;
        }

        try {
            $rate = ExchangeRate::query()
                ->where('is_active', true)
                ->latest('date')
                ->value('rate');

            if ($rate === null) {
                $rate = Cache::get('exchange_rate');
            }
        } catch (\Throwable) {
            // BD o caché no disponibles (instalación nueva, migraciones pendientes).
            return null;
        }

        return $this->memo = $this->normalize($rate);
    }

    /**
     * Olvida la tasa memorizada (tras actualizarla en el mismo proceso).
     */
    public function forget(): void
    {
        $this->memo = false;
    }

    /**
     * Tasa activa normalizada, o el valor por defecto si no hay tasa válida.
     */
    public function rateOr(float $default = 1.0): float
    {
        $rate = $this->activeRate();

        return $rate && $rate > 0 ? $rate : $default;
    }

    /**
     * Convierte un monto en USD a Bs usando la tasa dada (o la activa).
     */
    public function toBs(float|int|string|null $usd, ?float $rate = null): float
    {
        $rate = $rate ?? $this->rateOr();

        return round((float) $usd * $rate, 2);
    }

    /**
     * Convierte un monto en Bs a USD usando la tasa dada (o la activa).
     */
    public function toUsd(float|int|string|null $bs, ?float $rate = null): float
    {
        $rate = $rate ?? $this->rateOr();

        if ($rate <= 0) {
            return 0.0;
        }

        return round((float) $bs / $rate, 2);
    }

    /**
     * Normaliza un valor de tasa que puede venir con coma decimal o como string.
     */
    private function normalize(float|int|string|null $rate): ?float
    {
        if ($rate === null || $rate === '') {
            return null;
        }

        return (float) str_replace(',', '.', (string) $rate);
    }
}
