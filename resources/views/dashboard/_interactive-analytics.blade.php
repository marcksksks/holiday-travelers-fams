@php
    $analyticsRangeLabels = [
        7 => 'Last 7 days',
        30 => 'Last 30 days',
    ];
@endphp

<section
    class="space-y-3"
    data-dashboard-analytics-root
    data-dashboard-analytics-default-range="{{ $dashboardAnalytics['default_range'] }}">

    <x-section-header
        title="Interactive Analytics">

        <x-slot:actions>

            <div
                class="inline-flex rounded-xl border border-border bg-card p-1 shadow-sm"
                role="group"
                aria-label="Analytics reporting period">

                @foreach ($analyticsRangeLabels as $days => $label)

                    <button
                        type="button"
                        @class([
                            'rounded-lg px-3 py-1.5 text-[11px] font-semibold transition',
                            'bg-primary text-white shadow-sm' =>
                                $days === $dashboardAnalytics['default_range'],
                            'text-slate-500 hover:bg-background hover:text-primary' =>
                                $days !== $dashboardAnalytics['default_range'],
                        ])
                        data-dashboard-analytics-range="{{ $days }}"
                        aria-pressed="{{ $days === $dashboardAnalytics['default_range'] ? 'true' : 'false' }}">

                        {{ $label }}

                    </button>

                @endforeach

            </div>

        </x-slot:actions>

    </x-section-header>


    <p
        class="sr-only"
        aria-live="polite"
        data-dashboard-analytics-live>

        Showing analytics for the last 7 days.

    </p>


    @foreach ($analyticsRangeLabels as $days => $rangeLabel)

        @php
            $range =
                $dashboardAnalytics['ranges'][$days];

            $statusMax =
                max(
                    1,
                    (int) (
                        collect(
                            $range['reservation_statuses']
                        )->max('count')
                        ?? 0
                    )
                );

            $facilityMax =
                max(
                    1,
                    (int) (
                        collect(
                            $range['facility_utilization']
                        )->max('count')
                        ?? 0
                    )
                );

            $series = [
                [
                    'key' => 'reservations',
                    'label' => 'Reservations',
                    'class_name' => 'text-primary',
                ],
            ];

            if ($dashboardAnalytics['can_view_appointments']) {
                $series[] = [
                    'key' => 'appointments',
                    'label' => 'Appointments',
                    'class_name' => 'text-accent',
                ];
            }

            if ($dashboardAnalytics['can_view_visitors']) {
                $series[] = [
                    'key' => 'visitors',
                    'label' => 'Visitors',
                    'class_name' => 'text-success',
                ];
            }

            $activityPayload = [
                'points' => $range['activity'],
                'series' => $series,
            ];
        @endphp


        <div
            @class([
                'space-y-3',
                'hidden' =>
                    $days !== $dashboardAnalytics['default_range'],
            ])
            data-dashboard-analytics-panel="{{ $days }}"
            aria-hidden="{{ $days === $dashboardAnalytics['default_range'] ? 'false' : 'true' }}">

                        <div class="grid gap-3 xl:grid-cols-[minmax(0,2fr)_minmax(280px,1fr)] xl:items-stretch">

<section class="card h-full overflow-hidden">

                <div class="flex flex-col gap-2 border-b border-border bg-background/40 px-4 py-3 sm:flex-row sm:items-start sm:justify-between">

                    <div>

                        <h3 class="font-heading text-sm font-semibold text-primary">
                            Operational Activity Trend
                        </h3>

                    </div>

                    <span class="w-fit rounded-full border border-border bg-card px-2.5 py-1 text-[9px] font-semibold uppercase tracking-wider text-slate-500">

                        {{ $range['start_date'] }}
                        &ndash;
                        {{ $range['end_date'] }}

                    </span>

                </div>


                <div class="p-3 sm:p-4">

                    <div
                        class="relative h-[160px] w-full overflow-hidden"
                        data-dashboard-line-chart>

                        <script
                            type="application/json"
                            data-dashboard-chart-data>@json($activityPayload)</script>

                        <div
                            class="absolute inset-0 hidden items-center justify-center rounded-xl border border-dashed border-border bg-background/40 px-4 py-3 text-center"
                            data-dashboard-chart-empty>

                            <div>

                                <div class="mx-auto flex h-8 w-8 items-center justify-center rounded-lg bg-primary/5 text-primary">

                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 19V9m5 10V5m5 14v-7m5 7V3" />

                                    </svg>

                                </div>

                                <p class="mt-2 text-sm font-semibold text-primary">
                                    No operational activity recorded for this period
                                </p>

                                <p class="mx-auto mt-1 max-w-lg text-[11px] leading-4 text-slate-500">
                                    Activity will appear when records are available.
                                </p>

                            </div>

                        </div>

                        <svg
                            class="absolute inset-0 h-full w-full"
                            viewBox="0 0 720 260"
                            role="img"
                            data-dashboard-chart-svg
                            aria-label="Operational activity trend">
                        </svg>

                    </div>


                    <div class="mt-2.5 flex flex-wrap gap-2">

                        <a
                            href="{{ route('reservations.index') }}"
                            class="inline-flex items-center gap-2 rounded-lg border border-border px-3 py-2 text-[11px] font-semibold text-primary transition hover:border-primary/30 hover:bg-primary/5">

                            <span class="h-2.5 w-2.5 rounded-full bg-primary"></span>
                            Reservations
                            <span aria-hidden="true">&rarr;</span>

                        </a>


                        @if ($dashboardAnalytics['can_view_appointments'])

                            <a
                                href="{{ route('appointments.index') }}"
                                class="inline-flex items-center gap-2 rounded-lg border border-border px-3 py-2 text-[11px] font-semibold text-primary transition hover:border-accent/30 hover:bg-accent/5">

                                <span class="h-2.5 w-2.5 rounded-full bg-accent"></span>
                                Appointments
                                <span aria-hidden="true">&rarr;</span>

                            </a>

                        @endif


                        @if ($dashboardAnalytics['can_view_visitors'])

                            <a
                                href="{{ route('visitors.index') }}"
                                class="inline-flex items-center gap-2 rounded-lg border border-border px-3 py-2 text-[11px] font-semibold text-primary transition hover:border-success/30 hover:bg-success/5">

                                <span class="h-2.5 w-2.5 rounded-full bg-success"></span>
                                Visitors
                                <span aria-hidden="true">&rarr;</span>

                            </a>

                        @endif

                    </div>

                </div>

            </section>

