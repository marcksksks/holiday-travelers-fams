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

    public function test_render_blueprint_enforces_production_security_controls(): void
    {
        $contents =
            file_get_contents(
                base_path(
                    'render.yaml'
                )
            );

        $this->assertIsString(
            $contents
        );

        foreach ([
            'key: APP_ENV',
            'value: production',
            'key: APP_DEBUG',
            'value: "false"',
            'key: DB_SSLMODE',
            'value: require',
            'key: SESSION_SECURE_COOKIE',
            'key: SESSION_ENCRYPT',
            'key: LOG_CHANNEL',
            'value: stderr',
            'key: LOG_LEVEL',
            'value: info',
            'key: SANCTUM_STATEFUL_DOMAINS',
            'key: CORS_ALLOWED_ORIGINS',
        ] as $expected) {
            $this->assertStringContainsString(
                $expected,
                $contents
            );
        }
    }

    public function test_production_cors_does_not_default_to_wildcard_origin(): void
    {
        $contents =
            file_get_contents(
                base_path(
                    'config/cors.php'
                )
            );

        $this->assertIsString(
            $contents
        );

        $this->assertStringContainsString(
            "'CORS_ALLOWED_ORIGINS'",
            $contents
        );

        $this->assertStringContainsString(
            "'APP_URL'",
            $contents
        );

        $this->assertStringNotContainsString(
            "env('CORS_ALLOWED_ORIGINS', '*')",
            $contents
        );

        $this->assertStringContainsString(
            "'supports_credentials' => true",
            $contents
        );
    }

    public function test_container_stderr_logging_respects_configured_log_level(): void
    {
        $contents =
            file_get_contents(
                base_path(
                    'config/logging.php'
                )
            );

        $this->assertIsString(
            $contents
        );

        $this->assertStringContainsString(
            "'stream' => 'php://stderr'",
            $contents
        );

        $this->assertStringContainsString(
            "'LOG_LEVEL'",
            $contents
        );
    }

    public function test_cors_runtime_values_are_not_escaped(): void
    {
        $this->assertContains(
            'api/*',
            config('cors.paths')
        );

        $this->assertContains(
            '*',
            config('cors.allowed_methods')
        );

        $this->assertNotContains(
            'api/\*',
            config('cors.paths')
        );

        $this->assertNotContains(
            '\*',
            config('cors.allowed_methods')
        );

        foreach (
            config('cors.allowed_origins') as $origin
        ) {
            $this->assertStringNotContainsString(
                '[http',
                $origin
            );

            $this->assertStringNotContainsString(
                '](',
                $origin
            );
        }

        $this->assertSame(
            'php://stderr',
            config(
                'logging.channels.stderr.with.stream'
            )
        );
    }

    public function test_container_uses_platform_runtime_port(): void
    {
        $contents =
            file_get_contents(
                base_path(
                    'docker/start.sh'
                )
            );

        $this->assertIsString(
            $contents
        );

        $this->assertStringContainsString(
            'PORT="${PORT:-8080}"',
            $contents
        );

        $this->assertStringContainsString(
            'listen ${PORT};',
            $contents
        );

        $this->assertStringContainsString(
            '/etc/nginx/http.d/default.conf',
            $contents
        );
    }
}
