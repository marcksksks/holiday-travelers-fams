{{-- =====================================================
     COMPACT FACILITY CALENDAR
====================================================== --}}

@php
    $calendarToday =
        today()->toDateString();

    $todayBlocks =
        $facilityUsage
            ->where('date', $calendarToday)
            ->count();

    $upcomingBlocks =
        $facilityUsage
            ->filter(
                fn (array $usage) =>
                    $usage['date'] >= $calendarToday
            )
            ->count();

    $scheduledFacilities =
        $facilityUsage
            ->pluck('facility_id')
            ->filter()
            ->unique()
            ->count();

    /*
     * Privacy boundary:
     * Only occupancy information is serialized.
     * No requester, visitor, host, email, contact,
     * notes, or appointment purpose is exposed.
     */
    /*
     * All facilities supplied here are already operationally
     * available according to ReservationController.
     *
     * Include them even when they currently have zero events.
     */
    $calendarFacilities =
        $facilities
            ->map(
                fn ($facility) => [
                    'id' =>
                        (string) $facility->id,

                    'name' =>
                        $facility->name,
                ]
            )
            ->values();


    $calendarEvents =
        $facilityUsage
            ->map(
                fn (array $usage) => [
                    'source' =>
                        $usage['source'],

                    'facility_id' =>
                        (string) $usage['facility_id'],

                    'facility_name' =>
                        $usage['facility_name'],

                    'date' =>
                        $usage['date'],

                    'start_time' =>
                        substr(
                            (string) $usage['start_time'],
                            0,
                            5
                        ),

                    'end_time' =>
                        filled($usage['end_time'])
                            ? substr(
                                (string) $usage['end_time'],
                                0,
                                5
                            )
                            : null,

                    'status' =>
                        $usage['status'],
                ]
            )
            ->values();
@endphp


<section class="card overflow-hidden">

    <div class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

        <div class="min-w-0">

            <div class="flex items-center gap-2">

                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary/5 text-primary">

                    <svg
                        class="h-4 w-4"
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


                <div>

                    <h2 class="font-heading text-sm font-semibold text-primary">
                        Facility Schedule
                    </h2>

                    <p class="mt-0.5 text-xs text-slate-500">
                        Reservations and appointments.
                    </p>

                </div>

            </div>

        </div>


        <button
            type="button"
            data-facility-calendar-open
            class="btn-outline inline-flex shrink-0 items-center justify-center gap-2">

            <svg
                class="h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 011-1V6a1 1 0 011-1z" />

            </svg>

            Open Calendar

        </button>

    </div>


    <div class="grid border-t border-border sm:grid-cols-3">

        <div class="flex items-center justify-between gap-3 border-b border-border px-5 py-3 sm:border-b-0 sm:border-r">

            <span class="text-xs text-slate-500">
                Today
            </span>

            <span class="font-heading text-lg font-bold text-primary">
                {{ number_format($todayBlocks) }}
            </span>

        </div>


        <div class="flex items-center justify-between gap-3 border-b border-border px-5 py-3 sm:border-b-0 sm:border-r">

            <span class="text-xs text-slate-500">
                Upcoming
            </span>

            <span class="font-heading text-lg font-bold text-secondary">
                {{ number_format($upcomingBlocks) }}
            </span>

        </div>


        <div class="flex items-center justify-between gap-3 px-5 py-3">

            <span class="text-xs text-slate-500">
                Facilities
            </span>

            <span class="font-heading text-lg font-bold text-accent">
                {{ number_format($scheduledFacilities) }}
            </span>

        </div>

    </div>

</section>


