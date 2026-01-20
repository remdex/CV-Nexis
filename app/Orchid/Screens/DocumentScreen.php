<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Actions\Button;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentType;
use App\Orchid\Layouts\DocumentSelection;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Orchid\Attachment\Models\Attachment as OrchidAttachment;
use Orchid\Support\Facades\Alert;
use Illuminate\Validation\Rule;
use Orchid\Screen\Layouts\Modal;

class DocumentScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'documents' => Document::with(['documentType', 'user', 'attachments'])->filters(DocumentSelection::class)->latest()->paginate(),
        ];
    }

    public function name(): ?string
    {
        return __('documents.title');
    }

    public function description(): ?string
    {
        return __('documents.description');
    }

    public function commandBar(): iterable
    {
        return [
            ModalToggle::make(__('documents.add'))
                ->modal('documentModal')
                ->method('create')
                ->icon('plus')
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.documents'
        ];
    }

    public function layout(): iterable
    {
        return [
            DocumentSelection::class,
            Layout::table('documents', [
                TD::make('custom_name', __('documents.fields.custom_name'))
                    ->render(function (Document $doc) {
                        $custom = e($doc->custom_name);

                        $attachment = optional($doc->attachments)->first();

                        if (! $attachment) {
                            return $custom;
                        }

                        $name = $attachment->original_name ?? 'file';
                        $size = $attachment->size ?? $attachment->file_size ?? 0;

                        $sizeText = $this->humanFilesize($size);

                        $url = route('platform.hrm.document.download-attachment', [$doc->id, $attachment->id]);

                        $fileLine = '<div class="text-muted" style="font-size:0.9em"><a class="text-muted" href="' . e($url) . '" target="_blank" rel="noopener noreferrer" download><span class="icon">🡻</span>' . e($name) . ' (' . e($sizeText) . ')</a></div>';

                        return $custom . $fileLine;
                    }),
                TD::make('type', __('documents.fields.type'))->render(function (Document $doc) {
                    return optional($doc->documentType)->name;
                }),
                TD::make('user', __('documents.fields.uploaded_by'))->render(function (Document $doc) {
                    return optional($doc->user)->name;
                }),
                TD::make('created_at', __('documents.fields.created_at'))->render(function (Document $doc) {
                    return $doc->created_at ? $doc->created_at->format('Y-m-d H:i') : '';
                }),
                TD::make('updated_at', __('documents.fields.updated_at'))->render(function (Document $doc) {
                    return $doc->updated_at ? $doc->updated_at->format('Y-m-d H:i') : '';
                }),
                TD::make('Actions', __('documents.title'))
                    ->alignRight()
                    ->render(function (Document $document) {
                        return view('partials.document-actions', ['document' => $document]);
                    }),
            ]),

            Layout::modal('documentModal', Layout::rows([
                Input::make('document.custom_name')->title(__('documents.fields.custom_name')),
                Relation::make('document.document_type_id')->fromModel(DocumentType::class, 'name')->title(__('documents.fields.type')),
                Upload::make('document.attachments')
                    ->title(__('documents.fields.file'))
                    ->targetRelation()
                    ->maxFiles(1)
                    ->storage('local')
                    ->help(__('documents.fields.file_help')),
            ]))
                ->title(__('documents.create'))
                ->applyButton(__('documents.create_button')),

            Layout::modal('editDocumentModal', Layout::rows([
                Input::make('document.custom_name')->title(__('documents.fields.custom_name')),
                Relation::make('document.document_type_id')->fromModel(DocumentType::class, 'name')->title(__('documents.fields.type')),
                Upload::make('document.attachments')
                    ->title(__('documents.fields.file'))
                    ->targetRelation()
                    ->maxFiles(1)
                    ->storage('local')
                    ->help(__('documents.fields.file_help')),
            ]))
                ->title(__('documents.edit_title'))
                ->applyButton(__('documents.update_button'))
                ->async('asyncEditModal'),
        
            // Modal for viewing document inline (PDF iframe)
            Layout::modal('viewDocumentModal', Layout::view('partials.document-view'))
                ->title('')
                ->async('asyncViewModal')
                ->size(Modal::SIZE_XL),
        ];
    }

    public function create(Request $request)
    {
        $request->validate([
            'document.custom_name' => 'nullable|string|max:255',
            'document.document_type_id' => 'nullable|exists:document_types,id',
        ]);

        $data = $request->input('document', []);
        $data['user_id'] = auth()->id();

        $document = Document::create($data);

        // Attach uploaded file if present
        $document->attachments()->syncWithoutDetaching($request->input('document.attachments', []));

        Alert::info(__('documents.alerts.created'));
    }

    /**
     * Prepare data for edit modal
     */
    public function asyncEditModal(Document $document): iterable
    {
        return [
            'document' => $document,
        ];
    }

    /**
     * Update an existing document (metadata and file)
     */
    public function update(Request $request, Document $document)
    {
        $request->validate([
            'document.custom_name' => 'nullable|string|max:255',
            'document.document_type_id' => ['nullable', Rule::exists('document_types', 'id')],
        ]);

        $data = $request->input('document', []);
        $document->fill($data)->save();

        // If attachments provided, replace existing attachments with the provided ones
        if ($request->has('document.attachments')) {
            $document->attachments()->sync($request->input('document.attachments', []));
        }

        Alert::info(__('documents.alerts.updated'));
    }

    /**
     * Convert bytes to a human readable file size.
     */
    private function humanFilesize($bytes): string
    {
        if (empty($bytes) || $bytes == 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log(max($bytes, 1), 1024));
        $size = round($bytes / pow(1024, $i), 2);

        return $size . ' ' . $units[$i];
    }

    /**
     * Download a specific attachment for a document
     */
    public function downloadAttachment(Document $document, OrchidAttachment $attachment)
    {
        if (! $document->attachments()->where('attachment_id', $attachment->id)->exists()) {
            Alert::error('File not found.');
            return back();
        }

        return $attachment->download();
    }

    /**
     * Return data for the view modal (iframe URL).
     */
    public function asyncViewModal(Document $document): iterable
    {
        $attachment = optional($document->attachments)->first();

        if (! $attachment) {
            return ['url' => null];
        }

        $url = route('platform.hrm.document.view-attachment', [$document->id, $attachment->id]);

        return [
            'url' => $url,
            'name' => $attachment->original_name ?? null,
        ];
    }

    /**
     * Serve attachment inline for viewing (Content-Disposition: inline).
     */
    public function viewAttachment(Document $document, OrchidAttachment $attachment, Request $request)
    {
        if (! $document->attachments()->where('attachment_id', $attachment->id)->exists()) {
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

    public function delete(Document $document)
    {
        if (! optional(auth()->user())->hasAccess('platform.systems.documents.delete')) {
            Alert::warning(__('documents.alerts.no_delete_permission'));
            return;
        }

        $document->delete();
        Alert::info(__('documents.alerts.deleted'));
    }
}
