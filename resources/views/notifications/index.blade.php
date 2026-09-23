@extends('layouts.app')

@section('title', 'Notifications')

@section('content')

<div class="space-y-6">

    {{-- =====================================================
         NOTIFICATIONS WORKSPACE HEADER
    ====================================================== --}}
    <x-page-header
        eyebrow="Activity Center"
        title="Notifications"
        badge="Notification Center"
        description="Review alerts, approvals, requests, and recent system activity from one centralized feed.">

        <x-slot:actions>

            @if ($unreadCount > 0)

                <form
                    method="POST"
                    action="{{ route('notifications.read-all') }}">

                    @csrf

                    <button
                        type="submit"
                        class="btn-outline whitespace-nowrap">

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
                                d="M5 13l4 4L19 7" />

                        </svg>

                        Mark all as read

                    </button>

                </form>

            @endif

        </x-slot:actions>

    </x-page-header>


    {{-- =====================================================
         ACTIVITY SUMMARY
    ====================================================== --}}
    <section class="space-y-4">

        <x-section-header
            eyebrow="Overview"
            title="Activity Summary"
            description="A quick view of notification volume and unread activity for your account." />


        <div class="grid gap-3 sm:grid-cols-3">

            <x-metric-card
                label="Total Notifications"
                :value="number_format($totalCount)"
                :href="route('notifications.index', ['status' => 'all'])"
                helper="All notifications currently available in your personal activity feed."
                tone="primary">

                <x-slot:icon>

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
                            d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 00-4-5.66V5a2 2 0 10-4 0v.34A6 6 0 006 11v3.2c0 .53-.21 1.04-.59 1.41L4 17h5m6 0a3 3 0 01-6 0" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Unread"
                :value="number_format($unreadCount)"
                :href="route('notifications.index', ['status' => 'unread'])"
                helper="Notifications that have not yet been opened."
                :tone="$unreadCount > 0 ? 'warning' : 'success'">

                <x-slot:icon>

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
                            d="M12 9v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Read"
                :value="number_format($readCount)"
                :href="route('notifications.index', ['status' => 'read'])"
                helper="Notifications you have already reviewed."
                tone="success">

                <x-slot:icon>

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
                            d="M5 13l4 4L19 7" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>

        </div>

    </section>


    <x-section-header
        eyebrow="Inbox"
        title="Notification Feed"
        description="Search and filter your personal notification history." />

    {{-- =====================================================
         NOTIFICATION CENTER
    ====================================================== --}}
    <div class="card overflow-hidden">

        {{-- Search --}}
        <div class="border-b border-border px-4 py-4 sm:px-6">

            <form
                method="GET"
                action="{{ route('notifications.index') }}"
                class="flex flex-col gap-2 sm:flex-row sm:items-center">

                <input
                    type="hidden"
                    name="status"
                    value="{{ $status }}">


                <div class="relative min-w-0 flex-1">

                    <div
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />

                        </svg>

                    </div>


                    <input
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        maxlength="120"
                        autocomplete="off"
                        placeholder="Search notifications..."
                        aria-label="Search notifications"
                        class="w-full rounded-xl border border-border bg-background py-2.5 pl-10 pr-10 text-sm text-primary outline-none transition placeholder:text-slate-400 focus:border-accent focus:bg-card focus:ring-2 focus:ring-accent/10">


                    @if ($search !== '')

                        <a
                            href="{{ route('notifications.index', ['status' => $status]) }}"
                            aria-label="Clear notification search"
                            title="Clear search"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 transition hover:text-primary">

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

                        </a>

                    @endif

                </div>


                <button
                    type="submit"
                    class="btn-primary justify-center whitespace-nowrap px-4">

                    Search

                </button>

            </form>


            @if ($search !== '')

                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500">

                    <span>
                        Search results for
                    </span>

                    <span class="rounded-lg bg-accent/10 px-2 py-1 font-medium text-primary">
                        “{{ $search }}”
                    </span>

                    <span>
                        · {{ number_format($notifications->total()) }}
                        {{ $notifications->total() === 1 ? 'result' : 'results' }}
                    </span>

                </div>

            @endif

        </div>

        {{-- Toolbar --}}
        <div class="flex flex-col gap-3 border-b border-border px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">

            @php

                $filters = [
                    'all' => [
                        'label' => 'All',
                        'count' => $totalCount,
                    ],

                    'unread' => [
                        'label' => 'Unread',
                        'count' => $unreadCount,
                    ],

                    'read' => [
                        'label' => 'Read',
                        'count' => $readCount,
                    ],
                ];

            @endphp


            {{-- Filter tabs --}}
            <div
                class="inline-flex w-fit rounded-xl bg-background p-1"
                role="navigation"
                aria-label="Notification filters">

                @foreach ($filters as $key => $filter)

                    <a
                        href="{{ route('notifications.index', array_filter([
                            'status' => $key,
                            'q' => $search,
                        ])) }}"
                        @if ($status === $key)
                            aria-current="page"
                        @endif
                        class="flex items-center gap-2 rounded-lg px-3.5 py-2 font-button text-xs font-semibold transition
                        {{ $status === $key
                            ? 'bg-card text-primary shadow-sm ring-1 ring-border'
                            : 'text-slate-500 hover:text-primary'
                        }}">

                        <span>
                            {{ $filter['label'] }}
                        </span>


                        <span
                            class="rounded-full px-1.5 py-0.5 text-[10px]
                            {{ $status === $key
                                ? 'bg-primary/10 text-primary'
                                : 'bg-slate-200/70 text-slate-500'
                            }}">

                            {{ number_format($filter['count']) }}

                        </span>

                    </a>

                @endforeach

            </div>


            {{-- Results information --}}
            @if ($notifications->total() > 0)

                <p class="text-xs text-slate-400">

                    Showing

                    <span class="font-medium text-slate-600">
                        {{ number_format($notifications->firstItem()) }}
                    </span>

                    –

                    <span class="font-medium text-slate-600">
                        {{ number_format($notifications->lastItem()) }}
                    </span>

                    of

                    <span class="font-medium text-slate-600">
                        {{ number_format($notifications->total()) }}
                    </span>

                </p>

            @endif

        </div>



        {{-- =================================================
             LIST
        ================================================== --}}
        <div class="divide-y divide-border">

            @forelse ($notifications as $notification)

                @php

                    $severity =
                        strtolower(
                            $notification->severity
                            ?? 'info'
                        );


                    $styles = match ($severity) {

                        'success' => [
                            'icon' =>
                                'bg-success/10 text-success',
                        ],

                        'warning' => [
                            'icon' =>
                                'bg-warning/10 text-amber-600',
                        ],

                        'error', 'danger' => [
                            'icon' =>
                                'bg-error/10 text-error',
                        ],

                        default => [
                            'icon' =>
                                'bg-accent/10 text-accent',
                        ],
                    };


                    $moduleLabel =
                        $notification->module
                            ? ucwords(
                                str_replace(
                                    ['_', '-'],
                                    ' ',
                                    $notification->module
                                )
                            )
                            : null;

                @endphp


                <a
                    href="{{ route('notifications.open', $notification) }}"
                    aria-label="Open notification: {{ $notification->title }}"
                    class="group relative block px-4 py-4 transition duration-200 hover:bg-background focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-accent sm:px-6 sm:py-5
                    {{ !$notification->is_read
                        ? 'bg-accent/[0.04]'
                        : 'bg-card'
                    }}">

                    {{-- Unread indicator --}}
                    @if (!$notification->is_read)

                        <span
                            class="absolute inset-y-0 left-0 w-1 bg-secondary">
                        </span>

                    @endif


                    <div class="flex gap-3 sm:gap-4">

                        {{-- Severity icon --}}
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $styles['icon'] }} sm:h-11 sm:w-11">

                            @if ($severity === 'success')

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

                            @elseif ($severity === 'warning')

                                <svg
                                    class="h-5 w-5"
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

                            @else

                                <svg
                                    class="h-5 w-5"
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



                        {{-- Notification content --}}
                        <div class="min-w-0 flex-1">

                            <div class="flex items-start justify-between gap-4">

                                <div class="min-w-0 flex-1">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <h3
                                            class="font-button text-sm font-semibold
                                            {{ !$notification->is_read
                                                ? 'text-primary'
                                                : 'text-slate-700'
                                            }}">

                                            {{ $notification->title }}

                                        </h3>


                                        @if (!$notification->is_read)

                                            <span
                                                class="h-2 w-2 rounded-full bg-secondary"
                                                aria-label="Unread">
                                            </span>

                                        @endif

                                    </div>


                                    <p class="mt-1 max-w-3xl text-sm leading-relaxed text-slate-500">

                                        {{ $notification->body }}

                                    </p>


                                    <div class="mt-2.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-400">

                                        <span>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>


                                        <span class="hidden sm:inline">
                                            •
                                        </span>


                                        <span class="hidden sm:inline">
                                            {{ $notification->created_at->format('M d, Y · h:i A') }}
                                        </span>


                                        @if ($moduleLabel)

                                            <span>
                                                •
                                            </span>


                                            <span class="font-medium text-slate-500">
                                                {{ $moduleLabel }}
                                            </span>

                                        @endif

                                    </div>

                                </div>



                                {{-- Chevron --}}
                                <div
                                    class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-300 transition duration-200 group-hover:bg-accent/10 group-hover:text-accent">

                                    <svg
                                        class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-0.5"
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

                    </div>

                </a>


            @empty

                {{-- Empty state --}}
                <div class="px-6 py-16 text-center">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-accent/10 text-accent">

                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 00-4-5.66V5a2 2 0 10-4 0v.34A6 6 0 006 11v3.2c0 .53-.21 1.04-.59 1.41L4 17h5m6 0a3 3 0 01-6 0" />

                        </svg>

                    </div>


                    <h3 class="mt-4 font-heading text-base font-semibold text-primary">

                        @if ($search !== '')

                            No matching notifications

                        @elseif ($status === 'unread')

                            You're all caught up

                        @elseif ($status === 'read')

                            No read notifications

                        @else

                            No notifications yet

                        @endif

                    </h3>


                    <p class="mx-auto mt-1 max-w-sm text-sm text-slate-500">

                        @if ($search !== '')

                            Try another keyword or clear the current search to view all notifications.

                        @elseif ($status === 'unread')

                            There are no unread notifications requiring your attention.

                        @elseif ($status === 'read')

                            Notifications you open will appear here.

                        @else

                            New system activity and alerts will appear here when available.

                        @endif

                    </p>

                </div>

            @endforelse

        </div>



        {{-- Pagination --}}
        @if ($notifications->hasPages())

            <div class="border-t border-border px-4 py-4 sm:px-6">

                {{ $notifications->links() }}

            </div>

        @endif

    </div>

</div>

@endsection