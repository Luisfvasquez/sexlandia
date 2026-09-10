<?php

use Illuminate\Support\Str;

use function Pest\Laravel\get;

/*
| Humo + invariantes responsive del storefront (enfoque móvil).
| Las páginas públicas deben renderizar y declarar el viewport; los títulos
| de cabecera nunca deben quedar fijados a un tamaño de escritorio sin una
| escala menor para teléfonos.
*/

it('renders public storefront pages with a mobile viewport', function (string $route) {
    get(route($route))
        ->assertOk()
        ->assertSee('width=device-width, initial-scale=1', false);
})->with([
    'storefront',
    'storefront.catalog',
    'nosotros',
    'contacto',
]);

it('teleports the mobile menu out of the backdrop-filtered header so it can open full-screen', function (string $route) {
    $html = get(route($route))->assertOk()->getContent();

    // El menú móvil debe estar dentro de un <template x-teleport="body"> …
    expect($html)->toContain('x-teleport="body"');

    // … y su marcado (con los enlaces de navegación) debe seguir presente.
    $menu = Str::after($html, 'x-show="mobileMenuOpen"');
    expect($menu)
        ->toContain('NOSOTROS')
        ->toContain('VISÍTANOS');
})->with(['storefront', 'storefront.catalog', 'nosotros', 'contacto']);

it('never pins a storefront heading to a desktop-only size without a phone scale', function () {
    $files = glob(dirname(__DIR__, 2).'/resources/views/storefront/{*,*/*,*/*/*}.blade.php', GLOB_BRACE);

    /** Un token grande (text-6xl+ o text-[Xrem]) sin prefijo de breakpoint (sm:/md:/lg:/xl:/min-[...]:). */
    $rawBig = '/(?<![\w:\[-])(?:text-(?:6xl|7xl|8xl|9xl)|text-\[[0-9.]+rem\])\b/';
    /** Un escalón chico presente en la misma clase. */
    $smallBase = '/(?<![\w:-])text-(?:xs|sm|base|lg|xl|2xl|3xl|4xl|5xl)\b/';

    $offenders = [];

    foreach ($files as $file) {
        foreach (file($file) as $i => $line) {
            if (preg_match($rawBig, $line) && ! preg_match($smallBase, $line)) {
                $offenders[] = basename($file).':'.($i + 1).'  '.trim($line);
            }
        }
    }

    expect($offenders)->toBe([]);
});
