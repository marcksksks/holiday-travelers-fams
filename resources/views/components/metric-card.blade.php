@props([
    'label',
    'value',
    'href' => null,
    'helper' => null,
    'tone' => 'primary',
])

@php
    $toneClasses = match ($tone) {
        'secondary' => [
            'icon' => 'bg-secondary/10 text-secondary',
            'value' => 'text-secondary',
            'hover' => 'hover:border-secondary/30',
        ],

        'accent' => [
            'icon' => 'bg-accent/10 text-accent',
            'value' => 'text-accent',
            'hover' => 'hover:border-accent/30',
        ],

        'success' => [
            'icon' => 'bg-success/10 text-success',
            'value' => 'text-success',
            'hover' => 'hover:border-success/30',
        ],

        'warning' => [
            'icon' => 'bg-warning/10 text-amber-600',
            'value' => 'text-amber-600',
            'hover' => 'hover:border-warning/40',
        ],

        'error' => [
            'icon' => 'bg-error/10 text-error',
            'value' => 'text-error',
            'hover' => 'hover:border-error/30',
        ],

        default => [
            'icon' => 'bg-primary/10 text-primary',
            'value' => 'text-primary',
            'hover' => 'hover:border-primary/20',
        ],
    };

    $tag =
        $href
            ? 'a'
            : 'div';
@endphp


<{{ $tag }}
    @if ($href)
        href="{{ $href }}"
    @endif
    {{ $attributes->class([
        'group card relative overflow-hidden p-4 transition-all duration-200',
        'hover:-translate-y-0.5 hover:shadow-soft' => $href,
        $toneClasses['hover'] => $href,
    ]) }}>

    <div class="flex items-start justify-between gap-3">

        <div class="min-w-0">

            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                {{ $label }}
            </p>

            <p class="mt-1.5 font-heading text-2xl font-bold {{ $toneClasses['value'] }}">
                {{ $value }}
            </p>

        </div>


        @isset($icon)

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $toneClasses['icon'] }}">
                {{ $icon }}
            </div>

        @endisset

    </div>


    @if ($helper)

        <p class="mt-3 text-xs leading-5 text-slate-500">
            {{ $helper }}
        </p>

    @endif


    @if ($href)

        <div class="mt-3 flex items-center gap-1 text-[11px] font-semibold text-slate-400 transition group-hover:text-primary">

            <span>
                Open workspace
            </span>

            <span class="transition-transform group-hover:translate-x-1">
                &rarr;
            </span>

        </div>

    @endif

</{{ $tag }}>