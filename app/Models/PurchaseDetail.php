<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class PurchaseDetail extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'bulk_id',
        'quantity',
        'base_quantity',
        'unit_cost',
        'subtotal',
        'previous_cost',
        'new_cost',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'base_quantity' => 'decimal:2',
            'unit_cost' => 'decimal:4',
            'subtotal' => 'decimal:2',
            'previous_cost' => 'decimal:4',
            'new_cost' => 'decimal:4',
        ];
    }

    /**
     * Costo unitario (almacenado en USD) convertido a bolívares con la tasa
     * congelada en la compra.
     */
    public function getUnitCostBsAttribute()
    {
        $rate = $this->purchase->exchange_rate;

        if ($rate && $rate > 0) {
            return round($this->unit_cost * $rate, 2);
        }

        return 0;
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function bulk(): BelongsTo
    {
        return $this->belongsTo(Bulk::class);
    }
}
