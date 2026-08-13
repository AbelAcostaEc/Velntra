@props([
    'title',
    'subtitle' => null,
    'status' => null,
    'statusVariant' => 'neutral',
])

<article {{ $attributes->merge(['class' => 'rounded-xl border border-primary-200 bg-white p-4 shadow-sm']) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h3 class="truncate text-sm font-semibold text-primary-950">{{ $title }}</h3>
            @if ($subtitle)
                <p class="mt-1 text-sm text-primary-500">{{ $subtitle }}</p>
            @endif
        </div>
        @if (isset($status) && $status instanceof \Illuminate\View\ComponentSlot && $status->isNotEmpty())
            <div class="flex flex-wrap items-center justify-end gap-1.5">
                {{ $status }}
            </div>
        @elseif ($status)
            <x-badge :variant="$statusVariant">{{ $status }}</x-badge>
        @endif
    </div>

    @if (isset($meta) && trim($meta->toHtml()) !== '')
        <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
            {{ $meta }}
        </dl>
    @endif

    @if (isset($actions) && trim($actions->toHtml()) !== '')
        <div class="mt-4 flex items-center justify-end gap-2 border-t border-primary-100 pt-4">
            {{ $actions }}
        </div>
    @endif
</article>
