@extends('storefront.layout')

{{-- Los @push de SEO deben ejecutarse antes de que el layout pinte el <head> --}}
@include('storefront.partials.seo-jsonld')

@section('content')
    @if (session('success') || $errors->any())
        <div class="sl-flash-wrap">
            @if (session('success'))
                <div class="sl-alert sl-alert--ok">{{ session('success') }}</div>
            @endif
            @foreach ($errors->all() as $error)
                <div class="sl-alert sl-alert--err">{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @include('storefront.partials.hero')
    @include('storefront.partials.manifesto')
    @include('storefront.partials.categories')
    @include('storefront.partials.featured')
    @include('storefront.partials.products')
    @include('storefront.partials.inventory')
    @include('storefront.partials.marquee')
    @include('storefront.partials.collection')
    @include('storefront.partials.couples')
    @include('storefront.partials.benefits')
    @include('storefront.partials.about')
    @include('storefront.partials.testimonials')
    @include('storefront.partials.location')
    @include('storefront.partials.faq')
    @include('storefront.partials.newsletter')
    @include('storefront.partials.final-cta')
@endsection
