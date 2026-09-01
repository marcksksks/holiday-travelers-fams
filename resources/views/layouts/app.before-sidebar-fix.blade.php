<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Dashboard') · Holiday Travelers Travel & Tours Inc.</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-900 antialiased">

@php
    $navItems = [
        'dashboard' => ['label' => 'Dashboard', 'route' => 'dashboard'],
        'facilities.index' => ['label' => 'Facilities', 'route' => 'facilities.index'],
        'reservations.index' => ['label' => 'Reservations', 'route' => 'reservations.index'],
        'appointments.index' => ['label' => 'Appointments', 'route' => 'appointments.index'],
        'visitors.index' => ['label' => 'Visitor Desk', 'route' => 'visitors.index'],
        'documents.index' => ['label' => 'Records Archive', 'route' => 'documents.index'],
        'legal.index' => ['label' => 'Legal Records', 'route' => 'legal.index'],
        'contracts.index' => ['label' => 'Contracts', 'route' => 'contracts.index'],
        'retention.index' => ['label' => 'Retention', 'route' => 'retention.index'],
        'reports.index' => ['label' => 'Reports', 'route' => 'reports.index'],
        'audit-trail.index' => ['label' => 'Audit Trail', 'route' => 'audit-trail.index'],
        'users.index' => ['label' => 'Staff Accounts', 'route' => 'users.index'],
    ];

    $allowedNav = \App\Support\Rbac::navFor(auth()->user()->app_role ?? null);
@endphp

<div data-app-container class="flex min-h-screen">

    {{-- Mobile overlay --}}
    <div
        data-sidebar-overlay
        class="fixed inset-0 z-40 hidden bg-slate-950/50 md:hidden">
    </div>

    {{-- SIDEBAR --}}
    <aside
        data-sidebar
        class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-slate-950 text-slate-200 transition-all duration-300 md:static md:translate-x-0 md:w-64">
        
        {{-- Sidebar Header --}}
        <div class="flex h-16 items-center justify-between border-b border-slate-800 px-4">

            <a
                href="{{ route('dashboard') }}"
                class="flex items-center gap-3 overflow-hidden text-xl font-bold tracking-tight text-white">

                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-sm font-bold text-slate-950">
                    F
                </span>

                <span data-sidebar-label class="whitespace-nowrap">
                    Holiday Travelers Travel & Tours Inc.
                </span>

            </a>

            {{-- Desktop collapse button --}}
            <button
                type="button"
                data-sidebar-toggle
                class="hidden rounded-lg p-2 text-slate-400 transition hover:bg-slate-800 hover:text-white md:block"
                aria-label="Collapse sidebar"
                title="Collapse sidebar">

                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7" />

                </svg>

            </button>

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
        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">

            @foreach ($navItems as $key => $item)

                @if (in_array($key, $allowedNav, true))

                    <a
                        href="{{ route($item['route']) }}"
                        class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                        {{ request()->routeIs($item['route'])
                            ? 'bg-slate-800 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">

                        {{-- Simple navigation icon --}}
                        <svg
                            class="h-5 w-5 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />

                        </svg>

                        <span data-sidebar-label class="whitespace-nowrap">
                            {{ $item['label'] }}
                        </span>

                    </a>

                @endif

            @endforeach

        </nav>

        {{-- User information --}}
        <div class="border-t border-slate-800 px-4 py-4">

            <div class="flex items-center gap-3">

                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-800 text-sm font-semibold text-white">
                    {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                </div>

                <div data-sidebar-label class="min-w-0">

                    <p class="truncate text-sm font-medium text-slate-200">
                        {{ auth()->user()->full_name }}
                    </p>

                    <p class="truncate text-xs text-slate-500">
                        {{ \App\Models\User::ROLES[auth()->user()->app_role] ?? auth()->user()->app_role }}
                    </p>

                </div>

            </div>

        </div>

    </aside>


    {{-- MAIN CONTENT --}}
    <div
        data-main-content
        class="flex min-w-0 flex-1 flex-col transition-all duration-300">

        {{-- Header --}}
        <header
            class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/95 px-4 backdrop-blur md:px-8">

            <div class="flex items-center gap-3">

                {{-- Mobile menu --}}
                <button
                    type="button"
                    data-mobile-menu
                    class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden"
                    aria-label="Open navigation">

                    ☰

                </button>

                <div>

                    <h1 class="text-lg font-semibold">
                        @yield('title', 'Dashboard')
                    </h1>

                    <p class="hidden text-xs text-slate-500 sm:block">
                        Facilities and Administrative Management System
                    </p>

                </div>

            </div>


            {{-- Header right side --}}
            <div class="flex items-center gap-3">

                {{-- Notifications --}}
                @if (Route::has('notifications.index'))

                    <a
                        href="{{ route('notifications.index') }}"
                        class="relative rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
                        title="Notifications">

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 01-6 0" />

                        </svg>

                    </a>

                @endif


                {{-- Profile --}}
                <div class="relative">

                    <button
                        type="button"
                        data-profile-button
                        class="flex items-center gap-2 rounded-lg px-2 py-1.5 transition hover:bg-slate-100">

                        <div class="hidden text-right sm:block">

                            <p class="text-sm font-medium text-slate-800">
                                {{ auth()->user()->full_name }}
                            </p>

                            <p class="text-xs text-slate-500">
                                {{ \App\Models\User::ROLES[auth()->user()->app_role] ?? auth()->user()->app_role }}
                            </p>

                        </div>

                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">

                            {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}

                        </div>

                    </button>


                    {{-- Profile dropdown --}}
                    <div
                        data-profile-menu
                        class="absolute right-0 mt-2 hidden w-52 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">

                        <div class="border-b border-slate-100 px-4 py-3">

                            <p class="text-sm font-medium text-slate-900">
                                {{ auth()->user()->full_name }}
                            </p>

                            <p class="truncate text-xs text-slate-500">
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


        {{-- Page --}}
        <main class="flex-1 p-4 md:p-8">

            {{-- Success message --}}
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