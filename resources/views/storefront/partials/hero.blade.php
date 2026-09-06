@php
    $hero = config('site.hero');
@endphp

<section id="inicio" class="hero section-pad">
    <div class="hero__copy">
        <p class="eyebrow">{{ config('site.brand.eyebrow') }}</p>
        <h1 class="hero__title">
            <span class="hero__feel">{{ $hero['title'][0] }}</span>
            <span class="hero__something">{{ $hero['title'][1] }}</span>
            <span class="hero__different">{{ $hero['title'][2] }}</span>
        </h1>
        <p class="hero__dek">{{ $hero['text'] }}</p>
        <div class="hero__ctas">
            <a class="arrow-link" href="#productos">
                {{ $hero['cta'] }}
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7" />
                </svg>
            </a>
            @if ($featured ?? null)
                <a href="#destacado" class="hero__chip-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l2.4 6.9L22 9l-5.6 4.3L18.5 22 12 17.8 5.5 22l2.1-8.7L2 9l7.6-.1z"/></svg>
                    <span>{{ \Illuminate\Support\Str::limit($featured->name, 32) }}</span>
                </a>
            @endif
        </div>
    </div>

    <div class="hero__media-wrap">
        <div class="hero__media">
            <div class="hero__photo-card">
                <x-sl.product-media :product="$featured ?? null" size="full" :eager="true" class="hero__img" alt="Producto destacado de {{ config('site.brand.name') }}" />
                <div class="hero__photo-overlay">
                    <span class="hero__badge-tag">COLECCIÓN OFICIAL</span>
                    <p>{{ ($featured ?? null) ? $featured->name : config('site.brand.tagline') }}</p>
                </div>
            </div>
        </div>
        <span class="hero__edition">01 / COLECCIÓN OFICIAL</span>
    </div>
    <span class="hero__side-note">{{ config('site.brand.city_note') }}</span>
</section>