{{-- =====================================================
     FACILITY CALENDAR MODAL
====================================================== --}}
<div
    data-facility-calendar-modal
    class="fixed inset-0 z-[140] hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="facility-calendar-title">

    <div
        data-facility-calendar-backdrop
        class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
    </div>


    <div class="relative flex min-h-full items-center justify-center p-2 sm:p-5">

        <div class="flex max-h-[calc(100vh-1rem)] w-full max-w-7xl flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-2xl sm:max-h-[calc(100vh-2.5rem)]">

            {{-- Modal header --}}
            <div class="flex shrink-0 items-center justify-between gap-4 border-b border-border px-4 py-3 sm:px-6">

                <div>

                    <h2
                        id="facility-calendar-title"
                        class="font-heading text-lg font-semibold text-primary">

                        Facility Schedule

                    </h2>

                </div>


                <button
                    type="button"
                    data-facility-calendar-close
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary"
                    aria-label="Close Facility Schedule">

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


            {{-- Calendar controls --}}
            <div class="flex shrink-0 flex-col gap-2.5 border-b border-border bg-background/35 px-4 py-2.5 lg:flex-row lg:items-center lg:justify-between sm:px-6">

                <div class="flex flex-wrap items-center gap-2">

                    <button
                        type="button"
                        data-calendar-today
                        class="btn-outline px-3 py-2 text-xs">

                        Today

                    </button>


                    <div class="flex items-center">

                        <button
                            type="button"
                            data-calendar-prev
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-card hover:text-primary"
                            aria-label="Previous month">

                            <svg
                                class="h-4 w-4"
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


                        <button
                            type="button"
                            data-calendar-next
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-card hover:text-primary"
                            aria-label="Next month">

                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7" />

                            </svg>

                        </button>

                    </div>


                    <h3
                        data-calendar-month-label
                        class="min-w-40 font-heading text-base font-semibold text-primary">
                    </h3>

                </div>


                <div class="flex flex-wrap items-center gap-3">

                    <select
                        data-calendar-facility-filter
                        class="input min-w-52 py-2 text-xs"
                        aria-label="Filter calendar by facility">

                        <option value="">
                            All facilities
                        </option>

                    </select>


                    <div class="flex flex-wrap items-center gap-3 text-[10px] font-medium text-slate-500">

    <span class="inline-flex items-center gap-1.5">

        <span class="h-2 w-2 rounded-full bg-secondary"></span>

        Reservation

    </span>


    <span class="inline-flex items-center gap-1.5">

        <span class="h-2 w-2 rounded-full bg-accent"></span>

        Appointment

    </span>


    <span
        class="inline-flex h-6 w-6 items-center justify-center rounded-full text-slate-400 transition hover:bg-card hover:text-primary"
        title="Appointment entries show occupancy only. Visitor, host, contact, and appointment details remain private."
        aria-label="Appointment entries show occupancy only. Visitor, host, contact, and appointment details remain private.">

        <svg
            class="h-3.5 w-3.5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

        </svg>

    </span>

