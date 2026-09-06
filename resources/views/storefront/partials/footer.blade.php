@php
    $split = config('site.brand.name_split');
    $c = config('site.contact');
    $navBase = $navBase ?? '';
@endphp
<footer class="bg-ink border-t border-white/5 pt-24 pb-8 overflow-hidden relative min-h-[100dvh] flex flex-col justify-between">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 w-full mt-auto mb-auto">
        
        {{-- Enlaces y Logo --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-24 mb-24">
            {{-- Izquierda: Logo --}}
            <div>
                <a href="{{ route('storefront') }}" class="flex items-center gap-3 text-white group w-max" aria-label="{{ config('site.brand.name') }} — inicio">
                    <div class="relative w-10 h-10 rounded-full overflow-hidden shadow-lg shadow-rose-600/20 group-hover:scale-110 transition-transform">
                        <img src="{{ asset('sexlandia/logo.jpg') }}" alt="Logo {{ config('site.brand.name') }}" class="w-full h-full object-cover">
                    </div>
                    <span class="text-2xl font-serif italic tracking-wider">
                        {{ $split[0] }}<span class="font-sans not-italic font-bold ml-1">{{ $split[1] }}</span>
                    </span>
                </a>
                <p class="mt-6 text-neutral-500 font-light text-sm max-w-xs leading-relaxed">
                    Elevando tu intimidad con diseño, discreción y el mejor asesoramiento de la ciudad.
                </p>
            </div>

            {{-- Derecha: Navegación --}}
            <nav aria-label="Enlaces del pie" class="grid grid-cols-2 gap-x-8 gap-y-6">
                <a href="{{ $navBase }}#productos" class="text-xs tracking-[0.2em] font-medium text-neutral-400 hover:text-rose-500 transition-colors uppercase">Tienda</a>
                <a href="{{ route('nosotros') }}" class="text-xs tracking-[0.2em] font-medium text-neutral-400 hover:text-rose-500 transition-colors uppercase">Nosotros</a>
                <a href="{{ $navBase }}#destacado" class="text-xs tracking-[0.2em] font-medium text-neutral-400 hover:text-rose-500 transition-colors uppercase">Destacados</a>
                <a href="{{ route('contacto') }}" class="text-xs tracking-[0.2em] font-medium text-neutral-400 hover:text-rose-500 transition-colors uppercase">Visítanos</a>
                <a href="{{ $navBase }}#album-coleccion" class="text-xs tracking-[0.2em] font-medium text-neutral-400 hover:text-rose-500 transition-colors uppercase">Colección</a>
                <a href="{{ $c['instagram'] }}" target="_blank" rel="noopener" class="text-xs tracking-[0.2em] font-medium text-neutral-400 hover:text-rose-500 transition-colors uppercase">Instagram</a>
                <a href="https://wa.me/{{ $c['whatsapp'] }}" target="_blank" rel="noopener" class="text-xs tracking-[0.2em] font-medium text-neutral-400 hover:text-rose-500 transition-colors uppercase">WhatsApp</a>
            </nav>
        </div>

        {{-- Legal --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between border-t border-white/10 pt-8 gap-6 md:gap-4">
            <span class="text-[9px] tracking-[0.2em] uppercase text-neutral-600 font-mono">
                +18 SOLAMENTE · CONTENIDO EXCLUSIVO PARA ADULTOS
            </span>
            <span class="text-[9px] tracking-[0.2em] uppercase text-neutral-600 font-mono">
                {{ config('site.location.city') }}, Venezuela
            </span>
            <span class="text-[9px] tracking-[0.2em] uppercase text-neutral-600 font-mono">
                © {{ date('Y') }} {{ \Illuminate\Support\Str::upper(config('site.brand.name')) }}. TODOS LOS DERECHOS RESERVADOS.
            </span>
        </div>
    </div>

    {{-- Marca de agua gigante --}}
    <div class="relative w-full overflow-hidden text-center select-none pointer-events-none mt-12 md:-mt-12" aria-hidden="true">
        <span class="block text-[15vw] leading-[0.7] font-black text-white/5 uppercase tracking-tighter whitespace-nowrap w-full">
            SIGUE CURIOSO.
        </span>
    </div>
</footer>