<section class="card flex h-full flex-col overflow-hidden">

                    <div class="border-b border-border bg-background/40 px-4 py-3">

                        <h3 class="font-heading text-sm font-semibold text-primary">
                            Facility Utilization
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Approved reservations by facility.
                        </p>

                    </div>


                    <div class="flex-1 p-3 sm:p-4">

                        @if (count($range['facility_utilization']) > 0)

                            <div class="space-y-3">

                                @foreach ($range['facility_utilization'] as $facilityItem)

                                    @php
                                        $facilityWidth =
                                            max(
                                                4,
                                                (
                                                    $facilityItem['count']
                                                    /
                                                    $facilityMax
                                                )
                                                *
                                                100
                                            );
                                    @endphp

                                    <a
                                        href="{{ route(
                                            'reservations.index',
                                            [
                                                'facility' =>
                                                    $facilityItem['facility_id'],
                                            ]
                                        ) }}"
                                        class="group block rounded-xl border border-border bg-background/40 p-3 transition hover:border-secondary/30 hover:bg-secondary/[0.03] focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40"
                                        data-dashboard-chart-drilldown>

                                        <div class="flex items-center justify-between gap-4">

                                            <span class="truncate text-xs font-semibold text-slate-600 group-hover:text-primary">
                                                {{ ctype_digit(trim((string) $facilityItem['name'])) ? 'Facility ' . trim((string) $facilityItem['name']) : $facilityItem['name'] }}
                                            </span>

                                            <span class="font-heading text-sm font-bold text-primary">
                                                {{ number_format($facilityItem['count']) }}
                                            </span>

                                        </div>

                                        <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">

                                            <div
                                                class="h-full rounded-full bg-secondary transition-all duration-300"
                                                style="width: {{ $facilityWidth }}%">
                                            </div>

                                        </div>

                                    </a>

                                @endforeach

                            </div>

                        @else

                            <div class="flex min-h-[160px] items-center justify-center rounded-xl border border-dashed border-border bg-background/50 px-4 py-4 text-center">

                                <p class="text-sm font-semibold text-primary">
                                    No approved facility activity
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    No data yet.
                                </p>

                            </div>

                        @endif

                    </div>

                </section>

            </div>


<section class="card overflow-hidden">

                    <div class="border-b border-border bg-background/40 px-4 py-3">

                        <h3 class="font-heading text-sm font-semibold text-primary">
                            Reservation Status
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Select a status to view records.
                        </p>

                    </div>


                    <div class="grid gap-2 p-3 sm:grid-cols-2 sm:p-4 lg:grid-cols-3 xl:grid-cols-5">

                        @foreach ($range['reservation_statuses'] as $statusItem)

                            @php
                                $statusWidth =
                                    $statusItem['count'] > 0
                                        ? max(
                                            4,
                                            (
                                                $statusItem['count']
                                                /
                                                $statusMax
                                            )
                                            *
                                            100
                                        )
                                        : 0;
                            @endphp

                            <a
                                href="{{ route(
                                    'reservations.index',
                                    [
                                        'status' =>
                                            $statusItem['key'],
                                    ]
                                ) }}"
                                class="group block rounded-lg border border-border bg-background/40 px-3 py-2.5 transition hover:border-primary/30 hover:bg-primary/[0.03] focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                                data-dashboard-chart-drilldown>

                                <div class="flex items-center justify-between gap-4">

                                    <span class="text-xs font-semibold text-slate-600 group-hover:text-primary">
                                        {{ $statusItem['label'] }}
                                    </span>

                                    <span class="font-heading text-sm font-bold text-primary">
                                        {{ number_format($statusItem['count']) }}
                                    </span>

                                </div>

                                <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">

                                    <div
                                        class="h-full rounded-full bg-primary transition-all duration-300"
                                        style="width: {{ $statusWidth }}%">
                                    </div>

                                </div>

                            </a>

                        @endforeach

                    </div>

                </section>

        </div>

    @endforeach

</section>