@extends('layouts.app')

@section('title', 'Edit Legal Record')

@section('content')

<div class="mx-auto max-w-4xl space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Edit Legal Record
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Update the legal record metadata, dates, responsible officer, and supporting file.
            </p>
        </div>

        <a
            href="{{ route('legal.index') }}"
            class="btn-outline">
            Back to Legal Records
        </a>

    </div>


    <form
        method="POST"
        action="{{ route('legal.update', $legal) }}"
        enctype="multipart/form-data"
        class="card overflow-hidden">

        @csrf
        @method('PUT')

        <div class="border-b border-border bg-background/60 px-6 py-5">

            <h3 class="font-heading text-base font-semibold text-primary">
                Record Information
            </h3>

            <p class="mt-1 text-xs text-slate-500">
                Fields marked with an asterisk are required.
            </p>

        </div>


        <div class="grid gap-5 p-6 md:grid-cols-2">

            <div class="md:col-span-2">
                <label for="title" class="label">
                    Title <span class="text-error">*</span>
                </label>

                <input
                    id="title"
                    type="text"
                    name="title"
                    required
                    value="{{ old('title', $legal->title) }}"
                    class="input @error('title') border-error @enderror">

                @error('title')
                    <p class="mt-1.5 text-xs font-medium text-error">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            <div>
                <label for="record_type" class="label">
                    Record Type <span class="text-error">*</span>
                </label>

                <select
                    id="record_type"
                    name="record_type"
                    class="input">

                    @foreach ([
                        'permit',
                        'license',
                        'legal_case',
                        'requirement',
                        'legal_document'
                    ] as $type)

                        <option
                            value="{{ $type }}"
                            @selected(old('record_type', $legal->record_type) === $type)>

                            {{ str($type)->headline() }}

                        </option>

                    @endforeach

                </select>
            </div>


            <div>
                <label for="reference_number" class="label">
                    Reference Number
                </label>

                <input
                    id="reference_number"
                    type="text"
                    name="reference_number"
                    value="{{ old('reference_number', $legal->reference_number) }}"
                    class="input">
            </div>


            <div>
                <label for="issuing_authority" class="label">
                    Issuing Authority
                </label>

                <input
                    id="issuing_authority"
                    type="text"
                    name="issuing_authority"
                    value="{{ old('issuing_authority', $legal->issuing_authority) }}"
                    class="input">
            </div>


            <div>
                <label for="responsible_officer_email" class="label">
                    Responsible Officer Email
                </label>

                <input
                    id="responsible_officer_email"
                    type="email"
                    name="responsible_officer_email"
                    value="{{ old('responsible_officer_email', $legal->responsible_officer_email) }}"
                    class="input">
            </div>


            <div>
                <label for="issue_date" class="label">
                    Issue Date
                </label>

                <input
                    id="issue_date"
                    type="date"
                    name="issue_date"
                    value="{{ old('issue_date', $legal->issue_date ? \Illuminate\Support\Carbon::parse($legal->issue_date)->format('Y-m-d') : '') }}"
                    class="input">
            </div>


            <div>
                <label for="expiration_date" class="label">
                    Expiration Date
                </label>

                <input
                    id="expiration_date"
                    type="date"
                    name="expiration_date"
                    value="{{ old('expiration_date', $legal->expiration_date ? \Illuminate\Support\Carbon::parse($legal->expiration_date)->format('Y-m-d') : '') }}"
                    class="input">
            </div>


            <div class="md:col-span-2">
                <label for="status" class="label">
                    Status <span class="text-error">*</span>
                </label>

                <select
                    id="status"
                    name="status"
                    class="input">

                    @foreach ([
                        'active',
                        'pending',
                        'expiring_soon',
                        'expired',
                        'renewed',
                        'closed'
                    ] as $status)

                        <option
                            value="{{ $status }}"
                            @selected(old('status', $legal->status) === $status)>

                            {{ str($status)->headline() }}

                        </option>

                    @endforeach

                </select>
            </div>


            <div class="md:col-span-2">
                <label for="description" class="label">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="input">{{ old('description', $legal->description) }}</textarea>
            </div>


            <div class="md:col-span-2">
                <label for="legal_notes" class="label">
                    Legal Notes
                </label>

                <textarea
                    id="legal_notes"
                    name="legal_notes"
                    rows="4"
                    class="input">{{ old('legal_notes', $legal->legal_notes) }}</textarea>
            </div>


            <div class="md:col-span-2">
                <label for="file" class="label">
                    Replace Supporting File
                </label>

                @if ($legal->file_name)

                    <p class="mb-2 text-xs text-slate-500">
                        Current file:
                        <span class="font-medium text-primary">
                            {{ $legal->file_name }}
                        </span>
                    </p>

                @endif

                <input
                    id="file"
                    type="file"
                    name="file"
                    accept="{{ \App\Support\DocumentUploadPolicy::acceptAttribute() }}"
                    class="block w-full rounded-xl border border-border bg-white text-xs text-slate-500 file:mr-3 file:border-0 file:bg-primary/10 file:px-4 file:py-3 file:font-button file:text-xs file:font-semibold file:text-primary hover:file:bg-primary/15">

                <p class="mt-1.5 text-xs text-slate-400">
                    Leave this empty to keep the existing file.
                </p>
            </div>

        </div>


        <div class="flex flex-col-reverse gap-3 border-t border-border bg-background/40 px-6 py-5 sm:flex-row sm:justify-end">

            <a
                href="{{ route('legal.index') }}"
                class="btn-outline justify-center">
                Cancel
            </a>

            <button
                type="submit"
                class="btn-secondary justify-center">
                Update Legal Record
            </button>

        </div>

    </form>

</div>

@endsection