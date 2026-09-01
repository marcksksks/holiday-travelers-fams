@extends('layouts.app')

@section('title', 'Reservations')

@section('content')

<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Facility Reservations
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Request facilities and manage reservation approvals, schedules, and status.
            </p>
        </div>

        <div class="rounded-xl border border-border bg-card px-4 py-2.5 shadow-card">
            <p class="text-xs text-slate-500">
                Total Requests
            </p>

            <p class="mt-0.5 font-heading text-lg font-bold text-primary">
                {{ $reservations->total() }}
            </p>
        </div>

    </div>


    <div class="grid gap-6 xl:grid-cols-[360px_minmax(0,1fr)]">

        {{-- Request Form --}}
        <div>

            <div class="card overflow-hidden xl:sticky xl:top-6">

                {{-- Form Header --}}
                <div class="border-b border-border bg-background/60 px-5 py-5">

                    <div class="flex items-center gap-3">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4" />

                            </svg>

                        </div>

                        <div>
                            <h3 class="font-heading text-base font-semibold text-primary">
                                Request a Facility
                            </h3>

                            <p class="mt-0.5 text-xs text-slate-500">
                                Submit a new reservation request.
                            </p>
                        </div>

                    </div>

                </div>


                <form
                    method="POST"
                    action="{{ route('reservations.store') }}"
                    class="space-y-5 p-5">

                    @csrf


                    {{-- Facility --}}
                    <div>

                        <label for="facility_id" class="label">
                            Facility
                            <span class="text-error">*</span>
                        </label>

                        <select
                            id="facility_id"
                            name="facility_id"
                            required
                            class="input @error('facility_id') border-error focus:border-error focus:ring-error/20 @enderror">

                            @forelse ($facilities as $facility)

                                <option
                                    value="{{ $facility->id }}"
                                    @selected(old('facility_id') == $facility->id)>

                                    {{ $facility->name }} ({{ $facility->capacity }} pax)

                                </option>

                            @empty

                                <option value="" disabled>
                                    No available facilities
                                </option>

                            @endforelse

                        </select>

                        @error('facility_id')
                            <p class="mt-1.5 text-xs font-medium text-error">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Date --}}
                    <div>

                        <label for="date" class="label">
                            Reservation Date
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
                                        d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

                                </svg>

                            </div>

                            <input
                                id="date"
                                type="date"
                                name="date"
                                value="{{ old('date') }}"
                                required
                                class="input pl-10 @error('date') border-error focus:border-error focus:ring-error/20 @enderror">

                        </div>

                        @error('date')
                            <p class="mt-1.5 text-xs font-medium text-error">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Time --}}
                    <div>

                        <label class="label">
                            Reservation Time
                            <span class="text-error">*</span>
                        </label>

                        <div class="grid grid-cols-2 gap-3">

                            <div>
                                <label for="start_time" class="mb-1 block text-xs text-slate-400">
                                    Start
                                </label>

                                <input
                                    id="start_time"
                                    type="time"
                                    name="start_time"
                                    value="{{ old('start_time') }}"
                                    required
                                    class="input @error('start_time') border-error focus:border-error focus:ring-error/20 @enderror">
                            </div>


                            <div>
                                <label for="end_time" class="mb-1 block text-xs text-slate-400">
                                    End
                                </label>

                                <input
                                    id="end_time"
                                    type="time"
                                    name="end_time"
                                    value="{{ old('end_time') }}"
                                    required
                                    class="input @error('end_time') border-error focus:border-error focus:ring-error/20 @enderror">
                            </div>

                        </div>

                        @error('start_time')
                            <p class="mt-1.5 text-xs font-medium text-error">
                                {{ $message }}
                            </p>
                        @enderror

                        @error('end_time')
                            <p class="mt-1.5 text-xs font-medium text-error">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Attendees --}}
                    <div>

                        <label for="attendees" class="label">
                            Number of Attendees
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
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5 5 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />

                                </svg>

                            </div>

                            <input
                                id="attendees"
                                type="number"
                                name="attendees"
                                min="1"
                                value="{{ old('attendees') }}"
                                placeholder="e.g. 10"
                                class="input pl-10 @error('attendees') border-error focus:border-error focus:ring-error/20 @enderror">

                        </div>

                        @error('attendees')
                            <p class="mt-1.5 text-xs font-medium text-error">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Purpose --}}
                    <div>

                        <label for="purpose" class="label">
                            Purpose
                        </label>

                        <textarea
                            id="purpose"
                            name="purpose"
                            rows="3"
                            placeholder="Describe the purpose of this reservation..."
                            class="input @error('purpose') border-error focus:border-error focus:ring-error/20 @enderror">{{ old('purpose') }}</textarea>

                        @error('purpose')
                            <p class="mt-1.5 text-xs font-medium text-error">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    <button
                        type="submit"
                        @disabled($facilities->isEmpty())
                        class="btn-secondary w-full disabled:cursor-not-allowed disabled:opacity-50">

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

                        Submit Request

                    </button>

                </form>

            </div>

        </div>


        {{-- Reservations List --}}
        <div class="min-w-0 space-y-4">

            {{-- List Heading --}}
            <div class="flex items-center justify-between">

                <div>
                    <h3 class="font-heading text-lg font-semibold text-primary">
                        Reservation Requests
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Review facility bookings and their current approval status.
                    </p>
                </div>

            </div>


            <div class="table-shell">

                <div class="overflow-x-auto">

                    <table class="min-w-full text-left text-sm">

                        <thead class="table-header">

                            <tr>
                                <th class="px-5 py-4 font-medium">
                                    Facility
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Schedule
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Requester
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Status
                                </th>

                                <th class="px-5 py-4 text-right font-medium">
                                    Actions
                                </th>
                            </tr>

                        </thead>


                        <tbody class="divide-y divide-border bg-card">

                            @forelse ($reservations as $reservation)

                                <tr class="transition-colors hover:bg-sky-50/40">

                                    {{-- Facility --}}
                                    <td class="px-5 py-4">

                                        <div class="flex items-center gap-3">

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
                                                        d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5" />

                                                </svg>

                                            </div>

                                            <div class="min-w-0">

                                                <p class="max-w-[180px] truncate font-button text-sm font-semibold text-primary">
                                                    {{ $reservation->facility_name }}
                                                </p>

                                                <p class="mt-0.5 text-[11px] text-slate-400">
                                                    Reservation #{{ $reservation->id }}
                                                </p>

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Schedule --}}
                                    <td class="px-5 py-4">

                                        <div class="space-y-1">

                                            <div class="flex items-center gap-2 text-sm font-medium text-slate-700">

                                                <svg
                                                    class="h-4 w-4 shrink-0 text-accent"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24">

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

                                                </svg>

                                                {{ $reservation->date->format('M d, Y') }}

                                            </div>

                                            <div class="flex items-center gap-2 pl-6 text-xs text-slate-500">

                                                <span>
                                                    {{ $reservation->start_time }}
                                                    &ndash;
                                                    {{ $reservation->end_time }}
                                                </span>

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Requester --}}
                                    <td class="px-5 py-4">

                                        <div class="flex items-center gap-2">

                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-accent/10 font-button text-xs font-semibold uppercase text-primary">
                                                {{ \Illuminate\Support\Str::substr($reservation->requester_name, 0, 1) }}
                                            </div>

                                            <div class="min-w-0">

                                                <p class="max-w-[160px] truncate text-sm font-medium text-slate-700">
                                                    {{ $reservation->requester_name }}
                                                </p>

                                                @if ($reservation->requester_email === auth()->user()->email)

                                                    <p class="mt-0.5 text-[10px] font-semibold uppercase tracking-wide text-accent">
                                                        Your request
                                                    </p>

                                                @endif

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Status --}}
                                    <td class="px-5 py-4">

                                        @switch($reservation->status)

                                            @case('pending')

                                                <span class="badge badge-warning">
                                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-warning"></span>
                                                    Pending
                                                </span>

                                                @break


                                            @case('approved')

                                                <span class="badge badge-success">
                                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                                    Approved
                                                </span>

                                                @break


                                            @case('rejected')

                                                <span class="badge badge-error">
                                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-error"></span>
                                                    Rejected
                                                </span>

                                                @break


                                            @case('cancelled')

                                                <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                                    Cancelled
                                                </span>

                                                @break


                                            @case('completed')

                                                <span class="badge badge-info">
                                                    Completed
                                                </span>

                                                @break


                                            @default

                                                <span class="badge badge-info">
                                                    {{ str($reservation->status)->headline() }}
                                                </span>

                                        @endswitch

                                    </td>


                                    {{-- Actions --}}
                                    <td class="px-5 py-4 text-right">

                                        <div class="flex items-center justify-end gap-2">

                                            @can('decideReservations')

                                                @if (
                                                    $reservation->status === 'pending' &&
                                                    $reservation->requester_email !== auth()->user()->email
                                                )

                                                    <form
                                                        method="POST"
                                                        action="{{ route('reservations.decide', $reservation) }}">

                                                        @csrf

                                                        <input
                                                            type="hidden"
                                                            name="decision"
                                                            value="approved">

                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center gap-1 rounded-lg bg-success/10 px-3 py-2 font-button text-xs font-semibold text-success transition hover:bg-success hover:text-white">

                                                            <svg
                                                                class="h-3.5 w-3.5"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24">

                                                                <path
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M5 13l4 4L19 7" />

                                                            </svg>

                                                            Approve

                                                        </button>

                                                    </form>


                                                    <form
                                                        method="POST"
                                                        action="{{ route('reservations.decide', $reservation) }}"
                                                        onsubmit="return confirm('Reject this reservation request?');">

                                                        @csrf

                                                        <input
                                                            type="hidden"
                                                            name="decision"
                                                            value="rejected">

                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center gap-1 rounded-lg bg-error/10 px-3 py-2 font-button text-xs font-semibold text-error transition hover:bg-error hover:text-white">

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

                                                            Reject

                                                        </button>

                                                    </form>

                                                @endif

                                            @endcan


                                            @if (
                                                $reservation->status === 'pending' &&
                                                $reservation->requester_email === auth()->user()->email
                                            )

                                                <form
                                                    method="POST"
                                                    action="{{ route('reservations.cancel', $reservation) }}"
                                                    onsubmit="return confirm('Cancel this reservation request?');">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center gap-1 rounded-lg border border-border bg-white px-3 py-2 font-button text-xs font-medium text-slate-500 transition hover:border-error/30 hover:bg-error/5 hover:text-error">

                                                        Cancel

                                                    </button>

                                                </form>

                                            @endif


                                            @if (
                                                $reservation->status !== 'pending' ||
                                                (
                                                    $reservation->requester_email !== auth()->user()->email &&
                                                    !auth()->user()->can('decideReservations')
                                                )
                                            )

                                                <span class="text-xs text-slate-400">
                                                    —
                                                </span>

                                            @endif

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="5"
                                        class="px-6 py-16">

                                        <div class="mx-auto flex max-w-sm flex-col items-center text-center">

                                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-accent/10 text-accent">

                                                <svg
                                                    class="h-7 w-7"
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

                                            <h3 class="font-heading text-base font-semibold text-primary">
                                                No reservation requests
                                            </h3>

                                            <p class="mt-1 text-sm text-slate-500">
                                                Submitted facility requests will appear here.
                                            </p>

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- Pagination --}}
            @if ($reservations->hasPages())

                <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
                    {{ $reservations->links() }}
                </div>

            @endif

        </div>

    </div>

</div>

@endsection