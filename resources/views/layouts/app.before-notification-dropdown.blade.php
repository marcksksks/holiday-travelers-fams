<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Dashboard') · Holiday Travelers Travel & Tours Inc.</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background font-body text-slate-700 antialiased">

@php
    $navItems = [
        'dashboard' => ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard'],
        'facilities.index' => ['label' => 'Facilities', 'route' => 'facilities.index', 'icon' => 'facilities'],
        'reservations.index' => ['label' => 'Reservations', 'route' => 'reservations.index', 'icon' => 'reservations'],
        'appointments.index' => ['label' => 'Appointments', 'route' => 'appointments.index', 'icon' => 'appointments'],
        'visitors.index' => ['label' => 'Visitor Desk', 'route' => 'visitors.index', 'icon' => 'visitors'],
        'documents.index' => ['label' => 'Records Archive', 'route' => 'documents.index', 'icon' => 'documents'],
        'legal.index' => ['label' => 'Legal Records', 'route' => 'legal.index', 'icon' => 'legal'],
        'contracts.index' => ['label' => 'Contracts', 'route' => 'contracts.index', 'icon' => 'contracts'],
        'retention.index' => ['label' => 'Retention', 'route' => 'retention.index', 'icon' => 'retention'],
        'reports.index' => ['label' => 'Reports', 'route' => 'reports.index', 'icon' => 'reports'],
        'audit-trail.index' => ['label' => 'Audit Trail', 'route' => 'audit-trail.index', 'icon' => 'audit'],
        'users.index' => ['label' => 'Staff Accounts', 'route' => 'users.index', 'icon' => 'users'],
    ];

    $allowedNav = \App\Support\Rbac::navFor(auth()->user()->app_role ?? null);
@endphp

<div class="flex min-h-screen">

    {{-- =========================
         SIDEBAR
    ========================== --}}
    <aside
        data-sidebar
        class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-primary text-white transition-all duration-300 md:static">

        {{-- Logo --}}
        <div class="flex h-20 items-center justify-between border-b border-white/10 px-5">

            <a
    href="{{ route('dashboard') }}"
    class="block min-w-0 overflow-hidden">

    <div data-sidebar-label class="min-w-0">

        <div class="whitespace-nowrap font-heading text-[20px] font-bold leading-tight tracking-tight text-white">
            Holiday Travelers
        </div>

        <div class="mt-1 whitespace-nowrap font-heading text-[12px] font-medium tracking-wide text-secondary">
            Travel & Tours Inc.
        </div>

    </div>

