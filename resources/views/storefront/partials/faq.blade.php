<section id="faq" class="relative min-h-[100dvh] flex items-center py-24 lg:py-32 bg-ink border-t border-white/5 overflow-hidden" aria-labelledby="faq-title">
    <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-8 items-start">
            
            {{-- Columna Izquierda --}}
            <div class="lg:col-span-5 lg:sticky lg:top-32 animate-slide-up-fade">
                <p class="text-rose-600 text-[10px] tracking-[0.3em] uppercase font-bold mb-6">SIN PENA</p>
                <h2 id="faq-title" class="text-4xl sm:text-5xl lg:text-[6rem] leading-[1.05] text-white font-bold tracking-tight">
                    Cosas que<br>
                    <em class="font-serif italic text-rose-500 font-normal">nos preguntan.</em>
                </h2>
            </div>

            {{-- Columna Derecha (Acordeón Alpine.js) --}}
            <div class="lg:col-span-7 flex flex-col animate-slide-up-fade [animation-delay:200ms]" x-data="{ active: 0 }">
                <div class="flex flex-col border-t border-white/10 mt-8 lg:mt-0">
                    @foreach (config('site.faq') as $i => [$q, $a])
                        <div class="border-b border-white/10 transition-colors duration-500 hover:border-rose-500/40">
                            <button 
                                type="button" 
                                @click="active === {{ $i }} ? active = null : active = {{ $i }}"
                                class="w-full flex items-center justify-between py-8 text-left group"
                                :aria-expanded="active === {{ $i }}"
                            >
                                <span class="text-lg md:text-xl font-medium text-neutral-300 transition-colors duration-300 group-hover:text-white" :class="active === {{ $i }} ? 'text-white' : ''">
                                    {{ $q }}
                                </span>
                                <svg class="w-6 h-6 text-rose-600 transform transition-transform duration-500" :class="active === {{ $i }} ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
                                </svg>
                            </button>
                            <div 
                                x-show="active === {{ $i }}" 
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 -translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-4"
                                class="overflow-hidden"
                            >
                                <p class="pb-8 text-neutral-400 font-light text-base md:text-lg leading-relaxed font-serif italic border-l border-rose-500/30 pl-6 ml-2">
                                    {{ $a }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>
