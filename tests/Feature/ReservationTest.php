<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_overlapping_reservations_are_rejected(): void
    {
        $facility = Facility::factory()->create(['status' => 'available']);
        $requester = User::factory()->create();
        $service = app(ReservationService::class);

        $service->submit($requester, [
            'facility_id' => $facility->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $this->expectException(ValidationException::class);

        $service->submit($requester, [
            'facility_id' => $facility->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '09:30',
            'end_time' => '10:30',
        ]);
    }


    public function test_employee_cannot_view_another_users_reservation_via_api(): void
    {
        $facility = Facility::factory()->create(['status' => 'available']);
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $reservation = app(ReservationService::class)->submit($owner, [
            'facility_id' => $facility->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $this->actingAs($otherUser, 'sanctum')
            ->getJson("/api/reservations/{$reservation->id}")
            ->assertForbidden();
    }

    public function test_requester_cannot_approve_their_own_reservation(): void
    {
        $facility = Facility::factory()->create(['status' => 'available']);
        $requester = User::factory()->role(User::ROLE_ADMIN_OFFICER)->create();
        $service = app(ReservationService::class);

        $reservation = $service->submit($requester, [
            'facility_id' => $facility->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $this->expectException(ValidationException::class);
        $service->decide($requester, $reservation, 'approved');
    }

    public function test_reservation_form_requires_attendees_and_purpose(): void
    {
        $facility = Facility::factory()->create([
            'status' => 'available',
            'capacity' => 20,
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/reservations', [
                'facility_id' => $facility->id,
                'date' => now()->addDay()->toDateString(),
                'start_time' => '09:00',
                'end_time' => '10:00',
            ])
            ->assertSessionHasErrors([
                'attendees',
                'purpose',
            ]);
    }


    public function test_owner_can_edit_and_resubmit_rejected_reservation(): void
    {
        $facility = Facility::factory()->create([
            'status' => 'available',
            'capacity' => 20,
        ]);

        $user = User::factory()->create();

        $reservation = app(ReservationService::class)->submit($user, [
            'facility_id' => $facility->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'attendees' => 5,
            'purpose' => 'Original purpose',
        ]);

        $reservation->update([
            'status' => 'rejected',
            'decision_by_email' => 'reviewer@example.test',
            'decision_at' => now(),
            'decision_note' => 'Please revise.',
        ]);

        $updated = app(ReservationService::class)->resubmit(
            $user,
            $reservation,
            [
                'facility_id' => $facility->id,
                'date' => now()->addDays(2)->toDateString(),
                'start_time' => '11:00',
                'end_time' => '12:30',
                'attendees' => 8,
                'purpose' => 'Revised purpose',
            ]
        );

        $this->assertSame('pending', $updated->status);
        $this->assertSame(8, $updated->attendees);
        $this->assertSame('Revised purpose', $updated->purpose);
        $this->assertNull($updated->decision_by_email);
        $this->assertNull($updated->decision_at);
        $this->assertNull($updated->decision_note);
    }


    public function test_user_cannot_edit_another_users_reservation(): void
    {
        $facility = Facility::factory()->create([
            'status' => 'available',
            'capacity' => 20,
        ]);

        $owner = User::factory()->create();
        $other = User::factory()->create();

        $reservation = app(ReservationService::class)->submit($owner, [
            'facility_id' => $facility->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'attendees' => 5,
            'purpose' => 'Owner request',
        ]);

        $this->expectException(ValidationException::class);

        app(ReservationService::class)->resubmit(
            $other,
            $reservation,
            [
                'facility_id' => $facility->id,
                'date' => now()->addDays(2)->toDateString(),
                'start_time' => '11:00',
                'end_time' => '12:00',
                'attendees' => 6,
                'purpose' => 'Unauthorized change',
            ]
        );
    }


    public function test_approved_reservation_cannot_be_edited_and_resubmitted(): void
    {
        $facility = Facility::factory()->create([
            'status' => 'available',
            'capacity' => 20,
        ]);

        $owner = User::factory()->create();

        $reservation = app(ReservationService::class)->submit($owner, [
            'facility_id' => $facility->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'attendees' => 5,
            'purpose' => 'Approved request',
        ]);

        $reservation->update([
            'status' => 'approved',
        ]);

        $this->expectException(ValidationException::class);

        app(ReservationService::class)->resubmit(
            $owner,
            $reservation,
            [
                'facility_id' => $facility->id,
                'date' => now()->addDays(2)->toDateString(),
                'start_time' => '11:00',
                'end_time' => '12:00',
                'attendees' => 6,
                'purpose' => 'Attempted revision',
            ]
        );
    }
}
