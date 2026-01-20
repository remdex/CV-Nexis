@push('head')
    <style>
        .ai-processing-container {
            padding: 20px;
        }
        .ai-status {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .ai-status.success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .ai-status.error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .ai-status.processing {
            background-color: #cce5ff;
            border: 1px solid #b8daff;
            color: #004085;
        }
    </style>
@endpush

<div class="ai-processing-container">
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="mb-0">{{ __('AI Document Processing') }}</h4>
        </div>
        <div class="card-body">
            <p class="text-muted">
                {{ __('Use this screen to process and analyze documents with AI. You can trigger AI processing from document screens or use the API endpoints directly.') }}
            </p>
            
            <h5 class="mt-4">{{ __('Available Endpoints') }}</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <strong>POST</strong> <code>{{ route('platform.ai.process') }}</code>
                    <br>
                    <small class="text-muted">{{ __('Process an attachment with AI. Parameters: attachment_id (required), model_type (optional), model_id (optional)') }}</small>
                </li>
                <li class="list-group-item">
                    <strong>POST</strong> <code>{{ route('platform.ai.analyze') }}</code>
                    <br>
                    <small class="text-muted">{{ __('Analyze an attachment with AI. Parameters: attachment_id (required), model_type (optional), model_id (optional)') }}</small>
                </li>
            </ul>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">{{ __('Integration Guide') }}</h4>
        </div>
        <div class="card-body">
            <p>{{ __('To integrate AI processing with your screens, use the following JavaScript example:') }}</p>
            <pre><code>// Using fetch API
fetch('{{ route('platform.ai.process') }}', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        attachment_id: 123,
        model_type: 'App\\Models\\Candidate',
        model_id: 456
    })
})
.then(response => response.json())
.then(data => {
    console.log('AI Result:', data);
});</code></pre>
        </div>
    </div>
</div>
