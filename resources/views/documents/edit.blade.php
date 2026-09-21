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


<div class="space-y-4">

    {{-- =====================================================
         MANAGE DOCUMENT HEADER
    ====================================================== --}}
    <section class="card overflow-hidden">

        <div class="px-5 py-5 sm:px-6">

            <a
                href="{{ route('documents.show', $document) }}"
                class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-primary transition hover:text-secondary">

                <svg
                    class="h-3.5 w-3.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7" />

                </svg>

                Document Details

            </a>


            <div class="mt-4 flex items-start gap-3">

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/5 text-primary">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-8.5a2.12 2.12 0 013 3L12 14l-4 1 1-4 6.5-6.5z" />

                    </svg>

                </div>


                <div class="min-w-0">

                    <p class="text-[9px] font-semibold uppercase tracking-[0.16em] text-secondary">
                        Document Management
                    </p>

                    <h1 class="mt-1 font-heading text-xl font-bold tracking-tight text-primary sm:text-2xl">
                        Manage Document
                    </h1>

                    <p class="mt-1 break-words text-sm font-medium text-slate-600">
                        {{ $document->title }}
                    </p>


                    <div class="mt-2 flex flex-wrap items-center gap-1.5">

                        <span class="rounded-full bg-primary/5 px-2.5 py-1 text-[9px] font-semibold text-primary">
                            {{ str($document->category)->headline() }}
                        </span>

                        <span class="rounded-full bg-accent/10 px-2.5 py-1 text-[9px] font-semibold text-primary">
                            {{ str($document->confidentiality)->headline() }}
                        </span>

                        <span class="rounded-full bg-primary/5 px-2.5 py-1 text-[9px] font-semibold text-primary">
                            v{{ $document->version ?? 1 }}
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- =====================================================
         EDIT METADATA
    ====================================================== --}}
    <section
        id="edit-metadata"
        class="card overflow-hidden scroll-mt-24">

        <div class="flex items-start justify-between gap-4 border-b border-border px-5 py-4 sm:px-6">

            <div>

                <h2 class="font-heading text-sm font-semibold text-primary">
                    Edit Metadata
                </h2>

                <p class="mt-0.5 text-[10px] leading-4 text-slate-400">
                    Update document classification and descriptive information without replacing the stored file.
                </p>

            </div>


            <span class="hidden rounded-full bg-primary/5 px-2.5 py-1 text-[9px] font-semibold text-primary sm:inline-flex">
                Metadata
            </span>

        </div>


        <form
            method="POST"
            action="{{ route('documents.update', $document) }}">

            @csrf
            @method('PUT')


            <div class="space-y-5 p-5 sm:p-6">

                {{-- Validation --}}
                @if ($errors->any())

                    <div class="rounded-xl border border-error/20 bg-error/5 px-4 py-3">

                        <div class="flex items-start gap-3">

                            <svg
                                class="mt-0.5 h-4 w-4 shrink-0 text-error"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v3m0 4h.01M10.3 3.6L2.5 17a2 2 0 001.7 3h15.6a2 2 0 001.7-3L13.7 3.6a2 2 0 00-3.4 0z" />

                            </svg>


                            <div>

                                <p class="text-xs font-semibold text-error">
                                    Please review the information below.
                                </p>

                                <ul class="mt-1.5 list-disc space-y-0.5 pl-4 text-[10px] text-error">

                                    @foreach ($errors->all() as $error)

                                        <li>
                                            {{ $error }}
                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                        </div>

                    </div>

                @endif


                {{-- =====================================================
                     ESSENTIAL INFORMATION
                ====================================================== --}}
                <section>

                    <div class="mb-3">

                        <h3 class="text-xs font-semibold text-primary">
                            Document Information
                        </h3>

                        <p class="mt-0.5 text-[10px] text-slate-400">
                            Core information used to identify and classify this record.
                        </p>

                    </div>


                    <div class="grid gap-3 sm:grid-cols-2">

                        <div class="sm:col-span-2">

                            <label class="label">
                                Document Title
                                <span class="text-error">*</span>
                            </label>

                            <input
                                name="title"
                                value="{{ old('title', $document->title) }}"
                                required
                                class="input">

                            @error('title')
                                <p class="mt-1 text-[10px] text-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        <div>

                            <label class="label">
                                Category
                                <span class="text-error">*</span>
                            </label>

                            <select
                                name="category"
                                required
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
                                    'other',
                                ] as $category)

                                    <option
                                        value="{{ $category }}"
                                        @selected(
                                            old(
                                                'category',
                                                $document->category
                                            ) === $category
                                        )>

                                        {{ str($category)->headline() }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div>

                            <label class="label">
                                Confidentiality
                                <span class="text-error">*</span>
                            </label>

                            <select
                                name="confidentiality"
                                required
                                class="input">

                                @foreach ([
                                    'general',
                                    'restricted',
                                    'confidential',
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

                    </div>

                </section>


                <div class="border-t border-border"></div>


                {{-- =====================================================
                     OWNERSHIP AND DATES
                ====================================================== --}}
                <section>

                    <div class="mb-3">

                        <h3 class="text-xs font-semibold text-primary">
                            Ownership & Dates
                        </h3>

                        <p class="mt-0.5 text-[10px] text-slate-400">
                            Optional administrative ownership and document validity information.
                        </p>

                    </div>


                    <div class="grid gap-3 sm:grid-cols-2">

                        <div>

                            <label class="label">
                                Department
                            </label>

                            <input
                                name="department"
                                value="{{ old('department', $document->department) }}"
                                placeholder="e.g. Administration"
                                class="input">

                        </div>


                        <div>

                            <label class="label">
                                Owner Email
                            </label>

                            <input
                                type="email"
                                name="owner_email"
                                value="{{ old('owner_email', $document->owner_email) }}"
                                placeholder="name@example.com"
                                class="input">

                        </div>


                        <div>

                            <label class="label">
                                Document Date
                            </label>

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

                            <label class="label">
                                Expiration Date
                            </label>

                            <input
                                type="date"
                                name="expiration_date"
                                value="{{ old(
                                    'expiration_date',
                                    $document->expiration_date?->format('Y-m-d')
                                ) }}"
                                class="input">

                            <p class="mt-1 text-[9px] text-slate-400">
                                Must not be earlier than the document date.
                            </p>

                        </div>

                    </div>

                </section>


                {{-- =====================================================
                     RELATED SYSTEM RECORD
                ====================================================== --}}
                <details
                    @if (
                        old(
                            'related_type',
                            $currentRelatedType
                        )
                    )
                        open
                    @endif
                    class="group overflow-hidden rounded-xl border border-border">

                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 transition hover:bg-background/60">

                        <div>

                            <p class="text-xs font-semibold text-primary">
                                Related System Record
                            </p>

                            <p class="mt-0.5 text-[10px] text-slate-400">
                                Optionally connect this document to another module record.
                            </p>

                        </div>


                        <div class="flex items-center gap-2">

                            <span class="rounded-full bg-primary/5 px-2 py-0.5 text-[9px] font-semibold text-slate-500">
                                Optional
                            </span>

                            <svg
                                class="h-4 w-4 text-slate-400 transition-transform duration-200 group-open:rotate-180"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7" />

                            </svg>

                        </div>

                    </summary>


                    <div class="grid gap-3 border-t border-border bg-background/25 p-4 sm:grid-cols-2">

                        <div>

                            <label class="label">
                                Related Module
                            </label>

                            <select
                                id="related_type"
                                name="related_type"
                                class="input">

                                <option value="">
                                    Not linked
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

                            <label class="label">
                                Related Record
                            </label>

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
                                            ) == $contract->id
                                            &&
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
                                            ) == $legalRecord->id
                                            &&
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
                                            ) == $reservation->id
                                            &&
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
                                            ) == $visitor->id
                                            &&
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

                </details>


                {{-- Description --}}
                <div>

                    <label class="label">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="3"
                        class="input resize-y"
                        placeholder="Add useful descriptive information about this document...">{{ old('description', $document->description) }}</textarea>

                </div>

            </div>


            {{-- Footer --}}
            <div class="flex flex-col-reverse gap-2 border-t border-border bg-background/30 px-5 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-6">

                <p class="text-[9px] text-slate-400">
                    Changes update metadata only. The stored file is not replaced.
                </p>


                <div class="flex flex-col-reverse gap-2 sm:flex-row">

                    <a
                        href="{{ route('documents.show', $document) }}"
                        class="btn-outline justify-center">

                        Cancel

                    </a>


                    <button
                        type="submit"
                        class="btn-primary justify-center">

                        Save Changes

                    </button>

                </div>

            </div>

        </form>

    </section>

    {{-- =====================================================
         DOCUMENT OPERATIONS
    ====================================================== --}}
    @include(
        'documents._document-operations',
        [
            'document' => $document,
            'allContainers' => $allContainers,
        ]
    )

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