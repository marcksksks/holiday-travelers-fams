<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Facility;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    public function __construct(
        private AuditService $audit,
        private FacilityAvailabilityService $availability,
    ) {}

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

            if (
                $facility
                &&
                empty($data['end_time'])
            ) {
                throw ValidationException::withMessages([
                    'end_time' => 'An end time is required when a facility is assigned to an appointment.',
                ]);
            }

            if ($facility) {
                $conflict = $this->availability->findConflict(
                    $facility->id,
                    $data['date'],
                    $data['start_time'],
                    $data['end_time'] ?? null
                );

                if ($conflict) {
                    $source =
                        $conflict['source'] === 'appointment'
                            ? 'another appointment'
                            : 'a facility reservation';

                    throw ValidationException::withMessages([
                        'start_time' => "Time conflict: {$facility->name} is already occupied by {$source} from {$conflict['start_time']} to {$conflict['end_time']}.",
                    ]);
                }
            }

            $data['facility_name'] = $facility?->name;
            $data['status'] = 'scheduled';

            /*
             * Create the appointment first because the visitor
             * stores the appointment_id foreign key.
             */
            $appointment = Appointment::create($data);

            /*
             * Every scheduled appointment represents an expected
             * visitor at reception.
             *
             * This is not a walk-in and does not perform check-in.
             * Visitor Desk remains responsible for arrival/departure.
             */
            $visitor = Visitor::create([
                'full_name' => $appointment->visitor_name,

                'contact_number' => $appointment->visitor_contact,

                'email' => $appointment->visitor_email,

                'organization' => $appointment->visitor_organization,

                'visitor_type' => $appointment->visitor_type ?: 'guest',

                'purpose' => $appointment->purpose,

                'host_email' => $appointment->host_email,

                'host_name' => $appointment->host_name,

                'appointment_id' => $appointment->id,

                'is_walk_in' => false,

                'status' => 'expected',

                'notes' => $appointment->notes,
            ]);

            /*
             * Complete the reverse relationship:
             *
             * Appointment -> Visitor
             * Visitor     -> Appointment
             */
            $appointment->update([
                'visitor_id' => $visitor->id,
            ]);

            $this->audit->log(
                $user,
                'create',
                'visitors',
                "Visitor • {$visitor->full_name}",
                (string) $visitor->id,
                "Expected visitor created from appointment #{$appointment->id}"
            );

            $this->audit->log(
                $user,
                'create',
                'appointments',
                "Appointment • {$appointment->visitor_name}",
                (string) $appointment->id,
                'Appointment scheduled'
            );

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

            if (
                $facility
                &&
                empty($data['end_time'])
            ) {
                throw ValidationException::withMessages([
                    'end_time' => 'An end time is required when a facility is assigned to an appointment.',
                ]);
            }
            if ($facility) {
                $conflict = $this->availability->findConflict(
                    $facility->id,
                    $data['date'],
                    $data['start_time'],
                    $data['end_time'] ?? null,
                    excludeAppointmentId: $appointment->id
                );

                if ($conflict) {
                    $source =
                        $conflict['source'] === 'appointment'
                            ? 'another appointment'
                            : 'a facility reservation';

                    throw ValidationException::withMessages([
                        'start_time' => "Time conflict: {$facility->name} is already occupied by {$source} from {$conflict['start_time']} to {$conflict['end_time']}.",
                    ]);
                }
            }

            $data['facility_name'] = $facility?->name;
            $data['status'] = $requestedStatus;
            $appointment->update($data);
            $appointment->refresh();

            $this->syncLinkedVisitor(
                $user,
                $appointment
            );
            $this->audit->log($user, 'update', 'appointments', "Appointment • {$appointment->visitor_name}", (string) $appointment->id, "Status: {$requestedStatus}");

            return $appointment->refresh();
        });
    }

    public function transition(
        User $user,
        Appointment $appointment,
        string $status
    ): Appointment {
        if (! $user->can('manageAppointments')) {
            throw ValidationException::withMessages([
                'app_role' => 'You are not authorised to manage appointments.',
            ])->status(403);
        }

        return DB::transaction(
            function () use (
                $user,
                $appointment,
                $status
            ) {
                $appointment =
                    Appointment::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $appointment->id
                        );

                $allowedTransitions = [
                    'scheduled' => [
                        'confirmed',
                        'cancelled',
                        'no_show',
                    ],
                    'confirmed' => [
                        'cancelled',
                        'checked_in',
                        'no_show',
                    ],
                    'checked_in' => [
                        'completed',
                    ],
                    'completed' => [],
                    'cancelled' => [],
                    'no_show' => [],
                ];

                if (
                    ! in_array(
                        $status,
                        $allowedTransitions[
                            $appointment->status
                        ] ?? [],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'status' => 'Invalid appointment status transition.',
                    ]);
                }

                $appointment->update([
                    'status' => $status,
                ]);

                $appointment->refresh();

                $this->syncLinkedVisitor(
                    $user,
                    $appointment
                );

                $this->audit->log(
                    $user,
                    'update',
                    'appointments',
                    "Appointment • {$appointment->visitor_name}",
                    (string) $appointment->id,
                    'Status: '.$status
                );

                return $appointment->refresh();
            }
        );
    }

    /**
     * Keep the pre-arrival Visitor record synchronized with
     * its linked Appointment.
     *
     * Reception-owned lifecycle data is protected once the
     * visitor has been checked in, completed, or declined.
     */
    private function syncLinkedVisitor(
        User $user,
        Appointment $appointment
    ): void {
        $visitor =
            $appointment->visitor;

        if (! $visitor) {
            return;
        }

        /*
         * Once reception has taken operational control of
         * the visit, appointment edits must not overwrite it.
         */
        if (
            in_array(
                $visitor->status,
                [
                    'checked_in',
                    'completed',
                    'declined',
                ],
                true
            )
        ) {
            return;
        }

        $nextStatus =
            match ($appointment->status) {
                'cancelled' => 'cancelled',

                'no_show' => 'no_show',

                'scheduled',
                'confirmed' => in_array(
                    $visitor->status,
                    [
                        'expected',
                        'awaiting_host',
                    ],
                    true
                )
                        ? $visitor->status
                        : 'expected',

                default => $visitor->status,
            };

        $visitor->fill([
            'full_name' => $appointment->visitor_name,

            'contact_number' => $appointment->visitor_contact,

            'email' => $appointment->visitor_email,

            'organization' => $appointment->visitor_organization,

            'visitor_type' => $appointment->visitor_type ?: 'guest',

            'purpose' => $appointment->purpose,

            'host_email' => $appointment->host_email,

            'host_name' => $appointment->host_name,

            'is_walk_in' => false,

            'status' => $nextStatus,
        ]);

        /*
         * Do not generate duplicate audit records when an
         * appointment edit did not actually change Visitor data.
         */
        if (! $visitor->isDirty()) {
            return;
        }

        $visitor->save();

        $this->audit->log(
            $user,
            'update',
            'visitors',
            "Visitor • {$visitor->full_name}",
            (string) $visitor->id,
            "Synchronized from appointment #{$appointment->id}; status: {$nextStatus}"
        );
    }

    private function conflict(int $facilityId, string $date, string $start, ?string $end, ?int $excludeId = null): bool
    {
        if (! $end) {
            return false;
        }

        return Appointment::query()->where('facility_id', $facilityId)->whereDate('date', $date)
            ->whereNotIn('status', ['cancelled', 'completed', 'no_show'])
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->get()->contains(fn (Appointment $a) => $this->overlap($start, $end, $a->start_time, $a->end_time));
    }

    private function overlap(string $aStart, string $aEnd, string $bStart, ?string $bEnd): bool
    {
        if (! $bEnd) {
            return false;
        }

        return $aStart < $bEnd && $bStart < $aEnd;
    }
}
