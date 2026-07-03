@props(['name' => 'search', 'placeholder' => 'Search'])

<div class="relative">
    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-primary-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
    <input
        name="{{ $name }}"
        type="search"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'h-10 w-full rounded-xl border-primary-200 bg-white pl-9 pr-3 text-sm text-primary-900 shadow-sm placeholder:text-primary-400 focus:border-accent-500 focus:ring-accent-500']) }}
    >
</div>
