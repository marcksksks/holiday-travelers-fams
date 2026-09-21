<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Validation\ValidationException;

/** Port of base44/functions/visitorCheckFlow/entry.ts */
class VisitorCheckService
{
    public function __construct(
        private AuditService $audit,
        private NotificationService $notifications,
    ) {}

    public function checkIn(User $user, Visitor $visitor, ?string $badgeNumber = null, ?string $notes = null): Visitor
    {
        if (! $user->can('operateVisitorDesk')) {
            throw ValidationException::withMessages([
                'app_role' => 'You are not authorised to operate the visitor desk.',
            ])->status(403);
        }

        if ($visitor->status === 'checked_in') {
            throw ValidationException::withMessages(['status' => 'Visitor is already checked in.']);
        }
        if ($visitor->status === 'completed') {
            throw ValidationException::withMessages(['status' => 'This visit is already closed.']);
        }

        if (
            ! in_array(
                $visitor->status,
                [
                    'expected',
                    'awaiting_host',
                ],
                true
            )
        ) {
            throw ValidationException::withMessages([
                'status' => 'Only expected or awaiting-host visitors can be checked in.',
            ]);
        }

        $visitor->update([
            'status' => 'checked_in',
            'check_in_at' => now(),
            'badge_number' => $badgeNumber ?: ($visitor->badge_number ?: ''),
            'notes' => $notes ?? $visitor->notes,
        ]);

        if ($visitor->appointment_id) {
            Appointment::where('id', $visitor->appointment_id)->update(['status' => 'checked_in']);
        }

        $this->notifications->notify([[
            'recipient_email' => $visitor->host_email,
            'title' => 'Your visitor has arrived',
            'body' => "{$visitor->full_name}".($visitor->organization ? " ({$visitor->organization})" : '').' checked in at reception'.($badgeNumber ? " — badge {$badgeNumber}" : '').'.',
            'module' => 'visitors',
            'severity' => 'success',
            'link' => '/visitors',
        ]]);

        $this->audit->log($user, 'check_in', 'visitors', "Visitor • {$visitor->full_name}", (string) $visitor->id, 'Host '.($visitor->host_email ?: 'unassigned'));

        return $visitor->refresh();
    }

    public function checkOut(User $user, Visitor $visitor): Visitor
    {
        if (! $user->can('operateVisitorDesk')) {
            throw ValidationException::withMessages([
                'app_role' => 'You are not authorised to operate the visitor desk.',
            ])->status(403);
        }

        if ($visitor->status !== 'checked_in') {
            throw ValidationException::withMessages(['status' => 'Only checked-in visitors can be checked out.']);
        }

        $start = $visitor->check_in_at ?? now();
        $duration = max(0, (int) round($start->diffInSeconds(now()) / 60));

        $visitor->update([
            'status' => 'completed',
            'check_out_at' => now(),
            'duration_minutes' => $duration,
        ]);

        if ($visitor->appointment_id) {
            Appointment::where('id', $visitor->appointment_id)->update(['status' => 'completed']);
        }

        $this->notifications->notify([[
            'recipient_email' => $visitor->host_email,
            'title' => 'Visitor checked out',
            'body' => "{$visitor->full_name} left after {$duration} minutes.",
            'module' => 'visitors',
            'severity' => 'info',
            'link' => '/visitors',
        ]]);

        $this->audit->log($user, 'check_out', 'visitors', "Visitor • {$visitor->full_name}", (string) $visitor->id, "Duration {$duration} min");

        return $visitor->refresh();
    }

    public function decline(User $user, Visitor $visitor, ?string $notes = null): Visitor
    {
        if (! $user->can('operateVisitorDesk')) {
            throw ValidationException::withMessages([
                'app_role' => 'You are not authorised to operate the visitor desk.',
            ])->status(403);
        }

        if (
            ! in_array(
                $visitor->status,
                [
                    'expected',
                    'awaiting_host',
                ],
                true
            )
        ) {
            throw ValidationException::withMessages([
                'status' => 'Only expected or awaiting-host visitors can be declined.',
            ]);
        }

        $visitor->update([
            'status' => 'declined',
            'notes' => $notes ?? $visitor->notes,
        ]);

        $this->audit->log($user, 'update', 'visitors', "Visitor • {$visitor->full_name}", (string) $visitor->id, 'Visit declined at reception');

        return $visitor->refresh();
    }
}
