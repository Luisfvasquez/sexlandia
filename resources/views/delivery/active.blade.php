@extends('delivery.layout')

@section('title', 'Entregas Activas en Ruta')

@section('content')
<div class="space-y-4">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <span>🚀</span> Entregas Activas
            </h2>
            <p class="text-xs text-gray-400">Paquetes asignados en camino a su destino.</p>
        </div>
        <span class="bg-amber-600/30 text-amber-300 border border-amber-500/40 text-xs font-bold px-3 py-1.5 rounded-xl">
            {{ $orders->count() }} En Ruta
        </span>
    </div>

    @if($orders->count() > 0)
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-gray-800 border border-amber-500/50 rounded-2xl p-5 shadow-lg relative">
                    
                    <div class="flex items-center justify-between border-b border-gray-700 pb-3 mb-3">
                        <div>
                            <span class="text-xs text-gray-400 block">Orden</span>
                            <span class="text-base font-bold text-amber-400">#{{ $order->order_number }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-400 block">Asignado</span>
                            <span class="text-xs font-semibold text-gray-300">
                                {{ $order->delivery_assigned_at ? $order->delivery_assigned_at->diffForHumans() : 'Hace un momento' }}
                            </span>
                        </div>
                    </div>

                    <!-- Datos Cliente y Teléfono -->
                    <div class="space-y-3 text-sm mb-4">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-2">
                                <span class="text-base">👤</span>
                                <div>
                                    <span class="text-xs text-gray-400 block">Cliente</span>
                                    <strong class="text-white text-base font-bold">
                                        {{ $order->client_name ?? ($order->client->name ?? 'Cliente General') }}
                                    </strong>
                                </div>
                            </div>
                            
                            @php
                                $phone = $order->client_phone ?? ($order->client->phone ?? null);
                            @endphp
                            @if($phone)
                                <a href="tel:{{ $phone }}" 
                                   class="bg-emerald-600/30 hover:bg-emerald-600/50 border border-emerald-500/40 text-emerald-300 text-xs font-bold py-2 px-3 rounded-xl flex items-center gap-1 shadow">
                                    📞 Llamar
                                </a>
                            @endif
                        </div>

                        <!-- Dirección -->
                        <div class="flex items-start gap-2 bg-gray-900/60 p-3 rounded-xl border border-gray-700">
                            <span class="text-base">📍</span>
                            <div class="flex-1">
                                <span class="text-xs text-gray-400 block">Dirección de Entrega</span>
                                <p class="text-white font-medium text-sm leading-snug">
                                    {{ $order->delivery_address ?? 'Sin dirección especificada' }}
                                </p>
                            </div>
                        </div>

                        @if($order->notes)
                            <div class="text-xs bg-amber-950/40 border border-amber-800/40 text-amber-200 p-2.5 rounded-lg">
                                <strong>Notas del Pedido:</strong> {{ $order->notes }}
                            </div>
                        @endif
                    </div>

                    <!-- Botones Rápidos de Acción Táctil -->
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-700">
                        @php
                            $mapQuery = ($order->latitude && $order->longitude) 
                                ? "{$order->latitude},{$order->longitude}" 
                                : urlencode($order->delivery_address);
                        @endphp

                        @if($order->delivery_address || ($order->latitude && $order->longitude))
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $mapQuery }}" 
                               target="_blank" 
                               class="bg-blue-600/30 hover:bg-blue-600/50 border border-blue-500/40 text-blue-200 text-xs font-bold py-3 px-3 rounded-xl text-center flex items-center justify-center gap-1 shadow">
                                🗺️ Abrir Mapa GPS
                            </a>
                        @endif

                        <a href="{{ route('delivery.show', $order->id) }}" 
                           class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold py-3 px-3 rounded-xl text-center flex items-center justify-center gap-1 shadow col-span-1">
                            ✅ Entregar Paquete
                        </a>
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <div class="bg-gray-800/50 border border-dashed border-gray-700 rounded-2xl p-10 text-center">
            <span class="text-4xl block mb-2">🚚</span>
            <p class="text-gray-300 font-bold">No tienes entregas activas en este momento.</p>
            <p class="text-xs text-gray-500 mt-1 mb-4">Ve a la sección de paquetes disponibles para tomar uno.</p>
            <a href="{{ route('delivery.available') }}" class="inline-block bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow transition">
                Ver Paquetes Disponibles
            </a>
        </div>
    @endif

</div>
@endsection
