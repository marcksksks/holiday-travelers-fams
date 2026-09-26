@php
    $forecastStatus =
        $trafficForecastTomorrow['status']
        ?? 'cold_start';

    $modelReady =
        $forecastStatus === 'ready';

    $syntheticDemoPersisted =
        (bool) (
            $trafficForecastDemoStatus['active']
            ?? false
        );

    $syntheticDemoActive =
        $syntheticDemoPersisted
        ||
        (bool) (
            $trafficForecastTomorrow['demo_mode']
            ?? false
        );

    $hourlyForecast =
        collect(
            $trafficForecastTomorrow['hourly']
            ?? []
        );

    $maxHourlyVisitors =
        max(
            1,
            (int) (
                $hourlyForecast
                    ->max('expected_visitors')
                ?? 0
            )
        );

    $peakRow =
        $hourlyForecast
            ->firstWhere(
                'hour',
                $trafficForecastTomorrow['peak_hour']
                    ?? null
            );

    $peakMeetingDemand =
        $peakRow['meeting_room_demand']
        ?? 'none';

    $confidenceLabel =
        match (
            $trafficForecastTomorrow['confidence']
            ?? 'unavailable'
        ) {
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low',
            'demonstration' => 'Demonstration',
            default => 'Unavailable',
        };
@endphp


