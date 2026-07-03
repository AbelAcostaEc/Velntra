@props(['label', 'value', 'trend' => null, 'variant' => 'neutral'])

<x-card class="h-full">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-primary-500">{{ $label }}</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight text-primary-950">{{ $value }}</p>
        </div>
        @isset($icon)
            <div class="rounded-xl bg-primary-100 p-2 text-primary-600">{{ $icon }}</div>
        @endisset
    </div>

    @if ($trend)
        <div class="mt-4">
            <x-badge :variant="$variant">{{ $trend }}</x-badge>
        </div>
    @endif
</x-card>
