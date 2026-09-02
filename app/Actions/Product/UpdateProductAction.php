<?php

namespace App\Actions\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

class UpdateProductAction
{
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
                $manager = ImageManager::usingDriver(Driver::class);
                $currentImagesCount = $product->images()->count();

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
                        'is_primary' => ($currentImagesCount == 0 && $index === 0) ? true : false,
                        'sort_order' => $currentImagesCount + $index,
                    ]);
                }
            }

            return $product;
        });
    }
}
