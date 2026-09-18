<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteNameIsolationTest extends TestCase
{
    public function test_all_named_routes_are_unique(): void
    {
        $names =
            collect(
                Route::getRoutes()->getRoutes()
            )
                ->map(
                    fn ($route) =>
                        $route->getName()
                )
                ->filter(
                    fn ($name) =>
                        is_string($name) &&
                        $name !== ''
                );

        $duplicates =
            $names
                ->countBy()
                ->filter(
                    fn (int $count): bool =>
                        $count > 1
                )
                ->keys()
                ->values()
                ->all();

        $this->assertSame(
            [],
            $duplicates,
            'Duplicate route names: '.implode(
                ', ',
                $duplicates
            )
        );
    }

    public function test_all_named_api_routes_use_api_prefix(): void
    {
        $apiRoutes =
            collect(
                Route::getRoutes()->getRoutes()
            )
                ->filter(
                    fn ($route): bool =>
                        str_starts_with(
                            $route->uri(),
                            'api/'
                        ) &&
                        is_string(
                            $route->getName()
                        )
                );

        $this->assertNotEmpty(
            $apiRoutes->all()
        );

        foreach ($apiRoutes as $route) {
            $this->assertStringStartsWith(
                'api.',
                $route->getName()
            );
        }
    }

    public function test_previous_web_api_collisions_are_isolated(): void
    {
        $names = [
            'appointments.index',
            'appointments.store',
            'appointments.update',
            'contracts.index',
            'contracts.store',
            'contracts.update',
            'documents.index',
            'documents.store',
            'documents.show',
            'facilities.index',
            'facilities.store',
            'facilities.update',
            'facilities.destroy',
            'reservations.index',
            'reservations.store',
            'users.index',
            'users.store',
            'visitors.index',
            'visitors.store',
        ];

        foreach ($names as $name) {
            $web =
                Route::getRoutes()
                    ->getByName($name);

            $api =
                Route::getRoutes()
                    ->getByName(
                        'api.'.$name
                    );

            $this->assertNotNull(
                $web,
                "Missing web route: {$name}"
            );

            $this->assertNotNull(
                $api,
                "Missing API route: api.{$name}"
            );

            $this->assertFalse(
                str_starts_with(
                    $web->uri(),
                    'api/'
                ),
                "Web route {$name} points to an API URI."
            );

            $this->assertTrue(
                str_starts_with(
                    $api->uri(),
                    'api/'
                ),
                "API route api.{$name} does not point to an API URI."
            );
        }
    }
}