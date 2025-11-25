<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenderChat extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'tender_id',
        'customer_id',
        'supplier_id',
        'translate_to_ru',
    ];

    protected function casts(): array
    {
        return [
            'translate_to_ru' => 'boolean',
        ];
    }

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TenderChatMessage::class, 'chat_id');
    }
}
