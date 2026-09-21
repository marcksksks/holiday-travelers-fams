@php
    $priorityClass = match ($record->priority) {
        'critical' => 'border-error/20 bg-error/10 text-error',
        'high' => 'border-warning/20 bg-warning/10 text-warning',
        'low' => 'border-success/20 bg-success/10 text-success',
        default => 'border-accent/20 bg-accent/10 text-accent',
    };

    $deadlineClass = match ($record->deadlineState()) {
        'expired',
        'overdue',
        'action_overdue' => 'border-error/20 bg-error/10 text-error',

        'due_today',
        'expires_today',
        'due_soon',
        'expiring_soon' => 'border-warning/20 bg-warning/10 text-warning',

        'closed' => 'border-slate-200 bg-slate-100 text-slate-500',

        default => 'border-success/20 bg-success/10 text-success',
    };
@endphp

<div
    id="legal-details-{{ $record->id }}"
    data-legal-modal
    class="fixed inset-0 z-[130] hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="legal-details-title-{{ $record->id }}">

    <button
        type="button"
        data-legal-modal-backdrop
        class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm"
        aria-label="Close legal matter details">
    </button>


    <div class="relative flex min-h-full items-center justify-center p-3 sm:p-6">

        <div class="flex max-h-[calc(100vh-2rem)] w-full max-w-5xl flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-2xl">

            <div class="flex items-start justify-between gap-4 border-b border-border bg-background/50 px-5 py-4 sm:px-6">

                <div class="min-w-0">

                    <div class="flex flex-wrap items-center gap-2">

                        <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-secondary">
                            Legal Matter #{{ $record->id }}
                        </p>

                        <span class="rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $priorityClass }}">
                            {{ str($record->priority)->headline() }} Priority
                        </span>

                        <span class="rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $deadlineClass }}">
                            {{ $record->deadlineLabel() }}
                        </span>

                    </div>

                    <h2
                        id="legal-details-title-{{ $record->id }}"
                        class="mt-1 truncate font-heading text-lg font-semibold text-primary">
                        {{ $record->title }}
                    </h2>

                    <p class="mt-1 text-xs text-slate-400">
                        {{ str($record->record_type)->headline() }}
                        @if ($record->reference_number)
                            · {{ $record->reference_number }}
                        @endif
                    </p>

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


            <div class="min-h-0 flex-1 overflow-y-auto p-5 sm:p-6">

                <div class="grid gap-5 xl:grid-cols-[minmax(0,1.4fr)_minmax(300px,0.8fr)]">

                    <div class="space-y-5">

                        {{-- Overview --}}
                        <section class="rounded-2xl border border-border p-5">

                            <div class="flex items-center justify-between gap-3">

                                <h3 class="font-heading text-sm font-semibold text-primary">
                                    Matter Overview
                                </h3>

                                <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                    {{ str($record->confidentiality_level)->headline() }}
                                </span>

                            </div>


                            <div class="mt-5 grid gap-4 sm:grid-cols-2">

                                @foreach ([
                                    'Legal Category' => $record->legal_category,
                                    'Issuing Authority' => $record->issuing_authority,
                                    'Jurisdiction' => $record->jurisdiction,
                                    'Responsible Officer' => $record->assignedOfficer?->full_name ?? $record->responsible_officer_email,
                                ] as $label => $value)

                                    <div>

                                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                            {{ $label }}
                                        </p>

                                        <p class="mt-1.5 text-sm font-medium text-primary">
                                            {{ $value ?: 'Not specified' }}
                                        </p>

                                    </div>

                                @endforeach

                            </div>

                        </section>


                        {{-- Dates --}}
                        <section class="rounded-2xl border border-border p-5">

                            <h3 class="font-heading text-sm font-semibold text-primary">
                                Important Dates
                            </h3>

                            <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                                @foreach ([
                                    'Issue Date' => $record->issue_date,
                                    'Expiration' => $record->expiration_date,
                                    'Due Date' => $record->due_date,
                                    'Next Action' => $record->next_action_date,
                                ] as $label => $date)

                                    <div>

                                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                            {{ $label }}
                                        </p>

                                        <p class="mt-1.5 text-sm font-semibold text-primary">
                                            {{ $date?->format('M d, Y') ?? 'Not set' }}
                                        </p>

                                    </div>

                                @endforeach

                            </div>

                        </section>


                        @if ($record->next_action)

                            <section class="rounded-2xl border border-warning/20 bg-warning/5 p-5">

                                <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-warning">
                                    Next Required Action
                                </p>

                                <p class="mt-2 text-sm font-semibold leading-6 text-primary">
                                    {{ $record->next_action }}
                                </p>

                                @if ($record->next_action_date)

                                    <p class="mt-2 text-xs text-slate-500">
                                        Target date:
                                        <strong class="font-semibold text-primary">
                                            {{ $record->next_action_date->format('M d, Y') }}
                                        </strong>
                                    </p>

                                @endif

                            </section>

                        @endif


                        @if ($record->description || $record->legal_basis || $record->legal_notes)

                            <section class="rounded-2xl border border-border p-5">

                                <h3 class="font-heading text-sm font-semibold text-primary">
                                    Legal Context
                                </h3>


                                @if ($record->description)

                                    <div class="mt-4">

                                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                            Description
                                        </p>

                                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">
                                            {{ $record->description }}
                                        </p>

                                    </div>

                                @endif


                                @if ($record->legal_basis)

                                    <div class="mt-4 border-t border-border pt-4">

                                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                            Legal Basis / Regulation
                                        </p>

                                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">
                                            {{ $record->legal_basis }}
                                        </p>

                                    </div>

                                @endif


                                @if ($record->legal_notes)

                                    <div class="mt-4 border-t border-border pt-4">

                                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                            Legal Notes
                                        </p>

                                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">
                                            {{ $record->legal_notes }}
                                        </p>

                                    </div>

                                @endif

                            </section>

                        @endif

                    </div>


                    <div class="space-y-5">

                        {{-- State --}}
                        <section class="rounded-2xl border border-border p-5">

                            <h3 class="font-heading text-sm font-semibold text-primary">
                                Current State
                            </h3>

                            <dl class="mt-4 space-y-3">

                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-xs text-slate-500">
                                        Matter Status
                                    </dt>
                                    <dd class="text-xs font-semibold text-primary">
                                        {{ str($record->status)->headline() }}
                                    </dd>
                                </div>

                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-xs text-slate-500">
                                        Review Status
                                    </dt>
                                    <dd class="text-xs font-semibold text-primary">
                                        {{ str($record->review_status)->headline() }}
                                    </dd>
                                </div>

                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-xs text-slate-500">
                                        Deadline State
                                    </dt>
                                    <dd class="text-xs font-semibold text-primary">
                                        {{ $record->deadlineLabel() }}
                                    </dd>
                                </div>

                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-xs text-slate-500">
                                        Linked Documents
                                    </dt>
                                    <dd class="text-xs font-semibold text-primary">
                                        {{ $record->archive_documents_count }}
                                    </dd>
                                </div>

                            </dl>

                        </section>


                        {{-- Attachment --}}
                        <section class="rounded-2xl border border-border p-5">

                            <h3 class="font-heading text-sm font-semibold text-primary">
                                Supporting File
                            </h3>

                            @if ($record->file_name)

                                <div class="mt-4 flex items-center gap-3 rounded-xl bg-background p-3">

                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-accent/10 text-accent">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 3h7l5 5v13H7V3zm7 0v6h6" />
                                        </svg>
                                    </div>

                                    <p class="min-w-0 truncate text-xs font-medium text-primary">
                                        {{ $record->file_name }}
                                    </p>

                                </div>

                            @else

                                <p class="mt-3 text-xs text-slate-400">
                                    No direct supporting file attached.
                                </p>

                            @endif

                        </section>


                        {{-- Activity --}}
                        <section class="rounded-2xl border border-border p-5">

                            <div class="flex items-center justify-between gap-3">

                                <h3 class="font-heading text-sm font-semibold text-primary">
                                    Activity Timeline
                                </h3>

                                <span class="text-[10px] text-slate-400">
                                    {{ $recordActivities->count() }} events
                                </span>

                            </div>


                            <div class="mt-4 space-y-4">

                                @forelse ($recordActivities->take(8) as $activity)

                                    <div class="relative pl-5">

                                        <span class="absolute left-0 top-1.5 h-2 w-2 rounded-full bg-secondary"></span>

                                        <p class="text-xs font-semibold text-primary">
                                            {{ str($activity->action)->headline() }}
                                        </p>

                                        @if ($activity->details)
                                            <p class="mt-0.5 text-xs leading-5 text-slate-500">
                                                {{ $activity->details }}
                                            </p>
                                        @endif

                                        <p class="mt-1 text-[10px] text-slate-400">
                                            {{ $activity->actor_email }}
                                            ·
                                            {{ $activity->created_at?->format('M d, Y h:i A') }}
                                        </p>

                                    </div>

                                @empty

                                    <p class="text-xs text-slate-400">
                                        No legal activity has been recorded yet.
                                    </p>

                                @endforelse

                            </div>

                        </section>

                    </div>

                </div>

            </div>


            <div class="flex flex-wrap justify-end gap-2 border-t border-border bg-background/40 px-5 py-4 sm:px-6">

                <button
                    type="button"
                    data-legal-modal-close
                    class="btn-outline">
                    Close
                </button>

                @can('manageLegal')

                    <a
                        href="{{ route('legal.edit', $record) }}"
                        class="btn-primary">
                        Edit Record
                    </a>

                @endcan

            </div>

        </div>

    </div>

</div>