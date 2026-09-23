@extends('layouts.app')

@section('title', 'Staff Accounts')

@section('content')

<div class="space-y-6">

    {{-- =====================================================
         STAFF ACCOUNTS HEADER
    ====================================================== --}}
    <x-page-header
        eyebrow="Administration"
        title="Staff Accounts"
        badge="Access Governance"
        description="Manage staff identities, system roles, account access, password requirements, and security policy from one administrative workspace.">

        <x-slot:actions>

            <a
                href="{{ route('account-recovery.admin.index') }}"
                class="btn-secondary inline-flex items-center justify-center gap-2">

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
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4" />

                </svg>

                Recovery Requests

            </a>

            <button
                type="button"
                data-staff-create-open
                class="btn-primary inline-flex items-center justify-center gap-2">

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
                        d="M12 4v16m8-8H4" />

                </svg>

                Add Staff

            </button>

        </x-slot:actions>

    </x-page-header>


    {{-- =====================================================
         ACCESS OVERVIEW
    ====================================================== --}}
    <section class="space-y-4">

        <x-section-header
            eyebrow="Overview"
            title="Access Overview"
            description="A current snapshot of registered accounts, access availability, and login requirements." />


        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

            <x-metric-card
                label="Total Staff"
                :value="number_format($stats['total'])"
                :href="route('users.index')"
                helper="Registered staff identities managed by the system."
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
                            d="M17 20h5v-2a4 4 0 00-5-4m-4 6H3v-2a4 4 0 014-4h2a4 4 0 014 4v2zm-5-8a4 4 0 100-8 4 4 0 000 8zm9-2a3 3 0 100-6 3 3 0 000 6z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Active Accounts"
                :value="number_format($stats['active'])"
                :href="route('users.index', ['status' => 'active'])"
                helper="Staff accounts currently permitted to sign in."
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
                label="Deactivated"
                :value="number_format($stats['inactive'])"
                :href="route('users.index', ['status' => 'inactive'])"
                helper="Accounts whose system sign-in access is currently disabled."
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
                            d="M18.364 18.364A9 9 0 105.636 5.636m12.728 12.728L5.636 5.636" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Password Change"
                :value="number_format($stats['password_change'])"
                helper="Accounts required to replace their temporary password at login."
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
                            d="M12 11V7a4 4 0 118 0v4m-12 0V7a4 4 0 018 0v4m-9 0h10a2 2 0 012 2v6H5v-6a2 2 0 012-2z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>

        </div>


        <div class="rounded-xl border border-accent/20 bg-accent/5 px-4 py-3">

            <div class="flex items-start gap-3">

                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 3l7 4v5c0 4.4-2.9 8.4-7 9.7C7.9 20.4 5 16.4 5 12V7l7-4zM9 12l2 2 4-4" />

                    </svg>

                </div>


                <div>

                    <p class="text-xs font-semibold text-primary">
                        Privileged Account Security
                    </p>

                    <p class="mt-1 text-[11px] leading-5 text-slate-500">
                        Administrative Officers, General Managers, Legal Officers, and System Administrators are subject to mandatory multi-factor authentication before normal application access is allowed.
                    </p>

                </div>

            </div>

        </div>

    </section>

<div class="min-w-0">
        {{-- Directory --}}
        <div class="min-w-0 space-y-4">

            <x-section-header
                eyebrow="Directory"
                title="Staff Directory"
                description="Search staff, review assigned roles and security requirements, and manage account access.">

                <x-slot:actions>

                    <span class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-3 py-2 text-xs font-medium text-slate-500">

                        <span class="h-2 w-2 rounded-full bg-accent"></span>

                        {{ number_format($staff->total()) }}
                        {{ \Illuminate\Support\Str::plural('account', $staff->total()) }}

                    </span>

                </x-slot:actions>

            </x-section-header>

            {{-- Automatic Filters --}}
