<div
    data-reservation-details-modal
    class="fixed inset-0 z-[130] hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="reservation-details-title">

    <div
        data-reservation-details-backdrop
        class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
    </div>

    <div class="relative flex min-h-full items-center justify-center p-3 sm:p-6">

        <div class="flex max-h-[calc(100vh-2rem)] w-full max-w-2xl flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-2xl">

            {{-- Header --}}
            <div class="flex items-start justify-between gap-4 border-b border-border px-5 py-4 sm:px-6">

                <div class="min-w-0">

                    <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-secondary">
                        Facility Reservation
                    </p>

                    <h2
                        id="reservation-details-title"
                        class="mt-1 font-heading text-lg font-semibold text-primary">

                        Reservation Details

                    </h2>

                    <p
                        data-detail-reservation-number
                        class="mt-1 text-xs text-slate-400">
                    </p>

                </div>

                <button
                    type="button"
                    data-reservation-details-close
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary"
                    aria-label="Close reservation details">

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


            {{-- Content --}}
            <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6">

                {{-- Facility --}}
                <div class="rounded-2xl border border-accent/20 bg-accent/5 p-4">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                        <div>

                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                Facility
                            </p>

                            <p
                                data-detail-facility
                                class="mt-1 font-heading text-base font-semibold text-primary">
                            </p>

                            <p class="mt-1 text-xs text-slate-500">

                                <span data-detail-facility-type></span>

                                <span
                                    data-detail-location-separator
                                    class="mx-1 text-slate-300">
                                    &bull;
                                </span>

                                <span data-detail-location></span>

                            </p>

                        </div>

                        <span
                            data-detail-status
                            class="inline-flex self-start rounded-full bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">
                        </span>

                    </div>

                </div>


                {{-- Details grid --}}
                <div class="mt-5 grid gap-4 sm:grid-cols-2">

                    <div class="rounded-xl border border-border p-4">

                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                            Schedule
                        </p>

                        <p
                            data-detail-date
                            class="mt-2 text-sm font-semibold text-slate-700">
                        </p>

                        <p
                            data-detail-time
                            class="mt-1 text-xs text-slate-500">
                        </p>

                    </div>


                    <div class="rounded-xl border border-border p-4">

                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                            Attendees
                        </p>

                        <p
                            data-detail-attendees
                            class="mt-2 text-sm font-semibold text-slate-700">
                        </p>

                        <p
                            data-detail-capacity
                            class="mt-1 text-xs text-slate-500">
                        </p>

                    </div>


                    <div class="rounded-xl border border-border p-4 sm:col-span-2">

                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                            Requester
                        </p>

                        <p
                            data-detail-requester
                            class="mt-2 text-sm font-semibold text-slate-700">
                        </p>

                        <p
                            data-detail-requester-email
                            class="mt-1 break-all text-xs text-slate-500">
                        </p>

                    </div>

                </div>


                {{-- Purpose --}}
                <div class="mt-5">

                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                        Purpose
                    </p>

                    <p
                        data-detail-purpose
                        class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">
                    </p>

                </div>


                {{-- Decision note --}}
                <div
                    data-detail-decision-wrap
                    class="mt-5 hidden rounded-xl border border-border bg-background/60 p-4">

                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                        Decision Note
                    </p>

                    <p
                        data-detail-decision-note
                        class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">
                    </p>

                </div>

            </div>


            <div class="flex justify-end border-t border-border bg-background/40 px-5 py-4 sm:px-6">

                <button
                    type="button"
                    data-reservation-details-close
                    class="btn-outline">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', () => {

    const modal =
        document.querySelector(
            '[data-reservation-details-modal]'
        );

    if (!modal) {
        return;
    }

    const text = (selector, value, fallback = '—') => {

        const element =
            modal.querySelector(selector);

        if (element) {
            element.textContent =
                value && value.trim()
                    ? value
                    : fallback;
        }

    };


    const headline = (value) => {

        if (!value) {
            return 'Unknown';
        }

        return value
            .replaceAll('_', ' ')
            .replace(
                /\b\w/g,
                (letter) => letter.toUpperCase()
            );

    };


    const formatTime = (value) => {

        if (!value) {
            return '';
        }

        const parts =
            value.substring(0, 5).split(':');

        let hour =
            Number(parts[0]);

        const minute =
            parts[1];

        const suffix =
            hour >= 12 ? 'PM' : 'AM';

        hour =
            hour % 12 || 12;

        return `${hour}:${minute} ${suffix}`;

    };


    const close = () => {

        modal.classList.add('hidden');

        document.body.classList.remove(
            'overflow-hidden'
        );

    };


    const open = (trigger) => {

        text(
            '[data-detail-reservation-number]',
            `Reservation #${trigger.dataset.reservationId}`
        );

        text(
            '[data-detail-facility]',
            trigger.dataset.reservationFacility
        );

        text(
            '[data-detail-facility-type]',
            headline(
                trigger.dataset.reservationFacilityType
            )
        );

        text(
            '[data-detail-location]',
            trigger.dataset.reservationLocation,
            ''
        );

        const separator =
            modal.querySelector(
                '[data-detail-location-separator]'
            );

        if (separator) {

            separator.classList.toggle(
                'hidden',
                !trigger.dataset.reservationLocation
            );

        }


        text(
            '[data-detail-status]',
            headline(
                trigger.dataset.reservationStatus
            )
        );

        text(
            '[data-detail-date]',
            trigger.dataset.reservationDate
        );


        const start =
            formatTime(
                trigger.dataset.reservationStart
            );

        const end =
            formatTime(
                trigger.dataset.reservationEnd
            );

        text(
            '[data-detail-time]',
            end
                ? `${start} – ${end}`
                : start
        );


        const unit =
            trigger.dataset.reservationFacilityType ===
            'vehicle'
                ? 'passengers'
                : 'people';

        text(
            '[data-detail-attendees]',
            trigger.dataset.reservationAttendees
                ? `${trigger.dataset.reservationAttendees} ${unit}`
                : 'Not specified'
        );


        text(
            '[data-detail-capacity]',
            trigger.dataset.reservationCapacity
                ? `Facility capacity: ${trigger.dataset.reservationCapacity}`
                : 'No capacity limit recorded'
        );


        text(
            '[data-detail-requester]',
            trigger.dataset.reservationRequester
        );

        text(
            '[data-detail-requester-email]',
            trigger.dataset.reservationRequesterEmail
        );


        text(
            '[data-detail-purpose]',
            trigger.dataset.reservationPurpose,
            'No purpose provided.'
        );


        const decision =
            trigger.dataset.reservationNote || '';

        const decisionWrap =
            modal.querySelector(
                '[data-detail-decision-wrap]'
            );

        if (decisionWrap) {

            decisionWrap.classList.toggle(
                'hidden',
                !decision
            );

        }

        text(
            '[data-detail-decision-note]',
            decision,
            ''
        );


        modal.classList.remove('hidden');

        document.body.classList.add(
            'overflow-hidden'
        );

    };


    document.addEventListener(
        'click',
        (event) => {

            const trigger =
                event.target.closest(
                    '[data-reservation-view-open]'
                );

            if (trigger) {

                event.preventDefault();

                open(trigger);

                return;

            }


            if (
                event.target.closest(
                    '[data-reservation-details-close]'
                )
                ||
                event.target.closest(
                    '[data-reservation-details-backdrop]'
                )
            ) {

                close();

            }

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

                close();

            }

        }
    );

});
</script>