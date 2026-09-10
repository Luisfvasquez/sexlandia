<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * robots.txt generado dinámicamente para que la línea Sitemap: siempre
     * apunte al dominio público real (config/site.php → url), sin depender de
     * un archivo estático que hay que editar por entorno.
     */
    public function __invoke(): Response
    {
        $base = rtrim(config('site.url'), '/');

        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /client',
            'Disallow: /delivery',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /password',
            'Disallow: /profile',
            // Resultados de búsqueda interna: sin valor para indexar.
            'Disallow: /*?*search=',
            '',
            'Sitemap: '.$base.'/sitemap.xml',
            '',
        ];

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
