@extends('layouts.app')

@section('title', 'Privacy Reviews')

@section('content')

@php
    $statusLabels = [
        'pending' => 'Pending',
        'under_review' => 'Under Review',
        'approved' => 'Approved',
        'partially_approved' => 'Partially Approved',
        'denied' => 'Denied',
        'completed' => 'Completed',
    ];
@endphp

<div class="space-y-6">

    <x-page-header
        eyebrow="Privacy Governance"
        title="Privacy Request Reviews"
        badge="Controlled Review"
        description="Review verified privacy-rights requests without directly deleting or altering personal data." />

    @if(session('status'))
        <div
            role="status"
            class="rounded-xl border border-success/20 bg-success/5 px-4 py-3 text-sm text-success">

            {{ session('status') }}

        </div>
    @endif

    @if($errors->any())
        <div
            role="alert"
            class="rounded-xl border border-error/20 bg-error/5 px-4 py-3">

            <p class="text-sm font-semibold text-error">
                Privacy review action could not be completed.
            </p>

            <ul class="mt-2 list-disc space-y-1 pl-5 text-xs text-error">

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>
    @endif

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">

        @foreach([
            ['Total Requests', $stats['total']],
            ['Pending', $stats['pending']],
            ['Under Review', $stats['under_review']],
            ['Execution Pending', $stats['execution_pending']],
            ['Closed', $stats['closed']],
        ] as $metric)

            <div class="card p-4">

                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                    {{ $metric[0] }}
                </p>

                <p class="mt-2 font-heading text-2xl font-bold text-primary">
                    {{ number_format($metric[1]) }}
                </p>

            </div>

        @endforeach

    </section>

    <section class="card overflow-hidden">

        <div class="border-b border-border px-5 py-4">

            <h2 class="font-heading text-base font-semibold text-primary">
                Review Filters
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Filter by request subject, number, type, or governance status.
            </p>

        </div>

        <form
            method="GET"
            action="{{ route('privacy-requests.index') }}"
            class="grid gap-4 p-5 lg:grid-cols-4 lg:items-end">

            <div class="lg:col-span-2">

                <label for="privacy-search" class="label">
                    Search
                </label>

                <input
                    id="privacy-search"
                    type="search"
                    name="q"
                    maxlength="100"
                    value="{{ $search }}"
                    class="input"
                    placeholder="Request number, name, email, or type">

            </div>

            <div>

                <label for="privacy-status" class="label">
                    Status
                </label>

                <select
                    id="privacy-status"
                    name="status"
                    class="input">

                    <option value="">All statuses</option>

                    @foreach($statusLabels as $value => $label)
                        <option
                            value="{{ $value }}"
                            @selected($status === $value)>

                            {{ $label }}

                        </option>
                    @endforeach

                </select>

            </div>

            <div>

                <label for="privacy-type" class="label">
                    Type
                </label>

                <select
                    id="privacy-type"
                    name="type"
                    class="input">

                    <option value="">All types</option>

                    <option
                        value="erasure"
                        @selected($type === 'erasure')>
                        Erasure
                    </option>

                    <option
                        value="blocking"
                        @selected($type === 'blocking')>
                        Blocking
                    </option>

                    <option
                        value="withdraw_consent"
                        @selected($type === 'withdraw_consent')>
                        Withdraw Consent
                    </option>

                </select>

            </div>

            <div class="flex gap-2 lg:col-span-4 lg:justify-end">

                <a
                    href="{{ route('privacy-requests.index') }}"
                    class="btn-outline">
                    Reset
                </a>

                <button
                    type="submit"
                    class="btn-primary">
                    Apply Filters
                </button>

            </div>

        </form>

    </section>

    <section class="space-y-4">

        <div>

            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-accent">
                Governance Queue
            </p>

            <h2 class="mt-1 font-heading text-xl font-semibold text-primary">
                Privacy Requests
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Managers and system administrators may review requests. Reviewers cannot decide their own requests.
            </p>

        </div>

        @forelse($privacyRequests as $privacyRequest)

            @php
                $ownRequest =
                    $privacyRequest->user_id ===
                    auth()->id();

                $executionPending =
                    in_array(
                        $privacyRequest->status,
                        [
                            'approved',
                            'partially_approved',
                        ],
                        true
                    );
            @endphp

            <article class="card overflow-hidden">

                <div class="border-b border-border px-5 py-4">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                        <div>

                            <h3 class="font-heading text-base font-semibold text-primary">
                                Request #{{ $privacyRequest->id }}
                            </h3>

                            <p class="mt-1 text-sm text-slate-600">

                                {{
                                    $privacyRequest->user?->full_name
                                    ?? 'Former / unavailable account'
                                }}

                            </p>

                            <p class="text-xs text-slate-400">

                                {{
                                    $privacyRequest->user?->email
                                    ?? 'No active account reference'
                                }}

                            </p>

                        </div>

                        <div class="flex flex-wrap gap-2">

                            <span class="badge badge-info">

                                {{
                                    str($privacyRequest->type)
                                        ->replace('_', ' ')
                                        ->title()
                                }}

                            </span>

                            <span class="badge">

                                {{
                                    $statusLabels[
                                        $privacyRequest->status
                                    ]
                                    ??
                                    str($privacyRequest->status)
                                        ->replace('_', ' ')
                                        ->title()
                                }}

                            </span>

                        </div>

                    </div>

                </div>

                <div class="grid gap-5 p-5 xl:grid-cols-2">

                    <div class="space-y-4">

                        <div>

                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                Request Details
                            </p>

                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">

                                {{
                                    $privacyRequest->details
                                    ?: 'No additional details provided.'
                                }}

                            </p>

                        </div>

                        <div>

                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                Submitted
                            </p>

                            <p class="mt-1 text-sm text-slate-600">

                                {{
                                    optional(
                                        $privacyRequest->submitted_at
                                    )->format(
                                        'M j, Y g:i A'
                                    )
                                }}

                            </p>

                        </div>

                        <div>

                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                Identity Verification
                            </p>

                            <p class="mt-1 text-sm text-slate-600">

                                {{
                                    $privacyRequest->identity_verified_at
                                    ? 'Verified'
                                    : 'Not verified'
                                }}

                            </p>

                        </div>

                        <div>

                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                Reviewer
                            </p>

                            <p class="mt-1 text-sm text-slate-600">

                                {{
                                    $privacyRequest->reviewedBy?->full_name
                                    ?? 'Not assigned'
                                }}

                            </p>

                        </div>

                        @if($privacyRequest->decision_reason)

                            <div>

                                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                    Decision Reason
                                </p>

                                <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-600">
                                    {{ $privacyRequest->decision_reason }}
                                </p>

                            </div>

                        @endif

                        @if($privacyRequest->retention_basis)

                            <div>

                                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                    Retention Basis
                                </p>

                                <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-600">
                                    {{ $privacyRequest->retention_basis }}
                                </p>

                            </div>

                        @endif

                        @if($executionPending)

                            <div class="rounded-xl border border-warning/20 bg-warning/5 px-4 py-3">

                                <p class="text-xs font-semibold text-warning">
                                    Controlled execution pending
                                </p>

                                <p class="mt-1 text-xs text-slate-600">
                                    Approval has not deleted or anonymized any personal data.
                                </p>

                            </div>

                        @endif

                    </div>

                    <div>

                        @if($ownRequest)

                            <div class="rounded-xl border border-error/20 bg-error/5 px-4 py-3">

                                <p class="text-sm font-semibold text-error">
                                    Separation of duties
                                </p>

                                <p class="mt-1 text-xs text-slate-600">
                                    You cannot review or decide your own privacy request.
                                </p>

                            </div>

                        @elseif($privacyRequest->status === 'pending')

                            <form
                                method="POST"
                                action="{{ route('privacy-requests.start-review', $privacyRequest) }}"
                                data-submit-loading
                                data-loading-text="Starting review...">

                                @csrf

                                <div class="rounded-xl border border-accent/20 bg-accent/5 p-4">

                                    <p class="text-sm font-semibold text-primary">
                                        Begin controlled review
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        This records reviewer intake only. No personal information will be changed.
                                    </p>

                                    <button
                                        type="submit"
                                        class="btn-primary mt-4 w-full justify-center">

                                        Start Review

                                    </button>

                                </div>

                            </form>

                        @elseif($privacyRequest->status === 'under_review')

                            <form
                                method="POST"
                                action="{{ route('privacy-requests.decision', $privacyRequest) }}"
                                data-submit-loading
                                data-loading-text="Recording decision..."
                                class="space-y-4">

                                @csrf

                                <div>

                                    <label
                                        for="decision-{{ $privacyRequest->id }}"
                                        class="label">

                                        Decision

                                    </label>

                                    <select
                                        id="decision-{{ $privacyRequest->id }}"
                                        name="decision"
                                        required
                                        class="input">

                                        <option value="">
                                            Select decision
                                        </option>

                                        <option value="approved">
                                            Approve
                                        </option>

                                        <option value="partially_approved">
                                            Partially Approve
                                        </option>

                                        <option value="denied">
                                            Deny
                                        </option>

                                    </select>

                                </div>

                                <div>

                                    <label
                                        for="decision-reason-{{ $privacyRequest->id }}"
                                        class="label">

                                        Decision Reason

                                    </label>

                                    <textarea
                                        id="decision-reason-{{ $privacyRequest->id }}"
                                        name="decision_reason"
                                        maxlength="2000"
                                        rows="4"
                                        required
                                        class="input resize-y"></textarea>

                                </div>

                                <div>

                                    <label
                                        for="retention-basis-{{ $privacyRequest->id }}"
                                        class="label">

                                        Retention Basis

                                    </label>

                                    <textarea
                                        id="retention-basis-{{ $privacyRequest->id }}"
                                        name="retention_basis"
                                        maxlength="2000"
                                        rows="3"
                                        class="input resize-y"></textarea>

                                    <p class="mt-1 text-[10px] text-slate-400">
                                        Required for partial approval or denial.
                                    </p>

                                </div>

                                <button
                                    type="submit"
                                    class="btn-primary w-full justify-center">

                                    Record Decision

                                </button>

                            </form>

                        @else

                            <div class="rounded-xl border border-border bg-background/50 px-4 py-3">

                                <p class="text-sm font-semibold text-primary">
                                    Governance decision recorded
                                </p>

                                <p class="mt-1 text-xs text-slate-500">

                                    {{
                                        $executionPending
                                        ? 'Awaiting controlled execution.'
                                        : 'No further reviewer action is available.'
                                    }}

                                </p>

                            </div>

                        @endif

                    </div>

                </div>


                @if(
                    $executionPending
                    && auth()->user()->can('executePrivacy')
                )

                    <div class="border-t border-border bg-warning/5 px-5 py-5">

                        <div class="grid gap-4 lg:grid-cols-2 lg:items-start">

                            <div>

                                <p class="text-sm font-semibold text-primary">
                                    Controlled privacy execution
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-600">
                                    This is a high-impact action. Review and execution must be performed by different users.
                                </p>

                                <p class="mt-2 text-xs leading-5 text-slate-500">
                                    Erasure anonymizes structured identity while preserving operational, retention, legal, contractual, and audit records. Blocking revokes account access without deleting records. Consent withdrawal updates active consent records.
                                </p>

                            </div>

                            <form
                                method="POST"
                                action="{{ route('privacy-requests.execute', $privacyRequest) }}"
                                data-submit-loading
                                data-loading-text="Executing privacy request..."
                                class="space-y-3 rounded-xl border border-warning/20 bg-background p-4">

                                @csrf

                                <div>

                                    <label
                                        for="execution-password-{{ $privacyRequest->id }}"
                                        class="label">

                                        Current Password

                                    </label>

                                    <input
                                        id="execution-password-{{ $privacyRequest->id }}"
                                        type="password"
                                        name="current_password"
                                        autocomplete="current-password"
                                        required
                                        class="input">

                                </div>

                                <label class="flex items-start gap-3 text-xs leading-5 text-slate-600">

                                    <input
                                        type="checkbox"
                                        name="confirm_execution"
                                        value="1"
                                        required
                                        class="mt-1">

                                    <span>
                                        I confirm that I reviewed the recorded decision and understand that this executes the approved privacy action.
                                    </span>

                                </label>

                                <button
                                    type="submit"
                                    class="btn-primary w-full justify-center">

                                    Execute Controlled Request

                                </button>

                            </form>

                        </div>

                    </div>

                @endif


                @if(
                    $privacyRequest->status === 'completed'
                    && $privacyRequest->completed_at
                )

                    <div class="border-t border-border bg-success/5 px-5 py-4">

                        <p class="text-sm font-semibold text-success">
                            Controlled execution completed
                        </p>

                        <p class="mt-1 text-xs text-slate-600">

                            Executed
                            {{
                                optional(
                                    $privacyRequest->executed_at
                                )->format('M j, Y g:i A')
                            }}

                            @if($privacyRequest->executedBy)
                                by {{ $privacyRequest->executedBy->full_name }}
                            @endif

                        </p>

                    </div>

                @endif
            </article>

        @empty

            <div class="card px-6 py-14 text-center">

                <h3 class="font-heading text-base font-semibold text-primary">
                    No privacy requests found
                </h3>

                <p class="mt-2 text-sm text-slate-500">
                    No requests match the selected filters.
                </p>

            </div>

        @endforelse

        @if($privacyRequests->hasPages())

            <div>
                {{ $privacyRequests->links() }}
            </div>

        @endif

    </section>

    <div class="rounded-xl border border-accent/20 bg-accent/5 px-4 py-3">

        <p class="text-xs leading-5 text-slate-600">

            <span class="font-semibold text-primary">
                Governance boundary:
            </span>

            This workspace records decisions only. Actual erasure, anonymization, blocking, or consent withdrawal requires controlled execution.

        </p>

    </div>

</div>

@endsection