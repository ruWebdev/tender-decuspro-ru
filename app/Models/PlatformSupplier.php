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
        'title',
        'phone',
        'all_phones',
        'email',
        'all_emails',
        'website',
        'location',
        'contact_person',
        'established_year',
        'employee_count',
        'comment',
        'description',
        'main_products',
        'export_markets',
        'certifications',
        'keyword',
        'source_external_id',
        'parsed_at',
        'language',
        'invitation_sent',
        'last_mailing_at',
    ];

    protected $casts = [
        'established_year' => 'integer',
        'employee_count' => 'integer',
        'parsed_at' => 'datetime',
        'invitation_sent' => 'boolean',
        'last_mailing_at' => 'datetime',
    ];
}
