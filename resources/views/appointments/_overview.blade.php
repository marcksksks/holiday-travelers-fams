{{-- =====================================================
     APPOINTMENT OVERVIEW
====================================================== --}}

<div class="grid gap-3 sm:grid-cols-3">

    {{-- Today --}}
    <a
        href="{{ route('appointments.index', ['scope' => 'today']) }}"
        @if ($scope === 'today')
            aria-current="page"
        @endif
        class="card group relative overflow-hidden p-5 transition
        {{ $scope === 'today'
            ? 'ring-2 ring-accent/30'
            : 'hover:-translate-y-0.5 hover:shadow-md'
        }}">

        @if ($scope === 'today')
            <span class="absolute inset-x-0 top-0 h-1 bg-accent"></span>
        @endif


        <div class="flex items-center justify-between gap-4">

            <div>

                <p class="text-xs font-medium text-slate-500">
                    Today
                </p>

                <p class="mt-1 font-heading text-2xl font-bold text-primary">
                    {{ number_format($todayCount) }}
                </p>

                <p class="mt-1 text-[11px] text-slate-400">
                    Appointments scheduled today
                </p>

            </div>


            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-accent/10 text-accent">

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

        </div>

    </a>



    {{-- Upcoming --}}
    <a
        href="{{ route('appointments.index', ['scope' => 'upcoming']) }}"
        @if ($scope === 'upcoming')
            aria-current="page"
        @endif
        class="card group relative overflow-hidden p-5 transition
        {{ $scope === 'upcoming'
            ? 'ring-2 ring-secondary/30'
            : 'hover:-translate-y-0.5 hover:shadow-md'
        }}">

        @if ($scope === 'upcoming')
            <span class="absolute inset-x-0 top-0 h-1 bg-secondary"></span>
        @endif


        <div class="flex items-center justify-between gap-4">

            <div>

                <p class="text-xs font-medium text-slate-500">
                    Upcoming
                </p>

                <p class="mt-1 font-heading text-2xl font-bold text-primary">
                    {{ number_format($upcomingCount) }}
                </p>

                <p class="mt-1 text-[11px] text-slate-400">
                    Scheduled and confirmed visits
                </p>

            </div>


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
                        d="M12 8v4l3 3M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                </svg>

            </div>

        </div>

    </a>



    {{-- Checked In --}}
    <a
        href="{{ route('appointments.index', ['status' => 'checked_in']) }}"
        @if ($status === 'checked_in')
            aria-current="page"
        @endif
        class="card group relative overflow-hidden p-5 transition
        {{ $status === 'checked_in'
            ? 'ring-2 ring-success/30'
            : 'hover:-translate-y-0.5 hover:shadow-md'
        }}">

        @if ($status === 'checked_in')
            <span class="absolute inset-x-0 top-0 h-1 bg-success"></span>
        @endif


        <div class="flex items-center justify-between gap-4">

            <div>

                <p class="text-xs font-medium text-slate-500">
                    Checked In
                </p>

                <p class="mt-1 font-heading text-2xl font-bold text-primary">
                    {{ number_format($checkedInCount) }}
                </p>

                <p class="mt-1 text-[11px] text-slate-400">
                    Visitors currently on site
                </p>

            </div>


            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-success/10 text-success">

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

        </div>

    </a>

</div>