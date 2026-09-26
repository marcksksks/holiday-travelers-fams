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


<section class="space-y-3">

    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">

        <div>

            <div class="mb-2 flex items-center gap-2">

                <span class="relative flex h-2.5 w-2.5">

                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-success opacity-40"></span>

                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-success"></span>

                </span>

                <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-success">
                    Live
                </p>

            </div>


            <h2 class="font-heading text-lg font-semibold text-primary">
                Current Operations
            </h2>

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
                Auto-refresh
            </span>

        </div>

    </div>


    {{-- Operational cards --}}
    <div
        @class([
            'grid gap-3',
            'lg:grid-cols-3' => $liveOperationsCardCount >= 3,
            'sm:grid-cols-2' => $liveOperationsCardCount === 2,
            'grid-cols-1' => $liveOperationsCardCount === 1,
        ])>


        {{-- Visitors currently on site --}}
        @if ($canViewVisitors)

            <a
                href="{{ route('visitors.index', ['status' => 'checked_in']) }}"
                class="group card relative overflow-hidden p-4 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-soft">

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

                        No visitors on site.

                    @elseif ($checkedInVisitors === 1)

                        1 visitor is currently inside the premises.

                    @else

                        {{ number_format($checkedInVisitors) }} visitors are currently inside the premises.

                    @endif

                </p>


                <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-success">

                    <span>
                        Visitor Desk
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
                class="group card relative overflow-hidden p-4 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-soft">

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
                                Nothing scheduled.
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
            class="group card relative overflow-hidden p-4 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-soft">

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
                    No facilities in use.
                </p>

            @endif


            <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-secondary">

                <span>
                    Reservations
                </span>

                <span class="transition-transform group-hover:translate-x-1">
                    &rarr;
                </span>

            </div>

        </a>

    </div>

</section>
