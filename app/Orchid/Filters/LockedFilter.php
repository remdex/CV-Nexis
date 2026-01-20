<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\CheckBox;

class LockedFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return __('candidates.filters.not_locked');
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters(): ?array
    {
        return ['not_locked'];
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
        if ($this->request->has('not_locked')) {
            return $builder->where('locked', 0);
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
            CheckBox::make('not_locked')
                ->value($this->request->get('not_locked'))
                ->placeholder(__('candidates.filters.not_locked'))
                ->title('')
        ];
    }
}
