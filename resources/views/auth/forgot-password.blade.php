@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')

<div class="overflow-hidden rounded-2xl border border-border bg-card shadow-soft">

    <div class="border-b border-border px-6 pb-6 pt-7 text-center sm:px-8">

        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-accent/10 text-accent">

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
                    d="M12 11V7a4 4 0 118 0v4m-8 0h8v8h-8M4 11h4v8H4z" />

            </svg>

        </div>


        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-secondary">
            Account Recovery
        </p>

        <h1 class="mt-2 font-heading text-2xl font-bold text-primary">
            Forgot your password?
        </h1>

        <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
            Enter your account email address. If an account exists, we'll send password reset instructions.
        </p>

    </div>


    <form
        method="POST"
        action="{{ route('password.email') }}"
        class="space-y-5 p-6 sm:p-8">

        @csrf

        <div>

            <label
                for="email"
                class="label">

                Email Address

            </label>

            <div class="relative">

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

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
                            d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />

                    </svg>

                </div>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="Enter your email address"
                    class="input pl-11">

            </div>

        </div>


        <button
            type="submit"
            class="btn-primary w-full justify-center">

            Send Reset Link

        </button>


        <div class="border-t border-border pt-5 text-center">

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

    </form>

</div>

@endsection