<div
    data-legal-actions-menu
    class="fixed z-[160] hidden w-56 overflow-hidden rounded-xl border border-border bg-card p-1.5 shadow-2xl"
    role="menu">

    <button
        type="button"
        data-legal-menu-view
        class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium text-primary transition hover:bg-accent/10">

        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c-1.5 4-4.5 7-9 7s-7.5-3-9-7c1.5-4 4.5-7 9-7s7.5 3 9 7z" />
        </svg>

        View Details

    </button>


    <a
        href="#"
        data-legal-menu-edit
        class="hidden w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium text-primary transition hover:bg-accent/10">

        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-9.5a2 2 0 012.8 2.8L12 12.6 9 13l.4-3 7.1-7.5z" />
        </svg>

        Edit Record

    </a>


    <button
        type="button"
        data-legal-menu-review
        class="hidden w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium text-primary transition hover:bg-secondary/10">

        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m5-3a11.95 11.95 0 01-8-3 11.95 11.95 0 01-8 3c0 5.25 3.4 10 8 11 4.6-1 8-5.75 8-11z" />
        </svg>

        Legal Review

    </button>

</div>


<script>
document.addEventListener('DOMContentLoaded', () => {

    const menu =
        document.querySelector(
            '[data-legal-actions-menu]'
        );

    if (!menu) {
        return;
    }

    const viewButton =
        menu.querySelector(
            '[data-legal-menu-view]'
        );

    const editButton =
        menu.querySelector(
            '[data-legal-menu-edit]'
        );

    const reviewButton =
        menu.querySelector(
            '[data-legal-menu-review]'
        );

    let detailsTarget = null;
    let reviewTarget = null;


    const closeMenu = () => {

        menu.classList.add('hidden');

        detailsTarget = null;
        reviewTarget = null;

    };


    const closeModal = (modal) => {

        if (!modal) {
            return;
        }

        modal.classList.add('hidden');

        if (
            !document.querySelector(
                '[data-legal-modal]:not(.hidden)'
            )
        ) {
            document.body.classList.remove(
                'overflow-hidden'
            );
        }

    };


    const openModal = (target) => {

        if (!target) {
            return;
        }

        const modal =
            document.getElementById(
                target
            );

        if (!modal) {
            return;
        }

        modal.classList.remove('hidden');

        document.body.classList.add(
            'overflow-hidden'
        );

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

        detailsTarget =
            trigger.dataset.detailsTarget;

        reviewTarget =
            trigger.dataset.reviewTarget;

        const canEdit =
            trigger.dataset.canEdit === '1';

        const canReview =
            trigger.dataset.canReview === '1';

        editButton.href =
            trigger.dataset.editUrl || '#';

        editButton.classList.toggle(
            'hidden',
            !canEdit
        );

        editButton.classList.toggle(
            'flex',
            canEdit
        );

        reviewButton.classList.toggle(
            'hidden',
            !canReview
        );

        reviewButton.classList.toggle(
            'flex',
            canReview
        );

        menu.classList.remove('hidden');

        requestAnimationFrame(
            () => positionMenu(trigger)
        );

    };


    viewButton.addEventListener(
        'click',
        () => {

            const target =
                detailsTarget;

            closeMenu();

            openModal(target);

        }
    );


    reviewButton.addEventListener(
        'click',
        () => {

            const target =
                reviewTarget;

            closeMenu();

            openModal(target);

        }
    );


    document.addEventListener(
        'click',
        (event) => {

            const actionsTrigger =
                event.target.closest(
                    '[data-legal-actions-open]'
                );

            if (actionsTrigger) {

                event.preventDefault();
                event.stopPropagation();

                openMenu(
                    actionsTrigger
                );

                return;
            }


            const directView =
                event.target.closest(
                    '[data-legal-view-open]'
                );

            if (directView) {

                event.preventDefault();

                openModal(
                    directView.dataset.modalTarget
                );

                return;
            }


            if (
                event.target.closest(
                    '[data-legal-create-open]'
                )
            ) {

                openModal(
                    'legal-create-modal'
                );

                return;
            }


            const closeButton =
                event.target.closest(
                    '[data-legal-modal-close]'
                );

            if (closeButton) {

                closeModal(
                    closeButton.closest(
                        '[data-legal-modal]'
                    )
                );

                return;
            }


            const backdrop =
                event.target.closest(
                    '[data-legal-modal-backdrop]'
                );

            if (backdrop) {

                closeModal(
                    backdrop.closest(
                        '[data-legal-modal]'
                    )
                );

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

            if (event.key !== 'Escape') {
                return;
            }

            closeMenu();

            const openModals =
                Array.from(
                    document.querySelectorAll(
                        '[data-legal-modal]:not(.hidden)'
                    )
                );

            const last =
                openModals.at(-1);

            closeModal(last);

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