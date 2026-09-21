@php
    $treePrefix =
        $treePrefix
        ?? 'documents-library';

    $allDocumentsActive =
        ! request()->filled('container');
@endphp


<div class="p-3">

    {{-- All documents --}}
    <a
        href="{{ route('documents.index') }}"
        @if ($allDocumentsActive)
            aria-current="page"
        @endif
        @class([
            'group flex items-center gap-2.5 rounded-xl px-3 py-2.5 transition',
            'bg-primary text-white shadow-sm' => $allDocumentsActive,
            'text-slate-600 hover:bg-background hover:text-primary' => ! $allDocumentsActive,
        ])>

        <span
            @class([
                'flex h-8 w-8 shrink-0 items-center justify-center rounded-lg',
                'bg-white/10 text-white' => $allDocumentsActive,
                'bg-primary/5 text-primary' => ! $allDocumentsActive,
            ])>

            <svg
                class="h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 5h16v14H4V5zm4 4h8M8 13h8" />

            </svg>

        </span>


        <div class="min-w-0 flex-1">

            <p class="truncate text-xs font-semibold">
                All Documents
            </p>

            <p
                @class([
                    'mt-0.5 text-[9px]',
                    'text-white/55' => $allDocumentsActive,
                    'text-slate-400' => ! $allDocumentsActive,
                ])>

                Complete accessible library

            </p>

        </div>


        <span
            @class([
                'rounded-full px-2 py-0.5 text-[9px] font-semibold',
                'bg-white/10 text-white' => $allDocumentsActive,
                'bg-primary/5 text-primary' => ! $allDocumentsActive,
            ])>

            {{ number_format($counts['total']) }}

        </span>

    </a>


    {{-- Folder section --}}
    <div class="mt-5">

        <div class="mb-2 flex items-center justify-between px-2">

            <p class="text-[9px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                Folders
            </p>

            @if ($selectedContainer)

                <a
                    href="{{ route('documents.index') }}"
                    class="text-[9px] font-semibold text-slate-400 transition hover:text-primary">

                    Reset

                </a>

            @endif

        </div>


        @if ($rootContainers->isEmpty())

            <div class="rounded-xl border border-dashed border-border px-3 py-5 text-center">

                <svg
                    class="mx-auto h-5 w-5 text-slate-300"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 7h6l2 2h10v10H3V7z" />

                </svg>

                <p class="mt-2 text-[10px] text-slate-400">
                    No folders available.
                </p>

            </div>

        @else

            <div class="space-y-0.5">

                @foreach ($rootContainers as $container)

                    @include(
                        'documents._container-node',
                        [
                            'container' =>
                                $container,

                            'level' =>
                                0,

                            'selectedContainer' =>
                                $selectedContainer,

                            'treePrefix' =>
                                $treePrefix,
                        ]
                    )

                @endforeach

            </div>

        @endif

    </div>

</div>