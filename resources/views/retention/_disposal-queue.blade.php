@can('approveRetentionDisposal')

    @if ($retentionTab === 'disposal')

        <section class="space-y-4">

            {{-- Workspace heading --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">

                <div>

                    <div class="flex flex-wrap items-center gap-2">

                        <h3 class="font-heading text-lg font-semibold text-primary">
                            Disposal Queue
                        </h3>

                        @if ($pendingDisposals->isNotEmpty())

                            <span class="rounded-full bg-warning/10 px-2.5 py-1 text-[10px] font-semibold text-amber-700 ring-1 ring-inset ring-warning/20">
                                {{ $pendingDisposals->count() }}
                                {{ Str::plural('request', $pendingDisposals->count()) }}
                            </span>

                        @endif

                    </div>

                    <p class="mt-1 max-w-2xl text-xs leading-5 text-slate-500">
                        Review records submitted for controlled disposition before authorization is granted.
                    </p>

                </div>

            </div>


            @if ($pendingDisposals->isNotEmpty())

                <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-card">

                    {{-- Queue header --}}
                    <div class="hidden grid-cols-[minmax(220px,1.5fr)_minmax(160px,1fr)_minmax(180px,1fr)_auto] gap-4 border-b border-border bg-background/40 px-5 py-3 lg:grid">

                        <span class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                            Record
                        </span>

                        <span class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                            Requested By
                        </span>

                        <span class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                            Request
                        </span>

                        <span class="text-right text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                            Action
                        </span>

                    </div>


                    <div class="divide-y divide-border">

                        @foreach ($pendingDisposals as $pending)

                            @php
                                $isOwnRequest =
                                    $pending->disposition_requested_by ===
                                    auth()->user()?->email;

                                $policyLabel =
                                    $pending->policy?->name
                                    ?: $pending->policy_name
                                    ?: 'No retention policy assigned';
                            @endphp


                            <article class="px-5 py-4 transition hover:bg-background/40">

                                <div class="grid gap-4 lg:grid-cols-[minmax(220px,1.5fr)_minmax(160px,1fr)_minmax(180px,1fr)_auto] lg:items-start">

                                    {{-- Record --}}
                                    <div class="min-w-0">

                                        <div class="flex flex-wrap items-center gap-2">

                                            <span class="rounded-full bg-primary/5 px-2 py-0.5 text-[10px] font-semibold text-primary ring-1 ring-inset ring-primary/10">
                                                {{ str($pending->record_type)->headline() }}
                                            </span>

                                            <span class="rounded-full bg-warning/10 px-2 py-0.5 text-[10px] font-semibold text-amber-700">
                                                Pending Approval
                                            </span>

                                            @if ($isOwnRequest)

                                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">
                                                    Your Request
                                                </span>

                                            @endif

                                        </div>


                                        <h4 class="mt-2 truncate font-heading text-sm font-semibold text-primary">
                                            {{ $pending->record_title }}
                                        </h4>


                                        <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] text-slate-500">

                                            @if ($pending->record_id)

                                                <span>
                                                    Record #{{ $pending->record_id }}
                                                </span>

                                                <span
                                                    class="text-slate-300"
                                                    aria-hidden="true">
                                                    •
                                                </span>

                                            @endif

                                            <span class="truncate">
                                                {{ $policyLabel }}
                                            </span>

                                        </div>


                                        <div class="mt-3 rounded-lg bg-background/60 px-3 py-2">

                                            <p class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                                                Disposal Reason
                                            </p>

                                            <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-600">
                                                {{ in_array(trim((string) $pending->disposition_reason), ['', '-', '--', '---'], true) ? 'No disposal reason recorded.' : $pending->disposition_reason }}
                                            </p>

                                        </div>

                                    </div>


                                    {{-- Requester --}}
                                    <div>

                                        <p class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 lg:hidden">
                                            Requested By
                                        </p>

                                        <p class="mt-1 break-all text-xs font-medium text-slate-700 lg:mt-0">
                                            {{ $pending->disposition_requested_by ?: 'Unknown requester' }}
                                        </p>

                                        @if ($isOwnRequest)

                                            <p class="mt-1 text-[10px] leading-4 text-amber-700">
                                                Another authorized approver must make the decision.
                                            </p>

                                        @endif

                                    </div>


                                    {{-- Requested time --}}
                                    <div>

                                        <p class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 lg:hidden">
                                            Requested
                                        </p>

                                        @if ($pending->disposition_requested_at)

                                            <p class="mt-1 text-xs font-medium text-slate-700 lg:mt-0">
                                                {{ $pending->disposition_requested_at->format('M d, Y') }}
                                            </p>

                                            <p class="mt-1 text-[10px] text-slate-400">
                                                {{ $pending->disposition_requested_at->format('h:i A') }}
                                            </p>

                                        @else

                                            <p class="mt-1 text-xs text-slate-400 lg:mt-0">
                                                Date unavailable
                                            </p>

                                        @endif

                                    </div>


                                    {{-- Action --}}
                                    <div class="lg:text-right">

                                        <a
                                            href="{{ route('retention.disposition', $pending) }}"
                                            class="btn-outline inline-flex w-full justify-center whitespace-nowrap lg:w-auto">

                                            {{ $isOwnRequest ? 'View Request' : 'Review Request' }}

                                            <svg
                                                class="h-3.5 w-3.5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true">

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 5l7 7-7 7" />

                                            </svg>

                                        </a>

                                    </div>

                                </div>

                            </article>

                        @endforeach

                    </div>

                </div>

            @else

                <section class="rounded-2xl border border-dashed border-border bg-card px-6 py-12 text-center">

                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-success/10 text-success">

                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7" />

                        </svg>

                    </div>

                    <h3 class="mt-4 font-heading text-base font-semibold text-primary">
                        Disposal queue is clear
                    </h3>

                    <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">
                        No retention records currently require authorized disposition review.
                    </p>

                </section>

            @endif

        </section>

    @endif

@endcan