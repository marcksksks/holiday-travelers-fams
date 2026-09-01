<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Contract;
use App\Models\Facility;
use App\Models\Reservation;
use App\Models\Visitor;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->date('from') ?? now()->subDays(30);
        $to = $request->date('to') ?? now();

        return view('reports.index', [
            'from' => $from,
            'to' => $to,
            'reservationsByStatus' => Reservation::whereBetween('date', [$from, $to])
                ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'visitorsByType' => Visitor::whereBetween('created_at', [$from, $to])
                ->selectRaw('visitor_type, count(*) as total')->groupBy('visitor_type')->pluck('total', 'visitor_type'),
            'appointmentsByStatus' => Appointment::whereBetween('date', [$from, $to])
                ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'facilityUtilization' => Reservation::with('facility')
                ->where('status', 'approved')->whereBetween('date', [$from, $to])
                ->selectRaw('facility_id, count(*) as bookings')->groupBy('facility_id')
                ->orderByDesc('bookings')->get(),
            'contractsByStatus' => Contract::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'facilities' => Facility::count(),
        ]);
    }
}
