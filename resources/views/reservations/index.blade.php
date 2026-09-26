@extends('layouts.app')

@section('title', 'Facilities Reservation')

@section('content')

<div class="space-y-5">

    @php
        $reservationWorkspaceDescription =
            $canDecide
                ? 'Review and manage facility reservation requests.'
                : 'Reserve facilities and track your requests.';

    @endphp


    {{-- =====================================================
         RESERVATIONS WORKSPACE HEADER
    ====================================================== --}}
    <x-page-header
        title="Reservations"
        :description="$reservationWorkspaceDescription">

        <x-slot:actions>

            @if ($facilities->isNotEmpty())

                <button
                    type="button"
                    data-reservation-create-open
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

                    Reserve Facility

                </button>

            @else

                <a
                    href="{{ route('facilities.index') }}"
                    class="btn-outline inline-flex items-center gap-2">

                    Browse Facilities

                </a>

            @endif

        </x-slot:actions>

    </x-page-header>


    {{-- =====================================================
         RESERVATION OVERVIEW
    ====================================================== --}}
    <section class="space-y-4">

                <x-section-header title="Reservation Status" />


        <div class="grid gap-3 sm:grid-cols-3">




            <x-metric-card
                label="Pending"
                :show-action="false"
                :value="number_format($counts['pending'])"
                :href="route('reservations.index', ['status' => 'pending'])"
                :helper="$canDecide
                    ? 'Requests currently waiting for a reservation decision.'
                    : 'Your requests currently waiting for review.'"
                tone="warning">

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
                            d="M12 8v4l3 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Approved"
                :show-action="false"
                :value="number_format($counts['approved'])"
                :href="route('reservations.index', ['status' => 'approved'])"
                helper="Approved for use."
                tone="success">

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
                            d="M5 13l4 4L19 7" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Completed"
                :show-action="false"
                :value="number_format($counts['completed'])"
                :href="route('reservations.index', ['status' => 'completed'])"
                helper="Completed reservations."
                tone="accent">

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
                            d="M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>

        </div>


    </section>

    {{-- =====================================================
         SHARED FACILITY AVAILABILITY
    ====================================================== --}}
    @include('reservations._facility-usage')


    {{-- =====================================================
         RESERVATION WORKSPACE
    ====================================================== --}}
    <div class="card overflow-visible">

        {{-- Workspace heading --}}
        <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2 class="font-heading text-base font-semibold text-primary">
                    {{ $canDecide ? 'Reservation Requests' : 'My Reservations' }}
                </h2>

                <p class="mt-1 text-xs text-slate-500">

                    @if ($reservations->total())

                        Showing
                        {{ number_format($reservations->firstItem()) }}
                        –
                        {{ number_format($reservations->lastItem()) }}
                        of
                        {{ number_format($reservations->total()) }}

                    @else

                        No reservations match the current view

                    @endif

                </p>

            </div>


            @if (
                request()->filled('search')
                ||
                request()->filled('status')
                ||
                request()->filled('facility')
                ||
                request()->filled('date')
            )

                <a
                    href="{{ route('reservations.index') }}"
                    class="inline-flex items-center gap-1.5 self-start text-xs font-semibold text-slate-500 transition hover:text-primary sm:self-auto">

                    <svg
                        class="h-3.5 w-3.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />

                    </svg>

                    Clear filters

                </a>

            @endif

        </div>


        {{-- Compact filter toolbar --}}
        <form
            method="GET"
            action="{{ route('reservations.index') }}"
            class="border-t border-border bg-background/35 px-5 py-4">

            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[minmax(240px,1.6fr)_180px_220px_170px_auto]">

                {{-- Search --}}
                <div class="relative">

                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">

                        <svg
                            class="h-4 w-4 text-slate-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />

                        </svg>

                    </div>

                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search reservations..."
                        class="input pl-9">

                </div>


                {{-- Status --}}
                <select
                    name="status"
                    class="input"
                    aria-label="Reservation status">

                    <option value="">
                        All statuses
                    </option>

                    @foreach ([
                        'pending',
                        'approved',
                        'rejected',
                        'cancelled',
                        'completed'
                    ] as $status)

                        <option
                            value="{{ $status }}"
                            @selected(request('status') === $status)>

                            {{ str($status)->headline() }}

                        </option>

                    @endforeach

                </select>


                {{-- Facility --}}
                <select
                    name="facility"
                    class="input"
                    aria-label="Facility">

                    <option value="">
                        All facilities
                    </option>

                    @foreach ($filterFacilities as $facility)

                        <option
                            value="{{ $facility->id }}"
                            @selected(
                                (string) request('facility') ===
                                (string) $facility->id
                            )>

                            {{ $facility->name }}

                        </option>

                    @endforeach

                </select>


                {{-- Date --}}
                <input
                    type="date"
                    name="date"
                    value="{{ request('date') }}"
                    class="input"
                    aria-label="Reservation date">


                {{-- Apply --}}
                <button
                    type="submit"
                    class="btn-primary justify-center whitespace-nowrap">

                    Apply

                </button>

            </div>

        </form>

    </div>

    @include('reservations._details-modal')
    @include('reservations._actions-menu')

    @include('reservations._mobile-cards')




    @if ($reservations->hasPages())

        <div class="card px-5 py-4">
            {{ $reservations->links() }}
        </div>

    @endif




    {{-- =====================================================
         EDIT / RESUBMIT RESERVATION MODAL
    ====================================================== --}}
    <div
        data-reservation-edit-modal
        class="fixed inset-0 z-[90] hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="edit-reservation-title">

        <div
            data-reservation-edit-backdrop
            class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
        </div>


        <div class="relative flex min-h-full items-center justify-center p-3 sm:p-6">

            <div class="flex max-h-[calc(100vh-1.5rem)] w-full max-w-xl flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-2xl sm:max-h-[calc(100vh-3rem)]">

                {{-- Header --}}
                <div class="flex shrink-0 items-start justify-between gap-4 border-b border-border px-5 py-4 sm:px-6">

                    <div class="flex items-start gap-3">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-accent/10 text-primary">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 13H9v-2.828l6.586-6.586z" />

                            </svg>

                        </div>


                        <div>

                            <h2
                                id="edit-reservation-title"
                                class="font-heading text-lg font-semibold text-primary">

                                Edit Reservation Request

                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Update the request and submit it for approval again.
                            </p>

                        </div>

                    </div>


                    <button
                        type="button"
                        data-reservation-edit-close
                        aria-label="Close edit reservation"
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
                    data-reservation-edit-form
                    data-submit-loading
                    data-loading-text="Resubmitting..."
                    method="POST"
                    action=""
                    class="flex min-h-0 flex-1 flex-col">

                    @csrf
                    @method('PUT')

                    <input
                        type="hidden"
                        name="_reservation_modal_context"
                        value="edit">

                    <input
                        type="hidden"
                        name="_reservation_edit_id"
                        data-edit-reservation-id>

                    <input
                        type="hidden"
                        name="facility_id"
                        data-edit-facility-id>


                    <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6">

                        @if (
                            $errors->any()
                            &&
                            old('_reservation_modal_context') === 'edit'
                        )

                            <div class="mb-5 rounded-xl border border-error/20 bg-error/5 p-4">

                                <p class="text-sm font-semibold text-error">
                                    Please correct the following:
                                </p>

                                <ul class="mt-2 list-disc space-y-1 pl-5 text-xs text-error">

                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach

                                </ul>

                            </div>

                        @endif


                        {{-- Rejection reason --}}
                        <div
                            data-edit-rejection-box
                            class="mb-5 hidden rounded-xl border border-error/20 bg-error/5 p-4">

                            <p class="text-xs font-semibold uppercase tracking-wide text-error">
                                Previous rejection reason
                            </p>

                            <p
                                data-edit-rejection-note
                                class="mt-1.5 text-sm leading-relaxed text-slate-600">
                            </p>

                        </div>


                        {{-- Fixed facility --}}
                        <div class="mb-6 rounded-2xl border border-accent/20 bg-accent/5 p-4">

                            <div class="flex items-start justify-between gap-4">

                                <div>

                                    <p
                                        data-edit-facility-name
                                        class="font-heading text-base font-semibold text-primary">
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">

                                        <span data-edit-facility-type></span>

                                        <span
                                            data-edit-capacity-separator
                                            class="mx-1 text-slate-300">

                                            •

                                        </span>

                                        <span data-edit-facility-capacity></span>

                                    </p>

                                </div>


                                <span
                                    data-edit-facility-status
                                    class="rounded-full bg-success/10 px-2.5 py-1 text-[11px] font-semibold text-success">
                                </span>

                            </div>


                            <p class="mt-3 text-xs text-slate-400">
                                The facility cannot be changed while editing this request.
                            </p>

                        </div>


                        <div class="space-y-5">

                            {{-- Date --}}
                            <div>

                                <label
                                    for="edit_reservation_date"
                                    class="label">

                                    Reservation Date
                                    <span class="text-error">*</span>

                                </label>

                                <input
                                    id="edit_reservation_date"
                                    data-edit-date
                                    type="date"
                                    name="date"
                                    min="{{ now()->toDateString() }}"
                                    required
                                    class="input">

                            </div>


                            {{-- Times --}}
                            <div>

                                <label class="label">
                                    Reservation Time
                                    <span class="text-error">*</span>
                                </label>

                                <div class="grid gap-4 sm:grid-cols-2">

                                    <div>

                                        <label
                                            for="edit_reservation_start"
                                            class="mb-1.5 block text-xs font-medium text-slate-500">

                                            Start

                                        </label>

                                        <input
                                            id="edit_reservation_start"
                                            data-edit-start
                                            type="time"
                                            name="start_time"
                                            required
                                            class="input">

                                    </div>


                                    <div>

                                        <label
                                            for="edit_reservation_end"
                                            class="mb-1.5 block text-xs font-medium text-slate-500">

                                            End

                                        </label>

                                        <input
                                            id="edit_reservation_end"
                                            data-edit-end
                                            type="time"
                                            name="end_time"
                                            required
                                            class="input">

                                    </div>

                                </div>

                            </div>


                            {{-- Attendees / passengers --}}
                            <div>

                                <label
                                    for="edit_reservation_attendees"
                                    class="label">

                                    <span data-edit-attendees-label>
                                        Number of Attendees
                                    </span>

                                    <span class="text-error">*</span>

                                </label>

                                <input
                                    id="edit_reservation_attendees"
                                    data-edit-attendees
                                    type="number"
                                    name="attendees"
                                    min="1"
                                    required
                                    class="input">

                                <p
                                    data-edit-capacity-help
                                    class="mt-1.5 text-xs text-slate-400">
                                </p>

                            </div>


                            {{-- Purpose --}}
                            <div>

                                <label
                                    for="edit_reservation_purpose"
                                    class="label">

                                    Purpose
                                    <span class="text-error">*</span>

                                </label>

                                <textarea
                                    id="edit_reservation_purpose"
                                    data-edit-purpose
                                    name="purpose"
                                    rows="3"
                                    maxlength="2000"
                                    required
                                    placeholder="Describe the purpose of this reservation..."
                                    class="input"></textarea>

                            </div>

                        </div>

                    </div>


                    {{-- Only one primary footer action --}}
                    <div class="flex shrink-0 items-center justify-between gap-4 border-t border-border bg-background/50 px-5 py-4 sm:px-6">

                        <p class="hidden text-xs text-slate-400 sm:block">
                            The revised request will return to Pending approval.
                        </p>

                        <button
                            type="submit"
                            class="btn-primary ml-auto inline-flex items-center justify-center gap-2">

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

                            Resubmit for Approval

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal =
            document.querySelector(
                '[data-reservation-edit-modal]'
            );

        if (!modal) {
            return;
        }

        const form =
            modal.querySelector(
                '[data-reservation-edit-form]'
            );

        const triggers =
            Array.from(
                document.querySelectorAll(
                    '[data-reservation-edit-open]'
                )
            );

        const closeButtons =
            modal.querySelectorAll(
                '[data-reservation-edit-close]'
            );

        const backdrop =
            modal.querySelector(
                '[data-reservation-edit-backdrop]'
            );

        const idInput =
            modal.querySelector(
                '[data-edit-reservation-id]'
            );

        const facilityIdInput =
            modal.querySelector(
                '[data-edit-facility-id]'
            );

        const facilityName =
            modal.querySelector(
                '[data-edit-facility-name]'
            );

        const facilityType =
            modal.querySelector(
                '[data-edit-facility-type]'
            );

        const facilityCapacity =
            modal.querySelector(
                '[data-edit-facility-capacity]'
            );

        const facilityStatus =
            modal.querySelector(
                '[data-edit-facility-status]'
            );

        const capacitySeparator =
            modal.querySelector(
                '[data-edit-capacity-separator]'
            );

        const capacityHelp =
            modal.querySelector(
                '[data-edit-capacity-help]'
            );

        const attendeesLabel =
            modal.querySelector(
                '[data-edit-attendees-label]'
            );

        const dateInput =
            modal.querySelector(
                '[data-edit-date]'
            );

        const startInput =
            modal.querySelector(
                '[data-edit-start]'
            );

        const endInput =
            modal.querySelector(
                '[data-edit-end]'
            );

        const attendeesInput =
            modal.querySelector(
                '[data-edit-attendees]'
            );

        const purposeInput =
            modal.querySelector(
                '[data-edit-purpose]'
            );

        const rejectionBox =
            modal.querySelector(
                '[data-edit-rejection-box]'
            );

        const rejectionNote =
            modal.querySelector(
                '[data-edit-rejection-note]'
            );

        let previouslyFocused = null;
        let previousBodyOverflow = '';


        const headline = (value) => {
            if (!value) {
                return 'Facility';
            }

            return value
                .replaceAll('_', ' ')
                .replace(
                    /\b\w/g,
                    (letter) => letter.toUpperCase()
                );
        };


        const populate = (
            trigger,
            oldValues = null
        ) => {
            const type =
                trigger.dataset.reservationFacilityType || '';

            const capacity =
                trigger.dataset.reservationCapacity || '';

            const isVehicle =
                type === 'vehicle';

            const value = (
                key,
                fallback
            ) => {
                if (
                    oldValues
                    &&
                    oldValues[key] !== null
                    &&
                    oldValues[key] !== undefined
                ) {
                    return oldValues[key];
                }

                return fallback;
            };


            form.action =
                trigger.dataset.reservationUpdateUrl;

            idInput.value =
                trigger.dataset.reservationId;

            facilityIdInput.value =
                trigger.dataset.reservationFacilityId;

            facilityName.textContent =
                trigger.dataset.reservationFacility;

            facilityType.textContent =
                headline(type);

            facilityStatus.textContent =
                headline(
                    trigger.dataset.reservationFacilityStatus
                    || 'unknown'
                );


            if (capacity !== '') {

                facilityCapacity.textContent =
                    `${capacity} ${
                        isVehicle
                            ? 'passengers'
                            : 'people'
                    }`;

                capacitySeparator.classList.remove(
                    'hidden'
                );

                capacityHelp.textContent =
                    `Maximum capacity: ${capacity} ${
                        isVehicle
                            ? 'passengers'
                            : 'people'
                    }.`;

                attendeesInput.max =
                    capacity;

            } else {

                facilityCapacity.textContent =
                    '';

                capacitySeparator.classList.add(
                    'hidden'
                );

                capacityHelp.textContent =
                    '';

                attendeesInput.removeAttribute(
                    'max'
                );
            }


            attendeesLabel.textContent =
                isVehicle
                    ? 'Number of Passengers'
                    : 'Number of Attendees';


            dateInput.value =
                value(
                    'date',
                    trigger.dataset.reservationDate
                );

            startInput.value =
                value(
                    'start_time',
                    trigger.dataset.reservationStart
                );

            endInput.value =
                value(
                    'end_time',
                    trigger.dataset.reservationEnd
                );

            attendeesInput.value =
                value(
                    'attendees',
                    trigger.dataset.reservationAttendees
                );

            purposeInput.value =
                value(
                    'purpose',
                    trigger.dataset.reservationPurpose
                );


            const note =
                trigger.dataset.reservationNote || '';

            if (note.trim() !== '') {

                rejectionNote.textContent =
                    note;

                rejectionBox.classList.remove(
                    'hidden'
                );

            } else {

                rejectionNote.textContent =
                    '';

                rejectionBox.classList.add(
                    'hidden'
                );
            }
        };


        const openModal = (
            trigger,
            oldValues = null
        ) => {
            populate(
                trigger,
                oldValues
            );

            previouslyFocused =
                document.activeElement;

            previousBodyOverflow =
                document.body.style.overflow;

            modal.classList.remove(
                'fams-modal-closing'
            );

            modal.classList.remove(
                'hidden'
            );

            document.body.style.overflow =
                'hidden';

            window.requestAnimationFrame(
                () => {
                    dateInput?.focus();
                }
            );
        };


        const closeModal = () => {

            if (
                modal.classList.contains(
                    'fams-modal-closing'
                )
            ) {
                return;
            }


            const finishClose = () => {

                modal.classList.add(
                    'hidden'
                );

                modal.classList.remove(
                    'fams-modal-closing'
                );

                document.body.style.overflow =
                    previousBodyOverflow;

                if (
                    previouslyFocused
                    &&
                    typeof previouslyFocused.focus ===
                        'function'
                ) {
                    previouslyFocused.focus();
                }
            };


            if (
                window.matchMedia(
                    '(prefers-reduced-motion: reduce)'
                ).matches
            ) {

                finishClose();
                return;
            }


            modal.classList.add(
                'fams-modal-closing'
            );

            window.setTimeout(
                finishClose,
                190
            );
        };


        triggers.forEach((trigger) => {
            trigger.addEventListener(
                'click',
                () => openModal(trigger)
            );
        });


        closeButtons.forEach((button) => {
            button.addEventListener(
                'click',
                closeModal
            );
        });


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


        const oldEditValues = {{ \Illuminate\Support\Js::from([
            'context' => old('_reservation_modal_context'),
            'id' => old('_reservation_edit_id'),
            'date' => old('date'),
            'start_time' => old('start_time'),
            'end_time' => old('end_time'),
            'attendees' => old('attendees'),
            'purpose' => old('purpose'),
        ]) }};


        if (
            oldEditValues.context === 'edit'
            &&
            oldEditValues.id
        ) {

            const trigger =
                triggers.find(
                    (item) =>
                        String(
                            item.dataset.reservationId
                        ) ===
                        String(
                            oldEditValues.id
                        )
                );

            if (trigger) {
                openModal(
                    trigger,
                    oldEditValues
                );
            }
        }
    });
    </script>

    {{-- =====================================================
     CREATE RESERVATION MODAL
====================================================== --}}
<div
    data-reservation-create-modal
    @if (
        ($errors->any() && old('_reservation_modal_context') === 'create')
        || request()->filled('reserve_facility')
    )
        data-open-reservation="true"
    @endif
    class="fixed inset-0 z-[80] hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="reserve-facility-title">

    <div
        data-reservation-create-backdrop
        class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
    </div>


    <div class="relative flex min-h-full items-center justify-center p-3 sm:p-6">

        <div class="flex max-h-[calc(100vh-1.5rem)] w-full max-w-xl flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-2xl sm:max-h-[calc(100vh-3rem)]">

            {{-- Header --}}
            <div class="flex shrink-0 items-center justify-between gap-4 border-b border-border px-5 py-4 sm:px-6">

                <div>

                    <h2
                        id="reserve-facility-title"
                        class="font-heading text-lg font-semibold text-primary">

                        Reserve Facility

                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Select a facility and complete the reservation details.
                    </p>

                </div>


                <button
                    type="button"
                    data-reservation-create-close
                    aria-label="Close reservation"
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
                action="{{ route('reservations.store') }}"
                data-submit-loading
                data-loading-text="Submitting..."
                class="flex min-h-0 flex-1 flex-col">

                @csrf

                <input
                    type="hidden"
                    name="_reservation_modal_context"
                    value="create">


                <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6">

                    @if (
                        $errors->any()
                        && old('_reservation_modal_context') === 'create'
                    )

                        <div class="mb-5 rounded-xl border border-error/20 bg-error/5 p-4">

                            <p class="text-sm font-semibold text-error">
                                Please check the reservation details.
                            </p>

                            <ul class="mt-2 list-disc space-y-1 pl-5 text-xs text-error">

                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach

                            </ul>

                        </div>

                    @endif


                    <div class="space-y-5">

                        {{-- Facility --}}
                        <div
                            data-reservation-facility-picker
                            class="relative">

                            <label
                                for="reservation_facility_search"
                                class="label">

                                Facility
                                <span class="text-error">*</span>

                            </label>


                            <input
                                type="hidden"
                                name="facility_id"
                                data-reservation-facility-id
                                value="{{ old('facility_id', request('reserve_facility')) }}">


                            <div class="relative">

                                <svg
                                    class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />

                                </svg>


                                <input
                                    id="reservation_facility_search"
                                    data-reservation-facility-search
                                    type="search"
                                    autocomplete="off"
                                    role="combobox"
                                    aria-autocomplete="list"
                                    aria-expanded="false"
                                    aria-controls="reservation_facility_results"
                                    placeholder="Search or choose a facility..."
                                    class="input pl-10 pr-10">


                                <svg
                                    class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />

                                </svg>

                            </div>


                            <div
                                id="reservation_facility_results"
                                data-reservation-facility-results
                                role="listbox"
                                class="absolute z-20 mt-1 hidden max-h-60 w-full overflow-y-auto rounded-xl border border-border bg-card p-1.5 shadow-xl">

                                @foreach ($facilities as $facility)

                                    @php
                                        $facilityDisplayName =
                                            ctype_digit(
                                                (string) $facility->name
                                            )
                                                ? 'Facility '.$facility->name
                                                : $facility->name;

                                        $facilityTypeLabel =
                                            str(
                                                $facility->facility_type
                                            )->headline();

                                        $facilitySearchText =
                                            strtolower(
                                                trim(
                                                    $facilityDisplayName
                                                    .' '
                                                    .$facilityTypeLabel
                                                    .' '
                                                    .($facility->location ?? '')
                                                )
                                            );
                                    @endphp


                                    <button
                                        type="button"
                                        role="option"
                                        data-reservation-facility-option
                                        data-facility-id="{{ $facility->id }}"
                                        data-facility-name="{{ $facilityDisplayName }}"
                                        data-facility-type="{{ $facility->facility_type }}"
                                        data-facility-type-label="{{ $facilityTypeLabel }}"
                                        data-facility-capacity="{{ $facility->capacity }}"
                                        data-facility-location="{{ $facility->location }}"
                                        data-facility-search="{{ $facilitySearchText }}"
                                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left transition hover:bg-background">

                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary/5 text-primary">

                                            <svg
                                                class="h-4 w-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24">

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5" />

                                            </svg>

                                        </span>


                                        <span class="min-w-0 flex-1">

                                            <span class="block truncate text-sm font-semibold text-primary">
                                                {{ $facilityDisplayName }}
                                            </span>

                                            <span class="mt-0.5 block truncate text-[11px] text-slate-400">

                                                {{ $facilityTypeLabel }}

                                                @if ($facility->location)
                                                    <span class="mx-1">•</span>
                                                    {{ $facility->location }}
                                                @endif

                                            </span>

                                        </span>

                                    </button>

                                @endforeach


                                <div
                                    data-reservation-facility-empty
                                    class="hidden px-3 py-6 text-center">

                                    <p class="text-sm font-semibold text-primary">
                                        No facilities found
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Try another facility name, type, or location.
                                    </p>

                                </div>

                            </div>


                            <p
                                data-reservation-facility-meta
                                class="mt-1.5 min-h-4 text-xs text-slate-400"
                                aria-live="polite">
                            </p>

                        </div>

                        {{-- Date --}}
                        <div>

                            <label
                                for="reservation_date"
                                class="label">

                                Reservation Date
                                <span class="text-error">*</span>

                            </label>

                            <input
                                id="reservation_date"
                                type="date"
                                name="date"
                                min="{{ now()->toDateString() }}"
                                value="{{ old('date', request('reserve_date')) }}"
                                required
                                class="input @error('date') border-error focus:border-error focus:ring-error/20 @enderror">

                        </div>


                        {{-- Time --}}
                        <div>

                            <label class="label">
                                Reservation Time
                                <span class="text-error">*</span>
                            </label>

                            <div class="grid gap-4 sm:grid-cols-2">

                                <div>

                                    <label
                                        for="reservation_start_time"
                                        class="mb-1.5 block text-xs font-medium text-slate-500">

                                        Start

                                    </label>

                                    <input
                                        id="reservation_start_time"
                                        type="time"
                                        name="start_time"
                                        value="{{ old('start_time') }}"
                                        required
                                        class="input @error('start_time') border-error focus:border-error focus:ring-error/20 @enderror">

                                </div>


                                <div>

                                    <label
                                        for="reservation_end_time"
                                        class="mb-1.5 block text-xs font-medium text-slate-500">

                                        End

                                    </label>

                                    <input
                                        id="reservation_end_time"
                                        type="time"
                                        name="end_time"
                                        value="{{ old('end_time') }}"
                                        required
                                        class="input @error('end_time') border-error focus:border-error focus:ring-error/20 @enderror">

                                </div>

                            </div>

                        </div>


                        {{-- Attendees --}}
                        <div>

                            <label
                                for="reservation_attendees"
                                class="label">

                                <span data-reservation-attendees-label>
                                    Number of Attendees
                                </span>

                                <span class="text-error">*</span>

                            </label>

                            <input
                                id="reservation_attendees"
                                data-reservation-attendees
                                type="number"
                                name="attendees"
                                min="1"
                                required
                                value="{{ old('attendees') }}"
                                class="input @error('attendees') border-error focus:border-error focus:ring-error/20 @enderror">

                            <p
                                data-reservation-capacity-help
                                class="mt-1.5 text-xs text-slate-400">
                            </p>

                        </div>


                        {{-- Purpose --}}
                        <div>

                            <label
                                for="reservation_purpose"
                                class="label">

                                Purpose
                                <span class="text-error">*</span>

                            </label>

                            <textarea
                                id="reservation_purpose"
                                name="purpose"
                                rows="3"
                                maxlength="2000"
                                required
                                placeholder="Briefly describe the purpose of this reservation..."
                                class="input @error('purpose') border-error focus:border-error focus:ring-error/20 @enderror">{{ old('purpose') }}</textarea>

                        </div>

                    </div>

                </div>


                <div class="flex shrink-0 items-center justify-end border-t border-border bg-background/50 px-5 py-4 sm:px-6">

                    <button
                        type="submit"
                        data-reservation-create-submit
                        class="btn-primary inline-flex items-center justify-center gap-2 disabled:cursor-not-allowed disabled:opacity-50">

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

                        Submit Reservation

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', () => {

    const modal =
        document.querySelector(
            '[data-reservation-create-modal]'
        );

    if (!modal) {
        return;
    }


    const openButtons =
        document.querySelectorAll(
            '[data-reservation-create-open]'
        );

    const closeButtons =
        modal.querySelectorAll(
            '[data-reservation-create-close]'
        );

    const backdrop =
        modal.querySelector(
            '[data-reservation-create-backdrop]'
        );

    const picker =
        modal.querySelector(
            '[data-reservation-facility-picker]'
        );

    const searchInput =
        modal.querySelector(
            '[data-reservation-facility-search]'
        );

    const facilityId =
        modal.querySelector(
            '[data-reservation-facility-id]'
        );

    const results =
        modal.querySelector(
            '[data-reservation-facility-results]'
        );

    const emptyState =
        modal.querySelector(
            '[data-reservation-facility-empty]'
        );

    const optionButtons =
        Array.from(
            modal.querySelectorAll(
                '[data-reservation-facility-option]'
            )
        );

    const facilityMeta =
        modal.querySelector(
            '[data-reservation-facility-meta]'
        );

    const attendeesLabel =
        modal.querySelector(
            '[data-reservation-attendees-label]'
        );

    const attendeesInput =
        modal.querySelector(
            '[data-reservation-attendees]'
        );

    const capacityHelp =
        modal.querySelector(
            '[data-reservation-capacity-help]'
        );

    const submitButton =
        modal.querySelector(
            '[data-reservation-create-submit]'
        );


    let selectedOption = null;
    let previouslyFocused = null;
    let previousBodyOverflow = '';


    const setExpanded =
        (expanded) => {

            results?.classList.toggle(
                'hidden',
                !expanded
            );

            searchInput?.setAttribute(
                'aria-expanded',
                expanded
                    ? 'true'
                    : 'false'
            );

        };


    const clearFacilityDetails =
        () => {

            facilityId.value = '';

            selectedOption = null;

            facilityMeta.textContent =
                'Select an available facility.';

            attendeesLabel.textContent =
                'Number of Attendees';

            capacityHelp.textContent = '';

            attendeesInput.removeAttribute(
                'max'
            );

            submitButton.disabled = true;

        };


    const selectFacility =
        (
            option,
            closeResults = true
        ) => {

            if (!option) {
                clearFacilityDetails();
                return;
            }


            selectedOption = option;

            facilityId.value =
                option.dataset.facilityId || '';

            searchInput.value =
                option.dataset.facilityName || '';

            const type =
                option.dataset.facilityType || '';

            const typeLabel =
                option.dataset.facilityTypeLabel || '';

            const capacity =
                option.dataset.facilityCapacity || '';

            const location =
                option.dataset.facilityLocation || '';

            const isVehicle =
                type === 'vehicle';


            attendeesLabel.textContent =
                isVehicle
                    ? 'Number of Passengers'
                    : 'Number of Attendees';


            const details = [];

            if (typeLabel) {
                details.push(typeLabel);
            }

            if (location) {
                details.push(location);
            }

            if (capacity) {

                details.push(
                    `Capacity ${capacity} ${
                        isVehicle
                            ? 'passengers'
                            : 'people'
                    }`
                );

                attendeesInput.max =
                    capacity;

                capacityHelp.textContent =
                    `Maximum capacity: ${capacity} ${
                        isVehicle
                            ? 'passengers'
                            : 'people'
                    }.`;

            } else {

                attendeesInput.removeAttribute(
                    'max'
                );

                capacityHelp.textContent = '';

            }


            facilityMeta.textContent =
                details.join(' • ');

            submitButton.disabled = false;


            optionButtons.forEach(
                (button) => {

                    const active =
                        button === option;

                    button.classList.toggle(
                        'bg-accent/10',
                        active
                    );

                    button.setAttribute(
                        'aria-selected',
                        active
                            ? 'true'
                            : 'false'
                    );

                }
            );


            if (closeResults) {
                setExpanded(false);
            }

        };


    const filterFacilities =
        () => {

            const query =
                String(
                    searchInput.value || ''
                )
                    .trim()
                    .toLowerCase();

            let visibleCount = 0;


            optionButtons.forEach(
                (option) => {

                    const matches =
                        query === ''
                        ||
                        String(
                            option.dataset.facilitySearch || ''
                        ).includes(query);

                    option.classList.toggle(
                        'hidden',
                        !matches
                    );

                    if (matches) {
                        visibleCount++;
                    }

                }
            );


            emptyState?.classList.toggle(
                'hidden',
                visibleCount !== 0
            );


            setExpanded(true);

        };


    const initialiseFacility =
        () => {

            const initialId =
                String(
                    facilityId.value || ''
                );

            if (!initialId) {

                clearFacilityDetails();
                return;

            }


            const option =
                optionButtons.find(
                    (button) =>
                        String(
                            button.dataset.facilityId
                        ) === initialId
                );


            if (option) {
                selectFacility(
                    option,
                    true
                );
            }
            else {
                clearFacilityDetails();
            }

        };


    const openModal = () => {

        previouslyFocused =
            document.activeElement;

        previousBodyOverflow =
            document.body.style.overflow;

        modal.classList.remove(
            'fams-modal-closing'
        );

        modal.classList.remove(
            'hidden'
        );

        document.body.style.overflow =
            'hidden';

        initialiseFacility();


        window.requestAnimationFrame(
            () => {

                if (facilityId.value) {

                    modal.querySelector(
                        '#reservation_date'
                    )?.focus();

                }
                else {

                    searchInput?.focus();

                }

            }
        );

    };


    const closeModal = () => {

        if (
            modal.classList.contains(
                'fams-modal-closing'
            )
        ) {
            return;
        }


        setExpanded(false);


        const finishClose = () => {

            modal.classList.add(
                'hidden'
            );

            modal.classList.remove(
                'fams-modal-closing'
            );

            document.body.style.overflow =
                previousBodyOverflow;

            if (
                previouslyFocused
                &&
                typeof previouslyFocused.focus ===
                    'function'
            ) {
                previouslyFocused.focus();
            }

        };


        if (
            window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            ).matches
        ) {

            finishClose();
            return;

        }


        modal.classList.add(
            'fams-modal-closing'
        );

        window.setTimeout(
            finishClose,
            190
        );

    };


    openButtons.forEach(
        (button) => {

            button.addEventListener(
                'click',
                openModal
            );

        }
    );


    optionButtons.forEach(
        (option) => {

            option.addEventListener(
                'click',
                () => {

                    selectFacility(
                        option
                    );

                    modal.querySelector(
                        '#reservation_date'
                    )?.focus();

                }
            );

        }
    );


    searchInput?.addEventListener(
        'focus',
        filterFacilities
    );


    searchInput?.addEventListener(
        'click',
        filterFacilities
    );


    searchInput?.addEventListener(
        'input',
        () => {

            if (
                selectedOption
                &&
                searchInput.value !==
                    selectedOption.dataset.facilityName
            ) {

                clearFacilityDetails();

            }

            filterFacilities();

        }
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
        'click',
        (event) => {

            if (
                !modal.classList.contains('hidden')
                &&
                picker
                &&
                !picker.contains(event.target)
            ) {
                setExpanded(false);
            }

        }
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

                if (
                    results
                    &&
                    !results.classList.contains(
                        'hidden'
                    )
                ) {

                    setExpanded(false);
                    searchInput?.focus();
                    return;

                }

                closeModal();

            }

        }
    );


    initialiseFacility();


    if (
        modal.dataset.openReservation ===
        'true'
    ) {
        openModal();
    }

});
</script>

@endsection