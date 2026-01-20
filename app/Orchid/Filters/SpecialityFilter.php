<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Relation;

class SpecialityFilter extends Filter
{
    /**
     * The displayable name of the filter.
     */
    public function name(): string
    {
        return __('specialities.title');
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
        $ids = $this->request->get('speciality_ids', []);

        if (empty($ids)) {
            return $builder;
        }

        $matchAll = $this->request->get('speciality_match_all', false);

        if ($matchAll) {
            // AND logic: candidate must have ALL selected specialities
            foreach ($ids as $id) {
                $builder->whereHas('specialities', function (Builder $q) use ($id) {
                    $q->where('specialities.id', $id);
                });
            }
        } else {
            // OR logic: candidate must have ANY of the selected specialities
            $builder->whereHas('specialities', function (Builder $q) use ($ids) {
                $q->whereIn('specialities.id', $ids);
            });
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
            Relation::make('speciality_ids')
                ->fromModel(\App\Models\Speciality::class, 'name')
                ->multiple()
                ->class('form-control mb-2')
                ->placeholder(__('candidates.filters.speciality_main_placeholder'))
                ->title('')
                ->value($this->request->get('speciality_ids', [])),

            CheckBox::make('speciality_match_all')
                ->placeholder(__('candidates.filters.match_all_specialities'))
                ->value($this->request->get('speciality_match_all', false))
                ->sendTrueOrFalse(),
        ];
    }
}
