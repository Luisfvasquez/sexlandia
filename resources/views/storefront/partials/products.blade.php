@php
    $rate = $exchangeRate ? (float) str_replace(',', '.', $exchangeRate) : 1;
    if ($rate <= 0) { $rate = 1; }
    $wa = config('site.contact.whatsapp');
    $list = ($products ?? collect())->take(9);
@endphp
<section id="productos" class="relative min-h-[100dvh] flex items-center py-24 lg:py-16 bg-ink border-t border-white/5 overflow-hidden" aria-labelledby="productos-title">
    {{-- Glows sutiles para profundidad --}}
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_rgba(141,38,61,0.08),_transparent_60%)] pointer-events-none"></div>

    <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8">
        {{-- Cabecera --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 lg:mb-24 space-y-8 md:space-y-0 animate-slide-up-fade">
            <div>
                <h2 id="productos-title" class="text-4xl sm:text-5xl lg:text-7xl leading-[1.05] text-white font-bold tracking-tight">
                    Cosas que seguro<br>
                    <em class="font-serif italic text-rose-500 font-normal">te van a encantar.</em>
                </h2>
            </div>
            <div class="max-w-sm">
                <p class="text-neutral-400 font-light text-sm lg:text-base border-l border-rose-500/30 pl-5">
                    No tienes que saber exactamente qué buscas. Explora nuestros productos reales disponibles hoy en {{ config('site.location.city') }}.
                </p>
            </div>
        </div>

        @if ($list->count())
            {{-- Grilla de Productos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 lg:gap-x-10 lg:gap-y-20">
                @foreach ($list as $product)
                    @php
                        $usd = $product->display_price;
                        $bs = $product->display_price * $rate;
                        $trackStock = $product->track_inventory;
                        $stock = $product->inventory->stock ?? 0;
                        $available = ! $trackStock || $product->allow_negative_stock || $stock > 0;
                        $stockLabel = ! $trackStock
                            ? 'DISPONIBLE'
                            : ($stock > 0
                                ? ($product->unit_type === 'gram'
                                    ? number_format($stock / 1000, 2, ',', '.') . ' KG'
                                    : ((int) $stock) . ' DISPONIBLES')
                                : 'AGOTADO');
                        $productUrl = route('storefront.product', $product->slug);
                        $waText = rawurlencode('Hola ' . config('site.brand.name') . ', me interesa: ' . $product->name . ' → ' . $product->public_url);
                        $delay = ($loop->index % 3) * 150;
                    @endphp
                    <article class="group relative flex flex-col animate-slide-up-fade" style="animation-delay: {{ $delay }}ms">

                        {{-- Contenedor de Imagen --}}
                        <a href="{{ $productUrl }}" class="relative w-full aspect-[4/5] bg-black overflow-hidden mb-6 border border-white/10 group-hover:border-rose-500/40 transition-colors duration-700 block" aria-label="Ver {{ $product->name }}">
                            {{-- Efecto de oscurecimiento pasivo, se va en hover --}}
                            <div class="absolute inset-0 bg-ink/40 group-hover:bg-transparent transition-colors duration-[1.5s] pointer-events-none z-10"></div>

                            <x-sl.product-media
                                :product="$product"
                                class="w-full h-full object-cover grayscale opacity-80 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-105 transition-all duration-[1.5s] ease-out"
                            />

                            {{-- Badge Categoría --}}
                            @if ($product->category)
                                <div class="absolute top-4 left-4 z-20">
                                    <span class="text-[9px] text-white tracking-[0.2em] uppercase bg-black/60 backdrop-blur-md px-3 py-1.5 border border-white/10">
                                        {{ \Illuminate\Support\Str::upper($product->category->name) }}
                                    </span>
                                </div>
                            @endif

                            {{-- Precio flotante --}}
                            <div class="absolute bottom-4 right-4 z-20 overflow-hidden">
                                <span class="block text-sm font-mono tracking-widest bg-rose-900/90 text-white px-3 py-1.5 backdrop-blur-md border border-rose-500/30 transform translate-y-full opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500 ease-out">
                                    ${{ number_format($usd, 2, ',', '.') }}
                                </span>
                            </div>
                        </a>

                        {{-- Información del Producto --}}
                        <div class="flex-1 flex flex-col">
                            <h3 class="text-xl text-white font-serif italic mb-2 transition-colors duration-500">
                                <a href="{{ $productUrl }}" class="hover:text-rose-400 group-hover:text-rose-400 transition-colors">{{ $product->name }}</a>
                            </h3>
                            
                            @if ($product->description)
                                <p class="text-neutral-400 font-light text-xs line-clamp-2 mb-5 leading-relaxed">
                                    {{ $product->description }}
                                </p>
                            @endif

                            <div class="mt-auto">
                                {{-- Meta (Stock & Precio Base) --}}
                                <div class="flex justify-between items-end mb-4 border-t border-white/10 pt-4">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-2 text-[9px] tracking-[0.15em] uppercase font-bold {{ $available ? 'text-green-500' : 'text-neutral-500' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $available ? 'bg-green-500 animate-pulse' : 'bg-neutral-500' }}"></span>
                                            {{ $stockLabel }}
                                        </div>
                                        @if ($exchangeRate)
                                            <span class="text-neutral-600 font-mono text-[10px] tracking-widest">{{ number_format($bs, 2, ',', '.') }} Bs</span>
                                        @endif
                                    </div>
                                    <span class="text-lg text-white font-light block group-hover:hidden transition-all duration-500">
                                        ${{ number_format($usd, 2, ',', '.') }}
                                    </span>
                                </div>

                                {{-- Controles de Acción --}}
                                <div class="flex items-stretch gap-2 h-11" x-data="{ qty: {{ $product->unit_type === 'gram' ? '1' : '1' }} }">
                                    @if ($available)
                                        <div class="flex items-center border border-white/20 bg-black/40 text-white w-24 transition-colors hover:border-white/40">
                                            <button type="button" class="px-2 w-8 text-neutral-400 hover:text-rose-500 transition-colors h-full" @click="qty = Math.max(1, qty - 1)">&minus;</button>
                                            <input type="text" x-model="qty" class="w-full text-center bg-transparent border-none text-xs p-0 focus:ring-0 text-white font-mono" readonly>
                                            <button type="button" class="px-2 w-8 text-neutral-400 hover:text-rose-500 transition-colors h-full" @click="qty++">+</button>
                                        </div>
                                        <button type="button" class="flex-1 bg-white text-black font-medium tracking-[0.15em] text-[10px] uppercase hover:bg-rose-600 hover:text-white transition-all duration-300 border border-transparent" @click="addToCart(@js($product), qty)">
                                            Añadir
                                        </button>
                                    @else
                                        <button type="button" class="flex-1 bg-neutral-900 text-neutral-600 font-medium tracking-[0.15em] text-[10px] uppercase cursor-not-allowed border border-white/5" disabled>
                                            Agotado
                                        </button>
                                    @endif

                                    <a class="flex items-center justify-center w-11 border border-white/20 text-neutral-400 hover:border-green-500 hover:text-green-500 hover:bg-green-500/10 transition-all duration-300 group/wa" href="https://wa.me/{{ $wa }}?text={{ $waText }}" target="_blank" rel="noopener" aria-label="Consultar {{ $product->name }} por WhatsApp">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="group-hover/wa:scale-110 transition-transform"><path d="M12 2a10 10 0 00-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1012 2zm0 2a8 8 0 11-4.2 14.8l-.3-.2-2.8.8.8-2.7-.2-.3A8 8 0 0112 4zm4.5 9.9c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1l-.7.9c-.1.2-.3.2-.5.1a6.5 6.5 0 01-1.9-1.2 7.2 7.2 0 01-1.3-1.7c-.1-.2 0-.4.1-.5l.4-.5.2-.4v-.4l-.8-1.8c-.2-.4-.4-.4-.5-.4h-.5a1 1 0 00-.7.3c-.3.3-1 .9-1 2.2s1 2.6 1.1 2.8c.2.2 2 3.1 4.9 4.3.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.4-.6 1.6-1.2.2-.6.2-1 .1-1.2z"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Link final al catálogo --}}
            <div class="mt-20 lg:mt-32 flex justify-center animate-slide-up-fade">
                <a href="{{ route('storefront.catalog') }}" class="group flex items-center gap-4 text-white font-medium pb-2 border-b border-rose-600 hover:text-rose-400 hover:border-rose-400 transition-all duration-300">
                    <span class="tracking-[0.2em] text-xs uppercase">Ver Todo El Catálogo</span>
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="transition-transform duration-300 group-hover:translate-x-2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                </a>
            </div>
        @else
            <p class="text-neutral-500 font-light italic mt-12 border-l border-neutral-800 pl-4">Pronto publicaremos nuestro catálogo. Escríbenos por WhatsApp para conocer la disponibilidad actual.</p>
        @endif
    </div>
</section>
