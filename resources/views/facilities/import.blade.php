@extends('layouts.app')

@section('title', 'Bulk Import Facilities')

@section('content')

<div class="space-y-6">

    <x-page-header
        eyebrow="Facilities Administration"
        title="Bulk Import Facilities"
        badge="Controlled Data Import"
        description="Import validated facility master data from CSV, Excel, or JSON with all-or-nothing processing.">

        <x-slot:actions>

            <a
                href="{{ route('facilities.index') }}"
                class="btn-outline">

                Back to Facilities

            </a>

        </x-slot:actions>

    </x-page-header>


    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.3fr)_minmax(320px,0.7fr)]">

        <section class="card overflow-hidden">

            <div class="border-b border-border px-5 py-4">

                <h2 class="font-heading text-base font-semibold text-primary">
                    Upload Import File
                </h2>

                <p class="mt-1 text-xs leading-5 text-slate-500">
                    Maximum 5 MB and 500 records per batch. If any record is invalid,
                    the entire import is rejected and no facilities are created.
                </p>

            </div>


            <form
                method="POST"
                action="{{ route('facilities.import.store') }}"
                enctype="multipart/form-data"
                class="space-y-5 p-5"
                data-import-form>

                @csrf


                <div>

                    <label
                        for="import_file"
                        class="label">

                        Import File

                    </label>

                    <input
                        id="import_file"
                        type="file"
                        name="import_file"
                        accept=".csv,.xlsx,.json"
                        required
                        class="input"
                        aria-describedby="import-file-help">

                    <p
                        id="import-file-help"
                        class="mt-1.5 text-xs leading-5 text-slate-500">

                        Accepted formats:
                        <strong>CSV</strong>,
                        <strong>XLSX</strong>, and
                        <strong>JSON</strong>.

                    </p>

                    @error('import_file')

                        <div
                            class="mt-3 rounded-xl border border-error/20 bg-error/5 p-3"
                            role="alert">

                            @foreach ($errors->get('import_file') as $message)

                                <p class="text-xs font-medium text-error">
                                    {{ $message }}
                                </p>

                            @endforeach

                        </div>

                    @enderror

                </div>


                <div class="rounded-xl border border-accent/20 bg-accent/5 p-4">

                    <p class="text-xs font-semibold text-primary">
                        Required fields
                    </p>

                    <p class="mt-1 text-xs leading-5 text-slate-600">
                        <code>name</code>,
                        <code>facility_type</code>,
                        and
                        <code>status</code>.
                    </p>

                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        Optional fields are description, location, capacity, and equipment.
                        Separate multiple equipment entries with semicolons.
                    </p>

                </div>


                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">

                    <a
                        href="{{ route('facilities.index') }}"
                        class="btn-outline justify-center">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        class="btn-primary justify-center"
                        data-import-submit>

                        <span data-import-label>
                            Validate &amp; Import
                        </span>

                    </button>

                </div>

            </form>

        </section>


        <aside class="space-y-4">

            <section class="card p-5">

                <h2 class="font-heading text-sm font-semibold text-primary">
                    Import Template
                </h2>

                <p class="mt-2 text-xs leading-5 text-slate-500">
                    Start from the official CSV template to ensure column names
                    match the import specification.
                </p>

                <a
                    href="{{ route('facilities.import.template') }}"
                    class="btn-outline mt-4 w-full justify-center">

                    Download CSV Template

                </a>

            </section>


            <section class="card p-5">

                <h2 class="font-heading text-sm font-semibold text-primary">
                    Supported Values
                </h2>

                <div class="mt-3 space-y-3 text-xs leading-5 text-slate-500">

                    <div>
                        <p class="font-semibold text-slate-700">
                            Facility Type
                        </p>

                        <p>
                            conference_room, meeting_room, training_room,
                            function_room, vehicle, other
                        </p>
                    </div>

                    <div>
                        <p class="font-semibold text-slate-700">
                            Status
                        </p>

                        <p>
                            available, maintenance, unavailable, archived
                        </p>
                    </div>

                </div>

            </section>


            <section class="rounded-xl border border-warning/30 bg-warning/5 p-4">

                <p class="text-xs font-semibold text-primary">
                    Import Safety
                </p>

                <p class="mt-1 text-xs leading-5 text-slate-600">
                    Existing facility names and duplicates inside the file are rejected.
                    Imports do not overwrite existing facilities.
                </p>

            </section>

        </aside>

    </div>

</div>


<script>
    const importForm =
        document.querySelector(
            '[data-import-form]'
        );

    if (importForm) {
        importForm.addEventListener(
            'submit',
            () => {

                const button =
                    importForm.querySelector(
                        '[data-import-submit]'
                    );

                const label =
                    importForm.querySelector(
                        '[data-import-label]'
                    );

                if (button) {
                    button.disabled = true;
                    button.setAttribute(
                        'aria-busy',
                        'true'
                    );
                }

                if (label) {
                    label.textContent =
                        'Validating...';
                }

            }
        );
    }
</script>

@endsection