@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')

@php
    $reservationMax = max(1, (int) ($reservationsByStatus->max() ?? 0));
    $visitorMax = max(1, (int) ($visitorsByType->max() ?? 0));
    $visitorEntryMax = max(1, (int) ($visitorEntryType->max() ?? 0));
    $appointmentMax = max(1, (int) ($appointmentsByStatus->max() ?? 0));
    $documentStatusMax = max(1, (int) ($documentsByStatus->max() ?? 0));
    $documentCategoryMax = max(1, (int) ($documentsByCategory->max() ?? 0));
    $retentionMax = max(1, (int) ($retentionByCompliance->max() ?? 0));
    $legalMax = max(1, (int) ($legalByReviewStatus->max() ?? 0));
    $contractMax = max(1, (int) ($contractsByStatus->max() ?? 0));
    $contractLegalMax = max(1, (int) ($contractsByLegalReview->max() ?? 0));
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

            <p class="mt-1 max-w-3xl text-sm text-slate-500">
                Operational, compliance, legal, contract, document, visitor,
                appointment, reservation, and facility insights.
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

                @error('from')
                    <p class="mt-1 text-xs font-medium text-error">
                        {{ $message }}
                    </p>
                @enderror
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
                <button
                    type="submit"
                    class="btn-secondary">
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


    {{-- Executive Summary --}}
    <section>

        <div class="mb-3">
            <h3 class="font-heading text-base font-semibold text-primary">
                Executive Summary
            </h3>

            <p class="mt-1 text-xs text-slate-500">
                Key activity recorded during the selected reporting period.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

            <div class="card p-5">
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

            <div class="card p-5">
                <p class="text-xs font-medium text-slate-500">
                    Visitors
                </p>

                <p class="mt-2 font-heading text-3xl font-bold text-success">
                    {{ $summary['visitors'] }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    {{ $summary['walk_in_visitors'] }} walk-in
                </p>
            </div>

            <div class="card p-5">
                <p class="text-xs font-medium text-slate-500">
                    Appointments
                </p>

                <p class="mt-2 font-heading text-3xl font-bold text-primary">
                    {{ $summary['appointments'] }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    During selected period
                </p>
            </div>

            <div class="card p-5">
                <p class="text-xs font-medium text-slate-500">
                    Documents Added
                </p>

                <p class="mt-2 font-heading text-3xl font-bold text-secondary">
                    {{ $summary['documents'] }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Created during selected period
                </p>
            </div>

            <div class="card p-5">
                <p class="text-xs font-medium text-slate-500">
                    Reservation Attendees
                </p>

                <p class="mt-2 font-heading text-3xl font-bold text-primary">
                    {{ $summary['reservation_attendees'] }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Total requested attendees
                </p>
            </div>

            <div class="card p-5">
                <p class="text-xs font-medium text-slate-500">
                    Available Facilities
                </p>

                <p class="mt-2 font-heading text-3xl font-bold text-success">
                    {{ $summary['available_facilities'] }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Of {{ $summary['total_facilities'] }} total
                </p>
            </div>

            <div class="card p-5">
                <p class="text-xs font-medium text-slate-500">
                    Average Visit Duration
                </p>

                <p class="mt-2 font-heading text-3xl font-bold text-accent">
                    {{ number_format($summary['average_visit_minutes'], 1) }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Minutes
                </p>
            </div>

            <div class="card p-5">
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

        </div>

    </section>


    {{-- Current Compliance Snapshot --}}
    <section class="card overflow-hidden">

        <div class="border-b border-border px-5 py-4">
            <h3 class="font-heading text-base font-semibold text-primary">
                Current Compliance & Deadline Snapshot
            </h3>

            <p class="mt-1 text-xs text-slate-500">
                Current lifecycle conditions regardless of the selected reporting period.
            </p>
        </div>

        <div class="grid gap-px bg-border sm:grid-cols-2 xl:grid-cols-4">

            <div class="bg-card p-5">
                <p class="text-xs text-slate-500">
                    Retention Issues
                </p>

                <p class="mt-2 font-heading text-2xl font-bold text-warning">
                    {{ $summary['retention_issues'] }}
                </p>
            </div>

            <div class="bg-card p-5">
                <p class="text-xs text-slate-500">
                    Pending Disposal Approvals
                </p>

                <p class="mt-2 font-heading text-2xl font-bold text-warning">
                    {{ $summary['pending_disposals'] }}
                </p>
            </div>

            <div class="bg-card p-5">
                <p class="text-xs text-slate-500">
                    Legal Action Required
                </p>

                <p class="mt-2 font-heading text-2xl font-bold text-error">
                    {{ $summary['legal_action_required'] }}
                </p>
            </div>

            <div class="bg-card p-5">
                <p class="text-xs text-slate-500">
                    Contracts Expiring in 30 Days
                </p>

                <p class="mt-2 font-heading text-2xl font-bold text-secondary">
                    {{ $summary['contracts_expiring_soon'] }}
                </p>
            </div>

        </div>

        <div class="border-t border-border bg-card px-5 py-3">
            <p class="text-xs text-slate-500">
                Legal records expiring within 30 days:
                <span class="font-semibold text-primary">
                    {{ $summary['legal_expiring_soon'] }}
                </span>
            </p>
        </div>

    </section>


    {{-- Operational Breakdowns --}}
    <div class="grid gap-6 xl:grid-cols-2">

        @php
            $sections = [
                [
                    'title' => 'Reservations by Status',
                    'description' => 'Reservation outcomes during the selected period.',
                    'data' => $reservationsByStatus,
                    'max' => $reservationMax,
                    'bar' => 'bg-primary',
                    'text' => 'text-primary',
                ],
                [
                    'title' => 'Visitors by Type',
                    'description' => 'Visitor classifications during the selected period.',
                    'data' => $visitorsByType,
                    'max' => $visitorMax,
                    'bar' => 'bg-success',
                    'text' => 'text-success',
                ],
                [
                    'title' => 'Visitor Entry Type',
                    'description' => 'Walk-in visitors compared with scheduled visitors.',
                    'data' => $visitorEntryType,
                    'max' => $visitorEntryMax,
                    'bar' => 'bg-accent',
                    'text' => 'text-accent',
                ],
                [
                    'title' => 'Appointments by Status',
                    'description' => 'Appointment outcomes during the selected period.',
                    'data' => $appointmentsByStatus,
                    'max' => $appointmentMax,
                    'bar' => 'bg-accent',
                    'text' => 'text-accent',
                ],
                [
                    'title' => 'Documents by Status',
                    'description' => 'Documents created during the selected reporting period.',
                    'data' => $documentsByStatus,
                    'max' => $documentStatusMax,
                    'bar' => 'bg-primary',
                    'text' => 'text-primary',
                ],
                [
                    'title' => 'Documents by Category',
                    'description' => 'Document classifications created during the period.',
                    'data' => $documentsByCategory,
                    'max' => $documentCategoryMax,
                    'bar' => 'bg-secondary',
                    'text' => 'text-secondary',
                ],
                [
                    'title' => 'Retention Compliance',
                    'description' => 'Current records-retention compliance portfolio.',
                    'data' => $retentionByCompliance,
                    'max' => $retentionMax,
                    'bar' => 'bg-warning',
                    'text' => 'text-warning',
                ],
                [
                    'title' => 'Legal Review Status',
                    'description' => 'Current legal-management review state.',
                    'data' => $legalByReviewStatus,
                    'max' => $legalMax,
                    'bar' => 'bg-error',
                    'text' => 'text-error',
                ],
                [
                    'title' => 'Contracts by Status',
                    'description' => 'Current contract lifecycle portfolio.',
                    'data' => $contractsByStatus,
                    'max' => $contractMax,
                    'bar' => 'bg-secondary',
                    'text' => 'text-secondary',
                ],
                [
                    'title' => 'Contract Legal Review',
                    'description' => 'Current legal-review state of contracts.',
                    'data' => $contractsByLegalReview,
                    'max' => $contractLegalMax,
                    'bar' => 'bg-primary',
                    'text' => 'text-primary',
                ],
            ];
        @endphp

        @foreach ($sections as $section)

            <section class="card overflow-hidden">

                <div class="border-b border-border px-5 py-4">

                    <h3 class="font-heading text-base font-semibold text-primary">
                        {{ $section['title'] }}
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        {{ $section['description'] }}
                    </p>

                </div>

                <div class="space-y-4 p-5">

                    @forelse ($section['data'] as $label => $count)

                        <div>

                            <div class="mb-1.5 flex items-center justify-between gap-4">

                                <span class="text-sm font-medium text-slate-600">
                                    {{ str($label)->headline() }}
                                </span>

                                <span class="font-heading text-sm font-bold {{ $section['text'] }}">
                                    {{ $count }}
                                </span>

                            </div>

                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">

                                <div
                                    class="h-full rounded-full {{ $section['bar'] }}"
                                    style="width: {{ ($count / $section['max']) * 100 }}%">
                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="py-8 text-center text-sm text-slate-400">
                            No records available.
                        </div>

                    @endforelse

                </div>

            </section>

        @endforeach

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
                {{ $summary['available_facilities'] }}
                Available
            </span>

        </div>

        <div class="p-5">

            @forelse ($facilityUtilization as $row)

                <div class="border-b border-border py-4 first:pt-0 last:border-0 last:pb-0">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

                        <div class="min-w-0 flex-1">

                            <p class="truncate text-sm font-semibold text-primary">
                                {{ $row->facility->name ?? 'Unknown Facility' }}
                            </p>

                            <p class="mt-0.5 text-xs text-slate-400">
                                Approved bookings
                            </p>

                        </div>

                        <div class="sm:w-72">

                            <div class="mb-1.5 flex justify-between">

                                <span class="text-xs text-slate-400">
                                    Relative utilization
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


    {{-- Scope --}}
    <div class="rounded-xl border border-accent/20 bg-accent/5 px-4 py-3">

        <p class="text-xs leading-5 text-slate-600">

            <span class="font-semibold text-primary">
                Report scope:
            </span>

            Reservation, visitor, appointment, document, and facility-utilization
            statistics follow the selected date range. Contract, legal, and retention
            compliance indicators represent the current portfolio.

        </p>

    </div>

</div>

@endsection