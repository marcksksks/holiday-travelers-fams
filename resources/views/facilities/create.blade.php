@extends('layouts.app')

@section('title', 'Add Facility')

@section('content')

<div class="mx-auto max-w-4xl space-y-6">

    {{-- Page Header --}}
    <div>

        <a
            href="{{ route('facilities.index') }}"
            class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-primary">

            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 19l-7-7 7-7" />
            </svg>

            Back to Facilities

        </a>


        <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

        <h2 class="font-heading text-2xl font-bold text-primary">
            Add New Facility
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Register a new room, shared space, or facility for reservation and administration.
        </p>

    </div>


    {{-- Form Card --}}
    <div class="card overflow-hidden">

        <div class="border-b border-border bg-background/60 px-6 py-5">

            <div class="flex items-center gap-3">

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 4v16m8-8H4" />
                    </svg>

                </div>

                <div>

                    <h3 class="font-heading text-base font-semibold text-primary">
                        Facility Details
                    </h3>

                    <p class="mt-0.5 text-xs text-slate-500">
                        Complete the information below to add the facility.
                    </p>

                </div>

            </div>

        </div>


        <form
            method="POST"
            action="{{ route('facilities.store') }}">

            @csrf


            <div class="px-6 py-6 md:px-8">

                @include('facilities._form', ['facility' => null])

            </div>


            {{-- Footer --}}
            <div class="flex flex-col-reverse gap-3 border-t border-border bg-background/60 px-6 py-4 sm:flex-row sm:items-center sm:justify-end md:px-8">

                <a
                    href="{{ route('facilities.index') }}"
                    class="btn-outline">

                    Cancel

                </a>


                <button
                    type="submit"
                    class="btn-secondary">

                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M5 13l4 4L19 7" />
                    </svg>

                    Save Facility

                </button>

            </div>

        </form>

    </div>


    {{-- Information --}}
    <div class="rounded-2xl border border-accent/20 bg-accent/5 px-5 py-4">

        <div class="flex gap-3">

            <div class="mt-0.5 text-accent">

                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

            </div>

            <div>

                <p class="font-button text-sm font-semibold text-primary">
                    Facility Availability
                </p>

                <p class="mt-1 text-xs leading-relaxed text-slate-500">
                    Set the status to Available when the facility is ready to accept reservation requests.
                </p>

            </div>

        </div>

    </div>

</div>

@endsection