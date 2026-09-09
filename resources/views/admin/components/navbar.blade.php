<header class="bg-chocolate border-b border-white/10 px-4 md:px-6 py-4 flex items-center justify-between z-10 relative">

    <div class="flex items-center gap-4">
        {{-- Botón menú hamburguesa (Solo visible en móviles) --}}
        <button @click="sidebarOpen = true" class="text-cream/60 hover:text-bone focus:outline-none lg:hidden">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <h2 class="text-lg md:text-xl font-serif font-semibold tracking-wide text-bone">
            Panel Administrativo
        </h2>
    </div>

    <div class="flex items-center gap-4">

        {{-- Contenedor de Tasa BCV con Botón de Recarga --}}
        <div class="bg-butter/10 border-l-4 border-butter p-2 px-3 text-butter flex items-center gap-3 rounded-r-lg">
            <p class="text-sm m-0">
                <strong>Dólar BCV:</strong> <span class="font-bold">Bs. {{ $exchangeRate ?? 'N/D' }}</span>
            </p>

            {{-- Botón circular de actualización --}}
            <a href="{{ route('admin.products.forzarActualizacionDolar') }}"
                class="p-1.5 rounded-full hover:bg-butter/20 transition-colors focus:outline-none focus:ring-2 focus:ring-butter/50"
                title="Actualizar tasa ahora">
                <svg class="w-4 h-4 text-butter" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
            </a>
        </div>

        {{-- Dropdown de Usuario --}}
        <div class="relative" x-data="{ dropdownOpen: false }">

            {{-- Botón del perfil --}}
            <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false"
                class="flex items-center gap-2 text-cream/80 hover:text-bone focus:outline-none">
                {{-- Icono de usuario SVG --}}
                <div class="bg-white/10 p-1.5 rounded-full">
                    <svg class="w-5 h-5 text-cream/70" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                            clip-rule="evenodd" />
                    </svg>
                </div>

                <span class="hidden md:block font-medium">
                    {{ auth()->user()->name ?? 'Usuario' }}
                </span>

                {{-- Flecha hacia abajo --}}
                <svg class="w-4 h-4 text-cream/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            {{-- Menú Desplegable --}}
            <div x-show="dropdownOpen" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-3 w-48 bg-chocolate rounded-lg shadow-lg py-1 z-50 ring-1 ring-white/10"
                style="display: none;">

                {{-- Enlace a Perfil --}}
                <a href="{{ route('profile.edit') }}"
                    class="block px-4 py-2 text-sm text-cream/80 hover:bg-white/5 hover:text-bone transition-colors">
                    Perfil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="block w-full text-left px-4 py-2 text-sm text-rose-300 hover:bg-rose-500/10 transition-colors">
                        Salir
                    </button>
                </form>
            </div>

        </div>

    </div>

</header>
