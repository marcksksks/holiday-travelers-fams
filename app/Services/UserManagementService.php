<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserManagementService
{
    public function __construct(private AuditService $audit) {}

    public function create(
        User $admin,
        string $email,
        string $password,
        string $fullName,
        string $role,
        ?string $department = null,
        ?string $jobTitle = null,
        ?string $phone = null
    ): User {
        if (! $admin->isSysAdmin()) {
            throw ValidationException::withMessages([
                'app_role' => 'Forbidden - system administrators only.',
            ])->status(403);
        }

        if (strlen($password) < 8) {
            throw ValidationException::withMessages([
                'password' => 'Password must be at least 8 characters.',
            ]);
        }

        $user = User::create([
            'full_name' => $fullName,
            'email' => $email,
            'password' => Hash::make($password),
            'app_role' => $role ?: User::ROLE_EMPLOYEE,

            'department' => $department ?: null,
            'job_title' => $jobTitle ?: null,
            'phone' => $phone ?: null,

            'is_active' => true,
            'force_password_change' => true,
        ]);

        $this->audit->log(
            $admin,
            'create',
            'users',
            "User - {$user->full_name}",
            (string) $user->id,
            "Role: {$user->app_role}"
        );

        return $user;
    }

    public function setRole(
        User $admin,
        User $target,
        string $role
    ): User {
        if (! $admin->isSysAdmin()) {
            throw ValidationException::withMessages([
                'app_role' => 'Forbidden - system administrators only.',
            ])->status(403);
        }

        if (
            $target->isSysAdmin() &&
            $role !== User::ROLE_SYS_ADMIN &&
            $target->is_active &&
            User::where('app_role', User::ROLE_SYS_ADMIN)
                ->where('is_active', true)
                ->count() <= 1
        ) {
            throw ValidationException::withMessages([
                'app_role' => 'At least one active system administrator must remain.',
            ]);
        }

        $target->update([
            'app_role' => $role,
        ]);

        $this->audit->log(
            $admin,
            'role_change',
            'users',
            "User - {$target->full_name}",
            (string) $target->id,
            "New role: {$role}"
        );

        return $target->refresh();
    }

    public function setActive(
        User $admin,
        User $target,
        bool $active
    ): User {
        if (! $admin->isSysAdmin()) {
            throw ValidationException::withMessages([
                'app_role' => 'Forbidden - system administrators only.',
            ])->status(403);
        }

        if ($target->id === $admin->id && ! $active) {
            throw ValidationException::withMessages([
                'is_active' => 'You cannot deactivate your own administrator account.',
            ]);
        }

        if (
            ! $active &&
            $target->isSysAdmin() &&
            $target->is_active &&
            User::where('app_role', User::ROLE_SYS_ADMIN)
                ->where('is_active', true)
                ->count() <= 1
        ) {
            throw ValidationException::withMessages([
                'is_active' => 'At least one active system administrator must remain.',
            ]);
        }

        return \Illuminate\Support\Facades\DB::transaction(
            function () use (
                $admin,
                $target,
                $active
            ): User {
                $attributes = [
                    'is_active' => $active,
                ];

                if (! $active) {
                    $attributes['remember_token'] = null;
                }

                $target
                    ->forceFill($attributes)
                    ->save();

                if (! $active) {
                    \Illuminate\Support\Facades\DB::connection(
                        config('session.connection')
                    )
                        ->table(
                            config(
                                'session.table',
                                'sessions'
                            )
                        )
                        ->where(
                            'user_id',
                            $target->getKey()
                        )
                        ->delete();

                    $target
                        ->tokens()
                        ->delete();
                }

                $this->audit->log(
                    $admin,
                    'update',
                    'users',
                    "User - {$target->full_name}",
                    (string) $target->id,
                    $active
                        ? 'Reactivated'
                        : 'Deactivated'
                );

                return $target->refresh();
            }
        );
    }
}