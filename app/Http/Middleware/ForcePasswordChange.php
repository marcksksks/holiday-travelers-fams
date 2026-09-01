<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Mirrors AppShell.jsx: if force_password_change is set, every route
     * except /change-password redirects there.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->force_password_change && ! $request->routeIs('password.change', 'password.change.update', 'logout')) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Password change required.', 'code' => 'password_change_required'], 403);
            }
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
