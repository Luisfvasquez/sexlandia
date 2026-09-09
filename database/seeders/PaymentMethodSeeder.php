<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds. Idempotente: se puede re-ejecutar sin duplicar.
     */
    public function run(): void
    {
        $requiresReference = ['Zelle', 'Binance', 'Transferencia', 'Pago Móvil'];

        $paymentMethods = [
            'Efectivo',
            'Zelle',
            'Binance',
            'Tarjeta de crédito/débito',
            'Pago Móvil',
            'Transferencia',
            'Punto de Venta',
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::firstOrCreate(
                ['name' => $method],
                [
                    'is_active' => true,
                    'requires_reference' => in_array($method, $requiresReference, true),
                ]
            );
        }
    }
}
