@extends('layouts.app')

@section('title', 'Retention Review')

@section('content')

@php
    $policyLabel =
        $retention->policy?->name
        ?: $retention->policy_name
        ?: 'No retention policy assigned';

    $daysToReview = $retention->review_date
        ? (int) now()
            ->startOfDay()
            ->diffInDays(
                $retention->review_date
                    ->copy()
                    ->startOfDay(),
                false
            )
        : null;
@endphp


<div class="mx-auto max-w-6xl space-y-6">

    {{-- Page header --}}
    <header>

        <a
            href="{{ route('retention.index', ['tab' => 'records']) }}"
            class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-primary">

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
                    d="M15 19l-7-7 7-7" />

            </svg>

            Retention Register

        </a>


        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

            <div>

                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                    Controlled Review
                </p>

                <h1 class="mt-1 font-heading text-2xl font-bold tracking-tight text-primary">
                    Retention Review
                </h1>

                <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                    Review the record's retention requirements, compliance position, and next lifecycle action.
                </p>

            </div>


            <span @class([
                'badge self-start sm:self-auto',

                'badge-warning' =>
                    $retention->status === 'review_required',

                'badge-error' =>
                    $retention->status === 'marked_for_disposal',

                'badge-success' =>
                    $retention->status === 'retained',

                'badge-info' =>
                    ! in_array(
                        $retention->status,
                        [
                            'review_required',
                            'marked_for_disposal',
                            'retained'
                        ],
                        true
                    ),
            ])>

                {{ str($retention->status)->headline() }}

            </span>

        </div>

    </header>


    {{-- Validation --}}
    @if ($errors->any())

        <div
            class="rounded-xl border border-error/20 bg-error/5 p-4"
            role="alert">

            <div class="flex items-start gap-3">

                <svg
                    class="mt-0.5 h-5 w-5 shrink-0 text-error"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9v4m0 4h.01M10.29 3.86l-7.82 13.55A2 2 0 004.2 20h15.6a2 2 0 001.73-2.99L13.71 3.86a2 2 0 00-3.42 0z" />

                </svg>

                <div>

                    <p class="text-sm font-semibold text-error">
                        Review the highlighted information before continuing.
                    </p>

                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-error">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    {{-- Record summary --}}
    <section class="overflow-hidden rounded-2xl border border-border bg-card shadow-card">

        <div class="flex flex-col gap-4 border-b border-border bg-background/40 px-5 py-5 sm:flex-row sm:items-start sm:justify-between">

            <div class="min-w-0">

                <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                    Record Under Review
                </p>

                <h2 class="mt-1 font-heading text-lg font-semibold text-primary">
                    {{ $retention->record_title }}
                </h2>


                <div class="mt-2 flex flex-wrap items-center gap-2">

                    <span class="rounded-full bg-primary/5 px-2.5 py-1 text-[10px] font-semibold text-primary ring-1 ring-inset ring-primary/10">
                        {{ str($retention->record_type)->headline() }}
                    </span>

                    @if ($retention->record_id)

                        <span class="text-[11px] text-slate-400">
                            Record #{{ $retention->record_id }}
                        </span>

                    @endif

                    <span class="text-[11px] text-slate-400">
                        {{ $policyLabel }}
                    </span>

                </div>

            </div>


            @if ($daysToReview !== null)

                <div class="text-left sm:text-right">

                    <p class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                        Current Review
                    </p>

                    <p class="mt-1 text-sm font-semibold text-primary">
                        {{ $retention->review_date->format('M d, Y') }}
                    </p>

                    @if ($daysToReview < 0)

                        <p class="mt-1 text-[11px] font-semibold text-error">
                            Overdue by {{ abs($daysToReview) }}
                            {{ Str::plural('day', abs($daysToReview)) }}
                        </p>

                    @elseif ($daysToReview === 0)

                        <p class="mt-1 text-[11px] font-semibold text-warning">
                            Due today
                        </p>

                    @elseif ($daysToReview <= 30)

                        <p class="mt-1 text-[11px] font-semibold text-amber-700">
                            Due in {{ $daysToReview }}
                            {{ Str::plural('day', $daysToReview) }}
                        </p>

                    @endif

                </div>

            @endif

        </div>


        {{-- Pending disposition lock --}}
        @if ($retention->disposition_status === 'pending')

            <div class="p-5">

                <section class="rounded-2xl border border-warning/30 bg-warning/5 p-5">

                    <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">

                        <div class="flex min-w-0 items-start gap-3">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-warning/10 text-amber-600">

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
                                        d="M12 8v4m0 4h.01M5.07 19H18.93a2 2 0 001.74-2.99L13.74 4a2 2 0 00-3.48 0L3.33 16.01A2 2 0 005.07 19z" />

                                </svg>

                            </div>


                            <div>

                                <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-amber-700">
                                    Controlled Disposition
                                </p>

                                <h3 class="mt-1 font-heading text-base font-semibold text-primary">
                                    Pending Disposal Approval
                                </h3>

                                <p class="mt-1 max-w-2xl text-xs leading-5 text-slate-600">
                                    This record already has an active disposal request. Routine lifecycle decisions are temporarily locked until the authorized disposal review is completed.
                                </p>

                            </div>

                        </div>


                        <span class="badge badge-warning self-start">
                            Pending Approval
                        </span>

                    </div>


                    <dl class="mt-5 grid gap-4 border-t border-warning/20 pt-4 sm:grid-cols-3">

                        <div>

                            <dt class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                                Requested By
                            </dt>

                            <dd class="mt-1 break-all text-sm font-medium text-primary">
                                {{ $retention->disposition_requested_by ?: 'Unknown requester' }}
                            </dd>

                        </div>


                        <div>

                            <dt class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                                Requested
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-primary">
                                {{ $retention->disposition_requested_at?->format('M d, Y h:i A') ?: 'Date unavailable' }}
                            </dd>

                        </div>


                        <div>

                            <dt class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                                Current Lifecycle
                            </dt>

                            <dd class="mt-1">
                                <span class="badge badge-error">
                                    Marked for Disposal
                                </span>
                            </dd>

                        </div>


                        <div class="sm:col-span-3">

                            <dt class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                                Disposal Reason
                            </dt>

                            <dd class="mt-1 text-sm leading-6 text-slate-600">
                                {{ in_array(trim((string) $retention->disposition_reason), ['', '-', '--', '---'], true) ? 'No disposal reason recorded.' : $retention->disposition_reason }}
                            </dd>

                        </div>

                    </dl>


                    <div class="mt-5 flex flex-col gap-2 border-t border-warning/20 pt-4 sm:flex-row sm:justify-end">

                        <a
                            href="{{ route('retention.index', ['tab' => 'records']) }}"
                            class="btn-outline justify-center">
                            Return to Register
                        </a>

                        @can('approveRetentionDisposal')

                            <a
                                href="{{ route('retention.disposition', $retention) }}"
                                class="btn-primary justify-center">
                                View Disposal Request
                            </a>

                        @endcan

                    </div>

                </section>

            </div>

        @else

        <form
            method="POST"
            action="{{ route('retention.decision', $retention) }}">

            @csrf


            <div class="grid gap-6 p-5 lg:grid-cols-[minmax(0,1fr)_320px]">

                {{-- Decision workspace --}}
                <div class="min-w-0 space-y-6">

                    {{-- Routine lifecycle decisions --}}
                    <section>

                        <div>

                            <h3 class="font-heading text-base font-semibold text-primary">
                                Choose Lifecycle Action
                            </h3>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Select the appropriate routine retention action for this record.
                            </p>

                        </div>


                        <div class="mt-4 grid gap-3 sm:grid-cols-3">

                            {{-- Retain --}}
                            <label
                                class="group cursor-pointer rounded-xl border border-border bg-card p-4 transition hover:border-primary/30 hover:bg-primary/[0.025] has-[:checked]:border-primary/40 has-[:checked]:bg-primary/5 has-[:checked]:ring-1 has-[:checked]:ring-primary/10">

                                <div class="flex items-start justify-between gap-3">

                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary/5 text-primary">

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
                                                d="M5 13l4 4L19 7" />

                                        </svg>

                                    </div>

                                    <input
                                        type="radio"
                                        name="decision"
                                        value="retain"
                                        required
                                        @checked(old('decision') === 'retain')
                                        class="mt-1 border-border text-primary focus:ring-primary">

                                </div>

                                <p class="mt-3 text-sm font-semibold text-primary">
                                    Retain
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Keep the record under its current retention requirement.
                                </p>

                            </label>


                            {{-- Extend --}}
                            <label
                                class="group cursor-pointer rounded-xl border border-border bg-card p-4 transition hover:border-primary/30 hover:bg-primary/[0.025] has-[:checked]:border-primary/40 has-[:checked]:bg-primary/5 has-[:checked]:ring-1 has-[:checked]:ring-primary/10">

                                <div class="flex items-start justify-between gap-3">

                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 text-sky-600">

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
                                                d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z" />

                                        </svg>

                                    </div>

                                    <input
                                        type="radio"
                                        name="decision"
                                        value="extend"
                                        required
                                        @checked(old('decision') === 'extend')
                                        class="mt-1 border-border text-primary focus:ring-primary">

                                </div>

                                <p class="mt-3 text-sm font-semibold text-primary">
                                    Extend
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Continue retention and establish another future review.
                                </p>

                            </label>


                            {{-- Archive --}}
                            <label
                                class="group cursor-pointer rounded-xl border border-border bg-card p-4 transition hover:border-primary/30 hover:bg-primary/[0.025] has-[:checked]:border-primary/40 has-[:checked]:bg-primary/5 has-[:checked]:ring-1 has-[:checked]:ring-primary/10">

                                <div class="flex items-start justify-between gap-3">

                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600">

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
                                                d="M5 8h14M7 8v10h10V8M9 12h6M6 4h12a1 1 0 011 1v3H5V5a1 1 0 011-1z" />

                                        </svg>

                                    </div>

                                    <input
                                        type="radio"
                                        name="decision"
                                        value="archive"
                                        required
                                        @checked(old('decision') === 'archive')
                                        class="mt-1 border-border text-primary focus:ring-primary">

                                </div>

                                <p class="mt-3 text-sm font-semibold text-primary">
                                    Archive
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Move the retention record and linked document to archived status.
                                </p>

                            </label>

                        </div>

                    </section>


                    {{-- Controlled disposition --}}
                    <section class="rounded-xl border border-error/20 bg-error/[0.025] p-4">

                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                            <div class="flex min-w-0 items-start gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-error/10 text-error">

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
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16" />

                                    </svg>

                                </div>

                                <div>

                                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-error">
                                        Controlled Disposition
                                    </p>

                                    <h3 class="mt-0.5 font-heading text-sm font-semibold text-primary">
                                        Submit for Disposal Review
                                    </h3>

                                    <p class="mt-1 max-w-xl text-xs leading-5 text-slate-500">
                                        Flag this record for a separate authorized disposal review. This action does not permanently delete the record or linked document.
                                    </p>

                                </div>

                            </div>


                            <label class="inline-flex cursor-pointer items-center gap-2 self-start rounded-lg border border-error/20 bg-card px-3 py-2 text-xs font-semibold text-error transition hover:bg-error/5">

                                <input
                                    type="radio"
                                    name="decision"
                                    value="mark_for_disposal"
                                    required
                                    @checked(old('decision') === 'mark_for_disposal')
                                    class="border-border text-error focus:ring-error">

                                Select Disposal

                            </label>

                        </div>

                    </section>


                    {{-- Next review --}}
                    <section
                        id="next-review-container"
                        class="hidden rounded-xl border border-primary/10 bg-primary/[0.025] p-4">

                        <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_220px] sm:items-end">

                            <div>

                                <h3 class="text-sm font-semibold text-primary">
                                    Next Review Date
                                </h3>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Retained and extended records require a future review date.
                                </p>

                            </div>


                            <div>

                                <label
                                    for="review_date"
                                    class="sr-only">
                                    Next Review Date
                                </label>

                                <input
                                    id="review_date"
                                    type="date"
                                    name="review_date"
                                    value="{{ old(
                                        'review_date',
                                        $retention->review_date
                                            && $retention->review_date->isFuture()
                                            ? $retention->review_date->toDateString()
                                            : ''
                                    ) }}"
                                    min="{{ now()->addDay()->toDateString() }}"
                                    class="input">

                                @error('review_date')

                                    <p class="mt-1 text-xs font-medium text-error">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                        </div>

                    </section>


                    {{-- Assessment --}}
                    <section>

                        <div class="grid gap-4 sm:grid-cols-2">

                            <div>

                                <label
                                    for="compliance_status"
                                    class="label">
                                    Compliance Assessment
                                </label>

                                <select
                                    id="compliance_status"
                                    name="compliance_status"
                                    required
                                    class="input">

                                    @foreach ([
                                        'compliant',
                                        'at_risk',
                                        'non_compliant'
                                    ] as $status)

                                        <option
                                            value="{{ $status }}"
                                            @selected(
                                                old(
                                                    'compliance_status',
                                                    $retention->compliance_status
                                                ) === $status
                                            )>

                                            {{ str($status)->headline() }}

                                        </option>

                                    @endforeach

                                </select>

                                @error('compliance_status')

                                    <p class="mt-1 text-xs font-medium text-error">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>


                            <div class="sm:col-span-2">

                                <label
                                    for="notes"
                                    class="label">
                                    Decision Notes
                                </label>

                                <textarea
                                    id="notes"
                                    name="notes"
                                    rows="4"
                                    maxlength="2000"
                                    class="input"
                                    placeholder="Document the reason, evidence, or context supporting this decision...">{{ old('notes', $retention->notes) }}</textarea>

                                <div class="mt-1.5 flex flex-col gap-1 text-[11px] text-slate-500 sm:flex-row sm:items-center sm:justify-between">

                                    <span id="decision-notes-help">
                                        Add supporting context for the selected retention action.
                                    </span>

                                    <span>
                                        Maximum 2,000 characters
                                    </span>

                                </div>

                                @error('notes')

                                    <p class="mt-1 text-xs font-medium text-error">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                        </div>

                    </section>

                </div>


                {{-- Current retention context --}}
                <aside class="space-y-4">

                    <section class="rounded-xl border border-border bg-background/30 p-4">

                        <h3 class="font-heading text-sm font-semibold text-primary">
                            Current Retention
                        </h3>


                        <dl class="mt-4 divide-y divide-border">

                            <div class="pb-3">

                                <dt class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                                    Policy
                                </dt>

                                <dd class="mt-1 text-sm font-medium text-primary">
                                    {{ $policyLabel }}
                                </dd>

                            </div>


                            @if ($retention->policy)

                                <div class="py-3">

                                    <dt class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                                        Retention Period
                                    </dt>

                                    <dd class="mt-1 text-sm font-medium text-primary">
                                        {{ $retention->policy->retention_years }}
                                        {{ Str::plural('year', $retention->policy->retention_years) }}
                                    </dd>

                                </div>

                            @endif


                            <div class="py-3">

                                <dt class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                                    Retention Start
                                </dt>

                                <dd class="mt-1 text-sm font-medium text-primary">
                                    {{ $retention->start_date?->format('M d, Y') ?: 'Not specified' }}
                                </dd>

                            </div>


                            <div class="py-3">

                                <dt class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                                    Review Date
                                </dt>

                                <dd class="mt-1 text-sm font-medium text-primary">
                                    {{ $retention->review_date?->format('M d, Y') ?: 'Not specified' }}
                                </dd>

                            </div>


                            <div class="pt-3">

                                <dt class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                                    Compliance
                                </dt>

                                <dd class="mt-2">

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

                                </dd>

                            </div>

                        </dl>

                    </section>


                    @if ($document)

                        <section class="rounded-xl border border-border bg-card p-4">

                            <p class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                                Linked Document
                            </p>

                            <h3 class="mt-2 font-heading text-sm font-semibold text-primary">
                                {{ $document->title }}
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ str($document->status)->headline() }}
                            </p>

                            <a
                                href="{{ route('documents.show', $document) }}"
                                class="btn-outline mt-4 flex w-full justify-center">
                                View Linked Document
                            </a>

                        </section>

                    @endif


                    <section class="rounded-xl border border-warning/20 bg-warning/5 p-4">

                        <div class="flex items-start gap-2.5">

                            <svg
                                class="mt-0.5 h-4 w-4 shrink-0 text-amber-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                            </svg>

                            <div>

                                <p class="text-xs font-semibold text-amber-700">
                                    Controlled disposal
                                </p>

                                <p class="mt-1 text-[11px] leading-5 text-slate-600">
                                    Disposal requires a separate authorized decision after this review.
                                </p>

                            </div>

                        </div>

                    </section>

                </aside>

            </div>


            {{-- Footer actions --}}
            <div class="flex flex-col-reverse gap-3 border-t border-border bg-background/30 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <p class="text-[11px] leading-5 text-slate-500">
                    The selected action will be recorded as the latest retention decision.
                </p>


                <div class="flex flex-col-reverse gap-2 sm:flex-row">

                    <a
                        href="{{ route('retention.index', ['tab' => 'records']) }}"
                        class="btn-outline justify-center">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn-primary justify-center">
                        Save Decision
                    </button>

                </div>

            </div>

        </form>

        @endif

    </section>

