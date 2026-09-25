<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityScanningWorkflowTest extends TestCase
{
    public function test_semgrep_sast_workflow_is_configured_for_php_security_scanning(): void
    {
        $contents =
            file_get_contents(
                base_path(
                    '.github/workflows/security-sast.yml'
                )
            );

        $this->assertIsString(
            $contents
        );

        foreach ([
            'PHP SAST - Semgrep',
            'semgrep==1.178.0',
            '--config p/php',
            '--config p/owasp-top-ten',
            '--error',
            '--metrics=off',
            '--exclude vendor',
            '--exclude node_modules',
            '--exclude backups',
        ] as $expected) {
            $this->assertStringContainsString(
                $expected,
                $contents
            );
        }

        $this->assertStringContainsString(
            'pull_request:',
            $contents
        );

        $this->assertStringContainsString(
            'schedule:',
            $contents
        );

        $this->assertStringContainsString(
            'workflow_dispatch:',
            $contents
        );
    }

    public function test_dast_workflow_requires_explicit_https_target_and_tls_13(): void
    {
        $contents =
            file_get_contents(
                base_path(
                    '.github/workflows/security-dast-tls.yml'
                )
            );

        $this->assertIsString(
            $contents
        );

        foreach ([
            'workflow_dispatch:',
            'target_url:',
            'required: true',
            'DAST target must use HTTPS.',
            '--tlsv1.3',
            '--tls-max 1.3',
            'zaproxy/action-baseline@v0.15.0',
            'fail_action: true',
            'allow_issue_writing: false',
            'fams-zap-baseline',
        ] as $expected) {
            $this->assertStringContainsString(
                $expected,
                $contents
            );
        }

        $this->assertStringNotContainsString(
            "on:\n  push:",
            $contents
        );

        $this->assertStringNotContainsString(
            "on:\r\n  push:",
            $contents
        );
    }

    public function test_dast_target_validation_blocks_local_and_private_targets(): void
    {
        $contents =
            file_get_contents(
                base_path(
                    '.github/workflows/security-dast-tls.yml'
                )
            );

        $this->assertIsString(
            $contents
        );

        foreach ([
            'localhost',
            'ip.is_private',
            'ip.is_loopback',
            'ip.is_link_local',
            'ip.is_reserved',
        ] as $expected) {
            $this->assertStringContainsString(
                $expected,
                $contents
            );
        }
    }
}
