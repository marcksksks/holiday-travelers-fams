<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

/**
 * Port of base44/functions/aiVisitorAssist/entry.ts. Reception/admin-only
 * suggestion tool — never approves, rejects, or grants access on its own;
 * every result is a proposal for a human to confirm.
 */
class AiAssistService
{
    private const ALLOWED_ROLES = ['receptionist', 'admin_officer', 'manager', 'sys_admin'];
    private const VISITOR_TYPES = ['customer', 'business_partner', 'supplier', 'government', 'applicant', 'guest', 'other'];

    public function __construct(private AuditService $audit) {}

    public function assist(User $user, string $mode, string $text, array $context = []): array
    {
        if (! $user->hasRole(self::ALLOWED_ROLES)) {
            throw ValidationException::withMessages(['app_role' => 'AI visitor assistance is limited to reception and administration.'])->status(403);
        }

        $suggestion = match ($mode) {
            'classify', 'extract' => $this->classify($text, $context),
            'summary' => $this->summarize($text, $context),
            'appointment_check' => $this->appointmentCheck($text, $context),
            default => throw ValidationException::withMessages(['mode' => 'Unknown assistance mode.']),
        };

        $this->audit->log($user, 'ai_assist', 'visitors', "AI assistance ({$mode})", null,
            'Suggestion generated for human review — not applied automatically.');

        return ['mode' => $mode, 'suggestion' => $suggestion];
    }

    /**
     * If AI_ASSIST_PROVIDER is configured, calls that LLM with the same
     * guard-railed prompt as the original. Otherwise returns a safe,
     * clearly-labelled rule-based fallback so the feature degrades gracefully.
     */
    private function llm(string $prompt, array $schema): array
    {
        $provider = config('services.ai_assist.provider', 'none');
        if ($provider === 'none' || ! config('services.ai_assist.api_key')) {
            return ['_fallback' => true];
        }

        $response = Http::withToken(config('services.ai_assist.api_key'))
            ->post(config('services.ai_assist.endpoint'), [
                'model' => config('services.ai_assist.model'),
                'prompt' => $prompt,
                'response_json_schema' => $schema,
            ]);

        return $response->successful() ? $response->json() : ['_fallback' => true];
    }

    private function classify(string $text, array $context): array
    {
        $schema = ['type' => 'object'];
        $result = $this->llm('classify', $schema);
        if (! ($result['_fallback'] ?? false)) {
            return $result;
        }

        return [
            'full_name' => '', 'organization' => '', 'contact_number' => '', 'email' => '',
            'visitor_type' => 'guest', 'purpose' => trim($text) ?: 'Not specified',
            'confidence' => 'low', 'reasoning' => 'AI provider not configured — showing a low-confidence placeholder for staff to complete manually.',
            'missing_fields' => ['full_name', 'organization', 'contact_number', 'email'],
        ];
    }

    private function summarize(string $text, array $context): array
    {
        $result = $this->llm('summary', ['type' => 'object']);
        if (! ($result['_fallback'] ?? false)) {
            return $result;
        }

        return ['summary' => 'Visit logged for review: '.trim($text ?: json_encode($context))];
    }

    private function appointmentCheck(string $text, array $context): array
    {
        $result = $this->llm('appointment_check', ['type' => 'object']);
        if (! ($result['_fallback'] ?? false)) {
            return $result;
        }

        return [
            'issues' => [],
            'overall' => 'AI provider not configured — no automated review performed. Please review this appointment manually.',
        ];
    }
}
