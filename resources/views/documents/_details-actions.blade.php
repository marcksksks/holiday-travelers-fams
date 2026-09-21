@can('manageDocuments')

    <details class="group relative">

        <summary
            class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-lg border border-border bg-card text-slate-400 transition hover:border-accent/40 hover:bg-accent/5 hover:text-primary"
            aria-label="Document actions">

            <svg
                class="h-5 w-5"
                fill="currentColor"
                viewBox="0 0 24 24">

                <circle cx="5" cy="12" r="1.6" />
                <circle cx="12" cy="12" r="1.6" />
                <circle cx="19" cy="12" r="1.6" />

            </svg>

        </summary>


        <div class="absolute right-0 z-40 mt-2 w-60 overflow-hidden rounded-xl border border-border bg-card p-1.5 shadow-2xl">

            @if (! $document->is_system_generated)

                <p class="px-3 pb-1 pt-1.5 text-[9px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                    Manage
                </p>


                <a
                    href="{{ route('documents.edit', $document) }}#edit-metadata"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-background hover:text-primary">

                    <svg
                        class="h-4 w-4 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-8.5a2.12 2.12 0 013 3L12 14l-4 1 1-4 6.5-6.5z" />

                    </svg>

                    Edit Metadata

                </a>


                <a
                    href="{{ route('documents.edit', $document) }}#move-document"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600 transition hover:bg-background hover:text-primary">

                    <svg
                        class="h-4 w-4 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 7h6l2 2h10v10H3V7z" />

                    </svg>

                    Move to Folder

                </a>


                <a
                    href="{{ route('documents.edit', $document) }}#upload-version"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600 transition hover:bg-background hover:text-primary">

                    <svg
                        class="h-4 w-4 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 16V4m0 0L7 9m5-5l5 5M5 20h14" />

                    </svg>

                    Upload New Version

                </a>

            @elseif ($document->source_module)

                <div class="rounded-lg bg-background px-3 py-2.5">

                    <p class="text-[10px] font-semibold text-primary">
                        System-managed record
                    </p>

                    <p class="mt-0.5 text-[9px] leading-4 text-slate-400">
                        Document content is managed from its source module.
                    </p>

                </div>

            @endif


            <div class="my-1 border-t border-border"></div>


            @if ($document->status === 'archived')

                <form
                    method="POST"
                    action="{{ route('documents.restore', $document) }}">

                    @csrf

                    <button
                        type="submit"
                        class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-semibold text-success transition hover:bg-success/5">

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 4v6h6M20 20v-6h-6M5.1 15a8 8 0 0013.8 1M18.9 9A8 8 0 005.1 8" />

                        </svg>

                        Restore Document

                    </button>

                </form>

            @else

                <form
                    method="POST"
                    action="{{ route('documents.archive', $document) }}"
                    onsubmit="return confirm('Archive this document? It will remain stored and can be restored later.');">

                    @csrf

                    <button
                        type="submit"
                        class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-semibold text-error transition hover:bg-error/5">

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 8h14M9 12h6m-8 8h10a2 2 0 002-2V8H5v10a2 2 0 002 2zM8 4h8l1 4H7l1-4z" />

                        </svg>

                        Archive Document

                    </button>

                </form>

            @endif

        </div>

    </details>

@endcan