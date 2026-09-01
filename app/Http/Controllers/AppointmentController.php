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
    public function __construct(private AppointmentService $appointments) {}

    public function index(Request $request)
    {
        $appointments = Appointment::with('facility')
            ->when($request->filled('date'), fn ($q) => $q->whereDate('date', $request->date('date')))
            ->orderBy('date')->orderBy('start_time')
            ->paginate(15)->withQueryString();

        $facilities = Facility::where('status', 'available')->orderBy('name')->get();

        return view('appointments.index', compact('appointments', 'facilities'));
    }

    public function store(AppointmentRequest $request): RedirectResponse
    {
        $appointment = $this->appointments->create($request->user(), $request->validated());

        AuditLog::create([
            'actor_email' => $request->user()->email, 'actor_role' => $request->user()->app_role,
            'action' => 'create', 'module' => 'appointments', 'record_label' => "Appointment • {$appointment->visitor_name}",
            'record_id' => $appointment->id, 'created_at' => now(),
        ]);

        return redirect()->route('appointments.index')->with('status', 'Appointment scheduled.');
    }

    public function update(AppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validated();
        if (! empty($data['facility_id'])) {
            $data['facility_name'] = Facility::find($data['facility_id'])->name;
        }
        $appointment->update($data);

        return redirect()->route('appointments.index')->with('status', 'Appointment updated.');
    }

    public function destroy(Request $request, Appointment $appointment): RedirectResponse
    {
        $appointment->update(['status' => 'cancelled']);

        AuditLog::create([
            'actor_email' => $request->user()->email, 'actor_role' => $request->user()->app_role,
            'action' => 'update', 'module' => 'appointments', 'record_label' => "Appointment • {$appointment->visitor_name}",
            'record_id' => $appointment->id, 'details' => 'Cancelled', 'created_at' => now(),
        ]);

        return redirect()->route('appointments.index')->with('status', 'Appointment cancelled.');
    }
}
