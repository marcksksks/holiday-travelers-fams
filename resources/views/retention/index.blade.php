@extends('layouts.app')

@section('title', 'Records Retention & Compliance')

@section('content')

<div class="space-y-6">

    {{-- =====================================================
     RETENTION WORKSPACE HEADER
====================================================== --}}
<x-page-header
    eyebrow="Records Governance"
    title="Records Retention & Compliance"
    badge="Governance Workspace"
    description="Monitor retention periods, compliance conditions, review schedules, policies, and controlled record disposition." />


{{-- =====================================================
     RETENTION OVERVIEW
====================================================== --}}
<section class="space-y-4">

    <x-section-header
        eyebrow="Overview"
        title="Compliance Status"
        description="A current snapshot of records under retention control and areas requiring compliance attention." />


    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

        <x-metric-card
            label="Tracked Records"
            :value="number_format($stats['total'])"
            :href="route('retention.index', ['tab' => 'records'])"
            helper="Records currently operating under retention control."
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
                        d="M9 12l2 2 4-4m5-4a11 11 0 01-8 3 11 11 0 01-8-3c0 5.25 3.44 10.74 8 12 4.56-1.26 8-6.75 8-12z" />

                </svg>

            </x-slot:icon>

        </x-metric-card>


        <x-metric-card
            label="Compliant"
            :value="number_format($stats['compliant'])"
            :href="route('retention.index', [
                'tab' => 'records',
                'compliance' => 'compliant',
            ])"
            helper="Records currently meeting their retention requirements."
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
                        d="M5 13l4 4L19 7" />

                </svg>

            </x-slot:icon>

        </x-metric-card>


        <x-metric-card
            label="At Risk"
            :value="number_format($stats['at_risk'])"
            :href="route('retention.index', [
                'tab' => 'records',
                'compliance' => 'at_risk',
            ])"
            helper="Records currently requiring compliance attention."
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
            label="Review Required"
            :value="number_format($stats['review_required'])"
            :href="route('retention.index', [
                'tab' => 'records',
                'status' => 'review_required',
            ])"
            helper="Records currently awaiting a retention review decision."
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
                        d="M12 8v4m0 4h.01M4 6h16v14H4V6zm4-3v3m8-3v3" />

                </svg>

            </x-slot:icon>

        </x-metric-card>

    </div>

</section>

