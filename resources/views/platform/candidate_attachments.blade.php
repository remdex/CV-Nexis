@php
    /**
     * $candidate is provided by the Screen query data
     */
@endphp

@use('Orchid\Screen\Actions\ModalToggle')
@use('Orchid\Screen\Actions\Button')

@if(isset($candidate) && $candidate->attachments && $candidate->attachments->count())
    <div class="form-group">
        <label class="form-label">{{ __('candidates.uploaded_files') }}</label>
        <ul class="list-unstyled">
            @foreach($candidate->attachments as $attachment)
                <li class="mb-1">
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <span style="display:inline-block;vertical-align:middle;">
                            {!! ModalToggle::make(__('documents.view'))
                                ->modal('viewAttachmentModal')
                                ->asyncParameters(['candidate' => $candidate->id, 'attachment' => $attachment->id])
                                ->icon('eye')
                                ->render() !!}
                        </span>

                        <a href="{{ route('platform.hrm.candidate.download-attachment', [$candidate->id, $attachment->id]) }}" class="btn p-0 m-0 align-middle">
                            <span class="icon">🡻</span>
                            {{ $attachment->original_name }}
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endif
