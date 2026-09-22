@include('visitors._mobile-cards')

<div class="table-shell hidden overflow-visible md:block">

    <div class="overflow-x-auto">

        <table class="min-w-full text-left text-sm">

            <thead class="table-header">

                <tr>

                    <th class="px-5 py-3.5 font-medium">
                        Visitor
                    </th>

                    <th class="px-5 py-3.5 font-medium">
                        Visit / Host
                    </th>

                    <th class="px-5 py-3.5 font-medium">
                        Arrival
                    </th>

                    <th class="px-5 py-3.5 font-medium">
                        Status
                    </th>

                    <th class="px-5 py-3.5 text-right font-medium">
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-border bg-card">

                @forelse ($visitors as $visitor)

                    <tr
                        @class([
                            'transition-colors',
                            'bg-success/[0.03] hover:bg-success/[0.06]' =>
                                $visitor->status === 'checked_in',
                            'hover:bg-sky-50/40' =>
                                $visitor->status !== 'checked_in',
                        ])>

                        {{-- Visitor --}}
                        <td class="px-5 py-4">

                            <div class="flex items-start gap-3">

                                <div
                                    @class([
                                        'flex h-10 w-10 shrink-0 items-center justify-center rounded-full font-heading text-sm font-bold uppercase',
                                        'bg-success/10 text-success' =>
                                            $visitor->status === 'checked_in',
                                        'bg-accent/10 text-primary' =>
                                            $visitor->status !== 'checked_in',
                                    ])>

                                    {{ \Illuminate\Support\Str::substr($visitor->full_name, 0, 1) }}

                                </div>


                                <div class="min-w-0">

                                    <button
                                        type="button"
                                        data-visitor-details-open="{{ $visitor->id }}"
                                        class="block max-w-[300px] truncate text-left font-button text-sm font-semibold text-primary transition hover:text-secondary">

                                        {{ $visitor->full_name }}

                                    </button>


                                    <div class="mt-1 flex flex-wrap items-center gap-1.5">

                                        @if ($visitor->organization)

                                            <span class="max-w-[220px] truncate text-[10px] text-slate-400">
                                                {{ $visitor->organization }}
                                            </span>

                                        @endif


                                        @if ($visitor->is_walk_in)

                                            <span class="rounded-full bg-secondary/10 px-1.5 py-0.5 text-[8px] font-semibold uppercase text-secondary">
                                                Walk-in
                                            </span>

                                        @elseif ($visitor->appointment_id)

                                            <span class="rounded-full bg-accent/10 px-1.5 py-0.5 text-[8px] font-semibold uppercase text-primary">
                                                Appointment
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </div>

                        </td>


                        {{-- Visit / Host --}}
                        <td class="px-5 py-4">

                            <div class="max-w-[280px]">

                                <p class="truncate text-xs font-medium text-slate-700">
                                    {{ str($visitor->visitor_type ?: 'guest')->headline() }}
                                </p>


                                <p class="mt-1 truncate text-[10px] text-slate-400">

                                    Host:
                                    {{ $visitor->host_name ?: ($visitor->host_email ?: 'Not assigned') }}

                                </p>


                                @if ($visitor->purpose)

                                    <p
                                        class="mt-1 max-w-[260px] truncate text-[10px] text-slate-400"
                                        title="{{ $visitor->purpose }}">

                                        {{ $visitor->purpose }}

                                    </p>

                                @endif

                            </div>

                        </td>


                        {{-- Arrival --}}
                        <td class="px-5 py-4">

                            @if ($visitor->check_in_at)

                                <div>

                                    <p
                                        @class([
                                            'text-xs font-semibold',
                                            'text-success' =>
                                                $visitor->status === 'checked_in',
                                            'text-primary' =>
                                                $visitor->status !== 'checked_in',
                                        ])>

                                        {{ $visitor->check_in_at->format('h:i A') }}

                                    </p>

                                    <p class="mt-0.5 text-[9px] text-slate-400">
                                        {{ $visitor->check_in_at->format('M d, Y') }}
                                    </p>


                                    @if ($visitor->status === 'checked_in')

                                        <div class="mt-1 flex items-center gap-1.5">

                                            <span
                                                class="h-1.5 w-1.5 shrink-0 rounded-full bg-success"
                                                aria-hidden="true">
                                            </span>

                                            <p
                                                class="text-[9px] font-semibold text-success"
                                                data-visitor-live-duration
                                                data-check-in-ms="{{ $visitor->check_in_at->timestamp * 1000 }}"
                                                data-server-now-ms="{{ now()->timestamp * 1000 }}">

                                                On site for
                                                <span data-visitor-live-duration-value>
                                                    calculating...
                                                </span>

                                            </p>

                                        </div>

                                    @endif

                                </div>

                            @else

                                <div>

                                    <p class="text-xs font-medium text-slate-500">
                                        Waiting
                                    </p>

                                    <p class="mt-0.5 text-[9px] text-slate-400">
                                        Not checked in
                                    </p>

                                </div>

                            @endif

                        </td>


                        {{-- Status --}}
                        <td class="px-5 py-4">

                            @switch($visitor->status)

                                @case('expected')

                                    <span class="badge badge-info">
                                        Expected
                                    </span>

                                    @break


                                @case('awaiting_host')

                                    <span class="badge badge-warning">
                                        Awaiting Host
                                    </span>

                                    @break


                                @case('checked_in')

                                    <span class="badge badge-success">
                                        On Site
                                    </span>

                                    @break


                                @case('completed')

                                    <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                        Completed
                                    </span>

                                    @break


                                @case('declined')

                                    <span class="badge badge-error">
                                        Declined
                                    </span>

                                    @break


                                @case('cancelled')

                                    <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                        Cancelled
                                    </span>

                                    @break


                                @case('no_show')

                                    <span class="badge badge-warning">
                                        No Show
                                    </span>

                                    @break

                                @default

                                    <span class="badge badge-info">
                                        {{ str($visitor->status)->headline() }}
                                    </span>

                            @endswitch

                        </td>


                        {{-- Actions --}}
                        <td class="px-5 py-4">

                            <div class="flex items-center justify-end gap-2">

                                @can('operateVisitorDesk')

                                    @if (in_array($visitor->status, ['expected', 'awaiting_host'], true))

                                        <button
                                            type="button"
                                            data-visitor-checkin-open
                                            data-visitor-checkin-action="{{ route('visitors.check-in', $visitor) }}"
                                            data-visitor-checkin-name="{{ $visitor->full_name }}"
                                            data-visitor-checkin-organization="{{ $visitor->organization }}"
                                            data-visitor-checkin-host="{{ $visitor->host_name ?: $visitor->host_email }}"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-success/10 px-3 py-2 font-button text-xs font-semibold text-success transition hover:bg-success hover:text-white">

                                            <svg
                                                class="h-3.5 w-3.5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24">

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M5 13l4 4L19 7" />

                                            </svg>

                                            Check In

                                        </button>


                                    @elseif ($visitor->status === 'checked_in')

                                        <button
                                            type="button"
                                            data-visitor-checkout-open
                                            data-visitor-checkout-action="{{ route('visitors.check-out', $visitor) }}"
                                            data-visitor-checkout-name="{{ $visitor->full_name }}"
                                            data-visitor-checkout-organization="{{ $visitor->organization }}"
                                            data-visitor-checkout-checkin="{{ $visitor->check_in_at?->toIso8601String() }}"
                                            data-visitor-checkout-checkin-label="{{ $visitor->check_in_at?->format('M d, Y • h:i A') }}"
                                            data-visitor-checkout-badge="{{ $visitor->badge_number }}"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-2 font-button text-xs font-semibold text-white transition hover:bg-secondary">

                                            <svg
                                                class="h-3.5 w-3.5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24">

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />

                                            </svg>

                                            Check Out

                                        </button>

                                    @endif

                                @endcan


                                @include(
                                    'visitors._row-actions',
                                    [
                                        'visitor' => $visitor,
                                    ]
                                )

                            </div>

                        </td>

                    </tr>


