@extends('layouts.app')

@section('title', 'Edit Contract')

@section('content')

@php
    $partyValues = old('parties', $contract->parties ?? ['']);

    if (! is_array($partyValues)) {
        $partyValues = [$partyValues];
    }

    if (count($partyValues) === 0) {
        $partyValues = [''];
    }
@endphp

<div class="mx-auto max-w-4xl space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Edit Contract
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Update contract details without bypassing the legal review and approval workflow.
            </p>
        </div>

        <a
            href="{{ route('contracts.index') }}"
            class="btn-outline">
            Back to Contracts
        </a>

    </div>


    <form
        method="POST"
        action="{{ route('contracts.update', $contract) }}"
        enctype="multipart/form-data"
        class="card overflow-hidden">

        @csrf
        @method('PUT')

        <div class="border-b border-border bg-background/60 px-6 py-5">

            <div class="flex flex-wrap items-center justify-between gap-3">

                <div>
                    <h3 class="font-heading text-base font-semibold text-primary">
                        Contract Information
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Workflow decisions remain controlled separately.
                    </p>
                </div>

                <span class="badge badge-info">
                    {{ str($contract->status)->headline() }}
                </span>

            </div>

        </div>


        <div class="grid gap-5 p-6 md:grid-cols-2">

            <div>
                <label for="contract_number" class="label">
                    Contract Number
                </label>

                <input
                    id="contract_number"
                    type="text"
                    name="contract_number"
                    value="{{ old('contract_number', $contract->contract_number) }}"
                    class="input">
            </div>


            <div>
                <label for="contract_type" class="label">
                    Contract Type <span class="text-error">*</span>
                </label>

                <select
                    id="contract_type"
                    name="contract_type"
                    class="input">

                    @foreach ([
                        'hotel',
                        'tour_operator',
                        'transportation',
                        'supplier',
                        'partnership',
                        'service',
                        'other'
                    ] as $type)

                        <option
                            value="{{ $type }}"
                            @selected(old('contract_type', $contract->contract_type) === $type)>

                            {{ str($type)->headline() }}

                        </option>

                    @endforeach

                </select>
            </div>


            <div class="md:col-span-2">
                <label for="title" class="label">
                    Title <span class="text-error">*</span>
                </label>

                <input
                    id="title"
                    type="text"
                    name="title"
                    required
                    value="{{ old('title', $contract->title) }}"
                    class="input">
            </div>


            <div class="md:col-span-2">
                <label class="label">
                    Parties
                </label>

                <div class="space-y-2">

                    @foreach ($partyValues as $party)

                        <input
                            type="text"
                            name="parties[]"
                            value="{{ $party }}"
                            placeholder="Contracting party"
                            class="input">

                    @endforeach

                </div>

                <p class="mt-1.5 text-xs text-slate-400">
                    Existing parties are preserved as separate fields.
                </p>
            </div>


            <div>
                <label for="start_date" class="label">
                    Start Date
                </label>

                <input
                    id="start_date"
                    type="date"
                    name="start_date"
                    value="{{ old('start_date', $contract->start_date ? \Illuminate\Support\Carbon::parse($contract->start_date)->format('Y-m-d') : '') }}"
                    class="input">
            </div>


            <div>
                <label for="end_date" class="label">
                    End Date
                </label>

                <input
                    id="end_date"
                    type="date"
                    name="end_date"
                    value="{{ old('end_date', $contract->end_date ? \Illuminate\Support\Carbon::parse($contract->end_date)->format('Y-m-d') : '') }}"
                    class="input">
            </div>


            <div>
                <label for="value" class="label">
                    Contract Value
                </label>

                <input
                    id="value"
                    type="number"
                    step="0.01"
                    min="0"
                    name="value"
                    value="{{ old('value', $contract->value) }}"
                    class="input">
            </div>


            <div>
                <label for="currency" class="label">
                    Currency
                </label>

                <input
                    id="currency"
                    type="text"
                    maxlength="8"
                    name="currency"
                    value="{{ old('currency', $contract->currency) }}"
                    placeholder="PHP"
                    class="input">
            </div>


            <div class="md:col-span-2">
                <label for="responsible_officer_email" class="label">
                    Responsible Officer Email
                </label>

                <input
                    id="responsible_officer_email"
                    type="email"
                    name="responsible_officer_email"
                    value="{{ old('responsible_officer_email', $contract->responsible_officer_email) }}"
                    class="input">
            </div>


            <div class="md:col-span-2">
                <label for="description" class="label">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="input">{{ old('description', $contract->description) }}</textarea>
            </div>


            <div class="md:col-span-2">
                <label for="file" class="label">
                    Replace Contract File
                </label>

                @if ($contract->file_name)

                    <p class="mb-2 text-xs text-slate-500">
                        Current file:
                        <span class="font-medium text-primary">
                            {{ $contract->file_name }}
                        </span>
                    </p>

                @endif

                <input
                    id="file"
                    type="file"
                    name="file"
                    class="block w-full rounded-xl border border-border bg-white text-xs text-slate-500 file:mr-3 file:border-0 file:bg-primary/10 file:px-4 file:py-3 file:font-button file:text-xs file:font-semibold file:text-primary hover:file:bg-primary/15">

                <p class="mt-1.5 text-xs text-slate-400">
                    Leave this empty to keep the current contract file.
                </p>
            </div>

        </div>


        <div class="border-t border-border bg-warning/5 px-6 py-4">

            <p class="text-xs leading-relaxed text-slate-500">
                Editing contract metadata does not constitute legal clearance or management approval. Workflow actions remain available only to their authorized roles.
            </p>

        </div>


        <div class="flex flex-col-reverse gap-3 border-t border-border bg-background/40 px-6 py-5 sm:flex-row sm:justify-end">

            <a
                href="{{ route('contracts.index') }}"
                class="btn-outline justify-center">
                Cancel
            </a>

            <button
                type="submit"
                class="btn-secondary justify-center">
                Update Contract
            </button>

        </div>

    </form>

</div>

@endsection