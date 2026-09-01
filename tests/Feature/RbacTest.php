<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_sys_admin_can_reach_user_management(): void
    {
        $employee = User::factory()->create();
        $sysAdmin = User::factory()->role(User::ROLE_SYS_ADMIN)->create();

        $this->actingAs($employee)->get('/users')->assertForbidden();
        $this->actingAs($sysAdmin)->get('/users')->assertOk();
    }

    public function test_dashboard_is_reachable_by_every_role(): void
    {
        foreach (User::ROLES as $role => $label) {
            $user = User::factory()->role($role)->create();
            $this->actingAs($user)->get('/dashboard')->assertOk();
        }
    }
}
