<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class VisitorForecastDemoDataService
{
    public const MARKER = '[SYNTHETIC_VISITOR_FORECAST_DEMO]';

    public function status(): array
    {
        $visitors =
            DB::table('visitors')
                ->where(
                    'notes',
                    'like',
                    self::MARKER.'%'
                )
                ->count();

        $appointments =
            DB::table('appointments')
                ->where(
                    'notes',
                    'like',
                    self::MARKER.'%'
                )
                ->count();

        $reservations =
            DB::table('reservations')
                ->where(
                    'purpose',
                    'like',
                    self::MARKER.'%'
                )
                ->count();

        return [
            'visitors' => $visitors,

            'appointments' => $appointments,

            'reservations' => $reservations,

            'total' => $visitors
                +
                $appointments
                +
                $reservations,

            'active' => (
                $visitors
                +
                $appointments
                +
                $reservations
            ) > 0,
        ];
    }

    public function seed(
        CarbonImmutable|string|null $referenceDate = null
    ): array {
        $this->assertSafeEnvironment();

        $today =
            $referenceDate instanceof CarbonImmutable
                ? $referenceDate->startOfDay()
                : CarbonImmutable::parse(
                    $referenceDate
                        ?? now()->toDateString()
                )->startOfDay();

        return DB::transaction(
            function () use ($today): array {
                /*
                 * Re-seeding is deterministic.
                 * Remove only records carrying the synthetic marker.
                 */
                $this->purgeInternal();

                $facility =
                    DB::table('facilities')
                        ->where(
                            'status',
                            'available'
                        )
                        ->orderBy('id')
                        ->first();

                $visitorRows = [];
                $appointmentRows = [];
                $reservationRows = [];

                $visitorSequence = 1;
                $appointmentSequence = 1;

                /*
                 * Build 70 days of deterministic historical activity.
                 *
                 * Weekday pattern:
                 * - quiet opening period
                 * - stronger 09:00-11:00 traffic
                 * - smaller afternoon peak
                 *
                 * Weekends remain intentionally light.
                 *
                 * These are synthetic demonstration observations only.
                 */
                for (
                    $daysAgo = 70;
                    $daysAgo >= 1;
                    $daysAgo--
                ) {
                    $date =
                        $today->subDays(
                            $daysAgo
                        );

                    $weekday =
                        (int) $date->dayOfWeekIso;

                    if ($weekday <= 5) {
                        $pattern = [
                            8 => 1,
                            9 => 2,
                            10 => 4,
                            11 => 3,
                            13 => 1,
                            14 => 2,
                            15 => 1,
                        ];

                        if ($weekday === 1) {
                            $pattern[10]++;
                        }

                        if ($weekday === 5) {
                            $pattern[14]++;
                        }

                        /*
                         * Small deterministic week-to-week variation
                         * prevents every historical week being identical.
                         */
                        $weekIndex =
                            intdiv(
                                $daysAgo,
                                7
                            );

                        if (($weekIndex % 3) === 0) {
                            $pattern[10]++;
                        }
                    } else {
                        $pattern = [
                            10 => 2,
                        ];
                    }

                    foreach (
                        $pattern as $hour => $count
                    ) {
                        for (
                            $index = 1;
                            $index <= $count;
                            $index++
                        ) {
                            $minute =
                                min(
                                    55,
                                    4
                                    +
                                    (
                                        $index
                                        *
                                        7
                                    )
                                );

                            $checkIn =
                                $date->setTime(
                                    $hour,
                                    $minute
                                );

                            $duration =
                                20
                                +
                                (
                                    (
                                        $visitorSequence
                                        +
                                        $hour
                                    )
                                    %
                                    31
                                );

                            $checkOut =
                                $checkIn->addMinutes(
                                    $duration
                                );

                            $visitorRows[] = [
                                'full_name' => sprintf(
                                    'Synthetic Demo Visitor %04d',
                                    $visitorSequence
                                ),

                                'contact_number' => null,

                                'email' => null,

                                'organization' => 'Synthetic Demonstration Data',

                                'visitor_type' => match (
                                    $visitorSequence
                                    %
                                    4
                                ) {
                                    0 => 'supplier',
                                    1 => 'guest',
                                    2 => 'business_partner',
                                    default => 'customer',
                                },

                                'purpose' => 'Synthetic visitor traffic forecasting demonstration',

                                'host_email' => null,

                                'host_name' => null,

                                'appointment_id' => null,

                                'id_reference' => null,

                                'badge_number' => null,

                                'is_walk_in' => true,

                                'status' => 'completed',

                                'check_in_at' => $checkIn,

                                'check_out_at' => $checkOut,

                                'duration_minutes' => $duration,

                                'ai_summary' => null,

                                'notes' => self::MARKER
                                    .' HISTORICAL WALK-IN',

                                'created_at' => $checkIn,

                                'updated_at' => $checkOut,
                            ];

                            $visitorSequence++;
                        }
                    }

                    /*
                     * Historical appointment signals.
                     *
                     * They are not treated as walk-ins by the target model;
                     * they provide contextual demand features.
                     */
                    if ($weekday <= 5) {
                        foreach (
                            [
                                '09:00',
                                '14:00',
                            ] as $startTime
                        ) {
                            $hour =
                                (int) substr(
                                    $startTime,
                                    0,
                                    2
                                );

                            $appointmentRows[] = [
                                'visitor_name' => sprintf(
                                    'Synthetic Scheduled Visitor %04d',
                                    $appointmentSequence
                                ),

                                'visitor_organization' => 'Synthetic Demonstration Data',

                                'visitor_email' => null,

                                'visitor_contact' => null,

                                'visitor_type' => 'guest',

                                'host_email' => null,

                                'host_name' => 'Synthetic Demo Host',

                                'date' => $date->toDateString(),

                                'start_time' => $startTime,

                                'end_time' => sprintf(
                                    '%02d:30',
                                    $hour
                                ),

                                'purpose' => 'Synthetic historical appointment signal',

                                'facility_id' => null,

                                'facility_name' => null,

                                'notes' => self::MARKER
                                    .' HISTORICAL APPOINTMENT',

                                'status' => 'confirmed',

                                'visitor_id' => null,

                                'created_at' => $date->setTime(
                                    max(
                                        0,
                                        $hour - 1
                                    ),
                                    0
                                ),

                                'updated_at' => $date->setTime(
                                    max(
                                        0,
                                        $hour - 1
                                    ),
                                    0
                                ),
                            ];

                            $appointmentSequence++;
                        }

                        if ($facility) {
                            $reservationRows[] = [
                                'facility_id' => $facility->id,

                                'facility_name' => $facility->name,

                                'requester_email' => 'synthetic-forecast-demo@example.invalid',

                                'requester_name' => 'Synthetic Forecast Demo',

                                'date' => $date->toDateString(),

                                'start_time' => '10:00',

                                'end_time' => '11:00',

                                'attendees' => 8
                                    +
                                    $weekday,

                                'purpose' => self::MARKER
                                    .' HISTORICAL FACILITY DEMAND',

                                'status' => 'approved',

                                'decision_by_email' => null,

                                'decision_at' => $date->setTime(
                                    8,
                                    0
                                ),

                                'decision_note' => 'Synthetic demonstration data',

                                'created_at' => $date->setTime(
                                    8,
                                    0
                                ),

                                'updated_at' => $date->setTime(
                                    8,
                                    0
                                ),
                            ];
                        }
                    }
                }

                /*
                 * Add seven days of known future appointment demand.
                 */
                for (
                    $offset = 1;
                    $offset <= 7;
                    $offset++
                ) {
                    $date =
                        $today->addDays(
                            $offset
                        );

                    $weekday =
                        (int) $date->dayOfWeekIso;

                    $futureTimes =
                        $weekday <= 5
                            ? [
                                '09:00',
                                '10:00',
                                '10:30',
                                '14:00',
                            ]
                            : [
                                '10:00',
                            ];

                    foreach (
                        $futureTimes as $startTime
                    ) {
                        [$hour, $minute] =
                            array_map(
                                'intval',
                                explode(
                                    ':',
                                    $startTime
                                )
                            );

                        $endAt =
                            $date
                                ->setTime(
                                    $hour,
                                    $minute
                                )
                                ->addMinutes(
                                    30
                                );

                        $appointmentRows[] = [
                            'visitor_name' => sprintf(
                                'Synthetic Future Visitor %04d',
                                $appointmentSequence
                            ),

                            'visitor_organization' => 'Synthetic Demonstration Data',

                            'visitor_email' => null,

                            'visitor_contact' => null,

                            'visitor_type' => 'guest',

                            'host_email' => null,

                            'host_name' => 'Synthetic Demo Host',

                            'date' => $date->toDateString(),

                            'start_time' => $startTime,

                            'end_time' => $endAt->format(
                                'H:i'
                            ),

                            'purpose' => 'Synthetic future forecast demand',

                            'facility_id' => null,

                            'facility_name' => null,

                            'notes' => self::MARKER
                                .' FUTURE APPOINTMENT',

                            'status' => 'scheduled',

                            'visitor_id' => null,

                            'created_at' => $today,

                            'updated_at' => $today,
                        ];

                        $appointmentSequence++;
                    }

                    if ($facility) {
                        $reservationCount =
                            $weekday <= 5
                                ? 2
                                : 1;

                        for (
                            $reservationIndex = 1;
                            $reservationIndex <= $reservationCount;
                            $reservationIndex++
                        ) {
                            $startHour =
                                9
                                +
                                $reservationIndex;

                            $reservationRows[] = [
                                'facility_id' => $facility->id,

                                'facility_name' => $facility->name,

                                'requester_email' => 'synthetic-forecast-demo@example.invalid',

                                'requester_name' => 'Synthetic Forecast Demo',

                                'date' => $date->toDateString(),

                                'start_time' => sprintf(
                                    '%02d:00',
                                    $startHour
                                ),

                                'end_time' => sprintf(
                                    '%02d:00',
                                    $startHour + 1
                                ),

                                'attendees' => 10
                                    +
                                    (
                                        $reservationIndex
                                        *
                                        5
                                    ),

                                'purpose' => self::MARKER
                                    .' FUTURE FACILITY DEMAND',

                                'status' => 'approved',

                                'decision_by_email' => null,

                                'decision_at' => $today,

                                'decision_note' => 'Synthetic demonstration data',

                                'created_at' => $today,

                                'updated_at' => $today,
                            ];
                        }
                    }
                }

                $this->insertChunks(
                    'visitors',
                    $visitorRows
                );

                $this->insertChunks(
                    'appointments',
                    $appointmentRows
                );

                if ($reservationRows !== []) {
                    $this->insertChunks(
                        'reservations',
                        $reservationRows
                    );
                }

                return array_merge(
                    $this->status(),
                    [
                        'reference_date' => $today->toDateString(),

                        'facility_signal_enabled' => $facility !== null,
                    ]
                );
            }
        );
    }

    public function purge(): array
    {
        $this->assertSafeEnvironment();

        DB::transaction(
            fn () => $this->purgeInternal()
        );

        return $this->status();
    }

    private function purgeInternal(): void
    {
        /*
         * Delete only explicitly marked synthetic records.
         * Real operational records are never matched by these predicates.
         */
        DB::table('visitors')
            ->where(
                'notes',
                'like',
                self::MARKER.'%'
            )
            ->delete();

        DB::table('appointments')
            ->where(
                'notes',
                'like',
                self::MARKER.'%'
            )
            ->delete();

        DB::table('reservations')
            ->where(
                'purpose',
                'like',
                self::MARKER.'%'
            )
            ->delete();
    }

    private function insertChunks(
        string $table,
        array $rows
    ): void {
        foreach (
            array_chunk(
                $rows,
                250
            ) as $chunk
        ) {
            DB::table(
                $table
            )->insert(
                $chunk
            );
        }
    }

    private function assertSafeEnvironment(): void
    {
        if (
            app()->environment(
                'production'
            )
        ) {
            throw new RuntimeException(
                'Synthetic visitor forecast demo data is disabled in production.'
            );
        }
    }
}
