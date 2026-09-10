@php $navBase = route('storefront'); @endphp

@extends('storefront.layout')

@php
    $rate = $exchangeRate ? (float) str_replace(',', '.', $exchangeRate) : 1;
    if ($rate <= 0) {
        $rate = 1;
    }
    $wa = config('site.contact.whatsapp');

    $usd = $product->display_price;
    $bs = $usd * $rate;

    $trackStock = $product->track_inventory;
    $stock = $product->inventory->stock ?? 0;
    $available = ! $trackStock || $product->allow_negative_stock || $stock > 0;
    $stockLabel = ! $trackStock
        ? 'DISPONIBLE'
        : ($stock > 0
            ? ($product->unit_type === 'gram'
                ? number_format($stock / 1000, 2, ',', '.') . ' KG DISPONIBLES'
                : ((int) $stock) . ' DISPONIBLES')
            : 'AGOTADO');

    // La marca solo se muestra si aporta información (no repite el nombre de la tienda).
    $showBrand = $product->brand
        && strcasecmp(trim($product->brand), trim(config('site.brand.name'))) !== 0;

    $images = $product->images;
    $mainSrc = $images->first()?->variantUrl('full');
    $ogImg = $images->first()?->variantUrl('full');

    $waText = rawurlencode(
        'Hola ' . config('site.brand.name') . ', me interesa este producto: ' .
        $product->name . ' → ' . $product->public_url,
    );

    $metaDesc = $product->description
        ? \Illuminate\Support\Str::limit(strip_tags($product->description), 155)
        : ('Consulta disponibilidad y precio de ' . $product->name . ' en ' . config('site.brand.name') . '.');

    $extraSchemas = [
        array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $metaDesc,
            'sku' => $product->sku ?: null,
            'brand' => $showBrand ? ['@type' => 'Brand', 'name' => $product->brand] : null,
            'category' => $product->category->name ?? null,
            'image' => $ogImg ?: null,
            'url' => $product->public_url,
            'offers' => [
                '@type' => 'Offer',
                'price' => number_format($usd, 2, '.', ''),
                'priceCurrency' => 'USD',
                'availability' => $available ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => $product->public_url,
            ],
        ]),
        [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => route('storefront')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Catálogo', 'item' => route('storefront.catalog')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $product->name, 'item' => $product->public_url],
            ],
        ],
    ];
@endphp

@section('title', $product->name . ($showBrand ? ' · ' . $product->brand : '') . ' · ' . config('site.brand.name'))
@section('meta_description', $metaDesc)
@section('canonical', $product->public_url)
@section('og_type', 'product')
@if ($ogImg)
    @section('og_image', $ogImg)
@endif

@include('storefront.partials.seo-jsonld', ['extraSchemas' => $extraSchemas])

