@extends('admin.layouts.app')

@section('title', 'Punto de Venta')

@section('content')
    <div x-data="posSystem()" class="grid grid-cols-1 lg:grid-cols-12 gap-6 relative">

        {{-- Mensajes de Sistema --}}
        <div x-show="alert.show" x-transition
            :class="alert.type === 'success' ? 'bg-emerald-500/15 border-emerald-500 text-emerald-300' :
                'bg-rose-500/15 border-rose-500 text-rose-300'"
            class="lg:col-span-12 border-l-4 p-4 rounded shadow-sm font-bold text-lg" style="display: none;">
            <span x-text="alert.message"></span>
        </div>

        {{-- LADO IZQUIERDO: BUSCADOR Y CARRITO (8 Columnas) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Buscador Unificado --}}
            <div class="bg-chocolate p-6 rounded-xl shadow-sm border-t-4 border-wine relative">
                <label class="block text-sm font-bold text-cream/90 mb-2">Escanea el Código de Barras o Escribe el
                    Nombre</label>
                <div class="relative">
                    <input type="text" x-model="searchQuery" @keydown.enter.prevent="searchProduct" autofocus
                        class="w-full rounded-lg border-white/15 focus:ring-wine text-lg shadow-sm pl-4 py-3"
                        placeholder="Dispara la lectora aquí o busca...">
                </div>

                {{-- Resultados desplegables --}}
                <div x-show="searchResults.length > 0" @click.away="searchResults = []" style="display: none;"
                    class="absolute z-50 w-full mt-1 bg-chocolate rounded-md shadow-2xl border border-white/10 max-h-60 overflow-y-auto left-0">
                    <ul class="py-1">
                        <template x-for="prod in searchResults" :key="prod.id">
                            <template x-for="bulk in prod.bulks" :key="bulk.id">
                                <li @click="addToCart(prod, bulk)"
                                    class="cursor-pointer hover:bg-wine/10 px-4 py-3 border-b border-white/10 flex justify-between items-center">
                                    <div>
                                        <span class="font-bold text-cream text-lg" x-text="prod.name"></span>
                                        <span class="text-sm text-blush font-bold ml-2"
                                            x-text="`(${bulk.name})`"></span>
                                        <div class="text-xs text-cream/60">Stock: <span
                                                x-text="prod.inventory?.stock || 0"></span> Und base</div>
                                    </div>
                                    <span class="font-black text-emerald-400 text-lg">$ <span
                                            x-text="parseFloat(bulk.sale_price).toFixed(2)"></span>
                                        <span class="block text-xs font-bold text-cream/50 text-right"
                                            x-text="'Bs. ' + (parseFloat(bulk.sale_price) * exchangeRate).toFixed(2)"></span>
                                    </span>
                                </li>
                            </template>
                        </template>
                    </ul>
                </div>
            </div>

            {{-- Carrito de Compras --}}
            <div x-ref="cartContainer"
                class="bg-chocolate rounded-xl shadow-sm overflow-y-auto max-h-[400px] min-h-[300px] relative">
                <table class="min-w-full divide-y divide-white/10">
                    <thead class="bg-chocolate text-white sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Producto</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider">Cant.</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider">Subtotal</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-chocolate divide-y divide-white/10">
                        <template x-for="(item, index) in cart" :key="index">
                            <tr class="hover:bg-white/5" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform translate-y-4"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-95">

                                <td class="px-4 py-3">
                                    <div class="font-bold text-bone" x-text="item.name"></div>
                                    <div class="text-xs text-cream/60 font-bold">
                                        <span x-text="item.presentation"></span> (A $ <span
                                            x-text="parseFloat(item.price).toFixed(2)"></span>)
                                    </div>
                                    <template
                                        x-if="!item.allow_negative && (item.current_stock < (item.quantity * item.conversion_factor))">
                                        <span
                                            class="text-[10px] text-white bg-rose-500 px-2 py-0.5 rounded font-bold mt-1 inline-block">Sin
                                            Stock Físico</span>
                                    </template>
                                </td>
                                <td class="px-4 py-3 w-32">
                                    <input type="number" :step="item.unit_type === 'gram' ? '0.01' : '1'"
                                        :min="item.unit_type === 'gram' ? '0.01' : '1'" x-model="item.quantity"
                                        @input="updateTotals"
                                        class="w-full text-center rounded-lg border-white/15 font-bold text-lg"
                                        inputmode="decimal"
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')"
                                        title="Solo números positivos">
                                </td>
                                <td class="px-4 py-3 text-right font-black text-bone text-lg">
                                    $ <span x-text="item.subtotal.toFixed(2)"></span>
                                    <span class="block text-xs font-bold text-cream/50"
                                        x-text="'Bs. ' + (item.subtotal * exchangeRate).toFixed(2)"></span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button @click="removeItem(index)"
                                        class="text-rose-400 hover:text-rose-300 font-black text-xl px-2 transition-colors focus:outline-none">×</button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="cart.length === 0">
                            <td colspan="4" class="px-4 py-16 text-center text-cream/50 font-medium">
                                Escanea un código de barras para añadir productos a la factura.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- LADO DERECHO: CLIENTE Y PAGOS (4 Columnas) --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- Caja Cliente --}}
            <div class="bg-chocolate p-5 rounded-xl shadow-sm border-t-4 border-wine">
                <label class="block text-xs font-bold text-cream/60 uppercase mb-2">Cédula o RIF del Cliente</label>
                <div class="flex space-x-2">
                    <input type="text" x-model="clientIdentification" @keydown.enter.prevent="searchClient"
                        class="w-full rounded-lg border-white/15 focus:ring-wine font-mono text-center font-bold"
                        placeholder="V-12345678"
                        oninput="this.value = this.value.replace(/[^a-zA-Z0-9\-]/g, '').toUpperCase()"
                        title="Solo letras, números y guiones (Ej: V-12345678)">
                    <button @click.prevent="searchClient"
                        class="bg-wine/15 text-blush px-3 rounded-lg hover:bg-wine/20 font-bold">OK</button>
                </div>

                {{-- Muestra si el cliente existe --}}
                <div x-show="clientId"
                    class="mt-3 p-2 bg-emerald-500/10 text-emerald-300 rounded-lg text-sm font-bold flex border border-emerald-500/25"
                    style="display: none;">
                    👤 <span x-text="clientName" class="ml-2"></span>
                </div>

                {{-- Muestra botón de crear si no existe --}}
                <div x-show="!clientId && clientIdentification && clientSearched" class="mt-3" style="display: none;">
                    <p class="text-xs text-rose-400 mb-2 font-bold">Cliente no encontrado.</p>
                    <button @click.prevent="openClientModal"
                        class="w-full bg-wine/15 text-blush py-2 rounded-lg hover:bg-wine/20 font-bold text-sm">
                        + Registrar rápidamente (<span x-text="clientIdentification"></span>)
                    </button>
                </div>
            </div>

            {{-- Modal: registro rápido de cliente --}}
            <template x-teleport="body">
                <div x-show="showClientModal" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;"
                    x-transition.opacity>
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div @click="showClientModal = false" class="fixed inset-0 bg-ink/70"></div>

                        <div class="bg-chocolate rounded-2xl p-6 max-w-sm w-full shadow-2xl relative z-10 border border-white/10"
                            @keydown.enter.prevent="confirmQuickCreateClient"
                            @keydown.escape.window="showClientModal = false">
                            <h2 class="text-lg font-black text-bone mb-1">Registrar cliente</h2>
                            <p class="text-xs text-cream/60 mb-4 font-mono">
                                <span x-text="clientIdentification"></span>
                            </p>

                            <label class="block text-xs font-bold text-cream/60 uppercase mb-2">Nombre del cliente</label>
                            <input type="text" x-model="newClientName" x-ref="newClientNameInput"
                                class="w-full rounded-lg border-white/15 focus:ring-wine font-bold"
                                placeholder="Consumidor Final"
                                maxlength="120">
                            <p class="text-[11px] text-cream/50 mt-2">
                                Si lo dejas vacío se guardará como <span class="font-bold text-cream/70">Consumidor Final</span>.
                            </p>

                            <div class="flex gap-2 mt-5">
                                <button @click.prevent="showClientModal = false"
                                    class="flex-1 bg-white/10 text-cream/90 py-2 rounded-lg hover:bg-white/15 font-bold text-sm">
                                    Cancelar
                                </button>
                                <button @click.prevent="confirmQuickCreateClient" :disabled="isSubmittingClient"
                                    :class="isSubmittingClient ? 'opacity-60 cursor-not-allowed' : 'hover:bg-wine-dark'"
                                    class="flex-1 bg-wine text-white py-2 rounded-lg font-bold text-sm transition-colors">
                                    <span x-text="isSubmittingClient ? 'Guardando...' : 'Guardar'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Caja Pagos (Múltiples) --}}
            <div class="bg-chocolate p-5 rounded-xl shadow-sm border-t-4 border-emerald-500">
                <div class="flex justify-between items-center mb-3">
                    <div class="flex items-center space-x-2">
                        <h3 class="text-sm font-bold text-cream/90 uppercase">Pagos Recibidos</h3>
                        <label class="inline-flex items-center text-xs font-bold ml-3">
                            <input type="checkbox" class="form-checkbox h-4 w-4" x-model="isCreditSale"
                                @change="toggleCreditSale">
                            <span class="ml-2">Vender a Fiado</span>
                        </label>
                    </div>
                    <div class="flex items-center">
                        <button x-show="!isCreditSale" @click.prevent="addPaymentLine"
                            class="text-xs bg-white/10 px-2 py-1 rounded hover:bg-white/15 font-bold">+ Dividir</button>
                        <span x-show="isCreditSale" class="text-xs text-amber-300 font-bold ml-2">Fiado activo</span>
                    </div>
                </div>

                <div class="space-y-3" x-show="!isCreditSale" style="display: none;">
                    <template x-for="(payment, index) in payments" :key="index">
                        <div class="bg-white/5 p-3 rounded-lg border relative">
                            <button x-show="payments.length > 1" @click.prevent="removePaymentLine(index)"
                                class="absolute -top-2 -right-2 bg-rose-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs shadow font-bold">X</button>

                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <select x-model="payment.payment_method_id"
                                    class="w-full text-xs rounded border-white/15 font-bold text-cream/90">
                                    <template x-for="pm in availablePaymentMethods" :key="pm.id">
                                        <option :value="pm.id" x-text="pm.name"></option>
                                    </template>
                                </select>
                                <input type="number" step="0.01" min="0" x-model="payment.amount"
                                    @input="onPaymentInput(index)" placeholder="Monto USD"
                                    class="w-full text-sm rounded border-white/15 text-right font-bold text-emerald-300"
                                    inputmode="decimal"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')"
                                    title="Solo números positivos">
                            </div>
                            <input type="text" x-model="payment.reference"
                                placeholder="Ref. Bancaria (Solo si aplica)"
                                class="w-full text-xs rounded border-white/15 py-1.5"
                                oninput="this.value = this.value.replace(/[^a-zA-Z0-9\-\s]/g, '').toUpperCase()"
                                title="Solo letras, números, guiones y espacios">
                        </div>
                    </template>
                </div>
            </div>

            {{-- Pantalla de Totales --}}
            <div class="bg-ink text-white p-6 rounded-xl shadow-xl">
                <div class="space-y-2">
                    <div class="flex justify-between items-center text-cream/50">
                        <span class="text-sm">Total Orden:</span>
                        <span class="text-right">
                            <span class="font-black text-xl">$ <span x-text="totalOrder.toFixed(2)"></span></span>
                            <span class="block text-xs text-cream/60"
                                x-text="'Bs. ' + (totalOrder * exchangeRate).toFixed(2)"></span>
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-cream/50">
                        <span class="text-sm">Pagado en Caja:</span>
                        <span class="font-bold text-emerald-400 text-lg">$ <span
                                x-text="amountReceived.toFixed(2)"></span></span>
                    </div>

                    {{-- Fiado / Vuelto --}}
                    <div x-show="amountPending > 0"
                        class="flex justify-between items-center bg-rose-500/20 p-2 rounded border border-rose-500 mt-2"
                        style="display: none;">
                        <span class="text-rose-300 text-xs font-bold uppercase">Fiado (Deuda)</span>
                        <span class="font-black text-rose-400">$ <span x-text="amountPending.toFixed(2)"></span></span>
                    </div>
                    <div x-show="amountPending < 0"
                        class="flex justify-between items-center bg-wine/20 p-2 rounded border border-wine mt-2"
                        style="display: none;">
                        <span class="text-blush text-xs font-bold uppercase">Cambio / Vuelto</span>
                        <span class="font-black text-blush">$ <span
                                x-text="Math.abs(amountPending).toFixed(2)"></span></span>
                    </div>

                    <div class="border-t border-white/15 mt-3 pt-3 text-right">
                        <span class="text-xs text-cream/60">Tasa Dólar: Bs. {{ $exchangeRate ?? 1 }}</span>
                    </div>
                </div>

                <button @click.prevent="processOrder" :disabled="isSubmitting || cart.length === 0"
                    :class="isSubmitting || cart.length === 0 ? 'bg-white/10 text-cream/50 cursor-not-allowed' :
                        'bg-emerald-500 text-bone hover:bg-emerald-500 shadow-[0_0_15px_rgba(34,197,94,0.4)]'"
                    class="w-full mt-4 font-black py-4 rounded-xl transition-all uppercase text-lg">
                    <span x-text="isSubmitting ? 'Facturando...' : 'Generar Venta'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- SCRIPTS ALPINE JS --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posSystem', () => ({
                isSubmitting: false,
                alert: {
                    show: false,
                    type: '',
                    message: ''
                },

                // Configuración
                exchangeRate: {{ $exchangeRate ? $exchangeRate : 1 }},
                availablePaymentMethods: @json($paymentMethods),

                // Cliente
                clientIdentification: '',
                clientId: null,
                clientName: '',
                clientSearched: false,
                showClientModal: false,
                newClientName: '',
                isSubmittingClient: false,

                // Modalidad de venta
                isCreditSale: false,

                // Buscador Productos
                searchQuery: '',
                searchResults: [],

                // Carrito
                cart: [],
                payments: [],
                totalOrder: 0,
                amountReceived: 0,
                amountPending: 0,

                init() {
                    if (!this.isCreditSale && this.availablePaymentMethods.length > 0) {
                        this.addPaymentLine();
                    }
                },

                // ====== 1. BÚSQUEDA DE PRODUCTOS (AJAX) ======
                async searchProduct() {
                    if (!this.searchQuery) return;

                    try {
                        let response = await fetch(
                            `{{ route('admin.pos.products.search') }}?q=${this.searchQuery}`);
                        let result = await response.json();

                        if (result.exact) {
                            // Coincidencia exacta (Ej: Lector de Código de barras)
                            let prod = result.data;
                            let defaultBulk = prod.bulks.find(b => b.is_default) || prod.bulks[0];
                            this.addToCart(prod, defaultBulk);
                        } else {
                            // Búsqueda por nombre
                            this.searchResults = result.data;
                        }
                    } catch (error) {
                        console.error('Error buscando producto:', error);
                    }
                },

                addToCart(prod, bulk) {
                    let existingItem = this.cart.find(item => item.product_id === prod.id && item
                        .bulk_id === bulk.id);

                    if (existingItem) {
                        // Forzar a Number antes de incrementar (x-model devuelve strings)
                        existingItem.quantity = Number(existingItem.quantity) + 1;
                    } else {
                        this.cart.push({
                            product_id: prod.id,
                            bulk_id: bulk.id,
                            name: prod.name,
                            presentation: bulk.name,
                            price: bulk.sale_price,
                            quantity: 1,
                            conversion_factor: bulk.quantity,
                            unit_type: prod.unit_type, // 'unit' o 'gram'
                            allow_negative: prod.allow_negative_stock,
                            current_stock: prod.inventory ? prod.inventory.stock : 0,
                            subtotal: 0
                        });
                    }
                    // Dentro de la función donde haces el this.cart.push(...) o this.cart.unshift(...)
                    this.$nextTick(() => {
                        let container = this.$refs.cartContainer;
                        if (container) {
                            container.scrollTo({
                                top: container.scrollHeight,
                                behavior: 'smooth'
                            });
                        }
                    });
                    this.searchQuery = '';
                    this.searchResults = [];
                    this.updateTotals();
                },

                removeItem(index) {
                    this.cart.splice(index, 1);
                    this.updateTotals();
                },

                // ====== 2. CLIENTES (AJAX) ======
                async searchClient() {
                    this.clientSearched = true;
                    if (!this.clientIdentification) {
                        this.clientId = null;
                        return;
                    }

                    try {
                        let response = await fetch(
                            `{{ route('admin.pos.clients.search') }}?q=${this.clientIdentification}`
                        );
                        let result = await response.json();

                        if (result.client) {
                            this.clientId = result.client.id;
                            this.clientName = result.client.name || 'Cliente Registrado';
                        } else {
                            this.clientId = null; // Muestra el botón de crear rápido
                        }
                    } catch (error) {
                        console.error(error);
                    }
                },

                openClientModal() {
                    this.newClientName = '';
                    this.showClientModal = true;
                    this.$nextTick(() => this.$refs.newClientNameInput?.focus());
                },

                async confirmQuickCreateClient() {
                    if (this.isSubmittingClient) return;
                    this.isSubmittingClient = true;

                    try {
                        let response = await fetch(`{{ route('admin.pos.clients.store') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                identification: this.clientIdentification,
                                // Vacío → el backend lo guarda como "Consumidor Final"
                                name: this.newClientName.trim() || null
                            })
                        });

                        let result = await response.json();
                        if (result.success) {
                            this.clientId = result.client.id;
                            this.clientName = result.client.name;
                            this.showClientModal = false;
                        } else {
                            alert(result.message || 'No se pudo registrar el cliente.');
                        }
                    } catch (error) {
                        alert('Error al crear el cliente.');
                    } finally {
                        this.isSubmittingClient = false;
                    }
                },

                // ====== 3. PAGOS Y TOTALES ======

                // Elige el siguiente método de pago disponible diferente al anterior
                _nextPaymentMethodId(currentId) {
                    let others = this.availablePaymentMethods.filter(pm => pm.id !== currentId);
                    return others.length > 0 ? others[0].id : (this.availablePaymentMethods[0]?.id ||
                        '');
                },

                addPaymentLine() {
                    if (this.isCreditSale) return;
                    // Calcular lo que ya está cubierto por las líneas actuales
                    let alreadyAssigned = this.payments.reduce((sum, p) => sum + (parseFloat(p
                        .amount) || 0), 0);
                    let remaining = Math.max(0, this.totalOrder - alreadyAssigned);

                    // El nuevo método será distinto al último usado
                    let lastMethodId = this.payments.length > 0 ?
                        this.payments[this.payments.length - 1].payment_method_id :
                        null;

                    this.payments.push({
                        payment_method_id: this._nextPaymentMethodId(lastMethodId),
                        amount: remaining > 0 ? parseFloat(remaining.toFixed(2)) : '',
                        reference: ''
                    });

                    this.updateTotals();
                },

                removePaymentLine(index) {
                    this.payments.splice(index, 1);
                    this.redistributeRemainder();
                    this.updateTotals();
                },

                toggleCreditSale() {
                    if (this.isCreditSale) {
                        // activar fiado: limpiar pagos y recalcular
                        this.payments = [];
                        this.amountReceived = 0;
                        this.amountPending = parseFloat((this.totalOrder - this.amountReceived).toFixed(
                            2));
                    } else {
                        // desactivar fiado: asegurar al menos una línea de pago
                        if (this.payments.length === 0 && this.availablePaymentMethods.length > 0) {
                            this.addPaymentLine();
                        }
                        this.updateTotals();
                    }
                },

                // Asigna el monto restante automáticamente al último método de pago
                redistributeRemainder() {
                    if (this.payments.length === 0) return;

                    // Sumar todo menos la última línea
                    let sumExceptLast = this.payments
                        .slice(0, -1)
                        .reduce((sum, p) => sum + (parseFloat(p.amount) || 0), 0);

                    let remainder = parseFloat((this.totalOrder - sumExceptLast).toFixed(2));
                    this.payments[this.payments.length - 1].amount = remainder > 0 ? remainder : 0;
                },

                // Cuando el usuario edita manualmente el monto de un pago
                onPaymentInput(index) {
                    let isLast = index === this.payments.length - 1;

                    if (!isLast) {
                        // Si editó una línea que NO es la última → redistribuir restante al último
                        this.redistributeRemainder();
                    }
                    // Si editó la última línea, no sobreescribimos lo que escribió

                    // Recalcular totales finales
                    this.amountReceived = this.payments.reduce((sum, p) => sum + (parseFloat(p
                        .amount) || 0), 0);
                    this.amountPending = parseFloat((this.totalOrder - this.amountReceived).toFixed(2));
                },

                updateTotals() {
                    // 1. Recalcular subtotales del carrito
                    this.cart.forEach(item => {
                        item.quantity = Number(item.quantity) || 0;
                        item.subtotal = item.quantity * parseFloat(item.price);
                    });

                    this.totalOrder = this.cart.reduce((sum, item) => sum + item.subtotal, 0);

                    // 2. Si hay pagos, redistribuir el restante al último método
                    if (this.payments.length > 0) {
                        this.redistributeRemainder();
                    }

                    // 3. Recalcular totales de pago
                    this.amountReceived = this.payments.reduce((sum, p) => sum + (parseFloat(p
                        .amount) || 0), 0);
                    this.amountPending = parseFloat((this.totalOrder - this.amountReceived).toFixed(2));
                },

                // ====== 4. PROCESAR VENTA (AJAX FINAL) ======
                async processOrder() {
                    this.isSubmitting = true;

                    let payload = {
                        client_id: this.clientId,
                        cart: this.cart,
                        payments: this.payments,
                        exchange_rate: this.exchangeRate
                    };

                    try {
                        let response = await fetch(`{{ route('admin.orders.store') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        });

                        let result = await response.json();

                        if (result.success) {
                            this.alert = {
                                show: true,
                                type: 'success',
                                message: result.message
                            };
                            // Resetear para próxima venta
                            this.cart = [];
                            this.payments = [];
                            this.addPaymentLine();
                            this.clientIdentification = '';
                            this.clientId = null;
                            this.clientSearched = false;
                            this.updateTotals();

                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                        } else {
                            this.alert = {
                                show: true,
                                type: 'error',
                                message: result.message || 'Error en validación.'
                            };
                        }
                    } catch (error) {
                        this.alert = {
                            show: true,
                            type: 'error',
                            message: 'Error de conexión con el servidor.'
                        };
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }));
        });
    </script>
@endsection
