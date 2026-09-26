import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {

    // FAMS POPOVER MOTION HELPERS
    const famsReducedMotion =
        window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        ).matches;

    const famsPopoverTimers =
        new WeakMap();


    const showFamsPopover = (element) => {

        if (!element) {
            return;
        }

        const previousTimer =
            famsPopoverTimers.get(element);

        if (previousTimer) {
            window.clearTimeout(
                previousTimer
            );
        }

        element.classList.remove(
            'hidden',
            'fams-popover-exit'
        );


        if (famsReducedMotion) {
            return;
        }


        element.classList.remove(
            'fams-popover-enter'
        );

        void element.offsetWidth;

        element.classList.add(
            'fams-popover-enter'
        );
    };


    const hideFamsPopover = (element) => {

        if (
            !element
            ||
            element.classList.contains(
                'hidden'
            )
        ) {
            return;
        }


        const previousTimer =
            famsPopoverTimers.get(element);

        if (previousTimer) {
            window.clearTimeout(
                previousTimer
            );
        }


        const finish = () => {

            element.classList.add(
                'hidden'
            );

            element.classList.remove(
                'fams-popover-enter',
                'fams-popover-exit'
            );
        };


        if (famsReducedMotion) {

            finish();
            return;
        }


        element.classList.remove(
            'fams-popover-enter'
        );

        element.classList.add(
            'fams-popover-exit'
        );


        const timer =
            window.setTimeout(
                finish,
                150
            );

        famsPopoverTimers.set(
            element,
            timer
        );
    };


    const toggleFamsPopover = (element) => {

        const shouldOpen =
            element.classList.contains(
                'hidden'
            )
            ||
            element.classList.contains(
                'fams-popover-exit'
            );

        if (shouldOpen) {
            showFamsPopover(element);
        } else {
            hideFamsPopover(element);
        }

        return shouldOpen;
    };

    // ==========================================
    // DESKTOP SIDEBAR COMPACT / EXPANDED
    // ==========================================

    const sidebar =
        document.querySelector(
            '[data-sidebar]'
        );

    const sidebarToggles =
        document.querySelectorAll(
            '[data-sidebar-toggle]'
        );

    const sidebarStorageKey =
        'fams-sidebar-collapsed';

    const legacySidebarStorageKey =
        'fams-sidebar-hidden';

    let sidebarCollapsed =
        localStorage.getItem(
            sidebarStorageKey
        ) === 'true';


    /*
     * Migrate the previous fully-hidden sidebar preference
     * to the compact navigation rail.
     */
    if (
        localStorage.getItem(
            sidebarStorageKey
        ) === null
        &&
        localStorage.getItem(
            legacySidebarStorageKey
        ) === 'true'
    ) {
        sidebarCollapsed = true;

        localStorage.setItem(
            sidebarStorageKey,
            'true'
        );

        localStorage.removeItem(
            legacySidebarStorageKey
        );
    }


    /*
     * Compact-rail tooltip.
     *
     * It is rendered outside the sidebar so the independently
     * scrolling navigation cannot clip the tooltip.
     */
    const sidebarTooltip =
        document.createElement(
            'div'
        );

    sidebarTooltip.className =
        'fams-sidebar-tooltip';

    sidebarTooltip.setAttribute(
        'role',
        'tooltip'
    );

    sidebarTooltip.setAttribute(
        'aria-hidden',
        'true'
    );

    document.body.appendChild(
        sidebarTooltip
    );


    let sidebarTooltipTarget =
        null;


    const hideSidebarTooltip = () => {

        sidebarTooltipTarget =
            null;

        sidebarTooltip.dataset.visible =
            'false';

        sidebarTooltip.setAttribute(
            'aria-hidden',
            'true'
        );
    };


    const showSidebarTooltip =
        (target) => {

            if (
                !sidebar
                ||
                sidebar.dataset.collapsed !== 'true'
                ||
                window.innerWidth < 768
            ) {
                hideSidebarTooltip();
                return;
            }


            const label =
                target.dataset.sidebarTooltip;

            if (!label) {
                hideSidebarTooltip();
                return;
            }


            sidebarTooltipTarget =
                target;

            sidebarTooltip.textContent =
                label;

            sidebarTooltip.dataset.visible =
                'true';

            sidebarTooltip.setAttribute(
                'aria-hidden',
                'false'
            );


            const targetRect =
                target.getBoundingClientRect();

            const tooltipRect =
                sidebarTooltip.getBoundingClientRect();


            const horizontalGap =
                12;

            const viewportPadding =
                12;


            let left =
                targetRect.right
                +
                horizontalGap;

            let top =
                targetRect.top
                +
                (
                    targetRect.height
                    -
                    tooltipRect.height
                ) / 2;


            left =
                Math.min(
                    left,
                    window.innerWidth
                    -
                    tooltipRect.width
                    -
                    viewportPadding
                );


            top =
                Math.max(
                    viewportPadding,
                    Math.min(
                        top,
                        window.innerHeight
                        -
                        tooltipRect.height
                        -
                        viewportPadding
                    )
                );


            sidebarTooltip.style.left =
                `${Math.round(left)}px`;

            sidebarTooltip.style.top =
                `${Math.round(top)}px`;
        };


    document
        .querySelectorAll(
            '[data-sidebar-tooltip]'
        )
        .forEach(
            (target) => {

                target.addEventListener(
                    'mouseenter',
                    () => {
                        showSidebarTooltip(
                            target
                        );
                    }
                );


                target.addEventListener(
                    'mouseleave',
                    hideSidebarTooltip
                );


                target.addEventListener(
                    'focus',
                    () => {
                        showSidebarTooltip(
                            target
                        );
                    }
                );


                target.addEventListener(
                    'blur',
                    hideSidebarTooltip
                );
            }
        );


    window.addEventListener(
        'resize',
        hideSidebarTooltip
    );


    document.addEventListener(
        'scroll',
        hideSidebarTooltip,
        true
    );


    const updateDesktopSidebar = () => {

        if (!sidebar) {
            return;
        }


        sidebar.dataset.collapsed =
            sidebarCollapsed
                ? 'true'
                : 'false';


        sidebarToggles.forEach(
            (button) => {

                button.setAttribute(
                    'aria-expanded',
                    String(
                        !sidebarCollapsed
                    )
                );

                button.setAttribute(
                    'aria-label',
                    sidebarCollapsed
                        ? 'Expand sidebar'
                        : 'Collapse sidebar'
                );

                button.setAttribute(
                    'title',
                    sidebarCollapsed
                        ? 'Expand sidebar'
                        : 'Collapse sidebar'
                );
            }
        );


        if (!sidebarCollapsed) {
            hideSidebarTooltip();
        }
    };


    sidebarToggles.forEach(
        (button) => {

            button.addEventListener(
                'click',
                () => {

                    sidebarCollapsed =
                        !sidebarCollapsed;


                    localStorage.setItem(
                        sidebarStorageKey,
                        String(
                            sidebarCollapsed
                        )
                    );


                    updateDesktopSidebar();
                }
            );
        }
    );


    updateDesktopSidebar();

    // ==========================================
    // MOBILE SIDEBAR
    // ==========================================

    const overlay =
        document.querySelector(
            '[data-sidebar-overlay]'
        );

    const menuButtons =
        document.querySelectorAll(
            '[data-mobile-menu]'
        );

    const mobileNavigation =
        window.matchMedia(
            '(max-width: 767px)'
        );


    const setMobileNavigationState =
        (open) => {

            menuButtons.forEach(
                (button) => {

                    button.setAttribute(
                        'aria-expanded',
                        String(open)
                    );
                }
            );

            if (overlay) {

                overlay.setAttribute(
                    'aria-hidden',
                    String(!open)
                );
            }
        };


    const openSidebar = () => {

        if (!sidebar) {
            return;
        }

        sidebar.classList.remove(
            '-translate-x-full'
        );

        overlay?.classList.remove(
            'hidden'
        );

        document.body.classList.add(
            'fams-mobile-nav-open'
        );

        setMobileNavigationState(
            true
        );


        window.requestAnimationFrame(
            () => {

                sidebar
                    .querySelector(
                        '[data-sidebar-nav-link]'
                    )
                    ?.focus();
            }
        );
    };


    const closeSidebar = () => {

        if (!sidebar) {
            return;
        }

        sidebar.classList.add(
            '-translate-x-full'
        );

        overlay?.classList.add(
            'hidden'
        );

        document.body.classList.remove(
            'fams-mobile-nav-open'
        );

        setMobileNavigationState(
            false
        );
    };


    menuButtons.forEach(
        (button) => {

            button.addEventListener(
                'click',
                () => {

                    const isInsideSidebar =
                        Boolean(
                            button.closest(
                                '[data-sidebar]'
                            )
                        );

                    if (isInsideSidebar) {

                        closeSidebar();
                        return;
                    }


                    if (
                        sidebar?.classList.contains(
                            '-translate-x-full'
                        )
                    ) {
                        openSidebar();
                    } else {
                        closeSidebar();
                    }
                }
            );
        }
    );


    overlay?.addEventListener(
        'click',
        closeSidebar
    );


    sidebar
        ?.querySelectorAll(
            '[data-sidebar-nav-link]'
        )
        .forEach(
            (link) => {

                link.addEventListener(
                    'click',
                    () => {

                        if (
                            mobileNavigation.matches
                        ) {
                            closeSidebar();
                        }
                    }
                );
            }
        );


    document.addEventListener(
        'keydown',
        (event) => {

            if (
                event.key === 'Escape'
                &&
                mobileNavigation.matches
                &&
                sidebar
                &&
                !sidebar.classList.contains(
                    '-translate-x-full'
                )
            ) {
                closeSidebar();

                document
                    .querySelector(
                        'header [data-mobile-menu]'
                    )
                    ?.focus();
            }
        }
    );


    const handleNavigationViewport =
        (event) => {

            if (!event.matches) {
                closeSidebar();
            }
        };


    if (
        typeof mobileNavigation.addEventListener
        === 'function'
    ) {
        mobileNavigation.addEventListener(
            'change',
            handleNavigationViewport
        );
    }


    setMobileNavigationState(
        false
    );

    // ==========================================
    // PROFILE DROPDOWN
    // ==========================================

    const profileButton =
        document.querySelector(
            '[data-profile-button]'
        );

    const profileMenu =
        document.querySelector(
            '[data-profile-menu]'
        );


    const closeProfileMenu = () => {

        if (
            !profileButton
            ||
            !profileMenu
        ) {
            return;
        }

        hideFamsPopover(
            profileMenu
        );

        profileButton.setAttribute(
            'aria-expanded',
            'false'
        );
    };


    const openProfileMenu = () => {

        if (
            !profileButton
            ||
            !profileMenu
        ) {
            return;
        }

        window.dispatchEvent(
            new CustomEvent(
                'fams:close-notification-menu'
            )
        );

        window.dispatchEvent(
            new CustomEvent(
                'fams:close-global-search'
            )
        );

        showFamsPopover(
            profileMenu
        );

        profileButton.setAttribute(
            'aria-expanded',
            'true'
        );
    };


    if (
        profileButton
        &&
        profileMenu
    ) {
        profileButton.addEventListener(
            'click',
            (event) => {

                event.stopPropagation();

                const closed =
                    profileMenu.classList.contains(
                        'hidden'
                    )
                    ||
                    profileMenu.classList.contains(
                        'fams-popover-exit'
                    );

                if (closed) {
                    openProfileMenu();
                } else {
                    closeProfileMenu();
                }
            }
        );


        document.addEventListener(
            'click',
            (event) => {

                if (
                    !profileMenu.contains(
                        event.target
                    )
                    &&
                    !profileButton.contains(
                        event.target
                    )
                ) {
                    closeProfileMenu();
                }
            }
        );


        document.addEventListener(
            'keydown',
            (event) => {

                if (
                    event.key === 'Escape'
                    &&
                    !profileMenu.classList.contains(
                        'hidden'
                    )
                ) {
                    closeProfileMenu();

                    profileButton.focus();
                }
            }
        );


        window.addEventListener(
            'fams:close-profile-menu',
            closeProfileMenu
        );
    }

    // ==========================================
    // TOAST NOTIFICATIONS
    // ==========================================

    const reducedToastMotion =
        window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        ).matches;


    const closeToast = (toast) => {

        if (
            !toast
            ||
            toast.dataset.closing === 'true'
        ) {
            return;
        }

        toast.dataset.closing = 'true';


        const removeToast = () => {
            toast.remove();
        };


        if (reducedToastMotion) {

            removeToast();
            return;
        }


        toast.classList.remove(
            'fams-toast-enter'
        );

        toast.classList.add(
            'fams-toast-leave'
        );


        window.setTimeout(
            removeToast,
            240
        );
    };


    document
        .querySelectorAll('[data-toast]')
        .forEach((toast) => {

            if (!reducedToastMotion) {

                window.requestAnimationFrame(
                    () => {
                        toast.classList.add(
                            'fams-toast-enter'
                        );
                    }
                );
            }


            const dismissButton =
                toast.querySelector(
                    '[data-dismiss]'
                );


            dismissButton?.addEventListener(
                'click',
                () => closeToast(toast)
            );


            // Validation errors remain visible.
            if (
                toast.hasAttribute(
                    'data-toast-persistent'
                )
            ) {
                return;
            }


            let timer =
                window.setTimeout(
                    () => closeToast(toast),
                    4000
                );


            // Give the user more time if they hover it.
            toast.addEventListener(
                'mouseenter',
                () => {
                    window.clearTimeout(timer);
                }
            );


            toast.addEventListener(
                'mouseleave',
                () => {

                    timer =
                        window.setTimeout(
                            () => closeToast(toast),
                            1800
                        );
                }
            );
        });

});


