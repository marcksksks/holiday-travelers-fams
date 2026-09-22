@extends('layouts.app')

@section('title', 'Audit Trail')

@section('content')

<div class="space-y-6">

    {{-- =====================================================
         AUDIT TRAIL HEADER
    ====================================================== --}}
    <x-page-header
        eyebrow="Governance & Accountability"
        title="Audit Trail"
        badge="Read-Only Ledger"
        description="Review accountable system activity, workflow decisions, record changes, and authenticated user actions.">

        <x-slot:actions>

            <div class="rounded-xl border border-border bg-card px-4 py-2.5 shadow-card">

                <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                    Recorded Events
                </p>

                <p class="mt-0.5 font-heading text-lg font-bold text-primary">
                    {{ number_format($stats['total']) }}
                </p>

            </div>

        </x-slot:actions>

    </x-page-header>


    {{-- =====================================================
         AUDIT OVERVIEW
    ====================================================== --}}
    <section class="space-y-4">

        <x-section-header
            eyebrow="Overview"
            title="Audit Overview"
            description="A current snapshot of recorded accountability activity across the system." />


        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

            <x-metric-card
                label="Total Events"
                :value="number_format($stats['total'])"
                :href="route('audit-trail.index')"
                helper="Complete recorded audit activity available in the ledger."
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
                            d="M9 12l2 2 4-4M12 22a10 10 0 100-20 10 10 0 000 20z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Today's Activity"
                :value="number_format($stats['today'])"
                :href="route('audit-trail.index', [
                    'from' => today()->toDateString(),
                    'to' => today()->toDateString(),
                ])"
                helper="Audit events recorded during the current system date."
                tone="secondary">

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
                            d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Accountable Actors"
                :value="number_format($stats['actors'])"
                helper="Unique actor email addresses represented in the audit ledger."
                tone="accent">

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
                            d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Audited Modules"
                :value="number_format($stats['modules'])"
                helper="Distinct modules currently producing accountability events."
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
                            d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>

        </div>

    </section>


    {{-- =====================================================
         AUDIT EVENT EXPLORER
    ====================================================== --}}
    <section class="space-y-4">

        <x-section-header
            eyebrow="Explorer"
            title="Find Audit Events"
            description="Narrow the immutable event ledger by actor, module, action, or recorded date." />


        <div class="card overflow-hidden">

            <form
                method="GET"
                action="{{ route('audit-trail.index') }}"
                class="p-4">

                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">

                    <div>

                        <label
                            for="actor_email"
                            class="label">

                            Actor

                        </label>

                        <input
                            id="actor_email"
                            type="text"
                            name="actor_email"
                            maxlength="255"
                            value="{{ request('actor_email') }}"
                            placeholder="Email address"
                            class="input">

                    </div>


                    <div>

                        <label
                            for="module"
                            class="label">

                            Module

                        </label>

                        <select
                            id="module"
                            name="module"
                            class="input">

                            <option value="">
                                All Modules
                            </option>

                            @foreach ($moduleOptions as $module)

                                <option
                                    value="{{ $module }}"
                                    @selected(request('module') === $module)>

                                    {{ str($module)->headline() }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div>

                        <label
                            for="action"
                            class="label">

                            Action

                        </label>

                        <select
                            id="action"
                            name="action"
                            class="input">

                            <option value="">
                                All Actions
                            </option>

                            @foreach ($actionOptions as $action)

                                <option
                                    value="{{ $action }}"
                                    @selected(request('action') === $action)>

                                    {{ str($action)->headline() }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div>

                        <label
                            for="from"
                            class="label">

                            From

                        </label>

                        <input
                            id="from"
                            type="date"
                            name="from"
                            value="{{ request('from') }}"
                            class="input">

                    </div>


                    <div>

                        <label
                            for="to"
                            class="label">

                            To

                        </label>

                        <input
                            id="to"
                            type="date"
                            name="to"
                            value="{{ request('to') }}"
                            class="input">

                        @error('to')

                            <p class="mt-1.5 text-xs font-medium text-error">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                </div>


                <div class="mt-4 flex flex-col gap-3 border-t border-border pt-4 sm:flex-row sm:items-center sm:justify-between">

                    <div class="text-xs text-slate-500">

                        Showing

                        <span class="font-semibold text-primary">
                            {{ number_format($logs->firstItem() ?? 0) }}
                        </span>

                        &ndash;

                        <span class="font-semibold text-primary">
                            {{ number_format($logs->lastItem() ?? 0) }}
                        </span>

                        of

                        <span class="font-semibold text-primary">
                            {{ number_format($logs->total()) }}
                        </span>

                        matching events

                    </div>


                    <div class="flex gap-2">

                        <button
                            type="submit"
                            class="btn-primary flex-1 justify-center sm:flex-none">

                            Apply Filters

                        </button>


                        <a
                            href="{{ route('audit-trail.index') }}"
                            class="btn-outline justify-center">

                            Clear

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </section>

    {{-- =====================================================
         AUDIT EVENT LEDGER
    ====================================================== --}}
    <x-section-header
        eyebrow="Ledger"
        title="Audit Events"
        description="Recorded events are shown newest first and remain read-only historical accountability records.">

        <x-slot:actions>

            <span class="rounded-full bg-primary/5 px-3 py-1 text-[10px] font-semibold text-primary">
                {{ number_format($logs->total()) }}
                {{ $logs->total() === 1 ? 'event' : 'events' }}
            </span>

        </x-slot:actions>

    </x-section-header>


    @include('audit._mobile-cards')


    {{-- Audit Table --}}
    <div class="table-shell hidden md:block">

        <div class="overflow-x-auto">

            <table class="min-w-full text-left text-sm">

                <thead class="table-header">

                    <tr>

                        <th class="px-5 py-4 font-medium">
                            Date & Time
                        </th>

                        <th class="px-5 py-4 font-medium">
                            Actor
                        </th>

                        <th class="px-5 py-4 font-medium">
                            Action
                        </th>

                        <th class="px-5 py-4 font-medium">
                            Module
                        </th>

                        <th class="px-5 py-4 font-medium">
                            Record
                        </th>

                        <th class="px-5 py-4 font-medium">
                            Details
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-border bg-card">

                    @forelse ($logs as $log)

                        @php
                            $actionKey = strtolower((string) $log->action);

                            $actionClass = match (true) {
                                str_contains($actionKey, 'reject'),
                                str_contains($actionKey, 'decline'),
                                str_contains($actionKey, 'delete'),
                                str_contains($actionKey, 'objection')
                                    => 'badge-error',

                                str_contains($actionKey, 'approve'),
                                str_contains($actionKey, 'create'),
                                str_contains($actionKey, 'upload'),
                                str_contains($actionKey, 'check_in'),
                                str_contains($actionKey, 'login')
                                    => 'badge-success',

                                str_contains($actionKey, 'review'),
                                str_contains($actionKey, 'update'),
                                str_contains($actionKey, 'renew'),
                                str_contains($actionKey, 'sync')
                                    => 'badge-info',

                                default => 'bg-slate-100 text-slate-600',
                            };
                        @endphp


                        <tr class="align-top transition hover:bg-sky-50/40">

                            {{-- Time --}}
                            <td class="whitespace-nowrap px-5 py-4">

                                <p class="text-sm font-medium text-slate-700">
                                    {{ $log->created_at?->format('M d, Y') ?? 'Unknown' }}
                                </p>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    {{ $log->created_at?->format('h:i:s A') ?? '' }}
                                </p>

                            </td>


                            {{-- Actor --}}
                            <td class="px-5 py-4">

                                <div class="flex min-w-[190px] items-center gap-3">

                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-bold uppercase text-primary">

                                        {{ strtoupper(substr($log->actor_email ?: 'S', 0, 1)) }}

                                    </div>


                                    <div class="min-w-0">

                                        <p class="max-w-[210px] truncate text-sm font-medium text-slate-700">
                                            {{ $log->actor_email ?: 'System' }}
                                        </p>

                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            {{ $log->actor_role ? str($log->actor_role)->headline() : 'System' }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            {{-- Action --}}
                            <td class="px-5 py-4">

                                <span class="badge {{ $actionClass }}">
                                    {{ str($log->action ?: 'unknown')->headline() }}
                                </span>

                            </td>


                            {{-- Module --}}
                            <td class="px-5 py-4">

                                <span class="inline-flex rounded-lg bg-primary/5 px-2.5 py-1 text-xs font-medium text-primary">
                                    {{ str($log->module ?: 'system')->headline() }}
                                </span>

                            </td>


                            {{-- Record --}}
                            <td class="px-5 py-4">

                                @if ($log->record_label)

                                    <p class="max-w-[220px] text-sm font-medium text-slate-700">
                                        {{ $log->record_label }}
                                    </p>

                                @else

                                    <span class="text-xs italic text-slate-400">
                                        No record label
                                    </span>

                                @endif


                                @if ($log->record_id)

                                    <p class="mt-1 text-[10px] text-slate-400">
                                        Record ID: {{ $log->record_id }}
                                    </p>

                                @endif

                            </td>


                            {{-- Details --}}
                            <td class="px-5 py-4">

                                @if ($log->details)

                                    <p
                                        class="max-w-sm text-xs leading-5 text-slate-500"
                                        title="{{ $log->details }}">

                                        {{ Str::limit($log->details, 140) }}

                                    </p>

                                @else

                                    <span class="text-xs text-slate-400">
                                        No additional details
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="px-6 py-16">

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
                                                d="M9 12l2 2 4-4M12 22a10 10 0 100-20 10 10 0 000 20z" />

                                        </svg>

                                    </div>

                                    <h3 class="font-heading text-base font-semibold text-primary">
                                        No audit events found
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-500">
                                        No system activities match the selected filters.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    @if ($logs->hasPages())

        <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
            {{ $logs->withQueryString()->links() }}
        </div>

    @endif


    {{-- Information --}}
    <div class="rounded-xl border border-accent/20 bg-accent/5 px-4 py-3">

        <div class="flex items-start gap-3">

            <svg
                class="mt-0.5 h-4 w-4 shrink-0 text-accent"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M12 22a10 10 0 100-20 10 10 0 000 20z" />

            </svg>

            <p class="text-xs leading-5 text-slate-600">

                <span class="font-semibold text-primary">
                    Audit integrity:
                </span>

                This page presents recorded system activity. Audit entries should be treated as historical accountability records rather than ordinary editable business records.

            </p>

        </div>

    </div>

</div>

@endsection