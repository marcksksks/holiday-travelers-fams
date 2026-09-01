import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // DESKTOP SIDEBAR COLLAPSE
    // ==========================================

    const sidebar = document.querySelector('[data-sidebar]');
    const sidebarToggles = document.querySelectorAll('[data-sidebar-toggle]');
    const sidebarLabels = document.querySelectorAll('[data-sidebar-label]');

    let sidebarCollapsed = false;

    const updateSidebar = () => {

        if (!sidebar) return;

        if (sidebarCollapsed) {

            sidebar.classList.remove('md:w-64');
            sidebar.classList.add('md:w-20');

            sidebarLabels.forEach((label) => {
                label.classList.add('hidden');
            });

        } else {

            sidebar.classList.remove('md:w-20');
            sidebar.classList.add('md:w-64');

            sidebarLabels.forEach((label) => {
                label.classList.remove('hidden');
            });

        }

    };

    sidebarToggles.forEach((button) => {

        button.addEventListener('click', () => {

            sidebarCollapsed = !sidebarCollapsed;

            updateSidebar();

        });

    });


    // ==========================================
    // MOBILE SIDEBAR
    // ==========================================

    const overlay = document.querySelector('[data-sidebar-overlay]');
    const menuButtons = document.querySelectorAll('[data-mobile-menu]');

    const openSidebar = () => {

        if (!sidebar) return;

        sidebar.classList.remove('-translate-x-full');

        if (overlay) {
            overlay.classList.remove('hidden');
        }

    };

    const closeSidebar = () => {

        if (!sidebar) return;

        sidebar.classList.add('-translate-x-full');

        if (overlay) {
            overlay.classList.add('hidden');
        }

    };

    menuButtons.forEach((button) => {

        button.addEventListener('click', () => {

            if (sidebar?.classList.contains('-translate-x-full')) {

                openSidebar();

            } else {

                closeSidebar();

            }

        });

    });

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }


    // ==========================================
    // PROFILE DROPDOWN
    // ==========================================

    const profileButton = document.querySelector('[data-profile-button]');
    const profileMenu = document.querySelector('[data-profile-menu]');

    if (profileButton && profileMenu) {

        profileButton.addEventListener('click', (event) => {

            event.stopPropagation();

            profileMenu.classList.toggle('hidden');

        });

        document.addEventListener('click', (event) => {

            if (
                !profileMenu.contains(event.target) &&
                !profileButton.contains(event.target)
            ) {

                profileMenu.classList.add('hidden');

            }

        });

    }


    // ==========================================
    // DISMISS ALERTS
    // ==========================================

    document.querySelectorAll('[data-dismiss]').forEach((button) => {

        button.addEventListener('click', () => {

            const toast = button.closest('[data-toast]');

            if (toast) {
                toast.remove();
            }

        });

    });


    // ==========================================
    // AUTO-HIDE ALERTS
    // ==========================================

    document.querySelectorAll('[data-toast]').forEach((toast) => {

        setTimeout(() => {

            toast.style.transition = 'opacity 300ms ease';
            toast.style.opacity = '0';

            setTimeout(() => {

                toast.remove();

            }, 300);

        }, 5000);

    });

});
