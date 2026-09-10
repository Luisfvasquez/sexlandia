@extends('admin.layouts.app')

@section('title', 'Detalle de Orden ' . $order->order_number)

@section('content')
    <div class="space-y-6" x-data="{ showUploadModal: false }">

        {{-- Alertas --}}
        @if (session('success'))
            <div class="bg-emerald-500/15 border-l-4 border-emerald-500 text-emerald-300 p-4 rounded-r shadow-sm">
                <p class="font-bold">{{ session('success') }}</p>
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-rose-500/15 border-l-4 border-rose-500 text-rose-300 p-4 rounded-r shadow-sm">
                <p class="font-bold">{{ $errors->first() }}</p>
            </div>
        @endif

        {{-- Encabezado con Badges --}}
        <div
            class="bg-chocolate rounded-xl shadow-sm p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.orders.index') }}" class="text-cream/50 hover:text-cream/70">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <h1 class="text-3xl font-black text-cream">{{ $order->order_number }}</h1>
                </div>
                <p class="text-sm text-cream/60 mt-1 ml-9">Creada el {{ $order->created_at->format('d/m/Y h:i A') }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                @php
                    // Diccionarios de traducción
                    $statusTranslations = [
                        'pending' => 'Pendiente',
                        'processing' => 'Procesando',
                        'ready_for_pickup' => 'Listo para retirar',
                        'ready_for_delivery' => 'Listo para delivery',
                        'in_transit' => 'En tránsito (Repartidor)',
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

                <span
                    class="px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider 
        {{ in_array($order->status, ['completed', 'delivered'])
            ? 'bg-emerald-500/15 text-emerald-300'
            : ($order->status == 'cancelled'
                ? 'bg-rose-500/15 text-rose-300'
                : ($order->status == 'in_transit' ? 'bg-amber-500/15 text-amber-300' : 'bg-amber-500/15 text-amber-300')) }}">
                    Estado: {{ $statusTranslations[$order->status] ?? ucfirst($order->status) }}
                </span>

                <span
                    class="px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider 
        {{ $order->payment_status == 'paid' ? 'bg-wine/15 text-blush' : 'bg-amber-500/15 text-amber-200' }}">
                    Pago: {{ $paymentTranslations[$order->payment_status] ?? ucfirst($order->payment_status) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- COLUMNA IZQUIERDA: Info General y Productos --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Datos del Cliente y Envío --}}
                <div class="bg-chocolate rounded-xl shadow-sm p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-xs font-bold text-cream/50 uppercase tracking-wider mb-2">Datos del Cliente</h3>
                        <p class="font-bold text-cream text-lg">
                            {{ $order->client_name ?? ($order->client->name ?? 'Consumidor Final') }}</p>
                        <p class="text-sm text-cream/70">{{ $order->client->identification ?? 'N/A' }}</p>
                        <p class="text-sm text-cream/70">{{ $order->client_phone ?? ($order->client->phone ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-cream/50 uppercase tracking-wider mb-2">Detalles de Entrega</h3>
                        <p class="font-bold text-cream">
                            {{ $order->order_type === 'pickup' || $order->order_type === 'store_pickup' ? 'Retiro en Tienda' : 'Delivery / Envió a domicilio' }}
                        </p>
                        <p class="text-sm text-cream/70 mt-1">📍 {{ $order->delivery_address ?? 'No especificada' }}</p>
                        
                        @if($order->deliveryUser)
                            <div class="mt-3 pt-3 border-t border-white/10 text-xs">
                                <span class="font-bold text-cream/90 block">🚚 Repartidor Asignado:</span>
                                <p class="text-blush font-semibold">{{ $order->deliveryUser->name }} ({{ $order->deliveryUser->email }})</p>
                                @if($order->delivery_assigned_at)
                                    <p class="text-cream/60">Tomado: {{ $order->delivery_assigned_at->format('d/m/Y h:i A') }}</p>
                                @endif
                                @if($order->delivered_at)
                                    <p class="text-emerald-400 font-semibold mt-1">✓ Entregado: {{ $order->delivered_at->format('d/m/Y h:i A') }}</p>
                                @endif
                                @if($order->delivery_notes)
                                    <p class="text-cream/70 italic mt-1">Notas: "{{ $order->delivery_notes }}"</p>
                                @endif
                            </div>
                        @else
                            <p class="text-xs text-amber-300 mt-2 font-medium">⏳ Esperando asignación de repartidor</p>
                        @endif
                    </div>
                </div>

                {{-- Detalle de Productos --}}
                <div class="bg-chocolate rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-white/10 bg-white/5/50">
                        <h3 class="font-bold text-cream">Productos Solicitados</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-white/10">
                            <thead class="bg-white/5 text-xs text-cream/60 uppercase font-bold">
                                <tr>
                                    <th class="px-6 py-3 text-left">Producto</th>
                                    <th class="px-6 py-3 text-center">Cant.</th>
                                    <th class="px-6 py-3 text-right">P. Unitario</th>
                                    <th class="px-6 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10 text-sm">
                                {{-- Modificamos la iteración aquí --}}
                                @foreach ($details as $detail)
                                    <tr>
                                        <td class="px-6 py-4 font-semibold text-cream">
                                            {{ $detail->product->name }}
                                            @if ($detail->bulk)
                                                <span class="block text-xs text-cream/50 font-normal">Presentación:
                                                    {{ $detail->bulk->name }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center font-bold text-cream/70">
                                            {{ $detail->product->unit_type === 'gram' ? number_format($detail->quantity, 3) . ' Kg' : number_format($detail->quantity, 0) . ' Und' }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-cream/60">$
                                            {{ number_format($detail->unit_price, 2) }}</td>
                                        <td class="px-6 py-4 text-right font-bold text-cream">$
                                            {{ number_format($detail->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Enlaces de paginación con padding para que respire el diseño --}}
                    @if ($details->hasPages())
                        <div class="px-6 py-4 border-t border-white/10">
                            {{ $details->links() }}
                        </div>
                    @endif

                    <div class="p-6 bg-white/5 flex justify-end">
                        <div class="w-full md:w-1/2 space-y-2">
                            <div class="flex justify-between text-sm text-cream/60">
                                <span>Subtotal</span>
                                {{-- Estos totales seguirán funcionando porque provienen del objeto Order principal --}}
                                <span>$ {{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div
                                class="flex justify-between text-xl font-black text-bone border-t border-white/10 pt-2">
                                <span>Total</span>
                                <span>$ {{ number_format($order->total, 2) }}</span>
                            </div>
                            @if ($order->exchange_rate)
                                <div class="flex justify-end text-sm font-bold text-blush">
                                    ≈ Bs. {{ number_format($order->total * $order->exchange_rate, 2) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Botones de Acción (Aprobar/Rechazar) SOLO si está Pendiente --}}
                @if ($order->payment_status === 'pending' or $order->status === 'ready_for_pickup')
                    <div class="bg-chocolate rounded-xl shadow-sm p-6 border border-white/10 space-y-3">
                        <h3 class="font-bold text-cream text-sm mb-4">Acciones de Verificación</h3>

                        {{-- Botón Aprobar --}}
                        <form action="{{ route('admin.orders.approve', $order->id) }}" method="POST">
                            @csrf

                            <div class="flex flex-col gap-3">
                                {{-- Mostrar la opción de solo verificar pago si NO ha sido verificado y NO es de tienda --}}
                                @php
                                    $nextStatus = $order->order_type === 'delivery' ? 'ready_for_delivery' : 'ready_for_pickup';
                                    $btnText = $order->order_type === 'delivery' ? 'Solo Verificar Pago (Lista para delivery)' : 'Solo Verificar Pago (Lista para retiro)';
                                @endphp
                                @if ($order->order_type !== 'store' && $order->status !== $nextStatus && $order->payment_status === 'pending')
                                    <button type="submit" name="status" value="{{ $nextStatus }}"
                                        onclick="return confirm('¿Aprobar el pago y marcar como LISTA PARA ENTREGA/RETIRO?');"
                                        class="w-full bg-wine hover:bg-wine-dark text-white font-bold py-3 px-4 rounded-xl shadow transition-colors flex justify-center items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $btnText }}
                                    </button>
                                @endif

                                {{-- Botón para verificar pago Y entregar, o solo entregar si ya estaba verificada --}}
                                <button type="submit" name="status" value="completed"
                                    onclick="return confirm('¿Estás seguro de marcar esta orden como COMPLETADA y ENTREGADA?');"
                                    class="w-full bg-emerald-600 hover:bg-emerald-600 text-white font-bold py-3 px-4 rounded-xl shadow transition-colors flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ $order->payment_status === 'pending' ? 'Verificar Pago y Entregar Pedido' : 'Marcar como Entregada' }}
                                </button>
                            </div>
                        </form>

                        <div class="relative py-2">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-white/10"></div>
                            </div>
                            <div class="relative flex justify-center"><span
                                    class="bg-chocolate px-2 text-xs text-cream/50">Ó</span></div>
                        </div>

                        {{-- Formulario Rechazar --}}
                        <form action="{{ route('admin.orders.reject', $order->id) }}" method="POST"
                            onsubmit="return confirm('ATENCIÓN: ¿Seguro que deseas RECHAZAR esta orden? Se cancelará la compra y el stock será devuelto al inventario automáticamente.');">
                            @csrf
                            <div class="space-y-2 mb-3">
                                <label class="text-xs font-bold text-cream/60">Motivo del rechazo (Opcional):</label>
                                <input type="text" name="notes"
                                    placeholder="Ej: Pago no recibido, referencia inválida..."
                                    class="w-full text-sm rounded-lg border-white/15 focus:border-rose-500 focus:ring focus:ring-rose-500/30">
                            </div>
                            <button type="submit"
                                class="w-full bg-rose-500/10 text-rose-300 hover:bg-rose-600 hover:text-white font-bold py-3 px-4 rounded-xl border border-rose-500/25 hover:border-transparent shadow-sm transition-colors flex justify-center items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Rechazar y Devolver Stock
                            </button>
                        </form>
                    </div>
                @endif

            </div>

            {{-- COLUMNA DERECHA: Comprobantes --}}
            <div class="space-y-6">

                {{-- Panel de Validación de Pagos --}}
                <div class="bg-chocolate rounded-xl shadow-sm border border-white/10 overflow-hidden">
                    <div class="p-4 border-b border-white/10 bg-white/5/50 flex justify-between items-center">
                        <h3 class="font-bold text-cream">Verificación de Pago</h3>

                        {{-- Solo se muestra si la orden está pendiente Y la colección de comprobantes está vacía --}}
                        @if ($order->payment_status === 'pending' && $order->paymentProofs->isEmpty())
                            <button @click="showUploadModal = true"
                                class="text-xs font-bold bg-wine/10 text-blush px-3 py-1.5 rounded-lg hover:bg-wine/15 transition-colors border border-wine/20 shadow-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                Subir Manualmente
                            </button>
                        @endif
                    </div>

                    <div class="p-6 space-y-6">
                        @forelse($order->paymentProofs as $proof)
                            <div class="border border-white/10 rounded-xl p-4 bg-white/5 relative">
                                {{-- Badge de estado del comprobante --}}
                                <span
                                    class="absolute top-3 right-3 text-xs font-bold px-2 py-1 rounded {{ $proof->status == 'verified' ? 'bg-emerald-500/15 text-emerald-300' : ($proof->status == 'rejected' ? 'bg-rose-500/15 text-rose-300' : 'bg-amber-500/15 text-amber-300') }}">
                                    {{ ucfirst($proof->status) }}
                                </span>

                                <p class="text-xs font-bold text-cream/50 uppercase mb-1">Referencia</p>
                                <p class="font-mono font-bold text-lg text-cream mb-3">{{ $proof->reference }}</p>

                                @if ($proof->images->count() > 0)
                                    @php $proofImageUrl = route('admin.orders.proofImage', $proof->images->first()->id); @endphp
                                    <div
                                        class="mt-2 aspect-[3/4] bg-white/10 rounded-lg overflow-hidden border border-white/15">
                                        <a href="{{ $proofImageUrl }}" target="_blank"
                                            title="Clic para ampliar">
                                            <img src="{{ $proofImageUrl }}" alt="Comprobante"
                                                class="w-full h-full object-cover hover:scale-105 transition-transform">
                                        </a>
                                    </div>
                                    <p class="text-center text-xs text-cream/50 mt-2">Haz clic en la imagen para ampliar
                                    </p>
                                @else
                                    <div class="bg-white/5 text-cream/50 text-xs p-4 rounded text-center">Sin imagen
                                        adjunta</div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center p-4 text-cream/60 text-sm italic">
                                El cliente no ha subido comprobantes para esta orden.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
        <div x-cloak x-show="showUploadModal"
            class="fixed inset-0 bg-ink/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div @click.away="showUploadModal = false" x-show="showUploadModal" x-transition.scale.95
                class="bg-chocolate rounded-2xl w-full max-w-md shadow-2xl overflow-hidden flex flex-col">

                <div class="px-5 py-4 bg-white/5 border-b border-white/10 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 bg-wine/10 text-blush rounded-lg flex items-center justify-center border border-wine/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-cream text-sm">Cargar Comprobante Manual</h3>
                            <p class="text-[10px] font-bold text-cream/50">Archiva recibos de WhatsApp o similares</p>
                        </div>
                    </div>
                    <button type="button" @click="showUploadModal = false"
                        class="text-cream/50 hover:text-cream/70 bg-white/5 p-1.5 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.orders.proof', $order->id) }}" method="POST"
                    enctype="multipart/form-data" x-data="{ isSubmitting: false }"
                    @submit="setTimeout(() => isSubmitting = true, 50)" class="p-5 space-y-4">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-cream/60 uppercase tracking-wider">¿Dónde se recibió el
                            dinero?</label>
                        <select name="payment_method_id" required
                            class="w-full text-sm rounded-xl border-white/10 focus:border-wine focus:ring-wine bg-white/5 font-semibold">
                            <option value="" disabled selected>Selecciona el método de pago...</option>
                            @foreach ($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-cream/60 uppercase tracking-wider">Monto
                                Total (USD)</label>
                            <input type="number" step="0.01" name="amount" value="{{ $order->total }}" required
                                class="w-full text-sm rounded-xl border-white/10 focus:border-wine focus:ring-wine bg-white/5 font-black text-blush">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-cream/60 uppercase tracking-wider">Nro.
                                Referencia</label>
                            <input type="text" name="reference" required placeholder="Ej: 001452"
                                class="w-full text-sm rounded-xl border-white/10 focus:border-wine focus:ring-wine bg-white/5 font-bold">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-cream/60 uppercase tracking-wider">Foto del Recibo
                            (Obligatorio)</label>
                        <input type="file" name="payment_proof" accept="image/png, image/jpeg, image/jpg, image/webp"
                            required
                            class="w-full text-xs rounded-xl border border-white/10 p-1.5 bg-white/5 file:mr-3 file:rounded-lg file:border-0 file:bg-wine/15 file:text-blush file:font-bold file:px-3 file:py-1.5 file:cursor-pointer">
                    </div>

                    <div class="pt-2 flex gap-3">
                        <button type="button" @click="showUploadModal = false"
                            class="w-1/3 bg-white/5 text-cream/70 font-bold py-2.5 rounded-xl hover:bg-white/10 transition-colors text-xs">Cancelar</button>

                        <button type="submit" :disabled="isSubmitting"
                            class="flex-1 bg-wine disabled:bg-wine text-white font-bold py-2.5 rounded-xl hover:bg-wine-dark shadow-sm transition-colors text-xs flex justify-center items-center gap-2">
                            <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            <svg x-cloak x-show="isSubmitting" class="w-4 h-4 animate-spin" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span x-text="isSubmitting ? 'Guardando...' : 'Cargar Comprobante'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
