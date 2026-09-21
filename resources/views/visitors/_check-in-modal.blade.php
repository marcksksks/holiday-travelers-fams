@can('operateVisitorDesk')

    <div
        data-visitor-checkin-modal
        class="fixed inset-0 z-[90] hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="visitor-checkin-title">

        {{-- Backdrop --}}
        <div
            data-visitor-checkin-backdrop
            class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
        </div>


        <div class="relative flex min-h-full items-center justify-center p-2 sm:p-5">

            <div
                data-visitor-checkin-panel
                class="w-full max-w-lg overflow-hidden rounded-2xl border border-border bg-card shadow-2xl">

                {{-- Header --}}
                <div class="flex items-start justify-between gap-4 border-b border-border px-5 py-4">

                    <div class="flex items-start gap-3">

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


                        <div class="min-w-0">

                            <p class="text-[9px] font-semibold uppercase tracking-[0.16em] text-secondary">
                                Visitor Desk
                            </p>

                            <h2
                                id="visitor-checkin-title"
                                class="mt-0.5 font-heading text-lg font-semibold text-primary">

                                Check In Visitor

                            </h2>

                            <p class="mt-1 text-xs text-slate-500">
                                Confirm arrival and optionally assign a reception badge.
                            </p>

                        </div>

                    </div>


                    <button
                        type="button"
                        data-visitor-checkin-close
                        aria-label="Close check-in dialog"
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
                    data-visitor-checkin-form>

                    @csrf


                    <div class="space-y-4 p-5">

                        {{-- Visitor summary --}}
                        <div class="rounded-xl bg-background/60 p-4">

                            <div class="flex items-start gap-3">

                                <div
                                    data-visitor-checkin-initial
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-accent/10 font-heading text-sm font-bold uppercase text-primary">

                                    V

                                </div>


                                <div class="min-w-0 flex-1">

                                    <p
                                        data-visitor-checkin-name
                                        class="truncate text-sm font-semibold text-primary">

                                        Visitor

                                    </p>

                                    <p
                                        data-visitor-checkin-organization
                                        class="mt-0.5 truncate text-[10px] text-slate-400">

                                        —

                                    </p>

                                </div>

                            </div>


                            <div class="mt-3 border-t border-border pt-3">

                                <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                    Host
                                </p>

                                <p
                                    data-visitor-checkin-host
                                    class="mt-1 truncate text-xs font-medium text-primary">

                                    Not assigned

                                </p>

                            </div>

                        </div>


                        {{-- Badge --}}
                        <div>

                            <label
                                for="visitor_checkin_badge"
                                class="label">

                                Badge Number

                            </label>

                            <input
                                id="visitor_checkin_badge"
                                type="text"
                                name="badge_number"
                                maxlength="100"
                                autocomplete="off"
                                placeholder="e.g. VIS-024"
                                class="input">

                            <p class="mt-1 text-[9px] text-slate-400">
                                Optional reception or access badge identifier.
                            </p>

                        </div>


                        {{-- Notes --}}
                        <div>

                            <label
                                for="visitor_checkin_notes"
                                class="label">

                                Reception Notes

                            </label>

                            <textarea
                                id="visitor_checkin_notes"
                                name="notes"
                                rows="3"
                                placeholder="Optional arrival notes..."
                                class="input resize-y"></textarea>

                        </div>


                        <div class="rounded-xl border border-success/15 bg-success/5 px-3.5 py-3">

                            <div class="flex items-start gap-2.5">

                                <svg
                                    class="mt-0.5 h-4 w-4 shrink-0 text-success"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7" />

                                </svg>


                                <p class="text-[10px] leading-4 text-slate-500">
                                    Confirming check-in marks the visitor as on site. If linked to an appointment, that appointment will also move to its checked-in state.
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- Footer --}}
                    <div class="flex flex-col-reverse gap-2 border-t border-border bg-background/40 px-5 py-3 sm:flex-row sm:justify-end">

                        <button
                            type="button"
                            data-visitor-checkin-close
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
                                    d="M5 13l4 4L19 7" />

                            </svg>

                            Confirm Check In

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endcan