@php
    $loc = config('site.location');
    $c = config('site.contact');
    $g = ($gallery ?? collect())->values();
@endphp
<section id="visitanos" class="relative min-h-[100dvh] flex items-center py-24 bg-ink border-t border-white/5 overflow-hidden" aria-labelledby="location-title">
    <span id="contacto" class="absolute -top-24"></span>
    <span id="ubicacion" class="absolute -top-24"></span>

    <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-start">
            
            {{-- Columna Izquierda (Foto y Mapa) --}}
            <div class="relative flex flex-col space-y-8 animate-slide-up-fade">
                {{-- Foto Boutique --}}
                <div class="relative w-full aspect-[4/3] lg:aspect-square bg-black border border-white/10 overflow-hidden group">
                    <x-sl.product-media :product="$g->get(0) ?? null" class="w-full h-full object-cover grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-[2s] ease-out" alt="Boutique {{ config('site.brand.name') }}" />
                </div>

                {{-- Mapa Embed en Escala de grises --}}
                <div class="relative w-full aspect-[21/9] bg-black border border-white/10 overflow-hidden grayscale hover:grayscale-0 transition-all duration-700">
                    <iframe src="{{ $loc['maps_embed_url'] }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full border-0" title="Ubicación de {{ config('site.brand.name') }}"></iframe>
                </div>
            </div>

            {{-- Columna Derecha (Info) --}}
            <div class="flex flex-col animate-slide-up-fade [animation-delay:200ms] lg:pt-12">
                <p class="text-rose-600 text-[10px] tracking-[0.3em] uppercase font-bold mb-6">TIENDA FÍSICA · CONTACTO</p>
                <h2 id="location-title" class="text-6xl lg:text-[7rem] leading-[1.05] text-white font-bold tracking-tight mb-16">
                    Pasa a<br>
                    <em class="font-serif italic text-rose-500 font-normal">saludar.</em>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    {{-- Dirección --}}
                    <div>
                        <span class="text-xs font-mono tracking-widest text-neutral-500 uppercase block mb-4">Dirección</span>
                        <p class="text-neutral-300 font-light leading-relaxed">
                            @foreach ($loc['address_lines'] as $line){{ $line }}<br>@endforeach
                            <a href="{{ $loc['maps_link'] }}" target="_blank" rel="noopener" class="text-rose-500 hover:text-rose-400 border-b border-rose-500/30 hover:border-rose-400 mt-2 inline-block transition-colors">Ver en Google Maps</a>
                        </p>
                    </div>

                    {{-- Horario --}}
                    <div>
                        <span class="text-xs font-mono tracking-widest text-neutral-500 uppercase block mb-4">Horario</span>
                        <p class="text-neutral-300 font-light leading-relaxed">
                            @foreach ($loc['hours_text'] as $h){{ $h }}<br>@endforeach
                        </p>
                    </div>

                    {{-- Contacto --}}
                    <div class="md:col-span-2 border-t border-white/10 pt-12 mt-4">
                        <span class="text-xs font-mono tracking-widest text-neutral-500 uppercase block mb-4">Contacto & Reservas</span>
                        <div class="flex flex-col space-y-3 text-neutral-300 font-light">
                            <p>WhatsApp: <a href="https://wa.me/{{ $c['whatsapp'] }}" target="_blank" rel="noopener" class="hover:text-rose-500 transition-colors">{{ $c['whatsapp_display'] }}</a></p>
                            <p>Email: <a href="mailto:{{ $c['email'] }}" class="hover:text-rose-500 transition-colors">{{ $c['email'] }}</a></p>
                            <p>Instagram: <a href="{{ $c['instagram'] }}" target="_blank" rel="noopener" class="hover:text-rose-500 transition-colors">{{ $c['instagram_handle'] }}</a></p>
                        </div>
                    </div>
                </div>

                {{-- CTA WhatsApp --}}
                <div class="mt-16">
                    <a href="https://wa.me/{{ $c['whatsapp'] }}?text={{ rawurlencode('Hola ' . config('site.brand.name') . ', quisiera información sobre la tienda') }}"
                        target="_blank" rel="noopener"
                        class="inline-flex items-center justify-center gap-3 bg-white text-ink font-bold text-xs uppercase tracking-widest px-8 py-5 hover:bg-rose-500 hover:text-white transition-all duration-300">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 00-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1012 2zm0 2a8 8 0 11-4.2 14.8l-.3-.2-2.8.8.8-2.7-.2-.3A8 8 0 0112 4z"/></svg>
                        Escribir por WhatsApp
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</section>
