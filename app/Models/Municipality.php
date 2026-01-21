<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Municipality extends Model
{
    use HasFactory, AsSource;

    protected $table = 'municipalities';

    // Use auto-incrementing id
    protected $fillable = [
        'external_id',
        'code',
        'name',
        'county_external_id',
        'valid_from',
        'type',
        'type_short',
    ];

    protected $casts = [
        'valid_from' => 'date',
    ];
}
