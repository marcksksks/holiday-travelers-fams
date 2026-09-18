<?php

namespace Tests\Feature;

use App\Models\ArchiveDocument;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\DocumentFileStorageService;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class DocumentFileLifecycleIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function actor(): User
    {
        return User::factory()
            ->role(User::ROLE_ADMIN_OFFICER)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    private function failAfterCreatePersist(): DocumentFileStorageService
    {
        return new class extends DocumentFileStorageService
        {
            public function create(
                UploadedFile $file,
                string $directory,
                Closure $persist
            ): mixed {
                return parent::create(
                    $file,
                    $directory,
                    function (
                        string $newPath
                    ) use ($persist): mixed {
                        $persist($newPath);

                        throw new RuntimeException(
                            'Forced document create failure.'
                        );
                    }
                );
            }
        };
    }

    private function failAfterReplacePersist(): DocumentFileStorageService
    {
        return new class extends DocumentFileStorageService
        {
            public function replace(
                UploadedFile $file,
                string $directory,
                ?string $oldPath,
                Closure $persist
            ): mixed {
                return parent::replace(
                    $file,
                    $directory,
                    $oldPath,
                    function (
                        string $newPath
                    ) use ($persist): mixed {
                        $persist($newPath);

                        throw new RuntimeException(
                            'Forced document version failure.'
                        );
                    }
                );
            }
        };
    }

    public function test_web_document_create_failure_rolls_back_database_and_file(): void
    {
        Storage::fake('documents');

        $actor = $this->actor();

        $beforeDocuments =
            ArchiveDocument::count();

        $beforeAudits =
            AuditLog::count();

        $this->app->instance(
            DocumentFileStorageService::class,
            $this->failAfterCreatePersist()
        );

        $this->withoutExceptionHandling();

        try {
            $this
                ->actingAs($actor)
                ->post('/documents', [
                    'title' =>
                        'Web Rollback Document',

                    'category' =>
                        'administrative',

                    'confidentiality' =>
                        'general',

                    'status' =>
                        'active',

                    'description' =>
                        'Forced rollback integration test.',

                    'file' =>
                        UploadedFile::fake()
                            ->create(
                                'web-rollback.pdf',
                                10,
                                'application/pdf'
                            ),
                ]);

            $this->fail(
                'Expected forced document create failure.'
            );
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Forced document create failure.',
                $exception->getMessage()
            );
        }

        $this->assertSame(
            $beforeDocuments,
            ArchiveDocument::count()
        );

        $this->assertSame(
            $beforeAudits,
            AuditLog::count()
        );

        $this->assertFalse(
            ArchiveDocument::query()
                ->where(
                    'title',
                    'Web Rollback Document'
                )
                ->exists()
        );

        $this->assertSame(
            [],
            Storage::disk('documents')
                ->allFiles('archive')
        );
    }

    public function test_api_document_create_failure_rolls_back_database_and_file(): void
    {
        Storage::fake('documents');

        $actor = $this->actor();

        $beforeDocuments =
            ArchiveDocument::count();

        $beforeAudits =
            AuditLog::count();

        $this->app->instance(
            DocumentFileStorageService::class,
            $this->failAfterCreatePersist()
        );

        $this->withoutExceptionHandling();

        try {
            $this
                ->actingAs($actor, 'sanctum')
                ->post(
                    '/api/documents',
                    [
                        'title' =>
                            'API Rollback Document',

                        'category' =>
                            'administrative',

                        'confidentiality' =>
                            'general',

                        'status' =>
                            'active',

                        'description' =>
                            'Forced API rollback integration test.',

                        'file' =>
                            UploadedFile::fake()
                                ->create(
                                    'api-rollback.pdf',
                                    10,
                                    'application/pdf'
                                ),
                    ],
                    [
                        'Accept' =>
                            'application/json',
                    ]
                );

            $this->fail(
                'Expected forced document create failure.'
            );
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Forced document create failure.',
                $exception->getMessage()
            );
        }

        $this->assertSame(
            $beforeDocuments,
            ArchiveDocument::count()
        );

        $this->assertSame(
            $beforeAudits,
            AuditLog::count()
        );

        $this->assertFalse(
            ArchiveDocument::query()
                ->where(
                    'title',
                    'API Rollback Document'
                )
                ->exists()
        );

        $this->assertSame(
            [],
            Storage::disk('documents')
                ->allFiles('archive')
        );
    }

    public function test_failed_version_upload_restores_database_and_preserves_old_file(): void
    {
        Storage::fake('documents');

        $actor = $this->actor();

        $oldPath =
            'archive/original-version.pdf';

        Storage::disk('documents')->put(
            $oldPath,
            'original document content'
        );

        $originalHistory = [[
            'version' => 1,
            'action' => 'upload',
            'by' => $actor->email,
            'at' => now()->subDay()->toISOString(),
            'note' => 'Initial upload',
        ]];

        $document = ArchiveDocument::create([
            'title' =>
                'Version Rollback Document',

            'category' =>
                'administrative',

            'confidentiality' =>
                'general',

            'status' =>
                'active',

            'version' =>
                1,

            'file_uri' =>
                $oldPath,

            'file_name' =>
                'original-version.pdf',

            'uploaded_by_email' =>
                $actor->email,

            'is_system_generated' =>
                false,

            'history' =>
                $originalHistory,
        ]);

        $beforeAudits =
            AuditLog::count();

        $this->app->instance(
            DocumentFileStorageService::class,
            $this->failAfterReplacePersist()
        );

        $this->withoutExceptionHandling();

        try {
            $this
                ->actingAs($actor)
                ->post(
                    "/documents/{$document->id}/version",
                    [
                        'file' =>
                            UploadedFile::fake()
                                ->create(
                                    'replacement-version.pdf',
                                    10,
                                    'application/pdf'
                                ),

                        'version_note' =>
                            'This update must roll back.',
                    ]
                );

            $this->fail(
                'Expected forced document version failure.'
            );
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Forced document version failure.',
                $exception->getMessage()
            );
        }

        $document->refresh();

        $this->assertSame(
            $oldPath,
            $document->file_uri
        );

        $this->assertSame(
            'original-version.pdf',
            $document->file_name
        );

        $this->assertSame(
            1,
            $document->version
        );

        $this->assertSame(
            $originalHistory,
            $document->history
        );

        $this->assertSame(
            $beforeAudits,
            AuditLog::count()
        );

        Storage::disk('documents')
            ->assertExists($oldPath);

        $files =
            Storage::disk('documents')
                ->allFiles('archive');

        $this->assertCount(
            1,
            $files
        );

        $this->assertContains(
            $oldPath,
            $files
        );
    }
}