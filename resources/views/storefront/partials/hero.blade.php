@php
    $hero = config('site.hero');
@endphp

<section id="inicio" class="relative min-h-[100dvh] flex items-center py-24 lg:py-0 bg-ink overflow-hidden">
    {{-- Fondo sensual: gradiente radial color vino que palpita muy suave --}}
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_50%,_rgba(141,38,61,0.15),_transparent_60%)] pointer-events-none animate-breathe"></div>

    <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8 flex flex-col lg:flex-row items-center justify-between mt-12 lg:mt-0">
        
        <div class="relative z-10 w-full lg:w-[55%] flex flex-col justify-center space-y-6 lg:space-y-8">
            <p class="text-rose-600 font-medium tracking-[0.3em] uppercase text-xs md:text-sm animate-slide-up-fade opacity-0">
                {{ config('site.brand.eyebrow') }}
            </p>
            <h1 class="flex flex-col text-5xl min-[400px]:text-6xl md:text-[6rem] xl:text-[7rem] leading-[0.9]">
                <span class="font-sans font-bold text-white tracking-tighter animate-slide-up-fade opacity-0 [animation-delay:200ms]">
                    {{ $hero['title'][0] }}
                </span>
                <span class="font-serif italic text-rose-500 -mt-2 xl:-mt-6 ml-8 md:ml-20 relative z-10 drop-shadow-[0_0_15px_rgba(225,29,72,0.4)] animate-slide-up-fade opacity-0 [animation-delay:400ms]">
                    {{ $hero['title'][1] }}
                </span>
                <span class="font-sans font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-neutral-100 to-neutral-500 animate-slide-up-fade opacity-0 [animation-delay:600ms] pr-4">
                    {{ $hero['title'][2] }}
                </span>
            </h1>
            <p class="text-neutral-400 text-lg xl:text-xl max-w-md leading-relaxed font-light animate-slide-up-fade opacity-0 [animation-delay:800ms]">
                {{ $hero['text'] }}
            </p>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 mt-8 animate-slide-up-fade opacity-0 [animation-delay:1000ms]">
                <a href="#productos" class="group flex items-center gap-3 text-white font-medium pb-1 border-b border-rose-600 hover:text-rose-400 hover:border-rose-400 transition-all duration-300">
                    <span class="tracking-[0.2em] text-xs uppercase">{{ $hero['cta'] }}</span>
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="transition-transform duration-300 group-hover:translate-x-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7" />
                    </svg>
                </a>
                @if ($featured ?? null)
                    <a href="#destacado" class="flex items-center gap-2 px-5 py-2.5 rounded-full border border-rose-900/50 bg-rose-900/20 text-rose-300 text-[10px] tracking-widest uppercase backdrop-blur-md hover:bg-rose-900/40 hover:scale-105 transition-all duration-300 shadow-[0_0_15px_rgba(141,38,61,0.2)]">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 6.9L22 9l-5.6 4.3L18.5 22 12 17.8 5.5 22l2.1-8.7L2 9l7.6-.1z"/></svg>
                        <span>{{ \Illuminate\Support\Str::limit($featured->name, 28) }}</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="relative w-full lg:w-[40%] max-w-md xl:max-w-lg mx-auto lg:mx-0 mt-16 lg:mt-0 animate-slide-up-fade opacity-0 [animation-delay:1200ms]">
            <div class="relative w-full aspect-[4/5] rounded-tl-[4rem] rounded-br-[4rem] overflow-hidden shadow-[0_0_60px_rgba(141,38,61,0.25)] animate-breathe group border border-white/5">
                <div class="absolute inset-0 bg-ink/30 group-hover:bg-transparent transition-colors duration-700 z-10 pointer-events-none"></div>
                
                <x-sl.product-media :product="$featured ?? null" size="full" :eager="true" class="w-full h-full object-cover transition-transform duration-[1.5s] ease-out group-hover:scale-105 saturate-50 group-hover:saturate-100" alt="Producto destacado de {{ config('site.brand.name') }}" />
                
                <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/40 to-transparent flex flex-col justify-end p-8 z-20 pointer-events-none">
                    <span class="text-rose-500 text-[10px] font-bold tracking-[0.2em] mb-3 opacity-80">COLECCIÓN OFICIAL</span>
                    <p class="text-white text-2xl font-serif italic drop-shadow-lg">{{ ($featured ?? null) ? $featured->name : config('site.brand.tagline') }}</p>
                </div>
            </div>
            
            <div class="absolute -right-16 top-1/2 -translate-y-1/2 rotate-90 origin-bottom-left hidden xl:block">
                <span class="text-neutral-600 text-[9px] tracking-[0.3em] uppercase">01 / COLECCIÓN OFICIAL</span>
            </div>
        </div>

    </div>
    
    <div class="absolute left-8 bottom-12 -rotate-90 origin-bottom-left hidden lg:block">
        <span class="text-neutral-600 text-[10px] tracking-[0.3em] uppercase">{{ config('site.brand.city_note') }}</span>
    </div>
</section>


