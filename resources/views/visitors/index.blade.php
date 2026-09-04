@extends('layouts.app')

@section('title', 'Visitor Desk')

@section('content')

<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Visitor Desk
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Register visitors, monitor arrivals, and manage check-in and check-out activity.
            </p>
        </div>


        <div class="rounded-xl border border-border bg-card px-4 py-2.5 shadow-card">

            <p class="text-xs text-slate-500">
                Visitor Records
            </p>

            <p class="mt-0.5 font-heading text-lg font-bold text-primary">
                {{ $visitors->total() }}
            </p>

        </div>

    </div>


    @can('useAiAssist')
    {{-- AI Visitor Assistant --}}
    @php
        $aiProviderConfigured =
            config('services.ai_assist.provider', 'none') !== 'none'
            && filled(config('services.ai_assist.api_key'))
            && filled(config('services.ai_assist.endpoint'));
    @endphp

    <section
        data-ai-visitor-assistant
        data-ai-endpoint="{{ route('visitors.ai-assist') }}"
        data-ai-csrf="{{ csrf_token() }}"
        class="overflow-hidden rounded-2xl border border-accent/30 bg-card shadow-card">

        {{-- AI Header --}}
        <div class="flex flex-col gap-4 border-b border-border bg-gradient-to-r from-accent/10 via-white to-secondary/5 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

            <div class="flex items-center gap-3">

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary text-white shadow-sm">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 3v4M3 5h4M19 17v4M17 19h4M12 3l1.09 3.26L16 7.35l-2.91 1.09L12 12l-1.09-3.56L8 7.35l2.91-1.09L12 3zM7 12l1.17 3.33L11.5 16.5l-3.33 1.17L7 21l-1.17-3.33L2.5 16.5l3.33-1.17L7 12z" />

                    </svg>

                </div>


                <div>

                    <div class="flex flex-wrap items-center gap-2">

                        <h3 class="font-heading text-base font-semibold text-primary">
                            AI Visitor Assistant
                        </h3>

                        <span class="rounded-full bg-primary/10 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-primary">
                            Human Reviewed
                        </span>

                    </div>

                    <p class="mt-1 text-xs text-slate-500">
                        Extract, summarize, and review visitor information before registration.
                    </p>

                </div>

            </div>


            @if ($aiProviderConfigured)

                <div class="inline-flex w-fit items-center gap-2 rounded-full bg-success/10 px-3 py-1.5 text-xs font-semibold text-success">

                    <span class="h-2 w-2 rounded-full bg-success"></span>

                    AI Provider Connected

                </div>

            @else

                <div class="inline-flex w-fit items-center gap-2 rounded-full bg-warning/10 px-3 py-1.5 text-xs font-semibold text-amber-700">

                    <span class="h-2 w-2 rounded-full bg-warning"></span>

                    Safe Fallback Mode

                </div>

            @endif


            <button
                type="button"
                data-ai-panel-toggle
                aria-expanded="false"
                class="btn-outline shrink-0">

                <svg
                    data-ai-toggle-icon
                    class="h-4 w-4 transition-transform duration-200"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7" />

                </svg>

                <span data-ai-toggle-text>
                    Open Assistant
                </span>

            </button>

        </div>


        <div
            data-ai-panel-body
            class="hidden grid lg:grid-cols-2">

            {{-- AI Input --}}
            <div class="border-b border-border p-6 lg:border-b-0 lg:border-r">

                <label
                    for="ai_visitor_text"
                    class="label">

                    Visitor Information

                </label>


                <textarea
                    id="ai_visitor_text"
                    data-ai-input
                    rows="6"
                    class="input"
                    placeholder="Example: Juan Dela Cruz from ABC Travel is here to meet Maria Santos regarding a supplier agreement."></textarea>


                <p class="mt-2 text-xs leading-relaxed text-slate-400">
                    Enter information provided by the visitor. AI-generated results are suggestions only and must be reviewed by staff.
                </p>


                {{-- AI Actions --}}
                <div class="mt-4 flex flex-wrap gap-2">

                    <button
                        type="button"
                        data-ai-mode="extract"
                        class="btn-secondary">

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6M9 16h6M9 8h3M5 3h10l4 4v14H5V3z" />

                        </svg>

                        Extract Details

                    </button>


                    <button
                        type="button"
                        data-ai-mode="summary"
                        class="btn-outline">

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h10" />

                        </svg>

                        Summarize

                    </button>


                    <button
                        type="button"
                        data-ai-mode="appointment_check"
                        class="btn-outline">

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 7V3M16 7V3M4 11h16M9 16l2 2 4-4M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

                        </svg>

                        Check Appointment

                    </button>

                </div>


                {{-- Safety Message --}}
                <div class="mt-5 flex gap-3 rounded-xl border border-accent/20 bg-accent/5 p-4">

                    <svg
                        class="mt-0.5 h-4 w-4 shrink-0 text-accent"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                    </svg>

                    <p class="text-xs leading-relaxed text-slate-500">
                        The assistant cannot approve, reject, check in, or grant access to visitors. Staff remain responsible for all final decisions.
                    </p>

                </div>

            </div>


            {{-- AI Result --}}
            <div class="relative min-h-[280px] p-6">

                <div class="mb-4 flex items-center justify-between gap-3">

                    <div>

                        <h4 class="font-heading text-sm font-semibold text-primary">
                            Assistant Suggestion
                        </h4>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Review the result before applying anything.
                        </p>

                    </div>


                    <span
                        data-ai-status
                        class="hidden rounded-full bg-accent/10 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-primary">
                    </span>

                </div>


                {{-- Empty Result --}}
                <div
                    data-ai-empty
                    class="flex min-h-[180px] flex-col items-center justify-center text-center">

                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-accent/10 text-accent">

                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6V4m0 16v-2m8-6h-2M6 12H4m13.657-5.657l-1.414 1.414M7.757 16.243l-1.414 1.414m11.314 0l-1.414-1.414M7.757 7.757L6.343 6.343M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                        </svg>

                    </div>

                    <p class="font-button text-sm font-medium text-primary">
                        Ready to assist
                    </p>

                    <p class="mt-1 max-w-xs text-xs leading-relaxed text-slate-500">
                        Enter visitor information and select one of the assistance options.
                    </p>

                </div>


                {{-- Loading --}}
                <div
                    data-ai-loading
                    class="hidden min-h-[180px] flex-col items-center justify-center text-center">

                    <svg
                        class="mb-3 h-7 w-7 animate-spin text-secondary"
                        fill="none"
                        viewBox="0 0 24 24">

                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4">
                        </circle>

                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                        </path>

                    </svg>

                    <p class="font-button text-sm font-medium text-primary">
                        Analyzing visitor information...
                    </p>

                </div>


                {{-- Result Content --}}
                <div
                    data-ai-result
                    class="hidden space-y-4">
                </div>


                @can('operateVisitorDesk')
                {{-- Apply --}}
                <div
                    data-ai-apply-wrap
                    class="mt-5 hidden border-t border-border pt-4">

                    <button
                        type="button"
                        data-ai-apply
                        class="btn-primary w-full">

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

                        Apply Suggested Fields

                    </button>

                    <p class="mt-2 text-center text-[10px] leading-relaxed text-slate-400">
                        Applying a suggestion only fills the registration form. It does not submit or approve the visitor.
                    </p>

                </div>
                @endcan

            </div>

        </div>

    </section>
    @endcan


    {{-- Main Layout --}}
    <div @class([
        'grid gap-6',
        'xl:grid-cols-[370px_minmax(0,1fr)]' => auth()->user()->can('operateVisitorDesk'),
    ])>

        @can('operateVisitorDesk')
        {{-- Log Visitor --}}
        <div>

            <div class="card overflow-hidden xl:sticky xl:top-6">

                {{-- Form Header --}}
                <div class="border-b border-border bg-background/60 px-5 py-5">

                    <div class="flex items-center gap-3">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8zM19 8v6M22 11h-6" />

                            </svg>

                        </div>


                        <div>

                            <h3 class="font-heading text-base font-semibold text-primary">
                                Log a Visitor
                            </h3>

                            <p class="mt-0.5 text-xs text-slate-500">
                                Register a walk-in or arriving visitor.
                            </p>

                        </div>

                    </div>

                </div>


                <form
                    method="POST"
                    action="{{ route('visitors.store') }}"
                    class="space-y-5 p-5">

                    @csrf


                    {{-- Full Name --}}
                    <div>

                        <label for="full_name" class="label">
                            Full Name
                            <span class="text-error">*</span>
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
                                        d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8z" />

                                </svg>

                            </div>


                            <input
                                id="full_name"
                                type="text"
                                name="full_name"
                                value="{{ old('full_name') }}"
                                required
                                placeholder="Visitor's complete name"
                                class="input pl-10 @error('full_name') border-error focus:border-error focus:ring-error/20 @enderror">

                        </div>

                        @error('full_name')
                            <p class="mt-1.5 text-xs font-medium text-error">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Organization --}}
                    <div>

                        <label for="organization" class="label">
                            Organization
                        </label>

                        <input
                            id="organization"
                            type="text"
                            name="organization"
                            value="{{ old('organization') }}"
                            placeholder="Company or organization"
                            class="input @error('organization') border-error focus:border-error focus:ring-error/20 @enderror">

                        @error('organization')
                            <p class="mt-1.5 text-xs font-medium text-error">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Visitor Type --}}
                    <div>

                        <label for="visitor_type" class="label">
                            Visitor Type
                        </label>

                        <select
                            id="visitor_type"
                            name="visitor_type"
                            class="input @error('visitor_type') border-error focus:border-error focus:ring-error/20 @enderror">

                            @foreach ([
                                'customer',
                                'business_partner',
                                'supplier',
                                'government',
                                'applicant',
                                'guest',
                                'other'
                            ] as $type)

                                <option
                                    value="{{ $type }}"
                                    @selected(old('visitor_type', 'guest') === $type)>

                                    {{ str($type)->headline() }}

                                </option>

                            @endforeach

                        </select>

                        @error('visitor_type')
                            <p class="mt-1.5 text-xs font-medium text-error">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Host Email --}}
                    <div>

                        <label for="host_email" class="label">
                            Host Email
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
                                id="host_email"
                                type="email"
                                name="host_email"
                                value="{{ old('host_email') }}"
                                placeholder="host@example.com"
                                class="input pl-10 @error('host_email') border-error focus:border-error focus:ring-error/20 @enderror">

                        </div>

                        @error('host_email')
                            <p class="mt-1.5 text-xs font-medium text-error">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Purpose --}}
                    <div>

                        <label for="purpose" class="label">
                            Purpose of Visit
                        </label>

                        <textarea
                            id="purpose"
                            name="purpose"
                            rows="3"
                            placeholder="Briefly describe the purpose of the visit..."
                            class="input @error('purpose') border-error focus:border-error focus:ring-error/20 @enderror">{{ old('purpose') }}</textarea>

                        @error('purpose')
                            <p class="mt-1.5 text-xs font-medium text-error">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Walk-in --}}
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-border bg-background/60 p-4 transition hover:border-accent/40">

                        <input
                            type="checkbox"
                            name="is_walk_in"
                            value="1"
                            @checked(old('is_walk_in', true))
                            class="mt-0.5 h-4 w-4 rounded border-slate-300 text-secondary focus:ring-secondary">

                        <span>

                            <span class="block font-button text-sm font-medium text-primary">
                                Walk-in Visitor
                            </span>

                            <span class="mt-0.5 block text-xs leading-relaxed text-slate-500">
                                Select this when the visitor does not have a prior appointment.
                            </span>

                        </span>

                    </label>


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

                        Log Visitor

                    </button>

                </form>

            </div>

        </div>
        @endcan


        {{-- Visitor Records --}}
        <div class="min-w-0 space-y-4">

            {{-- Heading --}}
            <div>

                <h3 class="font-heading text-lg font-semibold text-primary">
                    Visitor Activity
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Monitor expected visitors, active check-ins, and completed visits.
                </p>

            </div>


            {{-- Filters --}}
            <div class="flex flex-wrap gap-2">

                @foreach ([
                    '' => 'All',
                    'expected' => 'Expected',
                    'checked_in' => 'Checked In',
                    'completed' => 'Completed'
                ] as $value => $label)

                    <a
                        href="{{ route('visitors.index', array_filter(['status' => $value])) }}"
                        class="{{ request('status', '') === $value
                            ? 'border-primary bg-primary text-white shadow-sm'
                            : 'border-border bg-white text-slate-600 hover:border-accent hover:bg-accent/5 hover:text-primary'
                        }} inline-flex items-center rounded-full border px-4 py-2 font-button text-xs font-medium transition">

                        {{ $label }}

                    </a>

                @endforeach

            </div>


            {{-- Table --}}
            <div class="table-shell">

                <div class="overflow-x-auto">

                    <table class="min-w-full text-left text-sm">

                        <thead class="table-header">

                            <tr>

                                <th class="px-5 py-4 font-medium">
                                    Visitor
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Host
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Status
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Check-in
                                </th>

                                <th class="px-5 py-4 text-right font-medium">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-border bg-card">

                            @forelse ($visitors as $visitor)

                                <tr class="transition-colors hover:bg-sky-50/40">

                                    {{-- Visitor --}}
                                    <td class="px-5 py-4">

                                        <div class="flex items-center gap-3">

                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-accent/10 font-button text-sm font-bold uppercase text-primary">

                                                {{ \Illuminate\Support\Str::substr($visitor->full_name, 0, 1) }}

                                            </div>


                                            <div class="min-w-0">

                                                <p class="max-w-[180px] truncate font-button text-sm font-semibold text-primary">
                                                    {{ $visitor->full_name }}
                                                </p>


                                                @if ($visitor->organization)

                                                    <p class="mt-0.5 max-w-[180px] truncate text-xs text-slate-400">
                                                        {{ $visitor->organization }}
                                                    </p>

                                                @elseif ($visitor->visitor_type)

                                                    <p class="mt-0.5 text-xs text-slate-400">
                                                        {{ str($visitor->visitor_type)->headline() }}
                                                    </p>

                                                @endif

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Host --}}
                                    <td class="px-5 py-4">

                                        <div class="flex items-center gap-2 text-slate-600">

                                            <svg
                                                class="h-4 w-4 shrink-0 text-slate-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24">

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8z" />

                                            </svg>

                                            <span class="max-w-[180px] truncate">
                                                {{ $visitor->host_name ?: ($visitor->host_email ?: 'Not assigned') }}
                                            </span>

                                        </div>

                                    </td>


                                    {{-- Status --}}
                                    <td class="px-5 py-4">

                                        @switch($visitor->status)

                                            @case('expected')

                                                <span class="badge badge-info">
                                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-accent"></span>
                                                    Expected
                                                </span>

                                                @break


                                            @case('awaiting_host')

                                                <span class="badge badge-warning">
                                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-warning"></span>
                                                    Awaiting Host
                                                </span>

                                                @break


                                            @case('checked_in')

                                                <span class="badge badge-success">
                                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                                    Checked In
                                                </span>

                                                @break


                                            @case('completed')

                                                <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                                    Completed
                                                </span>

                                                @break


                                            @case('declined')

                                                <span class="badge badge-error">
                                                    Declined
                                                </span>

                                                @break


                                            @default

                                                <span class="badge badge-info">
                                                    {{ str($visitor->status)->headline() }}
                                                </span>

                                        @endswitch

                                    </td>


                                    {{-- Check-in --}}
                                    <td class="px-5 py-4">

                                        @if ($visitor->check_in_at)

                                            <div class="flex items-center gap-2 text-slate-600">

                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-success/10 text-success">

                                                    <svg
                                                        class="h-4 w-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24">

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                                                    </svg>

                                                </div>

                                                <div>

                                                    <p class="text-sm font-medium text-slate-700">
                                                        {{ $visitor->check_in_at->format('h:i A') }}
                                                    </p>

                                                    <p class="text-[10px] text-slate-400">
                                                        {{ $visitor->check_in_at->format('M d, Y') }}
                                                    </p>

                                                </div>

                                            </div>

                                        @else

                                            <span class="text-xs text-slate-400">
                                                Not checked in
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Actions --}}
                                    <td class="px-5 py-4 text-right">

                                        <div class="flex items-center justify-end gap-2">

                                            @can('operateVisitorDesk')

                                                @if (in_array($visitor->status, ['expected', 'awaiting_host']))

                                                    <form
                                                        method="POST"
                                                        action="{{ route('visitors.check-in', $visitor) }}">

                                                        @csrf

                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center gap-1.5 rounded-lg bg-success/10 px-3 py-2 font-button text-xs font-semibold text-success transition hover:bg-success hover:text-white">

                                                            <svg
                                                                class="h-3.5 w-3.5"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24">

                                                                <path
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M5 13l4 4L19 7" />

                                                            </svg>

                                                            Check In

                                                        </button>

                                                    </form>

                                                    <form
                                                        method="POST"
                                                        action="{{ route('visitors.decline', $visitor) }}"
                                                        onsubmit="return confirm('Decline this visitor?');">

                                                        @csrf

                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center gap-1.5 rounded-lg bg-error/10 px-3 py-2 font-button text-xs font-semibold text-error transition hover:bg-error hover:text-white">

                                                            <svg
                                                                class="h-3.5 w-3.5"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24">

                                                                <path
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M6 18L18 6M6 6l12 12" />

                                                            </svg>

                                                            Decline

                                                        </button>

                                                    </form>

                                                @endif


                                                @if ($visitor->status === 'checked_in')

                                                    <form
                                                        method="POST"
                                                        action="{{ route('visitors.check-out', $visitor) }}"
                                                        onsubmit="return confirm('Check out this visitor?');">

                                                        @csrf

                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-white px-3 py-2 font-button text-xs font-medium text-primary transition hover:border-secondary hover:bg-secondary/5 hover:text-secondary">

                                                            <svg
                                                                class="h-3.5 w-3.5"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24">

                                                                <path
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />

                                                            </svg>

                                                            Check Out

                                                        </button>

                                                    </form>

                                                @endif

                                            @endcan


                                            @if (
                                                !in_array($visitor->status, ['expected', 'awaiting_host', 'checked_in']) ||
                                                !auth()->user()->can('operateVisitorDesk')
                                            )

                                                <span class="text-xs text-slate-400">
                                                    —
                                                </span>

                                            @endif

                                        </div>

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
                                                        d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8z" />

                                                </svg>

                                            </div>


                                            <h3 class="font-heading text-base font-semibold text-primary">
                                                No visitors found
                                            </h3>

                                            <p class="mt-1 text-sm text-slate-500">

                                                @if (request('status'))

                                                    No visitors match the selected status.

                                                @else

                                                    Visitor records will appear here once registered.

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


            {{-- Pagination --}}
            @if ($visitors->hasPages())

                <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
                    {{ $visitors->withQueryString()->links() }}
                </div>

            @endif

        </div>

    </div>

</div>

@endsection