<form
    id="staffFilterForm"
    method="GET"
    action="{{ route('users.index') }}"
    class="card overflow-hidden">

    <div class="flex flex-col gap-4 p-4 lg:flex-row lg:items-center">

        {{-- Search --}}
        <div class="min-w-0 flex-1">

            <label
                for="staffSearch"
                class="sr-only">
                Search staff
            </label>

            <div class="relative">

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

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
                            d="M21 21l-4.35-4.35M19 11a8 8 0 11-16 0 8 8 0 0116 0z" />

                    </svg>

                </div>


                <input
                    id="staffSearch"
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by name, email, department or job title..."
                    autocomplete="off"
                    class="input w-full pl-10 pr-10">


                <div
                    id="staffSearchIndicator"
                    class="pointer-events-none absolute inset-y-0 right-0 hidden items-center pr-3">

                    <svg
                        class="h-4 w-4 animate-spin text-accent"
                        fill="none"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

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

                </div>

            </div>

        </div>


        {{-- Filters --}}
        <div class="grid gap-3 sm:grid-cols-2 lg:flex lg:shrink-0 lg:items-center">

            <div class="relative">

                <label
                    for="staffRoleFilter"
                    class="sr-only">
                    Filter by role
                </label>

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">

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
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM5 21a7 7 0 0114 0" />

                    </svg>

                </div>

                <select
                    id="staffRoleFilter"
                    name="role"
                    onchange="this.form.submit()"
                    class="input min-w-[170px] pl-9">

                    <option value="">
                        All Roles
                    </option>

                    @foreach (\App\Models\User::ROLES as $value => $label)

                        <option
                            value="{{ $value }}"
                            @selected(request('role') === $value)>

                            {{ $label }}

                        </option>

                    @endforeach

                </select>

            </div>


            <div class="relative">

                <label
                    for="staffStatusFilter"
                    class="sr-only">
                    Filter by status
                </label>

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">

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
                            d="M9 12l2 2 4-4m5-4a11 11 0 01-8 3 11 11 0 01-8-3c0 5.25 3.44 10.74 8 12 4.56-1.26 8-6.75 8-12z" />

                    </svg>

                </div>

                <select
                    id="staffStatusFilter"
                    name="status"
                    onchange="this.form.submit()"
                    class="input min-w-[160px] pl-9">

                    <option value="">
                        All Statuses
                    </option>

                    <option
                        value="active"
                        @selected(request('status') === 'active')>
                        Active
                    </option>

                    <option
                        value="inactive"
                        @selected(request('status') === 'inactive')>
                        Deactivated
                    </option>

                </select>

            </div>

        </div>


        {{-- Clear Filters --}}
        @if (request()->filled('search') || request()->filled('role') || request()->filled('status'))

            <a
                href="{{ route('users.index') }}"
                class="inline-flex h-[42px] shrink-0 items-center justify-center gap-1.5 rounded-lg px-3 text-xs font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-primary">

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

                Clear

            </a>

        @endif

    </div>


    {{-- Active filter summary --}}
    @if (request()->filled('search') || request()->filled('role') || request()->filled('status'))

        <div class="flex flex-wrap items-center gap-2 border-t border-border bg-background/50 px-4 py-3">

            <span class="mr-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                Active Filters
            </span>


            @if (request()->filled('search'))

                <span class="inline-flex items-center rounded-full bg-primary/5 px-2.5 py-1 text-[11px] font-medium text-primary ring-1 ring-inset ring-primary/10">
                    Search: {{ request('search') }}
                </span>

            @endif


            @if (request()->filled('role'))

                <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-1 text-[11px] font-medium text-sky-700 ring-1 ring-inset ring-sky-200">
                    Role:
                    {{ \App\Models\User::ROLES[request('role')] ?? str(request('role'))->headline() }}
                </span>

            @endif


            @if (request()->filled('status'))

                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-600 ring-1 ring-inset ring-slate-200">
                    Status:
                    {{ request('status') === 'active' ? 'Active' : 'Deactivated' }}
                </span>

            @endif

        </div>

    @endif

</form>


@include('users._mobile-cards')

