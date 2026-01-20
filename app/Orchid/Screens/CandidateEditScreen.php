<?php

namespace App\Orchid\Screens;


use App\Models\Candidate;
use App\Models\CandidateComment;
use App\Models\User;
use App\Models\Speciality;
use App\Models\Competence;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\CheckBox;
use App\Orchid\Fields\PreviewUpload;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Screen\Fields\Attach;
use Orchid\Screen\Fields\Group;
use Orchid\Attachment\Models\Attachment as OrchidAttachment;
use Orchid\Screen\Layouts\Modal;

class CandidateEditScreen extends Screen
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
        $candidate->load(['user', 'lockedByUser', 'specialities', 'competences', 'workedCompanies', 'attachments']);
        
        // Get last 3 comments for this candidate
        $lastComments = $candidate->exists 
            ? $candidate->comments()->with('user')->latest()->take(3)->get() 
            : collect();
        
        return [
            'candidate' => $candidate,
            'comments' => $lastComments,
            'showComments' => $candidate->exists,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->candidate->exists ? __('candidates.edit') : __('candidates.create_new');
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
         $buttons = [
            Button::make(__('candidates.buttons.create'))
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee(!$this->candidate->exists),

            Button::make(__('candidates.buttons.update'))
                ->icon('pencil')
                ->method('createOrUpdate')
                ->canSee($this->candidate->exists)
        ];

        if (Auth::user()->hasAccess('platform.systems.candidates.delete')){
            $buttons[] = Button::make(__('candidates.buttons.remove'))
                ->icon('trash')
                ->method('remove')
                ->canSee($this->candidate->exists);
        }

        return $buttons;
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return __('candidates.description');
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::columns([
                [Layout::rows([

                        Group::make([
                            Input::make('candidate.name')
                                ->title(__('candidates.fields.name'))
                                ->placeholder(__('candidates.fields.name')),
                            Input::make('candidate.surname')
                                ->title(__('candidates.fields.surname'))
                                ->placeholder(__('candidates.fields.surname'))
                        ]),
                        
                    Group::make([
                        Input::make('candidate.email')
                            ->type('email')
                            ->title(__('candidates.fields.email'))
                            ->placeholder(__('candidates.fields.email'))
                           ,

                        Input::make('candidate.phone')
                            ->type('tel')
                            ->title(__('candidates.fields.phone'))
                            ->placeholder(__('candidates.fields.phone'))
                        ]), 

                           Input::make('candidate.city')
                            ->title(__('candidates.fields.city'))
                            ->placeholder(__('candidates.fields.city'))
                            ,

                        Input::make('candidate.speciality_entered_manually')
                            ->title(__('candidates.fields.speciality_entered_manually'))
                            ->placeholder(__('candidates.fields.speciality_entered_manually'))
                           ,

                        Group::make([
                            CheckBox::make('candidate.locked')
                                ->title(__('candidates.status.locked'))
                                ->placeholder(__('candidates.status.locked'))
                                ,

                            CheckBox::make('candidate.active')
                                ->title(__('candidates.status.active'))
                                ->placeholder(__('candidates.status.active'))
                                ,

                            CheckBox::make('candidate.black_list')
                                ->title(__('candidates.status.black_list'))
                                ->placeholder(__('candidates.status.black_list'))
                                ,

                            Input::make('candidate.lockedByUser.name')
                                ->title(__('candidates.fields.locked_by'))
                                ->disabled()
                                ->canSee($this->candidate->exists && $this->candidate->locked && $this->candidate->lockedByUser)
                                ,
                        ]),

                        Input::make('candidate.user.name')
                            ->title(__('candidates.fields.created_by'))
                            ->disabled()
                            ->canSee($this->candidate->exists && $this->candidate->user),
                    ]),
                    Layout::rows([
                            Relation::make('candidate.specialities')
                                ->multiple()
                                ->fromModel(Speciality::class, 'name')
                                ->title(__('candidates.fields.main_specialities')),

                            Relation::make('candidate.competences')
                                ->multiple()
                                ->fromModel(Competence::class, 'name')
                                ->title(__('candidates.fields.main_competences')),

                            Relation::make('candidate.workedCompanies')
                                ->multiple()
                                ->fromModel(Company::class, 'name', 'company_code')
                                ->title(__('candidates.fields.worked_companies')),
                        ]),
                            $this->candidate->exists 
                        ? Layout::component(\App\View\Components\PreviousCompaniesLink::class) : [],
                ],
                    
                    // Last 3 comments block                    
                    [
                        Layout::rows([
                            TextArea::make('candidate.comment')
                                ->title(__('candidates.fields.comment'))
                                ->rows(12)
                                ->placeholder(__('candidates.fields.comment')),

                            PreviewUpload::make('candidate.attachments')
                                ->title(__('candidates.fields.cv'))
                                ->targetRelation()
                                ->storage('local')
                                ->groups('cv')
                                ->maxFiles(10)
                                ->acceptedFiles('.pdf,.doc,.docx')
                                ->previewModal('viewAttachmentModal')
                                ->downloadRoute('platform.hrm.candidate.download-attachment')
                                ->modelId($this->candidate->id)
                                ->modelIdField('candidate')
                                ->aiRoute('platform.ai.process')
                                ->mapAiFields([
                                    // AIField => OurLocalField
                                    'name' => 'candidate.name',
                                    'surname' => 'candidate.surname',
                                    'city' => 'candidate.city',
                                    'email' => 'candidate.email',
                                    'phone' => 'candidate.phone',
                                    'speciality' => 'candidate.speciality_entered_manually',
                                    'comment' => 'candidate.comment',
                                    'competences' => 'candidate.competences',
                                    'specialities' => 'candidate.specialities',
                                    'previous_companies' => 'candidate.workedCompanies'
                                ])
                                
                        ]),
                        $this->candidate->exists 
                        ? Layout::component(\App\View\Components\LastComments::class) 
                        : []
                        
                    ],
                ]),
            Layout::modal('viewAttachmentModal', [
                Layout::component(\App\View\Components\DocumentView::class),
            ])
                ->title('')
                ->async('asyncViewAttachment')
                ->size(Modal::SIZE_XL),
        ];
    }

    /**
     * Async loader for view modal — returns iframe URL for the attachment
     */
    public function asyncViewAttachment(Candidate $candidate, OrchidAttachment $attachment): iterable
    {
        if (! $candidate->attachments()->where('attachment_id', $attachment->id)->exists()) {
            return ['url' => null];
        }

        $url = route('platform.hrm.candidate.view-attachment', [$candidate->id, $attachment->id]);

        return [
            'url' => $url,
            'name' => $attachment->original_name ?? null,
        ];
    }

    /**
     * Serve candidate attachment inline for viewing (Content-Disposition: inline).
     */
    public function viewAttachment(Candidate $candidate, OrchidAttachment $attachment, Request $request)
    {
        if (! $candidate->attachments()->where('attachment_id', $attachment->id)->exists()) {
            Alert::error('File not found.');
            return back();
        }

        $disk = $attachment->disk ?? 'local';
        $path = $attachment->physicalPath();

        try {
            $absolute = Storage::disk($disk)->path($path);

            return response()->file($absolute, [
                'Content-Disposition' => 'inline; filename="' . ($attachment->original_name ?? 'file') . '"',
            ]);
        } catch (\Exception $e) {
            Alert::error('Unable to open file.');
            return back();
        }
    }

     /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Request $request)
    {
        /*$request->validate([
            'candidate.name' => 'required|string|max:255',
            'candidate.surname' => 'required|string|max:255',
        ]);*/

        $candidateData = $request->get('candidate');
        
        // Set user_id from authenticated user when creating a new candidate
        if (!$this->candidate->exists) {
            $candidateData['user_id'] = auth()->id();
        }
        
        // Convert checkbox value to boolean (checkboxes send 'on' or are not present)
        $isNowLocked = isset($candidateData['locked']) && $candidateData['locked'];
        $candidateData['locked'] = $isNowLocked ? 1 : 0;
        
        // Handle locked_by_user_id logic
        $wasLocked = $this->candidate->locked ?? false;
        
        // If changing from unlocked to locked, set locked_by_user_id
        if (!$wasLocked && $isNowLocked) {
            $candidateData['locked_by_user_id'] = auth()->id();
        }
        
        // If changing from locked to unlocked, clear locked_by_user_id
        if ($wasLocked && !$isNowLocked) {
            $candidateData['locked_by_user_id'] = null;
        }
        
        $candidateData['active'] = isset($candidateData['active']) && $candidateData['active'] ? 1 : 0;
        $candidateData['black_list'] = isset($candidateData['black_list']) && $candidateData['black_list'] ? 1 : 0;

        // Extract specialities, competences and attachment before filling
        $specialities = $candidateData['specialities'] ?? [];
        unset($candidateData['specialities']);
        
        $competences = $candidateData['competences'] ?? [];
        unset($candidateData['competences']);

        $workedCompanies = $candidateData['workedCompanies'] ?? [];
        unset($candidateData['workedCompanies']);
        
        $attachmentIds = $candidateData['attachment'] ?? [];
        unset($candidateData['attachment']);
        
        $this->candidate->fill($candidateData)->save();
        
        // Sync the many-to-many relationships
        $this->candidate->specialities()->sync($specialities);
        $this->candidate->competences()->sync($competences);
        $this->candidate->workedCompanies()->sync($workedCompanies);
        
        // Handle CV attachment - sync with the cv group
        $this->candidate->attachments()->syncWithoutDetaching(
            $request->input('candidate.attachments', [])
        );

        if ($this->candidate->wasRecentlyCreated) {
            Alert::info(__('candidates.alerts.created'));
        } else {
            Alert::info(__('candidates.alerts.updated'));
        }

        return redirect()->route('platform.hrm.candidate.edit', $this->candidate);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        if (! optional(auth()->user())->hasAccess('platform.systems.candidates.delete')) {
            Alert::warning(__('candidates.alerts.no_delete_permission'));
            return;
        }

        $this->candidate->delete();

        Alert::info(__('candidates.alerts.deleted'));

        return redirect()->route('platform.hrm.candidate.list');
    }

    /**
     * Download the CV attachment
     *
     * @param Candidate $candidate
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function downloadCV(Candidate $candidate)
    {
        $attachment = $candidate->attachments()->where('group', 'cv')->first();
        
        if (!$attachment) {
            Alert::error('No CV file found.');
            return back();
        }

        return Storage::disk($attachment->disk)
            ->download($attachment->physicalPath(), $attachment->original_name);
    }


    /**
     * Download a specific attachment for a candidate
     *
     * @param Candidate $candidate
     * @param OrchidAttachment $attachment
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function downloadAttachment(Candidate $candidate, OrchidAttachment $attachment)
    {
        // Ensure the attachment is attached to this candidate
        if (!$candidate->attachments()->where('attachment_id', $attachment->id)->exists()) {
            Alert::error('File not found.');
            return back();
        }

        return $attachment->download();
    }


}