<section
    data-visitor-traffic-forecast
    data-synthetic-demo="{{ $syntheticDemoActive ? 'true' : 'false' }}"
    data-forecast-status="{{ $forecastStatus }}"
    data-forecast-scheduled="{{ $trafficForecastTomorrow['scheduled_visitors'] ?? 0 }}"
    class="space-y-4">

    <x-section-header
        eyebrow="AI-Assisted Predictive Analytics"
        title="Visitor Traffic Intelligence"
        description="Forecast expected visitor demand, peak periods, front-desk staffing needs, and meeting-room pressure using local operational data.">

        <x-slot:actions>

            <div class="flex flex-wrap items-center gap-2">

                <span
                    @class([
                        'inline-flex items-center rounded-full border px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em]',
                        'border-success/20 bg-success/10 text-success' => $modelReady,
                        'border-warning/25 bg-warning/10 text-warning' => ! $modelReady,
                    ])>

                    {{ $modelReady ? 'Local ML Ready' : 'Cold Start' }}

                </span>


                <span class="inline-flex items-center rounded-full border border-border bg-background px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">

                    Zero API Cost

                </span>

            </div>

        </x-slot:actions>

    </x-section-header>


    @if ($syntheticDemoActive)

        <div
            data-synthetic-forecast-disclosure
            role="status"
            class="rounded-2xl border border-accent/30 bg-accent/5 px-4 py-4 sm:px-5">

            <div class="flex items-start gap-3">

                <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-accent/10 text-secondary">

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-3-3v6m8-3a8 8 0 11-16 0 8 8 0 0116 0z" />

                    </svg>

                </div>


                <div class="min-w-0 flex-1">

                    <div class="flex flex-wrap items-center gap-2">

                        <p class="font-button text-sm font-semibold text-primary">
                            Synthetic Demonstration Data Active
                        </p>

                        <span class="inline-flex rounded-full border border-accent/25 bg-accent/10 px-2 py-0.5 text-[9px] font-semibold uppercase tracking-[0.12em] text-secondary">
                            Academic Demo
                        </span>

                    </div>


                    <p class="mt-1.5 text-xs leading-5 text-slate-500">

                        Visitor Traffic Intelligence is currently using synthetic academic training observations to demonstrate and evaluate the forecasting workflow.

                        <strong class="font-semibold text-primary">
                            These records are not client production history and must not be presented as actual Holiday Travelers visitor traffic.
                        </strong>

                    </p>


                    @if (! $syntheticDemoPersisted)

                        <p class="mt-3 text-[10px] leading-4 text-slate-400">
                            In-memory demo mode: synthetic KNN training observations exist only for this forecast calculation. No synthetic visitor, appointment, or reservation records are stored in operational PostgreSQL tables.
                        </p>

                    @endif

                    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-[10px] text-slate-400">

                        <span>
                            Synthetic visitors:
                            <strong class="font-semibold text-primary">
                                {{ number_format($trafficForecastDemoStatus['visitors'] ?? 0) }}
                            </strong>
                        </span>

                        <span>
                            Synthetic appointments:
                            <strong class="font-semibold text-primary">
                                {{ number_format($trafficForecastDemoStatus['appointments'] ?? 0) }}
                            </strong>
                        </span>

                        <span>
                            Synthetic reservations:
                            <strong class="font-semibold text-primary">
                                {{ number_format($trafficForecastDemoStatus['reservations'] ?? 0) }}
                            </strong>
                        </span>

                    </div>

                </div>

            </div>

        </div>

    @endif


    @if (! $modelReady)

        <div class="rounded-2xl border border-warning/25 bg-warning/5 px-4 py-3.5 sm:px-5">

            <div class="flex items-start gap-3">

                <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-warning/10 text-warning">

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v4m0 4h.01M10.3 3.7L2.7 17a2 2 0 001.7 3h15.2a2 2 0 001.7-3L13.7 3.7a2 2 0 00-3.4 0z" />

                    </svg>

                </div>


                <div class="min-w-0">

                    <p class="font-button text-sm font-semibold text-primary">
                        Historical learning is not available yet
                    </p>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        The production database does not yet contain enough completed check-in history to train the local forecasting model.
                        The values below show known scheduled appointment demand only; the system does not invent an ML prediction.
                    </p>

                </div>

            </div>

        </div>

    @endif


    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">

        <div class="card p-4">

            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                Expected Tomorrow
            </p>

            <p class="mt-2 font-heading text-2xl font-bold text-primary">
                {{ number_format($trafficForecastTomorrow['expected_visitors'] ?? 0) }}
            </p>

            <p class="mt-1 text-xs text-slate-500">
                Visitors across operating hours
            </p>

        </div>


        <div class="card p-4">

            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                Scheduled Demand
            </p>

            <p class="mt-2 font-heading text-2xl font-bold text-primary">
                {{ number_format($trafficForecastTomorrow['scheduled_visitors'] ?? 0) }}
            </p>

            <p class="mt-1 text-xs text-slate-500">
                Known appointments from PostgreSQL
            </p>

        </div>


        <div class="card p-4">

            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                Predicted Walk-ins
            </p>

            <p class="mt-2 font-heading text-2xl font-bold text-primary">
                {{ number_format((float) ($trafficForecastTomorrow['predicted_walk_ins'] ?? 0), 1) }}
            </p>

            <p class="mt-1 text-xs text-slate-500">
                {{ $modelReady ? 'Local KNN estimate' : 'Unavailable during cold start' }}
            </p>

        </div>


        <div class="card p-4">

            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                Peak Period
            </p>

            <p class="mt-2 font-heading text-xl font-bold text-primary">
                {{ $trafficForecastTomorrow['peak_hour'] ?? '—' }}
            </p>

            <p class="mt-1 text-xs text-slate-500">
                {{ number_format($trafficForecastTomorrow['peak_expected_visitors'] ?? 0) }}
                expected visitor{{ ($trafficForecastTomorrow['peak_expected_visitors'] ?? 0) === 1 ? '' : 's' }}
            </p>

        </div>


        <div class="card p-4">

            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                Front Desk Coverage
            </p>

            <p class="mt-2 font-heading text-2xl font-bold text-primary">
                {{ number_format($trafficForecastTomorrow['recommended_peak_staff'] ?? 1) }}
            </p>

            <p class="mt-1 text-xs text-slate-500">
                Personnel suggested at predicted peak
            </p>

        </div>

    </div>


    <div class="grid gap-4 xl:grid-cols-[minmax(0,1.45fr)_minmax(280px,0.75fr)]">

        <div class="card p-4 sm:p-5">

            <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">

                <div>

                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-secondary">
                        Tomorrow
                    </p>

                    <h3 class="mt-1 font-heading text-base font-semibold text-primary">
                        Hourly Visitor Forecast
                    </h3>

                </div>


                <div class="text-xs text-slate-500">

                    Confidence:
                    <span class="font-semibold text-primary">
                        {{ $confidenceLabel }}
                    </span>

                </div>

            </div>


            <div class="mt-5 space-y-3">

                @foreach ($hourlyForecast as $hour)

                    @php
                        $hourExpected =
                            (int) (
                                $hour['expected_visitors']
                                ?? 0
                            );

                        $barWidth =
                            $hourExpected > 0
                                ? max(
                                    4,
                                    min(
                                        100,
                                        (int) round(
                                            (
                                                $hourExpected
                                                /
                                                $maxHourlyVisitors
                                            )
                                            *
                                            100
                                        )
                                    )
                                )
                                : 0;
                    @endphp

                    <div class="grid grid-cols-[48px_minmax(0,1fr)_34px] items-center gap-3">

                        <span class="text-xs font-medium tabular-nums text-slate-500">
                            {{ $hour['hour'] }}
                        </span>


                        <div class="h-2.5 overflow-hidden rounded-full bg-background">

                            <div
                                class="h-full rounded-full bg-accent transition-all"
                                style="width: {{ $barWidth }}%">
                            </div>

                        </div>


                        <span class="text-right text-xs font-semibold tabular-nums text-primary">
                            {{ $hourExpected }}
                        </span>

                    </div>

                @endforeach

            </div>


            <div class="mt-5 grid gap-3 border-t border-border pt-4 sm:grid-cols-3">

                <div>

                    <p class="text-[10px] uppercase tracking-wide text-slate-400">
                        Traffic Level
                    </p>

                    <p class="mt-1 text-sm font-semibold capitalize text-primary">
                        {{ str_replace('_', ' ', $trafficForecastTomorrow['peak_traffic_level'] ?? 'low') }}
                    </p>

                </div>


                <div>

                    <p class="text-[10px] uppercase tracking-wide text-slate-400">
                        Meeting Room Demand
                    </p>

                    <p class="mt-1 text-sm font-semibold capitalize text-primary">
                        {{ str_replace('_', ' ', $peakMeetingDemand) }}
                    </p>

                </div>


                <div>

                    <p class="text-[10px] uppercase tracking-wide text-slate-400">
                        Forecast Engine
                    </p>

                    <p class="mt-1 text-sm font-semibold text-primary">
                        {{ $modelReady ? 'Local KNN regression' : 'Scheduled-demand baseline' }}
                    </p>

                </div>

            </div>

        </div>


        <div class="card p-4 sm:p-5">

            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-secondary">
                Planning Horizon
            </p>

            <h3 class="mt-1 font-heading text-base font-semibold text-primary">
                Next 7 Days
            </h3>


            <div class="mt-4 space-y-2.5">

                @foreach ($trafficForecastWeek as $day)

                    @php
                        $forecastDate =
                            \Carbon\CarbonImmutable::parse(
                                $day['date']
                            );
                    @endphp

                    <div class="flex items-center justify-between gap-3 rounded-xl border border-border bg-background/50 px-3 py-2.5">

                        <div class="min-w-0">

                            <p class="text-xs font-semibold text-primary">
                                {{ $forecastDate->format('D, M d') }}
                            </p>

                            <p class="mt-0.5 text-[10px] text-slate-400">
                                Peak {{ $day['peak_hour'] ?? '—' }}
                            </p>

                        </div>


                        <div class="text-right">

                            <p class="font-heading text-base font-bold tabular-nums text-primary">
                                {{ number_format($day['expected_visitors'] ?? 0) }}
                            </p>

                            <p class="text-[9px] uppercase tracking-wide text-slate-400">
                                expected
                            </p>

                        </div>

                    </div>

                @endforeach

            </div>


            <div class="mt-4 rounded-xl border border-border bg-background/60 px-3.5 py-3">

                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                    Model Readiness
                </p>

                <p class="mt-1 text-xs leading-5 text-slate-500">

                    {{ number_format($trafficForecastTomorrow['history']['timestamped_visits'] ?? 0) }}
                    timestamped visits across
                    {{ number_format($trafficForecastTomorrow['history']['distinct_visit_days'] ?? 0) }}
                    historical days.

                    Minimum:
                    {{ number_format($trafficForecastTomorrow['history']['minimum_visits_required'] ?? 100) }}
                    visits across
                    {{ number_format($trafficForecastTomorrow['history']['minimum_days_required'] ?? 21) }}
                    days.

                </p>

            </div>

        </div>

    </div>


    @if ($trafficForecastValidation['available'] ?? false)

        <div
            data-synthetic-backtest
            class="card p-4 sm:p-5">

            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">

                <div class="max-w-2xl">

                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-secondary">
                        Synthetic Model Validation
                    </p>

                    <h3 class="mt-1 font-heading text-base font-semibold text-primary">
                        Chronological Backtest
                    </h3>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        The first
                        {{ number_format($trafficForecastValidation['training_days']) }}
                        synthetic days train the local KNN model and the final
                        {{ number_format($trafficForecastValidation['validation_days']) }}
                        days are held out for evaluation.
                    </p>

                </div>


                <span class="inline-flex w-fit rounded-full border border-warning/25 bg-warning/10 px-2.5 py-1 text-[9px] font-semibold uppercase tracking-[0.12em] text-warning">
                    Synthetic Backtest Only
                </span>

            </div>


            <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

                <div class="rounded-xl border border-border bg-background/60 p-3.5">

                    <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                        MAE
                    </p>

                    <p class="mt-1 font-heading text-xl font-bold text-primary">
                        {{ number_format((float) $trafficForecastValidation['mae_visitors_per_hour'], 2) }}
                    </p>

                    <p class="mt-1 text-[10px] text-slate-500">
                        visitors / hour
                    </p>

                </div>


                <div class="rounded-xl border border-border bg-background/60 p-3.5">

                    <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                        WAPE
                    </p>

                    <p class="mt-1 font-heading text-xl font-bold text-primary">
                        {{ number_format((float) $trafficForecastValidation['wape_percent'], 1) }}%
                    </p>

                    <p class="mt-1 text-[10px] text-slate-500">
                        weighted absolute percentage error
                    </p>

                </div>


                <div class="rounded-xl border border-border bg-background/60 p-3.5">

                    <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                        Training Observations
                    </p>

                    <p class="mt-1 font-heading text-xl font-bold text-primary">
                        {{ number_format($trafficForecastValidation['training_observations']) }}
                    </p>

                    <p class="mt-1 text-[10px] text-slate-500">
                        chronological hourly samples
                    </p>

                </div>


                <div class="rounded-xl border border-border bg-background/60 p-3.5">

                    <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                        Holdout Observations
                    </p>

                    <p class="mt-1 font-heading text-xl font-bold text-primary">
                        {{ number_format($trafficForecastValidation['validation_observations']) }}
                    </p>

                    <p class="mt-1 text-[10px] text-slate-500">
                        unseen hourly samples
                    </p>

                </div>

            </div>


            <p class="mt-4 text-[10px] leading-4 text-slate-400">
                These MAE and WAPE values evaluate only the synthetic academic demonstration dataset.
                They do not establish production accuracy or real-world generalization.
            </p>

        </div>

    @endif

    <p class="text-[10px] leading-4 text-slate-400">
        Predictions are decision-support estimates, not guaranteed visitor counts.
        Staffing recommendations are operational rules applied after forecasting.
        No visitor name, email, phone number, ID reference, or free-text purpose is used by the forecasting model.
    </p>

</section>