</a>

            {{-- Mobile close --}}
            <button
                type="button"
                data-mobile-menu
                class="rounded-lg p-2 text-slate-400 hover:bg-slate-800 hover:text-white md:hidden"
                aria-label="Close navigation">

                ✕

            </button>

        </div>

        {{-- Navigation --}}
        <nav class="flex-1 space-y-2 overflow-y-auto px-4 py-6">

            <p data-sidebar-label
               class="mb-3 px-2 font-button text-[10px] font-semibold uppercase tracking-[0.18em] text-accent">
                Main Menu
            </p>

            @foreach ($navItems as $key => $item)

                @if (in_array($key, $allowedNav, true))

                    <a
                        href="{{ route($item['route']) }}"
                        class="group flex items-center gap-3 rounded-xl px-4 py-3 font-button text-sm font-medium transition-all duration-200
                        {{ request()->routeIs($item['route'])
                            ? 'border-l-4 border-secondary bg-white/10 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">

                        <x-nav-icon :name="$item['icon']" />

                        <span
                            data-sidebar-label
                            class="whitespace-nowrap">
                            {{ $item['label'] }}
                        </span>

                    </a>

                @endif

            @endforeach

        </nav>

        {{-- User information --}}
        <div class="border-t border-white/10 bg-primary/50 px-4 py-4">

            <div class="flex items-center gap-3">

                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-secondary text-sm font-semibold text-white shadow-md">
                    {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                </div>

                <div
                    data-sidebar-label
                    class="min-w-0">

                    <p class="truncate font-button text-sm font-medium text-white">
                        {{ auth()->user()->full_name }}
                    </p>

                    <p class="truncate text-xs text-accent">
                        {{ \App\Models\User::ROLES[auth()->user()->app_role] ?? auth()->user()->app_role }}
                    </p>

                </div>

            </div>

        </div>

    </aside>


    {{-- =========================
         MAIN CONTENT
    ========================== --}}
    <div class="flex min-w-0 flex-1 flex-col">

        {{-- Header --}}
        <header
            class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-border bg-card px-4 shadow-sm md:px-8">

            <div class="flex items-center gap-3">

                {{-- Desktop sidebar toggle --}}
                <button
                    type="button"
                    data-sidebar-toggle
                    class="hidden rounded-lg p-2 text-primary transition hover:bg-primary/5 md:block"
                    aria-label="Toggle sidebar"
                    title="Toggle sidebar">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />

                    </svg>

                </button>

                {{-- Mobile menu --}}
                <button
                    type="button"
                    data-mobile-menu
                    class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden"
                    aria-label="Open navigation">

                    ☰

                </button>

                <div>

                    <h1 class="font-heading text-xl font-bold text-primary">
                        @yield('title', 'Dashboard')
                    </h1>

                    <p class="hidden text-xs text-accent sm:block">
                        Facilities and Administrative Management System
                    </p>

                </div>

            </div>


            {{-- Header right --}}
            <div class="flex items-center gap-3">

                {{-- Notifications --}}
                @php
                    $unreadNotificationCount = \App\Models\AppNotification::where(
                        'recipient_email',
                        auth()->user()->email
                    )
                        ->where('is_read', false)
                        ->count();
                @endphp

                @if (Route::has('notifications.index'))
                    <a
                        href="{{ route('notifications.index') }}"
                        class="relative rounded-lg p-2 text-slate-500 transition hover:bg-accent/10 hover:text-primary"
                        title="{{ $unreadNotificationCount > 0 ? $unreadNotificationCount . ' unread notifications' : 'Notifications' }}" aria-label="{{ $unreadNotificationCount > 0 ? $unreadNotificationCount . ' unread notifications' : 'Notifications' }}">

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />

                        </svg>

                        @if ($unreadNotificationCount > 0)

                            <span
                                class="absolute -right-1 -top-1 flex h-[18px] min-w-[18px] items-center justify-center rounded-full border-2 border-white bg-secondary px-1 font-button text-[9px] font-bold leading-none text-white shadow-sm">

                                {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}

                            </span>

                        @endif

                    </a>
                @endif


                {{-- Profile --}}
                <div class="relative">

                    <button
                        type="button"
                        data-profile-button
                        class="flex items-center gap-2 rounded-lg px-2 py-1.5 transition hover:bg-primary/5">

                        <div class="hidden text-right sm:block">

                            <p class="font-button text-sm font-medium text-primary">
                                {{ auth()->user()->full_name }}
                            </p>

                            <p class="text-xs text-accent">
                                {{ \App\Models\User::ROLES[auth()->user()->app_role] ?? auth()->user()->app_role }}
                            </p>

                        </div>

                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-sm font-semibold text-white shadow-sm">

                            {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}

                        </div>

                    </button>


                    {{-- Profile dropdown --}}
                    <div
                        data-profile-menu
                        class="absolute right-0 mt-2 hidden w-52 overflow-hidden rounded-2xl border border-border bg-card shadow-soft">

                        <div class="border-b border-slate-100 px-4 py-3">

                            <p class="text-sm font-medium text-slate-900">
                                {{ auth()->user()->full_name }}
                            </p>

                            <p class="truncate text-xs text-accent">
                                {{ auth()->user()->email }}
                            </p>

                        </div>

                        @if (Route::has('password.change'))

                            <a
                                href="{{ route('password.change') }}"
                                class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                Change Password
                            </a>

                        @endif

                        <form method="POST" action="{{ route('logout') }}">

                            @csrf

                            <button
                                type="submit"
                                class="w-full px-4 py-2.5 text-left text-sm text-red-600 hover:bg-red-50">
                                Log out
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </header>


        {{-- Mobile overlay --}}
        <div
            data-sidebar-overlay
            class="fixed inset-0 z-40 hidden bg-slate-950/50 md:hidden">
        </div>


        {{-- Page content --}}
        <main class="flex-1 bg-background p-4 md:p-8 lg:p-10">

            {{-- Success --}}
            @if (session('status'))

                <div
                    data-toast
                    class="mb-5 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm">

                    <span>
                        {{ session('status') }}
                    </span>

                    <button
                        type="button"
                        data-dismiss
                        class="ml-4 text-emerald-600 hover:text-emerald-900">

                        ✕

                    </button>

                </div>

            @endif


            {{-- Validation errors --}}
            @if ($errors->any())

                <div
                    data-toast
                    class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">

                    <div class="flex justify-between">

                        <p class="font-medium">
                            Please correct the following:
                        </p>

                        <button
                            type="button"
                            data-dismiss
                            class="text-red-600 hover:text-red-900">

                            ✕

                        </button>

                    </div>

                    <ul class="mt-2 list-inside list-disc">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            @yield('content')

        </main>

    </div>

</div>

</body>
</html>







