@php
    $navBase = route('storefront');
    $brand = config('site.brand.name');
    $city = config('site.location.city');

    $searchTerm = trim((string) request('search'));
    $activeCat = request('category', 'all');
    $activeCategory = ($activeCat !== 'all')
        ? ($categories->firstWhere('id', (int) $activeCat) ?? null)
        : null;
    $currentPage = (int) request('page', 1);

    // Título / descripción / H1 dependientes del filtro activo.
    if ($searchTerm !== '') {
        $seoTitle = 'Resultados para «' . $searchTerm . '» · ' . $brand;
        $seoDesc = 'Productos que coinciden con «' . $searchTerm . '» en el catálogo de ' . $brand . ', sex shop en ' . $city . '.';
    } elseif ($activeCategory) {
        $seoTitle = $activeCategory->name . ' en ' . $city . ' · ' . $brand;
        $seoDesc = 'Compra ' . \Illuminate\Support\Str::lower($activeCategory->name) . ' en ' . $city . ': stock verificado, marcas originales, empaque discreto y delivery. Catálogo de ' . $brand . '.';
    } else {
        $seoTitle = 'Sex shop en ' . $city . ' · Catálogo completo · ' . $brand;
        $seoDesc = 'Catálogo completo de ' . $brand . ', sexshop en ' . $city . ': succionadores, vibradores, lubricantes y accesorios con stock verificado, empaque discreto y delivery.';
    }

    // Canónica: consolida búsquedas y paginado hacia la URL limpia del filtro,
    // siempre sobre el dominio público (config/site.php → url).
    $catalogCanonical = rtrim(config('site.url'), '/') . '/catalogo'
        . ($activeCategory ? '?category=' . $activeCategory->id : '');

    // Las páginas de búsqueda y las paginadas (>1) no aportan contenido único: follow pero no index.
    $catalogRobots = ($searchTerm !== '' || $currentPage > 1)
        ? 'noindex,follow'
        : 'index,follow,max-image-preview:large';

    $extraSchemas = [
        [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $seoTitle,
            'description' => $seoDesc,
            'url' => $catalogCanonical,
            'isPartOf' => ['@id' => rtrim(config('site.url'), '/') . '/#website'],
            'inLanguage' => 'es-VE',
        ],
        [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_values(array_filter([
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => route('storefront')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Catálogo', 'item' => route('storefront.catalog')],
                $activeCategory
                    ? ['@type' => 'ListItem', 'position' => 3, 'name' => $activeCategory->name, 'item' => $catalogCanonical]
                    : null,
            ])),
        ],
    ];
@endphp

@extends('storefront.layout')

@section('title', $seoTitle)
@section('meta_description', $seoDesc)
@section('canonical', $catalogCanonical)
@section('robots', $catalogRobots)

@include('storefront.partials.seo-jsonld', ['extraSchemas' => $extraSchemas, 'includeProductList' => true])

