<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\AuditLog;
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
        $appointments = Appointment::with('facility')
            ->when(
                $request->filled('date'),
                fn ($q) =>
                    $q->whereDate(
                        'date',
                        $request->date('date')
                    )
            )
            ->orderBy('date')
            ->orderBy('start_time')
            ->paginate(15)
            ->withQueryString();

        $facilities = Facility::where(
            'status',
            'available'
        )
            ->orderBy('name')
            ->get();

        return view(
            'appointments.index',
            compact(
                'appointments',
                'facilities'
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
                    'completed',
                    'cancelled',
                ],
                true
            ),
            422
        );

        $appointment->update([
            'status' => 'cancelled',
        ]);

        AuditLog::create([
            'actor_email' =>
                $request->user()->email,

            'actor_role' =>
                $request->user()->app_role,

            'action' =>
                'update',

            'module' =>
                'appointments',

            'record_label' =>
                "Appointment • {$appointment->visitor_name}",

            'record_id' =>
                $appointment->id,

            'details' =>
                'Cancelled',

            'created_at' =>
                now(),
        ]);

        return redirect()
            ->route('appointments.index')
            ->with(
                'status',
                'Appointment cancelled.'
            );
    }
}