{{-- Staff Table --}}
            <div class="table-shell hidden md:block">

                <div class="overflow-x-auto rounded-xl">

                    <table class="w-full text-left text-sm">

                        <thead class="table-header border-b border-border">

                            <tr>

                                <th class="px-5 py-3.5 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">
                                    Staff Member
                                </th>

                                <th class="hidden px-5 py-3.5 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500 sm:table-cell">
                                    Role
                                </th>

                                <th class="hidden px-5 py-3.5 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500 lg:table-cell">
                                    Security
                                </th>

                                <th class="px-5 py-3.5 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">
                                    Status
                                </th>

                                <th class="px-5 py-3.5 text-right text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-border bg-card">

                            @forelse ($staff as $person)

                                <tr class="group transition-colors hover:bg-sky-50/60">

                                    {{-- Staff --}}
                                    <td class="px-5 py-4">

                                        <div class="flex min-w-[190px] items-center gap-3 sm:min-w-[230px]">

                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-primary/10 bg-primary/5 font-heading text-sm font-bold text-primary transition group-hover:bg-primary/10">

                                                {{ strtoupper(substr($person->full_name ?: $person->email, 0, 1)) }}

                                            </div>


                                            <div class="min-w-0">

                                                <div class="flex items-center gap-2">

                                                    <p class="max-w-[220px] truncate font-heading text-sm font-semibold text-primary">
                                                        {{ $person->full_name }}
                                                    </p>


                                                    @if ($person->id === auth()->id())

                                                        <span class="rounded-full bg-accent/10 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wide text-primary">
                                                            You
                                                        </span>

                                                    @endif

                                                </div>


                                                <p class="mt-0.5 max-w-[240px] truncate text-xs text-slate-500" title="{{ $person->email }}">
                                                    {{ $person->email }}
                                                </p>


                                                <div class="mt-1.5 sm:hidden">

                                                    <span class="inline-flex rounded-md bg-primary/5 px-2 py-0.5 text-[10px] font-semibold text-primary ring-1 ring-inset ring-primary/10">
                                                        {{ \App\Models\User::ROLES[$person->app_role] ?? str($person->app_role)->headline() }}
                                                    </span>

                                                </div>

                                                @if ($person->job_title || $person->department)

                                                    <p class="mt-1 max-w-[240px] truncate text-[10px] text-slate-400">

                                                        {{ $person->job_title ?: 'Staff' }}

                                                        @if ($person->department)
                                                            · {{ $person->department }}
                                                        @endif

                                                    </p>

                                                @endif

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Role --}}
                                    <td class="hidden px-5 py-4 sm:table-cell">

                                        @php
                                            $roleStyle = match ($person->app_role) {
                                                'sys_admin' => 'bg-violet-50 text-violet-700 ring-violet-200',
                                                'manager' => 'bg-amber-50 text-amber-700 ring-amber-200',
                                                'admin_officer' => 'bg-sky-50 text-sky-700 ring-sky-200',
                                                'legal_officer' => 'bg-rose-50 text-rose-700 ring-rose-200',
                                                'receptionist' => 'bg-cyan-50 text-cyan-700 ring-cyan-200',
                                                default => 'bg-slate-50 text-slate-600 ring-slate-200',
                                            };
                                        @endphp

                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $roleStyle }}">

                                            <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>

                                            {{ \App\Models\User::ROLES[$person->app_role] ?? str($person->app_role)->headline() }}

                                        </span>

                                    </td>

                                    {{-- Security --}}
                                    <td class="hidden px-5 py-4 lg:table-cell">

                                        <div class="flex flex-col items-start gap-1.5">

                                            @if ($person->force_password_change)

                                                <span class="badge badge-warning">
                                                    Password Change
                                                </span>

                                            @else

                                                <span class="badge badge-success">
                                                    Password Set
                                                </span>

                                            @endif


                                            @if ($person->requiresMandatoryMfa())

                                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-primary">

                                                    <svg
                                                        class="h-3.5 w-3.5"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24">

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 3l7 4v5c0 4.4-2.9 8.4-7 9.7C7.9 20.4 5 16.4 5 12V7l7-4z" />

                                                    </svg>

                                                    MFA Required

                                                </span>

                                            @else

                                                <span class="text-[10px] font-medium text-slate-400">
                                                    MFA Optional
                                                </span>

                                            @endif

                                        </div>

                                    </td>

