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
                                Protect your account credentials.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-5">

                    <p class="text-sm leading-6 text-slate-500">
                        Change your password regularly and avoid sharing your credentials with other staff members.
                    </p>


                    <a
                        href="{{ route('password.change') }}"
                        class="btn-primary mt-4 w-full justify-center">

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

                        Change Password

                    </a>

                </div>

            </section>

        </div>

    </div>


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

@endsection