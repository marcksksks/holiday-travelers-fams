@extends('layouts.app')

@section('title', 'Manage Document')

@section('content')

@php
    $currentRelatedType = null;
    $currentRelatedId = null;

    if ($document->linked_contract_id) {
        $currentRelatedType = 'contract';
        $currentRelatedId = $document->linked_contract_id;
    } elseif ($document->linked_legal_record_id) {
        $currentRelatedType = 'legal';
        $currentRelatedId = $document->linked_legal_record_id;
    } elseif ($document->linked_reservation_id) {
        $currentRelatedType = 'reservation';
        $currentRelatedId = $document->linked_reservation_id;
    } elseif ($document->linked_visitor_id) {
        $currentRelatedType = 'visitor';
        $currentRelatedId = $document->linked_visitor_id;
    }
@endphp


<div class="space-y-6">

    <div>

        <a
            href="{{ route('documents.show', $document) }}"
            class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-primary">

            &larr; Back to Document Details

        </a>

        <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

        <h1 class="font-heading text-2xl font-bold text-primary">
            Manage Document
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            {{ $document->title }}
        </p>

    </div>


    {{-- Metadata --}}
    <section
        id="edit-metadata"
        class="card overflow-hidden">

        <div class="border-b border-border px-6 py-5">

            <h2 class="font-heading text-base font-semibold text-primary">
                Edit Metadata
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Update classification and descriptive information without replacing the file.
            </p>

        </div>


        <form
            method="POST"
            action="{{ route('documents.update', $document) }}"
            class="space-y-5 p-6">

            @csrf
            @method('PUT')


            <div class="grid gap-4 md:grid-cols-2">

                <div>
                    <label class="label">Document Title *</label>

                    <input
                        name="title"
                        value="{{ old('title', $document->title) }}"
                        required
                        class="input">
                </div>


                <div>
                    <label class="label">Category *</label>

                    <select
                        name="category"
                        class="input">

                        @foreach ([
                            'administrative',
                            'contract',
                            'legal',
                            'permit',
                            'license',
                            'compliance',
                            'partnership',
                            'financial',
                            'operational',
                            'other'
                        ] as $category)

                            <option
                                value="{{ $category }}"
                                @selected(
                                    old('category', $document->category) === $category
                                )>

                                {{ str($category)->headline() }}

                            </option>

                        @endforeach

                    </select>
                </div>


                <div>
                    <label class="label">Department</label>

                    <input
                        name="department"
                        value="{{ old('department', $document->department) }}"
                        class="input">
                </div>


                <div>
                    <label class="label">Owner Email</label>

                    <input
                        type="email"
                        name="owner_email"
                        value="{{ old('owner_email', $document->owner_email) }}"
                        class="input">
                </div>


                <div>
                    <label class="label">Confidentiality *</label>

                    <select
                        name="confidentiality"
                        class="input">

                        @foreach ([
                            'general',
                            'restricted',
                            'confidential'
                        ] as $level)

                            <option
                                value="{{ $level }}"
                                @selected(
                                    old(
                                        'confidentiality',
                                        $document->confidentiality
                                    ) === $level
                                )>

                                {{ str($level)->headline() }}

                            </option>

                        @endforeach

                    </select>
                </div>


                <div>
                    <label class="label">Document Date</label>

                    <input
                        type="date"
                        name="document_date"
                        value="{{ old(
                            'document_date',
                            $document->document_date?->format('Y-m-d')
                        ) }}"
                        class="input">
                </div>


                <div>
                    <label class="label">Expiration Date</label>

                    <input
                        type="date"
                        name="expiration_date"
                        value="{{ old(
                            'expiration_date',
                            $document->expiration_date?->format('Y-m-d')
                        ) }}"
                        class="input">
                </div>

            </div>


            <div class="rounded-xl border border-border bg-background/50 p-4">

                <p class="font-heading text-sm font-semibold text-primary">
                    Related System Record
                </p>

                <div class="mt-4 grid gap-4 md:grid-cols-2">

                    <div>
                        <label class="label">Related Module</label>

                        <select
                            id="related_type"
                            name="related_type"
                            class="input">

                            <option value="">
                                None
                            </option>

                            @if ($contracts->isNotEmpty())
                                <option
                                    value="contract"
                                    @selected(
                                        old(
                                            'related_type',
                                            $currentRelatedType
                                        ) === 'contract'
                                    )>
                                    Contract
                                </option>
                            @endif

                            @if ($legalRecords->isNotEmpty())
                                <option
                                    value="legal"
                                    @selected(
                                        old(
                                            'related_type',
                                            $currentRelatedType
                                        ) === 'legal'
                                    )>
                                    Legal Record
                                </option>
                            @endif

                            <option
                                value="reservation"
                                @selected(
                                    old(
                                        'related_type',
                                        $currentRelatedType
                                    ) === 'reservation'
                                )>
                                Facility Reservation
                            </option>

                            @if ($visitors->isNotEmpty())
                                <option
                                    value="visitor"
                                    @selected(
                                        old(
                                            'related_type',
                                            $currentRelatedType
                                        ) === 'visitor'
                                    )>
                                    Visitor Record
                                </option>
                            @endif

                        </select>
                    </div>


                    <div>
                        <label class="label">Related Record</label>

                        <select
                            id="related_id"
                            name="related_id"
                            class="input">

                            <option value="">
                                Select a record
                            </option>


                            @foreach ($contracts as $contract)

                                <option
                                    value="{{ $contract->id }}"
                                    data-related-type="contract"
                                    @selected(
                                        old(
                                            'related_id',
                                            $currentRelatedId
                                        ) == $contract->id &&
                                        old(
                                            'related_type',
                                            $currentRelatedType
                                        ) === 'contract'
                                    )>

                                    {{ $contract->contract_number ?: 'Contract #'.$contract->id }}
                                    - {{ $contract->title }}

                                </option>

                            @endforeach


                            @foreach ($legalRecords as $legalRecord)

                                <option
                                    value="{{ $legalRecord->id }}"
                                    data-related-type="legal"
                                    @selected(
                                        old(
                                            'related_id',
                                            $currentRelatedId
                                        ) == $legalRecord->id &&
                                        old(
                                            'related_type',
                                            $currentRelatedType
                                        ) === 'legal'
                                    )>

                                    {{ $legalRecord->reference_number ?: 'Legal #'.$legalRecord->id }}
                                    - {{ $legalRecord->title }}

                                </option>

                            @endforeach


                            @foreach ($reservations as $reservation)

                                <option
                                    value="{{ $reservation->id }}"
                                    data-related-type="reservation"
                                    @selected(
                                        old(
                                            'related_id',
                                            $currentRelatedId
                                        ) == $reservation->id &&
                                        old(
                                            'related_type',
                                            $currentRelatedType
                                        ) === 'reservation'
                                    )>

                                    #{{ $reservation->id }}
                                    - {{ $reservation->facility_name }}
                                    - {{ $reservation->date?->format('M d, Y') }}

                                </option>

                            @endforeach


                            @foreach ($visitors as $visitor)

                                <option
                                    value="{{ $visitor->id }}"
                                    data-related-type="visitor"
                                    @selected(
                                        old(
                                            'related_id',
                                            $currentRelatedId
                                        ) == $visitor->id &&
                                        old(
                                            'related_type',
                                            $currentRelatedType
                                        ) === 'visitor'
                                    )>

                                    #{{ $visitor->id }}
                                    - {{ $visitor->full_name }}

                                </option>

                            @endforeach

                        </select>
                    </div>

                </div>

            </div>


            <div>
                <label class="label">Description</label>

                <textarea
                    name="description"
                    rows="4"
                    class="input">{{ old('description', $document->description) }}</textarea>
            </div>


            @if ($errors->any())

                <div class="rounded-xl border border-error/20 bg-error/5 p-4">

                    <ul class="list-disc space-y-1 pl-5 text-sm text-error">

                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </div>

            @endif


            <div class="flex justify-end">

                <button
                    type="submit"
                    class="btn-primary">

                    Save Metadata

                </button>

            </div>

        </form>

    </section>


    {{-- Move --}}
    <section
        id="move-document"
        class="card overflow-hidden">

        <div class="border-b border-border px-6 py-5">

            <h2 class="font-heading text-base font-semibold text-primary">
                Move to Folder
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Change where this manually uploaded document appears in Document Management.
            </p>

        </div>


        <form
            method="POST"
            action="{{ route('documents.move', $document) }}"
            class="p-6">

            @csrf

            <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">

                <div>
                    <label class="label">
                        Destination Folder
                    </label>

                    <select
                        name="container_id"
                        class="input">

                        <option value="">
                            Unfiled
                        </option>

                        @foreach ($allContainers as $container)

                            <option
                                value="{{ $container->id }}"
                                @selected(
                                    $document->container_id ===
                                    $container->id
                                )>

                                {{ str_replace(
                                    '/',
                                    ' / ',
                                    $container->path
                                ) }}

                            </option>

                        @endforeach

                    </select>
                </div>


                <button
                    type="submit"
                    class="btn-primary">

                    Move Document

                </button>

            </div>

        </form>

    </section>


    {{-- Version --}}
    <section
        id="upload-version"
        class="card overflow-hidden">

        <div class="border-b border-border px-6 py-5">

            <h2 class="font-heading text-base font-semibold text-primary">
                Upload New Version
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Current version:
                <strong>v{{ $document->version ?? 1 }}</strong>
            </p>

        </div>


        <form
            method="POST"
            action="{{ route('documents.version', $document) }}"
            enctype="multipart/form-data"
            class="space-y-5 p-6">

            @csrf


            <div>
                <label class="label">
                    Replacement File *
                </label>

                <input
                    type="file"
                    name="file"
                    required
                    class="input">

                @if ($document->file_name)

                    <p class="mt-1.5 text-xs text-slate-500">
                        Current file:
                        {{ $document->file_name }}
                    </p>

                @endif
            </div>


            <div>
                <label class="label">
                    Version Note
                </label>

                <textarea
                    name="version_note"
                    rows="3"
                    placeholder="Describe what changed in this version..."
                    class="input"></textarea>
            </div>


            <div class="flex justify-end">

                <button
                    type="submit"
                    class="btn-primary">

                    Upload New Version

                </button>

            </div>

        </form>

    </section>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const typeSelect =
        document.getElementById('related_type');

    const recordSelect =
        document.getElementById('related_id');

    if (!typeSelect || !recordSelect) {
        return;
    }

    const options = Array.from(
        recordSelect.querySelectorAll(
            'option[data-related-type]'
        )
    );

    function syncRecords(clearSelection = false) {

        const type = typeSelect.value;

        if (clearSelection) {
            recordSelect.value = '';
        }

        options.forEach(function (option) {

            const matches =
                type &&
                option.dataset.relatedType === type;

            option.hidden = !matches;
            option.disabled = !matches;
        });

        recordSelect.disabled = !type;
    }

    typeSelect.addEventListener(
        'change',
        function () {
            syncRecords(true);
        }
    );

    syncRecords(false);
});
</script>

@endsection