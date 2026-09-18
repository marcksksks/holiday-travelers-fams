<?php

namespace Tests\Feature;

use Tests\TestCase;

class DockerBuildContextSecurityTest extends TestCase
{
    public function test_sensitive_local_material_is_excluded_from_docker_context(): void
    {
        $contents =
            file_get_contents(
                base_path('.dockerignore')
            );

        $this->assertIsString(
            $contents
        );

        foreach ([
            '.git',
            '.env',
            'vendor',
            'node_modules',
            'public/build',
            'storage/framework',
            'storage/logs',
            'backups',
            '*.backup',
            '*.before-*',
        ] as $pattern) {
            $this->assertStringContainsString(
                $pattern,
                $contents
            );
        }
    }

    public function test_environment_template_is_not_excluded(): void
    {
        $contents =
            file_get_contents(
                base_path('.dockerignore')
            );

        $this->assertStringContainsString(
            '!.env.example',
            $contents
        );
    }

    public function test_legacy_document_storage_is_not_excluded_yet(): void
    {
        $lines =
            file(
                base_path('.dockerignore'),
                FILE_IGNORE_NEW_LINES
            );

        $normalized =
            array_map(
                fn (string $line): string =>
                    trim($line),
                $lines
            );

        $this->assertNotContains(
            'storage/app/documents',
            $normalized
        );

        $this->assertNotContains(
            '/storage/app/documents',
            $normalized
        );
    }
}