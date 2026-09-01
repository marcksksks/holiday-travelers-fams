@extends('layouts.app')

@section('title', 'Records Retention & Compliance')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Records Retention & Compliance
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Monitor retention periods, compliance conditions, review schedules, and record disposition.
            </p>
        </div>

        <div class="rounded-xl border border-border bg-card px-4 py-2.5 shadow-card">
            <p class="text-xs text-slate-500">
                Active Policies
            </p>

            <p class="mt-0.5 font-heading text-lg font-bold text-primary">
                {{ $policies->count() }}
            </p>
        </div>

    </div>


    {{-- Summary Cards --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="card relative overflow-hidden p-5">
            <div class="absolute inset-x-0 bottom-0 h-1 bg-primary"></div>

            <p class="text-xs font-medium text-slate-500">
                Tracked Records
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-primary">
                {{ $stats['total'] }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Records under retention tracking
            </p>
        </div>


        <div class="card relative overflow-hidden p-5">
            <div class="absolute inset-x-0 bottom-0 h-1 bg-success"></div>

            <p class="text-xs font-medium text-slate-500">
                Compliant
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-success">
                {{ $stats['compliant'] }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Records currently compliant
            </p>
        </div>


        <div class="card relative overflow-hidden p-5">
            <div class="absolute inset-x-0 bottom-0 h-1 bg-warning"></div>

            <p class="text-xs font-medium text-slate-500">
                At Risk
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-amber-600">
                {{ $stats['at_risk'] }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Compliance attention needed
            </p>
        </div>


        <div class="card relative overflow-hidden p-5">
            <div class="absolute inset-x-0 bottom-0 h-1 bg-error"></div>

            <p class="text-xs font-medium text-slate-500">
                Review Required
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-error">
                {{ $stats['review_required'] }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Records awaiting review
            </p>
        </div>

    </div>


    <div class="grid gap-6 xl:grid-cols-[400px_minmax(0,1fr)]">

        {{-- Track Record --}}
        @can('manageRetention')

            <div>

                <div class="card overflow-hidden xl:sticky xl:top-6">

                    <div class="border-b border-border bg-background/60 px-5 py-5">

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
                        class="space-y-5 p-5">

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
                                    No policy selected
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
                                    Retention Status
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

            </div>

        @endcan


        {{-- Register --}}
        <div class="min-w-0 space-y-4">

            <div class="flex flex-col gap-4">

                <div>
                    <h3 class="font-heading text-lg font-semibold text-primary">
                        Retention Register
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Monitor review schedules, disposition status, and compliance.
                    </p>
                </div>


                {{-- Filters --}}
                <form
                    method="GET"
                    action="{{ route('retention.index') }}"
                    class="grid gap-2 sm:grid-cols-3">

                    <select
                        name="record_type"
                        onchange="this.form.submit()"
                        class="input">

                        <option value="">
                            All Record Types
                        </option>

                        @foreach ([
                            'document',
                            'contract',
                            'legal_record',
                            'other'
                        ] as $type)

                            <option
                                value="{{ $type }}"
                                @selected(request('record_type') === $type)>

                                {{ str($type)->headline() }}

                            </option>

                        @endforeach

                    </select>


                    <select
                        name="status"
                        onchange="this.form.submit()"
                        class="input">

                        <option value="">
                            All Retention Statuses
                        </option>

                        @foreach ([
                            'retained',
                            'review_required',
                            'extended',
                            'archived',
                            'marked_for_disposal'
                        ] as $status)

                            <option
                                value="{{ $status }}"
                                @selected(request('status') === $status)>

                                {{ str($status)->headline() }}

                            </option>

                        @endforeach

                    </select>


                    <select
                        name="compliance"
                        onchange="this.form.submit()"
                        class="input">

                        <option value="">
                            All Compliance
                        </option>

                        @foreach ([
                            'compliant',
                            'at_risk',
                            'non_compliant'
                        ] as $status)

                            <option
                                value="{{ $status }}"
                                @selected(request('compliance') === $status)>

                                {{ str($status)->headline() }}

                            </option>

                        @endforeach

                    </select>

                </form>

            </div>


            <div class="table-shell">

                <div class="overflow-x-auto">

                    <table class="min-w-full text-left text-sm">

                        <thead class="table-header">

                            <tr>
                                <th class="px-5 py-4 font-medium">
                                    Record
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Policy
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Review
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Retention Status
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Compliance
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


                                <tr class="transition hover:bg-sky-50/40">

                                    {{-- Record --}}
                                    <td class="px-5 py-4">

                                        <div class="flex items-start gap-3">

                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">

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

                                                <p class="max-w-[220px] truncate font-button text-sm font-semibold text-primary">
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
                                    <td class="px-5 py-4">

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
                                                No policy
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Review --}}
                                    <td class="px-5 py-4">

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

                                                <p class="mt-1 text-[10px] font-medium text-error">
                                                    Overdue by {{ abs($daysToReview) }} days
                                                </p>

                                            @elseif ($daysToReview === 0)

                                                <p class="mt-1 text-[10px] font-medium text-error">
                                                    Review due today
                                                </p>

                                            @elseif ($daysToReview <= 30)

                                                <p class="mt-1 text-[10px] font-medium text-amber-600">
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
                                    <td class="px-5 py-4">

                                        @can('manageRetention')

                                            <form
                                                method="POST"
                                                action="{{ route('retention.update', $retention) }}"
                                                class="min-w-[170px]">

                                                @csrf
                                                @method('PUT')

                                                <input type="hidden"
                                                       name="record_title"
                                                       value="{{ $retention->record_title }}">

                                                <input type="hidden"
                                                       name="record_type"
                                                       value="{{ $retention->record_type }}">

                                                <input type="hidden"
                                                       name="record_id"
                                                       value="{{ $retention->record_id }}">

                                                <input type="hidden"
                                                       name="policy_id"
                                                       value="{{ $retention->policy_id }}">

                                                <input type="hidden"
                                                       name="start_date"
                                                       value="{{ $retention->start_date?->toDateString() }}">

                                                <input type="hidden"
                                                       name="review_date"
                                                       value="{{ $retention->review_date?->toDateString() }}">

                                                <input type="hidden"
                                                       name="compliance_status"
                                                       value="{{ $retention->compliance_status }}">

                                                <input type="hidden"
                                                       name="notes"
                                                       value="{{ $retention->notes }}">

                                                <select
                                                    name="status"
                                                    onchange="this.form.submit()"
                                                    class="input py-2 text-xs">

                                                    @foreach ([
                                                        'retained',
                                                        'review_required',
                                                        'extended',
                                                        'archived',
                                                        'marked_for_disposal'
                                                    ] as $status)

                                                        <option
                                                            value="{{ $status }}"
                                                            @selected($retention->status === $status)>

                                                            {{ str($status)->headline() }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </form>

                                        @else

                                            <span class="badge bg-slate-100 text-slate-600">
                                                {{ str($retention->status)->headline() }}
                                            </span>

                                        @endcan

                                    </td>


                                    {{-- Compliance --}}
                                    <td class="px-5 py-4">

                                        @can('manageRetention')

                                            <form
                                                method="POST"
                                                action="{{ route('retention.update', $retention) }}"
                                                class="min-w-[145px]">

                                                @csrf
                                                @method('PUT')

                                                <input type="hidden"
                                                       name="record_title"
                                                       value="{{ $retention->record_title }}">

                                                <input type="hidden"
                                                       name="record_type"
                                                       value="{{ $retention->record_type }}">

                                                <input type="hidden"
                                                       name="record_id"
                                                       value="{{ $retention->record_id }}">

                                                <input type="hidden"
                                                       name="policy_id"
                                                       value="{{ $retention->policy_id }}">

                                                <input type="hidden"
                                                       name="start_date"
                                                       value="{{ $retention->start_date?->toDateString() }}">

                                                <input type="hidden"
                                                       name="review_date"
                                                       value="{{ $retention->review_date?->toDateString() }}">

                                                <input type="hidden"
                                                       name="status"
                                                       value="{{ $retention->status }}">

                                                <input type="hidden"
                                                       name="notes"
                                                       value="{{ $retention->notes }}">

                                                <select
                                                    name="compliance_status"
                                                    onchange="this.form.submit()"
                                                    @class([
                                                        'input py-2 text-xs font-semibold',

                                                        'border-success/30 bg-success/5 text-success'
                                                            => $retention->compliance_status === 'compliant',

                                                        'border-warning/40 bg-warning/5 text-amber-700'
                                                            => $retention->compliance_status === 'at_risk',

                                                        'border-error/30 bg-error/5 text-error'
                                                            => $retention->compliance_status === 'non_compliant',
                                                    ])>

                                                    @foreach ([
                                                        'compliant',
                                                        'at_risk',
                                                        'non_compliant'
                                                    ] as $status)

                                                        <option
                                                            value="{{ $status }}"
                                                            @selected($retention->compliance_status === $status)>

                                                            {{ str($status)->headline() }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </form>

                                        @else

                                            <span @class([
                                                'badge',
                                                'badge-success' => $retention->compliance_status === 'compliant',
                                                'badge-warning' => $retention->compliance_status === 'at_risk',
                                                'badge-error' => $retention->compliance_status === 'non_compliant',
                                            ])>

                                                {{ str($retention->compliance_status)->headline() }}

                                            </span>

                                        @endcan

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5" class="px-6 py-16">

                                        <div class="mx-auto flex max-w-sm flex-col items-center text-center">

                                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-accent/10 text-accent">

                                                <svg
                                                    class="h-7 w-7"
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

                                            <h3 class="font-heading text-base font-semibold text-primary">
                                                No retention records found
                                            </h3>

                                            <p class="mt-1 text-sm text-slate-500">
                                                Records registered for retention monitoring will appear here.
                                            </p>

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            @if ($retentions->hasPages())

                <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
                    {{ $retentions->withQueryString()->links() }}
                </div>

            @endif

        </div>

    </div>


    {{-- Retention Policies --}}
    @can('manageRetention')

        <div class="card overflow-hidden">

            <div class="border-b border-border bg-background/60 px-5 py-5">

                <div class="flex items-center justify-between gap-4">

                    <div>
                        <h3 class="font-heading text-base font-semibold text-primary">
                            Retention Policies
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Define retention duration and legal basis for organizational record categories.
                        </p>
                    </div>

                    <span class="badge badge-info">
                        {{ $allPolicies->count() }} Policies
                    </span>

                </div>

            </div>


            <div class="grid gap-6 p-5 xl:grid-cols-[360px_minmax(0,1fr)]">

                {{-- Add Policy --}}
                <form
                    method="POST"
                    action="{{ route('retention-policies.store') }}"
                    class="space-y-4 rounded-xl border border-border bg-background/40 p-4">

                    @csrf

                    <h4 class="font-heading text-sm font-semibold text-primary">
                        Create Policy
                    </h4>


                    <div>
                        <label class="label">
                            Policy Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            required
                            placeholder="e.g. Contract Records Policy"
                            class="input">
                    </div>


                    <div>
                        <label class="label">
                            Record Category
                        </label>

                        <select name="record_category" class="input">

                            @foreach ([
                                'administrative',
                                'contract',
                                'legal',
                                'permit',
                                'license',
                                'compliance',
                                'partnership',
                                'financial',
                                'operational',
                                'other'
                            ] as $category)

                                <option value="{{ $category }}">
                                    {{ str($category)->headline() }}
                                </option>

                            @endforeach

                        </select>
                    </div>


                    <div>
                        <label class="label">
                            Retention Years
                        </label>

                        <input
                            type="number"
                            min="1"
                            name="retention_years"
                            value="5"
                            required
                            class="input">
                    </div>


                    <div>
                        <label class="label">
                            Legal Basis
                        </label>

                        <textarea
                            name="legal_basis"
                            rows="2"
                            placeholder="Law, regulation, policy, or contractual basis..."
                            class="input"></textarea>
                    </div>


                    <div>
                        <label class="label">
                            Description
                        </label>

                        <textarea
                            name="description"
                            rows="2"
                            placeholder="Describe the policy..."
                            class="input"></textarea>
                    </div>


                    <input type="hidden" name="is_active" value="0">

                    <label class="flex items-center gap-2 text-sm text-slate-600">

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            checked
                            class="rounded border-border text-primary focus:ring-primary">

                        Active policy

                    </label>


                    <button type="submit" class="btn-primary w-full justify-center">
                        Create Policy
                    </button>

                </form>


                {{-- Policy List --}}
                <div class="space-y-3">

                    @forelse ($allPolicies as $policy)

                        <div class="rounded-xl border border-border bg-white p-4">

                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                                <div>

                                    <div class="flex flex-wrap items-center gap-2">

                                        <h4 class="font-button text-sm font-semibold text-primary">
                                            {{ $policy->name }}
                                        </h4>

                                        @if ($policy->is_active)

                                            <span class="badge badge-success">
                                                Active
                                            </span>

                                        @else

                                            <span class="badge bg-slate-100 text-slate-500">
                                                Inactive
                                            </span>

                                        @endif

                                    </div>


                                    <p class="mt-2 text-xs text-slate-500">

                                        {{ str($policy->record_category)->headline() }}

                                        <span class="mx-1">•</span>

                                        {{ $policy->retention_years }}
                                        {{ Str::plural('year', $policy->retention_years) }}

                                    </p>


                                    @if ($policy->legal_basis)

                                        <p class="mt-2 text-xs text-slate-500">
                                            <span class="font-semibold text-slate-600">
                                                Legal basis:
                                            </span>

                                            {{ $policy->legal_basis }}
                                        </p>

                                    @endif


                                    @if ($policy->description)

                                        <p class="mt-2 text-xs leading-5 text-slate-400">
                                            {{ $policy->description }}
                                        </p>

                                    @endif

                                </div>


                                {{-- Toggle Policy --}}
                                <form
                                    method="POST"
                                    action="{{ route('retention-policies.update', $policy) }}">

                                    @csrf
                                    @method('PUT')

                                    <input type="hidden"
                                           name="name"
                                           value="{{ $policy->name }}">

                                    <input type="hidden"
                                           name="record_category"
                                           value="{{ $policy->record_category }}">

                                    <input type="hidden"
                                           name="retention_years"
                                           value="{{ $policy->retention_years }}">

                                    <input type="hidden"
                                           name="description"
                                           value="{{ $policy->description }}">

                                    <input type="hidden"
                                           name="legal_basis"
                                           value="{{ $policy->legal_basis }}">

                                    <input type="hidden"
                                           name="is_active"
                                           value="{{ $policy->is_active ? 0 : 1 }}">

                                    <button
                                        type="submit"
                                        class="btn-outline whitespace-nowrap text-xs">

                                        {{ $policy->is_active ? 'Deactivate' : 'Activate' }}

                                    </button>

                                </form>

                            </div>

                        </div>

                    @empty

                        <div class="rounded-xl border border-dashed border-border p-10 text-center">

                            <p class="text-sm font-medium text-slate-600">
                                No retention policies
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Create your first retention policy using the form.
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    @endcan

</div>

@endsection