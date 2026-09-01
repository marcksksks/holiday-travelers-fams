<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserManagementService $users) {}

    public function index(Request $request)
    {
        abort_unless($request->user()->can('manageUsers'), 403);

        $staff = User::orderBy('full_name')->paginate(20)->withQueryString();

        return view('users.index', compact('staff'));
    }

    public function store(CreateUserRequest $request): RedirectResponse
    {
        $this->users->create(
            $request->user(),
            $request->string('email'),
            $request->string('password'),
            $request->string('full_name'),
            $request->string('app_role')
        );

        return redirect()->route('users.index')->with('status', 'Account created — temporary password issued.');
    }

    public function setRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['app_role' => ['required', 'in:employee,receptionist,admin_officer,manager,legal_officer,sys_admin']]);
        $this->users->setRole($request->user(), $user, $data['app_role']);

        return back()->with('status', "Role updated to {$data['app_role']}.");
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        $this->users->setActive($request->user(), $user, ! $user->is_active);

        return back()->with('status', $user->is_active ? 'Account reactivated.' : 'Account deactivated.');
    }
}