@empty

                    <tr>

                        <td
                            colspan="5"
                            class="px-6 py-14">

                            <div class="mx-auto max-w-sm text-center">

                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-accent/10 text-accent">

                                    <svg
                                        class="h-6 w-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8z" />

                                    </svg>

                                </div>


                                <h3 class="mt-3 font-heading text-sm font-semibold text-primary">
                                    No visitors found
                                </h3>


                                <p class="mt-1 text-xs leading-5 text-slate-500">

                                    @if (
                                        request()->filled('q')
                                        ||
                                        request()->filled('status')
                                        ||
                                        request()->filled('visitor_type')
                                    )

                                        No visitor records match the current filters.

                                    @else

                                        Visitor records will appear here after registration.

                                    @endif

                                </p>


                                @if (
                                    request()->filled('q')
                                    ||
                                    request()->filled('status')
                                    ||
                                    request()->filled('visitor_type')
                                )

                                    <a
                                        href="{{ route('visitors.index') }}"
                                        class="btn-outline mt-4">

                                        Clear Filters

                                    </a>

                                @endif

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

{{-- Visitor detail modals live outside the table for valid HTML structure. --}}
@foreach ($visitors as $visitor)

    @include(
        'visitors._details-modal',
        [
            'visitor' => $visitor,
        ]
    )

