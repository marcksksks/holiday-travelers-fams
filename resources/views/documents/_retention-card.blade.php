@php
    $retention =
        $document->retentionRecord;

    $retentionStatus =
        $retention?->status;

    $complianceStatus =
        $retention?->compliance_status;

    $reviewDatePassed =
        $retention?->review_date
        &&
        $retention->review_date->isPast();
@endphp


<section class="card overflow-hidden">

    {{-- =====================================================
         HEADER
    ====================================================== --}}
    <div class="flex items-start justify-between gap-3 border-b border-border px-4 py-4">

        <div class="flex min-w-0 items-center gap-3">

            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary/5 text-primary">

                <svg
                    class="h-4 w-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12l2 2 4-4m5-4v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3 8 3z" />

                </svg>

            </span>


            <div class="min-w-0">

                <h2 class="font-heading text-sm font-semibold text-primary">
                    Retention & Compliance
                </h2>

                <p class="mt-0.5 text-[10px] text-slate-400">
                    Records lifecycle and compliance status
                </p>

            </div>

        </div>


        @if ($retention)

            @switch($complianceStatus)

                @case('compliant')

                    <span class="badge badge-success shrink-0">
                        Compliant
                    </span>

                    @break


                @case('at_risk')

                    <span class="badge badge-warning shrink-0">
                        At Risk
                    </span>

                    @break


                @case('non_compliant')

                    <span class="badge badge-error shrink-0">
                        Non-Compliant
                    </span>

                    @break


                @default

                    <span class="badge bg-slate-100 text-slate-600 shrink-0">
                        Not Assessed
                    </span>

            @endswitch

        @endif

    </div>


    {{-- =====================================================
         ASSIGNED RETENTION POLICY
    ====================================================== --}}
    @if ($retention)

        <div class="p-4">

            {{-- Policy --}}
            <div class="rounded-xl bg-background/60 p-3">

                <p class="text-[9px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                    Retention Policy
                </p>


                <div class="mt-1.5 flex flex-wrap items-center justify-between gap-2">

                    <p class="min-w-0 break-words text-xs font-semibold leading-5 text-primary">
                        {{ $retention->policy_name ?: 'No policy name' }}
                    </p>


                    @switch($retentionStatus)

                        @case('retained')

                            <span class="badge badge-success">
                                Retained
                            </span>

                            @break


                        @case('review_required')

                            <span class="badge badge-warning">
                                Review Required
                            </span>

                            @break


                        @case('extended')

                            <span class="badge badge-info">
                                Extended
                            </span>

                            @break


                        @case('archived')

                            <span class="badge bg-slate-100 text-slate-600">
                                Archived
                            </span>

                            @break


                        @case('marked_for_disposal')

                            <span class="badge badge-error">
                                Marked for Disposal
                            </span>

                            @break


                        @default

                            <span class="badge bg-slate-100 text-slate-600">
                                {{ str($retentionStatus ?? 'unknown')->headline() }}
                            </span>

                    @endswitch

                </div>

            </div>


            {{-- Important dates --}}
            <div class="mt-4 grid grid-cols-2 gap-3">

                <div class="rounded-xl border border-border px-3 py-3">

                    <div class="flex items-center gap-1.5">

                        <svg
                            class="h-3.5 w-3.5 text-slate-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 7V3m8 4V3M5 11h14M5 5h14v16H5V5z" />

                        </svg>

                        <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                            Retention Start
                        </p>

                    </div>


                    <p class="mt-2 text-xs font-semibold text-primary">
                        {{ $retention->start_date?->format('M d, Y') ?: 'Not specified' }}
                    </p>

                </div>


                <div
                    @class([
                        'rounded-xl border px-3 py-3',
                        'border-error/25 bg-error/5' => $reviewDatePassed,
                        'border-border' => ! $reviewDatePassed,
                    ])>

                    <div class="flex items-center gap-1.5">

                        <svg
                            @class([
                                'h-3.5 w-3.5',
                                'text-error' => $reviewDatePassed,
                                'text-slate-400' => ! $reviewDatePassed,
                            ])
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />

                        </svg>


                        <p
                            @class([
                                'text-[9px] font-semibold uppercase tracking-wide',
                                'text-error' => $reviewDatePassed,
                                'text-slate-400' => ! $reviewDatePassed,
                            ])>

                            Review Date

                        </p>

                    </div>


                    <p
                        @class([
                            'mt-2 text-xs font-semibold',
                            'text-error' => $reviewDatePassed,
                            'text-primary' => ! $reviewDatePassed,
                        ])>

                        {{ $retention->review_date?->format('M d, Y') ?: 'Not specified' }}

                    </p>


                    @if ($reviewDatePassed)

                        <p class="mt-1 text-[9px] font-medium text-error">
                            Review date passed
                        </p>

                    @endif

                </div>

            </div>


            {{-- Notes --}}
            @if ($retention->notes)

                <details class="group mt-4 overflow-hidden rounded-xl border border-border">

                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-3 py-2.5 transition hover:bg-background">

                        <span class="text-[10px] font-semibold text-primary">
                            Retention Notes
                        </span>


                        <svg
                            class="h-3.5 w-3.5 text-slate-400 transition-transform duration-200 group-open:rotate-180"
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


                    <div class="border-t border-border bg-background/30 px-3 py-3">

                        <p class="text-[11px] leading-5 text-slate-500">
                            {{ $retention->notes }}
                        </p>

                    </div>

                </details>

            @endif


            {{-- Open retention --}}
            <a
                href="{{ route('retention.index', ['record_type' => 'document']) }}"
                class="btn-outline mt-4 flex w-full justify-center text-xs">

                <svg
                    class="h-4 w-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5l7 7-7 7" />

                </svg>

                Open Records Retention

            </a>

        </div>


    {{-- =====================================================
         NO RETENTION POLICY
    ====================================================== --}}
    @else

        <div class="p-4">

            <div class="rounded-xl border border-dashed border-border bg-background/40 px-4 py-5 text-center">

                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-primary/5 text-primary">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m5-4v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3 8 3z" />

                    </svg>

                </div>


                <p class="mt-3 text-xs font-semibold text-primary">
                    No retention policy assigned
                </p>

                <p class="mx-auto mt-1 max-w-xs text-[10px] leading-4 text-slate-400">
                    This document is not yet associated with an active retention policy.
                </p>

            </div>


            @can('manageRetention')

                @if ($retentionPolicies->isNotEmpty())

                    <details
                        @if ($errors->has('policy_id') || $errors->has('start_date'))
                            open
                        @endif
                        class="group mt-4 overflow-hidden rounded-xl border border-border">

                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-3 py-3 transition hover:bg-background">

                            <div>

                                <p class="text-xs font-semibold text-primary">
                                    Assign Retention Policy
                                </p>

                                <p class="mt-0.5 text-[9px] text-slate-400">
                                    Apply an active {{ str($document->category)->headline() }} policy.
                                </p>

                            </div>


                            <svg
                                class="h-4 w-4 text-slate-400 transition-transform duration-200 group-open:rotate-180"
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


                        <form
                            method="POST"
                            action="{{ route('documents.retention.assign', $document) }}"
                            class="space-y-3 border-t border-border bg-background/30 p-3">

                            @csrf


                            <div>

                                <label class="label">
                                    Retention Policy
                                </label>

                                <select
                                    name="policy_id"
                                    required
                                    class="input">

                                    <option value="">
                                        Select policy
                                    </option>

                                    @foreach ($retentionPolicies as $policy)

                                        <option
                                            value="{{ $policy->id }}"
                                            @selected((string) old('policy_id') === (string) $policy->id)>

                                            {{ $policy->name }}
                                            -
                                            {{ $policy->retention_years }}
                                            {{ $policy->retention_years === 1 ? 'year' : 'years' }}

                                        </option>

                                    @endforeach

                                </select>

                                @error('policy_id')

                                    <p class="mt-1 text-[10px] text-error">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>


                            <div>

                                <label class="label">
                                    Retention Start
                                </label>

                                <input
                                    type="date"
                                    name="start_date"
                                    value="{{ old(
                                        'start_date',
                                        $document->document_date?->format('Y-m-d')
                                        ?? now()->format('Y-m-d')
                                    ) }}"
                                    class="input">

                                @error('start_date')

                                    <p class="mt-1 text-[10px] text-error">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>


                            <div>

                                <label class="label">
                                    Notes
                                </label>

                                <textarea
                                    name="notes"
                                    rows="2"
                                    class="input resize-y"
                                    placeholder="Optional retention notes...">{{ old('notes') }}</textarea>

                                @error('notes')

                                    <p class="mt-1 text-[10px] text-error">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>


                            <button
                                type="submit"
                                class="btn-primary flex w-full justify-center">

                                Assign Policy

                            </button>

                        </form>

                    </details>

                @else

                    <div class="mt-4 rounded-xl border border-warning/20 bg-warning/5 p-3">

                        <div class="flex items-start gap-2.5">

                            <svg
                                class="mt-0.5 h-4 w-4 shrink-0 text-amber-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v3m0 4h.01M10.3 3.6L2.5 17a2 2 0 001.7 3h15.6a2 2 0 001.7-3L13.7 3.6a2 2 0 00-3.4 0z" />

                            </svg>


                            <div>

                                <p class="text-[10px] font-semibold text-amber-700">
                                    No matching active policy
                                </p>

                                <p class="mt-1 text-[10px] leading-4 text-slate-500">
                                    Create an active policy for the
                                    {{ str($document->category)->headline() }}
                                    category first.
                                </p>


                                <a
                                    href="{{ route('retention.index') }}"
                                    class="mt-2 inline-flex text-[10px] font-semibold text-primary transition hover:text-secondary">

                                    Open Records Retention

                                    <span class="ml-1">
                                        &rarr;
                                    </span>

                                </a>

                            </div>

                        </div>

                    </div>

                @endif

            @else

                <a
                    href="{{ route('retention.index', ['record_type' => 'document']) }}"
                    class="btn-outline mt-4 flex w-full justify-center text-xs">

                    Open Records Retention

                </a>

            @endcan

        </div>

    @endif

</section>