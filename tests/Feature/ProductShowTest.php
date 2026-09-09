<?php

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use Database\Seeders\CategorySeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Str;

use function Pest\Laravel\get;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(CategorySeeder::class);
});

function makeProduct(array $overrides = []): Product
{
    $name = $overrides['name'] ?? 'Producto '.Str::random(5);

    $product = Product::create(array_merge([
        'category_id' => Category::first()->id,
        'uuid' => (string) Str::uuid(),
        'name' => $name,
        'slug' => Str::slug($name).'-'.Str::random(4),
        'description' => 'Descripción de prueba del producto.',
        'sku' => 'SKU-'.Str::upper(Str::random(6)),
        'sku_barcode' => (string) random_int(1000000000000, 9999999999999),
        'price' => 12.50,
        'cost' => 6.00,
        'unit_type' => 'unit',
        'track_inventory' => true,
        'allow_negative_stock' => false,
        'status' => 'active',
    ], $overrides));

    Inventory::create(['product_id' => $product->id, 'stock' => 8]);

    return $product;
}

it('shows a product page to guests using the slug in the URL', function () {
    $product = makeProduct(['name' => 'Vibrador Aurora']);
    makeProduct(['name' => 'Vibrador Nova']); // similar (misma categoría)

    $response = get(route('storefront.product', $product->slug));

    $response->assertOk()
        ->assertSee('Vibrador Aurora')
        ->assertSee('Vibrador Nova')            // sugerencia de producto similar
        ->assertSee('También te puede', false)  // sección de similares
        ->assertSee('/product/'.$product->slug, false); // la URL usa slug

    // El id no debe aparecer como segmento de ruta.
    expect(route('storefront.product', $product->slug))
        ->toContain('/product/'.$product->slug)
        ->not->toContain('/product/'.$product->id);
});

it('returns 404 for an inactive product', function () {
    $product = makeProduct(['status' => 'inactive']);

    get(route('storefront.product', $product->slug))->assertNotFound();
});

it('builds the canonical public_url from config/site.php, not the request host', function () {
    config(['site.url' => 'https://sexlandiaboutique.com']);

    $product = makeProduct(['name' => 'Lubricante Base Agua']);

    expect($product->public_url)->toBe('https://sexlandiaboutique.com/product/'.$product->slug);
});

it('links catalog cards to the product page and puts the canonical url in the WhatsApp message', function () {
    config(['site.url' => 'https://sexlandiaboutique.com']);
    $product = makeProduct(['name' => 'Succionador Lía']);

    $html = get(route('storefront.catalog'))->assertOk()->getContent();

    expect($html)
        ->toContain('/product/'.$product->slug)
        ->toContain(rawurlencode('https://sexlandiaboutique.com/product/'.$product->slug));
});

it('keeps the catalog reachable without logging in', function () {
    get(route('storefront.catalog'))->assertOk();
});
