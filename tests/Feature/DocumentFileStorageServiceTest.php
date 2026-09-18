<?php

namespace Tests\Feature;

use App\Services\DocumentFileStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class DocumentFileStorageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_create_keeps_new_file(): void
    {
        Storage::fake('documents');

        $file = UploadedFile::fake()->create(
            'document.pdf',
            10,
            'application/pdf'
        );

        $path = app(
            DocumentFileStorageService::class
        )->create(
            $file,
            'archive',
            function (string $newPath): string {
                Storage::disk('documents')
                    ->assertExists($newPath);

                return $newPath;
            }
        );

        Storage::disk('documents')
            ->assertExists($path);

        $this->assertStringStartsWith(
            'archive/',
            $path
        );
    }

    public function test_failed_create_removes_new_file(): void
    {
        Storage::fake('documents');

        $file = UploadedFile::fake()->create(
            'document.pdf',
            10,
            'application/pdf'
        );

        try {
            app(
                DocumentFileStorageService::class
            )->create(
                $file,
                'archive',
                function (): never {
                    throw new RuntimeException(
                        'Simulated database failure.'
                    );
                }
            );

            $this->fail(
                'Expected simulated database failure.'
            );
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Simulated database failure.',
                $exception->getMessage()
            );
        }

        $this->assertSame(
            [],
            Storage::disk('documents')
                ->allFiles('archive')
        );
    }

    public function test_successful_replacement_deletes_old_file_after_success(): void
    {
        Storage::fake('documents');

        $oldPath = 'contracts/old.pdf';

        Storage::disk('documents')->put(
            $oldPath,
            'old file'
        );

        $file = UploadedFile::fake()->create(
            'replacement.pdf',
            10,
            'application/pdf'
        );

        $newPath = app(
            DocumentFileStorageService::class
        )->replace(
            $file,
            'contracts',
            $oldPath,
            function (
                string $replacementPath
            ) use ($oldPath): string {
                Storage::disk('documents')
                    ->assertExists($oldPath);

                Storage::disk('documents')
                    ->assertExists(
                        $replacementPath
                    );

                return $replacementPath;
            }
        );

        Storage::disk('documents')
            ->assertMissing($oldPath);

        Storage::disk('documents')
            ->assertExists($newPath);

        $this->assertNotSame(
            $oldPath,
            $newPath
        );
    }

    public function test_failed_replacement_preserves_old_file_and_removes_new_file(): void
    {
        Storage::fake('documents');

        $oldPath = 'legal/old.pdf';

        Storage::disk('documents')->put(
            $oldPath,
            'old file'
        );

        $file = UploadedFile::fake()->create(
            'replacement.pdf',
            10,
            'application/pdf'
        );

        try {
            app(
                DocumentFileStorageService::class
            )->replace(
                $file,
                'legal',
                $oldPath,
                function (
                    string $newPath
                ): never {
                    Storage::disk(
                        'documents'
                    )->assertExists(
                        $newPath
                    );

                    throw new RuntimeException(
                        'Simulated database failure.'
                    );
                }
            );

            $this->fail(
                'Expected simulated database failure.'
            );
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Simulated database failure.',
                $exception->getMessage()
            );
        }

        Storage::disk('documents')
            ->assertExists($oldPath);

        $this->assertSame(
            [$oldPath],
            Storage::disk('documents')
                ->allFiles('legal')
        );
    }
}