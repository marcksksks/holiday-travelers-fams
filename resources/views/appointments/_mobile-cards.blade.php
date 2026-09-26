<div
    data-appointment-card-grid
    class="grid gap-2.5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">

    @forelse ($appointments as $appointment)

        @php
            $appointmentStartsAt = null;

            if (
                in_array(
                    $appointment->status,
                    [
                        'scheduled',
                        'confirmed',
                    ],
                    true
                )
            ) {
                $appointmentStartsAt =
                    \Illuminate\Support\Carbon::parse(
                        $appointment->date->format('Y-m-d')
                        .' '
                        .substr(
                            (string) $appointment->start_time,
                            0,
                            5
                        ),
                        config(
                            'app.timezone',
                            'Asia/Manila'
                        )
                    );
            }
        @endphp


        <article
            data-appointment-mobile-card
            class="card flex min-h-[190px] flex-col overflow-hidden">

            <div class="flex-1 p-3">

                {{-- Header --}}
                <div class="flex items-start justify-between gap-2">

                    <div class="flex min-w-0 items-center gap-2.5">

                        <div
                            @class([
                                'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg font-heading text-xs font-bold uppercase',
                                'bg-success/10 text-success' =>
                                    $appointment->status === 'checked_in',
                                'bg-accent/10 text-primary' =>
                                    $appointment->status !== 'checked_in',
                            ])>

                            {{
                                \Illuminate\Support\Str::substr(
                                    $appointment->visitor_name,
                                    0,
                                    1
                                )
                            }}

                        </div>


                        <div class="min-w-0">

                            <p class="truncate text-sm font-semibold text-primary">
                                {{ $appointment->visitor_name }}
                            </p>

                            <p class="mt-0.5 truncate text-[10px] text-slate-400">

                                @if ($appointment->visitor_organization)

                                    {{ $appointment->visitor_organization }}

                                @else

                                    {{
                                        str(
                                            $appointment->visitor_type
                                            ?: 'guest'
                                        )->headline()
                                    }}

                                @endif

                            </p>

                        </div>

                    </div>


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
                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-border bg-card text-slate-400 transition hover:border-accent/40 hover:bg-accent/5 hover:text-primary">

                        <svg
                            class="h-4 w-4"
                            fill="currentColor"
                            viewBox="0 0 24 24">

                            <circle cx="5" cy="12" r="1.6" />
                            <circle cx="12" cy="12" r="1.6" />
                            <circle cx="19" cy="12" r="1.6" />

                        </svg>

                    </button>

                </div>


                {{-- Status + Date --}}
                <div class="mt-3 flex flex-wrap items-center gap-2">

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


                    <span class="text-[10px] text-slate-400">
                        {{ $appointment->date->format('M d, Y') }}
                    </span>

                </div>


                {{-- Live timing --}}
                @if ($appointmentStartsAt)

                    <div
                        class="mt-2 flex items-center gap-1.5"
                        data-appointment-live-timing
                        data-appointment-start-ms="{{ $appointmentStartsAt->timestamp * 1000 }}"
                        data-server-now-ms="{{ now()->timestamp * 1000 }}">

                        <span
                            class="h-1.5 w-1.5 shrink-0 rounded-full bg-secondary"
                            aria-hidden="true">
                        </span>

                        <span
                            class="text-[9px] font-semibold text-secondary"
                            data-appointment-live-timing-value>

                            Calculating...

                        </span>

                    </div>

                @endif


                {{-- Schedule --}}
                <div class="mt-3 grid grid-cols-2 gap-2">

                    <div class="rounded-lg bg-background/70 px-2.5 py-2">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Time
                        </p>

                        <p class="mt-1 text-xs font-medium text-primary">
                            {{ $appointment->start_time }}
                        </p>

                        @if ($appointment->end_time)

                            <p class="mt-0.5 text-[10px] text-slate-400">
                                to {{ $appointment->end_time }}
                            </p>

                        @endif

                    </div>


                    <div class="rounded-lg bg-background/70 px-2.5 py-2">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Host
                        </p>

                        <p class="mt-1 truncate text-xs font-medium text-primary">
                            {{
                                $appointment->host_name
                                ?: (
                                    $appointment->host_email
                                    ?: 'Not assigned'
                                )
                            }}
                        </p>

                    </div>

                </div>


                @if ($appointment->facility_name)

                    <div class="mt-2 flex items-center gap-1.5 text-[11px] text-slate-500">

                        <svg
                            class="h-3.5 w-3.5 shrink-0 text-accent"
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

                            {{
                                ctype_digit(
                                    (string) $appointment->facility_name
                                )
                                    ? 'Facility '.$appointment->facility_name
                                    : $appointment->facility_name
                            }}

                        </span>

                    </div>

                @endif


                {{-- Hidden View trigger --}}
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
                    class="hidden"
                    tabindex="-1">
                </button>


                {{-- Hidden Edit trigger --}}
                @can('manageAppointments')

                    @if (
                        in_array(
                            $appointment->status,
                            [
                                'scheduled',
                                'confirmed',
                            ],
                            true
                        )
                    )

                        <button
                            type="button"
                            data-appointment-edit
                            data-appointment-id="{{ $appointment->id }}"
                            data-update-url="{{ route('appointments.update', $appointment) }}"
                            data-name="{{ $appointment->visitor_name }}"
                            data-organization="{{ $appointment->visitor_organization }}"
                            data-email="{{ $appointment->visitor_email }}"
                            data-contact="{{ $appointment->visitor_contact }}"
                            data-type="{{ $appointment->visitor_type }}"
                            data-host-name="{{ $appointment->host_name }}"
                            data-host-email="{{ $appointment->host_email }}"
                            data-date="{{ \Illuminate\Support\Carbon::parse($appointment->date)->format('Y-m-d') }}"
                            data-start="{{ substr((string) $appointment->start_time, 0, 5) }}"
                            data-end="{{ substr((string) $appointment->end_time, 0, 5) }}"
                            data-facility-id="{{ $appointment->facility_id }}"
                            data-purpose="{{ $appointment->purpose }}"
                            data-notes="{{ $appointment->notes }}"
                            class="hidden"
                            tabindex="-1">
                        </button>

                    @endif

                @endcan

            </div>

        </article>


    @empty

        <div class="card py-10 text-center sm:col-span-2 lg:col-span-3 xl:col-span-4 2xl:col-span-5">

            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-accent/10 text-accent">

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

            </div>

            <p class="mt-3 text-sm font-semibold text-primary">
                No appointments scheduled
            </p>

            <p class="mt-1 text-xs text-slate-400">
                New appointments will appear here.
            </p>

        </div>

    @endforelse

</div>
