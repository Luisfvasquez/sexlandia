@php $inv = ($inventoryList ?? collect())->take(6); @endphp
@if ($inv->count())
<section id="inventario" class="relative min-h-[100dvh] flex items-center py-24 lg:py-0 bg-ink border-t border-white/5 overflow-hidden" aria-labelledby="inventario-title">
    
    <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-8">
            
            {{-- Columna Izquierda: Mensaje y Confianza --}}
            <div class="lg:col-span-5 flex flex-col space-y-12 animate-slide-up-fade">
                <div>
                    <div class="flex items-center gap-4 mb-8">
                        <p class="text-rose-600 text-[10px] tracking-[0.3em] uppercase font-bold">
                            Tienda {{ \Illuminate\Support\Str::upper(config('site.location.city')) }}
                        </p>
                        <div class="flex items-center gap-2 text-[9px] tracking-[0.2em] text-green-500 font-bold border border-green-500/20 bg-green-500/10 px-3 py-1 rounded-none">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            Stock Verificado
                        </div>
                    </div>
                    
                    <h2 id="inventario-title" class="text-4xl sm:text-5xl lg:text-7xl leading-[1.05] text-white font-bold tracking-tight mb-8">
                        Lo ves aquí.<br>
                        <em class="font-serif italic text-rose-500 font-normal">Lo encuentras allá.</em>
                    </h2>
                    
                    <p class="text-neutral-400 font-light text-sm lg:text-base leading-relaxed max-w-sm border-l border-white/10 pl-5">
                        Nuestro catálogo muestra la disponibilidad real de {{ config('site.brand.name') }}. Si aparece aquí, está listo para retiro en tienda o delivery de inmediato. Sin esperas.
                    </p>
                </div>
            </div>

            {{-- Columna Derecha: Lista de Inventario Estilo Menú Exclusivo --}}
            <div class="lg:col-span-6 lg:col-start-7 flex flex-col pt-4 animate-slide-up-fade [animation-delay:200ms]">
                <div class="flex flex-col border-t border-white/10">
                    @foreach ($inv as $p)
                        @php
                            $stock = $p->inventory->stock ?? 0;
                            $label = $stock <= 0
                                ? 'Consultar'
                                : ($p->unit_type === 'gram'
                                    ? number_format($stock / 1000, 2, ',', '.') . ' kg en stock'
                                    : (int) $stock . ' und en stock');
                        @endphp
                        <div class="group flex items-center justify-between py-6 border-b border-white/10 hover:border-rose-500/40 transition-colors duration-500 cursor-default">
                            <span class="text-xs md:text-sm lg:text-base font-medium text-neutral-400 tracking-[0.1em] group-hover:text-white transition-all duration-500 group-hover:translate-x-3 transform truncate pr-4">
                                {{ \Illuminate\Support\Str::upper($p->name) }}
                            </span>
                            <span class="text-[9px] lg:text-[10px] font-mono tracking-widest text-neutral-500 group-hover:text-rose-400 transition-colors duration-500 whitespace-nowrap">
                                {{ \Illuminate\Support\Str::upper($label) }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-14 self-start lg:self-end">
                    <a href="#productos" class="group flex items-center gap-4 text-white font-medium pb-2 border-b border-rose-600 hover:text-rose-400 hover:border-rose-400 transition-all duration-300">
                        <span class="tracking-[0.2em] text-xs uppercase">Ver Todos Los Productos</span>
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="transition-transform duration-300 group-hover:translate-x-2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>
@endif
