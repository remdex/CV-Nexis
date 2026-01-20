<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;
use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;


class Candidate extends Model
{
    //
    use AsSource, Filterable, Attachable;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'surname',
        'user_id',
        'active',
        'black_list',
        'locked',
        'locked_by_user_id',
        'comment',
        'city',
        'email',
        'phone',
        'speciality_entered_manually'
    ];

    /**
     * Boot the model and register event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($candidate) {
            $candidate->attachment()->each(function ($attachment) {
                $attachment->delete();
            });
        });
    }

    /**
     * @var array
     */
    protected $casts = [
        'locked' => 'boolean',
        'active' => 'boolean',
        'black_list' => 'boolean',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'active' => true,
    ];

    /**
     * Get the user who created this candidate.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who locked this candidate.
     */
    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by_user_id');
    }

    /**
     * Get the specialities assigned to this candidate.
     */
    public function specialities()
    {
        return $this->belongsToMany(Speciality::class, 'candidate_speciality');
    }

    /**
     * Get the competences assigned to this candidate.
     */
    public function competences()
    {
        return $this->belongsToMany(Competence::class, 'candidate_competence');
    }

    /**
     * Companies this candidate has worked at (by company_code).
     */
    public function workedCompanies()
    {
        return $this->belongsToMany(
            Company::class,
            'candidate_company',      // pivot table
            'candidate_id',           // foreign key on pivot to candidates
            'company_code',           // related key on pivot (string code)
            'id',                     // local key on candidates
            'company_code'            // related key on companies table
        );
    }

    /**
     * Get the comments for this candidate.
     */
    public function comments()
    {
        return $this->hasMany(CandidateComment::class);
    }

}
