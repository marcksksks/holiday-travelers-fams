<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class OperationalReminderService
{
    private const WARNING_MINUTES = 15;

    public function __construct(
        private NotificationService $notifications
    ) {}

    /**
     * Process operational reminders that are due now.
     *
     * @return array<string, int>
     */
    public function run(): array
    {
        $now = now();

        $result = [
            'reservations_checked' => 0,
            'reservation_alert_targets' => 0,
            'visitors_checked' => 0,
            'visitor_alert_targets' => 0,
        ];

        $this->processReservationReminders(
            $now,
            $result
        );

        $this->processVisitorReminders(
            $now,
            $result
        );

        return $result;
    }

    /**
     * Notify the requester and operational administrators
     * when an approved reservation is within 15 minutes
     * of its scheduled end time.
     *
     * @param  array<string, int>  $result
     */
    private function processReservationReminders(
        Carbon $now,
        array &$result
    ): void {
        $reservations =
            Reservation::with('facility')
                ->where(
                    'status',
                    'approved'
                )
                ->whereDate(
                    'date',
                    $now->toDateString()
                )
                ->whereNotNull(
                    'end_time'
                )
                ->get();

        $result['reservations_checked'] =
            $reservations->count();

        foreach ($reservations as $reservation) {

            $endsAt =
                $this->dateTime(
                    $reservation->date,
                    $reservation->end_time
                );

            if (! $this->isInsideWarningWindow(
                $now,
                $endsAt
            )) {
                continue;
            }

            $recipientEmails =
                $this->operationalAdminEmails();

            if (filled(
                $reservation->requester_email
            )) {

                $recipientEmails->push(
                    strtolower(
                        trim(
                            $reservation->requester_email
                        )
                    )
                );

            }

            $recipientEmails =
                $recipientEmails
                    ->filter()
                    ->unique()
                    ->values();

            $minutesRemaining =
                $this->minutesRemaining(
                    $now,
                    $endsAt
                );

            $facilityName =
                $reservation->facility_name
                ?: $reservation->facility?->name
                ?: 'Facility';

            $endLabel =
                $endsAt->format('h:i A');

            $key =
                'reservation-ending-soon:'
                .$reservation->id
                .':'
                .$endsAt->format('YmdHi');

            $items =
                $recipientEmails
                    ->map(
                        fn (string $email) => [
                            'recipient_email' => $email,

                            'title' => 'Reservation Ending Soon',

                            'body' => "{$facilityName} reservation ends in approximately {$minutesRemaining} minute(s) at {$endLabel}. Please prepare for checkout or facility turnover.",

                            'module' => 'facilities',

                            'severity' => 'warning',

                            'link' => '/reservations',

                            'key' => $key,
                        ]
                    )
                    ->all();

            $this->notifications
                ->notifyOnce(
                    $items
                );

            $result['reservation_alert_targets'] +=
                count($items);
        }
    }

    /**
     * Notify visitor-desk users and the internal host
     * when a checked-in appointment visitor is within
     * 15 minutes of the appointment end time.
     *
     * Walk-in visitors are skipped because they currently
     * have no planned checkout timestamp.
     *
     * @param  array<string, int>  $result
     */
    private function processVisitorReminders(
        Carbon $now,
        array &$result
    ): void {
        $visitors =
            Visitor::with('appointment')
                ->where(
                    'status',
                    'checked_in'
                )
                ->whereNotNull(
                    'appointment_id'
                )
                ->get();

        $result['visitors_checked'] =
            $visitors->count();

        foreach ($visitors as $visitor) {

            $appointment =
                $visitor->appointment;

            if (
                ! $appointment ||
                ! filled($appointment->end_time)
            ) {
                continue;
            }

            $appointmentDate =
                $appointment->date;

            $endsAt =
                $this->dateTime(
                    $appointmentDate,
                    $appointment->end_time
                );

            if (! $this->isInsideWarningWindow(
                $now,
                $endsAt
            )) {
                continue;
            }

            $recipientEmails =
                $this->visitorOperationsEmails();

            /*
             * Add the host only when host_email belongs
             * to an active user in this system.
             */
            if (filled($visitor->host_email)) {

                $hostEmail =
                    strtolower(
                        trim(
                            $visitor->host_email
                        )
                    );

                $internalHost =
                    User::query()
                        ->where(
                            'is_active',
                            true
                        )
                        ->whereRaw(
                            'LOWER(email) = ?',
                            [
                                $hostEmail,
                            ]
                        )
                        ->first();

                if ($internalHost) {

                    $recipientEmails->push(
                        strtolower(
                            $internalHost->email
                        )
                    );

                }

            }

            $recipientEmails =
                $recipientEmails
                    ->filter()
                    ->unique()
                    ->values();

            $minutesRemaining =
                $this->minutesRemaining(
                    $now,
                    $endsAt
                );

            $visitorName =
                $visitor->full_name
                ?: 'Visitor';

            $endLabel =
                $endsAt->format('h:i A');

            $key =
                'visitor-checkout-soon:'
                .$visitor->id
                .':appointment:'
                .$appointment->id
                .':'
                .$endsAt->format('YmdHi');

            $items =
                $recipientEmails
                    ->map(
                        fn (string $email) => [
                            'recipient_email' => $email,

                            'title' => 'Visitor Checkout Approaching',

                            'body' => "{$visitorName} is scheduled to finish the visit in approximately {$minutesRemaining} minute(s) at {$endLabel}. Please prepare for visitor checkout.",

                            'module' => 'visitors',

                            'severity' => 'warning',

                            'link' => '/visitors',

                            'key' => $key,
                        ]
                    )
                    ->all();

            $this->notifications
                ->notifyOnce(
                    $items
                );

            $result['visitor_alert_targets'] +=
                count($items);
        }
    }

    /**
     * Administrative recipients for facility reservation
     * expiry warnings.
     *
     * @return Collection<int, string>
     */
    private function operationalAdminEmails(): Collection
    {
        return User::query()
            ->where(
                'is_active',
                true
            )
            ->whereIn(
                'app_role',
                [
                    User::ROLE_ADMIN_OFFICER,
                    User::ROLE_MANAGER,
                    User::ROLE_SYS_ADMIN,
                ]
            )
            ->pluck('email')
            ->map(
                fn ($email) => strtolower(
                    trim(
                        (string) $email
                    )
                )
            );
    }

    /**
     * Visitor-desk recipients.
     *
     * @return Collection<int, string>
     */
    private function visitorOperationsEmails(): Collection
    {
        return User::query()
            ->where(
                'is_active',
                true
            )
            ->whereIn(
                'app_role',
                [
                    User::ROLE_RECEPTIONIST,
                    User::ROLE_ADMIN_OFFICER,
                    User::ROLE_MANAGER,
                    User::ROLE_SYS_ADMIN,
                ]
            )
            ->pluck('email')
            ->map(
                fn ($email) => strtolower(
                    trim(
                        (string) $email
                    )
                )
            );
    }

    /**
     * The scheduled task can run a few seconds late,
     * therefore reminders use a range of:
     *
     *     more than 0 seconds remaining
     *     up to and including 15 minutes remaining
     */
    private function isInsideWarningWindow(
        Carbon $now,
        Carbon $endsAt
    ): bool {
        $secondsRemaining =
            $now->diffInSeconds(
                $endsAt,
                false
            );

        return
            $secondsRemaining > 0
            &&
            $secondsRemaining <=
                self::WARNING_MINUTES * 60;
    }

    private function minutesRemaining(
        Carbon $now,
        Carbon $endsAt
    ): int {
        $secondsRemaining =
            max(
                0,
                $now->diffInSeconds(
                    $endsAt,
                    false
                )
            );

        return max(
            1,
            (int) ceil(
                $secondsRemaining / 60
            )
        );
    }

    private function dateTime(
        mixed $date,
        mixed $time
    ): Carbon {
        $dateValue =
            $date instanceof Carbon
                ? $date->toDateString()
                : Carbon::parse(
                    $date,
                    config(
                        'app.timezone',
                        'Asia/Manila'
                    )
                )->toDateString();

        $timeValue =
            substr(
                (string) $time,
                0,
                8
            );

        return Carbon::parse(
            $dateValue
            .' '
            .$timeValue,
            config(
                'app.timezone',
                'Asia/Manila'
            )
        );
    }
}
