@php
    $siteSeo = config('site.seo');
    $brandName = config('site.brand.name');
    $siteUrl = rtrim(config('site.url'), '/');
    $pageTitle = trim($__env->yieldContent('title')) ?: $siteSeo['title'];
    $metaDescription = trim($__env->yieldContent('meta_description')) ?: $siteSeo['description'];
    // Canónica sobre el dominio público declarado (config/site.php → url), nunca
    // el host de desarrollo, y sin query string salvo que la página la fije.
    $canonical = trim($__env->yieldContent('canonical')) ?: $siteUrl.request()->getPathInfo();
    $robots = trim($__env->yieldContent('robots')) ?: 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1';

    $isRemote = fn (?string $p) => $p && \Illuminate\Support\Str::startsWith($p, ['http://', 'https://']);
    $toAbsolute = function (?string $path) use ($siteUrl, $isRemote) {
        if (! $path) {
            return null;
        }

        return $isRemote($path) ? $path : $siteUrl.'/'.ltrim($path, '/');
    };

    // Imagen social: la que fije la página → og_image de config → logo.
    // Si la ruta local no existe todavía (p. ej. falta subir og-image.jpg) se
    // cae al logo y no se declaran dimensiones fijas.
    $ogCandidate = trim($__env->yieldContent('og_image')) ?: ($siteSeo['og_image'] ?? null);
    $ogIsDesignated = $ogCandidate
        && ($isRemote($ogCandidate) || file_exists(public_path(ltrim($ogCandidate, '/'))));
    $ogImage = $toAbsolute($ogIsDesignated ? $ogCandidate : ($siteSeo['logo'] ?? null));
    $logoUrl = $toAbsolute($siteSeo['logo'] ?? null);
    $loc = config('site.location');
@endphp
<!DOCTYPE html>
<html lang="es-VE">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>document.documentElement.classList.add('js');</script>

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ $siteSeo['keywords'] }}">
    <meta name="author" content="{{ $siteSeo['author'] }}">
    <meta name="robots" content="{{ $robots }}">
    <meta name="googlebot" content="{{ $robots }}">
    <link rel="canonical" href="{{ $canonical }}">
    <link rel="alternate" hreflang="es-VE" href="{{ $canonical }}">
    <link rel="alternate" hreflang="es" href="{{ $canonical }}">
    <link rel="alternate" hreflang="x-default" href="{{ $canonical }}">
    <meta name="theme-color" content="#8d263d">
    <meta name="application-name" content="{{ $brandName }}">
    <meta name="apple-mobile-web-app-title" content="{{ $brandName }}">

    @if (! empty($siteSeo['google_site_verification']))
        <meta name="google-site-verification" content="{{ $siteSeo['google_site_verification'] }}">
    @endif
    @if (! empty($siteSeo['facebook_domain_verification']))
        <meta name="facebook-domain-verification" content="{{ $siteSeo['facebook_domain_verification'] }}">
    @endif

    {{-- Señales geográficas locales (Caracas) --}}
    <meta name="geo.region" content="{{ $siteSeo['geo_region'] ?? 'VE-A' }}">
    <meta name="geo.placename" content="{{ $siteSeo['geo_placename'] ?? $loc['city'] }}">
    <meta name="geo.position" content="{{ $loc['latitude'] }};{{ $loc['longitude'] }}">
    <meta name="ICBM" content="{{ $loc['latitude'] }}, {{ $loc['longitude'] }}">

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
        <meta property="og:image:secure_url" content="{{ $ogImage }}">
        <meta property="og:image:alt" content="{{ $brandName }} · Sex shop en {{ $loc['city'] }}">
        @if ($ogIsDesignated)
            <meta property="og:image:width" content="1200">
            <meta property="og:image:height" content="630">
        @endif
    @endif

    {{-- Twitter --}}
    <meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
    @if (! empty($siteSeo['twitter_site']))
        <meta name="twitter:site" content="{{ $siteSeo['twitter_site'] }}">
    @endif
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    @if ($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endif

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/jpeg" href="{{ asset('sexlandia/logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('sexlandia/logo.jpg') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/sexlandia.css') }}">
    @stack('head')

    {{-- Tailwind compilado (Vite). Sustituye al antiguo cdn.tailwindcss.com;
         el tema vive en tailwind.storefront.config.js. --}}
    @vite('resources/css/storefront.css')

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
    <script defer src="{{ asset('js/sexlandia.js') }}"></script>

    @stack('jsonld')
</head>
<body class="sexlandia-shell bg-ink text-white antialiased selection:bg-rose-500/30 selection:text-white font-sans">
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
