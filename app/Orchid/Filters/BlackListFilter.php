<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\CheckBox;

class BlackListFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return __('candidates.filters.not_blacklisted');
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters(): ?array
    {
        return ['not_blacklisted'];
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
        if ($this->request->has('not_blacklisted')) {
            return $builder->where('black_list', 0);
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
            CheckBox::make('not_blacklisted')
                ->value($this->request->get('not_blacklisted'))
                ->placeholder(__('candidates.filters.not_blacklisted'))
                ->title('')
        ];
    }
}
