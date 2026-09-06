@php
    $safeRate = $exchangeRate ? (float) str_replace(',', '.', $exchangeRate) : 1;
    if ($safeRate <= 0) {
        $safeRate = 1;
    }
@endphp
<script>
    function storefrontCart() {
        return {
            cartOpen: false,
            checkoutOpen: false,
            authPromptOpen: false,
            cart: [],
            safeRate: {{ $safeRate }},
            hasRate: @js((bool) $exchangeRate),
            isAuthenticated: @js(auth()->check()),
            userRole: @js(auth()->check() ? (auth()->user()->hasRole('client') ? 'client' : (auth()->user()->hasRole('admin') ? 'admin' : 'other')) : null),

            init() {
                const cached = localStorage.getItem('client_shopping_cart');
                if (cached) {
                    try { this.cart = JSON.parse(cached); } catch (e) { this.cart = []; }
                }
                window.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') { this.cartOpen = false; this.checkoutOpen = false; this.authPromptOpen = false; }
                });
            },

            persistCart() {
                localStorage.setItem('client_shopping_cart', JSON.stringify(this.cart));
            },

            addToCart(product, qty) {
                const parsedQty = parseFloat(qty);
                if (isNaN(parsedQty) || parsedQty <= 0) return;

                const existing = this.cart.find(item => item.id === product.id);

                if (product.track_inventory && !product.allow_negative_stock) {
                    const availableStock = product.inventory ? parseFloat(product.inventory.stock) : 0;
                    const availableQtyCommercial = product.unit_type === 'gram' ? (availableStock / 1000) : availableStock;
                    const currentInCart = existing ? existing.quantity : 0;
                    if (currentInCart + parsedQty > availableQtyCommercial) {
                        alert("El producto '" + product.name + "' no cuenta con inventario suficiente. Disponible: " +
                            availableQtyCommercial.toLocaleString('es-VE') + " " + (product.unit_type === 'gram' ? 'Kgs' : 'Unds'));
                        return;
                    }
                }

                if (existing) {
                    existing.quantity = parseFloat((existing.quantity + parsedQty).toFixed(3));
                } else {
                    this.cart.push({
                        id: product.id,
                        name: product.name,
                        category_name: product.category ? product.category.name : 'Varios',
                        unit_type: product.unit_type,
                        unit_label: product.unit_label,
                        display_price: parseFloat(product.display_price),
                        price: parseFloat(product.price),
                        quantity: parsedQty,
                        track_inventory: product.track_inventory,
                        allow_negative_stock: product.allow_negative_stock,
                        inventory_stock: product.inventory ? parseFloat(product.inventory.stock) : 0
                    });
                }

                this.persistCart();
                this.cartOpen = true;
            },

            removeFromCart(id) {
                this.cart = this.cart.filter(item => item.id !== id);
                this.persistCart();
            },

            changeQtyInCart(id, diff) {
                const item = this.cart.find(item => item.id === id);
                if (!item) return;
                let newQty = parseFloat((item.quantity + parseFloat(diff)).toFixed(3));
                if (newQty <= 0) {
                    this.removeFromCart(id);
                } else {
                    if (item.track_inventory && !item.allow_negative_stock) {
                        const availableQtyCommercial = item.unit_type === 'gram' ? (item.inventory_stock / 1000) : item.inventory_stock;
                        if (newQty > availableQtyCommercial) {
                            alert("No puedes exceder el stock disponible: " + availableQtyCommercial.toLocaleString('es-VE') + " " + (item.unit_type === 'gram' ? 'Kgs' : 'Unds'));
                            return;
                        }
                    }
                    item.quantity = newQty;
                    this.persistCart();
                }
            },

            totalItemsCount() { return this.cart.length; },
            calculateItemTotal(item) { return item.display_price * item.quantity; },
            calculateTotal() { return this.cart.reduce((sum, item) => sum + this.calculateItemTotal(item), 0); },

            formatCurrency(value, suffix = ' $') {
                return value.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + suffix;
            },
            formatQuantity(item) {
                if (item.unit_type === 'gram') {
                    return item.quantity.toLocaleString('es-VE', { minimumFractionDigits: 3, maximumFractionDigits: 3 }) + ' Kg';
                }
                return item.quantity.toLocaleString('es-VE', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' Und';
            },
            prepareCartForSubmit() {
                return this.cart.map(item => ({ id: item.id, quantity: item.quantity }));
            },

            handleCheckout() {
                if (this.cart.length === 0) return;
                if (!this.isAuthenticated) {
                    this.authPromptOpen = true;
                } else if (this.userRole === 'client') {
                    window.location.href = @js(route('client.checkout.view'));
                } else {
                    alert('Tu cuenta tiene rol administrativo. Para realizar pedidos, inicia sesión con una cuenta de cliente.');
                }
            }
        };
    }
</script>
