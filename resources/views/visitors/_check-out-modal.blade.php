@can('operateVisitorDesk')

    <div
        data-visitor-checkout-modal
        class="fixed inset-0 z-[90] hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="visitor-checkout-title">

        <div
            data-visitor-checkout-backdrop
            class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
        </div>


        <div class="relative flex min-h-full items-center justify-center p-2 sm:p-5">

            <div
                data-visitor-checkout-panel
                class="w-full max-w-lg overflow-hidden rounded-2xl border border-border bg-card shadow-2xl">

                {{-- Header --}}
                <div class="flex items-start justify-between gap-4 border-b border-border px-5 py-4">

                    <div class="flex items-start gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />

                            </svg>

                        </div>


                        <div>

                            <p class="text-[9px] font-semibold uppercase tracking-[0.16em] text-secondary">
                                Visitor Desk
                            </p>

                            <h2
                                id="visitor-checkout-title"
                                class="mt-0.5 font-heading text-lg font-semibold text-primary">

                                Confirm Departure

                            </h2>

                            <p class="mt-1 text-xs text-slate-500">
                                Review the active visit before closing the visitor record.
                            </p>

                        </div>

                    </div>


                    <button
                        type="button"
                        data-visitor-checkout-close
                        aria-label="Close departure dialog"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary">

                        <svg
                            class="h-5 w-5"
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

                </div>


                <form
                    method="POST"
                    data-visitor-checkout-form>

                    @csrf


                    <div class="space-y-4 p-5">

                        {{-- Visitor identity --}}
                        <div class="rounded-xl bg-background/60 p-4">

                            <div class="flex items-start gap-3">

                                <div
                                    data-visitor-checkout-initial
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-success/10 font-heading text-sm font-bold uppercase text-success">

                                    V

                                </div>


                                <div class="min-w-0">

                                    <p
                                        data-visitor-checkout-name
                                        class="truncate text-sm font-semibold text-primary">

                                        Visitor

                                    </p>

                                    <p
                                        data-visitor-checkout-organization
                                        class="mt-0.5 truncate text-[10px] text-slate-400">

                                        No organization

                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- Visit timing --}}
                        <div class="grid grid-cols-2 gap-3">

                            <div class="rounded-xl border border-border p-3">

                                <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                    Checked In
                                </p>

                                <p
                                    data-visitor-checkout-checkin-label
                                    class="mt-1.5 text-xs font-semibold text-primary">

                                    —

                                </p>

                            </div>


                            <div class="rounded-xl border border-success/20 bg-success/5 p-3">

                                <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                    Current Visit
                                </p>

                                <p
                                    data-visitor-checkout-duration
                                    class="mt-1.5 text-xs font-semibold text-success">

                                    —

                                </p>

                            </div>

                        </div>


                        {{-- Badge --}}
                        <div class="rounded-xl border border-border p-3">

                            <div class="flex items-center justify-between gap-3">

                                <div>

                                    <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                        Reception Badge
                                    </p>

                                    <p
                                        data-visitor-checkout-badge
                                        class="mt-1.5 text-xs font-semibold text-primary">

                                        Not assigned

                                    </p>

                                </div>


                                <svg
                                    class="h-5 w-5 text-slate-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M7 3h10a2 2 0 012 2v16l-7-3-7 3V5a2 2 0 012-2z" />

                                </svg>

                            </div>

                        </div>


                        <div class="rounded-xl border border-primary/15 bg-primary/5 px-3.5 py-3">

                            <div class="flex items-start gap-2.5">

                                <svg
                                    class="mt-0.5 h-4 w-4 shrink-0 text-primary"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                                </svg>


                                <p class="text-[10px] leading-4 text-slate-500">
                                    Confirming departure closes this visit. The final duration will be calculated by the server and a linked appointment will also be completed.
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- Footer --}}
                    <div class="flex flex-col-reverse gap-2 border-t border-border bg-background/40 px-5 py-3 sm:flex-row sm:justify-end">

                        <button
                            type="button"
                            data-visitor-checkout-close
                            class="btn-outline justify-center">

                            Cancel

                        </button>


                        <button
                            type="submit"
                            class="btn-primary justify-center">

                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7" />

                            </svg>

                            Confirm Departure

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endcan