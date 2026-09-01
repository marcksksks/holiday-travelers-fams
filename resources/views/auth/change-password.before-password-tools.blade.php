@extends(($forced ?? false) ? 'layouts.guest' : 'layouts.app')

@section('title', 'Change Password')

@section('content')

@php
    $isForced = $forced ?? false;
@endphp


@if (!$isForced)

    <div class="mx-auto max-w-3xl">

        {{-- Logged-in page introduction --}}
        <div class="mb-6">

            <p class="text-sm leading-relaxed text-slate-500">
                Manage your account security and update your current password.
            </p>

        </div>

@endif


<div class="{{ $isForced ? 'overflow-hidden rounded-2xl border border-border bg-card shadow-soft' : 'max-w-xl overflow-hidden rounded-2xl border border-border bg-card shadow-card' }}">

    {{-- Card Header --}}
    <div class="border-b border-border px-6 pb-6 pt-7 sm:px-8">

        <div class="flex items-start gap-4">

            {{-- Lock Icon --}}
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">

                <svg
                    class="h-6 w-6"
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


            <div class="min-w-0">

                <h2 class="font-heading text-xl font-bold text-primary">

                    @if ($isForced)
                        Change Your Password
                    @else
                        Account Security
                    @endif

                </h2>

                <p class="mt-1 text-sm leading-relaxed text-slate-500">

                    @if ($isForced)
                        Your administrator requires you to set a new password before continuing.
                    @else
                        Update your password to help keep your Holiday Travelers Travel & Tours Inc. account secure.
                    @endif

                </p>

            </div>

        </div>

    </div>


    {{-- Password Form --}}
    <form
        method="POST"
        action="{{ route('password.change.update') }}"
        class="space-y-5 p-6 sm:p-8">

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


        {{-- Confirm New Password --}}
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


        {{-- Security Information --}}
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

            <div class="text-xs leading-relaxed text-slate-600">

                <p class="font-medium text-primary">
                    Password security
                </p>

                <p class="mt-0.5">
                    Use a strong password that is different from passwords used on your other accounts.
                </p>

            </div>

        </div>


        {{-- Submit --}}
        <div class="pt-1">

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

        </div>

    </form>

</div>


@if (!$isForced)

    </div>

@else

    <p class="mt-5 text-center text-xs text-slate-400">
        Secure account management · Holiday Travelers Travel & Tours Inc.
    </p>

@endif


@endsection
