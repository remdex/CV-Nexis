<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Company;
use Orchid\Screen\Actions\Link;

class CompanyListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'companies';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('company_code', __('companies.fields.company_code'))
                ->class('text-nowrap')
                ->render(function (Company $company) {
                    return Link::make($company->company_code)
                        ->icon('pencil')
                        ->route('platform.hrm.company.edit', $company);
                }),
            TD::make('vat_code', __('companies.fields.vat_code'))
                ->class('text-nowrap'),
            TD::make('name', __('companies.fields.company_name'))
                ->render(function (Company $company) {
                    return Link::make($company->name)
                        ->route('platform.hrm.company.edit', $company);
                }),
            TD::make('activity', __('companies.fields.activity'))
                ->render(function (Company $company) {
                    $activities = $company->activityClassificators;
                    if ($activities->isEmpty()) {
                        return __('companies.fields.no_activity');
                    }
                    return $activities->pluck('name_lt')->implode(', ');
                }),
            TD::make('client_type', __('companies.fields.type')),
            TD::make('country', __('companies.fields.country')),
            TD::make('registration_date', __('companies.fields.registration_date'))
             ->class('text-nowrap')
                ->render(function (Company $company) {
                    return $company->registration_date?->format('Y-m-d');
                }),
            TD::make('activity_start_date', __('companies.fields.veiklos_pradzia'))
             ->class('text-nowrap')
                ->render(function (Company $company) {
                    return $company->activity_start_date->format('Y-m-d H:i');
                }),
            /*TD::make('activity_end_date', __('companies.fields.veiklos_pabaiga'))
                ->render(function (Company $company) {
                    return $company->activity_end_date?->format('Y-m-d H:i');
                }),*/
            TD::make('detailed_info', __('companies.fields.detailed_information'))
             ->class('text-nowrap')
                ->render(function (Company $company) {
                    $url = 'https://rekvizitai.vz.lt/imones/1/?scrollTo=searchForm&name=&company_code='
                        . urlencode($company->company_code)
                        . '&search_word=&industry=&search_terms=&location=&catUrlKey=&resetFilter=0&order=1&redirected=1';
                    return '<a href="' . e($url) . '" target="_blank" rel="noopener noreferrer">🔗 ' . __('companies.fields.view_details') . '</a>';
                }),
        ];
    }
}
