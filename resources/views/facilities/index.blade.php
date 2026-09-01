@extends('layouts.app')

@section('title', 'Facilities')

@section('content')

<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Facilities Management
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Manage meeting rooms, shared spaces, and other facilities available for reservation.
            </p>
        </div>

        @can('manageFacilities')

            <a
                href="{{ route('facilities.create') }}"
                class="btn-secondary shrink-0">

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

        @endcan

    </div>


    {{-- Information Bar --}}
    <div class="flex flex-col gap-3 rounded-2xl border border-accent/20 bg-accent/5 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

        <div class="flex items-start gap-3">

            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-accent/10 text-accent">

                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5M9 8h1M14 8h1M9 11h1M14 11h1" />

                </svg>

            </div>

            <div>
                <p class="font-button text-sm font-semibold text-primary">
                    Facility Directory
                </p>

                <p class="mt-0.5 text-xs text-slate-500">
                    Showing registered facilities and their current availability status.
                </p>
            </div>

        </div>

        <div class="text-xs font-medium text-slate-500">
            {{ $facilities->total() }}
            {{ \Illuminate\Support\Str::plural('facility', $facilities->total()) }}
        </div>

    </div>


    {{-- Facilities Table --}}
    <div class="table-shell">

        <div class="overflow-x-auto">

            <table class="min-w-full text-left text-sm">

                <thead class="table-header">

                    <tr>
                        <th class="px-6 py-4 font-medium">
                            Facility
                        </th>

                        <th class="px-6 py-4 font-medium">
                            Type
                        </th>

                        <th class="px-6 py-4 font-medium">
                            Location
                        </th>

                        <th class="px-6 py-4 font-medium">
                            Capacity
                        </th>

                        <th class="px-6 py-4 font-medium">
                            Status
                        </th>

                        @can('manageFacilities')
                            <th class="px-6 py-4 text-right font-medium">
                                Actions
                            </th>
                        @endcan
                    </tr>

                </thead>


                <tbody class="divide-y divide-border bg-card">

                    @forelse ($facilities as $facility)

                        <tr class="transition-colors hover:bg-sky-50/40">

                            {{-- Facility --}}
                            <td class="px-6 py-4">

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

                                        <p class="truncate font-button text-sm font-semibold text-primary">
                                            {{ $facility->name }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-slate-400">
                                            Facility #{{ $facility->id }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            {{-- Type --}}
                            <td class="px-6 py-4">

                                <span class="inline-flex rounded-lg bg-primary/5 px-2.5 py-1 text-xs font-medium text-primary">
                                    {{ str($facility->facility_type)->headline() }}
                                </span>

                            </td>


                            {{-- Location --}}
                            <td class="px-6 py-4 text-slate-600">

                                <div class="flex items-center gap-2">

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

                                    <span>
                                        {{ $facility->location ?: 'Not specified' }}
                                    </span>

                                </div>

                            </td>


                            {{-- Capacity --}}
                            <td class="px-6 py-4">

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
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5 5 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />

                                    </svg>

                                    <span class="font-medium">
                                        {{ $facility->capacity }}
                                    </span>

                                    <span class="text-xs text-slate-400">
                                        people
                                    </span>

                                </div>

                            </td>


                            {{-- Status --}}
                            <td class="px-6 py-4">

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
                            @can('manageFacilities')

                                <td class="px-6 py-4 text-right">

                                    <a
                                        href="{{ route('facilities.edit', $facility) }}"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-white px-3 py-2 font-button text-xs font-medium text-primary transition hover:border-accent hover:bg-accent/5">

                                        <svg
                                            class="h-3.5 w-3.5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 13H9v-2.828l6.586-6.586z" />

                                        </svg>

                                        Edit

                                    </a>

                                </td>

                            @endcan

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-16 text-center">

                                <div class="mx-auto flex max-w-sm flex-col items-center">

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
                                                d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5" />

                                        </svg>

                                    </div>

                                    <h3 class="font-heading text-base font-semibold text-primary">
                                        No facilities found
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Facilities added to the system will appear here.
                                    </p>

                                    @can('manageFacilities')

                                        <a
                                            href="{{ route('facilities.create') }}"
                                            class="btn-secondary mt-5">

                                            Add your first facility

                                        </a>

                                    @endcan

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- Pagination --}}
    @if ($facilities->hasPages())

        <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
            {{ $facilities->links() }}
        </div>

    @endif

</div>

@endsection