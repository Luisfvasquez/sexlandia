@php
    $active = $active ?? '';
    $tabs = [
        'catalog'  => ['Catálogo', route('storefront.catalog')],
        'purchases' => ['Mis compras', route('client.purchases')],
        'invoices' => ['Mis facturas', route('client.invoices')],
        'profile'  => ['Mi perfil', route('client.profile')],
    ];
@endphp
<nav class="account-nav" aria-label="Navegación de cuenta">
    <div class="account-nav__inner">
        @foreach ($tabs as $key => [$label, $href])
            <a href="{{ $href }}" @class(['account-nav__tab', 'is-active' => $active === $key])
               @if ($active === $key) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
    </div>
</nav>
