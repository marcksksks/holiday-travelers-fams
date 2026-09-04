@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="space-y-8">

    {{-- Welcome --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold tracking-tight text-primary">
                Welcome back, {{ auth()->user()->full_name }}!
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Here's what's happening across your administrative system today.
            </p>
        </div>

    </div>


    {{-- Statistics --}}
    @php
        $dashboardCardCount = 2;

        if (auth()->user()->can('viewAppointments')) {
            $dashboardCardCount++;
        }

        if (auth()->user()->can('viewVisitors')) {
            $dashboardCardCount++;
        }

        if (auth()->user()->can('viewContracts')) {
            $dashboardCardCount++;
        }

        if (auth()->user()->can('viewLegal')) {
            $dashboardCardCount++;
        }
    @endphp

    <div @class([
        'grid grid-cols-1 gap-5 sm:grid-cols-2',
        'lg:grid-cols-3 xl:grid-cols-6' => $dashboardCardCount >= 5,
        'lg:grid-cols-4 xl:grid-cols-4' => $dashboardCardCount === 4,
        'lg:grid-cols-3 xl:grid-cols-3' => $dashboardCardCount === 3,
        'lg:grid-cols-2 xl:grid-cols-2' => $dashboardCardCount <= 2,
    ])>


        {{-- Facilities --}}
        <a
            href="{{ route('facilities.index') }}"
            class="group card relative overflow-hidden border-b-4 border-b-primary p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

            <div class="flex items-start justify-between gap-3">

                <div>
                    <p class="text-xs font-medium leading-relaxed text-slate-500">
                        Available Facilities
                    </p>

                    <p class="mt-2 font-heading text-3xl font-bold text-primary">
                        {{ $facilityCount }}
                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5M9 8h1M14 8h1M9 11h1M14 11h1" />
                    </svg>

                </div>

            </div>

            <div class="mt-5 flex items-center gap-1 text-xs font-medium text-primary">
                <span>View facilities</span>
                <span class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
            </div>

        </a>


        {{-- Reservations --}}
        <a
            href="{{ route('reservations.index') }}"
            class="group card relative overflow-hidden border-b-4 border-b-secondary p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

            <div class="flex items-start justify-between gap-3">

                <div>
                    <p class="text-xs font-medium leading-relaxed text-slate-500">
                        Pending Reservations
                    </p>

                    <p class="mt-2 font-heading text-3xl font-bold text-primary">
                        {{ $pendingReservations }}
                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-secondary/10 text-secondary">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                    </svg>

                </div>

            </div>

            <div class="mt-5 flex items-center gap-1 text-xs font-medium text-secondary">
                <span>
                    @can('decideReservations')
                        Review reservations
                    @else
                        View reservations
                    @endcan
                </span>
                <span class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
            </div>

        </a>        {{-- Appointments --}}
        @can('viewAppointments')

<a
            href="{{ route('appointments.index') }}"
            class="group card relative overflow-hidden border-b-4 border-b-accent p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

            <div class="flex items-start justify-between gap-3">

                <div>
                    <p class="text-xs font-medium leading-relaxed text-slate-500">
                        Today's Appointments
                    </p>

                    <p class="mt-2 font-heading text-3xl font-bold text-primary">
                        {{ $todaysAppointments }}
                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-accent/10 text-accent">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1zM8 15h3M8 18h5" />
                    </svg>

                </div>

            </div>

            <div class="mt-5 flex items-center gap-1 text-xs font-medium text-accent">
                <span>View appointments</span>
                <span class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
            </div>

        </a>

        @endcan        {{-- Visitors --}}
        @can('viewVisitors')

<a
            href="{{ route('visitors.index') }}"
            class="group card relative overflow-hidden border-b-4 border-b-success p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

            <div class="flex items-start justify-between gap-3">

                <div>
                    <p class="text-xs font-medium leading-relaxed text-slate-500">
                        Checked-in Visitors
                    </p>

                    <p class="mt-2 font-heading text-3xl font-bold text-primary">
                        {{ $checkedInVisitors }}
                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-success/10 text-success">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8zM17 11h4M19 9v4" />
                    </svg>

                </div>

            </div>

            <div class="mt-5 flex items-center gap-1 text-xs font-medium text-success">
                <span>Open visitor desk</span>
                <span class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
            </div>

        </a>

        @endcan        {{-- Contracts --}}
        @can('viewContracts')

<a
            href="{{ route('contracts.index') }}"
            class="group card relative overflow-hidden border-b-4 border-b-warning p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

            <div class="flex items-start justify-between gap-3">

                <div>
                    <p class="text-xs font-medium leading-relaxed text-slate-500">
                        Contracts Expiring Soon
                    </p>

                    <p class="mt-2 font-heading text-3xl font-bold text-primary">
                        {{ $contractsExpiringSoon }}
                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-warning/10 text-amber-600">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M8 3h8l3 3v15H5V3h3zM8 3v5h8V3M8 13h8M8 17h6" />
                    </svg>

                </div>

            </div>

            <div class="mt-5 flex items-center gap-1 text-xs font-medium text-amber-600">
                <span>View contracts</span>
                <span class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
            </div>

        </a>

        @endcan        {{-- Legal --}}
        @can('viewLegal')

<a
            href="{{ route('legal.index') }}"
            class="group card relative overflow-hidden border-b-4 border-b-error p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

            <div class="flex items-start justify-between gap-3">

                <div>
                    <p class="text-xs font-medium leading-relaxed text-slate-500">
                        Legal Action Required
                    </p>

                    <p class="mt-2 font-heading text-3xl font-bold text-primary">
                        {{ $legalActionRequired }}
                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-error/10 text-error">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 3v18M5 7h14M7 7l-3 6h6L7 7zM17 7l-3 6h6l-3-6zM5 21h14" />
                    </svg>

                </div>

            </div>

            <div class="mt-5 flex items-center gap-1 text-xs font-medium text-error">
                <span>Review legal records</span>
                <span class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
            </div>

        </a>

        @endcan

    </div>


    {{-- Documents & Compliance --}}
    @if (
        $canViewDocuments ||
        $canManageRetention ||
        $canApproveDisposal
    )

        <section class="space-y-4">

            <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">

                <div>

                    <div class="mb-2 h-1 w-10 rounded-full bg-accent"></div>

                    <h2 class="font-heading text-lg font-semibold text-primary">
                        Documents & Compliance
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Records requiring document, retention, or disposition attention.
                    </p>

                </div>

            </div>


            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">


                {{-- Documents Needing Review --}}
                @if ($canViewDocuments)

                    <a
                        href="{{ route('documents.index', ['status' => 'needs_review']) }}"
                        class="group card relative overflow-hidden border-l-4 border-l-warning p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

                        <div class="flex items-start justify-between gap-4">

                            <div>

                                <p class="text-xs font-medium text-slate-500">
                                    Documents Needing Review
                                </p>

                                <p class="mt-2 font-heading text-3xl font-bold text-primary">
                                    {{ $documentNeedsReview }}
                                </p>

                                <p class="mt-2 text-xs text-slate-400">
                                    Documents currently flagged for review.
                                </p>

                            </div>


                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-warning/10 text-amber-600">

                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6M9 16h4M7 3h7l5 5v13H7V3zm7 0v5h5" />

                                </svg>

                            </div>

                        </div>


                        <div class="mt-5 flex items-center gap-1 text-xs font-semibold text-amber-600">

                            <span>
                                Review documents
                            </span>

                            <span class="transition-transform group-hover:translate-x-1">
                                &rarr;
                            </span>

                        </div>

                    </a>

                @endif


                {{-- Retention Reviews --}}
                @if ($canManageRetention)

                    <a
                        href="{{ route('retention.index', ['status' => 'review_required']) }}"
                        class="group card relative overflow-hidden border-l-4 border-l-error p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

                        <div class="flex items-start justify-between gap-4">

                            <div>

                                <p class="text-xs font-medium text-slate-500">
                                    Retention Reviews Required
                                </p>

                                <p class="mt-2 font-heading text-3xl font-bold text-primary">
                                    {{ $retentionReviewRequired }}
                                </p>

                                <p class="mt-2 text-xs text-slate-400">
                                    Retention records requiring a decision.
                                </p>

                            </div>


                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-error/10 text-error">

                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 9v4m0 4h.01M10.3 4.7L3.5 17a2 2 0 001.75 3h13.5a2 2 0 001.75-3L13.7 4.7a2 2 0 00-3.4 0z" />

                                </svg>

                            </div>

                        </div>


                        <div class="mt-5 flex items-center gap-1 text-xs font-semibold text-error">

                            <span>
                                Review retention records
                            </span>

                            <span class="transition-transform group-hover:translate-x-1">
                                &rarr;
                            </span>

                        </div>

                    </a>

                @endif


                {{-- Disposal Approvals --}}
                @if ($canApproveDisposal)

                    <a
                        href="{{ route('retention.index') }}"
                        class="group card relative overflow-hidden border-l-4 border-l-secondary p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

                        <div class="flex items-start justify-between gap-4">

                            <div>

                                <p class="text-xs font-medium text-slate-500">
                                    Pending Disposal Approvals
                                </p>

                                <p class="mt-2 font-heading text-3xl font-bold text-primary">
                                    {{ $pendingDisposalApprovals }}
                                </p>

                                <p class="mt-2 text-xs text-slate-400">
                                    Disposal requests waiting for authorization.
                                </p>

                            </div>


                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-secondary/10 text-secondary">

                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 11l3 3L22 4M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />

                                </svg>

                            </div>

                        </div>


                        <div class="mt-5 flex items-center gap-1 text-xs font-semibold text-secondary">

                            <span>
                                Review disposal requests
                            </span>

                            <span class="transition-transform group-hover:translate-x-1">
                                &rarr;
                            </span>

                        </div>

                    </a>

                @endif

            </div>

        </section>

    @endif


    {{-- Dashboard panels --}}
    <div @class([
        'grid gap-6',
        'lg:grid-cols-2' => auth()->user()->can('viewAppointments'),
        'grid-cols-1' => ! auth()->user()->can('viewAppointments'),
    ])>


        {{-- Reservations --}}
        <section class="card overflow-hidden">

            <div class="flex items-center justify-between gap-4 border-b border-border px-6 py-5">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                        </svg>

                    </div>

                    <div>
                        <h2 class="font-heading text-base font-semibold text-primary">
                            Upcoming Approved Reservations
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Recently approved facility bookings
                        </p>
                    </div>

                </div>

                <a
                    href="{{ route('reservations.index') }}"
                    class="group flex shrink-0 items-center gap-1 text-xs font-medium text-primary hover:text-secondary">

                    View all

                    <span class="transition-transform group-hover:translate-x-1">
                        &rarr;
                    </span>

                </a>

            </div>


            <ul class="divide-y divide-border">

                @forelse ($upcomingReservations as $reservation)

                    <li class="flex items-center justify-between gap-4 px-6 py-4 transition-colors hover:bg-background">

                        <div class="min-w-0">

                            <p class="truncate font-button text-sm font-medium text-primary">
                                {{ $reservation->facility_name }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $reservation->date->format('M d, Y') }}
                                &middot;
                                {{ $reservation->start_time }}&ndash;{{ $reservation->end_time }}
                            </p>

                        </div>

                        <span class="badge badge-success shrink-0">
                            Approved
                        </span>

                    </li>

                @empty

                    <li class="px-6 py-10">

                        <div class="flex flex-col items-center justify-center text-center">

                            <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-slate-400">

                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                                </svg>

                            </div>

                            <p class="text-sm font-medium text-slate-400">
                                No upcoming reservations.
                            </p>

                        </div>

                    </li>

                @endforelse

            </ul>

        </section>


        {{-- Appointments --}}
        @can('viewAppointments')

        <section class="card overflow-hidden">

            <div class="flex items-center justify-between gap-4 border-b border-border px-6 py-5">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-accent/10 text-accent">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1zM8 15h3M8 18h5" />
                        </svg>

                    </div>

                    <div>
                        <h2 class="font-heading text-base font-semibold text-primary">
                            Today's Appointments
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Scheduled visitors for today
                        </p>
                    </div>

                </div>

                <a
                    href="{{ route('appointments.index') }}"
                    class="group flex shrink-0 items-center gap-1 text-xs font-medium text-primary hover:text-accent">

                    View all

                    <span class="transition-transform group-hover:translate-x-1">
                        &rarr;
                    </span>

                </a>

            </div>


            <ul class="divide-y divide-border">

                @forelse ($recentAppointments as $appointment)

                    <li class="flex items-center justify-between gap-4 px-6 py-4 transition-colors hover:bg-background">

                        <div class="min-w-0">

                            <p class="truncate font-button text-sm font-medium text-primary">
                                {{ $appointment->visitor_name }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">

                                {{ $appointment->start_time }}

                                @if($appointment->host_name)
                                    &middot; Host: {{ $appointment->host_name }}
                                @endif

                            </p>

                        </div>

                        <span class="badge badge-info shrink-0">
                            {{ ucfirst($appointment->status) }}
                        </span>

                    </li>

                @empty

                    <li class="px-6 py-10">

                        <div class="flex flex-col items-center justify-center text-center">

                            <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-accent/10 text-accent">

                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8z" />
                                </svg>

                            </div>

                            <p class="text-sm font-medium text-slate-400">
                                No appointments scheduled today.
                            </p>

                        </div>

                    </li>

                @endforelse

            </ul>

                </section>

        @endcan

    </div>

</div>

@endsection