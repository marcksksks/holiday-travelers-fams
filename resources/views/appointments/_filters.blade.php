<section class="space-y-4">

    <x-section-header
        eyebrow="Workspace"
        title="Find Appointments"
        description="Search or narrow the appointment schedule by status, date, or facility." />


    <div class="card p-4">

        <form
            method="GET"
            action="{{ route('appointments.index') }}"
            class="space-y-3">

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
                            d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />

                    </svg>

                </div>


                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    maxlength="120"
                    placeholder="Search visitor, organization, host, purpose or facility..."
                    class="input w-full pl-10">

            </div>


            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

                <select
                    name="status"
                    class="input">

                    <option value="">
                        All statuses
                    </option>

                    @foreach ([
                        'scheduled' => 'Scheduled',
                        'confirmed' => 'Confirmed',
                        'checked_in' => 'Checked In',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No Show',
                    ] as $value => $label)

                        <option
                            value="{{ $value }}"
                            @selected($status === $value)>

                            {{ $label }}

                        </option>

                    @endforeach

                </select>


                <input
                    type="date"
                    name="date"
                    value="{{ $date }}"
                    class="input">


                <select
                    name="facility_id"
                    class="input">

                    <option value="">
                        All facilities
                    </option>

                    @foreach ($filterFacilities as $facility)

                        <option
                            value="{{ $facility->id }}"
                            @selected((int) $facilityId === $facility->id)>

                            {{ $facility->name }}

                        </option>

                    @endforeach

                </select>


                <div class="flex gap-2">

                    <button
                        type="submit"
                        class="btn-primary flex-1 justify-center">

                        Apply

                    </button>


                    @if (
                        $search !== ''
                        || $status !== ''
                        || $date !== ''
                        || $facilityId
                        || $scope !== ''
                    )

                        <a
                            href="{{ route('appointments.index') }}"
                            class="btn-outline justify-center">

                            Clear

                        </a>

                    @endif

                </div>

            </div>


            @if ($scope !== '')

                <input
                    type="hidden"
                    name="scope"
                    value="{{ $scope }}">

            @endif

        </form>


        @if (
            $search !== ''
            || $status !== ''
            || $date !== ''
            || $facilityId
            || $scope !== ''
        )

            <div class="mt-3 border-t border-border pt-3 text-xs text-slate-500">

                Showing

                <span class="font-semibold text-primary">
                    {{ number_format($appointments->total()) }}
                </span>

                matching
                {{ $appointments->total() === 1 ? 'appointment' : 'appointments' }}

            </div>

        @endif

    </div>

</section>