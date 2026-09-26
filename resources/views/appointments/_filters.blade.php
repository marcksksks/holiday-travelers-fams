<div
    data-appointment-filter-bar
    class="card p-3">

    <form
        method="GET"
        action="{{ route('appointments.index') }}">

        <div class="grid gap-2 md:grid-cols-2 xl:grid-cols-[minmax(260px,1.6fr)_170px_170px_210px_auto]">

            {{-- Search --}}
            <div class="relative">

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">

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
                    placeholder="Search appointments..."
                    class="input w-full pl-9"
                    aria-label="Search appointments">

            </div>


            {{-- Status --}}
            <select
                name="status"
                class="input"
                aria-label="Appointment status">

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


            {{-- Date --}}
            <input
                type="date"
                name="date"
                value="{{ $date }}"
                class="input"
                aria-label="Appointment date">


            {{-- Facility --}}
            <select
                name="facility_id"
                class="input"
                aria-label="Facility">

                <option value="">
                    All facilities
                </option>

                @foreach ($filterFacilities as $facility)

                    <option
                        value="{{ $facility->id }}"
                        @selected((int) $facilityId === $facility->id)>

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


            {{-- Actions --}}
            <div class="flex gap-2">

                <button
                    type="submit"
                    class="btn-primary flex-1 justify-center whitespace-nowrap xl:flex-none">

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
                        class="btn-outline justify-center px-3"
                        aria-label="Clear appointment filters"
                        title="Clear filters">

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

                    </a>

                @endif

            </div>


            @if ($scope !== '')

                <input
                    type="hidden"
                    name="scope"
                    value="{{ $scope }}">

            @endif

        </div>


        @if (
            $search !== ''
            || $status !== ''
            || $date !== ''
            || $facilityId
            || $scope !== ''
        )

            <p class="mt-2 text-[11px] text-slate-500">

                <span class="font-semibold text-primary">
                    {{ number_format($appointments->total()) }}
                </span>

                matching
                {{ $appointments->total() === 1 ? 'appointment' : 'appointments' }}

            </p>

        @endif

    </form>

</div>
