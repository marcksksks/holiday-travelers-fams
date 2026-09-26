{{-- =====================================================
     APPOINTMENT OVERVIEW
====================================================== --}}

<section class="space-y-3">

    <x-section-header
        title="Schedule Overview" />


    <div class="grid gap-3 sm:grid-cols-3">

        <x-metric-card
            label="Today's Appointments"
            :show-action="false"
            :value="number_format($todayCount)"
            :href="route('appointments.index', ['scope' => 'today'])"
            helper="Scheduled today."
            tone="accent"
            class="{{ $scope === 'today' ? 'ring-2 ring-accent/30' : '' }}">

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

        </x-metric-card>


        <x-metric-card
            label="Upcoming"
            :show-action="false"
            :value="number_format($upcomingCount)"
            :href="route('appointments.index', ['scope' => 'upcoming'])"
            helper="Scheduled or confirmed ahead."
            tone="warning"
            class="{{ $scope === 'upcoming' ? 'ring-2 ring-warning/30' : '' }}">

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
                        d="M12 8v4l3 3M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                </svg>

            </x-slot:icon>

        </x-metric-card>


        <x-metric-card
            label="Checked In"
            :show-action="false"
            :value="number_format($checkedInCount)"
            :href="route('appointments.index', ['status' => 'checked_in'])"
            helper="Currently checked in."
            tone="success"
            class="{{ $status === 'checked_in' ? 'ring-2 ring-success/30' : '' }}">

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

    </div>

</section>