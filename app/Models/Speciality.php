<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Speciality extends Model
{
    use HasFactory, AsSource;

    /**
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Get the candidates assigned to this speciality.
     */
    public function candidates()
    {
        return $this->belongsToMany(Candidate::class, 'candidate_speciality');
    }
}