</div>


<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {
        const radios =
            document.querySelectorAll(
                'input[name="decision"]'
            );

        const reviewContainer =
            document.getElementById(
                'next-review-container'
            );

        const reviewInput =
            document.getElementById(
                'review_date'
            );

        const notesHelp =
            document.getElementById(
                'decision-notes-help'
            );

        if (
            !reviewContainer ||
            !reviewInput ||
            !notesHelp
        ) {
            return;
        }

        function updateDecisionState() {
            const selected =
                document.querySelector(
                    'input[name="decision"]:checked'
                );

            const requiresDate =
                selected &&
                (
                    selected.value === 'retain' ||
                    selected.value === 'extend'
                );

            reviewContainer.classList.toggle(
                'hidden',
                !requiresDate
            );

            reviewInput.required =
                Boolean(requiresDate);

            if (!selected) {
                notesHelp.textContent =
                    'Add supporting context for the selected retention action.';

                return;
            }

            if (
                selected.value ===
                'mark_for_disposal'
            ) {
                notesHelp.textContent =
                    'For disposal requests, these notes will be used as the disposal reason when provided.';

                return;
            }

            if (
                selected.value ===
                'archive'
            ) {
                notesHelp.textContent =
                    'Document why the record is appropriate for archival status.';

                return;
            }

            notesHelp.textContent =
                'Document the basis for retaining or extending this record.';
        }

        radios.forEach(
            function (radio) {
                radio.addEventListener(
                    'change',
                    updateDecisionState
                );
            }
        );

        updateDecisionState();
    }
);
</script>

@endsection