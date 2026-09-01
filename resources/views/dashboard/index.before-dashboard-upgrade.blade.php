@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="space-y-8">

    {{-- Welcome --}}
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">
            Welcome back, {{ auth()->user()->full_name }}!
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Here's what's happening across your administrative system today.
        </p>
    </div>


    {{-- Statistics --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">

        <a href="{{ route('facilities.index') }}"
           class="card group p-4 transition hover:-translate-y-1 hover:shadow-md">

            <p class="text-xs font-medium text-slate-500">
                Available Facilities
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-900">
                {{ $facilityCount }}
            </p>

            <p class="mt-2 text-xs text-slate-400 group-hover:text-slate-700">
                View facilities →
            </p>

        </a>


        <a href="{{ route('reservations.index') }}"
           class="card group p-4 transition hover:-translate-y-1 hover:shadow-md">

            <p class="text-xs font-medium text-slate-500">
                Pending Reservations
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-900">
                {{ $pendingReservations }}
            </p>

            <p class="mt-2 text-xs text-slate-400 group-hover:text-slate-700">
                Review reservations →
            </p>

        </a>


        <a href="{{ route('appointments.index') }}"
           class="card group p-4 transition hover:-translate-y-1 hover:shadow-md">

            <p class="text-xs font-medium text-slate-500">
                Today's Appointments
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-900">
                {{ $todaysAppointments }}
            </p>

            <p class="mt-2 text-xs text-slate-400 group-hover:text-slate-700">
                View appointments →
            </p>

        </a>


        <a href="{{ route('visitors.index') }}"
           class="card group p-4 transition hover:-translate-y-1 hover:shadow-md">

            <p class="text-xs font-medium text-slate-500">
                Checked-in Visitors
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-900">
                {{ $checkedInVisitors }}
            </p>

            <p class="mt-2 text-xs text-slate-400 group-hover:text-slate-700">
                Open visitor desk →
            </p>

        </a>


        <a href="{{ route('contracts.index') }}"
           class="card group p-4 transition hover:-translate-y-1 hover:shadow-md">

            <p class="text-xs font-medium text-slate-500">
                Contracts Expiring Soon
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-900">
                {{ $contractsExpiringSoon }}
            </p>

            <p class="mt-2 text-xs text-slate-400 group-hover:text-slate-700">
                View contracts →
            </p>

        </a>


        <a href="{{ route('legal.index') }}"
           class="card group p-4 transition hover:-translate-y-1 hover:shadow-md">

            <p class="text-xs font-medium text-slate-500">
                Legal Action Required
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-900">
                {{ $legalActionRequired }}
            </p>

            <p class="mt-2 text-xs text-slate-400 group-hover:text-slate-700">
                Review legal records →
            </p>

        </a>

    </div>


    {{-- Dashboard panels --}}
    <div class="grid gap-6 lg:grid-cols-2">

        {{-- Reservations --}}
        <div class="card overflow-hidden">

            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">

                <div>
                    <h2 class="font-semibold text-slate-900">
                        Upcoming Approved Reservations
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Recently approved facility bookings
                    </p>
                </div>

                <a
                    href="{{ route('reservations.index') }}"
                    class="text-xs font-medium text-slate-600 hover:text-slate-900">
                    View all →
                </a>

            </div>

            <ul class="divide-y divide-slate-100">

                @forelse ($upcomingReservations as $reservation)

                    <li class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-slate-50">

                        <div class="min-w-0">

                            <p class="truncate text-sm font-medium text-slate-800">
                                {{ $reservation->facility_name }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $reservation->date->format('M d, Y') }}
                                ·
                                {{ $reservation->start_time }}–{{ $reservation->end_time }}
                            </p>

                        </div>

                        <span class="badge shrink-0 bg-emerald-50 text-emerald-700">
                            Approved
                        </span>

                    </li>

                @empty

                    <li class="px-5 py-8 text-center text-sm text-slate-400">
                        No upcoming reservations.
                    </li>

                @endforelse

            </ul>

        </div>


        {{-- Appointments --}}
        <div class="card overflow-hidden">

            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">

                <div>
                    <h2 class="font-semibold text-slate-900">
                        Today's Appointments
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Scheduled visitors for today
                    </p>
                </div>

                <a
                    href="{{ route('appointments.index') }}"
                    class="text-xs font-medium text-slate-600 hover:text-slate-900">
                    View all →
                </a>

            </div>

            <ul class="divide-y divide-slate-100">

                @forelse ($recentAppointments as $appointment)

                    <li class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-slate-50">

                        <div class="min-w-0">

                            <p class="truncate text-sm font-medium text-slate-800">
                                {{ $appointment->visitor_name }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">

                                {{ $appointment->start_time }}

                                @if($appointment->host_name)
                                    · Host: {{ $appointment->host_name }}
                                @endif

                            </p>

                        </div>

                        <span class="badge shrink-0 bg-slate-100 text-slate-700">
                            {{ ucfirst($appointment->status) }}
                        </span>

                    </li>

                @empty

                    <li class="px-5 py-8 text-center text-sm text-slate-400">
                        No appointments scheduled today.
                    </li>

                @endforelse

            </ul>

        </div>

    </div>

</div>

@endsection