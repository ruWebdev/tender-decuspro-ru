<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory, HasUuids;

    /**
     * Атрибуты, доступные для массового заполнения.
     *
     * @var list<string>
     */
    protected $fillable = [
        'supplier_profile_id',
        'path',
        'mime',
        'size',
    ];

    /**
     * Преобразования типов атрибутов.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function supplierProfile(): BelongsTo
    {
        return $this->belongsTo(SupplierProfile::class);
    }
}
