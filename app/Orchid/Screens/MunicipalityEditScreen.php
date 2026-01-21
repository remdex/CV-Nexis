<?php

namespace App\Orchid\Screens;

use App\Models\Municipality;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class MunicipalityEditScreen extends Screen
{
    public $municipality;

    public function query(Municipality $municipality): iterable
    {
        return ['municipality' => $municipality];
    }

    public function name(): ?string
    {
        return $this->municipality->exists ? 'Edit Municipality' : 'Create Municipality';
    }

    public function permission(): ?iterable
    {
        return ['platform.systems.companies'];
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Save')
                ->icon('check')
                ->method('createOrUpdate'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('municipality.code')->title('Code'),
                Input::make('municipality.name')->title('Name'),
                Input::make('municipality.county_external_id')->title('County External ID'),
                DateTimer::make('municipality.valid_from')->title('Valid From')->format('Y-m-d')->allowInput(),
                Input::make('municipality.type')->title('Type'),
                Input::make('municipality.type_short')->title('Type (short)'),
            ])
        ];
    }

    public function createOrUpdate(Municipality $municipality, Request $request)
    {
        $municipality->fill($request->get('municipality'))->save();

        Alert::info('Municipality saved.');

        return redirect()->route('platform.hrm.municipality.list');
    }
}
