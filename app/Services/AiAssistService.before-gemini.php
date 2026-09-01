<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * AI-assisted visitor management service.
 *
 * This service provides suggestions only.
 * It never automatically approves, rejects, checks in,
 * or grants access to a visitor.
 *
 * All AI results require human review and confirmation.
 */
class AiAssistService
{
    private const ALLOWED_ROLES = [
        'receptionist',
        'admin_officer',
        'manager',
        'sys_admin',
    ];

    private const VISITOR_TYPES = [
        'customer',
        'business_partner',
        'supplier',
        'government',
        'applicant',
        'guest',
        'other',
    ];

    public function __construct(
        private AuditService $audit
    ) {}

    public function assist(
        User $user,
        string $mode,
        string $text,
        array $context = []
    ): array {
        if (! $user->hasRole(self::ALLOWED_ROLES)) {
            throw ValidationException::withMessages([
                'app_role' => 'AI visitor assistance is limited to reception and administration.',
            ])->status(403);
        }

        $suggestion = match ($mode) {
            'classify', 'extract' => $this->classify($text, $context),
            'summary' => $this->summarize($text, $context),
            'appointment_check' => $this->appointmentCheck($text, $context),

            default => throw ValidationException::withMessages([
                'mode' => 'Unknown assistance mode.',
            ]),
        };

        $this->audit->log(
            $user,
            'ai_assist',
            'visitors',
            "AI assistance ({$mode})",
            null,
            'Suggestion generated for human review - not applied automatically.'
        );

        return [
            'mode' => $mode,
            'suggestion' => $suggestion,
        ];
    }

    /**
     * Send a request to the configured AI endpoint.
     *
     * If no provider is configured, the endpoint is unavailable,
     * or the request fails, the service safely falls back to
     * rule-based/manual-review suggestions.
     */
    private function llm(string $prompt, array $schema): array
    {
        $provider = config('services.ai_assist.provider', 'none');
        $apiKey = config('services.ai_assist.api_key');
        $endpoint = config('services.ai_assist.endpoint');
        $model = config('services.ai_assist.model');

        if (
            $provider === 'none' ||
            ! $apiKey ||
            ! $endpoint
        ) {
            return [
                '_fallback' => true,
            ];
        }

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(30)
                ->post($endpoint, [
                    'model' => $model,
                    'prompt' => $prompt,
                    'response_json_schema' => $schema,
                ]);

            if (! $response->successful()) {
                return [
                    '_fallback' => true,
                ];
            }

            $result = $response->json();

            return is_array($result)
                ? $result
                : ['_fallback' => true];

        } catch (Throwable $e) {
            report($e);

            return [
                '_fallback' => true,
            ];
        }
    }

    private function classify(string $text, array $context): array
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'full_name' => ['type' => 'string'],
                'organization' => ['type' => 'string'],
                'contact_number' => ['type' => 'string'],
                'email' => ['type' => 'string'],
                'visitor_type' => [
                    'type' => 'string',
                    'enum' => self::VISITOR_TYPES,
                ],
                'purpose' => ['type' => 'string'],
                'confidence' => ['type' => 'string'],
                'reasoning' => ['type' => 'string'],
                'missing_fields' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
            ],
        ];

        $prompt = <<<PROMPT
You are assisting reception staff with visitor registration.

Analyze the visitor information below and suggest structured fields.

Visitor information:
{$text}

Additional context:
{$this->contextToText($context)}

Allowed visitor types:
customer, business_partner, supplier, government, applicant, guest, other

Important:
- Provide suggestions only.
- Do not approve or deny access.
- Do not invent information that is not supported by the input.
- Clearly identify missing fields.
PROMPT;

        $result = $this->llm($prompt, $schema);

        if (! ($result['_fallback'] ?? false)) {
            return $result;
        }

        return [
            'full_name' => '',
            'organization' => '',
            'contact_number' => '',
            'email' => '',
            'visitor_type' => 'guest',
            'purpose' => trim($text) ?: 'Not specified',
            'confidence' => 'low',
            'reasoning' => 'AI provider not configured - staff should review and complete the visitor information manually.',
            'missing_fields' => [
                'full_name',
                'organization',
                'contact_number',
                'email',
            ],
        ];
    }

    private function summarize(string $text, array $context): array
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'summary' => ['type' => 'string'],
            ],
        ];

        $prompt = <<<PROMPT
Summarize the following visitor information for reception staff.

Visitor information:
{$text}

Additional context:
{$this->contextToText($context)}

Keep the summary concise and factual.
Do not make access-control decisions.
PROMPT;

        $result = $this->llm($prompt, $schema);

        if (! ($result['_fallback'] ?? false)) {
            return $result;
        }

        $source = trim($text);

        if ($source === '' && ! empty($context)) {
            $source = json_encode(
                $context,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        }

        return [
            'summary' => 'Visit logged for manual review: '.($source ?: 'No visitor information supplied.'),
        ];
    }

    private function appointmentCheck(
        string $text,
        array $context
    ): array {
        $schema = [
            'type' => 'object',
            'properties' => [
                'issues' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'overall' => ['type' => 'string'],
            ],
        ];

        $prompt = <<<PROMPT
Review the following visitor and appointment information.

Visitor information:
{$text}

Appointment context:
{$this->contextToText($context)}

Identify possible inconsistencies, missing information,
or details that reception staff should manually verify.

Important:
- Do not approve or deny access.
- Do not automatically modify the appointment.
- Return review suggestions only.
PROMPT;

        $result = $this->llm($prompt, $schema);

        if (! ($result['_fallback'] ?? false)) {
            return $result;
        }

        return [
            'issues' => [],
            'overall' => 'AI provider not configured - no automated appointment review was performed. Please review this appointment manually.',
        ];
    }

    private function contextToText(array $context): string
    {
        if (empty($context)) {
            return 'None provided.';
        }

        return json_encode(
            $context,
            JSON_PRETTY_PRINT |
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        ) ?: 'None provided.';
    }
}