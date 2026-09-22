<div class="grid gap-3 md:hidden">

    @forelse ($retentions as $retention)

        @php
            $daysToReview = $retention->review_date
                ? (int) now()
                    ->startOfDay()
                    ->diffInDays(
                        $retention->review_date
                            ->copy()
                            ->startOfDay(),
                        false
                    )
                : null;
        @endphp


        <article
            data-retention-mobile-card
            @class([
                'card overflow-hidden',
                'border-error/20 bg-error/[0.02]' =>
                    $daysToReview !== null
                    && $daysToReview < 0,

                'border-warning/30 bg-warning/[0.02]' =>
                    $daysToReview !== null
                    && $daysToReview >= 0
                    && $daysToReview <= 30,
            ])>

            <div class="p-4">

                <div class="flex items-start justify-between gap-3">

                    <div class="flex min-w-0 items-start gap-3">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 3h7l5 5v13H7V3zm7 0v5h5" />

                            </svg>

                        </div>


                        <div class="min-w-0">

                            <p class="truncate font-button text-sm font-semibold text-primary">
                                {{ $retention->record_title }}
                            </p>

                            <div class="mt-1 flex flex-wrap items-center gap-1.5">

                                <span class="rounded-full bg-primary/5 px-2 py-0.5 text-[9px] font-medium text-primary">
                                    {{ str($retention->record_type)->headline() }}
                                </span>


                                @if ($retention->record_id)

                                    <span class="text-[10px] text-slate-400">
                                        ID #{{ $retention->record_id }}
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>


                    <span @class([
                        'badge',

                        'badge-success' =>
                            $retention->compliance_status === 'compliant',

                        'badge-warning' =>
                            $retention->compliance_status === 'at_risk',

                        'badge-error' =>
                            $retention->compliance_status === 'non_compliant',
                    ])>

                        {{ str($retention->compliance_status)->headline() }}

                    </span>

                </div>


                <div class="mt-4 grid grid-cols-2 gap-3">

                    <div class="rounded-xl bg-background px-3 py-2.5">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Policy
                        </p>


                        @if ($retention->policy)

                            <p class="mt-1 truncate text-xs font-medium text-slate-700">
                                {{ $retention->policy->name }}
                            </p>

                            <p class="mt-0.5 text-[10px] text-slate-400">
                                {{ $retention->policy->retention_years }}
                                {{ Str::plural('year', $retention->policy->retention_years) }}
                            </p>

                        @elseif ($retention->policy_name)

                            <p class="mt-1 truncate text-xs font-medium text-slate-700">
                                {{ $retention->policy_name }}
                            </p>

                        @else

                            <p class="mt-1 text-xs text-slate-400">
                                No policy assigned
                            </p>

                        @endif

                    </div>


                    <div class="rounded-xl bg-background px-3 py-2.5">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Review
                        </p>


                        @if ($retention->review_date)

                            <p
                                @class([
                                    'mt-1 text-xs font-semibold',
                                    'text-error' =>
                                        $daysToReview !== null
                                        && $daysToReview < 0,

                                    'text-amber-600' =>
                                        $daysToReview !== null
                                        && $daysToReview >= 0
                                        && $daysToReview <= 30,

                                    'text-slate-700' =>
                                        $daysToReview === null
                                        || $daysToReview > 30,
                                ])>

                                {{ $retention->review_date->format('M d, Y') }}

                            </p>


                            @if ($daysToReview !== null && $daysToReview < 0)

                                <p class="mt-1 text-[10px] font-semibold text-error">
                                    Overdue by {{ abs($daysToReview) }} days
                                </p>

                            @elseif ($daysToReview === 0)

                                <p class="mt-1 text-[10px] font-semibold text-error">
                                    Review due today
                                </p>

                            @elseif ($daysToReview !== null && $daysToReview <= 30)

                                <p class="mt-1 text-[10px] font-semibold text-amber-600">
                                    Due in {{ $daysToReview }} days
                                </p>

                            @endif

                        @else

                            <p class="mt-1 text-xs text-slate-400">
                                No review date
                            </p>

                        @endif

                    </div>

                </div>


                <div class="mt-3 flex flex-wrap items-center gap-2">

                    <span @class([
                        'badge',

                        'badge-success' =>
                            $retention->status === 'retained',

                        'badge-warning' =>
                            $retention->status === 'review_required',

                        'badge-info' =>
                            $retention->status === 'extended',

                        'bg-slate-100 text-slate-600' =>
                            $retention->status === 'archived',

                        'badge-error' =>
                            $retention->status === 'marked_for_disposal',
                    ])>

                        {{ str($retention->status)->headline() }}

                    </span>


                    @if ($retention->last_action_by)

                        <span class="truncate text-[10px] text-slate-400">
                            Updated by {{ $retention->last_action_by }}
                        </span>

                    @endif

                </div>

            </div>


            @can('manageRetention')

                <div class="border-t border-border bg-background/40 px-4 py-3">

                    <a
                        href="{{ route('retention.review', $retention) }}"
                        class="btn-outline w-full justify-center">

                        @if ($retention->status === 'review_required')
                            Review Record
                        @else
                            Manage Record
                        @endif

                    </a>

                </div>

            @endcan

        </article>


    @empty

        <div class="card">

            <x-empty-state
                title="No retention records found"
                description="No records match the current retention filters.">

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
                            d="M7 3h7l5 5v13H7V3zm7 0v5h5" />

                    </svg>

                </x-slot:icon>

            </x-empty-state>

        </div>

    @endforelse

</div>