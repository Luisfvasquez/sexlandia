<?php

namespace App\Actions\Product;

use App\Models\BulkType;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

class CreateProductAction
{
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
                    $directory = dirname($image->path);
                    $filename = basename($image->path);
                    $thumbPath = $directory.'/thumb_'.$filename;
                    Storage::disk($image->disk ?? 'public')->delete([$image->path, $thumbPath]);
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
                $manager = ImageManager::usingDriver(Driver::class);
                foreach ($images as $index => $imageFile) {
                    $filename = uniqid('img_').'.webp';
                    $path = 'products/'.$product->id.'/'.$filename;
                    $thumbPath = 'products/'.$product->id.'/thumb_'.$filename;

                    $mainImage = $manager->decode($imageFile);
                    $encodedMain = $mainImage->encodeUsingFormat(Format::WEBP, quality: 80);
                    Storage::disk('public')->put($path, (string) $encodedMain);

                    $thumbImage = $manager->decode($imageFile)->scale(width: 300);
                    $encodedThumb = $thumbImage->encodeUsingFormat(Format::WEBP, quality: 80);
                    Storage::disk('public')->put($thumbPath, (string) $encodedThumb);

                    $product->images()->create([
                        'path' => $path,
                        'disk' => 'public',
                        'original_name' => $imageFile->getClientOriginalName(),
                        'mime_type' => 'image/webp',
                        'size' => strlen((string) $encodedMain),
                        'is_primary' => $index === 0 ? true : false,
                        'sort_order' => $index,
                    ]);
                }
            }

            return $product;
        });
    }
}
