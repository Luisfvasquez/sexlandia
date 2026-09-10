import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/storefront.css',
            ],
            refresh: true,
        }),
    ],

    build: {
        // Evita que el minificador reescriba `@media (min-width: …)` a la
        // sintaxis de rango `@media (width >= …)` (Baseline 2023): rompería el
        // layout responsive en iOS Safari < 16.4 y navegadores Android viejos,
        // habituales en el público objetivo.
        cssTarget: ['chrome87', 'safari13.1', 'firefox78', 'edge88'],
    },
});
