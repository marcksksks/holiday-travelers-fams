<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePrivilegedMfa
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (
            ! $user ||
            ! $user->requiresMandatoryMfa() ||
            $user->hasTwoFactorEnabled()
        ) {
            return $next($request);
        }

        /*
         * Privileged users must still be able to change a forced
         * password, configure MFA, confirm MFA, or sign out.
         */
        if (
            $request->routeIs(
                'password.change',
                'password.change.update',
                'settings.index',
                'settings.mfa.setup',
                'settings.mfa.confirm',
                'logout'
            )
        ) {
            return $next($request);
        }

        if (
            $request->expectsJson() ||
            $request->is('api/*')
        ) {
            return response()->json([
                'message' => 'Two-factor authentication is required for this account.',
                'code' => 'mfa_required',
            ], 403);
        }

        return redirect()
            ->route('settings.index')
            ->withErrors([
                'mfa' => 'Two-factor authentication is required for your role. Complete MFA setup before accessing the system.',
            ]);
    }
}
