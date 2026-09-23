@extends('layouts.app')

@section('title', 'Account Recovery Requests')

@section('content')

<div
    data-recovery-admin-workspace
    class="space-y-6">


    <x-page-header
        eyebrow="Identity Security"
        title="Account Recovery"
        badge="Sys Admin"
        description="Review administrator-assisted recovery requests, verify staff identity, and authorize secure short-lived password recovery.">

        <x-slot:actions>

            <a
                href="{{ route('users.index') }}"
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
                        d="M15 19l-7-7 7-7" />

                </svg>

                Staff Accounts

            </a>

        </x-slot:actions>

    </x-page-header>


    {{-- =====================================================
         RECOVERY OVERVIEW
    ====================================================== --}}
    <section class="space-y-4">

        <x-section-header
            eyebrow="Overview"
            title="Recovery Activity"
            description="Monitor pending identity verification, active reset authorizations, completed recoveries, and closed requests." />


        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

            <x-metric-card
                label="Pending"
                :value="number_format($stats['pending'])"
                :href="route(
                    'account-recovery.admin.index',
                    ['status' => 'pending']
                )"
                helper="Requests waiting for System Administrator verification."
                tone="warning">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Authorized"
                :value="number_format($stats['approved'])"
                :href="route(
                    'account-recovery.admin.index',
                    ['status' => 'approved']
                )"
                helper="Approved requests with an active password reset window."
                tone="accent">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m5 13 4 4L19 7" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Completed"
                :value="number_format($stats['completed'])"
                :href="route(
                    'account-recovery.admin.index',
                    ['status' => 'completed']
                )"
                helper="Password recovery processes completed successfully."
                tone="success">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Closed"
                :value="number_format($stats['closed'])"
                helper="Rejected or expired recovery requests."
                tone="error">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18 18 6M6 6l12 12" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>

        </div>

    </section>


    {{-- =====================================================
         VERIFICATION QUEUE
    ====================================================== --}}
    <section class="space-y-4">

        <x-section-header
            eyebrow="Verification Queue"
            title="Recovery Requests"
            description="Verify the staff member before authorizing recovery. Administrators never receive, choose, or view the replacement password.">

            <x-slot:actions>

                <span
                    class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-3 py-2 text-xs font-medium text-slate-500">

                    <span
                        class="h-2 w-2 rounded-full bg-accent">
                    </span>

                    {{ number_format($recoveries->total()) }}

                    {{ \Illuminate\Support\Str::plural(
                        'request',
                        $recoveries->total()
                    ) }}

                </span>

            </x-slot:actions>

        </x-section-header>


        {{-- Search and filter --}}
        <form
            method="GET"
            action="{{ route('account-recovery.admin.index') }}"
            class="card overflow-hidden">

            <div
                class="flex flex-col gap-3 p-4 lg:flex-row lg:items-center">

                <div class="relative min-w-0 flex-1">

                    <label
                        for="recoverySearch"
                        class="sr-only">
                        Search recovery requests
                    </label>


                    <div
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

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
                        id="recoverySearch"
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search name, email or recovery reference..."
                        autocomplete="off"
                        class="input w-full pl-10">

                </div>


                <div class="lg:w-48">

                    <label
                        for="recoveryStatusFilter"
                        class="sr-only">
                        Filter by recovery status
                    </label>

                    <select
                        id="recoveryStatusFilter"
                        name="status"
                        onchange="this.form.submit()"
                        class="input w-full">

                        <option value="">
                            All Statuses
                        </option>

                        <option
                            value="pending"
                            @selected(request('status') === 'pending')>
                            Pending
                        </option>

                        <option
                            value="approved"
                            @selected(request('status') === 'approved')>
                            Authorized
                        </option>

                        <option
                            value="completed"
                            @selected(request('status') === 'completed')>
                            Completed
                        </option>

                        <option
                            value="rejected"
                            @selected(request('status') === 'rejected')>
                            Rejected
                        </option>

                        <option
                            value="expired"
                            @selected(request('status') === 'expired')>
                            Expired
                        </option>

                    </select>

                </div>


                <button
                    type="submit"
                    class="btn-secondary justify-center">
                    Search
                </button>


                @if (
                    request()->filled('search')
                    ||
                    request()->filled('status')
                )

                    <a
                        href="{{ route('account-recovery.admin.index') }}"
                        class="inline-flex h-[42px] items-center justify-center px-3 text-xs font-semibold text-slate-500 transition hover:text-primary">

                        Clear

                    </a>

                @endif

            </div>

        </form>


        @if ($recoveries->isEmpty())

            <div class="card">

                <x-empty-state
                    title="No recovery requests found"
                    description="There are no account recovery requests matching the current filters.">

                    <x-slot:icon>

                        <svg
                            class="h-5 w-5"
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

                    </x-slot:icon>

                </x-empty-state>

            </div>

        @else


            {{-- =================================================
                 DESKTOP TABLE
            ================================================== --}}
            <div
                class="hidden overflow-hidden rounded-xl border border-border bg-card lg:block">

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead
                            class="border-b border-border bg-background/50">

                            <tr>

                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                                    Staff
                                </th>

                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                                    Recovery
                                </th>

                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                                    Requested
                                </th>

                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                                    Status
                                </th>

                                <th
                                    scope="col"
                                    class="px-4 py-3 text-right text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-border">

                            @foreach ($recoveries as $recovery)

                                @php
                                    $statusClasses =
                                        match ($recovery->status) {
                                            \App\Models\AccountRecoveryRequest::STATUS_APPROVED =>
                                                'bg-accent/10 text-accent',

                                            \App\Models\AccountRecoveryRequest::STATUS_COMPLETED =>
                                                'bg-success/10 text-success',

                                            \App\Models\AccountRecoveryRequest::STATUS_REJECTED =>
                                                'bg-error/10 text-error',

                                            \App\Models\AccountRecoveryRequest::STATUS_EXPIRED =>
                                                'bg-warning/10 text-amber-600',

                                            default =>
                                                'bg-warning/10 text-amber-600',
                                        };

                                    $statusLabel =
                                        match ($recovery->status) {
                                            \App\Models\AccountRecoveryRequest::STATUS_APPROVED =>
                                                'Authorized',

                                            \App\Models\AccountRecoveryRequest::STATUS_COMPLETED =>
                                                'Completed',

                                            \App\Models\AccountRecoveryRequest::STATUS_REJECTED =>
                                                'Rejected',

                                            \App\Models\AccountRecoveryRequest::STATUS_EXPIRED =>
                                                'Expired',

                                            default =>
                                                'Pending',
                                        };

                                    $isOwnRequest =
                                        (int) $recovery->user_id
                                        ===
                                        (int) auth()->id();
                                @endphp


                                <tr
                                    data-recovery-request-row
                                    class="align-top">

                                    {{-- Staff --}}
                                    <td class="px-4 py-4">

                                        <p
                                            class="text-sm font-semibold text-primary">
                                            {{ $recovery->user->full_name }}
                                        </p>

                                        <p
                                            class="mt-1 text-xs text-slate-500">
                                            {{ $recovery->user->email }}
                                        </p>

                                        <p
                                            class="mt-1 text-[10px] text-slate-400">

                                            {{
                                                \App\Models\User::ROLES[
                                                    $recovery->user->app_role
                                                ]
                                                ??
                                                str(
                                                    $recovery->user->app_role
                                                )->headline()
                                            }}

                                        </p>

                                    </td>


                                    {{-- Recovery --}}
                                    <td class="px-4 py-4">

                                        <p
                                            class="font-mono text-xs font-semibold text-primary">
                                            {{ $recovery->reference }}
                                        </p>

                                        <p
                                            class="mt-1 text-[10px] text-slate-400">

                                            {{
                                                $recovery->recovery_method
                                                ===
                                                \App\Models\AccountRecoveryRequest::METHOD_RECOVERY_CODE
                                                    ? 'MFA recovery code'
                                                    : 'Administrator assisted'
                                            }}

                                        </p>

                                        @if ($recovery->request_ip)

                                            <p
                                                class="mt-1 text-[10px] text-slate-400">
                                                IP {{ $recovery->request_ip }}
                                            </p>

                                        @endif

                                    </td>


                                    {{-- Requested --}}
                                    <td class="px-4 py-4">

                                        <p
                                            class="text-xs font-medium text-slate-600">

                                            {{
                                                $recovery
                                                    ->requested_at
                                                    ?->format('M d, Y')
                                            }}

                                        </p>

                                        <p
                                            class="mt-1 text-[10px] text-slate-400">

                                            {{
                                                $recovery
                                                    ->requested_at
                                                    ?->format('g:i A')
                                            }}

                                        </p>

                                        @if ($recovery->approver)

                                            <p
                                                class="mt-2 max-w-[180px] text-[10px] leading-4 text-slate-400">

                                                Reviewed by
                                                {{ $recovery->approver->full_name }}

                                            </p>

                                        @endif

                                    </td>


                                    {{-- Status --}}
                                    <td class="px-4 py-4">

                                        <span
                                            class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-semibold {{ $statusClasses }}">

                                            {{ $statusLabel }}

                                        </span>


                                        @if (
                                            $recovery->status
                                            ===
                                            \App\Models\AccountRecoveryRequest::STATUS_APPROVED
                                            &&
                                            $recovery->expires_at
                                        )

                                            <p
                                                class="mt-2 max-w-[170px] text-[10px] leading-4 text-slate-400">

                                                Reset window until
                                                {{
                                                    $recovery
                                                        ->expires_at
                                                        ->format('g:i A')
                                                }}

                                            </p>

                                        @endif

                                    </td>


                                    {{-- Actions --}}
                                    <td
                                        class="px-4 py-4 text-right">

                                        @if (
                                            $recovery->status
                                            ===
                                            \App\Models\AccountRecoveryRequest::STATUS_PENDING
                                        )

                                            @if ($isOwnRequest)

                                                <p
                                                    class="ml-auto max-w-[220px] text-[10px] leading-4 text-amber-600">

                                                    Another System Administrator
                                                    must authorize your recovery.

                                                </p>

                                            @else

                                                <div
                                                    class="flex justify-end gap-2">

                                                    <form
                                                        method="POST"
                                                        action="{{ route(
                                                            'account-recovery.admin.reject',
                                                            $recovery
                                                        ) }}">

                                                        @csrf

                                                        <button
                                                            type="submit"
                                                            class="btn-secondary">
                                                            Reject
                                                        </button>

                                                    </form>


                                                    <form
                                                        method="POST"
                                                        action="{{ route(
                                                            'account-recovery.admin.approve',
                                                            $recovery
                                                        ) }}">

                                                        @csrf

                                                        <button
                                                            type="submit"
                                                            class="btn-primary">
                                                            Verify &amp; Authorize
                                                        </button>

                                                    </form>

                                                </div>

                                            @endif

                                        @else

                                            <span
                                                class="text-xs text-slate-400">
                                                No action required
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- =================================================
                 MOBILE CARDS
            ================================================== --}}
            <div class="space-y-3 lg:hidden">

                @foreach ($recoveries as $recovery)

                    @php
                        $statusClasses =
                            match ($recovery->status) {
                                \App\Models\AccountRecoveryRequest::STATUS_APPROVED =>
                                    'bg-accent/10 text-accent',

                                \App\Models\AccountRecoveryRequest::STATUS_COMPLETED =>
                                    'bg-success/10 text-success',

                                \App\Models\AccountRecoveryRequest::STATUS_REJECTED =>
                                    'bg-error/10 text-error',

                                \App\Models\AccountRecoveryRequest::STATUS_EXPIRED =>
                                    'bg-warning/10 text-amber-600',

                                default =>
                                    'bg-warning/10 text-amber-600',
                            };

                        $statusLabel =
                            match ($recovery->status) {
                                \App\Models\AccountRecoveryRequest::STATUS_APPROVED =>
                                    'Authorized',

                                \App\Models\AccountRecoveryRequest::STATUS_COMPLETED =>
                                    'Completed',

                                \App\Models\AccountRecoveryRequest::STATUS_REJECTED =>
                                    'Rejected',

                                \App\Models\AccountRecoveryRequest::STATUS_EXPIRED =>
                                    'Expired',

                                default =>
                                    'Pending',
                            };

                        $isOwnRequest =
                            (int) $recovery->user_id
                            ===
                            (int) auth()->id();
                    @endphp


                    <article
                        data-recovery-mobile-card
                        class="card p-4">

                        <div
                            class="flex items-start justify-between gap-3">

                            <div class="min-w-0">

                                <p
                                    class="truncate text-sm font-semibold text-primary">
                                    {{ $recovery->user->full_name }}
                                </p>

                                <p
                                    class="mt-1 truncate text-xs text-slate-500">
                                    {{ $recovery->user->email }}
                                </p>

                            </div>


                            <span
                                class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-semibold {{ $statusClasses }}">

                                {{ $statusLabel }}

                            </span>

                        </div>


                        <div
                            class="mt-4 grid grid-cols-2 gap-3">

                            <div>

                                <p
                                    class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                                    Reference
                                </p>

                                <p
                                    class="mt-1 break-all font-mono text-[11px] text-primary">
                                    {{ $recovery->reference }}
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                                    Requested
                                </p>

                                <p
                                    class="mt-1 text-[11px] text-slate-600">

                                    {{
                                        $recovery
                                            ->requested_at
                                            ?->format('M d · g:i A')
                                    }}

                                </p>

                            </div>

                        </div>


                        <div
                            class="mt-3 rounded-lg bg-background/50 px-3 py-2">

                            <p
                                class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                                Recovery Method
                            </p>

                            <p
                                class="mt-1 text-[11px] text-slate-600">

                                {{
                                    $recovery->recovery_method
                                    ===
                                    \App\Models\AccountRecoveryRequest::METHOD_RECOVERY_CODE
                                        ? 'MFA recovery code'
                                        : 'Administrator assisted'
                                }}

                            </p>

                        </div>


                        @if (
                            $recovery->status
                            ===
                            \App\Models\AccountRecoveryRequest::STATUS_PENDING
                        )

                            <div
                                class="mt-4 border-t border-border pt-4">

                                @if ($isOwnRequest)

                                    <p
                                        class="text-xs leading-5 text-amber-600">

                                        Another System Administrator
                                        must authorize this request.

                                    </p>

                                @else

                                    <div
                                        class="grid grid-cols-2 gap-2">

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'account-recovery.admin.reject',
                                                $recovery
                                            ) }}">

                                            @csrf

                                            <button
                                                type="submit"
                                                class="btn-secondary w-full justify-center">
                                                Reject
                                            </button>

                                        </form>


                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'account-recovery.admin.approve',
                                                $recovery
                                            ) }}">

                                            @csrf

                                            <button
                                                type="submit"
                                                class="btn-primary w-full justify-center">
                                                Authorize
                                            </button>

                                        </form>

                                    </div>

                                @endif

                            </div>

                        @endif

                    </article>

                @endforeach

            </div>


            @if ($recoveries->hasPages())

                <div>
                    {{ $recoveries->links() }}
                </div>

            @endif

        @endif

    </section>


    {{-- =====================================================
         SECURITY POLICY
    ====================================================== --}}
    <div
        class="rounded-xl border border-accent/20 bg-accent/5 px-4 py-3">

        <div class="flex items-start gap-3">

            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">

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
                        d="M12 3l7 4v5c0 4.4-2.9 8.4-7 9.7C7.9 20.4 5 16.4 5 12V7l7-4zM9 12l2 2 4-4" />

                </svg>

            </div>


            <div>

                <p
                    class="text-xs font-semibold text-primary">
                    Recovery Security Policy
                </p>

                <p
                    class="mt-1 text-[11px] leading-5 text-slate-500">

                    Authorization never exposes or assigns a password.
                    The requester receives only a short-lived
                    15-minute reset authorization in the browser
                    session that initiated recovery. System
                    Administrators cannot approve their own recovery
                    requests.

                </p>

            </div>

        </div>

    </div>

</div>

@endsection