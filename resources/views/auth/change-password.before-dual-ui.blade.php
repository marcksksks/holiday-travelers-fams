@extends('layouts.guest')

@section('title', 'Change Password')

@section('content')

<div class="overflow-hidden rounded-2xl border border-border bg-card shadow-soft">

    {{-- Header --}}
    <div class="border-b border-border px-8 pb-6 pt-8 text-center">

        <div class="mx-auto mb-4 h-1 w-16 rounded-full bg-secondary"></div>

        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary">
            <svg
                class="h-7 w-7"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 11c1.105 0 2 .895 2 2v2a2 2 0 11-4 0v-2c0-1.105.895-2 2-2zm6 0V8a6 6 0 10-12 0v3m-1 0h14a1 1 0 011 1v8a1 1 0 01-1 1H5a1 1 0 01-1-1v-8a1 1 0 011-1z" />

            </svg>
        </div>

        <h1 class="font-heading text-2xl font-bold text-primary">
            Change Your Password
        </h1>

        <p class="mx-auto mt-2 max-w-sm text-sm leading-relaxed text-slate-500">
            Your administrator requires you to set a new password before continuing.
        </p>

    </div>


    {{-- Form --}}
    <form
        method="POST"
        action="{{ route('password.change.update') }}"
        class="space-y-5 p-8">

        @csrf
        @method('PUT')


        {{-- Current Password --}}
        <div>

            <label for="current_password" class="label">
                Current Password
            </label>

            <div class="relative">

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 11c1.105 0 2 .895 2 2v2a2 2 0 11-4 0v-2c0-1.105.895-2 2-2zm6 0V8a6 6 0 10-12 0v3m-1 0h14a1 1 0 011 1v8a1 1 0 01-1 1H5a1 1 0 01-1-1v-8a1 1 0 011-1z" />

                    </svg>

                </div>

                <input
                    id="current_password"
                    type="password"
                    name="current_password"
                    required
                    autofocus
                    autocomplete="current-password"
                    placeholder="Enter your current password"
                    class="input pl-11">

            </div>

        </div>


        {{-- New Password --}}
        <div>

            <label for="password" class="label">
                New Password
            </label>

            <div class="relative">

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4" />

                    </svg>

                </div>

                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Create a new password"
                    class="input pl-11">

            </div>

        </div>


        {{-- Confirm Password --}}
        <div>

            <label for="password_confirmation" class="label">
                Confirm New Password
            </label>

            <div class="relative">

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

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

                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Re-enter your new password"
                    class="input pl-11">

            </div>

        </div>


        {{-- Security note --}}
        <div class="flex items-start gap-3 rounded-xl border border-accent/20 bg-accent/10 px-4 py-3">

            <svg
                class="mt-0.5 h-5 w-5 shrink-0 text-accent"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

            </svg>

            <p class="text-xs leading-relaxed text-slate-600">
                Choose a secure password that you do not use for other accounts.
            </p>

        </div>


        {{-- Submit --}}
        <button
            type="submit"
            class="flex w-full items-center justify-center gap-2 rounded-xl bg-secondary px-4 py-3 font-button text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition-all duration-200 hover:bg-[#E08A3B] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-secondary/40 focus:ring-offset-2">

            Update Password

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

        </button>

    </form>

</div>


<p class="mt-5 text-center text-xs text-slate-400">
    Secure account management · Holiday Travelers Travel & Tours Inc.
</p>

@endsection
