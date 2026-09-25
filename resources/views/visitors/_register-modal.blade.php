@can('operateVisitorDesk')

    <div
        id="register-visitor"
        data-visitor-register-modal
        @if (
            $errors->any()
            &&
            old('_visitor_modal_context') === 'create'
        )
            data-open-on-error="true"
        @endif
        class="fixed inset-0 z-[80] hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="register-visitor-title">

        {{-- Backdrop --}}
        <div
            data-visitor-register-backdrop
            class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
        </div>


        <div class="relative flex min-h-full items-center justify-center p-2 sm:p-5">

            <div
                data-visitor-register-panel
                class="flex max-h-[calc(100vh-1rem)] w-full max-w-2xl flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-2xl sm:max-h-[calc(100vh-2.5rem)]">

                {{-- =====================================================
                     HEADER
                ====================================================== --}}
                <div class="flex shrink-0 items-start justify-between gap-4 border-b border-border px-5 py-4 sm:px-6">

                    <div class="flex min-w-0 items-start gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8zM19 8v6M22 11h-6" />

                            </svg>

                        </div>


                        <div>

                            <p class="text-[9px] font-semibold uppercase tracking-[0.16em] text-secondary">
                                Visitor Desk
                            </p>

                            <h2
                                id="register-visitor-title"
                                class="mt-0.5 font-heading text-lg font-semibold text-primary">

                                Register Visitor

                            </h2>

                            <p class="mt-1 text-xs text-slate-500">
                                Record an arriving visitor for reception processing.
                            </p>

                        </div>

                    </div>


                    <button
                        type="button"
                        data-visitor-register-close
                        aria-label="Close visitor registration"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary">

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

                </div>


                <form
                    method="POST"
                    action="{{ route('visitors.store') }}"
                    class="flex min-h-0 flex-1 flex-col">

                    @csrf

                    <input
                        type="hidden"
                        name="_visitor_modal_context"
                        value="create">

                    <input
                        id="appointment_id"
                        type="hidden"
                        name="appointment_id"
                        value="{{ old('appointment_id') }}">


                    {{-- =====================================================
                         SCROLLABLE FORM
                    ====================================================== --}}
                    <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6">

                        <div class="space-y-5">

                            {{-- Validation --}}
                            @if (
                                $errors->any()
                                &&
                                old('_visitor_modal_context') === 'create'
                            )

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
                                                Please review the visitor information.
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
                                 VISITOR IDENTITY
                            ====================================================== --}}
                            <section>

                                <div class="mb-3">

                                    <h3 class="text-xs font-semibold text-primary">
                                        Visitor Information
                                    </h3>

                                    <p class="mt-0.5 text-[10px] text-slate-400">
                                        Basic information identifying the arriving visitor.
                                    </p>

                                </div>


                                <div class="grid gap-3 sm:grid-cols-2">

                                    {{-- Full Name --}}
                                    <div class="sm:col-span-2">

                                        <label
                                            for="full_name"
                                            class="label">

                                            Full Name
                                            <span class="text-error">*</span>

                                        </label>


                                        <input
                                            id="full_name"
                                            type="text"
                                            name="full_name"
                                            value="{{ old('full_name') }}"
                                            required
                                            autocomplete="name"
                                            placeholder="Visitor's complete name"
                                            class="input @error('full_name') border-error focus:border-error focus:ring-error/20 @enderror">


                                        @error('full_name')

                                            <p class="mt-1 text-[10px] text-error">
                                                {{ $message }}
                                            </p>

                                        @enderror

                                    </div>


                                    {{-- Organization --}}
                                    <div>

                                        <label
                                            for="organization"
                                            class="label">

                                            Organization

                                        </label>

                                        <input
                                            id="organization"
                                            type="text"
                                            name="organization"
                                            value="{{ old('organization') }}"
                                            placeholder="Company or organization"
                                            class="input @error('organization') border-error focus:border-error focus:ring-error/20 @enderror">

                                    </div>


                                    {{-- Visitor Type --}}
                                    <div>

                                        <label
                                            for="visitor_type"
                                            class="label">

                                            Visitor Type

                                        </label>

                                        <select
                                            id="visitor_type"
                                            name="visitor_type"
                                            class="input @error('visitor_type') border-error focus:border-error focus:ring-error/20 @enderror">

                                            @foreach ([
                                                'customer',
                                                'business_partner',
                                                'supplier',
                                                'government',
                                                'applicant',
                                                'guest',
                                                'other',
                                            ] as $type)

                                                <option
                                                    value="{{ $type }}"
                                                    @selected(
                                                        old(
                                                            'visitor_type',
                                                            'guest'
                                                        ) === $type
                                                    )>

                                                    {{ str($type)->headline() }}

                                                </option>

                                            @endforeach

                                        </select>

                                    </div>


                                    {{-- Contact Number --}}
                                    <div>

                                        <label
                                            for="contact_number"
                                            class="label">

                                            Contact Number

                                        </label>

                                        <input
                                            id="contact_number"
                                            type="text"
                                            name="contact_number"
                                            value="{{ old('contact_number') }}"
                                            autocomplete="tel"
                                            placeholder="Visitor contact number"
                                            class="input @error('contact_number') border-error focus:border-error focus:ring-error/20 @enderror">

                                        @error('contact_number')

                                            <p class="mt-1 text-[10px] text-error">
                                                {{ $message }}
                                            </p>

                                        @enderror

                                    </div>


                                    {{-- Email --}}
                                    <div>

                                        <label
                                            for="email"
                                            class="label">

                                            Visitor Email

                                        </label>

                                        <input
                                            id="email"
                                            type="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            autocomplete="email"
                                            placeholder="visitor@example.com"
                                            class="input @error('email') border-error focus:border-error focus:ring-error/20 @enderror">

                                        @error('email')

                                            <p class="mt-1 text-[10px] text-error">
                                                {{ $message }}
                                            </p>

                                        @enderror

                                    </div>

                                </div>

                            </section>


                            <div class="border-t border-border"></div>


                            {{-- =====================================================
                                 VISIT INFORMATION
                            ====================================================== --}}
                            <section>

                                <div class="mb-3">

                                    <h3 class="text-xs font-semibold text-primary">
                                        Visit Information
                                    </h3>

                                    <p class="mt-0.5 text-[10px] text-slate-400">
                                        Identify the host and reason for the visit.
                                    </p>

                                </div>


                                <div class="space-y-3">

                                    {{-- Host Name --}}
                                    <div>

                                        <label
                                            for="host_name"
                                            class="label">

                                            Host Name

                                        </label>

                                        <input
                                            id="host_name"
                                            type="text"
                                            name="host_name"
                                            value="{{ old('host_name') }}"
                                            placeholder="Person the visitor is meeting"
                                            class="input @error('host_name') border-error focus:border-error focus:ring-error/20 @enderror">

                                        @error('host_name')

                                            <p class="mt-1 text-[10px] text-error">
                                                {{ $message }}
                                            </p>

                                        @enderror

                                    </div>


                                    {{-- Host --}}
                                    <div>

                                        <label
                                            for="host_email"
                                            class="label">

                                            Host Email

                                        </label>

                                        <input
                                            id="host_email"
                                            type="email"
                                            name="host_email"
                                            value="{{ old('host_email') }}"
                                            placeholder="host@example.com"
                                            class="input @error('host_email') border-error focus:border-error focus:ring-error/20 @enderror">


                                        @error('host_email')

                                            <p class="mt-1 text-[10px] text-error">
                                                {{ $message }}
                                            </p>

                                        @enderror

                                    </div>


                                    {{-- Purpose --}}
                                    <div>

                                        <label
                                            for="purpose"
                                            class="label">

                                            Purpose of Visit

                                        </label>

                                        <textarea
                                            id="purpose"
                                            name="purpose"
                                            rows="3"
                                            placeholder="Briefly describe the purpose of the visit..."
                                            class="input resize-y @error('purpose') border-error focus:border-error focus:ring-error/20 @enderror">{{ old('purpose') }}</textarea>

                                    </div>

                                </div>

                            </section>


                            {{-- =====================================================
                                 ARRIVAL TYPE
                            ====================================================== --}}
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-border bg-background/50 p-3.5 transition hover:border-accent/40">

                                <input
                                    type="hidden"
                                    name="is_walk_in"
                                    value="0">

                                <input
                                    id="is_walk_in"
                                    type="checkbox"
                                    name="is_walk_in"
                                    value="1"
                                    @checked(old('is_walk_in', true))
                                    class="mt-0.5 h-4 w-4 rounded border-slate-300 text-secondary focus:ring-secondary">


                                <span class="min-w-0">

                                    <span class="block text-xs font-semibold text-primary">
                                        Walk-in Visitor
                                    </span>

                                    <span class="mt-0.5 block text-[10px] leading-4 text-slate-500">
                                        Use this for a visitor who arrived without a prior appointment.
                                    </span>

                                </span>

                            </label>


                            <div class="rounded-xl border border-accent/20 bg-accent/5 px-3.5 py-3">

                                <label class="flex cursor-pointer items-start gap-3">

                                    <input
                                        id="privacy_acknowledged"
                                        type="checkbox"
                                        name="privacy_acknowledged"
                                        value="1"
                                        required
                                        @checked(old('privacy_acknowledged'))
                                        class="mt-0.5 h-4 w-4 rounded border-slate-300 text-accent focus:ring-accent">

                                    <span class="min-w-0">

                                        <span class="block text-xs font-semibold text-primary">
                                            Privacy notice acknowledgement
                                        </span>

                                        <span class="mt-1 block text-[10px] leading-4 text-slate-500">
                                            I confirm that the visitor was informed of and acknowledged the privacy notice covering the recording of identity, contact, and visit details for visitor management, security, and access-control purposes.
                                        </span>

                                    </span>

                                </label>

                                @error('privacy_acknowledged')
                                    <p class="mt-2 text-[10px] font-medium text-error">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>


                            <div class="rounded-xl border border-accent/15 bg-accent/5 px-3.5 py-3">

                                <div class="flex items-start gap-2.5">

                                    <svg
                                        class="mt-0.5 h-4 w-4 shrink-0 text-accent"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                                    </svg>


                                    <p class="text-[10px] leading-4 text-slate-500">
                                        Registering a visitor creates the reception record only. Check-in remains a separate staff action from the Visitor Activity queue.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =====================================================
                         FOOTER
                    ====================================================== --}}
                    <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-border bg-background/40 px-5 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-6">

                        <p class="text-[9px] text-slate-400">
                            <span class="text-error">*</span>
                            Required field
                        </p>


                        <div class="flex flex-col-reverse gap-2 sm:flex-row">

                            <button
                                type="button"
                                data-visitor-register-close
                                class="btn-outline justify-center">

                                Cancel

                            </button>


                            <button
                                type="submit"
                                class="btn-primary justify-center">

                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7" />

                                </svg>

                                Register Visitor

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endcan