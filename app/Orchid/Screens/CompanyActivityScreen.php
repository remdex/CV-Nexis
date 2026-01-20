<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Layout;
use App\Models\ActivityClassificator;
use Exception;
use Illuminate\Http\Request;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Illuminate\View\Factory;

class CompanyActivityScreen extends Screen
{

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'activities' => ActivityClassificator::latest()->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('activities.title');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.activities',
        ];
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return __('activities.description');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make(__('activities.add'))
                ->modal('activityModal')
                ->method('create')
                ->icon('plus'),
        ];
    }

    /**
     * Prepare query data for edit modal
     */
    public function asyncEditModal(ActivityClassificator $activity): iterable
    {
        return [
            'activity' => $activity,
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
            Layout::table('activities', [
                TD::make('code', __('activities.fields.code'))
                    ->sort(),
                TD::make('name_lt', __('activities.fields.name_lt'))
                    ->sort(),
                /*TD::make('name_en', __('activities.fields.name_en'))
                    ->sort(),*/
                TD::make('level', __('activities.fields.level')),
                TD::make('Actions', __('activities.fields.actions'))
                    ->alignRight()
                    ->render(function (ActivityClassificator $activity) {
                        return ModalToggle::make('')
                            ->modal('editActivityModal')
                            ->method('update')
                            ->asyncParameters(['activity' => $activity->id])
                            ->icon('pencil');
                    }),
            ]),

            Layout::modal('activityModal', 
                Layout::rows([
                    Input::make('activity.code')
                        ->title(__('activities.fields.code'))
                        ->placeholder(__('activities.placeholders.code'))
                        ->help(__('activities.help.code')),
                    Input::make('activity.name_lt')
                        ->title(__('activities.fields.name_lt'))
                        ->placeholder(__('activities.placeholders.name_lt'))
                        ->help(__('activities.help.name_lt')),
                    Input::make('activity.name_en')
                        ->title(__('activities.fields.name_en'))
                        ->placeholder(__('activities.placeholders.name_en'))
                        ->help(__('activities.help.name_en')),
                    TextArea::make('activity.notes_lt')
                        ->title(__('activities.fields.notes_lt'))
                        ->placeholder(__('activities.placeholders.notes_lt'))
                        ->rows(5),
                    TextArea::make('activity.notes_en')
                        ->title(__('activities.fields.notes_en'))
                        ->placeholder(__('activities.placeholders.notes_en'))
                        ->rows(5),
                    Input::make('activity.level')
                        ->title(__('activities.fields.level'))
                        ->placeholder(__('activities.placeholders.level')),
                    Input::make('activity.broader_activity_type')
                        ->title(__('activities.fields.broader_activity_type'))
                        ->placeholder(__('activities.placeholders.broader_activity_type')),
                ]))
                ->title(__('activities.create'))
                ->applyButton(__('activities.create_button')),

            Layout::modal('editActivityModal',
                Layout::rows([
                    Input::make('activity.id')
                        ->type('hidden'),
                    Input::make('activity.code')
                        ->title(__('activities.fields.code'))
                        ->placeholder(__('activities.placeholders.code'))
                        ->help(__('activities.help.code')),
                    Input::make('activity.name_lt')
                        ->title(__('activities.fields.name_lt'))
                        ->placeholder(__('activities.placeholders.name_lt'))
                        ->help(__('activities.help.name_lt')),
                    Input::make('activity.name_en')
                        ->title(__('activities.fields.name_en'))
                        ->placeholder(__('activities.placeholders.name_en'))
                        ->help(__('activities.help.name_en')),
                    TextArea::make('activity.notes_lt')
                        ->title(__('activities.fields.notes_lt'))
                        ->placeholder(__('activities.placeholders.notes_lt'))
                        ->rows(5),
                    TextArea::make('activity.notes_en')
                        ->title(__('activities.fields.notes_en'))
                        ->placeholder(__('activities.placeholders.notes_en'))
                        ->rows(5),
                    Input::make('activity.level')
                        ->title(__('activities.fields.level'))
                        ->placeholder(__('activities.placeholders.level')),
                    Input::make('activity.broader_activity_type')
                        ->title(__('activities.fields.broader_activity_type'))
                        ->placeholder(__('activities.placeholders.broader_activity_type')),
                ]))
                ->title(__('activities.edit_title'))
                ->applyButton(__('activities.update_button'))
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
        $request->validate([
            'activity.code' => 'nullable|max:255',
            'activity.name_lt' => 'nullable|max:255',
            'activity.name_en' => 'nullable|max:255',
        ]);

        $activity = new ActivityClassificator();
        $activity->code = $request->input('activity.code');
        $activity->name_lt = $request->input('activity.name_lt');
        $activity->name_en = $request->input('activity.name_en');
        $activity->notes_lt = $request->input('activity.notes_lt');
        $activity->notes_en = $request->input('activity.notes_en');
        $activity->level = $request->input('activity.level');
        $activity->broader_activity_type = $request->input('activity.broader_activity_type');
        $activity->save();
    }

    /**
     * Update an existing activity
     *
     * @param \Illuminate\Http\Request $request
     * @param ActivityClassificator $activity
     *
     * @return void
     */
    public function update(Request $request, ActivityClassificator $activity)
    {
        $request->validate([
            'activity.code' => 'nullable|max:255',
            'activity.name_lt' => 'nullable|max:255',
            'activity.name_en' => 'nullable|max:255',
        ]);

        $activity->code = $request->input('activity.code');
        $activity->name_lt = $request->input('activity.name_lt');
        $activity->name_en = $request->input('activity.name_en');
        $activity->notes_lt = $request->input('activity.notes_lt');
        $activity->notes_en = $request->input('activity.notes_en');
        $activity->level = $request->input('activity.level');
        $activity->broader_activity_type = $request->input('activity.broader_activity_type');
        $activity->save();
    }

}

