<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveAccount
{
    /**
     * Deactivated accounts see a lock screen instead of the app,
     * mirroring AppShell.jsx's `user?.is_active === false` branch.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->is_active && ! $request->routeIs('logout')) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Account is deactivated.', 'code' => 'account_deactivated'], 403);
            }
            return response()->view('auth.deactivated', [], 403);
        }

        return $next($request);
    }
}
