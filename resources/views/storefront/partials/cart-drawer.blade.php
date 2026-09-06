<template x-teleport="body">
    <div x-show="cartOpen" x-cloak>
        <div class="cart-backdrop" @click="cartOpen = false" x-transition.opacity></div>

        <aside class="cart-drawer" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full" aria-label="Carrito de compra">

            <div class="cart-drawer__head">
                <h2>Mi carrito</h2>
                <button type="button" class="cart-drawer__close" @click="cartOpen = false" aria-label="Cerrar carrito">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" d="M6 6l12 12M6 18L18 6" />
                    </svg>
                </button>
            </div>

            <div class="cart-drawer__body">
                <template x-if="cart.length === 0">
                    <p style="text-align:center;padding:48px 0;color:rgba(23,21,21,.5);font-size:13px;">
                        Tu carrito está vacío.<br>Añade productos desde el catálogo.
                    </p>
                </template>

                <template x-for="item in cart" :key="item.id">
                    <div class="cart-line">
                        <div style="min-width:0;">
                            <p class="cart-line__name" x-text="item.name"></p>
                            <p class="cart-line__cat" x-text="item.category_name"></p>
                            <div class="qty-stepper" style="margin-top:8px;">
                                <button type="button" @click="changeQtyInCart(item.id, item.unit_type === 'gram' ? -0.05 : -1)">&minus;</button>
                                <input type="text" :value="formatQuantity(item)" readonly>
                                <button type="button" @click="changeQtyInCart(item.id, item.unit_type === 'gram' ? 0.05 : 1)">+</button>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <button type="button" class="cart-line__rm" @click="removeFromCart(item.id)">Quitar</button>
                            <p style="font-weight:800;margin:8px 0 0;" x-text="formatCurrency(calculateItemTotal(item))"></p>
                            <p x-show="hasRate" style="color:var(--wine);font-weight:700;font-size:11px;margin:2px 0 0;"
                                x-text="formatCurrency(calculateItemTotal(item) * safeRate, ' Bs')"></p>
                        </div>
                    </div>
                </template>
            </div>

            <div class="cart-drawer__foot">
                <div class="cart-drawer__total">
                    <span style="font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;">Total estimado</span>
                    <span x-text="formatCurrency(calculateTotal())"></span>
                </div>
                <p x-show="hasRate" style="text-align:right;color:var(--wine);font-weight:700;font-size:12px;margin:-8px 0 12px;"
                    x-text="formatCurrency(calculateTotal() * safeRate, ' Bs')"></p>
                <button type="button" class="btn-block" :disabled="cart.length === 0" @click="handleCheckout()">
                    Proceder al pedido
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
                <p style="margin:12px 0 0;text-align:center;font-size:11px;color:rgba(23,21,21,.55);">
                    o escríbenos por
                    <a href="https://wa.me/{{ config('site.contact.whatsapp') }}" target="_blank" rel="noopener"
                        style="color:#1f7a4d;font-weight:700;">WhatsApp</a>
                </p>
            </div>
        </aside>
    </div>
</template>
