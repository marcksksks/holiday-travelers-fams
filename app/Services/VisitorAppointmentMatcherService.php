<?php

namespace App\Services;

use App\Models\Appointment;

class VisitorAppointmentMatcherService
{
    private const ACTIVE_STATUSES = [
        'scheduled',
        'confirmed',
    ];

    public function match(array $identity): array
    {
        $email = $this->normalize(
            $identity['email'] ?? null
        );

        $fullName = $this->normalize(
            $identity['full_name'] ?? null
        );

        if ($email === '' && $fullName === '') {
            return [
                'status' => 'not_checked',
                'appointment' => null,
                'mismatches' => [],
                'suggested_host' => $this->contextHost(
                    $identity
                ),
            ];
        }

        $query = Appointment::query()
            ->whereDate(
                'date',
                today()
            )
            ->whereIn(
                'status',
                self::ACTIVE_STATUSES
            );

        if ($email !== '') {
            $query->whereRaw(
                "LOWER(COALESCE(visitor_email, '')) = ?",
                [$email]
            );
        } else {
            $query->whereRaw(
                'LOWER(visitor_name) = ?',
                [$fullName]
            );
        }

        $candidates = $query
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        if ($candidates->isEmpty()) {
            return [
                'status' => 'not_found',
                'appointment' => null,
                'mismatches' => [],
                'suggested_host' => $this->contextHost(
                    $identity
                ),
            ];
        }

        $appointment = $candidates
            ->sortByDesc(
                fn (Appointment $candidate): int => $this->score(
                    $candidate,
                    $identity
                )
            )
            ->first();

        $mismatches =
            $this->mismatches(
                $appointment,
                $identity
            );

        if ($candidates->count() > 1) {
            $mismatches[] =
                'Multiple active appointments match the visitor identity; verify the correct appointment.';
        }

        $status =
            empty($mismatches)
            && $candidates->count() === 1
                ? 'matched'
                : 'partial';

        return [
            'status' => $status,

            'appointment' => [
                'id' => $appointment->id,

                'visitor_name' => $appointment->visitor_name,

                'date' => $appointment->date
                    ?->format('Y-m-d'),

                'start_time' => $appointment->start_time,

                'end_time' => $appointment->end_time,

                'host_name' => $appointment->host_name,

                'host_email' => $appointment->host_email,

                'facility_name' => $appointment->facility_name,

                'status' => $appointment->status,
            ],

            'mismatches' => array_values(
                array_unique(
                    $mismatches
                )
            ),

            'suggested_host' => $appointment->host_name
                ?: $appointment->host_email
                ?: $this->contextHost(
                    $identity
                ),
        ];
    }

    private function score(
        Appointment $appointment,
        array $identity
    ): int {
        $score = 0;

        if (
            $this->same(
                $identity['email'] ?? null,
                $appointment->visitor_email
            )
        ) {
            $score += 4;
        }

        if (
            $this->same(
                $identity['full_name'] ?? null,
                $appointment->visitor_name
            )
        ) {
            $score += 3;
        }

        if (
            $this->same(
                $identity['host_email'] ?? null,
                $appointment->host_email
            )
        ) {
            $score += 2;
        }

        if (
            $this->same(
                $identity['organization'] ?? null,
                $appointment->visitor_organization
            )
        ) {
            $score++;
        }

        return $score;
    }

    private function mismatches(
        Appointment $appointment,
        array $identity
    ): array {
        $mismatches = [];

        $this->compare(
            $mismatches,
            'Visitor name',
            $identity['full_name'] ?? null,
            $appointment->visitor_name
        );

        $this->compare(
            $mismatches,
            'Visitor email',
            $identity['email'] ?? null,
            $appointment->visitor_email
        );

        $this->compare(
            $mismatches,
            'Organization',
            $identity['organization'] ?? null,
            $appointment->visitor_organization
        );

        $this->compare(
            $mismatches,
            'Host email',
            $identity['host_email'] ?? null,
            $appointment->host_email
        );

        $this->compare(
            $mismatches,
            'Host name',
            $identity['host_name'] ?? null,
            $appointment->host_name
        );

        return $mismatches;
    }

    private function compare(
        array &$mismatches,
        string $label,
        mixed $provided,
        mixed $scheduled
    ): void {
        $providedValue =
            $this->normalize($provided);

        $scheduledValue =
            $this->normalize($scheduled);

        if (
            $providedValue === ''
            || $scheduledValue === ''
        ) {
            return;
        }

        if ($providedValue !== $scheduledValue) {
            $mismatches[] =
                "{$label} differs from the appointment record.";
        }
    }

    private function contextHost(
        array $identity
    ): ?string {
        $hostName =
            trim(
                (string) (
                    $identity['host_name']
                    ?? ''
                )
            );

        if ($hostName !== '') {
            return $hostName;
        }

        $hostEmail =
            trim(
                (string) (
                    $identity['host_email']
                    ?? ''
                )
            );

        return $hostEmail !== ''
            ? $hostEmail
            : null;
    }

    private function same(
        mixed $first,
        mixed $second
    ): bool {
        $firstValue =
            $this->normalize($first);

        $secondValue =
            $this->normalize($second);

        return $firstValue !== ''
            && $secondValue !== ''
            && $firstValue === $secondValue;
    }

    private function normalize(
        mixed $value
    ): string {
        return mb_strtolower(
            trim(
                preg_replace(
                    '/\s+/u',
                    ' ',
                    (string) $value
                ) ?? ''
            )
        );
    }
}
