<div class="grid gap-3 md:hidden">

    @forelse ($documents as $document)

        <article
            data-document-mobile-card
            class="card overflow-visible">

            <div class="p-4">

                <div class="flex items-start justify-between gap-3">

                    <div class="flex min-w-0 items-start gap-3">

                        <div
                            @class([
                                'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl',
                                'bg-accent/10 text-primary' =>
                                    $document->is_system_generated,

                                'bg-primary/5 text-primary' =>
                                    ! $document->is_system_generated,
                            ])>

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 3h7l5 5v13H7V3zm7 0v5h5M10 13h6M10 17h6" />

                            </svg>

                        </div>


                        <div class="min-w-0">

                            <a
                                href="{{ route('documents.show', $document) }}"
                                class="block truncate font-button text-sm font-semibold text-primary transition hover:text-secondary">

                                {{ $document->title }}

                            </a>


                            <div class="mt-1 flex flex-wrap items-center gap-1.5">

                                <span class="text-[10px] font-medium text-slate-500">
                                    {{ str($document->category)->headline() }}
                                </span>


                                @if ($document->is_system_generated)

                                    <span class="rounded-full bg-accent/10 px-1.5 py-0.5 text-[8px] font-semibold uppercase text-primary">
                                        System
                                    </span>

                                @else

                                    <span class="rounded-full bg-primary/5 px-1.5 py-0.5 text-[8px] font-semibold uppercase text-primary">
                                        v{{ $document->version ?? 1 }}
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>


                    @switch($document->status)

                        @case('active')

                            <span class="badge badge-success">
                                Active
                            </span>

                            @break


                        @case('needs_review')

                            <span class="badge badge-warning">
                                Needs Review
                            </span>

                            @break


                        @case('archived')

                            <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                Archived
                            </span>

                            @break


                        @case('superseded')

                            <span class="badge badge-info">
                                Superseded
                            </span>

                            @break


                        @default

                            <span class="badge badge-info">
                                {{ str($document->status)->headline() }}
                            </span>

                    @endswitch

                </div>


                <div class="mt-4 grid grid-cols-2 gap-3">

                    <div class="rounded-xl bg-background px-3 py-2.5">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Confidentiality
                        </p>

                        <p class="mt-1 truncate text-xs font-medium text-slate-700">
                            {{ str($document->confidentiality ?? 'general')->headline() }}
                        </p>

                    </div>


                    <div class="rounded-xl bg-background px-3 py-2.5">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Updated
                        </p>

                        <p class="mt-1 text-xs font-medium text-slate-700">
                            {{ $document->updated_at?->format('M d, Y') }}
                        </p>

                        <p class="mt-0.5 text-[10px] text-slate-400">
                            {{ $document->updated_at?->format('h:i A') }}
                        </p>

                    </div>

                </div>


                @if ($document->file_name)

                    <div class="mt-3 rounded-xl border border-border px-3 py-2.5">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            File
                        </p>

                        <p
                            class="mt-1 truncate text-xs text-slate-600"
                            title="{{ $document->file_name }}">

                            {{ $document->file_name }}

                        </p>

                    </div>

                @endif


                @if (
                    $document->container
                    ||
                    $document->relatedModuleLabel()
                )

                    <div class="mt-3 flex flex-wrap gap-1.5">

                        @if ($document->container)

                            <a
                                href="{{ route('documents.index', ['container' => $document->container->id]) }}"
                                class="inline-flex max-w-full items-center gap-1 rounded-md bg-primary/5 px-2 py-1 text-[9px] font-medium text-primary">

                                <svg
                                    class="h-3 w-3 shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 7h6l2 2h10v10H3V7z" />

                                </svg>

                                <span class="truncate">
                                    {{ str_replace('/', ' / ', $document->container->path) }}
                                </span>

                            </a>

                        @endif


                        @if ($document->relatedModuleLabel())

                            <span class="inline-flex max-w-full rounded-md bg-accent/10 px-2 py-1 text-[9px] font-medium text-primary">

                                {{ $document->relatedModuleLabel() }}

                                @if ($document->relatedRecordLabel())

                                    &nbsp;·&nbsp;

                                    <span class="truncate">
                                        {{ $document->relatedRecordLabel() }}
                                    </span>

                                @endif

                            </span>

                        @endif

                    </div>

                @endif

            </div>


            <div class="flex items-center gap-2 border-t border-border bg-background/40 px-4 py-3">

                <a
                    href="{{ route('documents.show', $document) }}"
                    class="btn-outline flex-1 justify-center">

                    View Details

                </a>


                @include(
                    'documents._row-actions',
                    [
                        'document' => $document,
                    ]
                )

            </div>

        </article>


    @empty

        <div class="card">

            <x-empty-state
                title="No documents found"
                description="No documents match the current folder and filters.">

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
                            d="M7 3h7l5 5v13H7V3zm7 0v5h5M10 13h6M10 17h6" />

                    </svg>

                </x-slot:icon>

            </x-empty-state>

        </div>

    @endforelse

</div>