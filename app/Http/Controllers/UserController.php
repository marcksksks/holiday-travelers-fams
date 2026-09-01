<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(
        private UserManagementService $users
    ) {}

    public function index(Request $request)
    {
        abort_unless(
            $request->user()->can('manageUsers'),
            403
        );

        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],

            'role' => [
                'nullable',
                Rule::in(array_keys(User::ROLES)),
            ],

            'status' => [
                'nullable',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $staff = User::query()

            ->when(
                $request->filled('search'),
                function ($query) use ($request) {
                    $search = '%'.$request->string('search').'%';

                    $query->where(function ($q) use ($search) {
                        $q->where('full_name', 'ilike', $search)
                            ->orWhere('email', 'ilike', $search)
                            ->orWhere('department', 'ilike', $search)
                            ->orWhere('job_title', 'ilike', $search);
                    });
                }
            )

            ->when(
                $request->filled('role'),
                fn ($query) => $query->where(
                    'app_role',
                    $request->string('role')
                )
            )

            ->when(
                $request->status === 'active',
                fn ($query) => $query->where('is_active', true)
            )

            ->when(
                $request->status === 'inactive',
                fn ($query) => $query->where('is_active', false)
            )

            ->orderBy('full_name')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => User::count(),

            'active' => User::where(
                'is_active',
                true
            )->count(),

            'inactive' => User::where(
                'is_active',
                false
            )->count(),

            'password_change' => User::where(
                'force_password_change',
                true
            )->count(),
        ];

        return view(
            'users.index',
            compact('staff', 'stats')
        );
    }

    public function store(
        CreateUserRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $this->users->create(
            $request->user(),
            $data['email'],
            $data['password'],
            $data['full_name'],
            $data['app_role'],
            $data['department'] ?? null,
            $data['job_title'] ?? null,
            $data['phone'] ?? null,
        );

        return redirect()
            ->route('users.index')
            ->with(
                'status',
                'Account created - temporary password issued.'
            );
    }

    public function setRole(
        Request $request,
        User $user
    ): RedirectResponse {
        $data = $request->validate([
            'app_role' => [
                'required',
                Rule::in(array_keys(User::ROLES)),
            ],
        ]);

        $this->users->setRole(
            $request->user(),
            $user,
            $data['app_role']
        );

        return back()->with(
            'status',
            'Staff role updated.'
        );
    }

    public function toggleActive(
        Request $request,
        User $user
    ): RedirectResponse {
        $newState = ! $user->is_active;

        $this->users->setActive(
            $request->user(),
            $user,
            $newState
        );

        return back()->with(
            'status',
            $newState
                ? 'Account reactivated.'
                : 'Account deactivated.'
        );
    }
}