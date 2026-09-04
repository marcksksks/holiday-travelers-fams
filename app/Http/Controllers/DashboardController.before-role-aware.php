<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Contract;
use App\Models\Facility;
use App\Models\LegalRecord;
use App\Models\Reservation;
use App\Models\Visitor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        return view('dashboard.index', [
            'facilityCount' => Facility::where('status', 'available')->count(),
            'pendingReservations' => Reservation::where('status', 'pending')->count(),
            'todaysAppointments' => Appointment::whereDate('date', $today)->count(),
            'checkedInVisitors' => Visitor::where('status', 'checked_in')->count(),
            'contractsExpiringSoon' => Contract::where('status', 'expiring_soon')->count(),
            'legalActionRequired' => LegalRecord::where('review_status', 'action_required')->count(),
            'upcomingReservations' => Reservation::with('facility')
                ->where('status', 'approved')->whereDate('date', '>=', $today)
                ->orderBy('date')->orderBy('start_time')->limit(6)->get(),
            'recentAppointments' => Appointment::whereDate('date', $today)
                ->orderBy('start_time')->limit(6)->get(),
        ]);
    }
}
