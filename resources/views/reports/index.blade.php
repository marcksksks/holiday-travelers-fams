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

    $reportQuery = [
        'from' => $from->toDateString(),
        'to' => $to->toDateString(),
        'facility_sort' => $facilitySort,
    ];

    $averageVisitMinutes =
        max(
            0,
            (float) $summary['average_visit_minutes']
        );

    $averageVisitRounded =
        (int) round(
            $averageVisitMinutes
        );

    $averageVisitDays =
        intdiv(
            $averageVisitRounded,
            1440
        );

    $averageVisitHours =
        intdiv(
            $averageVisitRounded % 1440,
            60
        );

    $averageVisitRemainderMinutes =
        $averageVisitRounded % 60;

    if ($averageVisitDays > 0) {
        $averageVisitDisplay =
            $averageVisitDays.
            'd '.
            $averageVisitHours.
            'h '.
            $averageVisitRemainderMinutes.
            'm';
    } elseif ($averageVisitHours > 0) {
        $averageVisitDisplay =
            $averageVisitHours.
            'h '.
            $averageVisitRemainderMinutes.
            'm';
    } else {
        $averageVisitDisplay =
            $averageVisitRemainderMinutes.
            'm';
    }
@endphp

<div class="space-y-6">

    {{-- =====================================================
         REPORTS & ANALYTICS HEADER
    ====================================================== --}}
    <x-page-header
        eyebrow="Management Intelligence"
        title="Reports & Analytics"
        badge="Operational Intelligence"
        description="Monitor operational performance, facilities, visitors, records, compliance, legal matters, and contracts from one analytics workspace.">

        <x-slot:actions>

            <div class="rounded-xl border border-border bg-card px-4 py-2.5 shadow-card">

                <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                    Reporting Period
                </p>

                <p class="mt-0.5 whitespace-nowrap text-xs font-semibold text-primary">
                    {{ $from->format('M d, Y') }}
                    &ndash;
                    {{ $to->format('M d, Y') }}
                </p>

            </div>

        </x-slot:actions>

    </x-page-header>


    {{-- =====================================================
         EXPORT & PRINT
    ====================================================== --}}
    <section
        class="card overflow-hidden"
        aria-labelledby="report-export-title">

        <div class="flex flex-col gap-4 p-4 lg:flex-row lg:items-center lg:justify-between">

            <div class="min-w-0">

                <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                    Report Delivery
                </p>

                <h2
                    id="report-export-title"
                    class="mt-1 font-heading text-base font-semibold text-primary">

                    Export &amp; Print

                </h2>

                <p class="mt-1 max-w-2xl text-xs leading-5 text-slate-500">

                    Download the currently filtered management report as PDF,
                    Excel, or CSV, or open the print-optimized report.

                </p>

            </div>


            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 lg:flex lg:flex-wrap">

                <a
                    href="{{ route('reports.export.pdf', $reportQuery) }}"
                    class="btn-outline justify-center"
                    data-report-export
                    data-report-format="PDF"
                    aria-label="Download report as PDF">

                    <span
                        data-export-label
                        aria-live="polite">

                        Download PDF

                    </span>

                </a>


                <a
                    href="{{ route('reports.export.xlsx', $reportQuery) }}"
                    class="btn-outline justify-center"
                    data-report-export
                    data-report-format="Excel"
                    aria-label="Download report as Excel workbook">

                    <span
                        data-export-label
                        aria-live="polite">

                        Download Excel

                    </span>

                </a>


                <a
                    href="{{ route('reports.export.csv', $reportQuery) }}"
                    class="btn-outline justify-center"
                    data-report-export
                    data-report-format="CSV"
                    aria-label="Download report as CSV">

                    <span
                        data-export-label
                        aria-live="polite">

                        Download CSV

                    </span>

                </a>


                <a
                    href="{{ route('reports.print', $reportQuery) }}"
                    target="_blank"
                    rel="noopener"
                    class="btn-primary justify-center"
                    data-report-export
                    data-report-format="Print"
                    aria-label="Open printable report">

                    <span
                        data-export-label
                        aria-live="polite">

                        Print Report

                    </span>

                </a>

            </div>

        </div>


        <div class="border-t border-border bg-background/40 px-4 py-3">

            <p class="text-[11px] leading-5 text-slate-500">

                <span class="font-semibold text-primary">
                    Current export:
                </span>

                {{ $from->format('M d, Y') }}
                &ndash;
                {{ $to->format('M d, Y') }}

                <span aria-hidden="true">
                    &bull;
                </span>

                Facility order:
                {{ str($facilitySort)->replace('_', ' ')->headline() }}

            </p>

        </div>

    </section>


    {{-- =====================================================
         REPORTING PERIOD
    ====================================================== --}}
    <section class="space-y-4">

        <x-section-header
            eyebrow="Reporting Period"
            title="Report Window"
            description="Adjust the period used for operational activity metrics and facility-utilization analytics." />


        <div class="card overflow-hidden">

            <form
                method="GET"
                action="{{ route('reports.index') }}"
                class="grid gap-4 p-4 md:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_auto] xl:items-end">

                <div>

                    <label
                        for="from"
                        class="label">

                        From Date

                    </label>

                    <input
                        id="from"
                        type="date"
                        name="from"
                        value="{{ $from->toDateString() }}"
                        class="input">

                    @error('from')

                        <p class="mt-1.5 text-xs font-medium text-error">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                <div>

                    <label
                        for="to"
                        class="label">

                        To Date

                    </label>

                    <input
                        id="to"
                        type="date"
                        name="to"
                        value="{{ $to->toDateString() }}"
                        class="input">

                    @error('to')

                        <p class="mt-1.5 text-xs font-medium text-error">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                <div>

                    <label
                        for="facility_sort"
                        class="label">

                        Facility Sort

                    </label>

                    <select
                        id="facility_sort"
                        name="facility_sort"
                        class="input">

                        <option
                            value="bookings_desc"
                            @selected($facilitySort === 'bookings_desc')>

                            Most Booked First

                        </option>

                        <option
                            value="bookings_asc"
                            @selected($facilitySort === 'bookings_asc')>

                            Least Booked First

                        </option>

                        <option
                            value="name_asc"
                            @selected($facilitySort === 'name_asc')>

                            Facility Name A-Z

                        </option>

                        <option
                            value="name_desc"
                            @selected($facilitySort === 'name_desc')>

                            Facility Name Z-A

                        </option>

                    </select>

                    @error('facility_sort')

                        <p class="mt-1.5 text-xs font-medium text-error">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                <div class="flex gap-2 md:col-span-2 xl:col-span-1">

                    <button
                        type="submit"
                        class="btn-primary flex-1 justify-center">

                        Update Report

                    </button>


                    <a
                        href="{{ route('reports.index') }}"
                        class="btn-outline justify-center">

                        Reset

                    </a>

                </div>

            </form>


            <div class="flex flex-wrap items-center gap-2 border-t border-border bg-background/40 px-4 py-3">

                <span class="mr-1 text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                    Quick Range
                </span>


                <a
                    href="{{ route('reports.index', [
                        'from' => today()->subDays(6)->toDateString(),
                        'to' => today()->toDateString(),
                        'facility_sort' => $facilitySort,
                    ]) }}"
                    class="rounded-lg border border-border bg-card px-3 py-1.5 text-[11px] font-semibold text-slate-500 transition hover:border-primary/20 hover:text-primary">

                    Last 7 days

                </a>


                <a
                    href="{{ route('reports.index', [
                        'from' => today()->subDays(29)->toDateString(),
                        'to' => today()->toDateString(),
                        'facility_sort' => $facilitySort,
                    ]) }}"
                    class="rounded-lg border border-border bg-card px-3 py-1.5 text-[11px] font-semibold text-slate-500 transition hover:border-primary/20 hover:text-primary">

                    Last 30 days

                </a>


                <a
                    href="{{ route('reports.index', [
                        'from' => today()->startOfMonth()->toDateString(),
                        'to' => today()->toDateString(),
                        'facility_sort' => $facilitySort,
                    ]) }}"
                    class="rounded-lg border border-border bg-card px-3 py-1.5 text-[11px] font-semibold text-slate-500 transition hover:border-primary/20 hover:text-primary">

                    This month

                </a>

            </div>

        </div>

    </section>

    {{-- =====================================================
         EXECUTIVE SUMMARY
    ====================================================== --}}
    <section class="space-y-4">

        <x-section-header
            eyebrow="Performance"
            title="Executive Summary"
            description="Selected-period activity with current portfolio and operational-capacity indicators clearly identified." />


        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

            <x-metric-card
                label="Reservations"
                :value="number_format($summary['reservations'])"
                :helper="number_format($summary['approved_reservations']).' approved during the selected period.'"
                tone="primary">

                <x-slot:icon>

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

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Visitors"
                :value="number_format($summary['visitors'])"
                :helper="number_format($summary['walk_in_visitors']).' walk-in visitors during the period.'"
                tone="success">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Appointments"
                :value="number_format($summary['appointments'])"
                helper="Appointments scheduled inside the selected reporting period."
                tone="accent">

                <x-slot:icon>

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

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Documents Added"
                :value="number_format($summary['documents'])"
                helper="Document records created during the selected reporting period."
                tone="secondary">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 3h7l5 5v13H7V3zm7 0v5h5M10 13h6M10 17h6" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Reservation Attendees"
                :value="number_format($summary['reservation_attendees'])"
                helper="Total requested attendees across reservations in this period."
                tone="primary">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 20a5 5 0 00-10 0M12 11a4 4 0 100-8 4 4 0 000 8z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Available Facilities"
                :value="number_format($summary['available_facilities'])"
                :helper="'Current state: '.number_format($summary['available_facilities']).' of '.number_format($summary['total_facilities']).' facilities available.'"
                tone="success">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Average Visit Duration"
                :value="$averageVisitDisplay"
                :helper="number_format($summary['average_visit_minutes'], 1).' average minutes during the selected period.'"
                tone="accent">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Active Contracts"
                :value="number_format($summary['active_contracts'])"
                helper="Current active contract portfolio, independent of the selected reporting period."
                tone="secondary">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 7h8M8 11h8M8 15h5M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>

        </div>

    </section>

    {{-- =====================================================
         CURRENT COMPLIANCE WATCH
    ====================================================== --}}
    <section class="space-y-4">

        <x-section-header
            eyebrow="Current State"
            title="Compliance Watch"
            description="Current lifecycle and deadline conditions. These indicators are not limited by the selected reporting period." />


        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

            <x-metric-card
                label="Retention Issues"
                :value="number_format($summary['retention_issues'])"
                :href="route('retention.index', ['compliance' => 'at_risk'])"
                helper="Retention records currently at risk or non-compliant."
                tone="warning">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v4m0 4h.01M10.3 4.3L2.8 17.3A2 2 0 004.5 20h15a2 2 0 001.7-2.7L13.7 4.3a2 2 0 00-3.4 0z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Disposal Approvals"
                :value="number_format($summary['pending_disposals'])"
                :href="route('retention.index', ['tab' => 'disposal'])"
                helper="Controlled disposition requests currently awaiting approval."
                tone="warning">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 7h12M9 7V4h6v3m-7 0 1 13h6l1-13" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Legal Action Required"
                :value="number_format($summary['legal_action_required'])"
                :href="route('legal.index', ['review_status' => 'action_required'])"
                helper="Legal matters currently marked for follow-up."
                tone="error">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 3v18M5 7h14M7 7l-3 6h6L7 7zm10 0-3 6h6l-3-6z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Contracts Expiring"
                :value="number_format($summary['contracts_expiring_soon'])"
                :href="route('contracts.index', ['deadline' => 'due_soon'])"
                helper="Active contracts with recorded end dates within the next 30 days."
                tone="secondary">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>

        </div>


        <div class="rounded-xl border border-border bg-card px-4 py-3">

            <div class="flex flex-wrap items-center justify-between gap-2">

                <div>

                    <p class="text-xs font-semibold text-primary">
                        Legal Deadlines
                    </p>

                    <p class="mt-0.5 text-[11px] text-slate-500">
                        Legal records expiring within the next 30 days.
                    </p>

                </div>


                <span class="rounded-full bg-error/10 px-3 py-1 text-xs font-semibold text-error">
                    {{ number_format($summary['legal_expiring_soon']) }}
                </span>

            </div>

        </div>

    </section>

    {{-- =====================================================
         OPERATIONAL BREAKDOWNS
    ====================================================== --}}
    <x-section-header
        eyebrow="Analytics"
        title="Operational Breakdowns"
        description="Distribution views for operational activity and current governance portfolios." />


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


    <x-section-header
        eyebrow="Facilities"
        title="Facility Utilization"
        description="Approved facility use ranked across the selected reporting period." />


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


<script>
    document
        .querySelectorAll(
            '[data-report-export]'
        )
        .forEach((link) => {

            link.addEventListener(
                'click',
                () => {

                    const label =
                        link.querySelector(
                            '[data-export-label]'
                        );

                    if (! label) {
                        return;
                    }

                    const original =
                        label.textContent.trim();

                    const format =
                        link.dataset.reportFormat
                        || 'Report';

                    link.setAttribute(
                        'aria-busy',
                        'true'
                    );

                    link.classList.add(
                        'pointer-events-none',
                        'opacity-70'
                    );

                    label.textContent =
                        format === 'Print'
                            ? 'Opening...'
                            : 'Preparing...';

                    window.setTimeout(
                        () => {

                            label.textContent =
                                original;

                            link.removeAttribute(
                                'aria-busy'
                            );

                            link.classList.remove(
                                'pointer-events-none',
                                'opacity-70'
                            );

                        },
                        2500
                    );

                }
            );

        });
</script>

@endsection