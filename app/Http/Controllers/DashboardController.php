<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ArchiveDocument;
use App\Models\Contract;
use App\Models\Facility;
use App\Models\LegalRecord;
use App\Models\RecordRetention;
use App\Models\Reservation;
use App\Models\Visitor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $now = now();

        $today = $now->toDateString();

        $currentTime = $now->format('H:i:s');

        $thirtyDaysFromNow = $now
            ->copy()
            ->addDays(30)
            ->toDateString();

        $canViewAppointments =
            $user->can('viewAppointments');

        $canViewVisitors =
            $user->can('viewVisitors');

        $canViewContracts =
            $user->can('viewContracts');

        $canViewLegal =
            $user->can('viewLegal');

        $canViewDocuments =
            $user->can('viewDocuments');

        $canManageRetention =
            $user->can('manageRetention');

        $canApproveDisposal =
            $user->can('approveRetentionDisposal');

        /*
         * Document alert scope.
         *
         * Apply the same source-module restrictions
         * used by Document Management.
         */
        $documentAlertQuery =
            ArchiveDocument::query();

        if (! $canViewVisitors) {
            $documentAlertQuery
                ->where(function ($query) {
                    $query
                        ->whereNull('source_module')
                        ->orWhere(
                            'source_module',
                            '<>',
                            'visitors'
                        );
                })
                ->whereNull('linked_visitor_id');
        }

        if (! $canViewContracts) {
            $documentAlertQuery
                ->where(function ($query) {
                    $query
                        ->whereNull('source_module')
                        ->orWhere(
                            'source_module',
                            '<>',
                            'contracts'
                        );
                })
                ->whereNull('linked_contract_id');
        }

        if (! $canViewLegal) {
            $documentAlertQuery
                ->where(function ($query) {
                    $query
                        ->whereNull('source_module')
                        ->orWhere(
                            'source_module',
                            '<>',
                            'legal'
                        );
                })
                ->whereNull(
                    'linked_legal_record_id'
                );
        }

        /*
         * Live operations.
         *
         * These queries are anchored to the same Laravel
         * server timestamp used throughout this request.
         */
        $liveNextAppointment = null;

        if ($canViewAppointments) {

            $liveNextAppointment =
                Appointment::query()
                    ->whereIn(
                        'status',
                        [
                            'scheduled',
                            'confirmed',
                        ]
                    )
                    ->where(function ($query) use (
                        $today,
                        $currentTime
                    ) {

                        $query
                            ->whereDate(
                                'date',
                                '>',
                                $today
                            )
                            ->orWhere(function ($query) use (
                                $today,
                                $currentTime
                            ) {

                                $query
                                    ->whereDate(
                                        'date',
                                        $today
                                    )
                                    ->whereTime(
                                        'start_time',
                                        '>=',
                                        $currentTime
                                    );

                            });

                    })
                    ->orderBy('date')
                    ->orderBy('start_time')
                    ->first();

        }

        $liveReservationsInUse =
            Reservation::with('facility')
                ->where(
                    'status',
                    'approved'
                )
                ->whereDate(
                    'date',
                    $today
                )
                ->whereTime(
                    'start_time',
                    '<=',
                    $currentTime
                )
                ->whereTime(
                    'end_time',
                    '>',
                    $currentTime
                )
                ->orderBy('end_time')
                ->get();

        return view('dashboard.index', [

            /*
             * Live operations
             */
            'liveNextAppointment' => $liveNextAppointment,

            'liveReservationsInUse' => $liveReservationsInUse,

            'liveFacilitiesInUseCount' => $liveReservationsInUse
                ->pluck('facility_id')
                ->filter()
                ->unique()
                ->count(),

            /*
             * Main dashboard cards
             */
            'facilityCount' => Facility::where(
                'status',
                'available'
            )->count(),

            'pendingReservations' => Reservation::where(
                'status',
                'pending'
            )->count(),

            'todaysAppointments' => $canViewAppointments
                    ? Appointment::whereDate(
                        'date',
                        $today
                    )->count()
                    : 0,

            'checkedInVisitors' => $canViewVisitors
                    ? Visitor::where(
                        'status',
                        'checked_in'
                    )->count()
                    : 0,

            'contractsExpiringSoon' => $canViewContracts
                    ? Contract::whereIn(
                        'status',
                        [
                            'active',
                            'renewed',
                        ]
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
                        ->count()
                    : 0,

            'legalActionRequired' => $canViewLegal
                    ? LegalRecord::where(
                        'review_status',
                        'action_required'
                    )->count()
                    : 0,

            /*
             * Document & compliance alerts
             */
            'documentNeedsReview' => $canViewDocuments
                    ? (clone $documentAlertQuery)
                        ->where(
                            'status',
                            'needs_review'
                        )
                        ->count()
                    : 0,

            'retentionReviewRequired' => $canManageRetention
                    ? RecordRetention::where(
                        'status',
                        'review_required'
                    )->count()
                    : 0,

            'pendingDisposalApprovals' => $canApproveDisposal
                    ? RecordRetention::where(
                        'disposition_status',
                        'pending'
                    )->count()
                    : 0,

            /*
             * Permissions passed to Blade
             */
            'canViewAppointments' => $canViewAppointments,

            'canViewVisitors' => $canViewVisitors,

            'canViewContracts' => $canViewContracts,

            'canViewLegal' => $canViewLegal,

            'canViewDocuments' => $canViewDocuments,

            'canManageRetention' => $canManageRetention,

            'canApproveDisposal' => $canApproveDisposal,

            /*
             * Upcoming reservation list
             */
            'upcomingReservations' => Reservation::with('facility')
                ->where(
                    'status',
                    'approved'
                )
                ->whereDate(
                    'date',
                    '>=',
                    $today
                )
                ->orderBy('date')
                ->orderBy('start_time')
                ->limit(6)
                ->get(),

            /*
             * Today's appointment list
             */
            'recentAppointments' => $canViewAppointments
                    ? Appointment::whereDate(
                        'date',
                        $today
                    )
                        ->orderBy(
                            'start_time'
                        )
                        ->limit(6)
                        ->get()
                    : collect(),
        ]);
    }
}
