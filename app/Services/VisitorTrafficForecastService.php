<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Reservation;
use App\Models\Visitor;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class VisitorTrafficForecastService
{
    /**
     * Chronologically backtest the academic synthetic demonstration model.
     *
     * The first 56 synthetic days train the KNN model. The final 14 days
     * form an untouched chronological holdout set.
     *
     * These metrics validate demonstration behavior only. They are not
     * evidence of real-world forecasting accuracy.
     */
    public function validateSyntheticDemoModel(
        CarbonInterface|string $date
    ): array {
        if (
            ! (bool) config(
                'visitor_forecast.demo_mode',
                false
            )
        ) {
            return [
                'available' => false,
                'dataset' => 'none',
                'method' => null,
                'notice' => 'Synthetic backtesting is available only in academic demonstration mode.',
            ];
        }

        $target =
            $this->immutableDate(
                $date
            )->startOfDay();

        $startHour =
            $this->startHour();

        $endHour =
            $this->endHour();

        $generated =
            $this->syntheticDemoSamples(
                $target,
                $startHour,
                $endHour
            );

        $observations =
            $generated[
                'samples'
            ];

        $totalDays =
            (int) $generated[
                'days'
            ];

        $validationDays = 14;

        $trainingDays =
            $totalDays
            -
            $validationDays;

        $slotsPerDay =
            (
                $endHour
                -
                $startHour
            )
            +
            1;

        $trainingObservationCount =
            $trainingDays
            *
            $slotsPerDay;

        $validationObservationCount =
            $validationDays
            *
            $slotsPerDay;

        if (
            count(
                $observations
            )
            <
            (
                $trainingObservationCount
                +
                $validationObservationCount
            )
        ) {
            return [
                'available' => false,
                'dataset' => 'synthetic_academic_demo',
                'method' => 'chronological_holdout',
                'notice' => 'Synthetic backtest observations are incomplete.',
            ];
        }

        $trainingSamples =
            array_slice(
                $observations,
                0,
                $trainingObservationCount
            );

        $validationSamples =
            array_slice(
                $observations,
                $trainingObservationCount,
                $validationObservationCount
            );

        $absoluteErrorTotal = 0.0;
        $actualTotal = 0.0;

        foreach (
            $validationSamples as $sample
        ) {
            $actual =
                (float) $sample[
                    'walk_ins'
                ];

            $prediction =
                max(
                    0.0,
                    $this->knnPredict(
                        $sample[
                            'features'
                        ],
                        $trainingSamples
                    )
                );

            $absoluteErrorTotal +=
                abs(
                    $prediction
                    -
                    $actual
                );

            $actualTotal +=
                $actual;
        }

        $mae =
            $validationObservationCount > 0
                ? (
                    $absoluteErrorTotal
                    /
                    $validationObservationCount
                )
                : null;

        $wape =
            $actualTotal > 0
                ? (
                    $absoluteErrorTotal
                    /
                    $actualTotal
                )
                *
                100
                : null;

        return [
            'available' => true,

            'dataset' => 'synthetic_academic_demo',

            'method' => 'chronological_holdout',

            'training_days' => $trainingDays,

            'validation_days' => $validationDays,

            'training_observations' => $trainingObservationCount,

            'validation_observations' => $validationObservationCount,

            'mae_visitors_per_hour' => $mae === null
                    ? null
                    : round(
                        $mae,
                        2
                    ),

            'wape_percent' => $wape === null
                    ? null
                    : round(
                        $wape,
                        1
                    ),

            'notice' => 'Backtest metrics are calculated only from synthetic academic demonstration observations and do not establish real-world production accuracy.',
        ];
    }

    /**
     * Forecast used by the Visitor Traffic Intelligence UI.
     *
     * Production behavior remains forecastDay(). Academic demonstration
     * mode uses deterministic synthetic training observations in memory.
     */
    public function forecastForDisplayDay(
        CarbonInterface|string $date
    ): array {
        if (
            (bool) config(
                'visitor_forecast.demo_mode',
                false
            )
        ) {
            return $this->syntheticDemoForecast(
                $date
            );
        }

        return $this->forecastDay(
            $date
        );
    }

    public function forecastDay(
        CarbonInterface|string $date
    ): array {
        $target = $this->immutableDate($date)
            ->startOfDay();

        $historyDays = max(
            1,
            (int) config(
                'visitor_forecast.history_days',
                90
            )
        );

        $startHour = $this->startHour();
        $endHour = $this->endHour();

        $historyStart = $target
            ->subDays($historyDays)
            ->startOfDay();

        $historyEnd = $target
            ->subDay()
            ->endOfDay();

        $historyVisitors = Visitor::query()
            ->whereNotNull('check_in_at')
            ->whereBetween(
                'check_in_at',
                [
                    $historyStart,
                    $historyEnd,
                ]
            )
            ->get([
                'check_in_at',
                'is_walk_in',
            ]);

        $historyVisitCount = $historyVisitors->count();

        $historyDistinctDays = $historyVisitors
            ->map(
                fn (Visitor $visitor): string => $visitor->check_in_at->toDateString()
            )
            ->unique()
            ->count();

        $minimumHistoryVisits = max(
            1,
            (int) config(
                'visitor_forecast.minimum_history_visits',
                100
            )
        );

        $minimumHistoryDays = max(
            1,
            (int) config(
                'visitor_forecast.minimum_history_days',
                21
            )
        );

        $modelReady =
            $historyVisitCount >= $minimumHistoryVisits
            &&
            $historyDistinctDays >= $minimumHistoryDays;

        $targetSignals = $this->signalMaps(
            $target,
            $target
        );

        $samples = [];

        if ($modelReady) {
            $samples = $this->historicalSamples(
                $historyVisitors,
                $historyStart,
                $historyEnd,
                $startHour,
                $endHour
            );
        }

        $hourly = [];

        for ($hour = $startHour; $hour <= $endHour; $hour++) {
            $key = $this->bucketKey(
                $target,
                $hour
            );

            $scheduledAppointments =
                $targetSignals['appointments'][$key]
                ?? 0;

            $approvedReservations =
                $targetSignals['reservations'][$key]
                ?? 0;

            $reservationAttendees =
                $targetSignals['reservation_attendees'][$key]
                ?? 0;

            $predictedWalkIns = 0.0;

            if ($modelReady && $samples !== []) {
                $features = $this->featureVector(
                    $target,
                    $hour,
                    $scheduledAppointments,
                    $approvedReservations,
                    $reservationAttendees
                );

                $predictedWalkIns = $this->knnPredict(
                    $features,
                    $samples
                );
            }

            $predictedWalkIns = max(
                0.0,
                round(
                    $predictedWalkIns,
                    1
                )
            );

            /*
             * Scheduled appointment demand is deterministic.
             * Only walk-in volume is predicted by the local model.
             */
            $expectedVisitors = (int) ceil(
                $scheduledAppointments
                +
                $predictedWalkIns
            );

            $hourly[] = [
                'hour' => sprintf(
                    '%02d:00',
                    $hour
                ),

                'scheduled_appointments' => $scheduledAppointments,

                'predicted_walk_ins' => $predictedWalkIns,

                'expected_visitors' => $expectedVisitors,

                'approved_reservations' => $approvedReservations,

                'reservation_attendees' => $reservationAttendees,

                'traffic_level' => $this->trafficLevel(
                    $expectedVisitors
                ),

                'recommended_front_desk_staff' => $this->recommendedStaff(
                    $expectedVisitors
                ),

                'meeting_room_demand' => $this->meetingRoomDemand(
                    $approvedReservations,
                    $reservationAttendees
                ),
            ];
        }

        $peak = collect($hourly)
            ->sortByDesc('expected_visitors')
            ->first();

        $expectedTotal = (int) collect($hourly)
            ->sum('expected_visitors');

        $scheduledTotal = (int) collect($hourly)
            ->sum('scheduled_appointments');

        $predictedWalkInTotal = round(
            (float) collect($hourly)
                ->sum('predicted_walk_ins'),
            1
        );

        return [
            'date' => $target->toDateString(),

            'model' => 'local_knn_regression_v1',

            'status' => $modelReady
                    ? 'ready'
                    : 'cold_start',

            'forecast_source' => $modelReady
                    ? 'ml_walk_ins_plus_scheduled_demand'
                    : 'scheduled_demand_only',

            'history' => [
                'lookback_days' => $historyDays,

                'timestamped_visits' => $historyVisitCount,

                'distinct_visit_days' => $historyDistinctDays,

                'minimum_visits_required' => $minimumHistoryVisits,

                'minimum_days_required' => $minimumHistoryDays,
            ],

            'confidence' => $this->confidence(
                $modelReady,
                $historyVisitCount,
                $historyDistinctDays
            ),

            'expected_visitors' => $expectedTotal,

            'scheduled_visitors' => $scheduledTotal,

            'predicted_walk_ins' => $predictedWalkInTotal,

            'peak_hour' => $peak['hour']
                ?? null,

            'peak_expected_visitors' => $peak['expected_visitors']
                ?? 0,

            'peak_traffic_level' => $peak['traffic_level']
                ?? 'low',

            'recommended_peak_staff' => $peak['recommended_front_desk_staff']
                ?? 1,

            'hourly' => $hourly,

            'notice' => $modelReady
                    ? 'Forecast combines known appointment demand with locally learned historical walk-in patterns.'
                    : 'Insufficient historical check-in data for machine-learning forecasting. Showing scheduled demand only.',
        ];
    }

    /**
     * Academic demonstration forecast.
     *
     * Synthetic observations exist only in PHP memory for this request.
     * Known future appointments and reservations still come from the
     * application's real PostgreSQL records.
     */
    private function syntheticDemoForecast(
        CarbonInterface|string $date
    ): array {
        $target =
            $this->immutableDate(
                $date
            )->startOfDay();

        $startHour =
            $this->startHour();

        $endHour =
            $this->endHour();

        $historyDays =
            max(
                1,
                (int) config(
                    'visitor_forecast.history_days',
                    90
                )
            );

        $historyStart =
            $target
                ->subDays(
                    $historyDays
                )
                ->startOfDay();

        $historyEnd =
            $target
                ->subDay()
                ->endOfDay();

        /*
         * Real history is counted separately for transparent reporting.
         */
        $realHistory =
            Visitor::query()
                ->whereNotNull(
                    'check_in_at'
                )
                ->whereBetween(
                    'check_in_at',
                    [
                        $historyStart,
                        $historyEnd,
                    ]
                )
                ->get([
                    'check_in_at',
                ]);

        $realHistoryVisitCount =
            $realHistory->count();

        $realHistoryDistinctDays =
            $realHistory
                ->map(
                    fn (Visitor $visitor): string => $visitor
                        ->check_in_at
                        ->toDateString()
                )
                ->unique()
                ->count();

        $training =
            $this->syntheticDemoSamples(
                $target,
                $startHour,
                $endHour
            );

        $targetSignals =
            $this->signalMaps(
                $target,
                $target
            );

        $hourly = [];

        for (
            $hour = $startHour;
            $hour <= $endHour;
            $hour++
        ) {
            $key =
                $this->bucketKey(
                    $target,
                    $hour
                );

            $scheduledAppointments =
                $targetSignals[
                    'appointments'
                ][$key]
                    ?? 0;

            $approvedReservations =
                $targetSignals[
                    'reservations'
                ][$key]
                    ?? 0;

            $reservationAttendees =
                $targetSignals[
                    'reservation_attendees'
                ][$key]
                    ?? 0;

            $features =
                $this->featureVector(
                    $target,
                    $hour,
                    $scheduledAppointments,
                    $approvedReservations,
                    $reservationAttendees
                );

            $predictedWalkIns =
                max(
                    0.0,
                    round(
                        $this->knnPredict(
                            $features,
                            $training[
                                'samples'
                            ]
                        ),
                        1
                    )
                );

            $expectedVisitors =
                (int) ceil(
                    $scheduledAppointments
                    +
                    $predictedWalkIns
                );

            $hourly[] = [
                'hour' => sprintf(
                    '%02d:00',
                    $hour
                ),

                'scheduled_appointments' => $scheduledAppointments,

                'predicted_walk_ins' => $predictedWalkIns,

                'expected_visitors' => $expectedVisitors,

                'approved_reservations' => $approvedReservations,

                'reservation_attendees' => $reservationAttendees,

                'traffic_level' => $this->trafficLevel(
                    $expectedVisitors
                ),

                'recommended_front_desk_staff' => $this->recommendedStaff(
                    $expectedVisitors
                ),

                'meeting_room_demand' => $this->meetingRoomDemand(
                    $approvedReservations,
                    $reservationAttendees
                ),
            ];
        }

        $peak =
            collect(
                $hourly
            )
                ->sortByDesc(
                    'expected_visitors'
                )
                ->first();

        return [
            'date' => $target->toDateString(),

            'model' => 'local_knn_regression_v1',

            'demo_mode' => true,

            'status' => 'ready',

            'forecast_source' => 'synthetic_demo_model_plus_scheduled_demand',

            'history' => [
                /*
                 * Training history describes the in-memory academic
                 * demonstration observations.
                 */
                'lookback_days' => $training[
                        'days'
                    ],

                'timestamped_visits' => $training[
                        'visits'
                    ],

                'distinct_visit_days' => $training[
                        'days'
                    ],

                /*
                 * Real operational history is reported independently.
                 */
                'real_timestamped_visits' => $realHistoryVisitCount,

                'real_distinct_visit_days' => $realHistoryDistinctDays,

                'minimum_visits_required' => max(
                    1,
                    (int) config(
                        'visitor_forecast.minimum_history_visits',
                        100
                    )
                ),

                'minimum_days_required' => max(
                    1,
                    (int) config(
                        'visitor_forecast.minimum_history_days',
                        21
                    )
                ),
            ],

            /*
             * Do not claim measured accuracy from synthetic data.
             */
            'confidence' => 'demonstration',

            'expected_visitors' => (int) collect(
                $hourly
            )->sum(
                'expected_visitors'
            ),

            'scheduled_visitors' => (int) collect(
                $hourly
            )->sum(
                'scheduled_appointments'
            ),

            'predicted_walk_ins' => round(
                (float) collect(
                    $hourly
                )->sum(
                    'predicted_walk_ins'
                ),
                1
            ),

            'peak_hour' => $peak[
                    'hour'
                ]
                    ?? null,

            'peak_expected_visitors' => $peak[
                    'expected_visitors'
                ]
                    ?? 0,

            'peak_traffic_level' => $peak[
                    'traffic_level'
                ]
                    ?? 'low',

            'recommended_peak_staff' => $peak[
                    'recommended_front_desk_staff'
                ]
                    ?? 1,

            'hourly' => $hourly,

            'notice' => 'Academic demonstration mode uses synthetic KNN training observations generated in memory. No synthetic visitor, appointment, or reservation records are stored in operational PostgreSQL tables.',
        ];
    }

    /**
     * Build deterministic academic KNN training observations.
     *
     * These arrays are never persisted.
     */
    private function syntheticDemoSamples(
        CarbonImmutable $target,
        int $startHour,
        int $endHour
    ): array {
        $samples = [];
        $totalVisits = 0;
        $days = 70;

        for (
            $daysAgo = $days;
            $daysAgo >= 1;
            $daysAgo--
        ) {
            $date =
                $target->subDays(
                    $daysAgo
                );

            $weekday =
                (int) $date->dayOfWeekIso;

            $weekIndex =
                intdiv(
                    $daysAgo,
                    7
                );

            for (
                $hour = $startHour;
                $hour <= $endHour;
                $hour++
            ) {
                $walkIns = 0;

                if ($weekday <= 5) {
                    $walkIns =
                        match ($hour) {
                            8 => 1,
                            9 => 2,
                            10 => 4,
                            11 => 3,
                            13 => 1,
                            14 => 2,
                            15 => 1,
                            default => 0,
                        };

                    if (
                        $weekday === 1
                        &&
                        $hour === 10
                    ) {
                        $walkIns++;
                    }

                    if (
                        $weekday === 5
                        &&
                        $hour === 14
                    ) {
                        $walkIns++;
                    }

                    if (
                        ($weekIndex % 3) === 0
                        &&
                        $hour === 10
                    ) {
                        $walkIns++;
                    }
                } elseif ($hour === 10) {
                    $walkIns = 2;
                }

                $samples[] = [
                    'features' => $this->featureVector(
                        $date,
                        $hour,
                        0,
                        0,
                        0
                    ),

                    'walk_ins' => (float) $walkIns,
                ];

                $totalVisits +=
                    $walkIns;
            }
        }

        return [
            'samples' => $samples,

            'visits' => $totalVisits,

            'days' => $days,
        ];
    }

    private function historicalSamples(
        Collection $visitors,
        CarbonImmutable $historyStart,
        CarbonImmutable $historyEnd,
        int $startHour,
        int $endHour
    ): array {
        if ($visitors->isEmpty()) {
            return [];
        }

        $actualWalkIns = [];

        foreach ($visitors as $visitor) {
            $checkedInAt = CarbonImmutable::instance(
                $visitor->check_in_at
            );

            $hour = (int) $checkedInAt->format('G');

            if (
                $hour < $startHour
                ||
                $hour > $endHour
            ) {
                continue;
            }

            $key = $this->bucketKey(
                $checkedInAt,
                $hour
            );

            $actualWalkIns[$key] =
                $actualWalkIns[$key]
                ?? 0;

            if ($visitor->is_walk_in) {
                $actualWalkIns[$key]++;
            }
        }

        $firstVisit = $visitors
            ->sortBy('check_in_at')
            ->first();

        $lastVisit = $visitors
            ->sortByDesc('check_in_at')
            ->first();

        if (
            ! $firstVisit
            ||
            ! $lastVisit
        ) {
            return [];
        }

        $firstDate = CarbonImmutable::instance(
            $firstVisit->check_in_at
        )->startOfDay();

        $lastDate = CarbonImmutable::instance(
            $lastVisit->check_in_at
        )->startOfDay();

        if ($firstDate->lt($historyStart)) {
            $firstDate = $historyStart->startOfDay();
        }

        if ($lastDate->gt($historyEnd)) {
            $lastDate = $historyEnd->startOfDay();
        }

        $signals = $this->signalMaps(
            $firstDate,
            $lastDate
        );

        $samples = [];

        $cursor = $firstDate;

        while ($cursor->lte($lastDate)) {
            for (
                $hour = $startHour;
                $hour <= $endHour;
                $hour++
            ) {
                $key = $this->bucketKey(
                    $cursor,
                    $hour
                );

                $appointments =
                    $signals['appointments'][$key]
                    ?? 0;

                $reservations =
                    $signals['reservations'][$key]
                    ?? 0;

                $attendees =
                    $signals['reservation_attendees'][$key]
                    ?? 0;

                $samples[] = [
                    'features' => $this->featureVector(
                        $cursor,
                        $hour,
                        $appointments,
                        $reservations,
                        $attendees
                    ),

                    'walk_ins' => (float) (
                        $actualWalkIns[$key]
                        ?? 0
                    ),
                ];
            }

            $cursor = $cursor->addDay();
        }

        return $samples;
    }

    private function signalMaps(
        CarbonImmutable $from,
        CarbonImmutable $to
    ): array {
        $appointmentsMap = [];

        $appointments = Appointment::query()
            ->whereBetween(
                'date',
                [
                    $from->toDateString(),
                    $to->toDateString(),
                ]
            )
            ->whereIn(
                'status',
                [
                    'scheduled',
                    'confirmed',
                ]
            )
            ->get([
                'date',
                'start_time',
            ]);

        foreach ($appointments as $appointment) {
            $hour = $this->hourFromTime(
                $appointment->start_time
            );

            if ($hour === null) {
                continue;
            }

            $key = $this->bucketKey(
                CarbonImmutable::parse(
                    $appointment->date->toDateString()
                ),
                $hour
            );

            $appointmentsMap[$key] =
                ($appointmentsMap[$key] ?? 0)
                +
                1;
        }

        $reservationMap = [];
        $reservationAttendeesMap = [];

        $reservations = Reservation::query()
            ->whereBetween(
                'date',
                [
                    $from->toDateString(),
                    $to->toDateString(),
                ]
            )
            ->where(
                'status',
                'approved'
            )
            ->get([
                'date',
                'start_time',
                'attendees',
            ]);

        foreach ($reservations as $reservation) {
            $hour = $this->hourFromTime(
                $reservation->start_time
            );

            if ($hour === null) {
                continue;
            }

            $key = $this->bucketKey(
                CarbonImmutable::parse(
                    $reservation->date->toDateString()
                ),
                $hour
            );

            $reservationMap[$key] =
                ($reservationMap[$key] ?? 0)
                +
                1;

            $reservationAttendeesMap[$key] =
                ($reservationAttendeesMap[$key] ?? 0)
                +
                max(
                    0,
                    (int) $reservation->attendees
                );
        }

        return [
            'appointments' => $appointmentsMap,

            'reservations' => $reservationMap,

            'reservation_attendees' => $reservationAttendeesMap,
        ];
    }

    private function knnPredict(
        array $features,
        array $samples
    ): float {
        if ($samples === []) {
            return 0.0;
        }

        $distances = [];

        foreach ($samples as $sample) {
            $distance = $this->distance(
                $features,
                $sample['features']
            );

            $distances[] = [
                'distance' => $distance,

                'walk_ins' => (float) $sample['walk_ins'],
            ];
        }

        usort(
            $distances,
            fn (
                array $left,
                array $right
            ): int => $left['distance']
                <=>
                $right['distance']
        );

        $neighborCount = min(
            max(
                1,
                (int) config(
                    'visitor_forecast.neighbors',
                    12
                )
            ),
            count($distances)
        );

        $neighbors = array_slice(
            $distances,
            0,
            $neighborCount
        );

        $weightedTotal = 0.0;
        $weightTotal = 0.0;

        foreach ($neighbors as $neighbor) {
            $weight =
                1.0
                /
                (
                    $neighbor['distance']
                    +
                    0.05
                );

            $weightedTotal +=
                $weight
                *
                $neighbor['walk_ins'];

            $weightTotal +=
                $weight;
        }

        if ($weightTotal <= 0) {
            return 0.0;
        }

        return
            $weightedTotal
            /
            $weightTotal;
    }

    private function featureVector(
        CarbonInterface $date,
        int $hour,
        int $appointments,
        int $reservations,
        int $reservationAttendees
    ): array {
        $weekdayAngle =
            2
            *
            M_PI
            *
            (
                (int) $date->dayOfWeek
                /
                7
            );

        $hourAngle =
            2
            *
            M_PI
            *
            (
                $hour
                /
                24
            );

        return [
            sin($weekdayAngle),
            cos($weekdayAngle),
            sin($hourAngle),
            cos($hourAngle),

            min(
                $appointments,
                20
            ) / 20,

            min(
                $reservations,
                10
            ) / 10,

            min(
                $reservationAttendees,
                100
            ) / 100,
        ];
    }

    private function distance(
        array $left,
        array $right
    ): float {
        $sum = 0.0;

        foreach ($left as $index => $value) {
            $difference =
                (float) $value
                -
                (float) (
                    $right[$index]
                    ?? 0.0
                );

            $sum +=
                $difference
                *
                $difference;
        }

        return sqrt($sum);
    }

    private function confidence(
        bool $modelReady,
        int $historyVisits,
        int $historyDays
    ): string {
        if (! $modelReady) {
            return 'unavailable';
        }

        if (
            $historyVisits >= 500
            &&
            $historyDays >= 60
        ) {
            return 'high';
        }

        if (
            $historyVisits >= 200
            &&
            $historyDays >= 30
        ) {
            return 'medium';
        }

        return 'low';
    }

    private function trafficLevel(
        int $expectedVisitors
    ): string {
        $normalMax = (int) config(
            'visitor_forecast.staffing.normal_max',
            5
        );

        $moderateMax = (int) config(
            'visitor_forecast.staffing.moderate_max',
            10
        );

        if ($expectedVisitors <= $normalMax) {
            return 'low';
        }

        if ($expectedVisitors <= $moderateMax) {
            return 'moderate';
        }

        return 'high';
    }

    private function recommendedStaff(
        int $expectedVisitors
    ): int {
        $normalMax = (int) config(
            'visitor_forecast.staffing.normal_max',
            5
        );

        $moderateMax = (int) config(
            'visitor_forecast.staffing.moderate_max',
            10
        );

        $highMax = (int) config(
            'visitor_forecast.staffing.high_max',
            15
        );

        return match (true) {
            $expectedVisitors <= $normalMax => 1,
            $expectedVisitors <= $moderateMax => 2,
            $expectedVisitors <= $highMax => 3,
            default => 4,
        };
    }

    private function meetingRoomDemand(
        int $reservations,
        int $attendees
    ): string {
        return match (true) {
            $reservations <= 0 => 'none',

            $reservations >= 5
                ||
                $attendees >= 50 => 'high',

            $reservations >= 3
                ||
                $attendees >= 25 => 'moderate',

            default => 'low',
        };
    }

    private function hourFromTime(
        mixed $time
    ): ?int {
        $value = trim(
            (string) $time
        );

        if (
            ! preg_match(
                '/^(\d{1,2}):/',
                $value,
                $matches
            )
        ) {
            return null;
        }

        $hour = (int) $matches[1];

        if (
            $hour < 0
            ||
            $hour > 23
        ) {
            return null;
        }

        return $hour;
    }

    private function bucketKey(
        CarbonInterface $date,
        int $hour
    ): string {
        return
            $date->toDateString()
            .'|'
            .sprintf(
                '%02d',
                $hour
            );
    }

    private function startHour(): int
    {
        return min(
            23,
            max(
                0,
                (int) config(
                    'visitor_forecast.operating_start_hour',
                    8
                )
            )
        );
    }

    private function endHour(): int
    {
        return max(
            $this->startHour(),
            min(
                23,
                (int) config(
                    'visitor_forecast.operating_end_hour',
                    17
                )
            )
        );
    }

    private function immutableDate(
        CarbonInterface|string $date
    ): CarbonImmutable {
        if ($date instanceof CarbonInterface) {
            return CarbonImmutable::parse(
                $date->toDateTimeString(),
                $date->getTimezone()
            );
        }

        return CarbonImmutable::parse($date);
    }
}
