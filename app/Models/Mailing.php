<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mailing extends Model
{
    use HasFactory, HasUuids;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_RUNNING = 'running';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'name',
        'emails_limit',
        'notification_template_id',
        'tender_ids',
        'company_filter',
        'status',
        'sent_count',
        'total_recipients',
    ];

    protected $casts = [
        'tender_ids' => 'array',
        'emails_limit' => 'integer',
        'sent_count' => 'integer',
        'total_recipients' => 'integer',
    ];

    public function notificationTemplate(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(MailingLog::class);
    }

    /**
     * Получить тендеры, связанные с рассылкой
     */
    public function tenders()
    {
        $ids = $this->tender_ids ?? [];

        return Tender::whereIn('id', $ids)->get();
    }

    /**
     * Проверить, можно ли запустить рассылку
     */
    public function canStart(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_PAUSED], true);
    }

    /**
     * Проверить, можно ли остановить рассылку
     */
    public function canStop(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }
}