{{-- Retention Workspace Navigation --}}
    @php
        $retentionUser = request()->user();

        $retentionTabs = ['records'];

        if ($retentionUser && $retentionUser->can('approveRetentionDisposal')) {
            $retentionTabs[] = 'disposal';
        }

        if ($retentionUser && $retentionUser->can('manageRetention')) {
            $retentionTabs[] = 'policies';
        }

        $retentionTab = request('tab', 'records');

        if (! in_array($retentionTab, $retentionTabs, true)) {
            $retentionTab = 'records';
        }
    @endphp


    <nav
        class="flex gap-1 overflow-x-auto border-b border-border"
        aria-label="Retention workspace">

        <a
            href="{{ route('retention.index', ['tab' => 'records']) }}"
            @class([
                'group inline-flex shrink-0 items-center gap-2 border-b-2 px-4 py-3 text-sm font-semibold transition',
                'border-primary text-primary' => $retentionTab === 'records',
                'border-transparent text-slate-500 hover:border-slate-300 hover:text-primary' => $retentionTab !== 'records',
            ])
            @if ($retentionTab === 'records')
                aria-current="page"
            @endif>

            <svg
                class="h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />

            </svg>

            Records

            <span @class([
                'rounded-full px-2 py-0.5 text-[10px] font-semibold',
                'bg-primary/10 text-primary' => $retentionTab === 'records',
                'bg-slate-100 text-slate-500' => $retentionTab !== 'records',
            ])>
                {{ number_format($stats['total']) }}
            </span>

        </a>


        @can('approveRetentionDisposal')

            <a
                href="{{ route('retention.index', ['tab' => 'disposal']) }}"
                @class([
                    'group inline-flex shrink-0 items-center gap-2 border-b-2 px-4 py-3 text-sm font-semibold transition',
                    'border-warning text-amber-700' => $retentionTab === 'disposal',
                    'border-transparent text-slate-500 hover:border-slate-300 hover:text-primary' => $retentionTab !== 'disposal',
                ])
                @if ($retentionTab === 'disposal')
                    aria-current="page"
                @endif>

                <svg
                    class="h-4 w-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12h6m-3-3v6m8-3a8 8 0 11-16 0 8 8 0 0116 0z" />

                </svg>

                Disposal Queue

                @if ($pendingDisposals->isNotEmpty())

                    <span class="rounded-full bg-warning/10 px-2 py-0.5 text-[10px] font-semibold text-amber-700">
                        {{ $pendingDisposals->count() }}
                    </span>

                @endif

            </a>

        @endcan


        @can('manageRetention')

            <a
                href="{{ route('retention.index', ['tab' => 'policies']) }}"
                @class([
                    'group inline-flex shrink-0 items-center gap-2 border-b-2 px-4 py-3 text-sm font-semibold transition',
                    'border-primary text-primary' => $retentionTab === 'policies',
                    'border-transparent text-slate-500 hover:border-slate-300 hover:text-primary' => $retentionTab !== 'policies',
                ])
                @if ($retentionTab === 'policies')
                    aria-current="page"
                @endif>

                <svg
                    class="h-4 w-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12h6M9 16h6M9 8h6M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z" />

                </svg>

                Retention Policies

                <span @class([
                    'rounded-full px-2 py-0.5 text-[10px] font-semibold',
                    'bg-primary/10 text-primary' => $retentionTab === 'policies',
                    'bg-slate-100 text-slate-500' => $retentionTab !== 'policies',
                ])>
                    {{ $allPolicies->count() }}
                </span>

            </a>

        @endcan

    </nav>

    <div class="space-y-6">

        {{-- Track Record --}}
        @can('manageRetention')

            <dialog
                id="trackRetentionDialog"
                class="relative max-h-[90vh] w-[calc(100%_-_2rem)] max-w-2xl overflow-hidden rounded-2xl border border-border bg-card p-0 shadow-2xl backdrop:bg-slate-950/50">

                <button
                    type="button"
                    onclick="this.closest('dialog').close()"
                    class="absolute right-4 top-4 z-20 flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary"
                    aria-label="Close Track Record dialog">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />

                    </svg>

                </button>

                <div class="flex max-h-[90vh] flex-col overflow-hidden bg-card">

                    <div class="shrink-0 border-b border-border bg-background/60 px-5 py-5 pr-16">

                        <div class="flex items-center gap-3">

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12h14V7a2 2 0 00-2-2h-2M9 5a3 3 0 016 0M9 5h6M9 11h6M9 15h4" />

                                </svg>

                            </div>

                            <div>
                                <h3 class="font-heading text-base font-semibold text-primary">
                                    Track a Record
                                </h3>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Register a record for retention monitoring.
                                </p>
                            </div>

                        </div>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('retention.store') }}"
                        class="min-h-0 flex-1 space-y-5 overflow-y-auto p-5">

                        @csrf


                        <div>
                            <label for="record_title" class="label">
                                Record Title
                                <span class="text-error">*</span>
                            </label>

                            <input
                                id="record_title"
                                type="text"
                                name="record_title"
                                value="{{ old('record_title') }}"
                                required
                                placeholder="e.g. Partnership Agreement 2026"
                                class="input">

                            @error('record_title')
                                <p class="mt-1 text-xs font-medium text-error">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        <div class="grid gap-4 sm:grid-cols-2">

                            <div>
                                <label for="record_type" class="label">
                                    Record Type
                                </label>

                                <select id="record_type"
                                        name="record_type"
                                        class="input">

                                    @foreach ([
                                        'document',
                                        'contract',
                                        'legal_record',
                                        'other'
                                    ] as $type)

                                        <option
                                            value="{{ $type }}"
                                            @selected(old('record_type', 'document') === $type)>

                                            {{ str($type)->headline() }}

                                        </option>

                                    @endforeach

                                </select>
                            </div>


                            <div>
                                <label for="record_id" class="label">
                                    Related ID
                                </label>

                                <input
                                    id="record_id"
                                    type="number"
                                    min="1"
                                    name="record_id"
                                    value="{{ old('record_id') }}"
                                    placeholder="Optional"
                                    class="input">
                            </div>

                        </div>


                        <div>
                            <label for="policy_id" class="label">
                                Retention Policy
                            </label>

                            <select
                                id="policy_id"
                                name="policy_id"
                                class="input">

                                <option value="">
                                    No policy assigned selected
                                </option>

                                @foreach ($policies as $policy)

                                    <option
                                        value="{{ $policy->id }}"
                                        @selected((string) old('policy_id') === (string) $policy->id)>

                                        {{ $policy->name }}
                                        ({{ $policy->retention_years }} {{ Str::plural('year', $policy->retention_years) }})

                                    </option>

                                @endforeach

                            </select>
                        </div>


                        <div class="grid gap-4 sm:grid-cols-2">

                            <div>
                                <label for="start_date" class="label">
                                    Retention Start
                                </label>

                                <input
                                    id="start_date"
                                    type="date"
                                    name="start_date"
                                    value="{{ old('start_date') }}"
                                    class="input">
                            </div>


                            <div>
                                <label for="review_date" class="label">
                                    Review Date
                                </label>

                                <input
                                    id="review_date"
                                    type="date"
                                    name="review_date"
                                    value="{{ old('review_date') }}"
                                    class="input">
                            </div>

                        </div>


                        <div class="grid gap-4 sm:grid-cols-2">

                            <div>
                                <label for="status" class="label">
                                    Lifecycle
                                </label>

                                <select
                                    id="status"
                                    name="status"
                                    class="input">

                                    @foreach ([
                                        'retained',
                                        'review_required',
                                        'extended',
                                        'archived',
                                        'marked_for_disposal'
                                    ] as $status)

                                        <option
                                            value="{{ $status }}"
                                            @selected(old('status', 'retained') === $status)>

                                            {{ str($status)->headline() }}

                                        </option>

                                    @endforeach

                                </select>
                            </div>


                            <div>
                                <label for="compliance_status" class="label">
                                    Compliance
                                </label>

                                <select
                                    id="compliance_status"
                                    name="compliance_status"
                                    class="input">

                                    @foreach ([
                                        'compliant',
                                        'at_risk',
                                        'non_compliant'
                                    ] as $status)

                                        <option
                                            value="{{ $status }}"
                                            @selected(old('compliance_status', 'compliant') === $status)>

                                            {{ str($status)->headline() }}

                                        </option>

                                    @endforeach

                                </select>
                            </div>

                        </div>


                        <div>
                            <label for="notes" class="label">
                                Notes
                            </label>

                            <textarea
                                id="notes"
                                name="notes"
                                rows="3"
                                placeholder="Retention notes, review requirements, or compliance observations..."
                                class="input">{{ old('notes') }}</textarea>
                        </div>


                        <button type="submit" class="btn-secondary w-full">

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

                            Track Record

                        </button>

                    </form>

                </div>

            </dialog>

        @endcan


        {{-- Disposal Queue --}}
        @include('retention._disposal-queue')


        {{-- Register --}}
        @if ($retentionTab === 'records')
        <div class="min-w-0 space-y-4">

            <div class="space-y-4">

                {{-- Workspace heading --}}
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

                    <div>


                        <div class="flex flex-wrap items-center gap-2">

                            <h3 class="font-heading text-lg font-semibold text-primary">
                                Retention Register
                            </h3>

                            <span class="rounded-full bg-primary/5 px-2.5 py-1 text-[10px] font-semibold text-primary ring-1 ring-inset ring-primary/10">
                                {{ number_format($retentions->total()) }}
                                {{ Str::plural('record', $retentions->total()) }}
                            </span>

                        </div>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Monitor lifecycle status, review schedules, policies, and compliance health.
                        </p>

                    </div>


                    @can('manageRetention')

                        <button
                            type="button"
                            onclick="document.getElementById('trackRetentionDialog').showModal()"
                            class="btn-secondary shrink-0 justify-center">

                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4" />

                            </svg>

                            Track Retention Record

                        </button>

                    @endcan

                </div>


                {{-- Search and filters --}}
                @include('retention._records-toolbar')

            </div>

            @include('retention._mobile-records')

            <div class="table-shell hidden md:block">

                <div class="overflow-x-auto">

                    <table class="w-full min-w-[900px] text-left text-sm">

                        <thead class="table-header border-b border-border">

                            <tr>
                                <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">
                                    Record
                                </th>

                                <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">
                                    Policy
                                </th>

                                <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">
                                    Review
                                </th>

                                <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">
                                    Lifecycle
                                </th>

                                <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">
                                    Compliance
                                </th>

                                <th class="px-4 py-3 text-right text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">
                                    Action
                                </th>
                            </tr>

                        </thead>


                        <tbody class="divide-y divide-border bg-card">

                            @forelse ($retentions as $retention)

                                @php
                                    $daysToReview = $retention->review_date
                                        ? (int) now()->startOfDay()->diffInDays(
                                            $retention->review_date->copy()->startOfDay(),
                                            false
                                        )
                                        : null;
                                @endphp


                                <tr @class([
                                    'group transition-colors hover:bg-sky-50/60',
                                    'bg-error/[0.025]' =>
                                        $daysToReview !== null
                                        && $daysToReview < 0,
                                    'bg-warning/[0.025]' =>
                                        $daysToReview !== null
                                        && $daysToReview >= 0
                                        && $daysToReview <= 30,
                                ])>

                                    {{-- Record --}}
                                    <td class="px-4 py-3.5">

                                        <div class="flex items-start gap-3">

                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">

                                                <svg
                                                    class="h-5 w-5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24">

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M7 3h7l5 5v13H7V3zm7 0v5h5" />

                                                </svg>

                                            </div>

                                            <div class="min-w-0">

                                                <p class="max-w-[240px] truncate font-heading text-sm font-semibold text-primary">
                                                    {{ $retention->record_title }}
                                                </p>

                                                <div class="mt-1 flex flex-wrap gap-2">

                                                    <span class="rounded-full bg-primary/5 px-2 py-0.5 text-[10px] font-medium text-primary">
                                                        {{ str($retention->record_type)->headline() }}
                                                    </span>

                                                    @if ($retention->record_id)

                                                        <span class="text-[10px] text-slate-400">
                                                            ID #{{ $retention->record_id }}
                                                        </span>

                                                    @endif

                                                </div>

                                                @if ($retention->last_action_by)

                                                    <p class="mt-1 text-[10px] text-slate-400">
                                                        Updated by {{ $retention->last_action_by }}
                                                    </p>

                                                @endif

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Policy --}}
                                    <td class="px-4 py-3.5">

                                        @if ($retention->policy)

                                            <p class="text-sm font-medium text-slate-700">
                                                {{ $retention->policy->name }}
                                            </p>

                                            <p class="mt-1 text-[10px] text-slate-400">
                                                {{ $retention->policy->retention_years }}
                                                {{ Str::plural('year', $retention->policy->retention_years) }}
                                            </p>

                                        @elseif ($retention->policy_name)

                                            <p class="text-sm text-slate-600">
                                                {{ $retention->policy_name }}
                                            </p>

                                        @else

                                            <span class="text-xs text-slate-400">
                                                No policy assigned
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Review --}}
                                    <td class="px-4 py-3.5">

                                        @if ($retention->review_date)

                                            <p @class([
                                                'text-sm font-medium',
                                                'text-error' => $daysToReview !== null && $daysToReview < 0,
                                                'text-amber-600' => $daysToReview !== null && $daysToReview >= 0 && $daysToReview <= 30,
                                                'text-slate-700' => $daysToReview === null || $daysToReview > 30,
                                            ])>

                                                {{ $retention->review_date->format('M d, Y') }}

                                            </p>

                                            @if ($daysToReview < 0)

                                                <p class="mt-1.5 inline-flex rounded-full bg-error/10 px-2 py-0.5 text-[10px] font-semibold text-error">
                                                    Overdue by {{ abs($daysToReview) }} days
                                                </p>

                                            @elseif ($daysToReview === 0)

                                                <p class="mt-1.5 inline-flex rounded-full bg-error/10 px-2 py-0.5 text-[10px] font-semibold text-error">
                                                    Review due today
                                                </p>

                                            @elseif ($daysToReview <= 30)

                                                <p class="mt-1.5 inline-flex rounded-full bg-warning/10 px-2 py-0.5 text-[10px] font-semibold text-amber-600">
                                                    Due in {{ $daysToReview }} days
                                                </p>

                                            @endif

                                        @else

                                            <span class="text-xs text-slate-400">
                                                No review date
                                            </span>

                                        @endif

                                    </td>
                                    {{-- Status --}}
                                    <td class="px-4 py-3.5">

                                        <span @class([
                                            'badge',

                                            'badge-success' =>
                                                $retention->status === 'retained',

                                            'badge-warning' =>
                                                $retention->status === 'review_required',

                                            'badge-info' =>
                                                $retention->status === 'extended',

                                            'bg-slate-100 text-slate-600' =>
                                                $retention->status === 'archived',

                                            'badge-error' =>
                                                $retention->status === 'marked_for_disposal',
                                        ])>

                                            {{ str($retention->status)->headline() }}

                                        </span>

                                    </td>
                                    {{-- Compliance --}}
                                    <td class="px-4 py-3.5">

                                        <span @class([
                                            'badge',

                                            'badge-success' =>
                                                $retention->compliance_status === 'compliant',

                                            'badge-warning' =>
                                                $retention->compliance_status === 'at_risk',

                                            'badge-error' =>
                                                $retention->compliance_status === 'non_compliant',
                                        ])>

                                            {{ str($retention->compliance_status)->headline() }}

                                        </span>

                                    </td>


                                    {{-- Action --}}
                                    <td class="px-4 py-3.5 text-right">

                                        @can('manageRetention')

                                            <a
                                                href="{{ route('retention.review', $retention) }}"
                                                class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-border bg-card px-3 py-2 text-xs font-semibold text-primary transition hover:border-primary/20 hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">

                                                @if ($retention->status === 'review_required')

                                                    Review Record

                                                @else

                                                    Manage

                                                @endif

                                                <svg
                                                    class="h-3.5 w-3.5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                    aria-hidden="true">

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5l7 7-7 7" />

                                                </svg>

                                            </a>

                                        @else

                                            <span class="text-xs text-slate-400">
                                                —
                                            </span>

                                        @endcan

                                    </td>
