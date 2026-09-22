<dialog
    id="createContractDialog"
    @if ($errors->any())
        open
    @endif
    class="relative max-h-[92vh] w-[calc(100%_-_2rem)] max-w-3xl overflow-hidden rounded-2xl border border-border bg-card p-0 shadow-2xl backdrop:bg-slate-950/50">

    <button
        type="button"
        onclick="this.closest('dialog').close()"
        class="absolute right-4 top-4 z-20 flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary"
        aria-label="Close New Contract dialog">

        <svg
            class="h-5 w-5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12" />

        </svg>

    </button>


    <div class="flex max-h-[92vh] flex-col overflow-hidden">

        <div class="shrink-0 border-b border-border bg-background/60 px-6 py-5 pr-16">

            <div class="flex items-center gap-3">

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 7h8M8 11h8M8 15h5M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z" />

                    </svg>

                </div>


                <div>

                    <h3 class="font-heading text-base font-semibold text-primary">
                        New Contract
                    </h3>

                    <p class="mt-0.5 text-xs text-slate-500">
                        Create a draft before beginning legal review and management approval.
                    </p>

                </div>

            </div>

        </div>


        <form
            method="POST"
            action="{{ route('contracts.store') }}"
            enctype="multipart/form-data"
            class="min-h-0 flex-1 overflow-y-auto">

            @csrf


            <div class="grid gap-5 p-6 md:grid-cols-2">

                <div>

                    <label
                        for="contract_number"
                        class="label">

                        Contract Number

                    </label>

                    <input
                        id="contract_number"
                        type="text"
                        name="contract_number"
                        value="{{ old('contract_number') }}"
                        placeholder="e.g. HT-CTR-2026-001"
                        class="input">

                </div>


                <div>

                    <label
                        for="contract_type"
                        class="label">

                        Contract Type

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
                            'other',
                        ] as $type)

                            <option
                                value="{{ $type }}"
                                @selected(old('contract_type', 'service') === $type)>

                                {{ str($type)->headline() }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="md:col-span-2">

                    <label
                        for="title"
                        class="label">

                        Contract Title
                        <span class="text-error">*</span>

                    </label>

                    <input
                        id="title"
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        placeholder="e.g. Hotel Partnership Agreement"
                        class="input @error('title') border-error @enderror">

                    @error('title')

                        <p class="mt-1.5 text-xs font-medium text-error">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                <div class="md:col-span-2">

                    <label
                        for="party"
                        class="label">

                        Counterparty / Partner

                    </label>

                    <input
                        id="party"
                        type="text"
                        name="parties[]"
                        value="{{ old('parties.0') }}"
                        placeholder="e.g. Sunrise Hotel Corporation"
                        class="input">

                </div>


                <div>

                    <label
                        for="start_date"
                        class="label">

                        Start Date

                    </label>

                    <input
                        id="start_date"
                        type="date"
                        name="start_date"
                        value="{{ old('start_date') }}"
                        class="input">

                </div>


                <div>

                    <label
                        for="end_date"
                        class="label">

                        End Date

                    </label>

                    <input
                        id="end_date"
                        type="date"
                        name="end_date"
                        value="{{ old('end_date') }}"
                        class="input @error('end_date') border-error @enderror">

                    @error('end_date')

                        <p class="mt-1.5 text-xs font-medium text-error">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                <div>

                    <label
                        for="value"
                        class="label">

                        Contract Value

                    </label>

                    <input
                        id="value"
                        type="number"
                        step="0.01"
                        min="0"
                        name="value"
                        value="{{ old('value') }}"
                        placeholder="0.00"
                        class="input">

                </div>


                <div>

                    <label
                        for="currency"
                        class="label">

                        Currency

                    </label>

                    <input
                        id="currency"
                        type="text"
                        name="currency"
                        maxlength="8"
                        value="{{ old('currency', 'PHP') }}"
                        class="input uppercase">

                </div>


                <div class="md:col-span-2">

                    <label
                        for="responsible_officer_email"
                        class="label">

                        Responsible Officer

                    </label>

                    <input
                        id="responsible_officer_email"
                        type="email"
                        name="responsible_officer_email"
                        value="{{ old('responsible_officer_email', auth()->user()->email) }}"
                        class="input">

                </div>


                <div class="md:col-span-2">

                    <label
                        for="description"
                        class="label">

                        Description

                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Briefly describe the purpose and scope of this contract..."
                        class="input">{{ old('description') }}</textarea>

                </div>


                <div class="md:col-span-2">

                    <label
                        for="file"
                        class="label">

                        Contract File

                    </label>

                    <input
                        id="file"
                        type="file"
                        name="file"
                        accept="{{ \App\Support\DocumentUploadPolicy::acceptAttribute() }}"
                        class="block w-full rounded-xl border border-border bg-white text-xs text-slate-500 file:mr-3 file:border-0 file:bg-primary/10 file:px-4 file:py-3 file:font-button file:text-xs file:font-semibold file:text-primary hover:file:bg-primary/15">

                    <p class="mt-1.5 text-xs text-slate-400">
                        Maximum file size: 20 MB.
                    </p>

                    @error('file')

                        <p class="mt-1.5 text-xs font-medium text-error">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </div>


            <div class="border-t border-border bg-warning/5 px-6 py-4">

                <p class="text-xs leading-relaxed text-slate-500">
                    New contracts are saved as drafts. Legal review and management approval remain separate controlled workflow actions.
                </p>

            </div>


            <div class="flex flex-col-reverse gap-3 border-t border-border bg-background/40 px-6 py-5 sm:flex-row sm:justify-end">

                <button
                    type="button"
                    onclick="this.closest('dialog').close()"
                    class="btn-outline justify-center">

                    Cancel

                </button>


                <button
                    type="submit"
                    class="btn-primary justify-center">

                    Save Contract Draft

                </button>

            </div>

        </form>

    </div>

</dialog>