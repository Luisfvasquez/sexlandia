<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeliveryModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    public function test_guest_cannot_access_delivery_dashboard(): void
    {
        $response = $this->get(route('delivery.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_delivery_user_can_access_delivery_dashboard(): void
    {
        /** @var User $deliveryUser */
        $deliveryUser = User::factory()->create();
        $deliveryUser->assignRole('delivery');

        $response = $this->actingAs($deliveryUser)->get(route('delivery.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Panel Delivery');
    }

    public function test_delivery_user_can_claim_and_complete_delivery(): void
    {
        /** @var User $deliveryUser */
        $deliveryUser = User::factory()->create();
        $deliveryUser->assignRole('delivery');

        // Crear una orden para delivery en estado ready_for_delivery
        $order = Order::create([
            'uuid' => (string) Str::uuid(),
            'order_number' => 'ORD-TEST-001',
            'order_type' => 'delivery',
            'status' => 'ready_for_delivery',
            'payment_status' => 'paid',
            'client_name' => 'Juan Pérez',
            'client_phone' => '123456789',
            'delivery_address' => 'Calle Falsa 123',
            'total' => 150.00,
        ]);

        // 1. Ver lista de disponibles
        $response = $this->actingAs($deliveryUser)->get(route('delivery.available'));
        $response->assertStatus(200);
        $response->assertSee('ORD-TEST-001');

        // 2. Tomar paquete (claim)
        $response = $this->actingAs($deliveryUser)->post(route('delivery.claim', $order->id));
        $response->assertRedirect(route('delivery.active'));

        $order->refresh();
        $this->assertEquals($deliveryUser->id, $order->delivery_user_id);
        $this->assertEquals('in_transit', $order->status);
        $this->assertNotNull($order->delivery_assigned_at);

        // 3. Marcar como entregado (complete)
        $response = $this->actingAs($deliveryUser)->post(route('delivery.complete', $order->id), [
            'delivery_notes' => 'Entregado a cliente en persona.',
        ]);
        $response->assertRedirect(route('delivery.dashboard'));

        $order->refresh();
        $this->assertEquals('delivered', $order->status);
        $this->assertNotNull($order->delivered_at);
        $this->assertEquals('Entregado a cliente en persona.', $order->delivery_notes);
    }

    public function test_delivery_user_can_filter_and_convert_store_pickup_order(): void
    {
        /** @var User $deliveryUser */
        $deliveryUser = User::factory()->create();
        $deliveryUser->assignRole('delivery');

        // Orden de retiro en tienda
        $pickupOrder = Order::create([
            'uuid' => (string) Str::uuid(),
            'order_number' => 'ORD-PICKUP-001',
            'order_type' => 'store_pickup',
            'status' => 'ready_for_pickup',
            'payment_status' => 'paid',
            'client_name' => 'María Gómez',
            'client_phone' => '987654321',
            'delivery_address' => 'Retiro en Tienda',
            'latitude' => 10.4806,
            'longitude' => -66.9036,
            'total' => 200.00,
        ]);

        // 1. Por defecto, en tipo=delivery no debe aparecer la orden de retiro en tienda
        $response = $this->actingAs($deliveryUser)->get(route('delivery.available'));
        $response->assertStatus(200);
        $response->assertDontSee('ORD-PICKUP-001');

        // 2. Al cambiar el filtro a type=store_pickup debe aparecer
        $response = $this->actingAs($deliveryUser)->get(route('delivery.available', ['type' => 'store_pickup']));
        $response->assertStatus(200);
        $response->assertSee('ORD-PICKUP-001');

        // 3. Al reclamarla, se convierte a delivery y pasa a in_transit
        $response = $this->actingAs($deliveryUser)->post(route('delivery.claim', $pickupOrder->id));
        $response->assertRedirect(route('delivery.active'));

        $pickupOrder->refresh();
        $this->assertEquals('delivery', $pickupOrder->order_type);
        $this->assertEquals($deliveryUser->id, $pickupOrder->delivery_user_id);
        $this->assertEquals('in_transit', $pickupOrder->status);
    }
}
