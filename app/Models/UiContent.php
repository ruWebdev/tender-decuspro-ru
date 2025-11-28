<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UiContent extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'key',
        'value_ru',
        'value_en',
        'value_cn',
    ];

    /**
     * Получить переводы для указанного префикса
     */
    public static function getOverridesFor(string $locale, string $prefix = 'home.'): array
    {
        $items = static::query()
            ->where('key', 'LIKE', $prefix . '%')
            ->get(['key', 'value_ru', 'value_en', 'value_cn']);

        $map = [];
        foreach ($items as $item) {
            $val = match ($locale) {
                'en' => $item->value_en,
                'cn' => $item->value_cn,
                default => $item->value_ru,
            };
            if (!is_null($val) && $val !== '') {
                $map[$item->key] = $val;
            }
        }

        return $map;
    }

    /**
     * Получить все переводы для указанной локали
     */
    public static function getAllTranslations(string $locale): array
    {
        $items = static::query()->get(['key', 'value_ru', 'value_en', 'value_cn']);

        $map = [];
        foreach ($items as $item) {
            $val = match ($locale) {
                'en' => $item->value_en,
                'cn' => $item->value_cn,
                default => $item->value_ru,
            };
            if (!is_null($val) && $val !== '') {
                $map[$item->key] = $val;
            }
        }

        return $map;
    }
}
