@extends('delivery.layout')

@section('title', 'Historial de Entregas')

@section('content')
<div class="space-y-4">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <span>📋</span> Historial de Entregas
            </h2>
            <p class="text-xs text-gray-400">Registro de todas las entregas que has completado.</p>
        </div>
        <span class="bg-emerald-600/30 text-emerald-300 border border-emerald-500/40 text-xs font-bold px-3 py-1.5 rounded-xl">
            {{ $orders->total() }} Completadas
        </span>
    </div>

    @if($orders->count() > 0)
        <div class="space-y-3">
            @foreach($orders as $order)
                <div class="bg-gray-800 border border-gray-700/80 rounded-2xl p-4 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-white">Orden #{{ $order->order_number }}</span>
                            <span class="bg-emerald-900/60 text-emerald-300 border border-emerald-500/30 text-[10px] font-bold px-2 py-0.5 rounded-md">
                                Entregado
                            </span>
                        </div>
                        <p class="text-xs text-gray-300 font-medium">
                            👤 {{ $order->client_name ?? ($order->client->name ?? 'Cliente General') }}
                        </p>
                        <p class="text-[11px] text-gray-400">
                            Entregado: {{ $order->delivered_at->format('d/m/Y h:i A') }} ({{ $order->delivered_at->diffForHumans() }})
                        </p>
                    </div>

                    <div class="text-right">
                        <span class="text-sm font-black text-emerald-400 block">${{ number_format($order->total, 2) }}</span>
                        <a href="{{ route('delivery.show', $order->id) }}" class="text-xs text-indigo-400 hover:underline">
                            Ver Detalle
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @else
        <div class="bg-gray-800/50 border border-dashed border-gray-700 rounded-2xl p-10 text-center">
            <span class="text-4xl block mb-2">📦</span>
            <p class="text-gray-300 font-bold">Aún no has realizado ninguna entrega.</p>
            <p class="text-xs text-gray-500 mt-1">Tus entregas completadas aparecerán aquí como historial de trabajo.</p>
        </div>
    @endif

</div>
@endsection
