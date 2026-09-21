<div
    id="legal-create-modal"
    data-legal-modal
    class="fixed inset-0 z-[130] hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="legal-create-title">

    <button
        type="button"
        data-legal-modal-backdrop
        class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm"
        aria-label="Close legal record dialog">
    </button>


    <div class="relative flex min-h-full items-center justify-center p-3 sm:p-6">

        <div class="flex max-h-[calc(100vh-2rem)] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-2xl">

            <div class="flex items-start justify-between gap-4 border-b border-border bg-background/50 px-5 py-4 sm:px-6">

                <div class="flex items-start gap-3">

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 5v14M5 12h14" />
                        </svg>

                    </div>

                    <div>

                        <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-secondary">
                            Legal Management
                        </p>

                        <h3
                            id="legal-create-title"
                            class="mt-0.5 font-heading text-lg font-semibold text-primary">
                            New Legal Record
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Register and assign a legal, regulatory, compliance, or case matter.
                        </p>

                    </div>

                </div>


                <button
                    type="button"
                    data-legal-modal-close
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary"
                    aria-label="Close">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>

                </button>

            </div>


            <form
                method="POST"
                action="{{ route('legal.store') }}"
                enctype="multipart/form-data"
                class="min-h-0 flex-1 overflow-y-auto">

                @csrf

                <input
                    type="hidden"
                    name="form_context"
                    value="create">


                <div class="space-y-7 p-5 sm:p-6">

                    {{-- Matter identity --}}
                    <section>

                        <div class="mb-4">

                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                                Matter Identity
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Core information used to identify and classify the legal record.
                            </p>

                        </div>


                        <div class="grid gap-4 md:grid-cols-2">

                            <div class="md:col-span-2">

                                <label
                                    for="legal_create_title"
                                    class="label">

                                    Record Title
                                    <span class="text-error">*</span>

                                </label>

                                <input
                                    id="legal_create_title"
                                    type="text"
                                    name="title"
                                    required
                                    value="{{ old('form_context') === 'create' ? old('title') : '' }}"
                                    placeholder="e.g. Business Permit Renewal 2026"
                                    class="input @error('title') border-error @enderror">

                                @error('title')
                                    <p class="mt-1 text-xs font-medium text-error">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>


                            <div>

                                <label
                                    for="legal_create_type"
                                    class="label">

                                    Record Type
                                    <span class="text-error">*</span>

                                </label>

                                <select
                                    id="legal_create_type"
                                    name="record_type"
                                    required
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
                                            @selected(old('form_context') === 'create' && old('record_type', 'permit') === $type)>

                                            {{ str($type)->headline() }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            <div>

                                <label
                                    for="legal_create_category"
                                    class="label">
                                    Legal Category
                                </label>

                                <input
                                    id="legal_create_category"
                                    type="text"
                                    name="legal_category"
                                    value="{{ old('form_context') === 'create' ? old('legal_category') : '' }}"
                                    placeholder="e.g. Regulatory Compliance"
                                    class="input">

                            </div>


                            <div>

                                <label
                                    for="legal_create_reference"
                                    class="label">
                                    Reference Number
                                </label>

                                <input
                                    id="legal_create_reference"
                                    type="text"
                                    name="reference_number"
                                    value="{{ old('form_context') === 'create' ? old('reference_number') : '' }}"
                                    placeholder="e.g. BP-2026-00125"
                                    class="input">

                            </div>


                            <div>

                                <label
                                    for="legal_create_authority"
                                    class="label">
                                    Issuing Authority
                                </label>

                                <input
                                    id="legal_create_authority"
                                    type="text"
                                    name="issuing_authority"
                                    value="{{ old('form_context') === 'create' ? old('issuing_authority') : '' }}"
                                    placeholder="e.g. City Government"
                                    class="input">

                            </div>


                            <div>

                                <label
                                    for="legal_create_jurisdiction"
                                    class="label">
                                    Jurisdiction
                                </label>

                                <input
                                    id="legal_create_jurisdiction"
                                    type="text"
                                    name="jurisdiction"
                                    value="{{ old('form_context') === 'create' ? old('jurisdiction') : '' }}"
                                    placeholder="e.g. Philippines"
                                    class="input">

                            </div>


                            <div>

                                <label
                                    for="legal_create_status"
                                    class="label">

                                    Matter Status
                                    <span class="text-error">*</span>

                                </label>

                                <select
                                    id="legal_create_status"
                                    name="status"
                                    required
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
                                            @selected(old('form_context') === 'create' && old('status', 'active') === $status)>

                                            {{ str($status)->headline() }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>

                    </section>


                    {{-- Risk and assignment --}}
                    <section class="border-t border-border pt-6">

                        <div class="mb-4">

                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                                Priority & Assignment
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Define sensitivity, urgency, and the officer responsible for follow-up.
                            </p>

                        </div>


                        <div class="grid gap-4 md:grid-cols-2">

                            <div>

                                <label
                                    for="legal_create_priority"
                                    class="label">

                                    Priority
                                    <span class="text-error">*</span>

                                </label>

                                <select
                                    id="legal_create_priority"
                                    name="priority"
                                    required
                                    class="input">

                                    @foreach (['low', 'medium', 'high', 'critical'] as $priority)

                                        <option
                                            value="{{ $priority }}"
                                            @selected(
                                                (
                                                    old('form_context') === 'create'
                                                        ? old('priority', 'medium')
                                                        : 'medium'
                                                ) === $priority
                                            )>

                                            {{ str($priority)->headline() }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            <div>

                                <label
                                    for="legal_create_confidentiality"
                                    class="label">

                                    Confidentiality
                                    <span class="text-error">*</span>

                                </label>

                                <select
                                    id="legal_create_confidentiality"
                                    name="confidentiality_level"
                                    required
                                    class="input">

                                    @foreach (['internal', 'confidential', 'restricted'] as $level)

                                        <option
                                            value="{{ $level }}"
                                            @selected(old('form_context') === 'create' && old('confidentiality_level', 'internal') === $level)>

                                            {{ str($level)->headline() }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            <div>

                                <label
                                    for="legal_create_assignee"
                                    class="label">
                                    Assigned Officer
                                </label>

                                <select
                                    id="legal_create_assignee"
                                    name="assigned_user_id"
                                    class="input">

                                    <option value="">
                                        Unassigned
                                    </option>

                                    @foreach ($assignableOfficers as $officer)

                                        <option
                                            value="{{ $officer->id }}"
                                            @selected(old('form_context') === 'create' && (string) old('assigned_user_id') === (string) $officer->id)>

                                            {{ $officer->full_name }}
                                            — {{ \App\Models\User::ROLES[$officer->app_role] ?? str($officer->app_role)->headline() }}

                                        </option>

                                    @endforeach

                                </select>

                                @error('assigned_user_id')
                                    <p class="mt-1 text-xs font-medium text-error">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>


                            <div>

                                <label
                                    for="legal_create_officer_email"
                                    class="label">
                                    Fallback Responsible Email
                                </label>

                                <input
                                    id="legal_create_officer_email"
                                    type="email"
                                    name="responsible_officer_email"
                                    value="{{ old('form_context') === 'create' ? old('responsible_officer_email') : '' }}"
                                    placeholder="legal@example.com"
                                    class="input">

                                <p class="mt-1 text-[11px] text-slate-400">
                                    Assigned officer email takes precedence when an account is selected.
                                </p>

                            </div>

                        </div>

                    </section>


                    {{-- Important dates --}}
                    <section class="border-t border-border pt-6">

                        <div class="mb-4">

                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                                Important Dates
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Deadline state is calculated automatically from these dates.
                            </p>

                        </div>


                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

                            <div>
                                <label for="legal_create_issue_date" class="label">
                                    Issue Date
                                </label>
                                <input
                                    id="legal_create_issue_date"
                                    type="date"
                                    name="issue_date"
                                    value="{{ old('form_context') === 'create' ? old('issue_date') : '' }}"
                                    class="input">
                            </div>


                            <div>
                                <label for="legal_create_expiration_date" class="label">
                                    Expiration Date
                                </label>
                                <input
                                    id="legal_create_expiration_date"
                                    type="date"
                                    name="expiration_date"
                                    value="{{ old('form_context') === 'create' ? old('expiration_date') : '' }}"
                                    class="input">
                            </div>


                            <div>
                                <label for="legal_create_due_date" class="label">
                                    Due Date
                                </label>
                                <input
                                    id="legal_create_due_date"
                                    type="date"
                                    name="due_date"
                                    value="{{ old('form_context') === 'create' ? old('due_date') : '' }}"
                                    class="input">
                            </div>


                            <div>
                                <label for="legal_create_next_action_date" class="label">
                                    Next Action Date
                                </label>
                                <input
                                    id="legal_create_next_action_date"
                                    type="date"
                                    name="next_action_date"
                                    value="{{ old('form_context') === 'create' ? old('next_action_date') : '' }}"
                                    class="input">
                            </div>

                        </div>


                        <div class="mt-4">

                            <label for="legal_create_next_action" class="label">
                                Next Required Action
                            </label>

                            <input
                                id="legal_create_next_action"
                                type="text"
                                name="next_action"
                                maxlength="500"
                                value="{{ old('form_context') === 'create' ? old('next_action') : '' }}"
                                placeholder="e.g. Submit renewal requirements to City Government"
                                class="input">

                        </div>

                    </section>


                    {{-- Context --}}
                    <section class="border-t border-border pt-6">

                        <div class="mb-4">

                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                                Legal Context
                            </p>

                        </div>


                        <div class="grid gap-4 lg:grid-cols-2">

                            <div>

                                <label for="legal_create_description" class="label">
                                    Description
                                </label>

                                <textarea
                                    id="legal_create_description"
                                    name="description"
                                    rows="4"
                                    placeholder="Brief description of the legal matter..."
                                    class="input min-h-[110px]">{{ old('form_context') === 'create' ? old('description') : '' }}</textarea>

                            </div>


                            <div>

                                <label for="legal_create_basis" class="label">
                                    Legal Basis / Regulation
                                </label>

                                <textarea
                                    id="legal_create_basis"
                                    name="legal_basis"
                                    rows="4"
                                    placeholder="Applicable law, regulation, ordinance, policy, or legal basis..."
                                    class="input min-h-[110px]">{{ old('form_context') === 'create' ? old('legal_basis') : '' }}</textarea>

                            </div>


                            <div class="lg:col-span-2">

                                <label for="legal_create_notes" class="label">
                                    Legal Notes
                                </label>

                                <textarea
                                    id="legal_create_notes"
                                    name="legal_notes"
                                    rows="4"
                                    placeholder="Legal observations, obligations, recommendations, or follow-up notes..."
                                    class="input min-h-[110px]">{{ old('form_context') === 'create' ? old('legal_notes') : '' }}</textarea>

                            </div>

                        </div>

                    </section>


                    {{-- File --}}
                    <section class="border-t border-border pt-6">

                        <label for="legal_create_file" class="label">
                            Supporting File
                        </label>

                        <input
                            id="legal_create_file"
                            type="file"
                            name="file"
                            class="input">

                        <p class="mt-1.5 text-[11px] text-slate-400">
                            Maximum upload size: 20 MB. File security policy is enforced by the system.
                        </p>

                        @error('file')
                            <p class="mt-1 text-xs font-medium text-error">
                                {{ $message }}
                            </p>
                        @enderror

                    </section>

                </div>


                <div class="flex flex-col-reverse gap-2 border-t border-border bg-background/40 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">

                    <button
                        type="button"
                        data-legal-modal-close
                        class="btn-outline">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn-primary">
                        Save Legal Record
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>