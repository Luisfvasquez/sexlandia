<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex,nofollow">

        <title>{{ config('app.name', 'SEXLANDIA') }}</title>

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
        <style>[x-cloak]{display:none!important}</style>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans text-ink antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-chocolate">
            <div>
                <a href="{{ url('/') }}" class="brand-logo brand-logo--inverse" aria-label="{{ config('site.brand.name', 'SEXLANDIA') }}">
                    <span class="brand-logo__badge" aria-hidden="true">SL</span>
                    <span class="brand-logo__text">
                        <span class="brand-logo__sex">SEX</span><span class="brand-logo__landia">Landia</span>
                    </span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-cream shadow-xl overflow-hidden sm:rounded-2xl border border-wine/10">
                {{ $slot }}
            </div>

            <p class="mt-6 text-[11px] tracking-widest text-cream/40 uppercase">Contenido para mayores de 18 años</p>
        </div>
    </body>
</html>
