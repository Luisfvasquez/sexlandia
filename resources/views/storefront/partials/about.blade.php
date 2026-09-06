@php
    $about = config('site.about');
    $g = ($gallery ?? collect())->values();
@endphp
<section id="nosotros" class="brand-story section-pad section-pad--large" aria-labelledby="nosotros-title">
    <div class="brand-collage">
        <div class="brand-collage__a">
            <x-sl.product-media :product="$g->get(0) ?? null" class="brand-collage__img" alt="{{ config('site.brand.name') }} boutique" />
        </div>
        <div class="brand-collage__item brand-collage__b">
            <x-sl.product-media :product="$g->get(1) ?? null" class="brand-collage__img" alt="Selección {{ config('site.brand.name') }}" />
        </div>
        <div class="brand-collage__item brand-collage__c">
            <x-sl.product-media :product="$g->get(2) ?? null" class="brand-collage__img" alt="Producto {{ config('site.brand.name') }}" />
        </div>
        <div class="brand-collage__item brand-collage__d">
            <x-sl.product-media :product="$g->get(5) ?? $g->get(3) ?? null" class="brand-collage__img" alt="Producto {{ config('site.brand.name') }}" />
        </div>
        <div class="brand-collage__title reveal">
            <h2 id="nosotros-title">{{ $about['story_heading'][0] }}<br>{{ $about['story_heading'][1] }}<br><em>{{ $about['story_heading'][2] }}</em></h2>
        </div>
    </div>
    <div class="brand-story__text reveal">
        <p>{{ $about['paragraphs'][2] ?? $about['paragraphs'][0] }}</p>
        <a class="arrow-link" href="{{ route('nosotros') }}">CONÓCENOS MÁS
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
        </a>
    </div>
</section>
