<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;

class Company extends Model
{
    use AsSource, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_code',
        'name',
        'client_type',
        'registration_date',
        'deregistration_date',
        'annulment_date',
        'country',
        'type_code',
        'type_description',
        'type_from_date',
        'type_until_date',
        'annulment_type',
        'vat_code_prefix',
        'vat_code',
        'vat_registered_date',
        'vat_deregistered_date',
        'division_number',
        'division_name',
        'division_municipality',
        'division_code',
        'formed_date',
        'deformed_date',
        'activity_start_date',
        'activity_end_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'registration_date' => 'date',
        'deregistration_date' => 'date',
        'annulment_date' => 'date',
        'type_from_date' => 'date',
        'type_until_date' => 'date',
        'vat_registered_date' => 'date',
        'vat_deregistered_date' => 'date',
        'formed_date' => 'date',
        'deformed_date' => 'date',
        'activity_start_date' => 'date',
        'activity_end_date' => 'date',
    ];

    /**
     * Get the activity classificators assigned to this company.
     */
    public function activityClassificators()
    {
        return $this->belongsToMany(ActivityClassificator::class, 'company_activity');
    }
}
