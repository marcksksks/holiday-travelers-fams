<div class="grid gap-3 md:hidden">

    @forelse ($visitors as $visitor)

        <article
            data-visitor-mobile-card
            class="card overflow-hidden">

            <div class="p-4">

                <div class="flex items-start justify-between gap-3">

                    <div class="flex min-w-0 items-start gap-3">

                        <div
                            @class([
                                'flex h-11 w-11 shrink-0 items-center justify-center rounded-full font-heading text-sm font-bold uppercase',
                                'bg-success/10 text-success' =>
                                    $visitor->status === 'checked_in',

                                'bg-accent/10 text-primary' =>
                                    $visitor->status !== 'checked_in',
                            ])>

                            {{ \Illuminate\Support\Str::substr($visitor->full_name, 0, 1) }}

                        </div>


                        <div class="min-w-0">

                            <p class="truncate font-button text-sm font-semibold text-primary">
                                {{ $visitor->full_name }}
                            </p>


                            <div class="mt-1 flex flex-wrap items-center gap-1.5">

                                @if ($visitor->organization)

                                    <span class="max-w-[170px] truncate text-[10px] text-slate-400">
                                        {{ $visitor->organization }}
                                    </span>

                                @endif


                                @if ($visitor->is_walk_in)

                                    <span class="rounded-full bg-secondary/10 px-1.5 py-0.5 text-[8px] font-semibold uppercase text-secondary">
                                        Walk-in
                                    </span>

                                @elseif ($visitor->appointment_id)

                                    <span class="rounded-full bg-accent/10 px-1.5 py-0.5 text-[8px] font-semibold uppercase text-primary">
                                        Appointment
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>


                    @switch($visitor->status)

                        @case('expected')

                            <span class="badge badge-info">
                                Expected
                            </span>

                            @break


                        @case('awaiting_host')

                            <span class="badge badge-warning">
                                Awaiting Host
                            </span>

                            @break


                        @case('checked_in')

                            <span class="badge badge-success">
                                On Site
                            </span>

                            @break


                        @case('completed')

                            <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                Completed
                            </span>

                            @break


                        @case('declined')

                            <span class="badge badge-error">
                                Declined
                            </span>

                            @break


                        @case('cancelled')

                            <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                Cancelled
                            </span>

                            @break


                        @case('no_show')

                            <span class="badge badge-warning">
                                No Show
                            </span>

                            @break


                        @default

                            <span class="badge badge-info">
                                {{ str($visitor->status)->headline() }}
                            </span>

                    @endswitch

                </div>


                <div class="mt-4 grid grid-cols-2 gap-3">

                    <div class="rounded-xl bg-background px-3 py-2.5">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Visit Type
                        </p>

                        <p class="mt-1 truncate text-xs font-medium text-slate-700">
                            {{ str($visitor->visitor_type ?: 'guest')->headline() }}
                        </p>

                    </div>


                    <div class="rounded-xl bg-background px-3 py-2.5">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Host
                        </p>

                        <p class="mt-1 truncate text-xs font-medium text-slate-700">
                            {{ $visitor->host_name ?: ($visitor->host_email ?: 'Not assigned') }}
                        </p>

                    </div>

                </div>


                <div class="mt-3 rounded-xl border border-border px-3 py-2.5">

                    <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                        Arrival
                    </p>


                    @if ($visitor->check_in_at)

                        <p
                            @class([
                                'mt-1 text-xs font-semibold',
                                'text-success' =>
                                    $visitor->status === 'checked_in',

                                'text-primary' =>
                                    $visitor->status !== 'checked_in',
                            ])>

                            {{ $visitor->check_in_at->format('M d, Y · h:i A') }}

                        </p>


                        @if ($visitor->status === 'checked_in')

                            <p
                                class="mt-1 text-[10px] font-semibold text-success"
                                data-visitor-live-duration
                                data-check-in-ms="{{ $visitor->check_in_at->timestamp * 1000 }}"
                                data-server-now-ms="{{ now()->timestamp * 1000 }}">

                                On site for

                                <span data-visitor-live-duration-value>
                                    calculating...
                                </span>

                            </p>

                        @endif

                    @else

                        <p class="mt-1 text-xs font-medium text-slate-500">
                            Waiting · Not checked in
                        </p>

                    @endif

                </div>

            </div>


            <div class="flex flex-wrap items-center gap-2 border-t border-border bg-background/40 px-4 py-3">

                <button
                    type="button"
                    data-visitor-details-open="{{ $visitor->id }}"
                    class="btn-outline flex-1 justify-center">

                    View Details

                </button>


                @can('operateVisitorDesk')

                    @if (in_array($visitor->status, ['expected', 'awaiting_host'], true))

                        <button
                            type="button"
                            data-visitor-checkin-open
                            data-visitor-checkin-action="{{ route('visitors.check-in', $visitor) }}"
                            data-visitor-checkin-name="{{ $visitor->full_name }}"
                            data-visitor-checkin-organization="{{ $visitor->organization }}"
                            data-visitor-checkin-host="{{ $visitor->host_name ?: $visitor->host_email }}"
                            class="inline-flex flex-1 items-center justify-center rounded-lg bg-success/10 px-3 py-2.5 font-button text-xs font-semibold text-success transition hover:bg-success hover:text-white">

                            Check In

                        </button>


                        <button
                            type="button"
                            data-visitor-decline-open
                            data-visitor-decline-action="{{ route('visitors.decline', $visitor) }}"
                            data-visitor-decline-name="{{ $visitor->full_name }}"
                            data-visitor-decline-organization="{{ $visitor->organization }}"
                            data-visitor-decline-host="{{ $visitor->host_name ?: $visitor->host_email }}"
                            data-visitor-decline-purpose="{{ $visitor->purpose }}"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-error/20 bg-error/5 text-error transition hover:bg-error hover:text-white"
                            aria-label="Decline visit">

                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />

                            </svg>

                        </button>


                    @elseif ($visitor->status === 'checked_in')

                        <button
                            type="button"
                            data-visitor-checkout-open
                            data-visitor-checkout-action="{{ route('visitors.check-out', $visitor) }}"
                            data-visitor-checkout-name="{{ $visitor->full_name }}"
                            data-visitor-checkout-organization="{{ $visitor->organization }}"
                            data-visitor-checkout-checkin="{{ $visitor->check_in_at?->toIso8601String() }}"
                            data-visitor-checkout-checkin-label="{{ $visitor->check_in_at?->format('M d, Y • h:i A') }}"
                            data-visitor-checkout-badge="{{ $visitor->badge_number }}"
                            class="btn-primary flex-1 justify-center">

                            Check Out

                        </button>

                    @endif

                @endcan

            </div>

        </article>


    @empty

        <div class="card">

            <x-empty-state
                title="No visitors found"
                description="No visitor records match the current Visitor Desk filters.">

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

            </x-empty-state>

        </div>

    @endforelse

</div>