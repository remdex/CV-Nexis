<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class CandidateComment extends Model
{
    use AsSource;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'candidate_comments';

    /**
     * @var array
     */
    protected $fillable = [
        'candidate_id',
        'user_id',
        'comment',
    ];

    /**
     * Get the candidate that owns the comment.
     */
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Get the user who created the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
