<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/** Port of base44/functions/createUser/entry.ts (sys_admin only) */
class UserManagementService
{
    public function __construct(private AuditService $audit) {}

    public function create(User $admin, string $email, string $password, string $fullName, string $role): User
    {
        if (! $admin->isSysAdmin()) {
            throw ValidationException::withMessages(['app_role' => 'Forbidden — administrators only.'])->status(403);
        }
        if (strlen($password) < 8) {
            throw ValidationException::withMessages(['password' => 'Password must be at least 8 characters.']);
        }

        $user = User::create([
            'full_name' => $fullName,
            'email' => $email,
            'password' => Hash::make($password),
            'app_role' => $role ?: User::ROLE_EMPLOYEE,
            'force_password_change' => true,
        ]);


        $this->audit->log($admin, 'create', 'users', "User • {$user->full_name}", (string) $user->id, "Role: {$user->app_role}");

        return $user;
    }

    public function setRole(User $admin, User $target, string $role): User
    {
        if (! $admin->isSysAdmin()) {
            throw ValidationException::withMessages(['app_role' => 'Forbidden — administrators only.'])->status(403);
        }

        if ($target->isSysAdmin() && $role !== User::ROLE_SYS_ADMIN && $target->is_active && User::where('app_role', User::ROLE_SYS_ADMIN)->where('is_active', true)->count() <= 1) {
            throw ValidationException::withMessages(['app_role' => 'At least one active system administrator must remain.']);
        }
        $target->update(['app_role' => $role]);
        $this->audit->log($admin, 'role_change', 'users', "User • {$target->full_name}", (string) $target->id, "New role: {$role}");

        return $target->refresh();
    }

    public function setActive(User $admin, User $target, bool $active): User
    {
        if (! $admin->isSysAdmin()) {
            throw ValidationException::withMessages(['app_role' => 'Forbidden — administrators only.'])->status(403);
        }

        if ($target->id === $admin->id && ! $active) {
            throw ValidationException::withMessages(['is_active' => 'You cannot deactivate your own administrator account.']);
        }
        if (! $active && $target->isSysAdmin() && $target->is_active && User::where('app_role', User::ROLE_SYS_ADMIN)->where('is_active', true)->count() <= 1) {
            throw ValidationException::withMessages(['is_active' => 'At least one active system administrator must remain.']);
        }
        $target->update(['is_active' => $active]);
        $this->audit->log($admin, 'update', 'users', "User • {$target->full_name}", (string) $target->id, $active ? 'Reactivated' : 'Deactivated');

        return $target->refresh();
    }
}
