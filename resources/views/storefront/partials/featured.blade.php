@php
    $rate = $exchangeRate ? (float) str_replace(',', '.', $exchangeRate) : 1;
    if ($rate <= 0) { $rate = 1; }
    $wa = config('site.contact.whatsapp');
@endphp
@if ($featured ?? null)
@php
    $imgs = $featured->images;
    $usd = $featured->display_price;
    $bs = $featured->display_price * $rate;
    $inStock = ! $featured->track_inventory
        || $featured->allow_negative_stock
        || ($featured->inventory && $featured->inventory->stock > 0);
    $waText = rawurlencode('Hola ' . config('site.brand.name') . ', quiero consultar por: ' . $featured->name);
@endphp
<section id="destacado" class="product-story section-pad section-pad--large" aria-labelledby="destacado-title">
    <div class="product-story__headline reveal">
        <p class="eyebrow">DESTACADO DE TEMPORADA</p>
        <h2 id="destacado-title">Diseñado<br>para <em>desearlo.</em></h2>
    </div>

    <div class="product-story__art" data-featured-gallery>
        <div class="product-story__img-box">
            <x-sl.product-media :product="$featured" size="full" class="product-story__img" data-featured-main alt="{{ $featured->name }}" />
        </div>
    </div>

    <div class="product-story__details reveal">
        <p class="product-story__index">{{ config('site.brand.name') }} selección · {{ $featured->category->name ?? 'Colección' }}</p>
        <h3>{{ $featured->name }}</h3>
        <p class="product-story__desc">{{ $featured->description ?: 'Una pieza destacada de nuestra colección oficial, disponible hoy en tienda.' }}</p>

        @if ($imgs->count() > 1)
            <div class="product-story__thumbs" role="group" aria-label="Fotos del producto">
                @foreach ($imgs->take(4) as $img)
                    <button type="button" data-featured-thumb data-src="{{ $img->url }}"
                        class="{{ $loop->first ? 'is-active' : '' }}" aria-label="Ver foto {{ $loop->iteration }}">
                        <img src="{{ $img->thumb_url }}" alt="" loading="lazy" width="52" height="52">
                    </button>
                @endforeach
            </div>
        @endif

        <div class="product-story__price">
            <span>${{ number_format($usd, 2, ',', '.') }}</span>
            @if ($exchangeRate)
                <span class="stock-dot">{{ number_format($bs, 2, ',', '.') }} Bs</span>
            @endif
            <span class="stock-dot">{{ $inStock ? '● EN STOCK EN TIENDA' : '● CONSULTAR DISPONIBILIDAD' }}</span>
        </div>

        <div class="product-story__cta-row" x-data="{ qty: 1 }">
            @if ($inStock)
                <div class="qty-stepper">
                    <button type="button" @click="qty = Math.max(1, qty - 1)" aria-label="Restar">&minus;</button>
                    <input type="text" x-model="qty" inputmode="numeric" aria-label="Cantidad">
                    <button type="button" @click="qty++" aria-label="Sumar">+</button>
                </div>
                <button type="button" class="btn-add" @click="addToCart(@js($featured), qty)">Añadir al carrito</button>
            @endif
            <a class="wa-btn" href="https://wa.me/{{ $wa }}?text={{ $waText }}" target="_blank" rel="noopener">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 00-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1012 2zm0 2a8 8 0 11-4.2 14.8l-.3-.2-2.8.8.8-2.7-.2-.3A8 8 0 0112 4zm4.5 9.9c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1l-.7.9c-.1.2-.3.2-.5.1a6.5 6.5 0 01-1.9-1.2 7.2 7.2 0 01-1.3-1.7c-.1-.2 0-.4.1-.5l.4-.5.2-.4v-.4l-.8-1.8c-.2-.4-.4-.4-.5-.4h-.5a1 1 0 00-.7.3c-.3.3-1 .9-1 2.2s1 2.6 1.1 2.8c.2.2 2 3.1 4.9 4.3.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.4-.6 1.6-1.2.2-.6.2-1 .1-1.2z"/></svg>
                Consultar por WhatsApp
            </a>
        </div>
    </div>
</section>
@endif
