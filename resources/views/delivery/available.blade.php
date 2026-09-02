@extends('delivery.layout')

@section('title', 'Paquetes Disponibles')

@section('content')
<div class="space-y-4">

    <!-- Encabezado y Pestañas de Selección -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <span>📦</span> Paquetes Disponibles
                </h2>
                <p class="text-xs text-gray-400">Selecciona y toma un paquete para iniciar la entrega.</p>
            </div>
            <span class="bg-indigo-600/30 text-indigo-300 border border-indigo-500/40 text-xs font-bold px-3 py-1.5 rounded-xl">
                {{ $orders->total() }} Disponibles
            </span>
        </div>

        <!-- Pestañas Táctiles: Envíos vs Retiros en Tienda -->
        <div class="grid grid-cols-2 gap-2 bg-gray-800 p-1.5 rounded-2xl border border-gray-700">
            <a href="{{ route('delivery.available', ['type' => 'delivery']) }}" 
               class="py-2.5 px-3 rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5 {{ $type === 'delivery' ? 'bg-emerald-600 text-white shadow-lg' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                <span>🚚</span> Envíos a Domicilio
                <span class="bg-gray-900/60 text-white text-[10px] px-2 py-0.5 rounded-full">{{ $deliveryCount }}</span>
            </a>

            <a href="{{ route('delivery.available', ['type' => 'store_pickup']) }}" 
               class="py-2.5 px-3 rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5 {{ $type === 'store_pickup' ? 'bg-amber-600 text-white shadow-lg' : 'text-gray-400 hover:text-white hover:bg-gray-700/50' }}">
                <span>🏬</span> Retiros en Tienda
                <span class="bg-gray-900/60 text-white text-[10px] px-2 py-0.5 rounded-full">{{ $storePickupCount }}</span>
            </a>
        </div>
    </div>

    @if($orders->count() > 0)
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-gray-800 border border-gray-700 hover:border-emerald-500/40 rounded-2xl p-5 shadow-md transition">
                    
                    <!-- Header Tarjeta -->
                    <div class="flex items-center justify-between border-b border-gray-700 pb-3 mb-3">
                        <div>
                            <span class="text-xs text-gray-400 block">Orden</span>
                            <span class="text-base font-bold text-emerald-400">#{{ $order->order_number }}</span>
                        </div>
                        <div class="text-right">
                            @if(in_array($order->order_type, ['store_pickup', 'store']))
                                <span class="bg-amber-900/60 text-amber-300 border border-amber-500/40 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider block mb-1">
                                    Retiro en Tienda
                                </span>
                            @else
                                <span class="bg-emerald-900/60 text-emerald-300 border border-emerald-500/40 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider block mb-1">
                                    Envío Delivery
                                </span>
                            @endif
                            <span class="text-[11px] text-gray-400">{{ $order->created_at->format('d/m/Y h:i A') }}</span>
                        </div>
                    </div>

                    <!-- Datos Cliente y Dirección -->
                    <div class="space-y-2.5 text-sm mb-4">
                        <div class="flex items-start gap-2">
                            <span class="text-gray-400">👤</span>
                            <div>
                                <span class="text-xs text-gray-400 block">Cliente</span>
                                <strong class="text-white font-semibold">
                                    {{ $order->client_name ?? ($order->client->name ?? 'Cliente General') }}
                                </strong>
                                @if($order->client_phone || ($order->client->phone ?? null))
                                    <p class="text-xs text-gray-400">📞 {{ $order->client_phone ?? $order->client->phone }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-start gap-2">
                            <span class="text-gray-400">📍</span>
                            <div>
                                <span class="text-xs text-gray-400 block">Dirección de Entrega</span>
                                <p class="text-gray-200 font-medium leading-snug">
                                    {{ $order->delivery_address ?? ($order->order_type === 'store_pickup' ? 'Retiro en Tienda (Solicitar dirección al tomar)' : 'Sin dirección especificada') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-gray-700/60 text-xs text-gray-400">
                            <span>📦 {{ $order->details->count() }} producto(s)</span>
                            <span class="font-bold text-white text-sm">Total: ${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>

                    <!-- Botón Táctil de Tomar Paquete -->
                    <form action="{{ route('delivery.claim', $order->id) }}" method="POST">
                        @csrf
                        @if(in_array($order->order_type, ['store_pickup', 'store']))
                            <button type="submit" 
                                    onclick="return confirm('¿Confirmas que tomarás este paquete de Retiro en Tienda y lo entregarás por Delivery?')"
                                    class="w-full bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white font-bold py-3 px-4 rounded-xl shadow-lg flex items-center justify-center gap-2 active:scale-98 transition text-sm">
                                <span>🛵</span> Tomar y Convertir a Delivery
                            </button>
                        @else
                            <button type="submit" 
                                    onclick="return confirm('¿Confirmas que deseas tomar el paquete #{{ $order->order_number }} para entrega?')"
                                    class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold py-3 px-4 rounded-xl shadow-lg flex items-center justify-center gap-2 active:scale-98 transition text-sm">
                                <span>✋</span> Tomar Paquete para Entrega
                            </button>
                        @endif
                    </form>

                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @else
        <div class="bg-gray-800/50 border border-dashed border-gray-700 rounded-2xl p-10 text-center">
            <span class="text-4xl block mb-2">📭</span>
            <p class="text-gray-300 font-bold">
                {{ $type === 'store_pickup' ? 'No hay pedidos de Retiro en Tienda disponibles.' : 'No hay paquetes de Delivery listos para entregar.' }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Los nuevos pedidos listos para envío aparecerán automáticamente aquí.</p>
        </div>
    @endif

</div>
@endsection
