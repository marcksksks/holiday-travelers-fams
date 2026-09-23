@extends('layouts.guest')

@section('title', 'Recovery Status')

@section('content')

@php
    $statusConfig = match ($recoveryStatus) {
        \App\Models\AccountRecoveryRequest::STATUS_APPROVED => [
            'label' => 'Authorized',
            'title' => 'Recovery authorized',
            'message' => 'Your identity verification is complete. Create a new password before the authorization expires.',
            'surface' => 'bg-success/10 text-success',
        ],

        \App\Models\AccountRecoveryRequest::STATUS_REJECTED => [
            'label' => 'Rejected',
            'title' => 'Recovery was not authorized',
            'message' => 'The recovery request was not approved. If you still need access, submit a new request or contact your System Administrator.',
            'surface' => 'bg-error/10 text-error',
        ],

        \App\Models\AccountRecoveryRequest::STATUS_EXPIRED => [
            'label' => 'Expired',
            'title' => 'Recovery request expired',
            'message' => 'This recovery request or authorization is no longer valid. Start a new recovery request.',
            'surface' => 'bg-warning/10 text-amber-600',
        ],

        \App\Models\AccountRecoveryRequest::STATUS_COMPLETED => [
            'label' => 'Completed',
            'title' => 'Recovery completed',
            'message' => 'The password recovery process has already been completed.',
            'surface' => 'bg-success/10 text-success',
        ],

        default => [
            'label' => 'Pending',
            'title' => 'Verification pending',
            'message' => 'Your request is waiting for System Administrator verification. You can safely refresh this page to check the latest status.',
            'surface' => 'bg-accent/10 text-accent',
        ],
    };
@endphp


<div
    data-auth-workspace="account-recovery-status"
    class="overflow-hidden rounded-2xl border border-border bg-card shadow-soft">

    <div
        class="border-b border-border px-6 pb-6 pt-7 text-center sm:px-8">

        <div
            class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl {{ $statusConfig['surface'] }}">

            @if ($recoveryStatus === \App\Models\AccountRecoveryRequest::STATUS_APPROVED)

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
                        d="m5 13 4 4L19 7" />

                </svg>

            @else

                <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />

                </svg>

            @endif

        </div>


        <p
            class="text-xs font-semibold uppercase tracking-[0.18em] text-secondary">
            Account Recovery
        </p>

        <h1
            class="mt-2 font-heading text-2xl font-bold text-primary">
            {{ $statusConfig['title'] }}
        </h1>

        <p
            class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
            {{ $statusConfig['message'] }}
        </p>

    </div>


    <div class="space-y-5 p-6 sm:p-8">

        <div
            class="rounded-xl border border-border bg-background/35 p-4">

            <div
                class="flex items-center justify-between gap-4">

                <div>

                    <p
                        class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                        Recovery Reference
                    </p>

                    <p
                        class="mt-1 break-all font-mono text-sm font-semibold text-primary">
                        {{ $reference }}
                    </p>

                </div>


                <span
                    class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-semibold {{ $statusConfig['surface'] }}">
                    {{ $statusConfig['label'] }}
                </span>

            </div>

        </div>


        @if ($canReset)

            <a
                href="{{ route('account-recovery.reset') }}"
                class="btn-primary w-full justify-center">
                Create New Password
            </a>

        @elseif ($recoveryStatus === \App\Models\AccountRecoveryRequest::STATUS_PENDING)

            <a
                href="{{ route('account-recovery.status') }}"
                class="btn-primary w-full justify-center">
                Refresh Status
            </a>

            <p
                class="text-center text-[11px] leading-5 text-slate-400">
                Keep this browser session available while the request
                is being reviewed. The reference is for tracking only
                and cannot reset the account by itself.
            </p>

        @elseif (
            $recoveryStatus === \App\Models\AccountRecoveryRequest::STATUS_REJECTED
            ||
            $recoveryStatus === \App\Models\AccountRecoveryRequest::STATUS_EXPIRED
        )

            <a
                href="{{ route('password.request') }}"
                class="btn-primary w-full justify-center">
                Start New Recovery
            </a>

        @else

            <a
                href="{{ route('login') }}"
                class="btn-primary w-full justify-center">
                Return to Sign In
            </a>

        @endif


        <div
            class="border-t border-border pt-5 text-center">

            <a
                href="{{ route('login') }}"
                class="text-sm font-medium text-accent transition hover:text-primary">
                Back to sign in
            </a>

        </div>

    </div>

</div>

@endsection