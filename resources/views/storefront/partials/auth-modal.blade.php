<template x-teleport="body">
    <div class="sl-modal" x-show="authPromptOpen" x-cloak x-transition.opacity @click.self="authPromptOpen = false">
        <div class="sl-modal__box" style="max-width:420px;">
            <div class="sl-modal__head">
                <h2>Identifícate para pedir</h2>
                <button type="button" class="cart-drawer__close" @click="authPromptOpen = false" aria-label="Cerrar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" d="M6 6l12 12M6 18L18 6" />
                    </svg>
                </button>
            </div>
            <div class="sl-modal__body" style="text-align:center;">
                <p style="font-size:13px;line-height:1.6;color:rgba(23,21,21,.7);margin:0 0 22px;">
                    Para finalizar tu pedido y habilitar los datos de despacho, inicia sesión o crea tu cuenta.
                    Tu carrito queda guardado en este navegador.
                </p>
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <a href="{{ route('login') }}" class="btn-block" style="text-decoration:none;">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="btn-ghost" style="text-decoration:none;text-align:center;">Crear cuenta de cliente</a>
                    <button type="button" @click="authPromptOpen = false"
                        style="border:0;background:none;color:rgba(23,21,21,.5);font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;padding-top:6px;cursor:pointer;">
                        Seguir explorando
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
