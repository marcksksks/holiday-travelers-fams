@props([
    'title',
    'description' => null,
    'eyebrow' => null,
])

<div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">

    <div>

        @if ($eyebrow)

            <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                {{ $eyebrow }}
            </p>

        @endif

        <h2 class="font-heading text-lg font-semibold text-primary">
            {{ $title }}
        </h2>

        @if ($description)

            <p class="mt-1 text-xs leading-5 text-slate-500">
                {{ $description }}
            </p>

        @endif

    </div>


    @isset($actions)

        <div class="flex items-center gap-2">
            {{ $actions }}
        </div>

    @endisset

</div>