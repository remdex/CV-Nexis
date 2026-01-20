<?php

namespace App\Orchid\Layouts;

use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;
use App\Orchid\Filters\CompanyCodeFilter;
use App\Orchid\Filters\CompanyNameFilter;

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
            CompanyCodeFilter::class            
        ];
    }
}
