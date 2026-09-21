@props([
    'title',
    'description' => null,
])

<div class="flex flex-col items-center justify-center px-5 py-10 text-center">

    @isset($icon)

        <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-background text-slate-400">
            {{ $icon }}
        </div>

    @endisset


    <p class="text-sm font-semibold text-primary">
        {{ $title }}
    </p>


    @if ($description)

        <p class="mt-1 max-w-sm text-xs leading-5 text-slate-500">
            {{ $description }}
        </p>

    @endif

</div>