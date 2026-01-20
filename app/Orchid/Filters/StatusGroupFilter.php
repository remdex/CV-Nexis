<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;

class StatusGroupFilter extends Filter
{
    public function name(): string
    {
        return __('candidates.filters.status_group');
    }

    public function parameters(): ?array
    {
        // Ensure the filter system recognizes nested keys produced by the group.
        return [
            'status_group.active',
            'status_group.not_blacklisted',
            //'status_group.not_locked',
        ];
    }

    public function run(Builder $builder): Builder
    {


        if ($this->request->has('status_group.active')) {
            $builder->where('active', true);
        }

        if ($this->request->has('status_group.not_blacklisted')) {
            $builder->where('black_list', 0);
        }

        if ($this->request->has('status_group.not_locked')) {
            $builder->where('locked', 0);
        }

        return $builder;
    }

    public function display(): iterable
    {
        return [
                CheckBox::make('status_group.active')
                    ->value($this->request->get('status_group.active'))
                    ->placeholder(__('candidates.filters.active'))
                    ->title(''),

                CheckBox::make('status_group.not_blacklisted')
                    ->value($this->request->get('status_group.not_blacklisted'))
                    ->placeholder(__('candidates.filters.not_blacklisted'))
                    ->title(''),

                /*CheckBox::make('status_group.not_locked')
                    ->value($this->request->get('status_group.not_locked'))
                    ->placeholder(__('candidates.filters.not_locked'))
                    ->title('')*/
             
        ];
    }
}
