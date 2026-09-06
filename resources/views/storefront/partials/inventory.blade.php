@php $inv = ($inventoryList ?? collect())->take(6); @endphp
@if ($inv->count())
<section id="inventario" class="inventory section-pad section-pad--large" aria-labelledby="inventario-title">
    <div class="inventory__header">
        <div class="reveal">
            <p class="eyebrow eyebrow--light">DISPONIBILIDAD EN TIENDA {{ \Illuminate\Support\Str::upper(config('site.location.city')) }}</p>
            <span class="inventory__live">● STOCK VERIFICADO</span>
        </div>
        <h2 id="inventario-title" class="reveal">Lo ves aquí.<br><em>Lo encuentras allá.</em></h2>
        <div class="reveal">
            <p>Nuestro catálogo muestra la disponibilidad real de {{ config('site.brand.name') }}. Si aparece aquí, está listo para retiro en tienda o delivery.</p>
        </div>
    </div>
    <div class="inventory__list">
        @foreach ($inv as $p)
            @php
                $stock = $p->inventory->stock ?? 0;
                $label = $stock <= 0
                    ? 'CONSULTAR'
                    : ($p->unit_type === 'gram'
                        ? 'EN STOCK — ' . number_format($stock / 1000, 2, ',', '.') . ' KG'
                        : 'EN STOCK — ' . (int) $stock . ' UND');
            @endphp
            <div class="reveal">
                <span>{{ \Illuminate\Support\Str::upper($p->name) }}</span>
                <span>{{ $label }}</span>
            </div>
        @endforeach
    </div>
    <a class="arrow-link arrow-link--inverse" href="#productos">VER TODOS LOS PRODUCTOS
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
    </a>
</section>
@endif
