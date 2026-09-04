@extends('layouts.app')

@section('title', 'Retention Review')

@section('content')

<div class="mx-auto max-w-5xl space-y-6">

    {{-- Header --}}
    <div>

        <a
            href="{{ route('retention.index') }}"
            class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-primary">

            &larr; Back to Records Retention

        </a>

        <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

        <h1 class="font-heading text-2xl font-bold text-primary">
            Retention Review
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Review the retention requirements and record a controlled disposition decision.
        </p>

    </div>


    @if ($errors->any())

        <div class="rounded-xl border border-error/20 bg-error/5 p-4">

            <ul class="list-disc space-y-1 pl-5 text-sm text-error">

                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">

        {{-- Decision --}}
        <section class="card overflow-hidden">

            <div class="border-b border-border px-6 py-5">

                <div class="flex flex-wrap items-start justify-between gap-3">

                    <div>

                        <h2 class="font-heading text-lg font-semibold text-primary">
                            {{ $retention->record_title }}
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ str($retention->record_type)->headline() }}

                            @if ($retention->record_id)
                                · Record #{{ $retention->record_id }}
                            @endif
                        </p>

                    </div>


                    @if ($retention->status === 'review_required')

                        <span class="badge badge-warning">
                            Review Required
                        </span>

                    @else

                        <span class="badge bg-slate-100 text-slate-600">
                            {{ str($retention->status)->headline() }}
                        </span>

                    @endif

                </div>

            </div>


            <form
                method="POST"
                action="{{ route('retention.decision', $retention) }}"
                class="space-y-6 p-6">

                @csrf


                {{-- Decision --}}
                <div>

                    <label class="label">
                        Review Decision
                    </label>

                    <div class="mt-2 grid gap-3 sm:grid-cols-2">

                        <label class="cursor-pointer rounded-xl border border-border p-4 transition hover:border-primary/40 hover:bg-primary/5">

                            <div class="flex items-start gap-3">

                                <input
                                    type="radio"
                                    name="decision"
                                    value="retain"
                                    required
                                    @checked(old('decision') === 'retain')
                                    class="mt-1 border-border text-primary focus:ring-primary">

                                <div>
                                    <p class="font-button text-sm font-semibold text-primary">
                                        Keep / Retain
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Keep the record and establish its next review date.
                                    </p>
                                </div>

                            </div>

                        </label>


                        <label class="cursor-pointer rounded-xl border border-border p-4 transition hover:border-primary/40 hover:bg-primary/5">

                            <div class="flex items-start gap-3">

                                <input
                                    type="radio"
                                    name="decision"
                                    value="extend"
                                    required
                                    @checked(old('decision') === 'extend')
                                    class="mt-1 border-border text-primary focus:ring-primary">

                                <div>
                                    <p class="font-button text-sm font-semibold text-primary">
                                        Extend Retention
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Extend the retention period and set another future review date.
                                    </p>
                                </div>

                            </div>

                        </label>


                        <label class="cursor-pointer rounded-xl border border-border p-4 transition hover:border-primary/40 hover:bg-primary/5">

                            <div class="flex items-start gap-3">

                                <input
                                    type="radio"
                                    name="decision"
                                    value="archive"
                                    required
                                    @checked(old('decision') === 'archive')
                                    class="mt-1 border-border text-primary focus:ring-primary">

                                <div>
                                    <p class="font-button text-sm font-semibold text-primary">
                                        Archive
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Place the retention record and linked document in archived status.
                                    </p>
                                </div>

                            </div>

                        </label>


                        <label class="cursor-pointer rounded-xl border border-border p-4 transition hover:border-error/30 hover:bg-error/5">

                            <div class="flex items-start gap-3">

                                <input
                                    type="radio"
                                    name="decision"
                                    value="mark_for_disposal"
                                    required
                                    @checked(old('decision') === 'mark_for_disposal')
                                    class="mt-1 border-border text-primary focus:ring-primary">

                                <div>
                                    <p class="font-button text-sm font-semibold text-error">
                                        Mark for Disposal
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Flag the record for controlled disposition. No file will be deleted.
                                    </p>
                                </div>

                            </div>

                        </label>

                    </div>

                </div>


                {{-- Next review --}}
                <div
                    id="next-review-container"
                    class="hidden">

                    <label for="review_date" class="label">
                        Next Review Date *
                    </label>

                    <input
                        id="review_date"
                        type="date"
                        name="review_date"
                        value="{{ old(
                            'review_date',
                            $retention->review_date && $retention->review_date->isFuture()
                                ? $retention->review_date->toDateString()
                                : ''
                        ) }}"
                        min="{{ now()->addDay()->toDateString() }}"
                        class="input">

                    <p class="mt-1.5 text-xs text-slate-500">
                        Required when retaining or extending the record.
                    </p>

                </div>


                {{-- Compliance --}}
                <div>

                    <label for="compliance_status" class="label">
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

                </div>


                {{-- Notes --}}
                <div>

                    <label for="notes" class="label">
                        Decision Notes
                    </label>

                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                        class="input"
                        placeholder="Explain the reason for this retention decision...">{{ old('notes', $retention->notes) }}</textarea>

                </div>


                <div class="flex flex-col-reverse gap-3 border-t border-border pt-5 sm:flex-row sm:justify-end">

                    <a
                        href="{{ route('retention.index') }}"
                        class="btn-outline justify-center">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        class="btn-primary justify-center">

                        Confirm Decision

                    </button>

                </div>

            </form>

        </section>


        {{-- Current retention data --}}
        <aside class="space-y-5">

            <section class="card p-5">

                <h2 class="font-heading text-sm font-semibold text-primary">
                    Current Retention
                </h2>


                <dl class="mt-5 space-y-4">

                    <div>

                        <dt class="text-xs uppercase tracking-wide text-slate-400">
                            Policy
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-primary">
                            {{ $retention->policy_name ?: 'No policy assigned' }}
                        </dd>

                    </div>


                    @if ($retention->policy)

                        <div>

                            <dt class="text-xs uppercase tracking-wide text-slate-400">
                                Retention Period
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-primary">
                                {{ $retention->policy->retention_years }}
                                {{ Str::plural('year', $retention->policy->retention_years) }}
                            </dd>

                        </div>

                    @endif


                    <div>

                        <dt class="text-xs uppercase tracking-wide text-slate-400">
                            Retention Start
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-primary">
                            {{ $retention->start_date?->format('M d, Y') ?: 'Not specified' }}
                        </dd>

                    </div>


                    <div>

                        <dt class="text-xs uppercase tracking-wide text-slate-400">
                            Current Review Date
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-primary">
                            {{ $retention->review_date?->format('M d, Y') ?: 'Not specified' }}
                        </dd>

                    </div>


                    <div>

                        <dt class="text-xs uppercase tracking-wide text-slate-400">
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

                <section class="card p-5">

                    <h2 class="font-heading text-sm font-semibold text-primary">
                        Linked Document
                    </h2>

                    <p class="mt-3 text-sm font-medium text-primary">
                        {{ $document->title }}
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        Document Management
                        · {{ str($document->status)->headline() }}
                    </p>

                    <a
                        href="{{ route('documents.show', $document) }}"
                        class="btn-outline mt-4 flex w-full justify-center">

                        Open Document

                    </a>

                </section>

            @endif


            <section class="rounded-xl border border-warning/30 bg-warning/5 p-4">

                <p class="text-sm font-semibold text-amber-700">
                    Controlled disposition
                </p>

                <p class="mt-1 text-xs leading-5 text-slate-500">
                    Marking a record for disposal does not permanently delete the document. Disposal should require a separate authorized process.
                </p>

            </section>

        </aside>

    </div>

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

        function updateReviewField() {

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
                requiresDate;
        }

        radios.forEach(function (radio) {

            radio.addEventListener(
                'change',
                updateReviewField
            );

        });

        updateReviewField();
    }
);
</script>

@endsection