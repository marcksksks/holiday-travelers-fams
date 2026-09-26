@extends('layouts.app')

@section('title', 'Appointments')

@section('content')

<div class="space-y-4">

    {{-- =====================================================
         APPOINTMENTS WORKSPACE HEADER
    ====================================================== --}}
    <x-page-header

        title="Appointments"

        description="Manage visitor appointments, schedules, and facility assignments.">

        @can('manageAppointments')

            <x-slot:actions>

                <button
                    type="button"
                    data-appointment-create-open
                    class="btn-primary inline-flex items-center justify-center gap-2">

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4" />

                    </svg>

                    Schedule Appointment

                </button>

            </x-slot:actions>

        @endcan

    </x-page-header>

    <div class="min-w-0 space-y-4">

        @include('appointments._overview')


        {{-- Appointment Records --}}
        <div class="min-w-0 space-y-3">

            <x-section-header
                title="Appointment Schedule"
                description="Visitor appointment records." />


            @include('appointments._filters')


            @include('appointments._mobile-cards')

            @if ($appointments->hasPages())

                <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
                    {{ $appointments->links() }}
                </div>

            @endif

        </div>

    </div>

</div>


@can('manageAppointments')

    {{-- Live appointment countdown --}}
    @once
        <script>
            document.addEventListener(
                'DOMContentLoaded',
                () => {

                    const timingElements =
                        document.querySelectorAll(
                            '[data-appointment-live-timing]'
                        );


                    if (! timingElements.length) {
                        return;
                    }


                    /*
                     * Anchor all timers to Laravel's server time.
                     * performance.now() only measures elapsed browser
                     * time after the page has loaded.
                     */
                    const pageStartedAt =
                        performance.now();


                    const formatDuration =
                        (milliseconds) => {

                            const totalMinutes =
                                Math.max(
                                    1,
                                    Math.round(
                                        milliseconds / 60000
                                    )
                                );


                            const days =
                                Math.floor(
                                    totalMinutes / 1440
                                );


                            const hours =
                                Math.floor(
                                    (totalMinutes % 1440) / 60
                                );


                            const minutes =
                                totalMinutes % 60;


                            if (days > 0) {

                                if (hours > 0) {
                                    return `${days}d ${hours}h`;
                                }

                                return `${days}d`;

                            }


                            if (hours > 0) {

                                if (minutes > 0) {
                                    return `${hours}h ${minutes}m`;
                                }

                                return `${hours}h`;

                            }


                            return `${minutes}m`;

                        };


                    const famsAppointmentLiveTiming =
                        () => {

                            const browserElapsed =
                                performance.now() -
                                pageStartedAt;


                            timingElements.forEach(
                                (element) => {

                                    const startMs =
                                        Number(
                                            element.dataset
                                                .appointmentStartMs
                                        );


                                    const serverNowMs =
                                        Number(
                                            element.dataset
                                                .serverNowMs
                                        );


                                    const value =
                                        element.querySelector(
                                            '[data-appointment-live-timing-value]'
                                        );


                                    if (
                                        ! value ||
                                        ! Number.isFinite(startMs) ||
                                        ! Number.isFinite(serverNowMs)
                                    ) {
                                        return;
                                    }


                                    const currentServerTime =
                                        serverNowMs +
                                        browserElapsed;


                                    const difference =
                                        startMs -
                                        currentServerTime;


                                    /*
                                     * Within one minute of the scheduled
                                     * start, display "Starting now".
                                     */
                                    if (
                                        Math.abs(
                                            difference
                                        ) < 60000
                                    ) {

                                        value.textContent =
                                            'Starting now';

                                        return;
                                    }


                                    if (difference > 0) {

                                        value.textContent =
                                            `Starts in ${
                                                formatDuration(
                                                    difference
                                                )
                                            }`;

                                        return;
                                    }


                                    value.textContent =
                                        `Overdue by ${
                                            formatDuration(
                                                Math.abs(
                                                    difference
                                                )
                                            )
                                        }`;

                                }
                            );

                        };


                    famsAppointmentLiveTiming();


                    window.setInterval(
                        famsAppointmentLiveTiming,
                        30000
                    );

                }
            );
        </script>
    @endonce

    {{-- =====================================================
         SCHEDULE APPOINTMENT MODAL
    ====================================================== --}}
    <div
        data-appointment-create-modal
        class="fixed inset-0 z-[80] hidden"
        aria-hidden="true">

        {{-- Backdrop --}}
        <div
            data-appointment-create-backdrop
            class="absolute inset-0 bg-slate-950/50 backdrop-blur-[2px]">
        </div>


        {{-- Modal positioning --}}
        <div class="relative flex min-h-full items-center justify-center p-4 sm:p-6">

            <div
                data-appointment-create-panel
                class="relative w-full max-w-2xl">

                {{-- Close --}}
                <button
                    type="button"
                    data-appointment-create-close
                    aria-label="Close schedule appointment"
                    class="absolute right-4 top-4 z-20 flex h-9 w-9 items-center justify-center rounded-xl text-slate-400 transition hover:bg-background hover:text-primary">

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


