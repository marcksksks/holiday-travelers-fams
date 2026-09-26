@extends('layouts.app')

@section('title', 'Contracts')

@section('content')

@php
    $canRenewContracts = auth()->user()->can('manageContracts');
@endphp

<div class="space-y-4">

    {{-- =====================================================
         CONTRACT MANAGEMENT HEADER
    ====================================================== --}}
    <x-page-header

        title="Contract Management"

        description="Manage contract workflows, terms, approvals, and renewals.">

        @can('manageContracts')

            <x-slot:actions>

                <button
                    type="button"
                    onclick="document.getElementById('createContractDialog').showModal()"
                    class="btn-primary inline-flex items-center justify-center gap-2">

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4" />

                    </svg>

                    New Contract

                </button>

            </x-slot:actions>

        @endcan

    </x-page-header>


    {{-- =====================================================
         CONTRACT PORTFOLIO OVERVIEW
    ====================================================== --}}
    <section class="space-y-3">

        <x-section-header title="Contract Portfolio" />


        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

            <x-metric-card
                label="Total Contracts"
                :show-action="false"
                :value="number_format($contractStats['total'])"
                :href="route('contracts.index')"
                helper="Recorded contracts."
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
                            d="M8 7h8M8 11h8M8 15h5M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Active Contracts"
                :show-action="false"
                :value="number_format($contractStats['active'])"
                :href="route('contracts.index', ['status' => 'active'])"
                helper="Currently active."
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
                label="Workflow Review"
                :show-action="false"
                :value="number_format($contractStats['workflow'])"
                helper="Awaiting workflow action."
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
                            d="M12 8v4m0 4h.01M4 6h16v14H4V6zm4-3v3m8-3v3" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Renewal Attention"
                :show-action="false"
                :value="number_format($contractStats['renewal_attention'])"
                :href="route('contracts.index', ['deadline' => 'attention'])"
                helper="Near or past end date."
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
                            d="M12 8v4l3 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>

        </div>

    </section>


    <div class="space-y-4">

        @can('manageContracts')

            @include('contracts._create-modal')

        @endcan

        {{-- Contract Register --}}
        <div class="min-w-0 space-y-3">

            <x-section-header title="Contract Register" />


            <div class="card p-3">

                <form
                    method="GET"
                    action="{{ route('contracts.index') }}"
                    data-contract-filter-bar
                    class="grid gap-2 lg:grid-cols-[minmax(0,1fr)_210px_210px_auto]">

                    <div class="relative">

                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />

                            </svg>

                        </div>

                        <input
                            type="search"
                            name="q"
                            maxlength="120"
                            value="{{ $search }}"
                            placeholder="Search contracts..."
                            class="input w-full pl-10">

                    </div>


                    <select
                        name="status"
                        class="input">

                        <option value="">
                            All workflow statuses
                        </option>

                        @foreach ([
                            'draft',
                            'under_review',
                            'pending_approval',
                            'active',
                            'renewed',
                            'expired',
                        ] as $value)

                            <option
                                value="{{ $value }}"
                                @selected($status === $value)>

                                {{ str($value)->headline() }}

                            </option>

                        @endforeach

                    </select>


                    <select
                        name="deadline"
                        class="input">

                        <option value="">
                            All contract terms
                        </option>

                        <option
                            value="attention"
                            @selected($deadline === 'attention')>

                            Renewal attention

                        </option>


                        <option
                            value="due_soon"
                            @selected($deadline === 'due_soon')>

                            Ending within 30 days

                        </option>

                        <option
                            value="expired"
                            @selected($deadline === 'expired')>

                            End date passed

                        </option>

                        <option
                            value="open_ended"
                            @selected($deadline === 'open_ended')>

                            No end date

                        </option>

                    </select>


                    <div class="flex gap-2">

                        <button
                            type="submit"
                            class="btn-primary flex-1 justify-center">

                            Apply

                        </button>


                        @if (
                            $search !== ''
                            ||
                            $status !== ''
                            ||
                            $deadline !== ''
                        )

                            <a
                                href="{{ route('contracts.index') }}"
                                class="btn-outline justify-center">

                                Clear

                            </a>

                        @endif

                    </div>

                </form>


                @if (
                    $search !== ''
                    ||
                    $status !== ''
                    ||
                    $deadline !== ''
                )

                    <div class="mt-2 border-t border-border pt-2 text-[11px] text-slate-500">

                        <span class="font-semibold text-primary">
                            {{ number_format($contracts->total()) }}
                        </span>

                        matching
                        {{ $contracts->total() === 1 ? 'contract' : 'contracts' }}

                    </div>

                @endif

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
                        <div class="flex flex-wrap items-center justify-end gap-2">

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
                                        Expired Workflow
                                    </span>

                                    @break


                                @default

                                    <span class="badge badge-info">
                                        {{ str($contract->status)->headline() }}
                                    </span>

                            @endswitch


                            @if ($daysToEnd !== null && $daysToEnd < 0)

                                <span class="badge badge-error">
                                    End Date Passed
                                </span>


                            @elseif ($daysToEnd === 0)

                                <span class="badge badge-error">
                                    Ends Today
                                </span>


                            @elseif ($daysToEnd !== null && $daysToEnd <= 30)

                                <span class="badge badge-warning">
                                    Ending Soon
                                </span>

                            @endif

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
                                    End date passed {{ abs($daysToEnd) }} days ago.
                                </p>

                            @elseif ($daysToEnd === 0)

                                <p class="text-xs font-semibold text-error">
                                    The recorded contract end date is today.
                                </p>

                            @else

                                <p class="text-xs font-semibold text-amber-700">
                                    Recorded end date is in {{ $daysToEnd }} days.
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

                            <a
                                href="{{ route('contracts.edit', $contract) }}"
                                class="btn-outline text-xs">

                                Edit Contract

                            </a>


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