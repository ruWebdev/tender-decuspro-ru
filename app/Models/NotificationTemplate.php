<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'type',
        'body_ru',
        'body_en',
        'body_cn',
    ];

    public const TYPE_NEW_TENDER = 'new_tender';
    public const TYPE_BETTER_PRICE = 'better_price';
    public const TYPE_TENDER_CLOSED = 'tender_closed';
    public const TYPE_WON = 'won';
    public const TYPE_LOST = 'lost';
    public const TYPE_RETENDER = 'retender';
    public const TYPE_PLATFORM_INVITATION = 'platform_invitation';

    public static function types(): array
    {
        return [
            self::TYPE_NEW_TENDER,
            self::TYPE_BETTER_PRICE,
            self::TYPE_TENDER_CLOSED,
            self::TYPE_WON,
            self::TYPE_LOST,
            self::TYPE_RETENDER,
            self::TYPE_PLATFORM_INVITATION,
        ];
    }
}
