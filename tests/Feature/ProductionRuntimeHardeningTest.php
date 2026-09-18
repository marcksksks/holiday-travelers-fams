<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductionRuntimeHardeningTest extends TestCase
{
    public function test_php_production_security_configuration_is_hardened(): void
    {
        $contents =
            file_get_contents(
                base_path(
                    'docker/php-security.ini'
                )
            );

        $this->assertIsString(
            $contents
        );

        $this->assertStringContainsString(
            'expose_php = Off',
            $contents
        );

        $this->assertStringContainsString(
            'display_errors = Off',
            $contents
        );

        $this->assertStringContainsString(
            'display_startup_errors = Off',
            $contents
        );

        $this->assertStringContainsString(
            'log_errors = On',
            $contents
        );

        $this->assertStringContainsString(
            'cgi.fix_pathinfo = 0',
            $contents
        );
    }

    public function test_docker_image_installs_production_security_ini(): void
    {
        $contents =
            file_get_contents(
                base_path(
                    'Dockerfile'
                )
            );

        $this->assertIsString(
            $contents
        );

        $this->assertStringContainsString(
            'COPY docker/php-security.ini /usr/local/etc/php/conf.d/99-fams-security.ini',
            $contents
        );
    }

    public function test_nginx_suppresses_runtime_version_disclosure(): void
    {
        $contents =
            file_get_contents(
                base_path(
                    'docker/nginx.conf'
                )
            );

        $this->assertIsString(
            $contents
        );

        $this->assertStringContainsString(
            'server_tokens off;',
            $contents
        );

        $this->assertStringContainsString(
            'try_files $uri =404;',
            $contents
        );

        $this->assertStringContainsString(
            'fastcgi_hide_header X-Powered-By;',
            $contents
        );
    }
}