@php
    $historyEntries =
        collect(
            $document->history ?? []
        )
        ->reverse()
        ->values();

    $visibleHistory =
        $historyEntries->take(5);

    $olderHistory =
        $historyEntries->slice(5);
@endphp


<section class="card overflow-hidden">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-4 border-b border-border px-5 py-4">

        <div class="flex items-center gap-3">

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
                        d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />

                </svg>

            </span>


            <div>

                <h2 class="font-heading text-sm font-semibold text-primary">
                    Document History
                </h2>

                <p class="mt-0.5 text-[10px] text-slate-400">
                    Recent lifecycle and version activity
                </p>

            </div>

        </div>


        @if ($historyEntries->isNotEmpty())

            <span class="rounded-full bg-primary/5 px-2.5 py-1 text-[9px] font-semibold text-primary">
                {{ $historyEntries->count() }}
                {{ $historyEntries->count() === 1 ? 'event' : 'events' }}
            </span>

        @endif

    </div>


    @if ($historyEntries->isEmpty())

        <div class="px-5 py-10 text-center">

            <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-background text-slate-400">

                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4m0 4h.01M4 6h16v14H4V6z" />

                </svg>

            </div>


            <p class="mt-3 text-xs font-semibold text-primary">
                No history recorded
            </p>

            <p class="mt-1 text-[10px] text-slate-400">
                Document lifecycle activity will appear here.
            </p>

        </div>

    @else

        <div class="px-5 py-4">

            <div class="relative">

                {{-- Timeline rail --}}
                <div class="absolute bottom-3 left-[7px] top-3 w-px bg-border"></div>


                <div class="space-y-1">

                    @foreach ($visibleHistory as $entry)

                        <div class="relative flex gap-3 py-2.5">

                            <div class="relative z-10 mt-1.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-card">

                                <span class="h-2 w-2 rounded-full bg-secondary"></span>

                            </div>


                            <div class="min-w-0 flex-1">

                                <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">

                                    <div class="min-w-0">

                                        <p class="text-xs font-semibold text-primary">
                                            {{ str($entry['action'] ?? 'activity')->headline() }}
                                        </p>


                                        @if (! empty($entry['note']))

                                            <p class="mt-1 text-[11px] leading-5 text-slate-500">
                                                {{ $entry['note'] }}
                                            </p>

                                        @endif

                                    </div>


                                    @if (! empty($entry['at']))

                                        <time class="shrink-0 text-[9px] font-medium text-slate-400">

                                            {{
                                                \Illuminate\Support\Carbon::parse(
                                                    $entry['at']
                                                )->format('M d, Y')
                                            }}

                                        </time>

                                    @endif

                                </div>


                                <div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-[9px] text-slate-400">

                                    <span>
                                        By {{ $entry['by'] ?? 'system' }}
                                    </span>


                                    @if (! empty($entry['version']))

                                        <span class="text-slate-300">
                                            &bull;
                                        </span>

                                        <span>
                                            Version {{ $entry['version'] }}
                                        </span>

                                    @endif


                                    @if (! empty($entry['at']))

                                        <span class="text-slate-300">
                                            &bull;
                                        </span>

                                        <span>
                                            {{
                                                \Illuminate\Support\Carbon::parse(
                                                    $entry['at']
                                                )->format('h:i A')
                                            }}
                                        </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>


                @if ($olderHistory->isNotEmpty())

                    <details class="group mt-2">

                        <summary class="ml-7 inline-flex cursor-pointer list-none items-center gap-1.5 rounded-lg px-2.5 py-2 text-[10px] font-semibold text-primary transition hover:bg-primary/5">

                            Show {{ $olderHistory->count() }} older
                            {{ $olderHistory->count() === 1 ? 'event' : 'events' }}

                            <svg
                                class="h-3 w-3 transition-transform duration-200 group-open:rotate-180"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7" />

                            </svg>

                        </summary>


                        <div class="mt-1 space-y-1">

                            @foreach ($olderHistory as $entry)

                                <div class="relative flex gap-3 py-2.5">

                                    <div class="relative z-10 mt-1.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-card">

                                        <span class="h-2 w-2 rounded-full bg-slate-300"></span>

                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">

                                            <div class="min-w-0">

                                                <p class="text-xs font-semibold text-primary">
                                                    {{ str($entry['action'] ?? 'activity')->headline() }}
                                                </p>


                                                @if (! empty($entry['note']))

                                                    <p class="mt-1 text-[11px] leading-5 text-slate-500">
                                                        {{ $entry['note'] }}
                                                    </p>

                                                @endif

                                            </div>


                                            @if (! empty($entry['at']))

                                                <time class="shrink-0 text-[9px] text-slate-400">

                                                    {{
                                                        \Illuminate\Support\Carbon::parse(
                                                            $entry['at']
                                                        )->format('M d, Y')
                                                    }}

                                                </time>

                                            @endif

                                        </div>


                                        <div class="mt-1.5 flex flex-wrap items-center gap-x-2 text-[9px] text-slate-400">

                                            <span>
                                                By {{ $entry['by'] ?? 'system' }}
                                            </span>


                                            @if (! empty($entry['version']))

                                                <span class="text-slate-300">
                                                    &bull;
                                                </span>

                                                <span>
                                                    Version {{ $entry['version'] }}
                                                </span>

                                            @endif

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </details>

                @endif

            </div>

        </div>

    @endif

</section>