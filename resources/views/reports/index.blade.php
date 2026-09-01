@extends('layouts.app')

@section('title', 'Reports')

@section('content')

@php
    $reservationMax = max(1, (int) ($reservationsByStatus->max() ?? 0));
    $visitorMax = max(1, (int) ($visitorsByType->max() ?? 0));
    $appointmentMax = max(1, (int) ($appointmentsByStatus->max() ?? 0));
    $contractMax = max(1, (int) ($contractsByStatus->max() ?? 0));
    $facilityMax = max(1, (int) ($facilityUtilization->max('bookings') ?? 0));
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Reports & Analytics
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Review operational activity, visitor traffic, reservations, appointments, contracts, and facility utilization.
            </p>
        </div>

        <div class="rounded-xl border border-border bg-card px-4 py-2.5 shadow-card">

            <p class="text-xs text-slate-500">
                Reporting Period
            </p>

            <p class="mt-0.5 text-sm font-semibold text-primary">
                {{ $from->format('M d, Y') }}
                -
                {{ $to->format('M d, Y') }}
            </p>

        </div>

    </div>


    {{-- Date Filter --}}
    <div class="card p-5">

        <form
            method="GET"
            action="{{ route('reports.index') }}"
            class="flex flex-col gap-4 sm:flex-row sm:items-end">

            <div class="flex-1">

                <label for="from" class="label">
                    From Date
                </label>

                <input
                    id="from"
                    type="date"
                    name="from"
                    value="{{ $from->toDateString() }}"
                    class="input">

            </div>


            <div class="flex-1">

                <label for="to" class="label">
                    To Date
                </label>

                <input
                    id="to"
                    type="date"
                    name="to"
                    value="{{ $to->toDateString() }}"
                    class="input">

                @error('to')
                    <p class="mt-1 text-xs font-medium text-error">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            <div class="flex gap-2">

                <button type="submit" class="btn-secondary">
                    Update Report
                </button>

                <a
                    href="{{ route('reports.index') }}"
                    class="btn-outline">
                    Reset
                </a>

            </div>

        </form>

    </div>


    {{-- Summary --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Reservations --}}
        <div class="card relative overflow-hidden p-5">

            <div class="absolute inset-x-0 bottom-0 h-1 bg-primary"></div>

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-xs font-medium text-slate-500">
                        Reservations
                    </p>

                    <p class="mt-2 font-heading text-3xl font-bold text-primary">
                        {{ $summary['reservations'] }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        {{ $summary['approved_reservations'] }} approved
                    </p>

                </div>


                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 text-primary">

                    <svg class="h-5 w-5"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 7V3m8 4V3M5 11h14M5 5h14v16H5V5z" />

                    </svg>

                </div>

            </div>

        </div>


        {{-- Visitors --}}
        <div class="card relative overflow-hidden p-5">

            <div class="absolute inset-x-0 bottom-0 h-1 bg-success"></div>

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-xs font-medium text-slate-500">
                        Visitors
                    </p>

                    <p class="mt-2 font-heading text-3xl font-bold text-success">
                        {{ $summary['visitors'] }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Logged during selected period
                    </p>

                </div>


                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-success/10 text-success">

                    <svg class="h-5 w-5"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm11 10v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />

                    </svg>

                </div>

            </div>

        </div>


        {{-- Appointments --}}
        <div class="card relative overflow-hidden p-5">

            <div class="absolute inset-x-0 bottom-0 h-1 bg-accent"></div>

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-xs font-medium text-slate-500">
                        Appointments
                    </p>

                    <p class="mt-2 font-heading text-3xl font-bold text-primary">
                        {{ $summary['appointments'] }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Scheduled during selected period
                    </p>

                </div>


                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-accent/10 text-accent">

                    <svg class="h-5 w-5"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 2M12 22a10 10 0 100-20 10 10 0 000 20z" />

                    </svg>

                </div>

            </div>

        </div>


        {{-- Active Contracts --}}
        <div class="card relative overflow-hidden p-5">

            <div class="absolute inset-x-0 bottom-0 h-1 bg-secondary"></div>

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-xs font-medium text-slate-500">
                        Active Contracts
                    </p>

                    <p class="mt-2 font-heading text-3xl font-bold text-secondary">
                        {{ $summary['active_contracts'] }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Current contract portfolio
                    </p>

                </div>


                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                    <svg class="h-5 w-5"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 7h8M8 11h8M8 15h5M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z" />

                    </svg>

                </div>

            </div>

        </div>

    </div>


    {{-- Operational Breakdowns --}}
    <div class="grid gap-6 xl:grid-cols-2">

        {{-- Reservations --}}
        <section class="card overflow-hidden">

            <div class="border-b border-border px-5 py-4">

                <h3 class="font-heading text-base font-semibold text-primary">
                    Reservations by Status
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Reservation activity during the selected reporting period.
                </p>

            </div>


            <div class="space-y-4 p-5">

                @forelse ($reservationsByStatus as $status => $count)

                    <div>

                        <div class="mb-1.5 flex items-center justify-between gap-4">

                            <span class="text-sm font-medium text-slate-600">
                                {{ str($status)->headline() }}
                            </span>

                            <span class="font-heading text-sm font-bold text-primary">
                                {{ $count }}
                            </span>

                        </div>


                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">

                            <div
                                class="h-full rounded-full bg-primary"
                                style="width: {{ ($count / $reservationMax) * 100 }}%">
                            </div>

                        </div>

                    </div>

                @empty

                    <div class="py-8 text-center text-sm text-slate-400">
                        No reservation data for this period.
                    </div>

                @endforelse

            </div>

        </section>


        {{-- Visitors --}}
        <section class="card overflow-hidden">

            <div class="border-b border-border px-5 py-4">

                <h3 class="font-heading text-base font-semibold text-primary">
                    Visitors by Type
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Visitor classifications recorded during the selected period.
                </p>

            </div>


            <div class="space-y-4 p-5">

                @forelse ($visitorsByType as $type => $count)

                    <div>

                        <div class="mb-1.5 flex items-center justify-between gap-4">

                            <span class="text-sm font-medium text-slate-600">
                                {{ str($type)->headline() }}
                            </span>

                            <span class="font-heading text-sm font-bold text-success">
                                {{ $count }}
                            </span>

                        </div>


                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">

                            <div
                                class="h-full rounded-full bg-success"
                                style="width: {{ ($count / $visitorMax) * 100 }}%">
                            </div>

                        </div>

                    </div>

                @empty

                    <div class="py-8 text-center text-sm text-slate-400">
                        No visitor data for this period.
                    </div>

                @endforelse

            </div>

        </section>


        {{-- Appointments --}}
        <section class="card overflow-hidden">

            <div class="border-b border-border px-5 py-4">

                <h3 class="font-heading text-base font-semibold text-primary">
                    Appointments by Status
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Appointment outcomes within the reporting period.
                </p>

            </div>


            <div class="space-y-4 p-5">

                @forelse ($appointmentsByStatus as $status => $count)

                    <div>

                        <div class="mb-1.5 flex items-center justify-between gap-4">

                            <span class="text-sm font-medium text-slate-600">
                                {{ str($status)->headline() }}
                            </span>

                            <span class="font-heading text-sm font-bold text-accent">
                                {{ $count }}
                            </span>

                        </div>


                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">

                            <div
                                class="h-full rounded-full bg-accent"
                                style="width: {{ ($count / $appointmentMax) * 100 }}%">
                            </div>

                        </div>

                    </div>

                @empty

                    <div class="py-8 text-center text-sm text-slate-400">
                        No appointment data for this period.
                    </div>

                @endforelse

            </div>

        </section>


        {{-- Contracts --}}
        <section class="card overflow-hidden">

            <div class="border-b border-border px-5 py-4">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <h3 class="font-heading text-base font-semibold text-primary">
                            Contract Portfolio
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Current contracts grouped by lifecycle status.
                        </p>

                    </div>

                    <span class="badge badge-warning">
                        Current
                    </span>

                </div>

            </div>


            <div class="space-y-4 p-5">

                @forelse ($contractsByStatus as $status => $count)

                    <div>

                        <div class="mb-1.5 flex items-center justify-between gap-4">

                            <span class="text-sm font-medium text-slate-600">
                                {{ str($status)->headline() }}
                            </span>

                            <span class="font-heading text-sm font-bold text-secondary">
                                {{ $count }}
                            </span>

                        </div>


                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">

                            <div
                                class="h-full rounded-full bg-secondary"
                                style="width: {{ ($count / $contractMax) * 100 }}%">
                            </div>

                        </div>

                    </div>

                @empty

                    <div class="py-8 text-center text-sm text-slate-400">
                        No contract records available.
                    </div>

                @endforelse

            </div>

        </section>

    </div>


    {{-- Facility Utilization --}}
    <section class="card overflow-hidden">

        <div class="flex flex-col gap-3 border-b border-border px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h3 class="font-heading text-base font-semibold text-primary">
                    Facility Utilization
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Approved reservations ranked by facility during the selected reporting period.
                </p>

            </div>


            <span class="badge badge-info">
                {{ $summary['facilities'] }}
                {{ Str::plural('Facility', $summary['facilities']) }}
            </span>

        </div>


        <div class="p-5">

            @forelse ($facilityUtilization as $row)

                <div class="border-b border-border py-4 first:pt-0 last:border-0 last:pb-0">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

                        <div class="flex min-w-0 flex-1 items-center gap-3">

                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">

                                <svg class="h-5 w-5"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-6h6v6M9 10h.01M15 10h.01" />

                                </svg>

                            </div>


                            <div class="min-w-0">

                                <p class="truncate text-sm font-semibold text-primary">
                                    {{ $row->facility->name ?? 'Unknown Facility' }}
                                </p>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    Approved bookings
                                </p>

                            </div>

                        </div>


                        <div class="sm:w-64">

                            <div class="mb-1.5 flex justify-between">

                                <span class="text-xs text-slate-400">
                                    Utilization
                                </span>

                                <span class="text-xs font-semibold text-primary">
                                    {{ $row->bookings }}
                                    {{ Str::plural('booking', $row->bookings) }}
                                </span>

                            </div>


                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">

                                <div
                                    class="h-full rounded-full bg-primary"
                                    style="width: {{ ($row->bookings / $facilityMax) * 100 }}%">
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            @empty

                <div class="py-12 text-center">

                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-accent/10 text-accent">

                        <svg class="h-7 w-7"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-6h6v6" />

                        </svg>

                    </div>

                    <h4 class="font-heading text-sm font-semibold text-primary">
                        No facility utilization data
                    </h4>

                    <p class="mt-1 text-sm text-slate-400">
                        No approved facility bookings were found for this period.
                    </p>

                </div>

            @endforelse

        </div>

    </section>


    {{-- Scope note --}}
    <div class="rounded-xl border border-accent/20 bg-accent/5 px-4 py-3">

        <p class="text-xs leading-5 text-slate-600">

            <span class="font-semibold text-primary">
                Report scope:
            </span>

            Reservations, visitors, appointments, and facility utilization follow the selected date range.
            Contract statistics represent the current contract portfolio and are not limited by the report dates.

        </p>

    </div>

</div>

@endsection