<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailingLog extends Model
{
    use HasFactory, HasUuids;

    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'mailing_id',
        'platform_supplier_id',
        'email',
        'status',
        'error_message',
    ];

    public function mailing(): BelongsTo
    {
        return $this->belongsTo(Mailing::class);
    }

    public function platformSupplier(): BelongsTo
    {
        return $this->belongsTo(PlatformSupplier::class);
    }
}
