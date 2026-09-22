<div class="grid gap-3 md:hidden">

    @forelse ($appointments as $appointment)

        <article
            data-appointment-mobile-card
            class="card overflow-hidden">

            <div class="p-4">

                <div class="flex items-start justify-between gap-3">

                    <div class="flex min-w-0 items-start gap-3">

                        <div
                            @class([
                                'flex h-11 w-11 shrink-0 items-center justify-center rounded-full font-heading text-sm font-bold uppercase',
                                'bg-success/10 text-success' =>
                                    $appointment->status === 'checked_in',

                                'bg-accent/10 text-primary' =>
                                    $appointment->status !== 'checked_in',
                            ])>

                            {{ \Illuminate\Support\Str::substr($appointment->visitor_name, 0, 1) }}

                        </div>


                        <div class="min-w-0">

                            <p class="truncate font-button text-sm font-semibold text-primary">
                                {{ $appointment->visitor_name }}
                            </p>

                            <p class="mt-1 truncate text-[11px] text-slate-400">

                                @if ($appointment->visitor_organization)

                                    {{ $appointment->visitor_organization }}

                                @else

                                    {{ str($appointment->visitor_type ?: 'guest')->headline() }}

                                @endif

                            </p>

                        </div>

                    </div>


                    @switch($appointment->status)

                        @case('scheduled')

                            <span class="badge badge-info">
                                Scheduled
                            </span>

                            @break


                        @case('confirmed')

                            <span class="badge badge-success">
                                Confirmed
                            </span>

                            @break


                        @case('checked_in')

                            <span class="badge badge-success">
                                Checked In
                            </span>

                            @break


                        @case('completed')

                            <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                Completed
                            </span>

                            @break


                        @case('cancelled')

                            <span class="badge badge-error">
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
                                {{ str($appointment->status)->headline() }}
                            </span>

                    @endswitch

                </div>


                <div class="mt-4 grid grid-cols-2 gap-3">

                    <div class="rounded-xl bg-background px-3 py-2.5">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Schedule
                        </p>

                        <p class="mt-1 text-xs font-semibold text-primary">
                            {{ $appointment->date->format('M d, Y') }}
                        </p>

                        <p class="mt-0.5 text-[10px] text-slate-500">

                            {{ $appointment->start_time }}

                            @if ($appointment->end_time)
                                &ndash; {{ $appointment->end_time }}
                            @endif

                        </p>

                    </div>


                    <div class="rounded-xl bg-background px-3 py-2.5">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Host
                        </p>

                        <p class="mt-1 truncate text-xs font-medium text-slate-700">
                            {{ $appointment->host_name ?: ($appointment->host_email ?: 'Not assigned') }}
                        </p>

                    </div>

                </div>


                @if ($appointment->facility_name)

                    <div class="mt-3 flex items-center gap-2 text-xs text-slate-500">

                        <svg
                            class="h-4 w-4 shrink-0 text-accent"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5" />

                        </svg>

                        <span class="truncate">
                            {{ $appointment->facility_name }}
                        </span>

                    </div>

                @endif

            </div>


            <div class="flex items-center gap-2 border-t border-border bg-background/40 px-4 py-3">

                <button
                    type="button"
                    data-appointment-view
                    data-appointment-id="{{ $appointment->id }}"
                    data-name="{{ $appointment->visitor_name }}"
                    data-organization="{{ $appointment->visitor_organization }}"
                    data-email="{{ $appointment->visitor_email }}"
                    data-contact="{{ $appointment->visitor_contact }}"
                    data-type="{{ str($appointment->visitor_type)->replace('_', ' ')->title() }}"
                    data-host-name="{{ $appointment->host_name }}"
                    data-host-email="{{ $appointment->host_email }}"
                    data-date="{{ $appointment->date }}"
                    data-start="{{ $appointment->start_time }}"
                    data-end="{{ $appointment->end_time }}"
                    data-facility="{{ $appointment->facility_name ?: 'Not assigned' }}"
                    data-purpose="{{ $appointment->purpose }}"
                    data-notes="{{ $appointment->notes }}"
                    data-status="{{ $appointment->status }}"
                    class="btn-outline flex-1 justify-center">

                    View Details

                </button>


                <button
                    type="button"
                    data-appointment-actions-open
                    data-appointment-id="{{ $appointment->id }}"
                    data-status="{{ $appointment->status }}"
                    data-can-manage="{{ auth()->user()->can('manageAppointments') ? '1' : '0' }}"
                    data-status-url="{{ route('appointments.status', $appointment) }}"
                    data-cancel-url="{{ route('appointments.cancel', $appointment) }}"
                    aria-haspopup="menu"
                    aria-expanded="false"
                    aria-label="Appointment actions"
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-border bg-card text-slate-500 transition hover:border-accent/40 hover:text-primary">

                    <svg
                        class="h-5 w-5"
                        fill="currentColor"
                        viewBox="0 0 24 24">

                        <circle cx="5" cy="12" r="1.7" />
                        <circle cx="12" cy="12" r="1.7" />
                        <circle cx="19" cy="12" r="1.7" />

                    </svg>

                </button>

            </div>

        </article>


    @empty

        <div class="card">

            <x-empty-state
                title="No appointments found"
                description="No appointments match the current schedule filters.">

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
                            d="M8 7V3m8 4V3M5 11h14M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

                    </svg>

                </x-slot:icon>

            </x-empty-state>

        </div>

    @endforelse

</div>