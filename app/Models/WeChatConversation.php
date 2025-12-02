<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Модель диалога с пользователем WeChat
 */
class WeChatConversation extends Model
{
    use HasUuids;

    protected $table = 'wechat_conversations';

    protected $fillable = [
        'platform_supplier_id',
        'user_id',
        'wechat_openid',
        'wechat_unionid',
        'nickname',
        'avatar_url',
        'remark',
        'is_subscribed',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_subscribed' => 'boolean',
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    /**
     * Связь с поставщиком из справочника
     */
    public function platformSupplier(): BelongsTo
    {
        return $this->belongsTo(PlatformSupplier::class);
    }

    /**
     * Связь с пользователем системы
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Сообщения диалога
     */
    public function messages(): HasMany
    {
        return $this->hasMany(WeChatMessage::class, 'conversation_id');
    }

    /**
     * Количество непрочитанных сообщений
     */
    public function unreadMessagesCount(): int
    {
        return $this->messages()
            ->where('direction', 'incoming')
            ->where('is_read', false)
            ->count();
    }

    /**
     * Последнее сообщение
     */
    public function latestMessage(): ?WeChatMessage
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Отображаемое имя контакта
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->remark) {
            return $this->remark;
        }

        if ($this->platformSupplier) {
            return $this->platformSupplier->name;
        }

        if ($this->user) {
            return $this->user->name;
        }

        return $this->nickname ?? $this->wechat_openid;
    }
}
