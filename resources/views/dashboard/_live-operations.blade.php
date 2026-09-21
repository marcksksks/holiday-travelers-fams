@php
    $liveOperationsCardCount = 1;

    if ($canViewVisitors) {
        $liveOperationsCardCount++;
    }

    if ($canViewAppointments) {
        $liveOperationsCardCount++;
    }

    $nextFacilityRelease =
        $liveReservationsInUse
            ->sortBy('end_time')
            ->first();
@endphp


<section class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">

        <div>

            <div class="mb-2 flex items-center gap-2">

                <span class="relative flex h-2.5 w-2.5">

                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-success opacity-40"></span>

                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-success"></span>

                </span>

                <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-success">
                    Live Operations
                </p>

            </div>


            <h2 class="font-heading text-lg font-semibold text-primary">
                Current Operations
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Current visitor, appointment, and facility activity using Asia/Manila system time.
            </p>

        </div>


        <div class="flex items-center gap-2 text-[10px] font-medium text-slate-400">

            <svg
                class="h-3.5 w-3.5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true">

                <circle cx="12" cy="12" r="9" stroke-width="2"></circle>

                <path
                    stroke-linecap="round"
                    stroke-width="2"
                    d="M12 7v5l3 2">
                </path>

            </svg>

            <span>
                Live from server time
            </span>

        </div>

    </div>


    {{-- Operational cards --}}
    <div
        @class([
            'grid gap-5',
            'lg:grid-cols-3' => $liveOperationsCardCount >= 3,
            'sm:grid-cols-2' => $liveOperationsCardCount === 2,
            'grid-cols-1' => $liveOperationsCardCount === 1,
        ])>


        {{-- Visitors currently on site --}}
        @if ($canViewVisitors)

            <a
                href="{{ route('visitors.index', ['status' => 'checked_in']) }}"
                class="group card relative overflow-hidden p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

                <div class="absolute inset-x-0 top-0 h-1 bg-success"></div>


                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                            Visitors On Site
                        </p>

                        <div class="mt-3 flex items-baseline gap-2">

                            <p class="font-heading text-3xl font-bold text-primary">
                                {{ number_format($checkedInVisitors) }}
                            </p>

                            @if ($checkedInVisitors > 0)

                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-success">

                                    <span class="h-1.5 w-1.5 rounded-full bg-success"></span>

                                    Active

                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-success/10 text-success">

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8zM17 11h4M19 9v4" />

                        </svg>

                    </div>

                </div>


                <p class="mt-3 text-xs text-slate-500">

                    @if ($checkedInVisitors === 0)

                        No visitors are currently checked in.

                    @elseif ($checkedInVisitors === 1)

                        1 visitor is currently inside the premises.

                    @else

                        {{ number_format($checkedInVisitors) }} visitors are currently inside the premises.

                    @endif

                </p>


                <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-success">

                    <span>
                        Open Visitor Desk
                    </span>

                    <span class="transition-transform group-hover:translate-x-1">
                        &rarr;
                    </span>

                </div>

            </a>

        @endif



        {{-- Next appointment --}}
        @if ($canViewAppointments)

            <a
                href="{{ route('appointments.index') }}"
                class="group card relative overflow-hidden p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

                <div class="absolute inset-x-0 top-0 h-1 bg-accent"></div>


                <div class="flex items-start justify-between gap-4">

                    <div class="min-w-0">

                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                            Next Appointment
                        </p>


                        @if ($liveNextAppointment)

                            @php
                                $dashboardAppointmentStartsAt =
                                    \Illuminate\Support\Carbon::parse(
                                        $liveNextAppointment->date->format('Y-m-d')
                                        . ' '
                                        . substr(
                                            (string) $liveNextAppointment->start_time,
                                            0,
                                            5
                                        ),
                                        config(
                                            'app.timezone',
                                            'Asia/Manila'
                                        )
                                    );
                            @endphp


                            <p class="mt-3 font-heading text-2xl font-bold text-primary">
                                {{ $dashboardAppointmentStartsAt->format('h:i A') }}
                            </p>

                            <p class="mt-1 truncate text-xs font-medium text-slate-600">
                                {{ $liveNextAppointment->visitor_name }}
                            </p>

                            <p class="mt-0.5 text-[10px] text-slate-400">
                                {{ $dashboardAppointmentStartsAt->format('M d, Y') }}
                            </p>


                            <div
                                class="mt-3 flex items-center gap-1.5"
                                data-dashboard-appointment-timing
                                data-start-ms="{{ $dashboardAppointmentStartsAt->timestamp * 1000 }}"
                                data-server-now-ms="{{ now()->timestamp * 1000 }}">

                                <span class="h-1.5 w-1.5 rounded-full bg-accent"></span>

                                <span
                                    class="text-[10px] font-semibold text-accent"
                                    data-dashboard-appointment-timing-value>
                                    Calculating...
                                </span>

                            </div>

                        @else

                            <p class="mt-3 font-heading text-xl font-bold text-primary">
                                No upcoming appointment
                            </p>

                            <p class="mt-2 text-xs text-slate-500">
                                There are no scheduled or confirmed appointments ahead.
                            </p>

                        @endif

                    </div>


                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-accent/10 text-accent">

                        <svg
                            class="h-5 w-5"
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

                </div>

            </a>

        @endif



        {{-- Facilities currently occupied --}}
        <a
            href="{{ route('reservations.index', ['status' => 'approved']) }}"
            class="group card relative overflow-hidden p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

            <div class="absolute inset-x-0 top-0 h-1 bg-secondary"></div>


            <div class="flex items-start justify-between gap-4">

                <div class="min-w-0">

                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                        Facilities In Use
                    </p>

                    <p
                        class="mt-3 font-heading text-3xl font-bold text-primary"
                        data-dashboard-facility-count>
                        {{ number_format($liveFacilitiesInUseCount) }}
                    </p>

                </div>


                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 21h16M6 21V6l6-3 6 3v15M9 9h1M14 9h1M9 13h1M14 13h1" />

                    </svg>

                </div>

            </div>


            @if ($nextFacilityRelease)

                @php
                    $dashboardFacilityEndsAt =
                        \Illuminate\Support\Carbon::parse(
                            $nextFacilityRelease->date->format('Y-m-d')
                            . ' '
                            . substr(
                                (string) $nextFacilityRelease->end_time,
                                0,
                                5
                            ),
                            config(
                                'app.timezone',
                                'Asia/Manila'
                            )
                        );
                @endphp


                <div class="mt-3">

                    <p class="truncate text-xs font-medium text-slate-600">
                        {{ $nextFacilityRelease->facility_name }}
                    </p>

                    <div
                        class="mt-1 flex items-center gap-1.5"
                        data-dashboard-facility-timing
                        data-end-ms="{{ $dashboardFacilityEndsAt->timestamp * 1000 }}"
                        data-server-now-ms="{{ now()->timestamp * 1000 }}">

                        <span class="h-1.5 w-1.5 rounded-full bg-secondary"></span>

                        <span
                            class="text-[10px] font-semibold text-secondary"
                            data-dashboard-facility-timing-value>
                            Calculating...
                        </span>

                    </div>

                </div>

            @else

                <p class="mt-3 text-xs text-slate-500">
                    No approved facility reservations are currently in progress.
                </p>

            @endif


            <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-secondary">

                <span>
                    View reservations
                </span>

                <span class="transition-transform group-hover:translate-x-1">
                    &rarr;
                </span>

            </div>

        </a>

    </div>

