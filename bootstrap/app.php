<?php

use App\Http\Middleware\EnsureActiveAccount;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\ForcePasswordChange;
use App\Http\Middleware\RequirePrivilegedMfa;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        /*
         * Render terminates public TLS before forwarding
         * requests to this application over HTTP.
         *
         * Trust only the proxy metadata required for the
         * original client IP and HTTPS scheme. The forwarded
         * host header is intentionally not trusted.
         */
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO
        );
        $middleware->web(append: [
            SecurityHeaders::class,
            EnsureActiveAccount::class,
            ForcePasswordChange::class,
            RequirePrivilegedMfa::class,
        ]);
        $middleware->api(append: [
            SecurityHeaders::class,
            EnsureActiveAccount::class,
            ForcePasswordChange::class,
            RequirePrivilegedMfa::class,
        ]);

        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'privileged.mfa' => RequirePrivilegedMfa::class,
        ]);

        $middleware->statefulApi();
        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(
            function (
                Response $response,
                Throwable $exception,
                Request $request
            ): Response {
                return SecurityHeaders::apply(
                    $response,
                    $request
                );
            }
        );
    })->create();
