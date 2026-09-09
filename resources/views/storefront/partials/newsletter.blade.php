<section class="relative min-h-[100dvh] flex items-center py-24 lg:py-32 bg-ink border-t border-white/5 overflow-hidden" aria-labelledby="newsletter-title">
    {{-- Glow misterioso central --}}
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
        <div class="w-[80vw] h-[40vw] bg-[radial-gradient(ellipse_at_center,_rgba(141,38,61,0.08),_transparent_70%)]"></div>
    </div>

    <div class="relative w-full max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            
            {{-- Copy --}}
            <div class="animate-slide-up-fade">
                <p class="text-rose-600 text-[10px] tracking-[0.3em] uppercase font-bold mb-6">NOVEDADES &amp; LANZAMIENTOS</p>
                <h2 id="newsletter-title" class="text-4xl sm:text-6xl lg:text-[7rem] leading-[1.05] text-white font-bold tracking-tight mb-8">
                    Sigue<br>
                    <em class="font-serif italic text-rose-500 font-normal">curioseando.</em>
                </h2>
                <p class="text-neutral-400 font-light text-sm lg:text-base leading-relaxed max-w-md border-l border-white/10 pl-5">
                    Nuevas llegadas, reposiciones de stock y promociones exclusivas directo a tu correo.
                </p>
            </div>

            {{-- Form --}}
            <div class="animate-slide-up-fade [animation-delay:200ms]">
                <form method="get" action="https://wa.me/{{ config('site.contact.whatsapp') }}" target="_blank" rel="noopener"
                    onsubmit="this.action='https://wa.me/{{ config('site.contact.whatsapp') }}?text='+encodeURIComponent('Hola, quiero recibir novedades de {{ config('site.brand.name') }}. Mi correo: '+ (this.email.value||''));"
                    class="relative group"
                >
                    <label for="nl-email" class="text-xs font-mono tracking-widest text-neutral-500 uppercase block mb-2">Tu correo</label>
                    <div class="relative border-b border-white/20 group-hover:border-rose-500 transition-colors duration-500">
                        <input id="nl-email" name="email" type="email" placeholder="hola@correo.com" autocomplete="email" required
                            class="w-full bg-transparent border-0 text-2xl lg:text-3xl font-serif italic text-white placeholder-neutral-700 py-4 focus:ring-0 focus:outline-none pr-24 sm:pr-32"
                        >
                        <button type="submit" class="absolute right-0 bottom-4 flex items-center gap-3 text-xs uppercase tracking-[0.2em] font-bold text-white hover:text-rose-500 transition-colors cursor-pointer">
                            UNIRME
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</section>
