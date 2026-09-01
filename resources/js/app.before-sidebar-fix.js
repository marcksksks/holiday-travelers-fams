import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {

    // Desktop Sidebar Hide / Show
    const sidebar = document.querySelector('[data-sidebar]');
    const sidebarToggleButtons = document.querySelectorAll('[data-sidebar-toggle]');

    if (sidebar && sidebarToggleButtons.length) {

        sidebarToggleButtons.forEach((button) => {

            button.addEventListener('click', () => {

                const isHidden = sidebar.classList.toggle('md:hidden');

                sidebarToggleButtons.forEach((btn) => {
                    btn.setAttribute('aria-expanded', String(!isHidden));

                    btn.setAttribute(
                        'aria-label',
                        isHidden ? 'Show sidebar' : 'Hide sidebar'
                    );

                    btn.setAttribute(
                        'title',
                        isHidden ? 'Show sidebar' : 'Hide sidebar'
                    );
                });

            });

        });

    }


    // Mobile Sidebar
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


    // Profile Dropdown
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


    // Dismiss Toast / Alerts
    document.querySelectorAll('[data-dismiss]').forEach((button) => {

        button.addEventListener('click', () => {

            const toast = button.closest('[data-toast]');

            if (toast) {
                toast.remove();
            }

        });

    });


    // Automatically Hide Toasts
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
