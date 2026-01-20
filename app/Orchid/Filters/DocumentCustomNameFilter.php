<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;

class DocumentCustomNameFilter extends Filter
{
    public function name(): string
    {
        return __('documents.fields.custom_name');
    }

    public function parameters(): ?array
    {
        return [];
    }

    public function run(Builder $builder): Builder
    {
        $value = trim((string) $this->request->get('custom_name', ''));

        if ($value === '') {
            return $builder;
        }

        return $builder->where('custom_name', 'like', '%' . $value . '%');
    }

    public function display(): iterable
    {
        return [
            Input::make('custom_name')
                ->type('text')
                ->value($this->request->get('custom_name'))
                ->placeholder(__('documents.filters.custom_name_placeholder'))
                ->title(__('documents.fields.custom_name')),
        ];
    }
}
