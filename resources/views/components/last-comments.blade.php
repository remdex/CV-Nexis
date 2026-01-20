<div class="bg-white rounded shadow-sm p-4 mb-3">
    <h5 class="mb-3">
        <x-orchid-icon path="bs.chat-dots" class="me-2"/>
        {{ __('candidates.comments.last_comments') }}
    </h5>
    
    @if($comments->isEmpty())
        <p class="text-muted mb-3">{{ __('candidates.comments.no_comments') }}</p>
    @else
        <div class="list-group list-group-flush mb-3">
            @foreach($comments as $comment)
                <div class="list-group-item px-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="mb-1">{{ Str::limit($comment->comment, 100) }}</p>
                            <small class="text-muted">
                                {{ $comment->user->name ?? __('candidates.comments.unknown_user') }} 
                                &bull; 
                                {{ $comment->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    
    @if($candidate && $candidate->exists)
        <a href="{{ route('platform.hrm.candidate.comments', $candidate) }}" class="btn btn-sm btn-link p-0">
            <x-orchid-icon path="bs.arrow-right" class="me-1"/>
            {{ __('candidates.comments.view_all') }}
        </a>
    @endif
</div>
