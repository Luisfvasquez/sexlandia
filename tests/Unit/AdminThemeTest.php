<?php

/*
| Las vistas admin deben usar la paleta oscura SEXLANDIA (misma que el
| storefront): fondo `ink`, superficies `chocolate`, acento `wine`, texto
| `cream`/`bone`. Este test bloquea la reintroducción de la paleta clara
| genérica de Tailwind (`gray`, `blue`, `indigo`) en esas vistas.
*/

$adminViewFiles = function (): array {
    $dir = dirname(__DIR__, 2).'/resources/views';

    return array_merge(
        glob("{$dir}/admin/**/*.blade.php") ?: [],
        glob("{$dir}/admin/*.blade.php") ?: [],
        // Componentes Livewire embebidos en el panel admin.
        glob("{$dir}/livewire/*.blade.php") ?: [],
    );
};

it('swaps the admin layout body to the dark SEXLANDIA canvas', function () {
    $layout = file_get_contents(dirname(__DIR__, 2).'/resources/views/admin/layouts/app.blade.php');

    expect($layout)
        ->toContain('bg-ink text-cream')
        ->not->toContain('bg-gray-100');
});

it('themes the admin shell partials with wine + chocolate', function () {
    $base = dirname(__DIR__, 2).'/resources/views/admin/components';

    foreach (['sidebar', 'navbar'] as $partial) {
        $html = file_get_contents("{$base}/{$partial}.blade.php");

        expect($html)
            ->toContain('bg-chocolate')
            ->toContain('text-cream');
    }
});

it('has no leftover generic light-theme colour utilities in admin views', function () use ($adminViewFiles) {
    $legacy = '/\b(?:bg|text|border|ring|divide|from|to|via|placeholder|shadow)-(?:gray|slate|zinc|neutral|stone|blue|indigo|violet|purple)-(?:50|100|200|300|400|500|600|700|800|900|950)\b/';

    $offenders = [];

    foreach ($adminViewFiles() as $file) {
        if (! is_file($file)) {
            continue;
        }

        if (preg_match_all($legacy, file_get_contents($file), $m)) {
            $offenders[basename($file)] = array_unique($m[0]);
        }
    }

    expect($offenders)->toBe([]);
});
