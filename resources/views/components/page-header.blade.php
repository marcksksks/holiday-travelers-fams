@props([
    'title',
    'description' => null,
    'eyebrow' => null,
    'badge' => null,
])

<div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">

    <div class="min-w-0">

        <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

        @if ($eyebrow)

            <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-secondary">
                {{ $eyebrow }}
            </p>

        @endif


        <div class="flex flex-wrap items-center gap-3">

            <h1 class="font-heading text-2xl font-bold tracking-tight text-primary">
                {{ $title }}
            </h1>

            @if ($badge)

                <span class="rounded-full border border-secondary/20 bg-secondary/10 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-secondary">
                    {{ $badge }}
                </span>

            @endif

        </div>


        @if ($description)

            <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">
                {{ $description }}
            </p>

        @endif

    </div>


    @isset($actions)

        <div class="flex flex-wrap items-center gap-2">
            {{ $actions }}
        </div>

    @endisset

</div>