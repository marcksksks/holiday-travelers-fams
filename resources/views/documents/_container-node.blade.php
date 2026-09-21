@php
    $level =
        $level ?? 0;

    $treePrefix =
        $treePrefix
        ?? 'documents-tree';

    $path =
        strtolower(
            trim(
                $container->path,
                '/'
            )
        );

    $requiredPermission =
        match (true) {

            $path === 'visitors',
            str_starts_with(
                $path,
                'visitors/'
            ) => 'viewVisitors',

            $path === 'contracts',
            str_starts_with(
                $path,
                'contracts/'
            ) => 'viewContracts',

            $path === 'legal',
            str_starts_with(
                $path,
                'legal/'
            ) => 'viewLegal',

            default => null,

        };

    $canSeeContainer =
        ! $requiredPermission
        ||
        auth()->user()->can(
            $requiredPermission
        );

    $active =
        (string) request('container')
        ===
        (string) $container->id;

    $hasChildren =
        $container
            ->children
            ->isNotEmpty();

    $selectedPath =
        $selectedContainer?->path;

    $branchOpen =
        $active
        ||
        (
            $selectedPath
            &&
            (
                $selectedPath ===
                    $container->path
                ||
                str_starts_with(
                    $selectedPath,
                    $container->path.'/'
                )
            )
        );

    $childrenId =
        $treePrefix
        .'-children-'
        .$container->id;
@endphp


@if ($canSeeContainer)

    <div class="document-tree-node">

        <div
            @class([
                'group flex items-center rounded-lg transition',
                'bg-primary/10 ring-1 ring-inset ring-primary/10' => $active,
                'hover:bg-background' => ! $active,
            ])
            style="margin-left: {{ $level * 10 }}px;">

            {{-- Expand / collapse --}}
            @if ($hasChildren)

                <button
                    type="button"
                    data-container-toggle="{{ $childrenId }}"
                    aria-expanded="{{ $branchOpen ? 'true' : 'false' }}"
                    class="document-tree-toggle ml-1 flex h-8 w-7 shrink-0 items-center justify-center rounded-md text-slate-400 transition hover:bg-card hover:text-primary">

                    <svg
                        class="document-tree-chevron h-3.5 w-3.5 transition-transform duration-200 {{ $branchOpen ? 'rotate-90' : '' }}"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2.5"
                            d="M9 5l7 7-7 7" />

                    </svg>

                </button>

            @else

                <span class="ml-1 block w-7 shrink-0"></span>

            @endif


            {{-- Folder --}}
            <a
                href="{{ route('documents.index', ['container' => $container->id]) }}"
                @if ($active)
                    aria-current="page"
                @endif
                @class([
                    'flex min-w-0 flex-1 items-center gap-2 py-2 pr-2 text-xs transition',
                    'font-semibold text-primary' => $active,
                    'font-medium text-slate-600 hover:text-primary' => ! $active,
                ])>

                <span
                    @class([
                        'flex h-7 w-7 shrink-0 items-center justify-center rounded-lg transition',
                        'bg-secondary/10 text-secondary' => $active,
                        'bg-background text-slate-400 group-hover:bg-primary/5 group-hover:text-primary' => ! $active,
                    ])>

                    <svg
                        class="h-3.5 w-3.5"
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


                <span
                    class="min-w-0 flex-1 truncate"
                    title="{{ $container->name }}">

                    {{ $container->name }}

                </span>


                @if ($active)

                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-secondary"></span>

                @endif

            </a>

        </div>


        @if ($hasChildren)

            <div
                id="{{ $childrenId }}"
                class="document-tree-children {{ $branchOpen ? '' : 'hidden' }}">

                @foreach ($container->children as $child)

                    @include(
                        'documents._container-node',
                        [
                            'container' =>
                                $child,

                            'level' =>
                                $level + 1,

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

@endif