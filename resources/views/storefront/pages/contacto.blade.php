@php $navBase = route('storefront'); @endphp

@extends('storefront.layout')

@section('title', 'Contacto y ubicación · ' . config('site.brand.name') . ' en ' . config('site.location.city'))
@section('meta_description', 'Visítanos o escríbenos por WhatsApp. Dirección, horario y contacto de ' . config('site.brand.name') . ', sex shop con delivery discreto en ' . config('site.location.city') . '.')

@php
    $extraSchemas = [
        [
            '@context' => 'https://schema.org',
            '@type' => 'ContactPage',
            'name' => 'Contacto · ' . config('site.brand.name'),
            'url' => route('contacto'),
        ],
        [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => route('storefront')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Contacto', 'item' => route('contacto')],
            ],
        ],
    ];
@endphp
@include('storefront.partials.seo-jsonld', ['extraSchemas' => $extraSchemas])

@section('content')
    <section class="hero section-pad" style="min-height:46svh;">
        <div class="hero__copy">
            <p class="eyebrow">TIENDA FÍSICA · {{ \Illuminate\Support\Str::upper(config('site.location.city')) }}</p>
            <h1 class="hero__title" style="margin-bottom:20px;">
                <span class="hero__feel">Pasa a</span>
                <span class="hero__something">saludar.</span>
            </h1>
            <p class="hero__dek">Atención directa y discreta. Reserva por WhatsApp y retira en tienda, o pide delivery.</p>
            <a class="wa-btn" href="https://wa.me/{{ config('site.contact.whatsapp') }}?text={{ rawurlencode('Hola ' . config('site.brand.name') . ', quisiera información') }}" target="_blank" rel="noopener">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 00-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1012 2z"/></svg>
                Escribir por WhatsApp
            </a>
        </div>
    </section>

    @include('storefront.partials.location')
    @include('storefront.partials.faq')
    @include('storefront.partials.final-cta')
@endsection
