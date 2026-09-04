<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use App\Services\AuditService;
use App\Services\ReservationService;
use Illuminate\Http\Request;

class ReservationApiController extends Controller
{
    public function __construct(
        private ReservationService $service,
        private AuditService $audit
    ) {}

    public function index(Request $request)
    {
        $query = Reservation::with('facility')->orderByDesc('date');
        if (! $request->user()->can('decideReservations')) {
            $query->where('requester_email', $request->user()->email);
        }

        return ReservationResource::collection($query->paginate(20));
    }

    public function show(Request $request, Reservation $reservation)
    {
        abort_unless(
            $request->user()->can('decideReservations')
                || $reservation->requester_email === $request->user()->email,
            403
        );

        return new ReservationResource($reservation);
    }

    public function store(ReservationRequest $request)
    {
        $reservation = $this->service->submit($request->user(), $request->validated());

        return (new ReservationResource($reservation))->response()->setStatusCode(201);
    }

    public function decide(Request $request, Reservation $reservation)
    {
        abort_unless($request->user()->can('decideReservations'), 403);
        $data = $request->validate(['decision' => ['required', 'in:approved,rejected'], 'note' => ['nullable', 'string']]);

        $reservation = $this->service->decide($request->user(), $reservation, $data['decision'], $data['note'] ?? null);

        return new ReservationResource($reservation);
    }

    public function destroy(Request $request, Reservation $reservation)
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

        return response()->json(null, 204);
    }
}