{{-- Status --}}
                                    <td class="px-5 py-4">

                                        @if ($person->is_active)

                                            <span class="badge badge-success">
                                                <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                                Active
                                            </span>

                                        @else

                                            <span class="badge bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200">
                                                Deactivated
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Action --}}
                                    <td class="px-5 py-4 text-right">

                                        @if ($person->id === auth()->id())

                                            <div class="inline-flex items-center gap-2">

                                                <span class="h-2 w-2 rounded-full bg-accent"></span>

                                                <span class="text-xs font-medium text-slate-400">
                                                    Current account
                                                </span>

                                            </div>

                                        @else

                                            <button
                                                type="button"
                                                onclick="document.getElementById('staffManageDialog{{ $person->id }}').showModal()"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-border bg-card text-slate-500 transition hover:border-accent/40 hover:bg-sky-50 hover:text-primary"
                                                aria-label="Manage {{ $person->full_name }}"
                                                title="Manage account">

                                                <svg
                                                    class="h-5 w-5"
                                                    viewBox="0 0 24 24"
                                                    fill="currentColor"
                                                    aria-hidden="true">

                                                    <circle cx="5" cy="12" r="1.7" />
                                                    <circle cx="12" cy="12" r="1.7" />
                                                    <circle cx="19" cy="12" r="1.7" />

                                                </svg>

                                            </button>


                                            <dialog
                                                id="staffManageDialog{{ $person->id }}"
                                                class="w-[calc(100%_-_2rem)] max-h-[90vh] max-w-xl overflow-hidden rounded-2xl open:flex open:flex-col border border-border bg-card p-0 text-left shadow-2xl backdrop:bg-slate-950/50">

                                                {{-- Header --}}
                                                <div class="shrink-0 flex items-start justify-between gap-4 border-b border-border bg-card px-6 py-5">

                                                    <div class="flex min-w-0 items-center gap-3">

                                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary/10 font-heading text-sm font-bold text-primary">

                                                            {{ strtoupper(substr($person->full_name ?: $person->email, 0, 1)) }}

                                                        </div>


                                                        <div class="min-w-0">

                                                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-secondary">
                                                                Staff Account
                                                            </p>

                                                            <h3 class="mt-0.5 truncate font-heading text-lg font-semibold text-primary">
                                                                {{ $person->full_name }}
                                                            </h3>

                                                            <p class="truncate text-xs text-slate-500">
                                                                {{ $person->email }}
                                                            </p>

                                                        </div>

                                                    </div>


                                                    <button
                                                        type="button"
                                                        onclick="this.closest('dialog').close()"
                                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary"
                                                        aria-label="Close">

                                                        <svg
                                                            class="h-5 w-5"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24">

                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M6 18L18 6M6 6l12 12" />

                                                        </svg>

                                                    </button>

                                                </div>


                                                <div class="min-h-0 flex-1 space-y-5 overflow-y-auto p-6">

                                                    {{-- Staff information --}}

                                                    <div class="rounded-xl border border-border bg-background/40 p-4">

                                                        <div class="flex flex-wrap items-center justify-between gap-3">

                                                            <div>
                                                                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                                                                    Account Overview
                                                                </p>

                                                                <div class="mt-2 flex flex-wrap items-center gap-2">

                                                                    @if ($person->is_active)
                                                                        <span class="badge badge-success">
                                                                            Active Account
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-slate-100 text-slate-500">
                                                                            Deactivated
                                                                        </span>
                                                                    @endif

                                                                    <span class="badge badge-info">
                                                                        {{ \App\Models\User::ROLES[$person->app_role] ?? str($person->app_role)->headline() }}
                                                                    </span>

                                                                </div>
                                                            </div>

                                                            @if ($person->force_password_change)
                                                                <span class="badge badge-warning">
                                                                    Password Change Pending
                                                                </span>
                                                            @endif

                                                        </div>

                                                    </div>
                                                    <div class="grid gap-3 sm:grid-cols-2">

                                                        <div class="rounded-xl border border-border bg-background/50 p-4">

                                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                                                Department
                                                            </p>

                                                            <p class="mt-1.5 text-sm font-semibold text-primary">
                                                                {{ $person->department ?: 'Not assigned' }}
                                                            </p>

                                                        </div>


                                                        <div class="rounded-xl border border-border bg-background/50 p-4">

                                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                                                Job Title
                                                            </p>

                                                            <p class="mt-1.5 text-sm font-semibold text-primary">
                                                                {{ $person->job_title ?: 'Staff Member' }}
                                                            </p>

                                                        </div>

                                                    </div>


                                                    <div class="grid gap-3 sm:grid-cols-2">

                                                        <div class="rounded-xl border border-border bg-background/40 p-4">
                                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                                                Contact Number
                                                            </p>

                                                            <p class="mt-1.5 text-sm font-medium text-primary">
                                                                {{ $person->phone ?: 'Not provided' }}
                                                            </p>
                                                        </div>

                                                        <div class="rounded-xl border border-border bg-background/40 p-4">
                                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                                                Member Since
                                                            </p>

                                                            <p class="mt-1.5 text-sm font-medium text-primary">
                                                                {{ $person->created_at?->format('M d, Y') ?? 'Not available' }}
                                                            </p>
                                                        </div>

                                                    </div>


                                                    {{-- Role Management --}}
                                                    <div class="rounded-xl border border-border p-4">

                                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                                                            <div>
                                                                <h4 class="font-heading text-sm font-semibold text-primary">
                                                                    System Role
                                                                </h4>

                                                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                                                    Controls the modules and administrative functions available to this staff member.
                                                                </p>
                                                            </div>

                                                            <span class="inline-flex self-start rounded-full bg-primary/5 px-2.5 py-1 text-[11px] font-semibold text-primary ring-1 ring-inset ring-primary/10">
                                                                {{ \App\Models\User::ROLES[$person->app_role] ?? str($person->app_role)->headline() }}
                                                            </span>

                                                        </div>


                                                        <form
                                                            method="POST"
                                                            action="{{ route('users.set-role', $person) }}"
                                                            onsubmit="return confirm('Change this staff member\'s system role? Their module access may change immediately.');"
                                                            class="mt-4">

                                                            @csrf

                                                            <label
                                                                for="manage_role_{{ $person->id }}"
                                                                class="label">
                                                                Assigned Role
                                                            </label>

                                                            <div class="flex flex-col gap-3 sm:flex-row">

                                                                <select
                                                                    id="manage_role_{{ $person->id }}"
                                                                    name="app_role"
                                                                    class="input min-w-0 flex-1">

                                                                    @foreach (\App\Models\User::ROLES as $value => $label)

                                                                        <option
                                                                            value="{{ $value }}"
                                                                            @selected($person->app_role === $value)>
                                                                            {{ $label }}
                                                                        </option>

                                                                    @endforeach

                                                                </select>


                                                                <button
                                                                    type="submit"
                                                                    class="btn-outline shrink-0 justify-center">
                                                                    Update Role
                                                                </button>

                                                            </div>


                                                            <div class="mt-3 flex items-start gap-2 rounded-lg bg-background/60 px-3 py-2.5">

                                                                <svg
                                                                    class="mt-0.5 h-4 w-4 shrink-0 text-accent"
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    viewBox="0 0 24 24"
                                                                    aria-hidden="true">

                                                                    <path
                                                                        stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z" />

                                                                </svg>

                                                                <p class="text-[11px] leading-5 text-slate-500">
                                                                    Role changes affect this account's permitted modules and actions according to the system RBAC rules.
                                                                </p>

                                                            </div>

                                                        </form>

                                                    </div>

