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
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $request->filled('from')
            ? $request->date('from')->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = $request->filled('to')
            ? $request->date('to')->endOfDay()
            : now()->endOfDay();

        $fromDate = $from->toDateString();
        $toDate = $to->toDateString();

        $reservationBase = Reservation::query()
            ->whereBetween('date', [$fromDate, $toDate]);

        $visitorBase = Visitor::query()
            ->whereBetween('created_at', [$from, $to]);

        $appointmentBase = Appointment::query()
            ->whereBetween('date', [$fromDate, $toDate]);

        $reservationsByStatus = (clone $reservationBase)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $visitorsByType = (clone $visitorBase)
            ->selectRaw('visitor_type, count(*) as total')
            ->groupBy('visitor_type')
            ->pluck('total', 'visitor_type');

        $appointmentsByStatus = (clone $appointmentBase)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $facilityUtilization = Reservation::with('facility')
            ->where('status', 'approved')
            ->whereBetween('date', [$fromDate, $toDate])
            ->selectRaw('facility_id, count(*) as bookings')
            ->groupBy('facility_id')
            ->orderByDesc('bookings')
            ->get();

        $contractsByStatus = Contract::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $summary = [
            'reservations' => (clone $reservationBase)->count(),
            'approved_reservations' => (clone $reservationBase)
                ->where('status', 'approved')
                ->count(),
            'visitors' => (clone $visitorBase)->count(),
            'appointments' => (clone $appointmentBase)->count(),
            'facilities' => Facility::count(),
            'active_contracts' => Contract::where('status', 'active')->count(),
        ];

        return view('reports.index', [
            'from' => $from,
            'to' => $to,
            'reservationsByStatus' => $reservationsByStatus,
            'visitorsByType' => $visitorsByType,
            'appointmentsByStatus' => $appointmentsByStatus,
            'facilityUtilization' => $facilityUtilization,
            'contractsByStatus' => $contractsByStatus,
            'summary' => $summary,
        ]);
    }
}