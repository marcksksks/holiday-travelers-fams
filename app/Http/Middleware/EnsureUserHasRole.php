<?php

namespace App\Http\Middleware;

use App\Support\Rbac;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Route-level guard, e.g. ->middleware('role:manager,sys_admin')
     * Falls back to Rbac::NAV for the current route name when no roles are given.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        $role = $user?->app_role ?? 'employee';

        $allowed = $roles ?: (Rbac::NAV[$request->route()?->getName()] ?? null);

        if ($allowed && ! in_array($role, $allowed, true)) {
            abort(403, 'You do not have access to this module.');
        }

        return $next($request);
    }
}
