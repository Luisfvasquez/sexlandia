<?php

namespace App\Actions\Product;

use App\Models\Product;
use App\Services\ProductImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateProductAction
{
    public function __construct(private ProductImageService $images) {}

    public function handle(Product $product, array $validated, $images = null): Product
    {
        return DB::transaction(function () use ($product, $validated, $images) {
            $product->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']).'-'.uniqid(),
                'description' => $validated['description'] ?? null,
                'sku_barcode' => $validated['sku_barcode'],
                'brand' => $validated['brand'] ?? null,
                'cost' => $validated['cost'],
                'price' => $validated['price'],
                'unit_type' => $validated['unit_type'],
                'status' => $validated['status'],
            ]);

            $defaultBulk = $product->bulks()->where('is_default', true)->first();
            if ($defaultBulk) {
                $defaultBulk->update([
                    'purchase_price' => $validated['cost'],
                    'sale_price' => $validated['price'],
                    'sku' => $validated['sku'],
                    'sku_barcode' => $validated['sku_barcode'],
                ]);
            }

            $product->inventory()->update([
                'minimum_stock' => $validated['minimum_stock'],
            ]);

            if ($images) {
                $currentImagesCount = $product->images()->count();

                foreach ($images as $index => $imageFile) {
                    $this->images->attachToProduct(
                        $product,
                        $imageFile,
                        sortOrder: $currentImagesCount + $index,
                        isPrimary: $currentImagesCount === 0 && $index === 0,
                        altText: $product->name,
                    );
                }
            }

            return $product;
        });
    }
}
