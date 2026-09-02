<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Módulo Delivery') - {{ config('app.name', 'Inventario') }}</title>

    <!-- Google Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-900 text-gray-100 font-sans antialiased min-h-screen pb-20 md:pb-6">

    <!-- Top Navigation Bar -->
    <header class="bg-gray-800 border-b border-gray-700 sticky top-0 z-30 shadow-md">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-white font-bold text-lg shadow">
                    🚚
                </div>
                <div>
                    <h1 class="text-base font-bold text-white leading-tight">Panel Delivery</h1>
                    <p class="text-xs text-emerald-400 flex items-center gap-1 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Repartidor Activo
                    </p>
                </div>
            </div>

            <!-- Profile & Logout Dropdown -->
            <div class="flex items-center space-x-2" x-data="{ open: false }">
                <span class="text-xs font-medium text-gray-300 hidden sm:inline">{{ auth()->user()->name }}</span>
                <button @click="open = !open" class="focus:outline-none flex items-center gap-1 bg-gray-700 hover:bg-gray-600 p-1.5 rounded-lg transition">
                    <div class="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-xs font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false" style="display: none;"
                     class="absolute right-4 top-14 w-48 bg-gray-800 border border-gray-700 rounded-xl shadow-xl py-1 z-40 text-sm">
                    <div class="px-4 py-2 border-b border-gray-700">
                        <p class="font-semibold text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-indigo-400 hover:bg-gray-700 flex items-center gap-2">
                            <span>⚙️</span> Panel Administrador
                        </a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-300 hover:bg-gray-700 flex items-center gap-2">
                        <span>👤</span> Mi Perfil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-red-400 hover:bg-gray-700 flex items-center gap-2">
                            <span>🚪</span> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Global Notifications / Alerts -->
    <div class="max-w-4xl mx-auto px-4 mt-3">
        @if (session('success'))
            <div class="bg-emerald-900/80 border border-emerald-500/50 text-emerald-200 px-4 py-3 rounded-xl shadow mb-3 flex items-center justify-between" role="alert">
                <div class="flex items-center gap-2">
                    <span class="text-lg">✅</span>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-900/80 border border-red-500/50 text-red-200 px-4 py-3 rounded-xl shadow mb-3 flex items-center justify-between" role="alert">
                <div class="flex items-center gap-2">
                    <span class="text-lg">⚠️</span>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-white">&times;</button>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="max-w-4xl mx-auto px-4 py-4">
        @yield('content')
    </main>

    <!-- Bottom Navigation Bar for Mobile -->
    <nav class="fixed bottom-0 left-0 right-0 bg-gray-800/95 backdrop-blur-md border-t border-gray-700 z-30 px-2 py-2">
        <div class="max-w-md mx-auto grid grid-cols-4 gap-1 text-center">
            
            <a href="{{ route('delivery.dashboard') }}" 
               class="flex flex-col items-center py-1 px-2 rounded-lg transition {{ request()->routeIs('delivery.dashboard') ? 'text-emerald-400 font-bold bg-gray-700/60' : 'text-gray-400 hover:text-gray-200' }}">
                <span class="text-xl">📊</span>
                <span class="text-[11px] mt-0.5">Inicio</span>
            </a>

            <a href="{{ route('delivery.available') }}" 
               class="flex flex-col items-center py-1 px-2 rounded-lg transition relative {{ request()->routeIs('delivery.available') ? 'text-emerald-400 font-bold bg-gray-700/60' : 'text-gray-400 hover:text-gray-200' }}">
                <span class="text-xl">📦</span>
                <span class="text-[11px] mt-0.5">Disponibles</span>
            </a>

            <a href="{{ route('delivery.active') }}" 
               class="flex flex-col items-center py-1 px-2 rounded-lg transition relative {{ request()->routeIs('delivery.active') || request()->routeIs('delivery.show') ? 'text-emerald-400 font-bold bg-gray-700/60' : 'text-gray-400 hover:text-gray-200' }}">
                <span class="text-xl">🚀</span>
                <span class="text-[11px] mt-0.5">En Ruta</span>
            </a>

            <a href="{{ route('delivery.history') }}" 
               class="flex flex-col items-center py-1 px-2 rounded-lg transition {{ request()->routeIs('delivery.history') ? 'text-emerald-400 font-bold bg-gray-700/60' : 'text-gray-400 hover:text-gray-200' }}">
                <span class="text-xl">📋</span>
                <span class="text-[11px] mt-0.5">Historial</span>
            </a>

        </div>
    </nav>

    @livewireScripts
</body>
</html>
