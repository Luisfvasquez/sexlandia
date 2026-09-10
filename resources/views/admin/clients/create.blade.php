@extends('admin.layouts.app')

@section('title', 'Registrar Cliente')

@section('content')
    <div>
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-cream">
                Registrar Nuevo Cliente
            </h1>
            <a href="{{ route('admin.clients.index') }}"
                class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-chocolate text-white rounded-lg hover:bg-white/10 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al listado
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-rose-500/10 border-l-4 border-rose-500 p-4 rounded-lg shadow-sm">
                <ul class="list-disc list-inside text-rose-300 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Inicializamos Alpine para controlar la interacción de la cuenta web --}}
        <form action="{{ route('admin.clients.store') }}" method="POST" class="space-y-6" x-data="{ createAccount: false }">
            @csrf

            {{-- Ficha de Datos de Facturación --}}
            <div class="bg-chocolate p-6 rounded-xl shadow">
                <h2 class="text-xl font-semibold text-cream/90 mb-4 border-b pb-2">Información de Facturación</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-cream/90 mb-1">Nombre Completo / Razón Social</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full rounded-lg border-white/15 focus:border-wine focus:ring-wine"
                            oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s\-\.\,\']/g, '')"
                            title="Solo letras, espacios y puntuación básica">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-cream/90 mb-1">Cédula o RIF</label>
                        <input type="text" name="identification" value="{{ old('identification') }}" required
                            placeholder="Ej: V-12345678 o J-123456789"
                            class="w-full rounded-lg border-white/15 focus:border-wine focus:ring-wine"
                            oninput="this.value = this.value.replace(/[^a-zA-Z0-9\-]/g, '').toUpperCase()"
                            title="Solo letras, números y guiones (Ej: V-12345678)">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-cream/90 mb-1">Teléfono de Contacto</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required
                            class="w-full rounded-lg border-white/15 focus:border-wine focus:ring-wine"
                            oninput="this.value = this.value.replace(/[^0-9\+\-\s\(\)]/g, '')"
                            title="Solo números, guiones, paréntesis y el signo +"
                            placeholder="Ej: 0412-1234567">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-cream/90 mb-1">
                            Correo Electrónico <span x-show="createAccount" class="text-rose-400 font-bold"
                                style="display: none;">* Obligatorio</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" :required="createAccount"
                            class="w-full rounded-lg border-white/15 focus:border-wine focus:ring-wine">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-cream/90 mb-1">Dirección</label>
                        <textarea name="address" rows="3"
                            class="w-full rounded-lg border-white/15 focus:border-wine focus:ring-wine">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Ficha de Configuración de Acceso Digital --}}
            <div class="bg-chocolate p-6 rounded-xl shadow border-l-4 border-wine">
                <h2 class="text-xl font-semibold text-cream/90 mb-2">Acceso a Plataforma Digital</h2>
                <p class="text-sm text-cream/60 mb-4">Habilita este módulo si deseas que el cliente pueda entrar al catálogo
                    web a realizar apartados o consultar su historial.</p>

                <div class="flex items-center">
                    <input type="checkbox" name="create_account" id="create_account" value="1" x-model="createAccount"
                        class="h-4 w-4 text-blush focus:ring-wine border-white/15 rounded">
                    <label Axel for="create_account"
                        class="ml-2 block text-sm font-bold text-bone cursor-pointer select-none">
                        ¿Crear cuenta de usuario web para este cliente?
                    </label>
                </div>

                {{-- Aviso Informativo Reactivo con Alpine --}}
                <div x-show="createAccount" x-transition class="mt-4 p-4 bg-wine/10 border border-wine/20 rounded-lg"
                    style="display: none;">
                    <div class="flex">
                        <svg class="h-5 w-5 text-blush mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm text-blush">
                            <p class="font-bold">Información de Acceso Automático:</p>
                            <ul class="list-disc list-inside mt-1 ml-2 space-y-0.5">
                                <li><strong>Usuario:</strong> El correo electrónico proporcionado arriba.</li>
                                <li><strong>Contraseña Inicial:</strong> El número de Cédula/RIF que escribas (sin
                                    espacios).</li>
                                <li>El cliente podrá cambiar la contraseña en su primer inicio de sesión desde su perfil
                                    web.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" x-data="{ enviando: false }" @submit.window="enviando = true"
                    :disabled="enviando"
                    :class="enviando ? 'opacity-50 cursor-not-allowed bg-wine' : 'bg-wine hover:bg-wine-dark'"
                    class="bg-wine text-white px-8 py-3 rounded-xl font-bold hover:bg-wine-dark shadow-lg transition-all transform hover:-translate-y-1">
                    Guardar Registro de Cliente
                </button>
            </div>
        </form>
    </div>
@endsection
