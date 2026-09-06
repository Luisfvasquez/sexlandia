@php
    $split = config('site.brand.name_split');
    $c = config('site.contact');
    $navBase = $navBase ?? '';
@endphp
<footer class="footer section-pad">
    <div class="footer__top">
        <a href="{{ route('storefront') }}" class="brand-logo" aria-label="{{ config('site.brand.name') }} — inicio">
            <span class="brand-logo__badge" aria-hidden="true">SL</span>
            <span class="brand-logo__text">
                <span class="brand-logo__sex">{{ $split[0] }}</span><span class="brand-logo__landia">{{ $split[1] }}</span>
            </span>
        </a>
        <nav aria-label="Enlaces del pie">
            <a href="{{ $navBase }}#productos">TIENDA</a>
            <a href="{{ $navBase }}#destacado">DESTACADOS</a>
            <a href="{{ $navBase }}#album-coleccion">COLECCIÓN</a>
            <a href="{{ route('nosotros') }}">NOSOTROS</a>
            <a href="{{ route('contacto') }}">VISÍTANOS</a>
            <a href="{{ $c['instagram'] }}" target="_blank" rel="noopener">INSTAGRAM</a>
            <a href="https://wa.me/{{ $c['whatsapp'] }}" target="_blank" rel="noopener">WHATSAPP</a>
        </nav>
    </div>

    <div class="footer__legal">
        <span>+18 SOLAMENTE · CONTENIDO EXCLUSIVO PARA ADULTOS</span>
        <span>{{ config('site.location.city') }}, Venezuela</span>
        <span>© {{ date('Y') }} {{ \Illuminate\Support\Str::upper(config('site.brand.name')) }}. TODOS LOS DERECHOS RESERVADOS.</span>
    </div>
    <div class="footer__statement" aria-hidden="true">SIGUE CURIOSO.</div>
</footer>
