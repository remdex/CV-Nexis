<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;

class CompanyNameFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return __('companies.filters.company_name');
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters(): ?array
    {
        return [];
    }

    /**
     * Apply to a given Eloquent query builder.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        $name = trim((string) $this->request->get('name', ''));

        if ($name === '') {
            return $builder;
        }

        return $builder->where('name', 'like', '%'.$name.'%');
    }

    /**
     * Get the display fields.
     *
     * @return Field[]
     */
    public function display(): iterable
    {
        return [
            Input::make('name')
                ->type('text')
                ->value($this->request->get('name'))
                ->placeholder(__('companies.filters.search_by_name'))
                ->title('')
        ];
    }
}
