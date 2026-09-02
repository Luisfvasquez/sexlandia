<?php

namespace App\Actions\Client;

use App\Events\OrderStatusUpdated;
use App\Models\AccountReceivable;
use App\Models\OrderPayment;
use Illuminate\Support\Facades\DB;

class RegisterAbonoAction
{
    public function handle(AccountReceivable $account, array $validated)
    {
        return DB::transaction(function () use ($account, $validated) {
            $installmentNumber = $account->installments()->count() + 1;

            $account->installments()->create([
                'installment_number' => $installmentNumber,
                'amount' => $validated['amount'],
                'paid_amount' => $validated['amount'],
                'pending_amount' => 0.00,
                'due_date' => $validated['payment_date'],
                'paid_at' => $validated['payment_date'],
                'status' => 'paid',
                'notes' => $validated['notes'] ?? null,
            ]);

            $order = $account->order;
            $paymentNotes = 'Abono #'.$installmentNumber.' a cuenta por cobrar.';
            if (! empty($validated['notes'])) {
                $paymentNotes .= ' Observaciones: '.$validated['notes'];
            }

            OrderPayment::create([
                'order_id' => $order->id,
                'payment_method_id' => $validated['payment_method_id'],
                'amount' => $validated['amount'],
                'reference' => $validated['reference'] ?? null,
                'payment_date' => $validated['payment_date'],
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'notes' => $paymentNotes,
            ]);

            $account->paid_amount += $validated['amount'];
            $account->pending_amount -= $validated['amount'];

            if ($account->pending_amount <= 0) {
                $account->status = 'paid';
            } else {
                $account->status = 'partial';
            }
            $account->save();

            if ($account->status === 'paid') {
                $order->payment_status = 'paid';
                $order->verification_status = 'verified';
                $order->status = 'completed';
            } else {
                $order->payment_status = 'partial';
            }
            $order->save();

            event(new OrderStatusUpdated($order));

            return $account;
        });
    }
}
