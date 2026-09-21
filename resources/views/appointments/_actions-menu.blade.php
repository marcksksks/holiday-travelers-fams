{{-- =====================================================
     APPOINTMENT ACTIONS POPOVER
====================================================== --}}

<div
    data-appointment-actions-menu
    role="menu"
    class="fixed z-[120] hidden w-56 origin-top-right rounded-xl border border-border bg-card p-1.5 opacity-0 shadow-xl transition duration-150">

    {{-- View --}}
    <button
        type="button"
        data-action-view
        role="menuitem"
        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-slate-600 transition hover:bg-background hover:text-primary">

        <svg
            class="h-4 w-4 text-slate-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c-2 4-5 7-9 7s-7-3-9-7c2-4 5-7 9-7s7 3 9 7z" />

        </svg>

        View Details

    </button>


    {{-- Edit --}}
    <button
        type="button"
        data-action-edit
        role="menuitem"
        class="hidden w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-slate-600 transition hover:bg-background hover:text-primary">

        <svg
            class="h-4 w-4 text-slate-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />

        </svg>

        Edit Appointment

    </button>


    {{-- Confirm --}}
    <form
        data-action-confirm-form
        method="POST"
        class="hidden">

        @csrf
        @method('PATCH')

        <input
            type="hidden"
            name="status"
            value="confirmed">

        <button
            type="submit"
            role="menuitem"
            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-medium text-success transition hover:bg-success/5">

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

            Confirm Appointment

        </button>

    </form>


    {{-- Visitor Desk --}}
    <a
        data-action-visitor-desk
        href="{{ route('visitors.index') }}"
        role="menuitem"
        class="hidden w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-slate-600 transition hover:bg-background hover:text-primary">

        <svg
            class="h-4 w-4 text-slate-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17 20h5V4H2v16h5m10 0v-2a5 5 0 00-10 0v2m10 0H7m8-10a3 3 0 11-6 0 3 3 0 016 0z" />

        </svg>

        Go to Visitor Desk

    </a>


    {{-- Divider --}}
    <div
        data-action-danger-divider
        class="my-1 hidden border-t border-border">
    </div>


    {{-- No Show --}}
    <form
        data-action-no-show-form
        method="POST"
        class="hidden"
        onsubmit="return confirm('Mark this appointment as no show?');">

        @csrf
        @method('PATCH')

        <input
            type="hidden"
            name="status"
            value="no_show">

        <button
            type="submit"
            role="menuitem"
            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-slate-600 transition hover:bg-warning/5 hover:text-warning">

            <svg
                class="h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v2m0 4h.01M5.07 19h13.86L12 5 5.07 19z" />

            </svg>

            Mark No Show

        </button>

    </form>


    {{-- Cancel --}}
    <form
        data-action-cancel-form
        method="POST"
        class="hidden"
        onsubmit="return confirm('Cancel this appointment?');">

        @csrf
        @method('DELETE')

        <button
            type="submit"
            role="menuitem"
            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-error transition hover:bg-error/5">

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

            Cancel Appointment

        </button>

    </form>

</div>


