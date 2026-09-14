@extends('layouts.app')

@section('title', 'Disposal Review')

@section('content')

@php
    $isOwnRequest =
        $retention->disposition_requested_by ===
        auth()->user()?->email;

    $policyLabel =
        $retention->policy?->name
        ?: $retention->policy_name
        ?: 'No retention policy assigned';
@endphp


<div class="mx-auto max-w-6xl space-y-6">

    {{-- Page header --}}
    <header>

        <a
            href="{{ route('retention.index', ['tab' => 'disposal']) }}"
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

            Disposal Queue

        </a>


        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

            <div>

                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-amber-600">
                    Controlled Disposition
                </p>

                <h1 class="mt-1 font-heading text-2xl font-bold tracking-tight text-primary">
                    Review Disposal Request
                </h1>

                <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                    Review the request context and determine whether this record should proceed to authorized controlled disposition.
                </p>

            </div>


            <span class="badge badge-warning self-start sm:self-auto">
                Pending Approval
            </span>

        </div>

    </header>


    {{-- Validation errors --}}
    @if ($errors->any())

        <div
            class="rounded-xl border border-error/20 bg-error/5 p-4"
            role="alert">

            <div class="flex items-start gap-3">

                <div class="mt-0.5 text-error">

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
                            d="M12 9v4m0 4h.01M10.29 3.86l-7.82 13.55A2 2 0 004.2 20h15.6a2 2 0 001.73-2.99L13.71 3.86a2 2 0 00-3.42 0z" />

                    </svg>

                </div>

                <div>

                    <p class="text-sm font-semibold text-error">
                        The request could not be processed.
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


    {{-- Record context --}}
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

                    @if ($isOwnRequest)

                        <span class="rounded-full bg-warning/10 px-2.5 py-1 text-[10px] font-semibold text-amber-700">
                            Your Request
                        </span>

                    @endif

                </div>

            </div>


            <span class="badge badge-error self-start">
                Marked for Disposal
            </span>

        </div>


        <div class="grid gap-6 p-5 lg:grid-cols-[minmax(0,1fr)_320px]">

            {{-- Main decision workspace --}}
            <div class="min-w-0 space-y-6">

                {{-- Request details --}}
                <section>

                    <div class="flex items-center gap-2">

                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-warning/10 text-amber-600">

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

                        </div>

                        <div>

                            <h3 class="font-heading text-sm font-semibold text-primary">
                                Disposal Request
                            </h3>

                            <p class="text-[11px] text-slate-500">
                                Request information submitted during the retention review.
                            </p>

                        </div>

                    </div>


                    <dl class="mt-4 grid gap-4 rounded-xl border border-border bg-background/30 p-4 sm:grid-cols-2">

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

                                @if ($retention->disposition_requested_at)

                                    {{ $retention->disposition_requested_at->format('M d, Y') }}

                                    <span class="font-normal text-slate-400">
                                        · {{ $retention->disposition_requested_at->format('h:i A') }}
                                    </span>

                                @else

                                    Date unavailable

                                @endif

                            </dd>

                        </div>


                        <div class="sm:col-span-2">

                            <dt class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                                Disposal Reason
                            </dt>

                            <dd class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $retention->disposition_reason ?: 'No disposal reason was provided.' }}</dd>

                        </div>

                    </dl>

                </section>


                {{-- Safety context --}}
                <section class="rounded-xl border border-warning/20 bg-warning/5 p-4">

                    <div class="flex items-start gap-3">

                        <div class="mt-0.5 shrink-0 text-amber-600">

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
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                            </svg>

                        </div>

                        <div>

                            <p class="text-sm font-semibold text-amber-700">
                                Approval does not delete the record
                            </p>

                            <p class="mt-1 text-xs leading-5 text-slate-600">
                                Approval authorizes this request to proceed through the controlled disposition workflow. The linked record or document is not permanently deleted by this approval action.
                            </p>

                        </div>

                    </div>

                </section>


                {{-- Decision workspace --}}
                @if (! $isOwnRequest)

                    <section>

                        <div>

                            <h3 class="font-heading text-base font-semibold text-primary">
                                Make a Decision
                            </h3>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Review the request carefully before approving or rejecting the proposed disposition.
                            </p>

                        </div>


                        <div class="mt-4 grid gap-4 xl:grid-cols-2">

                            {{-- Approve --}}
                            <form
                                method="POST"
                                action="{{ route('retention.disposition.approve', $retention) }}"
                                class="flex flex-col rounded-xl border border-success/20 bg-success/5 p-4">

                                @csrf

                                <div>

                                    <div class="flex items-center gap-2">

                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-success/10 text-success">

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

                                        <div>

                                            <h4 class="text-sm font-semibold text-primary">
                                                Approve Request
                                            </h4>

                                            <p class="text-[11px] text-slate-500">
                                                Authorize controlled disposition.
                                            </p>

                                        </div>

                                    </div>


                                    <div class="mt-4">

                                        <label
                                            for="approval_notes"
                                            class="label">
                                            Approval Notes
                                            <span class="font-normal text-slate-400">
                                                (Optional)
                                            </span>
                                        </label>

                                        <textarea
                                            id="approval_notes"
                                            name="decision_notes"
                                            rows="4"
                                            maxlength="2000"
                                            class="input"
                                            placeholder="Add context for this approval...">{{ old('decision_notes') }}</textarea>

                                    </div>

                                </div>


                                <button
                                    type="submit"
                                    class="btn-primary mt-4 w-full justify-center"
                                    onclick="return confirm('Approve this disposal request? This action authorizes controlled disposition but does not delete the record.')">

                                    Approve Disposal Request

                                </button>

                            </form>


                            {{-- Reject --}}
                            <form
                                method="POST"
                                action="{{ route('retention.disposition.reject', $retention) }}"
                                class="flex flex-col rounded-xl border border-error/20 bg-error/5 p-4">

                                @csrf

                                <div>

                                    <div class="flex items-center gap-2">

                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-error/10 text-error">

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
                                                    d="M6 18L18 6M6 6l12 12" />

                                            </svg>

                                        </div>

                                        <div>

                                            <h4 class="text-sm font-semibold text-primary">
                                                Reject Request
                                            </h4>

                                            <p class="text-[11px] text-slate-500">
                                                Return the record for further review.
                                            </p>

                                        </div>

                                    </div>


                                    <div class="mt-4">

                                        <label
                                            for="rejection_notes"
                                            class="label">
                                            Rejection Reason *
                                        </label>

                                        <textarea
                                            id="rejection_notes"
                                            name="decision_notes"
                                            rows="4"
                                            maxlength="2000"
                                            required
                                            class="input"
                                            placeholder="Explain why this disposal request should not proceed...">{{ old('decision_notes') }}</textarea>

                                        <p class="mt-1.5 text-[11px] leading-4 text-slate-500">
                                            A clear reason helps the record owner determine the next retention action.
                                        </p>

                                    </div>

                                </div>


                                <button
                                    type="submit"
                                    class="btn-outline mt-4 w-full justify-center border-error/30 text-error hover:bg-error/10">

                                    Reject Disposal Request

                                </button>

                            </form>

                        </div>

                    </section>

                @else

                    <section
                        class="rounded-xl border border-warning/30 bg-warning/5 p-5"
                        aria-labelledby="separation-of-duties-title">

                        <div class="flex items-start gap-3">

                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-warning/10 text-amber-600">

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
                                        d="M12 11c0-1.105.895-2 2-2s2 .895 2 2v1m-4 0h4m-6 8h8a2 2 0 002-2v-6a2 2 0 00-2-2h-1V8a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2h4z" />

                                </svg>

                            </div>

                            <div>

                                <h3
                                    id="separation-of-duties-title"
                                    class="font-heading text-sm font-semibold text-amber-700">
                                    Separation of Duties
                                </h3>

                                <p class="mt-1 text-xs leading-5 text-slate-600">
                                    You submitted this disposal request. Another authorized approver must review and approve or reject it.
                                </p>

                                <a
                                    href="{{ route('retention.index', ['tab' => 'disposal']) }}"
                                    class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-primary transition hover:text-secondary">

                                    Return to Disposal Queue

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

                            </div>

                        </div>

                    </section>

                @endif

            </div>


            {{-- Retention context --}}
            <aside class="space-y-4">

                <section class="rounded-xl border border-border bg-background/30 p-4">

                    <h3 class="font-heading text-sm font-semibold text-primary">
                        Retention Context
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
                                Review Date
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-primary">
                                {{ $retention->review_date?->format('M d, Y') ?: 'Not specified' }}
                            </dd>

                        </div>


                        <div class="py-3">

                            <dt class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                                Lifecycle
                            </dt>

                            <dd class="mt-2">
                                <span class="badge badge-error">
                                    {{ str($retention->status)->headline() }}
                                </span>
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

            </aside>

        </div>

    </section>

</div>

@endsection