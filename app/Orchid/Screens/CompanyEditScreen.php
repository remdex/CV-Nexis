<?php

namespace App\Orchid\Screens;

use App\Models\Company;
use App\Models\ActivityClassificator;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class CompanyEditScreen extends Screen
{
    /**
     * @var Company
     */
    public $company;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Company $company): iterable
    {
        $company->load(['activityClassificators']);
        
        return [
            'company' => $company,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->company->exists ? __('companies.edit_title') : __('companies.add');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.companies',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('companies.create_button'))
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(!$this->company->exists),

            Button::make(__('companies.update_button'))
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee($this->company->exists),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('company.company_code')
                    ->title(__('companies.form_fields.company_code'))
                    ->placeholder(__('companies.form_placeholders.company_code'))
                    ->required()
                    ->help(__('companies.form_help.company_code')),

                Input::make('company.name')
                    ->title(__('companies.form_fields.company_name'))
                    ->placeholder(__('companies.form_placeholders.company_name'))
                    ->required(),

                Input::make('company.client_type')
                    ->title(__('companies.form_fields.client_type'))
                    ->placeholder(__('companies.form_placeholders.client_type')),

                Input::make('company.country')
                    ->title(__('companies.form_fields.country'))
                    ->placeholder(__('companies.form_placeholders.country'))
                    ->maxlength(3),

                DateTimer::make('company.registration_date')
                    ->title(__('companies.form_fields.registration_date'))
                    ->format('Y-m-d')
                    ->allowInput(),

                DateTimer::make('company.deregistration_date')
                    ->title(__('companies.form_fields.deregistration_date'))
                    ->format('Y-m-d')
                    ->allowInput(),

                Input::make('company.type_code')
                    ->title(__('companies.form_fields.type_code')),

                Input::make('company.type_description')
                    ->title(__('companies.form_fields.type_description')),

                Input::make('company.vat_code')
                    ->title(__('companies.form_fields.vat_code')),

                Input::make('company.vat_code_prefix')
                    ->title(__('companies.form_fields.vat_code_prefix'))
                    ->placeholder(__('companies.form_placeholders.vat_code_prefix')),

                DateTimer::make('company.vat_registered_date')
                    ->title(__('companies.form_fields.vat_registered'))
                    ->format('Y-m-d')
                    ->allowInput(),

                DateTimer::make('company.vat_deregistered_date')
                    ->title(__('companies.form_fields.vat_deregistered'))
                    ->format('Y-m-d')
                    ->allowInput(),

                Input::make('company.division_number')
                    ->title(__('companies.form_fields.division_number')),

                Input::make('company.division_name')
                    ->title(__('companies.form_fields.division_name')),

                Select::make('company.division_municipality')
                    ->title('Municipality')
                    ->options(Municipality::all()->pluck('name', 'code')->toArray())
                    ->empty(''),

                Input::make('company.division_code')
                    ->title(__('companies.form_fields.division_code')),

                Relation::make('company.activityClassificators')
                    ->multiple()
                    ->fromModel(ActivityClassificator::class, 'name_lt')
                    ->title(__('companies.form_fields.activity_classificators'))
                    ->help(__('companies.form_help.activity_classificators')),
            ]),
        ];
    }

    /**
     * @param Company $company
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Company $company, Request $request)
    {
        $request->validate([
            'company.company_code' => 'required|string|unique:companies,company_code,' . ($company->id ?? 'NULL'),
            'company.name' => 'required|string',
        ]);

        $companyData = $request->get('company');
        $activityClassificators = $companyData['activityClassificators'] ?? [];
        unset($companyData['activityClassificators']);

        $company->fill($companyData)->save();

        // Sync activity classificators
        $company->activityClassificators()->sync($activityClassificators);

        Alert::info(__('companies.alerts.saved'));

        return redirect()->route('platform.hrm.company.list');
    }
}
