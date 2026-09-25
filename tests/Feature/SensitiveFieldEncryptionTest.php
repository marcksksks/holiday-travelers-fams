<?php

namespace Tests\Feature;

use App\Models\AccountRecoveryRequest;
use App\Models\PrivacyConsent;
use App\Models\PrivacyRequest;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SensitiveFieldEncryptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_uses_aes_256_cipher_with_configured_key(): void
    {
        $this->assertSame(
            'AES-256-CBC',
            config('app.cipher')
        );

        $this->assertNotEmpty(
            config('app.key')
        );
    }

    public function test_user_phone_is_encrypted_at_rest(): void
    {
        $plaintext =
            '09171234567';

        $user =
            User::factory()
                ->create([
                    'phone' => $plaintext,
                ]);

        $raw =
            DB::table('users')
                ->where(
                    'id',
                    $user->id
                )
                ->value('phone');

        $this->assertNotNull(
            $raw
        );

        $this->assertNotSame(
            $plaintext,
            $raw
        );

        $this->assertSame(
            $plaintext,
            $user->fresh()->phone
        );
    }

    public function test_visitor_id_reference_uses_encrypted_cast(): void
    {
        $visitor =
            new Visitor;

        $visitor->id_reference =
            'GOV-ID-123456';

        $this->assertNotSame(
            'GOV-ID-123456',
            $visitor->getAttributes()[
                'id_reference'
            ]
        );

        $this->assertSame(
            'GOV-ID-123456',
            $visitor->id_reference
        );
    }

    public function test_account_recovery_network_metadata_uses_encrypted_casts(): void
    {
        $request =
            new AccountRecoveryRequest;

        $request->request_ip =
            '192.0.2.25';

        $request->user_agent =
            'FAMS Security Test Agent';

        $attributes =
            $request->getAttributes();

        $this->assertNotSame(
            '192.0.2.25',
            $attributes['request_ip']
        );

        $this->assertNotSame(
            'FAMS Security Test Agent',
            $attributes['user_agent']
        );

        $this->assertSame(
            '192.0.2.25',
            $request->request_ip
        );

        $this->assertSame(
            'FAMS Security Test Agent',
            $request->user_agent
        );
    }

    public function test_privacy_consent_metadata_is_encrypted_array(): void
    {
        $consent =
            new PrivacyConsent;

        $metadata = [
            'channel' => 'settings',

            'evidence' => 'explicit-choice',
        ];

        $consent->metadata =
            $metadata;

        $raw =
            $consent->getAttributes()[
                'metadata'
            ];

        $this->assertIsString(
            $raw
        );

        $this->assertStringNotContainsString(
            'explicit-choice',
            $raw
        );

        $this->assertSame(
            $metadata,
            $consent->metadata
        );
    }

    public function test_privacy_request_sensitive_content_is_encrypted(): void
    {
        $request =
            new PrivacyRequest;

        $request->details =
            'Sensitive request details';

        $request->decision_reason =
            'Sensitive reviewer reason';

        $request->retention_basis =
            'Sensitive retention basis';

        $request->execution_summary = [
            'action' => 'controlled-test',
        ];

        $raw =
            $request->getAttributes();

        $this->assertNotSame(
            'Sensitive request details',
            $raw['details']
        );

        $this->assertNotSame(
            'Sensitive reviewer reason',
            $raw['decision_reason']
        );

        $this->assertNotSame(
            'Sensitive retention basis',
            $raw['retention_basis']
        );

        $this->assertStringNotContainsString(
            'controlled-test',
            $raw['execution_summary']
        );

        $this->assertSame(
            'Sensitive request details',
            $request->details
        );

        $this->assertSame(
            'Sensitive reviewer reason',
            $request->decision_reason
        );

        $this->assertSame(
            'Sensitive retention basis',
            $request->retention_basis
        );

        $this->assertSame(
            [
                'action' => 'controlled-test',
            ],
            $request->execution_summary
        );
    }

    public function test_search_critical_email_remains_queryable(): void
    {
        $user =
            User::factory()
                ->create([
                    'email' => 'queryable@example.com',
                ]);

        $rawEmail =
            DB::table('users')
                ->where(
                    'id',
                    $user->id
                )
                ->value('email');

        $this->assertSame(
            'queryable@example.com',
            $rawEmail
        );

        $this->assertSame(
            $user->id,
            User::query()
                ->where(
                    'email',
                    'queryable@example.com'
                )
                ->value('id')
        );
    }

    public function test_sensitive_cast_matrix_is_exact(): void
    {
        $this->assertSame(
            'encrypted',
            (new User)
                ->getCasts()['phone']
        );

        $this->assertSame(
            'encrypted',
            (new Visitor)
                ->getCasts()['id_reference']
        );

        $recoveryCasts =
            (new AccountRecoveryRequest)
                ->getCasts();

        $this->assertSame(
            'encrypted',
            $recoveryCasts['request_ip']
        );

        $this->assertSame(
            'encrypted',
            $recoveryCasts['user_agent']
        );

        $this->assertSame(
            'encrypted:array',
            (new PrivacyConsent)
                ->getCasts()['metadata']
        );

        $requestCasts =
            (new PrivacyRequest)
                ->getCasts();

        $this->assertSame(
            'encrypted',
            $requestCasts['details']
        );

        $this->assertSame(
            'encrypted',
            $requestCasts[
                'decision_reason'
            ]
        );

        $this->assertSame(
            'encrypted',
            $requestCasts[
                'retention_basis'
            ]
        );

        $this->assertSame(
            'encrypted:array',
            $requestCasts[
                'execution_summary'
            ]
        );
    }
}
