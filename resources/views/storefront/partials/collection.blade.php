@php $g = ($gallery ?? collect())->values()->take(6); @endphp
@if ($g->count())
<section id="album-coleccion" class="fresh section-pad section-pad--large" aria-labelledby="coleccion-title">
    <div class="fresh__heading">
        <div class="reveal">
            <p class="eyebrow">COLECCIÓN OFICIAL · {{ \Illuminate\Support\Str::upper(config('site.brand.name')) }}</p>
            <h2 id="coleccion-title">Descubre la<br><em>colección completa.</em></h2>
        </div>
        <span>DESLIZA PARA VER MÁS →</span>
    </div>
    <div class="fresh__scroller">
        @foreach ($g as $p)
            @php $waText = rawurlencode('Hola ' . config('site.brand.name') . ', me interesa: ' . $p->name); @endphp
            <a class="fresh-item fresh-item--card" href="https://wa.me/{{ config('site.contact.whatsapp') }}?text={{ $waText }}"
                target="_blank" rel="noopener" aria-label="Consultar {{ $p->name }}">
                <span class="fresh-item__img-wrapper">
                    <x-sl.product-media :product="$p" class="fresh-item__img" alt="{{ $p->name }}" />
                </span>
                <span class="fresh-item__caption">
                    <span class="fresh-item__num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span>
                        <h3>{{ $p->name }}</h3>
                        <span class="fresh-item__cat">{{ $p->category->name ?? 'Colección' }}</span>
                        <span class="fresh-item__quote">{{ $p->description ? \Illuminate\Support\Str::limit($p->description, 60) : 'Consulta disponibilidad y precio por WhatsApp.' }}</span>
                    </span>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                </span>
            </a>
        @endforeach
    </div>
</section>
@endif
