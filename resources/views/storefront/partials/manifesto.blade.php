@php
    $about = config('site.about');
    $g = ($gallery ?? collect())->values();
@endphp
<section class="manifesto section-pad section-pad--large" aria-labelledby="manifesto-title">
    <div class="manifesto__intro">
        <p class="eyebrow eyebrow--light reveal">{{ $about['eyebrow'] }}</p>
        <h2 id="manifesto-title" class="reveal">{{ $about['heading'][0] }}<br><em>{{ $about['heading'][1] }}</em></h2>
        <div class="manifesto__image-card reveal">
            <x-sl.product-media :product="$g->get(1) ?? $g->get(0) ?? null" class="manifesto__photo" alt="Selección {{ config('site.brand.name') }}" />
            <div class="manifesto__photo-badge">
                <span aria-hidden="true">✦</span>
                <span>CALIDAD GARANTIZADA</span>
            </div>
        </div>
    </div>

    <div class="manifesto__body reveal">
        @foreach ($about['paragraphs'] as $p)
            <p>{{ $p }}</p>
        @endforeach
        <div class="manifesto__staccato">
            @foreach ($about['staccato'] as $s)
                <span>{{ $s }}</span>
            @endforeach
        </div>
        <div class="manifesto__mini-gallery">
            @foreach ($g->slice(2, 2) as $mp)
                <div class="mini-card">
                    <x-sl.product-media :product="$mp" size="thumb" alt="{{ $mp->name }}" />
                    <span>{{ $mp->category->name ?? 'Colección' }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>
