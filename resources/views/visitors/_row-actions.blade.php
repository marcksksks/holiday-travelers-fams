<div class="relative">

    <button
        type="button"
        data-visitor-menu-trigger="{{ $visitor->id }}"
        aria-label="More actions for {{ $visitor->full_name }}"
        class="flex h-9 w-9 items-center justify-center rounded-lg border border-border bg-card text-slate-400 transition hover:border-accent/40 hover:bg-accent/5 hover:text-primary">

        <svg
            class="h-5 w-5"
            fill="currentColor"
            viewBox="0 0 24 24">

            <circle cx="5" cy="12" r="1.6" />
            <circle cx="12" cy="12" r="1.6" />
            <circle cx="19" cy="12" r="1.6" />

        </svg>

    </button>


    <div
        data-visitor-menu="{{ $visitor->id }}"
        class="fixed z-[90] hidden w-56 overflow-hidden rounded-xl border border-border bg-card p-1.5 shadow-2xl">

        <button
            type="button"
            data-visitor-details-open="{{ $visitor->id }}"
            class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium text-slate-600 transition hover:bg-background hover:text-primary">

            <svg
                class="h-4 w-4 shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm7 0s-4 7-10 7S2 12 2 12s4-7 10-7 10 7 10 7z" />

            </svg>

            View Details

        </button>


        @can('operateVisitorDesk')

            @if (in_array($visitor->status, ['expected', 'awaiting_host'], true))

                <div class="my-1 border-t border-border"></div>


                <button
                    type="button"
                    data-visitor-decline-open
                    data-visitor-decline-action="{{ route('visitors.decline', $visitor) }}"
                    data-visitor-decline-name="{{ $visitor->full_name }}"
                    data-visitor-decline-organization="{{ $visitor->organization }}"
                    data-visitor-decline-host="{{ $visitor->host_name ?: $visitor->host_email }}"
                    data-visitor-decline-purpose="{{ $visitor->purpose }}"
                    class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-semibold text-error transition hover:bg-error/5">

                    <svg
                        class="h-4 w-4 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />

                    </svg>

                    Decline Visit

                </button>

            @endif

        @endcan

    </div>

</div>