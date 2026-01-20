<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Orchid\Attachment\Models\Attachment;
use Orchid\Platform\Dashboard;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Alert;

/**
 * Screen for handling AI processing requests.
 * 
 * This screen processes attachments with AI and provides methods
 * that can be called via AJAX from the Orchid dashboard.
 */
class AiScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('AI Processing');
    }

    /**
     * The description is displayed on the user's screen under the heading.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return __('AI-powered document analysis and processing');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::view('orchid.ai-processing'),
        ];
    }

    /**
     * Process an attachment with AI.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function process(Request $request): JsonResponse
    {
        $request->validate([
            'attachment_id' => 'required|integer',
            'model_type' => 'nullable|string',
            'model_id' => 'nullable|integer',
        ]);

        try {
            $attachmentId = $request->input('attachment_id');
            $modelType = $request->input('model_type');
            $modelId = $request->input('model_id');

            // Get the attachment model class from Dashboard
            /** @var Attachment $attachmentClass */
            $attachmentClass = Dashboard::model(Attachment::class);
            
            /** @var Attachment|null $attachment */
            $attachment = $attachmentClass::find($attachmentId);

            if (!$attachment) {
                return response()->json([
                    'success' => false,
                    'message' => __('Attachment not found'),
                    'attachment_id' => $attachmentId,
                ], 404);
            }

            // Process the attachment with AI (records timing information)
            $result = $this->processWithAi($attachment, $modelType, $modelId);

            // Build success message with token usage and timing info
            $tokenUsage = $result['ai_result']['token_usage'] ?? [];
            $totalTokens = $tokenUsage['total_tokens'] ?? 0;
            $message = __('hrm.ai_processing_completed');
            if ($totalTokens > 0) {
                $message .= ' ' . __('hrm.tokens_used', ['total' => $totalTokens]);
            }

            // Append timing details if available (milliseconds)
            $timings = $result['timings'] ?? [];
            $aiMs = isset($timings['ai_request_ms']) ? $timings['ai_request_ms'] : null;
            $dupMs = isset($timings['duplicate_search_ms']) ? $timings['duplicate_search_ms'] : null;

            if ($aiMs !== null || $dupMs !== null) {
                $aiText = $aiMs !== null ? sprintf('%.3f s', $aiMs / 1000) : __('hrm.n_a');
                $dupText = $dupMs !== null ? sprintf('%.3f s', $dupMs / 1000) : __('hrm.n_a');
                $message .= ' ' . __('hrm.ai_timing', ['ai' => $aiText, 'dup' => $dupText]);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'attachment_id' => $attachmentId,
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $this->maskSensitive($e->getMessage()),
                'attachment_id' => $request->input('attachment_id'),
            ], 500);
        }
    }

    /**
     * Analyze an attachment with AI.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function analyze(Request $request): JsonResponse
    {
        $request->validate([
            'attachment_id' => 'required|integer',
            'model_type' => 'nullable|string',
            'model_id' => 'nullable|integer',
        ]);

        try {
            $attachmentId = $request->input('attachment_id');
            
            /** @var Attachment $attachmentClass */
            $attachmentClass = Dashboard::model(Attachment::class);
            
            /** @var Attachment|null $attachment */
            $attachment = $attachmentClass::find($attachmentId);

            if (!$attachment) {
                return response()->json([
                    'success' => false,
                    'message' => __('Attachment not found'),
                ], 404);
            }

            // Perform AI analysis
            $analysis = $this->analyzeWithAi($attachment);

            return response()->json([
                'success' => true,
                'message' => __('Analysis completed'),
                'attachment_id' => $attachmentId,
                'data' => $analysis,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $this->maskSensitive($e->getMessage()),
            ], 500);
        }
    }

    /**
     * Process the attachment with AI using Gemini API.
     *
     * @param Attachment $attachment
     * @param string|null $modelType
     * @param int|null $modelId
     * @return array
     * @throws \Exception
     */
    protected function processWithAi(Attachment $attachment, ?string $modelType, ?int $modelId): array
    {
        $apiKey = config('services.gemini.api_key');
        
        if (empty($apiKey)) {
            throw new \Exception(__('Gemini API key is not configured'));
        }

        // Read file content from attachment
        $fileContent = $this->getAttachmentContent($attachment);
        
        // Call Gemini API to extract person information and measure duration
        $startAi = microtime(true);
        $aiResult = $this->callGeminiApi($fileContent, $attachment->mime, $apiKey);
        $aiDurationMs = (int) round((microtime(true) - $startAi) * 1000);

        /*$aiResult = json_decode('{
            "status": "processed",
            "extracted_data": {
                "phone": "+37060685404",
                "name": "Saulius",
                "surname": "Dauk\u0161a",
                "city": "\u0160iauliai",
                "comment": "Patyr\u0119s CNC stakli\u0173 ir lazerio operatorius, turintis ilgamet\u0119 patirt\u012f med\u017eio bei metalo apdirbimo srityse, gebantis dirbti su \u012fvairiomis stakl\u0117mis bei programavimo \u012franga.",
                "competences": [
                    3,
                    4,
                    5,
                    8
                ],
                "email": "Sauliusd753@gmail.com",
                "speciality": "Lazerio operatorius"
            },
            "token_usage": {
                "prompt_tokens": 1091,
                "completion_tokens": 140,
                "total_tokens": 2090
            }
        }',true);*/

        // Check for duplicate candidates if this is a new candidate (no modelId)
        $duplicateCandidates = [];
        $duplicateSearchMs = null;
        if (empty($modelId) || $modelId == 0) {
            $startDup = microtime(true);
            $duplicateCandidates = $this->findDuplicateCandidates($aiResult['extracted_data'] ?? []);
            $duplicateSearchMs = (int) round((microtime(true) - $startDup) * 1000);
        }

        return [
            'id' => $attachment->id,
            'name' => $attachment->name,
            'original_name' => $attachment->original_name,
            'mime' => $attachment->mime,
            'size' => $attachment->size,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'processed_at' => now()->toIso8601String(),
            'ai_result' => $aiResult,
            'duplicate_candidates' => $duplicateCandidates,
            'timings' => [
                'ai_request_ms' => $aiDurationMs ?? null,
                'duplicate_search_ms' => $duplicateSearchMs,
            ],
        ];
    }

    /**
     * Get content from an attachment.
     *
     * @param Attachment $attachment
     * @return string
     * @throws \Exception
     */
    protected function getAttachmentContent(Attachment $attachment): string
    {
        $disk = $attachment->disk ?? 'local';
        $path = $attachment->physicalPath();

        // Resolve absolute path using the configured storage disk (mirrors CandidateEditScreen)
        try {
            $absolute = \Illuminate\Support\Facades\Storage::disk($disk)->path($path);
        } catch (\Exception $e) {
            throw new \Exception(__('Attachment file not found'));
        }

        if (!file_exists($absolute)) {
            throw new \Exception(__('Attachment file not found'));
        }

        return file_get_contents($absolute);
    }

    /**
     * Call the Gemini API to extract person information.
     *
     * @param string $content
     * @param string $mimeType
     * @param string $apiKey
     * @return array
     * @throws \Exception
     */
    protected function callGeminiApi(string $content, string $mimeType, string $apiKey): array
    {
        $modelId = config('services.gemini.model', 'gemini-3-flash-preview');
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$modelId}:generateContent";

        // Load competence names to include as enum suggestions for the AI schema
        $competences = \App\Models\Competence::orderBy('name')->pluck('name')->toArray();
        $specialities = \App\Models\Speciality::orderBy('name')->pluck('name')->toArray();

        $payload = [
            "system_instruction" => [
                "parts" => [
                    "text" => "Your job is to parse uploaded persons CV. If field is not found just return empty string as attribute value."
                ]
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        [
                            'text' => "Extract person information from the following document:",
                        ],
                        [
                            'inlineData' => [
                                'mimeType' => $mimeType,
                                'data' => base64_encode($content),
                            ],
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'responseSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'email' => [
                            'type' => 'string',
                            'description' => 'Person e-mail found in a file',
                        ],
                        'phone' => [
                            'type' => 'string',
                            'description' => 'Person phone found in a file without spaces. Phone number starting with 8 should be replaced with +370.',
                        ],
                        'name' => [
                            'type' => 'string',
                            'description' => 'Person name found in a file. Only first letter uppercase.',
                        ],
                        'surname' => [
                            'type' => 'string',
                            'description' => 'Person surname found in a file. Only first letter uppercase.',
                        ],
                        'city' => [
                            'type' => 'string',
                            'description' => 'Person present city. Maximum two words',
                        ],
                        'speciality' => [
                            'type' => 'string',
                            'description' => 'Person speciality, max 3 words. The most recent one. Please respond in `'. app()->getLocale().'` language.',
                        ],
                        'comment' => [
                            'type' => 'string',
                            'description' => 'Evaluate person experience and work summary in one or few sentences. Please respond in `'. app()->getLocale().'` language.',
                            //'description' => 'Prepare CV summary for recruiter. It will be used as candidate representation for the company. Upto 8 - 10 sentences.',
                        ],
                        "previous_companies" => [
                            "type" => "array",
                            'description' => 'List of companies names where candidate has previously worked. Make sure to quote company name. E.g UAB/Ltd etc. followed by quoted "company name". Order from newest to oldest.',
                            "items" => [
                                "type" => "string"
                            ]
                        ],
                        "competences" => [
                            "type" => "array",
                            'description' => 'Person skills, competences. One skill, competence in one or max two words.',
                            "items" => [
                                "type" => "string",
                                "enum" => $competences,
                            ]
                        ],
                        "specialities" => [
                            "type" => "array",
                            'description' => 'Person specialities, positions which he took over his career. Up to 4, prioritize the most recent job roles.',
                            "items" => [
                                "type" => "string",
                                "enum" => $specialities,
                            ]
                        ]
                    ],
                    'required' => ['phone', 'name', 'surname'],
                ],
            ],
        ];

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("{$endpoint}?key={$apiKey}", $payload);

        if (!$response->successful()) {
            $body = $response->body();
            $safe = $this->maskSensitive($body);
            throw new \Exception(__('Gemini API request failed: :message', [
                'message' => $safe,
            ]));
        }

        $data = $response->json();

        // Extract token usage from Gemini response
        $usageMetadata = $data['usageMetadata'] ?? [];
        $promptTokens = $usageMetadata['promptTokenCount'] ?? 0;
        $candidatesTokens = $usageMetadata['candidatesTokenCount'] ?? 0;
        $totalTokens = $usageMetadata['totalTokenCount'] ?? 0;

        // Extract the generated content from Gemini response
        $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$generatedText) {
            throw new \Exception(__('No content generated by Gemini API'));
        }

        // Parse the JSON response from Gemini
        $result = json_decode($generatedText, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(__('Failed to parse Gemini API response'));
        }

        // Map competence names to their model IDs and names
        if (isset($result['competences']) && is_array($result['competences'])) {
            $competenceNames = $result['competences'];
            $competences = \App\Models\Competence::whereIn('name', $competenceNames)
                ->get(['id', 'name'])
                ->map(fn($c) => ['id' => $c->id, 'name' => $c->name])
                ->toArray();
            $result['competences'] = $competences;
        }

        // Map competence names to their model IDs and names
        if (isset($result['specialities']) && is_array($result['specialities'])) {
            $specialitiesNames = $result['specialities'];
            $specialities = \App\Models\Speciality::whereIn('name', $specialitiesNames)
                ->get(['id', 'name'])
                ->map(fn($c) => ['id' => $c->id, 'name' => $c->name])
                ->toArray();
            $result['specialities'] = $specialities;
        }
        
        // Map previous company names to company_code values for workedCompanies relation
        if (isset($result['previous_companies']) && is_array($result['previous_companies'])) {
            $result['previous_companies_original'] = $result['previous_companies'];

            $companyNames = collect($result['previous_companies'])
                ->filter(fn($n) => is_string($n) && trim($n) !== '')
                ->map(fn($n) => trim($n))
                ->unique()
                ->values();

            if ($companyNames->isNotEmpty()) {
                $companiesQuery = \App\Models\Company::query();
                $companiesQuery->where(function ($q) use ($companyNames) {
                    foreach ($companyNames as $nm) {
                        // Try exact first
                        $q->orWhere('name', $nm);

                                // Generate and search additional common legal-form variations
                                $variants = $this->generateCompanyNameVariants($nm);
                                foreach ($variants as $variant) {
                                    $q->orWhere('name', $variant);
                                }
                        
                    }
                });

                $companies = $companiesQuery->get(['company_code', 'name']);
                // Build a unified structure identical to specialities: [{id, name}]
                // Here, id equals company_code to match the Relation's value key
                $companyPairs = $companies
                    ->map(fn($c) => ['id' => (string) $c->company_code, 'name' => $c->name])
                    ->unique('id')
                    ->values()
                    ->all();

                $result['previous_companies'] = $companyPairs;

                // Build a single link containing comma-separated company_code values
                $codes = collect($companyPairs)->pluck('id')->unique()->values()->all();
                $codesParam = implode(',', $codes);
                $url = route('platform.hrm.company.list', ['company_code' => $codesParam]);
                $result['previous_companies_url'] = '<a href="' . e($url) . '" target="_blank" rel="noopener noreferrer">🔗 ' . __('companies.fields.view_details') . '</a>';

            } else {
                $result['previous_companies'] = [];
            }
        }


        return [
            'status' => 'processed',
            'extracted_data' => $result,
            'token_usage' => [
                'prompt_tokens' => $promptTokens,
                'completion_tokens' => $candidatesTokens,
                'total_tokens' => $totalTokens,
            ],
        ];
    }

    /**
     * Mask sensitive values (API keys, key= query params) in text shown to users.
     *
     * @param string|null $text
     * @return string
     */
    protected function maskSensitive(?string $text): string
    {
        if (empty($text)) {
            return (string) $text;
        }

        // Mask configured Gemini API key if present
        try {
            $apiKey = config('services.gemini.api_key');
        } catch (\Exception $ex) {
            $apiKey = null;
        }

        if (!empty($apiKey)) {
            $text = str_replace($apiKey, '***', $text);
        }

        // Mask any URL query param 'key' occurrences (e.g. ?key=... or &key=...)
        $text = preg_replace('/([?&]key=)[^&\s\"\']+/i', '$1***', $text);

        return $text;
    }

    /**
     * Normalize a raw company string to a core company name without legal forms/quotes.
     */
    protected function normalizeCompanyBaseName(string $name): string
    {
        $s = trim($name);

        // Normalize quotes to standard double quotes
        $s = str_replace(["„", "“", "”", "ʼ", "'"], '"', $s);
        $s = preg_replace('/\s+/', ' ', $s ?? '') ?? '';

        // If quoted, prefer the quoted inner content as the base
        if (preg_match('/"\s*([^"\\\\]+?)\s*"/u', $s, $m)) {
            $s = $m[1];
        }

        // Remove common legal prefixes
        $legalPrefix = '(UAB|MB|AB|VŠĮ|VŠI|IĮ|II|Viešoji\s+įstaiga|Uždaroji\s+akcinė\s+bendrovė|Mažoji\s+bendrija|Akcinė\s+bendrovė)';
        $s = preg_replace('/^\s*' . $legalPrefix . '\.?\s*/iu', '', $s);

        // Remove common legal suffixes (with optional preceding comma)
        $legalSuffix = '(UAB|MB|AB|VŠĮ|VŠI|IĮ)';
        $s = preg_replace('/\s*,?\s*' . $legalSuffix . '\.?\s*$/iu', '', $s);

        // Trim quotes and extra spaces
        $s = trim($s, " \t\n\r\0\x0B\"");
        $s = preg_replace('/\s+/', ' ', $s);

        // Title-case core words (best-effort, keeps diacritics)
        $s = mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');

        return $s;
    }

    /**
     * Build a set of likely official-name variants for searching.
     * Examples:
     * - UAB "Company Name"
     * - "Company Name", UAB
     * - Mažoji Bendrija "Company Name"
     * - Uždaroji Akcinė Bendrovė "Company Name"
     * - Company Name, MB
     * Also includes the cleaned base name.
     */
    protected function generateCompanyNameVariants(string $original): array
    {
        $base = $this->normalizeCompanyBaseName($original);
        if ($base === '') {
            return [];
        }

        // Prefer keeping original inner-case if the original already had quoted name
        if (preg_match('/"\s*([^"\\\\]+?)\s*"/u', $original, $m)) {
            $inner = trim($m[1]);
            if ($inner !== '') {
                $base = $inner;
            }
        }

        // Ensure base has single spaces
        $base = preg_replace('/\s+/', ' ', $base);

        $forms = [
            // abbreviation => full form
            ['abbr' => 'UAB', 'full' => 'Uždaroji Akcinė Bendrovė'],
            ['abbr' => 'MB',  'full' => 'Mažoji Bendrija'],
            // Common extras (kept minimal to avoid over-broad matches)
            ['abbr' => 'AB',  'full' => 'Akcinė Bendrovė'],
        ];

        $quoted = '"' . $base . '"';

        $variants = [
            // Base forms
            $base,
            $quoted,
        ];

        foreach ($forms as $f) {
            $abbr = $f['abbr'];
            $full = $f['full'];

            // Prefix with quotes
            $variants[] = $abbr . ' ' . $quoted;
            $variants[] = $full . ' ' . $quoted;

            // Suffix with comma and quotes
            $variants[] = $quoted . ', ' . $abbr;
            $variants[] = $quoted . ', ' . $full;

            // Prefix without quotes
            $variants[] = $abbr . ' ' . $base;
            $variants[] = $full . ' ' . $base;

            // Suffix without quotes
            $variants[] = $base . ' ' . $abbr;
            $variants[] = $base . ', ' . $abbr;
        }

        // Always include original exactly as provided (exact match already added outside, but safe)
        $variants[] = trim($original);

        // Deduplicate and return
        $variants = array_values(array_unique(array_filter(array_map(function ($v) {
            // Normalize inner spacing and trim
            $v = preg_replace('/\s+/', ' ', (string) $v);
            return trim($v);
        }, $variants))));

        return $variants;
    }

    /**
     * Analyze the attachment with AI.
     * Override this method or extend the screen for custom AI analysis.
     *
     * @param Attachment $attachment
     * @return array
     */
    protected function analyzeWithAi(Attachment $attachment): array
    {
        // TODO: Implement your AI analysis logic here
        // This is a placeholder that returns basic analysis
        
        return [
            'id' => $attachment->id,
            'type' => $attachment->mime,
            'size' => $attachment->size,
            'analyzed_at' => now()->toIso8601String(),
            // Add your AI analysis results here
            'analysis' => [
                'category' => 'document',
                'keywords' => [],
                'summary' => null,
            ],
        ];
    }

    /**
     * Find duplicate candidates based on extracted AI data.
     * Checks by phone, email, or name+surname combination.
     *
     * @param array $extractedData
     * @return array
     */
    protected function findDuplicateCandidates(array $extractedData): array
    {
        if (empty($extractedData)) {
            return [];
        }

        $phone = $extractedData['phone'] ?? null;
        $email = $extractedData['email'] ?? null;
        $name = $extractedData['name'] ?? null;
        $surname = $extractedData['surname'] ?? null;

        $duplicates = collect();

        // Check by phone (normalize phone for comparison)
        if (!empty($phone)) {
            $normalizedPhone = preg_replace('/[^0-9+]/', '', $phone);
            $candidates = \App\Models\Candidate::where(function ($query) use ($phone, $normalizedPhone) {
                $query->where('phone', 'LIKE', '%' . substr($normalizedPhone, -9) . '%');
            })->get();
            $duplicates = $duplicates->merge($candidates);
        }

        // Check by email
        if (!empty($email)) {
            $candidates = \App\Models\Candidate::where('email', 'LIKE', $email)->get();
            $duplicates = $duplicates->merge($candidates);
        }

        // Check by name + surname combination
        if (!empty($name) && !empty($surname)) {
            $candidates = \App\Models\Candidate::where(function ($query) use ($name, $surname) {
                $query->where('name', 'LIKE', $name)
                      ->where('surname', 'LIKE', $surname);
            })->get();
            $duplicates = $duplicates->merge($candidates);
        }

        // Remove duplicates and format for response
        return $duplicates->unique('id')->map(function ($candidate) use ($phone, $email, $name, $surname) {
            $matchedFields = [];
            
            if (!empty($phone) && $candidate->phone) {
                $normalizedPhone = preg_replace('/[^0-9+]/', '', $phone);
                $candidatePhone = preg_replace('/[^0-9+]/', '', $candidate->phone);
                if (str_contains($candidatePhone, substr($normalizedPhone, -9))) {
                    $matchedFields[] = __('candidates.fields.phone');
                }
            }
            
            if (!empty($email) && strcasecmp($candidate->email, $email) === 0) {
                $matchedFields[] = __('candidates.fields.email');
            }
            
            if (!empty($name) && !empty($surname) && 
                strcasecmp($candidate->name, $name) === 0 && 
                strcasecmp($candidate->surname, $surname) === 0) {
                $matchedFields[] = __('candidates.fields.name_surname');
            }

            return [
                'id' => $candidate->id,
                'name' => $candidate->name,
                'surname' => $candidate->surname,
                'email' => $candidate->email,
                'phone' => $candidate->phone,
                'matched_fields' => $matchedFields,
                'edit_url' => route('platform.hrm.candidate.edit', $candidate->id),
            ];
        })->values()->toArray();
    }
}
