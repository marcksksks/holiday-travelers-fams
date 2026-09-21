@can('operateVisitorDesk')

    <div
        data-visitor-decline-modal
        class="fixed inset-0 z-[90] hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="visitor-decline-title">

        {{-- Backdrop --}}
        <div
            data-visitor-decline-backdrop
            class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
        </div>


        <div class="relative flex min-h-full items-center justify-center p-2 sm:p-5">

            <div
                data-visitor-decline-panel
                class="w-full max-w-lg overflow-hidden rounded-2xl border border-border bg-card shadow-2xl">

                {{-- Header --}}
                <div class="flex items-start justify-between gap-4 border-b border-border px-5 py-4">

                    <div class="flex items-start gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-error/10 text-error">

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

                        </div>


                        <div class="min-w-0">

                            <p class="text-[9px] font-semibold uppercase tracking-[0.16em] text-secondary">
                                Visitor Desk
                            </p>

                            <h2
                                id="visitor-decline-title"
                                class="mt-0.5 font-heading text-lg font-semibold text-primary">

                                Decline Visit

                            </h2>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Review the visitor before marking this reception visit as declined.
                            </p>

                        </div>

                    </div>


                    <button
                        type="button"
                        data-visitor-decline-close
                        aria-label="Close decline visit dialog"
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
                    data-visitor-decline-form>

                    @csrf


                    <div class="space-y-4 p-5">

                        {{-- Visitor summary --}}
                        <div class="rounded-xl bg-background/60 p-4">

                            <div class="flex items-start gap-3">

                                <div
                                    data-visitor-decline-initial
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-accent/10 font-heading text-sm font-bold uppercase text-primary">

                                    V

                                </div>


                                <div class="min-w-0 flex-1">

                                    <p
                                        data-visitor-decline-name
                                        class="truncate text-sm font-semibold text-primary">

                                        Visitor

                                    </p>

                                    <p
                                        data-visitor-decline-organization
                                        class="mt-0.5 truncate text-[10px] text-slate-400">

                                        No organization

                                    </p>


                                    <div class="mt-2 border-t border-border pt-2">

                                        <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                            Host
                                        </p>

                                        <p
                                            data-visitor-decline-host
                                            class="mt-1 truncate text-xs font-medium text-primary">

                                            Not assigned

                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- Purpose --}}
                        <div>

                            <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                Purpose of Visit
                            </p>

                            <p
                                data-visitor-decline-purpose
                                class="mt-1.5 rounded-xl border border-border px-3 py-2.5 text-xs leading-5 text-slate-600">

                                Not specified

                            </p>

                        </div>


                        {{-- Decline reason --}}
                        <div>

                            <div class="flex items-center justify-between gap-3">

                                <label
                                    for="visitor_decline_notes"
                                    class="label">

                                    Decline Reason

                                </label>

                                <span class="text-[9px] font-medium text-slate-400">
                                    Optional
                                </span>

                            </div>


                            <textarea
                                id="visitor_decline_notes"
                                name="notes"
                                rows="3"
                                maxlength="1000"
                                placeholder="Example: Host unavailable, visitor arrived outside approved schedule..."
                                class="input resize-y"></textarea>


                            <p class="mt-1 text-[9px] leading-4 text-slate-400">
                                Add a reason when useful for reception records and follow-up.
                            </p>

                        </div>


                        {{-- Warning --}}
                        <div class="rounded-xl border border-error/15 bg-error/5 px-3.5 py-3">

                            <div class="flex items-start gap-2.5">

                                <svg
                                    class="mt-0.5 h-4 w-4 shrink-0 text-error"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 9v3m0 4h.01M10.3 3.6L2.5 17a2 2 0 001.7 3h15.6a2 2 0 001.7-3L13.7 3.6a2 2 0 00-3.4 0z" />

                                </svg>


                                <div>

                                    <p class="text-[10px] font-semibold text-error">
                                        This visit will be marked as declined.
                                    </p>

                                    <p class="mt-0.5 text-[10px] leading-4 text-slate-500">
                                        The visitor will leave the active reception queue and the action will be recorded in the audit trail.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Footer --}}
                    <div class="flex flex-col-reverse gap-2 border-t border-border bg-background/40 px-5 py-3 sm:flex-row sm:justify-end">

                        <button
                            type="button"
                            data-visitor-decline-close
                            class="btn-outline justify-center">

                            Cancel

                        </button>


                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-error px-4 py-2.5 font-button text-sm font-semibold text-white transition hover:bg-error/90">

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

                            Confirm Decline

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endcan