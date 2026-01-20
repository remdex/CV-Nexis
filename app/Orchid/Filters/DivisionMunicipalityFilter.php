<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;

class DivisionMunicipalityFilter extends Filter
{
    /**
     * The displayable name of the filter.
     */
    public function name(): string
    {
        return __('companies.filters.division_municipality');
    }

    /**
     * The array of matched parameters.
     */
    public function parameters(): ?array
    {
        return [];
    }

    /**
     * Apply to a given Eloquent query builder.
     */
    public function run(Builder $builder): Builder
    {
        $value = trim((string) $this->request->get('division_municipality', ''));

        if ($value !== '') {
            // Exact match on division_municipality field
            $builder = $builder->where('division_municipality', $value);
        }

        return $builder;
    }

    /**
     * Get the display fields.
     *
     * @return Field[]
     */
    public function display(): iterable
    {
        return [
            Input::make('division_municipality')
                ->type('text')
                ->value($this->request->get('division_municipality'))
                ->placeholder(__('companies.filters.search_by_division_municipality'))
                ->title('')
        ];
    }
}
