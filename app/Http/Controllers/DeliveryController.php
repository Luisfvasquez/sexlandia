<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    /**
     * Panel principal de repartidor (Dashboard táctil)
     */
    public function dashboard()
    {
        $user = auth()->user();

        $availableCount = Order::where('order_type', 'delivery')
            ->where('status', 'ready_for_delivery')
            ->whereNull('delivery_user_id')
            ->whereNull('delivered_at')
            ->count();

        // Entregas activas en curso por este repartidor
        $activeDeliveries = Order::with(['client', 'details.product'])
            ->where('delivery_user_id', $user->id)
            ->where('status', 'in_transit')
            ->whereNull('delivered_at')
            ->get();

        // Entregas realizadas hoy por este repartidor
        $completedTodayCount = Order::where('delivery_user_id', $user->id)
            ->whereDate('delivered_at', today())
            ->count();

        return view('delivery.dashboard', compact(
            'availableCount',
            'activeDeliveries',
            'completedTodayCount'
        ));
    }

    /**
     * Lista de paquetes disponibles para tomar
     */
    public function availableOrders(Request $request)
    {
        $type = $request->query('type', 'delivery'); // 'delivery' por defecto, u 'optionally 'store_pickup'

        $ordersQuery = Order::with(['client', 'details.product'])
            ->whereNull('delivery_user_id')
            ->whereNull('delivered_at');

        if ($type === 'store_pickup') {
            $ordersQuery->whereIn('order_type', ['store_pickup', 'store'])
                ->where('status', 'ready_for_pickup');
        } else {
            $ordersQuery->where('order_type', 'delivery')
                ->where('status', 'ready_for_delivery');
        }

        $orders = $ordersQuery->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $deliveryCount = Order::where('order_type', 'delivery')
            ->where('status', 'ready_for_delivery')
            ->whereNull('delivery_user_id')
            ->whereNull('delivered_at')
            ->count();

        $storePickupCount = Order::whereIn('order_type', ['store_pickup', 'store'])
            ->where('status', 'ready_for_pickup')
            ->whereNull('delivery_user_id')
            ->whereNull('delivered_at')
            ->count();

        return view('delivery.available', compact('orders', 'type', 'deliveryCount', 'storePickupCount'));
    }

    /**
     * Asignar/Tomar un paquete (concurrencia segura con transacción)
     */
    public function claimOrder(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            /** @var Order $order */
            $order = Order::where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            // Validar que la orden siga estando disponible
            if ($order->delivery_user_id !== null) {
                DB::rollBack();

                return back()->with('error', 'Este paquete ya ha sido asignado a otro repartidor.');
            }

            // Si es un paquete de retiro en tienda, se convierte dinámicamente a delivery
            $isConvertedFromPickup = in_array($order->order_type, ['store_pickup', 'store']);

            // Asignar al repartidor actual
            $order->update([
                'order_type' => 'delivery',
                'delivery_user_id' => auth()->id(),
                'delivery_assigned_at' => now(),
                'status' => 'in_transit',
            ]);

            DB::commit();

            event(new OrderStatusUpdated($order));

            $msg = $isConvertedFromPickup
                ? '¡Has tomado el paquete #'.$order->order_number.' (Retiro en Tienda convertido a Delivery)!'
                : '¡Has tomado el paquete #'.$order->order_number.'! Está listo en tus entregas activas.';

            return redirect()->route('delivery.active')
                ->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Ocurrió un error al tomar la entrega: '.$e->getMessage());
        }
    }

    /**
     * Ver entregas activas asignadas al repartidor
     */
    public function activeDeliveries()
    {
        $orders = Order::with(['client', 'details.product', 'details.bulk'])
            ->where('delivery_user_id', auth()->id())
            ->where('status', 'in_transit')
            ->whereNull('delivered_at')
            ->orderBy('delivery_assigned_at', 'desc')
            ->get();

        return view('delivery.active', compact('orders'));
    }

    /**
     * Detalle de un paquete específico para el repartidor
     */
    public function show($id)
    {
        $user = auth()->user();

        $order = Order::with([
            'client',
            'details.product',
            'details.bulk',
            'payments.paymentMethod',
        ])
            ->where('id', $id)
            // Un repartidor solo puede ver un paquete asignado a él o uno que
            // aún esté disponible para tomar. El admin puede ver cualquiera.
            ->when(! $user->hasRole('admin'), function ($query) use ($user) {
                $query->where(function ($scoped) use ($user) {
                    $scoped->where('delivery_user_id', $user->id)
                        ->orWhere(function ($available) {
                            $available->whereNull('delivery_user_id')
                                ->whereNull('delivered_at')
                                ->whereIn('status', ['ready_for_delivery', 'ready_for_pickup']);
                        });
                });
            })
            ->firstOrFail();

        return view('delivery.show', compact('order'));
    }

    /**
     * Marcar una entrega como completada
     */
    public function completeDelivery(Request $request, $id)
    {
        $request->validate([
            'delivery_notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::where('id', $id)
                ->where('delivery_user_id', auth()->id())
                ->firstOrFail();

            if ($order->delivered_at !== null) {
                DB::rollBack();

                return back()->with('error', 'Esta entrega ya fue registrada como completada previamente.');
            }

            $order->update([
                'delivered_at' => now(),
                'status' => 'delivered',
                'delivery_notes' => $request->input('delivery_notes'),
            ]);

            DB::commit();

            event(new OrderStatusUpdated($order));

            return redirect()->route('delivery.dashboard')
                ->with('success', '¡Entrega #'.$order->order_number.' marcada como entregada con éxito!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al completar la entrega: '.$e->getMessage());
        }
    }

    /**
     * Historial de entregas completadas por el repartidor
     */
    public function history()
    {
        $orders = Order::with(['client', 'details.product'])
            ->where('delivery_user_id', auth()->id())
            ->whereNotNull('delivered_at')
            ->orderBy('delivered_at', 'desc')
            ->paginate(15);

        return view('delivery.history', compact('orders'));
    }
}
