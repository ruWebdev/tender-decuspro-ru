<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderItemSupplierOffer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'tender_item_id',
        'external_item_id',
        'external_parent_item_id',
        'supplier_source',
        'supplier_type',
        'supplier_external_id',
        'supplier_name',
        'availability',
        'price',
        'price_sale',
        'currency',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_sale' => 'decimal:2',
            'meta' => 'array',
        ];
    }

    public function tenderItem(): BelongsTo
    {
        return $this->belongsTo(TenderItem::class);
    }
}
