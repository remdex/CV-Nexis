<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Candidate;
use Orchid\Screen\Actions\Link;

class CandidateListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'candidates';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('name', __('candidates.fields.name'))
                ->render(function (Candidate $candidate) {
                    return Link::make($candidate->name)
                        ->icon('pencil')
                        ->route('platform.hrm.candidate.edit', $candidate);
                }),
            TD::make('surname', __('candidates.fields.surname'))->render(function (Candidate $candidate) {
                    return Link::make($candidate->surname)
                        ->route('platform.hrm.candidate.edit', $candidate);
                }),
            TD::make('speciality_entered_manually', __('candidates.fields.speciality_entered_manually')),
            TD::make('created_at', __('candidates.fields.created')),
            TD::make('phone', __('candidates.fields.phone')),
            TD::make('city', __('candidates.fields.city_col')),
            TD::make('locked', __('candidates.status.locked'))
                ->render(function (Candidate $candidate) {
                    $icon = $candidate->locked ? 
                        '<span class="text-danger">🔒 ' . __('candidates.status.locked') . '</span>' : 
                        '<span class="text-success">✓ ' . __('candidates.status.unlocked') . '</span>';
                    $lockedBy = $candidate->locked && $candidate->lockedByUser ? 
                        ' <small>(by ' . e($candidate->lockedByUser->name) . ')</small>' : 
                        '';
                    return $icon . $lockedBy;
                }),
            TD::make('active', __('candidates.status.active'))
                ->render(function (Candidate $candidate) {
                    $icon = $candidate->active ? 
                        '<span class="text-success">✓ ' . __('candidates.status.active') . '</span>' : 
                        '<span class="text-danger">❌ ' . __('candidates.status.inactive') . '</span>';

                    return $icon;
                }),
            TD::make('black_list', __('candidates.status.black_list'))
                ->render(function (Candidate $candidate) {
                    $icon = $candidate->black_list ? 
                        '<span class="text-danger">🚫 ' . __('candidates.status.black_list') . '</span>' : 
                        '<span class="text-success">👌 ' . __('candidates.status.valid') . '</span>';

                    return $icon;
                }),
            TD::make('updated_at', __('candidates.fields.last_edit')),
        ];
    }
}
