<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class DocumentType extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'name'
    ];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
