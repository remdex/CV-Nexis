<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateSpeciality extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'candidate_speciality';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'candidate_id',
        'speciality_id'
    ];
}
