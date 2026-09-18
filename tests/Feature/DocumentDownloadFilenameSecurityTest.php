<?php

namespace Tests\Feature;

use App\Models\ArchiveDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class DocumentDownloadFilenameSecurityTest extends TestCase
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
        User $actor,
        string $path,
        string $fileName,
        string $title
    ): ArchiveDocument {
        return ArchiveDocument::create([
            'title' =>
                $title,

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
                $fileName,

            'uploaded_by_email' =>
                $actor->email,

            'is_system_generated' =>
                false,
        ]);
    }

    public function test_signed_download_safely_builds_content_disposition_for_original_filenames(): void
    {
        Storage::fake('documents');

        $manager =
            $this->manager();

        $cases = [
            'unicode' => [
                'name' =>
                    'résumé 2026.pdf',

                'encoded' =>
                    "filename*=utf-8''r%C3%A9sum%C3%A9%202026.pdf",
            ],

            'quote' => [
                'name' =>
                    'quote"report.pdf',

                'encoded' =>
                    'quote\"report.pdf',
            ],

            'control' => [
                'name' =>
                    "evil\r\nX-Test: injected.pdf",

                'encoded' =>
                    '%0D%0A',
            ],

            'percent' => [
                'name' =>
                    '100% report.pdf',

                'encoded' =>
                    '%25',
            ],
        ];

        foreach ($cases as $key => $case) {
            $path =
                "archive/download-security-{$key}.pdf";

            Storage::disk('documents')->put(
                $path,
                "download security test {$key}"
            );

            $document =
                $this->document(
                    $manager,
                    $path,
                    $case['name'],
                    "Download Security {$key}"
                );

            $signedUrl =
                URL::temporarySignedRoute(
                    'documents.download',
                    now()->addMinutes(5),
                    [
                        'document' =>
                            $document->id,
                    ]
                );

            $response =
                $this
                    ->actingAs($manager)
                    ->get($signedUrl);

            $response->assertOk();

            $disposition =
                (string) $response
                    ->headers
                    ->get(
                        'Content-Disposition'
                    );

            $this->assertStringStartsWith(
                'attachment;',
                $disposition,
                "Unexpected disposition for {$key}."
            );

            $this->assertStringNotContainsString(
                "\r",
                $disposition,
                "Raw carriage return leaked into Content-Disposition for {$key}."
            );

            $this->assertStringNotContainsString(
                "\n",
                $disposition,
                "Raw newline leaked into Content-Disposition for {$key}."
            );

            $this->assertStringContainsString(
                $case['encoded'],
                $disposition,
                "Expected safe filename encoding was not present for {$key}."
            );

            $this->assertFalse(
                $response->headers->has(
                    'X-Test'
                ),
                "Filename created an injected response header for {$key}."
            );
        }
    }
}