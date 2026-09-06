@php
    $rate = $exchangeRate ? (float) str_replace(',', '.', $exchangeRate) : 1;
    if ($rate <= 0) { $rate = 1; }
    $wa = config('site.contact.whatsapp');
    $list = ($products ?? collect())->take(9);
@endphp
<section id="productos" class="products section-pad section-pad--large" aria-labelledby="productos-title">
    <div class="section-intro section-intro--products">
        <h2 id="productos-title" class="reveal">Cosas que seguro<br><em>te van a encantar.</em></h2>
        <div class="reveal">
            <p>No tienes que saber exactamente qué buscas. Explora nuestros productos reales disponibles hoy en {{ config('site.location.city') }}.</p>
        </div>
    </div>

    @if ($list->count())
        <div class="products__grid">
            @foreach ($list as $product)
                @php
                    $mod = $loop->index % 6;
                    $cls = $mod === 5 ? 0 : $mod + 1;
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
                <article class="product product--{{ $cls }} reveal" style="--reveal-delay: {{ $loop->index * 70 }}ms">
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
                            <p class="product__tag">{{ \Illuminate\Support\Str::limit($product->description, 70) }}</p>
                        @endif
                        <p class="product__stock">● {{ $stockLabel }}@if ($exchangeRate) · {{ number_format($bs, 2, ',', '.') }} Bs @endif</p>
                        <div class="product__actions" x-data="{ qty: {{ $product->unit_type === 'gram' ? '1' : '1' }} }">
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

        <div style="margin-top:60px;" class="reveal">
            <a class="arrow-link" href="{{ route('storefront.catalog') }}">
                VER TODO EL CATÁLOGO
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
            </a>
        </div>
    @else
        <p style="font-size:16px;color:rgba(23,21,21,.6);">Pronto publicaremos nuestro catálogo. Escríbenos por WhatsApp para conocer la disponibilidad actual.</p>
    @endif
</section>
