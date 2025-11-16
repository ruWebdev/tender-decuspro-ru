<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tender extends Model
{
    use HasFactory, HasUuids;

    /**
     * Атрибуты, доступные для массового заполнения.
     *
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'title',
        'title_en',
        'title_cn',
        'description',
        'description_en',
        'description_cn',
        'hidden_comment',
        'valid_until',
        'status',
        'is_finished',
        'finished_at',
        'winner_proposal_id',
    ];

    /**
     * Преобразования типов атрибутов.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'valid_until' => 'datetime',
            'finished_at' => 'datetime',
            'is_finished' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TenderItem::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function winnerProposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'winner_proposal_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'winner_proposal_id');
    }

    public function getTitleAttribute($value): string
    {
        $locale = app()->getLocale();

        if ($locale === 'en' && $this->title_en) {
            return $this->title_en;
        }

        if (in_array($locale, ['cn', 'zh'], true) && $this->title_cn) {
            return $this->title_cn;
        }

        return $value;
    }
}
