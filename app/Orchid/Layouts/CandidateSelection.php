<?php

namespace App\Orchid\Layouts;

use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;
use App\Orchid\Filters\NameFilter;
use App\Orchid\Filters\SurnameFilter;
use App\Orchid\Filters\CityFilter;
use Orchid\Screen\Fields\Group;

//use App\Orchid\Filters\ActiveStatusFilter;
/*use App\Orchid\Filters\BlackListFilter;*/
use App\Orchid\Filters\LockedFilter;

use App\Orchid\Filters\SpecialityManualFilter;
use App\Orchid\Filters\SpecialityFilter;
use App\Orchid\Filters\CompetenceFilter;
use \App\Orchid\Filters\StatusGroupFilter;

class CandidateSelection extends Selection
{
    public $template = self::TEMPLATE_LINE;

    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            NameFilter::class,
            SurnameFilter::class,
            CityFilter::class,
            SpecialityManualFilter::class,
            SpecialityFilter::class,
            CompetenceFilter::class,
            StatusGroupFilter::class,
            LockedFilter::class

            /*ActiveStatusFilter::class,
            BlackListFilter::class,
            LockedFilter::class*/
            
        ];
    }
}
