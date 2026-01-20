<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;

class SurnameFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return __('candidates.fields.surname');
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
        $surname = (string) $this->request->get('surname');

        if (trim($surname) === '') {
            return $builder;
        }

        return $builder->where('surname', 'like', '%'.$surname.'%');
    }

    /**
     * Get the display fields.
     *
     * @return Field[]
     */
    public function display(): iterable
    {
         return [
            Input::make('surname')
                ->type('text')
                ->value($this->request->get('surname'))
                ->placeholder(__('candidates.filters.surname_placeholder'))
                ->title('')
        ];
    }
}
