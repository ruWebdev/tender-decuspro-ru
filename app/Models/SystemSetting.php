<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Модель системных настроек
 */
class SystemSetting extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Получить значение настройки по ключу
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        return Cache::remember("system_setting_{$key}", 60, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Установить значение настройки
     */
    public static function setValue(string $key, mixed $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("system_setting_{$key}");
    }

    /**
     * Проверить, заблокирован ли проект
     */
    public static function isProjectBlocked(): bool
    {
        return self::getValue('project_blocked', '0') === '1';
    }

    /**
     * Установить статус блокировки проекта
     */
    public static function setProjectBlocked(bool $blocked): void
    {
        self::setValue('project_blocked', $blocked ? '1' : '0');
    }
}
