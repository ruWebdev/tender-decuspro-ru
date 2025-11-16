<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenderItem extends Model
{
    use HasFactory, HasUuids;

    /**
     * Атрибуты, доступные для массового заполнения.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tender_id',
        'title',
        'name_en',
        'name_cn',
        'quantity',
        'unit',
        'meta',
        'position_index',
    ];

    /**
     * Преобразования типов атрибутов.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'meta' => 'array',
            'position_index' => 'integer',
        ];
    }

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function proposalItems(): HasMany
    {
        return $this->hasMany(ProposalItem::class);
    }

    public function getTitleAttribute($value): string
    {
        $locale = app()->getLocale();

        if ($locale === 'en' && $this->name_en) {
            return $this->name_en;
        }

        if (in_array($locale, ['cn', 'zh'], true) && $this->name_cn) {
            return $this->name_cn;
        }

        return $value;
    }
}
