<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    private function employee(): User
    {
        return User::factory()
            ->role(
                User::ROLE_EMPLOYEE
            )
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    public function test_guest_cannot_use_global_search(): void
    {
        $this
            ->getJson(
                route(
                    'global-search.index',
                    [
                        'q' => 'meeting',
                    ]
                )
            )
            ->assertUnauthorized();
    }

    public function test_employee_can_search_accessible_facility(): void
    {
        $employee =
            $this->employee();

        Facility::factory()
            ->create([
                'name' => 'Global Search Meeting Room',
                'status' => 'available',
            ]);

        $this
            ->actingAs($employee)
            ->getJson(
                route(
                    'global-search.index',
                    [
                        'q' => 'Global Search',
                    ]
                )
            )
            ->assertOk()
            ->assertJsonFragment([
                'title' => 'Global Search Meeting Room',
                'type' => 'Facility',
            ]);
    }

    public function test_employee_does_not_receive_inaccessible_legal_workspace(): void
    {
        $employee =
            $this->employee();

        $response =
            $this
                ->actingAs($employee)
                ->getJson(
                    route(
                        'global-search.index',
                        [
                            'q' => 'legal',
                        ]
                    )
                )
                ->assertOk();

        $response->assertJsonMissing([
            'title' => 'Legal Management',
        ]);
    }

    public function test_short_query_returns_empty_result_set(): void
    {
        $employee =
            $this->employee();

        $this
            ->actingAs($employee)
            ->getJson(
                route(
                    'global-search.index',
                    [
                        'q' => 'a',
                    ]
                )
            )
            ->assertOk()
            ->assertJsonPath(
                'count',
                0
            )
            ->assertJsonPath(
                'results',
                []
            );
    }
}
