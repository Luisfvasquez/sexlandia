@php
    $navBase = $navBase ?? '';
    $user = auth()->user();
    $isClient = $user && $user->hasRole('client');
    $isAdmin = $user && $user->hasRole('admin');

    if ($isClient) {
        $nav = [
            ['CATÁLOGO', route('storefront.catalog')],
            ['MIS COMPRAS', route('client.purchases')],
            ['MIS FACTURAS', route('client.invoices')],
            ['NOSOTROS', route('nosotros')],
            ['VISÍTANOS', route('contacto')],
        ];
    } else {
        $nav = [
            ['TIENDA', $navBase . '#productos'],
            ['DESTACADO', $navBase . '#destacado'],
            ['COLECCIÓN', $navBase . '#album-coleccion'],
            ['CATEGORÍAS', $navBase . '#categorias'],
            ['NOSOTROS', route('nosotros')],
            ['VISÍTANOS', route('contacto')],
        ];
    }
    $split = config('site.brand.name_split');
@endphp

<header x-data="{ mobileMenuOpen: false }" class="fixed top-0 left-0 right-0 z-50 bg-ink/90 backdrop-blur-md border-b border-white/5 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 h-20 flex items-center justify-between">
        
        {{-- Logo --}}
        <a href="{{ route('storefront') }}" class="flex items-center gap-3 text-white group" aria-label="{{ config('site.brand.name') }} — inicio">
            <div class="relative w-8 h-8 rounded-full overflow-hidden shadow-lg shadow-rose-600/20 group-hover:scale-110 transition-transform">
                <img src="{{ asset('sexlandia/logo.jpg') }}" alt="Logo {{ config('site.brand.name') }}" class="w-full h-full object-cover">
            </div>
            <span class="text-3xl font-serif italic tracking-wider">
                {{ $split[0] }}<span class="font-sans not-italic font-bold ml-1">{{ $split[1] }}</span>
            </span>
        </a>

        {{-- Nav Desktop --}}
        <nav class="hidden lg:flex items-center gap-8" aria-label="Navegación principal">
            @foreach ($nav as [$label, $href])
                <a href="{{ $href }}" class="text-xs tracking-[0.2em] font-medium text-neutral-400 hover:text-rose-500 transition-colors uppercase">
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        {{-- Actions --}}
        <div class="flex items-center gap-6">
            @if ($isAdmin)
                <a href="{{ route('dashboard') }}" class="hidden md:block text-xs tracking-[0.2em] font-medium text-neutral-400 hover:text-white transition-colors uppercase">MI PANEL</a>
            @elseif ($isClient)
                <div class="relative hidden md:block" x-data="{ open: false }" @keydown.escape="open = false">
                    <button type="button" @click="open = !open" :aria-expanded="open" class="flex items-center gap-2 text-xs tracking-[0.2em] font-medium text-neutral-400 hover:text-white transition-colors uppercase" aria-label="Menú de cuenta">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        <span>{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::words($user->name, 1, '')) }}</span>
                    </button>
                    <div x-show="open" x-transition.opacity.duration.200ms x-cloak @click.outside="open = false" class="absolute right-0 mt-4 w-48 bg-ink border border-white/10 shadow-2xl py-2 flex flex-col">
                        <a href="{{ route('client.profile') }}" class="px-4 py-3 text-xs tracking-widest text-neutral-300 hover:bg-white/5 hover:text-white transition-colors uppercase">Mi perfil</a>
                        <a href="{{ route('client.purchases') }}" class="px-4 py-3 text-xs tracking-widest text-neutral-300 hover:bg-white/5 hover:text-white transition-colors uppercase">Mis compras</a>
                        <a href="{{ route('client.invoices') }}" class="px-4 py-3 text-xs tracking-widest text-neutral-300 hover:bg-white/5 hover:text-white transition-colors uppercase">Mis facturas</a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-white/10 mt-2">
                            @csrf
                            <button type="submit" onclick="try{localStorage.removeItem('client_shopping_cart')}catch(e){}" class="w-full text-left px-4 py-3 text-xs tracking-widest text-rose-500 hover:bg-white/5 hover:text-rose-400 transition-colors uppercase">Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="hidden md:block text-xs tracking-[0.2em] font-medium text-neutral-400 hover:text-white transition-colors uppercase">INGRESAR</a>
            @endif

            <button type="button" class="flex items-center gap-2 text-xs tracking-[0.2em] font-medium text-white hover:text-rose-500 transition-colors uppercase" @click="cartOpen = true" aria-label="Abrir carrito">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                <span class="hidden md:inline">CARRITO</span>
                <span x-show="totalItemsCount() > 0" x-text="totalItemsCount()" x-cloak class="flex items-center justify-center px-1.5 min-w-[1.25rem] h-5 bg-rose-600 text-white rounded-full text-[10px] font-bold"></span>
            </button>

            <button type="button" class="lg:hidden text-neutral-300 hover:text-white" @click="mobileMenuOpen = true" aria-label="Abrir menú">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileMenuOpen" x-cloak class="fixed inset-0 z-[100] lg:hidden flex">
        <div x-show="mobileMenuOpen" x-transition.opacity.duration.300ms class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="relative ml-auto w-full max-w-sm h-full bg-ink border-l border-white/10 shadow-2xl flex flex-col">
            
            <div class="flex items-center justify-between p-6 border-b border-white/5">
                <span class="text-xs tracking-[0.3em] font-bold text-rose-500 uppercase">Menú</span>
                <button type="button" @click="mobileMenuOpen = false" class="text-neutral-400 hover:text-white">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" d="M6 6l12 12M6 18L18 6" /></svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto p-6 flex flex-col space-y-6">
                @foreach ($nav as $i => [$label, $href])
                    <a href="{{ $href }}" @click="mobileMenuOpen = false" class="flex items-baseline gap-4 group">
                        <span class="text-xs font-mono text-rose-600/50">0{{ $i + 1 }}</span>
                        <span class="text-xl font-serif text-neutral-300 group-hover:text-white transition-colors uppercase tracking-widest">{{ $label }}</span>
                    </a>
                @endforeach
                
                <div class="border-t border-white/10 my-4 pt-8">
                    @if ($isAdmin)
                        <a href="{{ route('dashboard') }}" class="flex items-baseline gap-4 group">
                            <span class="text-xs font-mono text-rose-600/50">✦</span>
                            <span class="text-xl font-serif text-white group-hover:text-rose-500 transition-colors uppercase tracking-widest">MI PANEL</span>
                        </a>
                    @elseif ($isClient)
                        <a href="{{ route('client.profile') }}" class="flex items-baseline gap-4 group mb-4">
                            <span class="text-xs font-mono text-rose-600/50">✦</span>
                            <span class="text-xl font-serif text-white group-hover:text-rose-500 transition-colors uppercase tracking-widest">MI PERFIL</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" onclick="try{localStorage.removeItem('client_shopping_cart')}catch(e){}" class="flex items-baseline gap-4 group">
                                <span class="text-xs font-mono text-rose-600/50">✕</span>
                                <span class="text-xl font-serif text-neutral-500 group-hover:text-rose-500 transition-colors uppercase tracking-widest">CERRAR SESIÓN</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="flex items-baseline gap-4 group">
                            <span class="text-xs font-mono text-rose-600/50">✦</span>
                            <span class="text-xl font-serif text-white group-hover:text-rose-500 transition-colors uppercase tracking-widest">INGRESAR</span>
                        </a>
                    @endif
                </div>
            </nav>
            <div class="p-6 border-t border-white/5">
                <p class="text-[10px] font-mono tracking-widest text-neutral-600 uppercase">{{ config('site.brand.claim') }}</p>
            </div>
        </div>
    </div>
</header>
