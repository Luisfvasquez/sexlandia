@extends('storefront.layout')

@section('title', 'Mis compras · ' . config('site.brand.name'))

@section('content')
    @include('storefront.partials.account-nav', ['active' => 'purchases'])

    <section class="relative bg-ink border-t border-white/5 overflow-hidden pt-16 pb-24 lg:pb-32" x-data="purchasesManager()">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_rgba(141,38,61,0.08),_transparent_60%)] pointer-events-none"></div>

        <div class="relative w-full max-w-5xl mx-auto px-6 lg:px-8">
            <div class="mb-12 animate-slide-up-fade">
                <p class="text-rose-600 text-[10px] tracking-[0.3em] uppercase font-bold mb-6">Tu historial</p>
                <h1 class="text-4xl sm:text-5xl lg:text-7xl leading-[1.05] text-white font-bold tracking-tight">
                    Mis <em class="font-serif italic text-rose-500 font-normal">compras.</em>
                </h1>
                <p class="mt-6 text-neutral-400 font-light text-sm lg:text-base leading-relaxed max-w-xl border-l border-white/10 pl-5">
                    Rastrea tus órdenes, estados de entrega y pagos registrados.
                </p>
            </div>

            @if (session('success'))
                <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 px-4 py-3 rounded-xl flex items-center gap-3">
                    <svg class="w-6 h-6 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-semibold text-sm">{{ session('success') }}</span>
                </div>
                @if (Str::contains(session('success'), 'Orden de compra') || Str::contains(session('success'), 'ORD-'))
                    <script>try { localStorage.removeItem('client_shopping_cart'); } catch (e) {}</script>
                @endif
            @endif

            <div class="space-y-6">
                <div class="bg-white/[0.03] border border-white/10 rounded-xl p-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <h3 class="font-bold text-white text-sm tracking-wide">Filtro de órdenes</h3>
                    <div class="flex flex-wrap gap-2 justify-center">
                        <button @click="statusFilter = 'all'"
                            :class="statusFilter === 'all' ? 'bg-white text-black' : 'bg-white/5 text-neutral-400 hover:bg-white/10'"
                            class="px-4 py-2 rounded-full text-[11px] font-bold tracking-[0.08em] uppercase transition-colors duration-200">Todas</button>
                        <button @click="statusFilter = 'pending'"
                            :class="statusFilter === 'pending' ? 'bg-white text-black' : 'bg-white/5 text-neutral-400 hover:bg-white/10'"
                            class="px-4 py-2 rounded-full text-[11px] font-bold tracking-[0.08em] uppercase transition-colors duration-200">En proceso</button>
                        <button @click="statusFilter = 'delivered'"
                            :class="statusFilter === 'delivered' ? 'bg-white text-black' : 'bg-white/5 text-neutral-400 hover:bg-white/10'"
                            class="px-4 py-2 rounded-full text-[11px] font-bold tracking-[0.08em] uppercase transition-colors duration-200">Entregadas</button>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($orders as $order)
                        @php
                            $statusMap = [
                                'completed' => ['label' => 'Entregado', 'class' => 'bg-green-500/10 text-green-400 border-green-500/20'],
                                'delivered' => ['label' => 'Entregado', 'class' => 'bg-green-500/10 text-green-400 border-green-500/20'],
                                'ready_for_pickup' => ['label' => 'Lista para retirar', 'class' => 'bg-blue-500/10 text-blue-400 border-blue-500/20 animate-pulse'],
                                'pending' => ['label' => 'En proceso', 'class' => 'bg-amber-500/10 text-amber-400 border-amber-500/20'],
                                'cancelled' => ['label' => 'Cancelado', 'class' => 'bg-rose-500/10 text-rose-400 border-rose-500/20'],
                            ];
                            $paymentMap = [
                                'paid' => ['label' => 'Pagado', 'class' => 'bg-green-500/10 text-green-400 border-green-500/20'],
                                'partial' => ['label' => 'Abonado', 'class' => 'bg-rose-500/10 text-rose-300 border-rose-500/20'],
                                'pending' => ['label' => 'Sin pagar', 'class' => 'bg-rose-500/10 text-rose-400 border-rose-500/20'],
                            ];
                            $statusData = $statusMap[$order->status] ?? ['label' => ucfirst($order->status), 'class' => 'bg-white/5 text-neutral-300 border-white/10'];
                            $paymentData = $paymentMap[$order->payment_status] ?? ['label' => ucfirst($order->payment_status), 'class' => 'bg-rose-500/10 text-rose-400 border-rose-500/20'];
                        @endphp
                        <div x-show="filterOrder(@js($order))"
                            class="bg-white/[0.03] border border-white/10 rounded-xl overflow-hidden transition-colors duration-300 hover:border-white/20"
                            x-data="{ expanded: false }">

                            <div @click="expanded = !expanded"
                                class="p-6 cursor-pointer hover:bg-white/[0.02] transition-colors flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <div class="flex items-center gap-3 w-full sm:w-auto">
                                    <div :class="expanded ? 'rotate-180' : 'rotate-0'"
                                        class="flex w-8 h-8 bg-white/5 rounded-lg items-center justify-center text-neutral-400 transition-transform duration-200 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                            <span class="font-mono font-bold text-white text-sm sm:text-base">{{ $order->order_number }}</span>
                                            <span class="text-[10px] text-neutral-500 font-semibold">{{ $order->created_at->format('d/m/Y h:i A') }}</span>
                                        </div>
                                        @if ($order->delivery_address != null)
                                            <p class="text-xs text-neutral-500 truncate mt-1">Despacho: {{ $order->delivery_address }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-3 sm:pt-0 border-t border-white/10 sm:border-t-0">
                                    <div class="text-left sm:text-right">
                                        <span class="text-base sm:text-lg font-black text-white block">${{ number_format($order->total, 2, ',', '.') }}</span>
                                        @if ($order->exchange_rate && $order->exchange_rate > 0)
                                            <span class="text-rose-400 font-bold text-[10px] sm:text-xs block">Ref. {{ number_format($order->total * $order->exchange_rate, 2, ',', '.') }} BS</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="inline-flex px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border {{ $statusData['class'] }}">{{ $statusData['label'] }}</span>
                                        <span class="inline-flex px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border {{ $paymentData['class'] }}">{{ $paymentData['label'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <div x-cloak x-show="expanded" x-transition class="border-t border-white/10 bg-black/20 p-6 space-y-6">
                                @if (($order->status == 'ready_for_pickup' and $order->payment_status == 'paid') && is_null($order->delivered_at))
                                    @php
                                        $qrSvg = QrCode::size(250)->margin(1)->generate(route('admin.orders.show', $order->id));
                                        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
                                    @endphp
                                    <div class="bg-gradient-to-br from-rose-600 to-rose-900 rounded-xl p-1">
                                        <div class="bg-neutral-950 rounded-lg p-6 sm:p-8 flex flex-col md:flex-row items-center justify-center gap-8 text-center md:text-left">
                                            <div class="bg-white p-2 rounded-lg shrink-0">
                                                <img src="{{ $qrBase64 }}" alt="Código QR Orden {{ $order->order_number }}" class="w-48 h-48 md:w-56 md:h-56">
                                            </div>
                                            <div class="space-y-4 max-w-sm">
                                                <h3 class="text-2xl font-black text-white leading-tight">¡Tu pedido está listo!</h3>
                                                <p class="text-neutral-400 text-sm">Presenta este código en el mostrador para retirar tus productos.</p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif(!is_null($order->delivered_at))
                                    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-4">
                                        <div class="bg-emerald-500/15 text-emerald-400 p-2 rounded-lg shrink-0">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-emerald-300">Pedido entregado</h4>
                                            <p class="text-sm text-emerald-400/80 font-medium">Retirado el {{ $order->delivered_at->format('d/m/Y \a \l\a\s h:i A') }}</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="border border-white/10 rounded-xl overflow-x-auto">
                                    <table class="w-full min-w-[420px] text-left border-collapse">
                                        <thead>
                                            <tr class="bg-white/5 text-neutral-500 uppercase text-[10px] font-bold tracking-wider border-b border-white/10">
                                                <th class="py-3 px-4 sm:px-6">Producto</th>
                                                <th class="py-3 px-3 sm:px-6 text-center">Cantidad</th>
                                                <th class="py-3 px-4 text-right hidden sm:table-cell">Precio unitario</th>
                                                <th class="py-3 px-4 sm:px-6 text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-white/10 text-sm">
                                            @foreach ($order->details as $detail)
                                                <tr class="hover:bg-white/[0.02]">
                                                    <td class="py-3 px-4 sm:px-6">
                                                        <span class="font-bold text-white">{{ $detail->product->name }}</span>
                                                        @if ($detail->product->brand)
                                                            <span class="text-[10px] bg-white/5 text-neutral-400 font-bold px-1.5 py-0.5 rounded uppercase ml-1">{{ $detail->product->brand }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-3 sm:px-6 text-center font-bold text-neutral-300 whitespace-nowrap">
                                                        {{ $detail->product->unit_type === 'gram' ? number_format($detail->quantity, 3, ',', '.') : number_format($detail->quantity, 0) }}
                                                        {{ $detail->product->unit_type === 'gram' ? 'Kg' : 'Und' }}
                                                    </td>
                                                    <td class="py-3 px-4 text-right text-neutral-400 font-mono hidden sm:table-cell">${{ number_format($detail->unit_price, 2, ',', '.') }}</td>
                                                    <td class="py-3 px-4 sm:px-6 text-right font-extrabold text-neutral-200 font-mono whitespace-nowrap">${{ number_format($detail->subtotal, 2, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-white/[0.03] border border-white/10 rounded-xl p-5 space-y-4">
                                        <div>
                                            <span class="block text-neutral-500 font-bold text-[10px] uppercase tracking-wider">Dirección de despacho</span>
                                            <p class="text-neutral-200 text-sm font-semibold mt-1">{{ $order->delivery_address }}</p>
                                        </div>
                                        @if ($order->notes)
                                            <div>
                                                <span class="block text-neutral-500 font-bold text-[10px] uppercase tracking-wider">Observaciones</span>
                                                <p class="text-neutral-400 text-sm italic mt-1">"{{ $order->notes }}"</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="bg-white/[0.03] border border-white/10 rounded-xl p-5 space-y-3 text-sm">
                                        <span class="block text-neutral-500 font-bold text-[10px] uppercase tracking-wider border-b border-white/10 pb-2 mb-2">Resumen financiero</span>
                                        <div class="flex justify-between items-center text-neutral-400">
                                            <span>Subtotal</span>
                                            <span class="font-bold text-neutral-200">${{ number_format($order->subtotal, 2, ',', '.') }}</span>
                                        </div>
                                        @if ($order->discount > 0)
                                            <div class="flex justify-between items-center text-rose-400 font-semibold">
                                                <span>Descuento</span><span>-${{ number_format($order->discount, 2, ',', '.') }}</span>
                                            </div>
                                        @endif
                                        @if ($order->tax > 0)
                                            <div class="flex justify-between items-center text-neutral-400">
                                                <span>Impuesto</span><span>+${{ number_format($order->tax, 2, ',', '.') }}</span>
                                            </div>
                                        @endif
                                        <hr class="border-white/10" />
                                        <div class="flex justify-between items-end">
                                            <span class="font-extrabold text-white text-base">Total pedido</span>
                                            <div class="text-right">
                                                <span class="text-xl font-black text-white">${{ number_format($order->total, 2, ',', '.') }}</span>
                                                @if ($order->exchange_rate && $order->exchange_rate > 0)
                                                    <span class="block text-rose-400 font-extrabold text-xs">Ref. {{ number_format($order->total * $order->exchange_rate, 2, ',', '.') }} BS</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white/[0.03] border border-white/10 rounded-xl p-12 text-center flex flex-col items-center justify-center gap-3">
                            <svg class="w-16 h-16 text-white/10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <span class="text-base font-bold text-neutral-300">No tienes historial de compras.</span>
                            <a href="{{ route('storefront.catalog') }}" class="bg-white text-black hover:bg-rose-600 hover:text-white font-bold py-2.5 px-6 rounded-full mt-2 inline-block text-[11px] tracking-[0.15em] uppercase transition-colors">Ir al catálogo</a>
                        </div>
                    @endforelse

                    @if ($orders->hasPages())
                        <div class="pt-2">{{ $orders->links('storefront.partials.pagination') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <script>
        function purchasesManager() {
            return {
                statusFilter: 'all',
                filterOrder(order) {
                    if (this.statusFilter === 'all') return true;
                    if (this.statusFilter === 'pending') return order.status === 'pending' || order.status === 'ready_for_pickup';
                    if (this.statusFilter === 'delivered') return order.status === 'delivered' || order.status === 'completed';
                    return true;
                }
            };
        }
    </script>
@endsection
