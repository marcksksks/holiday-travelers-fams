<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeaderPopoverCoordinationTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_notification_panel_uses_safe_viewport_positioning(): void
    {
        $user =
            User::factory()
                ->role(User::ROLE_EMPLOYEE)
                ->create([
                    'is_active' => true,
                    'force_password_change' => false,
                ]);

        $this
            ->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(
                'fixed inset-x-4 top-[4.5rem]',
                false
            )
            ->assertSee(
                'sm:absolute',
                false
            );
    }

    public function test_header_popovers_use_shared_close_events(): void
    {
        $javascript =
            file_get_contents(
                resource_path('js/app.js')
            );

        $search =
            file_get_contents(
                resource_path(
                    'views/components/global-search.blade.php'
                )
            );

        $this->assertStringContainsString(
            'fams:close-global-search',
            $javascript
        );

        $this->assertStringContainsString(
            'fams:close-global-search',
            $search
        );

        $this->assertStringContainsString(
            'fams:close-notification-menu',
            $search
        );
    }
}
