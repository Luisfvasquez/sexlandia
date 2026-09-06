<section id="faq" class="faq section-pad section-pad--large" aria-labelledby="faq-title">
    <div class="reveal">
        <p class="eyebrow">SIN PENA</p>
        <h2 id="faq-title">Cosas que<br><em>nos preguntan.</em></h2>
    </div>
    <div class="faq__list">
        @foreach (config('site.faq') as $i => [$q, $a])
            <div class="faq__item {{ $i === 0 ? 'is-open' : '' }}">
                <button type="button" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">
                    <span>{{ $q }}</span>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="faq__answer"><p>{{ $a }}</p></div>
            </div>
        @endforeach
    </div>
</section>
