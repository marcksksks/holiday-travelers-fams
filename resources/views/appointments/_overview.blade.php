{{-- =====================================================
     APPOINTMENT OVERVIEW
====================================================== --}}

<section class="space-y-4">

    <x-section-header
        eyebrow="Overview"
        title="Schedule Overview"
        description="A current snapshot of today's schedule, upcoming visits, and visitors already checked in." />


    <div class="grid gap-3 sm:grid-cols-3">

        <x-metric-card
            label="Today's Appointments"
            :value="number_format($todayCount)"
            :href="route('appointments.index', ['scope' => 'today'])"
            helper="Appointments scheduled for the current day."
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
            :value="number_format($upcomingCount)"
            :href="route('appointments.index', ['scope' => 'upcoming'])"
            helper="Scheduled and confirmed visits that are still ahead."
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
            :value="number_format($checkedInCount)"
            :href="route('appointments.index', ['status' => 'checked_in'])"
            helper="Appointment visitors currently recorded on site."
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