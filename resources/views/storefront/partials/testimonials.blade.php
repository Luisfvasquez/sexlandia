<section class="testimonials section-pad section-pad--large" aria-labelledby="testimonials-title">
    <p class="eyebrow reveal">LO QUE DICEN NUESTROS CLIENTES</p>
    <h2 id="testimonials-title" class="reveal" style="margin-top:18px;">Curiosidad<br><em>sin arrepentimientos.</em></h2>
    <div class="testimonials__cards-grid">
        @foreach (config('site.testimonials') as $t)
            <figure class="testimonial-card reveal">
                <div class="testimonial-card__stars" aria-label="5 de 5 estrellas">
                    @for ($i = 0; $i < 5; $i++)
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l2.4 6.9L22 9l-5.6 4.3L18.5 22 12 17.8 5.5 22l2.1-8.7L2 9l7.6-.1z"/></svg>
                    @endfor
                </div>
                <blockquote>“{{ $t['quote'] }}”</blockquote>
                <figcaption>
                    <cite>— {{ $t['author'] }}</cite>
                    <span class="testimonial-card__product">Compró: {{ $t['product'] }}</span>
                </figcaption>
            </figure>
        @endforeach
    </div>
</section>
