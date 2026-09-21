<div
    data-facility-details-modal
    class="fixed inset-0 z-[130] hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="facility-details-title">

    <div
        data-facility-details-backdrop
        class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
    </div>

    <div class="relative flex min-h-full items-center justify-center p-3 sm:p-6">

        <div class="flex max-h-[calc(100vh-2rem)] w-full max-w-2xl flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-2xl">

            <div class="flex items-start justify-between gap-4 border-b border-border px-5 py-4 sm:px-6">

                <div class="min-w-0">

                    <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-secondary">
                        Facilities Reservation
                    </p>

                    <h2
                        id="facility-details-title"
                        data-facility-detail-name
                        class="mt-1 truncate font-heading text-lg font-semibold text-primary">
                        Facility Details
                    </h2>

                    <p
                        data-facility-detail-reference
                        class="mt-1 text-xs text-slate-400">
                    </p>

                </div>

                <button
                    type="button"
                    data-facility-details-close
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary"
                    aria-label="Close facility details">

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


            <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6">

                <div class="rounded-2xl border border-accent/20 bg-accent/5 p-4">

                    <div class="flex items-start justify-between gap-4">

                        <div>

                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                Facility Type
                            </p>

                            <p
                                data-facility-detail-type
                                class="mt-1 text-sm font-semibold text-primary">
                            </p>

                        </div>

                        <span
                            data-facility-detail-status
                            class="badge badge-info">
                        </span>

                    </div>

                </div>


                <div class="mt-5 grid gap-4 sm:grid-cols-2">

                    <div class="rounded-xl border border-border p-4">

                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                            Location
                        </p>

                        <p
                            data-facility-detail-location
                            class="mt-2 text-sm font-semibold text-slate-700">
                        </p>

                    </div>


                    <div class="rounded-xl border border-border p-4">

                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                            Capacity
                        </p>

                        <p
                            data-facility-detail-capacity
                            class="mt-2 text-sm font-semibold text-slate-700">
                        </p>

                    </div>

                </div>


                <div class="mt-5">

                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                        Description
                    </p>

                    <p
                        data-facility-detail-description
                        class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">
                    </p>

                </div>

            </div>


            <div class="flex justify-end border-t border-border bg-background/40 px-5 py-4 sm:px-6">

                <button
                    type="button"
                    data-facility-details-close
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
            '[data-facility-details-modal]'
        );

    if (!modal) {
        return;
    }

    const setText = (selector, value, fallback = 'Not specified') => {

        const element =
            modal.querySelector(selector);

        if (!element) {
            return;
        }

        element.textContent =
            value && value.trim()
                ? value
                : fallback;

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


    const close = () => {

        modal.classList.add('hidden');

        document.body.classList.remove(
            'overflow-hidden'
        );

    };


    const open = (trigger) => {

        setText(
            '[data-facility-detail-name]',
            trigger.dataset.facilityName,
            'Facility Details'
        );

        setText(
            '[data-facility-detail-reference]',
            `Facility #${trigger.dataset.facilityId}`
        );

        setText(
            '[data-facility-detail-type]',
            headline(trigger.dataset.facilityType)
        );

        setText(
            '[data-facility-detail-status]',
            headline(trigger.dataset.facilityStatus)
        );

        setText(
            '[data-facility-detail-location]',
            trigger.dataset.facilityLocation
        );


        const capacity =
            trigger.dataset.facilityCapacity;

        const unit =
            trigger.dataset.facilityType === 'vehicle'
                ? 'passengers'
                : 'people';

        setText(
            '[data-facility-detail-capacity]',
            capacity
                ? `${capacity} ${unit}`
                : 'No capacity recorded'
        );

        setText(
            '[data-facility-detail-description]',
            trigger.dataset.facilityDescription,
            'No description provided.'
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
                    '[data-facility-view-open]'
                );

            if (trigger) {

                event.preventDefault();

                open(trigger);

                return;

            }

            if (
                event.target.closest(
                    '[data-facility-details-close]'
                )
                ||
                event.target.closest(
                    '[data-facility-details-backdrop]'
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
                !modal.classList.contains('hidden')
            ) {

                close();

            }

        }
    );

});
</script>