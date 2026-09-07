<template x-teleport="body">
    <div class="fixed inset-0 z-[160] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
        x-show="authPromptOpen" x-cloak x-transition.opacity @click.self="authPromptOpen = false">
        <div class="w-full max-w-[420px] bg-neutral-950 border border-white/10 rounded-2xl overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-6 py-5 border-b border-white/10">
                <h2 class="text-sm font-bold tracking-[0.15em] uppercase text-white">Identifícate para pedir</h2>
                <button type="button" class="text-neutral-400 hover:text-white transition-colors" @click="authPromptOpen = false" aria-label="Cerrar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" d="M6 6l12 12M6 18L18 6" />
                    </svg>
                </button>
            </div>
            <div class="px-6 py-6 text-center">
                <p class="text-sm leading-relaxed text-neutral-400 font-light mb-6">
                    Para finalizar tu pedido y habilitar los datos de despacho, inicia sesión o crea tu cuenta.
                    Tu carrito queda guardado en este navegador.
                </p>
                <div class="flex flex-col gap-3">
                    <a href="{{ route('login') }}"
                        class="flex items-center justify-center gap-3 bg-white text-black font-medium tracking-[0.15em] text-[11px] uppercase py-4 hover:bg-rose-600 hover:text-white transition-all duration-300">
                        Iniciar sesión
                    </a>
                    <a href="{{ route('register') }}"
                        class="flex items-center justify-center border border-white/20 text-neutral-300 font-medium tracking-[0.15em] text-[11px] uppercase py-4 hover:border-white/40 hover:text-white transition-colors">
                        Crear cuenta de cliente
                    </a>
                    <button type="button" @click="authPromptOpen = false"
                        class="pt-2 text-[10px] font-bold tracking-[0.14em] uppercase text-neutral-500 hover:text-neutral-300 transition-colors">
                        Seguir explorando
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
