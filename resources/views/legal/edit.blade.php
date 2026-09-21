@extends('layouts.app')

@section('title', 'Edit Legal Record')

@section('content')

<div class="mx-auto max-w-5xl space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>

            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-secondary">
                Legal Management
            </p>

            <h2 class="mt-1 font-heading text-2xl font-bold text-primary">
                Edit Legal Record
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Update classification, assignment, deadlines, legal context, and supporting information.
            </p>

        </div>


        <a
            href="{{ route('legal.index') }}"
            class="btn-outline">
            Back to Legal Management
        </a>

    </div>


    <form
        method="POST"
        action="{{ route('legal.update', $legal) }}"
        enctype="multipart/form-data"
        class="card overflow-hidden">

        @csrf
        @method('PUT')


        <div class="space-y-7 p-5 sm:p-6">

            <section>

                <p class="mb-4 text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                    Matter Identity
                </p>

                <div class="grid gap-4 md:grid-cols-2">

                    <div class="md:col-span-2">

                        <label class="label" for="edit_legal_title">
                            Record Title
                            <span class="text-error">*</span>
                        </label>

                        <input
                            id="edit_legal_title"
                            type="text"
                            name="title"
                            required
                            value="{{ old('title', $legal->title) }}"
                            class="input">

                    </div>


                    <div>

                        <label class="label" for="edit_legal_type">
                            Record Type
                        </label>

                        <select
                            id="edit_legal_type"
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

                        <label class="label" for="edit_legal_category">
                            Legal Category
                        </label>

                        <input
                            id="edit_legal_category"
                            type="text"
                            name="legal_category"
                            value="{{ old('legal_category', $legal->legal_category) }}"
                            class="input">

                    </div>


                    <div>

                        <label class="label" for="edit_legal_reference">
                            Reference Number
                        </label>

                        <input
                            id="edit_legal_reference"
                            type="text"
                            name="reference_number"
                            value="{{ old('reference_number', $legal->reference_number) }}"
                            class="input">

                    </div>


                    <div>

                        <label class="label" for="edit_legal_authority">
                            Issuing Authority
                        </label>

                        <input
                            id="edit_legal_authority"
                            type="text"
                            name="issuing_authority"
                            value="{{ old('issuing_authority', $legal->issuing_authority) }}"
                            class="input">

                    </div>


                    <div>

                        <label class="label" for="edit_legal_jurisdiction">
                            Jurisdiction
                        </label>

                        <input
                            id="edit_legal_jurisdiction"
                            type="text"
                            name="jurisdiction"
                            value="{{ old('jurisdiction', $legal->jurisdiction) }}"
                            class="input">

                    </div>


                    <div>

                        <label class="label" for="edit_legal_status">
                            Matter Status
                        </label>

                        <select
                            id="edit_legal_status"
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

                </div>

            </section>


            <section class="border-t border-border pt-6">

                <p class="mb-4 text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                    Priority & Assignment
                </p>

                <div class="grid gap-4 md:grid-cols-2">

                    <div>

                        <label class="label" for="edit_legal_priority">
                            Priority
                        </label>

                        <select
                            id="edit_legal_priority"
                            name="priority"
                            class="input">

                            @foreach (['low', 'medium', 'high', 'critical'] as $priority)

                                <option
                                    value="{{ $priority }}"
                                    @selected(old('priority', $legal->priority) === $priority)>

                                    {{ str($priority)->headline() }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div>

                        <label class="label" for="edit_legal_confidentiality">
                            Confidentiality
                        </label>

                        <select
                            id="edit_legal_confidentiality"
                            name="confidentiality_level"
                            class="input">

                            @foreach (['internal', 'confidential', 'restricted'] as $level)

                                <option
                                    value="{{ $level }}"
                                    @selected(old('confidentiality_level', $legal->confidentiality_level) === $level)>

                                    {{ str($level)->headline() }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div>

                        <label class="label" for="edit_legal_assigned">
                            Assigned Officer
                        </label>

                        <select
                            id="edit_legal_assigned"
                            name="assigned_user_id"
                            class="input">

                            <option value="">
                                Unassigned
                            </option>

                            @foreach ($assignableOfficers as $officer)

                                <option
                                    value="{{ $officer->id }}"
                                    @selected((string) old('assigned_user_id', $legal->assigned_user_id) === (string) $officer->id)>

                                    {{ $officer->full_name }}
                                    — {{ \App\Models\User::ROLES[$officer->app_role] ?? str($officer->app_role)->headline() }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div>

                        <label class="label" for="edit_legal_email">
                            Responsible Email
                        </label>

                        <input
                            id="edit_legal_email"
                            type="email"
                            name="responsible_officer_email"
                            value="{{ old('responsible_officer_email', $legal->responsible_officer_email) }}"
                            class="input">

                    </div>

                </div>

            </section>


            <section class="border-t border-border pt-6">

                <p class="mb-4 text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                    Important Dates
                </p>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

                    <div>
                        <label class="label" for="edit_issue_date">Issue Date</label>
                        <input
                            id="edit_issue_date"
                            type="date"
                            name="issue_date"
                            value="{{ old('issue_date', $legal->issue_date?->toDateString()) }}"
                            class="input">
                    </div>

                    <div>
                        <label class="label" for="edit_expiration_date">Expiration Date</label>
                        <input
                            id="edit_expiration_date"
                            type="date"
                            name="expiration_date"
                            value="{{ old('expiration_date', $legal->expiration_date?->toDateString()) }}"
                            class="input">
                    </div>

                    <div>
                        <label class="label" for="edit_due_date">Due Date</label>
                        <input
                            id="edit_due_date"
                            type="date"
                            name="due_date"
                            value="{{ old('due_date', $legal->due_date?->toDateString()) }}"
                            class="input">
                    </div>

                    <div>
                        <label class="label" for="edit_next_action_date">Next Action Date</label>
                        <input
                            id="edit_next_action_date"
                            type="date"
                            name="next_action_date"
                            value="{{ old('next_action_date', $legal->next_action_date?->toDateString()) }}"
                            class="input">
                    </div>

                </div>


                <div class="mt-4">

                    <label class="label" for="edit_next_action">
                        Next Required Action
                    </label>

                    <input
                        id="edit_next_action"
                        type="text"
                        name="next_action"
                        maxlength="500"
                        value="{{ old('next_action', $legal->next_action) }}"
                        class="input">

                </div>

            </section>


            <section class="border-t border-border pt-6">

                <p class="mb-4 text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                    Legal Context
                </p>

                <div class="grid gap-4 lg:grid-cols-2">

                    <div>

                        <label class="label" for="edit_description">
                            Description
                        </label>

                        <textarea
                            id="edit_description"
                            name="description"
                            rows="5"
                            class="input min-h-[130px]">{{ old('description', $legal->description) }}</textarea>

                    </div>


                    <div>

                        <label class="label" for="edit_legal_basis">
                            Legal Basis / Regulation
                        </label>

                        <textarea
                            id="edit_legal_basis"
                            name="legal_basis"
                            rows="5"
                            class="input min-h-[130px]">{{ old('legal_basis', $legal->legal_basis) }}</textarea>

                    </div>


                    <div class="lg:col-span-2">

                        <label class="label" for="edit_legal_notes">
                            Legal Notes
                        </label>

                        <textarea
                            id="edit_legal_notes"
                            name="legal_notes"
                            rows="5"
                            class="input min-h-[130px]">{{ old('legal_notes', $legal->legal_notes) }}</textarea>

                    </div>

                </div>

            </section>


            <section class="border-t border-border pt-6">

                <label class="label" for="edit_legal_file">
                    Replace Supporting File
                </label>

                @if ($legal->file_name)

                    <p class="mb-2 text-xs text-slate-500">
                        Current file:
                        <strong class="font-semibold text-primary">
                            {{ $legal->file_name }}
                        </strong>
                    </p>

                @endif

                <input
                    id="edit_legal_file"
                    type="file"
                    name="file"
                    class="input">

                @error('file')
                    <p class="mt-1 text-xs font-medium text-error">
                        {{ $message }}
                    </p>
                @enderror

            </section>

        </div>


        <div class="flex flex-col-reverse gap-2 border-t border-border bg-background/40 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">

            <a
                href="{{ route('legal.index') }}"
                class="btn-outline text-center">
                Cancel
            </a>

            <button
                type="submit"
                class="btn-primary">
                Save Changes
            </button>

        </div>

    </form>

</div>

@endsection