{{-- Security --}}
                                                    <div class="rounded-xl border border-border p-4">

                                                        <div class="flex items-start gap-3">

                                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/5 text-primary">
                                                                <svg
                                                                    class="h-4 w-4"
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path
                                                                        stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M12 11V7a4 4 0 118 0v4m-12 0V7a4 4 0 018 0v4m-9 0h10a2 2 0 012 2v6H5v-6a2 2 0 012-2z" />
                                                                </svg>
                                                            </div>

                                                            <div class="min-w-0 flex-1">

                                                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                                                                    <div>
                                                                        <h4 class="font-heading text-sm font-semibold text-primary">
                                                                            Login Security
                                                                        </h4>

                                                                        <p class="mt-1 text-xs text-slate-500">
                                                                            Current password state and multi-factor authentication policy for this staff account.
                                                                        </p>
                                                                    </div>

                                                                    @if ($person->force_password_change)

                                                                        <span class="badge badge-warning self-start">
                                                                            Change Required
                                                                        </span>

                                                                    @else

                                                                        <span class="badge badge-success self-start">
                                                                            Password Configured
                                                                        </span>

                                                                    @endif

                                                                </div>

                                                                @if ($person->force_password_change)

                                                                    <p class="mt-3 text-[11px] leading-5 text-amber-700">
                                                                        This staff member must replace the temporary password after signing in.
                                                                    </p>

                                                                @else

                                                                    <p class="mt-3 text-[11px] leading-5 text-slate-400">
                                                                        No forced password change is currently pending.
                                                                    </p>

                                                                @endif


                                                                <div class="mt-3 border-t border-border pt-3">

                                                                    <div class="flex flex-wrap items-center justify-between gap-2">

                                                                        <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                                                            MFA Policy
                                                                        </span>


                                                                        @if ($person->requiresMandatoryMfa())

                                                                            <span class="badge badge-info">
                                                                                Required
                                                                            </span>

                                                                        @else

                                                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-semibold text-slate-500">
                                                                                Optional
                                                                            </span>

                                                                        @endif

                                                                    </div>


                                                                    <p class="mt-2 text-[11px] leading-5 text-slate-500">

                                                                        @if ($person->requiresMandatoryMfa())

                                                                            This role must complete multi-factor authentication setup before normal application access is permitted.

                                                                        @else

                                                                            Multi-factor authentication is optional for this role under the current access policy.

                                                                        @endif

                                                                    </p>

                                                                </div>

                                                            </div>

                                                        </div>

                                                    </div>

