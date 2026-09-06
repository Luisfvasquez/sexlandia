<section class="email-section section-pad section-pad--large" aria-labelledby="newsletter-title">
    <div class="reveal">
        <p class="eyebrow">NOVEDADES &amp; LANZAMIENTOS</p>
        <h2 id="newsletter-title">Sigue<br><em>curioseando.</em></h2>
        <p>Nuevas llegadas, reposiciones de stock y promociones exclusivas directo a tu correo.</p>
    </div>
    <form method="get" action="https://wa.me/{{ config('site.contact.whatsapp') }}" target="_blank" rel="noopener"
        onsubmit="this.action='https://wa.me/{{ config('site.contact.whatsapp') }}?text='+encodeURIComponent('Hola, quiero recibir novedades de {{ config('site.brand.name') }}. Mi correo: '+ (this.email.value||''));">
        <label for="nl-email">Tu correo</label>
        <input id="nl-email" name="email" type="email" placeholder="hola@correo.com" autocomplete="email">
        <button type="submit">UNIRME
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
        </button>
    </form>
</section>
