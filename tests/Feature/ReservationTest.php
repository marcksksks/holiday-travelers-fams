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
}
