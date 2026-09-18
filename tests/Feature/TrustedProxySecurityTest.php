<?php

namespace Tests\Feature;

use App\Models\ArchiveDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TrustedProxySecurityTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): User
    {
        return User::factory()
            ->role(User::ROLE_MANAGER)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    private function document(
        User $actor
    ): ArchiveDocument {
        $path =
            'archive/proxy-security.pdf';

        Storage::disk('documents')->put(
            $path,
            'proxy security test'
        );

        return ArchiveDocument::create([
            'title' =>
                'Proxy Security Document',

            'category' =>
                'administrative',

            'confidentiality' =>
                'general',

            'status' =>
                'active',

            'version' =>
                1,

            'file_uri' =>
                $path,

            'file_name' =>
                'proxy-security.pdf',

            'uploaded_by_email' =>
                $actor->email,

            'is_system_generated' =>
                false,
        ]);
    }

    private function proxyServer(): array
    {
        return [
            'REMOTE_ADDR' =>
                '10.10.10.10',

            'SERVER_PORT' =>
                '80',

            'HTTPS' =>
                'off',
        ];
    }

    private function proxyHeaders(): array
    {
        return [
            'X-Forwarded-Proto' =>
                'https',

            'X-Forwarded-Port' =>
                '443',

            'X-Forwarded-For' =>
                '203.0.113.25',

            /*
             * This value must not control generated URLs
             * because X-Forwarded-Host is not trusted.
             */
            'X-Forwarded-Host' =>
                'attacker.example',
        ];
    }

    public function test_forwarded_https_generates_and_validates_secure_signed_document_url(): void
    {
        Storage::fake('documents');

        $manager =
            $this->manager();

        $document =
            $this->document(
                $manager
            );

        $linkResponse =
            $this
                ->withServerVariables(
                    $this->proxyServer()
                )
                ->withHeaders(
                    $this->proxyHeaders()
                )
                ->actingAs($manager)
                ->post(
                    "/documents/{$document->id}/request-link"
                );

        $linkResponse->assertOk();

        $signedUrl =
            (string) $linkResponse->json(
                'signed_url'
            );

        $this->assertStringStartsWith(
            'https://',
            $signedUrl
        );

        $this->assertNotSame(
            'attacker.example',
            parse_url(
                $signedUrl,
                PHP_URL_HOST
            )
        );

        $path =
            (string) parse_url(
                $signedUrl,
                PHP_URL_PATH
            );

        $query =
            parse_url(
                $signedUrl,
                PHP_URL_QUERY
            );

        $relativeUrl =
            $path .
            (
                is_string($query) &&
                $query !== ''
                    ? "?{$query}"
                    : ''
            );

        $downloadResponse =
            $this
                ->withServerVariables(
                    $this->proxyServer()
                )
                ->withHeaders(
                    $this->proxyHeaders()
                )
                ->actingAs($manager)
                ->get($relativeUrl);

        $downloadResponse->assertOk();
    }

    public function test_local_http_request_is_not_forced_to_https(): void
    {
        Storage::fake('documents');

        $manager =
            $this->manager();

        $document =
            $this->document(
                $manager
            );

        $response =
            $this
                ->actingAs($manager)
                ->post(
                    "/documents/{$document->id}/request-link"
                );

        $response->assertOk();

        $signedUrl =
            (string) $response->json(
                'signed_url'
            );

        $this->assertStringStartsWith(
            'http://',
            $signedUrl
        );
    }
}