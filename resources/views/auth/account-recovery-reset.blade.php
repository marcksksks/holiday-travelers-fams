@extends('layouts.guest')

@section('title', 'Create New Password')

@section('content')

<div
    data-auth-workspace="account-recovery-reset"
    class="overflow-hidden rounded-2xl border border-border bg-card shadow-soft">

    <div
        class="border-b border-border px-6 pb-6 pt-7 text-center sm:px-8">

        <div
            class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-success/10 text-success">

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
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4" />

            </svg>

        </div>


        <p
            class="text-xs font-semibold uppercase tracking-[0.18em] text-secondary">
            Recovery Authorized
        </p>

        <h1
            class="mt-2 font-heading text-2xl font-bold text-primary">
            Create a new password
        </h1>

        <p
            class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
            Choose a new password for your account. Existing sessions
            and API credentials will be revoked after recovery.
        </p>

    </div>


    <form
        method="POST"
        action="{{ route('account-recovery.update') }}"
        class="space-y-5 p-6 sm:p-8">

        @csrf


        <div
            class="rounded-xl border border-border bg-background/35 px-4 py-3">

            <div
                class="flex items-center justify-between gap-3">

                <div>

                    <p
                        class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                        Recovery Reference
                    </p>

                    <p
                        class="mt-1 break-all font-mono text-xs font-semibold text-primary">
                        {{ $reference }}
                    </p>

                </div>


                <span
                    class="shrink-0 rounded-md bg-success/10 px-2 py-1 text-[9px] font-semibold uppercase tracking-wide text-success">

                    {{ $recoveryMethod === \App\Models\AccountRecoveryRequest::METHOD_RECOVERY_CODE
                        ? 'Recovery Code'
                        : 'Admin Verified' }}

                </span>

            </div>

        </div>


        <div>

            <label
                for="password"
                class="label">
                New Password
            </label>

            <input
                id="password"
                type="password"
                name="password"
                required
                autofocus
                autocomplete="new-password"
                placeholder="Create a new password"
                class="input">

            <p
                class="mt-1.5 text-[11px] leading-5 text-slate-400">
                Use at least 8 characters. A longer unique passphrase is recommended.
            </p>

        </div>


        <div>

            <label
                for="password_confirmation"
                class="label">
                Confirm New Password
            </label>

            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Re-enter your new password"
                class="input">

        </div>


        <button
            type="submit"
            class="btn-primary w-full justify-center">
            Secure My Account
        </button>


        <div
            class="rounded-xl border border-accent/15 bg-accent/5 px-4 py-3">

            <p
                class="text-[11px] leading-5 text-slate-500">
                After recovery, existing sessions, remember-me access,
                and API tokens are revoked. Administrator-assisted
                recovery also clears the previous MFA enrollment so
                protected roles must configure MFA again.
            </p>

        </div>

    </form>

</div>

@endsection