<div
    class="relative"
    data-document-action-root>

    <button
        type="button"
        data-document-action-trigger
        aria-haspopup="menu"
        aria-expanded="false"
        aria-label="Document actions"
        class="flex h-8 w-8 items-center justify-center rounded-lg border border-border bg-card text-slate-400 transition hover:border-accent/40 hover:bg-accent/5 hover:text-primary">

        <svg
            class="h-4 w-4"
            fill="currentColor"
            viewBox="0 0 24 24">

            <circle cx="5" cy="12" r="1.6" />
            <circle cx="12" cy="12" r="1.6" />
            <circle cx="19" cy="12" r="1.6" />

        </svg>

    </button>


    <div
        data-document-action-menu
        role="menu"
        class="fixed z-[95] hidden w-56 overflow-hidden rounded-xl border border-border bg-card p-1 shadow-2xl">

        {{-- Primary --}}
        <a
            href="{{ route('documents.show', $document) }}"
            role="menuitem"
            class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-xs font-semibold text-primary transition hover:bg-primary/5">

            <svg
                class="h-4 w-4 shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c-1.5 4-4.5 7-9 7s-7.5-3-9-7c1.5-4 4.5-7 9-7s7.5 3 9 7z" />

            </svg>

            View Details

        </a>


        @if ($document->file_uri)

            <button
                type="button"
                role="menuitem"
                data-document-download="{{ route('documents.request-link', $document) }}"
                class="flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-xs text-slate-600 transition hover:bg-background hover:text-primary">

                <svg
                    class="h-4 w-4 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 3v12m0 0l-4-4m4 4l4-4M5 19h14" />

                </svg>

                Download File

            </button>

        @endif


        @can('manageDocuments')

            @if (! $document->is_system_generated)

                <div class="my-1 border-t border-border"></div>

                <p class="px-2.5 py-1 text-[8px] font-semibold uppercase tracking-[0.15em] text-slate-400">
                    Manage
                </p>


                <a
                    href="{{ route('documents.edit', $document) }}#edit-metadata"
                    role="menuitem"
                    class="block rounded-lg px-2.5 py-1.5 text-xs text-slate-600 transition hover:bg-background hover:text-primary">

                    Edit Metadata

                </a>


                <a
                    href="{{ route('documents.edit', $document) }}#move-document"
                    role="menuitem"
                    class="block rounded-lg px-2.5 py-1.5 text-xs text-slate-600 transition hover:bg-background hover:text-primary">

                    Move to Folder

                </a>


                <a
                    href="{{ route('documents.edit', $document) }}#upload-version"
                    role="menuitem"
                    class="block rounded-lg px-2.5 py-1.5 text-xs text-slate-600 transition hover:bg-background hover:text-primary">

                    Upload New Version

                </a>

            @endif

        @endcan


        @if (
            in_array(
                $document->source_module,
                [
                    'reservations',
                    'visitors',
                    'contracts',
                    'legal',
                ],
                true
            )
        )

            <div class="my-1 border-t border-border"></div>

            <p class="px-2.5 py-1 text-[8px] font-semibold uppercase tracking-[0.15em] text-slate-400">
                Related
            </p>


            @if ($document->source_module === 'reservations')

                <a
                    href="{{ route('reservations.index') }}"
                    role="menuitem"
                    class="block rounded-lg px-2.5 py-1.5 text-xs text-slate-600 transition hover:bg-background hover:text-primary">

                    Facilities Reservation

                </a>

            @elseif ($document->source_module === 'visitors')

                <a
                    href="{{ route('visitors.index') }}"
                    role="menuitem"
                    class="block rounded-lg px-2.5 py-1.5 text-xs text-slate-600 transition hover:bg-background hover:text-primary">

                    Visitor Management

                </a>

            @elseif ($document->source_module === 'contracts')

                <a
                    href="{{ route('contracts.index') }}"
                    role="menuitem"
                    class="block rounded-lg px-2.5 py-1.5 text-xs text-slate-600 transition hover:bg-background hover:text-primary">

                    Contract Management

                </a>

            @elseif ($document->source_module === 'legal')

                <a
                    href="{{ route('legal.index') }}"
                    role="menuitem"
                    class="block rounded-lg px-2.5 py-1.5 text-xs text-slate-600 transition hover:bg-background hover:text-primary">

                    Legal Management

                </a>

            @endif

        @endif


        @can('manageDocuments')

            <div class="my-1 border-t border-border"></div>

            @if ($document->status === 'archived')

                <form
                    method="POST"
                    action="{{ route('documents.restore', $document) }}">

                    @csrf

                    <button
                        type="submit"
                        role="menuitem"
                        class="flex w-full items-center rounded-lg px-2.5 py-2 text-left text-xs font-semibold text-success transition hover:bg-success/5">

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
                        role="menuitem"
                        class="flex w-full items-center rounded-lg px-2.5 py-2 text-left text-xs font-semibold text-error transition hover:bg-error/5">

                        Archive Document

                    </button>

                </form>

            @endif

        @endcan

    </div>

</div>
