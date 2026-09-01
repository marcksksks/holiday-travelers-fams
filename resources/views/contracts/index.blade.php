@extends('layouts.app')

@section('title', 'Contracts')

@section('content')

@php
    $canRenewContracts = auth()->user()->hasRole([
        \App\Models\User::ROLE_ADMIN_OFFICER,
        \App\Models\User::ROLE_MANAGER,
        \App\Models\User::ROLE_SYS_ADMIN,
    ]);
@endphp

<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Contract Management
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Create, review, approve, monitor, and renew organizational contracts.
            </p>
        </div>

        <div class="rounded-xl border border-border bg-card px-4 py-2.5 shadow-card">
            <p class="text-xs text-slate-500">
                Total Contracts
            </p>

            <p class="mt-0.5 font-heading text-lg font-bold text-primary">
                {{ $contracts->total() }}
            </p>
        </div>

    </div>


    <div class="grid gap-6 xl:grid-cols-[410px_minmax(0,1fr)]">

        {{-- New Contract --}}
        @can('manageContracts')

            <div>

                <div class="card overflow-hidden xl:sticky xl:top-6">

                    <div class="border-b border-border bg-background/60 px-5 py-5">

                        <div class="flex items-center gap-3">

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                                <svg class="h-5 w-5"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M8 7h8M8 11h8M8 15h5M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z" />

                                </svg>

                            </div>

                            <div>
                                <h3 class="font-heading text-base font-semibold text-primary">
                                    New Contract
                                </h3>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Contracts begin as drafts before legal review.
                                </p>
                            </div>

                        </div>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('contracts.store') }}"
                        enctype="multipart/form-data"
                        class="space-y-5 p-5">

                        @csrf


                        {{-- Contract Number --}}
                        <div>

                            <label for="contract_number" class="label">
                                Contract Number
                            </label>

                            <input
                                id="contract_number"
                                type="text"
                                name="contract_number"
                                value="{{ old('contract_number') }}"
                                placeholder="e.g. HT-CTR-2026-001"
                                class="input">

                        </div>


                        {{-- Title --}}
                        <div>

                            <label for="title" class="label">
                                Contract Title
                                <span class="text-error">*</span>
                            </label>

                            <input
                                id="title"
                                type="text"
                                name="title"
                                value="{{ old('title') }}"
                                required
                                placeholder="e.g. Hotel Partnership Agreement"
                                class="input @error('title') border-error @enderror">

                            @error('title')
                                <p class="mt-1.5 text-xs font-medium text-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Type --}}
                        <div>

                            <label for="contract_type" class="label">
                                Contract Type
                            </label>

                            <select
                                id="contract_type"
                                name="contract_type"
                                class="input">

                                @foreach ([
                                    'hotel',
                                    'tour_operator',
                                    'transportation',
                                    'supplier',
                                    'partnership',
                                    'service',
                                    'other'
                                ] as $type)

                                    <option
                                        value="{{ $type }}"
                                        @selected(old('contract_type', 'service') === $type)>

                                        {{ str($type)->headline() }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Counterparty --}}
                        <div>

                            <label for="party" class="label">
                                Counterparty / Partner
                            </label>

                            <input
                                id="party"
                                type="text"
                                name="parties[]"
                                value="{{ old('parties.0') }}"
                                placeholder="e.g. Sunrise Hotel Corporation"
                                class="input">

                        </div>


                        {{-- Dates --}}
                        <div class="grid gap-4 sm:grid-cols-2">

                            <div>
                                <label for="start_date" class="label">
                                    Start Date
                                </label>

                                <input
                                    id="start_date"
                                    type="date"
                                    name="start_date"
                                    value="{{ old('start_date') }}"
                                    class="input">
                            </div>


                            <div>
                                <label for="end_date" class="label">
                                    End Date
                                </label>

                                <input
                                    id="end_date"
                                    type="date"
                                    name="end_date"
                                    value="{{ old('end_date') }}"
                                    class="input">

                                @error('end_date')
                                    <p class="mt-1.5 text-xs font-medium text-error">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>


                        {{-- Value --}}
                        <div class="grid grid-cols-[1fr_110px] gap-3">

                            <div>
                                <label for="value" class="label">
                                    Contract Value
                                </label>

                                <input
                                    id="value"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="value"
                                    value="{{ old('value') }}"
                                    placeholder="0.00"
                                    class="input">
                            </div>


                            <div>
                                <label for="currency" class="label">
                                    Currency
                                </label>

                                <input
                                    id="currency"
                                    type="text"
                                    name="currency"
                                    maxlength="8"
                                    value="{{ old('currency', 'PHP') }}"
                                    class="input uppercase">
                            </div>

                        </div>


                        {{-- Officer --}}
                        <div>

                            <label for="responsible_officer_email" class="label">
                                Responsible Officer
                            </label>

                            <input
                                id="responsible_officer_email"
                                type="email"
                                name="responsible_officer_email"
                                value="{{ old('responsible_officer_email', auth()->user()->email) }}"
                                class="input">

                        </div>


                        {{-- Description --}}
                        <div>

                            <label for="description" class="label">
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                placeholder="Briefly describe the purpose and scope of this contract..."
                                class="input">{{ old('description') }}</textarea>

                        </div>


                        {{-- File --}}
                        <div>

                            <label for="file" class="label">
                                Contract File
                            </label>

                            <input
                                id="file"
                                type="file"
                                name="file"
                                class="block w-full rounded-xl border border-border bg-white text-xs text-slate-500 file:mr-3 file:border-0 file:bg-primary/10 file:px-4 file:py-3 file:font-button file:text-xs file:font-semibold file:text-primary hover:file:bg-primary/15">

                            <p class="mt-1.5 text-xs text-slate-400">
                                Maximum file size: 20 MB.
                            </p>

                            @error('file')
                                <p class="mt-1.5 text-xs font-medium text-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        <button type="submit" class="btn-secondary w-full">

                            <svg class="h-4 w-4"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M5 13l4 4L19 7" />

                            </svg>

                            Save Contract Draft

                        </button>

                    </form>

                </div>

            </div>

        @endcan


        {{-- Contract Register --}}
        <div class="min-w-0 space-y-4">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

                <div>
                    <h3 class="font-heading text-lg font-semibold text-primary">
                        Contract Register
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Track each contract through legal review, approval, activation, and renewal.
                    </p>
                </div>


                <form method="GET" action="{{ route('contracts.index') }}">

                    <select
                        name="status"
                        onchange="this.form.submit()"
                        class="input min-w-[190px]">

                        <option value="">
                            All Statuses
                        </option>

                        @foreach ([
                            'draft',
                            'under_review',
                            'pending_approval',
                            'active',
                            'renewed',
                            'expired'
                        ] as $status)

                            <option
                                value="{{ $status }}"
                                @selected(request('status') === $status)>

                                {{ str($status)->headline() }}

                            </option>

                        @endforeach

                    </select>

                </form>

            </div>


            @forelse ($contracts as $contract)

                @php
                    $daysToEnd = $contract->end_date
                        ? now()->startOfDay()->diffInDays($contract->end_date, false)
                        : null;

                    $parties = $contract->parties ?? [];
                @endphp


                <article class="card overflow-hidden">

                    {{-- Contract Header --}}
                    <div class="flex flex-col gap-4 border-b border-border px-5 py-5 sm:flex-row sm:items-start sm:justify-between">

                        <div class="flex min-w-0 items-start gap-3">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">

                                <svg class="h-5 w-5"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M8 7h8M8 11h8M8 15h5M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z" />

                                </svg>

                            </div>


                            <div class="min-w-0">

                                <div class="flex flex-wrap items-center gap-2">

                                    <h4 class="font-heading text-base font-semibold text-primary">
                                        {{ $contract->title }}
                                    </h4>

                                    <span class="rounded-full bg-accent/10 px-2 py-0.5 text-[10px] font-semibold text-primary">
                                        v{{ $contract->version ?? 1 }}
                                    </span>

                                </div>


                                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">

                                    <span>
                                        {{ str($contract->contract_type)->headline() }}
                                    </span>

                                    @if ($contract->contract_number)
                                        <span>•</span>
                                        <span>{{ $contract->contract_number }}</span>
                                    @endif

                                    @if ($contract->file_name)
                                        <span>•</span>
                                        <span class="max-w-[220px] truncate">
                                            {{ $contract->file_name }}
                                        </span>
                                    @endif

                                </div>

                            </div>

                        </div>


                        {{-- Main Status --}}
                        <div>

                            @switch($contract->status)

                                @case('draft')
                                    <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                        Draft
                                    </span>
                                    @break

                                @case('under_review')
                                    <span class="badge badge-info">
                                        Under Review
                                    </span>
                                    @break

                                @case('pending_approval')
                                    <span class="badge badge-warning">
                                        Pending Approval
                                    </span>
                                    @break

                                @case('active')
                                    <span class="badge badge-success">
                                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                        Active
                                    </span>
                                    @break

                                @case('renewed')
                                    <span class="badge badge-info">
                                        Renewed
                                    </span>
                                    @break

                                @case('expired')
                                    <span class="badge badge-error">
                                        Expired
                                    </span>
                                    @break

                                @default
                                    <span class="badge badge-info">
                                        {{ str($contract->status)->headline() }}
                                    </span>

                            @endswitch

                        </div>

                    </div>


                    {{-- Contract Details --}}
                    <div class="grid gap-5 px-5 py-5 sm:grid-cols-2 xl:grid-cols-4">

                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                Counterparty
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-700">
                                {{ count($parties) ? implode(', ', $parties) : 'Not specified' }}
                            </p>
                        </div>


                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                Contract Value
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-700">
                                @if ($contract->value !== null)
                                    {{ $contract->currency ?: 'PHP' }}
                                    {{ number_format((float) $contract->value, 2) }}
                                @else
                                    Not specified
                                @endif
                            </p>
                        </div>


                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                Contract Term
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-700">
                                {{ $contract->start_date?->format('M d, Y') ?? 'No start date' }}
                            </p>

                            <p class="mt-0.5 text-xs text-slate-400">
                                to {{ $contract->end_date?->format('M d, Y') ?? 'No end date' }}
                            </p>
                        </div>


                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                Responsible Officer
                            </p>

                            <p class="mt-1 break-all text-sm font-medium text-slate-700">
                                {{ $contract->responsible_officer_email ?: 'Not assigned' }}
                            </p>
                        </div>

                    </div>


                    {{-- Expiration Alert --}}
                    @if ($daysToEnd !== null && $daysToEnd <= 30)

                        <div @class([
                            'mx-5 mb-5 rounded-xl border px-4 py-3',
                            'border-error/20 bg-error/5' => $daysToEnd < 0,
                            'border-warning/30 bg-warning/5' => $daysToEnd >= 0,
                        ])>

                            @if ($daysToEnd < 0)

                                <p class="text-xs font-semibold text-error">
                                    Contract ended {{ abs($daysToEnd) }} days ago.
                                </p>

                            @elseif ($daysToEnd === 0)

                                <p class="text-xs font-semibold text-error">
                                    Contract ends today.
                                </p>

                            @else

                                <p class="text-xs font-semibold text-amber-700">
                                    Contract expires in {{ $daysToEnd }} days.
                                </p>

                            @endif

                        </div>

                    @endif


                    {{-- Workflow --}}
                    <div class="border-t border-border bg-background/40 px-5 py-4">

                        <div class="grid gap-4 sm:grid-cols-2">

                            {{-- Legal Review --}}
                            <div>

                                <p class="mb-2 text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                    Legal Review
                                </p>

                                @switch($contract->legal_review_status)

                                    @case('approved')
                                        <span class="badge badge-success">
                                            Approved
                                        </span>
                                        @break

                                    @case('objections')
                                        <span class="badge badge-error">
                                            Objections Raised
                                        </span>
                                        @break

                                    @case('pending')
                                        <span class="badge badge-warning">
                                            Pending
                                        </span>
                                        @break

                                    @case('in_review')
                                        <span class="badge badge-info">
                                            In Review
                                        </span>
                                        @break

                                    @default
                                        <span class="badge bg-slate-100 text-slate-500">
                                            Not Submitted
                                        </span>

                                @endswitch

                                @if ($contract->legal_reviewed_by)

                                    <p class="mt-2 text-[10px] text-slate-400">
                                        Reviewed by {{ $contract->legal_reviewed_by }}
                                    </p>

                                @endif

                            </div>


                            {{-- Approval --}}
                            <div>

                                <p class="mb-2 text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                    Management Approval
                                </p>

                                @switch($contract->approval_status)

                                    @case('approved')
                                        <span class="badge badge-success">
                                            Approved
                                        </span>
                                        @break

                                    @case('rejected')
                                        <span class="badge badge-error">
                                            Rejected
                                        </span>
                                        @break

                                    @case('pending')
                                        <span class="badge badge-warning">
                                            Awaiting Decision
                                        </span>
                                        @break

                                    @default
                                        <span class="badge bg-slate-100 text-slate-500">
                                            Not Submitted
                                        </span>

                                @endswitch

                                @if ($contract->approved_by)

                                    <p class="mt-2 text-[10px] text-slate-400">
                                        Decision by {{ $contract->approved_by }}
                                    </p>

                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- Workflow Actions --}}
                    <div class="flex flex-wrap items-end gap-2 border-t border-border px-5 py-4">

                        @can('manageContracts')

                            @if (in_array($contract->status, ['draft', 'renewed']))

                                <form method="POST"
                                      action="{{ route('contracts.submit-review', $contract) }}">

                                    @csrf

                                    <button type="submit" class="btn-secondary text-xs">
                                        Submit for Legal Review
                                    </button>

                                </form>

                            @endif

                        @endcan


                        @can('reviewLegal')

                            @if (in_array($contract->legal_review_status, ['pending', 'in_review']))

                                <form method="POST"
                                      action="{{ route('contracts.legal-review', $contract) }}">

                                    @csrf

                                    <input type="hidden"
                                           name="outcome"
                                           value="approved">

                                    <button type="submit"
                                            class="inline-flex items-center rounded-lg bg-success/10 px-3 py-2 font-button text-xs font-semibold text-success transition hover:bg-success hover:text-white">

                                        Clear Legal Review

                                    </button>

                                </form>


                                <form method="POST"
                                      action="{{ route('contracts.legal-review', $contract) }}"
                                      onsubmit="return confirm('Raise legal objections for this contract?');">

                                    @csrf

                                    <input type="hidden"
                                           name="outcome"
                                           value="objections">

                                    <button type="submit"
                                            class="inline-flex items-center rounded-lg bg-error/10 px-3 py-2 font-button text-xs font-semibold text-error transition hover:bg-error hover:text-white">

                                        Raise Objections

                                    </button>

                                </form>

                            @endif

                        @endcan


                        @can('approveContracts')

                            @if ($contract->approval_status === 'pending')

                                <form method="POST"
                                      action="{{ route('contracts.decide', $contract) }}">

                                    @csrf

                                    <input type="hidden"
                                           name="decision"
                                           value="approve">

                                    <button type="submit"
                                            class="inline-flex items-center rounded-lg bg-success px-3 py-2 font-button text-xs font-semibold text-white transition hover:opacity-90">

                                        Approve Contract

                                    </button>

                                </form>


                                <form method="POST"
                                      action="{{ route('contracts.decide', $contract) }}"
                                      onsubmit="return confirm('Reject this contract and return it to draft?');">

                                    @csrf

                                    <input type="hidden"
                                           name="decision"
                                           value="reject">

                                    <button type="submit"
                                            class="inline-flex items-center rounded-lg border border-error/20 bg-error/5 px-3 py-2 font-button text-xs font-semibold text-error transition hover:bg-error hover:text-white">

                                        Reject

                                    </button>

                                </form>

                            @endif

                        @endcan


                        {{-- Renewal --}}
                        @if ($canRenewContracts && in_array($contract->status, ['active', 'expired', 'renewed']))

                            <form
                                method="POST"
                                action="{{ route('contracts.renew', $contract) }}"
                                class="ml-auto flex flex-wrap items-end gap-2">

                                @csrf

                                <div>
                                    <label class="mb-1 block text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                        New End Date
                                    </label>

                                    <input
                                        type="date"
                                        name="new_end_date"
                                        required
                                        min="{{ now()->addDay()->toDateString() }}"
                                        class="input py-2 text-xs">
                                </div>

                                <button type="submit" class="btn-outline text-xs">
                                    Renew
                                </button>

                            </form>

                        @endif

                    </div>

                </article>

            @empty

                <div class="card px-6 py-16 text-center">

                    <div class="mx-auto flex max-w-sm flex-col items-center">

                        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-accent/10 text-accent">

                            <svg class="h-7 w-7"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M8 7h8M8 11h8M8 15h5M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z" />

                            </svg>

                        </div>

                        <h3 class="font-heading text-base font-semibold text-primary">
                            No contracts found
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Contracts created in the system will appear here.
                        </p>

                    </div>

                </div>

            @endforelse


            @if ($contracts->hasPages())

                <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
                    {{ $contracts->withQueryString()->links() }}
                </div>

            @endif

        </div>

    </div>

</div>

@endsection