@php $g = ($gallery ?? collect())->values(); @endphp
<section class="couples section-pad section-pad--large" aria-labelledby="couples-title">
    <div class="couples__art couples__art--card reveal">
        <x-sl.product-media :product="$g->get(3) ?? $g->get(0) ?? null" class="couples__img" alt="Colección para parejas {{ config('site.brand.name') }}" />
        <div class="couples__sub-card">
            <x-sl.product-media :product="$g->get(4) ?? $g->get(1) ?? null" class="couples__img-mini" alt="Accesorios para parejas" />
        </div>
    </div>
    <div class="couples__copy reveal">
        <p class="eyebrow">CONEXIÓN &amp; CÓMPLICES</p>
        <h2 id="couples-title">¿Mejor<br><em>juntos?</em></h2>
        <p>A veces un pequeño detalle transforma la dinámica por completo. Explora juguetes con control remoto, accesorios suaves y lubricantes diseñados para dos.</p>
        <a class="arrow-link" href="#productos">VER COLECCIÓN PARA DOS
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
        </a>
    </div>
</section>
