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
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /*
         * One authoritative password policy for every password
         * creation, change, and recovery workflow.
         */
        Password::defaults(
            fn () => Password::min(12)
                ->mixedCase()
                ->numbers()
                ->symbols()
        );

        RateLimiter::for(
            'api',
            function (Request $request): Limit {
                $userId =
                    $request->user()
                        ?->getAuthIdentifier();

                $key =
                    $userId !== null
                        ? "user:{$userId}"
                        : 'ip:'.$request->ip();

                return Limit::perMinute(120)
                    ->by($key);
            }
        );

        RateLimiter::for(
            'password-reset-link',
            fn (Request $request): Limit => Limit::perMinute(5)
                ->by(
                    'password-reset-link:'.$request->ip()
                )
        );

        RateLimiter::for(
            'ai-assist',
            function (Request $request): Limit {
                $userId =
                    $request->user()
                        ?->getAuthIdentifier();

                $key =
                    $userId !== null
                        ? "ai-assist:user:{$userId}"
                        : 'ai-assist:ip:'.$request->ip();

                return Limit::perMinute(10)
                    ->by($key);
            }
        );

        RateLimiter::for(
            'calendar-sync',
            function (Request $request): Limit {
                $userId =
                    $request->user()
                        ?->getAuthIdentifier();

                $key =
                    $userId !== null
                        ? "calendar-sync:user:{$userId}"
                        : 'calendar-sync:ip:'.$request->ip();

                return Limit::perMinute(5)
                    ->by($key);
            }
        );

        foreach (
            array_keys(Rbac::PERMISSIONS) as $permission
        ) {
            Gate::define(
                $permission,
                fn (User $user) => Rbac::can(
                    $permission,
                    $user->app_role
                )
            );
        }

        Gate::before(
            fn (
                User $user,
                string $ability
            ) => $user->isSysAdmin()
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
