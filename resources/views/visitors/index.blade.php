@extends('layouts.app')

@section('title', 'Visitor Desk')

@section('content')

<div class="space-y-4">

    {{-- =====================================================
         VISITOR DESK HEADER
    ====================================================== --}}
    <x-page-header
        eyebrow="Reception Operations"
        title="Visitor Desk"
        badge="Front Desk Operations"
        description="Manage arrivals, host coordination, active visits, departures, and visitor records from one workspace.">

        <x-slot:actions>

            @can('useAiAssist')

                <button
                    type="button"
                    data-ai-assistant-open
                    class="btn-outline inline-flex items-center gap-2">

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 3l1.1 3.3L16 7.4l-2.9 1.1L12 12l-1.1-3.5L8 7.4l2.9-1.1L12 3zM6 14l1 3 3 1-3 1-1 3-1-3-3-1 3-1 1-3z" />

                    </svg>

                    AI Assistant

                </button>

            @endcan


            @can('operateVisitorDesk')

                <a
                    href="#register-visitor"
                    data-visitor-register-open
                    class="btn-primary inline-flex items-center gap-2">

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8zM19 8v6M22 11h-6" />

                    </svg>

                    Register Visitor

                </a>

            @endcan

        </x-slot:actions>

    </x-page-header>


    {{-- =====================================================
         VISITOR OPERATIONS OVERVIEW
    ====================================================== --}}
    <section class="space-y-4">

        <x-section-header
            eyebrow="Overview"
            title="Visitor Status"
            description="A live operational snapshot of arrivals, host coordination, active visits, and completed visitor records." />


        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

            <x-metric-card
                label="Expected"
                :value="number_format($visitorCounts['expected'])"
                :href="route('visitors.index', array_filter([
                    'status' => 'expected',
                    'q' => request('q'),
                    'visitor_type' => request('visitor_type'),
                ]))"
                helper="Visitors registered and currently awaiting arrival."
                tone="accent">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Awaiting Host"
                :value="number_format($visitorCounts['awaiting_host'])"
                :href="route('visitors.index', array_filter([
                    'status' => 'awaiting_host',
                    'q' => request('q'),
                    'visitor_type' => request('visitor_type'),
                ]))"
                helper="Arrivals currently requiring host coordination."
                tone="warning">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="On Site"
                :value="number_format($visitorCounts['checked_in'])"
                :href="route('visitors.index', array_filter([
                    'status' => 'checked_in',
                    'q' => request('q'),
                    'visitor_type' => request('visitor_type'),
                ]))"
                helper="Visitors currently checked in and present on site."
                tone="success">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Completed"
                :value="number_format($visitorCounts['completed'])"
                :href="route('visitors.index', array_filter([
                    'status' => 'completed',
                    'q' => request('q'),
                    'visitor_type' => request('visitor_type'),
                ]))"
                helper="Visitor records with a completed visit lifecycle."
                tone="primary">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>

        </div>


        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-border bg-card px-4 py-3">

            <div>

                <p class="text-xs font-semibold text-primary">
                    Visitor Records
                </p>

                <p class="mt-0.5 text-[11px] text-slate-500">

                    {{ number_format($visitorCounts['total']) }}

                    total visitor
                    {{ $visitorCounts['total'] === 1 ? 'record' : 'records' }}

                </p>

            </div>


            @if ($visitorCounts['declined'] > 0)

                <a
                    href="{{ route('visitors.index', ['status' => 'declined']) }}"
                    class="text-xs font-semibold text-error transition hover:text-primary">

                    {{ number_format($visitorCounts['declined']) }}
                    declined →

                </a>

            @endif

        </div>

    </section>

    {{-- =====================================================
         AI-ASSISTED VISITOR TRAFFIC INTELLIGENCE
    ====================================================== --}}
    @include('visitors._traffic-forecast')

    {{-- AI Visitor Assistant Modal --}}
    @include('visitors._ai-assistant-modal')

    {{-- =====================================================
         VISITOR DESK WORKSPACE
    ====================================================== --}}
    <div class="min-w-0">

        @include('visitors._register-modal')

        @include('visitors._check-in-modal')

        @include('visitors._check-out-modal')

        @include('visitors._decline-modal')

        {{-- Visitor Records --}}
        <div class="min-w-0 space-y-4">

            <x-section-header
                eyebrow="Visitor Queue"
                title="Visitor Activity"
                description="Monitor arrivals, host coordination, active visits, and completed records.">

                <x-slot:actions>

                    <div class="text-left sm:text-right">

                        <p class="font-heading text-lg font-bold text-primary">
                            {{ number_format($visitors->total()) }}
                        </p>

                        <p class="text-[9px] uppercase tracking-wide text-slate-400">
                            {{ $visitors->total() === 1 ? 'result' : 'results' }}
                        </p>

                    </div>

                </x-slot:actions>

            </x-section-header>


            {{-- =====================================================
                 VISITOR DESK FILTER TOOLBAR
            ====================================================== --}}
            <form
                method="GET"
                action="{{ route('visitors.index') }}"
                class="card p-3">

                <div class="flex flex-col gap-2 lg:flex-row lg:items-center">

                    {{-- Search --}}
                    <div class="relative min-w-0 flex-1">

                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">

                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 110-15 7.5 7.5 0 010 15z" />

                            </svg>

                        </div>


                        <input
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Search visitor, company, host, email or badge..."
                            class="input pl-9">

                    </div>


                    {{-- Status --}}
                    <div class="lg:w-44">

                        <select
                            name="status"
                            class="input">

                            <option value="">
                                All statuses
                            </option>

                            <option
                                value="expected"
                                @selected(request('status') === 'expected')>

                                Expected

                            </option>

                            <option
                                value="awaiting_host"
                                @selected(request('status') === 'awaiting_host')>

                                Awaiting Host

                            </option>

                            <option
                                value="checked_in"
                                @selected(request('status') === 'checked_in')>

                                On Site

                            </option>

                            <option
                                value="completed"
                                @selected(request('status') === 'completed')>

                                Completed

                            </option>

                            <option
                                value="declined"
                                @selected(request('status') === 'declined')>

                                Declined

                            </option>

                            <option
                                value="cancelled"
                                @selected(request('status') === 'cancelled')>

                                Cancelled

                            </option>


                            <option
                                value="no_show"
                                @selected(request('status') === 'no_show')>

                                No Show

                            </option>

                        </select>

                    </div>


                    {{-- Visitor type --}}
                    <div class="lg:w-48">

                        <select
                            name="visitor_type"
                            class="input">

                            <option value="">
                                All visitor types
                            </option>

                            @foreach ($allowedVisitorTypes as $type)

                                <option
                                    value="{{ $type }}"
                                    @selected(request('visitor_type') === $type)>

                                    {{ str($type)->headline() }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="flex gap-2">

                        <button
                            type="submit"
                            class="btn-primary justify-center">

                            Apply

                        </button>


                        @if (
                            request()->filled('q')
                            ||
                            request()->filled('status')
                            ||
                            request()->filled('visitor_type')
                        )

                            <a
                                href="{{ route('visitors.index') }}"
                                class="btn-outline justify-center">

                                Clear

                            </a>

                        @endif

                    </div>

                </div>

            </form>

            {{-- Modern Visitor Activity --}}
            @include(
                'visitors._activity-table',
                [
                    'visitors' => $visitors,
                ]
            )

            {{-- Pagination --}}
            @if ($visitors->hasPages())

                <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
                    {{ $visitors->withQueryString()->links() }}
                </div>

            @endif

        </div>

    </div>

</div>

<script>
document.addEventListener(
    'DOMContentLoaded',
    () => {

        const modal =
            document.querySelector(
                '[data-visitor-register-modal]'
            );

        if (! modal) {
            return;
        }


        const panel =
            modal.querySelector(
                '[data-visitor-register-panel]'
            );

        const backdrop =
            modal.querySelector(
                '[data-visitor-register-backdrop]'
            );

        const closeButtons =
            modal.querySelectorAll(
                '[data-visitor-register-close]'
            );

        const openTriggers =
            document.querySelectorAll(
                'a[href="#register-visitor"], [data-visitor-register-open]'
            );


        let previouslyFocused = null;


        const openModal = () => {

            previouslyFocused =
                document.activeElement;

            modal.classList.remove(
                'hidden'
            );

            document.body.classList.add(
                'overflow-hidden'
            );


            requestAnimationFrame(
                () => {

                    document
                        .getElementById('full_name')
                        ?.focus();

                }
            );

        };


        const closeModal = () => {

            modal.classList.add(
                'hidden'
            );

            document.body.classList.remove(
                'overflow-hidden'
            );

            previouslyFocused?.focus?.();

        };


        openTriggers.forEach(
            (trigger) => {

                trigger.addEventListener(
                    'click',
                    (event) => {

                        event.preventDefault();

                        openModal();

                    }
                );

            }
        );


        closeButtons.forEach(
            (button) => {

                button.addEventListener(
                    'click',
                    closeModal
                );

            }
        );


        backdrop?.addEventListener(
            'click',
            closeModal
        );


        document.addEventListener(
            'keydown',
            (event) => {

                if (
                    event.key === 'Escape'
                    &&
                    ! modal.classList.contains('hidden')
                ) {

                    closeModal();

                }

            }
        );


        /*
         * If VisitorRequest validation fails,
         * reopen only the registration modal.
         */
        if (
            modal.dataset.openOnError === 'true'
        ) {

            openModal();

        }


        /*
         * AI-assisted extraction still fills the exact
         * existing visitor form field IDs.
         *
         * After "Apply Suggested Fields" is clicked,
         * show the registration modal so reception staff
         * can review the suggested values before submitting.
         */
        document
            .querySelectorAll(
                '[data-ai-apply]'
            )
            .forEach(
                (button) => {

                    button.addEventListener(
                        'click',
                        () => {

                            window.setTimeout(
                                openModal,
                                80
                            );

                        }
                    );

                }
            );


        /*
         * Prevent clicks inside the panel from being
         * interpreted as backdrop clicks.
         */
        panel?.addEventListener(
            'click',
            (event) => {

                event.stopPropagation();

            }
        );

    }
);
</script>

<script>
document.addEventListener(
    'DOMContentLoaded',
    () => {

        /*
         * =====================================================
         * VISITOR DETAILS MODALS
         * =====================================================
         */
        const closeDetails =
            (modal) => {

                if (! modal) {
                    return;
                }

                modal.classList.add('hidden');

                if (
                    ! document.querySelector(
                        '[data-visitor-details-modal]:not(.hidden)'
                    )
                ) {

                    document.body.classList.remove(
                        'overflow-hidden'
                    );

                }

            };


        document
            .querySelectorAll(
                '[data-visitor-details-open]'
            )
            .forEach(
                (trigger) => {

                    trigger.addEventListener(
                        'click',
                        () => {

                            const id =
                                trigger.dataset.visitorDetailsOpen;

                            const modal =
                                document.querySelector(
                                    `[data-visitor-details-modal="${id}"]`
                                );

                            if (! modal) {
                                return;
                            }

                            modal.classList.remove(
                                'hidden'
                            );

                            document.body.classList.add(
                                'overflow-hidden'
                            );

                        }
                    );

                }
            );


        document
            .querySelectorAll(
                '[data-visitor-details-modal]'
            )
            .forEach(
                (modal) => {

                    modal
                        .querySelectorAll(
                            '[data-visitor-details-close]'
                        )
                        .forEach(
                            (button) => {

                                button.addEventListener(
                                    'click',
                                    () => closeDetails(modal)
                                );

                            }
                        );


                    modal
                        .querySelector(
                            '[data-visitor-details-backdrop]'
                        )
                        ?.addEventListener(
                            'click',
                            () => closeDetails(modal)
                        );

                }
            );


        /*
         * =====================================================
         * FIXED VISITOR ACTION MENUS
         * Prevent clipping inside overflow-x-auto tables.
         * =====================================================
         */
        const menus =
            Array.from(
                document.querySelectorAll(
                    '[data-visitor-menu]'
                )
            );


        const closeMenus =
            () => {

                menus.forEach(
                    (menu) => {

                        menu.classList.add(
                            'hidden'
                        );

                    }
                );

            };


        document
            .querySelectorAll(
                '[data-visitor-menu-trigger]'
            )
            .forEach(
                (trigger) => {

                    trigger.addEventListener(
                        'click',
                        (event) => {

                            event.stopPropagation();

                            const id =
                                trigger.dataset.visitorMenuTrigger;

                            const menu =
                                document.querySelector(
                                    `[data-visitor-menu="${id}"]`
                                );

                            if (! menu) {
                                return;
                            }


                            const wasOpen =
                                ! menu.classList.contains(
                                    'hidden'
                                );

                            closeMenus();

                            if (wasOpen) {
                                return;
                            }


                            menu.classList.remove(
                                'hidden'
                            );


                            const triggerRect =
                                trigger.getBoundingClientRect();

                            const menuRect =
                                menu.getBoundingClientRect();


                            let left =
                                triggerRect.right
                                -
                                menuRect.width;

                            let top =
                                triggerRect.bottom
                                +
                                6;


                            const viewportPadding =
                                8;


                            if (
                                left
                                <
                                viewportPadding
                            ) {

                                left =
                                    viewportPadding;

                            }


                            if (
                                left
                                +
                                menuRect.width
                                >
                                window.innerWidth
                                -
                                viewportPadding
                            ) {

                                left =
                                    window.innerWidth
                                    -
                                    menuRect.width
                                    -
                                    viewportPadding;

                            }


                            if (
                                top
                                +
                                menuRect.height
                                >
                                window.innerHeight
                                -
                                viewportPadding
                            ) {

                                top =
                                    triggerRect.top
                                    -
                                    menuRect.height
                                    -
                                    6;

                            }


                            menu.style.left =
                                `${left}px`;

                            menu.style.top =
                                `${Math.max(
                                    viewportPadding,
                                    top
                                )}px`;

                        }
                    );

                }
            );


        document.addEventListener(
            'click',
            closeMenus
        );


        menus.forEach(
            (menu) => {

                menu.addEventListener(
                    'click',
                    (event) => {

                        event.stopPropagation();

                    }
                );

            }
        );


        window.addEventListener(
            'resize',
            closeMenus
        );


        window.addEventListener(
            'scroll',
            closeMenus,
            true
        );


        /*
         * =====================================================
         * ESCAPE
         * =====================================================
         */
        document.addEventListener(
            'keydown',
            (event) => {

                if (
                    event.key
                    !==
                    'Escape'
                ) {
                    return;
                }

                closeMenus();


                document
                    .querySelectorAll(
                        '[data-visitor-details-modal]:not(.hidden)'
                    )
                    .forEach(
                        (modal) => closeDetails(modal)
                    );

            }
        );

    }
);
</script>

<script>
document.addEventListener(
    'DOMContentLoaded',
    () => {

        const modal =
            document.querySelector(
                '[data-visitor-checkin-modal]'
            );

        if (! modal) {
            return;
        }


        const form =
            modal.querySelector(
                '[data-visitor-checkin-form]'
            );

        const backdrop =
            modal.querySelector(
                '[data-visitor-checkin-backdrop]'
            );

        const name =
            modal.querySelector(
                '[data-visitor-checkin-name]'
            );

        const organization =
            modal.querySelector(
                '[data-visitor-checkin-organization]'
            );

        const host =
            modal.querySelector(
                '[data-visitor-checkin-host]'
            );

        const initial =
            modal.querySelector(
                '[data-visitor-checkin-initial]'
            );

        const badge =
            document.getElementById(
                'visitor_checkin_badge'
            );

        const notes =
            document.getElementById(
                'visitor_checkin_notes'
            );


        let previousFocus = null;


        const closeModal =
            () => {

                modal.classList.add(
                    'hidden'
                );

                document.body.classList.remove(
                    'overflow-hidden'
                );

                form?.reset();

                previousFocus?.focus?.();

            };


        const openModal =
            (trigger) => {

                previousFocus =
                    document.activeElement;


                form.action =
                    trigger.dataset.visitorCheckinAction;


                const visitorName =
                    trigger.dataset.visitorCheckinName
                    ||
                    'Visitor';

                const visitorOrganization =
                    trigger.dataset.visitorCheckinOrganization
                    ||
                    'No organization';

                const visitorHost =
                    trigger.dataset.visitorCheckinHost
                    ||
                    'Not assigned';


                name.textContent =
                    visitorName;

                organization.textContent =
                    visitorOrganization;

                host.textContent =
                    visitorHost;

                initial.textContent =
                    visitorName
                        .trim()
                        .charAt(0)
                        .toUpperCase()
                    ||
                    'V';


                if (badge) {
                    badge.value = '';
                }

                if (notes) {
                    notes.value = '';
                }


                modal.classList.remove(
                    'hidden'
                );

                document.body.classList.add(
                    'overflow-hidden'
                );


                requestAnimationFrame(
                    () => {

                        badge?.focus();

                    }
                );

            };


        document
            .querySelectorAll(
                '[data-visitor-checkin-open]'
            )
            .forEach(
                (trigger) => {

                    trigger.addEventListener(
                        'click',
                        () => openModal(trigger)
                    );

                }
            );


        modal
            .querySelectorAll(
                '[data-visitor-checkin-close]'
            )
            .forEach(
                (button) => {

                    button.addEventListener(
                        'click',
                        closeModal
                    );

                }
            );


        backdrop?.addEventListener(
            'click',
            closeModal
        );


        document.addEventListener(
            'keydown',
            (event) => {

                if (
                    event.key === 'Escape'
                    &&
                    ! modal.classList.contains('hidden')
                ) {

                    closeModal();

                }

            }
        );

    }
);
</script>

<script>
document.addEventListener(
    'DOMContentLoaded',
    () => {

        const modal =
            document.querySelector(
                '[data-visitor-checkout-modal]'
            );

        if (! modal) {
            return;
        }


        const form =
            modal.querySelector(
                '[data-visitor-checkout-form]'
            );

        const name =
            modal.querySelector(
                '[data-visitor-checkout-name]'
            );

        const organization =
            modal.querySelector(
                '[data-visitor-checkout-organization]'
            );

        const initial =
            modal.querySelector(
                '[data-visitor-checkout-initial]'
            );

        const checkinLabel =
            modal.querySelector(
                '[data-visitor-checkout-checkin-label]'
            );

        const duration =
            modal.querySelector(
                '[data-visitor-checkout-duration]'
            );

        const badge =
            modal.querySelector(
                '[data-visitor-checkout-badge]'
            );

        const backdrop =
            modal.querySelector(
                '[data-visitor-checkout-backdrop]'
            );


        let previousFocus = null;
        let durationTimer = null;
        let activeCheckIn = null;


        const formatDuration =
            (startIso) => {

                if (! startIso) {
                    return 'Unavailable';
                }


                const start =
                    new Date(startIso);

                if (
                    Number.isNaN(
                        start.getTime()
                    )
                ) {
                    return 'Unavailable';
                }


                const totalMinutes =
                    Math.max(
                        0,
                        Math.floor(
                            (
                                Date.now()
                                -
                                start.getTime()
                            )
                            /
                            60000
                        )
                    );


                const hours =
                    Math.floor(
                        totalMinutes / 60
                    );

                const minutes =
                    totalMinutes % 60;


                if (hours > 0) {

                    return `${hours} hr${hours === 1 ? '' : 's'} ${minutes} min`;

                }


                return `${minutes} min`;

            };


        const refreshDuration =
            () => {

                if (! duration) {
                    return;
                }

                duration.textContent =
                    formatDuration(
                        activeCheckIn
                    );

            };


        const stopTimer =
            () => {

                if (durationTimer) {

                    window.clearInterval(
                        durationTimer
                    );

                    durationTimer =
                        null;

                }

            };


        const closeModal =
            () => {

                stopTimer();

                modal.classList.add(
                    'hidden'
                );

                document.body.classList.remove(
                    'overflow-hidden'
                );

                activeCheckIn =
                    null;

                previousFocus?.focus?.();

            };


        const openModal =
            (trigger) => {

                previousFocus =
                    document.activeElement;


                form.action =
                    trigger.dataset.visitorCheckoutAction;


                const visitorName =
                    trigger.dataset.visitorCheckoutName
                    ||
                    'Visitor';

                const visitorOrganization =
                    trigger.dataset.visitorCheckoutOrganization
                    ||
                    'No organization';

                const visitorBadge =
                    trigger.dataset.visitorCheckoutBadge
                    ||
                    'Not assigned';

                activeCheckIn =
                    trigger.dataset.visitorCheckoutCheckin
                    ||
                    null;


                name.textContent =
                    visitorName;

                organization.textContent =
                    visitorOrganization;

                badge.textContent =
                    visitorBadge;

                checkinLabel.textContent =
                    trigger.dataset.visitorCheckoutCheckinLabel
                    ||
                    'Unavailable';

                initial.textContent =
                    visitorName
                        .trim()
                        .charAt(0)
                        .toUpperCase()
                    ||
                    'V';


                refreshDuration();

                stopTimer();

                durationTimer =
                    window.setInterval(
                        refreshDuration,
                        60000
                    );


                modal.classList.remove(
                    'hidden'
                );

                document.body.classList.add(
                    'overflow-hidden'
                );

            };


        document
            .querySelectorAll(
                '[data-visitor-checkout-open]'
            )
            .forEach(
                (trigger) => {

                    trigger.addEventListener(
                        'click',
                        () => openModal(trigger)
                    );

                }
            );


        modal
            .querySelectorAll(
                '[data-visitor-checkout-close]'
            )
            .forEach(
                (button) => {

                    button.addEventListener(
                        'click',
                        closeModal
                    );

                }
            );


        backdrop?.addEventListener(
            'click',
            closeModal
        );


        document.addEventListener(
            'keydown',
            (event) => {

                if (
                    event.key === 'Escape'
                    &&
                    ! modal.classList.contains('hidden')
                ) {

                    closeModal();

                }

            }
        );

    }
);
</script>

<script>
document.addEventListener(
    'DOMContentLoaded',
    () => {

        const modal =
            document.querySelector(
                '[data-visitor-decline-modal]'
            );

        if (! modal) {
            return;
        }


        const form =
            modal.querySelector(
                '[data-visitor-decline-form]'
            );

        const backdrop =
            modal.querySelector(
                '[data-visitor-decline-backdrop]'
            );

        const name =
            modal.querySelector(
                '[data-visitor-decline-name]'
            );

        const organization =
            modal.querySelector(
                '[data-visitor-decline-organization]'
            );

        const host =
            modal.querySelector(
                '[data-visitor-decline-host]'
            );

        const purpose =
            modal.querySelector(
                '[data-visitor-decline-purpose]'
            );

        const initial =
            modal.querySelector(
                '[data-visitor-decline-initial]'
            );

        const notes =
            document.getElementById(
                'visitor_decline_notes'
            );


        let previousFocus = null;


        const closeModal =
            () => {

                modal.classList.add(
                    'hidden'
                );

                document.body.classList.remove(
                    'overflow-hidden'
                );


                if (notes) {

                    notes.disabled =
                        false;

                    notes.value =
                        '';

                }


                previousFocus?.focus?.();

            };


        const openModal =
            (trigger) => {

                previousFocus =
                    document.activeElement;


                form.action =
                    trigger.dataset.visitorDeclineAction;


                const visitorName =
                    trigger.dataset.visitorDeclineName
                    ||
                    'Visitor';


                name.textContent =
                    visitorName;

                organization.textContent =
                    trigger.dataset.visitorDeclineOrganization
                    ||
                    'No organization';

                host.textContent =
                    trigger.dataset.visitorDeclineHost
                    ||
                    'Not assigned';

                purpose.textContent =
                    trigger.dataset.visitorDeclinePurpose
                    ||
                    'Not specified';

                initial.textContent =
                    visitorName
                        .trim()
                        .charAt(0)
                        .toUpperCase()
                    ||
                    'V';


                if (notes) {

                    notes.disabled =
                        false;

                    notes.value =
                        '';

                }


                modal.classList.remove(
                    'hidden'
                );

                document.body.classList.add(
                    'overflow-hidden'
                );


                requestAnimationFrame(
                    () => {

                        notes?.focus();

                    }
                );

            };


        document
            .querySelectorAll(
                '[data-visitor-decline-open]'
            )
            .forEach(
                (trigger) => {

                    trigger.addEventListener(
                        'click',
                        () => openModal(trigger)
                    );

                }
            );


        modal
            .querySelectorAll(
                '[data-visitor-decline-close]'
            )
            .forEach(
                (button) => {

                    button.addEventListener(
                        'click',
                        closeModal
                    );

                }
            );


        backdrop?.addEventListener(
            'click',
            closeModal
        );


        /*
         * Important:
         *
         * If notes are empty, disable the field before submit.
         * This prevents notes="" from replacing existing
         * visitor notes with an empty string.
         */
        form?.addEventListener(
            'submit',
            () => {

                if (
                    notes
                    &&
                    notes.value.trim() === ''
                ) {

                    notes.disabled =
                        true;

                }

            }
        );


        document.addEventListener(
            'keydown',
            (event) => {

                if (
                    event.key === 'Escape'
                    &&
                    ! modal.classList.contains('hidden')
                ) {

                    closeModal();

                }

            }
        );

    }
);
</script>

<script>
document.addEventListener(
    'DOMContentLoaded',
    () => {

        const modal =
            document.querySelector(
                '[data-ai-assistant-modal]'
            );

        if (! modal) {
            return;
        }


        const panel =
            modal.querySelector(
                '[data-ai-assistant-panel]'
            );

        const backdrop =
            modal.querySelector(
                '[data-ai-assistant-backdrop]'
            );

        const input =
            modal.querySelector(
                '[data-ai-input]'
            );


        let previousFocus =
            null;


        const openAssistant =
            () => {

                previousFocus =
                    document.activeElement;


                modal.classList.remove(
                    'hidden'
                );

                document.body.classList.add(
                    'overflow-hidden'
                );


                requestAnimationFrame(
                    () => {

                        input?.focus();

                    }
                );

            };


        const closeAssistant =
            () => {

                modal.classList.add(
                    'hidden'
                );

                document.body.classList.remove(
                    'overflow-hidden'
                );


                previousFocus?.focus?.();

            };


        document
            .querySelectorAll(
                '[data-ai-assistant-open]'
            )
            .forEach(
                (button) => {

                    button.addEventListener(
                        'click',
                        openAssistant
                    );

                }
            );


        modal
            .querySelectorAll(
                '[data-ai-assistant-close]'
            )
            .forEach(
                (button) => {

                    button.addEventListener(
                        'click',
                        closeAssistant
                    );

                }
            );


        backdrop?.addEventListener(
            'click',
            closeAssistant
        );


        panel?.addEventListener(
            'click',
            (event) => {

                event.stopPropagation();

            }
        );


        /*
         * app.js applies the suggestion to the existing
         * registration field IDs.
         *
         * The Visitor registration modal script already
         * opens registration after this same click.
         *
         * Close AI first so registration becomes the
         * visible human-review step.
         */
        modal
            .querySelector(
                '[data-ai-apply]'
            )
            ?.addEventListener(
                'click',
                () => {

                    closeAssistant();

                }
            );


        document.addEventListener(
            'keydown',
            (event) => {

                if (
                    event.key === 'Escape'
                    &&
                    ! modal.classList.contains('hidden')
                ) {

                    closeAssistant();

                }

            }
        );

    }
);
</script>

@endsection