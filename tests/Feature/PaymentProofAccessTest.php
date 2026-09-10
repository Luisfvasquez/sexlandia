<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    Storage::fake('local');

    $order = Order::create([
        'uuid' => (string) Str::uuid(),
        'order_number' => 'ORD-PROOF-001',
        'order_type' => 'delivery',
        'status' => 'pending',
        'payment_status' => 'pending',
        'client_name' => 'Cliente Prueba',
        'client_phone' => '04140000000',
        'delivery_address' => 'Calle Falsa 123',
        'total' => 50.00,
    ]);

    $proof = PaymentProof::create([
        'order_id' => $order->id,
        'reference' => 'REF-PROOF-1',
        'status' => 'pending',
    ]);

    Storage::disk('local')->put('receipts/proof-1.jpg', 'binary-image-bytes');

    $this->proofImage = $proof->images()->create([
        'path' => 'receipts/proof-1.jpg',
        'disk' => 'local',
        'original_name' => 'proof-1.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 18,
        'is_primary' => true,
    ]);
});

test('guest is redirected to login when requesting a payment proof image', function () {
    $this->get(route('admin.orders.proofImage', $this->proofImage->id))
        ->assertRedirect(route('login'));
});

test('client role cannot access payment proof images', function () {
    $client = User::factory()->create();
    $client->assignRole('client');

    $this->actingAs($client)
        ->get(route('admin.orders.proofImage', $this->proofImage->id))
        ->assertForbidden();
});

test('admin can stream a payment proof image from the private disk', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get(route('admin.orders.proofImage', $this->proofImage->id));

    $response->assertOk();
    expect($response->streamedContent())->toBe('binary-image-bytes');
});

test('the endpoint refuses to serve an image that is not a payment proof', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->seed(CategorySeeder::class);

    $product = Product::create([
        'category_id' => Category::first()->id,
        'uuid' => (string) Str::uuid(),
        'name' => 'Producto',
        'slug' => 'producto',
        'sku' => 'SKU-1',
        'sku_barcode' => '9999999999999',
        'price' => 10,
        'cost' => 5,
        'unit_type' => 'unit',
        'status' => 'active',
    ]);

    $productImage = $product->images()->create([
        'path' => 'products/1/img.jpg',
        'disk' => 'public',
        'is_primary' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.orders.proofImage', $productImage->id))
        ->assertNotFound();
});