</section>


@once
    <script>
        document.addEventListener(
            'DOMContentLoaded',
            () => {

                const appointmentElements =
                    document.querySelectorAll(
                        '[data-dashboard-appointment-timing]'
                    );

                const facilityElements =
                    document.querySelectorAll(
                        '[data-dashboard-facility-timing]'
                    );


                if (
                    ! appointmentElements.length &&
                    ! facilityElements.length
                ) {
                    return;
                }


                const pageStartedAt =
                    performance.now();


                const formatDuration =
                    (milliseconds) => {

                        const totalMinutes =
                            Math.max(
                                0,
                                Math.ceil(
                                    milliseconds / 60000
                                )
                            );


                        if (totalMinutes < 1) {
                            return '< 1m';
                        }


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

                            if (hours > 0) {
                                return `${days}d ${hours}h`;
                            }

                            return `${days}d`;

                        }


                        if (hours > 0) {

                            if (minutes > 0) {
                                return `${hours}h ${minutes}m`;
                            }

                            return `${hours}h`;

                        }


                        return `${minutes}m`;

                    };


                const serverTimeFor =
                    (element) => {

                        const serverNow =
                            Number(
                                element.dataset.serverNowMs
                            );

                        if (! Number.isFinite(serverNow)) {
                            return null;
                        }

                        return serverNow +
                            (
                                performance.now() -
                                pageStartedAt
                            );

                    };


                const updateAppointmentTiming =
                    () => {

                        appointmentElements.forEach(
                            (element) => {

                                const value =
                                    element.querySelector(
                                        '[data-dashboard-appointment-timing-value]'
                                    );

                                const startMs =
                                    Number(
                                        element.dataset.startMs
                                    );

                                const currentTime =
                                    serverTimeFor(
                                        element
                                    );


                                if (
                                    ! value ||
                                    ! Number.isFinite(startMs) ||
                                    currentTime === null
                                ) {
                                    return;
                                }


                                const difference =
                                    startMs -
                                    currentTime;


                                if (
                                    Math.abs(
                                        difference
                                    ) < 60000
                                ) {

                                    value.textContent =
                                        'Starting now';

                                    return;
                                }


                                if (difference > 0) {

                                    value.textContent =
                                        `Starts in ${
                                            formatDuration(
                                                difference
                                            )
                                        }`;

                                    return;
                                }


                                value.textContent =
                                    `Started ${
                                        formatDuration(
                                            Math.abs(
                                                difference
                                            )
                                        )
                                    } ago`;

                            }
                        );

                    };


                const updateFacilityTiming =
                    () => {

                        facilityElements.forEach(
                            (element) => {

                                const value =
                                    element.querySelector(
                                        '[data-dashboard-facility-timing-value]'
                                    );

                                const endMs =
                                    Number(
                                        element.dataset.endMs
                                    );

                                const currentTime =
                                    serverTimeFor(
                                        element
                                    );


                                if (
                                    ! value ||
                                    ! Number.isFinite(endMs) ||
                                    currentTime === null
                                ) {
                                    return;
                                }


                                const remaining =
                                    endMs -
                                    currentTime;


                                if (remaining <= 0) {

                                    value.textContent =
                                        'Reservation ended';

                                    return;
                                }


                                value.textContent =
                                    `${
                                        formatDuration(
                                            remaining
                                        )
                                    } remaining`;

                            }
                        );

                    };


                const updateLiveOperations =
                    () => {

                        updateAppointmentTiming();
                        updateFacilityTiming();

                    };


                updateLiveOperations();


                window.setInterval(
                    updateLiveOperations,
                    30000
                );

            }
        );
    </script>
@endonce