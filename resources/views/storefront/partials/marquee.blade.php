@php $g = ($gallery ?? collect())->values(); @endphp
<section class="relative min-h-[100dvh] flex flex-col justify-center py-24 bg-ink border-t border-white/5 overflow-hidden" aria-labelledby="interrupt-title">
    
    <style>
        @keyframes marquee { 0% { transform: translateX(0%); } 100% { transform: translateX(-50%); } }
        .animate-marquee { animation: marquee 40s linear infinite; }
        .animate-marquee:hover { animation-play-state: paused; }
    </style>

    <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8 mb-24">
        <h2 id="interrupt-title" class="text-5xl lg:text-[7rem] leading-[1.02] text-white font-bold tracking-tight animate-slide-up-fade">
            No necesitas<br>
            <em class="font-serif italic text-rose-500 font-normal">una razón</em><br>
            para tener curiosidad.
        </h2>
    </div>

    @if ($g->count())
        {{-- Contenedor de Marquesina Infinita --}}
        <div class="relative w-full flex overflow-x-hidden">
            {{-- Difuminados en los bordes para transición suave --}}
            <div class="absolute inset-y-0 left-0 w-32 bg-gradient-to-r from-ink to-transparent z-10 pointer-events-none"></div>
            <div class="absolute inset-y-0 right-0 w-32 bg-gradient-to-l from-ink to-transparent z-10 pointer-events-none"></div>
            
            {{-- La tira que se mueve. Duplicamos la data para que el loop no se corte --}}
            <div class="flex animate-marquee whitespace-nowrap">
                @foreach ($g->concat($g)->concat($g) as $p)
                    <div class="relative w-40 md:w-56 mx-4 group">
                        <div class="aspect-[4/5] bg-black overflow-hidden border border-white/10 group-hover:border-rose-500/50 transition-colors duration-500">
                            <x-sl.product-media 
                                :product="$p" 
                                size="thumb" 
                                class="w-full h-full object-cover grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-[1.5s]" 
                                alt="{{ $p->name }}" 
                            />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
