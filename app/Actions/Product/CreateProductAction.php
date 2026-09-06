<?php

namespace App\Actions\Product;

use App\Models\BulkType;
use App\Models\Product;
use App\Services\ProductImageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateProductAction
{
    public function __construct(private ProductImageService $images) {}

    public function handle(array $validated, $images = null, $presentations = null): Product
    {
        return DB::transaction(function () use ($validated, $images, $presentations) {
            $trashedProduct = Product::onlyTrashed()
                ->where(function ($query) use ($validated) {
                    $query->where('sku_barcode', $validated['sku_barcode'])
                        ->orWhere('sku', $validated['sku']);
                })
                ->first();

            if ($trashedProduct) {
                $trashedProduct->restore();
                $trashedProduct->update([
                    'category_id' => $validated['category_id'],
                    'name' => $validated['name'],
                    'slug' => Str::slug($validated['name']).'-'.uniqid(),
                    'description' => $validated['description'] ?? null,
                    'sku' => $validated['sku'],
                    'sku_barcode' => $validated['sku_barcode'],
                    'brand' => $validated['brand'] ?? null,
                    'cost' => $validated['cost'],
                    'price' => $validated['price'],
                    'unit_type' => $validated['unit_type'],
                    'status' => 'active',
                    'created_by' => Auth::user()->id ?? 1,
                ]);

                $product = $trashedProduct;
                $product->bulks()->delete();

                foreach ($product->images as $image) {
                    $this->images->deleteForImage($image);
                    $image->delete();
                }

                $productDir = 'products/'.$product->id;
                if (Storage::disk('public')->exists($productDir)) {
                    Storage::disk('public')->deleteDirectory($productDir);
                }

                $product->inventory()->delete();
            } else {
                $product = Product::create([
                    'category_id' => $validated['category_id'],
                    'uuid' => Str::uuid(),
                    'name' => $validated['name'],
                    'slug' => Str::slug($validated['name']).'-'.uniqid(),
                    'description' => $validated['description'] ?? null,
                    'sku' => $validated['sku'],
                    'sku_barcode' => $validated['sku_barcode'],
                    'brand' => $validated['brand'] ?? null,
                    'cost' => $validated['cost'],
                    'price' => $validated['price'],
                    'unit_type' => $validated['unit_type'],
                    'created_by' => Auth::user()->id ?? 1,
                ]);
            }

            $product->bulks()->create([
                'bulk_type_id' => 1,
                'name' => $validated['name'],
                'quantity' => 1,
                'purchase_price' => $validated['cost'],
                'sale_price' => $validated['price'],
                'sku' => $validated['sku'],
                'sku_barcode' => $validated['sku_barcode'],
                'is_default' => true,
                'is_active' => true,
            ]);

            if ($presentations) {
                foreach ($presentations as $presentation) {
                    $bulkType = BulkType::find($presentation['bulk_type_id']);
                    $prefix = $bulkType ? $bulkType->name : 'Bulto';
                    $finalName = $prefix.' '.trim($presentation['name']);

                    $product->bulks()->create([
                        'bulk_type_id' => $presentation['bulk_type_id'],
                        'name' => $finalName,
                        'quantity' => $presentation['quantity'],
                        'purchase_price' => $presentation['purchase_price'],
                        'sale_price' => $presentation['sale_price'],
                        'sku' => $presentation['sku'],
                        'sku_barcode' => $presentation['sku_barcode'],
                        'is_default' => false,
                        'is_active' => true,
                    ]);
                }
            }

            $product->inventory()->create([
                'stock' => 0,
                'reserved_stock' => 0,
                'minimum_stock' => $validated['minimum_stock'] ?? 0,
                'maximum_stock' => null,
            ]);

            if ($images) {
                foreach ($images as $index => $imageFile) {
                    $this->images->attachToProduct(
                        $product,
                        $imageFile,
                        sortOrder: $index,
                        isPrimary: $index === 0,
                        altText: $product->name,
                    );
                }
            }

            return $product;
        });
    }
}
