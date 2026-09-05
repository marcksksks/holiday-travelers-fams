<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest;
use App\Models\Facility;
use App\Models\Reservation;
use App\Services\AuditService;
use App\Services\ReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(
        private ReservationService $reservations,
        private AuditService $audit
    ) {}

    public function index(Request $request)
    {
        $canDecide =
            $request->user()->can('decideReservations');

        $baseQuery = Reservation::query();

        if (! $canDecide) {
            $baseQuery->where(
                'requester_email',
                $request->user()->email
            );
        }

        $counts = [
            'total' =>
                (clone $baseQuery)->count(),

            'pending' =>
                (clone $baseQuery)
                    ->where('status', 'pending')
                    ->count(),

            'approved' =>
                (clone $baseQuery)
                    ->where('status', 'approved')
                    ->count(),

            'rejected' =>
                (clone $baseQuery)
                    ->where('status', 'rejected')
                    ->count(),

            'cancelled' =>
                (clone $baseQuery)
                    ->where('status', 'cancelled')
                    ->count(),

            'completed' =>
                (clone $baseQuery)
                    ->where('status', 'completed')
                    ->count(),
        ];

        $filterFacilityIds =
            (clone $baseQuery)
                ->whereNotNull('facility_id')
                ->distinct()
                ->pluck('facility_id');

        $filterFacilities = Facility::query()
            ->whereIn('id', $filterFacilityIds)
            ->orderBy('name')
            ->get();

        $query = clone $baseQuery;

        if ($request->filled('search')) {
            $search = trim(
                (string) $request->input('search')
            );

            $searchLike =
                '%'.strtolower($search).'%';

            $query->where(
                function ($reservationQuery) use (
                    $searchLike
                ) {
                    $reservationQuery
                        ->whereRaw(
                            'LOWER(facility_name) LIKE ?',
                            [$searchLike]
                        )
                        ->orWhereRaw(
                            'LOWER(requester_name) LIKE ?',
                            [$searchLike]
                        )
                        ->orWhereRaw(
                            'LOWER(requester_email) LIKE ?',
                            [$searchLike]
                        )
                        ->orWhereRaw(
                            'LOWER(COALESCE(purpose, ?)) LIKE ?',
                            ['', $searchLike]
                        );
                }
            );
        }

        if ($request->filled('status')) {
            $request->validate([
                'status' => [
                    'in:pending,approved,rejected,cancelled,completed',
                ],
            ]);

            $query->where(
                'status',
                $request->input('status')
            );
        }

        if ($request->filled('facility')) {
            $request->validate([
                'facility' => [
                    'integer',
                    'exists:facilities,id',
                ],
            ]);

            $query->where(
                'facility_id',
                $request->integer('facility')
            );
        }

        if ($request->filled('date')) {
            $request->validate([
                'date' => [
                    'date',
                ],
            ]);

            $query->whereDate(
                'date',
                $request->input('date')
            );
        }

        $reservations = $query
            ->with('facility')
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->paginate(15)
            ->withQueryString();

        $facilities = Facility::query()
            ->where('status', 'available')
            ->orderBy('name')
            ->get();

        return view(
            'reservations.index',
            compact(
                'reservations',
                'facilities',
                'filterFacilities',
                'counts',
                'canDecide'
            )
        );
    }

    public function store(ReservationRequest $request): RedirectResponse
    {
        $this->reservations->submit($request->user(), $request->validated());

        return redirect()->route('reservations.index')->with('status', 'Reservation request submitted.');
    }

    public function update(
        ReservationRequest $request,
        Reservation $reservation
    ): RedirectResponse {
        $this->reservations->resubmit(
            $request->user(),
            $reservation,
            $request->validated()
        );

        return redirect()
            ->route('reservations.index')
            ->with(
                'status',
                'Reservation updated and resubmitted for approval.'
            );
    }

    public function decide(Request $request, Reservation $reservation): RedirectResponse
    {
        abort_unless($request->user()->can('decideReservations'), 403);

        $data = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
            'note' => ['nullable', 'string'],
        ]);

        $this->reservations->decide($request->user(), $reservation, $data['decision'], $data['note'] ?? null);

        return redirect()->route('reservations.index')->with('status', "Reservation {$data['decision']}.");
    }

    public function destroy(Request $request, Reservation $reservation): RedirectResponse
    {
        abort_unless($reservation->requester_email === $request->user()->email || $request->user()->can('decideReservations'), 403);
        abort_if(in_array($reservation->status, ['cancelled', 'rejected', 'completed'], true), 422);
        $reservation->update(['status' => 'cancelled']);

        $this->audit->log(
            $request->user(),
            'update',
            'facilities',
            "Reservation • {$reservation->facility_name} {$reservation->date->toDateString()}",
            (string) $reservation->id,
            'Cancelled'
        );

        return redirect()->route('reservations.index')->with('status', 'Reservation cancelled.');
    }
}
