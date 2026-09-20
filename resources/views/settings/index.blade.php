@extends('layouts.app')

@section('title', 'Settings')

@section('content')

<div class="mx-auto max-w-7xl space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>

            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Settings
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Manage your personal profile, account security, and view application information.
            </p>

        </div>


        <div class="flex items-center gap-3 rounded-xl border border-border bg-card px-4 py-3 shadow-card">

            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary font-heading text-sm font-bold text-white">

                {{ strtoupper(substr($user->full_name ?: $user->email, 0, 1)) }}

            </div>

            <div>

                <p class="font-button text-sm font-semibold text-primary">
                    {{ $user->full_name }}
                </p>

                <p class="text-xs text-slate-400">
                    {{ \App\Models\User::ROLES[$user->app_role] ?? str($user->app_role)->headline() }}
                </p>

            </div>

        </div>

    </div>


    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">

        {{-- Profile --}}
        <section class="card overflow-hidden">

            <div class="border-b border-border bg-background/60 px-6 py-5">

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
                                d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8" />

                        </svg>

                    </div>


                    <div>

                        <h3 class="font-heading text-base font-semibold text-primary">
                            Personal Profile
                        </h3>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Update your personal and work information.
                        </p>

                    </div>

                </div>

            </div>


            <form
                method="POST"
                action="{{ route('settings.profile.update') }}"
                class="space-y-6 p-6">

                @csrf
                @method('PUT')


                <div class="grid gap-5 md:grid-cols-2">

                    {{-- Full Name --}}
                    <div>

                        <label for="full_name" class="label">
                            Full Name
                            <span class="text-error">*</span>
                        </label>

                        <input
                            id="full_name"
                            type="text"
                            name="full_name"
                            required
                            value="{{ old('full_name', $user->full_name) }}"
                            class="input @error('full_name') border-error @enderror">

                        @error('full_name')

                            <p class="mt-1 text-xs font-medium text-error">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Email --}}
                    <div>

                        <label for="email" class="label">
                            Email Address
                            <span class="text-error">*</span>
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            required
                            value="{{ old('email', $user->email) }}"
                            class="input @error('email') border-error @enderror">

                        @error('email')

                            <p class="mt-1 text-xs font-medium text-error">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Department --}}
                    <div>

                        <label for="department" class="label">
                            Department
                        </label>

                        <input
                            id="department"
                            type="text"
                            name="department"
                            value="{{ old('department', $user->department) }}"
                            placeholder="e.g. Administration"
                            class="input">

                    </div>


                    {{-- Job Title --}}
                    <div>

                        <label for="job_title" class="label">
                            Job Title
                        </label>

                        <input
                            id="job_title"
                            type="text"
                            name="job_title"
                            value="{{ old('job_title', $user->job_title) }}"
                            placeholder="e.g. Administrative Officer"
                            class="input">

                    </div>


                    {{-- Contact Number --}}
                    <div class="md:col-span-2">

                        <label for="phone" class="label">
                            Contact Number
                        </label>

                        <input
                            id="phone"
                            type="text"
                            name="phone"
                            value="{{ old('phone', $user->phone) }}"
                            placeholder="09XXXXXXXXX"
                            class="input">

                    </div>

                </div>


                <div class="flex flex-col gap-3 border-t border-border pt-5 sm:flex-row sm:items-center sm:justify-between">

                    <p class="text-xs leading-5 text-slate-400">
                        Your system role and account status can only be changed by an authorized System Administrator.
                    </p>


                    <button
                        type="submit"
                        class="btn-secondary shrink-0">

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

                        Save Changes

                    </button>

                </div>

            </form>

        </section>


        {{-- Right Column --}}
        <div class="space-y-6">

            {{-- Account --}}
            <section class="card overflow-hidden">

                <div class="border-b border-border px-5 py-4">

                    <h3 class="font-heading text-base font-semibold text-primary">
                        Account Information
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Current account access information.
                    </p>

                </div>


                <div class="divide-y divide-border">

                    <div class="flex items-center justify-between gap-4 px-5 py-4">

                        <span class="text-sm text-slate-500">
                            System Role
                        </span>

                        <span class="text-right text-sm font-semibold text-primary">
                            {{ \App\Models\User::ROLES[$user->app_role] ?? str($user->app_role)->headline() }}
                        </span>

                    </div>


                    <div class="flex items-center justify-between gap-4 px-5 py-4">

                        <span class="text-sm text-slate-500">
                            Account Status
                        </span>

                        @if ($user->is_active)

                            <span class="badge badge-success">
                                <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                Active
                            </span>

                        @else

                            <span class="badge bg-slate-100 text-slate-500">
                                Deactivated
                            </span>

                        @endif

                    </div>


                    <div class="flex items-center justify-between gap-4 px-5 py-4">

                        <span class="text-sm text-slate-500">
                            Password
                        </span>

                        @if ($user->force_password_change)

                            <span class="badge badge-warning">
                                Change Required
                            </span>

                        @else

                            <span class="badge badge-success">
                                Updated
                            </span>

                        @endif

                    </div>

                </div>

            </section>
            {{-- Security --}}
            <section class="card overflow-hidden">

                <div class="border-b border-border px-5 py-4">

                    <div class="flex items-center justify-between gap-4">

                        <div class="flex items-center gap-3">

                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary/10 text-primary">

                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 3l7 4v5c0 5-3 8-7 9-4-1-7-4-7-9V7l7-4zm-2 9l2 2 4-4" />

                                </svg>

                            </div>

                            <div>

                                <h3 class="font-heading text-base font-semibold text-primary">
                                    Security
                                </h3>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Protect your account credentials and sign-in access.
                                </p>

                            </div>

                        </div>


                        @if ($mfaEnabled)

                            <span class="badge badge-success">
                                MFA Enabled
                            </span>

                        @elseif ($mfaRequired)

                            <span class="badge badge-warning">
                                MFA Required
                            </span>

                        @elseif ($mfaSetupPending)

                            <span class="badge badge-warning">
                                Setup Pending
                            </span>

                        @else

                            <span class="badge bg-slate-100 text-slate-500">
                                MFA Disabled
                            </span>

                        @endif

                    </div>

                </div>


                <div class="space-y-5 p-5">

                    {{-- Password --}}
                    <div>

                        <div class="flex items-start gap-3">

                            <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-background text-slate-500">

                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M7 11V8a5 5 0 0110 0v3m-11 0h12v10H6V11z" />

                                </svg>

                            </div>

                            <div class="min-w-0 flex-1">

                                <p class="text-sm font-semibold text-primary">
                                    Account Password
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Change your password regularly and never share it with other staff members.
                                </p>

                            </div>

                        </div>


                        <a
                            href="{{ route('password.change') }}"
                            class="btn-secondary mt-4 w-full justify-center">

                            Change Password

                        </a>

                    </div>


                    <div class="border-t border-border"></div>


                    {{-- MFA Messages --}}
                    @error('mfa')

                        <div class="rounded-xl border border-error/30 bg-error/5 px-4 py-3">

                            <p class="text-xs font-medium text-error">
                                {{ $message }}
                            </p>

                        </div>

                    @enderror


                    @error('current_password')

                        <div class="rounded-xl border border-error/30 bg-error/5 px-4 py-3">

                            <p class="text-xs font-medium text-error">
                                {{ $message }}
                            </p>

                        </div>

                    @enderror


                    @if ($mfaRequired)

                        <div class="rounded-xl border border-warning/30 bg-warning/5 px-4 py-3">

                            <p class="text-xs font-semibold text-primary">
                                MFA is mandatory for your role.
                            </p>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Administrative, management, legal, and system administrator accounts must keep two-factor authentication enabled.
                            </p>

                        </div>

                    @endif


                    {{-- One-time Recovery Codes --}}
                    @if (! empty($mfaRecoveryCodes))

                        <div
                            id="mfa-recovery-panel"
                            class="rounded-2xl border border-warning/40 bg-warning/5 p-4">

                            <div class="flex items-start gap-3">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-warning/10 text-warning">

                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 9v4m0 4h.01M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />

                                    </svg>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-primary">
                                        Save Your Recovery Codes
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-600">
                                        These codes can restore access if you lose your authenticator.
                                        Each code works only once. Store them somewhere secure.
                                    </p>

                                </div>

                            </div>


                            <div
                                id="mfa-recovery-codes"
                                class="mt-4 grid grid-cols-2 gap-2">

                                @foreach ($mfaRecoveryCodes as $code)

                                    <code
                                        class="rounded-lg border border-border bg-card px-3 py-2 text-center font-mono text-xs font-semibold text-primary">
                                        {{ $code }}
                                    </code>

                                @endforeach

                            </div>


                            <button
                                type="button"
                                id="copy-mfa-recovery-codes"
                                class="btn-secondary mt-4 w-full justify-center">

                                Copy Recovery Codes

                            </button>


                            <p
                                id="mfa-recovery-copy-status"
                                class="mt-2 hidden text-center text-xs font-medium text-success">

                                Recovery codes copied.

                            </p>

                        </div>

                    @endif


                    {{-- Enabled State --}}
                    @if ($mfaEnabled)

                        <div>

                            <div class="flex items-start gap-3">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-success/10 text-success">

                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />

                                    </svg>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-primary">
                                        Two-Factor Authentication Enabled
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Your account requires an authenticator code or an unused recovery code after your password is accepted.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="rounded-xl border border-border bg-background/50 p-4">

                            <p class="text-sm font-semibold text-primary">
                                Regenerate Recovery Codes
                            </p>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Creating a new set immediately invalidates all previous recovery codes.
                            </p>


                            <form
                                method="POST"
                                action="{{ route('settings.mfa.recovery.regenerate') }}"
                                class="mt-4 space-y-3">

                                @csrf

                                <div>

                                    <label
                                        for="mfa_recovery_password"
                                        class="label">

                                        Current Password

                                    </label>

                                    <input
                                        id="mfa_recovery_password"
                                        type="password"
                                        name="current_password"
                                        required
                                        autocomplete="current-password"
                                        class="input"
                                        placeholder="Enter current password">

                                </div>

                                <button
                                    type="submit"
                                    class="btn-secondary w-full justify-center">

                                    Generate New Recovery Codes

                                </button>

                            </form>

                        </div>


                        @if ($mfaRequired)

                            <div class="rounded-xl border border-primary/15 bg-primary/5 p-4">

                                <p class="text-sm font-semibold text-primary">
                                    MFA Required by Security Policy
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Two-factor authentication cannot be disabled while your account has a privileged role.
                                </p>

                            </div>

                        @else

                            <div class="rounded-xl border border-error/20 bg-error/5 p-4">

                                <p class="text-sm font-semibold text-error">
                                    Disable Two-Factor Authentication
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-600">
                                    Your account will return to password-only authentication.
                                    Existing recovery codes will also become invalid.
                                </p>


                                <form
                                    method="POST"
                                    action="{{ route('settings.mfa.disable') }}"
                                    class="mt-4 space-y-3"
                                    onsubmit="return confirm('Disable two-factor authentication for your account?');">

                                    @csrf
                                    @method('DELETE')

                                    <div>

                                        <label
                                            for="mfa_disable_password"
                                            class="label">

                                            Current Password

                                        </label>

                                        <input
                                            id="mfa_disable_password"
                                            type="password"
                                            name="current_password"
                                            required
                                            autocomplete="current-password"
                                            class="input"
                                            placeholder="Enter current password">

                                    </div>

                                    <button
                                        type="submit"
                                        class="inline-flex w-full items-center justify-center rounded-lg bg-error px-4 py-2.5 font-button text-sm font-semibold text-white transition hover:opacity-90">

                                        Disable Two-Factor Authentication

                                    </button>

                                </form>

                            </div>

                        @endif


                    {{-- Authorized Setup State --}}
                    @elseif ($mfaSetupPending && $mfaSetupAuthorized)

                        <div>

                            <div class="flex items-start gap-3">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                                    <span class="font-heading text-sm font-bold">
                                        1
                                    </span>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-primary">
                                        Scan the QR Code
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Open your authenticator app and scan this code.
                                    </p>

                                </div>

                            </div>


                            @if ($mfaQrCode)

                                <div class="mt-4 flex justify-center rounded-2xl border border-border bg-white p-4">

                                    {!! $mfaQrCode !!}

                                </div>

                            @endif

                        </div>


                        <div>

                            <div class="flex items-start gap-3">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                                    <span class="font-heading text-sm font-bold">
                                        2
                                    </span>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-primary">
                                        Manual Setup Key
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        If you cannot scan the QR code, enter this key manually in your authenticator app.
                                    </p>

                                </div>

                            </div>


                            @if ($mfaSecret)

                                <div class="mt-3 rounded-xl border border-border bg-background p-3">

                                    <code
                                        id="mfa-manual-secret"
                                        class="block break-all text-center font-mono text-xs font-semibold tracking-wider text-primary">
                                        {{ $mfaSecret }}
                                    </code>

                                </div>


                                <button
                                    type="button"
                                    id="copy-mfa-secret"
                                    class="btn-secondary mt-3 w-full justify-center">

                                    Copy Setup Key

                                </button>

                            @endif

                        </div>


                        <div>

                            <div class="flex items-start gap-3">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                                    <span class="font-heading text-sm font-bold">
                                        3
                                    </span>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-primary">
                                        Confirm Authenticator
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Enter the current code shown by your authenticator app.
                                    </p>

                                </div>

                            </div>


                            <form
                                method="POST"
                                action="{{ route('settings.mfa.confirm') }}"
                                class="mt-4 space-y-3">

                                @csrf

                                <div>

                                    <label
                                        for="settings_2fa_code"
                                        class="label">

                                        Authentication Code

                                    </label>

                                    <input
                                        id="settings_2fa_code"
                                        type="text"
                                        name="2fa_code"
                                        required
                                        inputmode="numeric"
                                        pattern="[0-9]*"
                                        minlength="{{ config('two-factor.totp.digits', 6) }}"
                                        maxlength="{{ config('two-factor.totp.digits', 6) }}"
                                        autocomplete="one-time-code"
                                        class="input text-center font-mono text-lg tracking-[0.35em] @error('2fa_code') border-error @enderror"
                                        placeholder="000000">

                                    @error('2fa_code')

                                        <p class="mt-1 text-xs font-medium text-error">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>

                                <button
                                    type="submit"
                                    class="btn-primary w-full justify-center">

                                    Confirm and Enable MFA

                                </button>

                            </form>

                        </div>


                        <div class="rounded-xl border border-border bg-background/50 p-4">

                            <p class="text-xs leading-5 text-slate-500">
                                The QR code and manual setup key are available only during the password-authorized setup window.
                            </p>

                        </div>


                        <form
                            method="POST"
                            action="{{ route('settings.mfa.disable') }}"
                            class="space-y-3">

                            @csrf
                            @method('DELETE')

                            <label
                                for="mfa_cancel_password"
                                class="label">

                                Current Password to Cancel Setup

                            </label>

                            <input
                                id="mfa_cancel_password"
                                type="password"
                                name="current_password"
                                required
                                autocomplete="current-password"
                                class="input"
                                placeholder="Enter current password">

                            <button
                                type="submit"
                                class="btn-secondary w-full justify-center">

                                Cancel MFA Setup

                            </button>

                        </form>


                    {{-- Expired Pending Setup --}}
                    @elseif ($mfaSetupPending)

                        <div class="rounded-xl border border-warning/30 bg-warning/5 p-4">

                            <p class="text-sm font-semibold text-primary">
                                MFA Setup Authorization Expired
                            </p>

                            <p class="mt-1 text-xs leading-5 text-slate-600">
                                For security, the QR code and shared secret are no longer displayed.
                                Re-enter your current password to restart setup with a new secret.
                            </p>

                        </div>


                        <form
                            method="POST"
                            action="{{ route('settings.mfa.setup') }}"
                            class="space-y-3">

                            @csrf

                            <div>

                                <label
                                    for="mfa_restart_password"
                                    class="label">

                                    Current Password

                                </label>

                                <input
                                    id="mfa_restart_password"
                                    type="password"
                                    name="current_password"
                                    required
                                    autocomplete="current-password"
                                    class="input"
                                    placeholder="Enter current password">

                            </div>

                            <button
                                type="submit"
                                class="btn-primary w-full justify-center">

                                Restart MFA Setup

                            </button>

                        </form>


                    {{-- Disabled State --}}
                    @else

                        <div>

                            <div class="flex items-start gap-3">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500">

                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 11V7a4 4 0 118 0v4m-8 0h8v8h-8m-4-8H4v8h4" />

                                    </svg>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-primary">
                                        Two-Factor Authentication
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Add an authenticator app as a second verification step after your password.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <form
                            method="POST"
                            action="{{ route('settings.mfa.setup') }}"
                            class="space-y-3">

                            @csrf

                            <div>

                                <label
                                    for="mfa_setup_password"
                                    class="label">

                                    Current Password

                                </label>

                                <input
                                    id="mfa_setup_password"
                                    type="password"
                                    name="current_password"
                                    required
                                    autocomplete="current-password"
                                    class="input"
                                    placeholder="Enter current password">

                            </div>

                            <button
                                type="submit"
                                class="btn-primary w-full justify-center">

                                Enable Two-Factor Authentication

                            </button>

                        </form>

                    @endif

                </div>

            </section>

        </div>

    </div>


    {{-- Appearance --}}
    <section class="card overflow-hidden">

        <div class="border-b border-border bg-background/60 px-6 py-5">

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
                            d="M12 3v1m0 16v1M4.22 4.22l.7.7m14.16 14.16.7.7M3 12h1m16 0h1M4.22 19.78l.7-.7M19.08 4.92l.7-.7M16 12a4 4 0 11-8 0 4 4 0 018 0z" />

                    </svg>

                </div>


                <div>

                    <h3 class="font-heading text-base font-semibold text-primary">
                        Appearance
                    </h3>

                    <p class="mt-0.5 text-xs text-slate-500">
                        Choose how Holiday Travelers FAMS appears on this device.
                    </p>

                </div>

            </div>

        </div>


        <div class="grid gap-4 p-6 md:grid-cols-3">

            {{-- Light --}}
            <button
                type="button"
                data-theme-option="light"
                class="theme-choice rounded-2xl p-5 text-left">

                <div class="mb-4 flex items-center justify-between">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-secondary">

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 3v1m0 16v1M4.22 4.22l.7.7m14.16 14.16.7.7M3 12h1m16 0h1M4.22 19.78l.7-.7M19.08 4.92l.7-.7M16 12a4 4 0 11-8 0 4 4 0 018 0z" />

                        </svg>

                    </div>

                    <span
                        data-theme-check="light"
                        class="hidden text-secondary">

                        <svg class="h-5 w-5"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M5 13l4 4L19 7" />

                        </svg>

                    </span>

                </div>


                <p class="font-heading text-sm font-semibold text-primary">
                    Light
                </p>

                <p class="mt-1 text-xs leading-5 text-slate-500">
                    Sky & Sunset with bright cards and light backgrounds.
                </p>

            </button>


            {{-- Dark --}}
            <button
                type="button"
                data-theme-option="dark"
                class="theme-choice rounded-2xl p-5 text-left">

                <div class="mb-4 flex items-center justify-between">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary text-accent">

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />

                        </svg>

                    </div>

                    <span
                        data-theme-check="dark"
                        class="hidden text-secondary">

                        <svg class="h-5 w-5"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M5 13l4 4L19 7" />

                        </svg>

                    </span>

                </div>


                <p class="font-heading text-sm font-semibold text-primary">
                    Dark
                </p>

                <p class="mt-1 text-xs leading-5 text-slate-500">
                    Night Sky & Sunset with dark navy and slate surfaces.
                </p>

            </button>


            {{-- System --}}
            <button
                type="button"
                data-theme-option="system"
                class="theme-choice rounded-2xl p-5 text-left">

                <div class="mb-4 flex items-center justify-between">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-accent/10 text-accent">

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 5h16v11H4V5zm4 15h8m-4-4v4" />

                        </svg>

                    </div>

                    <span
                        data-theme-check="system"
                        class="hidden text-secondary">

                        <svg class="h-5 w-5"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M5 13l4 4L19 7" />

                        </svg>

                    </span>

                </div>


                <p class="font-heading text-sm font-semibold text-primary">
                    System
                </p>

                <p class="mt-1 text-xs leading-5 text-slate-500">
                    Automatically follows your Windows or browser appearance.
                </p>

            </button>

        </div>


        <div class="border-t border-border bg-background/40 px-6 py-4">

            <p class="text-xs text-slate-500">
                Current appearance:
                <span
                    data-theme-current
                    class="font-semibold text-primary">
                    System
                </span>
            </p>

        </div>

    </section>


    {{-- Application Information --}}
    <section class="card overflow-hidden">

        <div class="border-b border-border bg-background/60 px-6 py-5">

            <div class="flex items-center gap-3">

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-accent/10 text-accent">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 15.5A3.5 3.5 0 1012 8a3.5 3.5 0 000 7.5zm7.4-3.5a7.7 7.7 0 00-.08-1.08l2-1.55-2-3.46-2.48 1a8.5 8.5 0 00-1.87-1.08L14.6 3h-4l-.37 2.83a8.5 8.5 0 00-1.87 1.08l-2.48-1-2 3.46 2 1.55A7.7 7.7 0 005.8 12c0 .36.03.72.08 1.08l-2 1.55 2 3.46 2.48-1a8.5 8.5 0 001.87 1.08L10.6 21h4l.37-2.83a8.5 8.5 0 001.87-1.08l2.48 1 2-3.46-2-1.55c.05-.36.08-.72.08-1.08z" />

                    </svg>

                </div>


                <div>

                    <h3 class="font-heading text-base font-semibold text-primary">
                        Application Information
                    </h3>

                    <p class="mt-0.5 text-xs text-slate-500">
                        Current Facilities and Administrative Management System configuration.
                    </p>

                </div>

            </div>

        </div>


        <div class="grid divide-y divide-border md:grid-cols-2 md:divide-x md:divide-y-0 xl:grid-cols-4">

            {{-- Theme --}}
            <div class="p-5">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Interface
                </p>

                <p class="mt-2 text-sm font-semibold text-primary">
                    Sky & Sunset
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Current system design theme
                </p>

            </div>


            {{-- Timezone --}}
            <div class="p-5">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Timezone
                </p>

                <p class="mt-2 text-sm font-semibold text-primary">
                    {{ config('app.timezone') }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Application date and time zone
                </p>

            </div>


            {{-- Environment --}}
            <div class="p-5">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Environment
                </p>

                <p class="mt-2 text-sm font-semibold text-primary">
                    {{ str(app()->environment())->headline() }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Laravel {{ app()->version() }}
                </p>

            </div>


            {{-- AI --}}
            <div class="p-5">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    AI Visitor Assistant
                </p>


                <div class="mt-2 flex flex-wrap items-center gap-2">

                    <p class="text-sm font-semibold text-primary">
                        {{ str($aiProvider)->headline() }}
                    </p>


                    @if ($aiConfigured)

                        <span class="badge badge-success">
                            Connected
                        </span>

                    @else

                        <span class="badge badge-warning">
                            Fallback Mode
                        </span>

                    @endif

                </div>


                @if ($aiConfigured && $aiModel)

                    <p class="mt-1 text-xs text-slate-400">
                        Model: {{ $aiModel }}
                    </p>

                @else

                    <p class="mt-1 text-xs text-slate-400">
                        AI credentials are never displayed here.
                    </p>

                @endif

            </div>

        </div>

    </section>


    {{-- Privacy Note --}}
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
                    d="M12 9v4m0 4h.01M12 22a10 10 0 100-20 10 10 0 000 20z" />

            </svg>


            <p class="text-xs leading-5 text-slate-600">

                <span class="font-semibold text-primary">
                    Security note:
                </span>

                Sensitive application credentials such as the Gemini API key, database password, and other environment secrets are not displayed or editable from this page.

            </p>

        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const options = document.querySelectorAll('[data-theme-option]');
    const current = document.querySelector('[data-theme-current]');

    function labelFor(mode) {
        if (mode === 'light') {
            return 'Light - Sky & Sunset';
        }

        if (mode === 'dark') {
            return 'Dark - Night Sky & Sunset';
        }

        return 'System - Follow Device';
    }

    function syncThemeControls() {

        if (!window.FAMSTheme) {
            return;
        }

        const mode = window.FAMSTheme.get();

        options.forEach(function (option) {

            const optionMode = option.dataset.themeOption;
            const active = optionMode === mode;

            option.classList.toggle('is-active', active);

            const check = document.querySelector(
                '[data-theme-check="' + optionMode + '"]'
            );

            if (check) {
                check.classList.toggle('hidden', !active);
            }

        });

        if (current) {
            current.textContent = labelFor(mode);
        }
    }

    options.forEach(function (option) {

        option.addEventListener('click', function () {

            if (!window.FAMSTheme) {
                return;
            }

            window.FAMSTheme.set(option.dataset.themeOption);

            syncThemeControls();

        });

    });

    window.addEventListener(
        'fams-theme-change',
        syncThemeControls
    );

    syncThemeControls();


    /*
     * MFA secret and recovery-code copy helpers.
     *
     * These values are only present in the DOM while the backend
     * explicitly exposes them during their authorized display window.
     */
    const copySecretButton =
        document.getElementById(
            'copy-mfa-secret'
        );

    const manualSecret =
        document.getElementById(
            'mfa-manual-secret'
        );

    if (
        copySecretButton
        && manualSecret
        && navigator.clipboard
    ) {
        copySecretButton.addEventListener(
            'click',
            async function () {
                await navigator.clipboard.writeText(
                    manualSecret.textContent.trim()
                );

                const original =
                    copySecretButton.textContent;

                copySecretButton.textContent =
                    'Setup Key Copied';

                window.setTimeout(
                    function () {
                        copySecretButton.textContent =
                            original;
                    },
                    1500
                );
            }
        );
    }


    const copyRecoveryButton =
        document.getElementById(
            'copy-mfa-recovery-codes'
        );

    const recoveryContainer =
        document.getElementById(
            'mfa-recovery-codes'
        );

    const recoveryCopyStatus =
        document.getElementById(
            'mfa-recovery-copy-status'
        );

    if (
        copyRecoveryButton
        && recoveryContainer
        && navigator.clipboard
    ) {
        copyRecoveryButton.addEventListener(
            'click',
            async function () {
                const codes =
                    Array.from(
                        recoveryContainer.querySelectorAll(
                            'code'
                        )
                    )
                    .map(function (element) {
                        return element.textContent.trim();
                    })
                    .join('\n');

                await navigator.clipboard.writeText(
                    codes
                );

                if (recoveryCopyStatus) {
                    recoveryCopyStatus.classList.remove(
                        'hidden'
                    );

                    window.setTimeout(
                        function () {
                            recoveryCopyStatus.classList.add(
                                'hidden'
                            );
                        },
                        2000
                    );
                }
            }
        );
    }

});
</script>

@endsection