@section('content')
    <section class="relative bg-ink border-t border-white/5 overflow-hidden pt-28 sm:pt-32 pb-24 lg:pb-32">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_rgba(141,38,61,0.08),_transparent_60%)] pointer-events-none"></div>

        <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8">

            {{-- Migas / volver --}}
            <nav class="mb-8 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] font-bold tracking-[0.15em] uppercase text-neutral-500" aria-label="Ruta de navegación">
                <a href="{{ route('storefront.catalog') }}" class="hover:text-rose-400 transition-colors">Catálogo</a>
                <span aria-hidden="true">/</span>
                @if ($product->category)
                    <a href="{{ route('storefront.catalog', ['category' => $product->category->id]) }}" class="hover:text-rose-400 transition-colors">{{ $product->category->name }}</a>
                    <span aria-hidden="true">/</span>
                @endif
                <span class="text-neutral-300 normal-case tracking-normal font-medium">{{ $product->name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-start" x-data="{ current: @js($mainSrc) }">

                {{-- Galería --}}
                <div class="animate-slide-up-fade">
                    <div class="relative w-full aspect-[4/5] bg-black overflow-hidden border border-white/10">
                        @if ($images->count())
                            <img :src="current" alt="{{ $product->name }}" class="w-full h-full object-cover" width="1600" height="2000" fetchpriority="high">
                        @else
                            <x-sl.product-media :product="$product" size="full" :eager="true" class="w-full h-full object-cover" />
                        @endif
                        @if ($product->category)
                            <span class="absolute top-4 left-4 z-10 text-[9px] text-white tracking-[0.2em] uppercase bg-black/60 backdrop-blur-md px-3 py-1.5 border border-white/10">
                                {{ \Illuminate\Support\Str::upper($product->category->name) }}
                            </span>
                        @endif
                    </div>

                    @if ($images->count() > 1)
                        <div class="mt-4 flex flex-wrap gap-3" role="group" aria-label="Fotos de {{ $product->name }}">
                            @foreach ($images as $img)
                                @php $full = $img->variantUrl('full'); @endphp
                                <button type="button" @click="current = @js($full)"
                                    :class="current === @js($full) ? 'border-rose-500' : 'border-white/10 hover:border-white/40'"
                                    class="relative w-16 h-20 sm:w-20 sm:h-24 shrink-0 overflow-hidden border transition-colors"
                                    aria-label="Ver foto {{ $loop->iteration }}">
                                    <img src="{{ $img->variantUrl('thumb') }}" alt="{{ $product->name }} — foto {{ $loop->iteration }}" loading="lazy" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Información --}}
                <div class="flex flex-col animate-slide-up-fade [animation-delay:150ms]">
                    @if ($showBrand)
                        <p class="text-rose-500 text-[10px] tracking-[0.25em] uppercase font-bold mb-3">{{ $product->brand }}</p>
                    @elseif ($product->category)
                        <p class="text-rose-500 text-[10px] tracking-[0.25em] uppercase font-bold mb-3">{{ $product->category->name }}</p>
                    @endif

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl leading-[1.1] text-white font-bold tracking-tight">
                        {{ $product->name }}
                    </h1>

                    <div class="mt-6 flex items-end justify-between gap-4 border-y border-white/10 py-5">
                        <div class="flex flex-col">
                            <span class="text-4xl font-light text-white tracking-tight">${{ number_format($usd, 2, ',', '.') }}</span>
                            @if ($exchangeRate)
                                <span class="text-xs text-neutral-500 mt-1 font-mono tracking-widest">{{ number_format($bs, 2, ',', '.') }} Bs</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 text-[10px] tracking-[0.15em] uppercase font-bold {{ $available ? 'text-green-500' : 'text-neutral-500' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $available ? 'bg-green-500 animate-pulse' : 'bg-neutral-500' }}"></span>
                            {{ $stockLabel }}
                        </div>
                    </div>

                    @if ($product->description)
                        <p class="mt-6 text-neutral-400 font-light text-sm sm:text-base leading-relaxed whitespace-pre-line">
                            {{ $product->description }}
                        </p>
                    @endif

                    {{-- Acciones --}}
                    <div class="mt-8 space-y-4" x-data="{ qty: 1 }">
                        @if ($available)
                            <div class="flex items-stretch gap-3 h-12">
                                <div class="flex items-center border border-white/20 bg-black/40 text-white w-28 shrink-0">
                                    <button type="button" class="px-4 h-full text-neutral-400 hover:text-rose-500 transition-colors" @click="qty = Math.max(1, qty - 1)" aria-label="Restar">&minus;</button>
                                    <input type="text" x-model="qty" inputmode="numeric" class="w-full text-center bg-transparent border-none text-sm p-0 focus:ring-0 text-white font-mono" aria-label="Cantidad">
                                    <button type="button" class="px-4 h-full text-neutral-400 hover:text-rose-500 transition-colors" @click="qty++" aria-label="Sumar">+</button>
                                </div>
                                <button type="button" class="flex-1 bg-white text-black font-medium tracking-[0.15em] text-xs uppercase hover:bg-rose-600 hover:text-white transition-all duration-300" @click="addToCart(@js($product->toCartPayload()), qty)">
                                    Añadir al carrito
                                </button>
                            </div>
                        @else
                            <button type="button" class="w-full h-12 bg-neutral-900 text-neutral-600 font-medium tracking-[0.15em] text-xs uppercase cursor-not-allowed border border-white/5" disabled>
                                Agotado — consúltanos disponibilidad
                            </button>
                        @endif

                        <a class="flex items-center justify-center gap-3 w-full h-12 border border-white/20 text-neutral-300 font-medium tracking-[0.15em] text-xs uppercase hover:border-green-500 hover:text-green-500 hover:bg-green-500/10 transition-all duration-300"
                            href="https://wa.me/{{ $wa }}?text={{ $waText }}" target="_blank" rel="noopener">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 00-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1012 2zm0 2a8 8 0 11-4.2 14.8l-.3-.2-2.8.8.8-2.7-.2-.3A8 8 0 0112 4z"/></svg>
                            Consultar por WhatsApp
                        </a>
                    </div>

                    <dl class="mt-8 grid grid-cols-2 gap-x-6 gap-y-4 border-t border-white/10 pt-6 text-sm">
                        @if ($product->sku)
                            <div>
                                <dt class="text-[10px] font-mono tracking-widest text-neutral-600 uppercase">SKU</dt>
                                <dd class="text-neutral-300 font-mono">{{ $product->sku }}</dd>
                            </div>
                        @endif
                        @if ($product->category)
                            <div>
                                <dt class="text-[10px] font-mono tracking-widest text-neutral-600 uppercase">Categoría</dt>
                                <dd class="text-neutral-300">{{ $product->category->name }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-[10px] font-mono tracking-widest text-neutral-600 uppercase">Unidad</dt>
                            <dd class="text-neutral-300">{{ $product->unit_type === 'gram' ? 'Por peso (Kg)' : 'Por unidad' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Productos similares --}}
            @if ($similar->count())
                <div class="mt-24 lg:mt-32">
                    <div class="flex items-end justify-between gap-6 mb-12 animate-slide-up-fade">
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl leading-[1.05] text-white font-bold tracking-tight">
                            También te puede<br>
                            <em class="font-serif italic text-rose-500 font-normal">gustar.</em>
                        </h2>
                        <a href="{{ route('storefront.catalog') }}" class="hidden sm:inline-flex items-center gap-2 text-xs tracking-[0.2em] uppercase font-medium text-neutral-400 hover:text-rose-400 border-b border-rose-600/50 hover:border-rose-400 pb-1 transition-colors shrink-0">
                            Ver todo
                        </a>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                        @foreach ($similar as $s)
                            @php
                                $sUsd = $s->display_price;
                                $sStock = $s->inventory->stock ?? 0;
                                $sAvail = ! $s->track_inventory || $s->allow_negative_stock || $sStock > 0;
                            @endphp
                            <a href="{{ route('storefront.product', $s->slug) }}" class="group flex flex-col">
                                <div class="relative w-full aspect-[4/5] bg-black overflow-hidden border border-white/10 group-hover:border-rose-500/40 transition-colors duration-500">
                                    <div class="absolute inset-0 bg-ink/40 group-hover:bg-transparent transition-colors duration-700 pointer-events-none z-10"></div>
                                    <x-sl.product-media :product="$s" class="w-full h-full object-cover grayscale opacity-80 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700 ease-out" />
                                </div>
                                <h3 class="mt-3 text-sm sm:text-base text-white font-serif italic leading-tight group-hover:text-rose-400 transition-colors line-clamp-2">
                                    {{ $s->name }}
                                </h3>
                                <div class="mt-1 flex items-center justify-between">
                                    <span class="text-sm text-neutral-300 font-light">${{ number_format($sUsd, 2, ',', '.') }}</span>
                                    <span class="flex items-center gap-1.5 text-[8px] tracking-[0.15em] uppercase font-bold {{ $sAvail ? 'text-green-500' : 'text-neutral-500' }}">
                                        <span class="w-1 h-1 rounded-full {{ $sAvail ? 'bg-green-500' : 'bg-neutral-500' }}"></span>
                                        {{ $sAvail ? 'Disponible' : 'Agotado' }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