// ==========================================
// NOTIFICATION DROPDOWN
// ==========================================

document.addEventListener('DOMContentLoaded', () => {

    const notificationButton =
        document.querySelector(
            '[data-notification-button]'
        );

    const notificationMenu =
        document.querySelector(
            '[data-notification-menu]'
        );


    if (
        !notificationButton
        ||
        !notificationMenu
    ) {
        return;
    }


    const reducedMotion =
        window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        ).matches;


    let closeTimer = null;


    const showNotifications = () => {

        window.dispatchEvent(
            new CustomEvent(
                'fams:close-profile-menu'
            )
        );

        window.dispatchEvent(
            new CustomEvent(
                'fams:close-global-search'
            )
        );

        if (closeTimer) {
            window.clearTimeout(closeTimer);
            closeTimer = null;
        }


        notificationMenu.classList.remove(
            'hidden',
            'fams-popover-exit'
        );


        if (!reducedMotion) {

            notificationMenu.classList.remove(
                'fams-popover-enter'
            );

            void notificationMenu.offsetWidth;

            notificationMenu.classList.add(
                'fams-popover-enter'
            );
        }


        notificationButton.setAttribute(
            'aria-expanded',
            'true'
        );
    };


    const hideNotifications = () => {

        if (
            notificationMenu.classList.contains(
                'hidden'
            )
        ) {
            notificationButton.setAttribute(
                'aria-expanded',
                'false'
            );

            return;
        }


        const finishClose = () => {

            notificationMenu.classList.add(
                'hidden'
            );

            notificationMenu.classList.remove(
                'fams-popover-enter',
                'fams-popover-exit'
            );

            notificationButton.setAttribute(
                'aria-expanded',
                'false'
            );
        };


        if (reducedMotion) {

            finishClose();
            return;
        }


        notificationMenu.classList.remove(
            'fams-popover-enter'
        );

        notificationMenu.classList.add(
            'fams-popover-exit'
        );


        closeTimer =
            window.setTimeout(
                finishClose,
                150
            );
    };


    notificationButton.addEventListener(
        'click',
        (event) => {

            event.preventDefault();
            event.stopPropagation();


            const isClosed =
                notificationMenu.classList.contains(
                    'hidden'
                )
                ||
                notificationMenu.classList.contains(
                    'fams-popover-exit'
                );


            if (isClosed) {

                showNotifications();

            } else {

                hideNotifications();
            }
        }
    );


    notificationMenu.addEventListener(
        'click',
        (event) => {

            event.stopPropagation();
        }
    );


    document.addEventListener(
        'click',
        () => {

            hideNotifications();
        }
    );


    document.addEventListener(
        'keydown',
        (event) => {

            if (event.key === 'Escape') {

                hideNotifications();

                notificationButton.focus();
            }
        }
    );


    window.addEventListener(
        'fams:close-notification-menu',
        () => {

            if (closeTimer) {
                window.clearTimeout(closeTimer);
                closeTimer = null;
            }

            notificationMenu.classList.add(
                'hidden'
            );

            notificationMenu.classList.remove(
                'fams-popover-enter',
                'fams-popover-exit'
            );

            notificationButton.setAttribute(
                'aria-expanded',
                'false'
            );
        }
    );
});



