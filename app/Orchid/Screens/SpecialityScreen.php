<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;
use App\Models\Speciality as ModelSpeciality;
use Exception;
use Illuminate\Http\Request;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Illuminate\View\Factory;

class SpecialityScreen extends Screen
{

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'specialities' => ModelSpeciality::latest()->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('specialities.title');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.specialities',
        ];
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return __('specialities.description');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make(__('specialities.add'))
                ->modal('specialityModal')
                ->method('create')
                ->icon('plus'),
        ];
    }

    /**
     * Prepare query data for edit modal
     */
    public function asyncEditModal(ModelSpeciality $speciality): iterable
    {
        return [
            'speciality' => $speciality,
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
            Layout::table('specialities', [
                TD::make('name', __('specialities.fields.name')),
                TD::make('Actions', __('specialities.title'))
                ->alignRight()
                ->render(function (ModelSpeciality $speciality) {
                    return view('partials.speciality-actions', [
                        'speciality' => $speciality,
                    ]);
                }),
                
            ]),

            Layout::modal('specialityModal', 
                Layout::rows([
                    Input::make('speciality.name')
                        ->title(__('specialities.fields.name'))
                        ->placeholder(__('specialities.fields.placeholder'))
                        ->help(__('specialities.fields.help_create')),
                ]))
                ->title(__('specialities.create'))
                ->applyButton(__('specialities.create_button')),

            Layout::modal('editSpecialityModal',
                Layout::rows([
                    Input::make('speciality.name')
                        ->title(__('specialities.fields.name'))
                        ->placeholder(__('specialities.fields.placeholder'))
                        ->help(__('specialities.fields.help')),
                ]))
                ->title(__('specialities.edit_title'))
                ->applyButton(__('specialities.update_button'))
                ->async('asyncEditModal'),
        ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function create(Request $request)
    {
        // Validate form data, save task to database, etc.
        $request->validate([
            'speciality.name' => 'required|max:255',
        ]);

        $task = new ModelSpeciality();
        $task->name = $request->input('speciality.name');
        $task->save();
    }

    /**
     * Update an existing speciality
     *
     * @param \Illuminate\Http\Request $request
     * @param ModelSpeciality $speciality
     *
     * @return void
     */
    public function update(Request $request, ModelSpeciality $speciality)
    {
        $request->validate([
            'speciality.name' => 'required|max:255',
        ]);

        $speciality->name = $request->input('speciality.name');
        $speciality->save();
    }

    /**
     * Delete a speciality
     *
     * @param ModelSpeciality $task
     *
     * @return void
     */
    public function delete(ModelSpeciality $speciality)
    {
        $speciality->delete();
    }

}
