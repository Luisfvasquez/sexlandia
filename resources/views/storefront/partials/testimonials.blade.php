<section id="testimonios" 
    x-data="{
        scroll(direction) {
            const el = this.$refs.scroller;
            const itemWidth = el.querySelector('figure').offsetWidth + 32;
            el.scrollBy({ left: direction * itemWidth, behavior: 'smooth' });
        }
    }"
    class="relative min-h-[100dvh] flex flex-col justify-center py-24 bg-ink border-t border-white/5 overflow-hidden" 
    aria-labelledby="testimonials-title">
    
    <div class="absolute top-0 right-0 w-1/3 h-full bg-[radial-gradient(ellipse_at_top_right,_rgba(141,38,61,0.08),_transparent_70%)] pointer-events-none"></div>

    <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8 mb-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between animate-slide-up-fade">
            <div>
                <p class="text-rose-600 text-[10px] tracking-[0.3em] uppercase font-bold mb-6">
                    LO QUE DICEN NUESTROS CLIENTES
                </p>
                <h2 id="testimonials-title" class="text-4xl sm:text-5xl lg:text-7xl leading-[1.05] text-white font-bold tracking-tight">
                    Curiosidad<br>
                    <em class="font-serif italic text-rose-500 font-normal">sin arrepentimientos.</em>
                </h2>
            </div>
            
            {{-- Controles PC --}}
            <div class="hidden lg:flex items-center gap-3 mt-8 md:mt-0">
                <button type="button" @click="scroll(-1)" class="w-12 h-12 border border-white/20 flex items-center justify-center text-neutral-400 hover:text-white hover:border-white transition-all duration-300" aria-label="Anterior">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
                </button>
                <button type="button" @click="scroll(1)" class="w-12 h-12 border border-white/20 flex items-center justify-center text-neutral-400 hover:text-white hover:border-white transition-all duration-300" aria-label="Siguiente">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Contenedor de Scroll --}}
    <div x-ref="scroller" class="w-full overflow-x-auto pb-12 snap-x snap-mandatory scrollbar-hide pl-6 lg:pl-[max(2rem,calc((100vw-80rem)/2))] scroll-smooth">
        <div class="flex gap-6 lg:gap-8 w-max pr-6 lg:pr-[max(2rem,calc((100vw-80rem)/2))]">
            @foreach (config('site.testimonials') as $t)
                <figure class="w-[85vw] md:w-[380px] lg:w-[420px] snap-center shrink-0 bg-white/5 border border-white/10 p-8 lg:p-10 hover:border-rose-500/30 transition-colors duration-500">
                    <div class="flex items-center gap-1 mb-6 text-rose-500">
                        @for ($i = 0; $i < 5; $i++)
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 6.9L22 9l-5.6 4.3L18.5 22 12 17.8 5.5 22l2.1-8.7L2 9l7.6-.1z"/></svg>
                        @endfor
                    </div>
                    <blockquote class="text-lg lg:text-xl text-neutral-300 font-serif italic leading-relaxed mb-8">
                        “{{ $t['quote'] }}”
                    </blockquote>
                    <figcaption class="flex flex-col border-t border-white/10 pt-6">
                        <cite class="text-white font-bold text-xs uppercase tracking-widest not-italic mb-1">
                            — {{ $t['author'] }}
                        </cite>
                        <span class="text-neutral-500 text-[10px] tracking-[0.1em] uppercase">
                            Compró: {{ $t['product'] }}
                        </span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
