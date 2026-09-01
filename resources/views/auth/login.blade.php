@extends('layouts.guest')

@section('title', 'Sign in')

@section('content')

<div class="overflow-hidden rounded-2xl border border-border bg-card shadow-soft">

    {{-- Header --}}
    <div class="border-b border-border px-8 pb-6 pt-8 text-center">

        <div class="mx-auto mb-4 h-1 w-16 rounded-full bg-secondary"></div>

        <h1 class="font-heading text-3xl font-bold tracking-tight text-primary">
            Holiday Travelers Travel & Tours Inc.
        </h1>

        <p class="mt-2 text-sm font-medium text-slate-500">
            Facilities & Administrative Management System
        </p>

        <p class="mt-4 text-xs uppercase tracking-[0.18em] text-accent">
            Administrative Portal
        </p>

    </div>


    {{-- Form --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-5 p-8">

        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="label">
                Email Address
            </label>

            <div class="relative">

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

                    <svg class="h-5 w-5"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />

                    </svg>

                </div>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="Enter your email address"
                    class="input pl-11">

            </div>
        </div>


        {{-- Password --}}
        <div>

            <div class="mb-1.5 flex items-center justify-between">

                <label for="password" class="font-body text-sm font-medium text-primary">
                    Password
                </label>

                @if (Route::has('password.request'))
                    <a
                        href="{{ route('password.request') }}"
                        class="text-xs font-medium text-accent transition hover:text-primary">
                        Forgot password?
                    </a>
                @endif

            </div>

            <div class="relative">

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

                    <svg class="h-5 w-5"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 11c1.105 0 2 .895 2 2v2a2 2 0 11-4 0v-2c0-1.105.895-2 2-2zm6 0V8a6 6 0 10-12 0v3m-1 0h14a1 1 0 011 1v8a1 1 0 01-1 1H5a1 1 0 01-1-1v-8a1 1 0 011-1z" />

                    </svg>

                </div>

                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your password"
                    class="input pl-11">

            </div>
        </div>


        {{-- Remember me --}}
        <div class="flex items-center">

            <input
                id="remember"
                name="remember"
                type="checkbox"
                class="h-4 w-4 rounded border-border text-primary focus:ring-accent">

            <label for="remember" class="ml-2 text-sm text-slate-600">
                Remember me
            </label>

        </div>


        {{-- Submit --}}
        <button
            type="submit"
            class="flex w-full items-center justify-center gap-2 rounded-xl bg-secondary px-4 py-3 font-button text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition-all duration-200 hover:bg-[#E08A3B] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-secondary/40 focus:ring-offset-2">

            Sign in

            <svg
                class="h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5l7 7-7 7" />

            </svg>

        </button>


        {{-- Account help --}}
        <div class="border-t border-border pt-5 text-center">

            <p class="text-xs leading-relaxed text-slate-500">
                Need access to Holiday Travelers Travel & Tours Inc.?
                <span class="font-medium text-primary">
                    Contact your system administrator.
                </span>
            </p>

        </div>

    </form>

</div>


<p class="mt-5 text-center text-xs text-slate-400">
    Secure Facilities & Administrative Management Portal
</p>

@endsection
