<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;
use App\Models\Municipality;

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
            Select::make('division_municipality')
                ->options(Municipality::all()->pluck('name', 'code')->toArray())
                ->value($this->request->get('division_municipality'))
                ->empty(__('companies.no_select_municipality'))
                ->title('')
        ];
    }
}