@section('content')
    @php
        $wa = config('site.contact.whatsapp');
    @endphp

    <section class="relative bg-ink border-t border-white/5 overflow-hidden pt-28 sm:pt-32 pb-24 lg:pb-32">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_rgba(141,38,61,0.08),_transparent_60%)] pointer-events-none"></div>

        <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8">
            {{-- Cabecera --}}
            <div class="mb-12 lg:mb-16 animate-slide-up-fade">
                <p class="text-rose-600 text-[10px] tracking-[0.3em] uppercase font-bold mb-6">
                    Sex shop en {{ $city }} · Stock verificado
                </p>
                <h1 class="text-4xl sm:text-5xl lg:text-7xl leading-[1.05] text-white font-bold tracking-tight">
                    @if ($searchTerm !== '')
                        Resultados para<br>
                        <em class="font-serif italic text-rose-500 font-normal">«{{ $searchTerm }}».</em>
                    @elseif ($activeCategory)
                        {{ $activeCategory->name }}<br>
                        <em class="font-serif italic text-rose-500 font-normal">en {{ $city }}.</em>
                    @else
                        Todo lo disponible<br>
                        <em class="font-serif italic text-rose-500 font-normal">hoy en {{ $city }}.</em>
                    @endif
                </h1>
            </div>

            {{-- Filtros --}}
            <form method="GET" action="{{ route('storefront.catalog') }}" class="mb-14 flex flex-col gap-5 animate-slide-up-fade [animation-delay:150ms]">
                <div class="flex items-center gap-3 border border-white/15 bg-black/40 px-4 py-2 focus-within:border-rose-500/50 transition-colors max-w-2xl">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="text-neutral-500 shrink-0" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o marca…"
                        class="flex-1 bg-transparent border-0 text-sm text-white placeholder-neutral-600 py-1.5 focus:ring-0 focus:outline-none min-w-0"
                        aria-label="Buscar productos">
                    @if ($activeCat !== 'all')
                        <input type="hidden" name="category" value="{{ $activeCat }}">
                    @endif
                    <button type="submit" class="shrink-0 bg-white text-black hover:bg-rose-600 hover:text-white transition-colors px-5 py-2 text-[10px] font-bold tracking-[0.15em] uppercase">Buscar</button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('storefront.catalog', array_filter(['search' => request('search')])) }}"
                       @class([
                           'px-4 py-2 text-[11px] font-bold tracking-[0.08em] uppercase border transition-colors',
                           'bg-rose-600 text-white border-rose-600' => $activeCat === 'all',
                           'text-neutral-400 border-white/15 hover:text-white hover:border-white/40' => $activeCat !== 'all',
                       ])>Todas</a>
                    @foreach ($categories as $category)
                        <a href="{{ route('storefront.catalog', array_filter(['search' => request('search'), 'category' => $category->id])) }}"
                           @class([
                               'px-4 py-2 text-[11px] font-bold tracking-[0.08em] uppercase border transition-colors',
                               'bg-rose-600 text-white border-rose-600' => (string) $activeCat === (string) $category->id,
                               'text-neutral-400 border-white/15 hover:text-white hover:border-white/40' => (string) $activeCat !== (string) $category->id,
                           ])>{{ $category->name }}</a>
                    @endforeach
                </div>
            </form>

            @if ($products->count())
                <div class="grid grid-cols-1 min-[480px]:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-x-6 lg:gap-y-14">
                    @foreach ($products as $product)
                        @php
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
                        @endphp
                        <article class="group relative flex flex-col animate-slide-up-fade" style="animation-delay: {{ ($loop->index % 8) * 60 }}ms">
                            <a href="{{ $productUrl }}" class="relative w-full aspect-[4/5] bg-black overflow-hidden mb-5 border border-white/10 group-hover:border-rose-500/40 transition-colors duration-700 block" aria-label="Ver {{ $product->name }}">
                                <div class="absolute inset-0 bg-ink/40 group-hover:bg-transparent transition-colors duration-[1.5s] pointer-events-none z-10"></div>
                                <x-sl.product-media :product="$product"
                                    class="w-full h-full object-cover grayscale opacity-80 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-105 transition-all duration-[1.5s] ease-out" />
                                @if ($product->category)
                                    <div class="absolute top-3 left-3 z-20">
                                        <span class="text-[9px] text-white tracking-[0.2em] uppercase bg-black/60 backdrop-blur-md px-2.5 py-1 border border-white/10">
                                            {{ \Illuminate\Support\Str::upper($product->category->name) }}
                                        </span>
                                    </div>
                                @endif
                            </a>

                            <div class="flex-1 flex flex-col">
                                <h3 class="text-lg text-white font-serif italic mb-2 leading-tight transition-colors duration-500">
                                    <a href="{{ $productUrl }}" class="hover:text-rose-400 group-hover:text-rose-400 transition-colors">{{ $product->name }}</a>
                                </h3>
                                @if ($product->description)
                                    <p class="text-neutral-400 font-light text-xs line-clamp-2 mb-4 leading-relaxed">
                                        {{ \Illuminate\Support\Str::limit($product->description, 78) }}
                                    </p>
                                @endif

                                <div class="mt-auto">
                                    <div class="flex justify-between items-end mb-4 border-t border-white/10 pt-3">
                                        <div class="flex items-center gap-2 text-[9px] tracking-[0.15em] uppercase font-bold {{ $available ? 'text-green-500' : 'text-neutral-500' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $available ? 'bg-green-500 animate-pulse' : 'bg-neutral-500' }}"></span>
                                            {{ $stockLabel }}
                                        </div>
                                    </div>

                                    <div x-data="{ qty: 1 }" class="flex flex-col gap-2">
                                        @if ($available)
                                            <div class="flex items-center gap-2 h-10">
                                                <div class="flex items-center border border-white/20 bg-black/40 text-white w-24 shrink-0 h-full transition-colors hover:border-white/40">
                                                    <button type="button" class="px-2 w-8 text-neutral-400 hover:text-rose-500 transition-colors h-full" @click="qty = Math.max(1, qty - 1)" aria-label="Restar">&minus;</button>
                                                    <input type="text" x-model="qty" inputmode="numeric" class="w-full text-center bg-transparent border-none text-xs p-0 focus:ring-0 text-white font-mono" aria-label="Cantidad de {{ $product->name }}">
                                                    <button type="button" class="px-2 w-8 text-neutral-400 hover:text-rose-500 transition-colors h-full" @click="qty++" aria-label="Sumar">+</button>
                                                </div>
                                                <button type="button" class="flex-1 h-full bg-white text-black font-medium tracking-[0.15em] text-[10px] uppercase hover:bg-rose-600 hover:text-white transition-all duration-300 border border-transparent" @click="addToCart(@js($product->toCartPayload()), qty)">
                                                    Añadir
                                                </button>
                                            </div>
                                        @else
                                            <button type="button" class="h-10 bg-neutral-900 text-neutral-600 font-medium tracking-[0.15em] text-[10px] uppercase cursor-not-allowed border border-white/5" disabled>
                                                Agotado
                                            </button>
                                        @endif
                                        <a class="flex items-center justify-center gap-2 h-10 border border-white/20 text-neutral-400 text-[10px] tracking-[0.15em] uppercase hover:border-green-500 hover:text-green-500 hover:bg-green-500/10 transition-all duration-300"
                                            href="https://wa.me/{{ $wa }}?text={{ $waText }}" target="_blank" rel="noopener" aria-label="Consultar {{ $product->name }} por WhatsApp">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 00-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1012 2zm0 2a8 8 0 11-4.2 14.8l-.3-.2-2.8.8.8-2.7-.2-.3A8 8 0 0112 4z"/></svg>
                                            WhatsApp
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-16">
                    {{ $products->onEachSide(1)->links('storefront.partials.pagination') }}
                </div>
            @else
                <p class="text-neutral-400 font-light text-base border-l border-rose-500/30 pl-5 max-w-xl">
                    No encontramos productos con esos filtros. Prueba con otra búsqueda o
                    <a href="{{ route('storefront.catalog') }}" class="text-rose-400 border-b border-rose-500/40 hover:text-rose-300">ver todo el catálogo</a>.
                </p>
            @endif
        </div>
    </section>
@endsection