{{-- Account access --}}
                                                    <div class="overflow-hidden rounded-xl border border-border">

                                                        <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">

                                                            <div>
                                                                <div class="flex items-center gap-2">

                                                                    <span class="h-2 w-2 rounded-full {{ $person->is_active ? 'bg-success' : 'bg-slate-400' }}"></span>

                                                                    <h4 class="font-heading text-sm font-semibold text-primary">
                                                                        Account Access
                                                                    </h4>

                                                                </div>

                                                                <p class="mt-1.5 text-xs leading-5 text-slate-500">
                                                                    {{ $person->is_active
                                                                        ? 'This staff member can currently sign in and use permitted system modules.'
                                                                        : 'System sign-in is disabled for this staff member.' }}
                                                                </p>
                                                            </div>

                                                            <span class="{{ $person->is_active ? 'badge badge-success' : 'badge bg-slate-100 text-slate-500' }}">
                                                                {{ $person->is_active ? 'Active' : 'Deactivated' }}
                                                            </span>

                                                        </div>


                                                        <div class="{{ $person->is_active ? 'border-t border-error/10 bg-error/[0.03]' : 'border-t border-success/10 bg-success/[0.03]' }} px-4 py-3">

                                                            <form
                                                                method="POST"
                                                                action="{{ route('users.toggle-active', $person) }}"
                                                                onsubmit="return confirm('{{ $person->is_active ? 'Deactivate this staff account? The user will no longer be able to sign in.' : 'Reactivate this staff account and restore sign-in access?' }}');">

                                                                @csrf

                                                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                                                                    <p class="text-[11px] leading-5 text-slate-500">
                                                                        {{ $person->is_active
                                                                            ? 'Deactivate access while preserving this staff member’s records and history.'
                                                                            : 'Restore system access using the staff member’s assigned role.' }}
                                                                    </p>

                                                                    @if ($person->is_active)

                                                                        <button
                                                                            type="submit"
                                                                            class="shrink-0 rounded-lg border border-error/20 bg-card px-4 py-2.5 text-xs font-semibold text-error transition hover:bg-error hover:text-white">
                                                                            Deactivate Account
                                                                        </button>

                                                                    @else

                                                                        <button
                                                                            type="submit"
                                                                            class="shrink-0 rounded-lg bg-success px-4 py-2.5 text-xs font-semibold text-white transition hover:opacity-90">
                                                                            Reactivate Account
                                                                        </button>

                                                                    @endif

                                                                </div>

                                                            </form>

                                                        </div>

                                                    </div>