// ==========================================
// AI VISITOR ASSISTANT
// ==========================================

document.addEventListener('DOMContentLoaded', () => {

    const assistant = document.querySelector(
        '[data-ai-visitor-assistant]'
    );

    if (!assistant) {
        return;
    }

    const endpoint = assistant.dataset.aiEndpoint;
    const csrf = assistant.dataset.aiCsrf;

    const input = assistant.querySelector('[data-ai-input]');
    const emptyState = assistant.querySelector('[data-ai-empty]');
    const loadingState = assistant.querySelector('[data-ai-loading]');
    const resultContainer = assistant.querySelector('[data-ai-result]');
    const statusBadge = assistant.querySelector('[data-ai-status]');
    const applyWrap = assistant.querySelector('[data-ai-apply-wrap]');
    const applyButton = assistant.querySelector('[data-ai-apply]');

    const modeButtons = assistant.querySelectorAll(
        '[data-ai-mode]'
    );

    let currentSuggestion = null;
    let currentMode = null;


    const clearNode = (node) => {

        while (node.firstChild) {
            node.removeChild(node.firstChild);
        }

    };


    const makeText = (
        tag,
        text,
        className = ''
    ) => {

        const element = document.createElement(tag);

        element.textContent = text;

        if (className) {
            element.className = className;
        }

        return element;

    };


    const makeRow = (label, value) => {

        const row = document.createElement('div');

        row.className =
            'grid gap-1 rounded-xl border border-border bg-background/60 px-4 py-3 sm:grid-cols-[130px_minmax(0,1fr)]';

        row.appendChild(
            makeText(
                'div',
                label,
                'text-xs font-medium text-slate-400'
            )
        );

        row.appendChild(
            makeText(
                'div',
                value || 'Not provided',
                'break-words text-sm font-medium text-primary'
            )
        );

        return row;

    };


    const getFormContext = () => {

        const fieldIds = [
            'full_name',
            'contact_number',
            'email',
            'organization',
            'visitor_type',
            'host_email',
            'host_name',
            'purpose'
        ];

        const context = {};

        fieldIds.forEach((id) => {

            const field = document.getElementById(id);

            if (!field) {
                return;
            }

            const value = field.value?.trim();

            if (value) {
                context[id] = value;
            }

        });

        return context;

    };


    const setLoading = (loading) => {

        emptyState.classList.add('hidden');
        resultContainer.classList.add('hidden');
        applyWrap.classList.add('hidden');

        if (loading) {

            loadingState.classList.remove('hidden');
            loadingState.classList.add('flex');

            modeButtons.forEach((button) => {
                button.disabled = true;
                button.classList.add(
                    'cursor-not-allowed',
                    'opacity-60'
                );
            });

        } else {

            loadingState.classList.add('hidden');
            loadingState.classList.remove('flex');

            modeButtons.forEach((button) => {
                button.disabled = false;
                button.classList.remove(
                    'cursor-not-allowed',
                    'opacity-60'
                );
            });

        }

    };


    const showError = (message) => {

        setLoading(false);

        clearNode(resultContainer);

        const box = document.createElement('div');

        box.className =
            'rounded-xl border border-error/20 bg-error/5 p-4';

        box.appendChild(
            makeText(
                'p',
                'Unable to complete AI assistance',
                'font-button text-sm font-semibold text-error'
            )
        );

        box.appendChild(
            makeText(
                'p',
                message,
                'mt-1 text-xs leading-relaxed text-slate-500'
            )
        );

        resultContainer.appendChild(box);
        resultContainer.classList.remove('hidden');

        statusBadge.textContent = 'Error';
        statusBadge.classList.remove('hidden');
    };


    const renderExtract = (suggestion) => {

        resultContainer.appendChild(
            makeRow(
                'Full Name',
                suggestion.full_name
            )
        );

        resultContainer.appendChild(
            makeRow(
                'Organization',
                suggestion.organization
            )
        );

        resultContainer.appendChild(
            makeRow(
                'Visitor Type',
                suggestion.visitor_type
                    ? suggestion.visitor_type
                        .replaceAll('_', ' ')
                    : ''
            )
        );

        resultContainer.appendChild(
            makeRow(
                'Purpose',
                suggestion.purpose
            )
        );


        if (suggestion.contact_number) {

            resultContainer.appendChild(
                makeRow(
                    'Contact Number',
                    suggestion.contact_number
                )
            );

        }


        if (suggestion.email) {

            resultContainer.appendChild(
                makeRow(
                    'Visitor Email',
                    suggestion.email
                )
            );

        }


        if (suggestion.confidence) {

            const confidenceBox =
                document.createElement('div');

            confidenceBox.className =
                'rounded-xl border border-accent/20 bg-accent/5 p-4';

            confidenceBox.appendChild(
                makeText(
                    'p',
                    'Confidence',
                    'text-xs font-medium text-slate-400'
                )
            );

            confidenceBox.appendChild(
                makeText(
                    'p',
                    suggestion.confidence,
                    'mt-1 font-button text-sm font-semibold capitalize text-primary'
                )
            );

            resultContainer.appendChild(confidenceBox);

        }


        if (suggestion.reasoning) {

            const reasoning =
                document.createElement('div');

            reasoning.className =
                'rounded-xl border border-warning/20 bg-warning/5 p-4';

            reasoning.appendChild(
                makeText(
                    'p',
                    'Assistant Note',
                    'font-button text-xs font-semibold text-amber-700'
                )
            );

            reasoning.appendChild(
                makeText(
                    'p',
                    suggestion.reasoning,
                    'mt-1 text-xs leading-relaxed text-slate-500'
                )
            );

            resultContainer.appendChild(reasoning);

        }


        if (
            Array.isArray(suggestion.missing_fields) &&
            suggestion.missing_fields.length > 0
        ) {

            const missingBox =
                document.createElement('div');

            missingBox.className =
                'rounded-xl border border-border p-4';

            missingBox.appendChild(
                makeText(
                    'p',
                    'Missing Information',
                    'mb-2 font-button text-xs font-semibold text-primary'
                )
            );

            const chips =
                document.createElement('div');

            chips.className =
                'flex flex-wrap gap-2';

            suggestion.missing_fields.forEach((field) => {

                const chip =
                    document.createElement('span');

                chip.className =
                    'rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-medium text-slate-600';

                chip.textContent =
                    String(field).replaceAll('_', ' ');

                chips.appendChild(chip);

            });

            missingBox.appendChild(chips);
            resultContainer.appendChild(missingBox);

        }

        applyWrap.classList.remove('hidden');

    };


    const renderSummary = (suggestion) => {

        const box = document.createElement('div');

        box.className =
            'rounded-xl border border-accent/20 bg-accent/5 p-5';

        box.appendChild(
            makeText(
                'p',
                'Visit Summary',
                'font-button text-xs font-semibold uppercase tracking-wide text-primary'
            )
        );

        box.appendChild(
            makeText(
                'p',
                suggestion.summary || 'No summary returned.',
                'mt-3 text-sm leading-relaxed text-slate-600'
            )
        );

        resultContainer.appendChild(box);

    };


    const renderAppointmentCheck = (suggestion) => {

        const overall =
            document.createElement('div');

        overall.className =
            'rounded-xl border border-accent/20 bg-accent/5 p-4';

        overall.appendChild(
            makeText(
                'p',
                'Appointment Review',
                'font-button text-xs font-semibold uppercase tracking-wide text-primary'
            )
        );

        overall.appendChild(
            makeText(
                'p',
                suggestion.overall ||
                    'No review summary returned.',
                'mt-2 text-sm leading-relaxed text-slate-600'
            )
        );

        resultContainer.appendChild(overall);


        if (
            Array.isArray(suggestion.issues) &&
            suggestion.issues.length > 0
        ) {

            const issues =
                document.createElement('div');

            issues.className =
                'rounded-xl border border-warning/20 bg-warning/5 p-4';

            issues.appendChild(
                makeText(
                    'p',
                    'Items to Verify',
                    'font-button text-xs font-semibold text-amber-700'
                )
            );

            const list =
                document.createElement('ul');

            list.className =
                'mt-2 space-y-2 text-xs text-slate-600';

            suggestion.issues.forEach((issue) => {

                const item =
                    document.createElement('li');

                item.className =
                    'flex items-start gap-2';

                const dot =
                    document.createElement('span');

                dot.className =
                    'mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-warning';

                item.appendChild(dot);

                item.appendChild(
                    makeText(
                        'span',
                        issue
                    )
                );

                list.appendChild(item);

            });

            issues.appendChild(list);
            resultContainer.appendChild(issues);

        }

    };



    const renderTriage = (suggestion) => {

        const classification =
            document.createElement('div');

        classification.className =
            'grid gap-3 sm:grid-cols-2';

        classification.appendChild(
            makeRow(
                'Visitor Type',
                suggestion.visitor_type
                    ? String(suggestion.visitor_type)
                        .replaceAll('_', ' ')
                    : ''
            )
        );

        classification.appendChild(
            makeRow(
                'Purpose Category',
                suggestion.purpose_category
                    ? String(suggestion.purpose_category)
                        .replaceAll('_', ' ')
                    : ''
            )
        );

        classification.appendChild(
            makeRow(
                'Suggested Routing',
                suggestion.suggested_department
                    ? String(suggestion.suggested_department)
                        .replaceAll('_', ' ')
                    : ''
            )
        );

        classification.appendChild(
            makeRow(
                'Suggested Host',
                suggestion.suggested_host || ''
            )
        );

        resultContainer.appendChild(classification);


        const summary =
            document.createElement('div');

        summary.className =
            'rounded-xl border border-accent/20 bg-accent/5 p-4';

        summary.appendChild(
            makeText(
                'p',
                'Visitor Summary',
                'font-button text-xs font-semibold uppercase tracking-wide text-primary'
            )
        );

        summary.appendChild(
            makeText(
                'p',
                suggestion.summary ||
                    'No summary returned.',
                'mt-2 text-sm leading-relaxed text-slate-600'
            )
        );

        resultContainer.appendChild(summary);


        const appointment =
            document.createElement('div');

        appointment.className =
            'rounded-xl border border-border bg-background/60 p-4';

        appointment.appendChild(
            makeText(
                'p',
                'Appointment Verification',
                'font-button text-xs font-semibold uppercase tracking-wide text-primary'
            )
        );

        appointment.appendChild(
            makeText(
                'p',
                String(
                    suggestion.appointment_match ||
                    'not_checked'
                )
                    .replaceAll('_', ' '),
                'mt-2 text-sm font-semibold capitalize text-primary'
            )
        );

        if (suggestion.appointment) {

            const details =
                document.createElement('div');

            details.className =
                'mt-3 grid gap-2 text-xs text-slate-500 sm:grid-cols-2';

            [
                [
                    'Appointment',
                    `#${suggestion.appointment.id}`
                ],
                [
                    'Date',
                    suggestion.appointment.date
                ],
                [
                    'Time',
                    [
                        suggestion.appointment.start_time,
                        suggestion.appointment.end_time
                    ]
                        .filter(Boolean)
                        .join(' - ')
                ],
                [
                    'Facility',
                    suggestion.appointment.facility_name ||
                        'Not assigned'
                ]
            ].forEach(([label, value]) => {

                const item =
                    document.createElement('div');

                item.appendChild(
                    makeText(
                        'span',
                        `${label}: `,
                        'font-medium text-slate-400'
                    )
                );

                item.appendChild(
                    makeText(
                        'span',
                        value || 'Not provided',
                        'text-slate-600'
                    )
                );

                details.appendChild(item);
            });

            appointment.appendChild(details);
        }

        resultContainer.appendChild(appointment);


        const renderListBox = (
            title,
            items,
            warning = false
        ) => {

            if (
                !Array.isArray(items) ||
                items.length === 0
            ) {
                return;
            }

            const box =
                document.createElement('div');

            box.className =
                warning
                    ? 'rounded-xl border border-warning/20 bg-warning/5 p-4'
                    : 'rounded-xl border border-border p-4';

            box.appendChild(
                makeText(
                    'p',
                    title,
                    warning
                        ? 'font-button text-xs font-semibold text-amber-700'
                        : 'font-button text-xs font-semibold text-primary'
                )
            );

            const list =
                document.createElement('ul');

            list.className =
                'mt-2 space-y-1.5 text-xs text-slate-600';

            items.forEach((item) => {

                const row =
                    document.createElement('li');

                row.className =
                    'flex items-start gap-2';

                const dot =
                    document.createElement('span');

                dot.className =
                    warning
                        ? 'mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-warning'
                        : 'mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-secondary';

                row.appendChild(dot);

                row.appendChild(
                    makeText(
                        'span',
                        String(item)
                            .replaceAll('_', ' ')
                    )
                );

                list.appendChild(row);
            });

            box.appendChild(list);
            resultContainer.appendChild(box);
        };


        renderListBox(
            'Missing Information',
            suggestion.missing_information
        );

        renderListBox(
            'Appointment Details to Verify',
            suggestion.mismatches,
            true
        );


        const action =
            document.createElement('div');

        action.className =
            'rounded-xl border border-primary/15 bg-primary/5 p-4';

        action.appendChild(
            makeText(
                'p',
                'Suggested Staff Action',
                'font-button text-xs font-semibold text-primary'
            )
        );

        action.appendChild(
            makeText(
                'p',
                suggestion.suggested_action ||
                    'Continue manual visitor review.',
                'mt-2 text-xs leading-relaxed text-slate-600'
            )
        );

        resultContainer.appendChild(action);


        if (suggestion.security_notice) {

            const security =
                document.createElement('div');

            security.className =
                'rounded-xl border border-error/20 bg-error/5 p-4';

            security.appendChild(
                makeText(
                    'p',
                    'Security Notice',
                    'font-button text-xs font-semibold text-error'
                )
            );

            security.appendChild(
                makeText(
                    'p',
                    suggestion.security_notice,
                    'mt-2 text-xs leading-relaxed text-slate-600'
                )
            );

            resultContainer.appendChild(security);
        }


        if (suggestion.confidence) {

            resultContainer.appendChild(
                makeRow(
                    'AI Confidence',
                    suggestion.confidence
                )
            );
        }


        if (
            currentMode === 'triage'
        ) {
            applyWrap.classList.remove(
                'hidden'
            );
        }
    };
    const renderSuggestion = (mode, suggestion) => {

        setLoading(false);

        clearNode(resultContainer);

        currentSuggestion = suggestion;
        currentMode = mode;

        statusBadge.textContent =
            mode === 'triage'
                ? (
                    suggestion.source === 'ai'
                        ? 'AI Triage'
                        : 'Safe Fallback'
                )
                : mode === 'extract'
                    ? 'Extracted'
                    : mode === 'summary'
                        ? 'Summary'
                        : 'Reviewed';

        statusBadge.classList.remove('hidden');


        if (mode === 'triage') {

            renderTriage(suggestion);

        } else if (mode === 'extract' || mode === 'classify') {

            renderExtract(suggestion);

        } else if (mode === 'summary') {

            renderSummary(suggestion);

        } else if (mode === 'appointment_check') {

            renderAppointmentCheck(suggestion);

        }


        resultContainer.classList.remove('hidden');

    };


    const requestAssistance = async (mode) => {

        const text = input.value.trim();
        const context = getFormContext();

        if (
            !text &&
            Object.keys(context).length === 0
        ) {

            showError(
                'Enter visitor information or complete part of the visitor registration form first.'
            );

            input.focus();

            return;

        }

        setLoading(true);

        statusBadge.classList.add('hidden');

        try {

            const response = await fetch(endpoint, {

                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },

                credentials: 'same-origin',

                body: JSON.stringify({
                    mode,
                    text,
                    context,
                }),

            });


            const data = await response.json();


            if (!response.ok) {

                const validationMessage =
                    data?.message ||
                    Object.values(data?.errors || {})
                        .flat()
                        .join(' ') ||
                    'The request could not be completed.';

                throw new Error(validationMessage);

            }


            if (!data?.suggestion) {

                throw new Error(
                    'The assistant returned an unexpected response.'
                );

            }


            renderSuggestion(
                data.mode || mode,
                data.suggestion
            );

        } catch (error) {

            showError(
                error?.message ||
                'An unexpected error occurred.'
            );

        }

    };


    modeButtons.forEach((button) => {

        button.addEventListener('click', () => {

            requestAssistance(
                button.dataset.aiMode
            );

        });

    });


    applyButton.addEventListener('click', () => {

        if (
            !currentSuggestion ||
            !['triage', 'extract', 'classify'].includes(currentMode)
        ) {
            return;
        }

        const mappings = {
            full_name: 'full_name',
            contact_number: 'contact_number',
            email: 'email',
            organization: 'organization',
            visitor_type: 'visitor_type',
            host_name: 'host_name',
            purpose: 'purpose',
        };


        Object.entries(mappings).forEach(
            ([suggestionKey, fieldId]) => {

                const suggestedValue =
                    currentSuggestion[suggestionKey];

                if (
                    suggestedValue === undefined ||
                    suggestedValue === null ||
                    String(suggestedValue).trim() === ''
                ) {
                    return;
                }

                const field =
                    document.getElementById(fieldId);

                if (!field) {
                    return;
                }


                if (field.tagName === 'SELECT') {

                    const exists =
                        Array.from(field.options)
                            .some(
                                (option) =>
                                    option.value === suggestedValue
                            );

                    if (exists) {
                        field.value = suggestedValue;
                    }

                } else {

                    field.value = suggestedValue;

                }


                field.dispatchEvent(
                    new Event(
                        'change',
                        { bubbles: true }
                    )
                );

            }
        );



        /*
         * Only an exact PostgreSQL-backed match can be linked
         * automatically after the staff member clicks Apply.
         * Partial AI/database suggestions remain unlinked.
         */
        const appointmentField =
            document.getElementById(
                'appointment_id'
            );

        const walkInField =
            document.getElementById(
                'is_walk_in'
            );

        if (
            currentMode === 'triage'
            &&
            currentSuggestion.appointment_match ===
                'matched'
            &&
            currentSuggestion.appointment?.id
            &&
            appointmentField
        ) {
            appointmentField.value =
                String(
                    currentSuggestion
                        .appointment
                        .id
                );

            if (walkInField) {
                walkInField.checked =
                    false;

                walkInField.dispatchEvent(
                    new Event(
                        'change',
                        {
                            bubbles: true
                        }
                    )
                );
            }
        } else if (appointmentField) {
            appointmentField.value = '';
        }
        statusBadge.textContent = 'Applied';


        const visitorForm =
            document
                .getElementById('full_name')
                ?.closest('form');

        visitorForm?.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        });

        document
            .getElementById('full_name')
            ?.focus();

    });

});


