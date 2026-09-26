@extends('layouts.app')

@section('title', 'Legal Management')

@section('content')

@php
    $activeFilters = collect([
        $filters['q'] ?? null,
        $filters['record_type'] ?? null,
        $filters['status'] ?? null,
        $filters['review_status'] ?? null,
        $filters['priority'] ?? null,
        $filters['assigned_user_id'] ?? null,
        $filters['deadline'] ?? null,
    ])->filter(fn ($value) => filled($value))->count();
@endphp

<div class="space-y-4">

    {{-- =====================================================
         LEGAL MANAGEMENT HEADER
    ====================================================== --}}
    <x-page-header

        title="Legal Management"

        description="Manage legal matters, reviews, deadlines, and compliance records.">

        <x-slot:actions>


            @can('manageLegal')

                <button
                    type="button"
                    data-legal-create-open
                    class="btn-primary inline-flex items-center gap-2">

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 5v14M5 12h14" />

                    </svg>

                    New Legal Record

                </button>

            @endcan

        </x-slot:actions>

    </x-page-header>


    {{-- =====================================================
         LEGAL PORTFOLIO OVERVIEW
    ====================================================== --}}
    <section class="space-y-3">

        <x-section-header title="Legal Portfolio" />


        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">

            <x-metric-card
                label="Total Matters"
                :show-action="false"
                :value="number_format($kpis['total'])"
                :href="route('legal.index')"
                helper="Accessible matters."
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
                            d="M6 3h9l3 3v15H6V3zm3 5h6M9 12h6M9 16h4" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Active"
                :show-action="false"
                :value="number_format($kpis['active'])"
                :href="route('legal.index', ['status' => 'active'])"
                helper="In progress."
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
                label="Action Required"
                :show-action="false"
                :value="number_format($kpis['action_required'])"
                :href="route('legal.index', ['review_status' => 'action_required'])"
                helper="Requires follow-up."
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
                            d="M12 9v4m0 4h.01M10.3 3.8L2.5 18a2 2 0 001.8 3h15.4a2 2 0 001.8-3L13.7 3.8a2 2 0 00-3.4 0z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Due Soon"
                :show-action="false"
                :value="number_format($kpis['due_soon'])"
                :href="route('legal.index', ['deadline' => 'due_soon'])"
                helper="Due within 7 days."
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
                            d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Overdue / Expired"
                :show-action="false"
                :value="number_format($kpis['overdue'])"
                helper="Past due or expired."
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
                            d="M12 6v6l4 2M5 3l14 18" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>

        </div>

    </section>

    {{-- Search + Filters --}}
    <div class="card overflow-hidden">

        <div class="border-b border-border bg-background/40 px-4 py-3 sm:px-5">

            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <h3 class="font-heading text-base font-semibold text-primary">
                        Legal Record Register
                    </h3>

                </div>

            </div>

        </div>


        <form
            method="GET"
            action="{{ route('legal.index') }}"
            data-legal-filter-bar
            class="border-b border-border px-4 py-3 sm:px-5">

            <div class="grid gap-2 lg:grid-cols-12">

                <div class="lg:col-span-4">

                    <label
                        for="legal_search"
                        class="sr-only">
                        Search legal records
                    </label>

                    <div class="relative">

                        <svg
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m21 21-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />

                        </svg>

                        <input
                            id="legal_search"
                            type="search"
                            name="q"
                            value="{{ $filters['q'] ?? '' }}"
                            placeholder="Search legal records..."
                            class="input pl-10">

                    </div>

                </div>


                <div class="lg:col-span-2">

                    <select
                        name="record_type"
                        class="input">

                        <option value="">
                            All Types
                        </option>

                        @foreach ([
                            'permit',
                            'license',
                            'legal_case',
                            'requirement',
                            'legal_document'
                        ] as $type)

                            <option
                                value="{{ $type }}"
                                @selected(($filters['record_type'] ?? '') === $type)>

                                {{ str($type)->headline() }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="lg:col-span-2">

                    <select
                        name="priority"
                        class="input">

                        <option value="">
                            All Priorities
                        </option>

                        @foreach (['critical', 'high', 'medium', 'low'] as $priority)

                            <option
                                value="{{ $priority }}"
                                @selected(($filters['priority'] ?? '') === $priority)>

                                {{ str($priority)->headline() }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="lg:col-span-2">

                    <select
                        name="status"
                        class="input">

                        <option value="">
                            All Statuses
                        </option>

                        @foreach ([
                            'active',
                            'pending',
                            'expiring_soon',
                            'expired',
                            'renewed',
                            'closed'
                        ] as $status)

                            <option
                                value="{{ $status }}"
                                @selected(($filters['status'] ?? '') === $status)>

                                {{ str($status)->headline() }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="flex gap-2 lg:col-span-2">

                    <button
                        type="submit"
                        class="btn-primary flex-1">
                        Apply
                    </button>

                    <a
                        href="{{ route('legal.index') }}"
                        class="btn-outline px-3"
                        title="Reset filters">

                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v6h6M20 20v-6h-6M5 19A9 9 0 0019 5" />
                        </svg>

                    </a>

                </div>

            </div>


            <div class="mt-2 grid gap-2 sm:grid-cols-2 lg:grid-cols-4">

                <select
                    name="review_status"
                    class="input">

                    <option value="">
                        All Review States
                    </option>

                    @foreach ([
                        'not_reviewed',
                        'in_review',
                        'reviewed',
                        'action_required'
                    ] as $review)

                        <option
                            value="{{ $review }}"
                            @selected(($filters['review_status'] ?? '') === $review)>

                            {{ str($review)->headline() }}

                        </option>

                    @endforeach

                </select>


                <select
                    name="assigned_user_id"
                    class="input">

                    <option value="">
                        All Officers
                    </option>

                    @foreach ($assignableOfficers as $officer)

                        <option
                            value="{{ $officer->id }}"
                            @selected((string) ($filters['assigned_user_id'] ?? '') === (string) $officer->id)>

                            {{ $officer->full_name }}

                        </option>

                    @endforeach

                </select>


                <select
                    name="deadline"
                    class="input">

                    <option value="">
                        All Deadlines
                    </option>

                    @foreach ([
                        'overdue' => 'Overdue',
                        'due_today' => 'Due Today',
                        'due_soon' => 'Due Within 7 Days',
                        'expired' => 'Expired',
                        'expiring_soon' => 'Expiring Within 30 Days',
                    ] as $value => $label)

                        <option
                            value="{{ $value }}"
                            @selected(($filters['deadline'] ?? '') === $value)>

                            {{ $label }}

                        </option>

                    @endforeach

                </select>


                <div class="flex items-center">

                    @if ($activeFilters > 0)

                        <span class="text-xs text-slate-500">

                            <strong class="font-semibold text-primary">
                                {{ $activeFilters }}
                            </strong>

                            active
                            {{ \Illuminate\Support\Str::plural('filter', $activeFilters) }}

                        </span>

                    @endif

                </div>

            </div>

        </form>


        {{-- Desktop Table --}}
        <div class="hidden overflow-x-auto lg:block">

            <table class="min-w-full divide-y divide-border">

                <thead class="bg-background/50">

                    <tr class="text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">

                        <th class="px-5 py-3">
                            Legal Matter
                        </th>

                        <th class="px-4 py-3">
                            Classification
                        </th>

                        <th class="px-4 py-3">
                            Assignment
                        </th>

                        <th class="px-4 py-3">
                            Deadline
                        </th>

                        <th class="px-4 py-3">
                            Status
                        </th>

                        <th class="px-4 py-3">
                            Review
                        </th>

                        <th class="w-14 px-4 py-3 text-right">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-border bg-card">

                    @forelse ($records as $record)

                        @php
                            $priorityClass = match ($record->priority) {
                                'critical' => 'border-error/20 bg-error/10 text-error',
                                'high' => 'border-warning/20 bg-warning/10 text-warning',
                                'low' => 'border-success/20 bg-success/10 text-success',
                                default => 'border-accent/20 bg-accent/10 text-accent',
                            };

                            $deadlineClass = match ($record->deadlineState()) {
                                'expired',
                                'overdue',
                                'action_overdue' => 'border-error/20 bg-error/10 text-error',

                                'due_today',
                                'expires_today',
                                'due_soon',
                                'expiring_soon' => 'border-warning/20 bg-warning/10 text-warning',

                                'closed' => 'border-slate-200 bg-slate-100 text-slate-500',

                                default => 'border-success/20 bg-success/10 text-success',
                            };

                            $reviewClass = match ($record->review_status) {
                                'action_required' => 'border-error/20 bg-error/10 text-error',
                                'in_review' => 'border-warning/20 bg-warning/10 text-warning',
                                'reviewed' => 'border-success/20 bg-success/10 text-success',
                                default => 'border-slate-200 bg-slate-50 text-slate-500',
                            };

                            $statusClass = match ($record->status) {
                                'active',
                                'renewed' => 'border-success/20 bg-success/10 text-success',

                                'pending',
                                'expiring_soon' => 'border-warning/20 bg-warning/10 text-warning',

                                'expired' => 'border-error/20 bg-error/10 text-error',

                                default => 'border-slate-200 bg-slate-100 text-slate-500',
                            };

                            $deadlineDate = $record->deadlineDate();
                        @endphp

                        <tr class="transition hover:bg-background/35">

                            <td class="max-w-[310px] px-5 py-4 align-top">

                                <button
                                    type="button"
                                    data-legal-view-open
                                    data-modal-target="legal-details-{{ $record->id }}"
                                    class="text-left">

                                    <p class="truncate text-sm font-semibold text-primary hover:text-secondary">
                                        {{ $record->title }}
                                    </p>

                                </button>

                                <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] text-slate-400">

                                    <span>
                                        #{{ $record->id }}
                                    </span>

                                    @if ($record->reference_number)
                                        <span>
                                            {{ $record->reference_number }}
                                        </span>
                                    @endif

                                    @if ($record->issuing_authority)
                                        <span>
                                            {{ $record->issuing_authority }}
                                        </span>
                                    @endif

                                </div>

                                @if ($record->next_action)

                                    <p class="mt-2 line-clamp-1 text-xs text-slate-500">
                                        Next: {{ $record->next_action }}
                                    </p>

                                @endif

                            </td>


                            <td class="px-4 py-4 align-top">

                                <p class="text-sm font-medium text-primary">
                                    {{ str($record->record_type)->headline() }}
                                </p>

                                <div class="mt-2 flex flex-wrap gap-1.5">

                                    <span class="rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $priorityClass }}">
                                        {{ str($record->priority)->headline() }}
                                    </span>

                                    @if ($record->legal_category)

                                        <span class="rounded-full border border-border bg-background px-2 py-0.5 text-[10px] font-medium text-slate-500">
                                            {{ $record->legal_category }}
                                        </span>

                                    @endif

                                </div>

                            </td>


                            <td class="px-4 py-4 align-top">

                                @if ($record->assignedOfficer)

                                    <p class="text-sm font-semibold text-primary">
                                        {{ $record->assignedOfficer->full_name }}
                                    </p>

                                    <p class="mt-1 max-w-[190px] truncate text-[11px] text-slate-400">
                                        {{ $record->assignedOfficer->email }}
                                    </p>

                                @elseif ($record->responsible_officer_email)

                                    <p class="max-w-[190px] truncate text-xs font-medium text-slate-600">
                                        {{ $record->responsible_officer_email }}
                                    </p>

                                @else

                                    <span class="text-xs text-slate-400">
                                        Unassigned
                                    </span>

                                @endif

                            </td>


                            <td class="px-4 py-4 align-top">

                                @if ($deadlineDate)

                                    <p class="text-sm font-semibold text-primary">
                                        {{ $deadlineDate->format('M d, Y') }}
                                    </p>

                                    <span class="mt-1.5 inline-flex rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $deadlineClass }}">
                                        {{ $record->deadlineLabel() }}
                                    </span>

                                @else

                                    <span class="text-xs text-slate-400">
                                        No deadline
                                    </span>

                                @endif

                            </td>


                            <td class="px-4 py-4 align-top">

                                <span class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-semibold {{ $statusClass }}">
                                    {{ str($record->status)->headline() }}
                                </span>

                            </td>


                            <td class="px-4 py-4 align-top">

                                <span class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-semibold {{ $reviewClass }}">
                                    {{ str($record->review_status)->headline() }}
                                </span>

                            </td>


                            <td class="px-4 py-4 text-right align-top">

                                <button
                                    type="button"
                                    data-legal-actions-open
                                    data-details-target="legal-details-{{ $record->id }}"
                                    data-review-target="legal-review-{{ $record->id }}"
                                    data-edit-url="{{ route('legal.edit', $record) }}"
                                    data-can-edit="{{ auth()->user()->can('manageLegal') ? '1' : '0' }}"
                                    data-can-review="{{ auth()->user()->can('reviewLegal') ? '1' : '0' }}"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary"
                                    aria-label="Legal record actions">

                                    <svg
                                        class="h-5 w-5"
                                        fill="currentColor"
                                        viewBox="0 0 20 20">

                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4z" />

                                    </svg>

                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-6 py-16 text-center">

                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-background text-slate-400">

                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 3h9l3 3v15H6V3zm3 5h6M9 12h6M9 16h4" />
                                    </svg>

                                </div>

                                <h4 class="mt-4 font-heading text-sm font-semibold text-primary">
                                    No legal records found
                                </h4>

                                <p class="mx-auto mt-1 max-w-md text-xs leading-5 text-slate-500">

                                    @if ($activeFilters > 0)
                                        No legal records match the current filters.
                                    @else
                                        There are no legal matters registered yet.
                                    @endif

                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Mobile / Tablet Cards --}}
        <div class="divide-y divide-border lg:hidden">

            @forelse ($records as $record)

                @php
                    $priorityClass = match ($record->priority) {
                        'critical' => 'border-error/20 bg-error/10 text-error',
                        'high' => 'border-warning/20 bg-warning/10 text-warning',
                        'low' => 'border-success/20 bg-success/10 text-success',
                        default => 'border-accent/20 bg-accent/10 text-accent',
                    };

                    $deadlineClass = match ($record->deadlineState()) {
                        'expired',
                        'overdue',
                        'action_overdue' => 'border-error/20 bg-error/10 text-error',

                        'due_today',
                        'expires_today',
                        'due_soon',
                        'expiring_soon' => 'border-warning/20 bg-warning/10 text-warning',

                        'closed' => 'border-slate-200 bg-slate-100 text-slate-500',

                        default => 'border-success/20 bg-success/10 text-success',
                    };

                    $deadlineDate = $record->deadlineDate();
                @endphp

                <div class="p-5">

                    <div class="flex items-start justify-between gap-3">

                        <div class="min-w-0">

                            <button
                                type="button"
                                data-legal-view-open
                                data-modal-target="legal-details-{{ $record->id }}"
                                class="block max-w-full text-left">

                                <p class="truncate text-sm font-semibold text-primary">
                                    {{ $record->title }}
                                </p>

                            </button>

                            <p class="mt-1 text-xs text-slate-400">
                                {{ str($record->record_type)->headline() }}
                                @if ($record->reference_number)
                                    · {{ $record->reference_number }}
                                @endif
                            </p>

                        </div>


                        <button
                            type="button"
                            data-legal-actions-open
                            data-details-target="legal-details-{{ $record->id }}"
                            data-review-target="legal-review-{{ $record->id }}"
                            data-edit-url="{{ route('legal.edit', $record) }}"
                            data-can-edit="{{ auth()->user()->can('manageLegal') ? '1' : '0' }}"
                            data-can-review="{{ auth()->user()->can('reviewLegal') ? '1' : '0' }}"
                            class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary"
                            aria-label="Legal record actions">

                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4z" />
                            </svg>

                        </button>

                    </div>


                    <div class="mt-4 grid grid-cols-2 gap-3">

                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                Priority
                            </p>
                            <span class="mt-1 inline-flex rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $priorityClass }}">
                                {{ str($record->priority)->headline() }}
                            </span>
                        </div>


                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                Deadline
                            </p>

                            @if ($deadlineDate)
                                <p class="mt-1 text-xs font-semibold text-primary">
                                    {{ $deadlineDate->format('M d, Y') }}
                                </p>
                                <span class="mt-1 inline-flex rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $deadlineClass }}">
                                    {{ $record->deadlineLabel() }}
                                </span>
                            @else
                                <p class="mt-1 text-xs text-slate-400">
                                    None
                                </p>
                            @endif
                        </div>


                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                Assigned
                            </p>
                            <p class="mt-1 truncate text-xs font-medium text-primary">
                                {{ $record->assignedOfficer?->full_name ?? $record->responsible_officer_email ?? 'Unassigned' }}
                            </p>
                        </div>


                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                Review
                            </p>
                            <p class="mt-1 text-xs font-medium text-primary">
                                {{ str($record->review_status)->headline() }}
                            </p>
                        </div>

                    </div>

                </div>

            @empty

                <div class="px-5 py-12 text-center text-sm text-slate-500">
                    No legal records found.
                </div>

            @endforelse

        </div>


        @if ($records->hasPages())

            <div class="border-t border-border px-5 py-4">
                {{ $records->links() }}
            </div>

        @endif

    </div>

</div>


@can('manageLegal')
    @include('legal._create-modal')
@endcan


@foreach ($records as $record)

    @include('legal._details-modal', [
        'record' => $record,
        'recordActivities' => $activities->get((string) $record->id, collect()),
    ])

    @can('reviewLegal')
        @include('legal._review-modal', [
            'record' => $record,
        ])
    @endcan

@endforeach


@include('legal._actions-menu')


@if (old('form_context') === 'create')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document
                .querySelector('[data-legal-create-open]')
                ?.click();
        });
    </script>

@endif


@if (str_starts_with((string) old('form_context'), 'review-'))

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const id =
                @json(old('review_record_id'));

            if (!id) {
                return;
            }

            const trigger =
                document.querySelector(
                    `[data-legal-actions-open][data-review-target="legal-review-${id}"]`
                );

            if (!trigger) {
                return;
            }

            trigger.click();

            requestAnimationFrame(() => {
                document
                    .querySelector('[data-legal-menu-review]')
                    ?.click();
            });

        });
    </script>

@endif

@endsection