<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Support\Carbon;

class DashboardAnalyticsService
{
    public function build(
        User $user,
        Carbon $now
    ): array {
        $today =
            $now
                ->copy()
                ->toDateString();

        $start30 =
            $now
                ->copy()
                ->subDays(29)
                ->toDateString();

        $start30Timestamp =
            $now
                ->copy()
                ->subDays(29)
                ->startOfDay();

        $endTimestamp =
            $now
                ->copy()
                ->endOfDay();

        $canViewAppointments =
            $user->can(
                'viewAppointments'
            );

        $canViewVisitors =
            $user->can(
                'viewVisitors'
            );

        $reservationRows =
            Reservation::query()
                ->whereBetween(
                    'date',
                    [
                        $start30,
                        $today,
                    ]
                )
                ->selectRaw(
                    'date, status, COUNT(*) AS total'
                )
                ->groupBy(
                    'date',
                    'status'
                )
                ->get();

        $reservationDaily =
            $reservationRows
                ->groupBy(
                    fn ($row) => Carbon::parse(
                        $row->date
                    )->toDateString()
                )
                ->map(
                    fn ($rows) => (int) $rows->sum(
                        'total'
                    )
                );

        $appointmentDaily =
            collect();

        if ($canViewAppointments) {
            $appointmentDaily =
                Appointment::query()
                    ->whereBetween(
                        'date',
                        [
                            $start30,
                            $today,
                        ]
                    )
                    ->selectRaw(
                        'date, COUNT(*) AS total'
                    )
                    ->groupBy(
                        'date'
                    )
                    ->get()
                    ->mapWithKeys(
                        fn ($row) => [
                            Carbon::parse(
                                $row->date
                            )->toDateString() => (int) $row->total,
                        ]
                    );
        }

        $visitorDaily =
            collect();

        if ($canViewVisitors) {
            $visitorDaily =
                Visitor::query()
                    ->whereBetween(
                        'created_at',
                        [
                            $start30Timestamp,
                            $endTimestamp,
                        ]
                    )
                    ->selectRaw(
                        'DATE(created_at) AS activity_date, COUNT(*) AS total'
                    )
                    ->groupByRaw(
                        'DATE(created_at)'
                    )
                    ->get()
                    ->mapWithKeys(
                        fn ($row) => [
                            Carbon::parse(
                                $row->activity_date
                            )->toDateString() => (int) $row->total,
                        ]
                    );
        }

        $facilityRows =
            Reservation::query()
                ->with(
                    'facility:id,name'
                )
                ->where(
                    'status',
                    'approved'
                )
                ->whereBetween(
                    'date',
                    [
                        $start30,
                        $today,
                    ]
                )
                ->whereNotNull(
                    'facility_id'
                )
                ->selectRaw(
                    'date, facility_id, COUNT(*) AS bookings'
                )
                ->groupBy(
                    'date',
                    'facility_id'
                )
                ->get();

        $statusKeys = [
            'pending',
            'approved',
            'rejected',
            'cancelled',
            'completed',
        ];

        $buildRange =
            function (
                int $days
            ) use (
                $now,
                $reservationRows,
                $reservationDaily,
                $appointmentDaily,
                $visitorDaily,
                $facilityRows,
                $statusKeys,
                $canViewAppointments,
                $canViewVisitors
            ): array {
                $startDate =
                    $now
                        ->copy()
                        ->subDays(
                            $days - 1
                        )
                        ->toDateString();

                $activity =
                    collect(
                        range(
                            $days - 1,
                            0
                        )
                    )
                        ->map(
                            function (
                                int $daysAgo
                            ) use (
                                $now,
                                $reservationDaily,
                                $appointmentDaily,
                                $visitorDaily,
                                $canViewAppointments,
                                $canViewVisitors
                            ): array {
                                $date =
                                    $now
                                        ->copy()
                                        ->subDays(
                                            $daysAgo
                                        );

                                $dateKey =
                                    $date->toDateString();

                                $point = [
                                    'date' => $dateKey,

                                    'label' => $date->format(
                                        'M j'
                                    ),

                                    'reservations' => (int) (
                                        $reservationDaily[
                                            $dateKey
                                        ]
                                        ?? 0
                                    ),
                                ];

                                if ($canViewAppointments) {
                                    $point['appointments'] =
                                        (int) (
                                            $appointmentDaily[
                                                $dateKey
                                            ]
                                            ?? 0
                                        );
                                }

                                if ($canViewVisitors) {
                                    $point['visitors'] =
                                        (int) (
                                            $visitorDaily[
                                                $dateKey
                                            ]
                                            ?? 0
                                        );
                                }

                                return $point;
                            }
                        )
                        ->values();

                $rangeReservations =
                    $reservationRows
                        ->filter(
                            fn ($row) => Carbon::parse(
                                $row->date
                            )->toDateString()
                                >=
                                $startDate
                        );

                $reservationStatuses =
                    collect(
                        $statusKeys
                    )
                        ->map(
                            function (
                                string $status
                            ) use (
                                $rangeReservations
                            ): array {
                                return [
                                    'key' => $status,

                                    'label' => str($status)
                                        ->replace(
                                            '_',
                                            ' '
                                        )
                                        ->headline()
                                        ->toString(),

                                    'count' => (int) (
                                        $rangeReservations
                                            ->where(
                                                'status',
                                                $status
                                            )
                                            ->sum(
                                                'total'
                                            )
                                    ),
                                ];
                            }
                        )
                        ->values();

                $rangeFacilities =
                    $facilityRows
                        ->filter(
                            fn ($row) => Carbon::parse(
                                $row->date
                            )->toDateString()
                                >=
                                $startDate
                        );

                $facilityUtilization =
                    $rangeFacilities
                        ->groupBy(
                            'facility_id'
                        )
                        ->map(
                            function (
                                $rows,
                                $facilityId
                            ): array {
                                $first =
                                    $rows->first();

                                return [
                                    'facility_id' => (int) $facilityId,

                                    'name' => $first?->facility?->name
                                        ?? 'Unknown Facility',

                                    'count' => (int) $rows->sum(
                                        'bookings'
                                    ),
                                ];
                            }
                        )
                        ->sortByDesc(
                            'count'
                        )
                        ->take(6)
                        ->values();

                return [
                    'days' => $days,

                    'start_date' => $startDate,

                    'end_date' => $now
                        ->copy()
                        ->toDateString(),

                    'activity' => $activity,

                    'reservation_statuses' => $reservationStatuses,

                    'facility_utilization' => $facilityUtilization,
                ];
            };

        return [
            'default_range' => 7,

            'can_view_appointments' => $canViewAppointments,

            'can_view_visitors' => $canViewVisitors,

            'ranges' => [
                7 => $buildRange(7),
                30 => $buildRange(30),
            ],
        ];
    }
}
