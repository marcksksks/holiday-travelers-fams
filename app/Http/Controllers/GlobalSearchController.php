<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ArchiveDocument;
use App\Models\Contract;
use App\Models\Facility;
use App\Models\LegalRecord;
use App\Models\RecordRetention;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Visitor;
use App\Services\DocumentAccessService;
use App\Support\Rbac;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function __construct(
        private DocumentAccessService $documents
    ) {}

    public function index(
        Request $request
    ): JsonResponse {
        $validated =
            $request->validate([
                'q' => [
                    'nullable',
                    'string',
                    'max:100',
                ],
            ]);

        $search =
            mb_substr(
                trim(
                    (string) ($validated['q'] ?? '')
                ),
                0,
                100
            );

        if (mb_strlen($search) < 2) {
            return response()->json([
                'query' => $search,
                'count' => 0,
                'results' => [],
            ]);
        }

        $user =
            $request->user();

        $role =
            $user->app_role;

        $plainNeedle =
            mb_strtolower($search);

        $needle =
            '%'.$plainNeedle.'%';

        $results = [];

        $add =
            function (
                string $section,
                string $type,
                string $title,
                ?string $subtitle,
                string $url
            ) use (&$results): void {
                $results[] = [
                    'section' => $section,
                    'type' => $type,
                    'title' => $title,
                    'subtitle' => $subtitle,
                    'url' => $url,
                ];
            };

        /*
        |--------------------------------------------------------------------------
        | Workspaces
        |--------------------------------------------------------------------------
        */

        $workspaces = [
            [
                'route' => 'dashboard',
                'title' => 'Dashboard',
                'keywords' => 'home overview operations command center',
            ],
            [
                'route' => 'facilities.index',
                'title' => 'Facilities Reservation',
                'keywords' => 'facilities rooms vehicles resources spaces',
            ],
            [
                'route' => 'reservations.index',
                'title' => 'Reservations',
                'keywords' => 'bookings requests approvals facility',
            ],
            [
                'route' => 'appointments.index',
                'title' => 'Appointments',
                'keywords' => 'visitor schedule meetings appointment',
            ],
            [
                'route' => 'visitors.index',
                'title' => 'Visitor Desk',
                'keywords' => 'visitor reception guests check in check out',
            ],
            [
                'route' => 'documents.index',
                'title' => 'Document Management',
                'keywords' => 'documents archives library records files',
            ],
            [
                'route' => 'retention.index',
                'title' => 'Records Retention & Compliance',
                'keywords' => 'retention compliance governance disposal',
            ],
            [
                'route' => 'legal.index',
                'title' => 'Legal Management',
                'keywords' => 'legal permits licenses matters compliance',
            ],
            [
                'route' => 'contracts.index',
                'title' => 'Contract Management',
                'keywords' => 'contracts agreements renewal approval',
            ],
            [
                'route' => 'reports.index',
                'title' => 'Reports & Analytics',
                'keywords' => 'reports analytics intelligence performance',
            ],
            [
                'route' => 'audit-trail.index',
                'title' => 'Audit Trail',
                'keywords' => 'audit ledger accountability activity events',
            ],
            [
                'route' => 'users.index',
                'title' => 'Staff Accounts',
                'keywords' => 'staff users accounts roles administration',
            ],
            [
                'route' => 'settings.index',
                'title' => 'Settings',
                'keywords' => 'settings profile security appearance mfa',
            ],
            [
                'route' => 'notifications.index',
                'title' => 'Notifications',
                'keywords' => 'notifications alerts inbox activity',
            ],
        ];

        foreach ($workspaces as $workspace) {

            if (
                ! Rbac::canAccessRoute(
                    $workspace['route'],
                    $role
                )
            ) {
                continue;
            }

            $haystack =
                mb_strtolower(
                    $workspace['title'].' '.$workspace['keywords']
                );

            if (
                ! str_contains(
                    $haystack,
                    $plainNeedle
                )
            ) {
                continue;
            }

            $add(
                'Navigation',
                'Workspace',
                $workspace['title'],
                'Open workspace',
                route($workspace['route'])
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Facilities
        |--------------------------------------------------------------------------
        */

        if (
            Rbac::canAccessRoute(
                'facilities.index',
                $role
            )
        ) {
            $query =
                Facility::query();

            if (
                ! Rbac::can(
                    'viewArchivedFacilities',
                    $role
                )
            ) {
                $query->where(
                    'status',
                    '!=',
                    'archived'
                );
            }

            $facilities =
                $query
                    ->where(
                        function ($query) use ($needle): void {
                            $query
                                ->whereRaw(
                                    'LOWER(name) LIKE ?',
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(location, '')) LIKE ?",
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(facility_type, '')) LIKE ?",
                                    [$needle]
                                );
                        }
                    )
                    ->orderBy('name')
                    ->limit(4)
                    ->get();

            foreach ($facilities as $facility) {

                $add(
                    'Facilities',
                    'Facility',
                    $facility->name,
                    implode(
                        ' · ',
                        array_filter([
                            $facility->location,
                            ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    (string) $facility->status
                                )
                            ),
                        ])
                    ),
                    route(
                        'facilities.index',
                        [
                            'search' => $facility->name,
                        ]
                    )
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Reservations
        |--------------------------------------------------------------------------
        */

        if (
            Rbac::canAccessRoute(
                'reservations.index',
                $role
            )
        ) {
            $query =
                Reservation::query();

            if (
                ! Rbac::can(
                    'decideReservations',
                    $role
                )
            ) {
                $query->where(
                    'requester_email',
                    $user->email
                );
            }

            $reservations =
                $query
                    ->where(
                        function ($query) use ($needle): void {
                            $query
                                ->whereRaw(
                                    'LOWER(facility_name) LIKE ?',
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    'LOWER(requester_name) LIKE ?',
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    'LOWER(requester_email) LIKE ?',
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(purpose, '')) LIKE ?",
                                    [$needle]
                                );
                        }
                    )
                    ->orderByDesc('date')
                    ->limit(4)
                    ->get();

            foreach ($reservations as $reservation) {

                $add(
                    'Reservations',
                    'Reservation',
                    $reservation->facility_name,
                    implode(
                        ' · ',
                        array_filter([
                            $reservation->requester_name,
                            $reservation->date?->format('M d, Y'),
                            ucfirst(
                                (string) $reservation->status
                            ),
                        ])
                    ),
                    route(
                        'reservations.index',
                        [
                            'search' => $reservation->facility_name,
                        ]
                    )
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Appointments
        |--------------------------------------------------------------------------
        */

        if (
            Rbac::canAccessRoute(
                'appointments.index',
                $role
            )
        ) {
            $appointments =
                Appointment::query()
                    ->where(
                        function ($query) use ($needle): void {
                            $query
                                ->whereRaw(
                                    'LOWER(visitor_name) LIKE ?',
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(visitor_organization, '')) LIKE ?",
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(visitor_email, '')) LIKE ?",
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(host_name, '')) LIKE ?",
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(purpose, '')) LIKE ?",
                                    [$needle]
                                );
                        }
                    )
                    ->orderByDesc('date')
                    ->limit(4)
                    ->get();

            foreach ($appointments as $appointment) {

                $add(
                    'Appointments',
                    'Appointment',
                    $appointment->visitor_name,
                    implode(
                        ' · ',
                        array_filter([
                            $appointment->visitor_organization,
                            $appointment->facility_name,
                            $appointment->date?->format('M d, Y'),
                            ucfirst(
                                str_replace(
                                    '_',
                                    ' ',
                                    (string) $appointment->status
                                )
                            ),
                        ])
                    ),
                    route(
                        'appointments.index',
                        [
                            'q' => $appointment->visitor_name,
                        ]
                    )
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Visitors
        |--------------------------------------------------------------------------
        */

        if (
            Rbac::canAccessRoute(
                'visitors.index',
                $role
            )
            &&
            Rbac::can(
                'viewVisitors',
                $role
            )
        ) {
            $visitors =
                Visitor::query()
                    ->where(
                        function ($query) use ($needle): void {
                            $query
                                ->whereRaw(
                                    'LOWER(full_name) LIKE ?',
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(organization, '')) LIKE ?",
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(email, '')) LIKE ?",
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(host_name, '')) LIKE ?",
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(badge_number, '')) LIKE ?",
                                    [$needle]
                                );
                        }
                    )
                    ->orderByDesc('updated_at')
                    ->limit(4)
                    ->get();

            foreach ($visitors as $visitor) {

                $add(
                    'Visitors',
                    'Visitor',
                    $visitor->full_name,
                    implode(
                        ' · ',
                        array_filter([
                            $visitor->organization,
                            $visitor->host_name
                                ? 'Host: '.$visitor->host_name
                                : null,
                            ucfirst(
                                str_replace(
                                    '_',
                                    ' ',
                                    (string) $visitor->status
                                )
                            ),
                        ])
                    ),
                    route(
                        'visitors.index',
                        [
                            'q' => $visitor->full_name,
                        ]
                    )
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Documents
        |--------------------------------------------------------------------------
        */

        if (
            Rbac::canAccessRoute(
                'documents.index',
                $role
            )
        ) {
            $documents =
                $this
                    ->documents
                    ->scopeAccessible(
                        $user,
                        ArchiveDocument::query()
                    )
                    ->where(
                        function ($query) use ($needle): void {
                            $query
                                ->whereRaw(
                                    'LOWER(title) LIKE ?',
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(description, '')) LIKE ?",
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(file_name, '')) LIKE ?",
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(category, '')) LIKE ?",
                                    [$needle]
                                );
                        }
                    )
                    ->orderByDesc('updated_at')
                    ->limit(4)
                    ->get();

            foreach ($documents as $document) {

                $add(
                    'Documents',
                    'Document',
                    $document->title,
                    implode(
                        ' · ',
                        array_filter([
                            $document->category,
                            ucfirst(
                                (string) $document->status
                            ),
                        ])
                    ),
                    route(
                        'documents.show',
                        $document
                    )
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Retention
        |--------------------------------------------------------------------------
        */

        if (
            Rbac::canAccessRoute(
                'retention.index',
                $role
            )
        ) {
            $records =
                RecordRetention::query()
                    ->where(
                        function ($query) use ($needle): void {
                            $query
                                ->whereRaw(
                                    'LOWER(record_title) LIKE ?',
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(policy_name, '')) LIKE ?",
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(record_type, '')) LIKE ?",
                                    [$needle]
                                );
                        }
                    )
                    ->orderByDesc('updated_at')
                    ->limit(4)
                    ->get();

            foreach ($records as $record) {

                $add(
                    'Retention',
                    'Retention Record',
                    $record->record_title,
                    $record->policy_name,
                    route(
                        'retention.index',
                        [
                            'q' => $record->record_title,
                        ]
                    )
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Legal
        |--------------------------------------------------------------------------
        */

        if (
            Rbac::canAccessRoute(
                'legal.index',
                $role
            )
        ) {
            $records =
                LegalRecord::query()
                    ->where(
                        function ($query) use ($needle): void {
                            $query
                                ->whereRaw(
                                    'LOWER(title) LIKE ?',
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(reference_number, '')) LIKE ?",
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(issuing_authority, '')) LIKE ?",
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(legal_category, '')) LIKE ?",
                                    [$needle]
                                );
                        }
                    )
                    ->orderByDesc('updated_at')
                    ->limit(4)
                    ->get();

            foreach ($records as $record) {

                $add(
                    'Legal',
                    'Legal Record',
                    $record->title,
                    $record->reference_number,
                    route(
                        'legal.index',
                        [
                            'q' => $record->title,
                        ]
                    )
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Contracts
        |--------------------------------------------------------------------------
        */

        if (
            Rbac::canAccessRoute(
                'contracts.index',
                $role
            )
        ) {
            $contracts =
                Contract::query()
                    ->where(
                        function ($query) use ($needle): void {
                            $query
                                ->whereRaw(
                                    'LOWER(title) LIKE ?',
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(contract_number, '')) LIKE ?",
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(responsible_officer_email, '')) LIKE ?",
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(contract_type, '')) LIKE ?",
                                    [$needle]
                                );
                        }
                    )
                    ->orderByDesc('updated_at')
                    ->limit(4)
                    ->get();

            foreach ($contracts as $contract) {

                $add(
                    'Contracts',
                    'Contract',
                    $contract->title,
                    $contract->contract_number,
                    route(
                        'contracts.index',
                        [
                            'q' => $contract->title,
                        ]
                    )
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Staff accounts
        |--------------------------------------------------------------------------
        */

        if (
            Rbac::canAccessRoute(
                'users.index',
                $role
            )
        ) {
            $staff =
                User::query()
                    ->where(
                        function ($query) use ($needle): void {
                            $query
                                ->whereRaw(
                                    'LOWER(full_name) LIKE ?',
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    'LOWER(email) LIKE ?',
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(department, '')) LIKE ?",
                                    [$needle]
                                )
                                ->orWhereRaw(
                                    "LOWER(COALESCE(job_title, '')) LIKE ?",
                                    [$needle]
                                );
                        }
                    )
                    ->orderBy('full_name')
                    ->limit(4)
                    ->get();

            foreach ($staff as $member) {

                $add(
                    'Staff',
                    'Staff Account',
                    $member->full_name,
                    implode(
                        ' · ',
                        array_filter([
                            User::ROLES[
                                $member->app_role
                            ] ?? $member->app_role,
                            $member->email,
                        ])
                    ),
                    route(
                        'users.index',
                        [
                            'search' => $member->email,
                        ]
                    )
                );
            }
        }

        $results =
            array_slice(
                $results,
                0,
                28
            );

        return response()->json([
            'query' => $search,
            'count' => count($results),
            'results' => $results,
        ]);
    }
}
