<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Dashboard') · Holiday Travelers Travel & Tours Inc.</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Appearance: Light / Dark / System --}}
    <script>
        (function () {
            const storageKey = 'fams-appearance';
            const root = document.documentElement;
            const media = window.matchMedia('(prefers-color-scheme: dark)');

            function getMode() {
                try {
                    const saved = localStorage.getItem(storageKey);

                    return ['light', 'dark', 'system'].includes(saved)
                        ? saved
                        : 'system';
                } catch (error) {
                    return 'system';
                }
            }

            function applyMode(mode) {
                const dark = mode === 'dark'
                    || (mode === 'system' && media.matches);

                root.classList.toggle('dark', dark);

                root.dataset.theme = mode;
                root.dataset.themeEffective = dark ? 'dark' : 'light';
            }

            function setMode(mode) {
                if (! ['light', 'dark', 'system'].includes(mode)) {
                    mode = 'system';
                }

                try {
                    localStorage.setItem(storageKey, mode);
                } catch (error) {
                    // Continue using the selected theme for this page.
                }

                applyMode(mode);

                window.dispatchEvent(new CustomEvent('fams-theme-change', {
                    detail: {
                        mode: mode,
                        effective: root.dataset.themeEffective
                    }
                }));
            }

            applyMode(getMode());

            if (typeof media.addEventListener === 'function') {
                media.addEventListener('change', function () {
                    if (getMode() === 'system') {
                        applyMode('system');

                        window.dispatchEvent(new CustomEvent('fams-theme-change', {
                            detail: {
                                mode: 'system',
                                effective: root.dataset.themeEffective
                            }
                        }));
                    }
                });
            }

            window.FAMSTheme = {
                get: getMode,
                set: setMode,
                apply: applyMode
            };
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="overflow-x-hidden bg-background font-body text-slate-700 antialiased">

<a
    href="#main-content"
    class="fams-skip-link">
    Skip to main content
</a>

@php
    $navItems = [
        'dashboard' => [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'icon' => 'dashboard',
            'active' => ['dashboard'],
        ],

        'facilities.index' => [
            'label' => 'Facilities Reservation',
            'route' => 'facilities.index',
            'icon' => 'facilities',
            'active' => [
                'facilities.*',
                'reservations.*',
            ],
        ],

        'appointments.index' => [
            'label' => 'Appointments',
            'route' => 'appointments.index',
            'icon' => 'appointments',
            'active' => ['appointments.*'],
        ],

        'visitors.index' => [
            'label' => 'Visitor Desk',
            'route' => 'visitors.index',
            'icon' => 'visitors',
            'active' => ['visitors.*'],
        ],

        'documents.index' => [
            'label' => 'Document Management',
            'route' => 'documents.index',
            'icon' => 'documents',
            'active' => ['documents.*'],
        ],

        'retention.index' => [
            'label' => 'Retention',
            'route' => 'retention.index',
            'icon' => 'retention',
            'active' => ['retention.*'],
        ],

        'legal.index' => [
            'label' => 'Legal Management',
            'route' => 'legal.index',
            'icon' => 'legal',
            'active' => ['legal.*'],
        ],

        'contracts.index' => [
            'label' => 'Contracts',
            'route' => 'contracts.index',
            'icon' => 'contracts',
            'active' => ['contracts.*'],
        ],

        'reports.index' => [
            'label' => 'Reports',
            'route' => 'reports.index',
            'icon' => 'reports',
            'active' => ['reports.*'],
        ],

        'audit-trail.index' => [
            'label' => 'Audit Trail',
            'route' => 'audit-trail.index',
            'icon' => 'audit',
            'active' => ['audit-trail.*'],
        ],

        'privacy-requests.index' => [
            'label' => 'Privacy Reviews',
            'route' => 'privacy-requests.index',
            'icon' => 'audit',
            'active' => [
                'privacy-requests.*',
            ],
        ],

        'users.index' => [
            'label' => 'Staff Accounts',
            'route' => 'users.index',
            'icon' => 'users',
            'active' => ['users.*'],
        ],

        'settings.index' => [
            'label' => 'Settings',
            'route' => 'settings.index',
            'icon' => 'settings',
            'active' => ['settings.*'],
        ],
    ];


    $navGroups = [
        [
            'label' => 'Overview',
            'items' => [
                'dashboard',
            ],
        ],

        [
            'label' => 'Operations',
            'items' => [
                'facilities.index',
                'appointments.index',
                'visitors.index',
            ],
        ],

        [
            'label' => 'Records & Compliance',
            'items' => [
                'documents.index',
                'retention.index',
            ],
        ],

        [
            'label' => 'Legal & Contracts',
            'items' => [
                'legal.index',
                'contracts.index',
            ],
        ],

        [
            'label' => 'Management',
            'items' => [
                'reports.index',
                'audit-trail.index',
                'privacy-requests.index',
            ],
        ],

        [
            'label' => 'Administration',
            'items' => [
                'users.index',
            ],
        ],

        [
            'label' => 'Account',
            'items' => [
                'settings.index',
            ],
        ],
    ];


    $allowedNav =
        \App\Support\Rbac::navFor(
            auth()->user()->app_role ?? null
        );
@endphp

<div class="flex min-h-dvh">

    {{-- =========================
         MODERN SIDEBAR
    ========================== --}}
    <aside
        id="application-sidebar"
        aria-label="Primary navigation"
        data-sidebar
        data-collapsed="false"
        class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-primary text-white shadow-2xl transition-all duration-300 ease-out md:sticky md:top-0 md:bottom-auto md:h-screen md:self-start md:translate-x-0">

        {{-- =====================================================
             BRAND
        ====================================================== --}}
        <div class="shrink-0 border-b border-white/10 px-4 py-4">

            <div class="flex items-center justify-between gap-3">

                <a
                    href="{{ route('dashboard') }}"
                    data-sidebar-tooltip="Holiday Travelers"
                    aria-label="Holiday Travelers Dashboard"
                    class="group flex min-w-0 items-center gap-3">

                    <div
                        class="company-logo-shell flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white p-1.5 shadow-lg ring-1 ring-white/20">

                        <img
                            src="{{ asset('images/holiday-travelers-mark.png') }}"
                            alt="Holiday Travelers logo"
                            class="h-full w-full object-contain">

                    </div>


                    <div
                        data-sidebar-label
                        class="min-w-0">

                        <p class="truncate font-heading text-sm font-bold tracking-tight text-white">
                            Holiday Travelers
                        </p>

                        <p class="mt-0.5 truncate text-[9px] font-semibold uppercase tracking-[0.14em] text-secondary">
                            Facilities & Admin System
                        </p>

                    </div>

                </a>


                {{-- Mobile close --}}
                <button
                    type="button"
                    data-mobile-menu
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-white/60 transition hover:bg-white/10 hover:text-white md:hidden"
                    aria-controls="application-sidebar"
                    aria-expanded="false"
                    aria-label="Close navigation">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />

                    </svg>

                </button>

            </div>

        </div>


        {{-- =====================================================
             NAVIGATION
        ====================================================== --}}
        <nav
            aria-label="Primary"
            class="min-h-0 flex-1 overflow-y-auto px-3 py-4">

            <div class="space-y-5">

                @foreach ($navGroups as $group)

                    @php
                        $visibleNavKeys =
                            array_values(
                                array_filter(
                                    $group['items'],
                                    fn ($key) =>
                                        isset($navItems[$key])
                                        &&
                                        in_array(
                                            $key,
                                            $allowedNav,
                                            true
                                        )
                                )
                            );
                    @endphp


                    @if (count($visibleNavKeys) > 0)

                        <section>

                            <p
                                data-sidebar-label
                                class="mb-1.5 px-3 text-[9px] font-semibold uppercase tracking-[0.18em] text-white/35">

                                {{ $group['label'] }}

                            </p>


                            <div class="space-y-1">

                                @foreach ($visibleNavKeys as $key)

                                    @php
                                        $item =
                                            $navItems[$key];

                                        $isActive =
                                            false;

                                        foreach (
                                            $item['active']
                                                ?? [$item['route']]
                                            as $activePattern
                                        ) {
                                            if (
                                                request()->routeIs(
                                                    $activePattern
                                                )
                                            ) {
                                                $isActive =
                                                    true;

                                                break;
                                            }
                                        }
                                    @endphp


                                    <a
                                        href="{{ route($item['route']) }}"
                                        data-sidebar-nav-link
                                        data-sidebar-tooltip="{{ $item['label'] }}"
                                        aria-label="{{ $item['label'] }}"
                                        @if ($isActive)
                                            aria-current="page"
                                        @endif
                                        @class([
                                            'group relative flex min-h-11 items-center gap-3 overflow-hidden rounded-xl px-3 py-2.5 font-button text-[13px] font-medium transition-all duration-200',
                                            'bg-white/10 text-white shadow-sm ring-1 ring-inset ring-white/10' => $isActive,
                                            'text-white/65 hover:bg-white/5 hover:text-white' => ! $isActive,
                                        ])>

                                        {{-- Active orange rail --}}
                                        @if ($isActive)

                                            <span class="absolute inset-y-2 left-0 w-1 rounded-r-full bg-secondary">
                                            </span>

                                        @endif


                                        {{-- Icon --}}
                                        <span
                                            @class([
                                                'flex h-8 w-8 shrink-0 items-center justify-center rounded-lg transition-all duration-200',
                                                'bg-secondary/15 text-secondary' => $isActive,
                                                'bg-white/5 text-white/60 group-hover:bg-white/10 group-hover:text-white' => ! $isActive,
                                            ])>

                                            <x-nav-icon :name="$item['icon']" />

                                        </span>


                                        {{-- Label --}}
                                        <span
                                            data-sidebar-label
                                            class="min-w-0 flex-1 truncate">

                                            {{ $item['label'] }}

                                        </span>


                                        {{-- Active indicator --}}
                                        @if ($isActive)

                                            <span
                                                data-sidebar-label
                                                class="hidden h-1.5 w-1.5 shrink-0 rounded-full bg-secondary shadow-sm">
                                            </span>

                                        @endif

                                    </a>

                                @endforeach

                            </div>

                        </section>

                    @endif

                @endforeach

            </div>

        </nav>


        {{-- =====================================================
             USER
        ====================================================== --}}
        <div class="shrink-0 border-t border-white/10 p-3">

            <div data-sidebar-user-card class="rounded-xl border border-white/10 bg-white/5 p-3">

                <div class="flex items-center gap-3">

                    <div class="relative shrink-0">

                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-secondary font-button text-sm font-semibold text-white shadow-lg">

                            {{
                                strtoupper(
                                    substr(
                                        auth()->user()->full_name,
                                        0,
                                        1
                                    )
                                )
                            }}

                        </div>


                        <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-primary bg-success">
                        </span>

                    </div>


                    <div
                        data-sidebar-label
                        class="min-w-0 flex-1">

                        <p class="truncate font-button text-xs font-semibold text-white">

                            {{ auth()->user()->full_name }}

                        </p>

                        <p class="mt-0.5 truncate text-[10px] text-white/45">

                            {{
                                \App\Models\User::ROLES[
                                    auth()->user()->app_role
                                ]
                                ??
                                auth()->user()->app_role
                            }}

                        </p>

                    </div>

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
            data-app-header
            class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-border bg-card px-4 shadow-sm md:h-20 md:px-8">

            <div class="flex items-center gap-3">

                {{-- Desktop sidebar toggle --}}
                <button
                    type="button"
                    data-sidebar-toggle
                    class="hidden rounded-lg p-2 text-primary transition hover:bg-primary/5 md:block"
                    aria-controls="application-sidebar"
                    aria-expanded="true"
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
                            d="M4 6h16M4 12h16M4 18h16" />

                    </svg>

                </button>

                {{-- Mobile menu --}}
                <button
                    type="button"
                    data-mobile-menu
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-primary transition hover:bg-primary/5 md:hidden"
                    aria-controls="application-sidebar"
                    aria-expanded="false"
                    aria-label="Open navigation">

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
                            d="M4 6h16M4 12h16M4 18h16" />

                    </svg>

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
                {{-- Global search --}}
                <x-global-search />

                {{-- Light / dark appearance toggle --}}
                <button
                    type="button"
                    data-theme-toggle
                    aria-label="Switch to dark mode"
                    aria-pressed="false"
                    title="Switch to dark mode"
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-accent/40">

                    {{-- Moon: shown while interface is light --}}
                    <svg
                        data-theme-icon-dark
                        class="h-[17px] w-[17px]"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />

                    </svg>


                    {{-- Sun: shown while interface is dark --}}
                    <svg
                        data-theme-icon-light
                        class="hidden h-[18px] w-[18px]"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <circle
                            cx="12"
                            cy="12"
                            r="4"
                            stroke-width="1.8">
                        </circle>

                        <path
                            stroke-linecap="round"
                            stroke-width="1.8"
                            d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.65 17.65l1.42 1.42M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.65 6.35l1.42-1.42">
                        </path>

                    </svg>

                </button>


                @once
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {

                            const button =
                                document.querySelector(
                                    '[data-theme-toggle]'
                                );

                            if (! button) {
                                return;
                            }

                            const moon =
                                button.querySelector(
                                    '[data-theme-icon-dark]'
                                );

                            const sun =
                                button.querySelector(
                                    '[data-theme-icon-light]'
                                );


                            const effectiveTheme = () =>
                                document
                                    .documentElement
                                    .dataset
                                    .themeEffective
                                ===
                                'dark'
                                    ? 'dark'
                                    : 'light';


                            const syncToggle = () => {

                                const dark =
                                    effectiveTheme()
                                    ===
                                    'dark';

                                moon?.classList.toggle(
                                    'hidden',
                                    dark
                                );

                                sun?.classList.toggle(
                                    'hidden',
                                    ! dark
                                );

                                const label =
                                    dark
                                        ? 'Switch to light mode'
                                        : 'Switch to dark mode';

                                button.setAttribute(
                                    'aria-label',
                                    label
                                );

                                button.setAttribute(
                                    'title',
                                    label
                                );

                                button.setAttribute(
                                    'aria-pressed',
                                    String(dark)
                                );
                            };


                            button.addEventListener(
                                'click',
                                () => {

                                    if (! window.FAMSTheme) {
                                        return;
                                    }

                                    const nextMode =
                                        effectiveTheme()
                                        ===
                                        'dark'
                                            ? 'light'
                                            : 'dark';

                                    window
                                        .FAMSTheme
                                        .set(
                                            nextMode
                                        );

                                    syncToggle();
                                }
                            );


                            window.addEventListener(
                                'fams-theme-change',
                                syncToggle
                            );

                            syncToggle();

                        });
                    </script>
                @endonce


                {{-- Global real-time system clock --}}
                <x-system-clock />

                {{-- Notifications --}}
                @php
                    $notificationBaseQuery = \App\Models\AppNotification::where(
                        'recipient_email',
                        auth()->user()->email
                    );

                    $unreadNotificationCount = (clone $notificationBaseQuery)
                        ->where('is_read', false)
                        ->count();

                    $headerNotifications = (clone $notificationBaseQuery)
                        ->orderByDesc('created_at')
                        ->limit(5)
                        ->get();
                @endphp

                @if (Route::has('notifications.index'))

                    <div class="relative">

                        {{-- Bell --}}
                        <button
                            type="button"
                            data-notification-button
                            class="relative rounded-lg p-2 text-slate-500 transition hover:bg-accent/10 hover:text-primary"
                            aria-controls="notification-menu"
                            aria-label="{{ $unreadNotificationCount > 0 ? $unreadNotificationCount . ' unread notifications' : 'Notifications' }}"
                            aria-expanded="false"
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
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />

                            </svg>

                            @if ($unreadNotificationCount > 0)

                                <span
                                    class="absolute -right-1 -top-1 flex h-[18px] min-w-[18px] items-center justify-center rounded-full border-2 border-white bg-secondary px-1 font-button text-[9px] font-bold leading-none text-white shadow-sm">

                                    {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}

                                </span>

                            @endif

                        </button>


                        {{-- Dropdown --}}
                        <div
                            id="notification-menu"
                            data-notification-menu
                            role="region"
                            aria-label="Recent notifications"
                            class="fixed inset-x-4 top-[4.5rem] z-[70] hidden w-auto overflow-hidden rounded-xl border border-border bg-card shadow-soft sm:absolute sm:inset-x-auto sm:right-0 sm:top-full sm:mt-3 sm:w-96 sm:max-w-[calc(100vw-2rem)] sm:rounded-2xl">

                            {{-- Header --}}
                            <div class="border-b border-border px-5 py-4">

                                <div class="flex items-center gap-2">

                                    <h3 class="font-heading text-sm font-semibold text-primary">
                                        Notifications
                                    </h3>


                                    @if ($unreadNotificationCount > 0)

                                        <span
                                            class="h-2 w-2 rounded-full bg-secondary"
                                            aria-hidden="true">
                                        </span>

                                    @endif

                                </div>


                                <p class="mt-0.5 text-xs text-slate-500">

                                    @if ($unreadNotificationCount > 0)

                                        {{ number_format($unreadNotificationCount) }}
                                        unread

                                    @else

                                        You're all caught up

                                    @endif

                                </p>

                            </div>


                            {{-- Notification items --}}
                            <div class="max-h-96 overflow-y-auto">

                                @forelse ($headerNotifications as $notification)

                                    @php
                                        $severity = strtolower($notification->severity ?? 'info');

                                        $iconClass = match ($severity) {
                                            'success' => 'bg-success/10 text-success',
                                            'warning' => 'bg-warning/10 text-amber-600',
                                            'error', 'danger' => 'bg-error/10 text-error',
                                            default => 'bg-accent/10 text-accent',
                                        };
                                    @endphp
                                    <a
                                        href="{{ route('notifications.open', $notification) }}"
                                        aria-label="Open notification: {{ $notification->title }}"
                                        class="group block border-b border-border px-5 py-4 transition hover:bg-background focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-accent {{ !$notification->is_read ? 'bg-accent/5' : '' }}">

                                        <div class="flex gap-3">

                                            {{-- Severity icon --}}
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $iconClass }}">

                                                @if ($severity === 'success')

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

                                                @elseif ($severity === 'warning')

                                                    <svg
                                                        class="h-4 w-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24">

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />

                                                    </svg>

                                                @elseif ($severity === 'error' || $severity === 'danger')

                                                    <svg
                                                        class="h-4 w-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24">

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12" />

                                                    </svg>

                                                @else

                                                    <svg
                                                        class="h-4 w-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24">

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                                                    </svg>

                                                @endif

                                            </div>


                                            <div class="min-w-0 flex-1">

                                                <div class="flex items-start justify-between gap-3">

                                                    <div class="min-w-0">

                                                        <div class="flex items-center gap-2">

                                                            <p class="truncate font-button text-sm font-semibold text-primary">
                                                                {{ $notification->title }}
                                                            </p>

                                                            @if (!$notification->is_read)

                                                                <span
                                                                    class="h-2 w-2 shrink-0 rounded-full bg-secondary"
                                                                    title="Unread">
                                                                </span>

                                                            @endif

                                                        </div>


                                                        <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-slate-500">
                                                            {{ \Illuminate\Support\Str::limit($notification->body, 100) }}
                                                        </p>


                                                        <div class="mt-2 flex items-center gap-2 text-[10px] text-slate-400">

                                                            <span>
                                                                {{ $notification->created_at->diffForHumans() }}
                                                            </span>

                                                            @if ($notification->module)

                                                                <span>
                                                                    •
                                                                </span>

                                                                <span class="capitalize">
                                                                    {{ str_replace('_', ' ', $notification->module) }}
                                                                </span>

                                                            @endif

                                                        </div>

                                                    </div>


                                                    <svg
                                                        class="mt-1 h-4 w-4 shrink-0 text-slate-300 transition duration-200 group-hover:translate-x-0.5 group-hover:text-accent"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24">

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 5l7 7-7 7" />

                                                    </svg>

                                                </div>

                                            </div>

                                        </div>

                                    </a>


                                @empty

                                    <div class="px-6 py-10 text-center">

                                        <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-accent/10 text-accent">

                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 00-4-5.66V5a2 2 0 10-4 0v.34A6 6 0 006 11v3.2c0 .53-.21 1.04-.59 1.41L4 17h5m6 0a3 3 0 01-6 0" />
                                            </svg>

                                        </div>

                                        <p class="font-button text-sm font-medium text-primary">
                                            No notifications
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            You're all caught up.
                                        </p>

                                    </div>

                                @endforelse

                            </div>


                            {{-- Footer --}}
                            <div class="bg-background px-4 py-3">

                                <a
                                    href="{{ route('notifications.index') }}"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 font-button text-xs font-semibold text-primary transition hover:bg-accent/10">

                                    View all notifications

                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 5l7 7-7 7" />
                                    </svg>

                                </a>

                            </div>

                        </div>

                    </div>

                @endif



                {{-- Profile --}}
                <div class="relative">

                    <button
                        type="button"
                        data-profile-button
                        class="flex items-center gap-2 rounded-lg px-2 py-1.5 transition hover:bg-primary/5"
                        aria-controls="profile-menu"
                        aria-expanded="false"
                        aria-haspopup="menu">

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
                        id="profile-menu"
                        data-profile-menu
                        role="menu"
                        aria-label="Account menu"
                        class="absolute right-0 mt-2 hidden w-56 overflow-hidden rounded-2xl border border-border bg-card shadow-soft">

                        <div class="border-b border-slate-100 px-4 py-3">

                            <p class="text-sm font-medium text-slate-900">
                                {{ auth()->user()->full_name }}
                            </p>

                            <p class="truncate text-xs text-accent">
                                {{ auth()->user()->email }}
                            </p>

                        </div>

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
            aria-hidden="true"
            class="fixed inset-0 z-40 hidden bg-slate-950/55 backdrop-blur-[1px] md:hidden">
        </div>


        {{-- Page content --}}
        <main
            id="main-content"
            tabindex="-1"
            class="flex-1 bg-background p-4 outline-none md:p-8 lg:p-10">

                        {{-- Success --}}
            @if (session('status'))

                <div
                    data-toast
                    data-toast-type="success"
                    data-toast-floating
                    role="status"
                    aria-live="polite"
                    class="fams-toast flex items-start gap-3 rounded-2xl border border-success/20 bg-card px-4 py-4 shadow-xl">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-success/10 text-success">

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7" />

                        </svg>

                    </div>


                    <div class="min-w-0 flex-1">

                        <p class="font-button text-sm font-semibold text-primary">
                            Success
                        </p>

                        <p class="mt-0.5 text-sm leading-relaxed text-slate-500">
                            {{ session('status') }}
                        </p>

                    </div>


                    <button
                        type="button"
                        data-dismiss
                        aria-label="Dismiss notification"
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary">

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />

                        </svg>

                    </button>

                </div>

            @endif


            {{-- Validation errors --}}
            @if ($errors->any())

                <div
                    data-toast
                    data-toast-type="error"
                    data-toast-persistent
                    role="alert"
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







