<?php

namespace App\Models;

use App\Services\CurrencyService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Product extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'uuid',
        'name',
        'slug',
        'description',
        'sku',
        'sku_barcode',
        'brand',
        'cost',
        'price',
        'unit_type',
        'track_inventory',
        'exchange_rate',
        'allow_negative_stock',
        'has_variants',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'track_inventory' => 'boolean',
            'allow_negative_stock' => 'boolean',
            'has_variants' => 'boolean',
        ];
    }

    // Atributos adicionales para mostrar en vistas
    protected $appends = [
        'display_price',
        'display_price_bs',
        'unit_label',
        'public_url',
    ];

    /**
     * URL pública (canónica) de la ficha del producto, con el dominio real del
     * sitio (config/site.php → url). Se usa para SEO y para compartir por WhatsApp.
     */
    public function getPublicUrlAttribute(): string
    {
        return rtrim(config('site.url'), '/').'/product/'.$this->slug;
    }

    /**
     * Precio "display" (USD) convertido a bolívares con la tasa activa.
     * Los precios se almacenan en USD; el monto en Bs es siempre USD * tasa.
     */
    public function getDisplayPriceBsAttribute(): float
    {
        return app(CurrencyService::class)->toBs($this->display_price);
    }

    /**
     * Costo "display" (USD) convertido a bolívares con la tasa activa.
     */
    public function getDisplayCostBsAttribute(): float
    {
        return app(CurrencyService::class)->toBs($this->display_cost);
    }

    /**
     * Retorna el precio "display" (por kilo si es pesable, por unidad si no).
     */
    public function getDisplayPriceAttribute(): float
    {
        if ($this->unit_type === 'gram') {
            return round($this->price * 1000, 2);
        }

        return (float) $this->price;
    }

    /**
     * Retorna el costo "display" (por kilo si es pesable, por unidad si no).
     */
    public function getDisplayCostAttribute(): float
    {
        if ($this->unit_type === 'gram') {
            return round($this->cost * 1000, 2);
        }

        return (float) $this->cost;
    }

    /**
     * Etiqueta de la unidad para mostrar en vistas.
     */
    public function getUnitLabelAttribute(): string
    {
        return $this->unit_type === 'gram' ? '/Kg' : '/Und';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function bulks(): HasMany
    {
        return $this->hasMany(Bulk::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
