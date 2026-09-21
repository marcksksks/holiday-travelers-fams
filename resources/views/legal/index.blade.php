@extends('layouts.app')

@section('title', 'Legal Records')

@section('content')

<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Legal Records
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Manage permits, licenses, legal requirements, cases, and compliance-related records.
            </p>
        </div>


        <div class="rounded-xl border border-border bg-card px-4 py-2.5 shadow-card">

            <p class="text-xs text-slate-500">
                Total Records
            </p>

            <p class="mt-0.5 font-heading text-lg font-bold text-primary">
                {{ $records->total() }}
            </p>

        </div>

    </div>


    <div class="grid gap-6 xl:grid-cols-[410px_minmax(0,1fr)]">

        {{-- Add Legal Record --}}
        @can('manageLegal')

            <div>

                <div class="card overflow-hidden xl:sticky xl:top-6">

                    {{-- Form Header --}}
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
                                        d="M9 12h6M9 16h6M9 8h3M6 3h9l3 3v15H6V3z" />

                                </svg>

                            </div>


                            <div>

                                <h3 class="font-heading text-base font-semibold text-primary">
                                    Add Legal Record
                                </h3>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Register a legal, regulatory, or compliance record.
                                </p>

                            </div>

                        </div>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('legal.store') }}"
                        enctype="multipart/form-data"
                        class="space-y-5 p-5">

                        @csrf


                        {{-- Title --}}
                        <div>

                            <label for="title" class="label">
                                Record Title
                                <span class="text-error">*</span>
                            </label>

                            <input
                                id="title"
                                type="text"
                                name="title"
                                value="{{ old('title') }}"
                                required
                                placeholder="e.g. Business Permit 2026"
                                class="input @error('title') border-error @enderror">

                            @error('title')
                                <p class="mt-1.5 text-xs font-medium text-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Type --}}
                        <div>

                            <label for="record_type" class="label">
                                Record Type
                                <span class="text-error">*</span>
                            </label>

                            <select
                                id="record_type"
                                name="record_type"
                                class="input">

                                @foreach ([
                                    'permit',
                                    'license',
                                    'legal_case',
                                    'requirement',
                                    'legal_document'
                                ] as $type)

                                    <option
                                        value="{{ $type }}"
                                        @selected(old('record_type', 'permit') === $type)>

                                        {{ str($type)->headline() }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Reference --}}
                        <div>

                            <label for="reference_number" class="label">
                                Reference Number
                            </label>

                            <input
                                id="reference_number"
                                type="text"
                                name="reference_number"
                                value="{{ old('reference_number') }}"
                                placeholder="e.g. BP-2026-00125"
                                class="input">

                        </div>


                        {{-- Authority --}}
                        <div>

                            <label for="issuing_authority" class="label">
                                Issuing Authority
                            </label>

                            <input
                                id="issuing_authority"
                                type="text"
                                name="issuing_authority"
                                value="{{ old('issuing_authority') }}"
                                placeholder="e.g. City Government"
                                class="input">

                        </div>


                        {{-- Dates --}}
                        <div class="grid gap-4 sm:grid-cols-2">

                            <div>

                                <label for="issue_date" class="label">
                                    Issue Date
                                </label>

                                <input
                                    id="issue_date"
                                    type="date"
                                    name="issue_date"
                                    value="{{ old('issue_date') }}"
                                    class="input">

                            </div>


                            <div>

                                <label for="expiration_date" class="label">
                                    Expiration Date
                                </label>

                                <input
                                    id="expiration_date"
                                    type="date"
                                    name="expiration_date"
                                    value="{{ old('expiration_date') }}"
                                    class="input">

                                @error('expiration_date')
                                    <p class="mt-1.5 text-xs font-medium text-error">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                        </div>


                        {{-- Responsible Officer --}}
                        <div>

                            <label for="responsible_officer_email" class="label">
                                Responsible Officer
                            </label>

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
                                            d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />

                                    </svg>

                                </div>

                                <input
                                    id="responsible_officer_email"
                                    type="email"
                                    name="responsible_officer_email"
                                    value="{{ old('responsible_officer_email') }}"
                                    placeholder="officer@example.com"
                                    class="input pl-10">

                            </div>

                        </div>


                        {{-- Status --}}
                        <div>

                            <label for="status" class="label">
                                Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                class="input">

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
                                        @selected(old('status', 'active') === $status)>

                                        {{ str($status)->headline() }}

                                    </option>

                                @endforeach

                            </select>

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
                                placeholder="Brief description of this legal record..."
                                class="input">{{ old('description') }}</textarea>

                        </div>


                        {{-- Legal Notes --}}
                        <div>

                            <label for="legal_notes" class="label">
                                Legal Notes
                            </label>

                            <textarea
                                id="legal_notes"
                                name="legal_notes"
                                rows="3"
                                placeholder="Important legal observations, obligations, or follow-up actions..."
                                class="input">{{ old('legal_notes') }}</textarea>

                        </div>


                        {{-- File --}}
                        <div>

                            <label for="file" class="label">
                                Supporting File
                            </label>

                            <input
                                id="file"
                                type="file"
                                name="file"
                                accept="{{ \App\Support\DocumentUploadPolicy::acceptAttribute() }}"
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


                        <button
                            type="submit"
                            class="btn-secondary w-full">

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

                            Save Legal Record

                        </button>

                    </form>

                </div>

            </div>

        @endcan


        {{-- Legal Records List --}}
        <div class="min-w-0 space-y-4">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

                <div>

                    <h3 class="font-heading text-lg font-semibold text-primary">
                        Legal Record Register
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Monitor legal status, expiration dates, and review requirements.
                    </p>

                </div>


                {{-- Status Filter --}}
                <form method="GET" action="{{ route('legal.index') }}">

                    <select
                        name="status"
                        onchange="this.form.submit()"
                        class="input min-w-[190px]">

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
                                @selected(request('status') === $status)>

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
                                    Authority
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Expiration
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Status
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Review
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-border bg-card">

                            @forelse ($records as $record)

                                @php
                                    $daysToExpiry = $record->expiration_date
                                        ? now()->startOfDay()->diffInDays($record->expiration_date, false)
                                        : null;
                                @endphp


                                <tr class="transition hover:bg-sky-50/40">

                                    {{-- Record --}}
                                    <td class="px-5 py-4">

                                        <div class="flex items-center gap-3">

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
                                                        d="M9 12h6M9 16h6M9 8h3M6 3h9l3 3v15H6V3z" />

                                                </svg>

                                            </div>


                                            <div class="min-w-0">

                                                <p class="max-w-[220px] truncate font-button text-sm font-semibold text-primary">
                                                    {{ $record->title }}
                                                </p>


                                                <div class="mt-1 flex flex-wrap items-center gap-2">

                                                    <span class="rounded-full bg-primary/5 px-2 py-0.5 text-[10px] font-medium text-primary">
                                                        {{ str($record->record_type)->headline() }}
                                                    </span>


                                                    @if ($record->reference_number)

                                                        <span class="text-[10px] text-slate-400">
                                                            #{{ $record->reference_number }}
                                                        </span>

                                                    @endif

                                                </div>


                                                @if ($record->file_name)

                                                    <p class="mt-1 max-w-[220px] truncate text-[10px] text-slate-400">
                                                        File: {{ $record->file_name }}
                                                    </p>

                                                @endif


                                                @can('manageLegal')

                                                    <a
                                                        href="{{ route('legal.edit', $record) }}"
                                                        class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-primary transition hover:text-secondary">

                                                        Edit Record

                                                        <svg
                                                            class="h-3 w-3"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24">

                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.4-9.4a2 2 0 112.8 2.8L11.8 13H9v-2.8l6.6-6.6z" />

                                                        </svg>

                                                    </a>

                                                @endcan

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Authority --}}
                                    <td class="px-5 py-4">

                                        <p class="max-w-[180px] text-sm text-slate-600">
                                            {{ $record->issuing_authority ?: 'Not specified' }}
                                        </p>


                                        @if ($record->responsible_officer_email)

                                            <p class="mt-1 max-w-[180px] truncate text-[10px] text-slate-400">
                                                {{ $record->responsible_officer_email }}
                                            </p>

                                        @endif

                                    </td>


                                    {{-- Expiration --}}
                                    <td class="px-5 py-4">

                                        @if ($record->expiration_date)

                                            <p @class([
                                                'text-sm font-medium',
                                                'text-error' => $daysToExpiry !== null && $daysToExpiry < 0,
                                                'text-amber-600' => $daysToExpiry !== null && $daysToExpiry >= 0 && $daysToExpiry <= 30,
                                                'text-slate-700' => $daysToExpiry === null || $daysToExpiry > 30,
                                            ])>

                                                {{ $record->expiration_date->format('M d, Y') }}

                                            </p>


                                            @if ($daysToExpiry !== null && $daysToExpiry < 0)

                                                <p class="mt-1 text-[10px] font-medium text-error">
                                                    Expired {{ abs($daysToExpiry) }} days ago
                                                </p>

                                            @elseif ($daysToExpiry !== null && $daysToExpiry === 0)

                                                <p class="mt-1 text-[10px] font-medium text-error">
                                                    Expires today
                                                </p>

                                            @elseif ($daysToExpiry !== null && $daysToExpiry <= 30)

                                                <p class="mt-1 text-[10px] font-medium text-amber-600">
                                                    {{ $daysToExpiry }} days remaining
                                                </p>

                                            @endif

                                        @else

                                            <span class="text-xs text-slate-400">
                                                No expiration
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Status --}}
                                    <td class="px-5 py-4">

                                        @switch($record->status)

                                            @case('active')

                                                <span class="badge badge-success">
                                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                                    Active
                                                </span>

                                                @break


                                            @case('pending')

                                                <span class="badge badge-warning">
                                                    Pending
                                                </span>

                                                @break


                                            @case('expiring_soon')

                                                <span class="badge badge-warning">
                                                    Expiring Soon
                                                </span>

                                                @break


                                            @case('expired')

                                                <span class="badge badge-error">
                                                    Expired
                                                </span>

                                                @break


                                            @case('renewed')

                                                <span class="badge badge-info">
                                                    Renewed
                                                </span>

                                                @break


                                            @case('closed')

                                                <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                                    Closed
                                                </span>

                                                @break


                                            @default

                                                <span class="badge badge-info">
                                                    {{ str($record->status)->headline() }}
                                                </span>

                                        @endswitch

                                    </td>


                                    {{-- Review --}}
                                    <td class="px-5 py-4">

                                        @can('reviewLegal')

                                            <form
                                                method="POST"
                                                action="{{ route('legal.review', $record) }}"
                                                class="min-w-[150px]">

                                                @csrf

                                                <select
                                                    name="review_status"
                                                    onchange="this.form.submit()"
                                                    @class([
                                                        'input py-2 text-xs font-medium',

                                                        'border-slate-200 bg-slate-50 text-slate-600'
                                                            => $record->review_status === 'not_reviewed',

                                                        'border-accent/30 bg-accent/5 text-primary'
                                                            => $record->review_status === 'in_review',

                                                        'border-success/30 bg-success/5 text-success'
                                                            => $record->review_status === 'reviewed',

                                                        'border-error/30 bg-error/5 text-error'
                                                            => $record->review_status === 'action_required',
                                                    ])>

                                                    @foreach ([
                                                        'not_reviewed',
                                                        'in_review',
                                                        'reviewed',
                                                        'action_required'
                                                    ] as $reviewStatus)

                                                        <option
                                                            value="{{ $reviewStatus }}"
                                                            @selected($record->review_status === $reviewStatus)>

                                                            {{ str($reviewStatus)->headline() }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </form>

                                        @else

                                            @switch($record->review_status)

                                                @case('reviewed')

                                                    <span class="badge badge-success">
                                                        Reviewed
                                                    </span>

                                                    @break


                                                @case('action_required')

                                                    <span class="badge badge-error">
                                                        Action Required
                                                    </span>

                                                    @break


                                                @case('in_review')

                                                    <span class="badge badge-info">
                                                        In Review
                                                    </span>

                                                    @break


                                                @default

                                                    <span class="badge bg-slate-100 text-slate-600">
                                                        Not Reviewed
                                                    </span>

                                            @endswitch

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
                                                        d="M9 12h6M9 16h6M9 8h3M6 3h9l3 3v15H6V3z" />

                                                </svg>

                                            </div>


                                            <h3 class="font-heading text-base font-semibold text-primary">
                                                No legal records found
                                            </h3>

                                            <p class="mt-1 text-sm text-slate-500">

                                                @if (request('status'))

                                                    No records match the selected status.

                                                @else

                                                    Legal and compliance records will appear here.

                                                @endif

                                            </p>

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            @if ($records->hasPages())

                <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
                    {{ $records->withQueryString()->links() }}
                </div>

            @endif

        </div>

    </div>

</div>

@endsection