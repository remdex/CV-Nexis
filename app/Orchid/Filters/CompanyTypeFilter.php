<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;

class CompanyTypeFilter extends Filter
{
    /**
     * The displayable name of the filter.
     */
    public function name(): string
    {
        return __('companies.filters.type');
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
        $type = $this->request->get('type_code', '');

        if ($type === '' || $type === null) {
            return $builder;
        }

        if (is_array($type)) {
            $builder->whereIn('type_code', $type);
            return $builder;
        }

        $type = trim((string) $type);

        if ($type === '') {
            return $builder;
        }

        if (strpos($type, ',') !== false) {
            $codes = array_filter(array_map('trim', explode(',', $type)), function ($v) {
                return $v !== '';
            });

            if (count($codes) > 1) {
                $builder->whereIn('type_code', $codes);
            } elseif (count($codes) === 1) {
                $builder->where('type_code', $codes[0]);
            }

            return $builder;
        }

        $builder->where('type_code', $type);

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
            Select::make('type_code')
                ->options(config('company.types', []))
                ->placeholder(__('companies.filters.type_placeholder'))
                ->title('')
                ->empty(__('companies.no_select_company_type'))
                ->value($this->request->get('type_code')),
        ];
    }
}
