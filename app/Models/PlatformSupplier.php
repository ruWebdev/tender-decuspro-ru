<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformSupplier extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'website',
        'comment',
        'language',
        'invitation_sent',
        'last_mailing_at',
    ];

    protected $casts = [
        'invitation_sent' => 'boolean',
        'last_mailing_at' => 'datetime',
    ];
}
