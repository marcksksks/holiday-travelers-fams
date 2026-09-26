<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentsWorkspaceTest extends TestCase
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

    public function test_receptionist_sees_appointment_management_workspace(): void
    {
        $receptionist =
            $this->user(
                User::ROLE_RECEPTIONIST
            );

        $this
            ->actingAs($receptionist)
            ->get(
                route('appointments.index')
            )
            ->assertOk()
            ->assertDontSee('Schedule Management')
            ->assertSee(
                'Schedule Overview'
            )
            ->assertSee('data-appointment-filter-bar', false)
            ->assertSee(
                'Appointment Schedule'
            )
            ->assertSee(
                'Schedule Appointment'
            )
            ->assertDontSee(
                'Schedule a Visit'
            )
            ->assertSee(
                'Schedule Appointment'
            );
    }

    public function test_employee_can_view_workspace_without_management_action(): void
    {
        $employee =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $this
            ->actingAs($employee)
            ->get(
                route('appointments.index')
            )
            ->assertOk()
            ->assertSee(
                'Schedule Overview'
            )
            ->assertSee('data-appointment-filter-bar', false)
            ->assertDontSee(
                'Schedule Appointment'
            );
    }

    public function test_appointment_schedule_contains_responsive_card_and_correct_scheduled_label(): void
    {
        $employee =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        Appointment::create([
            'visitor_name' => 'Responsive Appointment Visitor',

            'visitor_type' => 'guest',

            'date' => now()
                ->addDay()
                ->toDateString(),

            'start_time' => '09:00',

            'end_time' => '10:00',

            'status' => 'scheduled',
        ]);

        $this
            ->actingAs($employee)
            ->get(
                route('appointments.index')
            )
            ->assertOk()
            ->assertSee(
                'Responsive Appointment Visitor'
            )
            ->assertSee(
                'data-appointment-mobile-card',
                false
            )
            ->assertSee(
                'data-appointment-card-grid',
                false
            )
            ->assertDontSee(
                'table-shell hidden md:block',
                false
            )
            ->assertSee(
                'Scheduled'
            )
            ->assertDontSee(
                'Appointmentd'
            );
    }
}
