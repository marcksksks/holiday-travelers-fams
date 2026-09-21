<div
    data-visitor-details-modal="{{ $visitor->id }}"
    class="fixed inset-0 z-[85] hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="visitor-details-title-{{ $visitor->id }}">

    <div
        data-visitor-details-backdrop
        class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
    </div>


    <div class="relative flex min-h-full items-center justify-center p-2 sm:p-5">

        <div class="flex max-h-[calc(100vh-1rem)] w-full max-w-xl flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-2xl sm:max-h-[calc(100vh-2.5rem)]">

            {{-- Header --}}
            <div class="flex items-start justify-between gap-4 border-b border-border px-5 py-4">

                <div class="flex min-w-0 items-start gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-accent/10 font-heading text-sm font-bold uppercase text-primary">

                        {{ \Illuminate\Support\Str::substr($visitor->full_name, 0, 1) }}

                    </div>


                    <div class="min-w-0">

                        <p class="text-[9px] font-semibold uppercase tracking-[0.16em] text-secondary">
                            Visitor Record
                        </p>

                        <h2
                            id="visitor-details-title-{{ $visitor->id }}"
                            class="mt-0.5 break-words font-heading text-lg font-semibold text-primary">

                            {{ $visitor->full_name }}

                        </h2>


                        <div class="mt-1.5 flex flex-wrap gap-1.5">

                            <span class="rounded-full bg-primary/5 px-2 py-1 text-[9px] font-semibold text-primary">
                                {{ str($visitor->visitor_type ?: 'guest')->headline() }}
                            </span>


                            @if ($visitor->is_walk_in)

                                <span class="rounded-full bg-secondary/10 px-2 py-1 text-[9px] font-semibold text-secondary">
                                    Walk-in
                                </span>

                            @elseif ($visitor->appointment_id)

                                <span class="rounded-full bg-accent/10 px-2 py-1 text-[9px] font-semibold text-primary">
                                    Appointment Linked
                                </span>

                            @endif

                        </div>

                    </div>

                </div>


                <button
                    type="button"
                    data-visitor-details-close
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary"
                    aria-label="Close visitor details">

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


            {{-- Content --}}
            <div class="min-h-0 flex-1 overflow-y-auto p-5">

                <div class="space-y-4">

                    {{-- Visit status --}}
                    <div class="rounded-xl bg-background/60 p-3">

                        <div class="flex flex-wrap items-center justify-between gap-2">

                            <div>

                                <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                    Visit Status
                                </p>

                                <p class="mt-1 text-xs font-semibold text-primary">
                                    {{ str($visitor->status)->headline() }}
                                </p>

                            </div>


                            @if ($visitor->badge_number)

                                <div class="text-right">

                                    <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                        Badge
                                    </p>

                                    <p class="mt-1 text-xs font-semibold text-primary">
                                        {{ $visitor->badge_number }}
                                    </p>

                                </div>

                            @endif

                        </div>

                    </div>


                    {{-- Visitor / Host --}}
                    <div class="grid gap-3 sm:grid-cols-2">

                        <div class="rounded-xl border border-border p-3">

                            <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                Organization
                            </p>

                            <p class="mt-1.5 break-words text-xs font-medium text-primary">
                                {{ $visitor->organization ?: 'Not specified' }}
                            </p>

                        </div>


                        <div class="rounded-xl border border-border p-3">

                            <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                Host
                            </p>

                            <p class="mt-1.5 break-words text-xs font-medium text-primary">
                                {{ $visitor->host_name ?: ($visitor->host_email ?: 'Not assigned') }}
                            </p>

                        </div>

                    </div>


                    {{-- Purpose --}}
                    <div>

                        <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                            Purpose of Visit
                        </p>

                        <p class="mt-1.5 whitespace-pre-line text-xs leading-5 text-slate-600">
                            {{ $visitor->purpose ?: 'No purpose recorded.' }}
                        </p>

                    </div>


                    {{-- Visit timing --}}
                    <div class="grid gap-3 sm:grid-cols-2">

                        <div class="rounded-xl border border-border p-3">

                            <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                Checked In
                            </p>

                            <p class="mt-1.5 text-xs font-semibold text-primary">
                                {{ $visitor->check_in_at?->format('M d, Y • h:i A') ?: 'Not checked in' }}
                            </p>

                        </div>


                        <div class="rounded-xl border border-border p-3">

                            <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                Checked Out
                            </p>

                            <p class="mt-1.5 text-xs font-semibold text-primary">
                                {{ $visitor->check_out_at?->format('M d, Y • h:i A') ?: 'Not checked out' }}
                            </p>


                            @if ($visitor->duration_minutes)

                                <p class="mt-1 text-[9px] text-slate-400">
                                    {{ $visitor->duration_minutes }} minutes on site
                                </p>

                            @endif

                        </div>

                    </div>


                    @if ($visitor->appointment_id)

                        <div class="rounded-xl border border-accent/20 bg-accent/5 p-3">

                            <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                Linked Appointment
                            </p>

                            <p class="mt-1 text-xs font-semibold text-primary">
                                Appointment #{{ $visitor->appointment_id }}
                            </p>

                            <p class="mt-1 text-[10px] leading-4 text-slate-500">
                                Appointment scheduling remains managed from the Appointments module.
                            </p>

                        </div>

                    @endif

                </div>

            </div>


            <div class="flex justify-end border-t border-border bg-background/30 px-5 py-3">

                <button
                    type="button"
                    data-visitor-details-close
                    class="btn-outline">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>