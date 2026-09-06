@php
    $navBase = $navBase ?? '';
    $user = auth()->user();
    $isClient = $user && $user->hasRole('client');
    $isAdmin = $user && $user->hasRole('admin');

    if ($isClient) {
        $nav = [
            ['CATÁLOGO', route('storefront.catalog')],
            ['MIS COMPRAS', route('client.purchases')],
            ['MIS FACTURAS', route('client.invoices')],
            ['NOSOTROS', route('nosotros')],
            ['VISÍTANOS', route('contacto')],
        ];
    } else {
        $nav = [
            ['TIENDA', $navBase . '#productos'],
            ['DESTACADO', $navBase . '#destacado'],
            ['COLECCIÓN', $navBase . '#album-coleccion'],
            ['CATEGORÍAS', $navBase . '#categorias'],
            ['NOSOTROS', route('nosotros')],
            ['VISÍTANOS', route('contacto')],
        ];
    }
    $split = config('site.brand.name_split');
@endphp

<header class="site-header">
    <div class="site-header__inner">
        <a href="{{ route('storefront') }}" class="brand-logo" aria-label="{{ config('site.brand.name') }} — inicio">
            <span class="brand-logo__badge" aria-hidden="true">SL</span>
            <span class="brand-logo__text">
                <span class="brand-logo__sex">{{ $split[0] }}</span><span class="brand-logo__landia">{{ $split[1] }}</span>
            </span>
        </a>

        <nav class="site-header__nav" aria-label="Navegación principal">
            @foreach ($nav as [$label, $href])
                <a href="{{ $href }}">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="site-header__actions">
            @if ($isAdmin)
                <a class="site-header__link" href="{{ route('dashboard') }}">
                    <span class="site-header__link--label">MI PANEL</span>
                </a>
            @elseif ($isClient)
                <div class="site-header__account" x-data="{ open: false }" @keydown.escape="open = false">
                    <button type="button" class="site-header__link" @click="open = !open" :aria-expanded="open" aria-label="Menú de cuenta">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="site-header__link--label">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::words($user->name, 1, '')) }}</span>
                    </button>
                    <div class="site-header__menu-pop" x-show="open" x-transition x-cloak @click.outside="open = false">
                        <a href="{{ route('client.profile') }}">Mi perfil</a>
                        <a href="{{ route('client.purchases') }}">Mis compras</a>
                        <a href="{{ route('client.invoices') }}">Mis facturas</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" onclick="try{localStorage.removeItem('client_shopping_cart')}catch(e){}">Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            @else
                <a class="site-header__link" href="{{ route('login') }}">
                    <span class="site-header__link--label">INGRESAR</span>
                </a>
            @endif

            <button type="button" class="site-header__link site-header__bag" @click="cartOpen = true" aria-label="Abrir carrito">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span class="site-header__link--label">CARRITO</span>
                <span class="site-header__bag-count" x-show="totalItemsCount() > 0" x-text="totalItemsCount()" x-cloak></span>
            </button>

            <button type="button" class="site-header__menu" data-menu-open aria-label="Abrir menú">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>
        </div>
    </div>
</header>

<div class="mobile-menu" id="mobile-menu" aria-label="Menú móvil">
    <button class="mobile-menu__close" data-menu-close aria-label="Cerrar menú">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
            <path stroke-linecap="round" d="M6 6l12 12M6 18L18 6" />
        </svg>
    </button>
    <nav aria-label="Navegación móvil">
        @foreach ($nav as $i => [$label, $href])
            <a href="{{ $href }}"><span>0{{ $i + 1 }}</span>{{ $label }}</a>
        @endforeach
        @if ($isClient)
            <a href="{{ route('client.profile') }}"><span>0{{ count($nav) + 1 }}</span>MI PERFIL</a>
        @endif
    </nav>
    <p>{{ config('site.brand.claim') }}</p>
</div>
