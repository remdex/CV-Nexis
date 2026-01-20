<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;

class SpecialityManualFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return __('candidates.fields.speciality_entered_manually');
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
        $value = $this->request->get('speciality_entered_manually');

        if ($value === null || trim($value) === '') {
            return $builder;
        }

        return $builder->where('speciality_entered_manually', 'like', '%'.$value.'%');
    }

    /**
     * Get the display fields.
     *
     * @return Field[]
     */
    public function display(): iterable
    {
        return [
                Input::make('speciality_entered_manually')
                ->type('text')
                ->value($this->request->get('speciality_entered_manually'))
                ->placeholder(__('candidates.filters.speciality_placeholder'))
                ->title('')
        ];
    }
}
