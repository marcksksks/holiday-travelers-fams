<?php

namespace Tests\Feature;

use Tests\TestCase;

class DocumentFilesystemReportingSecurityTest extends TestCase
{
    public function test_private_documents_disk_reports_failures_without_throwing_them(): void
    {
        $this->assertFalse(
            config(
                'filesystems.disks.documents.throw'
            )
        );

        $this->assertTrue(
            config(
                'filesystems.disks.documents.report'
            )
        );

        $this->assertSame(
            'local',
            config(
                'filesystems.disks.documents.driver'
            )
        );

        $this->assertFalse(
            config(
                'filesystems.disks.documents.serve'
            )
        );
    }
}