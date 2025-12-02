<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * Модель настроек WeChat Official Account
 */
class WeChatSettings extends Model
{
    use HasUuids;

    protected $table = 'wechat_settings';

    protected $fillable = [
        'app_id',
        'app_secret',
        'token',
        'encoding_aes_key',
        'access_token',
        'access_token_expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'access_token_expires_at' => 'datetime',
        ];
    }

    /**
     * Шифрование AppSecret при сохранении
     */
    public function setAppSecretAttribute(?string $value): void
    {
        $this->attributes['app_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Дешифрование AppSecret при чтении
     */
    public function getAppSecretAttribute(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Получить активные настройки
     */
    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Проверить, истёк ли access_token
     */
    public function isAccessTokenExpired(): bool
    {
        if (! $this->access_token || ! $this->access_token_expires_at) {
            return true;
        }

        return $this->access_token_expires_at->isPast();
    }
}
