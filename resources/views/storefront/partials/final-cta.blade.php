@php $g = ($gallery ?? collect())->values(); @endphp
<section class="final-cta section-pad section-pad--large" aria-labelledby="final-cta-title">
    <div class="final-cta__art reveal">
        <x-sl.product-media :product="$g->get(0) ?? null" class="final-cta__img-main" alt="Colección {{ config('site.brand.name') }}" />
        <x-sl.product-media :product="$g->get(1) ?? null" class="final-cta__img-sub" alt="Producto destacado {{ config('site.brand.name') }}" />
    </div>
    <div class="final-cta__copy reveal">
        <p class="eyebrow eyebrow--light">EXPERIENCIA {{ \Illuminate\Support\Str::upper(config('site.brand.name')) }}</p>
        <h2 id="final-cta-title">¿Viste algo<br><em>que te gustó?</em></h2>
        <p>NO LE DES TANTAS VUELTAS. ESCRÍBENOS Y TE ASESORAMOS DE INMEDIATO, SIN PENA.</p>
        <a class="arrow-link arrow-link--inverse"
            href="https://wa.me/{{ config('site.contact.whatsapp') }}?text={{ rawurlencode('Hola ' . config('site.brand.name') . ', quisiera hacer un pedido') }}"
            target="_blank" rel="noopener">
            PEDIR POR WHATSAPP
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
        </a>
    </div>
</section>