</tr>

                            @empty

                                <tr>

                                    <td colspan="6" class="px-6 py-16">

                                        <div class="mx-auto flex max-w-md flex-col items-center text-center">

                                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/5 text-primary ring-1 ring-inset ring-primary/10">

                                                <svg
                                                    class="h-6 w-6"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                    aria-hidden="true">

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M7 3h7l5 5v13H7V3zm7 0v5h5" />

                                                </svg>

                                            </div>


                                            @if (
                                                request()->filled('q')
                                                || request()->filled('record_type')
                                                || request()->filled('status')
                                                || request()->filled('compliance')
                                                || request()->filled('attention')
                                            )

                                                <h3 class="mt-4 font-heading text-base font-semibold text-primary">
                                                    No matching retention records
                                                </h3>

                                                <p class="mt-1.5 text-sm leading-6 text-slate-500">
                                                    @if (request()->filled('q'))
                                                        No retention records match “{{ request('q') }}” with the selected filters.
                                                    @else
                                                        No records match the current quick view or selected retention filters.
                                                    @endif
                                                </p>

                                                <a
                                                    href="{{ route('retention.index', ['tab' => 'records']) }}"
                                                    class="btn-outline mt-4 justify-center">
                                                    Clear Filters
                                                </a>

                                            @else

                                                <h3 class="mt-4 font-heading text-base font-semibold text-primary">
                                                    No retention records yet
                                                </h3>

                                                <p class="mt-1.5 text-sm leading-6 text-slate-500">
                                                    Records placed under retention monitoring will appear in this register.
                                                </p>

                                            @endif

                                        </div>

                                    </td>

                                </tr>
@endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            @if ($retentions->total() > 0)

                <div class="rounded-xl border border-border bg-card px-4 py-4 shadow-card">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-2 text-xs text-slate-500">

                            <span class="h-2 w-2 rounded-full bg-accent"></span>

                            <span>
                                Showing
                                <span class="font-semibold text-primary">
                                    {{ $retentions->firstItem() ?? 0 }}
                                </span>
                                –
                                <span class="font-semibold text-primary">
                                    {{ $retentions->lastItem() ?? 0 }}
                                </span>
                                of
                                <span class="font-semibold text-primary">
                                    {{ number_format($retentions->total()) }}
                                </span>
                                retention records
                            </span>

                        </div>


                        @if ($retentions->hasPages())

                            <div class="shrink-0">
                                {{ $retentions->withQueryString()->links() }}
                            </div>

                        @endif

                    </div>

                </div>

            @endif

        </div>

        @endif

    </div>


    {{-- Retention Policies --}}
    @include('retention._policies')

</div>



@if ($errors->hasAny([
    'record_title',
    'record_type',
    'record_id',
    'policy_id',
    'start_date',
    'review_date',
    'status',
    'compliance_status',
    'notes'
]))
    <script data-retention-create-validation>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('trackRetentionDialog')?.showModal();
        });
    </script>
@endif

@endsection