@props([
    'variant' => 'empty',
    'title' => 'No results',
    'description' => 'There is nothing to show yet.',
])

@php
    $styles = [
        'empty' => ['bg-primary-100 text-primary-500', 'M4 6h16M4 12h16M4 18h10'],
        'forbidden' => ['bg-amber-100 text-amber-700', 'M12 9v4m0 4h.01M5 19h14a2 2 0 0 0 1.7-3L13.7 4a2 2 0 0 0-3.4 0L3.3 16A2 2 0 0 0 5 19Z'],
        'error' => ['bg-red-100 text-red-700', 'M6 18 18 6M6 6l12 12'],
        'success' => ['bg-accent-100 text-accent-700', 'M5 13l4 4L19 7'],
    ][$variant] ?? ['bg-primary-100 text-primary-500', 'M4 6h16M4 12h16M4 18h10'];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-xl border border-primary-200 bg-white px-6 py-12 text-center']) }}>
    <div class="grid h-12 w-12 place-items-center rounded-xl {{ $styles[0] }}">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $styles[1] }}"/></svg>
    </div>
    <h3 class="mt-4 text-sm font-semibold text-primary-950">{{ $title }}</h3>
    <p class="mt-1 max-w-sm text-sm leading-6 text-primary-500">{{ $description }}</p>
    @isset($action)
        <div class="mt-5">{{ $action }}</div>
    @endisset
</div>
