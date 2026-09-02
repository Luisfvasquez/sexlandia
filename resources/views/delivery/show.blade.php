@extends('delivery.layout')

@section('title', 'Detalle de Entrega #' . $order->order_number)

@section('content')
<div class="space-y-5">

    <!-- Header con Botón de Regresar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('delivery.active') }}" class="text-xs font-bold text-gray-400 hover:text-white flex items-center gap-1 bg-gray-800 px-3 py-2 rounded-xl border border-gray-700">
            &larr; Volver
        </a>
        <span class="text-sm font-black text-amber-400">Orden #{{ $order->order_number }}</span>
    </div>

    <!-- Tarjeta Principal de Información -->
    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5 shadow-lg space-y-4">
        
        <div class="flex items-center justify-between border-b border-gray-700 pb-3">
            <div>
                <span class="text-xs text-gray-400 block">Estado Actual</span>
                @if($order->delivered_at)
                    <span class="inline-block bg-emerald-900/60 text-emerald-300 border border-emerald-500/40 text-xs font-bold px-2.5 py-1 rounded-lg">
                        Entregado ({{ $order->delivered_at->format('d/m/Y h:i A') }})
                    </span>
                @else
                    <span class="inline-block bg-amber-900/60 text-amber-300 border border-amber-500/40 text-xs font-bold px-2.5 py-1 rounded-lg animate-pulse">
                        En Ruta a Destino
                    </span>
                @endif
            </div>
            <div class="text-right">
                <span class="text-xs text-gray-400 block">Total a Pagar/Valor</span>
                <span class="text-base font-black text-white">${{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <!-- Cliente & Contacto -->
        <div class="space-y-3">
            <div class="bg-gray-900/80 p-3.5 rounded-xl border border-gray-700">
                <span class="text-xs text-gray-400 block font-semibold uppercase mb-1">Cliente</span>
                <p class="text-white font-bold text-base">{{ $order->client_name ?? ($order->client->name ?? 'Cliente General') }}</p>
                
                @php
                    $phone = $order->client_phone ?? ($order->client->phone ?? null);
                @endphp
                @if($phone)
                    <div class="mt-2 pt-2 border-t border-gray-800 flex items-center justify-between">
                        <span class="text-xs text-gray-300">📞 {{ $phone }}</span>
                        <a href="tel:{{ $phone }}" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold py-1.5 px-3 rounded-lg shadow">
                            Llamar al cliente
                        </a>
                    </div>
                @endif
            </div>

            <!-- Dirección de Entrega y Coordenadas -->
            <div class="bg-gray-900/80 p-3.5 rounded-xl border border-gray-700 space-y-3">
                <div>
                    <span class="text-xs text-gray-400 block font-semibold uppercase mb-1">Dirección de Entrega</span>
                    <p class="text-gray-100 font-medium text-sm leading-relaxed">
                        {{ $order->delivery_address ?? 'Pickup / Sin dirección ingresada' }}
                    </p>
                    @if($order->latitude && $order->longitude)
                        <p class="text-[11px] text-emerald-400 font-mono mt-1">
                            📍 GPS: {{ $order->latitude }}, {{ $order->longitude }}
                        </p>
                    @endif
                </div>

                @if($order->latitude && $order->longitude)
                    <div id="delivery-order-map" class="w-full h-48 rounded-xl border border-gray-700 z-10"></div>
                @endif
                
                @php
                    $mapQuery = ($order->latitude && $order->longitude) 
                        ? "{$order->latitude},{$order->longitude}" 
                        : urlencode($order->delivery_address);
                @endphp

                @if($order->delivery_address || ($order->latitude && $order->longitude))
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $mapQuery }}" 
                       target="_blank" 
                       class="w-full bg-blue-600/30 hover:bg-blue-600/50 border border-blue-500/40 text-blue-200 text-xs font-bold py-2.5 px-3 rounded-lg text-center flex items-center justify-center gap-1 shadow">
                        🗺️ Navegar con Google Maps / Waze
                    </a>
                @endif
            </div>
        </div>

        @if($order->latitude && $order->longitude)
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const lat = {{ $order->latitude }};
                    const lng = {{ $order->longitude }};
                    const map = L.map('delivery-order-map').setView([lat, lng], 16);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(map);
                    L.marker([lat, lng]).addTo(map)
                        .bindPopup('<b>Punto de Entrega</b><br>{{ $order->client_name }}')
                        .openPopup();
                });
            </script>
        @endif

        <!-- Productos incluidos -->
        <div>
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Productos del Paquete ({{ $order->details->count() }})</h4>
            <div class="bg-gray-900/50 rounded-xl border border-gray-700/80 divide-y divide-gray-800">
                @foreach($order->details as $detail)
                    <div class="p-3 flex items-center justify-between text-sm">
                        <div>
                            <p class="font-semibold text-white">{{ $detail->product->name ?? 'Producto' }}</p>
                            @if($detail->bulk)
                                <p class="text-xs text-gray-400">Presentación: {{ $detail->bulk->name }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-emerald-400">x{{ $detail->quantity }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if($order->notes)
            <div class="bg-gray-900/40 p-3 rounded-xl border border-gray-700 text-xs">
                <span class="font-bold text-amber-400 block mb-1">Notas del cliente/pedido:</span>
                <p class="text-gray-300">{{ $order->notes }}</p>
            </div>
        @endif

    </div>

    <!-- Formulario para Confirmar Entrega Realizada -->
    @if(!$order->delivered_at)
        <div class="bg-gradient-to-br from-emerald-950/60 to-gray-800 border border-emerald-500/50 rounded-2xl p-5 shadow-xl space-y-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span>✅</span> Confirmar Entrega
            </h3>

            <form action="{{ route('delivery.complete', $order->id) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="delivery_notes" class="block text-xs font-medium text-gray-300 mb-1">
                        Notas de Entrega (Opcional):
                    </label>
                    <textarea name="delivery_notes" id="delivery_notes" rows="2" 
                              placeholder="Ej: Entregado en portería, recibido por Juan..."
                              class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-emerald-500"></textarea>
                </div>

                <button type="submit" 
                        onclick="return confirm('¿Confirmas que has entregado la orden #{{ $order->order_number }} al cliente?')"
                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg transition flex items-center justify-center gap-2 text-base active:scale-98">
                    <span>🎉</span> Marcar como Entregado
                </button>
            </form>
        </div>
    @else
        <div class="bg-gray-800/80 border border-emerald-500/40 rounded-2xl p-4 text-center">
            <span class="text-2xl block mb-1">✨</span>
            <p class="text-emerald-300 font-bold text-sm">Esta entrega fue completada exitosamente.</p>
            @if($order->delivery_notes)
                <p class="text-xs text-gray-400 mt-2 bg-gray-900/60 p-2.5 rounded-lg border border-gray-700">
                    <strong>Comentarios de Entrega:</strong> {{ $order->delivery_notes }}
                </p>
            @endif
        </div>
    @endif

</div>
@endsection
