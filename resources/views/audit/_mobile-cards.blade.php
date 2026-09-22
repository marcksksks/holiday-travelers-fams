<div class="grid gap-3 md:hidden">

    @forelse ($logs as $log)

        @php
            $actionKey =
                strtolower(
                    (string) $log->action
                );

            $actionClass =
                match (true) {
                    str_contains($actionKey, 'reject'),
                    str_contains($actionKey, 'decline'),
                    str_contains($actionKey, 'delete'),
                    str_contains($actionKey, 'objection')
                        => 'badge-error',

                    str_contains($actionKey, 'approve'),
                    str_contains($actionKey, 'create'),
                    str_contains($actionKey, 'upload'),
                    str_contains($actionKey, 'check_in'),
                    str_contains($actionKey, 'login')
                        => 'badge-success',

                    str_contains($actionKey, 'review'),
                    str_contains($actionKey, 'update'),
                    str_contains($actionKey, 'renew'),
                    str_contains($actionKey, 'sync')
                        => 'badge-info',

                    default =>
                        'bg-slate-100 text-slate-600',
                };
        @endphp


        <article
            data-audit-mobile-card
            class="card overflow-hidden">

            <div class="p-4">

                <div class="flex items-start justify-between gap-3">

                    <div>

                        <p class="text-xs font-semibold text-primary">
                            {{ $log->created_at?->format('M d, Y') ?? 'Unknown' }}
                        </p>

                        <p class="mt-0.5 text-[10px] text-slate-400">
                            {{ $log->created_at?->format('h:i:s A') ?? '' }}
                        </p>

                    </div>


                    <span class="badge {{ $actionClass }}">
                        {{ str($log->action ?: 'unknown')->headline() }}
                    </span>

                </div>


                <div class="mt-4 flex items-center gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-bold uppercase text-primary">

                        {{ strtoupper(substr($log->actor_email ?: 'S', 0, 1)) }}

                    </div>


                    <div class="min-w-0">

                        <p class="truncate text-sm font-semibold text-slate-700">
                            {{ $log->actor_email ?: 'System' }}
                        </p>

                        <p class="mt-0.5 text-[10px] text-slate-400">
                            {{ $log->actor_role ? str($log->actor_role)->headline() : 'System' }}
                        </p>

                    </div>

                </div>


                <div class="mt-4 grid grid-cols-2 gap-3">

                    <div class="rounded-xl bg-background px-3 py-2.5">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Module
                        </p>

                        <p class="mt-1 truncate text-xs font-medium text-slate-700">
                            {{ str($log->module ?: 'system')->headline() }}
                        </p>

                    </div>


                    <div class="rounded-xl bg-background px-3 py-2.5">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Record
                        </p>

                        <p class="mt-1 truncate text-xs font-medium text-slate-700">
                            {{ $log->record_label ?: 'No record label' }}
                        </p>

                        @if ($log->record_id)

                            <p class="mt-0.5 text-[9px] text-slate-400">
                                ID #{{ $log->record_id }}
                            </p>

                        @endif

                    </div>

                </div>


                <div class="mt-3 rounded-xl border border-border px-3 py-2.5">

                    <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                        Details
                    </p>


                    @if ($log->details)

                        <p class="mt-1 text-xs leading-5 text-slate-600">
                            {{ Str::limit($log->details, 180) }}
                        </p>

                    @else

                        <p class="mt-1 text-xs text-slate-400">
                            No additional details
                        </p>

                    @endif

                </div>

            </div>

        </article>


    @empty

        <div class="card">

            <x-empty-state
                title="No audit events found"
                description="No system activity matches the selected audit filters.">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4M12 22a10 10 0 100-20 10 10 0 000 20z" />

                    </svg>

                </x-slot:icon>

            </x-empty-state>

        </div>

    @endforelse

</div>