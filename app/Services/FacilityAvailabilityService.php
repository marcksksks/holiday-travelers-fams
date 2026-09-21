<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Reservation;

class FacilityAvailabilityService
{
    public function findConflict(
        int $facilityId,
        string $date,
        string $start,
        ?string $end,
        ?int $excludeAppointmentId = null,
        ?int $excludeReservationId = null,
        array $appointmentStatuses = [
            'scheduled',
            'confirmed',
            'checked_in',
        ],
        array $reservationStatuses = [
            'pending',
            'approved',
        ],
    ): ?array {
        if (! $end) {
            return null;
        }

        $reservation = Reservation::query()
            ->where('facility_id', $facilityId)
            ->whereDate('date', $date)
            ->whereIn(
                'status',
                $reservationStatuses
            )
            ->when(
                $excludeReservationId,
                fn ($query) => $query->where(
                    'id',
                    '!=',
                    $excludeReservationId
                )
            )
            ->get()
            ->first(
                fn (Reservation $reservation) => $this->overlaps(
                    $start,
                    $end,
                    $reservation->start_time,
                    $reservation->end_time
                )
            );

        if ($reservation) {
            return [
                'source' => 'reservation',
                'id' => $reservation->id,
                'status' => $reservation->status,
                'start_time' => $reservation->start_time,
                'end_time' => $reservation->end_time,
            ];
        }

        $appointment = Appointment::query()
            ->where('facility_id', $facilityId)
            ->whereDate('date', $date)
            ->whereIn(
                'status',
                $appointmentStatuses
            )
            ->when(
                $excludeAppointmentId,
                fn ($query) => $query->where(
                    'id',
                    '!=',
                    $excludeAppointmentId
                )
            )
            ->get()
            ->first(
                fn (Appointment $appointment) => $this->overlaps(
                    $start,
                    $end,
                    $appointment->start_time,
                    $appointment->end_time
                )
            );

        if ($appointment) {
            return [
                'source' => 'appointment',
                'id' => $appointment->id,
                'status' => $appointment->status,
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
            ];
        }

        return null;
    }

    private function overlaps(
        string $startA,
        string $endA,
        string $startB,
        ?string $endB
    ): bool {
        if (! $endB) {
            return false;
        }

        return
            $startA < $endB
            &&
            $startB < $endA;
    }
}
