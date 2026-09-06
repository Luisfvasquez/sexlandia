<?php

namespace App\Providers;

use App\Services\CurrencyService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CurrencyService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Se resuelve de forma perezosa y tolerante a fallos: si la caché o la BD
        // aún no están disponibles (instalación nueva, migraciones pendientes),
        // no debe romper toda la aplicación.
        //
        // $exchangeRate = bolívares por dólar (Bs/USD). Los precios se guardan en
        // USD y el equivalente en Bs se calcula como: valor_usd * $exchangeRate.
        View::composer('*', function ($view) {
            static $rate = false;

            if ($rate === false) {
                $rate = app(CurrencyService::class)->activeRate();
            }

            $view->with('exchangeRate', $rate);
        });
    }
}
