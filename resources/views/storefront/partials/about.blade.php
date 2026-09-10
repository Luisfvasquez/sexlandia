@php
    $about = config('site.about');
    $g = ($gallery ?? collect())->values();
@endphp
<section id="nosotros" class="relative min-h-[100dvh] flex items-center py-24 bg-ink border-t border-white/5 overflow-hidden" aria-labelledby="nosotros-title">
    <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            {{-- Titulo (flujo normal en móvil, superpuesto en desktop) --}}
            <h2 id="nosotros-title" class="lg:hidden text-4xl sm:text-6xl leading-[1.05] text-white font-bold tracking-tight animate-slide-up-fade">
                {{ $about['story_heading'][0] }}
                {{ $about['story_heading'][1] }}
                <em class="font-serif italic text-rose-500 font-normal">{{ $about['story_heading'][2] }}</em>
            </h2>

            {{-- Composicion Grafica (Izquierda) --}}
            <div class="relative w-full aspect-[4/5] lg:aspect-square animate-slide-up-fade">
                {{-- Imagen base (Fondo) --}}
                <div class="absolute top-0 left-0 w-[85%] h-[85%] bg-black border border-white/10 overflow-hidden group">
                    <x-sl.product-media :product="$g->get(0) ?? null" class="w-full h-full object-cover grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-[2s] ease-out" alt="{{ config('site.brand.name') }} boutique" />
                </div>
                {{-- Imagen secundaria (Frente, abajo derecha) --}}
                <div class="absolute bottom-0 right-0 w-[55%] h-[55%] bg-black border border-white/10 shadow-2xl shadow-black overflow-hidden group z-10 translate-y-6 lg:translate-y-12 lg:-translate-x-4">
                    <x-sl.product-media :product="$g->get(1) ?? null" class="w-full h-full object-cover grayscale opacity-70 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-[2s] ease-out" alt="Selección {{ config('site.brand.name') }}" />
                </div>

                {{-- Titulo Absoluto superpuesto (solo desktop) --}}
                <div class="hidden lg:block absolute top-1/2 left-0 -translate-y-1/2 lg:-left-8 w-[120%] z-20 pointer-events-none">
                    <h2 aria-hidden="true" class="lg:text-[7rem] leading-[1.02] text-white font-bold tracking-tight drop-shadow-2xl">
                        {{ $about['story_heading'][0] }}<br>
                        {{ $about['story_heading'][1] }}<br>
                        <em class="font-serif italic text-rose-500 font-normal">{{ $about['story_heading'][2] }}</em>
                    </h2>
                </div>
            </div>

            {{-- Texto de historia (Derecha) --}}
            <div class="flex flex-col justify-center animate-slide-up-fade [animation-delay:200ms] lg:pl-16">
                <p class="text-xl lg:text-3xl font-light text-neutral-300 leading-relaxed font-serif italic mb-12 border-l border-rose-500/30 pl-8">
                    "{{ $about['paragraphs'][2] ?? $about['paragraphs'][0] }}"
                </p>
                <div class="pt-4">
                    <a href="{{ route('nosotros') }}" class="group flex items-center gap-4 text-white font-medium w-max pb-2 border-b border-rose-600 hover:text-rose-400 hover:border-rose-400 transition-all duration-300">
                        <span class="tracking-[0.2em] text-xs uppercase">CONÓCENOS MÁS</span>
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="transition-transform duration-300 group-hover:translate-x-2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>
