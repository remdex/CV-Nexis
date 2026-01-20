<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;

class Document extends Model
{
    use HasFactory, AsSource, Attachable, Filterable;

    protected $fillable = [
        'custom_name',
        'document_type_id',
        'user_id'
    ];

    /**
     * Boot the model and register event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($item) {
            $item->attachment()->each(function ($item) {
                $item->delete();
            });
        });
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
