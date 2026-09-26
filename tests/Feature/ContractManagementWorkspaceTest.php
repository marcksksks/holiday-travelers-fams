<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractManagementWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    private function user(
        string $role
    ): User {
        return User::factory()
            ->role($role)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    public function test_system_admin_sees_contract_portfolio_workspace(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->actingAs($admin)
            ->get(
                route('contracts.index')
            )
            ->assertOk()
            ->assertSee(
                'Contract Portfolio'
            )
            ->assertSee('data-contract-filter-bar', false)
            ->assertSee('Search contracts...')
            ->assertSee(
                'Total Contracts'
            )
            ->assertSee(
                'Active Contracts'
            )
            ->assertSee(
                'Workflow Review'
            )
            ->assertSee(
                'Renewal Attention'
            )
            ->assertSee(
                'New Contract'
            )
            ->assertSee(
                'Contract Register'
            );
    }

    public function test_legal_officer_can_view_contract_workspace_without_creation_action(): void
    {
        $legalOfficer =
            $this->user(
                User::ROLE_LEGAL_OFFICER
            );

        $this
            ->actingAs($legalOfficer)
            ->get(
                route('contracts.index')
            )
            ->assertOk()
            ->assertSee(
                'Contract Portfolio'
            )
            ->assertSee(
                'Contract Register'
            )
            ->assertDontSee(
                'Save Contract Draft'
            );
    }

    public function test_active_contract_with_past_end_date_displays_term_warning_separately(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        Contract::create([
            'title' => 'Past End Date Active Contract',

            'contract_type' => 'service',

            'status' => 'active',

            'legal_review_status' => 'approved',

            'approval_status' => 'approved',

            'start_date' => now()
                ->subMonth()
                ->toDateString(),

            'end_date' => now()
                ->subDay()
                ->toDateString(),

            'currency' => 'PHP',

            'version' => 1,
        ]);

        $this
            ->actingAs($admin)
            ->get(
                route('contracts.index')
            )
            ->assertOk()
            ->assertSee(
                'Past End Date Active Contract'
            )
            ->assertSee(
                'Active'
            )
            ->assertSee(
                'End Date Passed'
            );
    }

    public function test_contract_workspace_can_filter_by_term_state_and_search(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        Contract::create([
            'title' => 'Expired Search Contract',

            'contract_type' => 'service',

            'status' => 'active',

            'end_date' => now()
                ->subDay()
                ->toDateString(),
        ]);

        Contract::create([
            'title' => 'Future Search Contract',

            'contract_type' => 'service',

            'status' => 'active',

            'end_date' => now()
                ->addMonths(3)
                ->toDateString(),
        ]);

        $this
            ->actingAs($admin)
            ->get(
                route(
                    'contracts.index',
                    [
                        'q' => 'Expired Search',

                        'deadline' => 'expired',
                    ]
                )
            )
            ->assertOk()
            ->assertSee(
                'Expired Search Contract'
            )
            ->assertDontSee(
                'Future Search Contract'
            );
    }

    public function test_renewal_attention_filter_includes_past_end_date_contracts(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        Contract::create([
            'title' => 'Attention Past Contract',

            'contract_type' => 'service',

            'status' => 'active',

            'end_date' => now()
                ->subDays(3)
                ->toDateString(),
        ]);

        Contract::create([
            'title' => 'Attention Future Contract',

            'contract_type' => 'service',

            'status' => 'active',

            'end_date' => now()
                ->addMonths(3)
                ->toDateString(),
        ]);

        $this
            ->actingAs($admin)
            ->get(
                route(
                    'contracts.index',
                    [
                        'deadline' => 'attention',
                    ]
                )
            )
            ->assertOk()
            ->assertSee(
                'Attention Past Contract'
            )
            ->assertDontSee(
                'Attention Future Contract'
            );
    }
}
