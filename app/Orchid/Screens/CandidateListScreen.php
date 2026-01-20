<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use App\Models\Candidate;
use Orchid\Screen\Actions\Link;
use App\Orchid\Layouts\CandidateListLayout;
use App\Orchid\Layouts\CandidateSelection;

class CandidateListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
         return [
            'candidates' => Candidate::with(['lockedByUser'])
                ->filters(CandidateSelection::class)
                ->orderBy('created_at', 'desc')
                ->paginate()
        ];

    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('candidates.title');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.candidates',
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
            Link::make(__('candidates.create'))
                ->icon('pencil')
                ->route('platform.hrm.candidate.edit')
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
            CandidateSelection::class,
            CandidateListLayout::class
        ];
    }
}
