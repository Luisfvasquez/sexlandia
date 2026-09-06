@php $g = ($gallery ?? collect())->values(); @endphp
<section class="interrupt section-pad" aria-labelledby="interrupt-title">
    <div class="interrupt__content section-pad">
        <h2 id="interrupt-title" class="reveal">No necesitas<br><em>una razón</em><br>para tener curiosidad.</h2>
    </div>
    @if ($g->count())
        <div class="interrupt__marquee-container">
            <div class="interrupt__marquee">
                @foreach ($g->concat($g) as $p)
                    <div class="marquee-card">
                        <x-sl.product-media :product="$p" size="thumb" alt="{{ $p->name }}" />
                        <span>{{ \Illuminate\Support\Str::limit($p->name, 22) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
