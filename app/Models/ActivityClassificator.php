<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;
use Illuminate\Support\Str;

class ActivityClassificator extends Model
{
    use HasFactory, AsSource;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'code',
        'name_lt',
        'name_en',
        'notes_lt',
        'notes_en',
        'level',
        'broader_activity_type',
    ];

    /**
     * Boot function to generate UUID for id if not provided
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
