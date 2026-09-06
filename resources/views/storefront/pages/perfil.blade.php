@extends('storefront.layout')

@section('title', 'Mi perfil · ' . config('site.brand.name'))

@section('content')
    @include('storefront.partials.account-nav', ['active' => 'profile'])

    <section class="account section-pad">
        <div class="account__intro reveal">
            <p class="eyebrow">TU CUENTA</p>
            <h1>Mi <em>perfil.</em></h1>
            <p class="account__lead">Mantén al día tus datos de facturación, dirección de despacho y clave de acceso.</p>
        </div>

        <div class="max-w-3xl mx-auto space-y-8">
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-3 shadow-sm">
                    <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex flex-col gap-1 shadow-sm">
                    @foreach ($errors->all() as $error)
                        <span class="font-semibold text-sm">{{ $error }}</span>
                    @endforeach
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="bg-gradient-to-r from-slate-900 to-indigo-950 px-8 py-8 text-white border-b border-slate-800 flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center border border-white/20 text-2xl font-black shrink-0">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                    <div>
                        <h3 class="text-xl font-extrabold tracking-tight">{{ $user->name }}</h3>
                        <p class="text-slate-400 text-xs mt-1">Identificación: <span class="font-bold text-slate-300">{{ $client->identification }}</span></p>
                    </div>
                </div>

                <form action="{{ route('client.profile.update') }}" method="POST" class="p-8 space-y-8">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-6">
                        <h4 class="text-slate-800 font-extrabold text-sm uppercase tracking-wider border-b border-slate-50 pb-2">Datos personales y de facturación</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-slate-700 font-extrabold text-xs">Nombre completo o razón social</label>
                                <input type="text" name="name" required value="{{ old('name', $user->name) }}" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all outline-none" />
                            </div>
                            <div class="space-y-2">
                                <label class="block text-slate-700 font-extrabold text-xs">Cédula de identidad / RIF</label>
                                <input type="text" name="identification" required value="{{ old('identification', $client->identification) }}" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all outline-none" />
                            </div>
                            <div class="space-y-2">
                                <label class="block text-slate-700 font-extrabold text-xs">Correo electrónico</label>
                                <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all outline-none" />
                            </div>
                            <div class="space-y-2">
                                <label class="block text-slate-700 font-extrabold text-xs">Teléfono de contacto</label>
                                <input type="tel" name="phone" value="{{ old('phone', $client->phone) }}" placeholder="Ej: 04121234567" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all outline-none" />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-slate-700 font-extrabold text-xs">Dirección de despacho predeterminada</label>
                            <textarea name="address" rows="3" placeholder="Tu dirección de envío habitual…" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all outline-none">{{ old('address', $client->address) }}</textarea>
                        </div>
                    </div>

                    <div class="space-y-6 pt-4 border-t border-slate-100">
                        <h4 class="text-slate-800 font-extrabold text-sm uppercase tracking-wider border-b border-slate-50 pb-2">Seguridad de la cuenta (opcional)</h4>
                        <div class="bg-amber-50 border border-amber-100/50 rounded-2xl p-4 text-xs text-amber-900">
                            Completa estos campos solo si deseas cambiar tu contraseña. De lo contrario, déjalos en blanco.
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-slate-700 font-extrabold text-xs">Nueva contraseña</label>
                                <input type="password" name="password" placeholder="Mínimo 8 caracteres" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all outline-none" />
                            </div>
                            <div class="space-y-2">
                                <label class="block text-slate-700 font-extrabold text-xs">Confirmar nueva contraseña</label>
                                <input type="password" name="password_confirmation" placeholder="Repite la contraseña" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all outline-none" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-6 border-t border-slate-100">
                        <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold py-3.5 px-8 rounded-xl shadow-lg transition-all duration-300 cursor-pointer">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
