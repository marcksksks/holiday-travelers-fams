import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    // ==========================================
    // DESKTOP SIDEBAR HIDE / SHOW
    // ==========================================

    const sidebar = document.querySelector('[data-sidebar]');
    const sidebarToggles = document.querySelectorAll('[data-sidebar-toggle]');

    let sidebarHidden = false;

    const updateDesktopSidebar = () => {

        if (!sidebar) return;

        if (sidebarHidden) {

            // Completely hide the sidebar on desktop
            sidebar.classList.add(
                'md:w-0',
                'md:opacity-0',
                'md:overflow-hidden',
                'md:pointer-events-none'
            );

        } else {

            // Restore the sidebar
            sidebar.classList.remove(
                'md:w-0',
                'md:opacity-0',
                'md:overflow-hidden',
                'md:pointer-events-none'
            );

        }

        sidebarToggles.forEach((button) => {

            button.setAttribute(
                'aria-expanded',
                String(!sidebarHidden)
            );

            button.setAttribute(
                'aria-label',
                sidebarHidden ? 'Show sidebar' : 'Hide sidebar'
            );

            button.setAttribute(
                'title',
                sidebarHidden ? 'Show sidebar' : 'Hide sidebar'
            );

        });

    };

    sidebarToggles.forEach((button) => {

        button.addEventListener('click', () => {

            sidebarHidden = !sidebarHidden;

            updateDesktopSidebar();

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


// ==========================================
// NOTIFICATION DROPDOWN
// ==========================================

document.addEventListener('DOMContentLoaded', () => {

    const notificationButton =
        document.querySelector('[data-notification-button]');

    const notificationMenu =
        document.querySelector('[data-notification-menu]');

    if (!notificationButton || !notificationMenu) {
        return;
    }

    const closeNotifications = () => {

        notificationMenu.classList.add('hidden');

        notificationButton.setAttribute(
            'aria-expanded',
            'false'
        );

    };

    notificationButton.addEventListener('click', (event) => {

        event.stopPropagation();

        const willOpen =
            notificationMenu.classList.contains('hidden');

        notificationMenu.classList.toggle('hidden');

        notificationButton.setAttribute(
            'aria-expanded',
            String(willOpen)
        );

    });


    notificationMenu.addEventListener('click', (event) => {
        event.stopPropagation();
    });


    document.addEventListener('click', () => {
        closeNotifications();
    });


    document.addEventListener('keydown', (event) => {

        if (event.key === 'Escape') {
            closeNotifications();
        }

    });

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
            'organization',
            'visitor_type',
            'host_email',
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


    const renderSuggestion = (mode, suggestion) => {

        setLoading(false);

        clearNode(resultContainer);

        currentSuggestion = suggestion;
        currentMode = mode;

        statusBadge.textContent =
            mode === 'extract'
                ? 'Extracted'
                : mode === 'summary'
                    ? 'Summary'
                    : 'Reviewed';

        statusBadge.classList.remove('hidden');


        if (mode === 'extract' || mode === 'classify') {

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
            !['extract', 'classify'].includes(currentMode)
        ) {
            return;
        }

        const mappings = {
            full_name: 'full_name',
            organization: 'organization',
            visitor_type: 'visitor_type',
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
