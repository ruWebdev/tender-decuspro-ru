<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderQuestion extends Model
{
    protected $fillable = [
        'tender_id',
        'user_id',
        'question',
        'answer',
        'is_public',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
