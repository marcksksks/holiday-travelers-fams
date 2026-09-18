<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    private function assertBaselineHeaders(
        $response
    ): void {
        $response->assertHeader(
            'X-Content-Type-Options',
            'nosniff'
        );

        $response->assertHeader(
            'X-Frame-Options',
            'DENY'
        );

        $response->assertHeader(
            'Referrer-Policy',
            'strict-origin-when-cross-origin'
        );

        $response->assertHeader(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=()'
        );

        $response->assertHeader(
            'X-Permitted-Cross-Domain-Policies',
            'none'
        );
    }

    public function test_web_response_has_baseline_security_headers(): void
    {
        $response =
            $this->get('/login');

        $response->assertOk();

        $this->assertBaselineHeaders(
            $response
        );

        $this->assertFalse(
            $response->headers->has(
                'Strict-Transport-Security'
            )
        );
    }

    public function test_api_error_response_has_baseline_security_headers(): void
    {
        $response =
            $this->getJson('/api/me');

        $response->assertUnauthorized();

        $this->assertBaselineHeaders(
            $response
        );
    }

    public function test_production_https_response_has_hsts(): void
    {
        config([
            'app.env' =>
                'production',
        ]);

        $response =
            $this
                ->withServerVariables([
                    'REMOTE_ADDR' =>
                        '10.10.10.10',

                    'SERVER_PORT' =>
                        '80',

                    'HTTPS' =>
                        'off',
                ])
                ->withHeaders([
                    'X-Forwarded-Proto' =>
                        'https',

                    'X-Forwarded-Port' =>
                        '443',
                ])
                ->get('/login');

        $response->assertOk();

        $this->assertBaselineHeaders(
            $response
        );

        $response->assertHeader(
            'Strict-Transport-Security',
            'max-age=31536000'
        );
    }
}