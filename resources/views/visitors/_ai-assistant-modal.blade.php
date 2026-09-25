@can('useAiAssist')

    @php
        $aiProviderConfigured =
            in_array(
                config('services.ai_assist.provider', 'none'),
                ['openai', 'gemini'],
                true
            )
            && filled(config('services.ai_assist.api_key'))
            && filled(config('services.ai_assist.model'))
            && filled(config('services.ai_assist.endpoint'));
    @endphp


    <div
        id="ai-visitor-assistant"
        data-ai-assistant-modal
        data-ai-visitor-assistant
        data-ai-endpoint="{{ route('visitors.ai-assist') }}"
        data-ai-csrf="{{ csrf_token() }}"
        class="fixed inset-0 z-[95] hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="ai-visitor-assistant-title">

        {{-- Backdrop --}}
        <div
            data-ai-assistant-backdrop
            class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
        </div>


        <div class="relative flex min-h-full items-center justify-center p-2 sm:p-5">

            <div
                data-ai-assistant-panel
                class="flex max-h-[calc(100vh-1rem)] w-full max-w-5xl flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-2xl sm:max-h-[calc(100vh-2.5rem)]">

                {{-- =====================================================
                     HEADER
                ====================================================== --}}
                <div class="flex shrink-0 items-start justify-between gap-4 border-b border-border bg-gradient-to-r from-accent/10 via-card to-secondary/5 px-5 py-4 sm:px-6">

                    <div class="flex min-w-0 items-start gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary text-white shadow-sm">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 3v4M3 5h4M19 17v4M17 19h4M12 3l1.09 3.26L16 7.35l-2.91 1.09L12 12l-1.09-3.56L8 7.35l2.91-1.09L12 3z" />

                            </svg>

                        </div>


                        <div class="min-w-0">

                            <div class="flex flex-wrap items-center gap-2">

                                <h2
                                    id="ai-visitor-assistant-title"
                                    class="font-heading text-lg font-semibold text-primary">

                                    Visitor Intelligence Assistant

                                </h2>


                                <span class="rounded-full bg-primary/10 px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wide text-primary">
                                    Human Reviewed
                                </span>

                            </div>


                            <p class="mt-1 max-w-xl text-xs leading-5 text-slate-500">
                                Classify, summarize, route, and verify visitor information against today's appointments before registration.
                            </p>

                        </div>

                    </div>


                    <div class="flex shrink-0 items-center gap-2">

                        @if ($aiProviderConfigured)

                            <span class="hidden items-center gap-1.5 rounded-full bg-success/10 px-2.5 py-1 text-[9px] font-semibold text-success sm:inline-flex">

                                <span class="h-1.5 w-1.5 rounded-full bg-success"></span>

                                AI Connected

                            </span>

                        @else

                            <span class="hidden items-center gap-1.5 rounded-full bg-warning/10 px-2.5 py-1 text-[9px] font-semibold text-amber-700 sm:inline-flex">

                                <span class="h-1.5 w-1.5 rounded-full bg-warning"></span>

                                Safe Fallback

                            </span>

                        @endif


                        <button
                            type="button"
                            data-ai-assistant-close
                            aria-label="Close Visitor Intelligence Assistant"
                            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary">

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

                </div>


                {{-- =====================================================
                     BODY
                ====================================================== --}}
                <div class="min-h-0 flex-1 overflow-y-auto">

                    <div class="grid min-h-full lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">

                        {{-- =================================================
                             INPUT
                        ================================================== --}}
                        <section class="border-b border-border p-5 sm:p-6 lg:border-b-0 lg:border-r">

                            <div>

                                <p class="text-[9px] font-semibold uppercase tracking-[0.16em] text-secondary">
                                    Step 1
                                </p>

                                <h3 class="mt-1 text-sm font-semibold text-primary">
                                    Visitor Information
                                </h3>

                                <p class="mt-1 text-[10px] leading-4 text-slate-400">
                                    Paste or type the information provided by the arriving visitor.
                                </p>

                            </div>


                            <textarea
                                id="ai_visitor_text"
                                data-ai-input
                                rows="8"
                                maxlength="2000"
                                class="input mt-4 resize-y"
                                placeholder="Example: Juan Dela Cruz from ABC Travel is here to meet Maria Santos regarding a supplier agreement."></textarea>


                            <div class="mt-2 flex items-center justify-between gap-3">

                                <p class="text-[9px] text-slate-400">
                                    Suggestions must be reviewed by reception staff.
                                </p>

                                <span class="text-[9px] text-slate-400">
                                    Max 2,000 characters
                                </span>

                            </div>


                            {{-- Actions --}}
                            <div class="mt-5">

                                <p class="mb-2 text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                    Assistance Type
                                </p>


                                <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-4">

                                    <button
                                        type="button"
                                        data-ai-mode="triage"
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
                                                d="M9 12l2 2 4-4M12 3a9 9 0 100 18 9 9 0 000-18z" />

                                        </svg>

                                        Analyze Visit

                                    </button>

                                    <button
                                        type="button"
                                        data-ai-mode="extract"
                                        class="btn-secondary justify-center">

                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12h6M9 16h6M9 8h3M5 3h10l4 4v14H5V3z" />

                                        </svg>

                                        Extract

                                    </button>


                                    <button
                                        type="button"
                                        data-ai-mode="summary"
                                        class="btn-outline justify-center">

                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 6h16M4 12h16M4 18h10" />

                                        </svg>

                                        Summarize

                                    </button>


                                    <button
                                        type="button"
                                        data-ai-mode="appointment_check"
                                        class="btn-outline justify-center">

                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 7V3M16 7V3M4 11h16M9 16l2 2 4-4M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

                                        </svg>

                                        Appointment

                                    </button>

                                </div>

                            </div>


                            {{-- Safety --}}
                            <div class="mt-5 rounded-xl border border-accent/20 bg-accent/5 p-3.5">

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


                                    <div>

                                        <p class="text-[10px] font-semibold text-primary">
                                            Human decision required
                                        </p>

                                        <p class="mt-0.5 text-[10px] leading-4 text-slate-500">
                                            The assistant cannot approve, decline, check in, check out, or grant visitor access.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </section>


                        {{-- =================================================
                             RESULT
                        ================================================== --}}
                        <section class="relative p-5 sm:p-6">

                            <div class="flex items-start justify-between gap-3">

                                <div>

                                    <p class="text-[9px] font-semibold uppercase tracking-[0.16em] text-secondary">
                                        Step 2
                                    </p>

                                    <h3 class="mt-1 text-sm font-semibold text-primary">
                                        Assistant Suggestion
                                    </h3>

                                    <p class="mt-1 text-[10px] leading-4 text-slate-400">
                                        Verify all suggested information before using it.
                                    </p>

                                </div>


                                <span
                                    data-ai-status
                                    class="hidden rounded-full bg-accent/10 px-2.5 py-1 text-[9px] font-semibold uppercase tracking-wide text-primary">
                                </span>

                            </div>


                            {{-- Empty --}}
                            <div
                                data-ai-empty
                                class="flex min-h-[300px] flex-col items-center justify-center px-4 text-center">

                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent/10 text-accent">

                                    <svg
                                        class="h-6 w-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 6V4m0 16v-2m8-6h-2M6 12H4m13.657-5.657l-1.414 1.414M7.757 16.243l-1.414 1.414m11.314 0l-1.414-1.414M7.757 7.757L6.343 6.343M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                    </svg>

                                </div>


                                <p class="mt-3 text-sm font-semibold text-primary">
                                    Ready to assist
                                </p>

                                <p class="mt-1 max-w-sm text-xs leading-5 text-slate-500">
                                    Enter visitor information and choose Analyze Visit for the complete AI-assisted workflow.
                                </p>

                            </div>


                            {{-- Loading --}}
                            <div
                                data-ai-loading
                                class="hidden min-h-[300px] flex-col items-center justify-center text-center">

                                <svg
                                    class="h-7 w-7 animate-spin text-secondary"
                                    fill="none"
                                    viewBox="0 0 24 24">

                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4">
                                    </circle>

                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                    </path>

                                </svg>


                                <p class="mt-3 text-sm font-semibold text-primary">
                                    Analyzing visitor information...
                                </p>

                                <p class="mt-1 text-[10px] text-slate-400">
                                    Please keep this window open.
                                </p>

                            </div>


                            {{-- Existing app.js renders content here --}}
                            <div
                                data-ai-result
                                class="hidden mt-5 space-y-4">
                            </div>


                            @can('operateVisitorDesk')

                                <div
                                    data-ai-apply-wrap
                                    class="mt-5 hidden border-t border-border pt-4">

                                    <button
                                        type="button"
                                        data-ai-apply
                                        class="btn-primary w-full justify-center">

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

                                        Apply to Registration

                                    </button>


                                    <p class="mt-2 text-center text-[9px] leading-4 text-slate-400">
                                        Suggested values fill the registration form only. Registration is never submitted automatically.
                                    </p>

                                </div>

                            @endcan

                        </section>

                    </div>

                </div>


                {{-- =====================================================
                     FOOTER
                ====================================================== --}}
                <div class="flex shrink-0 items-center justify-between gap-3 border-t border-border bg-background/30 px-5 py-3 sm:px-6">

                    <p class="text-[9px] text-slate-400">
                        AI output may be incomplete or incorrect.
                    </p>


                    <button
                        type="button"
                        data-ai-assistant-close
                        class="btn-outline">

                        Close

                    </button>

                </div>

            </div>

        </div>

    </div>

@endcan