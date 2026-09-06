@extends('storefront.layout')

@section('title', 'Finalizar compra · ' . config('site.brand.name'))

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
    @php
        $safeRate = isset($exchangeRate) && $exchangeRate > 0 ? (float) str_replace(',', '.', $exchangeRate) : 1;
    @endphp

    <section class="account section-pad" x-data="checkoutManager()" x-init="init()" style="padding-top:120px;">
        <div class="account__intro reveal">
            <a class="arrow-link" href="{{ route('storefront.catalog') }}" style="margin-bottom:18px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 19l-7-7 7-7"/></svg>
                VOLVER AL CATÁLOGO
            </a>
            <h1>Finalizar <em>compra.</em></h1>
            <p class="account__lead">Verifica tus productos, elige el método de entrega y reporta tu pago.</p>
        </div>

        @if ($errors->any())
            <div class="max-w-5xl mx-auto mb-6">
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex flex-col gap-1 shadow-sm">
                    @foreach ($errors->all() as $error)
                        <span class="font-semibold text-sm">{{ $error }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="max-w-5xl mx-auto">
            <form action="{{ route('client.checkout') }}" method="POST" enctype="multipart/form-data"
                @submit="setTimeout(() => isSubmitting = true, 50)" class="flex flex-col lg:flex-row gap-8">
                @csrf
                <input type="hidden" name="cart_items" :value="JSON.stringify(prepareCartForSubmit())" />

                <div class="flex-1 space-y-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center"><span class="font-extrabold">1</span></div>
                            <h3 class="font-extrabold text-slate-800 text-lg">Método de Entrega</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="relative flex cursor-pointer rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-400"
                                :class="{ 'ring-2 ring-indigo-600 border-indigo-600 bg-indigo-50/30': deliveryType === 'store_pickup' }">
                                <input type="radio" name="delivery_type" value="store_pickup" x-model="deliveryType" class="sr-only">
                                <div class="flex flex-col">
                                    <span class="block text-sm font-bold text-slate-900">Retiro en Tienda</span>
                                    <span class="mt-1 text-xs text-slate-500">Busca tu pedido sin costo adicional.</span>
                                </div>
                            </label>
                            <label class="relative flex cursor-pointer rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-400"
                                :class="{ 'ring-2 ring-indigo-600 border-indigo-600 bg-indigo-50/30': deliveryType === 'delivery' }">
                                <input type="radio" name="delivery_type" value="delivery" x-model="deliveryType" class="sr-only">
                                <div class="flex flex-col">
                                    <span class="block text-sm font-bold text-slate-900">Delivery</span>
                                    <span class="mt-1 text-xs text-slate-500">Recibe en tu dirección.</span>
                                </div>
                            </label>
                        </div>

                        <div x-show="deliveryType === 'delivery'" x-transition class="space-y-4 pt-2">
                            <div class="space-y-2">
                                <label class="block text-slate-700 font-extrabold text-xs uppercase tracking-wider">Dirección detallada</label>
                                <textarea name="delivery_address" id="delivery_address" rows="2" placeholder="Ej: Av. Principal, Urb. Centro, Casa Nro 45..."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all outline-none">{{ old('delivery_address', $client->address) }}</textarea>
                            </div>
                            <input type="hidden" name="latitude" id="input_latitude" value="{{ old('latitude', $client->latitude) }}">
                            <input type="hidden" name="longitude" id="input_longitude" value="{{ old('longitude', $client->longitude) }}">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-slate-700 font-extrabold text-xs uppercase tracking-wider">📍 Ubicación en el mapa</label>
                                    <button type="button" id="btn-use-location" class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold px-3 py-1.5 rounded-lg border border-indigo-200 transition">🎯 Usar mi ubicación (GPS)</button>
                                </div>
                                <p class="text-xs text-slate-500">Arrastra el marcador o haz clic en el mapa para fijar tu ubicación.</p>
                                <div id="checkout-map" class="w-full h-64 rounded-xl border border-slate-200 shadow-inner z-10"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center"><span class="font-extrabold">2</span></div>
                            <h3 class="font-extrabold text-slate-800 text-lg">Reporte de Pago</h3>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-slate-700 font-extrabold text-xs uppercase tracking-wider">¿A dónde transferiste?</label>
                            <select name="payment_method_id" x-model="selectedMethod" @change="updateMethodDescription()" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-semibold text-slate-700 transition-all outline-none">
                                <option value="" disabled selected>Selecciona una cuenta receptora...</option>
                                @foreach ($paymentMethods as $pm)
                                    <option value="{{ $pm->id }}" data-desc="{{ $pm->description }}">{{ $pm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="methodDescription !== ''" x-transition
                            class="bg-indigo-50 border border-indigo-100/50 rounded-xl p-4 text-xs text-indigo-900 whitespace-pre-wrap font-mono" x-text="methodDescription"></div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                            <div class="space-y-2">
                                <label class="block text-slate-700 font-extrabold text-xs uppercase tracking-wider">Número de referencia</label>
                                <input type="text" name="reference" required value="{{ old('reference') }}" maxlength="50" placeholder="Ej: 001456228"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-bold transition-all outline-none" />
                            </div>
                            <div class="space-y-2">
                                <label class="block text-slate-700 font-extrabold text-xs uppercase tracking-wider">Comprobante (imagen)</label>
                                <input type="file" name="payment_proof" accept="image/png, image/jpeg, image/jpg, image/webp" required
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:bg-white text-xs font-semibold text-slate-500 transition-all outline-none file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200" />
                            </div>
                        </div>
                        <div class="space-y-2 pt-2">
                            <label class="block text-slate-700 font-extrabold text-xs uppercase tracking-wider">Notas adicionales (opcional)</label>
                            <textarea name="notes" rows="2" placeholder="Alguna indicación sobre tu pago o pedido..."
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all outline-none">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-96 shrink-0">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 sticky top-6">
                        <h3 class="font-extrabold text-slate-800 text-lg mb-4 border-b border-slate-100 pb-3">Resumen de compra</h3>
                        <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 pr-2 mb-4 scrollbar-thin">
                            <template x-for="item in cart" :key="item.id">
                                <div class="py-3 flex justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <span x-text="item.name" class="text-sm font-bold text-slate-800 block truncate"></span>
                                        <span class="text-xs font-semibold text-slate-400"><span x-text="formatQuantity(item)"></span> x $<span x-text="formatCurrency(item.display_price)"></span></span>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span x-text="'$' + formatCurrency(calculateItemTotal(item))" class="font-extrabold text-slate-800 text-sm block"></span>
                                        @if ($exchangeRate)
                                            <span x-text="formatCurrency(calculateItemTotal(item) * {{ $safeRate }}) + ' BS'" class="text-indigo-600 font-bold text-xxs block"></span>
                                        @endif
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="bg-indigo-50/50 border border-indigo-100/50 rounded-xl p-4 space-y-3">
                            <div class="flex justify-between items-center text-sm font-semibold text-indigo-900/70">
                                <span>Cant. de ítems</span><span x-text="cart.length"></span>
                            </div>
                            <hr class="border-indigo-100/50" />
                            <div class="flex justify-between items-end">
                                <span class="text-sm font-extrabold text-slate-800 block">Total a pagar</span>
                                <div class="text-right">
                                    <span x-text="'$' + formatCurrency(calculateTotal())" class="text-2xl font-black text-slate-900 block leading-none"></span>
                                    @if ($exchangeRate)
                                        <span x-text="'≈ ' + formatCurrency(calculateTotal() * {{ $safeRate }}) + ' BS'" class="font-bold text-indigo-700 text-xs block mt-1"></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <button type="submit" :disabled="cart.length === 0 || isSubmitting"
                            class="w-full mt-6 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 disabled:from-slate-200 disabled:to-slate-200 disabled:text-slate-400 text-white font-extrabold py-3.5 px-4 rounded-xl shadow-lg transition-all duration-300 flex items-center justify-center gap-2 cursor-pointer disabled:cursor-not-allowed">
                            <span x-text="isSubmitting ? 'Procesando envío...' : 'Confirmar pago y pedido'"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function checkoutManager() {
            return {
                cart: [],
                deliveryType: 'store_pickup',
                selectedMethod: '{{ old('payment_method_id') }}',
                methodDescription: '',
                isSubmitting: false,
                init() {
                    const cached = localStorage.getItem('client_shopping_cart');
                    if (cached) { try { this.cart = JSON.parse(cached); } catch (e) { this.cart = []; } }
                    if (this.cart.length === 0) { window.location.href = "{{ route('storefront.catalog') }}"; }
                    this.$nextTick(() => this.updateMethodDescription());
                },
                updateMethodDescription() {
                    if (!this.selectedMethod) { this.methodDescription = ''; return; }
                    const select = document.querySelector('select[name="payment_method_id"]');
                    const option = select.options[select.selectedIndex];
                    this.methodDescription = option.getAttribute('data-desc') || 'No hay instrucciones adicionales para este método.';
                },
                calculateItemTotal(item) { return item.display_price * item.quantity; },
                calculateTotal() { return this.cart.reduce((sum, item) => sum + this.calculateItemTotal(item), 0); },
                formatCurrency(value) { return parseFloat(value).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },
                formatQuantity(item) {
                    if (item.unit_type === 'gram') return item.quantity.toLocaleString('es-VE', { minimumFractionDigits: 3, maximumFractionDigits: 3 }) + ' Kg';
                    return item.quantity.toLocaleString('es-VE', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' Und';
                },
                prepareCartForSubmit() { return this.cart.map(item => ({ id: item.id, quantity: item.quantity })); }
            };
        }
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let map, marker;
            const latInput = document.getElementById('input_latitude');
            const lngInput = document.getElementById('input_longitude');
            const btnLocation = document.getElementById('btn-use-location');
            const mapContainer = document.getElementById('checkout-map');
            if (!mapContainer || typeof L === 'undefined') return;

            let defaultLat = parseFloat(latInput.value) || 10.4806;
            let defaultLng = parseFloat(lngInput.value) || -66.9036;
            map = L.map('checkout-map').setView([defaultLat, defaultLng], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(map);
            marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

            function updateInputs(lat, lng) { latInput.value = lat.toFixed(7); lngInput.value = lng.toFixed(7); }
            updateInputs(defaultLat, defaultLng);
            marker.on('dragend', () => { const p = marker.getLatLng(); updateInputs(p.lat, p.lng); });
            map.on('click', (e) => { marker.setLatLng(e.latlng); updateInputs(e.latlng.lat, e.latlng.lng); });

            if (btnLocation) {
                btnLocation.addEventListener('click', function () {
                    if (!navigator.geolocation) { alert('Tu navegador no soporta geolocalización.'); return; }
                    btnLocation.textContent = '⏳ Obteniendo GPS...';
                    navigator.geolocation.getCurrentPosition(function (pos) {
                        const lat = pos.coords.latitude, lng = pos.coords.longitude;
                        map.setView([lat, lng], 16); marker.setLatLng([lat, lng]); updateInputs(lat, lng);
                        btnLocation.textContent = '✅ Ubicación fijada';
                        setTimeout(() => { btnLocation.textContent = '🎯 Usar mi ubicación (GPS)'; }, 3000);
                    }, function () {
                        alert('No se pudo obtener la ubicación GPS. Selecciónala en el mapa.');
                        btnLocation.textContent = '🎯 Usar mi ubicación (GPS)';
                    }, { enableHighAccuracy: true });
                });
            }

            document.querySelectorAll('input[name="delivery_type"]').forEach(radio => {
                radio.addEventListener('change', function () {
                    if (this.value === 'delivery') setTimeout(() => map.invalidateSize(), 200);
                });
            });
        });
    </script>
@endpush
