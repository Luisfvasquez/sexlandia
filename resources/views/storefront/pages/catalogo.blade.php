@php $navBase = route('storefront'); @endphp

@extends('storefront.layout')

@section('title', 'Catálogo · ' . config('site.brand.name'))
@section('meta_description', 'Explora el catálogo completo de ' . config('site.brand.name') . ': succionadores, vibradores, lubricantes y más, con stock verificado en ' . config('site.location.city') . '.')

@section('content')
    @php
        $rate = $exchangeRate ? (float) str_replace(',', '.', $exchangeRate) : 1;
        if ($rate <= 0) { $rate = 1; }
        $wa = config('site.contact.whatsapp');
        $activeCat = request('category', 'all');
    @endphp

    <section class="account section-pad" style="padding-top:120px;">
        <div class="account__intro reveal">
            <p class="eyebrow">CATÁLOGO COMPLETO · STOCK VERIFICADO</p>
            <h1>Todo lo disponible<br><em>hoy en {{ config('site.location.city') }}.</em></h1>
        </div>

        {{-- Filtros --}}
        <form method="GET" action="{{ route('storefront.catalog') }}" class="catalog-filters reveal">
            <div class="catalog-filters__search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o marca…"
                    aria-label="Buscar productos">
                @if ($activeCat !== 'all')
                    <input type="hidden" name="category" value="{{ $activeCat }}">
                @endif
                <button type="submit">Buscar</button>
            </div>
            <div class="catalog-filters__cats">
                <a href="{{ route('storefront.catalog', array_filter(['search' => request('search')])) }}"
                   @class(['catalog-chip', 'is-active' => $activeCat === 'all'])>Todas</a>
                @foreach ($categories as $category)
                    <a href="{{ route('storefront.catalog', array_filter(['search' => request('search'), 'category' => $category->id])) }}"
                       @class(['catalog-chip', 'is-active' => (string) $activeCat === (string) $category->id])>{{ $category->name }}</a>
                @endforeach
            </div>
        </form>

        @if ($products->count())
            <div class="catalog-grid">
                @foreach ($products as $product)
                    @php
                        $usd = $product->display_price;
                        $bs = $product->display_price * $rate;
                        $trackStock = $product->track_inventory;
                        $stock = $product->inventory->stock ?? 0;
                        $available = ! $trackStock || $product->allow_negative_stock || $stock > 0;
                        $stockLabel = ! $trackStock
                            ? 'DISPONIBLE'
                            : ($stock > 0
                                ? ($product->unit_type === 'gram'
                                    ? number_format($stock / 1000, 2, ',', '.') . ' KG EN STOCK'
                                    : ((int) $stock) . ' EN STOCK')
                                : 'AGOTADO');
                        $waText = rawurlencode('Hola ' . config('site.brand.name') . ', me interesa: ' . $product->name);
                    @endphp
                    <article class="product reveal" style="--reveal-delay: {{ ($loop->index % 9) * 60 }}ms">
                        <div class="product__media">
                            <x-sl.product-media :product="$product" class="product-card-img" />
                            <span class="product__sheen" aria-hidden="true"></span>
                            @if ($product->category)
                                <span class="product__badge">{{ \Illuminate\Support\Str::upper($product->category->name) }}</span>
                            @endif
                        </div>
                        <div class="product__meta">
                            <h3>{{ $product->name }}</h3>
                            <span class="product__price">${{ number_format($usd, 2, ',', '.') }}</span>
                            @if ($product->description)
                                <p class="product__tag">{{ \Illuminate\Support\Str::limit($product->description, 78) }}</p>
                            @endif
                            <p class="product__stock">● {{ $stockLabel }}@if ($exchangeRate) · {{ number_format($bs, 2, ',', '.') }} Bs @endif</p>
                            <div class="product__actions" x-data="{ qty: 1 }">
                                @if ($available)
                                    <div class="qty-stepper">
                                        <button type="button" @click="qty = Math.max(1, qty - 1)" aria-label="Restar">&minus;</button>
                                        <input type="text" x-model="qty" inputmode="numeric" aria-label="Cantidad de {{ $product->name }}">
                                        <button type="button" @click="qty++" aria-label="Sumar">+</button>
                                    </div>
                                    <button type="button" class="btn-add" @click="addToCart(@js($product), qty)">Añadir</button>
                                @else
                                    <button type="button" class="btn-add" disabled>Agotado</button>
                                @endif
                                <a class="wa-btn wa-btn--ghost" href="https://wa.me/{{ $wa }}?text={{ $waText }}"
                                    target="_blank" rel="noopener" aria-label="Consultar {{ $product->name }} por WhatsApp">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 00-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1012 2zm0 2a8 8 0 11-4.2 14.8l-.3-.2-2.8.8.8-2.7-.2-.3A8 8 0 0112 4z"/></svg>
                                    WhatsApp
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="catalog-pagination reveal">
                {{ $products->onEachSide(1)->links() }}
            </div>
        @else
            <p class="catalog-empty reveal">No encontramos productos con esos filtros. Prueba con otra búsqueda o
                <a href="{{ route('storefront.catalog') }}">ver todo el catálogo</a>.</p>
        @endif
    </section>
@endsection