<div>

            <div class="card max-h-[calc(100vh-2rem)] overflow-y-auto">

                {{-- Form Header --}}
                <div class="border-b border-border bg-background/60 px-5 py-3.5">

                    <div class="flex items-center gap-3">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-secondary/10 text-secondary">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

                            </svg>

                        </div>


                        <div>

                            <h3 class="font-heading text-base font-semibold text-primary">
                                Schedule Appointment
                            </h3>

                        </div>

                    </div>

                </div>


                <form
                    method="POST"
                    action="{{ route('appointments.store') }}"
                    class="space-y-4 p-5">

                    @csrf

                    <input
                        type="hidden"
                        name="_appointment_modal_context"
                        value="create">


                    {{-- Visitor Name --}}
                    <div>

                        <label for="visitor_name" class="label">
                            Visitor Name
                            <span class="text-error">*</span>
                        </label>

                        <div class="relative">

                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8z" />

                                </svg>

                            </div>

                            <input
                                id="visitor_name"
                                type="text"
                                name="visitor_name"
                                value="{{ old('visitor_name') }}"
                                required
                                placeholder="Full name of visitor"
                                class="input pl-10 @error('visitor_name') border-error focus:border-error focus:ring-error/20 @enderror">

                        </div>

                        @error('visitor_name')
                            <p class="mt-1.5 text-xs font-medium text-error">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Organization + Visitor Type --}}
                    <div class="grid gap-4 sm:grid-cols-2">

                        <div>

                            <label for="visitor_organization" class="label">
                                Organization
                            </label>

                            <input
                                id="visitor_organization"
                                type="text"
                                name="visitor_organization"
                                value="{{ old('visitor_organization') }}"
                                placeholder="Company or organization"
                                class="input @error('visitor_organization') border-error focus:border-error focus:ring-error/20 @enderror">

                            @error('visitor_organization')
                                <p class="mt-1.5 text-xs font-medium text-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        <div>

                            <label for="visitor_type" class="label">
                                Visitor Type
                            </label>

                            <select
                                id="visitor_type"
                                name="visitor_type"
                                class="input @error('visitor_type') border-error focus:border-error focus:ring-error/20 @enderror">

                                @foreach ([
                                    'customer',
                                    'business_partner',
                                    'supplier',
                                    'government',
                                    'applicant',
                                    'guest',
                                    'other'
                                ] as $type)

                                    <option
                                        value="{{ $type }}"
                                        @selected(old('visitor_type', 'guest') === $type)>

                                        {{ str($type)->headline() }}

                                    </option>

                                @endforeach

                            </select>

                            @error('visitor_type')
                                <p class="mt-1.5 text-xs font-medium text-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>


                    {{-- Host + Facility --}}
                    <div class="grid gap-4 sm:grid-cols-2">

                        <div>

                            <label for="host_email" class="label">
                                Host Email
                            </label>

                            <div class="relative">

                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />

                                    </svg>

                                </div>

                                <input
                                    id="host_email"
                                    type="email"
                                    name="host_email"
                                    value="{{ old('host_email') }}"
                                    placeholder="host@example.com"
                                    class="input pl-10 @error('host_email') border-error focus:border-error focus:ring-error/20 @enderror">

                            </div>

                            @error('host_email')
                                <p class="mt-1.5 text-xs font-medium text-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        <div>

                            <label for="facility_id" class="label">
                                Room / Facility
                            </label>

                            <select
                                id="facility_id"
                                name="facility_id"
                                class="input @error('facility_id') border-error focus:border-error focus:ring-error/20 @enderror">

                                <option value="">
                                    None
                                </option>

                                @foreach ($facilities as $facility)

                                    <option
                                        value="{{ $facility->id }}"
                                        @selected(old('facility_id') == $facility->id)>

                                        {{
                                            ctype_digit(
                                                (string) $facility->name
                                            )
                                                ? 'Facility '.$facility->name
                                                : $facility->name
                                        }}

                                    </option>

                                @endforeach

                            </select>

                            @error('facility_id')
                                <p class="mt-1.5 text-xs font-medium text-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>


                    {{-- Schedule --}}
                    <div class="grid gap-4 sm:grid-cols-3">

                        <div>

                            <label for="date" class="label">
                                Date
                                <span class="text-error">*</span>
                            </label>

                            <input
                                id="date"
                                type="date"
                                name="date"
                                value="{{ old('date') }}"
                                required
                                class="input @error('date') border-error focus:border-error focus:ring-error/20 @enderror">

                            @error('date')
                                <p class="mt-1.5 text-xs font-medium text-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        <div>

                            <label for="start_time" class="label">
                                Start
                                <span class="text-error">*</span>
                            </label>

                            <input
                                id="start_time"
                                type="time"
                                name="start_time"
                                value="{{ old('start_time') }}"
                                required
                                class="input @error('start_time') border-error focus:border-error focus:ring-error/20 @enderror">

                            @error('start_time')
                                <p class="mt-1.5 text-xs font-medium text-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        <div>

                            <label for="end_time" class="label">
                                End
                            </label>

                            <input
                                id="end_time"
                                type="time"
                                name="end_time"
                                value="{{ old('end_time') }}"
                                class="input @error('end_time') border-error focus:border-error focus:ring-error/20 @enderror">

                            @error('end_time')
                                <p class="mt-1.5 text-xs font-medium text-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>

                    <input
                        type="hidden"
                        name="status"
                        value="scheduled">


                    <button
                        type="submit"
                        class="btn-primary ml-auto inline-flex">

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

                        Schedule Appointment

                    </button>

                </form>

            </div>

        </div>

            </div>

        </div>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const modal =
                document.querySelector(
                    '[data-appointment-create-modal]'
                );

            const openButton =
                document.querySelector(
                    '[data-appointment-create-open]'
                );

            const closeButtons =
                document.querySelectorAll(
                    '[data-appointment-create-close]'
                );

            const backdrop =
                document.querySelector(
                    '[data-appointment-create-backdrop]'
                );


            if (!modal || !openButton) {
                return;
            }


            let previousOverflow = '';


            const openModal = () => {

                previousOverflow =
                    document.body.style.overflow;

                modal.classList.remove('hidden');

                modal.setAttribute(
                    'aria-hidden',
                    'false'
                );

                document.body.style.overflow =
                    'hidden';


                window.requestAnimationFrame(
                    () => {

                        modal
                            .querySelector(
                                'input:not([type="hidden"]), select, textarea'
                            )
                            ?.focus();

                    }
                );
            };


            const closeModal = () => {

                modal.classList.add('hidden');

                modal.setAttribute(
                    'aria-hidden',
                    'true'
                );

                document.body.style.overflow =
                    previousOverflow;

                openButton.focus();
            };


            openButton.addEventListener(
                'click',
                openModal
            );


            closeButtons.forEach(
                (button) => {

                    button.addEventListener(
                        'click',
                        closeModal
                    );
                }
            );


            backdrop?.addEventListener(
                'click',
                closeModal
            );


            document.addEventListener(
                'keydown',
                (event) => {

                    if (
                        event.key === 'Escape'
                        &&
                        !modal.classList.contains(
                            'hidden'
                        )
                    ) {
                        closeModal();
                    }
                }
            );


            const reopenForValidation =
                {{ \Illuminate\Support\Js::from(
                    $errors->any()
                    && old('_appointment_modal_context') === 'create'
                ) }};


            if (reopenForValidation) {
                openModal();
            }

        });
    </script>

@endcan


    @include('appointments._details-modal')
    @include('appointments._edit-modal')
    @include('appointments._actions-menu')

@endsection