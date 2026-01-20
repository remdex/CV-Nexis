<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;

class DocumentDateRangeFilter extends Filter
{
    public function name(): string
    {
        return __('documents.fields.date_range');
    }

    public function parameters(): array
    {
        return ['created_from', 'created_to'];
    }

    public function run(Builder $builder): Builder
    {
        $from = $this->request->get('created_from');
        $to = $this->request->get('created_to');

        if ($from) {
            $builder = $builder->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $builder = $builder->whereDate('created_at', '<=', $to);
        }

        return $builder;
    }

    public function display(): array
    {
        return [
            Group::make([
                DateTimer::make('created_from')
                    ->format('Y-m-d')
                    ->placeholder(__('documents.select_date'))
                    ->value($this->request->get('created_from'))
                    ->title(__('documents.fields.date_from')),

                DateTimer::make('created_to')
                    ->format('Y-m-d')
                    ->placeholder(__('documents.select_date'))
                    ->value($this->request->get('created_to'))
                    ->title(__('documents.fields.date_to')),
            ]),
        ];
    }

    public function value(): string
    {
        $from = $this->request->get('created_from');
        $to = $this->request->get('created_to');

        if (! $from && ! $to) {
            return '';
        }

        if ($from && $to) {
            return $from . ' — ' . $to;
        }

        return $from ?: $to;
    }
}
