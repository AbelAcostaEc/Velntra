@props([
    'title' => 'Filters',
    'description' => 'Refine the current table results.',
    'activeCount' => 0,
    'clearAction' => null,
    'applyLabel' => 'Apply filters',
    'clearLabel' => 'Clear',
])

<div x-data="{ open: false }" {{ $attributes->merge(['class' => 'w-full md:w-auto']) }}>
    <div class="flex items-center gap-2 md:hidden">
        <x-button variant="secondary" x-on:click="open = ! open">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M7 12h10m-7 6h4"/></svg>
            {{ $title }}
            @if ($activeCount > 0)
                <span class="rounded-full bg-accent-100 px-2 py-0.5 text-xs text-accent-700">{{ $activeCount }}</span>
            @endif
        </x-button>
    </div>

    <div
        x-bind:class="{ 'hidden': ! open, 'block': open }"
        class="mt-3 rounded-xl border border-primary-200 bg-primary-50 p-4 md:mt-0 md:flex md:flex-wrap md:items-end md:gap-3 md:border-0 md:bg-transparent md:p-0"
    >
        <div class="mb-4 md:hidden">
            <h2 class="text-sm font-semibold text-primary-950">{{ $title }}</h2>
            <p class="mt-1 text-sm text-primary-500">{{ $description }}</p>
        </div>

        <div class="grid gap-4 md:flex md:flex-wrap md:items-end md:gap-3">
            {{ $slot }}
        </div>

        <div class="mt-4 flex items-center gap-2 md:mt-0">
            @if ($clearAction)
                <x-button variant="secondary" size="sm" wire:click="{{ $clearAction }}">{{ $clearLabel }}</x-button>
            @endif
            <x-button size="sm" class="md:hidden" x-on:click="open = false">{{ $applyLabel }}</x-button>
            @isset($actions)
                {{ $actions }}
            @endisset
        </div>
    </div>
</div>
