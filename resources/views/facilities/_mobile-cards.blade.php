<div class="grid gap-3 md:hidden">

    @forelse ($facilities as $facility)

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


        <article
            data-facility-mobile-card
            class="card overflow-hidden">

            <div class="p-4">

                <div class="flex items-start justify-between gap-3">

                    <div class="flex min-w-0 items-start gap-3">

                        <div
                            @class([
                                'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl',
                                'bg-success/10 text-success' => $facility->status === 'available',
                                'bg-warning/10 text-amber-600' => $facility->status === 'maintenance',
                                'bg-slate-100 text-slate-500' => in_array(
                                    $facility->status,
                                    ['unavailable', 'archived'],
                                    true
                                ),
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

                            <p class="truncate font-button text-sm font-semibold text-primary">
                                {{ $facility->name }}
                            </p>

                            <p class="mt-1 text-[11px] text-slate-400">
                                Facility #{{ $facility->id }}
                                ·
                                {{ str($facility->facility_type)->headline() }}
                            </p>

                        </div>

                    </div>


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
                                Unavailable
                            </span>

                            @break


                        @case('archived')

                            <span class="badge bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200">
                                Archived
                            </span>

                            @break

                    @endswitch

                </div>


                <div class="mt-4 grid grid-cols-2 gap-3">

                    <div class="rounded-xl bg-background px-3 py-2.5">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Location
                        </p>

                        <p class="mt-1 truncate text-xs font-medium text-slate-700">
                            {{ $facility->location ?: 'Not specified' }}
                        </p>

                    </div>


                    <div class="rounded-xl bg-background px-3 py-2.5">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Capacity
                        </p>

                        <p class="mt-1 text-xs font-medium text-slate-700">

                            @if ($facility->capacity !== null)

                                {{ number_format($facility->capacity) }}

                                {{
                                    $facility->facility_type === 'vehicle'
                                        ? 'passengers'
                                        : 'people'
                                }}

                            @else

                                Not specified

                            @endif

                        </p>

                    </div>

                </div>


                @if ($facility->description)

                    <p class="mt-3 line-clamp-2 text-xs leading-5 text-slate-500">
                        {{ $facility->description }}
                    </p>

                @endif

            </div>


            <div class="flex items-center gap-2 border-t border-border bg-background/40 px-4 py-3">

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
                    class="btn-outline flex-1 justify-center">

                    View Details

                </button>


                @if ($facility->status === 'available')

                    <a
                        href="{{ route('reservations.index', ['reserve_facility' => $facility->id]) }}"
                        class="btn-primary flex-1 justify-center">

                        Reserve

                    </a>

                @endif


                <button
                    type="button"
                    data-facility-actions-open
                    data-facility-id="{{ $facility->id }}"
                    data-can-edit="{{ $canEditFacility ? '1' : '0' }}"
                    data-can-archive="{{ $canArchiveFacility ? '1' : '0' }}"
                    data-can-restore="{{ $canRestoreFacility ? '1' : '0' }}"
                    data-archive-url="{{ route('facilities.destroy', $facility) }}"
                    data-restore-url="{{ route('facilities.restore', $facility) }}"
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-border bg-card text-slate-500 transition hover:border-accent/40 hover:text-primary"
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

            </div>

        </article>


    @empty

        <div class="card">

            <x-empty-state
                title="No facilities found"
                description="No facilities match the current directory filters.">

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

            </x-empty-state>

        </div>

    @endforelse

</div>