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
    config(['site.url' => 'https://sexlandiaboutique.com']);
});

function makeSeoProduct(array $overrides = []): Product
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
        'price' => 19.90,
        'cost' => 8.00,
        'unit_type' => 'unit',
        'track_inventory' => true,
        'allow_negative_stock' => false,
        'status' => 'active',
    ], $overrides));

    Inventory::create(['product_id' => $product->id, 'stock' => 5]);

    return $product;
}

it('renders core SEO head tags on the homepage', function () {
    $html = get(route('storefront'))->assertOk()->getContent();

    expect($html)
        ->toContain('<title>')
        ->toContain('<meta name="description"')
        ->toContain('rel="canonical" href="https://sexlandiaboutique.com/"')
        ->toContain('name="robots" content="index,follow')
        ->toContain('property="og:type" content="website"')
        ->toContain('property="og:image"')
        ->toContain('hreflang="x-default"')
        ->toContain('name="geo.region" content="VE-M"')
        ->toContain('name="geo.placename"');
});

it('emits Organization, Store and WebSite JSON-LD on every storefront page', function (string $route) {
    $html = get(route($route))->assertOk()->getContent();

    expect($html)
        ->toContain('"@type":"Organization"')
        ->toContain('"@type":"Store"')
        ->toContain('"@type":"WebSite"')
        ->toContain('"@id":"https://sexlandiaboutique.com/#organization"')
        ->toContain('"SearchAction"');
})->with(['storefront', 'nosotros', 'contacto', 'storefront.catalog']);

it('only emits FAQ structured data where the FAQ block is visible', function () {
    $product = makeSeoProduct();

    $home = get(route('storefront'))->getContent();
    $productPage = get(route('storefront.product', $product->slug))->getContent();

    expect($home)->toContain('"@type":"FAQPage"');
    expect($productPage)->not->toContain('"@type":"FAQPage"');
});

it('points the product page canonical at the public domain and adds Product + Breadcrumb JSON-LD', function () {
    $product = makeSeoProduct(['name' => 'Vibrador Aurora']);

    $html = get(route('storefront.product', $product->slug))->assertOk()->getContent();

    expect($html)
        ->toContain('rel="canonical" href="https://sexlandiaboutique.com/product/'.$product->slug.'"')
        ->toContain('property="og:type" content="product"')
        ->toContain('"@type":"Product"')
        ->toContain('"@type":"Offer"')
        ->toContain('"priceCurrency":"USD"')
        ->toContain('"@type":"BreadcrumbList"');
});

it('keeps the base catalog indexable but blocks internal search results', function () {
    $indexable = get(route('storefront.catalog'))->assertOk()->getContent();
    expect($indexable)->toContain('name="robots" content="index,follow');

    $search = get(route('storefront.catalog', ['search' => 'vibrador']))->assertOk()->getContent();
    expect($search)
        ->toContain('name="robots" content="noindex,follow"')
        ->toContain('rel="canonical" href="https://sexlandiaboutique.com/catalogo"');
});

it('gives each category a localized title and self-referential canonical', function () {
    $category = Category::first();
    $category->update(['is_active' => true]);
    makeSeoProduct(['category_id' => $category->id]);

    $html = get(route('storefront.catalog', ['category' => $category->id]))->assertOk()->getContent();

    expect($html)
        ->toContain('rel="canonical" href="https://sexlandiaboutique.com/catalogo?category='.$category->id.'"')
        ->toContain($category->name.' en Caracas')
        ->toContain('"@type":"CollectionPage"');
});

it('serves a dynamic robots.txt with an absolute sitemap URL', function () {
    $response = get('/robots.txt')->assertOk();

    expect($response->headers->get('content-type'))->toContain('text/plain');
    expect($response->getContent())
        ->toContain('Sitemap: https://sexlandiaboutique.com/sitemap.xml')
        ->toContain('Disallow: /admin')
        ->toContain('Disallow: /*?*search=');
});

it('builds sitemap.xml on the public domain with product, category and image entries', function () {
    $category = Category::first();
    $product = makeSeoProduct(['category_id' => $category->id, 'name' => 'Succionador Lía']);

    $xml = get('/sitemap.xml')->assertOk()
        ->assertHeader('content-type', 'application/xml; charset=UTF-8')
        ->getContent();

    expect($xml)
        ->toContain('<loc>https://sexlandiaboutique.com/</loc>')
        ->toContain('<loc>https://sexlandiaboutique.com/product/'.$product->slug.'</loc>')
        ->toContain('<loc>https://sexlandiaboutique.com/catalogo?category='.$category->id.'</loc>')
        ->toContain('<lastmod>')
        ->toContain('xmlns:image=');

    // XML válido y bien formado.
    expect(simplexml_load_string($xml))->not->toBeFalse();
});

it('serves storefront CSS from the compiled Vite bundle, not the Tailwind Play CDN', function () {
    $html = get(route('storefront'))->assertOk()->getContent();

    expect($html)
        ->not->toContain('cdn.tailwindcss.com')
        ->not->toContain('tailwind.config =')
        ->toContain('/build/assets/storefront-');
});
