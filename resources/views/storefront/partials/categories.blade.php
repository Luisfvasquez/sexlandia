@php $cats = $categories ?? collect(); @endphp
@if ($cats->count())
<section id="categorias" class="categories section-pad section-pad--large" aria-labelledby="categorias-title">
    <div class="section-intro section-intro--split">
        <p class="eyebrow reveal">COMPRA POR SENSACIÓN</p>
        <h2 id="categorias-title" class="reveal">No todo empieza<br>con una categoría.</h2>
    </div>
    <div class="categories__grid">
        @foreach ($cats->take(6) as $category)
            @php
                $mod = $loop->index % 6;
                $tile = $mod === 5 ? 0 : $mod + 1;
            @endphp
            <a href="{{ route('storefront.catalog', ['category' => $category->id]) }}"
                class="category-tile category-tile--{{ $tile }} reveal" aria-label="Ver categoría {{ $category->name }}">
                <span class="category-tile__bg">
                    <x-sl.product-media :image="$category->cover ?? null" :alt="'Categoría ' . $category->name" class="category-tile__img" />
                    <span class="category-tile__overlay"></span>
                </span>
                <span class="category-tile__num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <span class="category-tile__copy">
                    <h3>{{ \Illuminate\Support\Str::upper($category->name) }}</h3>
                    <p>{{ $category->description ?: (($category->visible_count ?? 0) . ' productos disponibles') }}</p>
                    <span class="category-tile__explore">EXPLORAR
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </span>
                </span>
            </a>
        @endforeach
    </div>
</section>
@endif
