<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\ArchiveDocument;
use App\Models\Contract;
use App\Models\Facility;
use App\Models\LegalRecord;
use App\Models\RecordRetention;
use App\Models\Reservation;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportDataService
{
    /**
     * Build one authoritative report dataset shared by the
     * dashboard, PDF, spreadsheet, CSV, and print outputs.
     *
     * @return array<string, mixed>
     */
    public function build(Request $request): array
    {
        $request->validate([
            'from' => [
                'nullable',
                'date',
            ],
            'to' => [
                'nullable',
                'date',
                'after_or_equal:from',
            ],
            'facility_sort' => [
                'nullable',
                Rule::in([
                    'bookings_desc',
                    'bookings_asc',
                    'name_asc',
                    'name_desc',
                ]),
            ],
        ]);

        $from =
            $request->filled('from')
                ? $request->date('from')->startOfDay()
                : now()->subDays(30)->startOfDay();

        $to =
            $request->filled('to')
                ? $request->date('to')->endOfDay()
                : now()->endOfDay();

        $facilitySort =
            $request->string(
                'facility_sort',
                'bookings_desc'
            )->toString();

        $fromDate =
            $from->toDateString();

        $toDate =
            $to->toDateString();

        /*
         * Date-scoped operational datasets.
         */
        $reservationBase =
            Reservation::query()
                ->whereBetween(
                    'date',
                    [
                        $fromDate,
                        $toDate,
                    ]
                );

        $visitorBase =
            Visitor::query()
                ->whereBetween(
                    'created_at',
                    [
                        $from,
                        $to,
                    ]
                );

        $appointmentBase =
            Appointment::query()
                ->whereBetween(
                    'date',
                    [
                        $fromDate,
                        $toDate,
                    ]
                );

        $documentBase =
            ArchiveDocument::query()
                ->whereBetween(
                    'created_at',
                    [
                        $from,
                        $to,
                    ]
                );

        /*
         * Operational breakdowns.
         */
        $reservationsByStatus =
            (clone $reservationBase)
                ->selectRaw(
                    'status, count(*) as total'
                )
                ->groupBy('status')
                ->orderBy('status')
                ->pluck(
                    'total',
                    'status'
                );

        $visitorsByType =
            (clone $visitorBase)
                ->selectRaw(
                    'visitor_type, count(*) as total'
                )
                ->groupBy('visitor_type')
                ->orderBy('visitor_type')
                ->pluck(
                    'total',
                    'visitor_type'
                );

        $appointmentsByStatus =
            (clone $appointmentBase)
                ->selectRaw(
                    'status, count(*) as total'
                )
                ->groupBy('status')
                ->orderBy('status')
                ->pluck(
                    'total',
                    'status'
                );

        $visitorEntryType =
            collect([
                'walk_in' => (clone $visitorBase)
                    ->where(
                        'is_walk_in',
                        true
                    )
                    ->count(),

                'scheduled' => (clone $visitorBase)
                    ->where(
                        'is_walk_in',
                        false
                    )
                    ->count(),
            ]);

        /*
         * Document analytics.
         */
        $documentsByStatus =
            (clone $documentBase)
                ->selectRaw(
                    'status, count(*) as total'
                )
                ->groupBy('status')
                ->orderBy('status')
                ->pluck(
                    'total',
                    'status'
                );

        $documentsByCategory =
            (clone $documentBase)
                ->selectRaw(
                    'category, count(*) as total'
                )
                ->groupBy('category')
                ->orderBy('category')
                ->pluck(
                    'total',
                    'category'
                );

        /*
         * Current governance / compliance portfolio.
         */
        $retentionByCompliance =
            RecordRetention::query()
                ->selectRaw(
                    'compliance_status, count(*) as total'
                )
                ->groupBy(
                    'compliance_status'
                )
                ->orderBy(
                    'compliance_status'
                )
                ->pluck(
                    'total',
                    'compliance_status'
                );

        $legalByReviewStatus =
            LegalRecord::query()
                ->selectRaw(
                    'review_status, count(*) as total'
                )
                ->groupBy('review_status')
                ->orderBy('review_status')
                ->pluck(
                    'total',
                    'review_status'
                );

        $contractsByStatus =
            Contract::query()
                ->selectRaw(
                    'status, count(*) as total'
                )
                ->groupBy('status')
                ->orderBy('status')
                ->pluck(
                    'total',
                    'status'
                );

        $contractsByLegalReview =
            Contract::query()
                ->selectRaw(
                    'legal_review_status, count(*) as total'
                )
                ->groupBy(
                    'legal_review_status'
                )
                ->orderBy(
                    'legal_review_status'
                )
                ->pluck(
                    'total',
                    'legal_review_status'
                );

        /*
         * Facility utilization.
         */
        $facilityUtilization =
            Reservation::with('facility')
                ->where(
                    'status',
                    'approved'
                )
                ->whereBetween(
                    'date',
                    [
                        $fromDate,
                        $toDate,
                    ]
                )
                ->selectRaw(
                    'facility_id, count(*) as bookings'
                )
                ->groupBy('facility_id')
                ->get();

        $facilityUtilization =
            match ($facilitySort) {
                'bookings_asc' => $facilityUtilization
                    ->sortBy('bookings')
                    ->values(),

                'name_asc' => $facilityUtilization
                    ->sortBy(
                        fn ($row) => strtolower(
                            (string) (
                                $row->facility?->name
                                ?? ''
                            )
                        )
                    )
                    ->values(),

                'name_desc' => $facilityUtilization
                    ->sortByDesc(
                        fn ($row) => strtolower(
                            (string) (
                                $row->facility?->name
                                ?? ''
                            )
                        )
                    )
                    ->values(),

                default => $facilityUtilization
                    ->sortByDesc('bookings')
                    ->values(),
            };

        /*
         * Deadline monitoring.
         */
        $today =
            now()->toDateString();

        $thirtyDaysFromNow =
            now()
                ->addDays(30)
                ->toDateString();

        $contractsExpiringSoon =
            Contract::query()
                ->where(
                    'status',
                    'active'
                )
                ->whereNotNull(
                    'end_date'
                )
                ->whereBetween(
                    'end_date',
                    [
                        $today,
                        $thirtyDaysFromNow,
                    ]
                )
                ->count();

        $legalExpiringSoon =
            LegalRecord::query()
                ->whereNotNull(
                    'expiration_date'
                )
                ->whereBetween(
                    'expiration_date',
                    [
                        $today,
                        $thirtyDaysFromNow,
                    ]
                )
                ->count();

        /*
         * Executive summary.
         */
        $summary = [
            'reservations' => (clone $reservationBase)
                ->count(),

            'approved_reservations' => (clone $reservationBase)
                ->where(
                    'status',
                    'approved'
                )
                ->count(),

            'reservation_attendees' => (int) (
                (clone $reservationBase)
                    ->sum('attendees')
            ),

            'visitors' => (clone $visitorBase)
                ->count(),

            'walk_in_visitors' => (clone $visitorBase)
                ->where(
                    'is_walk_in',
                    true
                )
                ->count(),

            'average_visit_minutes' => round(
                (float) (
                    (clone $visitorBase)
                        ->whereNotNull(
                            'duration_minutes'
                        )
                        ->avg(
                            'duration_minutes'
                        )
                    ?? 0
                ),
                1
            ),

            'appointments' => (clone $appointmentBase)
                ->count(),

            'documents' => (clone $documentBase)
                ->count(),

            'total_facilities' => Facility::count(),

            'available_facilities' => Facility::where(
                'status',
                'available'
            )->count(),

            'active_contracts' => Contract::where(
                'status',
                'active'
            )->count(),

            'contracts_expiring_soon' => $contractsExpiringSoon,

            'retention_issues' => RecordRetention::whereIn(
                'compliance_status',
                [
                    'at_risk',
                    'non_compliant',
                ]
            )->count(),

            'pending_disposals' => RecordRetention::where(
                'disposition_status',
                'pending'
            )->count(),

            'legal_action_required' => LegalRecord::where(
                'review_status',
                'action_required'
            )->count(),

            'legal_expiring_soon' => $legalExpiringSoon,
        ];

        return [
            'from' => $from,

            'to' => $to,

            'facilitySort' => $facilitySort,

            'reservationsByStatus' => $reservationsByStatus,

            'visitorsByType' => $visitorsByType,

            'visitorEntryType' => $visitorEntryType,

            'appointmentsByStatus' => $appointmentsByStatus,

            'documentsByStatus' => $documentsByStatus,

            'documentsByCategory' => $documentsByCategory,

            'retentionByCompliance' => $retentionByCompliance,

            'legalByReviewStatus' => $legalByReviewStatus,

            'contractsByStatus' => $contractsByStatus,

            'contractsByLegalReview' => $contractsByLegalReview,

            'facilityUtilization' => $facilityUtilization,

            'summary' => $summary,
        ];
    }
}
