@php $navBase = route('storefront'); @endphp

@extends('storefront.layout')

@section('title', 'Nosotros · ' . config('site.brand.name') . ' — Sex shop con asesoría sin pena en ' . config('site.location.city'))
@section('meta_description', 'Conoce a ' . config('site.brand.name') . ': una tienda de bienestar sexual en ' . config('site.location.city') . ' pensada para gente curiosa. Empaque discreto, marcas originales y asesoría sin juicios.')
@section('og_type', 'article')

@php
    $extraSchemas = [
        [
            '@context' => 'https://schema.org',
            '@type' => 'AboutPage',
            'name' => 'Nosotros · ' . config('site.brand.name'),
            'url' => route('nosotros'),
            'description' => 'Historia y propuesta de ' . config('site.brand.name') . '.',
        ],
        [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => route('storefront')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Nosotros', 'item' => route('nosotros')],
            ],
        ],
    ];
@endphp
@include('storefront.partials.seo-jsonld', ['extraSchemas' => $extraSchemas])

@section('content')
    <section class="hero section-pad" style="min-height:60svh;">
        <div class="hero__copy">
            <p class="eyebrow">{{ config('site.about.eyebrow') }}</p>
            <h1 class="hero__title" style="margin-bottom:20px;">
                <span class="hero__feel">Gente</span>
                <span class="hero__something">curiosa,</span>
                <span class="hero__different">SIN PENA.</span>
            </h1>
            <p class="hero__dek">{{ config('site.about.paragraphs')[0] }}</p>
            <a class="arrow-link" href="{{ route('storefront') }}#productos">VER EL CATÁLOGO
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="hero__media-wrap">
            <div class="hero__media">
                <div class="hero__photo-card">
                    <x-sl.product-media :product="$featured ?? null" :eager="true" class="hero__img" alt="Boutique {{ config('site.brand.name') }}" />
                </div>
            </div>
        </div>
    </section>

    @include('storefront.partials.about')
    @include('storefront.partials.benefits')
    @include('storefront.partials.testimonials')
    @include('storefront.partials.faq')
    @include('storefront.partials.final-cta')
@endsection
