<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex,nofollow">

        <title>{{ config('app.name', 'SEXLANDIA') }} · Panel</title>

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap">
        <link rel="stylesheet" href="{{ asset('css/sexlandia.css') }}">

        <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            /* remapeo: las vistas heredadas usan la escala "indigo" → ahora vino SEXLANDIA */
                            indigo: {
                                50: '#f7edef', 100: '#eed7dc', 200: '#e0b8c0', 300: '#cd8f9c',
                                400: '#b25f72', 500: '#9c3a4f', 600: '#8d263d',
                                700: '#5c1424', 800: '#4a1620', 900: '#3a141b', 950: '#250b11',
                            },
                            wine: '#8d263d', 'wine-dark': '#5c1424', cream: '#f3eee8',
                            chocolate: '#2b1c18', blush: '#d7aaa6', bone: '#fbf8f3', ink: '#171515',
                        },
                        fontFamily: {
                            sans: ['"DM Sans"', 'Figtree', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                            serif: ['"Instrument Serif"', 'Georgia', 'serif'],
                        },
                    },
                },
            };
        </script>
        <style>
            body { background: #f3eee8; }
            [x-cloak] { display: none !important; }
            .scrollbar-none::-webkit-scrollbar { display: none; }
            .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
            .animate-spin-slow { animation: spin 8s linear infinite; }
            @keyframes spin { from { transform: rotate(0); } to { transform: rotate(360deg); } }
        </style>

        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased text-ink">
        <div class="min-h-screen bg-cream">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white border-b border-wine/10 shadow-sm">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
