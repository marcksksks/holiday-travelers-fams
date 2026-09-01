@extends('layouts.app')

@section('title', 'Audit Trail')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Audit Trail
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Review accountable system activity, workflow decisions, record changes, and user actions.
            </p>
        </div>


        <div class="rounded-xl border border-border bg-card px-4 py-2.5 shadow-card">

            <p class="text-xs text-slate-500">
                Recorded Events
            </p>

            <p class="mt-0.5 font-heading text-lg font-bold text-primary">
                {{ number_format($stats['total']) }}
            </p>

        </div>

    </div>


    {{-- Statistics --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="card relative overflow-hidden p-5">

            <div class="absolute inset-x-0 bottom-0 h-1 bg-primary"></div>

            <p class="text-xs font-medium text-slate-500">
                Total Events
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-primary">
                {{ number_format($stats['total']) }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Complete recorded activity
            </p>

        </div>


        <div class="card relative overflow-hidden p-5">

            <div class="absolute inset-x-0 bottom-0 h-1 bg-secondary"></div>

            <p class="text-xs font-medium text-slate-500">
                Today's Activity
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-secondary">
                {{ number_format($stats['today']) }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Events recorded today
            </p>

        </div>


        <div class="card relative overflow-hidden p-5">

            <div class="absolute inset-x-0 bottom-0 h-1 bg-accent"></div>

            <p class="text-xs font-medium text-slate-500">
                Actors
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-accent">
                {{ number_format($stats['actors']) }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Unique accountable users
            </p>

        </div>


        <div class="card relative overflow-hidden p-5">

            <div class="absolute inset-x-0 bottom-0 h-1 bg-success"></div>

            <p class="text-xs font-medium text-slate-500">
                Modules
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-success">
                {{ number_format($stats['modules']) }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Modules producing audit events
            </p>

        </div>

    </div>


    {{-- Filters --}}
    <div class="card p-5">

        <form
            method="GET"
            action="{{ route('audit-trail.index') }}"
            class="space-y-4">

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">

                <div>

                    <label for="actor_email" class="label">
                        Actor
                    </label>

                    <input
                        id="actor_email"
                        type="text"
                        name="actor_email"
                        value="{{ request('actor_email') }}"
                        placeholder="Email address"
                        class="input">

                </div>


                <div>

                    <label for="module" class="label">
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

                    <label for="action" class="label">
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

                    <label for="from" class="label">
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

                    <label for="to" class="label">
                        To
                    </label>

                    <input
                        id="to"
                        type="date"
                        name="to"
                        value="{{ request('to') }}"
                        class="input">

                    @error('to')
                        <p class="mt-1 text-xs font-medium text-error">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>


            <div class="flex flex-wrap gap-2">

                <button
                    type="submit"
                    class="btn-secondary">

                    Apply Filters

                </button>


                <a
                    href="{{ route('audit-trail.index') }}"
                    class="btn-outline">

                    Clear

                </a>

            </div>

        </form>

    </div>


    {{-- Audit Table --}}
    <div class="table-shell">

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