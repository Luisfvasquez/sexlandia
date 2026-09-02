<?php

namespace App\Http\Controllers;

use App\Actions\Client\RegisterAbonoAction;
use App\Actions\Client\RegisterClientAction;
use App\Events\ClientUpdated;
use App\Http\Requests\RegisterAbonoRequest;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\AccountReceivable;
use App\Models\Client;
use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        // Cargamos la relación user y deudas para comprobar deudas rápidamente desde el listado
        $clients = Client::with(['user', 'accountsReceivable'])
            ->paginate(10);

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(StoreClientRequest $request, RegisterClientAction $registerClient)
    {
        try {
            $client = $registerClient->handle(
                $request->validated(),
                $request->boolean('create_account')
            );

            return redirect()->route('admin.clients.index')
                ->with('success', 'Cliente registrado correctamente'.($client->user_id ? ' junto con su cuenta de acceso web.' : '.'));

        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'Hubo un error al registrar el cliente: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $client->load([
            'orders.details.product',
            'orders.details.bulk',
            'orders.payments.paymentMethod',
            'accountsReceivable.installments',
        ]);

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('admin.clients.show', compact('client', 'paymentMethods'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        $validated = $request->validated();

        $client->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'email' => $validated['email'] ?? $client->email,
            'is_active' => $request->boolean('is_active', $client->is_active),
        ]);

        event(new ClientUpdated($client));

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Registrar un abono a una cuenta por cobrar de un cliente.
     */
    public function registerAbono(RegisterAbonoRequest $request, Client $client, RegisterAbonoAction $registerAbono)
    {
        $account = AccountReceivable::where('client_id', $client->id)
            ->findOrFail($request->validated('account_receivable_id'));

        try {
            $registerAbono->handle($account, $request->validated());

            return redirect()->route('admin.clients.show', $client->id)
                ->with('success', 'Abono registrado correctamente de '.number_format($request->validated('amount'), 2, ',', '.').'.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'Hubo un error al registrar el abono: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Actualizar el estado de verificación de un pedido.
     */
    public function updateOrderVerification(Request $request, string $orderId)
    {
        $validated = $request->validate([
            'verification_status' => 'required|in:verified',
        ]);

        $order = Order::findOrFail($orderId);

        if ($order->verification_status !== 'pending') {
            return redirect()->back()->with('error', 'El estado de verificación del pedido ya no se encuentra pendiente.');
        }

        $order->verification_status = 'verified';
        $order->verified_at = now();
        $order->verified_by = auth()->id();
        $order->save();

        return redirect()->back()->with('success', 'Estado de verificación del pedido '.$order->order_number.' actualizado a Verificado.');
    }
}
