@php
    $statusTranslations = [
        'pending' => 'Pendiente',
        'processing' => 'Procesando',
        'ready_for_pickup' => 'Listo para retirar',
        'completed' => 'Completada',
        'delivered' => 'Entregada',
        'cancelled' => 'Cancelada',
    ];

    $paymentTranslations = [
        'pending' => 'Pendiente',
        'partial' => 'Parcial',
        'paid' => 'Pagado',
        'rejected' => 'Rechazado',
    ];
@endphp

<div>
    {{-- Panel de Métricas / Resumen Financiero Histórico --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-chocolate p-6 rounded-xl shadow border-l-4 border-emerald-500">
            <h2 class="text-cream/60 text-sm font-medium uppercase">Órdenes Completadas</h2>
            <p class="text-2xl font-bold text-emerald-400">{{ $completedCount }}</p>
        </div>
        <div class="bg-chocolate p-6 rounded-xl shadow border-l-4 border-amber-500">
            <h2 class="text-cream/60 text-sm font-medium uppercase">Órdenes en Proceso</h2>
            <p class="text-2xl font-bold text-amber-300">{{ $processingCount }}</p>
        </div>
        <div class="bg-chocolate p-6 rounded-xl shadow border-l-4 border-rose-500">
            <h2 class="text-cream/60 text-sm font-medium uppercase">Órdenes Rechazadas / Canceladas</h2>
            <p class="text-2xl font-bold text-rose-300">{{ $rejectedCount }}</p>
        </div>
    </div>

    {{-- Buscador en Tiempo Real --}}
    <div class="mb-4 flex items-center">
        <input type="text" wire:model.live.debounce.300ms="search"
            placeholder="Buscar por número de orden, RIF o cliente..."
            class="w-full md:w-1/3 px-4 py-2 border border-white/15 rounded-lg shadow-sm focus:ring-wine focus:border-wine">

        <div wire:loading wire:target="search" class="text-sm text-cream/60 ml-2">
            Buscando...
        </div>
    </div>

    {{-- Tabla de Órdenes --}}
    <div class="bg-chocolate rounded-xl shadow-sm overflow-hidden relative">
        {{-- Loader para la reactividad --}}
        <div wire:loading.delay class="absolute inset-0 bg-chocolate bg-opacity-70 z-10 flex items-center justify-center">
            <span class="text-cream/70 font-semibold">Cargando datos...</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
                <thead class="bg-white/5">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-cream/60 uppercase">Orden</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-cream/60 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-cream/60 uppercase">Estado</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-cream/60 uppercase">Pago</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-cream/60 uppercase">Total (USD)</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-cream/60 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($orders as $order)
                        <tr wire:key="order-{{ $order->id }}" x-data="{ openModal: false }" class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-blush">
                                {{ $order->order_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-bone">{{ $order->client->name ?? 'Invitado' }}</div>
                                <div class="text-xs text-cream/60">{{ $order->client->identification ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    {{ $order->status == 'completed' || $order->status == 'delivered'
                                        ? 'bg-emerald-500/15 text-emerald-300'
                                        : ($order->status == 'pending'
                                            ? 'bg-amber-500/15 text-amber-300'
                                            : ($order->status == 'ready_for_pickup'
                                                ? 'bg-wine/15 text-blush'
                                                : 'bg-white/5 text-cream/70')) }}">
                                    {{ $statusTranslations[$order->status] ?? ucfirst($order->status) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    {{ $order->payment_status == 'paid'
                                        ? 'bg-wine/15 text-blush'
                                        : ($order->payment_status == 'partial'
                                            ? 'bg-amber-500/15 text-amber-200'
                                            : 'bg-rose-500/15 text-rose-300') }}">
                                    {{ $paymentTranslations[$order->payment_status] ?? ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-black text-bone">
                                $ {{ number_format($order->total, 2) }}
                                @if ($order->exchange_rate)
                                    <span class="block text-xs font-bold text-cream/50">Bs. {{ number_format($order->total * $order->exchange_rate, 2) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                    class="inline-flex items-center gap-1 text-blush hover:text-blush font-bold bg-wine/10 hover:bg-wine/15 px-3 py-1.5 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    Revisar
                                </a>
                                <button @click="openModal = true"
                                    class="inline-flex items-center gap-1 text-cream/70 hover:text-bone font-bold bg-white/5 hover:bg-white/10 px-3 py-1.5 rounded-lg transition-colors">
                                    Detalle
                                </button>
                            </td>

                            {{-- Modal de Detalle (Teleport para evitar problemas de z-index) --}}
                            <template x-teleport="body">
                                <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                    <div class="flex items-center justify-center min-h-screen px-4">
                                        <div @click="openModal = false" class="fixed inset-0 bg-ink opacity-50"></div>
                                        <div class="bg-chocolate rounded-2xl p-8 max-w-2xl w-full shadow-2xl relative z-10">
                                            <h2 class="text-2xl font-black mb-4">Detalle Orden {{ $order->order_number }}</h2>

                                            <div class="border-t border-b py-4 my-4 space-y-2">
                                                @foreach ($order->details as $detail)
                                                    <div class="flex justify-between">
                                                        <span>{{ $detail->quantity }}x {{ $detail->product->name ?? 'N/A' }}
                                                            ({{ $detail->bulk?->name ?? 'Unidad' }})
                                                        </span>
                                                        <span class="font-bold">$ {{ number_format($detail->subtotal, 2) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="flex justify-between text-xl font-black">
                                                <span>Total:</span>
                                                <span>$ {{ number_format($order->total, 2) }}
                                                    @if ($order->exchange_rate)
                                                        <span class="block text-xs text-cream/50 text-right">Bs. {{ number_format($order->total * $order->exchange_rate, 2) }}</span>
                                                    @endif
                                                </span>
                                            </div>

                                            <button @click="openModal = false"
                                                class="mt-6 w-full bg-chocolate text-white py-3 rounded-xl font-bold">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-cream/60 italic">No hay órdenes registradas con este criterio.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-white/10">
            {{ $orders->links() }}
        </div>
    </div>
</div>
