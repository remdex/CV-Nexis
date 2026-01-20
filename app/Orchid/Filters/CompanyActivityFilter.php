<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Relation;

class CompanyActivityFilter extends Filter
{
    /**
     * The displayable name of the filter.
     */
    public function name(): string
    {
        return __('companies.filters.activity');
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
        $ids = $this->request->get('activity_ids', []);

        if (empty($ids)) {
            return $builder;
        }

        $builder->whereHas('activityClassificators', function (Builder $q) use ($ids) {
            $table = $q->getModel()->getTable();
            $q->whereIn($table . '.id', $ids);
        });

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
            Relation::make('activity_ids')
                ->fromModel(\App\Models\ActivityClassificator::class, 'name_lt')
                ->multiple()
                ->placeholder(__('companies.filters.activity_placeholder'))
                ->title('')
                ->class('form-control mb-2')
                ->value($this->request->get('activity_ids', [])),
        ];
    }
}