// ==========================================
// AI ASSISTANT COLLAPSE
// ==========================================

document.addEventListener('DOMContentLoaded', () => {

    const toggle =
        document.querySelector('[data-ai-panel-toggle]');

    const panel =
        document.querySelector('[data-ai-panel-body]');

    const toggleText =
        document.querySelector('[data-ai-toggle-text]');

    const toggleIcon =
        document.querySelector('[data-ai-toggle-icon]');

    if (!toggle || !panel) {
        return;
    }

    const updatePanel = (open) => {

        panel.classList.toggle('hidden', !open);

        toggle.setAttribute(
            'aria-expanded',
            String(open)
        );

        if (toggleText) {
            toggleText.textContent =
                open
                    ? 'Close Assistant'
                    : 'Open Assistant';
        }

        if (toggleIcon) {
            toggleIcon.classList.toggle(
                'rotate-180',
                open
            );
        }

    };

    updatePanel(false);

    toggle.addEventListener('click', () => {

        const open =
            toggle.getAttribute('aria-expanded') !== 'true';

        updatePanel(open);

    });

});


// =====================================================
// FAMS FORM SUBMIT FEEDBACK
// =====================================================

document.addEventListener('DOMContentLoaded', () => {

    const forms =
        document.querySelectorAll(
            '[data-submit-loading]'
        );


    forms.forEach((form) => {

        let submitting = false;


        form.addEventListener('submit', (event) => {

            if (submitting) {

                event.preventDefault();
                return;
            }


            submitting = true;


            const buttons =
                form.querySelectorAll(
                    'button[type="submit"]'
                );


            buttons.forEach((button) => {

                button.dataset.originalHtml =
                    button.innerHTML;

                button.disabled = true;

                button.setAttribute(
                    'aria-busy',
                    'true'
                );

                button.classList.add(
                    'cursor-wait',
                    'opacity-75'
                );


                const loadingText =
                    form.dataset.loadingText
                    || 'Processing...';


                button.innerHTML = `
                    <span
                        class="fams-submit-spinner"
                        aria-hidden="true">
                    </span>

                    <span>
                        ${loadingText}
                    </span>
                `;
            });
        });


        // Restore buttons if browser returns through
        // back/forward cache.
        window.addEventListener(
            'pageshow',
            (event) => {

                if (!event.persisted) {
                    return;
                }


                submitting = false;


                const buttons =
                    form.querySelectorAll(
                        'button[type="submit"]'
                    );


                buttons.forEach((button) => {

                    if (
                        button.dataset.originalHtml
                    ) {
                        button.innerHTML =
                            button.dataset.originalHtml;
                    }

                    button.disabled = false;

                    button.removeAttribute(
                        'aria-busy'
                    );

                    button.classList.remove(
                        'cursor-wait',
                        'opacity-75'
                    );
                });
            }
        );
    });
});

