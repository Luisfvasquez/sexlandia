@php $g = ($gallery ?? collect())->values()->take(6); @endphp
@if ($g->count())
<section id="album-coleccion" 
    x-data="{
        scroll(direction) {
            const el = this.$refs.scroller;
            const itemWidth = el.querySelector('a').offsetWidth + 40; // width + gap aprox
            el.scrollBy({ left: direction * itemWidth, behavior: 'smooth' });
        }
    }" 
    class="relative min-h-[100dvh] flex flex-col justify-center py-24 bg-ink border-t border-white/5 overflow-hidden" 
    aria-labelledby="coleccion-title">
    
    <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8 mb-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between animate-slide-up-fade">
            <div>
                <p class="text-rose-600 text-[10px] tracking-[0.3em] uppercase font-bold mb-6">
                    Colección Oficial · {{ \Illuminate\Support\Str::upper(config('site.brand.name')) }}
                </p>
                <h2 id="coleccion-title" class="text-5xl lg:text-7xl leading-[1.05] text-white font-bold tracking-tight">
                    Descubre la<br>
                    <em class="font-serif italic text-rose-500 font-normal">colección completa.</em>
                </h2>
            </div>
            
            {{-- Controles PC (Botones) --}}
            <div class="hidden lg:flex items-center gap-3 mt-8 md:mt-0">
                <button type="button" @click="scroll(-1)" class="w-12 h-12 border border-white/20 flex items-center justify-center text-neutral-400 hover:text-white hover:border-white transition-all duration-300" aria-label="Anterior">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
                </button>
                <button type="button" @click="scroll(1)" class="w-12 h-12 border border-white/20 flex items-center justify-center text-neutral-400 hover:text-white hover:border-white transition-all duration-300" aria-label="Siguiente">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                </button>
            </div>
            
            {{-- Control Mobile (Texto) --}}
            <span class="lg:hidden text-[10px] text-neutral-500 tracking-[0.2em] uppercase mt-8 md:mt-0 animate-pulse border border-white/10 px-4 py-2 w-max">
                Desliza para ver más →
            </span>
        </div>
    </div>

    {{-- Contenedor de Scroll Horizontal Estilo App --}}
    <div x-ref="scroller" class="w-full overflow-x-auto pb-12 snap-x snap-mandatory scrollbar-hide pl-6 lg:pl-[max(2rem,calc((100vw-80rem)/2))] scroll-smooth">
        <div class="flex gap-6 lg:gap-10 w-max pr-6 lg:pr-[max(2rem,calc((100vw-80rem)/2))]">
            @foreach ($g as $p)
                @php $waText = rawurlencode('Hola ' . config('site.brand.name') . ', me interesa: ' . $p->name); @endphp
                <a href="https://wa.me/{{ config('site.contact.whatsapp') }}?text={{ $waText }}" target="_blank" rel="noopener" class="group relative flex flex-col w-[85vw] md:w-[50vw] lg:w-[450px] snap-center snap-always">
                    
                    <div class="relative aspect-[3/4] bg-black overflow-hidden border border-white/10 group-hover:border-rose-500/50 transition-colors duration-500">
                        {{-- Overlay misterioso --}}
                        <div class="absolute inset-0 bg-ink/60 group-hover:bg-transparent transition-colors duration-[1.5s] pointer-events-none z-10"></div>
                        
                        <x-sl.product-media 
                            :product="$p" 
                            class="w-full h-full object-cover grayscale opacity-70 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-105 transition-all duration-[1.5s] ease-out" 
                            alt="{{ $p->name }}" 
                        />
                        
                        {{-- Detalles (Suben en Hover) --}}
                        <div class="absolute bottom-0 left-0 w-full p-8 z-20 translate-y-6 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500 bg-gradient-to-t from-ink via-ink/80 to-transparent">
                            <span class="text-rose-500 text-[9px] tracking-[0.2em] uppercase mb-3 block font-bold">
                                {{ $p->category->name ?? 'Colección' }}
                            </span>
                            <h3 class="text-3xl text-white font-serif italic mb-3 leading-tight">{{ $p->name }}</h3>
                            <p class="text-neutral-400 font-light text-xs line-clamp-2 leading-relaxed">
                                {{ $p->description ? $p->description : 'Consulta disponibilidad y precio por WhatsApp directo con nosotros.' }}
                            </p>
                        </div>
                    </div>
                    
                    {{-- Índice numérico por fuera --}}
                    <div class="mt-6 flex items-center justify-between border-b border-white/10 pb-4 group-hover:border-rose-500/30 transition-colors">
                        <span class="text-neutral-500 font-mono tracking-widest text-sm">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" class="text-neutral-500 group-hover:text-rose-500 transition-all group-hover:translate-x-3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endif
