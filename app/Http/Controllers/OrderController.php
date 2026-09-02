<?php

namespace App\Http\Controllers;

use App\Actions\Order\ProcessOrderAction;
use App\Events\OrderStatusUpdated;
use App\Events\SaleRejected;
use App\Http\Requests\ApproveOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UploadProofRequest;
use App\Models\AccountReceivable;
use App\Models\Bulk;
use App\Models\Client;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\PaymentMethod;
use App\Models\PaymentProof;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.orders.index');
    }

    public function create()
    {
        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('admin.orders.create', compact('paymentMethods'));
    }

    public function show(Order $order)
    {
        $order->load([
            'client',
            'verifiedBy',
            'deliveryUser',
            'payments.paymentMethod',
            'paymentProofs.images',
        ]);

        $details = $order->details()
            ->with(['product', 'bulk'])
            ->paginate(4);

        $paymentMethods = PaymentMethod::where('is_active', true)
            ->where('show_in_checkout', true)
            ->get();

        return view('admin.orders.show', compact('order', 'details', 'paymentMethods'));
    }

    public function uploadProof(UploadProofRequest $request, Order $order)
    {
        try {
            DB::beginTransaction();

            $file = $request->file('payment_proof');
            $path = $file->store('receipts', 'public');

            $proof = PaymentProof::create([
                'order_id' => $order->id,
                'uploaded_by' => auth()->id(),
                'reference' => $request->reference,
                'status' => 'pending',
                'notes' => 'Comprobante reportado manualmente por administración (ej: Vía WhatsApp).',
            ]);

            $proof->images()->create([
                'path' => $path,
                'disk' => 'public',
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'is_primary' => true,
            ]);

            OrderPayment::create([
                'order_id' => $order->id,
                'payment_method_id' => $request->payment_method_id,
                'amount' => $request->amount,
                'reference' => $request->reference,
                'payment_date' => now(),
                'status' => 'pending',
                'notes' => 'Cargado manualmente por administración.',
            ]);

            DB::commit();

            return back()->with('success', '¡Comprobante adjuntado con éxito! Ahora puedes verificar la información y Aprobar la orden.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Error al subir el comprobante: '.$e->getMessage()]);
        }
    }

    public function approve(ApproveOrderRequest $request, Order $order)
    {
        if ($order->status === 'completed') {
            return back()->withErrors(['error' => 'La orden ya fue procesada y completada anteriormente.']);
        }

        try {
            DB::beginTransaction();

            $updateData = [
                'status' => $request->status,
                'verification_status' => 'verified',
                'payment_status' => 'paid',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ];

            if ($request->status === 'completed' && is_null($order->delivered_at)) {
                $updateData['delivered_at'] = now();
            }

            $order->update($updateData);

            $order->paymentProofs()->update([
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            $order->payments()->update([
                'status' => 'verified',
                'verified_by' => auth()->id(),
            ]);

            $account = AccountReceivable::where('order_id', $order->id)->first();
            if ($account && $account->status !== 'paid') {
                $account->update([
                    'paid_amount' => $account->total_amount,
                    'pending_amount' => 0.00,
                    'status' => 'paid',
                    'notes' => trim($account->notes.' | Liquidada automáticamente al aprobar pago.'),
                ]);
            }

            DB::commit();

            event(new OrderStatusUpdated($order));

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', 'El pago ha sido verificado y el estado de la orden actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Error al procesar la orden: '.$e->getMessage()]);
        }
    }

    public function reject(Request $request, Order $order)
    {
        $order->load('details.product.inventory');

        if ($order->payment_status !== 'pending') {
            return back()->withErrors(['error' => 'Solo se pueden rechazar órdenes en estado pendiente.']);
        }

        try {
            DB::beginTransaction();

            foreach ($order->details as $detail) {
                $product = $detail->product;

                if ($product && $product->track_inventory && $product->inventory) {
                    $previousStock = $product->inventory->stock;
                    $qtyToReturn = $detail->base_quantity;

                    $product->inventory->increment('stock', $qtyToReturn);

                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'type' => 'return',
                        'reference_type' => get_class($order),
                        'reference_id' => $order->id,
                        'quantity' => $qtyToReturn,
                        'previous_stock' => $previousStock,
                        'new_stock' => $previousStock + $qtyToReturn,
                        'notes' => 'Reintegro por orden rechazada: '.$order->order_number,
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'rejected',
                'verification_status' => 'rejected',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'notes' => $order->notes.' | Rechazada el '.now()->format('d/m/Y').' por: '.($request->notes ?? 'Sin justificación'),
            ]);

            $order->paymentProofs()->update([
                'status' => 'rejected',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            $order->payments()->update([
                'status' => 'rejected',
                'verified_by' => auth()->id(),
            ]);

            $account = AccountReceivable::where('order_id', $order->id)->first();
            if ($account) {
                $account->update([
                    'status' => 'cancelled',
                    'pending_amount' => 0.00,
                    'notes' => trim($account->notes.' | Anulada por rechazo de la orden.'),
                ]);
            }

            DB::commit();

            event(new SaleRejected($order));

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', 'Orden rechazada, deuda anulada y stock reintegrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Error al rechazar la orden: '.$e->getMessage()]);
        }
    }

    // ==========================================
    // API ENDPOINTS PARA ALPINE.JS
    // ==========================================
    public function searchProduct(Request $request)
    {
        $query = $request->get('q');
        if (! $query) {
            return response()->json([]);
        }

        // Buscar primero coincidencia exacta por código de barras en Producto o Bulto
        $exactProduct = Product::with(['inventory', 'bulks'])->where('sku_barcode', $query)->where('status', 'active')->first();
        if ($exactProduct) {
            return response()->json(['exact' => true, 'data' => $exactProduct]);
        }

        $exactBulk = Bulk::with('product.inventory')->where('sku_barcode', $query)->first();
        if ($exactBulk && $exactBulk->product->status === 'active') {
            // Transformamos para que el frontend lo lea igual
            $bulkProduct = clone $exactBulk->product;
            $bulkProduct->bulks = collect([$exactBulk]);

            return response()->json(['exact' => true, 'data' => $bulkProduct]);
        }

        // Si no es código de barras, buscar por nombre
        $products = Product::with(['inventory', 'bulks'])
            ->where('name', 'like', "%{$query}%")
            ->where('status', 'active')
            ->take(10)
            ->get();

        return response()->json(['exact' => false, 'data' => $products]);
    }

    public function searchClient(Request $request)
    {
        $query = $request->get('q');
        $client = Client::where('identification', $query)->first();

        return response()->json(['client' => $client]); // Retorna null si no existe
    }

    public function storeClient(Request $request)
    {
        $request->validate(['identification' => ['required', 'string', 'regex:/^[a-zA-Z0-9\-]+$/', 'unique:clients,identification']]);

        $client = Client::create([
            'uuid' => Str::uuid(),
            'identification' => $request->identification,
            'name' => 'Consumidor Final', // Nombre por defecto
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'client' => $client]);
    }

    // ==========================================
    // PROCESAMIENTO DE LA VENTA
    // ==========================================
    public function store(StoreOrderRequest $request, ProcessOrderAction $processOrder)
    {
        try {
            $order = $processOrder->handle($request->validated());

            return response()->json([
                'success' => true,
                'message' => '¡Venta Procesada! Orden: '.$order->order_number,
                'redirect' => route('admin.orders.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function deliver(Request $request, Order $order)
    {
        if ($order->status !== 'completed' || $order->delivered_at !== null) {
            return back()->withErrors(['error' => 'La orden no está lista para entrega o ya fue entregada.']);
        }

        try {
            DB::beginTransaction();

            $order->update([
                'delivered_at' => now(),
            ]);

            DB::commit();

            event(new OrderStatusUpdated($order));

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', 'La orden ha sido marcada como entregada al cliente exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Error al procesar la entrega: '.$e->getMessage()]);
        }
    }
}
