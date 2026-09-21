<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Facility;
use App\Services\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        private AppointmentService $appointments
    ) {}

    public function index(Request $request)
    {
        $search = mb_substr(
            trim(
                (string) $request->query(
                    'q',
                    ''
                )
            ),
            0,
            120
        );

        $status = strtolower(
            trim(
                (string) $request->query(
                    'status',
                    ''
                )
            )
        );

        $allowedStatuses = [
            'scheduled',
            'confirmed',
            'checked_in',
            'completed',
            'cancelled',
            'no_show',
        ];

        if (
            ! in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {
            $status = '';
        }

        $scope = strtolower(
            trim(
                (string) $request->query(
                    'scope',
                    ''
                )
            )
        );

        if (
            ! in_array(
                $scope,
                [
                    'today',
                    'upcoming',
                    'past',
                ],
                true
            )
        ) {
            $scope = '';
        }

        $date = trim(
            (string) $request->query(
                'date',
                ''
            )
        );

        if ($date !== '') {

            $parsedDate =
                \DateTimeImmutable::createFromFormat(
                    '!Y-m-d',
                    $date
                );

            if (
                ! $parsedDate
                ||
                $parsedDate->format('Y-m-d')
                    !== $date
            ) {
                $date = '';
            }
        }

        $facilityId =
            $request->integer(
                'facility_id'
            );

        if ($facilityId <= 0) {
            $facilityId = null;
        }

        $baseQuery =
            Appointment::query()
                ->with('facility');

        /*
         * Overview counts.
         */
        $todayCount =
            Appointment::query()
                ->whereDate(
                    'date',
                    today()
                )
                ->count();

        $upcomingCount =
            Appointment::query()
                ->whereDate(
                    'date',
                    '>=',
                    today()
                )
                ->whereIn(
                    'status',
                    [
                        'scheduled',
                        'confirmed',
                    ]
                )
                ->count();

        $checkedInCount =
            Appointment::query()
                ->where(
                    'status',
                    'checked_in'
                )
                ->count();

        /*
         * Search.
         */
        if ($search !== '') {

            $needle =
                '%'
                .mb_strtolower($search)
                .'%';

            $baseQuery->where(
                function ($query) use ($needle) {

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
                            "LOWER(COALESCE(host_email, '')) LIKE ?",
                            [$needle]
                        )

                        ->orWhereRaw(
                            "LOWER(COALESCE(purpose, '')) LIKE ?",
                            [$needle]
                        )

                        ->orWhereRaw(
                            "LOWER(COALESCE(facility_name, '')) LIKE ?",
                            [$needle]
                        );
                }
            );
        }

        /*
         * Filters.
         */
        if ($status !== '') {

            $baseQuery->where(
                'status',
                $status
            );
        }

        if ($facilityId) {

            $baseQuery->where(
                'facility_id',
                $facilityId
            );
        }

        if ($date !== '') {

            $baseQuery->whereDate(
                'date',
                $date
            );
        }

        if ($scope === 'today') {

            $baseQuery->whereDate(
                'date',
                today()
            );
        }

        if ($scope === 'upcoming') {

            $baseQuery
                ->whereDate(
                    'date',
                    '>=',
                    today()
                )
                ->whereIn(
                    'status',
                    [
                        'scheduled',
                        'confirmed',
                    ]
                );
        }

        if ($scope === 'past') {

            $baseQuery->whereDate(
                'date',
                '<',
                today()
            );
        }

        $appointments =
            $baseQuery
                ->orderBy('date')
                ->orderBy('start_time')
                ->paginate(15)
                ->withQueryString();

        /*
         * Available facilities are used by
         * the Schedule Appointment form.
         */
        $facilities =
            Facility::where(
                'status',
                'available'
            )
                ->orderBy('name')
                ->get();

        /*
         * All facilities are available for
         * filtering historical records.
         */
        $filterFacilities =
            Facility::query()
                ->orderBy('name')
                ->get();

        return view(
            'appointments.index',
            compact(
                'appointments',
                'facilities',
                'filterFacilities',
                'search',
                'status',
                'scope',
                'date',
                'facilityId',
                'todayCount',
                'upcomingCount',
                'checkedInCount'
            )
        );
    }

    public function store(
        AppointmentRequest $request
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'manageAppointments'
            ),
            403
        );

        $this->appointments->create(
            $request->user(),
            $request->validated()
        );

        return redirect()
            ->route('appointments.index')
            ->with(
                'status',
                'Appointment scheduled.'
            );
    }

    public function update(
        AppointmentRequest $request,
        Appointment $appointment
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'manageAppointments'
            ),
            403
        );

        $this->appointments->update(
            $request->user(),
            $appointment,
            $request->validated()
        );

        return redirect()
            ->route('appointments.index')
            ->with(
                'status',
                'Appointment updated.'
            );
    }

    public function status(
        Request $request,
        Appointment $appointment
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'manageAppointments'
            ),
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                'in:confirmed,no_show',
            ],
        ]);

        $this->appointments->transition(
            $request->user(),
            $appointment,
            $validated['status']
        );

        $message =
            $validated['status'] === 'confirmed'
                ? 'Appointment confirmed.'
                : 'Appointment marked as no show.';

        return redirect()
            ->route('appointments.index')
            ->with(
                'status',
                $message
            );
    }

    public function destroy(
        Request $request,
        Appointment $appointment
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'manageAppointments'
            ),
            403
        );

        abort_if(
            in_array(
                $appointment->status,
                [
                    'checked_in',
                    'completed',
                    'cancelled',
                    'no_show',
                ],
                true
            ),
            422
        );

        $this->appointments->transition(
            $request->user(),
            $appointment,
            'cancelled'
        );

        return redirect()
            ->route('appointments.index')
            ->with(
                'status',
                'Appointment cancelled.'
            );
    }
}
