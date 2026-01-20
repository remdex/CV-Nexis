<?php

namespace App\Orchid\Layouts;

use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;
use App\Orchid\Filters\CompanyCodeFilter;
use App\Orchid\Filters\CompanyNameFilter;
use App\Orchid\Filters\CompanyActivityFilter;
use App\Orchid\Filters\CompanyTypeFilter;
use App\Orchid\Filters\DivisionMunicipalityFilter;

class CompanySelection extends Selection
{
    public $template = self::TEMPLATE_LINE;

    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            CompanyNameFilter::class,
            CompanyActivityFilter::class,
            CompanyCodeFilter::class,
            DivisionMunicipalityFilter::class,
            CompanyTypeFilter::class            
        ];
    }
}
