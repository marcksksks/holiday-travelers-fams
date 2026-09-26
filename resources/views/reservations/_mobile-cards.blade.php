{{-- =====================================================
     MOBILE RESERVATION CARDS
====================================================== --}}

<div class="grid gap-2.5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">

    @forelse ($reservations as $reservation)

        @php
            $isReservationOwner =
                $reservation->requester_email ===
                auth()->user()->email;

            $canReviewReservation =
                auth()->user()->can(
                    'decideReservations'
                );

            $canEditReservation =
                $isReservationOwner
                &&
                in_array(
                    $reservation->status,
                    ['pending', 'rejected'],
                    true
                );

            $canCancelReservation =
                $isReservationOwner
                &&
                $reservation->status === 'pending';

            $canDecideReservation =
                $canReviewReservation
                &&
                ! $isReservationOwner
                &&
                $reservation->status === 'pending';

            $isHistorical =
                in_array(
                    $reservation->status,
                    [
                        'rejected',
                        'cancelled',
                        'completed',
                    ],
                    true
                );

            $startTime =
                \Illuminate\Support\Carbon::createFromFormat(
                    'H:i',
                    substr(
                        $reservation->start_time,
                        0,
                        5
                    )
                )->format('g:i A');

            $endTime =
                \Illuminate\Support\Carbon::createFromFormat(
                    'H:i',
                    substr(
                        $reservation->end_time,
                        0,
                        5
                    )
                )->format('g:i A');
        @endphp


        <article
            @class([
                'card h-full min-h-[170px] overflow-hidden transition hover:-translate-y-0.5 hover:shadow-soft',
                'opacity-70' => $isHistorical,
            ])>

            <div class="p-3">

                {{-- Top row --}}
                <div class="flex items-start gap-3">

                    <div
                        @class([
                            'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg',
                            'bg-warning/10 text-amber-600' => $reservation->status === 'pending',
                            'bg-success/10 text-success' => $reservation->status === 'approved',
                            'bg-error/10 text-error' => $reservation->status === 'rejected',
                            'bg-slate-100 text-slate-400' => in_array(
                                $reservation->status,
                                ['cancelled', 'completed'],
                                true
                            ),
                        ])>

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5" />

                        </svg>

                    </div>


                    <div class="min-w-0 flex-1">

                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">

                                <h3 class="truncate font-button text-sm font-semibold text-primary">
                                    {{ $reservation->facility_name }}
                                </h3>

                                <p class="mt-0.5 text-[11px] text-slate-400">
                                    Reservation #{{ $reservation->id }}
                                </p>

                            </div>


                            {{-- Actions --}}
                            <button
                                type="button"
                                data-reservation-actions-open
                                data-reservation-id="{{ $reservation->id }}"
                                data-can-decide="{{ $canDecideReservation ? '1' : '0' }}"
                                data-can-edit="{{ $canEditReservation ? '1' : '0' }}"
                                data-can-cancel="{{ $canCancelReservation ? '1' : '0' }}"
                                data-decide-url="{{ route('reservations.decide', $reservation) }}"
                                data-cancel-url="{{ route('reservations.cancel', $reservation) }}"
                                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-border bg-card text-slate-500 transition hover:bg-background hover:text-primary"
                                aria-label="Reservation actions">

                                <svg
                                    class="h-5 w-5"
                                    fill="currentColor"
                                    viewBox="0 0 24 24">

                                    <circle cx="5" cy="12" r="1.7" />
                                    <circle cx="12" cy="12" r="1.7" />
                                    <circle cx="19" cy="12" r="1.7" />

                                </svg>

                            </button>

                        </div>

                    </div>

                </div>


                {{-- Status + schedule --}}
                <div class="mt-3 flex flex-wrap items-center gap-2">

                    @switch($reservation->status)

                        @case('pending')

                            <span class="badge badge-warning">
                                Pending
                            </span>

                            @break


                        @case('approved')

                            <span class="badge badge-success">
                                Approved
                            </span>

                            @break


                        @case('rejected')

                            <span class="badge badge-error">
                                Rejected
                            </span>

                            @break


                        @case('completed')

                            <span class="badge badge-info">
                                Completed
                            </span>

                            @break


                        @default

                            <span class="badge bg-slate-100 text-slate-600">
                                {{ str($reservation->status)->headline() }}
                            </span>

                    @endswitch


                    <span class="text-xs text-slate-400">
                        {{ $reservation->date->format('M d, Y') }}
                    </span>

                </div>


                @if ($reservation->status === 'approved')

                    @php
                        $mobileReservationDate =
                            $reservation->date->format('Y-m-d');

                        $mobileReservationStartsAt =
                            \Illuminate\Support\Carbon::parse(
                                $mobileReservationDate
                                . ' '
                                . substr(
                                    (string) $reservation->start_time,
                                    0,
                                    5
                                ),
                                config(
                                    'app.timezone',
                                    'Asia/Manila'
                                )
                            );

                        $mobileReservationEndsAt =
                            \Illuminate\Support\Carbon::parse(
                                $mobileReservationDate
                                . ' '
                                . substr(
                                    (string) $reservation->end_time,
                                    0,
                                    5
                                ),
                                config(
                                    'app.timezone',
                                    'Asia/Manila'
                                )
                            );
                    @endphp

                    <div
                        class="mt-2 flex items-center gap-1.5"
                        data-reservation-live-timing
                        data-reservation-mobile-live-timing
                        data-reservation-start-ms="{{ $mobileReservationStartsAt->timestamp * 1000 }}"
                        data-reservation-end-ms="{{ $mobileReservationEndsAt->timestamp * 1000 }}"
                        data-server-now-ms="{{ now()->timestamp * 1000 }}">

                        <span
                            class="h-1.5 w-1.5 shrink-0 rounded-full bg-success"
                            aria-hidden="true">
                        </span>

                        <span
                            class="text-[10px] font-semibold text-success"
                            data-reservation-live-timing-value>
                            Calculating...
                        </span>

                    </div>

                @endif


                {{-- Main information --}}
                <div class="mt-2.5 grid grid-cols-2 gap-2">

                    <div class="rounded-lg bg-background/70 px-2.5 py-2">

                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                            Time
                        </p>

                        <p class="mt-1 text-xs font-semibold text-slate-700">
                            {{ $startTime }}
                        </p>

                        <p class="mt-0.5 text-[11px] text-slate-500">
                            to {{ $endTime }}
                        </p>

                    </div>


                    <div class="rounded-lg bg-background/70 px-2.5 py-2">

                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">

                            {{
                                $reservation->facility?->facility_type === 'vehicle'
                                    ? 'Passengers'
                                    : 'Attendees'
                            }}

                        </p>

                        <p class="mt-1 text-xs font-semibold text-slate-700">

                            @if ($reservation->attendees)

                                {{ number_format($reservation->attendees) }}

                            @else

                                —

                            @endif

                        </p>

                        @if ($reservation->facility?->capacity)

                            <p class="mt-0.5 text-[11px] text-slate-400">
                                Capacity {{ number_format($reservation->facility->capacity) }}
                            </p>

                        @endif

                    </div>

                </div>


                {{-- Requester --}}
                @if ($canDecide)

                    <div class="mt-3 flex items-center gap-2 border-t border-border pt-2.5">

                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-accent/10 text-[10px] font-semibold uppercase text-primary">

                            {{
                                \Illuminate\Support\Str::substr(
                                    $reservation->requester_name,
                                    0,
                                    1
                                )
                            }}

                        </div>

                        <div class="min-w-0">

                            <p class="truncate text-xs font-medium text-slate-600">
                                {{ $reservation->requester_name }}
                            </p>

                            @if ($isReservationOwner)

                                <p class="text-[9px] font-semibold uppercase tracking-wide text-accent">
                                    Your request
                                </p>

                            @endif

                        </div>

                    </div>

                @endif


                {{-- Purpose --}}
                @if ($reservation->purpose)

                    <p
                        class="mt-2 line-clamp-1 text-[11px] leading-4 text-slate-500"
                        title="{{ $reservation->purpose }}">

                        {{ $reservation->purpose }}

                    </p>

                @endif


                {{-- Rejection reason --}}
                @if (
                    $reservation->status === 'rejected'
                    &&
                    filled($reservation->decision_note)
                )

                    <div class="mt-3 rounded-lg bg-error/5 px-3 py-2">

                        <p class="text-[11px] leading-5 text-error">

                            <span class="font-semibold">
                                Reason:
                            </span>

                            {{ $reservation->decision_note }}

                        </p>

                    </div>

                @endif

            </div>


            {{-- Hidden details trigger --}}
            <button
                type="button"
                data-reservation-view-open
                data-reservation-id="{{ $reservation->id }}"
                data-reservation-facility="{{ $reservation->facility_name }}"
                data-reservation-facility-type="{{ $reservation->facility?->facility_type }}"
                data-reservation-location="{{ $reservation->facility?->location }}"
                data-reservation-capacity="{{ $reservation->facility?->capacity }}"
                data-reservation-date="{{ $reservation->date->format('M d, Y') }}"
                data-reservation-start="{{ substr($reservation->start_time, 0, 5) }}"
                data-reservation-end="{{ substr($reservation->end_time, 0, 5) }}"
                data-reservation-attendees="{{ $reservation->attendees }}"
                data-reservation-purpose="{{ $reservation->purpose }}"
                data-reservation-requester="{{ $reservation->requester_name }}"
                data-reservation-requester-email="{{ $reservation->requester_email }}"
                data-reservation-status="{{ $reservation->status }}"
                data-reservation-note="{{ $reservation->decision_note }}"
                class="hidden"
                tabindex="-1"
                aria-hidden="true">
            </button>


            @if ($canEditReservation)

                {{-- Hidden edit trigger --}}
                <button
                    type="button"
                    data-reservation-edit-open
                    data-reservation-id="{{ $reservation->id }}"
                    data-reservation-facility-id="{{ $reservation->facility_id }}"
                    data-reservation-facility="{{ $reservation->facility_name }}"
                    data-reservation-facility-type="{{ $reservation->facility?->facility_type }}"
                    data-reservation-facility-status="{{ $reservation->facility?->status }}"
                    data-reservation-capacity="{{ $reservation->facility?->capacity }}"
                    data-reservation-date="{{ $reservation->date->toDateString() }}"
                    data-reservation-start="{{ substr($reservation->start_time, 0, 5) }}"
                    data-reservation-end="{{ substr($reservation->end_time, 0, 5) }}"
                    data-reservation-attendees="{{ $reservation->attendees }}"
                    data-reservation-purpose="{{ $reservation->purpose }}"
                    data-reservation-note="{{ $reservation->decision_note }}"
                    data-reservation-update-url="{{ route('reservations.resubmit', $reservation) }}"
                    class="hidden"
                    tabindex="-1"
                    aria-hidden="true">
                </button>

            @endif

        </article>


    @empty

        <div class="card px-5 py-10 text-center">

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
                        d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

                </svg>

            </div>


            @if (
                request()->filled('search')
                ||
                request()->filled('status')
                ||
                request()->filled('facility')
                ||
                request()->filled('date')
            )

                <h3 class="mt-3 font-heading text-sm font-semibold text-primary">
                    No matching reservations
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Try changing or clearing the current filters.
                </p>

                <a
                    href="{{ route('reservations.index') }}"
                    class="btn-outline mt-4 inline-flex">

                    Clear filters

                </a>

            @else

                <h3 class="mt-3 font-heading text-sm font-semibold text-primary">
                    No reservation requests
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Facility reservation requests will appear here.
                </p>

            @endif

        </div>

    @endforelse

</div>