<script>
document.addEventListener('DOMContentLoaded', () => {

    const menu =
        document.querySelector(
            '[data-appointment-actions-menu]'
        );

    const triggers =
        document.querySelectorAll(
            '[data-appointment-actions-open]'
        );

    if (!menu || !triggers.length) {
        return;
    }


    const view =
        menu.querySelector('[data-action-view]');

    const edit =
        menu.querySelector('[data-action-edit]');

    const confirmForm =
        menu.querySelector('[data-action-confirm-form]');

    const visitorDesk =
        menu.querySelector('[data-action-visitor-desk]');

    const noShowForm =
        menu.querySelector('[data-action-no-show-form]');

    const cancelForm =
        menu.querySelector('[data-action-cancel-form]');

    const divider =
        menu.querySelector('[data-action-danger-divider]');


    let currentTrigger = null;


    const showElement = (element, show, flex = false) => {

        if (!element) return;

        element.classList.toggle(
            'hidden',
            !show
        );

        if (flex) {
            element.classList.toggle(
                'flex',
                show
            );
        }

    };


    const closeMenu = () => {

        if (menu.classList.contains('hidden')) {
            return;
        }

        menu.classList.add('opacity-0');

        currentTrigger?.setAttribute(
            'aria-expanded',
            'false'
        );

        setTimeout(() => {

            menu.classList.add('hidden');

        }, 120);
    };


    const positionMenu = trigger => {

        const rect =
            trigger.getBoundingClientRect();

        menu.style.visibility = 'hidden';
        menu.classList.remove('hidden');

        const menuRect =
            menu.getBoundingClientRect();


        let top =
            rect.bottom + 8;

        let left =
            rect.right - menuRect.width;


        if (
            top + menuRect.height >
            window.innerHeight - 12
        ) {
            top =
                rect.top -
                menuRect.height -
                8;
        }


        left =
            Math.max(
                12,
                Math.min(
                    left,
                    window.innerWidth -
                        menuRect.width -
                        12
                )
            );


        menu.style.top =
            `${Math.max(12, top)}px`;

        menu.style.left =
            `${left}px`;

        menu.style.visibility =
            'visible';


        requestAnimationFrame(() => {
            menu.classList.remove('opacity-0');
        });
    };


    const openMenu = trigger => {

        if (
            currentTrigger === trigger
            &&
            !menu.classList.contains('hidden')
        ) {
            closeMenu();
            return;
        }


        currentTrigger?.setAttribute(
            'aria-expanded',
            'false'
        );

        currentTrigger =
            trigger;


        const id =
            trigger.dataset.appointmentId;

        const status =
            trigger.dataset.status;

        const canManage =
            trigger.dataset.canManage === '1';


        const editable =
            canManage &&
            [
                'scheduled',
                'confirmed'
            ].includes(status);


        const canConfirm =
            canManage &&
            status === 'scheduled';


        const visitorDeskAllowed =
            [
                'confirmed',
                'checked_in'
            ].includes(status);


        const canNoShow =
            canManage &&
            [
                'scheduled',
                'confirmed'
            ].includes(status);


        const canCancel =
            canManage &&
            [
                'scheduled',
                'confirmed'
            ].includes(status);


        showElement(
            edit,
            editable,
            true
        );

        showElement(
            confirmForm,
            canConfirm
        );

        showElement(
            visitorDesk,
            visitorDeskAllowed,
            true
        );

        showElement(
            noShowForm,
            canNoShow
        );

        showElement(
            cancelForm,
            canCancel
        );

        showElement(
            divider,
            canNoShow || canCancel
        );


        if (confirmForm) {
            confirmForm.action =
                trigger.dataset.statusUrl;
        }

        if (noShowForm) {
            noShowForm.action =
                trigger.dataset.statusUrl;
        }

        if (cancelForm) {
            cancelForm.action =
                trigger.dataset.cancelUrl;
        }


        view.dataset.targetAppointment =
            id;

        edit.dataset.targetAppointment =
            id;


        trigger.setAttribute(
            'aria-expanded',
            'true'
        );


        positionMenu(trigger);
    };


    triggers.forEach(trigger => {

        trigger.addEventListener(
            'click',
            event => {

                event.preventDefault();
                event.stopPropagation();

                openMenu(trigger);
            }
        );

    });


    view?.addEventListener(
        'click',
        () => {

            const id =
                view.dataset.targetAppointment;

            closeMenu();

            document.querySelector(
                `[data-appointment-view][data-appointment-id="${id}"]`
            )?.click();

        }
    );


    edit?.addEventListener(
        'click',
        () => {

            const id =
                edit.dataset.targetAppointment;

            closeMenu();

            document.querySelector(
                `[data-appointment-edit][data-appointment-id="${id}"]`
            )?.click();

        }
    );


    document.addEventListener(
        'click',
        event => {

            if (
                !menu.contains(event.target)
            ) {
                closeMenu();
            }

        }
    );


    document.addEventListener(
        'keydown',
        event => {

            if (event.key === 'Escape') {

                const trigger =
                    currentTrigger;

                closeMenu();

                trigger?.focus();
            }

        }
    );


    window.addEventListener(
        'resize',
        closeMenu
    );


    window.addEventListener(
        'scroll',
        closeMenu,
        true
    );

});
</script>