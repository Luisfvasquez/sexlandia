<?php

namespace App\Models;

use App\Services\ProductImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Contracts\Auditable;

class Image extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'path',
        'disk',
        'original_name',
        'mime_type',
        'size',
        'alt_text',
        'is_primary',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * URL pública de una rendition ('full'|'md'|'thumb').
     * Si la rendition derivada no existe en disco, cae a la imagen principal.
     */
    public function variantUrl(string $size = 'full'): string
    {
        $disk = Storage::disk($this->disk ?? 'public');

        if ($size === 'full') {
            return $disk->url($this->path);
        }

        $variant = ProductImageService::variantPath($this->path, $size);

        try {
            if ($disk->exists($variant)) {
                return $disk->url($variant);
            }
        } catch (\Throwable $e) {
            // sin acceso al disco: usamos la principal
        }

        return $disk->url($this->path);
    }

    public function getUrlAttribute(): string
    {
        return $this->variantUrl('full');
    }

    public function getMdUrlAttribute(): string
    {
        return $this->variantUrl('md');
    }

    public function getThumbUrlAttribute(): string
    {
        return $this->variantUrl('thumb');
    }
}
