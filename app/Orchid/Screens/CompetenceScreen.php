<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;
use App\Models\Competence as ModelCompetence;
use Exception;
use Illuminate\Http\Request;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Illuminate\View\Factory;

class CompetenceScreen extends Screen
{

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'competences' => ModelCompetence::latest()->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('competences.title');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.competences',
        ];
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return __('competences.description');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make(__('competences.add'))
                ->modal('competenceModal')
                ->method('create')
                ->icon('plus'),
        ];
    }

    /**
     * Prepare query data for edit modal
     */
    public function asyncEditModal(ModelCompetence $competence): iterable
    {
        return [
            'competence' => $competence,
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
            Layout::table('competences', [
                TD::make('name', __('competences.fields.name')),
                TD::make('Actions', __('competences.title'))
                ->alignRight()
                ->render(function (ModelCompetence $competence) {
                    return view('partials.competence-actions', [
                        'competence' => $competence,
                    ]);
                }),
                
            ]),

            Layout::modal('competenceModal', 
                Layout::rows([
                    Input::make('competence.name')
                        ->title(__('competences.fields.name'))
                        ->placeholder(__('competences.fields.placeholder'))
                        ->help(__('competences.fields.help_create')),
                ]))
                ->title(__('competences.create'))
                ->applyButton(__('competences.create_button')),

            Layout::modal('editCompetenceModal',
                Layout::rows([
                    Input::make('competence.name')
                        ->title(__('competences.fields.name'))
                        ->placeholder(__('competences.fields.placeholder'))
                        ->help(__('competences.fields.help')),
                ]))
                ->title(__('competences.edit_title'))
                ->applyButton(__('competences.update_button'))
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
            'competence.name' => 'required|max:255',
        ]);

        $task = new ModelCompetence();
        $task->name = $request->input('competence.name');
        $task->save();
    }

    /**
     * Update an existing competence
     *
     * @param \Illuminate\Http\Request $request
     * @param ModelCompetence $competence
     *
     * @return void
     */
    public function update(Request $request, ModelCompetence $competence)
    {
        $request->validate([
            'competence.name' => 'required|max:255',
        ]);

        $competence->name = $request->input('competence.name');
        $competence->save();
    }

    /**
     * Delete a competence
     *
     * @param ModelCompetence $task
     *
     * @return void
     */
    public function delete(ModelCompetence $competence)
    {
        $competence->delete();
    }

}
