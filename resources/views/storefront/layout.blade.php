@php
    $siteSeo = config('site.seo');
    $brandName = config('site.brand.name');
    $pageTitle = trim($__env->yieldContent('title')) ?: $siteSeo['title'];
    $metaDescription = trim($__env->yieldContent('meta_description')) ?: $siteSeo['description'];
    $canonical = trim($__env->yieldContent('canonical')) ?: url()->current();
    $ogImage = trim($__env->yieldContent('og_image')) ?: ($siteSeo['og_image'] ?? null);
    if ($ogImage && ! \Illuminate\Support\Str::startsWith($ogImage, ['http://', 'https://'])) {
        $ogImage = url($ogImage);
    }
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>document.documentElement.classList.add('js');</script>

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ $siteSeo['keywords'] }}">
    <meta name="author" content="{{ $siteSeo['author'] }}">
    <meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1">
    <link rel="canonical" href="{{ $canonical }}">
    <meta name="theme-color" content="#8d263d">

    {{-- Contenido para adultos: etiqueta RTA + rating estándar --}}
    <meta name="rating" content="adult">
    <meta name="rating" content="RTA-5042-1996-1400-1577-RTA">

    {{-- Open Graph --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $brandName }}">
    <meta property="og:locale" content="{{ $siteSeo['locale'] }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="og:image:alt" content="{{ $brandName }}">
    @endif

    {{-- Twitter --}}
    <meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    @if ($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endif

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap">

    <link rel="stylesheet" href="{{ asset('css/sexlandia.css') }}">
    @stack('head')

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cream: '#f3eee8', wine: '#8d263d', 'wine-dark': '#5c1424',
                        chocolate: '#2b1c18', blush: '#d7aaa6', powder: '#e7d1ce',
                        butter: '#e8d5a6', ink: '#171515', bone: '#fbf8f3',
                        /* remapeo: las vistas de cliente heredadas usan la escala "indigo" → vino SEXLANDIA */
                        indigo: {
                            50: '#f7edef', 100: '#eed7dc', 200: '#e0b8c0', 300: '#cd8f9c',
                            400: '#b25f72', 500: '#9c3a4f', 600: '#8d263d',
                            700: '#5c1424', 800: '#4a1620', 900: '#3a141b', 950: '#250b11',
                        },
                    },
                    fontFamily: {
                        sans: ['"DM Sans"', 'Figtree', 'sans-serif'],
                        serif: ['"Instrument Serif"', 'Georgia', 'serif'],
                    },
                },
            },
        };
    </script>
    <style>
        /* Helpers usados por las vistas de cliente reubicadas en el shell del storefront */
        [x-cloak] { display: none !important; }
        .scrollbar-none::-webkit-scrollbar { display: none; }
        .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
        .scrollbar-thin::-webkit-scrollbar { width: 6px; height: 6px; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(23,21,21,.18); border-radius: 999px; }
        .animate-spin-slow { animation: sl-spin 8s linear infinite; }
        @keyframes sl-spin { from { transform: rotate(0); } to { transform: rotate(360deg); } }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
    <script defer src="{{ asset('js/sexlandia.js') }}"></script>

    @stack('jsonld')
</head>
<body class="sexlandia-shell">
    <a href="#contenido" class="skip-link">Saltar al contenido</a>
    <div class="scroll-progress" aria-hidden="true"></div>

    <div x-data="storefrontCart()">
        @includeWhen(config('site.age_gate'), 'storefront.partials.age-gate')
        @include('storefront.partials.header')

        <main id="contenido">
            @yield('content')
        </main>

        @include('storefront.partials.footer')

        @include('storefront.partials.cart-drawer')
        @include('storefront.partials.auth-modal')
        @include('storefront.partials.cart-script')
    </div>

    @stack('scripts')
</body>
</html>
