<div
    data-reservation-actions-menu
    class="fixed z-[120] hidden w-52 overflow-hidden rounded-xl border border-border bg-card p-1.5 shadow-2xl"
    role="menu">

    <button
        type="button"
        data-reservation-menu-view
        class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium text-primary transition hover:bg-accent/10">

        <svg
            class="h-4 w-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c-1.5 4-4.5 7-9 7s-7.5-3-9-7c1.5-4 4.5-7 9-7s7.5 3 9 7z" />

        </svg>

        View Details

    </button>


    <div
        data-reservation-menu-divider
        class="my-1 border-t border-border">
    </div>


    <form
        method="POST"
        data-reservation-menu-approve-form
        class="hidden">

        @csrf

        <input type="hidden" name="decision" value="approved">

        <button
            type="submit"
            class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium text-success transition hover:bg-success/10">

            Approve

        </button>

    </form>


    <form
        method="POST"
        data-reservation-menu-reject-form
        class="hidden">

        @csrf

        <input type="hidden" name="decision" value="rejected">

        <button
            type="submit"
            class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium text-error transition hover:bg-error/10">

            Reject

        </button>

    </form>


    <button
        type="button"
        data-reservation-menu-edit
        class="hidden w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium text-primary transition hover:bg-accent/10">

        Edit Request

    </button>


    <form
        method="POST"
        data-reservation-menu-cancel-form
        class="hidden"
        onsubmit="return confirm('Cancel this reservation request?');">

        @csrf
        @method('DELETE')

        <button
            type="submit"
            class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium text-slate-600 transition hover:bg-error/5 hover:text-error">

            Cancel Request

        </button>

    </form>

</div>


<script>
document.addEventListener('DOMContentLoaded', () => {

    const menu =
        document.querySelector(
            '[data-reservation-actions-menu]'
        );

    if (!menu) {
        return;
    }

    const viewButton =
        menu.querySelector(
            '[data-reservation-menu-view]'
        );

    const editButton =
        menu.querySelector(
            '[data-reservation-menu-edit]'
        );

    const approveForm =
        menu.querySelector(
            '[data-reservation-menu-approve-form]'
        );

    const rejectForm =
        menu.querySelector(
            '[data-reservation-menu-reject-form]'
        );

    const cancelForm =
        menu.querySelector(
            '[data-reservation-menu-cancel-form]'
        );

    const divider =
        menu.querySelector(
            '[data-reservation-menu-divider]'
        );

    let currentReservationId = null;


    const closeMenu = () => {

        menu.classList.add('hidden');

        currentReservationId = null;

    };


    const positionMenu = (trigger) => {

        const rect =
            trigger.getBoundingClientRect();

        const menuRect =
            menu.getBoundingClientRect();

        const padding = 12;
        const gap = 8;

        let left =
            rect.right -
            menuRect.width;

        left =
            Math.max(
                padding,
                Math.min(
                    left,
                    window.innerWidth -
                    menuRect.width -
                    padding
                )
            );

        let top =
            rect.bottom +
            gap;

        if (
            top +
            menuRect.height >
            window.innerHeight -
            padding
        ) {

            top =
                rect.top -
                menuRect.height -
                gap;

        }

        menu.style.left =
            `${left}px`;

        menu.style.top =
            `${Math.max(padding, top)}px`;

    };


    const openMenu = (trigger) => {

        currentReservationId =
            trigger.dataset.reservationId;

        const canDecide =
            trigger.dataset.canDecide === '1';

        const canEdit =
            trigger.dataset.canEdit === '1';

        const canCancel =
            trigger.dataset.canCancel === '1';


        approveForm.action =
            trigger.dataset.decideUrl || '';

        rejectForm.action =
            trigger.dataset.decideUrl || '';

        cancelForm.action =
            trigger.dataset.cancelUrl || '';


        approveForm.classList.toggle(
            'hidden',
            !canDecide
        );

        rejectForm.classList.toggle(
            'hidden',
            !canDecide
        );

        cancelForm.classList.toggle(
            'hidden',
            !canCancel
        );

        editButton.classList.toggle(
            'hidden',
            !canEdit
        );

        editButton.classList.toggle(
            'flex',
            canEdit
        );


        divider.classList.toggle(
            'hidden',
            !canDecide &&
            !canEdit &&
            !canCancel
        );


        menu.classList.remove('hidden');

        requestAnimationFrame(
            () => positionMenu(trigger)
        );

    };


    viewButton.addEventListener(
        'click',
        () => {

            const trigger =
                document.querySelector(
                    `[data-reservation-view-open][data-reservation-id="${currentReservationId}"]`
                );

            closeMenu();

            if (trigger) {
                trigger.click();
            }

        }
    );


    editButton.addEventListener(
        'click',
        () => {

            const trigger =
                document.querySelector(
                    `[data-reservation-edit-open][data-reservation-id="${currentReservationId}"]`
                );

            closeMenu();

            if (trigger) {
                trigger.click();
            }

        }
    );


    document.addEventListener(
        'click',
        (event) => {

            const trigger =
                event.target.closest(
                    '[data-reservation-actions-open]'
                );

            if (trigger) {

                event.preventDefault();
                event.stopPropagation();

                openMenu(trigger);

                return;

            }

            if (!menu.contains(event.target)) {
                closeMenu();
            }

        }
    );


    document.addEventListener(
        'keydown',
        (event) => {

            if (event.key === 'Escape') {
                closeMenu();
            }

        }
    );

    window.addEventListener(
        'resize',
        closeMenu
    );

    document.addEventListener(
        'scroll',
        closeMenu,
        true
    );

});
</script>