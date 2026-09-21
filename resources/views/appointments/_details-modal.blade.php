<div
    data-appointment-details-modal
    class="fixed inset-0 z-[90] hidden opacity-0 transition-opacity duration-200"
    role="dialog"
    aria-modal="true"
    aria-labelledby="appointment-details-title">

    <div
        data-appointment-details-backdrop
        class="absolute inset-0 bg-slate-950/50 backdrop-blur-[2px]">
    </div>

    <div class="relative flex min-h-full items-center justify-center p-4 sm:p-6">

        <div
            data-appointment-details-panel
            class="w-full max-w-2xl translate-y-2 scale-[0.98] overflow-hidden rounded-2xl border border-border bg-card shadow-2xl transition duration-200">

            <div class="flex items-start justify-between border-b border-border px-6 py-5">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-secondary">
                        Visitor Appointment
                    </p>

                    <h3
                        id="appointment-details-title"
                        data-detail-name
                        class="mt-1 font-heading text-xl font-bold text-primary">
                    </h3>

                    <p
                        data-detail-organization
                        class="mt-1 text-sm text-slate-500">
                    </p>
                </div>

                <button
                    type="button"
                    data-appointment-details-close
                    class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-400 transition hover:bg-background hover:text-primary"
                    aria-label="Close appointment details">

                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>

                </button>

            </div>

            <div class="space-y-5 p-6">

                <div class="flex items-center justify-between gap-3">

                    <span
                        data-detail-status
                        class="badge badge-info">
                    </span>

                    <span
                        data-detail-type
                        class="text-xs font-medium text-slate-500">
                    </span>

                </div>


                <div class="grid gap-4 sm:grid-cols-2">

                    <div class="rounded-xl border border-border bg-background/50 p-4">

                        <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Contact
                        </p>

                        <p data-detail-email class="text-sm font-medium text-primary"></p>
                        <p data-detail-contact class="mt-1 text-sm text-slate-500"></p>

                    </div>


                    <div class="rounded-xl border border-border bg-background/50 p-4">

                        <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Host
                        </p>

                        <p data-detail-host-name class="text-sm font-medium text-primary"></p>
                        <p data-detail-host-email class="mt-1 text-sm text-slate-500"></p>

                    </div>

                </div>


                <div class="rounded-xl border border-border bg-background/50 p-4">

                    <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Schedule
                    </p>

                    <div class="grid gap-3 sm:grid-cols-3">

                        <div>
                            <p class="text-xs text-slate-400">Date</p>
                            <p data-detail-date class="mt-1 text-sm font-semibold text-primary"></p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">Time</p>
                            <p data-detail-time class="mt-1 text-sm font-semibold text-primary"></p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">Facility</p>
                            <p data-detail-facility class="mt-1 text-sm font-semibold text-primary"></p>
                        </div>

                    </div>

                </div>


                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Purpose
                    </p>

                    <p data-detail-purpose class="mt-2 whitespace-pre-line text-sm text-slate-600"></p>
                </div>


                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Notes
                    </p>

                    <p data-detail-notes class="mt-2 whitespace-pre-line text-sm text-slate-600"></p>
                </div>

            </div>


            <div class="flex justify-end border-t border-border bg-background/40 px-6 py-4">

                <button
                    type="button"
                    data-appointment-details-close
                    class="btn-outline">
                    Close
                </button>

            </div>

        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', () => {

    const modal = document.querySelector('[data-appointment-details-modal]');
    const panel = document.querySelector('[data-appointment-details-panel]');
    const backdrop = document.querySelector('[data-appointment-details-backdrop]');
    const buttons = document.querySelectorAll('[data-appointment-view]');

    if (!modal || !panel) return;

    let trigger = null;

    const setText = (selector, value, fallback = 'Not provided') => {
        const element = modal.querySelector(selector);
        if (element) element.textContent = value?.trim() || fallback;
    };

    const formatDate = (value) => {
        if (!value) return 'Not provided';

        const match = value.match(/^(\d{4})-(\d{2})-(\d{2})/);
        if (!match) return value;

        const date = new Date(
            Number(match[1]),
            Number(match[2]) - 1,
            Number(match[3])
        );

        return new Intl.DateTimeFormat(undefined, {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }).format(date);
    };

    const formatTime = (value) => {
        if (!value) return '';

        const match = value.match(/^(\d{1,2}):(\d{2})/);
        if (!match) return value;

        let hour = Number(match[1]);
        const minute = match[2];
        const period = hour >= 12 ? 'PM' : 'AM';

        hour = hour % 12 || 12;

        return `${hour}:${minute} ${period}`;
    };

    const openModal = (button) => {

        trigger = button;

        setText('[data-detail-name]', button.dataset.name);
        setText('[data-detail-organization]', button.dataset.organization, 'No organization');
        setText('[data-detail-email]', button.dataset.email);
        setText('[data-detail-contact]', button.dataset.contact);
        setText('[data-detail-type]', button.dataset.type);
        setText('[data-detail-host-name]', button.dataset.hostName);
        setText('[data-detail-host-email]', button.dataset.hostEmail);
        setText('[data-detail-facility]', button.dataset.facility);
        setText('[data-detail-purpose]', button.dataset.purpose, 'No purpose provided');
        setText('[data-detail-notes]', button.dataset.notes, 'No additional notes');
        setText('[data-detail-date]', formatDate(button.dataset.date));

        const start = formatTime(button.dataset.start);
        const end = formatTime(button.dataset.end);

        setText(
            '[data-detail-time]',
            end ? `${start} – ${end}` : start
        );

        setText(
            '[data-detail-status]',
            (button.dataset.status || 'unknown')
                .replaceAll('_', ' ')
                .replace(/\b\w/g, char => char.toUpperCase())
        );

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            panel.classList.remove('translate-y-2', 'scale-[0.98]');
        });
    };

    const closeModal = () => {

        modal.classList.add('opacity-0');
        panel.classList.add('translate-y-2', 'scale-[0.98]');

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            trigger?.focus();
        }, 200);
    };

    buttons.forEach(button => {
        button.addEventListener('click', () => openModal(button));
    });

    modal.querySelectorAll('[data-appointment-details-close]')
        .forEach(button => button.addEventListener('click', closeModal));

    backdrop?.addEventListener('click', closeModal);

    document.addEventListener('keydown', event => {
        if (
            event.key === 'Escape'
            && !modal.classList.contains('hidden')
        ) {
            closeModal();
        }
    });

});
</script>