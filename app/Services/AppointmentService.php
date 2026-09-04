<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    public function __construct(private AuditService $audit) {}

    public function create(User $user, array $data): Appointment
    {
        if (! $user->can('manageAppointments')) {
            throw ValidationException::withMessages([
                'app_role' => 'You are not authorised to manage appointments.',
            ])->status(403);
        }

        return DB::transaction(function () use ($user, $data) {
            $facility = ! empty($data['facility_id'])
                ? Facility::query()->lockForUpdate()->findOrFail($data['facility_id'])
                : null;

            if ($facility && $facility->status !== 'available') {
                throw ValidationException::withMessages(['facility_id' => 'This facility is not available.']);
            }

            if ($facility && $this->conflict($facility->id, $data['date'], $data['start_time'], $data['end_time'] ?? null)) {
                throw ValidationException::withMessages(['start_time' => 'The selected facility is already reserved for that time.']);
            }

            $data['facility_name'] = $facility?->name;
            $data['status'] = 'scheduled';
            $appointment = Appointment::create($data);

            $this->audit->log($user, 'create', 'appointments', "Appointment • {$appointment->visitor_name}", (string) $appointment->id, 'Appointment scheduled');
            return $appointment->refresh();
        });
    }

    public function update(User $user, Appointment $appointment, array $data): Appointment
    {
        if (! $user->can('manageAppointments')) {
            throw ValidationException::withMessages([
                'app_role' => 'You are not authorised to manage appointments.',
            ])->status(403);
        }

        return DB::transaction(function () use ($user, $appointment, $data) {
            $appointment->refresh();
            $allowedTransitions = [
                'scheduled' => ['scheduled', 'confirmed', 'cancelled', 'no_show'],
                'confirmed' => ['confirmed', 'cancelled', 'checked_in', 'no_show'],
                'checked_in' => ['checked_in', 'completed'],
                'completed' => ['completed'],
                'cancelled' => ['cancelled'],
                'no_show' => ['no_show'],
            ];
            $requestedStatus = $data['status'] ?? $appointment->status;
            if (! in_array($requestedStatus, $allowedTransitions[$appointment->status] ?? [], true)) {
                throw ValidationException::withMessages(['status' => 'Invalid appointment status transition.']);
            }

            $facility = ! empty($data['facility_id'])
                ? Facility::query()->lockForUpdate()->findOrFail($data['facility_id'])
                : null;
            if ($facility && $facility->status !== 'available') {
                throw ValidationException::withMessages(['facility_id' => 'This facility is not available.']);
            }
            if ($facility && $this->conflict($facility->id, $data['date'], $data['start_time'], $data['end_time'] ?? null, $appointment->id)) {
                throw ValidationException::withMessages(['start_time' => 'The selected facility is already reserved for that time.']);
            }

            $data['facility_name'] = $facility?->name;
            $data['status'] = $requestedStatus;
            $appointment->update($data);
            $this->audit->log($user, 'update', 'appointments', "Appointment • {$appointment->visitor_name}", (string) $appointment->id, "Status: {$requestedStatus}");
            return $appointment->refresh();
        });
    }

    private function conflict(int $facilityId, string $date, string $start, ?string $end, ?int $excludeId = null): bool
    {
        if (! $end) return false;
        return Appointment::query()->where('facility_id', $facilityId)->whereDate('date', $date)
            ->whereNotIn('status', ['cancelled', 'completed', 'no_show'])
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->get()->contains(fn (Appointment $a) => $this->overlap($start, $end, $a->start_time, $a->end_time));
    }

    private function overlap(string $aStart, string $aEnd, string $bStart, ?string $bEnd): bool
    {
        if (! $bEnd) return false;
        return $aStart < $bEnd && $bStart < $aEnd;
    }
}
