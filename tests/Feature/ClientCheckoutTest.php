<?php

use App\Models\AccountReceivable;
use App\Models\Category;
use App\Models\ExchangeRate;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(CategorySeeder::class);

    // Disco público falso para las imágenes de comprobante.
    Storage::fake('public');

    // Método de pago habilitado para el checkout web.
    $this->paymentMethod = PaymentMethod::create([
        'name' => 'Pago Móvil',
        'is_active' => true,
        'requires_reference' => true,
        'show_in_checkout' => true,
    ]);

    ExchangeRate::create([
        'currency_from' => 'USD',
        'currency_to' => 'VES',
        'rate' => 40.0000,
        'date' => now(),
        'is_active' => true,
    ]);

    // Payload base con todos los campos que el checkout exige actualmente.
    $this->checkoutPayload = function (array $overrides = []): array {
        return array_merge([
            'delivery_type' => 'delivery',
            'delivery_address' => 'Av. Bolivar, Local 5',
            'payment_method_id' => $this->paymentMethod->id,
            'reference' => 'REF-000123',
            'payment_proof' => UploadedFile::fake()->image('comprobante.jpg'),
            'notes' => 'Llamar al llegar',
            'cart_items' => json_encode([]),
        ], $overrides);
    };
});

test('unauthenticated user cannot access checkout', function () {
    $response = $this->post(route('client.checkout'), ($this->checkoutPayload)());

    $response->assertRedirect('/login');
});

test('non-client role user cannot access checkout', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->post(route('client.checkout'), ($this->checkoutPayload)());

    $response->assertStatus(403);
});

test('client can checkout successfully with unit-based products', function () {
    $user = User::factory()->create();
    $user->assignRole('client');

    $product = Product::create([
        'category_id' => Category::first()->id,
        'uuid' => (string) Str::uuid(),
        'name' => 'Unit Product',
        'slug' => 'unit-product',
        'sku' => 'UNIT-001',
        'sku_barcode' => '1234567890123',
        'price' => 5.00,
        'cost' => 3.00,
        'unit_type' => 'unit',
        'track_inventory' => true,
        'allow_negative_stock' => false,
        'status' => 'active',
    ]);

    $inventory = Inventory::create(['product_id' => $product->id, 'stock' => 10.00]);

    $response = $this->actingAs($user)->post(route('client.checkout'), ($this->checkoutPayload)([
        'cart_items' => json_encode([['id' => $product->id, 'quantity' => 3]]),
    ]));

    $response->assertRedirect(route('client.purchases'));
    $response->assertSessionHasNoErrors();

    $order = Order::first();
    expect($order)->not->toBeNull();
    expect((float) $order->total)->toEqual(15.00);
    expect((float) $order->exchange_rate)->toEqual(40.0);
    expect($order->payment_status)->toEqual('pending');

    $detail = OrderDetail::first();
    expect($detail)->not->toBeNull();
    expect($detail->product_id)->toEqual($product->id);
    expect((float) $detail->quantity)->toEqual(3.0);
    expect((float) $detail->base_quantity)->toEqual(3.0);
    expect((float) $detail->unit_price)->toEqual(5.00);
    expect((float) $detail->unit_cost)->toEqual(3.00);
    expect((float) $detail->subtotal)->toEqual(15.00);

    $inventory->refresh();
    expect((float) $inventory->stock)->toEqual(7.00);

    $receivable = AccountReceivable::first();
    expect($receivable)->not->toBeNull();
    expect($receivable->order_id)->toEqual($order->id);
    expect((float) $receivable->total_amount)->toEqual(15.00);
    expect((float) $receivable->pending_amount)->toEqual(15.00);
    expect($receivable->due_date->toDateString())->toEqual(now()->addDays(7)->toDateString());

    $movement = InventoryMovement::first();
    expect($movement)->not->toBeNull();
    expect($movement->product_id)->toEqual($product->id);
    expect($movement->type)->toEqual('sale');
    expect((float) $movement->quantity)->toEqual(3.0);
    expect((float) $movement->previous_stock)->toEqual(10.0);
    expect((float) $movement->new_stock)->toEqual(7.0);

    // El comprobante de pago quedó registrado y almacenado.
    expect($order->paymentProofs()->count())->toEqual(1);
    expect($order->payments()->count())->toEqual(1);
});

