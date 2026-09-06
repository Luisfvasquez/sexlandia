@php $cats = $categories ?? collect(); @endphp
@if ($cats->count())
<section id="categorias" class="relative py-24 lg:py-40 bg-ink overflow-hidden border-t border-white/5" aria-labelledby="categorias-title">
    
    <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
        {{-- Intro --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-20 lg:mb-32 space-y-8 md:space-y-0 animate-slide-up-fade">
            <div class="space-y-4">
                <p class="text-rose-600 text-xs tracking-[0.3em] uppercase font-bold">
                    Compra por Sensación
                </p>
                <h2 id="categorias-title" class="text-5xl lg:text-7xl leading-[1.05] text-white font-bold tracking-tight">
                    No todo empieza<br>
                    <em class="font-serif italic text-rose-500 font-normal">con una categoría.</em>
                </h2>
            </div>
            <p class="text-neutral-400 font-light max-w-sm text-sm lg:text-base hidden md:block">
                Explora nuestras selecciones diseñadas para despertar cada sentido, sin las etiquetas tradicionales.
            </p>
        </div>

        {{-- Grilla Escalonada (Staggered Gallery) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-16 gap-x-8 items-start">
            @foreach ($cats->take(6) as $category)
                @php
                    $isMiddleCol = $loop->index % 3 === 1;
                    $delay = 200 + ($loop->index * 150);
                @endphp
                <a href="{{ route('storefront.catalog', ['category' => $category->id]) }}"
                    class="group relative block w-full {{ $isMiddleCol ? 'lg:mt-32' : '' }} animate-slide-up-fade"
                    style="animation-delay: {{ $delay }}ms;"
                    aria-label="Ver categoría {{ $category->name }}">
                    
                    {{-- Contenedor de la Imagen --}}
                    <div class="relative w-full aspect-[4/5] overflow-hidden bg-black mb-6">
                        <div class="absolute inset-0 ring-1 ring-inset ring-white/10 z-10 pointer-events-none transition-colors duration-700 group-hover:ring-rose-500/40"></div>
                        
                        {{-- Overlay Oscuro (Desaparece en hover) --}}
                        <div class="absolute inset-0 bg-ink/60 group-hover:bg-transparent transition-colors duration-[1.5s] z-10 pointer-events-none"></div>

                        <x-sl.product-media 
                            :image="$category->cover ?? null" 
                            :alt="'Categoría ' . $category->name" 
                            class="w-full h-full object-cover grayscale opacity-70 transition-all duration-[1.5s] ease-out group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-105" 
                        />

                        {{-- Número decorativo --}}
                        <div class="absolute top-6 right-6 z-20 overflow-hidden">
                            <span class="block font-serif italic text-rose-500/50 text-2xl group-hover:text-white transition-colors duration-700">
                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                    </div>

                    {{-- Texto debajo de la imagen --}}
                    <div class="relative z-20 px-2">
                        <h3 class="text-2xl lg:text-3xl font-sans font-bold text-white tracking-wide mb-2 group-hover:text-rose-400 transition-colors duration-500">
                            {{ \Illuminate\Support\Str::upper($category->name) }}
                        </h3>
                        
                        {{-- Descripción animada: Oculta por defecto, aparece y sube en hover --}}
                        <div class="overflow-hidden">
                            <p class="text-neutral-400 font-light text-sm transform translate-y-full opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500 ease-out">
                                {{ $category->description ?: (($category->visible_count ?? 0) . ' experiencias disponibles') }}
                            </p>
                        </div>

                        <div class="mt-4 flex items-center gap-2 text-white font-medium text-xs tracking-[0.2em] uppercase opacity-0 -translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-500 delay-100">
                            <span>Explorar</span>
                            <span class="h-[1px] w-6 bg-rose-500 group-hover:w-12 transition-all duration-500"></span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
