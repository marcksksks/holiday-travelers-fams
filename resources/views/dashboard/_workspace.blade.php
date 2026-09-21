@php
    $user =
        auth()->user();


    $attentionCount =
        $pendingReservations
        +
        ($canViewContracts
            ? $contractsExpiringSoon
            : 0)
        +
        ($canViewLegal
            ? $legalActionRequired
            : 0)
        +
        ($canViewDocuments
            ? $documentNeedsReview
            : 0)
        +
        ($canManageRetention
            ? $retentionReviewRequired
            : 0)
        +
        ($canApproveDisposal
            ? $pendingDisposalApprovals
            : 0);
@endphp


<div class="space-y-7">

    {{-- =====================================================
         PAGE HEADER
    ====================================================== --}}
    <x-page-header
        eyebrow="FAMS Command Center"
        title="Operations Dashboard"
        badge="Operations Overview"
        description="Monitor facilities, reservations, appointments, visitors, legal matters, contracts, documents, and compliance activity from one workspace.">

        <x-slot:actions>

            <div
                @class([
                    'inline-flex items-center gap-3 rounded-xl border px-4 py-2.5 shadow-sm',
                    'border-error/20 bg-error/5' => $attentionCount > 0,
                    'border-success/20 bg-success/5' => $attentionCount === 0,
                ])>

                <div
                    @class([
                        'flex h-8 w-8 items-center justify-center rounded-lg',
                        'bg-error/10 text-error' => $attentionCount > 0,
                        'bg-success/10 text-success' => $attentionCount === 0,
                    ])>

                    @if ($attentionCount > 0)

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v4m0 4h.01M10.3 3.8L2.5 18a2 2 0 001.8 3h15.4a2 2 0 001.8-3L13.7 3.8a2 2 0 00-3.4 0z" />

                        </svg>

                    @else

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

                    @endif

                </div>


                <div>

                    <p
                        @class([
                            'text-[9px] font-semibold uppercase tracking-[0.14em]',
                            'text-error' => $attentionCount > 0,
                            'text-success' => $attentionCount === 0,
                        ])>

                        {{ $attentionCount > 0 ? 'Needs Attention' : 'All Clear' }}

                    </p>

                    <p class="mt-0.5 text-xs font-medium text-primary">

                        @if ($attentionCount > 0)

                            {{ number_format($attentionCount) }}
                            {{ \Illuminate\Support\Str::plural('item', $attentionCount) }}

                        @else

                            No pending items

                        @endif

                    </p>

                </div>

            </div>

        </x-slot:actions>

    </x-page-header>


    {{-- =====================================================
         SYSTEM OVERVIEW
    ====================================================== --}}
    <section class="space-y-4">

        <x-section-header
            eyebrow="Overview"
            title="System Overview"
            description="A role-aware snapshot of the operational areas available to you." />


        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3 min-[1800px]:grid-cols-6">

            <x-metric-card
                label="Available Facilities"
                :value="number_format($facilityCount)"
                :href="route('facilities.index')"
                helper="Facilities currently available for reservation."
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
                            d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5M9 8h1M14 8h1" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Pending Reservations"
                :value="number_format($pendingReservations)"
                :href="route('reservations.index')"
                helper="Reservation requests currently awaiting action."
                tone="secondary">

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
                            d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            @if ($canViewAppointments)

                <x-metric-card
                    label="Today's Appointments"
                    :value="number_format($todaysAppointments)"
                    :href="route('appointments.index')"
                    helper="Appointments scheduled for today."
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
                                d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1zM8 15h3M8 18h5" />

                        </svg>

                    </x-slot:icon>

                </x-metric-card>

            @endif


            @if ($canViewVisitors)

                <x-metric-card
                    label="Visitors On Site"
                    :value="number_format($checkedInVisitors)"
                    :href="route('visitors.index', ['status' => 'checked_in'])"
                    helper="Visitors currently checked in."
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
                                d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8zM17 11h4M19 9v4" />

                        </svg>

                    </x-slot:icon>

                </x-metric-card>

            @endif


            @if ($canViewContracts)

                <x-metric-card
                    label="Contracts Expiring"
                    :value="number_format($contractsExpiringSoon)"
                    :href="route('contracts.index')"
                    helper="Active contracts expiring within 30 days."
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
                                d="M8 3h8l3 3v15H5V3h3zM8 3v5h8V3M8 13h8M8 17h6" />

                        </svg>

                    </x-slot:icon>

                </x-metric-card>

            @endif


            @if ($canViewLegal)

                <x-metric-card
                    label="Legal Action Required"
                    :value="number_format($legalActionRequired)"
                    :href="route('legal.index', ['review_status' => 'action_required'])"
                    helper="Legal matters currently requiring action."
                    tone="error">

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
                                d="M12 3v18M5 7h14M7 7l-3 6h6L7 7zM17 7l-3 6h6l-3-6zM5 21h14" />

                        </svg>

                    </x-slot:icon>

                </x-metric-card>

            @endif

        </div>

    </section>


    {{-- =====================================================
         LIVE OPERATIONS
    ====================================================== --}}
    @include('dashboard._live-operations')


    {{-- =====================================================
         ATTENTION CENTER
    ====================================================== --}}
    <section class="space-y-4">

            <x-section-header
                eyebrow="Work Queues"
                title="Needs Attention"
                description="Items that may require review, approval, renewal, or follow-up.">

                <x-slot:actions>

                    @if ($attentionCount === 0)

                        <span class="badge badge-success">
                            All clear
                        </span>

                    @else

                        <span class="badge badge-error">
                            {{ number_format($attentionCount) }}
                            pending
                        </span>

                    @endif

                </x-slot:actions>

            </x-section-header>


            @if ($attentionCount > 0)

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">

                    @if ($pendingReservations > 0)

                    <x-metric-card
                        label="Reservation Decisions"
                        :value="number_format($pendingReservations)"
                        :href="route('reservations.index')"
                        helper="Pending facility reservation requests."
                        tone="secondary" />

                @endif


                @if ($canViewDocuments && $documentNeedsReview > 0)

                    <x-metric-card
                        label="Documents Needing Review"
                        :value="number_format($documentNeedsReview)"
                        :href="route('documents.index', ['status' => 'needs_review'])"
                        helper="Documents currently flagged for review."
                        tone="warning" />

                @endif


                @if ($canManageRetention && $retentionReviewRequired > 0)

                    <x-metric-card
                        label="Retention Reviews"
                        :value="number_format($retentionReviewRequired)"
                        :href="route('retention.index', ['status' => 'review_required'])"
                        helper="Retention records requiring a lifecycle decision."
                        tone="error" />

                @endif


                @if ($canApproveDisposal && $pendingDisposalApprovals > 0)

                    <x-metric-card
                        label="Disposal Approvals"
                        :value="number_format($pendingDisposalApprovals)"
                        :href="route('retention.index')"
                        helper="Disposition requests waiting for authorization."
                        tone="secondary" />

                @endif


                @if ($canViewContracts && $contractsExpiringSoon > 0)

                    <x-metric-card
                        label="Contract Renewals"
                        :value="number_format($contractsExpiringSoon)"
                        :href="route('contracts.index')"
                        helper="Contracts reaching their end date within 30 days."
                        tone="warning" />

                @endif


                @if ($canViewLegal && $legalActionRequired > 0)

                    <x-metric-card
                        label="Legal Follow-up"
                        :value="number_format($legalActionRequired)"
                        :href="route('legal.index', ['review_status' => 'action_required'])"
                        helper="Legal matters marked as action required."
                        tone="error" />

                @endif

                </div>

            @else

                <div class="card border-success/20 bg-success/5 p-5">

                    <div class="flex items-start gap-4">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-success/10 text-success">

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

                        </div>


                        <div>

                            <p class="text-sm font-semibold text-primary">
                                All operational queues are clear
                            </p>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                There are currently no reservations, document reviews, retention decisions, contract renewals, disposal approvals, or legal matters requiring attention.
                            </p>

                        </div>

                    </div>

                </div>

            @endif

    </section>


    {{-- =====================================================
         SCHEDULE & UPCOMING WORK
    ====================================================== --}}
    <section class="space-y-4">

        <x-section-header
            eyebrow="Schedule"
            title="Upcoming Work"
            description="Approved reservations and today's appointments in one operational view." />


        <div
            @class([
                'grid gap-5',
                'xl:grid-cols-2' => $canViewAppointments,
                'grid-cols-1' => ! $canViewAppointments,
            ])>


            {{-- Upcoming Reservations --}}
            <section class="card overflow-hidden">

                <div class="flex items-center justify-between gap-4 border-b border-border bg-background/40 px-5 py-4">

                    <div>

                        <h3 class="font-heading text-sm font-semibold text-primary">
                            Upcoming Reservations
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Approved facility reservations.
                        </p>

                    </div>


                    <a
                        href="{{ route('reservations.index') }}"
                        class="text-xs font-semibold text-primary transition hover:text-secondary">

                        View all &rarr;

                    </a>

                </div>


                <div class="divide-y divide-border">

                    @forelse ($upcomingReservations as $reservation)

                        <a
                            href="{{ route('reservations.index') }}"
                            class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-background/60">

                            <div class="min-w-0">

                                <p class="truncate text-sm font-semibold text-primary">
                                    {{ $reservation->facility_name }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $reservation->date->format('M d, Y') }}
                                    &middot;
                                    {{ $reservation->start_time }}
                                    &ndash;
                                    {{ $reservation->end_time }}
                                </p>

                            </div>


                            <span class="badge badge-success shrink-0">
                                Approved
                            </span>

                        </a>

                    @empty

                        <x-empty-state
                            title="No upcoming reservations"
                            description="Approved facility bookings will appear here.">

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
                                        d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1V6a1 1 0 011-1z" />

                                </svg>

                            </x-slot:icon>

                        </x-empty-state>

                    @endforelse

                </div>

            </section>


            {{-- Today's Appointments --}}
            @if ($canViewAppointments)

                <section class="card overflow-hidden">

                    <div class="flex items-center justify-between gap-4 border-b border-border bg-background/40 px-5 py-4">

                        <div>

                            <h3 class="font-heading text-sm font-semibold text-primary">
                                Today's Appointments
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                Scheduled and confirmed appointments for today.
                            </p>

                        </div>


                        <a
                            href="{{ route('appointments.index') }}"
                            class="text-xs font-semibold text-primary transition hover:text-accent">

                            View all &rarr;

                        </a>

                    </div>


                    <div class="divide-y divide-border">

                        @forelse ($recentAppointments as $appointment)

                            <a
                                href="{{ route('appointments.index') }}"
                                class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-background/60">

                                <div class="min-w-0">

                                    <p class="truncate text-sm font-semibold text-primary">
                                        {{ $appointment->visitor_name }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">

                                        {{ $appointment->start_time }}

                                        @if ($appointment->host_name)

                                            &middot;
                                            Host:
                                            {{ $appointment->host_name }}

                                        @endif

                                    </p>

                                </div>


                                <span class="badge badge-info shrink-0">
                                    {{ str($appointment->status)->headline() }}
                                </span>

                            </a>

                        @empty

                            <x-empty-state
                                title="No appointments today"
                                description="Today's scheduled appointments will appear here.">

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
                                            d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1V6a1 1 0 011-1z" />

                                    </svg>

                                </x-slot:icon>

                            </x-empty-state>

                        @endforelse

                    </div>

                </section>

            @endif

        </div>

    </section>


    {{-- =====================================================
         QUICK ACCESS
    ====================================================== --}}
    <section class="space-y-4 xl:hidden">

        <x-section-header
            eyebrow="Navigation"
            title="Quick Access"
            description="Jump directly to the workspaces available for your role." />


        <div class="card p-4">

            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                <a
                    href="{{ route('facilities.index') }}"
                    class="group flex items-center gap-3 rounded-xl border border-border p-3 transition hover:border-primary/20 hover:bg-background">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                        <x-nav-icon name="facilities" />
                    </div>

                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-primary">
                            Facilities
                        </p>
                        <p class="mt-0.5 text-[11px] text-slate-400">
                            Reservations & availability
                        </p>
                    </div>

                </a>


                <a
                    href="{{ route('reservations.index') }}"
                    class="group flex items-center gap-3 rounded-xl border border-border p-3 transition hover:border-secondary/30 hover:bg-background">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-secondary/10 text-secondary">
                        <x-nav-icon name="appointments" />
                    </div>

                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-primary">
                            Reservations
                        </p>
                        <p class="mt-0.5 text-[11px] text-slate-400">
                            Requests & decisions
                        </p>
                    </div>

                </a>


                @if ($canViewAppointments)

                    <a
                        href="{{ route('appointments.index') }}"
                        class="group flex items-center gap-3 rounded-xl border border-border p-3 transition hover:border-accent/30 hover:bg-background">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-accent/10 text-accent">
                            <x-nav-icon name="appointments" />
                        </div>

                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-primary">
                                Appointments
                            </p>
                            <p class="mt-0.5 text-[11px] text-slate-400">
                                Schedule & visitors
                            </p>
                        </div>

                    </a>

                @endif


                @if ($canViewVisitors)

                    <a
                        href="{{ route('visitors.index') }}"
                        class="group flex items-center gap-3 rounded-xl border border-border p-3 transition hover:border-success/30 hover:bg-background">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-success/10 text-success">
                            <x-nav-icon name="visitors" />
                        </div>

                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-primary">
                                Visitor Desk
                            </p>
                            <p class="mt-0.5 text-[11px] text-slate-400">
                                Check-in & check-out
                            </p>
                        </div>

                    </a>

                @endif


                @if ($canViewDocuments)

                    <a
                        href="{{ route('documents.index') }}"
                        class="group flex items-center gap-3 rounded-xl border border-border p-3 transition hover:border-accent/30 hover:bg-background">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-accent/10 text-accent">
                            <x-nav-icon name="documents" />
                        </div>

                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-primary">
                                Documents
                            </p>
                            <p class="mt-0.5 text-[11px] text-slate-400">
                                Archive & records
                            </p>
                        </div>

                    </a>

                @endif


                @if ($canViewLegal)

                    <a
                        href="{{ route('legal.index') }}"
                        class="group flex items-center gap-3 rounded-xl border border-border p-3 transition hover:border-error/30 hover:bg-background">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-error/10 text-error">
                            <x-nav-icon name="legal" />
                        </div>

                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-primary">
                                Legal Management
                            </p>
                            <p class="mt-0.5 text-[11px] text-slate-400">
                                Matters & compliance
                            </p>
                        </div>

                    </a>

                @endif


                @if ($canViewContracts)

                    <a
                        href="{{ route('contracts.index') }}"
                        class="group flex items-center gap-3 rounded-xl border border-border p-3 transition hover:border-warning/30 hover:bg-background">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-warning/10 text-amber-600">
                            <x-nav-icon name="contracts" />
                        </div>

                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-primary">
                                Contracts
                            </p>
                            <p class="mt-0.5 text-[11px] text-slate-400">
                                Agreements & renewals
                            </p>
                        </div>

                    </a>

                @endif

            </div>

        </div>

    </section>

</div>