<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
    public function __construct(private UserManagementService $users) {}

    public function index(Request $request)
    {
        abort_unless($request->user()->can('manageUsers'), 403);

        return UserResource::collection(User::orderBy('full_name')->paginate(30));
    }

    public function store(CreateUserRequest $request)
    {
        $user = $this->users->create($request->user(), $request->string('email'), $request->string('password'), $request->string('full_name'), $request->string('app_role'));

        return (new UserResource($user))->response()->setStatusCode(201);
    }
}
