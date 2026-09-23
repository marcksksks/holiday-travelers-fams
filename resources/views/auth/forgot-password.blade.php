@extends('layouts.guest')

@section('title', 'Secure Account Recovery')

@section('content')

<div
    data-auth-workspace="account-recovery"
    class="overflow-hidden rounded-2xl border border-border bg-card shadow-soft">

    <div
        class="border-b border-border px-6 pb-6 pt-7 text-center sm:px-8">

        <div
            class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-accent/10 text-accent">

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
                    d="M12 11V7a4 4 0 118 0v4m-8 0h8v8h-8M4 11h4v8H4z" />

            </svg>

        </div>


        <p
            class="text-xs font-semibold uppercase tracking-[0.18em] text-secondary">
            Secure Account Recovery
        </p>

        <h1
            class="mt-2 font-heading text-2xl font-bold text-primary">
            Recover your account
        </h1>

        <p
            class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
            Choose a secure recovery method. Your password is never
            sent by email or exposed to an administrator.
        </p>

    </div>


    <div class="space-y-5 p-6 sm:p-8">

        {{-- Administrator-assisted recovery --}}
        <section
            aria-labelledby="admin-recovery-heading"
            class="rounded-xl border border-border bg-background/35 p-4">

            <div class="flex gap-3">

                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">

                    <svg
                        class="h-4.5 w-4.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m7-10a4 4 0 100-8 4 4 0 000 8zm8 3v6m3-3h-6" />

                    </svg>

                </div>


                <div class="min-w-0">

                    <h2
                        id="admin-recovery-heading"
                        class="font-button text-sm font-semibold text-primary">
                        Administrator Recovery
                    </h2>

                    <p
                        class="mt-1 text-xs leading-5 text-slate-500">
                        Submit a recovery request for verification by a
                        System Administrator.
                    </p>

                </div>

            </div>


            <form
                method="POST"
                action="{{ route('account-recovery.request') }}"
                class="mt-4 space-y-4">

                @csrf

                <div>

                    <label
                        for="admin-recovery-email"
                        class="label">
                        Account Email
                    </label>

                    <div class="relative">

                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

                            <svg
                                class="h-4.5 w-4.5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />

                            </svg>

                        </div>

                        <input
                            id="admin-recovery-email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            placeholder="Enter your account email"
                            class="input pl-11">

                    </div>

                </div>


                <button
                    type="submit"
                    class="btn-primary w-full justify-center">
                    Request Administrator Recovery
                </button>

            </form>

        </section>


        {{-- Recovery-code method --}}
        <details
            class="group rounded-xl border border-border bg-card">

            <summary
                class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3.5">

                <div class="flex min-w-0 items-center gap-3">

                    <div
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-accent/10 text-accent">

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M15 7a4 4 0 11-7.9 1M9 11l-6 6v4h4l6-6m4-8 2-2" />

                        </svg>

                    </div>

                    <div class="min-w-0">

                        <p
                            class="font-button text-sm font-semibold text-primary">
                            Have an MFA recovery code?
                        </p>

                        <p
                            class="mt-0.5 text-[11px] text-slate-500">
                            Recover immediately using one unused backup code.
                        </p>

                    </div>

                </div>


                <svg
                    class="h-4 w-4 shrink-0 text-slate-400 transition group-open:rotate-180"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m6 9 6 6 6-6" />

                </svg>

            </summary>


            <form
                method="POST"
                action="{{ route('account-recovery.recovery-code') }}"
                class="space-y-4 border-t border-border px-4 pb-4 pt-4">

                @csrf

                <div>

                    <label
                        for="recovery-code-email"
                        class="label">
                        Account Email
                    </label>

                    <input
                        id="recovery-code-email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        placeholder="Enter your account email"
                        class="input">

                </div>


                <div>

                    <label
                        for="recovery-code"
                        class="label">
                        MFA Recovery Code
                    </label>

                    <input
                        id="recovery-code"
                        type="text"
                        name="recovery_code"
                        required
                        autocomplete="one-time-code"
                        autocapitalize="none"
                        spellcheck="false"
                        placeholder="Enter an unused recovery code"
                        class="input font-mono">

                    <p
                        class="mt-1.5 text-[11px] leading-5 text-slate-400">
                        A recovery code can only be used once.
                    </p>

                </div>


                <button
                    type="submit"
                    class="btn-secondary w-full justify-center">
                    Use Recovery Code
                </button>

            </form>

        </details>


        <div
            class="rounded-xl border border-accent/15 bg-accent/5 px-4 py-3">

            <div class="flex gap-2.5">

                <svg
                    class="mt-0.5 h-4 w-4 shrink-0 text-accent"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M12 3 4 6v5c0 5 3.4 8.7 8 10 4.6-1.3 8-5 8-10V6l-8-3z" />

                </svg>

                <p
                    class="text-[11px] leading-5 text-slate-500">
                    Recovery requests use generic responses to protect
                    account privacy. Administrators can authorize a reset,
                    but they can never view or choose your new password.
                </p>

            </div>

        </div>


        <div
            class="border-t border-border pt-5 text-center">

            <a
                href="{{ route('login') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-accent transition hover:text-primary">

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

                Back to sign in

            </a>

        </div>

    </div>

</div>

@endsection