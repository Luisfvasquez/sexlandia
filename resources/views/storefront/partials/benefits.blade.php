<section class="benefits section-pad section-pad--large" aria-labelledby="benefits-title">
    <h2 id="benefits-title" class="reveal">Bueno<br><em>saberlo.</em></h2>
    <div class="benefits__rows">
        @foreach (config('site.benefits') as [$n, $title, $text])
            <div class="reveal">
                <span>{{ $n }}</span>
                <h3>{{ $title }}</h3>
                <p>{{ $text }}</p>
            </div>
        @endforeach
    </div>
</section>
