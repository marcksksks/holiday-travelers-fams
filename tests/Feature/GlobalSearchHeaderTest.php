<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalSearchHeaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_header_exposes_global_search_interface(): void
    {
        $user =
            User::factory()
                ->role(
                    User::ROLE_EMPLOYEE
                )
                ->create([
                    'is_active' => true,
                    'force_password_change' => false,
                ]);

        $this
            ->actingAs($user)
            ->get(
                route('dashboard')
            )
            ->assertOk()
            ->assertSee(
                'data-global-search',
                false
            )
            ->assertSee(
                'Search...',
                false
            )
            ->assertSee(
                'Ctrl K'
            )
            ->assertSee(
                'Search'
            )
            ->assertSee(
                route(
                    'global-search.index'
                ),
                false
            );
    }
}
