<section>

    {{-- Operations heading --}}
    <div class="mb-3 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">

        <div>

            <h2 class="font-heading text-sm font-semibold text-primary">
                Document Operations
            </h2>

            <p class="mt-0.5 text-[10px] text-slate-400">
                Manage filing location or create a new document version.
            </p>

        </div>


        <p class="text-[9px] text-slate-400">
            These actions do not modify document metadata.
        </p>

    </div>


    <div class="grid gap-4 xl:grid-cols-2">

        {{-- =====================================================
             MOVE DOCUMENT
        ====================================================== --}}
        <section
            id="move-document"
            class="card overflow-hidden scroll-mt-24">

            <div class="flex items-start gap-3 border-b border-border px-5 py-4">

                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary/5 text-primary">

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 7h6l2 2h10v10H3V7z" />

                    </svg>

                </span>


                <div>

                    <h3 class="font-heading text-sm font-semibold text-primary">
                        Move Document
                    </h3>

                    <p class="mt-0.5 text-[10px] leading-4 text-slate-400">
                        Change where this document appears in the library.
                    </p>

                </div>

            </div>


            <form
                method="POST"
                action="{{ route('documents.move', $document) }}"
                class="flex h-full flex-col">

                @csrf


                <div class="flex-1 space-y-4 p-5">

                    {{-- Current location --}}
                    <div class="rounded-xl bg-background/60 p-3">

                        <p class="text-[9px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                            Current Folder
                        </p>


                        <div class="mt-2 flex items-start gap-2">

                            <svg
                                class="mt-0.5 h-4 w-4 shrink-0 text-primary"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 7h6l2 2h10v10H3V7z" />

                            </svg>


                            <p class="min-w-0 break-words text-xs font-semibold text-primary">

                                @if ($document->container)

                                    {{
                                        str_replace(
                                            '/',
                                            ' / ',
                                            $document->container->path
                                        )
                                    }}

                                @else

                                    Unfiled

                                @endif

                            </p>

                        </div>

                    </div>


                    {{-- Destination --}}
                    <div>

                        <label class="label">
                            Destination Folder
                        </label>

                        <select
                            name="container_id"
                            class="input">

                            <option
                                value=""
                                @selected(
                                    old(
                                        'container_id',
                                        $document->container_id
                                    ) === null
                                )>

                                Unfiled

                            </option>


                            @foreach ($allContainers as $container)

                                <option
                                    value="{{ $container->id }}"
                                    @selected(
                                        (string) old(
                                            'container_id',
                                            $document->container_id
                                        )
                                        ===
                                        (string) $container->id
                                    )>

                                    {{
                                        str_replace(
                                            '/',
                                            ' / ',
                                            $container->path
                                        )
                                    }}

                                </option>

                            @endforeach

                        </select>


                        @error('container_id')

                            <p class="mt-1 text-[10px] text-error">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <p class="text-[9px] leading-4 text-slate-400">
                        Moving the document changes only its filing location. Its file, metadata, and version remain unchanged.
                    </p>

                </div>


                <div class="border-t border-border bg-background/30 px-5 py-3">

                    <button
                        type="submit"
                        class="btn-outline flex w-full justify-center">

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 12h14m-4-4l4 4-4 4" />

                        </svg>

                        Move Document

                    </button>

                </div>

            </form>

        </section>


        {{-- =====================================================
             UPLOAD NEW VERSION
        ====================================================== --}}
        <section
            id="upload-version"
            class="card overflow-hidden scroll-mt-24">

            <div class="flex items-start justify-between gap-3 border-b border-border px-5 py-4">

                <div class="flex items-start gap-3">

                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-accent/10 text-primary">

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 16V4m0 0L7 9m5-5l5 5M5 20h14" />

                        </svg>

                    </span>


                    <div>

                        <h3 class="font-heading text-sm font-semibold text-primary">
                            Upload New Version
                        </h3>

                        <p class="mt-0.5 text-[10px] leading-4 text-slate-400">
                            Replace the stored file while preserving document history.
                        </p>

                    </div>

                </div>


                <span class="shrink-0 rounded-full bg-primary/5 px-2.5 py-1 text-[9px] font-semibold text-primary">

                    v{{ $document->version ?? 1 }}
                    &rarr;
                    v{{ ($document->version ?? 1) + 1 }}

                </span>

            </div>


            <form
                method="POST"
                action="{{ route('documents.version', $document) }}"
                enctype="multipart/form-data"
                class="flex h-full flex-col">

                @csrf


                <div class="flex-1 space-y-4 p-5">

                    {{-- Current file --}}
                    <div class="rounded-xl bg-background/60 p-3">

                        <p class="text-[9px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                            Current File
                        </p>


                        <div class="mt-2 flex items-start gap-2">

                            <svg
                                class="mt-0.5 h-4 w-4 shrink-0 text-primary"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 3h7l5 5v13H7V3zm7 0v5h5" />

                            </svg>


                            <p class="min-w-0 break-all text-xs font-semibold text-primary">
                                {{ $document->file_name ?: 'No current file name' }}
                            </p>

                        </div>

                    </div>


                    {{-- File picker --}}
                    <div>

                        <label class="label">
                            Replacement File
                            <span class="text-error">*</span>
                        </label>


                        <label class="group relative mt-1 block cursor-pointer">

                            <input
                                type="file"
                                name="file"
                                accept="{{ \App\Support\DocumentUploadPolicy::acceptAttribute() }}"
                                required
                                data-version-file-input
                                class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0">


                            <div class="rounded-xl border border-dashed border-accent/40 bg-accent/5 px-4 py-5 text-center transition group-hover:border-accent/70 group-hover:bg-accent/10">

                                <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-xl bg-card text-primary shadow-sm ring-1 ring-border">

                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 16V4m0 0L7 9m5-5l5 5M5 20h14" />

                                    </svg>

                                </div>


                                <p class="mt-2 text-xs font-semibold text-primary">
                                    Choose replacement file
                                </p>


                                <p
                                    data-version-file-name
                                    class="mt-1 truncate text-[10px] text-slate-400">

                                    No file selected

                                </p>


                                <p class="mt-1 text-[9px] text-slate-400">
                                    Maximum file size: 20 MB
                                </p>

                            </div>

                        </label>


                        @error('file')

                            <p class="mt-1 text-[10px] text-error">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Version note --}}
                    <div>

                        <label class="label">
                            Version Note
                        </label>

                        <textarea
                            name="version_note"
                            rows="2"
                            maxlength="1000"
                            placeholder="Briefly describe what changed in this version..."
                            class="input resize-y">{{ old('version_note') }}</textarea>


                        <div class="mt-1 flex items-center justify-between gap-3">

                            <p class="text-[9px] text-slate-400">
                                Optional. This note will appear in document history.
                            </p>

                            <span class="text-[9px] text-slate-400">
                                Max 1,000 characters
                            </span>

                        </div>


                        @error('version_note')

                            <p class="mt-1 text-[10px] text-error">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                </div>


                <div class="border-t border-border bg-background/30 px-5 py-3">

                    <button
                        type="submit"
                        class="btn-primary flex w-full justify-center">

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 16V4m0 0L7 9m5-5l5 5M5 20h14" />

                        </svg>

                        Upload Version
                        {{ ($document->version ?? 1) + 1 }}

                    </button>

                </div>

            </form>

        </section>

    </div>

</section>


<script>
document.addEventListener(
    'DOMContentLoaded',
    () => {

        const fileInput =
            document.querySelector(
                '[data-version-file-input]'
            );

        const fileName =
            document.querySelector(
                '[data-version-file-name]'
            );

        if (
            ! fileInput
            ||
            ! fileName
        ) {
            return;
        }

        fileInput.addEventListener(
            'change',
            () => {

                fileName.textContent =
                    fileInput.files?.[0]?.name
                    ??
                    'No file selected';

            }
        );

    }
);
</script>