<div class="shrink-0 flex justify-end border-t border-border bg-card px-6 py-4">

                                                    <button
                                                        type="button"
                                                        onclick="this.closest('dialog').close()"
                                                        class="btn-outline">
                                                        Close
                                                    </button>

                                                </div>

                                            </dialog>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5" class="px-6 py-16">

                                        <div class="mx-auto flex max-w-md flex-col items-center text-center">

                                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/5 text-primary ring-1 ring-inset ring-primary/10">

                                                <svg
                                                    class="h-6 w-6"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                    aria-hidden="true">

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a4 4 0 00-5-4m-4 6H3v-2a4 4 0 014-4h2a4 4 0 014 4v2zm-5-8a4 4 0 100-8 4 4 0 000 8z" />

                                                </svg>

                                            </div>


                                            @if (request()->filled('search') || request()->filled('role') || request()->filled('status'))

                                                <h3 class="mt-4 font-heading text-base font-semibold text-primary">
                                                    No matching staff accounts
                                                </h3>

                                                <p class="mt-1.5 text-sm leading-6 text-slate-500">
                                                    No staff members match the current search or filters.
                                                </p>

                                                <a
                                                    href="{{ route('users.index') }}"
                                                    class="btn-outline mt-4 justify-center">
                                                    Clear Filters
                                                </a>

                                            @else

                                                <h3 class="mt-4 font-heading text-base font-semibold text-primary">
                                                    No staff accounts yet
                                                </h3>

                                                <p class="mt-1.5 text-sm leading-6 text-slate-500">
                                                    Create the first staff account to begin assigning system access and roles.
                                                </p>

                                                <button
                                                    type="button"
                                                    data-staff-create-open
                                                    class="btn-secondary mt-4 justify-center">
                                                    Add Staff
                                                </button>

                                            @endif

                                        </div>

                                    </td>

                                </tr>
@endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            @if ($staff->hasPages())

                <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
                    <div class="flex flex-col gap-3 border-t border-border bg-background/30 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-2 text-xs text-slate-500">

                            <span class="h-2 w-2 rounded-full bg-accent"></span>

                            <span>
                                Showing
                                <span class="font-semibold text-primary">
                                    {{ $staff->firstItem() ?? 0 }}
                                </span>
                                –
                                <span class="font-semibold text-primary">
                                    {{ $staff->lastItem() ?? 0 }}
                                </span>
                                of
                                <span class="font-semibold text-primary">
                                    {{ number_format($staff->total()) }}
                                </span>
                                staff accounts
                            </span>

                        </div>


                        @if ($staff->hasPages())

                            <div class="shrink-0">
                                {{ $staff->withQueryString()->links() }}
                            </div>

                        @endif

                    </div>
                </div>

            @endif

        </div>

    </div>


    {{-- Security Note --}}
    <div class="rounded-xl border border-warning/30 bg-warning/5 px-4 py-3">

        <p class="text-xs leading-5 text-slate-600">

            <span class="font-semibold text-primary">
                Account security:
            </span>

            Newly created staff accounts receive a temporary password and are required to change it after signing in. Role and account-status changes are recorded in the Audit Trail.

        </p>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Temporary Password Visibility
    |--------------------------------------------------------------------------
    */

    const password = document.getElementById('password');
    const passwordToggle = document.getElementById('toggleStaffPassword');

    if (password && passwordToggle) {

        passwordToggle.addEventListener('click', function () {

            const hidden = password.type === 'password';

            password.type = hidden ? 'text' : 'password';

            passwordToggle.textContent = hidden
                ? 'Hide'
                : 'Show';

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Automatic Staff Search
    |--------------------------------------------------------------------------
    */

    const filterForm = document.getElementById('staffFilterForm');
    const searchInput = document.getElementById('staffSearch');
    const searchIndicator = document.getElementById('staffSearchIndicator');

    let searchTimer;

    if (filterForm && searchInput) {

        searchInput.addEventListener('input', function () {

            clearTimeout(searchTimer);

            if (searchIndicator) {
                searchIndicator.classList.remove('hidden');
                searchIndicator.classList.add('flex');
            }

            searchTimer = setTimeout(function () {

                filterForm.submit();

            }, 500);

        });


        /*
         * Pressing Enter searches immediately.
         */
        searchInput.addEventListener('keydown', function (event) {

            if (event.key === 'Enter') {

                event.preventDefault();

                clearTimeout(searchTimer);

                filterForm.submit();

            }

        });


        /*
         * Clicking the browser search-field X also
         * automatically refreshes the directory.
         */
        searchInput.addEventListener('search', function () {

            clearTimeout(searchTimer);

            filterForm.submit();

        });

    }

});
</script>



@include('users._create-modal')



<script data-staff-create-modal-script>
    document.addEventListener('DOMContentLoaded', () => {

        const modal =
            document.querySelector(
                '[data-staff-create-modal]'
            );

        const openButton =
            document.querySelector(
                '[data-staff-create-open]'
            );

        if (! modal || ! openButton) {
            return;
        }


        const closeButtons =
            modal.querySelectorAll(
                '[data-staff-create-close]'
            );

        const password =
            modal.querySelector(
                '#staff_password'
            );

        const passwordToggle =
            modal.querySelector(
                '[data-staff-password-toggle]'
            );

        let previousFocus = null;


        const openModal =
            () => {

                previousFocus =
                    document.activeElement;

                modal.classList.remove(
                    'hidden'
                );

                document.body.classList.add(
                    'overflow-hidden'
                );

                window.setTimeout(
                    () => {
                        modal
                            .querySelector(
                                '#staff_full_name'
                            )
                            ?.focus();
                    },
                    50
                );

            };


        const closeModal =
            () => {

                modal.classList.add(
                    'hidden'
                );

                document.body.classList.remove(
                    'overflow-hidden'
                );

                previousFocus?.focus?.();

            };


        openButton.addEventListener(
            'click',
            openModal
        );


        closeButtons.forEach(
            (button) => {

                button.addEventListener(
                    'click',
                    closeModal
                );

            }
        );


        document.addEventListener(
            'keydown',
            (event) => {

                if (
                    event.key === 'Escape' &&
                    ! modal.classList.contains('hidden')
                ) {
                    closeModal();
                }

            }
        );


        if (
            password &&
            passwordToggle
        ) {

            passwordToggle.addEventListener(
                'click',
                () => {

                    const visible =
                        password.type === 'text';

                    password.type =
                        visible
                            ? 'password'
                            : 'text';

                    passwordToggle.textContent =
                        visible
                            ? 'Show'
                            : 'Hide';

                }
            );

        }


        const shouldOpenFromValidation =
            @json(
                $errors->has('full_name')
                ||
                $errors->has('email')
                ||
                $errors->has('department')
                ||
                $errors->has('job_title')
                ||
                $errors->has('phone')
                ||
                $errors->has('app_role')
                ||
                $errors->has('password')
            );


        if (shouldOpenFromValidation) {
            openModal();
        }

    });
</script>

@endsection