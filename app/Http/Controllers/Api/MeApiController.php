<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Support\Rbac;
use Illuminate\Http\Request;

class MeApiController extends Controller
{
    /** GET /api/me — current user + nav/permissions, used by the SPA-style layout shell. */
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => new UserResource($user),
            'nav' => Rbac::navFor($user->app_role),
            'permissions' => array_keys(array_filter(Rbac::PERMISSIONS, fn ($roles) => in_array($user->app_role, $roles, true))),
        ]);
    }
}
