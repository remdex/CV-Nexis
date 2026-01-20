<?php

namespace App\Orchid\Layouts;

use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;
use App\Orchid\Filters\DocumentTypeFilter;
use App\Orchid\Filters\DocumentCustomNameFilter;

class DocumentSelection extends Selection
{
    public $template = self::TEMPLATE_LINE;

    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            DocumentCustomNameFilter::class,
            DocumentTypeFilter::class,
            \App\Orchid\Filters\DocumentDateRangeFilter::class,
        ];
    }
}
