<?php

namespace App\Services;

use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

/**
 * Pipeline único de optimización de imágenes de producto.
 *
 * De cada archivo subido genera 3 renditions WebP bajo products/{id}/:
 *   - <archivo>.webp        (full  · máx 1600px · calidad 82)  → detalle / hero
 *   - md_<archivo>.webp     (medio · máx  900px · calidad 80)  → tarjetas de catálogo
 *   - thumb_<archivo>.webp  (thumb · máx  400px · calidad 72)  → grillas / miniaturas
 *
 * Todas se corrigen por orientación EXIF y nunca se agrandan (scaleDown).
 */
class ProductImageService
{
    /** @var array<string,array{w:int,q:int,prefix:string}> */
    public const SIZES = [
        'full' => ['w' => 1600, 'q' => 82, 'prefix' => ''],
        'md' => ['w' => 900, 'q' => 80, 'prefix' => 'md_'],
        'thumb' => ['w' => 400, 'q' => 72, 'prefix' => 'thumb_'],
    ];

    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = ImageManager::usingDriver(Driver::class);
    }

    /**
     * Procesa y guarda las 3 renditions de una imagen para un producto.
     *
     * @param  UploadedFile|\SplFileInfo|string  $source  archivo subido o ruta absoluta
     * @return array{path:string,mime_type:string,size:int,width:int,height:int}
     */
    public function storeForProduct(Product $product, $source): array
    {
        $filename = uniqid('img_').'.webp';
        $dir = 'products/'.$product->id;

        $mainSize = 0;
        $width = 0;
        $height = 0;

        foreach (self::SIZES as $key => $cfg) {
            $image = $this->manager->decode($this->readable($source))->orient();
            $image->scaleDown(width: $cfg['w']);

            $binary = (string) $image->encodeUsingFormat(Format::WEBP, quality: $cfg['q']);
            Storage::disk('public')->put($dir.'/'.$cfg['prefix'].$filename, $binary);

            if ($key === 'full') {
                $mainSize = strlen($binary);
                $width = $image->width();
                $height = $image->height();
            }
        }

        return [
            'path' => $dir.'/'.$filename,
            'mime_type' => 'image/webp',
            'size' => $mainSize,
            'width' => $width,
            'height' => $height,
        ];
    }

    /**
     * Crea el registro Image (+ archivos) y lo asocia al producto.
     *
     * @param  UploadedFile|\SplFileInfo|string  $source
     */
    public function attachToProduct(Product $product, $source, int $sortOrder = 0, bool $isPrimary = false, ?string $altText = null, ?string $originalName = null): Image
    {
        $meta = $this->storeForProduct($product, $source);

        return $product->images()->create([
            'path' => $meta['path'],
            'disk' => 'public',
            'original_name' => $originalName ?? ($source instanceof UploadedFile ? $source->getClientOriginalName() : basename((string) $source)),
            'mime_type' => $meta['mime_type'],
            'size' => $meta['size'],
            'alt_text' => $altText ?: $product->name,
            'is_primary' => $isPrimary,
            'sort_order' => $sortOrder,
        ]);
    }

    /**
     * Elimina del disco las 3 renditions asociadas a un registro Image.
     */
    public function deleteForImage(Image $image): void
    {
        $disk = Storage::disk($image->disk ?? 'public');
        $dir = dirname($image->path);
        $file = basename($image->path);

        $disk->delete([
            $image->path,
            $dir.'/'.self::SIZES['md']['prefix'].$file,
            $dir.'/'.self::SIZES['thumb']['prefix'].$file,
        ]);
    }

    /**
     * Devuelve la ruta relativa de una rendition ('full'|'md'|'thumb') a partir del path principal.
     */
    public static function variantPath(string $mainPath, string $size): string
    {
        $cfg = self::SIZES[$size] ?? self::SIZES['full'];

        return dirname($mainPath).'/'.$cfg['prefix'].basename($mainPath);
    }

    /**
     * Normaliza la fuente a algo que Intervention pueda decodificar múltiples veces.
     *
     * @param  UploadedFile|\SplFileInfo|string  $source
     * @return string
     */
    private function readable($source): string
    {
        if ($source instanceof UploadedFile || $source instanceof \SplFileInfo) {
            return $source->getRealPath() ?: $source->getPathname();
        }

        return (string) $source;
    }
}
