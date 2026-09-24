@extends('layouts.app')

@section('title', 'Facilities Reservation')

@section('content')

<div class="space-y-6">

    {{-- =====================================================
         FACILITIES WORKSPACE HEADER
    ====================================================== --}}
    <x-page-header
        eyebrow="Facilities Reservation"
        title="Facilities"
        badge="Resource Directory"
        description="Manage reservable rooms, vehicles, shared spaces, and operational resources from one workspace.">

        @can('manageFacilities')

            <x-slot:actions>

                <div class="flex flex-wrap items-center gap-2">

                    <a
                        href="{{ route('facilities.import.index') }}"
                        class="btn-outline inline-flex items-center justify-center gap-2">

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 3v12m0 0-4-4m4 4 4-4M5 19h14" />

                        </svg>

                        Bulk Import

                    </a>

                    <a
                        href="{{ route('facilities.create') }}"
                    data-facility-create-open
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

                    Add Facility

                    </a>

                </div>

            </x-slot:actions>

        @endcan

    </x-page-header>


    {{-- =====================================================
         MODULE NAVIGATION
    ====================================================== --}}
    <div class="card p-1.5">

        <nav
            class="grid grid-cols-2 gap-1 sm:inline-grid sm:min-w-[340px] sm:grid-cols-2"
            aria-label="Facilities Reservation workspace">

            <a
                href="{{ route('facilities.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 font-button text-sm font-semibold text-white shadow-sm"
                aria-current="page">

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

                Facilities

            </a>


            <a
                href="{{ route('reservations.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 font-button text-sm font-semibold text-slate-500 transition hover:bg-background hover:text-primary">

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

                Reservations

            </a>

        </nav>

    </div>


    {{-- =====================================================
         FACILITY OVERVIEW
    ====================================================== --}}
    <section class="space-y-4">

        <x-section-header
            eyebrow="Overview"
            title="Facility Status"
            description="A current snapshot of the facilities visible to your role." />


        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

            <x-metric-card
                label="Total Facilities"
                :value="number_format($counts['total'])"
                :href="route('facilities.index')"
                helper="Facilities currently visible in this directory."
                tone="primary">

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
                            d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Available"
                :value="number_format($counts['available'])"
                :href="route('facilities.index', ['status' => 'available'])"
                helper="Ready to accept new reservation requests."
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
                label="Maintenance"
                :value="number_format($counts['maintenance'])"
                :href="route('facilities.index', ['status' => 'maintenance'])"
                helper="Temporarily unavailable while maintenance is in progress."
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
                            d="M14.7 6.3a4 4 0 01-5 5L4 17v3h3l5.7-5.7a4 4 0 005-5l-3 3-3-3 3-3z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Unavailable"
                :value="number_format($counts['unavailable'])"
                :href="route('facilities.index', ['status' => 'unavailable'])"
                helper="Not currently open for reservation requests."
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
                            d="M6 18L18 6M6 6l12 12" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>

        </div>


        @if (
            $canViewArchived
            &&
            $counts['archived'] > 0
        )

            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-border bg-card px-4 py-3">

                <div>

                    <p class="text-xs font-semibold text-primary">
                        Archived Facilities
                    </p>

                    <p class="mt-0.5 text-[11px] text-slate-500">
                        {{ number_format($counts['archived']) }}
                        archived
                        {{ \Illuminate\Support\Str::plural('facility', $counts['archived']) }}
                        are retained outside the active reservable inventory.
                    </p>

                </div>

                <a
                    href="{{ route('facilities.index', ['status' => 'archived']) }}"
                    class="text-xs font-semibold text-accent transition hover:text-primary">

                    Review archived →

                </a>

            </div>

        @endif

    </section>

    {{-- =====================================================
         FACILITY FILTER TOOLBAR
    ====================================================== --}}
    <form
        method="GET"
        action="{{ route('facilities.index') }}"
        class="card overflow-hidden">

        <div class="flex flex-col gap-3 border-b border-border px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2 class="font-heading text-sm font-semibold text-primary">
                    Find Facilities
                </h2>

                <p class="mt-0.5 text-[11px] text-slate-400">
                    Search or narrow the directory by type and operational status.
                </p>

            </div>


            @if (
                request()->filled('search')
                ||
                request()->filled('type')
                ||
                request()->filled('status')
            )

                <a
                    href="{{ route('facilities.index') }}"
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


        <div class="bg-background/35 px-5 py-4">

            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[minmax(260px,1.6fr)_220px_200px_auto]">

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
                        placeholder="Search facilities..."
                        class="input pl-9"
                        aria-label="Search facilities">

                </div>


                {{-- Facility Type --}}
                <select
                    name="type"
                    class="input"
                    aria-label="Facility type">

                    <option value="">
                        All facility types
                    </option>

                    @foreach ([
                        'conference_room',
                        'meeting_room',
                        'training_room',
                        'function_room',
                        'vehicle',
                        'other'
                    ] as $type)

                        <option
                            value="{{ $type }}"
                            @selected(request('type') === $type)>

                            {{ str($type)->headline() }}

                        </option>

                    @endforeach

                </select>


                {{-- Status --}}
                <select
                    name="status"
                    class="input"
                    aria-label="Facility status">

                    <option value="">
                        All statuses
                    </option>

                    <option
                        value="available"
                        @selected(request('status') === 'available')>
                        Available
                    </option>

                    <option
                        value="maintenance"
                        @selected(request('status') === 'maintenance')>
                        Maintenance
                    </option>

                    <option
                        value="unavailable"
                        @selected(request('status') === 'unavailable')>
                        Unavailable
                    </option>

                    @if ($canViewArchived)

                        <option
                            value="archived"
                            @selected(request('status') === 'archived')>
                            Archived
                        </option>

                    @endif

                </select>


                {{-- Apply --}}
                <button
                    type="submit"
                    class="btn-primary justify-center whitespace-nowrap">

                    Apply

                </button>

            </div>

        </div>

    </form>


    {{-- Directory header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

        <div>

            <h2 class="font-heading text-base font-semibold text-primary">
                Facility Directory
            </h2>

            <p class="mt-0.5 text-xs text-slate-500">

                @if ($facilities->total() > 0)

                    Showing
                    {{ number_format($facilities->firstItem()) }}
                    –
                    {{ number_format($facilities->lastItem()) }}
                    of
                    {{ number_format($facilities->total()) }}
                    facilities

                @else

                    No facilities match this view

                @endif

            </p>

        </div>

    </div>


    @include('facilities._mobile-cards')


    {{-- Facilities table --}}
    <div class="table-shell hidden overflow-hidden md:block">

        <div class="overflow-x-auto">

            <table class="min-w-full text-left text-sm">

                <thead class="table-header sticky top-0 z-10">

                    <tr>

                        <th class="px-5 py-4 font-medium">
                            Facility
                        </th>

                        <th class="px-5 py-4 font-medium">
                            Type
                        </th>

                        <th class="px-5 py-4 font-medium">
                            Location
                        </th>

                        <th class="px-5 py-4 font-medium">
                            Capacity
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

                    @forelse ($facilities as $facility)

                        <tr class="group transition hover:bg-background/70">

                            {{-- Facility --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div
                                        @class([
                                            'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl',
                                            'bg-success/10 text-success' => $facility->status === 'available',
                                            'bg-warning/10 text-amber-600' => $facility->status === 'maintenance',
                                            'bg-slate-100 text-slate-500' => in_array($facility->status, ['unavailable', 'archived']),
                                        ])>

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

                                        <p class="max-w-[260px] truncate font-button text-sm font-semibold text-primary">
                                            {{ $facility->name }}
                                        </p>

                                        <div class="mt-1 flex flex-wrap items-center gap-2">

                                            <span class="text-xs text-slate-400">
                                                Facility #{{ $facility->id }}
                                            </span>


                                            @if ($facility->status === 'available')

                                                <span class="text-slate-300">
                                                    •
                                                </span>

                                                <a
                                                    href="{{ route('reservations.index', ['reserve_facility' => $facility->id]) }}"
                                                    class="inline-flex items-center gap-1 text-xs font-semibold text-accent transition hover:text-primary">

                                                    Reserve

                                                    <svg
                                                        class="h-3 w-3"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24">

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 5l7 7-7 7" />

                                                    </svg>

                                                </a>

                                            @endif

                                        </div>

                                    </div>

                                </div>

                            </td>


                            {{-- Type --}}
                            <td class="px-5 py-4">

                                <span class="inline-flex rounded-lg bg-primary/5 px-2.5 py-1 text-xs font-medium text-primary">
                                    {{ str($facility->facility_type)->headline() }}
                                </span>

                            </td>


                            {{-- Location --}}
                            <td class="px-5 py-4">

                                <div class="flex max-w-[220px] items-center gap-2 text-slate-600">

                                    <svg
                                        class="h-4 w-4 shrink-0 text-accent"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z" />

                                    </svg>

                                    <span class="truncate">
                                        {{ $facility->location ?: 'Not specified' }}
                                    </span>

                                </div>

                            </td>


                            {{-- Capacity --}}
                            <td class="px-5 py-4">

                                @if ($facility->capacity !== null)

                                    <div class="flex items-center gap-2 text-slate-600">

                                        <svg
                                            class="h-4 w-4 text-slate-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M12 12a4 4 0 100-8 4 4 0 000 8zm0 2c-4.418 0-8 2.239-8 5h16c0-2.761-3.582-5-8-5z" />

                                        </svg>

                                        <span class="font-semibold">
                                            {{ number_format($facility->capacity) }}
                                        </span>

                                        <span class="text-xs text-slate-400">
                                            {{ $facility->facility_type === 'vehicle' ? 'passengers' : 'people' }}
                                        </span>

                                    </div>

                                @else

                                    <span class="text-sm text-slate-400">
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- Status --}}
                            <td class="px-5 py-4">

                                @switch($facility->status)

                                    @case('available')

                                        <span class="badge badge-success">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                            Available
                                        </span>

                                        @break


                                    @case('maintenance')

                                        <span class="badge badge-warning">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-warning"></span>
                                            Maintenance
                                        </span>

                                        @break


                                    @case('unavailable')

                                        <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            Unavailable
                                        </span>

                                        @break


                                    @case('archived')

                                        <span class="badge bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            Archived
                                        </span>

                                        @break


                                    @default

                                        <span class="badge badge-info">
                                            {{ str($facility->status)->headline() }}
                                        </span>

                                @endswitch

                            </td>


                            {{-- Actions --}}
                            <td class="px-5 py-4 text-right">

                                @php

                                    $canEditFacility =
                                        auth()->user()->can(
                                            'manageFacilities'
                                        );

                                    $canArchiveFacility =
                                        $canEditFacility
                                        &&
                                        $facility->status !== 'archived';

                                    $canRestoreFacility =
                                        $canEditFacility
                                        &&
                                        $facility->status === 'archived';
                                @endphp


                                <button
                                    type="button"
                                    data-facility-actions-open
                                    data-facility-id="{{ $facility->id }}"

                                    data-can-edit="{{ $canEditFacility ? '1' : '0' }}"
                                    data-can-archive="{{ $canArchiveFacility ? '1' : '0' }}"
                                    data-can-restore="{{ $canRestoreFacility ? '1' : '0' }}"

                                    data-archive-url="{{ route('facilities.destroy', $facility) }}"
                                    data-restore-url="{{ route('facilities.restore', $facility) }}"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-border bg-card text-slate-500 transition hover:border-accent/40 hover:bg-accent/5 hover:text-primary focus:outline-none focus:ring-2 focus:ring-accent/20"
                                    aria-label="Open facility actions">

                                    <svg
                                        class="h-5 w-5"
                                        fill="currentColor"
                                        viewBox="0 0 24 24">

                                        <circle cx="5" cy="12" r="1.7" />
                                        <circle cx="12" cy="12" r="1.7" />
                                        <circle cx="19" cy="12" r="1.7" />

                                    </svg>

                                </button>


                                {{-- Hidden View Details trigger --}}
                                <button
                                    type="button"
                                    data-facility-view-open
                                    data-facility-id="{{ $facility->id }}"
                                    data-facility-name="{{ $facility->name }}"
                                    data-facility-description="{{ $facility->description }}"
                                    data-facility-location="{{ $facility->location }}"
                                    data-facility-capacity="{{ $facility->capacity }}"
                                    data-facility-type="{{ $facility->facility_type }}"
                                    data-facility-status="{{ $facility->status }}"
                                    class="hidden"
                                    tabindex="-1"
                                    aria-hidden="true">
                                </button>


                                @can('manageFacilities')

                                    {{-- Hidden existing edit-modal trigger --}}
                                    <a
                                        href="{{ route('facilities.edit', $facility) }}"
                                        data-facility-edit-open
                                        data-facility-id="{{ $facility->id }}"
                                        data-facility-name="{{ $facility->name }}"
                                        data-facility-description="{{ $facility->description }}"
                                        data-facility-location="{{ $facility->location }}"
                                        data-facility-capacity="{{ $facility->capacity }}"
                                        data-facility-type="{{ $facility->facility_type }}"
                                        data-facility-status="{{ $facility->status }}"
                                        data-facility-update-url="{{ route('facilities.update', $facility) }}"
                                        class="hidden"
                                        tabindex="-1"
                                        aria-hidden="true">
                                    </a>

                                @endcan

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-16">

                                <div class="mx-auto max-w-sm text-center">

                                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-accent/10 text-accent">

                                        <svg
                                            class="h-7 w-7"
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


                                    @if (
                                        request()->filled('search')
                                        || request()->filled('type')
                                        || request()->filled('status')
                                    )

                                        <h3 class="font-heading text-base font-semibold text-primary">
                                            No matching facilities
                                        </h3>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Try changing your search term or removing some filters.
                                        </p>

                                        <a
                                            href="{{ route('facilities.index') }}"
                                            class="btn-outline mt-5 inline-flex">

                                            Clear filters

                                        </a>

                                    @else

                                        <h3 class="font-heading text-base font-semibold text-primary">
                                            No facilities yet
                                        </h3>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Facilities added to the system will appear in this directory.
                                        </p>

                                        @can('manageFacilities')

                                            <a
                                                href="{{ route('facilities.create') }}"
                                                class="btn-primary mt-5 inline-flex">

                                                Add your first facility

                                            </a>

                                        @endcan

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    @include('facilities._details-modal')
    @include('facilities._actions-menu')
    {{-- Pagination --}}
    @if ($facilities->hasPages())

        <div class="card px-5 py-4">
            {{ $facilities->links() }}
        </div>

    @endif

</div>


    @can('manageFacilities')

        {{-- ======================================================
             ADD FACILITY MODAL
        ======================================================= --}}
        <div
            data-facility-create-modal
            @if ($errors->any() && old('_facility_modal_context') === 'create')
                data-open-on-error="true"
            @endif
            class="fixed inset-0 z-[80] hidden"
            role="dialog"
            aria-modal="true"
            aria-labelledby="facility-create-title">

            <div
                data-facility-create-backdrop
                class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
            </div>


            <div class="relative flex min-h-full items-center justify-center p-3 sm:p-6">

                <div
                    data-facility-create-panel
                    class="flex max-h-[calc(100vh-1.5rem)] w-full max-w-3xl flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-2xl sm:max-h-[calc(100vh-3rem)]">

                    {{-- Header --}}
                    <div class="flex shrink-0 items-start justify-between gap-4 border-b border-border px-5 py-4 sm:px-6">

                        <div class="flex items-start gap-3">

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

                                <h2
                                    id="facility-create-title"
                                    class="font-heading text-lg font-semibold text-primary">

                                    Add Facility

                                </h2>

                                <p class="mt-1 text-sm text-slate-500">
                                    Register a new reservable room or shared facility.
                                </p>

                            </div>

                        </div>


                        <button
                            type="button"
                            data-facility-create-close
                            aria-label="Close Add Facility dialog"
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
                        action="{{ route('facilities.store') }}"
                        class="flex min-h-0 flex-1 flex-col">

                        @csrf

                        <input
                            type="hidden"
                            name="_facility_modal_context"
                            value="create">


                        <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6">

                            @if ($errors->any() && old('_facility_modal_context') === 'create')

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


                            <div class="space-y-6">

                                {{-- Basic information --}}
                                <section>

                                    <div class="mb-4">

                                        <h3 class="font-heading text-sm font-semibold text-primary">
                                            Facility Information
                                        </h3>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Provide the main details of this facility.
                                        </p>

                                    </div>


                                    <div class="space-y-4">

                                        <div>

                                            <label
                                                for="create_facility_name"
                                                class="label">

                                                Facility Name
                                                <span class="text-error">*</span>

                                            </label>

                                            <input
                                                id="create_facility_name"
                                                type="text"
                                                name="name"
                                                value="{{ old('_facility_modal_context') === 'create' ? old('name') : '' }}"
                                                placeholder="e.g. Executive Meeting Room"
                                                required
                                                class="input">

                                        </div>


                                        <div>

                                            <label
                                                for="create_facility_description"
                                                class="label">

                                                Description

                                            </label>

                                            <textarea
                                                id="create_facility_description"
                                                name="description"
                                                rows="3"
                                                placeholder="Describe the purpose or special features of the facility..."
                                                class="input">{{ old('_facility_modal_context') === 'create' ? old('description') : '' }}</textarea>

                                        </div>

                                    </div>

                                </section>


                                <div class="border-t border-border"></div>


                                {{-- Location --}}
                                <section>

                                    <div class="mb-4">

                                        <h3 class="font-heading text-sm font-semibold text-primary">
                                            Location & Capacity
                                        </h3>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Specify where the facility is located and how many people it accommodates.
                                        </p>

                                    </div>


                                    <div class="grid gap-4 md:grid-cols-2">

                                        <div>

                                            <label
                                                for="create_facility_location"
                                                class="label">

                                                Location

                                            </label>

                                            <input
                                                id="create_facility_location"
                                                type="text"
                                                name="location"
                                                value="{{ old('_facility_modal_context') === 'create' ? old('location') : '' }}"
                                                placeholder="e.g. 2nd Floor, Main Office"
                                                class="input">

                                        </div>


                                        <div>

                                            <label
                                                for="create_facility_capacity"
                                                class="label">

                                                Capacity

                                            </label>

                                            <div class="relative">

                                                <input
                                                    id="create_facility_capacity"
                                                    type="number"
                                                    name="capacity"
                                                    min="0"
                                                    value="{{ old('_facility_modal_context') === 'create' ? old('capacity') : '' }}"
                                                    placeholder="e.g. 20"
                                                    class="input pr-16">

                                                <span
                                                    data-facility-capacity-unit
                                                    class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs text-slate-400">
                                                    people
                                                </span>

                                            </div>

                                        </div>

                                    </div>

                                </section>


                                <div class="border-t border-border"></div>


                                {{-- Classification --}}
                                <section>

                                    <div class="mb-4">

                                        <h3 class="font-heading text-sm font-semibold text-primary">
                                            Classification & Availability
                                        </h3>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Choose the facility category and current operating status.
                                        </p>

                                    </div>


                                    <div class="grid gap-4 md:grid-cols-2">

                                        <div>

                                            <label
                                                for="create_facility_type"
                                                class="label">

                                                Facility Type
                                                <span class="text-error">*</span>

                                            </label>

                                            <select
                                                id="create_facility_type"
                                                name="facility_type"
                                                required
                                                class="input">

                                                @foreach ([
                                                    'conference_room',
                                                    'meeting_room',
                                                    'training_room',
                                                    'function_room',
                                                    'vehicle',
                                                    'other'
                                                ] as $type)

                                                    <option
                                                        value="{{ $type }}"
                                                        @selected(
                                                            old('_facility_modal_context') === 'create'
                                                            && old('facility_type', 'meeting_room') === $type
                                                        )>

                                                        {{ str($type)->headline() }}

                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>


                                        <div>

                                            <label
                                                for="create_facility_status"
                                                class="label">

                                                Status
                                                <span class="text-error">*</span>

                                            </label>

                                            <select
                                                id="create_facility_status"
                                                name="status"
                                                required
                                                class="input">

                                                @foreach ([
                                                    'available',
                                                    'maintenance',
                                                    'unavailable',
                                                    'archived'
                                                ] as $status)

                                                    <option
                                                        value="{{ $status }}"
                                                        @selected(
                                                            old('_facility_modal_context') === 'create'
                                                            && old('status', 'available') === $status
                                                        )>

                                                        {{ str($status)->headline() }}

                                                    </option>

                                                @endforeach

                                            </select>

                                            <p class="mt-1.5 text-xs text-slate-400">
                                                Available facilities can accept reservation requests.
                                            </p>

                                        </div>

                                    </div>

                                </section>

                            </div>

                        </div>


                        {{-- Footer --}}
                        <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-border bg-background/50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">

                            <p class="text-xs text-slate-400">
                                <span class="text-error">*</span>
                                Required fields
                            </p>


                            <div class="flex flex-col-reverse gap-2 sm:flex-row">

                                <button
                                    type="button"
                                    data-facility-create-close
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

                                    Add Facility

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        {{-- ======================================================
             EDIT FACILITY MODAL
        ======================================================= --}}
        <div
            data-facility-edit-modal
            class="fixed inset-0 z-[80] hidden"
            role="dialog"
            aria-modal="true"
            aria-labelledby="facility-edit-title">

            <div
                data-facility-edit-backdrop
                class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
            </div>


            <div class="relative flex min-h-full items-center justify-center p-3 sm:p-6">

                <div
                    data-facility-edit-panel
                    class="flex max-h-[calc(100vh-1.5rem)] w-full max-w-3xl flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-2xl sm:max-h-[calc(100vh-3rem)]">

                    {{-- Header --}}
                    <div class="flex shrink-0 items-start justify-between gap-4 border-b border-border px-5 py-4 sm:px-6">

                        <div class="flex items-start gap-3">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">

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
                                    id="facility-edit-title"
                                    class="font-heading text-lg font-semibold text-primary">

                                    Edit Facility

                                </h2>

                                <p class="mt-1 text-sm text-slate-500">
                                    Update facility information and availability.
                                </p>

                                <p
                                    data-facility-edit-reference
                                    class="mt-1 text-xs font-medium text-accent">
                                </p>

                            </div>

                        </div>


                        <button
                            type="button"
                            data-facility-edit-close
                            aria-label="Close Edit Facility dialog"
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
                        data-facility-edit-form
                        class="flex min-h-0 flex-1 flex-col">

                        @csrf
                        @method('PUT')

                        <input
                            type="hidden"
                            name="_facility_modal_context"
                            data-facility-edit-context>


                        <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6">

                            <div
                                data-facility-edit-errors
                                class="mb-5 hidden rounded-xl border border-error/20 bg-error/5 p-4">

                                <p class="text-sm font-semibold text-error">
                                    Please correct the highlighted information and try again.
                                </p>

                            </div>


                            <div class="space-y-6">

                                <section>

                                    <div class="mb-4">

                                        <h3 class="font-heading text-sm font-semibold text-primary">
                                            Facility Information
                                        </h3>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Review or update the facility details.
                                        </p>

                                    </div>


                                    <div class="space-y-4">

                                        <div>

                                            <label
                                                for="edit_facility_name"
                                                class="label">

                                                Facility Name
                                                <span class="text-error">*</span>

                                            </label>

                                            <input
                                                id="edit_facility_name"
                                                type="text"
                                                name="name"
                                                required
                                                class="input">

                                        </div>


                                        <div>

                                            <label
                                                for="edit_facility_description"
                                                class="label">

                                                Description

                                            </label>

                                            <textarea
                                                id="edit_facility_description"
                                                name="description"
                                                rows="3"
                                                class="input"></textarea>

                                        </div>

                                    </div>

                                </section>


                                <div class="border-t border-border"></div>


                                <section>

                                    <div class="mb-4">

                                        <h3 class="font-heading text-sm font-semibold text-primary">
                                            Location & Capacity
                                        </h3>

                                    </div>


                                    <div class="grid gap-4 md:grid-cols-2">

                                        <div>

                                            <label
                                                for="edit_facility_location"
                                                class="label">

                                                Location

                                            </label>

                                            <input
                                                id="edit_facility_location"
                                                type="text"
                                                name="location"
                                                class="input">

                                        </div>


                                        <div>

                                            <label
                                                for="edit_facility_capacity"
                                                class="label">

                                                Capacity

                                            </label>

                                            <div class="relative">

                                                <input
                                                    id="edit_facility_capacity"
                                                    type="number"
                                                    name="capacity"
                                                    min="0"
                                                    class="input pr-16">

                                                <span
                                                    data-facility-capacity-unit
                                                    class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs text-slate-400">
                                                    people
                                                </span>

                                            </div>

                                        </div>

                                    </div>

                                </section>


                                <div class="border-t border-border"></div>


                                <section>

                                    <div class="mb-4">

                                        <h3 class="font-heading text-sm font-semibold text-primary">
                                            Classification & Availability
                                        </h3>

                                    </div>


                                    <div class="grid gap-4 md:grid-cols-2">

                                        <div>

                                            <label
                                                for="edit_facility_type"
                                                class="label">

                                                Facility Type
                                                <span class="text-error">*</span>

                                            </label>

                                            <select
                                                id="edit_facility_type"
                                                name="facility_type"
                                                required
                                                class="input">

                                                @foreach ([
                                                    'conference_room',
                                                    'meeting_room',
                                                    'training_room',
                                                    'function_room',
                                                    'vehicle',
                                                    'other'
                                                ] as $type)

                                                    <option value="{{ $type }}">
                                                        {{ str($type)->headline() }}
                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>


                                        <div>

                                            <label
                                                for="edit_facility_status"
                                                class="label">

                                                Status
                                                <span class="text-error">*</span>

                                            </label>

                                            <select
                                                id="edit_facility_status"
                                                name="status"
                                                required
                                                class="input">

                                                @foreach ([
                                                    'available',
                                                    'maintenance',
                                                    'unavailable',
                                                    'archived'
                                                ] as $status)

                                                    <option value="{{ $status }}">
                                                        {{ str($status)->headline() }}
                                                    </option>

                                                @endforeach

                                            </select>

                                            <p class="mt-1.5 text-xs text-slate-400">
                                                Available facilities can accept reservation requests.
                                            </p>

                                        </div>

                                    </div>

                                </section>

                            </div>

                        </div>


                        <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-border bg-background/50 px-5 py-4 sm:flex-row sm:items-center sm:justify-end sm:px-6">

                            <button
                                type="button"
                                data-facility-edit-close
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

                                Save Changes

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    @endcan

<script>
document.addEventListener('DOMContentLoaded', () => {

    const createModal =
        document.querySelector(
            '[data-facility-create-modal]'
        );

    const editModal =
        document.querySelector(
            '[data-facility-edit-modal]'
        );

    if (!createModal || !editModal) {
        return;
    }


    let previouslyFocused = null;
    let previousBodyOverflow = '';


    const openModal = (modal) => {
        previouslyFocused =
            document.activeElement;

        previousBodyOverflow =
            document.body.style.overflow;

        modal.classList.remove(
            'fams-modal-closing'
        );

        modal.classList.remove('hidden');

        document.body.style.overflow =
            'hidden';

        window.requestAnimationFrame(
            () => {
                modal.querySelector(
                    'input[type="text"]'
                )?.focus();
            }
        );
    };


    const closeModal = (modal) => {

        if (
            modal.classList.contains(
                'fams-modal-closing'
            )
        ) {
            return;
        }


        const finishClose = () => {

            modal.classList.add('hidden');

            modal.classList.remove(
                'fams-modal-closing'
            );

            document.body.style.overflow =
                previousBodyOverflow;

            if (
                previouslyFocused
                && typeof previouslyFocused.focus
                    === 'function'
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


    const createOpenButtons =
        document.querySelectorAll(
            '[data-facility-create-open]'
        );

    const createCloseButtons =
        createModal.querySelectorAll(
            '[data-facility-create-close]'
        );

    const editOpenButtons =
        document.querySelectorAll(
            '[data-facility-edit-open]'
        );

    const editCloseButtons =
        editModal.querySelectorAll(
            '[data-facility-edit-close]'
        );


    createOpenButtons.forEach(
        (button) => {
            button.addEventListener(
                'click',
                (event) => {
                    event.preventDefault();

                    openModal(createModal);
                }
            );
        }
    );


    createCloseButtons.forEach(
        (button) => {
            button.addEventListener(
                'click',
                () => closeModal(
                    createModal
                )
            );
        }
    );


    createModal
        .querySelector(
            '[data-facility-create-backdrop]'
        )
        ?.addEventListener(
            'click',
            () => closeModal(
                createModal
            )
        );


    const editForm =
        editModal.querySelector(
            '[data-facility-edit-form]'
        );

    const editContext =
        editModal.querySelector(
            '[data-facility-edit-context]'
        );

    const editReference =
        editModal.querySelector(
            '[data-facility-edit-reference]'
        );


    const editFields = {
        name:
            editModal.querySelector(
                '[name="name"]'
            ),

        description:
            editModal.querySelector(
                '[name="description"]'
            ),

        location:
            editModal.querySelector(
                '[name="location"]'
            ),

        capacity:
            editModal.querySelector(
                '[name="capacity"]'
            ),

        type:
            editModal.querySelector(
                '[name="facility_type"]'
            ),

        status:
            editModal.querySelector(
                '[name="status"]'
            ),
    };


    const populateEditModal = (
        button,
        overrides = null
    ) => {
        const id =
            button.dataset.facilityId;

        editForm.action =
            button.dataset.facilityUpdateUrl;

        editContext.value =
            `edit:${id}`;

        editReference.textContent =
            `Facility #${id} · ${button.dataset.facilityName}`;


        editFields.name.value =
            overrides?.name
            ?? button.dataset.facilityName
            ?? '';

        editFields.description.value =
            overrides?.description
            ?? button.dataset.facilityDescription
            ?? '';

        editFields.location.value =
            overrides?.location
            ?? button.dataset.facilityLocation
            ?? '';

        editFields.capacity.value =
            overrides?.capacity
            ?? button.dataset.facilityCapacity
            ?? '';

        editFields.type.value =
            overrides?.facility_type
            ?? button.dataset.facilityType
            ?? 'meeting_room';

        editFields.status.value =
            overrides?.status
            ?? button.dataset.facilityStatus
            ?? 'available';
    };


    editOpenButtons.forEach(
        (button) => {
            button.addEventListener(
                'click',
                (event) => {
                    event.preventDefault();

                    populateEditModal(
                        button
                    );

                    openModal(
                        editModal
                    );
                }
            );
        }
    );


    editCloseButtons.forEach(
        (button) => {
            button.addEventListener(
                'click',
                () => closeModal(
                    editModal
                )
            );
        }
    );


    editModal
        .querySelector(
            '[data-facility-edit-backdrop]'
        )
        ?.addEventListener(
            'click',
            () => closeModal(
                editModal
            )
        );


    document.addEventListener(
        'keydown',
        (event) => {

            if (event.key !== 'Escape') {
                return;
            }

            if (
                !createModal.classList.contains(
                    'hidden'
                )
            ) {
                closeModal(
                    createModal
                );

                return;
            }

            if (
                !editModal.classList.contains(
                    'hidden'
                )
            ) {
                closeModal(
                    editModal
                );
            }

        }
    );


    if (
        createModal.dataset.openOnError
            === 'true'
    ) {
        openModal(
            createModal
        );
    }


    const editValidationContext =
        @json(old('_facility_modal_context'));

    if (
        typeof editValidationContext
            === 'string'
        && editValidationContext
            .startsWith('edit:')
    ) {
        const id =
            editValidationContext
                .substring(5);

        const button =
            document.querySelector(
                `[data-facility-edit-open][data-facility-id="${id}"]`
            );

        if (button) {
            populateEditModal(
                button,
                {
                    name:
                        @json(old('name')),

                    description:
                        @json(old('description')),

                    location:
                        @json(old('location')),

                    capacity:
                        @json(old('capacity')),

                    facility_type:
                        @json(old('facility_type')),

                    status:
                        @json(old('status')),
                }
            );

            editModal
                .querySelector(
                    '[data-facility-edit-errors]'
                )
                ?.classList.remove(
                    'hidden'
                );

            openModal(
                editModal
            );
        }
    }

});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const updateFacilityCapacityUnits = () => {

        document.querySelectorAll(
            '[data-facility-create-modal], [data-facility-edit-modal]'
        ).forEach((modal) => {

            const typeSelect =
                modal.querySelector(
                    '[name="facility_type"]'
                );

            const unit =
                modal.querySelector(
                    '[data-facility-capacity-unit]'
                );

            if (!typeSelect || !unit) {
                return;
            }

            const updateUnit = () => {
                unit.textContent =
                    typeSelect.value === 'vehicle'
                        ? 'passengers'
                        : 'people';
            };

            typeSelect.addEventListener(
                'change',
                updateUnit
            );

            updateUnit();
        });

    };

    updateFacilityCapacityUnits();

});
</script>

@endsection