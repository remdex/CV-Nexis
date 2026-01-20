<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateCompetence extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'candidate_competence';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'candidate_id',
        'competence_id'
    ];
}
