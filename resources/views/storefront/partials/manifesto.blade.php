@php
    $about = config('site.about');
    $g = ($gallery ?? collect())->values();
@endphp
<section id="nosotros" class="relative min-h-[100dvh] flex items-center py-24 lg:py-32 bg-ink overflow-hidden border-t border-white/5">
    {{-- Textura sutil de fondo --}}
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_right,_rgba(141,38,61,0.12),_transparent_70%)] pointer-events-none"></div>

    <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-8 items-center">
            
            {{-- Columna Izquierda: Título e Imagen Principal --}}
            <div class="lg:col-span-5 flex flex-col space-y-12">
                <div class="space-y-4 animate-slide-up-fade">
                    <p class="text-rose-600 text-xs tracking-[0.3em] uppercase font-bold">
                        {{ $about['eyebrow'] }}
                    </p>
                    <h2 class="text-5xl lg:text-7xl leading-[1.05] text-white font-bold tracking-tight">
                        {{ $about['heading'][0] }}<br>
                        <em class="font-serif italic text-rose-500 font-normal">{{ $about['heading'][1] }}</em>
                    </h2>
                </div>

                @if ($mainImg = ($g->get(1) ?? $g->get(0)))
                    <div class="relative w-4/5 sm:w-2/3 lg:w-4/5 aspect-[3/4] overflow-hidden group animate-slide-up-fade [animation-delay:200ms]">
                        {{-- Sin shadows, puro marco interno elegante y oscurecimiento --}}
                        <div class="absolute inset-0 ring-1 ring-inset ring-white/10 z-10 pointer-events-none transition-colors duration-700 group-hover:ring-rose-500/30"></div>
                        
                        <x-sl.product-media 
                            :product="$mainImg" 
                            size="md" 
                            class="w-full h-full object-cover grayscale opacity-70 transition-all duration-[1.5s] ease-out group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-105" 
                            alt="Selección {{ config('site.brand.name') }}" 
                        />
                        
                        <div class="absolute bottom-0 left-0 p-6 z-20">
                            <p class="text-[9px] text-white tracking-[0.2em] uppercase bg-black/60 backdrop-blur-md px-4 py-2 border border-white/10">
                                ✦ Calidad Garantizada
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Columna Derecha: Texto y Detalles --}}
            <div class="lg:col-span-6 lg:col-start-7 flex flex-col space-y-12">
                <div class="space-y-6 text-neutral-400 text-lg lg:text-xl font-light leading-relaxed animate-slide-up-fade [animation-delay:400ms]">
                    @foreach ($about['paragraphs'] as $p)
                        <p>{{ $p }}</p>
                    @endforeach
                </div>

                {{-- Staccato (Palabras clave) con líneas expansivas --}}
                <div class="flex flex-col space-y-5 pt-8 border-t border-white/10 animate-slide-up-fade [animation-delay:600ms]">
                    @foreach ($about['staccato'] as $s)
                        <div class="flex items-center space-x-4 group cursor-default">
                            <span class="h-[1px] w-8 bg-rose-900 group-hover:w-16 transition-all duration-700 ease-out"></span>
                            <span class="text-white tracking-[0.2em] text-sm font-medium">{{ $s }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Mini Galería Minimalista (Cero sombras) --}}
                @if($g->count() > 2)
                    <div class="pt-8 grid grid-cols-2 gap-8 animate-slide-up-fade [animation-delay:800ms]">
                        @foreach ($g->slice(2, 2) as $mp)
                            <a href="#" class="group block">
                                <div class="relative aspect-square overflow-hidden mb-4 bg-black border border-white/5">
                                    <div class="absolute inset-0 bg-ink/60 group-hover:bg-transparent transition-colors duration-700 z-10 pointer-events-none"></div>
                                    <x-sl.product-media 
                                        :product="$mp" 
                                        size="thumb" 
                                        class="w-full h-full object-cover transition-transform duration-[1.5s] ease-out group-hover:scale-110 saturate-50 group-hover:saturate-100" 
                                        alt="{{ $mp->name }}" 
                                    />
                                </div>
                                <p class="text-[10px] text-rose-500 tracking-[0.15em] uppercase mb-1">
                                    {{ $mp->category->name ?? 'Colección' }}
                                </p>
                                <p class="text-sm text-neutral-300 truncate font-light">
                                    {{ $mp->name }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</section>