</div>

                </div>

            </div>


            <div class="min-h-0 flex-1 overflow-y-auto">

                <div class="grid min-h-full xl:grid-cols-[minmax(0,1fr)_300px]">

                    {{-- Month calendar --}}
                    <div class="min-w-0">

                        {{-- Weekday headings --}}
                        <div class="grid grid-cols-7 border-b border-border bg-background/40">

                            @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)

                                <div class="px-1 py-2 text-center text-[10px] font-semibold uppercase tracking-wide text-slate-400 sm:text-xs">
                                    {{ $day }}
                                </div>

                            @endforeach

                        </div>


                        <div
                            data-calendar-grid
                            class="grid grid-cols-7">
                        </div>

                    </div>


                    {{-- Selected day agenda --}}
                    <aside class="border-t border-border bg-background/30 xl:border-l xl:border-t-0">

                        <div class="sticky top-0 border-b border-border bg-card/95 px-4 py-4 backdrop-blur sm:px-5">

                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                Selected Day
                            </p>

                            <h3
                                data-calendar-selected-label
                                class="mt-1 font-heading text-sm font-semibold text-primary">
                            </h3>

                        </div>


                        <div
                            data-calendar-agenda
                            class="space-y-2 p-4 sm:p-5">
                        </div>

                    </aside>

                </div>

            </div>


            {{-- Privacy footer --}}


        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', () => {

    const events =
        {{ Illuminate\Support\Js::from($calendarEvents) }};

    const availableFacilities =
        {{ Illuminate\Support\Js::from($calendarFacilities) }};

    const reservationsIndexUrl =
        {{ Illuminate\Support\Js::from(route('reservations.index')) }};

    const applicationToday =
        {{ Illuminate\Support\Js::from($calendarToday) }};


    const modal =
        document.querySelector(
            '[data-facility-calendar-modal]'
        );

    if (!modal) {
        return;
    }


    const openButtons =
        document.querySelectorAll(
            '[data-facility-calendar-open]'
        );

    const closeButtons =
        modal.querySelectorAll(
            '[data-facility-calendar-close]'
        );

    const backdrop =
        modal.querySelector(
            '[data-facility-calendar-backdrop]'
        );

    const grid =
        modal.querySelector(
            '[data-calendar-grid]'
        );

    const monthLabel =
        modal.querySelector(
            '[data-calendar-month-label]'
        );

    const selectedLabel =
        modal.querySelector(
            '[data-calendar-selected-label]'
        );

    const agenda =
        modal.querySelector(
            '[data-calendar-agenda]'
        );

    const facilityFilter =
        modal.querySelector(
            '[data-calendar-facility-filter]'
        );

    const previousButton =
        modal.querySelector(
            '[data-calendar-prev]'
        );

    const nextButton =
        modal.querySelector(
            '[data-calendar-next]'
        );

    const todayButton =
        modal.querySelector(
            '[data-calendar-today]'
        );


    const realToday =
        new Date();

    realToday.setHours(
        0,
        0,
        0,
        0
    );


    let currentMonth =
        new Date(
            realToday.getFullYear(),
            realToday.getMonth(),
            1
        );

    let selectedDate =
        new Date(realToday);


    const pad =
        (value) =>
            String(value).padStart(2, '0');


    const dateKey =
        (date) =>
            `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;


    const parseDate =
        (value) => {

            const [
                year,
                month,
                day
            ] =
                value
                    .split('-')
                    .map(Number);

            return new Date(
                year,
                month - 1,
                day
            );

        };


    const timeLabel =
        (value) => {

            if (!value) {
                return '';
            }

            const [
                hourValue,
                minute
            ] =
                value
                    .slice(0, 5)
                    .split(':')
                    .map(Number);

            const suffix =
                hourValue >= 12
                    ? 'PM'
                    : 'AM';

            const hour =
                hourValue % 12 || 12;

            return `${hour}:${pad(minute)} ${suffix}`;

        };


    const statusLabel =
        (value) => {

            if (value === 'pending') {
                return 'Pending hold';
            }

            return String(value || '')
                .replaceAll('_', ' ')
                .replace(
                    /\b\w/g,
                    (letter) =>
                        letter.toUpperCase()
                );

        };



    const facilityLabel =
        (value) => {

            const label =
                String(
                    value || 'Facility'
                ).trim();

            return /^\d+$/.test(label)
                ? `Facility ${label}`
                : label;

        };

    const filteredEvents =
        () => {

            const facilityId =
                facilityFilter.value;

            return events.filter(
                (event) =>
                    !facilityId
                    ||
                    String(event.facility_id) ===
                        facilityId
            );

        };


    const populateFacilities =
        () => {

            availableFacilities
                .slice()
                .sort(
                    (a, b) =>
                        a.name.localeCompare(
                            b.name
                        )
                )
                .forEach(
                    (facility) => {

                        const option =
                            document.createElement(
                                'option'
                            );

                        option.value =
                            String(facility.id);

                        option.textContent =
                            facilityLabel(facility.name);

                        facilityFilter.appendChild(
                            option
                        );

                    }
                );

        };

    const renderAgenda =
        () => {

            const key =
                dateKey(selectedDate);

            selectedLabel.textContent =
                selectedDate.toLocaleDateString(
                    undefined,
                    {
                        weekday: 'long',
                        month: 'long',
                        day: 'numeric',
                        year: 'numeric',
                    }
                );


            const dayEvents =
                filteredEvents()
                    .filter(
                        (event) =>
                            event.date === key
                    )
                    .sort(
                        (a, b) =>
                            String(
                                a.start_time
                            ).localeCompare(
                                String(
                                    b.start_time
                                )
                            )
                    );


            agenda.innerHTML = '';


            const selectedFacilityId =
                facilityFilter.value;

            const selectedFacilityName =
                selectedFacilityId
                    ? facilityFilter.options[
                        facilityFilter.selectedIndex
                    ].textContent
                    : null;

            const isBookableDate =
                key >= applicationToday;

            /*
             * We offer the quick booking action only when:
             *
             * 1. a specific operationally available facility
             *    has been selected;
             * 2. the selected day is today or later; and
             * 3. there are no known occupancy blocks for that
             *    facility on that date.
             *
             * The server-side FacilityAvailabilityService still
             * performs the final conflict validation.
             */
            const canQuickReserve =
                Boolean(selectedFacilityId)
                &&
                isBookableDate
                &&
                dayEvents.length === 0;


            if (dayEvents.length === 0) {

                const empty =
                    document.createElement(
                        'div'
                    );

                empty.className =
                    'rounded-xl border border-dashed border-border bg-card px-4 py-6 text-center';


                if (
                    selectedFacilityId
                    &&
                    isBookableDate
                ) {

                    empty.innerHTML = `
                        <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-success/10 text-success">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7">
                                </path>

                            </svg>

                        </div>

                        <p class="mt-3 text-xs font-semibold text-primary">
                            No occupied blocks
                        </p>

                        <p class="mt-1 text-[10px] leading-4 text-slate-400">
                            ${escapeHtml(selectedFacilityName)}
                            has no listed occupancy for this date.
                        </p>

                        <button
                            type="button"
                            data-calendar-reserve-date
                            class="btn-primary mt-4 inline-flex items-center justify-center gap-2 text-xs">

                            <svg
                                class="h-3.5 w-3.5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z">
                                </path>

                            </svg>

                            Reserve This Date

                        </button>
                    `;

                }
                else if (!selectedFacilityId) {

                    empty.innerHTML = `
                        <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-primary/5 text-primary">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5">
                                </path>

                            </svg>

                        </div>

                        <p class="mt-3 text-xs font-semibold text-primary">
                            No occupied blocks
                        </p>

                        <p class="mt-1 text-[10px] leading-4 text-slate-400">
                            Select a facility above to check its schedule
                            and start a reservation.
                        </p>
                    `;

                }
                else {

                    empty.innerHTML = `
                        <p class="text-xs font-semibold text-primary">
                            No occupied blocks
                        </p>

                        <p class="mt-1 text-[10px] leading-4 text-slate-400">
                            Past dates cannot be used for new reservations.
                        </p>
                    `;

                }


                agenda.appendChild(
                    empty
                );


                if (canQuickReserve) {

                    const reserveButton =
                        empty.querySelector(
                            '[data-calendar-reserve-date]'
                        );

                    reserveButton.addEventListener(
                        'click',
                        () => {

                            const url =
                                new URL(
                                    reservationsIndexUrl,
                                    window.location.origin
                                );

                            url.searchParams.set(
                                'reserve_facility',
                                selectedFacilityId
                            );

                            url.searchParams.set(
                                'reserve_date',
                                key
                            );

                            window.location.href =
                                url.toString();

                        }
                    );

                }


                return;

            }


            dayEvents.forEach(
                (event) => {

                    const isAppointment =
                        event.source ===
                        'appointment';

                    const item =
                        document.createElement(
                            'article'
                        );

                    item.className =
                        'rounded-xl border border-border bg-card p-3 shadow-sm';

                    item.innerHTML = `
                        <div class="flex items-start gap-3">

                            <div class="mt-0.5 h-full min-h-12 w-1 shrink-0 rounded-full ${
                                isAppointment
                                    ? 'bg-accent'
                                    : 'bg-secondary'
                            }">
                            </div>

                            <div class="min-w-0 flex-1">

                                <p class="truncate text-xs font-semibold text-primary">
                                    ${escapeHtml(
                                        facilityLabel(event.facility_name)
                                    )}
                                </p>

                                <p class="mt-1 text-[11px] font-medium text-slate-600">

                                    ${escapeHtml(
                                        timeLabel(
                                            event.start_time
                                        )
                                    )}

                                    ${
                                        event.end_time
                                            ? ` – ${escapeHtml(
                                                timeLabel(
                                                    event.end_time
                                                )
                                            )}`
                                            : ''
                                    }

                                </p>

                                <div class="mt-2 flex flex-wrap items-center gap-2">

                                    <span class="${
                                        isAppointment
                                            ? 'bg-accent/10 text-primary'
                                            : 'bg-secondary/10 text-secondary'
                                    } rounded-full px-2 py-0.5 text-[9px] font-semibold">

                                        ${
                                            isAppointment
                                                ? 'Appointment'
                                                : 'Reservation'
                                        }

                                    </span>

                                    <span class="text-[9px] font-medium text-slate-400">

                                        ${escapeHtml(
                                            statusLabel(
                                                event.status
                                            )
                                        )}

                                    </span>

                                </div>

                            </div>

                        </div>
                    `;

                    agenda.appendChild(
                        item
                    );

                }
            );

        };

    const escapeHtml =
        (value) => {

            const div =
                document.createElement(
                    'div'
                );

            div.textContent =
                String(value ?? '');

            return div.innerHTML;

        };


    const renderCalendar =
        () => {

            monthLabel.textContent =
                currentMonth.toLocaleDateString(
                    undefined,
                    {
                        month: 'long',
                        year: 'numeric',
                    }
                );


            grid.innerHTML = '';


            const year =
                currentMonth.getFullYear();

            const month =
                currentMonth.getMonth();

            const firstWeekday =
                new Date(
                    year,
                    month,
                    1
                ).getDay();

            const lastDate =
                new Date(
                    year,
                    month + 1,
                    0
                ).getDate();

            const previousMonthLastDate =
                new Date(
                    year,
                    month,
                    0
                ).getDate();


            const visibleEvents =
                filteredEvents();


            const createDay =
                (
                    date,
                    muted = false
                ) => {

                    const key =
                        dateKey(date);

                    const isToday =
                        key ===
                        dateKey(realToday);

                    const isSelected =
                        key ===
                        dateKey(selectedDate);

                    const dayEvents =
                        visibleEvents
                            .filter(
                                (event) =>
                                    event.date === key
                            )
                            .sort(
                                (a, b) =>
                                    String(
                                        a.start_time
                                    ).localeCompare(
                                        String(
                                            b.start_time
                                        )
                                    )
                            );


                    const cell =
                        document.createElement(
                            'button'
                        );

                    cell.type = 'button';

                    cell.className =
                        'min-h-24 border-b border-r border-border p-1.5 text-left transition hover:bg-background/70 sm:min-h-28 sm:p-2';

                    if (muted) {
                        cell.classList.add(
                            'bg-background/20',
                            'opacity-50'
                        );
                    }

                    if (isSelected) {
                        cell.classList.add(
                            'ring-2',
                            'ring-inset',
                            'ring-accent/30'
                        );
                    }


                    const number =
                        document.createElement(
                            'span'
                        );

                    number.textContent =
                        String(date.getDate());

                    number.className =
                        'inline-flex h-6 min-w-6 items-center justify-center rounded-full px-1 text-[10px] font-semibold sm:text-xs';

                    if (isToday) {

                        number.classList.add(
                            'bg-primary',
                            'text-white'
                        );

                    }
                    else {

                        number.classList.add(
                            'text-slate-600'
                        );

                    }


                    cell.appendChild(
                        number
                    );


                    const eventContainer =
                        document.createElement(
                            'div'
                        );

                    eventContainer.className =
                        'mt-1 space-y-1';


                    dayEvents
                        .slice(0, 3)
                        .forEach(
                            (event) => {

                                const pill =
                                    document.createElement(
                                        'div'
                                    );

                                const isAppointment =
                                    event.source ===
                                    'appointment';

                                pill.className =
                                    `truncate rounded px-1.5 py-1 text-[8px] font-semibold sm:text-[9px] ${
                                        isAppointment
                                            ? 'bg-accent/10 text-primary'
                                            : 'bg-secondary/10 text-secondary'
                                    }`;

                                pill.textContent =
                                    `${timeLabel(event.start_time)} · ${facilityLabel(event.facility_name)}`;

                                eventContainer.appendChild(
                                    pill
                                );

                            }
                        );


                    if (dayEvents.length > 3) {

                        const more =
                            document.createElement(
                                'div'
                            );

                        more.className =
                            'px-1 text-[8px] font-semibold text-slate-400 sm:text-[9px]';

                        more.textContent =
                            `+${dayEvents.length - 3} more`;

                        eventContainer.appendChild(
                            more
                        );

                    }


                    cell.appendChild(
                        eventContainer
                    );


                    cell.addEventListener(
                        'click',
                        () => {

                            selectedDate =
                                new Date(date);

                            if (
                                selectedDate.getMonth() !==
                                currentMonth.getMonth()
                                ||
                                selectedDate.getFullYear() !==
                                currentMonth.getFullYear()
                            ) {

                                currentMonth =
                                    new Date(
                                        selectedDate.getFullYear(),
                                        selectedDate.getMonth(),
                                        1
                                    );

                            }

                            renderCalendar();
                            renderAgenda();

                        }
                    );


                    grid.appendChild(
                        cell
                    );

                };


            for (
                let offset =
                    firstWeekday;
                offset >
                    0;
                offset--
            ) {

                createDay(
                    new Date(
                        year,
                        month - 1,
                        previousMonthLastDate -
                            offset +
                            1
                    ),
                    true
                );

            }


            for (
                let day = 1;
                day <= lastDate;
                day++
            ) {

                createDay(
                    new Date(
                        year,
                        month,
                        day
                    )
                );

            }


            const usedCells =
                firstWeekday +
                lastDate;

            const trailingCells =
                usedCells <= 35
                    ? 35 - usedCells
                    : 42 - usedCells;


            for (
                let day = 1;
                day <= trailingCells;
                day++
            ) {

                createDay(
                    new Date(
                        year,
                        month + 1,
                        day
                    ),
                    true
                );

            }

        };


    const openModal =
        () => {

            modal.classList.remove(
                'hidden'
            );

            document.body.classList.add(
                'overflow-hidden'
            );

            renderCalendar();
            renderAgenda();

        };


    const closeModal =
        () => {

            modal.classList.add(
                'hidden'
            );

            document.body.classList.remove(
                'overflow-hidden'
            );

        };


    openButtons.forEach(
        (button) => {

            button.addEventListener(
                'click',
                openModal
            );

        }
    );


    closeButtons.forEach(
        (button) => {

            button.addEventListener(
                'click',
                closeModal
            );

        }
    );


    backdrop.addEventListener(
        'click',
        closeModal
    );


    previousButton.addEventListener(
        'click',
        () => {

            currentMonth =
                new Date(
                    currentMonth.getFullYear(),
                    currentMonth.getMonth() - 1,
                    1
                );

            renderCalendar();

        }
    );


    nextButton.addEventListener(
        'click',
        () => {

            currentMonth =
                new Date(
                    currentMonth.getFullYear(),
                    currentMonth.getMonth() + 1,
                    1
                );

            renderCalendar();

        }
    );


    todayButton.addEventListener(
        'click',
        () => {

            selectedDate =
                new Date(realToday);

            currentMonth =
                new Date(
                    realToday.getFullYear(),
                    realToday.getMonth(),
                    1
                );

            renderCalendar();
            renderAgenda();

        }
    );


    facilityFilter.addEventListener(
        'change',
        () => {

            renderCalendar();
            renderAgenda();

        }
    );


    document.addEventListener(
        'keydown',
        (event) => {

            if (
                event.key === 'Escape'
                &&
                !modal.classList.contains(
                    'hidden'
                )
            ) {

                closeModal();

            }

        }
    );


    /*
     * When the calendar sends the user into the existing
     * Reserve Facility modal, pre-fill only an empty date field.
     *
     * If Laravel returned validation errors with old input,
     * that old value remains authoritative.
     */
    const requestedReserveDate =
        new URLSearchParams(
            window.location.search
        ).get(
            'reserve_date'
        );

    if (requestedReserveDate) {

        const reservationModal =
            document.querySelector(
                '[data-reservation-create-modal]'
            );

        const reservationDateInput =
            reservationModal
                ? reservationModal.querySelector(
                    '[name="date"]'
                )
                : null;

        if (
            reservationDateInput
            &&
            !reservationDateInput.value
        ) {

            reservationDateInput.value =
                requestedReserveDate;

        }

    }


    populateFacilities();

});
</script>