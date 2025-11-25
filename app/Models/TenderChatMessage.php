<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderChatMessage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'body',
        'translated_body_ru',
        'translated_body_supplier',
        'is_read_by_customer',
        'is_read_by_supplier',
    ];

    protected function casts(): array
    {
        return [
            'is_read_by_customer' => 'boolean',
            'is_read_by_supplier' => 'boolean',
        ];
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(TenderChat::class, 'chat_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
