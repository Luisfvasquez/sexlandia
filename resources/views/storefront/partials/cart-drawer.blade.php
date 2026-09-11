<template x-teleport="body">
    <div x-show="cartOpen" x-cloak class="fixed inset-0 z-[150]">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="cartOpen = false" x-transition.opacity></div>

        <aside class="absolute inset-y-0 right-0 w-full max-w-[420px] bg-ink border-l border-white/10 flex flex-col shadow-2xl"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full" aria-label="Carrito de compra">

            <div class="flex items-center justify-between px-6 py-5 border-b border-white/10">
                <h2 class="text-sm font-bold tracking-[0.2em] uppercase text-white">Mi carrito</h2>
                <button type="button" class="text-neutral-400 hover:text-white transition-colors" @click="cartOpen = false" aria-label="Cerrar carrito">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" d="M6 6l12 12M6 18L18 6" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-4 scrollbar-thin">
                <template x-if="cart.length === 0">
                    <p class="text-center py-12 text-neutral-500 text-sm font-light">
                        Tu carrito está vacío.<br>Añade productos desde el catálogo.
                    </p>
                </template>

                <template x-for="item in cart" :key="item.id">
                    <div class="flex justify-between gap-4 py-4 border-b border-white/10">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-white truncate" x-text="item.name"></p>
                            <p class="text-[10px] tracking-[0.12em] uppercase text-neutral-500 mt-0.5" x-text="item.category_name"></p>
                            <div class="flex items-center border border-white/20 bg-black/40 text-white w-24 mt-3 h-9 transition-colors hover:border-white/40">
                                <button type="button" class="px-2 w-8 h-full text-neutral-400 hover:text-rose-500 transition-colors" @click="changeQtyInCart(item.id, item.unit_type === 'gram' ? -0.05 : -1)">&minus;</button>
                                <input type="text" class="w-full text-center bg-transparent border-none text-xs p-0 focus:ring-0 text-white font-mono" :value="formatQuantity(item)" readonly>
                                <button type="button" class="px-2 w-8 h-full text-neutral-400 hover:text-rose-500 transition-colors" @click="changeQtyInCart(item.id, item.unit_type === 'gram' ? 0.05 : 1)">+</button>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <button type="button" class="text-[10px] tracking-[0.1em] uppercase text-rose-500 hover:text-rose-400 transition-colors" @click="removeFromCart(item.id)">Quitar</button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="border-t border-white/10 bg-black/30 px-6 py-5">
                <p class="text-center text-[11px] text-neutral-500 mb-3">
                    Consulta disponibilidad y precio de tu pedido directamente por WhatsApp.
                </p>
                <button type="button"
                    class="w-full flex items-center justify-center gap-3 bg-green-600 text-white font-medium tracking-[0.15em] text-[11px] uppercase py-4 hover:bg-green-500 transition-all duration-300 disabled:bg-neutral-800 disabled:text-neutral-600 disabled:cursor-not-allowed"
                    :disabled="cart.length === 0" @click="handleCheckout()">
                    Consultar por WhatsApp
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 00-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1012 2zm0 2a8 8 0 11-4.2 14.8l-.3-.2-2.8.8.8-2.7-.2-.3A8 8 0 0112 4zm4.5 9.9c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1l-.7.9c-.1.2-.3.2-.5.1a6.5 6.5 0 01-1.9-1.2 7.2 7.2 0 01-1.3-1.7c-.1-.2 0-.4.1-.5l.4-.5.2-.4v-.4l-.8-1.8c-.2-.4-.4-.4-.5-.4h-.5a1 1 0 00-.7.3c-.3.3-1 .9-1 2.2s1 2.6 1.1 2.8c.2.2 2 3.1 4.9 4.3.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.4-.6 1.6-1.2.2-.6.2-1 .1-1.2z"/></svg>
                </button>
            </div>
        </aside>
    </div>
</template>
