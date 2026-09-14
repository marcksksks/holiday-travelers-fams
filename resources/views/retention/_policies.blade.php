@can('manageRetention')

    @if ($retentionTab === 'policies')

        @php
            $policyCategories = [
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
            ];

            $activePolicyCount = $allPolicies
                ->where('is_active', true)
                ->count();

            $inactivePolicyCount =
                $allPolicies->count() - $activePolicyCount;

            $failedPolicyForm = old('_policy_form');
            $failedPolicyId = old('_policy_id');

            $isFailedCreate =
                $failedPolicyForm === 'create';
        @endphp


        <section class="space-y-4">

            {{-- Workspace heading --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

                <div>

                    <div class="flex flex-wrap items-center gap-2">

                        <h3 class="font-heading text-lg font-semibold text-primary">
                            Retention Policies
                        </h3>

                        <span class="rounded-full bg-primary/5 px-2.5 py-1 text-[10px] font-semibold text-primary ring-1 ring-inset ring-primary/10">
                            {{ $allPolicies->count() }}
                            {{ Str::plural('policy', $allPolicies->count()) }}
                        </span>

                    </div>

                    <p class="mt-1 max-w-2xl text-xs leading-5 text-slate-500">
                        Manage retention periods, record categories, legal bases, and policy availability.
                    </p>


                    @if ($allPolicies->isNotEmpty())

                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-slate-500">

                            <span class="inline-flex items-center gap-1.5">

                                <span class="h-2 w-2 rounded-full bg-success"></span>

                                {{ $activePolicyCount }}
                                active

                            </span>

                            @if ($inactivePolicyCount > 0)

                                <span class="inline-flex items-center gap-1.5">

                                    <span class="h-2 w-2 rounded-full bg-slate-300"></span>

                                    {{ $inactivePolicyCount }}
                                    inactive

                                </span>

                            @endif

                        </div>

                    @endif

                </div>


                <button
                    type="button"
                    onclick="document.getElementById('newRetentionPolicyDialog').showModal()"
                    class="btn-secondary self-start sm:self-auto">

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4" />

                    </svg>

                    New Policy

                </button>

            </div>


            {{-- Policy register --}}
            @if ($allPolicies->isNotEmpty())

                <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-card">

                    {{-- Desktop header --}}
                    <div class="hidden grid-cols-[minmax(260px,1.8fr)_minmax(150px,1fr)_130px_120px_auto] gap-4 border-b border-border bg-background/40 px-5 py-3 lg:grid">

                        <span class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                            Policy
                        </span>

                        <span class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                            Category
                        </span>

                        <span class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                            Retention
                        </span>

                        <span class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                            Status
                        </span>

                        <span class="text-right text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                            Action
                        </span>

                    </div>


                    <div class="divide-y divide-border">

                        @foreach ($allPolicies as $policy)

                            @php
                                $isFailedEdit =
                                    $failedPolicyForm === 'edit'
                                    && (string) $failedPolicyId === (string) $policy->id;

                                $editName =
                                    $isFailedEdit
                                    ? old('name')
                                    : $policy->name;

                                $editCategory =
                                    $isFailedEdit
                                    ? old('record_category')
                                    : $policy->record_category;

                                $editYears =
                                    $isFailedEdit
                                    ? old('retention_years')
                                    : $policy->retention_years;

                                $editLegalBasis =
                                    $isFailedEdit
                                    ? old('legal_basis')
                                    : $policy->legal_basis;

                                $editDescription =
                                    $isFailedEdit
                                    ? old('description')
                                    : $policy->description;

                                $editIsActive =
                                    (string) (
                                        $isFailedEdit
                                        ? old('is_active', '0')
                                        : ($policy->is_active ? '1' : '0')
                                    ) === '1';
                            @endphp


                            <article class="px-5 py-4 transition hover:bg-background/40">

                                <div class="grid gap-4 lg:grid-cols-[minmax(260px,1.8fr)_minmax(150px,1fr)_130px_120px_auto] lg:items-center">

                                    {{-- Policy --}}
                                    <div class="min-w-0">

                                        <h4 class="truncate font-heading text-sm font-semibold text-primary">
                                            {{ $policy->name }}
                                        </h4>

                                        @if ($policy->legal_basis)

                                            <p class="mt-1 line-clamp-1 text-[11px] text-slate-500">
                                                {{ $policy->legal_basis }}
                                            </p>

                                        @elseif ($policy->description)

                                            <p class="mt-1 line-clamp-1 text-[11px] text-slate-500">
                                                {{ $policy->description }}
                                            </p>

                                        @else

                                            <p class="mt-1 text-[11px] text-slate-400">
                                                No legal basis or description provided.
                                            </p>

                                        @endif

                                    </div>


                                    {{-- Category --}}
                                    <div>

                                        <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 lg:hidden">
                                            Category
                                        </p>

                                        <span class="inline-flex rounded-full bg-primary/5 px-2.5 py-1 text-[11px] font-medium text-primary ring-1 ring-inset ring-primary/10">
                                            {{ str($policy->record_category)->headline() }}
                                        </span>

                                    </div>


                                    {{-- Retention --}}
                                    <div>

                                        <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 lg:hidden">
                                            Retention
                                        </p>

                                        <p class="text-sm font-medium text-slate-700">
                                            {{ $policy->retention_years }}
                                            {{ Str::plural('year', $policy->retention_years) }}
                                        </p>

                                    </div>


                                    {{-- Status --}}
                                    <div>

                                        <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 lg:hidden">
                                            Status
                                        </p>

                                        @if ($policy->is_active)

                                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-success">

                                                <span class="h-2 w-2 rounded-full bg-success"></span>

                                                Active

                                            </span>

                                        @else

                                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500">

                                                <span class="h-2 w-2 rounded-full bg-slate-300"></span>

                                                Inactive

                                            </span>

                                        @endif

                                    </div>


                                    {{-- Action --}}
                                    <div class="lg:text-right">

                                        <button
                                            type="button"
                                            onclick="document.getElementById('editRetentionPolicyDialog-{{ $policy->id }}').showModal()"
                                            class="btn-outline w-full justify-center whitespace-nowrap text-xs lg:w-auto">

                                            Manage

                                            <svg
                                                class="h-3.5 w-3.5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true">

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 5l7 7-7 7" />

                                            </svg>

                                        </button>

                                    </div>

                                </div>

                            </article>


                            {{-- Edit Policy dialog --}}
                            <dialog
                                id="editRetentionPolicyDialog-{{ $policy->id }}"
                                class="relative max-h-[90vh] w-[calc(100%_-_2rem)] max-w-2xl overflow-hidden rounded-2xl border border-border bg-card p-0 shadow-2xl backdrop:bg-slate-950/50">

                                <button
                                    type="button"
                                    onclick="this.closest('dialog').close()"
                                    class="absolute right-4 top-4 z-20 flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary"
                                    aria-label="Close policy management dialog">

                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />

                                    </svg>

                                </button>


                                <div class="flex max-h-[90vh] flex-col overflow-hidden">

                                    <div class="shrink-0 border-b border-border bg-background/60 px-5 py-5 pr-16">

                                        <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                                            Policy Management
                                        </p>

                                        <h3 class="mt-1 font-heading text-base font-semibold text-primary">
                                            Manage Retention Policy
                                        </h3>

                                        <p class="mt-1 truncate text-xs text-slate-500">
                                            {{ $policy->name }}
                                        </p>

                                    </div>


                                    <form
                                        method="POST"
                                        action="{{ route('retention-policies.update', $policy) }}"
                                        class="min-h-0 flex-1 overflow-y-auto">

                                        @csrf
                                        @method('PUT')

                                        <input
                                            type="hidden"
                                            name="_policy_form"
                                            value="edit">

                                        <input
                                            type="hidden"
                                            name="_policy_id"
                                            value="{{ $policy->id }}">


                                        <div class="space-y-5 p-5">

                                            <div>

                                                <label
                                                    for="edit_policy_name_{{ $policy->id }}"
                                                    class="label">
                                                    Policy Name
                                                    <span class="text-error">*</span>
                                                </label>

                                                <input
                                                    id="edit_policy_name_{{ $policy->id }}"
                                                    type="text"
                                                    name="name"
                                                    value="{{ $editName }}"
                                                    maxlength="255"
                                                    required
                                                    class="input">

                                                @if ($isFailedEdit)
                                                    @error('name')
                                                        <p class="mt-1 text-xs font-medium text-error">
                                                            {{ $message }}
                                                        </p>
                                                    @enderror
                                                @endif

                                            </div>


                                            <div class="grid gap-4 sm:grid-cols-2">

                                                <div>

                                                    <label
                                                        for="edit_policy_category_{{ $policy->id }}"
                                                        class="label">
                                                        Record Category
                                                        <span class="text-error">*</span>
                                                    </label>

                                                    <select
                                                        id="edit_policy_category_{{ $policy->id }}"
                                                        name="record_category"
                                                        required
                                                        class="input">

                                                        @foreach ($policyCategories as $category)

                                                            <option
                                                                value="{{ $category }}"
                                                                @selected($editCategory === $category)>

                                                                {{ str($category)->headline() }}

                                                            </option>

                                                        @endforeach

                                                    </select>

                                                    @if ($isFailedEdit)
                                                        @error('record_category')
                                                            <p class="mt-1 text-xs font-medium text-error">
                                                                {{ $message }}
                                                            </p>
                                                        @enderror
                                                    @endif

                                                </div>


                                                <div>

                                                    <label
                                                        for="edit_policy_years_{{ $policy->id }}"
                                                        class="label">
                                                        Retention Period
                                                        <span class="text-error">*</span>
                                                    </label>

                                                    <div class="relative">

                                                        <input
                                                            id="edit_policy_years_{{ $policy->id }}"
                                                            type="number"
                                                            name="retention_years"
                                                            value="{{ $editYears }}"
                                                            min="1"
                                                            max="100"
                                                            required
                                                            class="input pr-16">

                                                        <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs text-slate-400">
                                                            years
                                                        </span>

                                                    </div>

                                                    @if ($isFailedEdit)
                                                        @error('retention_years')
                                                            <p class="mt-1 text-xs font-medium text-error">
                                                                {{ $message }}
                                                            </p>
                                                        @enderror
                                                    @endif

                                                </div>

                                            </div>


                                            <div>

                                                <label
                                                    for="edit_policy_legal_basis_{{ $policy->id }}"
                                                    class="label">
                                                    Legal Basis
                                                </label>

                                                <textarea
                                                    id="edit_policy_legal_basis_{{ $policy->id }}"
                                                    name="legal_basis"
                                                    rows="3"
                                                    class="input"
                                                    placeholder="Law, regulation, policy, or contractual basis...">{{ $editLegalBasis }}</textarea>

                                                @if ($isFailedEdit)
                                                    @error('legal_basis')
                                                        <p class="mt-1 text-xs font-medium text-error">
                                                            {{ $message }}
                                                        </p>
                                                    @enderror
                                                @endif

                                            </div>


                                            <div>

                                                <label
                                                    for="edit_policy_description_{{ $policy->id }}"
                                                    class="label">
                                                    Description
                                                </label>

                                                <textarea
                                                    id="edit_policy_description_{{ $policy->id }}"
                                                    name="description"
                                                    rows="3"
                                                    class="input"
                                                    placeholder="Describe how this retention policy should be applied...">{{ $editDescription }}</textarea>

                                                @if ($isFailedEdit)
                                                    @error('description')
                                                        <p class="mt-1 text-xs font-medium text-error">
                                                            {{ $message }}
                                                        </p>
                                                    @enderror
                                                @endif

                                            </div>


                                            <div class="rounded-xl border border-border bg-background/40 p-4">

                                                <input
                                                    type="hidden"
                                                    name="is_active"
                                                    value="0">

                                                <label class="flex cursor-pointer items-start gap-3">

                                                    <input
                                                        type="checkbox"
                                                        name="is_active"
                                                        value="1"
                                                        @checked($editIsActive)
                                                        class="mt-0.5 rounded border-border text-primary focus:ring-primary">

                                                    <span>

                                                        <span class="block text-sm font-semibold text-primary">
                                                            Active policy
                                                        </span>

                                                        <span class="mt-0.5 block text-xs leading-5 text-slate-500">
                                                            Active policies are available when assigning retention rules to records.
                                                        </span>

                                                    </span>

                                                </label>

                                                @if ($isFailedEdit)
                                                    @error('is_active')
                                                        <p class="mt-2 text-xs font-medium text-error">
                                                            {{ $message }}
                                                        </p>
                                                    @enderror
                                                @endif

                                            </div>

                                        </div>


                                        <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-border bg-background/40 px-5 py-4 sm:flex-row sm:justify-end">

                                            <button
                                                type="button"
                                                onclick="this.closest('dialog').close()"
                                                class="btn-outline justify-center">
                                                Cancel
                                            </button>

                                            <button
                                                type="submit"
                                                class="btn-primary justify-center">
                                                Save Changes
                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </dialog>

                        @endforeach

                    </div>

                </div>

            @else

                <div class="rounded-2xl border border-dashed border-border bg-card px-6 py-12 text-center">

                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/5 text-primary">

                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6M9 16h6M9 8h6M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z" />

                        </svg>

                    </div>

                    <h3 class="mt-4 font-heading text-base font-semibold text-primary">
                        No retention policies yet
                    </h3>

                    <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">
                        Create a policy to define how long organizational records should be retained.
                    </p>

                    <button
                        type="button"
                        onclick="document.getElementById('newRetentionPolicyDialog').showModal()"
                        class="btn-secondary mt-4">
                        New Policy
                    </button>

                </div>

            @endif


            {{-- New Policy dialog --}}
            <dialog
                id="newRetentionPolicyDialog"
                class="relative max-h-[90vh] w-[calc(100%_-_2rem)] max-w-2xl overflow-hidden rounded-2xl border border-border bg-card p-0 shadow-2xl backdrop:bg-slate-950/50">

                <button
                    type="button"
                    onclick="this.closest('dialog').close()"
                    class="absolute right-4 top-4 z-20 flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary"
                    aria-label="Close New Policy dialog">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />

                    </svg>

                </button>


                <div class="flex max-h-[90vh] flex-col overflow-hidden">

                    <div class="shrink-0 border-b border-border bg-background/60 px-5 py-5 pr-16">

                        <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                            Retention Governance
                        </p>

                        <h3 class="mt-1 font-heading text-base font-semibold text-primary">
                            New Retention Policy
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Define the category, retention period, and governance basis for this policy.
                        </p>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('retention-policies.store') }}"
                        class="min-h-0 flex-1 overflow-y-auto">

                        @csrf

                        <input
                            type="hidden"
                            name="_policy_form"
                            value="create">


                        <div class="space-y-5 p-5">

                            <div>

                                <label
                                    for="new_policy_name"
                                    class="label">
                                    Policy Name
                                    <span class="text-error">*</span>
                                </label>

                                <input
                                    id="new_policy_name"
                                    type="text"
                                    name="name"
                                    value="{{ $isFailedCreate ? old('name') : '' }}"
                                    maxlength="255"
                                    required
                                    placeholder="e.g. Contract Records Policy"
                                    class="input">

                                @if ($isFailedCreate)
                                    @error('name')
                                        <p class="mt-1 text-xs font-medium text-error">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                @endif

                            </div>


                            <div class="grid gap-4 sm:grid-cols-2">

                                <div>

                                    <label
                                        for="new_policy_category"
                                        class="label">
                                        Record Category
                                        <span class="text-error">*</span>
                                    </label>

                                    <select
                                        id="new_policy_category"
                                        name="record_category"
                                        required
                                        class="input">

                                        @foreach ($policyCategories as $category)

                                            <option
                                                value="{{ $category }}"
                                                @selected(
                                                    (
                                                        $isFailedCreate
                                                        ? old('record_category', 'administrative')
                                                        : 'administrative'
                                                    ) === $category
                                                )>

                                                {{ str($category)->headline() }}

                                            </option>

                                        @endforeach

                                    </select>

                                    @if ($isFailedCreate)
                                        @error('record_category')
                                            <p class="mt-1 text-xs font-medium text-error">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    @endif

                                </div>


                                <div>

                                    <label
                                        for="new_policy_years"
                                        class="label">
                                        Retention Period
                                        <span class="text-error">*</span>
                                    </label>

                                    <div class="relative">

                                        <input
                                            id="new_policy_years"
                                            type="number"
                                            name="retention_years"
                                            value="{{ $isFailedCreate ? old('retention_years', 5) : 5 }}"
                                            min="1"
                                            max="100"
                                            required
                                            class="input pr-16">

                                        <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs text-slate-400">
                                            years
                                        </span>

                                    </div>

                                    @if ($isFailedCreate)
                                        @error('retention_years')
                                            <p class="mt-1 text-xs font-medium text-error">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    @endif

                                </div>

                            </div>


                            <div>

                                <label
                                    for="new_policy_legal_basis"
                                    class="label">
                                    Legal Basis
                                </label>

                                <textarea
                                    id="new_policy_legal_basis"
                                    name="legal_basis"
                                    rows="3"
                                    class="input"
                                    placeholder="Law, regulation, policy, or contractual basis...">{{ $isFailedCreate ? old('legal_basis') : '' }}</textarea>

                                @if ($isFailedCreate)
                                    @error('legal_basis')
                                        <p class="mt-1 text-xs font-medium text-error">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                @endif

                            </div>


                            <div>

                                <label
                                    for="new_policy_description"
                                    class="label">
                                    Description
                                </label>

                                <textarea
                                    id="new_policy_description"
                                    name="description"
                                    rows="3"
                                    class="input"
                                    placeholder="Describe how this policy should be applied...">{{ $isFailedCreate ? old('description') : '' }}</textarea>

                                @if ($isFailedCreate)
                                    @error('description')
                                        <p class="mt-1 text-xs font-medium text-error">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                @endif

                            </div>


                            <div class="rounded-xl border border-border bg-background/40 p-4">

                                <input
                                    type="hidden"
                                    name="is_active"
                                    value="0">

                                <label class="flex cursor-pointer items-start gap-3">

                                    <input
                                        type="checkbox"
                                        name="is_active"
                                        value="1"
                                        @checked(
                                            (string) (
                                                $isFailedCreate
                                                ? old('is_active', '1')
                                                : '1'
                                            ) === '1'
                                        )
                                        class="mt-0.5 rounded border-border text-primary focus:ring-primary">

                                    <span>

                                        <span class="block text-sm font-semibold text-primary">
                                            Active policy
                                        </span>

                                        <span class="mt-0.5 block text-xs leading-5 text-slate-500">
                                            Make this policy available immediately when assigning retention rules.
                                        </span>

                                    </span>

                                </label>

                                @if ($isFailedCreate)
                                    @error('is_active')
                                        <p class="mt-2 text-xs font-medium text-error">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                @endif

                            </div>

                        </div>


                        <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-border bg-background/40 px-5 py-4 sm:flex-row sm:justify-end">

                            <button
                                type="button"
                                onclick="this.closest('dialog').close()"
                                class="btn-outline justify-center">
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="btn-primary justify-center">
                                Create Policy
                            </button>

                        </div>

                    </form>

                </div>

            </dialog>

        </section>


        {{-- Reopen the correct policy dialog after validation failure --}}
        @if (
            $errors->hasAny([
                'name',
                'record_category',
                'retention_years',
                'description',
                'legal_basis',
                'is_active'
            ])
            && $failedPolicyForm === 'create'
        )

            <script data-policy-create-validation>
                document.addEventListener('DOMContentLoaded', () => {
                    document
                        .getElementById('newRetentionPolicyDialog')
                        ?.showModal();
                });
            </script>

        @elseif (
            $errors->hasAny([
                'name',
                'record_category',
                'retention_years',
                'description',
                'legal_basis',
                'is_active'
            ])
            && $failedPolicyForm === 'edit'
            && $failedPolicyId
        )

            <script data-policy-edit-validation>
                document.addEventListener('DOMContentLoaded', () => {
                    document
                        .getElementById(
                            'editRetentionPolicyDialog-{{ $failedPolicyId }}'
                        )
                        ?.showModal();
                });
            </script>

        @endif

    @endif

@endcan