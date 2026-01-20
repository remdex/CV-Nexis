@component($typeForm, get_defined_vars())
    @php
        // Prepare AI configuration early for use in controller attributes
        $modelIdValue = $modelId ?? null;
        $modelIdFieldName = $modelIdField ?? 'candidate';
        $aiRoute = $aiRoute ?? null;
        
        // Field mapping: AI extracted field => form input name
        $rawFieldMapping = $fieldMapping ?? [];
        $aiFieldMapping = [];
        foreach ($rawFieldMapping as $aiField => $formField) {
            // Convert dot notation to bracket notation: candidate.name => candidate[name]
            $parts = explode('.', $formField);
            if (count($parts) > 1) {
                $bracketNotation = array_shift($parts) . '[' . implode('][', $parts) . ']';
                $aiFieldMapping[$aiField] = $bracketNotation;
            } else {
                $aiFieldMapping[$aiField] = $formField;
            }
        }
    @endphp
    
    <div
        data-controller="upload_preview ai"
        data-upload_preview-storage="{{$storage ?? config('platform.attachment.disk', 'public')}}"
        data-upload_preview-name="{{$name}}"
        data-upload_preview-id="dropzone-{{$id}}"
        data-upload_preview-data='@json($value)'
        data-upload_preview-groups="{{$attributes['groups'] ?? ''}}"
        data-upload_preview-multiple="{{$attributes['multiple']}}"
        data-upload_preview-parallel-uploads="{{$parallelUploads }}"
        data-upload_preview-max-file-size="{{$maxFileSize }}"
        data-upload_preview-max-files="{{$maxFiles}}"
        data-upload_preview-timeout="{{$timeOut}}"
        data-upload_preview-accepted-files="{{$acceptedFiles }}"
        data-upload_preview-resize-quality="{{$resizeQuality }}"
        data-upload_preview-resize-width="{{$resizeWidth }}"
        data-upload_preview-is-media-library="{{ $media }}"
        data-upload_preview-close-on-add="{{ $closeOnAdd }}"
        data-upload_preview-resize-height="{{$resizeHeight }}"
        data-upload_preview-path="{{ $attributes['path'] ?? '' }}"
        data-ai-url-value="{{ $aiRoute ? route($aiRoute) : route('platform.ai.process') }}"
        data-ai-model-type-value="{{ $modelIdFieldName }}"
        data-ai-model-id-value="{{ $modelIdValue ?? '' }}"
        data-ai-field-mapping-value='@json($aiFieldMapping)'
    >
        <div id="dropzone-{{$id}}" class="dropzone-wrapper">
            <div class="fallback">
                <input type="file" value="" multiple/>
            </div>
            <div class="visual-dropzone sortable-dropzone dropzone-previews {{ ($attributes['showInline'] ?? true) ? 'd-flex flex-wrap gap-2' : '' }}">
                <div class="dz-message dz-preview dz-processing dz-image-preview">
                    <div class="bg-light d-flex justify-content-center align-items-center border r-2x"
                         style="min-height: 112px;">
                        <div class="px-2 py-4">
                            <x-orchid-icon path="bs.cloud-arrow-up" class="h3"/>
                            <small class="text-muted d-block mt-1">{{__('Upload file')}}</small>
                        </div>
                    </div>
                </div>

                @if($media)
                    <div class="dz-message dz-preview dz-processing dz-image-preview"
                         data-action="click->upload#openMedia">
                        <div class="bg-light d-flex justify-content-center align-items-center border r-2x"
                             style="min-height: 112px;">
                            <div class="px-2 py-4">
                                <x-orchid-icon path="bs.collection" class="h3"/>

                                <small class="text-muted d-block mt-1">{{__('Media catalog')}}</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="attachment modal fade center-scale" tabindex="-1" role="dialog" aria-hidden="false">
                <div class="modal-dialog modal-fullscreen-md-down">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title text-body-emphasis fw-light">
                                {{__('File Information')}}
                                <small class="text-muted d-block">{{__('Information to display')}}</small>
                            </h4>

                            <button type="button" class="btn-close" title="Close" data-bs-dismiss="modal"
                                    aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="form-group">
                                <label>{{__('System name')}}</label>
                                <input type="text" class="form-control" data-upload_preview-target="name" readonly
                                       maxlength="255">
                            </div>
                            <div class="form-group">
                                <label>{{ __('Display name') }}</label>
                                <input type="text" class="form-control" data-upload_preview-target="original"
                                       maxlength="255" placeholder="{{ __('Display name') }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __('Alternative text') }}</label>
                                <input type="text" class="form-control" data-upload_preview-target="alt"
                                       maxlength="255" placeholder="{{  __('Alternative text')  }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __('Description') }}</label>
                                <textarea class="form-control no-resize"
                                          data-upload_preview-target="description"
                                          placeholder="{{ __('Description') }}"
                                          maxlength="255"
                                          rows="3"></textarea>
                            </div>


                            @if($visibility === 'public')
                                <div class="form-group">
                                    <a href="#" data-action="click->upload#openLink">
                                        <small>
                                            <x-orchid-icon path="bs.share" class="me-2"/>

                                            {{ __('Link to file') }}
                                        </small>
                                    </a>
                                </div>
                            @endif


                        </div>
                        <div class="modal-footer">
                            <button type="button"
                                    data-bs-dismiss="modal"
                                    class="btn btn-link">
                                    <span>
                                        {{__('Close')}}
                                    </span>
                            </button>
                            <button type="button" data-action="click->upload#save" class="btn btn-default">
                                {{__('Apply')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @if($media)
                <div class="media modal fade enter-scale disable-scroll" tabindex="-1" role="dialog"
                     aria-hidden="false">
                    <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-md-down slide-up">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title text-body-emphasis fw-light">
                                    {{__('Media Library')}}
                                    <small class="text-muted d-block">{{__('Previously uploaded files')}}</small>
                                </h4>
                                <button type="button" class="btn-close" title="Close" data-bs-dismiss="modal"
                                        aria-label="Close">
                                </button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row justify-content-center">

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{__('Search file')}}</label>
                                            <input type="search"
                                                   data-upload_preview-target="search"
                                                   data-action="keydown->upload#resetPage keydown->upload#loadMedia"
                                                   class="form-control"
                                                   placeholder="{{ __('Search...') }}"
                                            >
                                        </div>

                                        <div class="media-loader spinner-border" role="status">
                                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                                        </div>

                                        <div class="row media-results m-0"></div>

                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-link d-block w-100"
                                                    data-upload_preview-target="loadmore"
                                                    data-action="click->upload#loadMore">{{ __('Load more') }}</button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <template id="dropzone-{{$id}}-media">
                    <div class="col-4 col-sm-3 my-3 position-relative media-item">
                      <div data-action="click->upload#addFile" data-key="{index}">
                         <img src="{element.url}" class="rounded mw-100" style="height: 50px;width: 100%;object-fit: cover;">
                          <p class="text-ellipsis small text-muted mt-1 mb-0" title="{element.original_name}">{element.original_name}</p>
                        </div>
                      </div>
                </template>
            @endif


            <template id="dropzone-{{$id}}-remove-button">
                <a href="javascript:;" class="btn-remove">&times;</a>
            </template>

            <template id="dropzone-{{$id}}-edit-button">
                <a href="javascript:;" class="btn-edit">
                    <x-orchid-icon path="bs.card-text" class="mb-1"/>
                </a>
            </template>
            
            <template id="dropzone-{{$id}}-ai-button">
                <a href="javascript:;" 
                                 class="btn btn-ai text-info p-0"
                           title="{{ __('documents.ai_process') }}"
                           style="font-size: 1rem;">
                            <x-orchid-icon path="bs.star"/>&nbsp;{{ __('documents.ai') }}
                        </a>
            </template>

            @if(!empty($attributes['previewModal']))
                <template id="dropzone-{{$id}}-preview-button">
                    <a href="javascript:;" class="btn-preview" title="{{ __('documents.view') }}">
                        <x-orchid-icon path="bs.eye" class="mb-1"/>
                    </a>
                </template>
            @endif
            
            <template id="dropzone-{{$id}}-duplicate-alert">
                <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                    <strong><x-orchid-icon path="bs.exclamation-triangle-fill" class="me-2"/>{{ __('hrm.duplicate_candidates_found') }}</strong>
                    <p class="mb-2 mt-2">{{ __('hrm.duplicate_candidates_message') }}</p>
                    <ul class="mb-0 list-unstyled" data-duplicate-list></ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
                </div>
            </template>

            <template id="dropzone-{{$id}}-duplicate-item">
                <li class="mb-1"><x-orchid-icon path="bs.pencil" class="me-2"/>
                    <a href="#" target="_blank" class="text-danger fw-bold" data-candidate-edit-url data-candidate-name></a>
                    <span class="text-muted small" data-candidate-meta></span>
                </li>
            </template>

        </div>
        
        {{-- Container for duplicate candidates alert --}}
        <div class="duplicate-candidates-alert" data-upload-id="{{ $id }}"></div>
    </div>


    {{-- Display uploaded files inline with preview action --}}
    @php
        // Additional attributes for file list display
        $previewModalName = $previewModal ?? null;
        $downloadRouteName = $downloadRoute ?? null;
        $uploadedFiles = collect($value)->filter(fn($item) => isset($item['id']));
    @endphp

    @if($previewModalName && $modelIdValue)
        <div class="uploaded-files-preview mt-3"
             data-controller="ai"
             data-ai-url-value="{{ $aiRoute ? route($aiRoute) : route('platform.ai.process') }}"
             data-ai-model-type-value="{{ $modelIdFieldName }}"
             data-ai-model-id-value="{{ $modelIdValue }}"
             data-ai-field-mapping-value='@json($aiFieldMapping)'>
            <div class="d-flex flex-column gap-2">
                @foreach($uploadedFiles as $attachment)
                    <div class="uploaded-file-item d-flex align-items-center justify-content-between gap-1 border rounded px-2 py-1 bg-light files-ai-{{ $attachment['id'] }}"
                         data-file-row-id="{{ $attachment['id'] }}">
                        <div class="d-inline-flex align-items-center gap-1">
                            @if($previewModalName)
                                {!! \Orchid\Screen\Actions\ModalToggle::make('')
                                    ->modal($previewModalName)
                                    ->asyncParameters([$modelIdFieldName => $modelIdValue, 'attachment' => $attachment['id']])
                                    ->icon('eye')
                                    ->class('btn btn-sm btn-link p-0')
                                    ->render() !!}
                            @endif

                            @if($downloadRouteName)
                                <a href="{{ route($downloadRouteName, [$modelIdValue, $attachment['id']]) }}" 
                                   class="btn btn-sm btn-link p-0 text-decoration-none" 
                                   title="{{ __('Download') }}">
                                    <x-orchid-icon path="bs.download" class="me-1"/>
                                    <span>{{ $attachment['original_name'] ?? $attachment['name'] ?? 'File' }}</span>
                                </a>
                            @else
                                <span>{{ $attachment['original_name'] ?? $attachment['name'] ?? 'File' }}</span>
                            @endif
                        </div>
                        
                        <a href="javascript:;" 
                           class="btn text-info p-0"
                           data-action="click->ai#process"
                           data-ai-attachment-id-param="{{ $attachment['id'] }}"
                           title="{{ __('documents.ai_process') }}"
                           style="font-size: 1rem;">
                            <x-orchid-icon path="bs.star"/>&nbsp;{{ __('documents.ai') }}
                        </a>

                    </div>
                @endforeach
            </div>
        </div>
        
    @endif
@endcomponent
