@props([
    'name' => 'search',
    'label' => 'Search',
    'placeholder' => 'Search records...',
    'clearable' => true,
])

<div class="w-full sm:max-w-sm" x-data="{ value: '' }">
    <label for="{{ $name }}" class="sr-only">{{ $label }}</label>

    <div class="relative">
        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-primary-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>

        <input
            x-ref="input"
            id="{{ $name }}"
            name="{{ $name }}"
            type="search"
            placeholder="{{ $placeholder }}"
            x-model="value"
            {{ $attributes->merge(['class' => 'h-10 w-full rounded-xl border-primary-200 bg-white pl-9 pr-9 text-sm text-primary-900 shadow-sm placeholder:text-primary-400 focus:border-accent-500 focus:ring-accent-500 disabled:cursor-not-allowed disabled:bg-primary-100 disabled:text-primary-500']) }}
        >

        @if ($clearable)
            <button
                type="button"
                x-cloak
                x-show="value.length > 0"
                x-on:click="value = ''; $refs.input?.dispatchEvent(new Event('input', { bubbles: true }))"
                class="absolute right-2 top-1/2 grid h-6 w-6 -translate-y-1/2 place-items-center rounded-lg text-primary-400 hover:bg-primary-100 hover:text-primary-700"
                aria-label="Clear search"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        @endif
    </div>
</div>
