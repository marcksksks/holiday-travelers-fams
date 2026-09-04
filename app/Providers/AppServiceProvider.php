<?php

namespace App\Providers;

use App\Models\Contract;
use App\Models\LegalRecord;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Visitor;
use App\Observers\ContractObserver;
use App\Observers\LegalRecordObserver;
use App\Observers\ReservationObserver;
use App\Observers\VisitorObserver;
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
        foreach (
            array_keys(Rbac::PERMISSIONS)
            as $permission
        ) {
            Gate::define(
                $permission,
                fn (User $user) =>
                    Rbac::can(
                        $permission,
                        $user->app_role
                    )
            );
        }

        Gate::before(
            fn (
                User $user,
                string $ability
            ) =>
                $user->isSysAdmin()
                    ? true
                    : null
        );

        Reservation::observe(
            ReservationObserver::class
        );

        Visitor::observe(
            VisitorObserver::class
        );

        Contract::observe(
            ContractObserver::class
        );

        LegalRecord::observe(
            LegalRecordObserver::class
        );
    }
}