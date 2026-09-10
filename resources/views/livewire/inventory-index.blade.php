<div>
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-cream">
            Control de Almacén
        </h1>
        <div class="flex space-x-2">
            <button
                class="inline-flex items-center px-4 py-2 bg-white/10 text-cream/90 font-semibold rounded-lg hover:bg-white/15 transition-colors">
                Historial de Movimientos
            </button>
        </div>
    </div>

    {{-- Resumen de Alertas (Corregido con variables globales) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-chocolate p-6 rounded-xl shadow border-l-4 border-rose-500">
            <h2 class="text-cream/60 text-sm font-medium uppercase">Productos Agotados</h2>
            <p class="text-2xl font-bold text-rose-300">{{ $totalOutOfStock }}</p>
        </div>
        <div class="bg-chocolate p-6 rounded-xl shadow border-l-4 border-amber-500">
            <h2 class="text-cream/60 text-sm font-medium uppercase">Bajo Stock Mínimo</h2>
            <p class="text-2xl font-bold text-amber-300">{{ $totalLowStock }}</p>
        </div>
        <div class="bg-chocolate p-6 rounded-xl shadow border-l-4 border-emerald-500">
            <h2 class="text-cream/60 text-sm font-medium uppercase">Total Unidades en Almacén</h2>
            <p class="text-2xl font-bold text-emerald-400">{{ number_format($totalStock, 2) }}</p>
        </div>
    </div>

    {{-- Buscador en Tiempo Real --}}
    <div class="mb-4">
        <input type="text" wire:model.live.debounce.300ms="search"
            placeholder="Buscar por nombre de producto o SKU..."
            class="w-full md:w-1/3 px-4 py-2 border border-white/15 rounded-lg shadow-sm focus:ring-wine focus:border-wine">

        <div wire:loading wire:target="search" class="text-sm text-cream/60 ml-2">
            Buscando...
        </div>
    </div>

    {{-- Tabla de Inventario --}}
    <div class="bg-chocolate rounded-xl shadow overflow-hidden relative">
        {{-- Loader para la paginación --}}
        <div wire:loading.delay class="absolute inset-0 bg-chocolate bg-opacity-70 z-10 flex items-center justify-center">
            <span class="text-cream/70 font-semibold">Cargando datos...</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
                <thead class="bg-white/5">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cream/60 uppercase tracking-wider">
                            Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cream/60 uppercase tracking-wider">SKU
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-cream/60 uppercase tracking-wider">
                            Stock Físico</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-cream/60 uppercase tracking-wider">
                            Reservado</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-cream/60 uppercase tracking-wider">
                            Disponible</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-cream/60 uppercase tracking-wider">
                            Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-cream/60 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-chocolate divide-y divide-white/10">
                    @forelse($inventories as $inventory)
                        <tr x-data="{ openAdjust: false }" class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-bone">{{ $inventory->product->name }}</div>
                                <div class="text-xs text-cream/60">
                                    {{ $inventory->product->category->name ?? 'General' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-cream/60">{{ $inventory->product->sku }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center font-semibold text-cream/90">
                                {{ number_format($inventory->stock, 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-amber-300 font-medium">
                                {{ number_format($inventory->reserved_stock, 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span
                                    class="text-lg font-bold {{ $inventory->stock - $inventory->reserved_stock <= $inventory->minimum_stock ? 'text-rose-300' : 'text-emerald-400' }}">
                                    {{ number_format($inventory->stock - $inventory->reserved_stock, 0) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($inventory->stock <= 0)
                                    <span
                                        class="px-2 py-1 text-xs font-bold rounded-full bg-rose-500/15 text-rose-300">Agotado</span>
                                @elseif($inventory->stock <= $inventory->minimum_stock)
                                    <span
                                        class="px-2 py-1 text-xs font-bold rounded-full bg-amber-500/15 text-amber-200">Stock
                                        Bajo</span>
                                @else
                                    <span
                                        class="px-2 py-1 text-xs font-bold rounded-full bg-emerald-500/15 text-emerald-300">Óptimo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <button @click="openAdjust = true"
                                    class="bg-chocolate text-white px-3 py-1 rounded-md hover:bg-white/10 transition">
                                    Ajustar
                                </button>

                                {{-- MODAL DE AJUSTE MANUAL CON ALPINE --}}
                                <template x-teleport="body">
                                    <div x-show="openAdjust" class="fixed inset-0 z-50 overflow-y-auto"
                                        style="display: none;">
                                        <div class="flex items-center justify-center min-h-screen px-4">
                                            <div class="fixed inset-0 bg-ink bg-opacity-75 transition-opacity"
                                                @click="openAdjust = false"></div>

                                            <div
                                                class="bg-chocolate rounded-xl shadow-xl overflow-hidden transform transition-all sm:max-w-lg sm:w-full z-50">
                                                <form action="{{ route('admin.inventories.update', $inventory->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="p-6 text-left">
                                                        <h3 class="text-xl font-bold text-bone mb-4">Ajuste de
                                                            Stock: {{ $inventory->product->name }}</h3>
                                                        <div class="space-y-4">
                                                            <div>
                                                                <label
                                                                    class="block text-sm font-medium text-cream/90">Tipo
                                                                    de Ajuste</label>
                                                                <select name="adjustment_type"
                                                                    class="mt-1 w-full rounded-lg border-white/15">
                                                                    <option value="addition">➕ Sumar (Entrada)</option>
                                                                    <option value="subtraction">➖ Restar (Salida/Merma)
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="block text-sm font-medium text-cream/90">Cantidad</label>
                                                                <input type="number" name="quantity" min="1"
                                                                    step="0.01"
                                                                    class="mt-1 w-full rounded-lg border-white/15"
                                                                    required>
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="block text-sm font-medium text-cream/90">Motivo</label>
                                                                <textarea name="reason" rows="2" class="mt-1 w-full rounded-lg border-white/15" required></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="bg-white/5 px-6 py-4 flex justify-end space-x-3">
                                                        <button type="button" @click="openAdjust = false"
                                                            class="text-cream/90 font-medium">Cancelar</button>
                                                        <button type="submit"
                                                            class="bg-wine text-white px-4 py-2 rounded-lg font-bold hover:bg-wine-dark">Procesar
                                                            Ajuste</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-cream/60">No hay existencias
                                registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($inventories->hasPages())
            <div class="px-6 py-4 border-t border-white/10">
                {{ $inventories->links() }}
            </div>
        @endif
    </div>
</div>
