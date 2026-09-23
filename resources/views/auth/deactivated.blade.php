@extends('layouts.guest')

@section('title', 'Account Deactivated')

@section('content')

<div class="overflow-hidden rounded-2xl border border-border bg-card shadow-soft">

    <div class="px-6 py-8 text-center sm:px-8">

        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-error/10 text-error">

            <svg
                class="h-7 w-7"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />

            </svg>

        </div>


        <p class="mt-5 text-xs font-semibold uppercase tracking-[0.18em] text-error">
            Access Restricted
        </p>

        <h1 class="mt-2 font-heading text-2xl font-bold text-primary">
            Account deactivated
        </h1>

        <p class="mx-auto mt-3 max-w-sm text-sm leading-6 text-slate-500">
            This account is currently inactive and cannot access the Facilities & Administrative Management System.
        </p>


        <div class="mt-6 rounded-xl border border-border bg-background/60 p-4 text-left">

            <p class="text-sm font-semibold text-primary">
                Need assistance?
            </p>

            <p class="mt-1 text-xs leading-5 text-slate-500">
                Contact your authorized system administrator if you believe your account should be active.
            </p>

        </div>


        <form
            method="POST"
            action="{{ route('logout') }}"
            class="mt-6">

            @csrf

            <button
                type="submit"
                class="btn-secondary w-full justify-center">

                Log out securely

            </button>

        </form>

    </div>

</div>

@endsection