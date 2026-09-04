<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Facility;
use App\Services\AppointmentService;
use App\Services\AuditService;
use Illuminate\Http\Request;

class AppointmentApiController extends Controller
{
    public function __construct(
        private AppointmentService $appointments,
        private AuditService $audit
    ) {}

    public function index(Request $request)
    {
        abort_unless($request->user()->can('viewAppointments'), 403);
        $appointments = Appointment::with('facility')
            ->when($request->filled('date'), fn ($q) => $q->whereDate('date', $request->date('date')))
            ->orderBy('date')->paginate(20);

        return AppointmentResource::collection($appointments);
    }

    public function show(Request $request, Appointment $appointment)
    {
        abort_unless($request->user()->can('viewAppointments'), 403);
        return new AppointmentResource($appointment);
    }

    public function store(AppointmentRequest $request)
    {
        abort_unless($request->user()->can('manageAppointments'), 403);
        $appointment = $this->appointments->create($request->user(), $request->validated());
        return (new AppointmentResource($appointment))->response()->setStatusCode(201);
    }

    public function update(AppointmentRequest $request, Appointment $appointment)
    {
        abort_unless($request->user()->can('manageAppointments'), 403);
        return new AppointmentResource($this->appointments->update($request->user(), $appointment, $request->validated()));
    }

    public function destroy(Request $request, Appointment $appointment)
    {
        abort_unless($request->user()->can('manageAppointments'), 403);
        abort_if(in_array($appointment->status, ['completed', 'cancelled'], true), 422);
        $appointment->update(['status' => 'cancelled']);

        $this->audit->log(
            $request->user(),
            'update',
            'appointments',
            "Appointment • {$appointment->visitor_name}",
            (string) $appointment->id,
            'Cancelled'
        );

        return response()->json(null, 204);
    }

}
