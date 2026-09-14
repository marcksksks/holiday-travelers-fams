<?php

namespace Tests\Feature;

use App\Models\RecordRetention;
use App\Models\RetentionPolicy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RetentionSearchTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()
            ->role(User::ROLE_ADMIN_OFFICER)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    private function retention(
        string $title,
        array $attributes = []
    ): RecordRetention {
        return RecordRetention::create(
            array_merge(
                [
                    'record_title' => $title,
                    'record_type' => 'document',
                    'status' => 'retained',
                    'compliance_status' => 'compliant',
                ],
                $attributes
            )
        );
    }

    private function policy(
        string $name,
        array $attributes = []
    ): RetentionPolicy {
        return RetentionPolicy::create(
            array_merge(
                [
                    'name' => $name,
                    'record_category' => 'administrative',
                    'retention_years' => 5,
                    'is_active' => true,
                ],
                $attributes
            )
        );
    }

    public function test_retention_records_can_be_searched_by_title_case_insensitively(): void
    {
        $user = $this->user();

        $this->retention(
            'Annual Corporate Compliance Archive'
        );

        $this->retention(
            'Employee Medical Records'
        );

        $response = $this
            ->actingAs($user)
            ->get(
                route(
                    'retention.index',
                    [
                        'tab' => 'records',
                        'q' => 'corporate compliance',
                    ]
                )
            );

        $response
            ->assertOk()
            ->assertSee('Annual Corporate Compliance Archive')
            ->assertDontSee('Employee Medical Records');
    }

    public function test_retention_records_can_be_searched_by_exact_numeric_record_id(): void
    {
        $user = $this->user();

        $this->retention(
            'Record With Matching External ID',
            [
                'record_id' => 987654,
            ]
        );

        $this->retention(
            'Record With Different External ID',
            [
                'record_id' => 123456,
            ]
        );

        $response = $this
            ->actingAs($user)
            ->get(
                route(
                    'retention.index',
                    [
                        'tab' => 'records',
                        'q' => '987654',
                    ]
                )
            );

        $response
            ->assertOk()
            ->assertSee('Record With Matching External ID')
            ->assertDontSee('Record With Different External ID');
    }

    public function test_retention_records_can_be_searched_by_stored_policy_name(): void
    {
        $user = $this->user();

        $this->retention(
            'Legacy Governance Record',
            [
                'policy_name' => 'Legacy Compliance Schedule',
            ]
        );

        $this->retention(
            'General Administrative Record',
            [
                'policy_name' => 'Standard Administrative Schedule',
            ]
        );

        $response = $this
            ->actingAs($user)
            ->get(
                route(
                    'retention.index',
                    [
                        'tab' => 'records',
                        'q' => 'legacy compliance',
                    ]
                )
            );

        $response
            ->assertOk()
            ->assertSee('Legacy Governance Record')
            ->assertDontSee('General Administrative Record');
    }

    public function test_retention_records_can_be_searched_by_related_policy_name(): void
    {
        $user = $this->user();

        $matchedPolicy = $this->policy(
            'Executive Contract Retention Policy',
            [
                'record_category' => 'contract',
                'retention_years' => 7,
            ]
        );

        $otherPolicy = $this->policy(
            'General Administrative Policy'
        );

        $this->retention(
            'Executive Supplier Agreement',
            [
                'record_type' => 'contract',
                'policy_id' => $matchedPolicy->id,
                'policy_name' => null,
            ]
        );

        $this->retention(
            'Routine Administrative File',
            [
                'policy_id' => $otherPolicy->id,
                'policy_name' => null,
            ]
        );

        $response = $this
            ->actingAs($user)
            ->get(
                route(
                    'retention.index',
                    [
                        'tab' => 'records',
                        'q' => 'executive contract',
                    ]
                )
            );

        $response
            ->assertOk()
            ->assertSee('Executive Supplier Agreement')
            ->assertDontSee('Routine Administrative File');
    }

    public function test_search_can_be_combined_with_existing_retention_filters(): void
    {
        $user = $this->user();

        $this->retention(
            'Vendor Contract Requiring Review',
            [
                'record_type' => 'contract',
                'status' => 'review_required',
                'compliance_status' => 'at_risk',
            ]
        );

        $this->retention(
            'Archived Vendor Contract',
            [
                'record_type' => 'contract',
                'status' => 'archived',
                'compliance_status' => 'compliant',
            ]
        );

        $this->retention(
            'Vendor Document Requiring Review',
            [
                'record_type' => 'document',
                'status' => 'review_required',
                'compliance_status' => 'at_risk',
            ]
        );

        $response = $this
            ->actingAs($user)
            ->get(
                route(
                    'retention.index',
                    [
                        'tab' => 'records',
                        'q' => 'Vendor',
                        'record_type' => 'contract',
                        'status' => 'review_required',
                        'compliance' => 'at_risk',
                    ]
                )
            );

        $response
            ->assertOk()
            ->assertSee('Vendor Contract Requiring Review')
            ->assertDontSee('Archived Vendor Contract')
            ->assertDontSee('Vendor Document Requiring Review');
    }

    public function test_search_with_no_matches_displays_filtered_empty_state(): void
    {
        $user = $this->user();

        $this->retention(
            'Existing Retention Record'
        );

        $response = $this
            ->actingAs($user)
            ->get(
                route(
                    'retention.index',
                    [
                        'tab' => 'records',
                        'q' => 'DefinitelyMissingRetentionRecord',
                    ]
                )
            );

        $response
            ->assertOk()
            ->assertSee('No matching retention records')
            ->assertDontSee('Existing Retention Record');
    }

    public function test_invalid_search_input_is_rejected_by_validation(): void
    {
        $user = $this->user();

        $response = $this
            ->actingAs($user)
            ->from(
                route(
                    'retention.index',
                    ['tab' => 'records']
                )
            )
            ->get(
                route(
                    'retention.index',
                    [
                        'tab' => 'records',
                        'q' => [
                            'invalid-array-input',
                        ],
                    ]
                )
            );

        $response
            ->assertRedirect(
                route(
                    'retention.index',
                    ['tab' => 'records']
                )
            )
            ->assertSessionHasErrors('q');
    }
}