@extends('layouts.app')

@section('title', 'Facilities Reservation')

@section('content')

<div class="space-y-6">

    {{-- =====================================================
         FACILITIES RESERVATION WORKSPACE HEADER
    ====================================================== --}}
    <section class="card overflow-visible">

        {{-- Main header --}}
        <div class="flex flex-col gap-5 px-5 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between">

            <div class="min-w-0">

                <div class="flex items-center gap-2">

                    <span class="h-2 w-2 rounded-full bg-secondary"></span>

                    <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                        Facilities Reservation
                    </p>

                </div>

                <h1 class="mt-2 font-heading text-xl font-bold text-primary sm:text-2xl">
                    Reservations
                </h1>

                <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">

                    @if ($canDecide)

                        Review requests and coordinate facility availability from one workspace.

                    @else

                        Reserve an available facility and track your requests from one workspace.

                    @endif

                </p>

            </div>


            {{-- Primary action --}}
            <div class="flex shrink-0 flex-wrap items-center gap-2">

                @if ($facilities->isNotEmpty())

                    <details class="group relative">

                        <summary
                            class="btn-primary flex cursor-pointer list-none items-center justify-center gap-2">

                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4" />

                            </svg>

                            Reserve Facility

                            <svg
                                class="h-3.5 w-3.5 transition group-open:rotate-180"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7" />

                            </svg>

                        </summary>


                        <div class="absolute right-0 z-40 mt-2 w-[min(22rem,calc(100vw-2rem))] overflow-hidden rounded-xl border border-border bg-card shadow-2xl">

                            <div class="border-b border-border px-4 py-3">

                                <p class="text-xs font-semibold text-primary">
                                    Choose a facility
                                </p>

                                <p class="mt-0.5 text-[11px] text-slate-400">
                                    Only operationally available facilities are listed.
                                </p>

                            </div>


                            <div class="max-h-72 overflow-y-auto p-1.5">

                                @foreach ($facilities as $facility)

                                    <a
                                        href="{{ route('reservations.index', ['reserve_facility' => $facility->id]) }}"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-background">

                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/5 text-primary">

                                            @if ($facility->facility_type === 'vehicle')

                                                <svg
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24">

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 13l2-5h14l2 5M5 13v6m14-6v6M6 17h.01M18 17h.01M5 13h14" />

                                                </svg>

                                            @else

                                                <svg
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24">

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5" />

                                                </svg>

                                            @endif

                                        </div>


                                        <div class="min-w-0 flex-1">

                                            <p class="truncate text-sm font-semibold text-slate-700">
                                                {{ $facility->name }}
                                            </p>

                                            <p class="mt-0.5 truncate text-[11px] text-slate-400">

                                                {{ str($facility->facility_type)->headline() }}

                                                @if ($facility->capacity)

                                                    &bull;
                                                    Capacity {{ number_format($facility->capacity) }}

                                                @endif

                                            </p>

                                        </div>


                                        <svg
                                            class="h-4 w-4 shrink-0 text-slate-300"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 5l7 7-7 7" />

                                        </svg>

                                    </a>

                                @endforeach

                            </div>

                        </div>

                    </details>

                @else

                    <a
                        href="{{ route('facilities.index') }}"
                        class="btn-outline inline-flex items-center gap-2">

                        Browse Facilities

                    </a>

                @endif

            </div>

        </div>


        {{-- Module navigation --}}
        <div class="border-t border-border px-3 sm:px-5">

            <nav
                class="flex gap-1"
                aria-label="Facilities Reservation workspace">

                <a
                    href="{{ route('facilities.index') }}"
                    class="relative inline-flex items-center gap-2 px-3 py-3.5 font-button text-sm font-semibold text-slate-500 transition hover:text-primary sm:px-4">

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5" />

                    </svg>

                    Facilities

                </a>


                <a
                    href="{{ route('reservations.index') }}"
                    class="relative inline-flex items-center gap-2 px-3 py-3.5 font-button text-sm font-semibold text-primary sm:px-4"
                    aria-current="page">

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

                    </svg>

                    Reservations

                    <span class="absolute inset-x-3 bottom-0 h-0.5 rounded-full bg-secondary sm:inset-x-4"></span>

                </a>

            </nav>

        </div>

    </section>

    {{-- =====================================================
         COMPACT RESERVATION OVERVIEW
    ====================================================== --}}
    <div class="card overflow-hidden">

        <div class="grid sm:grid-cols-3">

            {{-- Total --}}
            <a
                href="{{ route('reservations.index') }}"
                class="group flex items-center justify-between gap-4 border-b border-border px-5 py-4 transition hover:bg-background/70 sm:border-b-0 sm:border-r">

                <div>

                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        Total Requests
                    </p>

                    <p class="mt-1 font-heading text-2xl font-bold text-primary">
                        {{ number_format($counts['total']) }}
                    </p>

                </div>

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/5 text-primary transition group-hover:bg-primary/10">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

                    </svg>

                </div>

            </a>


            {{-- Pending --}}
            <a
                href="{{ route('reservations.index', ['status' => 'pending']) }}"
                @class([
                    'group flex items-center justify-between gap-4 border-b border-border px-5 py-4 transition hover:bg-background/70 sm:border-b-0 sm:border-r',
                    'bg-warning/5' => request('status') === 'pending',
                ])>

                <div>

                    <div class="flex items-center gap-2">

                        <span class="h-2 w-2 rounded-full bg-warning"></span>

                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                            Pending
                        </p>

                    </div>

                    <p class="mt-1 font-heading text-2xl font-bold text-amber-600">
                        {{ number_format($counts['pending']) }}
                    </p>

                </div>

                @if ($counts['pending'] > 0)

                    <span class="rounded-full bg-warning/10 px-2.5 py-1 text-[10px] font-semibold text-amber-700">
                        Needs review
                    </span>

                @endif

            </a>


            {{-- Approved --}}
            <a
                href="{{ route('reservations.index', ['status' => 'approved']) }}"
                @class([
                    'group flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-background/70',
                    'bg-success/5' => request('status') === 'approved',
                ])>

                <div>

                    <div class="flex items-center gap-2">

                        <span class="h-2 w-2 rounded-full bg-success"></span>

                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                            Approved
                        </p>

                    </div>

                    <p class="mt-1 font-heading text-2xl font-bold text-success">
                        {{ number_format($counts['approved']) }}
                    </p>

                </div>

                <span class="text-[11px] font-medium text-slate-400">
                    Active
                </span>

            </a>

        </div>

    </div>


    {{-- =====================================================
         SHARED FACILITY AVAILABILITY
    ====================================================== --}}
    @include('reservations._facility-usage')


    {{-- =====================================================
         RESERVATION WORKSPACE
    ====================================================== --}}
    <div class="card overflow-visible">

        {{-- Workspace heading --}}
        <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2 class="font-heading text-base font-semibold text-primary">
                    {{ $canDecide ? 'Reservation Requests' : 'My Reservations' }}
                </h2>

                <p class="mt-1 text-xs text-slate-500">

                    @if ($reservations->total())

                        Showing
                        {{ number_format($reservations->firstItem()) }}
                        –
                        {{ number_format($reservations->lastItem()) }}
                        of
                        {{ number_format($reservations->total()) }}

                    @else

                        No reservations match the current view

                    @endif

                </p>

            </div>


            @if (
                request()->filled('search')
                ||
                request()->filled('status')
                ||
                request()->filled('facility')
                ||
                request()->filled('date')
            )

                <a
                    href="{{ route('reservations.index') }}"
                    class="inline-flex items-center gap-1.5 self-start text-xs font-semibold text-slate-500 transition hover:text-primary sm:self-auto">

                    <svg
                        class="h-3.5 w-3.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />

                    </svg>

                    Clear filters

                </a>

            @endif

        </div>


        {{-- Compact filter toolbar --}}
        <form
            method="GET"
            action="{{ route('reservations.index') }}"
            class="border-t border-border bg-background/35 px-5 py-4">

            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[minmax(240px,1.6fr)_180px_220px_170px_auto]">

                {{-- Search --}}
                <div class="relative">

                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">

                        <svg
                            class="h-4 w-4 text-slate-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />

                        </svg>

                    </div>

                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search reservations..."
                        class="input pl-9">

                </div>


                {{-- Status --}}
                <select
                    name="status"
                    class="input"
                    aria-label="Reservation status">

                    <option value="">
                        All statuses
                    </option>

                    @foreach ([
                        'pending',
                        'approved',
                        'rejected',
                        'cancelled',
                        'completed'
                    ] as $status)

                        <option
                            value="{{ $status }}"
                            @selected(request('status') === $status)>

                            {{ str($status)->headline() }}

                        </option>

                    @endforeach

                </select>


                {{-- Facility --}}
                <select
                    name="facility"
                    class="input"
                    aria-label="Facility">

                    <option value="">
                        All facilities
                    </option>

                    @foreach ($filterFacilities as $facility)

                        <option
                            value="{{ $facility->id }}"
                            @selected(
                                (string) request('facility') ===
                                (string) $facility->id
                            )>

                            {{ $facility->name }}

                        </option>

                    @endforeach

                </select>


                {{-- Date --}}
                <input
                    type="date"
                    name="date"
                    value="{{ request('date') }}"
                    class="input"
                    aria-label="Reservation date">


                {{-- Apply --}}
                <button
                    type="submit"
                    class="btn-primary justify-center whitespace-nowrap">

                    Apply

                </button>

            </div>

        </form>

    </div>

    @include('reservations._details-modal')
    @include('reservations._actions-menu')

    @include('reservations._mobile-cards')

    {{-- Table --}}
    <div class="table-shell hidden overflow-hidden md:block">

        <div class="overflow-x-auto">

            <table class="min-w-full text-left text-sm">

                <thead class="table-header sticky top-0 z-10">

                    <tr>

                        <th class="px-5 py-3.5 font-medium">
                            Facility / Request
                        </th>

                        <th class="px-5 py-3.5 font-medium">
                            Schedule
                        </th>

                        @if ($canDecide)

                            <th class="px-5 py-3.5 font-medium">
                                Requester
                            </th>

                        @endif

                        <th class="px-5 py-3.5 font-medium">
                            Status
                        </th>

                        <th class="w-16 px-5 py-3.5 text-right font-medium">
                            <span class="sr-only">
                                Actions
                            </span>
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-border bg-card">

                    @forelse ($reservations as $reservation)

                        <tr
                            @class([
                                'group transition hover:bg-background/70',
                                'opacity-65' => in_array(
                                    $reservation->status,
                                    ['rejected', 'cancelled', 'completed'],
                                    true
                                ),
                            ])>

                            {{-- Reservation --}}
                            <td class="px-5 py-4">

                                <div class="flex items-start gap-3">

                                    <div
                                        @class([
                                            'mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg',
                                            'bg-warning/10 text-amber-600' => $reservation->status === 'pending',
                                            'bg-success/10 text-success' => $reservation->status === 'approved',
                                            'bg-error/10 text-error' => $reservation->status === 'rejected',
                                            'bg-slate-100 text-slate-400' => in_array(
                                                $reservation->status,
                                                ['cancelled', 'completed'],
                                                true
                                            ),
                                        ])>

                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5" />

                                        </svg>

                                    </div>


                                    <div class="min-w-0">

                                        <p class="max-w-[260px] truncate font-button text-sm font-semibold text-primary">
                                            {{ $reservation->facility_name }}
                                        </p>


                                        <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px] text-slate-400">

                                            <span>
                                                Reservation #{{ $reservation->id }}
                                            </span>


                                            @if ($reservation->attendees)

                                                <span aria-hidden="true">
                                                    &bull;
                                                </span>

                                                <span>

                                                    {{ number_format($reservation->attendees) }}

                                                    {{
                                                        $reservation->facility?->facility_type === 'vehicle'
                                                            ? 'passengers'
                                                            : 'people'
                                                    }}

                                                </span>

                                            @endif

                                        </div>


                                        @if ($reservation->purpose)

                                            <p
                                                class="mt-1.5 max-w-[280px] truncate text-xs text-slate-500"
                                                title="{{ $reservation->purpose }}">

                                                {{ $reservation->purpose }}

                                            </p>

                                        @endif

                                    </div>

                                </div>

                            </td>


                            {{-- Schedule --}}
                            <td class="px-5 py-4">

                                <div class="space-y-1">

                                    <p class="text-sm font-semibold text-slate-700">
                                        {{ $reservation->date->format('M d, Y') }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        {{ $reservation->start_time }}
                                        –
                                        {{ $reservation->end_time }}
                                    </p>


                                    @if ($reservation->status === 'approved')

                                        @php
                                            $reservationDate =
                                                $reservation->date->format('Y-m-d');

                                            $reservationStartsAt =
                                                \Illuminate\Support\Carbon::parse(
                                                    $reservationDate
                                                    . ' '
                                                    . substr(
                                                        (string) $reservation->start_time,
                                                        0,
                                                        5
                                                    ),
                                                    config(
                                                        'app.timezone',
                                                        'Asia/Manila'
                                                    )
                                                );

                                            $reservationEndsAt =
                                                \Illuminate\Support\Carbon::parse(
                                                    $reservationDate
                                                    . ' '
                                                    . substr(
                                                        (string) $reservation->end_time,
                                                        0,
                                                        5
                                                    ),
                                                    config(
                                                        'app.timezone',
                                                        'Asia/Manila'
                                                    )
                                                );
                                        @endphp

                                        <div
                                            class="flex items-center gap-1.5"
                                            data-reservation-live-timing
                                            data-reservation-start-ms="{{ $reservationStartsAt->timestamp * 1000 }}"
                                            data-reservation-end-ms="{{ $reservationEndsAt->timestamp * 1000 }}"
                                            data-server-now-ms="{{ now()->timestamp * 1000 }}">

                                            <span
                                                class="h-1.5 w-1.5 shrink-0 rounded-full bg-success"
                                                aria-hidden="true">
                                            </span>

                                            <span
                                                class="text-[9px] font-semibold text-success"
                                                data-reservation-live-timing-value>
                                                Calculating...
                                            </span>

                                        </div>

                                    @endif

                                </div>

                            </td>


                            @if ($canDecide)

                                {{-- Requester --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-2">

                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-accent/10 font-button text-xs font-semibold uppercase text-primary">
                                            {{ \Illuminate\Support\Str::substr($reservation->requester_name, 0, 1) }}
                                        </div>

                                        <div class="min-w-0">

                                            <p class="max-w-[170px] truncate text-sm font-medium text-slate-700">
                                                {{ $reservation->requester_name }}
                                            </p>

                                            @if ($reservation->requester_email === auth()->user()->email)

                                                <p class="mt-0.5 text-[10px] font-semibold uppercase tracking-wide text-accent">
                                                    Your request
                                                </p>

                                            @endif

                                        </div>

                                    </div>

                                </td>

                            @endif


                            {{-- Status --}}
                            <td class="px-5 py-4">

                                @switch($reservation->status)

                                    @case('pending')

                                        <span class="badge badge-warning">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-warning"></span>
                                            Pending
                                        </span>

                                        @break


                                    @case('approved')

                                        <span class="badge badge-success">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                            Approved
                                        </span>

                                        @break


                                    @case('rejected')

                                        <span class="badge badge-error">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-error"></span>
                                            Rejected
                                        </span>

                                        @break


                                    @case('cancelled')

                                        <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                            Cancelled
                                        </span>

                                        @break


                                    @case('completed')

                                        <span class="badge badge-info">
                                            Completed
                                        </span>

                                        @break

                                @endswitch

                                {{-- Reservation rejection reason --}}
                                @if (
                                    $reservation->status === 'rejected'
                                    &&
                                    filled($reservation->decision_note)
                                )

                                    <p
                                        class="mt-2 max-w-[180px] text-xs leading-relaxed text-error"
                                        title="{{ $reservation->decision_note }}">

                                        <span class="font-semibold">
                                            Reason:
                                        </span>

                                        {{ str($reservation->decision_note)->limit(80) }}

                                    </p>

                                @endif


                            </td>


                            {{-- Actions --}}
                            <td class="px-5 py-4 text-right">

                                @php
                                    $isReservationOwner =
                                        $reservation->requester_email ===
                                        auth()->user()->email;

                                    $canReviewReservation =
                                        auth()->user()->can(
                                            'decideReservations'
                                        );

                                    $canEditReservation =
                                        $isReservationOwner
                                        &&
                                        in_array(
                                            $reservation->status,
                                            ['pending', 'rejected'],
                                            true
                                        );

                                    $canCancelReservation =
                                        $isReservationOwner
                                        &&
                                        $reservation->status === 'pending';

                                    $canDecideReservation =
                                        $canReviewReservation
                                        &&
                                        ! $isReservationOwner
                                        &&
                                        $reservation->status === 'pending';
                                @endphp


                                <button
                                    type="button"
                                    data-reservation-actions-open
                                    data-reservation-id="{{ $reservation->id }}"
                                    data-can-decide="{{ $canDecideReservation ? '1' : '0' }}"
                                    data-can-edit="{{ $canEditReservation ? '1' : '0' }}"
                                    data-can-cancel="{{ $canCancelReservation ? '1' : '0' }}"
                                    data-decide-url="{{ route('reservations.decide', $reservation) }}"
                                    data-cancel-url="{{ route('reservations.cancel', $reservation) }}"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-border bg-card text-slate-500 transition hover:border-accent/40 hover:bg-accent/5 hover:text-primary focus:outline-none focus:ring-2 focus:ring-accent/20"
                                    aria-label="Open reservation actions">

                                    <svg
                                        class="h-5 w-5"
                                        fill="currentColor"
                                        viewBox="0 0 24 24">

                                        <circle cx="5" cy="12" r="1.7" />
                                        <circle cx="12" cy="12" r="1.7" />
                                        <circle cx="19" cy="12" r="1.7" />

                                    </svg>

                                </button>


                                {{-- Hidden View Details trigger --}}
                                <button
                                    type="button"
                                    data-reservation-view-open
                                    data-reservation-id="{{ $reservation->id }}"
                                    data-reservation-facility="{{ $reservation->facility_name }}"
                                    data-reservation-facility-type="{{ $reservation->facility?->facility_type }}"
                                    data-reservation-location="{{ $reservation->facility?->location }}"
                                    data-reservation-capacity="{{ $reservation->facility?->capacity }}"
                                    data-reservation-date="{{ $reservation->date->format('M d, Y') }}"
                                    data-reservation-start="{{ substr($reservation->start_time, 0, 5) }}"
                                    data-reservation-end="{{ substr($reservation->end_time, 0, 5) }}"
                                    data-reservation-attendees="{{ $reservation->attendees }}"
                                    data-reservation-purpose="{{ $reservation->purpose }}"
                                    data-reservation-requester="{{ $reservation->requester_name }}"
                                    data-reservation-requester-email="{{ $reservation->requester_email }}"
                                    data-reservation-status="{{ $reservation->status }}"
                                    data-reservation-note="{{ $reservation->decision_note }}"
                                    class="hidden"
                                    tabindex="-1"
                                    aria-hidden="true">
                                </button>


                                @if ($canEditReservation)

                                    {{-- Existing edit-modal trigger --}}
                                    <button
                                        type="button"
                                        data-reservation-edit-open
                                        data-reservation-id="{{ $reservation->id }}"
                                        data-reservation-facility-id="{{ $reservation->facility_id }}"
                                        data-reservation-facility="{{ $reservation->facility_name }}"
                                        data-reservation-facility-type="{{ $reservation->facility?->facility_type }}"
                                        data-reservation-facility-status="{{ $reservation->facility?->status }}"
                                        data-reservation-capacity="{{ $reservation->facility?->capacity }}"
                                        data-reservation-date="{{ $reservation->date->toDateString() }}"
                                        data-reservation-start="{{ substr($reservation->start_time, 0, 5) }}"
                                        data-reservation-end="{{ substr($reservation->end_time, 0, 5) }}"
                                        data-reservation-attendees="{{ $reservation->attendees }}"
                                        data-reservation-purpose="{{ $reservation->purpose }}"
                                        data-reservation-note="{{ $reservation->decision_note }}"
                                        data-reservation-update-url="{{ route('reservations.resubmit', $reservation) }}"
                                        class="hidden"
                                        tabindex="-1"
                                        aria-hidden="true">
                                    </button>

                                @endif

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="{{ $canDecide ? 5 : 4 }}"
                                class="px-6 py-16">

                                <div class="mx-auto max-w-sm text-center">

                                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-accent/10 text-accent">

                                        <svg
                                            class="h-7 w-7"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

                                        </svg>

                                    </div>


                                    @if (
                                        request()->filled('search')
                                        || request()->filled('status')
                                        || request()->filled('facility')
                                        || request()->filled('date')
                                    )

                                        <h3 class="font-heading text-base font-semibold text-primary">
                                            No matching reservations
                                        </h3>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Try changing your search or clearing some filters.
                                        </p>

                                        <a
                                            href="{{ route('reservations.index') }}"
                                            class="btn-outline mt-5 inline-flex">

                                            Clear filters

                                        </a>

                                    @else

                                        <h3 class="font-heading text-base font-semibold text-primary">
                                            No reservation requests
                                        </h3>

                                        <p class="mt-1 text-sm text-slate-500">
                                            New facility reservation requests will appear here.
                                        </p>

                                        @if ($facilities->isNotEmpty())

                                            

                                        @endif

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

                    {{-- Live approved-reservation timing --}}
                    @once
                        <script>
                            document.addEventListener(
                                'DOMContentLoaded',
                                () => {

                                    const timingElements =
                                        document.querySelectorAll(
                                            '[data-reservation-live-timing]'
                                        );

                                    if (! timingElements.length) {
                                        return;
                                    }

                                    const pageStartedAt =
                                        performance.now();


                                    const formatDuration =
                                        (milliseconds) => {

                                            const totalMinutes =
                                                Math.max(
                                                    0,
                                                    Math.ceil(
                                                        milliseconds / 60000
                                                    )
                                                );

                                            if (totalMinutes < 1) {
                                                return '< 1m';
                                            }

                                            const days =
                                                Math.floor(
                                                    totalMinutes / 1440
                                                );

                                            const hours =
                                                Math.floor(
                                                    (totalMinutes % 1440) / 60
                                                );

                                            const minutes =
                                                totalMinutes % 60;


                                            if (days > 0) {

                                                if (hours > 0) {
                                                    return `${days}d ${hours}h`;
                                                }

                                                return `${days}d`;

                                            }


                                            if (hours > 0) {

                                                if (minutes > 0) {
                                                    return `${hours}h ${minutes}m`;
                                                }

                                                return `${hours}h`;

                                            }


                                            return `${minutes}m`;

                                        };


                                    const famsReservationLiveTiming =
                                        () => {

                                            const elapsedSinceLoad =
                                                performance.now() -
                                                pageStartedAt;


                                            timingElements.forEach(
                                                (element) => {

                                                    const startMs =
                                                        Number(
                                                            element.dataset
                                                                .reservationStartMs
                                                        );

                                                    const endMs =
                                                        Number(
                                                            element.dataset
                                                                .reservationEndMs
                                                        );

                                                    const serverNowMs =
                                                        Number(
                                                            element.dataset
                                                                .serverNowMs
                                                        );

                                                    const value =
                                                        element.querySelector(
                                                            '[data-reservation-live-timing-value]'
                                                        );


                                                    if (
                                                        ! value ||
                                                        ! Number.isFinite(startMs) ||
                                                        ! Number.isFinite(endMs) ||
                                                        ! Number.isFinite(serverNowMs)
                                                    ) {
                                                        return;
                                                    }


                                                    const currentTime =
                                                        serverNowMs +
                                                        elapsedSinceLoad;


                                                    if (currentTime < startMs) {

                                                        const remaining =
                                                            startMs -
                                                            currentTime;

                                                        if (remaining < 60000) {

                                                            value.textContent =
                                                                'Starting now';

                                                        }
                                                        else {

                                                            value.textContent =
                                                                `Starts in ${
                                                                    formatDuration(
                                                                        remaining
                                                                    )
                                                                }`;

                                                        }

                                                        return;
                                                    }


                                                    if (
                                                        currentTime >= startMs &&
                                                        currentTime < endMs
                                                    ) {

                                                        const remaining =
                                                            endMs -
                                                            currentTime;

                                                        value.textContent =
                                                            `In use now \u2022 ${
                                                                formatDuration(
                                                                    remaining
                                                                )
                                                            } remaining`;

                                                        return;
                                                    }


                                                    const elapsed =
                                                        currentTime -
                                                        endMs;

                                                    value.textContent =
                                                        `Ended ${
                                                            formatDuration(
                                                                elapsed
                                                            )
                                                        } ago`;

                                                }
                                            );

                                        };


                                    famsReservationLiveTiming();

                                    window.setInterval(
                                        famsReservationLiveTiming,
                                        30000
                                    );

                                }
                            );
                        </script>
                    @endonce

        </div>

    </div>


    @if ($reservations->hasPages())

        <div class="card px-5 py-4">
            {{ $reservations->links() }}
        </div>

    @endif
    @php
        $selectedFacilityId =
            old('_reservation_modal_context') === 'create'
                ? old('facility_id')
                : request('reserve_facility');

        $selectedFacility =
            $facilities->first(
                fn ($facility) =>
                    (string) $facility->id ===
                    (string) $selectedFacilityId
            );
    @endphp



    {{-- =====================================================
         EDIT / RESUBMIT RESERVATION MODAL
    ====================================================== --}}
    <div
        data-reservation-edit-modal
        class="fixed inset-0 z-[90] hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="edit-reservation-title">

        <div
            data-reservation-edit-backdrop
            class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
        </div>


        <div class="relative flex min-h-full items-center justify-center p-3 sm:p-6">

            <div class="flex max-h-[calc(100vh-1.5rem)] w-full max-w-xl flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-2xl sm:max-h-[calc(100vh-3rem)]">

                {{-- Header --}}
                <div class="flex shrink-0 items-start justify-between gap-4 border-b border-border px-5 py-4 sm:px-6">

                    <div class="flex items-start gap-3">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-accent/10 text-primary">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 13H9v-2.828l6.586-6.586z" />

                            </svg>

                        </div>


                        <div>

                            <h2
                                id="edit-reservation-title"
                                class="font-heading text-lg font-semibold text-primary">

                                Edit Reservation Request

                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Update the request and submit it for approval again.
                            </p>

                        </div>

                    </div>


                    <button
                        type="button"
                        data-reservation-edit-close
                        aria-label="Close edit reservation"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary">

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />

                        </svg>

                    </button>

                </div>


                <form
                    data-reservation-edit-form
                    data-submit-loading
                    data-loading-text="Resubmitting..."
                    method="POST"
                    action=""
                    class="flex min-h-0 flex-1 flex-col">

                    @csrf
                    @method('PUT')

                    <input
                        type="hidden"
                        name="_reservation_modal_context"
                        value="edit">

                    <input
                        type="hidden"
                        name="_reservation_edit_id"
                        data-edit-reservation-id>

                    <input
                        type="hidden"
                        name="facility_id"
                        data-edit-facility-id>


                    <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6">

                        @if (
                            $errors->any()
                            &&
                            old('_reservation_modal_context') === 'edit'
                        )

                            <div class="mb-5 rounded-xl border border-error/20 bg-error/5 p-4">

                                <p class="text-sm font-semibold text-error">
                                    Please correct the following:
                                </p>

                                <ul class="mt-2 list-disc space-y-1 pl-5 text-xs text-error">

                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach

                                </ul>

                            </div>

                        @endif


                        {{-- Rejection reason --}}
                        <div
                            data-edit-rejection-box
                            class="mb-5 hidden rounded-xl border border-error/20 bg-error/5 p-4">

                            <p class="text-xs font-semibold uppercase tracking-wide text-error">
                                Previous rejection reason
                            </p>

                            <p
                                data-edit-rejection-note
                                class="mt-1.5 text-sm leading-relaxed text-slate-600">
                            </p>

                        </div>


                        {{-- Fixed facility --}}
                        <div class="mb-6 rounded-2xl border border-accent/20 bg-accent/5 p-4">

                            <div class="flex items-start justify-between gap-4">

                                <div>

                                    <p
                                        data-edit-facility-name
                                        class="font-heading text-base font-semibold text-primary">
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">

                                        <span data-edit-facility-type></span>

                                        <span
                                            data-edit-capacity-separator
                                            class="mx-1 text-slate-300">

                                            •

                                        </span>

                                        <span data-edit-facility-capacity></span>

                                    </p>

                                </div>


                                <span
                                    data-edit-facility-status
                                    class="rounded-full bg-success/10 px-2.5 py-1 text-[11px] font-semibold text-success">
                                </span>

                            </div>


                            <p class="mt-3 text-xs text-slate-400">
                                The facility cannot be changed while editing this request.
                            </p>

                        </div>


                        <div class="space-y-5">

                            {{-- Date --}}
                            <div>

                                <label
                                    for="edit_reservation_date"
                                    class="label">

                                    Reservation Date
                                    <span class="text-error">*</span>

                                </label>

                                <input
                                    id="edit_reservation_date"
                                    data-edit-date
                                    type="date"
                                    name="date"
                                    min="{{ now()->toDateString() }}"
                                    required
                                    class="input">

                            </div>


                            {{-- Times --}}
                            <div>

                                <label class="label">
                                    Reservation Time
                                    <span class="text-error">*</span>
                                </label>

                                <div class="grid gap-4 sm:grid-cols-2">

                                    <div>

                                        <label
                                            for="edit_reservation_start"
                                            class="mb-1.5 block text-xs font-medium text-slate-500">

                                            Start

                                        </label>

                                        <input
                                            id="edit_reservation_start"
                                            data-edit-start
                                            type="time"
                                            name="start_time"
                                            required
                                            class="input">

                                    </div>


                                    <div>

                                        <label
                                            for="edit_reservation_end"
                                            class="mb-1.5 block text-xs font-medium text-slate-500">

                                            End

                                        </label>

                                        <input
                                            id="edit_reservation_end"
                                            data-edit-end
                                            type="time"
                                            name="end_time"
                                            required
                                            class="input">

                                    </div>

                                </div>

                            </div>


                            {{-- Attendees / passengers --}}
                            <div>

                                <label
                                    for="edit_reservation_attendees"
                                    class="label">

                                    <span data-edit-attendees-label>
                                        Number of Attendees
                                    </span>

                                    <span class="text-error">*</span>

                                </label>

                                <input
                                    id="edit_reservation_attendees"
                                    data-edit-attendees
                                    type="number"
                                    name="attendees"
                                    min="1"
                                    required
                                    class="input">

                                <p
                                    data-edit-capacity-help
                                    class="mt-1.5 text-xs text-slate-400">
                                </p>

                            </div>


                            {{-- Purpose --}}
                            <div>

                                <label
                                    for="edit_reservation_purpose"
                                    class="label">

                                    Purpose
                                    <span class="text-error">*</span>

                                </label>

                                <textarea
                                    id="edit_reservation_purpose"
                                    data-edit-purpose
                                    name="purpose"
                                    rows="3"
                                    maxlength="2000"
                                    required
                                    placeholder="Describe the purpose of this reservation..."
                                    class="input"></textarea>

                            </div>

                        </div>

                    </div>


                    {{-- Only one primary footer action --}}
                    <div class="flex shrink-0 items-center justify-between gap-4 border-t border-border bg-background/50 px-5 py-4 sm:px-6">

                        <p class="hidden text-xs text-slate-400 sm:block">
                            The revised request will return to Pending approval.
                        </p>

                        <button
                            type="submit"
                            class="btn-primary ml-auto inline-flex items-center justify-center gap-2">

                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7" />

                            </svg>

                            Resubmit for Approval

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal =
            document.querySelector(
                '[data-reservation-edit-modal]'
            );

        if (!modal) {
            return;
        }

        const form =
            modal.querySelector(
                '[data-reservation-edit-form]'
            );

        const triggers =
            Array.from(
                document.querySelectorAll(
                    '[data-reservation-edit-open]'
                )
            );

        const closeButtons =
            modal.querySelectorAll(
                '[data-reservation-edit-close]'
            );

        const backdrop =
            modal.querySelector(
                '[data-reservation-edit-backdrop]'
            );

        const idInput =
            modal.querySelector(
                '[data-edit-reservation-id]'
            );

        const facilityIdInput =
            modal.querySelector(
                '[data-edit-facility-id]'
            );

        const facilityName =
            modal.querySelector(
                '[data-edit-facility-name]'
            );

        const facilityType =
            modal.querySelector(
                '[data-edit-facility-type]'
            );

        const facilityCapacity =
            modal.querySelector(
                '[data-edit-facility-capacity]'
            );

        const facilityStatus =
            modal.querySelector(
                '[data-edit-facility-status]'
            );

        const capacitySeparator =
            modal.querySelector(
                '[data-edit-capacity-separator]'
            );

        const capacityHelp =
            modal.querySelector(
                '[data-edit-capacity-help]'
            );

        const attendeesLabel =
            modal.querySelector(
                '[data-edit-attendees-label]'
            );

        const dateInput =
            modal.querySelector(
                '[data-edit-date]'
            );

        const startInput =
            modal.querySelector(
                '[data-edit-start]'
            );

        const endInput =
            modal.querySelector(
                '[data-edit-end]'
            );

        const attendeesInput =
            modal.querySelector(
                '[data-edit-attendees]'
            );

        const purposeInput =
            modal.querySelector(
                '[data-edit-purpose]'
            );

        const rejectionBox =
            modal.querySelector(
                '[data-edit-rejection-box]'
            );

        const rejectionNote =
            modal.querySelector(
                '[data-edit-rejection-note]'
            );

        let previouslyFocused = null;
        let previousBodyOverflow = '';


        const headline = (value) => {
            if (!value) {
                return 'Facility';
            }

            return value
                .replaceAll('_', ' ')
                .replace(
                    /\b\w/g,
                    (letter) => letter.toUpperCase()
                );
        };


        const populate = (
            trigger,
            oldValues = null
        ) => {
            const type =
                trigger.dataset.reservationFacilityType || '';

            const capacity =
                trigger.dataset.reservationCapacity || '';

            const isVehicle =
                type === 'vehicle';

            const value = (
                key,
                fallback
            ) => {
                if (
                    oldValues
                    &&
                    oldValues[key] !== null
                    &&
                    oldValues[key] !== undefined
                ) {
                    return oldValues[key];
                }

                return fallback;
            };


            form.action =
                trigger.dataset.reservationUpdateUrl;

            idInput.value =
                trigger.dataset.reservationId;

            facilityIdInput.value =
                trigger.dataset.reservationFacilityId;

            facilityName.textContent =
                trigger.dataset.reservationFacility;

            facilityType.textContent =
                headline(type);

            facilityStatus.textContent =
                headline(
                    trigger.dataset.reservationFacilityStatus
                    || 'unknown'
                );


            if (capacity !== '') {

                facilityCapacity.textContent =
                    `${capacity} ${
                        isVehicle
                            ? 'passengers'
                            : 'people'
                    }`;

                capacitySeparator.classList.remove(
                    'hidden'
                );

                capacityHelp.textContent =
                    `Maximum capacity: ${capacity} ${
                        isVehicle
                            ? 'passengers'
                            : 'people'
                    }.`;

                attendeesInput.max =
                    capacity;

            } else {

                facilityCapacity.textContent =
                    '';

                capacitySeparator.classList.add(
                    'hidden'
                );

                capacityHelp.textContent =
                    '';

                attendeesInput.removeAttribute(
                    'max'
                );
            }


            attendeesLabel.textContent =
                isVehicle
                    ? 'Number of Passengers'
                    : 'Number of Attendees';


            dateInput.value =
                value(
                    'date',
                    trigger.dataset.reservationDate
                );

            startInput.value =
                value(
                    'start_time',
                    trigger.dataset.reservationStart
                );

            endInput.value =
                value(
                    'end_time',
                    trigger.dataset.reservationEnd
                );

            attendeesInput.value =
                value(
                    'attendees',
                    trigger.dataset.reservationAttendees
                );

            purposeInput.value =
                value(
                    'purpose',
                    trigger.dataset.reservationPurpose
                );


            const note =
                trigger.dataset.reservationNote || '';

            if (note.trim() !== '') {

                rejectionNote.textContent =
                    note;

                rejectionBox.classList.remove(
                    'hidden'
                );

            } else {

                rejectionNote.textContent =
                    '';

                rejectionBox.classList.add(
                    'hidden'
                );
            }
        };


        const openModal = (
            trigger,
            oldValues = null
        ) => {
            populate(
                trigger,
                oldValues
            );

            previouslyFocused =
                document.activeElement;

            previousBodyOverflow =
                document.body.style.overflow;

            modal.classList.remove(
                'fams-modal-closing'
            );

            modal.classList.remove(
                'hidden'
            );

            document.body.style.overflow =
                'hidden';

            window.requestAnimationFrame(
                () => {
                    dateInput?.focus();
                }
            );
        };


        const closeModal = () => {

            if (
                modal.classList.contains(
                    'fams-modal-closing'
                )
            ) {
                return;
            }


            const finishClose = () => {

                modal.classList.add(
                    'hidden'
                );

                modal.classList.remove(
                    'fams-modal-closing'
                );

                document.body.style.overflow =
                    previousBodyOverflow;

                if (
                    previouslyFocused
                    &&
                    typeof previouslyFocused.focus ===
                        'function'
                ) {
                    previouslyFocused.focus();
                }
            };


            if (
                window.matchMedia(
                    '(prefers-reduced-motion: reduce)'
                ).matches
            ) {

                finishClose();
                return;
            }


            modal.classList.add(
                'fams-modal-closing'
            );

            window.setTimeout(
                finishClose,
                190
            );
        };


        triggers.forEach((trigger) => {
            trigger.addEventListener(
                'click',
                () => openModal(trigger)
            );
        });


        closeButtons.forEach((button) => {
            button.addEventListener(
                'click',
                closeModal
            );
        });


        backdrop?.addEventListener(
            'click',
            closeModal
        );


        document.addEventListener(
            'keydown',
            (event) => {
                if (
                    event.key === 'Escape'
                    &&
                    !modal.classList.contains(
                        'hidden'
                    )
                ) {
                    closeModal();
                }
            }
        );


        const oldEditValues = {{ \Illuminate\Support\Js::from([
            'context' => old('_reservation_modal_context'),
            'id' => old('_reservation_edit_id'),
            'date' => old('date'),
            'start_time' => old('start_time'),
            'end_time' => old('end_time'),
            'attendees' => old('attendees'),
            'purpose' => old('purpose'),
        ]) }};


        if (
            oldEditValues.context === 'edit'
            &&
            oldEditValues.id
        ) {

            const trigger =
                triggers.find(
                    (item) =>
                        String(
                            item.dataset.reservationId
                        ) ===
                        String(
                            oldEditValues.id
                        )
                );

            if (trigger) {
                openModal(
                    trigger,
                    oldEditValues
                );
            }
        }
    });
    </script>

    {{-- =====================================================
         RESERVE FACILITY MODAL
    ====================================================== --}}
    <div
        data-reservation-create-modal
        @if (
            ($errors->any() && old('_reservation_modal_context') === 'create')
            || $selectedFacility
        )
            data-open-reservation="true"
        @endif
        class="fixed inset-0 z-[80] hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="reserve-facility-title">

        {{-- Backdrop --}}
        <div
            data-reservation-create-backdrop
            class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
        </div>


        <div class="relative flex min-h-full items-center justify-center p-3 sm:p-6">

            <div class="flex max-h-[calc(100vh-1.5rem)] w-full max-w-xl flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-2xl sm:max-h-[calc(100vh-3rem)]">

                {{-- Header --}}
                <div class="flex shrink-0 items-start justify-between gap-4 border-b border-border px-5 py-4 sm:px-6">

                    <div class="flex min-w-0 items-start gap-3">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

                            </svg>

                        </div>


                        <div class="min-w-0">

                            <h2
                                id="reserve-facility-title"
                                class="font-heading text-lg font-semibold text-primary">

                                Reserve Facility

                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Complete the schedule and reservation details.
                            </p>

                        </div>

                    </div>


                    <button
                        type="button"
                        data-reservation-create-close
                        aria-label="Close reservation"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary">

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />

                        </svg>

                    </button>

                </div>


                <form
                    method="POST"
                    action="{{ route('reservations.store') }}"
                    data-submit-loading
                    data-loading-text="Submitting..."
                    class="flex min-h-0 flex-1 flex-col">

                    @csrf

                    <input
                        type="hidden"
                        name="_reservation_modal_context"
                        value="create">

                    <input
                        type="hidden"
                        name="facility_id"
                        value="{{ old('facility_id', request('reserve_facility')) }}">


                    <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6">

                        @if ($errors->any() && old('_reservation_modal_context') === 'create')

                            <div class="mb-5 rounded-xl border border-error/20 bg-error/5 p-4">

                                <div class="flex items-start gap-3">

                                    <div class="mt-0.5 text-error">

                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z" />

                                        </svg>

                                    </div>

                                    <div>

                                        <p class="text-sm font-semibold text-error">
                                            Please check the reservation details.
                                        </p>

                                        <ul class="mt-1.5 list-disc space-y-1 pl-4 text-xs text-error">

                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach

                                        </ul>

                                    </div>

                                </div>

                            </div>

                        @endif


                        @if ($selectedFacility)

                            {{-- Selected Facility --}}
                            <div class="mb-6 rounded-2xl border border-accent/20 bg-accent/5 p-4">

                                <div class="flex items-start gap-4">

                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-card text-primary shadow-sm ring-1 ring-border">

                                        @if ($selectedFacility->facility_type === 'vehicle')

                                            <svg
                                                class="h-6 w-6"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24">

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M3 13l2-5a2 2 0 011.9-1.4h10.2A2 2 0 0119 8l2 5M5 13h14v6H5v-6zm2 6v2m10-2v2M7 15h.01M17 15h.01" />

                                            </svg>

                                        @else

                                            <svg
                                                class="h-6 w-6"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24">

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5" />

                                            </svg>

                                        @endif

                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <div class="flex flex-wrap items-start justify-between gap-2">

                                            <div>

                                                <p class="font-heading text-base font-semibold text-primary">
                                                    {{ $selectedFacility->name }}
                                                </p>

                                                <p class="mt-0.5 text-xs text-slate-500">

                                                    {{ str($selectedFacility->facility_type)->headline() }}

                                                    @if ($selectedFacility->capacity !== null)

                                                        <span class="mx-1 text-slate-300">•</span>

                                                        {{ number_format($selectedFacility->capacity) }}

                                                        {{
                                                            $selectedFacility->facility_type === 'vehicle'
                                                                ? 'passengers'
                                                                : 'people'
                                                        }}

                                                    @endif

                                                </p>

                                            </div>


                                            <span class="badge badge-success">
                                                <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                                Available
                                            </span>

                                        </div>


                                        @if ($selectedFacility->location)

                                            <div class="mt-3 flex items-center gap-1.5 text-xs text-slate-500">

                                                <svg
                                                    class="h-3.5 w-3.5 shrink-0 text-accent"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24">

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z" />

                                                </svg>

                                                {{ $selectedFacility->location }}

                                            </div>

                                        @endif

                                    </div>

                                </div>

                            </div>


                            <div class="space-y-5">

                                {{-- Date --}}
                                <div>

                                    <label
                                        for="reservation_date"
                                        class="label">

                                        Reservation Date
                                        <span class="text-error">*</span>

                                    </label>

                                    <input
                                        id="reservation_date"
                                        type="date"
                                        name="date"
                                        min="{{ now()->toDateString() }}"
                                        value="{{ old('date') }}"
                                        required
                                        class="input @error('date') border-error focus:border-error focus:ring-error/20 @enderror">

                                </div>


                                {{-- Time --}}
                                <div>

                                    <label class="label">
                                        Reservation Time
                                        <span class="text-error">*</span>
                                    </label>

                                    <div class="grid gap-4 sm:grid-cols-2">

                                        <div>

                                            <label
                                                for="reservation_start_time"
                                                class="mb-1.5 block text-xs font-medium text-slate-500">

                                                Start

                                            </label>

                                            <input
                                                id="reservation_start_time"
                                                type="time"
                                                name="start_time"
                                                value="{{ old('start_time') }}"
                                                required
                                                class="input @error('start_time') border-error focus:border-error focus:ring-error/20 @enderror">

                                        </div>


                                        <div>

                                            <label
                                                for="reservation_end_time"
                                                class="mb-1.5 block text-xs font-medium text-slate-500">

                                                End

                                            </label>

                                            <input
                                                id="reservation_end_time"
                                                type="time"
                                                name="end_time"
                                                value="{{ old('end_time') }}"
                                                required
                                                class="input @error('end_time') border-error focus:border-error focus:ring-error/20 @enderror">

                                        </div>

                                    </div>

                                </div>


                                {{-- Attendees / Passengers --}}
                                <div>

                                    <label
                                        for="reservation_attendees"
                                        class="label">

                                        {{
                                            $selectedFacility->facility_type === 'vehicle'
                                                ? 'Number of Passengers'
                                                : 'Number of Attendees'
                                        }}

                                        <span class="text-error">*</span>

                                    </label>

                                    <input
                                        id="reservation_attendees"
                                        type="number"
                                        name="attendees"
                                        min="1"
                                        required
                                        value="{{ old('attendees') }}"
                                        placeholder="{{ $selectedFacility->facility_type === 'vehicle' ? 'e.g. 8 passengers' : 'e.g. 10 attendees' }}"
                                        class="input @error('attendees') border-error focus:border-error focus:ring-error/20 @enderror">

                                    @if ($selectedFacility->capacity !== null)

                                        <p class="mt-1.5 text-xs text-slate-400">
                                            Facility capacity:
                                            {{ number_format($selectedFacility->capacity) }}
                                            {{
                                                $selectedFacility->facility_type === 'vehicle'
                                                    ? 'passengers'
                                                    : 'people'
                                            }}.
                                        </p>

                                    @endif

                                </div>


                                {{-- Purpose --}}
                                <div>

                                    <label
                                        for="reservation_purpose"
                                        class="label">

                                        Purpose
                                        <span class="text-error">*</span>

                                    </label>

                                    <textarea
                                        id="reservation_purpose"
                                        name="purpose"
                                        rows="3"
                                        maxlength="2000"
                                        required
                                        placeholder="Briefly describe the purpose of this reservation..."
                                        class="input @error('purpose') border-error focus:border-error focus:ring-error/20 @enderror">{{ old('purpose') }}</textarea>

                                </div>

                            </div>


                        @else

                            <div class="rounded-2xl border border-warning/20 bg-warning/5 p-6 text-center">

                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-warning/10 text-warning">

                                    <svg
                                        class="h-6 w-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z" />

                                    </svg>

                                </div>

                                <h3 class="mt-3 font-heading text-sm font-semibold text-primary">
                                    Facility unavailable
                                </h3>

                                <p class="mt-1 text-xs text-slate-500">
                                    Return to Facilities and choose an available facility to reserve.
                                </p>

                            </div>

                        @endif

                    </div>


                    {{-- Simple footer --}}
                    @if ($selectedFacility)

                        <div class="flex shrink-0 items-center justify-between gap-4 border-t border-border bg-background/50 px-5 py-4 sm:px-6">

                            <p class="hidden text-xs text-slate-400 sm:block">
                                Reservation requests are submitted for approval.
                            </p>

                            <button
                                type="submit"
                                class="btn-primary ml-auto inline-flex items-center justify-center gap-2">

                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7" />

                                </svg>

                                Submit Reservation

                            </button>

                        </div>

                    @endif

                </form>

            </div>

        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal =
        document.querySelector(
            '[data-reservation-create-modal]'
        );

    if (!modal) {
        return;
    }

    const closeButtons =
        modal.querySelectorAll(
            '[data-reservation-create-close]'
        );

    const backdrop =
        modal.querySelector(
            '[data-reservation-create-backdrop]'
        );

    let previouslyFocused = null;
    let previousBodyOverflow = '';


    const openModal = () => {
        previouslyFocused =
            document.activeElement;

        previousBodyOverflow =
            document.body.style.overflow;

        modal.classList.remove(
            'fams-modal-closing'
        );

        modal.classList.remove('hidden');

        document.body.style.overflow =
            'hidden';

        window.requestAnimationFrame(() => {
            modal
                .querySelector(
                    '#reservation_date'
                )
                ?.focus();
        });
    };


    const closeModal = () => {

        if (
            modal.classList.contains(
                'fams-modal-closing'
            )
        ) {
            return;
        }


        const finishClose = () => {

            modal.classList.add('hidden');

            modal.classList.remove(
                'fams-modal-closing'
            );

            document.body.style.overflow =
                previousBodyOverflow;

            if (
                previouslyFocused
                && typeof previouslyFocused.focus === 'function'
            ) {
                previouslyFocused.focus();
            }
        };


        if (
            window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            ).matches
        ) {

            finishClose();
            return;
        }


        modal.classList.add(
            'fams-modal-closing'
        );

        window.setTimeout(
            finishClose,
            190
        );
    };


    closeButtons.forEach((button) => {
        button.addEventListener(
            'click',
            closeModal
        );
    });


    backdrop?.addEventListener(
        'click',
        closeModal
    );


    document.addEventListener(
        'keydown',
        (event) => {
            if (
                event.key === 'Escape'
                && ! modal.classList.contains('hidden')
            ) {
                closeModal();
            }
        }
    );


    if (
        modal.dataset.openReservation === 'true'
    ) {
        openModal();
    }
});
</script>

@endsection