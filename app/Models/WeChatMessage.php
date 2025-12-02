<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Модель сообщения WeChat
 */
class WeChatMessage extends Model
{
    use HasUuids;

    protected $table = 'wechat_messages';

    protected $fillable = [
        'conversation_id',
        'direction',
        'msg_type',
        'content',
        'translated_content_ru',
        'media_id',
        'media_url',
        'raw_data',
        'wechat_msg_id',
        'is_read',
        'sender_user_id',
    ];

    protected function casts(): array
    {
        return [
            'raw_data' => 'array',
            'is_read' => 'boolean',
        ];
    }

    /**
     * Диалог, к которому относится сообщение
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(WeChatConversation::class, 'conversation_id');
    }

    /**
     * Отправитель (для исходящих сообщений)
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    /**
     * Является ли сообщение входящим
     */
    public function isIncoming(): bool
    {
        return $this->direction === 'incoming';
    }

    /**
     * Является ли сообщение исходящим
     */
    public function isOutgoing(): bool
    {
        return $this->direction === 'outgoing';
    }

    /**
     * Получить отображаемый контент (с переводом, если есть)
     */
    public function getDisplayContentAttribute(): string
    {
        if ($this->translated_content_ru) {
            return $this->translated_content_ru;
        }

        return $this->content ?? '';
    }
}
