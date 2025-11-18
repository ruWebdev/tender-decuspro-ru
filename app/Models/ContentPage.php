<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentPage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'slug',
        'title_ru',
        'title_en',
        'title_cn',
        'body_ru',
        'body_en',
        'body_cn',
        'published',
    ];

    protected function casts(): array
    {
        return [
            'published' => 'boolean',
        ];
    }

    public function getTitleFor(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->title_en,
            'cn' => $this->title_cn,
            default => $this->title_ru,
        };
    }

    public function getBodyFor(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->body_en,
            'cn' => $this->body_cn,
            default => $this->body_ru,
        };
    }
}