// =====================================================
// FAMS DASHBOARD INTERACTIVE ANALYTICS
// =====================================================

(() => {

    const svgNamespace =
        'http://www.w3.org/2000/svg';

    const createSvgElement =
        (name) =>
            document.createElementNS(
                svgNamespace,
                name
            );


    const renderLineChart =
        (panel) => {

            const chart =
                panel.querySelector(
                    '[data-dashboard-line-chart]'
                );

            if (!chart) {
                return;
            }

            const svg =
                chart.querySelector(
                    '[data-dashboard-chart-svg]'
                );

                        const dashboardEmptyState =
                chart.querySelector(
                    '[data-dashboard-chart-empty]'
                );

const dataElement =
                chart.querySelector(
                    '[data-dashboard-chart-data]'
                );

            if (!svg || !dataElement) {
                return;
            }

            let payload;

            try {
                payload =
                    JSON.parse(
                        dataElement.textContent
                    );
            } catch (error) {
                console.warn(
                    'Dashboard analytics payload could not be parsed.',
                    error
                );

                return;
            }

            const points =
                Array.isArray(
                    payload.points
                )
                    ? payload.points
                    : [];

            const series =
                Array.isArray(
                    payload.series
                )
                    ? payload.series
                    : [];

            svg.replaceChildren();

            if (points.length === 0 || series.length === 0) {
                return;
            }

            const width = 720;
            const height = 260;

            const left = 48;
            const right = 18;
            const top = 18;
            const bottom = 38;

            const plotWidth =
                width - left - right;

            const plotHeight =
                height - top - bottom;

            const values = [];

            points.forEach((point) => {

                series.forEach((item) => {

                    const value =
                        Number(
                            point[item.key]
                            ?? 0
                        );

                    if (Number.isFinite(value)) {
                        values.push(value);
                    }
                });
            });

            const hasActivity =
                values.some(
                    (value) =>
                        value > 0
                );


            svg.classList.toggle(
                'hidden',
                !hasActivity
            );


            if (dashboardEmptyState) {
                dashboardEmptyState.classList.toggle(
                    'hidden',
                    hasActivity
                );

                dashboardEmptyState.classList.toggle(
                    'flex',
                    !hasActivity
                );
            }


            if (!hasActivity) {
                return;
            }


            const maxValue =
                Math.max(
                    ...values
                );

            const tickCount =
                Math.min(
                    4,
                    Math.max(
                        1,
                        maxValue
                    )
                );

            const tickStep =
                Math.max(
                    1,
                    Math.ceil(
                        maxValue /
                        tickCount
                    )
                );

            const axisMax =
                tickStep *
                tickCount;


            const xFor =
                (index) => {

                    if (points.length === 1) {
                        return left + plotWidth / 2;
                    }

                    return (
                        left +
                        (
                            index /
                            (points.length - 1)
                        ) *
                        plotWidth
                    );
                };


            const yFor =
                (value) =>
                    top +
                    plotHeight -
                    (
                        value / axisMax
                    ) *
                    plotHeight;


            for (
                let gridIndex = 0;
                gridIndex <= tickCount;
                gridIndex++
            ) {
                const ratio =
                    gridIndex / tickCount;

                const y =
                    top +
                    ratio *
                    plotHeight;

                const line =
                    createSvgElement(
                        'line'
                    );

                line.setAttribute(
                    'x1',
                    String(left)
                );

                line.setAttribute(
                    'x2',
                    String(width - right)
                );

                line.setAttribute(
                    'y1',
                    String(y)
                );

                line.setAttribute(
                    'y2',
                    String(y)
                );

                line.setAttribute(
                    'class',
                    'stroke-slate-200 dark:stroke-slate-700'
                );

                line.setAttribute(
                    'stroke-width',
                    '1'
                );

                svg.appendChild(line);


                const label =
                    createSvgElement(
                        'text'
                    );

                label.setAttribute(
                    'x',
                    String(left - 8)
                );

                label.setAttribute(
                    'y',
                    String(y + 4)
                );

                label.setAttribute(
                    'text-anchor',
                    'end'
                );

                label.setAttribute(
                    'class',
                    'fill-slate-400 text-[10px]'
                );

                label.textContent =
                    String(
                        Math.round(
                            axisMax * (1 - ratio)
                        )
                    );

                svg.appendChild(label);
            }


            const labelInterval =
                points.length <= 7
                    ? 1
                    : 5;


            points.forEach(
                (point, index) => {

                    const isLast =
                        index ===
                        points.length - 1;

                    if (index % labelInterval !== 0 && !isLast) {
                        return;
                    }

                    const text =
                        createSvgElement(
                            'text'
                        );

                    text.setAttribute(
                        'x',
                        String(
                            xFor(index)
                        )
                    );

                    text.setAttribute(
                        'y',
                        String(
                            height - 12
                        )
                    );

                    text.setAttribute(
                        'text-anchor',
                        'middle'
                    );

                    text.setAttribute(
                        'class',
                        'fill-slate-400 text-[10px]'
                    );

                    text.textContent =
                        point.label
                        || '';

                    svg.appendChild(text);
                }
            );


            series.forEach((item) => {

                const group =
                    createSvgElement(
                        'g'
                    );

                group.setAttribute(
                    'class',
                    item.class_name
                    || 'text-primary'
                );


                const coordinates =
                    points.map(
                        (point, index) => {

                            const value =
                                Math.max(
                                    0,
                                    Number(
                                        point[item.key]
                                        ?? 0
                                    )
                                );

                            return {
                                x:
                                    xFor(index),

                                y:
                                    yFor(value),

                                value,

                                point,
                            };
                        }
                    );


                const path =
                    createSvgElement(
                        'path'
                    );

                path.setAttribute(
                    'd',
                    coordinates
                        .map(
                            (
                                coordinate,
                                index
                            ) =>
                                `${
                                    index === 0
                                        ? 'M'
                                        : 'L'
                                } ${coordinate.x} ${coordinate.y}`
                        )
                        .join(' ')
                );

                path.setAttribute(
                    'fill',
                    'none'
                );

                path.setAttribute(
                    'stroke',
                    'currentColor'
                );

                path.setAttribute(
                    'stroke-width',
                    '3'
                );

                path.setAttribute(
                    'stroke-linecap',
                    'round'
                );

                path.setAttribute(
                    'stroke-linejoin',
                    'round'
                );

                group.appendChild(path);


                coordinates.forEach(
                    (coordinate) => {

                        const circle =
                            createSvgElement(
                                'circle'
                            );

                        circle.setAttribute(
                            'cx',
                            String(
                                coordinate.x
                            )
                        );

                        circle.setAttribute(
                            'cy',
                            String(
                                coordinate.y
                            )
                        );

                        circle.setAttribute(
                            'r',
                            points.length <= 7
                                ? '4'
                                : '2.75'
                        );

                        circle.setAttribute(
                            'fill',
                            'currentColor'
                        );


                        const title =
                            createSvgElement(
                                'title'
                            );

                        title.textContent =
                            `${item.label}: ${coordinate.value} · ${coordinate.point.date}`;

                        circle.appendChild(title);

                        group.appendChild(circle);
                    }
                );

                svg.appendChild(group);
            });
        };


    const initializeDashboardAnalytics =
        () => {

            const dashboardRoot =
                document.querySelector(
                    '[data-dashboard-realtime-root]'
                );

            if (!dashboardRoot) {
                return;
            }

            const analyticsRoot =
                dashboardRoot.querySelector(
                    '[data-dashboard-analytics-root]'
                );

            if (!analyticsRoot) {
                return;
            }

            const buttons =
                Array.from(
                    analyticsRoot.querySelectorAll(
                        '[data-dashboard-analytics-range]'
                    )
                );

            const panels =
                Array.from(
                    analyticsRoot.querySelectorAll(
                        '[data-dashboard-analytics-panel]'
                    )
                );

            const liveRegion =
                analyticsRoot.querySelector(
                    '[data-dashboard-analytics-live]'
                );

            if (buttons.length === 0 || panels.length === 0) {
                return;
            }

            const availableRanges =
                buttons.map(
                    (button) =>
                        button.dataset
                            .dashboardAnalyticsRange
                );

            const defaultRange =
                analyticsRoot.dataset
                    .dashboardAnalyticsDefaultRange
                || '7';

            let selectedRange =
                dashboardRoot.dataset
                    .dashboardAnalyticsSelectedRange
                || defaultRange;

            if (!availableRanges.includes(selectedRange)) {
                selectedRange =
                    defaultRange;
            }


            const applyRange =
                (range) => {

                    if (!availableRanges.includes(range)) {
                        return;
                    }

                    dashboardRoot.dataset
                        .dashboardAnalyticsSelectedRange =
                            range;


                    buttons.forEach(
                        (button) => {

                            const active =
                                button.dataset
                                    .dashboardAnalyticsRange
                                === range;

                            button.setAttribute(
                                'aria-pressed',
                                String(active)
                            );

                            button.classList.toggle(
                                'bg-primary',
                                active
                            );

                            button.classList.toggle(
                                'text-white',
                                active
                            );

                            button.classList.toggle(
                                'shadow-sm',
                                active
                            );

                            button.classList.toggle(
                                'text-slate-500',
                                !active
                            );
                        }
                    );


                    panels.forEach(
                        (panel) => {

                            const active =
                                panel.dataset
                                    .dashboardAnalyticsPanel
                                === range;

                            panel.classList.toggle(
                                'hidden',
                                !active
                            );

                            panel.setAttribute(
                                'aria-hidden',
                                String(!active)
                            );

                            if (active) {
                                renderLineChart(
                                    panel
                                );
                            }
                        }
                    );


                    if (liveRegion) {
                        liveRegion.textContent =
                            `Showing analytics for the last ${range} days.`;
                    }
                };


            buttons.forEach(
                (button) => {

                    button.addEventListener(
                        'click',
                        () => {

                            applyRange(
                                button.dataset
                                    .dashboardAnalyticsRange
                            );
                        }
                    );
                }
            );


            applyRange(
                selectedRange
            );
        };


    window.famsDashboardAnalyticsRefresh =
        initializeDashboardAnalytics;


    document.addEventListener(
        'DOMContentLoaded',
        initializeDashboardAnalytics
    );

})();
