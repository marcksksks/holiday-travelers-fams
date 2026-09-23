@extends('layouts.guest')

@section('title', 'Set a New Password')

@section('content')

<div class="overflow-hidden rounded-2xl border border-border bg-card shadow-soft">

    <div class="border-b border-border px-6 pb-6 pt-7 text-center sm:px-8">

        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">

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
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4" />

            </svg>

        </div>

        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-secondary">
            Secure Recovery
        </p>

        <h1 class="mt-2 font-heading text-2xl font-bold text-primary">
            Set a new password
        </h1>

        <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
            Create a strong replacement password for your Holiday Travelers account.
        </p>

    </div>


    <form
        method="POST"
        action="{{ route('password.update') }}"
        class="space-y-5 p-6 sm:p-8">

        @csrf

        <input
            type="hidden"
            name="token"
            value="{{ $token }}">


        <div>

            <label
                for="email"
                class="label">

                Email Address

            </label>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ $email ?? old('email') }}"
                required
                autofocus
                autocomplete="email"
                class="input">

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
                autocomplete="new-password"
                placeholder="Create a new password"
                class="input">

            <p class="mt-1.5 text-[11px] leading-5 text-slate-400">
                Use at least 8 characters and combine uppercase, lowercase, numbers, and symbols.
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

            Reset Password

        </button>

    </form>

</div>

@endsection