test('client can checkout successfully with weight-based (gram) products', function () {
    $user = User::factory()->create();
    $user->assignRole('client');

    $product = Product::create([
        'category_id' => Category::first()->id,
        'uuid' => (string) Str::uuid(),
        'name' => 'Weighted Product',
        'slug' => 'weighted-product',
        'sku' => 'WGHT-001',
        'sku_barcode' => '3210987654321',
        'price' => 0.012,
        'cost' => 0.008,
        'unit_type' => 'gram',
        'track_inventory' => true,
        'allow_negative_stock' => false,
        'status' => 'active',
    ]);

    $inventory = Inventory::create(['product_id' => $product->id, 'stock' => 5000.00]);

    $response = $this->actingAs($user)->post(route('client.checkout'), ($this->checkoutPayload)([
        'delivery_address' => 'Calle Falsa 123',
        'notes' => 'Dejar en conserjeria',
        'cart_items' => json_encode([['id' => $product->id, 'quantity' => 0.250]]),
    ]));

    $response->assertRedirect(route('client.purchases'));
    $response->assertSessionHasNoErrors();

    $order = Order::first();
    expect($order)->not->toBeNull();
    // 0.250 Kg -> 250 g. 250 * 0.012 = 3.00 USD
    expect((float) $order->total)->toEqual(3.00);

    $detail = OrderDetail::first();
    expect($detail)->not->toBeNull();
    expect($detail->product_id)->toEqual($product->id);
    expect((float) $detail->quantity)->toEqual(0.250);
    expect((float) $detail->base_quantity)->toEqual(250.0);
    expect((float) $detail->unit_price)->toEqual(12.00);
    expect((float) $detail->unit_cost)->toEqual(8.00);
    expect((float) $detail->subtotal)->toEqual(3.00);

    $inventory->refresh();
    expect((float) $inventory->stock)->toEqual(4750.00);

    $movement = InventoryMovement::first();
    expect($movement)->not->toBeNull();
    expect($movement->type)->toEqual('sale');
    expect((float) $movement->quantity)->toEqual(250.0);
    expect((float) $movement->previous_stock)->toEqual(5000.0);
    expect((float) $movement->new_stock)->toEqual(4750.0);
});

test('client checkout fails and rolls back when quantity exceeds available stock', function () {
    $user = User::factory()->create();
    $user->assignRole('client');

    $product1 = Product::create([
        'category_id' => Category::first()->id,
        'uuid' => (string) Str::uuid(),
        'name' => 'Prod 1',
        'slug' => 'prod-1',
        'sku' => 'P1',
        'sku_barcode' => '1111111111111',
        'price' => 2.00,
        'cost' => 1.00,
        'unit_type' => 'unit',
        'track_inventory' => true,
        'allow_negative_stock' => false,
        'status' => 'active',
    ]);
    $inv1 = Inventory::create(['product_id' => $product1->id, 'stock' => 5.00]);

    $product2 = Product::create([
        'category_id' => Category::first()->id,
        'uuid' => (string) Str::uuid(),
        'name' => 'Prod 2',
        'slug' => 'prod-2',
        'sku' => 'P2',
        'sku_barcode' => '2222222222222',
        'price' => 0.010,
        'cost' => 0.005,
        'unit_type' => 'gram',
        'track_inventory' => true,
        'allow_negative_stock' => false,
        'status' => 'active',
    ]);
    $inv2 = Inventory::create(['product_id' => $product2->id, 'stock' => 2000.00]);

    // Producto 2: se piden 2.5 Kg (2500 g) pero solo hay 2000 g.
    $response = $this->actingAs($user)->post(route('client.checkout'), ($this->checkoutPayload)([
        'delivery_address' => 'Direccion de prueba',
        'notes' => 'Sin novedad',
        'cart_items' => json_encode([
            ['id' => $product1->id, 'quantity' => 2],
            ['id' => $product2->id, 'quantity' => 2.5],
        ]),
    ]));

    // Vuelve atrás con el error de negocio en la sesión.
    $response->assertRedirect();
    $response->assertSessionHas('errors');

    // Rollback completo: nada se persistió.
    expect(Order::count())->toEqual(0);
    expect(OrderDetail::count())->toEqual(0);
    expect(AccountReceivable::count())->toEqual(0);
    expect(InventoryMovement::count())->toEqual(0);

    expect((float) $inv1->refresh()->stock)->toEqual(5.00);
    expect((float) $inv2->refresh()->stock)->toEqual(2000.00);
});
