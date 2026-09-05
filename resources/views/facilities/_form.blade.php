{{-- Basic Information --}}
<div class="space-y-5">

    <div>
        <div class="mb-4 flex items-center gap-3">

            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5" />
                </svg>
            </div>

            <div>
                <h3 class="font-heading text-base font-semibold text-primary">
                    Basic Information
                </h3>

                <p class="text-xs text-slate-500">
                    Enter the primary details of the facility.
                </p>
            </div>

        </div>


        <div class="space-y-4">

            {{-- Name --}}
            <div>
                <label for="name" class="label">
                    Facility Name
                    <span class="text-error">*</span>
                </label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $facility->name ?? '') }}"
                    required
                    placeholder="e.g. Executive Meeting Room"
                    class="input @error('name') border-error focus:border-error focus:ring-error/20 @enderror">

                @error('name')
                    <p class="mt-1.5 text-xs font-medium text-error">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- Description --}}
            <div>
                <label for="description" class="label">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    placeholder="Briefly describe the facility, purpose, equipment, or special features..."
                    class="input @error('description') border-error focus:border-error focus:ring-error/20 @enderror">{{ old('description', $facility->description ?? '') }}</textarea>

                <div class="mt-1.5 flex items-center justify-between gap-3">

                    <p class="text-xs text-slate-400">
                        Optional additional information about this facility.
                    </p>

                </div>

                @error('description')
                    <p class="mt-1.5 text-xs font-medium text-error">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>
    </div>


    <div class="border-t border-border pt-5">

        <div class="mb-4">

            <h3 class="font-heading text-base font-semibold text-primary">
                Location & Capacity
            </h3>

            <p class="mt-1 text-xs text-slate-500">
                Specify where the facility is located and how many people it can accommodate.
            </p>

        </div>


        <div class="grid gap-4 md:grid-cols-2">

            {{-- Location --}}
            <div>
                <label for="location" class="label">
                    Location
                </label>

                <div class="relative">

                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>

                    <input
                        id="location"
                        type="text"
                        name="location"
                        value="{{ old('location', $facility->location ?? '') }}"
                        placeholder="e.g. 2nd Floor, Main Office"
                        class="input pl-10 @error('location') border-error focus:border-error focus:ring-error/20 @enderror">

                </div>

                @error('location')
                    <p class="mt-1.5 text-xs font-medium text-error">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- Capacity --}}
            <div>
                <label for="capacity" class="label">
                    Capacity
                </label>

                <div class="relative">

                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5 5 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>

                    <input
                        id="capacity"
                        type="number"
                        name="capacity"
                        min="0"
                        value="{{ old('capacity', $facility->capacity ?? '') }}"
                        placeholder="e.g. 20"
                        class="input pl-10 pr-16 @error('capacity') border-error focus:border-error focus:ring-error/20 @enderror">

                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5">
                        <span
                            id="facility-capacity-unit"
                            class="text-xs text-slate-400">
                            people
                        </span>
                    </div>

                </div>

                @error('capacity')
                    <p class="mt-1.5 text-xs font-medium text-error">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>

    </div>


    <div class="border-t border-border pt-5">

        <div class="mb-4">

            <h3 class="font-heading text-base font-semibold text-primary">
                Classification & Availability
            </h3>

            <p class="mt-1 text-xs text-slate-500">
                Select the facility category and its current operating status.
            </p>

        </div>


        <div class="grid gap-4 md:grid-cols-2">

            {{-- Facility Type --}}
            <div>
                <label for="facility_type" class="label">
                    Facility Type
                    <span class="text-error">*</span>
                </label>

                <select
                    id="facility_type"
                    name="facility_type"
                    required
                    class="input @error('facility_type') border-error focus:border-error focus:ring-error/20 @enderror">

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
                            @selected(old('facility_type', $facility->facility_type ?? 'meeting_room') === $type)>

                            {{ str($type)->headline() }}

                        </option>

                    @endforeach

                </select>

                @error('facility_type')
                    <p class="mt-1.5 text-xs font-medium text-error">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- Status --}}
            <div>
                <label for="status" class="label">
                    Status
                    <span class="text-error">*</span>
                </label>

                <select
                    id="status"
                    name="status"
                    required
                    class="input @error('status') border-error focus:border-error focus:ring-error/20 @enderror">

                    @foreach ([
                        'available',
                        'maintenance',
                        'unavailable',
                        'archived'
                    ] as $status)

                        <option
                            value="{{ $status }}"
                            @selected(old('status', $facility->status ?? 'available') === $status)>

                            {{ str($status)->headline() }}

                        </option>

                    @endforeach

                </select>

                <p class="mt-1.5 text-xs text-slate-400">
                    Available facilities can be used for reservations.
                </p>

                @error('status')
                    <p class="mt-1.5 text-xs font-medium text-error">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>

    </div>

</div>