@php
    $rate = $exchangeRate ? (float) str_replace(',', '.', $exchangeRate) : 1;
    if ($rate <= 0) { $rate = 1; }
    $wa = config('site.contact.whatsapp');
@endphp
@if ($featured ?? null)
@php
    $imgs = $featured->images;
    $usd = $featured->display_price;
    $bs = $featured->display_price * $rate;
    $inStock = ! $featured->track_inventory
        || $featured->allow_negative_stock
        || ($featured->inventory && $featured->inventory->stock > 0);
    $waText = rawurlencode('Hola ' . config('site.brand.name') . ', quiero consultar por: ' . $featured->name);
@endphp
<section id="destacado" class="relative min-h-[100dvh] flex items-center py-24 lg:py-0 bg-ink border-t border-white/5 overflow-hidden" aria-labelledby="destacado-title">
    {{-- Glow sutil de fondo --}}
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,_rgba(141,38,61,0.05),_transparent_60%)] pointer-events-none"></div>

    <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-8 items-center" data-featured-gallery>
            
            {{-- Columna 1: Título (3/12) --}}
            <div class="lg:col-span-3 space-y-6 animate-slide-up-fade">
                <p class="text-rose-600 text-xs tracking-[0.3em] uppercase font-bold">
                    Destacado de Temporada
                </p>
                <h2 id="destacado-title" class="text-5xl lg:text-6xl text-white font-bold tracking-tight leading-[1.05]">
                    Diseñado<br>para <em class="font-serif italic text-rose-500 font-normal">desearlo.</em>
                </h2>
            </div>

            {{-- Columna 2: Arte / Imagen Principal (5/12) --}}
            <div class="lg:col-span-5 relative group animate-slide-up-fade [animation-delay:200ms]">
                {{-- Glow trasero en lugar de sombra --}}
                <div class="absolute -inset-10 bg-rose-500/10 blur-[80px] rounded-full pointer-events-none"></div>
                
                <div class="relative w-full aspect-[4/5] bg-black border border-white/10 overflow-hidden">
                    {{-- Overlay misterioso que desaparece en hover --}}
                    <div class="absolute inset-0 bg-ink/40 group-hover:bg-transparent transition-colors duration-700 pointer-events-none z-10"></div>
                    
                    <x-sl.product-media 
                        :product="$featured" 
                        size="full" 
                        class="w-full h-full object-cover transition-transform duration-[2s] ease-out group-hover:scale-105" 
                        data-featured-main 
                        alt="{{ $featured->name }}" 
                    />
                </div>
            </div>

            {{-- Columna 3: Detalles y CTA (4/12) --}}
            <div class="lg:col-span-4 flex flex-col space-y-8 animate-slide-up-fade [animation-delay:400ms]">
                
                <div>
                    <p class="text-rose-500 text-[9px] tracking-[0.2em] uppercase mb-3">
                        {{ config('site.brand.name') }} Selección · {{ $featured->category->name ?? 'Colección' }}
                    </p>
                    <h3 class="text-3xl lg:text-4xl text-white font-serif italic mb-4 leading-tight">
                        {{ $featured->name }}
                    </h3>
                    <p class="text-neutral-400 font-light text-sm leading-relaxed">
                        {{ $featured->description ?: 'Una pieza destacada de nuestra colección oficial, rigurosamente seleccionada y disponible hoy en tienda.' }}
                    </p>
                </div>

                @if ($imgs->count() > 1)
                    <div class="flex gap-4" role="group" aria-label="Fotos del producto">
                        @foreach ($imgs->take(4) as $img)
                            <button type="button" 
                                data-featured-thumb 
                                data-src="{{ $img->url }}"
                                class="relative w-14 h-14 overflow-hidden border border-white/10 hover:border-rose-500 focus:border-rose-500 focus:outline-none transition-colors duration-300 group {{ $loop->first ? 'ring-1 ring-offset-2 ring-offset-ink ring-rose-500' : '' }}" 
                                aria-label="Ver foto {{ $loop->iteration }}">
                                <div class="absolute inset-0 bg-black/60 group-hover:bg-transparent transition-colors duration-300"></div>
                                <img src="{{ $img->thumb_url }}" class="w-full h-full object-cover" alt="" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- Precio y Stock --}}
                <div class="border-y border-white/10 py-6 my-6 flex justify-between items-end">
                    <div class="flex flex-col">
                        <span class="text-4xl font-light text-white tracking-tight">${{ number_format($usd, 2, ',', '.') }}</span>
                        @if ($exchangeRate)
                            <span class="text-xs text-neutral-500 mt-1 font-mono tracking-widest">{{ number_format($bs, 2, ',', '.') }} Bs</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 text-[10px] tracking-[0.15em] uppercase {{ $inStock ? 'text-green-500' : 'text-neutral-500' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $inStock ? 'bg-green-500 animate-pulse' : 'bg-neutral-500' }}"></span>
                        {{ $inStock ? 'En Stock' : 'Agotado' }}
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="space-y-4" x-data="{ qty: 1 }">
                    @if ($inStock)
                        <div class="flex items-stretch gap-4 h-12">
                            <div class="flex items-center border border-white/20 bg-black/40 text-white w-28">
                                <button type="button" class="px-4 text-neutral-400 hover:text-white transition-colors h-full" @click="qty = Math.max(1, qty - 1)">&minus;</button>
                                <input type="text" x-model="qty" class="w-full text-center bg-transparent border-none text-sm p-0 focus:ring-0 text-white" readonly>
                                <button type="button" class="px-4 text-neutral-400 hover:text-white transition-colors h-full" @click="qty++">+</button>
                            </div>
                            
                            <button type="button" class="flex-1 bg-white text-black font-medium tracking-[0.15em] text-xs uppercase hover:bg-rose-600 hover:text-white transition-all duration-300 border border-transparent" @click="addToCart(@js($featured), qty)">
                                Añadir al carrito
                            </button>
                        </div>
                    @endif
                    
                    <a class="flex items-center justify-center gap-3 w-full h-12 border border-white/20 text-neutral-300 font-medium tracking-[0.15em] text-xs uppercase hover:border-white hover:text-white hover:bg-white/5 transition-all duration-300" href="https://wa.me/{{ $wa }}?text={{ $waText }}" target="_blank" rel="noopener">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 00-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1012 2zm0 2a8 8 0 11-4.2 14.8l-.3-.2-2.8.8.8-2.7-.2-.3A8 8 0 0112 4zm4.5 9.9c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1l-.7.9c-.1.2-.3.2-.5.1a6.5 6.5 0 01-1.9-1.2 7.2 7.2 0 01-1.3-1.7c-.1-.2 0-.4.1-.5l.4-.5.2-.4v-.4l-.8-1.8c-.2-.4-.4-.4-.5-.4h-.5a1 1 0 00-.7.3c-.3.3-1 .9-1 2.2s1 2.6 1.1 2.8c.2.2 2 3.1 4.9 4.3.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.4-.6 1.6-1.2.2-.6.2-1 .1-1.2z"/></svg>
                        Consultar por WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
