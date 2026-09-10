@extends('storefront.layout')

@section('title', 'Mis facturas · ' . config('site.brand.name'))

@section('content')
    @include('storefront.partials.account-nav', ['active' => 'invoices'])

    <section class="relative bg-ink border-t border-white/5 overflow-hidden pt-16 pb-24 lg:pb-32" x-data="invoicesManager()">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_rgba(141,38,61,0.08),_transparent_60%)] pointer-events-none"></div>

        <div class="relative w-full max-w-5xl mx-auto px-6 lg:px-8">
            <div class="mb-12 animate-slide-up-fade">
                <p class="text-rose-600 text-[10px] tracking-[0.3em] uppercase font-bold mb-6">Estado de cuenta</p>
                <h1 class="text-4xl sm:text-5xl lg:text-7xl leading-[1.05] text-white font-bold tracking-tight">
                    Mis <em class="font-serif italic text-rose-500 font-normal">facturas</em> y abonos.
                </h1>
                <p class="mt-6 text-neutral-400 font-light text-sm lg:text-base leading-relaxed max-w-xl border-l border-white/10 pl-5">
                    Monitorea saldos pendientes, cuotas y el historial de comprobantes reportados.
                </p>
            </div>

            @if (session('success'))
                <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 px-4 py-3 rounded-xl flex items-center gap-3">
                    <svg class="w-6 h-6 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-semibold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="space-y-8">
                @php
                    $globalTotalPending = $accounts->where('status', '!=', 'paid')->sum('pending_amount');
                    $globalTotalPaid = $accounts->sum('paid_amount');
                    $globalTotalDebt = $accounts->sum('total_amount');
                @endphp

                <div class="bg-gradient-to-br from-black to-rose-950/40 rounded-2xl p-8 border border-white/10 text-white flex flex-col md:flex-row md:items-center justify-between gap-8">
                    <div class="space-y-4">
                        <span class="bg-rose-500/15 text-rose-300 border border-rose-500/20 text-[10px] font-black tracking-widest uppercase px-3 py-1 rounded-full">Estado financiero global</span>
                        <h3 class="text-3xl font-black mt-2 tracking-tight">Balance pendiente</h3>
                        <div class="flex flex-wrap items-baseline gap-2">
                            <span class="text-4xl font-black text-rose-500">${{ number_format($globalTotalPending, 2, ',', '.') }}</span>
                            @if ($exchangeRate)
                                <span class="text-neutral-400 font-bold text-sm">≈ {{ number_format($globalTotalPending * $exchangeRate, 2, ',', '.') }} BS pendientes</span>
                            @else
                                <span class="text-neutral-400 font-bold text-sm">USD pendientes</span>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-8 border-t md:border-t-0 md:border-l border-white/10 pt-6 md:pt-0 md:pl-8">
                        <div>
                            <span class="text-neutral-500 text-[10px] font-bold uppercase tracking-wider block">Total comprado (a plazos)</span>
                            <span class="text-lg font-extrabold text-neutral-200 block">${{ number_format($globalTotalDebt, 2, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-neutral-500 text-[10px] font-bold uppercase tracking-wider block">Total amortizado</span>
                            <span class="text-lg font-extrabold text-emerald-400 block">${{ number_format($globalTotalPaid, 2, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="shrink-0">
                        <button @click="reportPaymentOpen = true"
                            class="w-full md:w-auto bg-gradient-to-r from-emerald-400 to-teal-500 hover:from-emerald-500 hover:to-teal-600 text-black font-black px-6 py-3.5 rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 inline-flex items-center justify-center gap-2 text-sm cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Reportar un pago
                        </button>
                    </div>
                </div>

                <div class="space-y-6">
                    <h3 class="font-extrabold text-white text-lg leading-tight">Mis cuentas de crédito y abonos</h3>

                    @forelse($accounts as $account)
                        <div class="bg-white/[0.03] border border-white/10 rounded-xl overflow-hidden transition-colors duration-300 hover:border-white/20">
                            <div class="p-6 border-b border-white/10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-black/20">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="bg-rose-500/10 border border-rose-500/20 text-rose-300 font-extrabold font-mono text-xs px-2.5 py-1 rounded-lg">Pedido #{{ $account->order->order_number }}</span>
                                        <span class="text-neutral-500 text-[10px] font-bold">Vence: {{ \Carbon\Carbon::parse($account->due_date)->format('d/m/Y') }}</span>
                                    </div>
                                    <p class="text-xs text-neutral-400 mt-2">Monto inicial: <span class="font-bold text-neutral-200">${{ number_format($account->total_amount, 2, ',', '.') }}</span></p>
                                </div>
                                <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-3 sm:pt-0 border-t border-white/10 sm:border-t-0">
                                    <div class="text-left sm:text-right">
                                        <span class="text-neutral-500 text-[10px] font-bold uppercase tracking-wider block">Saldo restante</span>
                                        <span class="text-base sm:text-lg font-black text-rose-500 block">${{ number_format($account->pending_amount, 2, ',', '.') }}</span>
                                        @if ($exchangeRate)
                                            <span class="text-rose-400 font-bold text-[10px] block">≈ {{ number_format($account->pending_amount * $exchangeRate, 2, ',', '.') }} BS</span>
                                        @endif
                                    </div>
                                    <div>
                                        @if ($account->status === 'paid')
                                            <span class="inline-flex px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold bg-green-500/10 text-green-400 border border-green-500/20">Liquidada</span>
                                        @elseif($account->status === 'cancelled')
                                            <span class="inline-flex px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold bg-rose-500/10 text-rose-400 border border-rose-500/20">Rechazada</span>
                                        @elseif($account->status === 'partial')
                                            <span class="inline-flex px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold bg-rose-500/10 text-rose-300 border border-rose-500/20">Parcial</span>
                                        @else
                                            <span class="inline-flex px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold bg-white/5 text-neutral-300 border border-white/10">Vigente</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="p-6">
                                <span class="block text-neutral-500 font-bold text-[10px] uppercase tracking-wider mb-4">Historial de cuotas y abonos</span>
                                <div class="overflow-x-auto rounded-xl border border-white/10">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="bg-white/5 text-neutral-500 uppercase text-[10px] font-bold tracking-wider border-b border-white/10">
                                                <th class="py-3 px-4 hidden sm:table-cell">Referencia / Banco</th>
                                                <th class="py-3 px-4">Fecha de reporte</th>
                                                <th class="py-3 px-4 text-right">Monto reportado</th>
                                                <th class="py-3 px-4 text-center">Estado</th>
                                                <th class="py-3 px-4 hidden md:table-cell">Observaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-white/10 text-sm">
                                            @forelse($account->order->payments->sortByDesc('created_at') as $payment)
                                                <tr class="hover:bg-white/[0.02]">
                                                    <td class="py-3 px-4 hidden sm:table-cell">
                                                        <span class="font-bold text-neutral-300 block">{{ $payment->reference ?? 'S/R' }}</span>
                                                        <span class="text-[10px] text-neutral-500 font-semibold">{{ $payment->paymentMethod->name ?? 'Método Borrado' }}</span>
                                                    </td>
                                                    <td class="py-3 px-4 text-neutral-300 font-medium">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') : $payment->created_at->format('d/m/Y') }}</td>
                                                    <td class="py-3 px-4 text-right font-extrabold text-neutral-200 font-mono">${{ number_format($payment->amount, 2, ',', '.') }}</td>
                                                    <td class="py-3 px-4 text-center">
                                                        @if ($payment->status === 'verified')
                                                            <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-green-500/10 text-green-400 border border-green-500/20">Aprobado</span>
                                                        @elseif($payment->status === 'pending')
                                                            <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/20 animate-pulse">En revisión</span>
                                                        @elseif($payment->status === 'rejected')
                                                            <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-rose-500/10 text-rose-400 border border-rose-500/20">Rechazado</span>
                                                        @else
                                                            <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-white/5 text-neutral-300 border border-white/10">{{ ucfirst($payment->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-4 text-neutral-400 text-xs truncate max-w-[12rem] hidden md:table-cell" title="{{ $payment->notes }}">{{ $payment->notes ?? 'Sin observaciones' }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="py-8 text-center text-neutral-500">No has reportado ningún comprobante para este pedido.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white/[0.03] border border-white/10 rounded-xl p-12 text-center flex flex-col items-center justify-center gap-3">
                            <svg class="w-16 h-16 text-white/10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-base font-bold text-neutral-300">No posees deudas vigentes.</span>
                            <p class="text-sm text-neutral-500">¡Estás al día con tus compromisos! Gracias por tu puntualidad.</p>
                        </div>
                    @endforelse

                    @if ($accounts->hasPages())
                        <div class="pt-2">{{ $accounts->links('storefront.partials.pagination') }}</div>
                    @endif
                </div>
            </div>

            {{-- Modal reportar pago --}}
            <div x-cloak x-show="reportPaymentOpen" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-[200] flex items-center justify-center p-4">
                <div @click.away="reportPaymentOpen = false" x-show="reportPaymentOpen" x-transition.scale.95
                    class="bg-neutral-950 border border-white/10 rounded-2xl w-full max-w-lg max-h-[95vh] flex flex-col overflow-hidden">
                    <div class="px-5 py-4 sm:px-6 sm:py-5 bg-white/5 border-b border-white/10 flex justify-between items-center shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center border border-emerald-500/20 shrink-0">
                                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-white text-sm sm:text-base leading-tight">Reportar comprobante</h3>
                                <p class="text-neutral-500 text-[10px] sm:text-xs font-medium">Registra tu comprobante para revisión</p>
                            </div>
                        </div>
                        <button type="button" @click="reportPaymentOpen = false" class="p-2 text-neutral-500 hover:text-white hover:bg-white/5 rounded-lg transition-colors shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('client.invoices.report') }}" method="POST" enctype="multipart/form-data"
                        @submit="setTimeout(() => isSubmitting = true, 50)" class="p-5 sm:p-6 space-y-4 overflow-y-auto">
                        @csrf
                        @if ($errors->any())
                            <div class="bg-rose-500/10 border border-rose-500/20 text-rose-300 px-3 py-2 rounded-xl text-xs font-bold mb-2">{{ $errors->first() }}</div>
                        @endif

                        <div class="bg-rose-500/10 border border-rose-500/20 rounded-xl p-4 text-xs text-rose-200 flex items-start gap-3">
                            <svg class="w-6 h-6 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <span class="font-extrabold block mb-0.5">Nota de operación</span>
                                <span>Al guardar, administración revisará la captura y aplicará el abono a tu deuda.</span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-neutral-300 font-extrabold text-xs uppercase tracking-wider">¿A cuál pedido corresponde?</label>
                            <select name="account_receivable_id" required class="w-full bg-white/5 border border-white/10 rounded-xl p-3 sm:p-3.5 text-white focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 text-sm transition-all outline-none font-semibold [&>option]:bg-neutral-900">
                                <option value="" disabled selected>Selecciona la cuenta...</option>
                                @foreach ($accounts->where('status', '!=', 'paid')->where('status', '!=', 'cancelled') as $acc)
                                    <option value="{{ $acc->id }}" {{ old('account_receivable_id') == $acc->id ? 'selected' : '' }}>
                                        Pedido #{{ $acc->order->order_number }} - Pendiente: ${{ number_format($acc->pending_amount, 2, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-neutral-300 font-extrabold text-xs uppercase tracking-wider">Método utilizado</label>
                                <select name="payment_method_id" required class="w-full bg-white/5 border border-white/10 rounded-xl p-3 sm:p-3.5 text-white focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 text-sm transition-all outline-none [&>option]:bg-neutral-900">
                                    <option value="" disabled selected>¿A dónde pagaste?</option>
                                    @foreach ($paymentMethods as $pm)
                                        <option value="{{ $pm->id }}" {{ old('payment_method_id') == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-neutral-300 font-extrabold text-xs uppercase tracking-wider">Monto abono (USD)</label>
                                <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required placeholder="0.00"
                                    class="w-full bg-white/5 border border-white/10 rounded-xl p-3 sm:p-3.5 text-white placeholder-neutral-600 focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 text-sm transition-all outline-none font-bold" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-neutral-300 font-extrabold text-xs uppercase tracking-wider">Ref. bancaria</label>
                                <input type="text" name="reference" value="{{ old('reference') }}" required placeholder="Ej: 00156942"
                                    class="w-full bg-white/5 border border-white/10 rounded-xl p-3 sm:p-3.5 text-white placeholder-neutral-600 focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 text-sm transition-all outline-none font-bold" />
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-neutral-300 font-extrabold text-xs uppercase tracking-wider">Captura (obligatorio)</label>
                                <input type="file" name="payment_proof" accept="image/png, image/jpeg, image/jpg, image/webp" required
                                    class="w-full bg-white/5 border border-white/10 rounded-xl p-1.5 text-xs text-neutral-400 transition-all outline-none file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-500/15 file:text-emerald-300 hover:file:bg-emerald-500/25" />
                            </div>
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row items-center gap-3 pt-4 border-t border-white/10 shrink-0 mt-4">
                            <button type="button" @click="reportPaymentOpen = false" class="w-full sm:w-1/3 bg-white/5 hover:bg-white/10 text-neutral-300 font-bold py-3.5 rounded-xl text-sm sm:text-xs transition-colors">Cancelar</button>
                            <button type="submit" :disabled="isSubmitting"
                                class="w-full sm:flex-1 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 disabled:from-neutral-700 disabled:to-neutral-700 text-white font-extrabold py-3.5 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 text-sm sm:text-xs cursor-pointer disabled:cursor-not-allowed">
                                <span x-text="isSubmitting ? 'Enviando...' : 'Enviar comprobante'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        function invoicesManager() {
            return {
                reportPaymentOpen: {{ $errors->any() ? 'true' : 'false' }},
                isSubmitting: false,
            };
        }
    </script>
@endsection
