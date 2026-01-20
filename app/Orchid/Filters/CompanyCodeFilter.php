<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;

class CompanyCodeFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return __('companies.filters.company_code');
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
        $companyCode = trim((string) $this->request->get('company_code', ''));
        $vatCode = trim((string) $this->request->get('vat_code', ''));

        if ($companyCode !== '') {
            // Support multiple comma-separated company codes, e.g. "1,2,3"
            $codes = array_filter(array_map('trim', explode(',', $companyCode)), function ($v) {
                return $v !== '';
            });

            if (count($codes) > 1) {
                $builder = $builder->whereIn('company_code', $codes);
            } elseif (count($codes) === 1) {
                $builder = $builder->where('company_code', $codes[0]);
            }
        }

        if ($vatCode !== '') {
            $builder = $builder->where('vat_code', $vatCode);
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
            Input::make('company_code')
                ->type('text')
                ->value($this->request->get('company_code'))
                ->placeholder(__('companies.filters.search_by_code'))
                ->title(''),
            
            Input::make('vat_code')
                ->type('text')
                ->value($this->request->get('vat_code'))
                ->placeholder(__('companies.filters.search_by_vat_code'))
                ->title('')
        ];
    }
}
