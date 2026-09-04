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
        $query = Reservation::with('facility')->orderByDesc('date')->orderByDesc('start_time');

        // Non-decision-makers only see their own requests, mirroring the
        // original UI where "My Requests" vs "Pending Decisions" are scoped by role.
        if (! $request->user()->can('decideReservations')) {
            $query->where('requester_email', $request->user()->email);
        }

        $reservations = $query->paginate(15)->withQueryString();
        $facilities = Facility::where('status', 'available')->orderBy('name')->get();

        return view('reservations.index', compact('reservations', 'facilities'));
    }

    public function store(ReservationRequest $request): RedirectResponse
    {
        $this->reservations->submit($request->user(), $request->validated());

        return redirect()->route('reservations.index')->with('status', 'Reservation request submitted.');
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
