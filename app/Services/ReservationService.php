<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function __construct(
        private AuditService $audit,
        private NotificationService $notifications,
    ) {}

    /** Port of base44/functions/submitReservation/entry.ts */
    public function submit(User $user, array $data): Reservation
    {
        return DB::transaction(function () use ($user, $data) {
            $facility = Facility::query()->lockForUpdate()->findOrFail($data['facility_id']);

            if ($facility->status !== 'available') {
                throw ValidationException::withMessages(['facility_id' => 'This facility is currently not available for booking.']);
            }
            if ($data['date'] < now()->toDateString()) {
                throw ValidationException::withMessages(['date' => 'Reservation date cannot be in the past.']);
            }
            if ($this->toMinutes($data['end_time']) <= $this->toMinutes($data['start_time'])) {
                throw ValidationException::withMessages(['end_time' => 'End time must be after the start time.']);
            }
            if (isset($data['attendees']) && $facility->capacity !== null && $data['attendees'] > $facility->capacity) {
                throw ValidationException::withMessages(['attendees' => "The facility capacity is {$facility->capacity}."]);
            }

            $clash = $this->findClash($facility->id, $data['date'], $data['start_time'], $data['end_time'], ['pending', 'approved']);
            if ($clash) {
                throw ValidationException::withMessages(['start_time' => "Time conflict: {$facility->name} is already booked {$clash->start_time}–{$clash->end_time} ({$clash->status})."]);
            }

            $reservation = Reservation::create([
                'facility_id' => $facility->id, 'facility_name' => $facility->name,
                'requester_email' => $user->email, 'requester_name' => $user->full_name,
                'date' => $data['date'], 'start_time' => $data['start_time'], 'end_time' => $data['end_time'],
                'attendees' => $data['attendees'] ?? null, 'purpose' => $data['purpose'] ?? '', 'status' => 'pending',
            ]);

            $officers = $this->notifications->usersByRole(User::ROLE_ADMIN_OFFICER);
            $this->notifications->notify($officers->map(fn ($o) => [
                'recipient_email' => $o->email, 'title' => 'Facility reservation request',
                'body' => "{$user->full_name} requested {$facility->name} on {$data['date']}, {$data['start_time']}–{$data['end_time']}.",
                'module' => 'facilities', 'severity' => 'info', 'link' => '/reservations',
            ])->all());
            $this->audit->log($user, 'create', 'facilities', "Reservation • {$facility->name} {$data['date']}", (string) $reservation->id, "{$data['start_time']}–{$data['end_time']} — ".($data['purpose'] ?? 'no purpose given'));
            return $reservation;
        });
    }

    /** Port of base44/functions/decideReservation/entry.ts */
    public function decide(User $user, Reservation $reservation, string $decision, ?string $note = null): Reservation
    {
        if (! $user->can('decideReservations')) {
            throw ValidationException::withMessages([
                'app_role' => 'You are not authorised to decide reservation requests.',
            ])->status(403);
        }

        if (! in_array($decision, ['approved', 'rejected'], true)) {
            throw ValidationException::withMessages(['decision' => 'A valid decision is required.']);
        }
        if ($reservation->requester_email === $user->email) {
            throw ValidationException::withMessages(['requester_email' => 'You cannot approve your own reservation request.']);
        }

        $reservation = DB::transaction(function () use ($reservation, $decision, $user, $note) {
            $reservation = Reservation::query()->lockForUpdate()->findOrFail($reservation->id);
            if ($reservation->status !== 'pending') {
                throw ValidationException::withMessages(['status' => 'This request has already been decided.']);
            }
            $facility = Facility::query()->lockForUpdate()->findOrFail($reservation->facility_id);
            if ($decision === 'approved') {
                if ($facility->status !== 'available') {
                    throw ValidationException::withMessages(['facility_id' => 'This facility is no longer available.']);
                }
                if ($facility->capacity !== null && $reservation->attendees !== null && $reservation->attendees > $facility->capacity) {
                    throw ValidationException::withMessages(['attendees' => "The facility capacity is {$facility->capacity}."]);
                }
                $clash = $this->findClash($reservation->facility_id, $reservation->date->toDateString(), $reservation->start_time, $reservation->end_time, ['approved'], excludeId: $reservation->id);
                if ($clash) {
                    throw ValidationException::withMessages(['start_time' => "Cannot approve — an approved booking already occupies {$clash->start_time}–{$clash->end_time}."]);
                }
            }
            $reservation->update([
                'status' => $decision,
                'decision_by_email' => $user->email,
                'decision_at' => now(),
                'decision_note' => $note ?? '',
            ]);
            return $reservation;
        });

        $this->notifications->notify([[
            'recipient_email' => $reservation->requester_email,
            'title' => "Reservation {$decision}",
            'body' => "{$reservation->facility_name} on {$reservation->date->toDateString()} ({$reservation->start_time}–{$reservation->end_time}) was {$decision}.".($note ? " Note: {$note}" : ''),
            'module' => 'facilities', 'severity' => $decision === 'approved' ? 'success' : 'warning', 'link' => '/reservations',
        ]]);
        $this->audit->log($user, $decision === 'approved' ? 'approve' : 'reject', 'facilities', "Reservation • {$reservation->facility_name} {$reservation->date->toDateString()}", (string) $reservation->id, $note ?? '');
        return $reservation->refresh();
    }

    private function findClash(int $facilityId, string $date, string $start, string $end, array $statuses, ?int $excludeId = null): ?Reservation
    {
        return Reservation::where('facility_id', $facilityId)
            ->whereDate('date', $date)
            ->whereIn('status', $statuses)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->get()
            ->first(fn (Reservation $r) => $this->overlaps($start, $end, $r->start_time, $r->end_time));
    }

    private function toMinutes(string $hhmm): int
    {
        [$h, $m] = array_pad(explode(':', $hhmm), 2, 0);

        return ((int) $h) * 60 + (int) $m;
    }

    private function overlaps(string $aStart, string $aEnd, string $bStart, string $bEnd): bool
    {
        return $this->toMinutes($aStart) < $this->toMinutes($bEnd) && $this->toMinutes($bStart) < $this->toMinutes($aEnd);
    }
}
