@php
    $retentionQuery = request()->only([
        'q',
        'record_type',
        'status',
        'compliance',
        'attention',
        'sort',
    ]);

    $sortLabels = [
        'updated_desc' => 'Recently updated',
        'review_asc' => 'Review date: soonest',
        'review_desc' => 'Review date: latest',
        'title_asc' => 'Title: A–Z',
        'title_desc' => 'Title: Z–A',
    ];

    $currentSort = request('sort', 'updated_desc');
@endphp


<form
    method="GET"
    action="{{ route('retention.index') }}"
    data-retention-filter-bar
    class="overflow-hidden rounded-xl border border-border bg-card shadow-card">

    <input
        type="hidden"
        name="tab"
        value="records">

    @if (request()->filled('attention'))
        <input
            type="hidden"
            name="attention"
            value="{{ request('attention') }}">
    @endif


    {{-- Quick views --}}
    <div class="flex flex-col gap-2 border-b border-border bg-background/30 px-3 py-2.5 lg:flex-row lg:items-center lg:justify-between">

        <div class="flex min-w-0 flex-col gap-2 sm:flex-row sm:items-center">


            <nav
                class="flex flex-wrap gap-1.5"
                aria-label="Retention attention views">

                <a
                    href="{{ route(
                        'retention.index',
                        array_merge(
                            \Illuminate\Support\Arr::except(
                                $retentionQuery,
                                ['attention']
                            ),
                            ['tab' => 'records']
                        )
                    ) }}"
                    @class([
                        'inline-flex min-h-8 items-center justify-center rounded-lg border px-2.5 py-1.5 text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2',
                        'border-primary bg-primary text-white' => ! request()->filled('attention'),
                        'border-border bg-card text-slate-600 hover:border-primary/20 hover:bg-primary/5 hover:text-primary' => request()->filled('attention'),
                    ])
                    @if (! request()->filled('attention'))
                        aria-current="page"
                    @endif>

                    All Records

                </a>


                <a
                    href="{{ route(
                        'retention.index',
                        array_merge(
                            \Illuminate\Support\Arr::except(
                                $retentionQuery,
                                ['attention']
                            ),
                            [
                                'tab' => 'records',
                                'attention' => 'overdue',
                            ]
                        )
                    ) }}"
                    @class([
                        'inline-flex min-h-8 items-center gap-1.5 rounded-lg border px-2.5 py-1.5 text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-error focus:ring-offset-2',
                        'border-error/30 bg-error/10 text-error' => request('attention') === 'overdue',
                        'border-border bg-card text-slate-600 hover:border-error/20 hover:bg-error/5 hover:text-error' => request('attention') !== 'overdue',
                    ])
                    @if (request('attention') === 'overdue')
                        aria-current="page"
                    @endif>

                    <span
                        class="h-2 w-2 rounded-full bg-error"
                        aria-hidden="true">
                    </span>

                    Overdue

                </a>


                <a
                    href="{{ route(
                        'retention.index',
                        array_merge(
                            \Illuminate\Support\Arr::except(
                                $retentionQuery,
                                ['attention']
                            ),
                            [
                                'tab' => 'records',
                                'attention' => 'due_soon',
                            ]
                        )
                    ) }}"
                    @class([
                        'inline-flex min-h-8 items-center gap-1.5 rounded-lg border px-2.5 py-1.5 text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-warning focus:ring-offset-2',
                        'border-warning/30 bg-warning/10 text-amber-700' => request('attention') === 'due_soon',
                        'border-border bg-card text-slate-600 hover:border-warning/20 hover:bg-warning/5 hover:text-amber-700' => request('attention') !== 'due_soon',
                    ])
                    @if (request('attention') === 'due_soon')
                        aria-current="page"
                    @endif>

                    <span
                        class="h-2 w-2 rounded-full bg-warning"
                        aria-hidden="true">
                    </span>

                    Due Soon

                </a>


                <a
                    href="{{ route(
                        'retention.index',
                        array_merge(
                            \Illuminate\Support\Arr::except(
                                $retentionQuery,
                                ['attention']
                            ),
                            [
                                'tab' => 'records',
                                'attention' => 'needs_review',
                            ]
                        )
                    ) }}"
                    @class([
                        'inline-flex min-h-8 items-center gap-1.5 rounded-lg border px-2.5 py-1.5 text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2',
                        'border-primary/30 bg-primary/10 text-primary' => request('attention') === 'needs_review',
                        'border-border bg-card text-slate-600 hover:border-primary/20 hover:bg-primary/5 hover:text-primary' => request('attention') !== 'needs_review',
                    ])
                    @if (request('attention') === 'needs_review')
                        aria-current="page"
                    @endif>

                    <svg
                        class="h-3.5 w-3.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4m0 4h.01M5.07 19H18.93a2 2 0 001.74-2.99L13.74 4a2 2 0 00-3.48 0L3.33 16.01A2 2 0 005.07 19z" />

                    </svg>

                    Needs Review

                </a>

            </nav>

        </div>


        @if (
            request()->filled('q')
            || request()->filled('record_type')
            || request()->filled('status')
            || request()->filled('compliance')
            || request()->filled('attention')
            || $currentSort !== 'updated_desc'
        )

            <a
                href="{{ route('retention.index', ['tab' => 'records']) }}"
                class="inline-flex shrink-0 items-center gap-1.5 self-start text-xs font-semibold text-slate-500 transition hover:text-primary lg:self-auto">

                <svg
                    class="h-3.5 w-3.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />

                </svg>

                Reset view

            </a>

        @endif

    </div>


    {{-- Search, filters and sorting --}}
    <div class="grid gap-2 p-3 md:grid-cols-2 xl:grid-cols-[minmax(240px,2fr)_repeat(4,minmax(130px,1fr))_auto]">

        <div class="min-w-0">

            <label
                for="retentionSearch"
                class="mb-1 block text-[9px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                Search
            </label>

            <div class="relative">

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />

                    </svg>

                </div>

                <input
                    id="retentionSearch"
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    maxlength="100"
                    autocomplete="off"
                    placeholder="Search retention records..."
                    class="input pl-10">

            </div>

        </div>


        <div>

            <label
                for="retentionRecordType"
                class="mb-1 block text-[9px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                Record Type
            </label>

            <select
                id="retentionRecordType"
                name="record_type"
                class="input">

                <option value="">
                    All types
                </option>

                @foreach ([
                    'document',
                    'contract',
                    'legal_record',
                    'other'
                ] as $type)

                    <option
                        value="{{ $type }}"
                        @selected(request('record_type') === $type)>

                        {{ str($type)->headline() }}

                    </option>

                @endforeach

            </select>

        </div>


        <div>

            <label
                for="retentionStatus"
                class="mb-1 block text-[9px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                Lifecycle
            </label>

            <select
                id="retentionStatus"
                name="status"
                class="input">

                <option value="">
                    All states
                </option>

                @foreach ([
                    'retained',
                    'review_required',
                    'extended',
                    'archived',
                    'marked_for_disposal'
                ] as $status)

                    <option
                        value="{{ $status }}"
                        @selected(request('status') === $status)>

                        {{ str($status)->headline() }}

                    </option>

                @endforeach

            </select>

        </div>


        <div>

            <label
                for="retentionCompliance"
                class="mb-1 block text-[9px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                Compliance
            </label>

            <select
                id="retentionCompliance"
                name="compliance"
                class="input">

                <option value="">
                    All states
                </option>

                @foreach ([
                    'compliant',
                    'at_risk',
                    'non_compliant'
                ] as $status)

                    <option
                        value="{{ $status }}"
                        @selected(request('compliance') === $status)>

                        {{ str($status)->headline() }}

                    </option>

                @endforeach

            </select>

        </div>


        <div>

            <label
                for="retentionSort"
                class="mb-1 block text-[9px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                Sort By
            </label>

            <select
                id="retentionSort"
                name="sort"
                class="input">

                @foreach ($sortLabels as $value => $label)

                    <option
                        value="{{ $value }}"
                        @selected($currentSort === $value)>

                        {{ $label }}

                    </option>

                @endforeach

            </select>

        </div>


        <div class="flex items-end">

            <button
                type="submit"
                class="btn-primary w-full justify-center xl:w-auto">
                Apply
            </button>

        </div>

    </div>


    {{-- Active state --}}
    @if (
        request()->filled('q')
        || request()->filled('record_type')
        || request()->filled('status')
        || request()->filled('compliance')
        || request()->filled('attention')
        || $currentSort !== 'updated_desc'
    )

        <div class="flex flex-wrap items-center gap-2 border-t border-border bg-background/30 px-3 py-2.5">

            <span class="mr-0.5 text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400">
                Active
            </span>


            @if (request()->filled('attention'))

                <a
                    href="{{ route(
                        'retention.index',
                        array_merge(
                            \Illuminate\Support\Arr::except(
                                $retentionQuery,
                                ['attention']
                            ),
                            ['tab' => 'records']
                        )
                    ) }}"
                    class="inline-flex items-center gap-1 rounded-full bg-primary/5 px-2.5 py-1 text-[11px] font-medium text-primary ring-1 ring-inset ring-primary/10 transition hover:bg-primary/10"
                    aria-label="Remove attention view">

                    {{ str(request('attention'))->headline() }}

                    <span aria-hidden="true">×</span>

                </a>

            @endif


            @if (request()->filled('q'))

                <a
                    href="{{ route(
                        'retention.index',
                        array_merge(
                            \Illuminate\Support\Arr::except(
                                $retentionQuery,
                                ['q']
                            ),
                            ['tab' => 'records']
                        )
                    ) }}"
                    class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200"
                    aria-label="Remove search filter">

                    Search: {{ request('q') }}

                    <span aria-hidden="true">×</span>

                </a>

            @endif


            @if (request()->filled('record_type'))

                <a
                    href="{{ route(
                        'retention.index',
                        array_merge(
                            \Illuminate\Support\Arr::except(
                                $retentionQuery,
                                ['record_type']
                            ),
                            ['tab' => 'records']
                        )
                    ) }}"
                    class="inline-flex items-center gap-1 rounded-full bg-primary/5 px-2.5 py-1 text-[11px] font-medium text-primary ring-1 ring-inset ring-primary/10 transition hover:bg-primary/10"
                    aria-label="Remove record type filter">

                    {{ str(request('record_type'))->headline() }}

                    <span aria-hidden="true">×</span>

                </a>

            @endif


            @if (request()->filled('status'))

                <a
                    href="{{ route(
                        'retention.index',
                        array_merge(
                            \Illuminate\Support\Arr::except(
                                $retentionQuery,
                                ['status']
                            ),
                            ['tab' => 'records']
                        )
                    ) }}"
                    class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-1 text-[11px] font-medium text-sky-700 ring-1 ring-inset ring-sky-200 transition hover:bg-sky-100"
                    aria-label="Remove lifecycle filter">

                    {{ str(request('status'))->headline() }}

                    <span aria-hidden="true">×</span>

                </a>

            @endif


            @if (request()->filled('compliance'))

                <a
                    href="{{ route(
                        'retention.index',
                        array_merge(
                            \Illuminate\Support\Arr::except(
                                $retentionQuery,
                                ['compliance']
                            ),
                            ['tab' => 'records']
                        )
                    ) }}"
                    class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-medium text-amber-700 ring-1 ring-inset ring-amber-200 transition hover:bg-amber-100"
                    aria-label="Remove compliance filter">

                    {{ str(request('compliance'))->headline() }}

                    <span aria-hidden="true">×</span>

                </a>

            @endif


            @if ($currentSort !== 'updated_desc')

                <a
                    href="{{ route(
                        'retention.index',
                        array_merge(
                            \Illuminate\Support\Arr::except(
                                $retentionQuery,
                                ['sort']
                            ),
                            ['tab' => 'records']
                        )
                    ) }}"
                    class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-600 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-200"
                    aria-label="Reset record sorting">

                    Sort: {{ $sortLabels[$currentSort] ?? 'Recently updated' }}

                    <span aria-hidden="true">×</span>

                </a>

            @endif

        </div>

    @endif

</form>