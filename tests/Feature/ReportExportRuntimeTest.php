<?php

namespace Tests\Feature;

use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ReportExportRuntimeTest extends TestCase
{
    public function test_report_export_libraries_are_available(): void
    {
        $this->assertTrue(
            class_exists(
                Pdf::class
            )
        );

        $this->assertTrue(
            class_exists(
                Spreadsheet::class
            )
        );

        $this->assertTrue(
            class_exists(
                Xlsx::class
            )
        );

        $this->assertTrue(
            class_exists(
                Csv::class
            )
        );
    }

    public function test_local_runtime_has_required_export_extensions(): void
    {
        foreach ([
            'dom',
            'fileinfo',
            'gd',
            'mbstring',
            'SimpleXML',
            'xml',
            'xmlreader',
            'xmlwriter',
            'zip',
            'zlib',
        ] as $extension) {
            $this->assertTrue(
                extension_loaded($extension),
                "{$extension} is required by the report export runtime."
            );
        }
    }

    public function test_docker_image_installs_required_export_extensions(): void
    {
        $contents = file_get_contents(
            base_path('Dockerfile')
        );

        $this->assertIsString($contents);

        foreach ([
            'freetype-dev',
            'libjpeg-turbo-dev',
            'libpng-dev',
            'libzip-dev',
            'libxml2-dev',
            'oniguruma-dev',
            'docker-php-ext-configure gd --with-freetype --with-jpeg',
            'gd',
            'zip',
            'mbstring',
            'dom',
            'simplexml',
            'xml',
            'xmlreader',
            'xmlwriter',
        ] as $marker) {
            $this->assertStringContainsString(
                $marker,
                $contents
            );
        }
    }
}
