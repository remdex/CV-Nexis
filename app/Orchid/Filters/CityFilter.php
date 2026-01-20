<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;

class CityFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return __('candidates.fields.city');
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
        $city = trim((string) $this->request->get('city', ''));

        if ($city === '') {
            return $builder;
        }

        return $builder->where('city', 'like', '%'.$city.'%');
    }

    /**
     * Get the display fields.
     *
     * @return Field[]
     */
    public function display(): iterable
    {
        return [
            Input::make('city')
                ->type('text')
                ->value($this->request->get('city'))
                ->placeholder(__('candidates.fields.city'))
                ->title('')
        ];
    }
}