@endforeach



@once
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const durationElements =
                document.querySelectorAll(
                    '[data-visitor-live-duration]'
                );

            if (! durationElements.length) {
                return;
            }

            /*
             * performance.now() is used so the timer continues from
             * the Laravel server timestamp instead of depending on
             * whether the user's computer clock is correct.
             */
            const pageStartedAt =
                performance.now();


            const formatDuration =
                (totalMilliseconds) => {

                    const totalMinutes =
                        Math.max(
                            0,
                            Math.floor(
                                totalMilliseconds / 60000
                            )
                        );

                    const days =
                        Math.floor(
                            totalMinutes / 1440
                        );

                    const hours =
                        Math.floor(
                            (totalMinutes % 1440) / 60
                        );

                    const minutes =
                        totalMinutes % 60;


                    if (days > 0) {

                        return hours > 0
                            ? `${days}d ${hours}h ${minutes}m`
                            : `${days}d ${minutes}m`;

                    }


                    if (hours > 0) {

                        return `${hours}h ${minutes}m`;

                    }


                    if (totalMinutes > 0) {

                        return `${totalMinutes}m`;

                    }


                    return '< 1m';
                };


            const famsVisitorLiveDuration =
                () => {

                    const elapsedSinceLoad =
                        performance.now() -
                        pageStartedAt;


                    durationElements.forEach(
                        (element) => {

                            const checkInMs =
                                Number(
                                    element.dataset.checkInMs
                                );

                            const serverNowMs =
                                Number(
                                    element.dataset.serverNowMs
                                );

                            const value =
                                element.querySelector(
                                    '[data-visitor-live-duration-value]'
                                );


                            if (
                                ! value ||
                                ! Number.isFinite(checkInMs) ||
                                ! Number.isFinite(serverNowMs)
                            ) {
                                return;
                            }


                            const currentServerTime =
                                serverNowMs +
                                elapsedSinceLoad;


                            const duration =
                                currentServerTime -
                                checkInMs;


                            value.textContent =
                                formatDuration(
                                    duration
                                );

                        }
                    );

                };


            famsVisitorLiveDuration();


            window.setInterval(
                famsVisitorLiveDuration,
                30000
            );

        });
    </script>
@endonce