<div>
    {{-- Panel de Métricas / Resumen Financiero Histórico --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-chocolate p-6 rounded-xl shadow border-l-4 border-wine">
            <h2 class="text-cream/60 text-sm font-medium uppercase">Total Facturas Procesadas</h2>
            <p class="text-2xl font-bold text-cream">{{ $totalPurchases }}</p>
        </div>
        <div class="bg-chocolate p-6 rounded-xl shadow border-l-4 border-emerald-500">
            <h2 class="text-cream/60 text-sm font-medium uppercase">Inversión Total (USD)</h2>
            <p class="text-2xl font-bold text-emerald-400">$ {{ number_format($totalInvestmentUsd, 2) }}</p>
        </div>
        <div class="bg-chocolate p-6 rounded-xl shadow border-l-4 border-wine">
            <h2 class="text-cream/60 text-sm font-medium uppercase">Equivalente en Bolívares</h2>
            <p class="text-2xl font-bold text-blush">
                Bs. {{ number_format($totalInvestmentBs, 2) }}
            </p>
        </div>
    </div>

    {{-- Buscador en Tiempo Real --}}
    <div class="mb-4 flex items-center">
        <input type="text" wire:model.live.debounce.300ms="search"
            placeholder="Buscar por código de factura, RIF o proveedor..."
            class="w-full md:w-1/3 px-4 py-2 border border-white/15 rounded-lg shadow-sm focus:ring-wine focus:border-wine">

        <div wire:loading wire:target="search" class="text-sm text-cream/60 ml-2">
            Buscando...
        </div>
    </div>

    {{-- Tabla de Registro Histórico --}}
    <div class="bg-chocolate rounded-xl shadow overflow-hidden relative">
        {{-- Loader para la reactividad --}}
        <div wire:loading.delay class="absolute inset-0 bg-chocolate bg-opacity-70 z-10 flex items-center justify-center">
            <span class="text-cream/70 font-semibold">Cargando datos...</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
                <thead class="bg-white/5">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-cream/60 uppercase tracking-wider">
                            Código / Factura</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-cream/60 uppercase tracking-wider">
                            Proveedor</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-cream/60 uppercase tracking-wider">Fecha
                            de Compra</th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-medium text-cream/60 uppercase tracking-wider">
                            Tasa Aplicada</th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-cream/60 uppercase tracking-wider">
                            Monto Total (Bs.)</th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-cream/60 uppercase tracking-wider">
                            Monto Total (USD)</th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-medium text-cream/60 uppercase tracking-wider">
                            Estado</th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-cream/60 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-chocolate divide-y divide-white/10">
                    @forelse($purchases as $purchase)
                        {{-- Fila con estado local Alpine para controlar su propio modal de detalles --}}
                        <tr wire:key="purchase-{{ $purchase->id }}" x-data="{ openDetail: false }" class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-bone">
                                #{{ $purchase->purchase_code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-bone">{{ $purchase->supplier->name ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-cream/60">RIF: {{ $purchase->supplier->rif ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-cream/70">
                                {{ $purchase->purchased_at ? $purchase->purchased_at->format('d/m/Y') : $purchase->created_at->format('d/m/Y') }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-cream/90 bg-white/5">
                                Bs. {{ number_format($purchase->exchange_rate, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-bone">
                                Bs. {{ number_format($purchase->total * $purchase->exchange_rate, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-emerald-400">
                                ${{ number_format($purchase->total, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if ($purchase->status === 'completed')
                                    <span
                                        class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-emerald-500/15 text-emerald-300">Procesado</span>
                                @else
                                    <span
                                        class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-amber-500/15 text-amber-300">{{ $purchase->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="openDetail = true"
                                    class="text-blush hover:text-blush bg-wine/10 px-3 py-1 rounded-md transition-colors">
                                    Ver Detalles
                                </button>

                                {{-- MODAL DE INSPECCIÓN DETALLADA DE LA COMPRA --}}
                                <template x-teleport="body">
                                    <div x-show="openDetail" style="display: none;"
                                        class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                                        <div
                                            class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                                            {{-- Capa de desenfoque trasera --}}
                                            <div x-show="openDetail" x-transition.opacity
                                                class="fixed inset-0 bg-ink bg-opacity-75 transition-opacity"
                                                @click="openDetail = false"></div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                                                aria-hidden="true">&#8203;</span>

                                            {{-- Cuerpo del Modal --}}
                                            <div x-show="openDetail" x-transition:enter="ease-out duration-300"
                                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                x-transition:leave="ease-in duration-200"
                                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                class="inline-block align-bottom bg-chocolate rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">

                                                <div
                                                    class="bg-chocolate p-6 border-b border-white/10 flex justify-between items-center">
                                                    <div>
                                                        <h3 class="text-xl font-bold text-bone">Resumen Documental
                                                            de Compra</h3>
                                                        <p class="text-xs text-cream/60 mt-1">Registrado por:
                                                            {{ $purchase->user->name ?? 'Sistema' }}</p>
                                                    </div>
                                                    <span
                                                        class="bg-white/5 text-cream text-xs px-3 py-1 rounded-md font-mono font-bold">Factura:
                                                        #{{ $purchase->purchase_code }}</span>
                                                </div>

                                                <div class="p-6 space-y-6">
                                                    {{-- Grid Informativo Superior --}}
                                                    <div
                                                        class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-white/5 p-4 rounded-xl border">
                                                        <div>
                                                            <span
                                                                class="block text-xs font-bold text-cream/50 uppercase">Proveedor</span>
                                                            <span
                                                                class="text-sm font-semibold text-cream">{{ $purchase->supplier->name ?? 'N/A' }}</span>
                                                        </div>
                                                        <div>
                                                            <span
                                                                class="block text-xs font-bold text-cream/50 uppercase">Fecha
                                                                Fiscal</span>
                                                            <span
                                                                class="text-sm font-semibold text-cream">{{ $purchase->purchased_at ? $purchase->purchased_at->format('d/m/Y') : $purchase->created_at->format('d/m/Y') }}</span>
                                                        </div>
                                                        <div>
                                                            <span
                                                                class="block text-xs font-bold text-cream/50 uppercase">Tasa
                                                                de Cambio Congelada</span>
                                                            <span class="text-sm font-bold text-blush">Bs.
                                                                {{ number_format($purchase->exchange_rate, 4) }}</span>
                                                        </div>
                                                    </div>

                                                    {{-- Tabla de Ítems Comprados --}}
                                                    <div>
                                                        <h4 class="text-sm font-bold text-cream/90 mb-2">Artículos
                                                            Ingresados al Inventario</h4>
                                                        <div class="border rounded-lg overflow-hidden bg-chocolate">
                                                            <table class="min-w-full divide-y divide-white/10 text-sm">
                                                                <thead class="bg-white/5 font-medium text-cream/60">
                                                                    <tr>
                                                                        <th class="px-4 py-2 text-left">Producto</th>
                                                                        <th class="px-4 py-2 text-left">Presentación
                                                                        </th>
                                                                        <th class="px-4 py-2 text-center">Cant. Comprada
                                                                        </th>
                                                                        <th class="px-4 py-2 class text-center">Equiv.
                                                                            Unidades Base</th>
                                                                        <th class="px-4 py-2 text-right">Costo Unit.
                                                                            (Bs)
                                                                        </th>
                                                                        <th class="px-4 py-2 text-right">Costo Unit.
                                                                            (USD)</th>
                                                                        <th class="px-4 py-2 text-right">Subtotal (Bs)
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="divide-y divide-white/10 text-cream/90">
                                                                    @foreach ($purchase->details as $detail)
                                                                        <tr>
                                                                            <td
                                                                                class="px-4 py-2 font-medium text-bone">
                                                                                {{ $detail->product->name ?? 'N/A' }}
                                                                            </td>
                                                                            <td class="px-4 py-2">
                                                                                <span
                                                                                    class="text-xs bg-white/5 px-2 py-0.5 rounded font-semibold text-cream/70">
                                                                                    {{ $detail->bulk->name ?? 'Unidad' }}
                                                                                </span>
                                                                            </td>
                                                                            <td
                                                                                class="px-4 py-2 text-center font-bold">
                                                                                {{ number_format($detail->quantity, 2) }}
                                                                            </td>
                                                                            <td
                                                                                class="px-4 py-2 text-center text-cream/60">
                                                                                {{ number_format($detail->base_quantity, 2) }}
                                                                            </td>
                                                                            <td class="px-4 py-2 text-right">Bs.
                                                                                {{ number_format($detail->unit_cost_bs, 2) }}
                                                                            </td>
                                                                            <td
                                                                                class="px-4 py-2 text-right text-emerald-400 font-semibold">
                                                                                ${{ number_format($detail->unit_cost, 2) }}
                                                                            </td>
                                                                            <td class="px-4 py-2 text-right font-bold">
                                                                                Bs.
                                                                                {{ number_format($detail->subtotal * $purchase->exchange_rate, 2) }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                    {{-- Notas adicionales si existen --}}
                                                    @if ($purchase->notes)
                                                        <div
                                                            class="bg-amber-500/10 p-3 rounded-lg border border-amber-500/20 text-xs text-amber-300">
                                                            <strong>Notas del Registro:</strong> {{ $purchase->notes }}
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Pie del Modal con Cierre Financiero --}}
                                                <div
                                                    class="bg-ink px-6 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                                                    <div class="text-white">
                                                        <span
                                                            class="text-xs text-cream/50 block uppercase font-bold">Total
                                                            General Compra</span>
                                                        <span class="text-2xl font-black">$
                                                            {{ number_format($purchase->total, 2) }}</span>
                                                        <span
                                                            class="text-emerald-400 font-bold ml-2">(Bs. {{ number_format($purchase->total * $purchase->exchange_rate, 2) }})</span>
                                                    </div>
                                                    <button type="button" @click="openDetail = false"
                                                        class="w-full sm:w-auto bg-chocolate text-bone font-bold px-4 py-2 rounded-lg hover:bg-white/5 transition-colors text-center shadow">
                                                        Cerrar Ventana
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-cream/60">
                                No se han encontrado compras con ese criterio. <a
                                    href="{{ route('admin.purchases.create') }}"
                                    class="text-blush hover:underline font-medium">Registra una nueva compra.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-white/10">
            {{ $purchases->links() }}
        </div>
    </div>
</div>
