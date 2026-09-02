@extends('delivery.layout')

@section('title', 'Inicio Repartidor')

@section('content')
<div class="space-y-6">

    <!-- Saludo y Resumen rápido -->
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-5 shadow-lg">
        <h2 class="text-xl font-bold text-white mb-1">¡Hola, {{ auth()->user()->name }}! 👋</h2>
        <p class="text-sm text-gray-400">Revisa tus entregas activas o toma nuevos paquetes disponibles para reparto.</p>
    </div>

    <!-- Indicadores Táctiles -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <!-- Disponibles -->
        <a href="{{ route('delivery.available') }}" class="block bg-gradient-to-br from-indigo-900/60 to-gray-800 border border-indigo-500/30 hover:border-indigo-400 rounded-2xl p-5 shadow transition transform active:scale-98">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs uppercase font-bold tracking-wider text-indigo-300">Disponibles</span>
                    <div class="text-3xl font-black text-white mt-1">{{ $availableCount }}</div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-600/30 text-indigo-300 flex items-center justify-center text-2xl border border-indigo-500/30">
                    📦
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold text-indigo-400">
                Ver paquetes en espera &rarr;
            </div>
        </a>

        <!-- En Ruta (Activas) -->
        <a href="{{ route('delivery.active') }}" class="block bg-gradient-to-br from-amber-900/60 to-gray-800 border border-amber-500/30 hover:border-amber-400 rounded-2xl p-5 shadow transition transform active:scale-98">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs uppercase font-bold tracking-wider text-amber-300">En Ruta</span>
                    <div class="text-3xl font-black text-white mt-1">{{ $activeDeliveries->count() }}</div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-600/30 text-amber-300 flex items-center justify-center text-2xl border border-amber-500/30">
                    🚀
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold text-amber-400">
                Ir a tus entregas activas &rarr;
            </div>
        </a>

        <!-- Completadas Hoy -->
        <a href="{{ route('delivery.history') }}" class="block bg-gradient-to-br from-emerald-900/60 to-gray-800 border border-emerald-500/30 hover:border-emerald-400 rounded-2xl p-5 shadow transition transform active:scale-98">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs uppercase font-bold tracking-wider text-emerald-300">Entregadas Hoy</span>
                    <div class="text-3xl font-black text-white mt-1">{{ $completedTodayCount }}</div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-600/30 text-emerald-300 flex items-center justify-center text-2xl border border-emerald-500/30">
                    ✅
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold text-emerald-400">
                Ver historial completo &rarr;
            </div>
        </a>

    </div>

    <!-- Sección de Entregas Activas Actuales -->
    <div class="mt-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span>⚡</span> Entrega(s) en Curso
            </h3>
            <a href="{{ route('delivery.active') }}" class="text-xs text-emerald-400 hover:underline">Ver todas</a>
        </div>

        @if($activeDeliveries->count() > 0)
            <div class="space-y-4">
                @foreach($activeDeliveries as $order)
                    <div class="bg-gray-800 border border-amber-500/40 rounded-2xl p-5 shadow-lg relative overflow-hidden">
                        <div class="absolute top-0 right-0 bg-amber-500 text-gray-950 font-black text-[10px] uppercase px-3 py-1 rounded-bl-xl tracking-wider">
                            En Transito
                        </div>

                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-bold text-amber-400">Orden #{{ $order->order_number }}</span>
                            <span class="text-xs text-gray-400">Tomado {{ $order->delivery_assigned_at ? $order->delivery_assigned_at->diffForHumans() : '' }}</span>
                        </div>

                        <div class="space-y-2 text-sm text-gray-300 mb-4">
                            <div class="flex items-start gap-2">
                                <span class="text-base">👤</span>
                                <div>
                                    <strong class="text-white">{{ $order->client_name ?? ($order->client->name ?? 'Cliente General') }}</strong>
                                    @if($order->client_phone || ($order->client->phone ?? null))
                                        <p class="text-xs text-gray-400">📞 {{ $order->client_phone ?? $order->client->phone }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-start gap-2">
                                <span class="text-base">📍</span>
                                <p class="text-gray-200 font-medium leading-snug">
                                    {{ $order->delivery_address ?? 'Sin dirección especificada' }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-700">
                            @if($order->delivery_address)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($order->delivery_address) }}" 
                                   target="_blank" 
                                   class="bg-blue-600/30 hover:bg-blue-600/50 border border-blue-500/40 text-blue-200 text-xs font-bold py-2.5 px-3 rounded-xl text-center flex items-center justify-center gap-1">
                                    🗺️ Abrir Mapa
                                </a>
                            @endif

                            <a href="{{ route('delivery.show', $order->id) }}" 
                               class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold py-2.5 px-3 rounded-xl text-center flex items-center justify-center gap-1 shadow col-span-1">
                                🚀 Detalle & Entregar
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-800/50 border border-dashed border-gray-700 rounded-2xl p-8 text-center">
                <span class="text-4xl block mb-2">🛵</span>
                <p class="text-gray-400 font-medium text-sm">No tienes entregas activas en este momento.</p>
                <a href="{{ route('delivery.available') }}" class="inline-block mt-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow transition">
                    Ver paquetes disponibles
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
