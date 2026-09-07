@php
    $active = $active ?? '';
    $tabs = [
        'catalog'  => ['Catálogo', route('storefront.catalog')],
        'purchases' => ['Mis compras', route('client.purchases')],
        'invoices' => ['Mis facturas', route('client.invoices')],
        'profile'  => ['Mi perfil', route('client.profile')],
    ];
@endphp
<nav class="sticky top-20 z-30 mt-20 bg-ink/90 backdrop-blur-md border-b border-white/10" aria-label="Navegación de cuenta">
    <div class="max-w-7xl mx-auto flex gap-2 overflow-x-auto px-6 lg:px-8 py-3 scrollbar-none">
        @foreach ($tabs as $key => [$label, $href])
            <a href="{{ $href }}"
               @class([
                   'flex-shrink-0 px-4 py-2 rounded-full text-[11px] font-bold tracking-[0.1em] uppercase border transition-colors duration-200',
                   'bg-white text-black border-white' => $active === $key,
                   'text-neutral-400 border-transparent hover:text-rose-400 hover:bg-white/5' => $active !== $key,
               ])
               @if ($active === $key) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
    </div>
</nav>
