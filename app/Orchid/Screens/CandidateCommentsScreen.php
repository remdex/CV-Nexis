<?php

namespace App\Orchid\Screens;

use App\Models\Candidate;
use App\Models\CandidateComment;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Screen\TD;

class CandidateCommentsScreen extends Screen
{
    /**
     * @var Candidate
     */
    public $candidate;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Candidate $candidate): iterable
    {
        $candidate->load(['comments.user']);
        
        return [
            'candidate' => $candidate,
            'comments' => $candidate->comments()->with('user')->orderBy('created_at', 'desc')->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('candidates.comments.for', [
            'name' => $this->candidate->name,
            'surname' => $this->candidate->surname,
        ]);
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
            Link::make(__('candidates.buttons.back_to_candidate'))
                ->icon('arrow-left')
                ->route('platform.hrm.candidate.edit', $this->candidate),
        ];
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return __('candidates.comments.description');
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('comments', [
                TD::make('user.name', __('candidates.comments.author'))
                    ->render(function (CandidateComment $comment) {
                        return $comment->user ? $comment->user->name : __('candidates.status.unknown');
                    }),
                TD::make('comment', __('candidates.comments.comment'))
                    ->render(function (CandidateComment $comment) {
                        return '<div style="white-space: pre-wrap;">' . e($comment->comment) . '</div>';
                    }),
                TD::make('created_at', __('candidates.comments.date'))
                    ->render(function (CandidateComment $comment) {
                        return $comment->created_at->format('Y-m-d H:i:s');
                    }),
                TD::make('actions', __('candidates.comments.actions'))
                    ->alignRight()
                    ->render(function (CandidateComment $comment) {
                        return Button::make(__('candidates.comments.delete'))
                            ->icon('trash')
                            ->confirm(__('candidates.comments.delete_confirm'))
                            ->method('deleteComment', ['comment' => $comment->id]);
                    }),
            ]),

            Layout::rows([
                TextArea::make('new_comment')
                    ->title(__('candidates.comments.add_new'))
                    ->rows(5)
                    ->placeholder(__('candidates.comments.placeholder'))
                    ->help(__('candidates.comments.help')),

                Button::make(__('candidates.comments.add_button'))
                    ->icon('plus')
                    ->method('addComment'),
            ]),
        ];
    }

    /**
     * Add a new comment
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addComment(Request $request)
    {
        $request->validate([
            'new_comment' => 'required|string|min:1',
        ]);

        CandidateComment::create([
            'candidate_id' => $this->candidate->id,
            'user_id' => auth()->id(),
            'comment' => $request->get('new_comment'),
        ]);

        Alert::success(__('candidates.comments.added'));

        return redirect()->route('platform.hrm.candidate.comments', $this->candidate);
    }

    /**
     * Delete a comment
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteComment(Request $request)
    {
        $commentId = $request->get('comment');
        $comment = CandidateComment::findOrFail($commentId);
        
        // Only allow deletion if user is the author or has admin rights
        if ($comment->user_id === auth()->id() || auth()->user()->hasAccess('platform.systems.users')) {
            $comment->delete();
            Alert::success(__('candidates.comments.deleted'));
        } else {
            Alert::error(__('candidates.comments.no_permission'));
        }

        return redirect()->route('platform.hrm.candidate.comments', $this->candidate);
    }
}
