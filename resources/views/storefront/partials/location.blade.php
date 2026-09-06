@php
    $loc = config('site.location');
    $c = config('site.contact');
    $g = ($gallery ?? collect())->values();
@endphp
<section id="visitanos" class="location section-pad section-pad--large" aria-labelledby="location-title">
    <span id="contacto" style="position:absolute;margin-top:-90px;"></span>
    <span id="ubicacion" style="position:absolute;margin-top:-90px;"></span>

    <div class="location__photo--box reveal">
        <x-sl.product-media :product="$g->get(0) ?? null" class="location__img-featured" alt="Boutique {{ config('site.brand.name') }}" />
    </div>

    <div class="location__details reveal">
        <p class="eyebrow">TIENDA FÍSICA · CONTACTO</p>
        <h2 id="location-title">Pasa a<br><em>saludar.</em></h2>
        <div class="location__meta">
            <div>
                <span>Dirección</span>
                <p>
                    @foreach ($loc['address_lines'] as $line){{ $line }}<br>@endforeach
                    <a href="{{ $loc['maps_link'] }}" target="_blank" rel="noopener">Ver en Google Maps</a>
                </p>
            </div>
            <div>
                <span>Horario</span>
                <p>@foreach ($loc['hours_text'] as $h){{ $h }}<br>@endforeach</p>
            </div>
            <div>
                <span>Contacto &amp; reservas</span>
                <p>
                    WhatsApp: <a href="https://wa.me/{{ $c['whatsapp'] }}" target="_blank" rel="noopener">{{ $c['whatsapp_display'] }}</a><br>
                    Email: <a href="mailto:{{ $c['email'] }}">{{ $c['email'] }}</a><br>
                    Instagram: <a href="{{ $c['instagram'] }}" target="_blank" rel="noopener">{{ $c['instagram_handle'] }}</a>
                </p>
            </div>
        </div>
        <a class="wa-btn" href="https://wa.me/{{ $c['whatsapp'] }}?text={{ rawurlencode('Hola ' . config('site.brand.name') . ', quisiera información sobre la tienda') }}"
            target="_blank" rel="noopener">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 00-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1012 2zm0 2a8 8 0 11-4.2 14.8l-.3-.2-2.8.8.8-2.7-.2-.3A8 8 0 0112 4z"/></svg>
            Escribir por WhatsApp
        </a>

        <div class="location__map">
            <iframe src="{{ $loc['maps_embed_url'] }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                title="Ubicación de {{ config('site.brand.name') }} en {{ $loc['city'] }}"></iframe>
        </div>
    </div>
</section>
