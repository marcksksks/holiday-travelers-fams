<?php

namespace App\Providers;

use App\Models\User;
use App\Support\Rbac;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register one Gate per entry in Rbac::PERMISSIONS, e.g.
        // Gate::allows('manageContracts'), Gate::allows('decideReservations'), etc.
        foreach (array_keys(Rbac::PERMISSIONS) as $permission) {
            Gate::define($permission, fn (User $user) => Rbac::can($permission, $user->app_role));
        }

        // sys_admin is never blocked by an undefined ability check.
        Gate::before(fn (User $user, string $ability) => $user->isSysAdmin() ? true : null);
    }
}
