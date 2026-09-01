@extends('layouts.app')

@section('title', 'Notifications')

@section('content')

<div class="mx-auto max-w-5xl space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Notifications
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Stay updated with recent activity across the system.
            </p>
        </div>


        @if ($notifications->where('is_read', false)->count() > 0)

            <form
                method="POST"
                action="{{ route('notifications.read-all') }}">

                @csrf

                <button
                    type="submit"
                    class="btn-outline">

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

                    Mark all as read

                </button>

            </form>

        @endif

    </div>


    {{-- Summary --}}
    <div class="grid gap-4 sm:grid-cols-3">

        <div class="card p-5">

            <p class="text-xs font-medium text-slate-500">
                Total Notifications
            </p>

            <p class="mt-2 font-heading text-2xl font-bold text-primary">
                {{ $notifications->count() }}
            </p>

        </div>


        <div class="card border-b-4 border-b-secondary p-5">

            <p class="text-xs font-medium text-slate-500">
                Unread
            </p>

            <p class="mt-2 font-heading text-2xl font-bold text-secondary">
                {{ $notifications->where('is_read', false)->count() }}
            </p>

        </div>


        <div class="card border-b-4 border-b-success p-5">

            <p class="text-xs font-medium text-slate-500">
                Read
            </p>

            <p class="mt-2 font-heading text-2xl font-bold text-success">
                {{ $notifications->where('is_read', true)->count() }}
            </p>

        </div>

    </div>


    {{-- Notifications List --}}
    <div class="card overflow-hidden">

        <div class="border-b border-border px-6 py-5">

            <h3 class="font-heading text-base font-semibold text-primary">
                Recent Notifications
            </h3>

            <p class="mt-1 text-xs text-slate-500">
                Latest system alerts and activity
            </p>

        </div>


        <div class="divide-y divide-border">

            @forelse ($notifications as $notification)

                @php

                    $severity = strtolower($notification->severity ?? 'info');

                    $styles = match ($severity) {
                        'success' => [
                            'wrap' => 'bg-success/5',
                            'icon' => 'bg-success/10 text-success',
                        ],

                        'warning' => [
                            'wrap' => 'bg-warning/5',
                            'icon' => 'bg-warning/10 text-amber-600',
                        ],

                        'error', 'danger' => [
                            'wrap' => 'bg-error/5',
                            'icon' => 'bg-error/10 text-error',
                        ],

                        default => [
                            'wrap' => 'bg-accent/5',
                            'icon' => 'bg-accent/10 text-accent',
                        ],
                    };

                @endphp


                <div class="relative px-6 py-5 transition hover:bg-background {{ !$notification->is_read ? $styles['wrap'] : '' }}">

                    @if (!$notification->is_read)

                        <span class="absolute left-0 top-0 h-full w-1 bg-secondary"></span>

                    @endif


                    <div class="flex gap-4">

                        {{-- Icon --}}
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $styles['icon'] }}">

                            @if ($severity === 'success')

                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>

                            @elseif ($severity === 'warning')

                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
                                </svg>

                            @elseif ($severity === 'error' || $severity === 'danger')

                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>

                            @else

                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>

                            @endif

                        </div>


                        {{-- Content --}}
                        <div class="min-w-0 flex-1">

                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">

                                <div class="min-w-0">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <h4 class="font-button text-sm font-semibold text-primary">
                                            {{ $notification->title }}
                                        </h4>


                                        @if (!$notification->is_read)

                                            <span class="rounded-full bg-secondary/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-secondary">
                                                New
                                            </span>

                                        @endif

                                    </div>


                                    <p class="mt-1 text-sm leading-relaxed text-slate-600">
                                        {{ $notification->body }}
                                    </p>


                                    <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-slate-400">

                                        <span>
                                            {{ $notification->created_at->format('M d, Y · h:i A') }}
                                        </span>


                                        @if ($notification->module)

                                            <span class="rounded-full bg-primary/5 px-2 py-1 font-medium text-primary">
                                                {{ ucfirst($notification->module) }}
                                            </span>

                                        @endif

                                    </div>

                                </div>


                                {{-- Actions --}}
                                <div class="flex shrink-0 items-center gap-2">

                                    @if ($notification->link)

                                        <a
                                            href="{{ $notification->link }}"
                                            class="btn-outline px-3 py-2 text-xs">

                                            View

                                        </a>

                                    @endif


                                    @if (!$notification->is_read)

                                        <form
                                            method="POST"
                                            action="{{ route('notifications.read', $notification) }}">

                                            @csrf

                                            <button
                                                type="submit"
                                                class="rounded-lg px-3 py-2 text-xs font-medium text-accent transition hover:bg-accent/10 hover:text-primary">

                                                Mark as read

                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            @empty

                <div class="px-6 py-16 text-center">

                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-accent/10 text-accent">

                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 00-4-5.66V5a2 2 0 10-4 0v.34A6 6 0 006 11v3.2c0 .53-.21 1.04-.59 1.41L4 17h5m6 0a3 3 0 01-6 0" />

                        </svg>

                    </div>

                    <h3 class="font-heading text-base font-semibold text-primary">
                        You're all caught up
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        No notifications are available right now.
                    </p>

                </div>

            @endforelse

        </div>

    </div>

</div>

@endsection