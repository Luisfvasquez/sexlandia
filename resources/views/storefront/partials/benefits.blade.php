<section class="relative min-h-[100dvh] flex items-center py-24 lg:py-32 bg-ink border-t border-white/5 overflow-hidden" aria-labelledby="benefits-title">
    <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-8 items-start">
            
            {{-- Columna Izquierda --}}
            <div class="lg:col-span-5 lg:sticky lg:top-32 animate-slide-up-fade">
                <h2 id="benefits-title" class="text-4xl sm:text-6xl lg:text-[7rem] leading-[1] text-white font-bold tracking-tight">
                    Bueno<br>
                    <em class="font-serif italic text-rose-500 font-normal">saberlo.</em>
                </h2>
            </div>

            {{-- Columna Derecha (Filas de Beneficios) --}}
            <div class="lg:col-span-7 flex flex-col animate-slide-up-fade [animation-delay:200ms]">
                <div class="flex flex-col border-t border-white/10 mt-8 lg:mt-0">
                    @foreach (config('site.benefits') as [$n, $title, $text])
                        <div class="group flex flex-col md:flex-row md:items-center py-10 lg:py-14 border-b border-white/10 hover:border-rose-500/40 transition-colors duration-500">
                            {{-- Numero --}}
                            <span class="text-xs font-mono tracking-[0.2em] text-rose-600 mb-4 md:mb-0 md:w-20 shrink-0 transition-transform duration-500 group-hover:translate-x-3">
                                {{ $n }}
                            </span>
                            {{-- Titulo --}}
                            <h3 class="text-base md:text-lg font-medium text-neutral-300 tracking-[0.15em] uppercase md:w-5/12 shrink-0 transition-colors duration-500 group-hover:text-white pr-6">
                                {{ $title }}
                            </h3>
                            {{-- Texto --}}
                            <p class="mt-4 md:mt-0 text-sm md:text-base font-light text-neutral-500 leading-relaxed font-serif italic transition-colors duration-500 group-hover:text-neutral-300">
                                {{ $text }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>
