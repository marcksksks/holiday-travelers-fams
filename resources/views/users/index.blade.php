@extends('layouts.app')

@section('title', 'Staff Accounts')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Staff Accounts
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Create staff accounts, assign system roles, and manage account access.
            </p>
        </div>

        <div class="rounded-xl border border-border bg-card px-4 py-2.5 shadow-card">

            <p class="text-xs text-slate-500">
                Total Accounts
            </p>

            <p class="mt-0.5 font-heading text-lg font-bold text-primary">
                {{ $stats['total'] }}
            </p>

        </div>

    </div>


    {{-- Statistics --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="card relative overflow-hidden p-5">

            <div class="absolute inset-x-0 bottom-0 h-1 bg-primary"></div>

            <p class="text-xs font-medium text-slate-500">
                Staff Accounts
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-primary">
                {{ $stats['total'] }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Registered system users
            </p>

        </div>


        <div class="card relative overflow-hidden p-5">

            <div class="absolute inset-x-0 bottom-0 h-1 bg-success"></div>

            <p class="text-xs font-medium text-slate-500">
                Active
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-success">
                {{ $stats['active'] }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Accounts with system access
            </p>

        </div>


        <div class="card relative overflow-hidden p-5">

            <div class="absolute inset-x-0 bottom-0 h-1 bg-slate-400"></div>

            <p class="text-xs font-medium text-slate-500">
                Deactivated
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-slate-600">
                {{ $stats['inactive'] }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Accounts without access
            </p>

        </div>


        <div class="card relative overflow-hidden p-5">

            <div class="absolute inset-x-0 bottom-0 h-1 bg-warning"></div>

            <p class="text-xs font-medium text-slate-500">
                Password Change
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-amber-600">
                {{ $stats['password_change'] }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Temporary passwords pending
            </p>

        </div>

    </div>


    <div class="grid gap-6 xl:grid-cols-[390px_minmax(0,1fr)]">

        {{-- Create Account --}}
        <div>

            <div class="card overflow-hidden xl:sticky xl:top-6">

                <div class="border-b border-border bg-background/60 px-5 py-5">

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
                                    d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm10-4v6m3-3h-6" />

                            </svg>

                        </div>


                        <div>

                            <h3 class="font-heading text-base font-semibold text-primary">
                                Create Staff Account
                            </h3>

                            <p class="mt-0.5 text-xs text-slate-500">
                                A password change will be required after first login.
                            </p>

                        </div>

                    </div>

                </div>


                <form
                    method="POST"
                    action="{{ route('users.store') }}"
                    class="space-y-5 p-5">

                    @csrf


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
                            value="{{ old('full_name') }}"
                            placeholder="Juan Dela Cruz"
                            class="input">

                        @error('full_name')
                            <p class="mt-1 text-xs font-medium text-error">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


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
                            value="{{ old('email') }}"
                            placeholder="staff@example.com"
                            class="input">

                        @error('email')
                            <p class="mt-1 text-xs font-medium text-error">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    <div class="grid gap-4 sm:grid-cols-2">

                        <div>

                            <label for="department" class="label">
                                Department
                            </label>

                            <input
                                id="department"
                                type="text"
                                name="department"
                                value="{{ old('department') }}"
                                placeholder="Administration"
                                class="input">

                        </div>


                        <div>

                            <label for="job_title" class="label">
                                Job Title
                            </label>

                            <input
                                id="job_title"
                                type="text"
                                name="job_title"
                                value="{{ old('job_title') }}"
                                placeholder="Officer"
                                class="input">

                        </div>

                    </div>


                    <div>

                        <label for="phone" class="label">
                            Contact Number
                        </label>

                        <input
                            id="phone"
                            type="text"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="09XXXXXXXXX"
                            class="input">

                    </div>


                    <div>

                        <label for="app_role" class="label">
                            System Role
                            <span class="text-error">*</span>
                        </label>

                        <select
                            id="app_role"
                            name="app_role"
                            class="input">

                            @foreach (\App\Models\User::ROLES as $value => $label)

                                <option
                                    value="{{ $value }}"
                                    @selected(old('app_role', 'employee') === $value)>

                                    {{ $label }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div>

                        <label for="password" class="label">
                            Temporary Password
                            <span class="text-error">*</span>
                        </label>


                        <div class="relative">

                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                minlength="8"
                                autocomplete="new-password"
                                placeholder="Minimum 8 characters"
                                class="input pr-20">


                            <button
                                type="button"
                                id="toggleStaffPassword"
                                class="absolute inset-y-0 right-3 text-xs font-semibold text-primary">

                                Show

                            </button>

                        </div>


                        <p class="mt-1.5 text-xs text-slate-400">
                            The staff member must replace this temporary password after signing in.
                        </p>


                        @error('password')
                            <p class="mt-1 text-xs font-medium text-error">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    <button
                        type="submit"
                        class="btn-secondary w-full">

                        Create Account

                    </button>

                </form>

            </div>

        </div>


        {{-- Directory --}}
        <div class="min-w-0 space-y-4">

            <div>

                <h3 class="font-heading text-lg font-semibold text-primary">
                    Staff Directory
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Search staff, review account status, and manage system roles.
                </p>

            </div>


            {{-- Automatic Filters --}}
<form
    id="staffFilterForm"
    method="GET"
    action="{{ route('users.index') }}"
    class="card grid gap-3 p-4 md:grid-cols-[1fr_190px_170px_auto]">

    {{-- Automatic Search --}}
    <div class="relative">

        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

            <svg
                class="h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-4.35-4.35M19 11a8 8 0 11-16 0 8 8 0 0116 0z" />

            </svg>

        </div>

        <input
            id="staffSearch"
            type="search"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search staff..."
            autocomplete="off"
            class="input pl-10 pr-10">

        <div
            id="staffSearchIndicator"
            class="pointer-events-none absolute inset-y-0 right-0 hidden items-center pr-3">

            <svg
                class="h-4 w-4 animate-spin text-accent"
                fill="none"
                viewBox="0 0 24 24">

                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4">
                </circle>

                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                </path>

            </svg>

        </div>

    </div>


    {{-- Automatic Role Filter --}}
    <select
        id="staffRoleFilter"
        name="role"
        onchange="this.form.submit()"
        class="input">

        <option value="">
            All Roles
        </option>

        @foreach (\App\Models\User::ROLES as $value => $label)

            <option
                value="{{ $value }}"
                @selected(request('role') === $value)>

                {{ $label }}

            </option>

        @endforeach

    </select>


    {{-- Automatic Status Filter --}}
    <select
        id="staffStatusFilter"
        name="status"
        onchange="this.form.submit()"
        class="input">

        <option value="">
            All Statuses
        </option>

        <option
            value="active"
            @selected(request('status') === 'active')>

            Active

        </option>

        <option
            value="inactive"
            @selected(request('status') === 'inactive')>

            Deactivated

        </option>

    </select>


    {{-- Reset --}}
    <a
        href="{{ route('users.index') }}"
        class="btn-outline justify-center">

        Clear

    </a>

</form>


{{-- Staff Table --}}
            <div class="table-shell">

                <div class="overflow-x-auto">

                    <table class="min-w-full text-left text-sm">

                        <thead class="table-header">

                            <tr>

                                <th class="px-5 py-4 font-medium">
                                    Staff Member
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Role
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Password
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Status
                                </th>

                                <th class="px-5 py-4 text-right font-medium">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-border bg-card">

                            @forelse ($staff as $person)

                                <tr class="transition hover:bg-sky-50/40">

                                    {{-- Staff --}}
                                    <td class="px-5 py-4">

                                        <div class="flex min-w-[230px] items-center gap-3">

                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 font-heading text-sm font-bold text-primary">

                                                {{ strtoupper(substr($person->full_name ?: $person->email, 0, 1)) }}

                                            </div>


                                            <div class="min-w-0">

                                                <div class="flex items-center gap-2">

                                                    <p class="max-w-[220px] truncate font-button text-sm font-semibold text-primary">
                                                        {{ $person->full_name }}
                                                    </p>


                                                    @if ($person->id === auth()->id())

                                                        <span class="rounded-full bg-accent/10 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wide text-primary">
                                                            You
                                                        </span>

                                                    @endif

                                                </div>


                                                <p class="mt-0.5 max-w-[240px] truncate text-xs text-slate-500">
                                                    {{ $person->email }}
                                                </p>


                                                @if ($person->job_title || $person->department)

                                                    <p class="mt-1 max-w-[240px] truncate text-[10px] text-slate-400">

                                                        {{ $person->job_title ?: 'Staff' }}

                                                        @if ($person->department)
                                                            · {{ $person->department }}
                                                        @endif

                                                    </p>

                                                @endif

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Role --}}
                                    <td class="px-5 py-4">

                                        @if ($person->id === auth()->id())

                                            <span class="badge badge-info">
                                                {{ \App\Models\User::ROLES[$person->app_role] ?? str($person->app_role)->headline() }}
                                            </span>

                                        @else

                                            <form
                                                method="POST"
                                                action="{{ route('users.set-role', $person) }}"
                                                class="min-w-[180px]">

                                                @csrf

                                                <select
                                                    name="app_role"
                                                    onchange="if(confirm('Change this staff member''s system role?')) this.form.submit(); else this.value='{{ $person->app_role }}';"
                                                    class="input py-2 text-xs">

                                                    @foreach (\App\Models\User::ROLES as $value => $label)

                                                        <option
                                                            value="{{ $value }}"
                                                            @selected($person->app_role === $value)>

                                                            {{ $label }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </form>

                                        @endif

                                    </td>


                                    {{-- Password --}}
                                    <td class="px-5 py-4">

                                        @if ($person->force_password_change)

                                            <span class="badge badge-warning">
                                                Change Required
                                            </span>

                                        @else

                                            <span class="badge badge-success">
                                                Password Set
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Status --}}
                                    <td class="px-5 py-4">

                                        @if ($person->is_active)

                                            <span class="badge badge-success">
                                                <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                                Active
                                            </span>

                                        @else

                                            <span class="badge bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200">
                                                Deactivated
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Action --}}
                                    <td class="px-5 py-4 text-right">

                                        @if ($person->id === auth()->id())

                                            <span class="text-xs text-slate-400">
                                                Current account
                                            </span>

                                        @else

                                            <form
                                                method="POST"
                                                action="{{ route('users.toggle-active', $person) }}"
                                                onsubmit="return confirm('{{ $person->is_active ? 'Deactivate this staff account?' : 'Reactivate this staff account?' }}');">

                                                @csrf

                                                @if ($person->is_active)

                                                    <button
                                                        type="submit"
                                                        class="inline-flex rounded-lg border border-error/20 bg-error/5 px-3 py-2 font-button text-xs font-semibold text-error transition hover:bg-error hover:text-white">

                                                        Deactivate

                                                    </button>

                                                @else

                                                    <button
                                                        type="submit"
                                                        class="inline-flex rounded-lg bg-success/10 px-3 py-2 font-button text-xs font-semibold text-success transition hover:bg-success hover:text-white">

                                                        Reactivate

                                                    </button>

                                                @endif

                                            </form>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5" class="px-6 py-16">

                                        <div class="mx-auto max-w-sm text-center">

                                            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-accent/10 text-accent">

                                                <svg
                                                    class="h-7 w-7"
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

                                            <h3 class="font-heading text-base font-semibold text-primary">
                                                No staff accounts found
                                            </h3>

                                            <p class="mt-1 text-sm text-slate-500">
                                                No users match the selected filters.
                                            </p>

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            @if ($staff->hasPages())

                <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
                    {{ $staff->withQueryString()->links() }}
                </div>

            @endif

        </div>

    </div>


    {{-- Security Note --}}
    <div class="rounded-xl border border-warning/30 bg-warning/5 px-4 py-3">

        <p class="text-xs leading-5 text-slate-600">

            <span class="font-semibold text-primary">
                Account security:
            </span>

            Newly created staff accounts receive a temporary password and are required to change it after signing in. Role and account-status changes are recorded in the Audit Trail.

        </p>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Temporary Password Visibility
    |--------------------------------------------------------------------------
    */

    const password = document.getElementById('password');
    const passwordToggle = document.getElementById('toggleStaffPassword');

    if (password && passwordToggle) {

        passwordToggle.addEventListener('click', function () {

            const hidden = password.type === 'password';

            password.type = hidden ? 'text' : 'password';

            passwordToggle.textContent = hidden
                ? 'Hide'
                : 'Show';

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Automatic Staff Search
    |--------------------------------------------------------------------------
    */

    const filterForm = document.getElementById('staffFilterForm');
    const searchInput = document.getElementById('staffSearch');
    const searchIndicator = document.getElementById('staffSearchIndicator');

    let searchTimer;

    if (filterForm && searchInput) {

        searchInput.addEventListener('input', function () {

            clearTimeout(searchTimer);

            if (searchIndicator) {
                searchIndicator.classList.remove('hidden');
                searchIndicator.classList.add('flex');
            }

            searchTimer = setTimeout(function () {

                filterForm.submit();

            }, 500);

        });


        /*
         * Pressing Enter searches immediately.
         */
        searchInput.addEventListener('keydown', function (event) {

            if (event.key === 'Enter') {

                event.preventDefault();

                clearTimeout(searchTimer);

                filterForm.submit();

            }

        });


        /*
         * Clicking the browser search-field X also
         * automatically refreshes the directory.
         */
        searchInput.addEventListener('search', function () {

            clearTimeout(searchTimer);

            filterForm.submit();

        });

    }

});
</script>

@endsection