<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Relation;

class CompetenceFilter extends Filter
{
    /**
     * The displayable name of the filter.
     */
    public function name(): string
    {
        return __('competences.title');
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
        $ids = $this->request->get('competence_ids', []);

        if (empty($ids)) {
            return $builder;
        }

        $matchAll = $this->request->get('competence_match_all', false);

        if ($matchAll) {
            // AND logic: candidate must have ALL selected competences
            foreach ($ids as $id) {
                $builder->whereHas('competences', function (Builder $q) use ($id) {
                    $q->where('competences.id', $id);
                });
            }
        } else {
            // OR logic: candidate must have ANY of the selected competences
            $builder->whereHas('competences', function (Builder $q) use ($ids) {
                $q->whereIn('competences.id', $ids);
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
            Relation::make('competence_ids')
                ->fromModel(\App\Models\Competence::class, 'name')
                ->multiple()
                ->placeholder(__('candidates.filters.competence_main_placeholder'))
                ->title('')
                ->class('form-control mb-2')
                ->value($this->request->get('competence_ids', [])),
      
            CheckBox::make('competence_match_all')
                ->placeholder(__('candidates.filters.match_all_competences'))
                ->value($this->request->get('competence_match_all', false))
                ->sendTrueOrFalse(),
        ];
    }
}
