@php
    $level = $level ?? 0;

    $path = strtolower(
        trim($container->path, '/')
    );

    $requiredPermission = match (true) {
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
        ! $requiredPermission ||
        auth()->user()->can(
            $requiredPermission
        );

    $active =
        (string) request('container') ===
        (string) $container->id;

    $hasChildren =
        $container->children->isNotEmpty();

    $selectedPath =
        $selectedContainer?->path ?? null;

    $branchOpen =
        $active ||
        (
            $selectedPath &&
            (
                $selectedPath ===
                    $container->path ||

                str_starts_with(
                    $selectedPath,
                    $container->path.'/'
                )
            )
        );
@endphp


@if ($canSeeContainer)

    <div class="document-tree-node">

        <div
            @class([
                'flex items-center gap-1 rounded-lg transition',
                'bg-primary/10' => $active,
                'hover:bg-background' => ! $active,
            ])
            style="padding-left: {{ 6 + ($level * 14) }}px;">

            @if ($hasChildren)

                <button
                    type="button"
                    class="document-tree-toggle flex h-8 w-7 shrink-0 items-center justify-center rounded-md text-slate-400 transition hover:text-primary"
                    data-container-toggle="container-children-{{ $container->id }}"
                    aria-expanded="{{ $branchOpen ? 'true' : 'false' }}">

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

                <span class="block w-7 shrink-0"></span>

            @endif


            <a
                href="{{ route('documents.index', ['container' => $container->id]) }}"
                @class([
                    'flex min-w-0 flex-1 items-center gap-2 py-2 pr-2 text-sm transition',
                    'font-semibold text-primary' => $active,
                    'text-slate-600 hover:text-primary' => ! $active,
                ])>

                <svg
                    class="h-4 w-4 shrink-0 {{ $active ? 'text-secondary' : 'text-slate-400' }}"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 7h6l2 2h10v10H3V7z" />

                </svg>

                <span
                    class="min-w-0 flex-1 truncate"
                    title="{{ $container->name }}">

                    {{ $container->name }}

                </span>

            </a>

        </div>


        @if ($hasChildren)

            <div
                id="container-children-{{ $container->id }}"
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
                        ]
                    )

                @endforeach

            </div>

        @endif

    </div>

@endif