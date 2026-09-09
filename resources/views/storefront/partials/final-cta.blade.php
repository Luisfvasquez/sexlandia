@php $g = ($gallery ?? collect())->values(); @endphp
<section class="relative min-h-[100dvh] flex items-center py-24 lg:py-32 bg-ink border-t border-white/5 overflow-hidden" aria-labelledby="final-cta-title">
    
    {{-- Glow vino profundo en la esquina --}}
    <div class="absolute bottom-0 left-0 w-full lg:w-2/3 h-full bg-[radial-gradient(ellipse_at_bottom_left,_rgba(141,38,61,0.15),_transparent_70%)] pointer-events-none"></div>

    <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            
            {{-- Composicion Grafica (Izquierda) --}}
            <div class="relative w-full aspect-square md:aspect-[4/3] lg:aspect-square animate-slide-up-fade order-2 lg:order-1">
                {{-- Foto principal (Fondo) --}}
                <div class="absolute inset-0 w-[80%] h-[80%] bg-black border border-white/10 overflow-hidden group">
                    <x-sl.product-media :product="$g->get(0) ?? null" class="w-full h-full object-cover grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-[2s] ease-out" alt="Colección {{ config('site.brand.name') }}" />
                </div>
                {{-- Foto superpuesta (Frente Abajo Derecha) --}}
                <div class="absolute bottom-0 right-0 w-[55%] h-[55%] bg-black border border-white/10 shadow-2xl shadow-black overflow-hidden group z-10 lg:translate-x-8 lg:translate-y-8">
                    <x-sl.product-media :product="$g->get(1) ?? null" class="w-full h-full object-cover grayscale opacity-70 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-[2s] ease-out" alt="Producto destacado {{ config('site.brand.name') }}" />
                </div>
            </div>

            {{-- Copy (Derecha) --}}
            <div class="flex flex-col space-y-8 animate-slide-up-fade [animation-delay:200ms] order-1 lg:order-2">
                <p class="text-rose-600 text-[10px] tracking-[0.3em] uppercase font-bold">
                    EXPERIENCIA {{ \Illuminate\Support\Str::upper(config('site.brand.name')) }}
                </p>
                <h2 id="final-cta-title" class="text-4xl sm:text-6xl lg:text-[7rem] leading-[1.05] text-white font-bold tracking-tight">
                    ¿Viste algo<br>
                    <em class="font-serif italic text-rose-500 font-normal">que te gustó?</em>
                </h2>
                <p class="text-neutral-400 font-mono tracking-[0.15em] text-[10px] lg:text-xs leading-relaxed max-w-md uppercase">
                    NO LE DES TANTAS VUELTAS. ESCRÍBENOS Y TE ASESORAMOS DE INMEDIATO, SIN PENA.
                </p>
                <div class="pt-4">
                    <a href="https://wa.me/{{ config('site.contact.whatsapp') }}?text={{ rawurlencode('Hola ' . config('site.brand.name') . ', quisiera hacer un pedido') }}" target="_blank" rel="noopener" class="group flex items-center gap-4 text-white font-medium w-max pb-2 border-b border-rose-600 hover:text-rose-400 hover:border-rose-400 transition-all duration-300">
                        <span class="tracking-[0.2em] text-xs uppercase">PEDIR POR WHATSAPP</span>
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="transition-transform duration-300 group-hover:translate-x-2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</section>
