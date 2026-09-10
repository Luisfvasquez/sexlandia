<?php

namespace App\Actions\Order;

use App\Events\SaleCreated;
use App\Models\AccountReceivable;
use App\Models\Client;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProcessOrderAction
{
    public function handle(array $data): Order
    {
        $clientId = $data['client_id'] ?? 0;

        if (empty($data['client_id'])) {
            $fallbackClient = Client::where('identification', $clientId)->first();

            if (! $fallbackClient) {
                throw new \Exception('Debes seleccionar un cliente válido para procesar la venta.');
            }

            $data['client_id'] = $fallbackClient->id;
        }

        $totalOrder = collect($data['cart'])->sum('subtotal');
        $amountReceived = collect($data['payments'] ?? [])->sum(fn ($p) => (float) ($p['amount'] ?? 0));
        $amountPending = round($totalOrder - $amountReceived, 2);

        $paymentStatus = 'pending';
        if ($amountReceived >= $totalOrder) {
            $paymentStatus = 'paid';
        } elseif ($amountReceived > 0) {
            $paymentStatus = 'partial';
        }

        $verificationStatus = $paymentStatus === 'paid' ? 'verified' : 'pending';

        return DB::transaction(function () use ($data, $totalOrder, $amountReceived, $amountPending, $paymentStatus, $verificationStatus) {
            $orderNumber = 'ORD-'.date('Ym').'-'.str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'uuid' => Str::uuid(),
                'client_id' => $data['client_id'],
                'verified_by' => auth()->id() ?? 1,
                'order_number' => $orderNumber,
                'order_type' => 'store',
                'payment_status' => $paymentStatus,
                'verification_status' => $verificationStatus,
                'status' => 'completed',
                'subtotal' => $totalOrder,
                'exchange_rate' => $data['exchange_rate'],
                'total' => $totalOrder,
                'notes' => 'Tasa de cambio: Bs. '.$data['exchange_rate'],
            ]);

            foreach ($data['cart'] as $item) {
                $baseQty = $item['quantity'] * $item['conversion_factor'];
                $product = Product::with('inventory')->find($item['product_id']);

                if (! $item['allow_negative'] && $product->inventory->stock < $baseQty) {
                    throw new \Exception("Stock insuficiente: {$item['name']}");
                }

                $order->details()->create([
                    'product_id' => $item['product_id'],
                    'bulk_id' => $item['bulk_id'],
                    'quantity' => $item['quantity'],
                    'base_quantity' => $baseQty,
                    'unit_price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $previousStock = $product->inventory->stock;
                $product->inventory()->decrement('stock', $baseQty);

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'reference_type' => get_class($order),
                    'reference_id' => $order->id,
                    'quantity' => $baseQty,
                    'previous_stock' => $previousStock,
                    'new_stock' => $previousStock - $baseQty,
                    'created_by' => auth()->id() ?? 1,
                ]);
            }

            if (! empty($data['payments'])) {
                foreach ($data['payments'] as $payment) {
                    if ((float) $payment['amount'] > 0) {
                        OrderPayment::create([
                            'order_id' => $order->id,
                            'payment_method_id' => $payment['payment_method_id'],
                            'amount' => $payment['amount'],
                            'reference' => $payment['reference'] ?? null,
                            'payment_date' => now(),
                            'status' => 'verified',
                            'verified_by' => auth()->id() ?? 1,
                        ]);
                    }
                }
            }

            if ($amountPending > 0) {
                AccountReceivable::create([
                    'order_id' => $order->id,
                    'client_id' => $data['client_id'],
                    'total_amount' => $totalOrder,
                    'paid_amount' => $amountReceived,
                    'pending_amount' => $amountPending,
                    'status' => $paymentStatus === 'partial' ? 'partial' : 'pending',
                    'due_date' => now()->addDays(15),
                ]);
            }

            event(new SaleCreated($order));

            return $order;
        });
    }
}
