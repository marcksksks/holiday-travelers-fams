<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Contract;
use App\Models\Facility;
use App\Models\LegalRecord;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReportAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::factory()
            ->role($role)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    public function test_report_access_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_MANAGER,
            User::ROLE_SYS_ADMIN,
        ];

        $roles = [
            User::ROLE_EMPLOYEE,
            User::ROLE_RECEPTIONIST,
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_MANAGER,
            User::ROLE_LEGAL_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($roles as $role) {
            $user = $this->user($role);

            $response = $this
                ->actingAs($user)
                ->get('/reports');

            if (in_array($role, $allowed, true)) {
                $response->assertOk();
            } else {
                $response->assertForbidden();
            }
        }
    }

    public function test_report_date_filter_counts_only_selected_period(): void
    {
        $manager = $this->user(
            User::ROLE_MANAGER
        );

        $facility = Facility::factory()->create([
            'status' => 'available',
        ]);

        Reservation::create([
            'facility_id' => $facility->id,
            'facility_name' => $facility->name,
            'requester_email' => 'inside@example.test',
            'requester_name' => 'Inside Range',
            'date' => '2026-09-10',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'attendees' => 10,
            'status' => 'approved',
        ]);

        Reservation::create([
            'facility_id' => $facility->id,
            'facility_name' => $facility->name,
            'requester_email' => 'outside@example.test',
            'requester_name' => 'Outside Range',
            'date' => '2026-08-01',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'attendees' => 99,
            'status' => 'approved',
        ]);

        $visitor = Visitor::create([
            'full_name' => 'Report Visitor',
            'visitor_type' => 'guest',
            'is_walk_in' => true,
            'status' => 'completed',
            'duration_minutes' => 30,
        ]);

        $visitor->forceFill([
            'created_at' => '2026-09-10 09:00:00',
            'updated_at' => '2026-09-10 09:30:00',
        ])->saveQuietly();

        Appointment::create([
            'visitor_name' => 'Report Appointment',
            'visitor_type' => 'guest',
            'date' => '2026-09-11',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'status' => 'scheduled',
        ]);

        $response = $this
            ->actingAs($manager)
            ->get(
                '/reports?from=2026-09-01&to=2026-09-30'
            );

        $response
            ->assertOk()
            ->assertViewHas(
                'summary',
                function (array $summary) {
                    return
                        $summary['reservations'] === 1 &&
                        $summary['approved_reservations'] === 1 &&
                        $summary['reservation_attendees'] === 10 &&
                        $summary['visitors'] === 1 &&
                        $summary['appointments'] === 1;
                }
            );
    }

    public function test_report_includes_current_compliance_snapshot(): void
    {
        $manager = $this->user(
            User::ROLE_MANAGER
        );

        DB::table('record_retentions')->insert([
            'record_title' => 'Compliance Test',
            'record_type' => 'document',
            'status' => 'review_required',
            'compliance_status' => 'at_risk',
            'disposition_status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        LegalRecord::create([
            'title' => 'Legal Action Test',
            'record_type' => 'permit',
            'status' => 'active',
            'review_status' => 'action_required',
            'expiration_date' => now()
                ->addDays(10)
                ->toDateString(),
        ]);

        Contract::create([
            'title' => 'Active Contract Test',
            'contract_type' => 'service',
            'status' => 'active',
            'end_date' => now()
                ->addDays(15)
                ->toDateString(),
        ]);

        $response = $this
            ->actingAs($manager)
            ->get('/reports');

        $response
            ->assertOk()
            ->assertViewHas(
                'summary',
                function (array $summary) {
                    return
                        $summary['retention_issues'] === 1 &&
                        $summary['pending_disposals'] === 1 &&
                        $summary['legal_action_required'] === 1 &&
                        $summary['legal_expiring_soon'] === 1 &&
                        $summary['active_contracts'] === 1 &&
                        $summary['contracts_expiring_soon'] === 1;
                }
            );
    }

    public function test_report_rejects_invalid_date_range(): void
    {
        $manager = $this->user(
            User::ROLE_MANAGER
        );

        $this->actingAs($manager)
            ->get(
                '/reports?from=2026-09-30&to=2026-09-01'
            )
            ->assertStatus(302)
            ->assertSessionHasErrors('to');
    }
}