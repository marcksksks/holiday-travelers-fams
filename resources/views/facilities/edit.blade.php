@extends('layouts.app')

@section('title', 'Edit Facility')

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

        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">

            <div>

                <h2 class="font-heading text-2xl font-bold text-primary">
                    Edit Facility
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Update information and availability for
                    <span class="font-medium text-primary">
                        {{ $facility->name }}
                    </span>.
                </p>

            </div>


            <div>

                @switch($facility->status)

                    @case('available')
                        <span class="badge badge-success">
                            Available
                        </span>
                        @break

                    @case('maintenance')
                        <span class="badge badge-warning">
                            Maintenance
                        </span>
                        @break

                    @case('archived')
                        <span class="badge bg-slate-100 text-slate-600">
                            Archived
                        </span>
                        @break

                    @default
                        <span class="badge badge-info">
                            {{ str($facility->status)->headline() }}
                        </span>

                @endswitch

            </div>

        </div>

    </div>


    {{-- Edit Form --}}
    <div class="card overflow-hidden">

        <div class="border-b border-border bg-background/60 px-6 py-5">

            <div class="flex items-center gap-3">

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 13H9v-2.828l6.586-6.586z" />
                    </svg>

                </div>

                <div>

                    <h3 class="font-heading text-base font-semibold text-primary">
                        Facility Information
                    </h3>

                    <p class="mt-0.5 text-xs text-slate-500">
                        Facility #{{ $facility->id }}
                    </p>

                </div>

            </div>

        </div>


        <form
            method="POST"
            action="{{ route('facilities.update', $facility) }}">

            @csrf
            @method('PUT')


            <div class="px-6 py-6 md:px-8">

                @include('facilities._form', ['facility' => $facility])

            </div>


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

                    Update Facility

                </button>

            </div>

        </form>

    </div>


    {{-- Danger Zone --}}
    @if ($facility->status !== 'archived')

        <div class="overflow-hidden rounded-2xl border border-error/20 bg-card">

            <div class="flex flex-col gap-4 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-error/10 text-error">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
                        </svg>

                    </div>

                    <div>

                        <h3 class="font-heading text-sm font-semibold text-error">
                            Archive Facility
                        </h3>

                        <p class="mt-1 max-w-xl text-xs leading-relaxed text-slate-500">
                            Archiving removes this facility from normal active use. Existing historical records will remain in the system.
                        </p>

                    </div>

                </div>


                <form
                    method="POST"
                    action="{{ route('facilities.destroy', $facility) }}"
                    onsubmit="return confirm('Are you sure you want to archive this facility?');">

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn-danger whitespace-nowrap">

                        Archive Facility

                    </button>

                </form>

            </div>

        </div>

    @endif

</div>

@endsection