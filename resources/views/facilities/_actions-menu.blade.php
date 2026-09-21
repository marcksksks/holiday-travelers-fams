<div
    data-facility-actions-menu
    class="fixed z-[120] hidden w-56 overflow-hidden rounded-xl border border-border bg-card p-1.5 shadow-2xl"
    role="menu">

    {{-- View --}}
    <button
        type="button"
        data-facility-menu-view
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


    {{-- Edit --}}
    <button
        type="button"
        data-facility-menu-edit
        class="hidden w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium text-primary transition hover:bg-accent/10">

        <svg
            class="h-4 w-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-9.5a2 2 0 012.8 2.8L12 12.6 9 13l.4-3 7.1-7.5z" />

        </svg>

        Edit Facility

    </button>


    <div
        data-facility-menu-divider
        class="my-1 hidden border-t border-border">
    </div>


    {{-- Restore --}}
    <form
        method="POST"
        data-facility-menu-restore-form
        class="hidden"
        onsubmit="return confirm('Restore this facility? It will return as unavailable until reviewed.');">

        @csrf
        @method('PATCH')

        <button
            type="submit"
            class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium text-success transition hover:bg-success/10">

            <svg
                class="h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v6h6M20 20v-6h-6M5.5 14A7 7 0 0018 17M18.5 10A7 7 0 006 7" />

            </svg>

            Restore Facility

        </button>

    </form>


    {{-- Archive --}}
    <form
        method="POST"
        data-facility-menu-archive-form
        class="hidden"
        onsubmit="return confirm('Archive this facility? It will no longer accept new reservation requests.');">

        @csrf
        @method('DELETE')

        <button
            type="submit"
            class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium text-error transition hover:bg-error/10">

            <svg
                class="h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 8h14M7 8v11h10V8M9 4h6l1 4H8l1-4z" />

            </svg>

            Archive Facility

        </button>

    </form>

</div>


<script>
document.addEventListener('DOMContentLoaded', () => {

    const menu =
        document.querySelector(
            '[data-facility-actions-menu]'
        );

    if (!menu) {
        return;
    }

    const viewButton =
        menu.querySelector(
            '[data-facility-menu-view]'
        );

    const editButton =
        menu.querySelector(
            '[data-facility-menu-edit]'
        );

    const archiveForm =
        menu.querySelector(
            '[data-facility-menu-archive-form]'
        );

    const restoreForm =
        menu.querySelector(
            '[data-facility-menu-restore-form]'
        );

    const divider =
        menu.querySelector(
            '[data-facility-menu-divider]'
        );

    let currentFacilityId = null;


    const closeMenu = () => {

        menu.classList.add('hidden');

        currentFacilityId = null;

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

        currentFacilityId =
            trigger.dataset.facilityId;

        const canEdit =
            trigger.dataset.canEdit === '1';

        const canArchive =
            trigger.dataset.canArchive === '1';

        const canRestore =
            trigger.dataset.canRestore === '1';


        archiveForm.action =
            trigger.dataset.archiveUrl || '';

        restoreForm.action =
            trigger.dataset.restoreUrl || '';


        editButton.classList.toggle(
            'hidden',
            !canEdit
        );

        editButton.classList.toggle(
            'flex',
            canEdit
        );


        archiveForm.classList.toggle(
            'hidden',
            !canArchive
        );

        restoreForm.classList.toggle(
            'hidden',
            !canRestore
        );


        divider.classList.toggle(
            'hidden',
            !canArchive &&
            !canRestore
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
                    `[data-facility-view-open][data-facility-id="${currentFacilityId}"]`
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
                    `[data-facility-edit-open][data-facility-id="${currentFacilityId}"]`
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
                    '[data-facility-actions-open]'
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